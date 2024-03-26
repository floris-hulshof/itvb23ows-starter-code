<?php

namespace App;

class Ai {
    public function move($moveNumber, $hand, $board){
        $url = 'ai:5000';
        $postData = json_encode([
            "move_number" => $moveNumber,
            "hand" => $hand,
            "board" => $board
        ]);
        $headers = [
            'Content-Type: application/json'
        ];
        $ch = curl_init();
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute cURL session
        $response = curl_exec($ch);


        // Check for errors
        if(curl_errno($ch)) {
            $error_message = curl_error($ch);
            // Handle error
            $response = json_encode(['error' => $error_message]);
        }

        // Close cURL session
        curl_close($ch);

        // Process response
        return json_decode($response);
    }

}