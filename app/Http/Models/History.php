<?php

namespace App\Http\Models;

use Core\Model;

class History extends Model
{
    protected static $table = "user_history";

    // Thuộc tính để lưu giá trị từ cơ sở dữ liệu
    public $id;
    public $user_id;
    public $song_id;
    // Getter methods
    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getSongId()
    {
        return $this->song_id;
    }

    public function create()
    {
        // Check if a record with the same user_id and song_id exists
        $stmt = $this->connection->prepare("
        DELETE FROM user_history 
        WHERE user_id = :user_id AND song_id = :song_id
    ");
        $stmt->execute([
            'user_id' => $this->user_id,
            'song_id' => $this->song_id,
        ]);

        // Insert the new record
        $stmt = $this->connection->prepare("
        INSERT INTO user_history SET 
            user_id = :user_id, 
            song_id = :song_id, 
            played_at = NOW() 
    ");
        $stmt->execute([
            'user_id' => $this->user_id,
            'song_id' => $this->song_id,
        ]);
    }

    public function getAllData()
    {
        $stmt = $this->connection->prepare("
            SELECT 
                *
            FROM 
                user_history
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
