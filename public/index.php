<?php
/**
 * Created by PhpStorm.
 * User: RickDAM
 * Date: 14/06/2019
 * Time: 10:59
 */

require '../vendor/autoload.php';
require '../src/config/database.php';

$app = new \Slim\App;
require '../src/routes/players.php';
require '../src/objects/players_entity.php';
require '../src/objects/teams_entity.php';
require '../src/queries/qplayers.php';

/** @noinspection PhpUnhandledExceptionInspection */
$app->run();