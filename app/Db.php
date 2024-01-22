<?php
class Db
{
    private $db;
    private $state;

    public function __construct()
    {
        $this->state = new State();
        $host = "mysql-container";
        $username = "root";
        $password = "root";
        $database = "hive";
        $port = 3306;
        $this->db = new mysqli($host, $username, $password, $database, $port);
        if ($this->db->connect_error) {
            die('Connection failed: ' . $this->db->connect_error);
        }
    }

    public function undoDB(Game $game)
    {
        $stmt = $this->db->prepare('SELECT * FROM moves WHERE id = ' . $game->getLastMove());
        $stmt->execute();
        return $stmt->get_result()->fetch_array();


        //$this->set_state($result[6]);
    }

    public function getLastGameId()
    {
        $result = $this->db->query("SELECT MAX(game_id) AS max_game_id FROM games");

        if ($result && $row = $result->fetch_assoc()) {
            return (int)$row['max_game_id'];
        } else {
            return 0;
        }
    }
    public function saveGame(){
        $query = "INSERT INTO games VALUES ()";

        if ($this->db->query($query)) {
            return true; // Game saved successfully
        } else {
            // Handle the error or return false to indicate failure
            return false;
        }
    }

}