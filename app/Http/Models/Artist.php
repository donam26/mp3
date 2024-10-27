<?php

namespace App\Http\Models;

use Core\Model;

class Artist extends Model
{
    protected static $table = "artists";

    // Attributes to hold database values
    public $id;
    public $name;
    public $bio;
    public $image;

    public function getId()
    {
        return $this->id;
    }

    // Getter for artist name
    public function getName()
    {
        return $this->name;
    }

    // Getter for artist bio
    public function getBio()
    {
        return $this->bio;
    }
    public function getImage()
    {
        return $this->image;
    }

    public function songs()
    {
        // Kiểm tra kết nối cơ sở dữ liệu từ Model (nếu có) hoặc từ chính lớp Artist
        if (!$this->connection) {
            throw new \Exception("Database connection not established");
        }

        $stmt = $this->connection->prepare("SELECT * FROM songs WHERE artist_id = :id");
        $stmt->execute(['id' => $this->id]);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, 'App\Http\Models\Song'); // Đảm bảo sử dụng tên đầy đủ
    }

    public function getArtistByName($name)
    {
        if (!$this->connection) {
            throw new \Exception("Database connection not established");
        }

        $stmt = $this->connection->prepare("SELECT * FROM artists WHERE name = :name");
        $stmt->execute(['name' => $name]);
        return $stmt->fetchObject('App\Http\Models\Artist'); // Sử dụng tên đầy đủ
    }

    public function getAllData()
    {
        $stmt = $this->connection->prepare("
            SELECT 
                *
            FROM 
                artists
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function save()
    {
        $stmt = $this->connection->prepare("
        UPDATE artists SET name = :name, bio = :bio WHERE id = :id
    ");
        $stmt->execute([
            'name' => $this->name,
            'bio' => $this->bio,
            'id' => $this->id,
        ]);
    }

    // Trong lớp Song
    public function deleteByArtistId($artist_id)
    {
        $stmt = $this->connection->prepare("DELETE FROM songs WHERE artist_id = :artist_id");
        $stmt->execute(['artist_id' => $artist_id]);
    }

    public function create()
    {
        $stmt = $this->connection->prepare("
        INSERT INTO artists SET 
            name = :name, 
            bio = :bio, 
            image = :image
    ");
        $stmt->execute([
            'name' => $this->name,
            'bio' => $this->bio,
            'image' => $this->image
        ]);
    }
}
