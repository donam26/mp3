<?php

namespace App\Http\Controllers\Admin;

use App\Http\Middleware\AuthAdminMiddleware;
use App\Http\Models\Album;
use App\Http\Models\Artist;
use App\Http\Models\Song;
use Core\View;

class AdminAlbumController
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
        $albumModel = new Album();
        $albums = $albumModel->getAllData();
        $data = [
            'albums' => $albums,
        ];

        return View::make('admin/albums/index', $data);
    }
    // Edit Song
    public function edit()
    {
        $album_id = $_POST['id'];
        $title = $_POST['title'];
        $release_date = $_POST['release_date'];

        $albumModel = new Album();
        $album = $albumModel->find($album_id);

        if ($album) {
            $album->title = $title;
            $album->release_date = $release_date;
            $album->save(); 
        }
        

        header("Location: /admin/albums");
        exit;
    }


    // Delete Song
    public function delete()
    {
        $album_id = $_POST['id'];

        // Initialize the Song model
        $albumModel = new Album();
        $song = $albumModel->find($album_id);

        if ($song) {
           
            $deleted = $albumModel->delete($album_id);
            if ($deleted) {
                echo "Song deleted successfully!";
            } else {
                echo "Failed to delete song.";
            }
        } else {
            echo "Song not found.";
        }

        header("Location: /admin/albums");
        exit;
    }
}
