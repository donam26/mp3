<?php

namespace App\Http\Controllers\Admin;

use App\Http\Middleware\AuthAdminMiddleware;
use App\Http\Models\Album;
use App\Http\Models\Artist;
use App\Http\Models\Song;
use Core\View;

class AdminSongController
{
    protected $middleware;

    public function __construct()
    {
        $this->middleware = new AuthAdminMiddleware();
    }
    public function index()
    {
        if ($this->middleware->handle() !== 'admin') {
            return View::make('index');
            return;
        }
        $songModel = new Song();
        $songs = $songModel->getAllData();

        $artistModel = new Artist();
        $artists = $artistModel->get();
        $albumModel = new Album();
        $albums = $albumModel->get();

        $data = [
            'songs' => $songs,
            'artists' => $artists,
            'albums' => $albums,
        ];

        return View::make('admin/songs/index', $data);
    }

    public function create()
    {
        $song_url = null;
        $img_song_url = null;

        // Xử lý tải lên file nhạc
        if (isset($_FILES['song_file']) && $_FILES['song_file']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['song_file']['tmp_name'];
            $fileName = $_FILES['song_file']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = '/uploads/songs/';
            $song_dest_path = $uploadFileDir . $newFileName;

            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }

            if (move_uploaded_file($fileTmpPath, $song_dest_path)) {
                $song_url = $song_dest_path;
            } else {
                echo "There was an error moving the uploaded song file.";
                return;
            }
        }

        // Xử lý tải lên file ảnh bìa
        if (isset($_FILES['song_img']) && $_FILES['song_img']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['song_img']['tmp_name'];
            $fileName = $_FILES['song_img']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = '/uploads/images/';
            $img_dest_path = $uploadFileDir . $newFileName;

            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }

            if (move_uploaded_file($fileTmpPath, $img_dest_path)) {
                $img_song_url = $img_dest_path;
            } else {
                echo "There was an error moving the uploaded image file.";
                return;
            }
        }

        // Kiểm tra nếu cả file nhạc và ảnh bìa đều được tải lên thành công
        if ($song_url && $img_song_url) {
            // Lưu thông tin bài hát vào cơ sở dữ liệu
            $songModel = new Song();
            $songModel->title = $_POST['title'];
            $songModel->songUrl = $song_url;
            $songModel->songImg = $img_song_url;
            $songModel->artistId = $_POST['artist_id'];
            $songModel->albumId = $_POST['album_id'];
            $songModel->genre = $_POST['genre'];
            $songModel->releaseDate = date("Y-m-d");
            $songModel->create();

            header("Location: /admin/songs");
            exit;
        } else {
            echo "File upload failed.";
        }
    }

    // Edit Song
    public function edit()
    {
        $song_id = $_POST['id'];
        $title = $_POST['title'];
        $artist_name = $_POST['artist_name'];
        $album_title = $_POST['album_title'];

        $songModel = new Song();
        $song = $songModel->find($song_id);

        if ($song) {
            $song->title = $title;
            var_dump($artist_name);
            $artist = $song->getArtistByName($artist_name);
            if (!$artist) {
                $artist = $song->createArtist($artist_name);
            }
            $song->artistId = $artist->id;

            $album = $song->getAlbumByTitle($album_title);
            if (!$album) {
                $album = $song->createAlbum($album_title);
            }
            $song->albumId = $album->id;

            $song->save();
        }


        header("Location: /admin/songs");
        exit;
    }


    // Delete Song
    public function delete()
    {
        $song_id = $_POST['id'];

        // Initialize the Song model
        $songModel = new Song();
        $song = $songModel->find($song_id);

        if ($song) {
            // Delete related records in user_history
            $songModel->deleteRelatedUserHistory($song_id);

            // Delete related records in song_statistics
            $songModel->deleteRelatedStatistics($song_id);

            // Now delete the song itself
            $deleted = $songModel->delete($song_id);
            if ($deleted) {
                echo "Song deleted successfully!";
            } else {
                echo "Failed to delete song.";
            }
        } else {
            echo "Song not found.";
        }

        header("Location: /admin/songs");
        exit;
    }
}
