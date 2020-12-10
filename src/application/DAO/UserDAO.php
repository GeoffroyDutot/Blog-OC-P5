<?php


namespace App\DAO;


use App\DTO\UserDTO;

class UserDAO extends DAO
{
    // Gets All Users
    public function getAll(array $filters, $limit = null): array
    {
        $users = [];

        // Retrieves users
        $query = "SELECT * FROM `user` WHERE role = 'ROLE_USER'";

        // Add filter on user's status
        if (isset($filters['is_deactivated'])) {
            $query .= " AND is_deactivated = ".$filters['is_deactivated'];
        }

        // Order user's on last date registered
        $query .= " ORDER BY date_registered DESC ";

        // Add a limit of user's to return
        if (!empty($limit)) {
            $query .= 'LIMIT ' . $limit;
        }

        // Get's data on db
        $req = $this->db->query($query);
        $data = $req->fetchAll(\PDO::FETCH_ASSOC);

        // If no users return empty array
        if (!$data) {
            return $users;
        }

        // Creates array of Users Objects
        foreach ($data as $user) {
            $users[] = new UserDTO($user);
        }

        return $users;
    }

    // Get user  by it's email
    public function getUserByEmail(string $email): ?UserDTO
    {
        // Retrieves user on email
        $req = $this->db->query('SELECT * FROM user WHERE email = \''.$email.'\'');
        $user = $req->fetch(\PDO::FETCH_ASSOC);

        // If user doesn't find return null
        if (empty($user)) {
            return null;
        }

        // Creates and return User's Object
        return new UserDTO($user);
    }

    // Get user by it's pseudo
    public function getUserByPseudo(string $pseudo): ?UserDTO
    {
        // Retrieves user on pseudo
        $req = $this->db->query('SELECT * FROM user WHERE pseudo = \''.$pseudo.'\'');
        $user = $req->fetch(\PDO::FETCH_ASSOC);

        // If user doesn't find return null
        if (empty($user)) {
            return null;
        }

        // Creates and return User's Object
        return new UserDTO($user);
    }

    // Get user by it's id
    public function getUserById(int $id): ?UserDTO
    {
        // Retrieves user on id
        $req = $this->db->query('SELECT * FROM user WHERE id = \''.$id.'\'');
        $user = $req->fetch(\PDO::FETCH_ASSOC);

        // If user doesn't find return null
        if (empty($user)) {
            return null;
        }

        // Creates and return User's Object
        return new UserDTO($user);
    }

    // Creates or updates an user
    public function save(UserDTO $userDTO): bool
    {
        if (!empty($userDTO->getId())) {
            // Set deactivated_at date to null if not a datetime
            $deactivatedAt = $userDTO->getDeactivatedAt() ? $userDTO->getDeactivatedAt()->format('Y-m-d H:i:s') : null;
            // Prepare the request
            $req = $this->db->prepare('UPDATE user SET email=:email, password=:password, pseudo=:pseudo, role=:role, profil_picture=:profilPicture, is_deactivated=:isDeactivated, reason_deactivation=:reasonDeactivation, deactivated_at=:deactivatedAt WHERE id = \''.$userDTO->getId().'\'');
            // Update the user
            $result = $req->execute(['email' => $userDTO->getEmail(), 'password' => $userDTO->getPassword(), 'pseudo' => $userDTO->getPseudo(), 'role' => $userDTO->getRole(), 'profilPicture' => $userDTO->getProfilPicture(), 'isDeactivated' => $userDTO->getIsDeactivated(), 'reasonDeactivation' => $userDTO->getReasonDeactivation(), 'deactivatedAt' => $deactivatedAt]);
        } else {
            // Prepare the request
            $req = $this->db->prepare('INSERT INTO `user`(`email`, `password`, `pseudo`, `role`, `profil_picture`, `date_registered`, `is_deactivated`, `reason_deactivation`, `deactivated_at`) VALUES(:email, :password, :pseudo, :role, :profilPicture, :dateRegistered, :isDeactivated, :reasonDeactivation, :deactivatedAt)');
            // Create the user
            $result = $req->execute(['email' => $userDTO->getEmail(), 'password' => $userDTO->getPassword(), 'pseudo' => $userDTO->getPseudo(), 'role' => 'ROLE_USER', 'profilPicture' => null, 'dateRegistered' => date('Y-m-d H:i:s'), 'isDeactivated' => 0, 'reasonDeactivation' => null, 'deactivatedAt' => null]);
        }

        return $result;
    }
}