<?php

namespace App\Http\Controllers\Admin;

use App\Http\Middleware\AuthAdminMiddleware;
use App\Http\Models\Album;
use App\Http\Models\Artist;
use App\Http\Models\Song;
use App\Http\Models\User;
use Core\View;

class AdminUserController
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
        $userModel = new User();
        $users = $userModel->getAllData();

        $data = [
            'users' => $users,
        ];

        return View::make('admin/users/index', $data);
    }
    // Edit Song
    public function edit()
    {
        $user_id = $_POST['id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $address = $_POST['address'];
        $role = $_POST['role'];

        $userModel = new User();
        $user = $userModel->find($user_id);

        if ($user) {
            $user->username = $username;
            $user->email = $email;
            $user->password = $password;
            $user->address = $address;
            $user->role = $role;
          
            $user->save(); 
        }
        

        header("Location: /admin/users");
        exit;
    }


    // Delete Song
    public function delete()
    {
        $user_id = $_POST['id'];

        // Initialize the Song model
        $userModel = new User();
        $user = $userModel->find($user_id);

        if ($user) {

            $deleted = $userModel->delete($user_id);
            if ($deleted) {
                echo "Song deleted successfully!";
            } else {
                echo "Failed to delete song.";
            }
        } else {
            echo "Song not found.";
        }

        header("Location: /admin/users");
        exit;
    }
}
