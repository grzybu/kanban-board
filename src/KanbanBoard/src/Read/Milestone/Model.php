<?php

declare(strict_types=1);

namespace KanbanBoard\Read\Milestone;

use Common\Read\DeserializableModel;
use Common\Traits\PercentTrait;

class Model implements DeserializableModel
{
    use PercentTrait;

    protected $identifier;

    protected $milestone;
    protected $number;
    protected $closedIssues;
    protected $openIssues;
    protected $htmlUrl;
    protected $repository;
    protected $title;


    public function __construct($identifier)
    {
        $this->identifier = $identifier;
    }

    public function setMilestone($milestone): void
    {
        $this->milestone = $milestone;
    }

    public function setNumber($number): void
    {
        $this->number = $number;
    }

    public function setClosedIssues($closedIssues): void
    {
        $this->closedIssues = $closedIssues;
    }

    public function setOpenIssues($openIssues): void
    {
        $this->openIssues = $openIssues;
    }

    public function setHtmlUrl($htmlUrl): void
    {
        $this->htmlUrl = $htmlUrl;
    }

    public function getId()
    {
        return $this->identifier;
    }

    public static function deserialize(array $data): Model
    {
        $item = new static($data['id']);

        $fields = [
            'title',
            'number',
        ];

        foreach ($fields as $field) {
            $item->$field = $data[$field] ?? null;
        }

        $item->closedIssues = $data['closed_issues'] ?? null;
        $item->openIssues = $data['open_issues'] ?? null;
        $item->htmlUrl = $data['html_url'] ?? null;

        return $item;
    }

    public function getPercent(): array
    {
        $complete = (int) $this->closedIssues;
        $remaining = (int) $this->openIssues;

        return $this->percent($complete, $remaining);
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getClosedIssues()
    {
        return $this->closedIssues;
    }

    public function getOpenIssues()
    {
        return $this->openIssues;
    }

    public function getUrl()
    {
        return $this->htmlUrl;
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
        return $this->htmlUrl;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }
}
