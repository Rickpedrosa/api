<?php
/**
 * Created by PhpStorm.
 * User: RickDAM
 * Date: 14/06/2019
 * Time: 10:59
 */

require '../vendor/autoload.php';
require '../src/config/database.php';

$app = new \Slim\App(['settings' => ['displayErrorDetails' => true]]);
require '../src/routes/players.php';
require '../src/routes/teams.php';
require '../src/objects/players_entity.php';
require '../src/objects/teams_entity.php';
require '../src/queries/qplayers.php';
require '../src/queries/qteams.php';

/** @noinspection PhpUnhandledExceptionInspection */
$app->run();