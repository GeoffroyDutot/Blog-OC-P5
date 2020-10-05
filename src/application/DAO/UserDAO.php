<?php


namespace App\DAO;


use App\DTO\UserDTO;

class UserDAO extends DAO {

    public function getAll($limit = null) : array {
        $db = $this->connectDb();
        $users = [];

        $query = "SELECT * FROM `user` WHERE role = 'ROLE_USER' ORDER BY date_registered DESC ";

        if ($limit) {
            $limit = 'LIMIT ' . $limit;
        }

        $req = $db->query($query . $limit);

        $data = $req->fetchAll(\PDO::FETCH_ASSOC);

        if (!$data) {
            return $users;
        }

        foreach ($data as $user) {
            $users[] = new UserDTO($user);
        }

        return $users;
    }

    public function save(UserDTO $user) {
        $db = $this->connectDb();

        $req = $db->prepare('INSERT INTO `user`(`email`, `password`, `pseudo`, `role`, `date_registered`) VALUES(:email, :password, :pseudo, :role, :dateRegistered)');
        $user = $req->execute(['email' => $user->getEmail(), 'password' => $user->getPassword(), 'pseudo' => $user->getPseudo(), 'role' => 'ROLE_USER', 'dateRegistered' => date('Y-m-d H:i:s')]);
        return $user;
    }

    public function getUserByEmail(string $email) {
        $db = $this->connectDb();

        $req = $db->query('SELECT * FROM user WHERE email = \''.$email.'\'');
        $user = $req->fetch(\PDO::FETCH_ASSOC);

        if (!empty($user)) {
            $userDTO = new UserDTO($user);
            if ($userDTO) {
                return $userDTO;
            }
        }

        return false;
    }

    public function getUserByPseudo(string $pseudo) {
        $db = $this->connectDb();

        $req = $db->query('SELECT * FROM user WHERE pseudo = \''.$pseudo.'\'');
        $user = $req->fetch(\PDO::FETCH_ASSOC);
        if (!empty($user)) {
            $userDTO = new UserDTO($user);
            if ($userDTO) {
                return $userDTO;
            }
        }

        return false;
    }

    public function getUserById(int $id) {
        $db = $this->connectDb();

        $req = $db->query('SELECT * FROM user WHERE id = \''.$id.'\'');
        $user = $req->fetch(\PDO::FETCH_ASSOC);
        if (!empty($user)) {
            $userDTO = new UserDTO($user);
            if ($userDTO) {
                return $userDTO;
            }
        }

        return false;
    }
}