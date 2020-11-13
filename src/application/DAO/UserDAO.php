<?php


namespace App\DAO;


use App\DTO\UserDTO;
use http\Client\Curl\User;

class UserDAO extends DAO {

    public function getAll(array $filters, $limit = null) : array {
        $db = $this->connectDb();
        $users = [];

        $query = "SELECT * FROM `user` WHERE role = 'ROLE_USER'";

        if (isset($filters['is_deactivated'])) {
            $query .= " AND is_deactivated = ".$filters['is_deactivated'];
        }

        $query .= " ORDER BY date_registered DESC ";

        if (!empty($limit)) {
            $query .= 'LIMIT ' . $limit;
        }

        $req = $db->query($query);

        $data = $req->fetchAll(\PDO::FETCH_ASSOC);

        if (!$data) {
            return $users;
        }

        foreach ($data as $user) {
            $users[] = new UserDTO($user);
        }

        return $users;
    }

    public function save(UserDTO $userDTO) {
        $db = $this->connectDb();

        $deactivatedAt = $userDTO->getDeactivatedAt() ? $userDTO->getDeactivatedAt()->format('Y-m-d H:i:s') : null;

        if (!empty($userDTO->getId())) {
            $req = $db->prepare('UPDATE user SET email=:email, password=:password, pseudo=:pseudo, role=:role, profil_picture=:profilPicture, is_deactivated=:isDeactivated, reason_deactivation=:reasonDeactivation, deactivated_at=:deactivatedAt WHERE id = \''.$userDTO->getId().'\'');
            $result = $req->execute(['email' => $userDTO->getEmail(), 'password' => $userDTO->getPassword(), 'pseudo' => $userDTO->getPseudo(), 'role' => $userDTO->getRole(), 'profilPicture' => $userDTO->getProfilPicture(), 'isDeactivated' => $userDTO->getIsDeactivated(), 'reasonDeactivation' => $userDTO->getReasonDeactivation(), 'deactivatedAt' => $deactivatedAt]);
        } else {
            $req = $db->prepare('INSERT INTO `user`(`email`, `password`, `pseudo`, `role`, `profilPicture`, `date_registered`, `is_deactivated`, `reason_deactivation`, `deactivated_at`) VALUES(:email, :password, :pseudo, :role, :dateRegistered, :isDeactivated, :reasonDeactivation, :deactivatedAt)');
            $result = $req->execute(['email' => $userDTO->getEmail(), 'password' => $userDTO->getPassword(), 'pseudo' => $userDTO->getPseudo(), 'role' => $userDTO->getRole(), 'profilPicture' => $userDTO->getProfilPicture(), 'dateRegistered' => $userDTO->getDateRegistered()->format('Y-m-d H:i:s'), 'isDeactivated' => $userDTO->getIsDeactivated(), 'reasonDeactivation' => $userDTO->getReasonDeactivation(), 'deactivatedAt' => $deactivatedAt]);
        }

        return $result;
    }

    public function getUserByEmail(string $email): ?UserDTO
    {
        $db = $this->connectDb();

        $req = $db->query('SELECT * FROM user WHERE email = \''.$email.'\'');
        $user = $req->fetch(\PDO::FETCH_ASSOC);

        if (empty($user)) {
            return null;
        }

        return new UserDTO($user);
    }

    public function getUserByPseudo(string $pseudo): ?UserDTO {
        $db = $this->connectDb();

        $req = $db->query('SELECT * FROM user WHERE pseudo = \''.$pseudo.'\'');
        $user = $req->fetch(\PDO::FETCH_ASSOC);

        if (empty($user)) {
            return null;
        }

        return new UserDTO($user);
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