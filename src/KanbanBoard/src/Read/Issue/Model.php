<?php

declare(strict_types=1);

namespace KanbanBoard\Read\Issue;

use Common\Read\DeserializableModel;
use Common\Traits\PercentTrait;

class Model implements DeserializableModel
{

    use PercentTrait;

    protected $identifier;
    protected $state;
    protected $pullRequest;
    protected $number;
    protected $labels;
    protected $assignee;
    protected $htmlUrl;
    protected $closedAt;
    protected $title;
    protected $body;

    public function __construct($identifier)
    {
        $this->identifier = $identifier;
        $this->labels = [];
    }

    public function getId()
    {
        return $this->identifier;
    }

    public function setLabels(array $labels)
    {
        foreach ($labels as $label) {
            $this->labels[] = $label['name'];
        }
    }

    public function hasLabel($labels)
    {
        if (!is_array($labels)) {
            $labels = [$labels];
        }

        foreach ($this->labels as $label) {
            if (in_array($label, $labels)) {
                return [$label];
            }
        }
        return [];
    }

    public static function deserialize(array $data)
    {
        $item = new static($data['id']);

        $fields = [
            'title',
            'body',
            'number',
            'state',
            'pull_request'
        ];

        foreach ($fields as $field) {
            $item->$field = $data[$field] ?? null;
        }

        $item->setLabels($data['labels'] ?? []);

        $item->assignee = isset($data['assignee']) ? $item->deserializeAssignee($data['assignee']) : null;
        $item->htmlUrl = $data['html_url'] ?? null;
        $item->closedAt = $data['closed_at'] ?? null;
        $item->pullRequest = $data['pull_request'] ?? null;

        return $item;
    }

    protected function deserializeAssignee($data)
    {
        if (isset($data['login'])) {
            return [
                'login' => $data['login'],
                'avatar' => $data['avatar_url'] ?? null,
            ];
        }

        return null;
    }

    public function getState()
    {
        return $this->state;
    }

    public function getBoardState()
    {
        if ($this->state === 'closed') {
            return 'completed';
        } elseif (null !== $this->getAssignee()) {
            return 'active';
        } else {
            return 'queued';
        }
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

    public function getAssigneeAvatar($size = 16)
    {
        if ($this->assignee) {
            return $this->assignee['avatar'] ? $this->assignee['avatar'] . '?s=' . $size : null;
        }
        return null;
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

    public function isPullRequest()
    {
        return $this->pullRequest !== null;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function isClosed()
    {
        return $this->closedAt !== null;
    }

    public function getProgress()
    {
        $complete = substr_count(strtolower($this->body), '[x]');
        $remaining = substr_count(strtolower($this->body), '[ ]');

        return $this->percent($complete, $remaining);
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function setPullRequest($pullRequest)
    {
        $this->pullRequest = $pullRequest;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }
}
