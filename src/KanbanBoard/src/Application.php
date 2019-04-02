<?php

declare(strict_types=1);


namespace KanbanBoard;

use KanbanBoard\GithubClient;

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
            $percent = $this->percent($data['closed_issues'], $data['open_issues']);
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
                'url' => $ii['html_url'],
                'assignee' => $this->hasValue($ii, 'assignee') ? $ii['assignee']['avatar_url'] . '?s=16' : null,
                'paused' => $this->labelsMatch($ii, $this->pausedLabels),
                'progress' => $this->percent(
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

    protected function state(array $issue): string
    {
        if ($issue['state'] === 'closed') {
            return 'completed';
        } elseif ($this->hasValue($issue, 'assignee') && $issue['assignee'] !== '') {
            return 'active';
        } else {
            return 'queued';
        }
    }

    protected function labelsMatch(array $issue, array $needles = []): array
    {
        if ($this->hasValue($issue, 'labels')) {
            foreach ($issue['labels'] as $label) {
                if (in_array($label['name'], $needles)) {
                    return [$label['name']];
                }
            }
        }
        return [];
    }

    protected function percent(int $complete, int $remaining): array
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

    protected function hasValue(array $array, string $key): bool
    {
        return is_array($array) && array_key_exists($key, $array) && !empty($array[$key]);
    }
}
