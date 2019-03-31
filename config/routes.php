<?php

return [
    ['GET', '/',  ['Controller\DefaultController', \KanbanBoard\Router\Section::PROTECTED ]],
    ['GET', '/auth',  ['Controller\AuthController']],
];
