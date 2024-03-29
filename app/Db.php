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

    public function undoDB($last_move)
    {
        $stmt = $this->db->prepare('SELECT * FROM moves WHERE id = ' . $last_move);
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
        $this->db->prepare('INSERT INTO games VALUES ()')->execute();
        return $this->db->insert_id;
    }
    public function playDB($game_id, $piece, $to, $lastMove, $state){

        $stmt = $this->db->prepare('insert into moves (game_id, type, move_from, move_to, previous_id, state) values (?, "play", ?, ?, ?, ?)');
        $stmt->bind_param('issis', $game_id, $piece, $to, $lastMove, $state);
        $stmt->execute();

        return $this->db->insert_id;
    }
    public function moveDB($from, $to, $last_move, $state)
    {
        $stmt = $this->db->prepare('insert into moves (game_id, type, move_from, move_to, previous_id, state) values (?, "move", ?, ?, ?, ?)');
        $stmt->bind_param('issis', $_SESSION['game_id'], $from, $to, $last_move, $state);
        $stmt->execute();

        return $this->db->insert_id;
    }

    public function passDB($game_id, $last_move, $state)
    {
        $stmt = $this->db->prepare('insert into moves (game_id, type, move_from, move_to, previous_id, state) values (?, "pass", null, null, ?, ?)');
        $stmt->bind_param('iis', $game_id, $last_move, $state);
        $stmt->execute();

        return $this->db->insert_id;
    }
    public function getCurrentGameDB($game_id){
        $stmt = $this->db->prepare('SELECT * FROM moves WHERE game_id = ?');
        $stmt->bind_param('i', $game_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getMoveId($id){
        $stmt = $this->db->prepare('SELECT * FROM moves WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_array();
    }
    public function deleteMoveId($id){
        $stmt = $this->db->prepare('DELETE FROM moves WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }


}