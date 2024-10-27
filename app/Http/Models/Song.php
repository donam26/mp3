<?php

namespace App\Http\Models;

use Core\Model;

class Song extends Model
{
    protected static $table = "songs";

    // Attributes to hold database values
    public $id;
    public $title;
    public $artistId;
    public $albumId;
    public $genre;
    public $songUrl;
    public $songImg;
    public $releaseDate;
    public $length;
    public $createdAt;

    // Getter methods
    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getArtistId()
    {
        return $this->artistId;
    }

    public function getAlbumId()
    {
        return $this->albumId;
    }
    public function getSongUrl()
    {
        return $this->songUrl;
    }
    public function getSongImg()
    {
        return $this->songImg;
    }
    public function getGenre()
    {
        return $this->genre;
    }

    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getAllData()
    {
        $stmt = $this->connection->prepare("
            SELECT 
                songs.id,
                songs.title,
                songs.genre,
                songs.song_img,
                songs.song_url,
                songs.release_date,
                songs.length,
                songs.album_id,
                songs.artist_id,
                artists.name AS artist_name,
                albums.title AS album_title
            FROM 
                songs
            LEFT JOIN 
                artists ON songs.artist_id = artists.id
            LEFT JOIN 
                albums ON songs.album_id = albums.id
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getArtistByName($name)
    {
        $stmt = $this->connection->prepare("SELECT * FROM artists WHERE name = :name");
        $stmt->execute(['name' => $name]);
        return $stmt->fetchObject('App\Http\Models\Artist'); // Sử dụng tên lớp đầy đủ
    }

    public function createArtist($name)
    {
        $stmt = $this->connection->prepare("INSERT INTO artists (name) VALUES (:name)");
        $stmt->execute(['name' => $name]);
        return $this->getArtistByName($name); // Fetch the newly created artist
    }

    public function getAlbumByTitle($title)
    {
        $stmt = $this->connection->prepare("SELECT * FROM albums WHERE title = :title");
        $stmt->execute(['title' => $title]);
        return $stmt->fetchObject('App\Http\Models\Album');
    }

    public function createAlbum($title)
    {
        $stmt = $this->connection->prepare("INSERT INTO albums (title) VALUES (:title)");
        $stmt->execute(['title' => $title]);
        return $this->getAlbumByTitle($title); // Fetch the newly created album
    }

    public function create()
    {
        $stmt = $this->connection->prepare("
        INSERT INTO songs SET 
            title = :title, 
            artist_id = :artist_id, 
            album_id = :album_id, 
            genre = :genre, 
            release_date = :release_date, 
            length = :length, 
            song_url = :song_url,
            song_img = :song_img
    ");
        $stmt->execute([
            'title' => $this->title,
            'artist_id' => $this->artistId,
            'album_id' => $this->albumId,
            'genre' => $this->genre,
            'release_date' => $this->releaseDate,
            'length' => $this->length,
            'song_url' => $this->songUrl,
            'song_img' => $this->songImg
        ]);
    }



    public function save()
    {
        $stmt = $this->connection->prepare("
        UPDATE songs SET title = :title, artist_id = :artist_id, album_id = :album_id WHERE id = :id
    ");
        $stmt->execute([
            'title' => $this->title,
            'artist_id' => $this->artistId,
            'album_id' => $this->albumId,
            'id' => $this->id
        ]);
    }

    public function deleteRelatedStatistics($song_id)
    {
        $stmt = $this->connection->prepare("DELETE FROM song_statistics WHERE song_id = :song_id");
        $stmt->execute(['song_id' => $song_id]);  // Match placeholder :song_id with key 'song_id' in execute array
    }

    public function deleteRelatedUserHistory($song_id)
    {
        $stmt = $this->connection->prepare("DELETE FROM user_history WHERE song_id = :song_id");
        $stmt->execute(['song_id' => $song_id]);
    }

    // Trong lớp Song
    public function deleteHistoryBySongId($song_id)
    {
        $stmt = $this->connection->prepare("DELETE FROM user_history WHERE song_id = :song_id");
        $stmt->execute(['song_id' => $song_id]);
    }


    public function deleteByArtistId($artist_id)
    {
        // Lấy danh sách tất cả các bài hát của nghệ sĩ
        $stmt = $this->connection->prepare("SELECT id FROM songs WHERE artist_id = :artist_id");
        $stmt->execute(['artist_id' => $artist_id]);
        $songs = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        // Xóa dữ liệu liên quan trong bảng song_statistics
        foreach ($songs as $song_id) {
            $this->deleteStatisticsBySongId($song_id);
            $this->deleteHistoryBySongId($song_id);
        }

        // Xóa các bài hát của nghệ sĩ
        $stmt = $this->connection->prepare("DELETE FROM songs WHERE artist_id = :artist_id");
        return $stmt->execute(['artist_id' => $artist_id]);
    }


    public function deleteStatisticsBySongId($song_id)
    {
        $stmt = $this->connection->prepare("DELETE FROM song_statistics WHERE song_id = :song_id");
        $stmt->execute(['song_id' => $song_id]);
    }
}
