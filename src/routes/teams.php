<?php
/**
 * Created by PhpStorm.
 * User: RickDAM
 * Date: 14/06/2019
 * Time: 18:54
 */
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App(['settings' => ['displayErrorDetails' => true]]);