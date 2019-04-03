<?php

use Common\Router\Section;

return [
    ['GET', '/',  ['Controller\DefaultController', Section::PROTECTED ]],
    ['GET', '/auth',  ['Controller\AuthController']],
];
