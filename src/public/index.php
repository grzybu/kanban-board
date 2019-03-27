<?php
require '../../vendor/autoload.php';

$dotenv = new Symfony\Component\Dotenv\Dotenv();
$dotenv->load('../.env');

use KanbanBoard\Authentication;
use KanbanBoard\GithubClient;
use KanbanBoard\Utilities;



$repositories = explode('|', Utilities::env('GH_REPOSITORIES'));
$authentication = new \KanbanBoard\Login();
$token = $authentication->login();
$github = new GithubClient($token, Utilities::env('GH_ACCOUNT'));
$board = new \KanbanBoard\Application($github, $repositories, ['waiting-for-feedback']);
$data = $board->board();
$m = new Mustache_Engine([
    'loader' => new Mustache_Loader_FilesystemLoader('../views'),
]);
echo $m->render('index', ['milestones' => $data]);
