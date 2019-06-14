<?php
/**
 * Created by PhpStorm.
 * User: RickDAM
 * Date: 14/06/2019
 * Time: 11:18
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App(['settings' => ['displayErrorDetails' => true]]);

$silverPlayersCount = 3643;
$lastSilverPage = 121;

$bronzePlayersCount = 12069;
$lastBronzePage = 402;

$app->get('/players', function (Request $request, Response $response) {
    $jsonData = array();
    if (isset($request->getQueryParams()['club'])) {
        $jsonData = getPlayersByClub($request->getQueryParams());
    } else if (isset($request->getQueryParams()['search'])) {
        $jsonData = getPlayersBySearch($request->getQueryParams());
    }
    /** @noinspection PhpUndefinedMethodInspection */
    return $response->withJson($jsonData, $jsonData['code']);
});

$app->get('/players/gold', function (Request $request, Response $response) {
    $jsonData = getCategoryPlayersByPagination($request->getQueryParams(), "gold");
    /** @noinspection PhpUndefinedMethodInspection */
    return $response->withJson($jsonData, $jsonData['code']);
});

$app->get('/players/silver', function (Request $request, Response $response) {
    $jsonData = getCategoryPlayersByPagination($request->getQueryParams(), "silver");
    /** @noinspection PhpUndefinedMethodInspection */
    return $response->withJson($jsonData, $jsonData['code']);
});

$app->get('/players/bronze', function (Request $request, Response $response) {
    $jsonData = getCategoryPlayersByPagination($request->getQueryParams(), "bronze");
    /** @noinspection PhpUndefinedMethodInspection */
    return $response->withJson($jsonData, $jsonData['code']);
});

$app->get('/players/{id}', function (Request $request, Response $response) {
    $jsonData = getPlayerById($request->getAttributes());
    /** @noinspection PhpUndefinedMethodInspection */
    return $response->withJson($jsonData, $jsonData['code']);
});


function getPlayersByClub($arrayParam)
{
    $products_arr = array();
    $players_array = array();
    $players_item = array();
    $temp = null;
    $club = "";
    $logo = "";
    $average = 0;
    $average_team = 0;
    $quality = 0;

    try {
        $database = new database();
        $db = $database->getConnection();

        $players = new players_entity($db);
        $stmt = $players->read($arrayParam);
        $num = $stmt->rowCount();

        if ($num > 0) {
            $pos = array();
            $counter = $num;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($counter == $num) {
                    $club = $row['club'];
                    $logo = $row['club_logo'];
                    $average = floatval($row['average']);
                    $average_team = floatval($row['average_team']);
                    $quality = floatval($row['quality']);
                }

                if ($temp == $row['id'] || $temp == null) {
                    array_push($pos, $row['pos']);
                    $players_item = array(
                        "id" => $row['id'],
                        "player_name" => $row['player_name'],
                        "player_photo" => $row['player_photo'],
                        "player_nationality" => $row['player_nationality'],
                        "player_potential" => intval($row['player_potential']),
                        "player_club" => $row['player_club'],
                        "player_price" => intval($row['player_price']),
                        "positions" => $pos
                    );
                    if ($counter == 1) {
                        array_push($players_array, $players_item);
                    }
                } else {
                    array_push($players_array, $players_item);
                    $pos = array();

                    array_push($pos, $row['pos']);
                    $players_item = array(
                        "id" => $row['id'],
                        "player_name" => $row['player_name'],
                        "player_photo" => $row['player_photo'],
                        "player_nationality" => $row['player_nationality'],
                        "player_potential" => intval($row['player_potential']),
                        "player_club" => $row['player_club'],
                        "player_price" => intval($row['player_price']),
                        "positions" => $pos
                    );
                    if ($counter == 1) {
                        array_push($players_array, $players_item);
                    }
                }
                $temp = $row['id'];
                $counter--;
            }

            $products_arr["code"] = 200;
            $products_arr["message"] = "";
            $products_arr["data"] = array(
                "name" => $club,
                "logo" => $logo,
                "average" => $average,
                "average_team" => $average_team,
                "quality" => $quality,
                "team" => $players_array);

            //http_response_code(200);
            //echo json_encode($products_arr);
            return $products_arr;
        } else {
            return array("code" => 404, "message" => "No products found.", "data" => []);
        }
    } catch (PDOException $e) {
        return array("code" => 404, "message" => $e, "data" => []);
    }
}

function getPlayersBySearch($arrayParam)
{
    $products_arr = array();
    $players_array = array();
    $players_item = array();
    $temp = null;
    try {
        $database = new database();
        $db = $database->getConnection();

        $players = new players_entity($db);
        $stmt = $players->read($arrayParam);
        $num = $stmt->rowCount();

        if ($num > 0) {
            $pos = array();
            $counter = $num;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($temp == $row['id'] || $temp == null) {
                    array_push($pos, $row['pos']);
                    $players_item = array(
                        "id" => $row['id'],
                        "player_name" => $row['player_name'],
                        "player_photo" => $row['player_photo'],
                        "player_nationality" => $row['player_nationality'],
                        "player_potential" => intval($row['player_potential']),
                        "player_club" => $row['player_club'],
                        "player_price" => intval($row['player_price']),
                        "positions" => $pos
                    );
                    if ($counter == 1) {
                        array_push($players_array, $players_item);
                    }
                } else {
                    array_push($players_array, $players_item);
                    $pos = array();

                    array_push($pos, $row['pos']);
                    $players_item = array(
                        "id" => $row['id'],
                        "player_name" => $row['player_name'],
                        "player_photo" => $row['player_photo'],
                        "player_nationality" => $row['player_nationality'],
                        "player_potential" => intval($row['player_potential']),
                        "player_club" => $row['player_club'],
                        "player_price" => intval($row['player_price']),
                        "positions" => $pos
                    );
                    if ($counter == 1) {
                        array_push($players_array, $players_item);
                    }
                }
                $temp = $row['id'];
                $counter--;
            }

            $products_arr["code"] = 200;
            $products_arr["message"] = "";
            $products_arr["data"] = array(
                "team" => $players_array);

            return $products_arr;
        } else {
            return array("code" => 404, "message" => "No products found.", "data" => []);
        }
    } catch (PDOException $e) {
        return array("code" => 404, "message" => $e, "data" => []);
    }
}

