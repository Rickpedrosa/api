<?php
/**
 * Created by PhpStorm.
 * User: RickDAM
 * Date: 15/06/2019
 * Time: 9:35
 */

class qteams
{
    public $BY_ALL = "SELECT club, club_logo AS logo, average, average_team, quality FROM teams ORDER BY quality DESC";

    public $BY_SEARCH = "SELECT club, club_logo AS logo, average, average_team, quality FROM teams WHERE club LIKE :search_param";

    public $BY_RANDOM = "SELECT t.*, p.*, pos.pos 
      FROM (SELECT * FROM teams  WHERE quality BETWEEN 3 AND 4 ORDER BY RAND() LIMIT :random) t 
      INNER JOIN players p ON p.player_club = t.club 
      INNER JOIN playerpositions pos ON p.id = pos.player_id 
      ORDER BY t.club, p.player_potential DESC, p.player_name";

    public function BY_FILTER($filterArray)
    {
        return "SELECT club, club_logo AS logo, average, average_team, quality FROM teams 
            WHERE " . $this->getWhereClause($filterArray) . " ORDER BY quality DESC LIMIT :lim";
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