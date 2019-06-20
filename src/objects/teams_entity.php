<?php

/**
 * Created by PhpStorm.
 * User: RickDAM
 * Date: 03/06/2019
 * Time: 13:01
 */
class Team
{

    /**
     * @var PDO
     */
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function read($params)
    {
        $stmt = null;
        $query = new qteams();
        $limit = isset($params['limit']) ? $params['limit'] : 30;

        if (isset($params['all'])) {
            $stmt = $this->conn->prepare($query->BY_ALL);
            $stmt->execute();
        } else if (isset($params['search'])) {
            $search_param = "%".$params['search'] . "%";
            $stmt = $this->conn->prepare($query->BY_SEARCH);
            $stmt->execute(array(':search_param' => $search_param));
        } else if (isset($params['random'])) {
            $stmt = $this->conn->prepare($query->BY_RANDOM);
            $stmt->bindParam(':random', intval($params['random']), PDO::PARAM_INT);
            $stmt->execute();
        } else if (isset($params['filter'])) {
            $stmt = $this->conn->prepare($query->BY_FILTER($params['filter']));
            $stmt->bindParam(':lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
        }

        return $stmt;
    }
}