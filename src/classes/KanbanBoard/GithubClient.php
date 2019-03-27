<?php

namespace KanbanBoard;

class GithubClient
{
    private $client;
    private $milestoneApi;
    private $account;

    public function __construct($token, $account)
    {
        $this->account = $account;
        $this->client= new \Github\Client(new \Github\HttpClient\CachedHttpClient(['cache_dir' => '/tmp/github-api-cache']));
        $this->client->authenticate($token, \Github\Client::AUTH_HTTP_TOKEN);
        $this->milestoneApi = $this->client->api('issues')->milestones();
    }

    public function milestones($repository)
    {
        return $this->milestoneApi->all($this->account, $repository);
    }

    public function issues($repository, $milestoneId)
    {
        $issueParameters = ['milestone' => $milestoneId, 'state' => 'all'];
        return $this->client->api('issue')->all($this->account, $repository, $issueParameters);
    }
}
