<?php


namespace App\DAO;


use App\DTO\UserDTO;

class UserDAO extends DAO {
    public function save(UserDTO $user) {
        $db = $this->connectDb();

        $req = $db->prepare('INSERT INTO `user`(`email`, `password`, `pseudo`, `role`) VALUES(:email, :password, :pseudo, :role)');
        $user = $req->execute(['email' => $user->getEmail(), 'password' => $user->getPassword(), 'pseudo' => $user->getPseudo(), 'role' => 'ROLE_USER']);
        return $user;
    }

    public function getUserByEmail(string $email) {
        $db = $this->connectDb();

        $req = $db->query('SELECT * FROM user WHERE email = \''.$email.'\'');
        $user = $req->fetchObject('App\DTO\UserDTO');

        return $user;
    }
}