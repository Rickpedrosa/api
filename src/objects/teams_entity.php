<?php

/**
 * Created by PhpStorm.
 * User: RickDAM
 * Date: 03/06/2019
 * Time: 13:01
 */
class Team
{

    // database connection and table name
    /**
     * @var PDO
     */
    private $conn;
    private $table_name = "teams";

    // object properties
    public $club;
    public $logo;
    public $average;
    public $average_team;
    public $quality;

    //call parameters
    public $params = array();
    public $club_param;
    public $all;
    public $search_param;

    // constructor with $database as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    function read()
    {
        $stmt = null;

        $all = isset($_GET['all']) ? $_GET['all'] : false;
        $search_param = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : null;
        $random = isset($_GET['random']) ? $_GET['random'] : null;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 20;
        $clubs_used = isset($_GET['clubs_used']) ? $_GET['clubs_used'] : null;
        $filter = isset($_GET['filter']) ? $_GET['filter'] : null;

        if (boolval($all)) {
            $query = "SELECT club, club_logo AS logo, average, average_team, quality FROM " . $this->table_name . " ORDER BY quality DESC";
            $stmt = $this->conn->prepare($query);
            //$stmt->bindParam(':lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
        } else if ($search_param != null) {
            $query = "SELECT club, club_logo AS logo, average, average_team, quality FROM " . $this->table_name . " WHERE club LIKE :search_param";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(array(':search_param' => $search_param));
        } else if ($random != null) {
            $query = "SELECT club, club_logo AS logo, average, average_team, quality FROM " . $this->table_name . " ORDER BY RAND() LIMIT :random";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':random', $random, PDO::PARAM_INT);
            $stmt->execute();
        } else if ($clubs_used != null) {
            $query = "SELECT club, club_logo AS logo, average, average_team, quality FROM " . $this->table_name . " 
            WHERE club NOT IN (" . $this->getParsedArray($clubs_used) . ") ORDER BY quality DESC LIMIT :lim";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
        } else if ($filter != null) {
            $query = "SELECT club, club_logo AS logo, average, average_team, quality FROM " . $this->table_name . " 
            WHERE " . $this->getWhereClause($filter) . " ORDER BY quality DESC LIMIT :lim";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            //DEFAULT
            $query = "SELECT club, club_logo AS logo, average, average_team, quality FROM " . $this->table_name . " WHERE club LIKE :arsenal";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(array(':arsenal' => 'arsenal'));
        }


        return $stmt;
    }

    function getParsedArray($arrayToParse)
    {
        $arrayToParse = explode(',', $arrayToParse);
        foreach ($arrayToParse as $key => $value) {
            $arrayToParse[$key] = '\'' . $value . '\'';
        }
        return implode(',', $arrayToParse);
    }

    function getWhereClause($rawArray)
    {
        $filter = array();
        $row = 0;
        foreach ($rawArray as $key => $value) {
            $val = explode(':', $value)[0];
            $signal = explode(':', $value)[1];
            if (isset($rawArray[$key])) {
                if ($row > 0) {
                    $arrayVal = 'AND ' . $key . ' ' . $signal . ' ' . $val . ' ';
                    array_push($filter, $arrayVal);
                } else {
                    $arrayVal = $key . ' ' . $signal . ' ' . $val . ' ';
                    array_push($filter, $arrayVal);
                }
            }
            $row++;
        }
        return implode('', $filter);
    }
}