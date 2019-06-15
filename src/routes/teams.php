<?php /** @noinspection PhpUndefinedMethodInspection */

/**
 * Created by PhpStorm.
 * User: RickDAM
 * Date: 14/06/2019
 * Time: 18:54
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/teams', function (Request $request, Response $response) {
    if (isset($request->getQueryParams()['random'])) {
        $jsonData = getClubsWithSquad($request->getQueryParams());
    } else {
        $jsonData = getClubs($request->getQueryParams());
    }
    return $response->withJson($jsonData, $jsonData['code']);
});


function getClubs($params)
{
    $products_arr = array();
    $products_arr["data"] = array();
    try {
        $database = new Database();
        $db = $database->getConnection();
        $teams = new Team($db);
        $stmt = $teams->read($params);
        $num = $stmt->rowCount();

        if ($num > 0 && $num != 1) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $product_item = array(
                    "club" => $row['club'],
                    "logo" => $row['logo'],
                    "average" => $row['average'],
                    "average_team" => $row['average_team'],
                    "quality" => $row['quality']);

                array_push($products_arr["data"], $product_item);
            }
            $products_arr["code"] = 200;
            $products_arr["message"] = "";
            return $products_arr;
        } else if ($num == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row['club'] != null) {
                $product_item = array(
                    "club" => $row['club'],
                    "logo" => $row['logo'],
                    "average" => $row['average'],
                    "average_team" => $row['average_team'],
                    "quality" => $row['quality']);
                array_push($products_arr["data"], $product_item);

                $products_arr["code"] = 200;
                $products_arr["message"] = "";
                return $products_arr;
            } else {
                return array("code" => 404, "message" => "No such club in the database.", "data" => []);
            }
        } else {
            return array("code" => 404, "message" => "No products found.", "data" => []);
        }
    } catch (PDOException $e) {
        return array("code" => 404, "message" => $e, "data" => []);
    }
}

function getClubsWithSquad($params)
{
    $products_arr = array();
    $products_arr["data"] = array();
    $club_array = array();
    $players_array = array();
    $club_item = array();
    $player_item = array();
    $pos = array();

    $temp_player = null;
    $temp_club = null;
    try {
        $database = new Database();
        $db = $database->getConnection();
        $teams = new Team($db);
        $stmt = $teams->read($params);
        $num = $stmt->rowCount();
        $counter = $num;

        if ($num > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($temp_club == null || ($temp_club['name'] == $row['club'])) {
                    if ($temp_player == null || ($temp_player['id'] == $row['id'])) {
                        array_push($pos, $row['pos']);
                    } else {
                        array_push($players_array, $temp_player);
                        $pos = array();
                        array_push($pos, $row['pos']);
                    }
                } else {
                    $club_item = $temp_club + array("team" => $players_array);
                    array_push($club_array, $club_item);

                    $players_array = array();
                    $pos = array();
                    array_push($pos, $row['pos']);
                }

                if ($counter == 1) {
                    $last_player = array(
                        "id" => $row['id'],
                        "player_name" => $row['player_name'],
                        "player_photo" => $row['player_photo'],
                        "player_nationality" => $row['player_nationality'],
                        "player_potential" => intval($row['player_potential']),
                        "player_club" => $row['player_club'],
                        "player_price" => intval($row['player_price']),
                        "positions" => $pos
                    );
                    array_push($players_array, $last_player);
                    $club_item = $temp_club + array("team" => $players_array);
                    array_push($club_array, $club_item);

                    $players_array = array();
                    $pos = array();
                    array_push($pos, $row['pos']);
                }

                $temp_club = array(
                    "name" => $row['club'],
                    "logo" => $row['club_logo'],
                    "average" => $row['average'],
                    "average_team" => $row['average_team'],
                    "quality" => $row['quality']);
                $temp_player = array(
                    "id" => $row['id'],
                    "player_name" => $row['player_name'],
                    "player_photo" => $row['player_photo'],
                    "player_nationality" => $row['player_nationality'],
                    "player_potential" => intval($row['player_potential']),
                    "player_club" => $row['player_club'],
                    "player_price" => intval($row['player_price']),
                    "positions" => $pos
                );
                $counter--;
            }
            $products_arr["code"] = 200;
            $products_arr["message"] = "";
            $products_arr["data"] = array(
                "clubs" => $club_array);

            return $products_arr;
        } else {
            return array("code" => 404, "message" => "No products found.", "data" => []);
        }
    } catch (PDOException $e) {
        return array("code" => 404, "message" => $e, "data" => []);
    }
}