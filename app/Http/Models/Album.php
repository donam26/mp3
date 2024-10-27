<?php

namespace App\Http\Models;

use Core\Model;

class Album extends Model
{
    protected static $table = "albums";

    // Define private attributes for album properties (matching database columns)
    public $id; // Ensure this matches the database column name
    public $title;
    public $release_date;
    public $image;

    // Getter for album ID
    public function getId()
    {
        return $this->id;
    }

    // Getter for album name
    public function getTitle()
    {
        return $this->title;
    }

    // Getter for album release date
    public function getReleaseDate()
    {
        return $this->release_date;
    }
    public function getImage()
    {
        return $this->image;
    }

    // Method to get all songs in the album
    public function songs()
    {
        $instance = new Song();
        $stmt = $instance->connection->prepare("SELECT * FROM songs WHERE id = :id");
        $stmt->execute(['id' => $this->id]);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, Song::class);
    }

    public function getAllData()
    {
        $stmt = $this->connection->prepare("
            SELECT 
                *
            FROM 
                albums
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function save()
    {
        $stmt = $this->connection->prepare("
        UPDATE albums SET title = :title, release_date = :release_date WHERE id = :id
    ");
        $stmt->execute([
            'title' => $this->title,
            'release_date' => $this->release_date,
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
          INSERT INTO albums SET 
              title = :title, 
              release_date = :release_date, 
              image = :image
      ");
        $stmt->execute([
            'title' => $this->title,
            'release_date' => $this->release_date,
            'image' => $this->image
        ]);
    }
}
