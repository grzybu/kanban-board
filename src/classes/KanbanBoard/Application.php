<?php

namespace KanbanBoard;

use KanbanBoard\GithubClient;
use vierbergenlars\SemVer\version;

use vierbergenlars\SemVer\expression;
use vierbergenlars\SemVer\SemVerException;
use \Michelf\Markdown;

class Application
{

    protected $github;
    protected $reposietories;
    protected $pausedLabels;

    public function __construct(GithubClient $github, array $repositories, array $pausedLabels = [])
    {
        $this->github = $github;
        $this->repositories = $repositories;
        $this->pausedLabels = $pausedLabels;
    }

    public function board()
    {
        $reposByMilestone = [];

        foreach ($this->repositories as $repository) {
            foreach ($this->github->milestones($repository) as $data) {
                $reposByMilestone[$data['title']] = $data;
                $reposByMilestone[$data['title']]['repository'] = $repository;
            }
        }
        ksort($reposByMilestone);

        $milestones = [];

        foreach ($reposByMilestone as $name => $data) {
            $issues = $this->issues($data['repository'], $data['number']);
            $percent = self::percent($data['closed_issues'], $data['open_issues']);
            if ($percent) {
                $milestones[] = [
                    'milestone' => $name,
                    'url' => $data['html_url'],
                    'progress' => $percent,
                    'queued' => $issues['queued'] ?? [],
                    'active' => $issues['active'] ?? [],
                    'completed' => $issues['completed'] ?? []
                ];
            }
        }
        return $milestones;
    }

    private function issues($repository, $milestoneId): array
    {
        $milestoneIssues = $this->github->issues($repository, $milestoneId);

        $issues = [];

        foreach ($milestoneIssues as $ii) {
            if (isset($ii['pull_request'])) {
                continue;
            }

            $issues[self::state($ii)][] = [
                'id' => $ii['id'], 'number' => $ii['number'],
                'title' => $ii['title'],
                'body' => Markdown::defaultTransform($ii['body']),
                'url' => $ii['html_url'],
                'assignee' => Utilities::hasValue($ii, 'assignee') ? $ii['assignee']['avatar_url'] . '?s=16' : null,
                'paused' => self::labelsMatch($ii, $this->pausedLabels),
                'progress' => self::percent(
                    substr_count(strtolower($ii['body']), '[x]'),
                    substr_count(strtolower($ii['body']), '[ ]')
                ),
                'closed' => $ii['closed_at'] ?? null
            ];
        }

        if (isset($issues['active'])) {
            usort($issues['active'], function ($issueA, $issueB) {
                return count($issueA['paused']) - count($issueB['paused']) === 0 ?
                    strcmp($issueA['title'], $issueB['title']) :
                    count($issueA['paused']) - count($issueB['paused']);
            });
        }


        return $issues;
    }

    private static function state(array $issue): string
    {
        if ($issue['state'] === 'closed') {
            return 'completed';
        } elseif (Utilities::hasValue($issue, 'assignee') && $issue['assignee'] !== '') {
            return 'active';
        } else {
            return 'queued';
        }
    }

    private static function labelsMatch(array $issue, array $needles = []): array
    {
        if (Utilities::hasValue($issue, 'labels')) {
            foreach ($issue['labels'] as $label) {
                if (in_array($label['name'], $needles)) {
                    return [$label['name']];
                }
            }
        }
        return [];
    }

    private static function percent(int $complete, int $remaining): array
    {
        $total = $complete + $remaining;
        if ($total > 0) {
            $percent = ($complete or $remaining) ? round($complete / $total * 100) : 0;
            return [
                'total' => $total,
                'complete' => $complete,
                'remaining' => $remaining,
                'percent' => $percent
            ];
        }
        return [];
    }
}
