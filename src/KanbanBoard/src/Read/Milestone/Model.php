<?php

declare(strict_types=1);

namespace KanbanBoard\Read\Milestone;

use Broadway\ReadModel\SerializableReadModel;

class Model implements SerializableReadModel
{
    protected $identifier;

    protected $milestone;
    protected $number;
    protected $closed_issues;
    protected $open_issues;
    protected $html_url;
    protected $repository;
    protected $title;


    public function __construct($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @param mixed $milestone
     */
    public function setMilestone($milestone): void
    {
        $this->milestone = $milestone;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number): void
    {
        $this->number = $number;
    }

    /**
     * @param mixed $closed_issues
     */
    public function setClosedIssues($closed_issues): void
    {
        $this->closed_issues = $closed_issues;
    }

    /**
     * @param mixed $open_issues
     */
    public function setOpenIssues($open_issues): void
    {
        $this->open_issues = $open_issues;
    }

    /**
     * @param mixed $html_url
     */
    public function setHtmlUrl($html_url): void
    {
        $this->html_url = $html_url;
    }

    public function getId(): string
    {
        return $this->identifier;
    }

    public static function deserialize(array $data)
    {
        $item = new static($data['id']);

        $fields = [
            'title',
            'number',
            'closed_issues',
            'open_issues',
            'html_url'
        ];

        foreach ($fields as $field) {
            $item->$field = $data[$field] ?? null;
        }

        return $item;
    }

    public function serialize(): array
    {
        $serialized = [
            'id' => $this->identifier,
            'milestone' => $this->milestone,
            'number' => $this->number,
            'closed_issues' => $this->closed_issues,
            'open_issues' => $this->open_issues,
            'html_url' => $this->html_url,
            'repository' => $this->repository,
            'title' => $this->title,
        ];

        return $serialized;
    }


    public function getPercent(): array
    {
        $complete = $this->closed_issues;
        $remaining = $this->open_issues;

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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getClosedIssues()
    {
        return $this->closed_issues;
    }

    public function getOpenIssues()
    {
        return $this->open_issues;
    }

    public function getUrl()
    {
        return $this->html_url;
    }

    public function getRepository(): string
    {
        return $this->repository;
    }

    public function setRepository(string $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * @return mixed
     */
    public function getMilestone()
    {
        return $this->milestone;
    }

    /**
     * @return mixed
     */
    public function getHtmlUrl()
    {
        return $this->html_url;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }
}
