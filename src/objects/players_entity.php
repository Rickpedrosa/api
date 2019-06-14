<?php

/**
 * Created by PhpStorm.
 * User: RickDAM
 * Date: 04/06/2019
 * Time: 22:33
 */
class players_entity
{

    /**
     * @var PDO
     */
    private $conn;
    private $limit = 30;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function read($params)
    {
        $stmt = null;
        $query = new qplayers();

        if (isset($params['club'])) {
            $stmt = $this->conn->prepare($query->BY_CLUB);
            $stmt->execute(array(':club' => $params['club']));
        } else if (isset($params['search'])) {
            $stmt = $this->conn->prepare($query->BY_SEARCH);
            $search = '%' . $params['search'] . '%';
            $stmt->bindParam(':search', $search, PDO::PARAM_STR);
            $stmt->execute();
        } else if (isset($params['id'])) {
            $stmt = $this->conn->prepare($query->BY_ID);
            $stmt->bindParam(':id', $params['id'], PDO::PARAM_INT);
            $stmt->execute();
        } else if (isset($params['gold'])) {
            $offset = (intval($params['page_number']) - 1) * $this->limit;
            $stmt = $this->conn->prepare($query->BY_GOLD);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
        }else if (isset($params['silver'])) {
            $offset = (intval($params['page_number']) - 1) * $this->limit;
            $stmt = $this->conn->prepare($query->BY_SILVER);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
        }else if (isset($params['bronze'])) {
            $offset = (intval($params['page_number']) - 1) * $this->limit;
            $stmt = $this->conn->prepare($query->BY_BRONZE);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
        }

        return $stmt;
    }
}