<?php

declare(strict_types=1);

namespace KanbanBoard\Read\Milestone;

class Model
{
    protected $identifier;

    protected $milestone;
    protected $number;
    protected $closed_issues;
    protected $open_issues;
    protected $html_url;
    protected $repository;


    public function __construct(int $identifier)
    {
        $this->identifier = $identifier;
    }

    public function getId(): int
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




}
