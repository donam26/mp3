<?php

namespace App\Http\Controllers\Api;

use App\Http\Middleware\AuthAdminMiddleware;
use App\Http\Models\Album;
use App\Http\Models\Artist;
use App\Http\Models\History;
use App\Http\Models\Song;
use Core\Response;
use Core\View;

class ApiSongController
{
    protected $middleware;

    public function __construct() {}

    public function index()
    {
        $songModel = new Song();
        $songs = $songModel->getAllData();

        return Response::json($songs);
    }

    public function hotmusic()
    {
        $songModel = new Song();
        $songs = $songModel->getAllData();

        return Response::json($songs);
    }

    public function recently()
    {
        $songModel = new \App\Http\Models\Song();
        $user_id = $_SESSION['user_id']; 
        $viewedSongs = $songModel->getViewedSongsByUser($user_id);
        return Response::json($viewedSongs);
    }

    public function discover()
    {
        $songModel = new Song();
        $songs = $songModel->getAllData();

        return Response::json($songs);
    }

    public function recordHistory() 
    {
        // Decode JSON payload
        $data = json_decode(file_get_contents("php://input"), true);
    
        // Ensure song_id is present in the decoded data
        if (!isset($data['song_id'])) {
            return Response::json(['error' => 'song_id is required'], 400);
        }
    
        $songModel = new History();
        $songModel->user_id = $_SESSION['user_id'];
        $songModel->song_id = $data['song_id'];
        $songModel->create();
    
        return Response::json(['message' => 'History recorded successfully']);
    }
    

    public function random()
{
    $songModel = new Song();
    $songs = $songModel->getAllData();

    // Chọn 3 bài hát ngẫu nhiên
    $randomSongs = array_rand($songs, 3);
    $selectedSongs = [];

    foreach ($randomSongs as $index) {
        $selectedSongs[] = $songs[$index];
    }

    return Response::json($selectedSongs);
}

}
