<?php 

namespace App\Http\Models;

use Core\Model;

class User extends Model {
    protected static $table = "users";

    // Thuộc tính để lưu giá trị từ cơ sở dữ liệu
    public $id;
    public $email;
    public $username;
    public $password;
    public $address;
    public $role;

    // Getter methods
    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getName()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getRole()
    {
        return $this->role;
    }

    // Setter methods
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($username)
    {
        $this->username = $username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function save()
    {
        $stmt = $this->connection->prepare("
        UPDATE users SET username = :username, email = :email, address = :address, password = :password,  role = :role WHERE id = :id
    ");
        $stmt->execute([
            'username' => $this->username,
            'email' => $this->email,
            'address' => $this->address,
            'password' => $this->password,
            'role' => $this->role,
            'id' => $this->id
        ]);
    }

    public function getAllData()
    {
        $stmt = $this->connection->prepare("
            SELECT 
                *
            FROM 
                users
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
