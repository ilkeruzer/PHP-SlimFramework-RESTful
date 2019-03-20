<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require "../src/config/Db.php";

$app = new \Slim\App;


//Book Routes...
require "../src/routes/book.php";

$app->run();