function getPlayerById($arrayParam)
{
    $products_arr = array();
    $players_array = array();
    $players_item = array();
    $temp = null;
    try {
        $database = new database();
        $db = $database->getConnection();

        $players = new players_entity($db);
        $stmt = $players->read($arrayParam);
        $num = $stmt->rowCount();

        if ($num > 0) {
            $pos = array();
            $counter = $num;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($temp == $row['id'] || $temp == null) {
                    array_push($pos, $row['pos']);
                    $players_item = array(
                        "id" => $row['id'],
                        "player_name" => $row['player_name'],
                        "player_photo" => $row['player_photo'],
                        "player_nationality" => $row['player_nationality'],
                        "player_potential" => intval($row['player_potential']),
                        "player_club" => $row['player_club'],
                        "player_price" => intval($row['player_price']),
                        "positions" => $pos
                    );
                    if ($counter == 1) {
                        array_push($players_array, $players_item);
                    }
                } else {
                    array_push($players_array, $players_item);
                    $pos = array();

                    array_push($pos, $row['pos']);
                    $players_item = array(
                        "id" => $row['id'],
                        "player_name" => $row['player_name'],
                        "player_photo" => $row['player_photo'],
                        "player_nationality" => $row['player_nationality'],
                        "player_potential" => intval($row['player_potential']),
                        "player_club" => $row['player_club'],
                        "player_price" => intval($row['player_price']),
                        "positions" => $pos
                    );
                    if ($counter == 1) {
                        array_push($players_array, $players_item);
                    }
                }
                $temp = $row['id'];
                $counter--;
            }

            $products_arr["code"] = 200;
            $products_arr["message"] = "";
            $products_arr["data"] = array(
                "player" => $players_array);

            return $products_arr;
        } else {
            return array("code" => 404, "message" => "No player with that id.", "data" => []);
        }
    } catch (PDOException $e) {
        return array("code" => 404, "message" => $e, "data" => []);
    }
}

function getCategoryPlayersByPagination($arrayParam, $category)
{
    if ($category == "gold") {
        $playersCount = 1969;
        $lastPage = 66;
    } else if ($category == "silver") {
        $playersCount = 3643;
        $lastPage = 121;
    } else {
        $playersCount = 12069;
        $lastPage = 402;
    }

    $products_arr = array();
    $players_array = array();
    $players_item = array();
    $temp = null;

    $previous = "";
    $next = "";
    try {
        $database = new database();
        $db = $database->getConnection();

        $players = new players_entity($db);
        $stmt = $players->read(array($category => true,
            "page_number" => $arrayParam['page']));
        $num = $stmt->rowCount();
        if ($num > 0) {
            $pos = array();
            $counter = $num;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($temp == $row['id'] || $temp == null) {
                    array_push($pos, $row['pos']);
                    $players_item = array(
                        "id" => $row['id'],
                        "player_name" => $row['player_name'],
                        "player_photo" => $row['player_photo'],
                        "player_nationality" => $row['player_nationality'],
                        "player_potential" => intval($row['player_potential']),
                        "player_club" => $row['player_club'],
                        "player_price" => intval($row['player_price']),
                        "positions" => $pos
                    );
                    if ($counter == 1) {
                        array_push($players_array, $players_item);
                    }
                } else {
                    array_push($players_array, $players_item);
                    $pos = array();

                    array_push($pos, $row['pos']);
                    $players_item = array(
                        "id" => $row['id'],
                        "player_name" => $row['player_name'],
                        "player_photo" => $row['player_photo'],
                        "player_nationality" => $row['player_nationality'],
                        "player_potential" => intval($row['player_potential']),
                        "player_club" => $row['player_club'],
                        "player_price" => intval($row['player_price']),
                        "positions" => $pos
                    );
                    if ($counter == 1) {
                        array_push($players_array, $players_item);
                    }
                }
                $temp = $row['id'];
                $counter--;
            }

            if (intval($arrayParam['page']) == 1) {
                $previous = "None";
                $next = "http://localhost/api/public/players/gold?page=" . (intval($arrayParam['page']) + 1);
            } else if (intval($arrayParam['page']) == $lastPage) {
                $next = "None";
                $previous = "http://localhost/api/public/players/gold?page=" . (intval($arrayParam['page']) - 1);
            } else if (intval($arrayParam['page']) > 0 && intval($arrayParam['page']) != 1) {
                $next = "http://localhost/api/public/players/gold?page=" . (intval($arrayParam['page']) + 1);
                $previous = "http://localhost/api/public/players/gold?page=" . (intval($arrayParam['page']) - 1);
            }

            $products_arr["links"] = array(
                "count" => $playersCount,
                "next" => $next,
                "previous" => $previous
            );
            $products_arr["code"] = 200;
            $products_arr["message"] = "";
            $products_arr["data"] = array(
                "player" => $players_array);

            return $products_arr;
        } else {
            return array("code" => 404, "message" => "No player with that id.", "data" => []);
        }
    } catch (PDOException $e) {
        return array("code" => 404, "message" => $e, "data" => []);
    }
}