<?php

namespace App\Http\Controllers\Admin;

use App\Http\Middleware\AuthAdminMiddleware;
use App\Http\Models\Album;
use App\Http\Models\Artist;
use App\Http\Models\Song;
use Core\View;

class AdminArtistController
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
        $artistModel = new Artist();
        $artists = $artistModel->getAllData();
        $albumModel = new Album();
        $albums = $albumModel->get();

        $data = [
            'artists' => $artists,
            'albums' => $albums,
        ];

        return View::make('admin/artists/index', $data);
    }
    // Edit Song
    public function edit()
    {
        $artist = $_POST['id'];
        $name = $_POST['name'];
        $bio = $_POST['bio'];

        $artistModel = new Artist();
        $artist = $artistModel->find($artist);

        if ($artist) {
            $artist->name = $name;
            $artist->bio = $bio;
            $artist->save(); 
        }

        header("Location: /admin/artists");
        exit;
    }


    // Delete Song
    public function delete()
    {
        $artist_id = $_POST['id'];
    
        $artistModel = new Artist();
        $artist = $artistModel->find($artist_id);
    
        if ($artist) {
            // Xóa tất cả các bài hát liên quan đến nghệ sĩ trước
            $songModel = new Song();
            $songModel->deleteByArtistId($artist_id);
    
            // Sau đó xóa nghệ sĩ
            $deleted = $artistModel->delete($artist_id);
            if ($deleted) {
                echo "Artist and related songs deleted successfully!";
            } else {
                echo "Failed to delete artist.";
            }
        } else {
            echo "Artist not found.";
        }
    
        header("Location: /admin/artists");
        exit;
    }

    public function create()
    {
        $img_song_url = null;
        // Xử lý tải lên file ảnh bìa
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
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
        if ($img_song_url) {
            // Lưu thông tin bài hát vào cơ sở dữ liệu
            $songModel = new Artist();
            $songModel->name = $_POST['name'];
            $songModel->image = $img_song_url;
            $songModel->bio = $_POST['bio'];
            $songModel->create();

            header("Location: /admin/songs");
            exit;
        } else {
            echo "File upload failed.";
        }
    }

    
}
