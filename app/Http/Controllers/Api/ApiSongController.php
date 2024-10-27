<?php

namespace App\Http\Controllers\Api;

use App\Http\Middleware\AuthAdminMiddleware;
use App\Http\Models\Album;
use App\Http\Models\Artist;
use App\Http\Models\Song;
use Core\Response;
use Core\View;

class ApiSongController
{
    protected $middleware;

    public function __construct()
    {
    }
    public function index()
    {
        $songModel = new Song();
        $songs = $songModel->getAllData();

        return Response::json($songs);
    }
}
