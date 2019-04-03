<?php

return [
    ['GET', '/',  ['Controller\DefaultController', \Common\Router\Section::PROTECTED ]],
    ['GET', '/auth',  ['Controller\AuthController']],
];
