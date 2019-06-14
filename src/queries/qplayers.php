<?php
/**
 * Created by PhpStorm.
 * User: RickDAM
 * Date: 14/06/2019
 * Time: 22:19
 */

class qplayers
{
    public $BY_CLUB = "# noinspection SqlNoDataSourceInspection
            SELECT t.*, p.*, pos.pos 
            FROM players p INNER JOIN playerpositions pos ON pos.player_id = p.id 
            INNER JOIN teams t ON t.club = p.player_club 
            WHERE p.player_club LIKE :club 
            ORDER BY p.player_potential DESC";

    public $BY_SEARCH = "-- noinspection SqlDialectInspection
            SELECT p.*, pos.pos 
            FROM players p INNER JOIN playerpositions pos ON pos.player_id = p.id 
            WHERE p.player_name LIKE :search
            ORDER BY p.player_price DESC, p.player_name";

    public $BY_ID = "-- noinspection SqlDialectInspection
            SELECT p.*, pos.pos 
            FROM players p INNER JOIN playerpositions pos ON pos.player_id = p.id 
            WHERE p.id = :id";

    public $BY_GOLD = "-- noinspection SqlDialectInspection
            SELECT p.*, pos.pos FROM (SELECT * FROM players WHERE player_potential >= 75 
            ORDER BY player_potential DESC LIMIT 30 OFFSET :offset)
             p INNER JOIN playerpositions pos ON pos.player_id = p.id";

    public $BY_SILVER = "-- noinspection SqlDialectInspection
            SELECT p.*, pos.pos FROM (SELECT * FROM players WHERE player_potential BETWEEN 70 AND 74 
            ORDER BY player_potential DESC LIMIT 30 OFFSET :offset)
             p INNER JOIN playerpositions pos ON pos.player_id = p.id";

    public $BY_BRONZE = "-- noinspection SqlDialectInspection
            SELECT p.*, pos.pos FROM (SELECT * FROM players WHERE player_potential < 70
            ORDER BY player_potential DESC LIMIT 30 OFFSET :offset)
             p INNER JOIN playerpositions pos ON pos.player_id = p.id";
}