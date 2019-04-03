<?php

declare(strict_types=1);

namespace KanbanBoard\Read\Issue;

use Common\Read\DeserializableModel;

class Model implements DeserializableModel
{
    protected $identifier;
    protected $state;
    protected $pullRequest;
    protected $number;
    protected $labels;
    protected $assignee;
    protected $htmlUrl;
    protected $closedAt;

    public function __construct($identifier)
    {
        $this->identifier = $identifier;
    }

    public function getId(): string
    {
        return $this->identifier;
    }

    public function setLabels(array $labels)
    {
        foreach ($labels as $label) {
            $this->labels[] = $label['name'];
        }
    }

    public static function deserialize(array $data)
    {
        $item = new static($data['id']);

        $fields = [
            'title',
            'body',
            'number'
        ];

        foreach ($fields as $field) {
            $item->$field = $data[$field] ?? null;
        }

        $item->setLabels($data['labels'] ?? []);

        $item->assignee = $item->deserializeAssigne($data['assignee']);
        $item->htmlUrl = $data['html_url'] ?? null;
        $item->closedAt = $data['closed_at'] ?? null;
        $item->pullRequest = $data['pull_request'] ?? null;

        return $item;
    }

    protected function deserializeAssigne($data)
    {
        if (isset($data['login'])) {
            return [
                'login' => $data['login'],
                'avatar' => $data['avatar_url']
            ];
        }

        return null;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getState()
    {
        return $this->state;
    }


    public function setState($state): void
    {
        $this->state = $state;
    }


    public function getNumber()
    {
        return $this->number;
    }


    public function setNumber($number): void
    {
        $this->number = $number;
    }

    public function getAssignee()
    {
        return $this->assignee;
    }

    public function setAssignee($assignee): void
    {
        $this->assignee = $assignee;
    }

    public function getHtmlUrl()
    {
        return $this->htmlUrl;
    }

    public function setHtmlUrl($htmlUrl): void
    {
        $this->htmlUrl = $htmlUrl;
    }

    public function getClosedAt()
    {
        return $this->closedAt;
    }

    public function setClosedAt($closedAt): void
    {
        $this->closedAt = $closedAt;
    }
}
