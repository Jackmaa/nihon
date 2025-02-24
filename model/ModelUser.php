<?php

class ModelUser extends Model {
    // Retrieve user details by email
    public function getUser(string $email) {
        $req = $this->getDb()->prepare('SELECT `id_user`, `name`, `email`, `password` FROM `user` WHERE `email` = :email');
        $req->bindParam(':email', $email, PDO::PARAM_STR);
        $req->execute();

        $data = $req->fetch(PDO::FETCH_ASSOC);
        return $data ? new User($data) : null;
    }

    // Check if the user is already connected
    public function isConnected() {
        if ($_SESSION) {
            header('Location: /nihon');
        }
    }

    // Check if the email is already registered
    public function checkUserMail(string $email) {

        $user = $this->getDb()->prepare('SELECT `email` FROM user WHERE `email` = :email');
        $user->bindParam(':email', $email, PDO::PARAM_STR);
        $user->execute();
        $data = $user->fetch(PDO::FETCH_ASSOC);

        if ($data->rowCount() > 0) {
            return false;
        } else {
            return true;
        }
    }

    // Check if the username is already taken
    public function checkUserName(string $username) {

        $user = $this->getDb()->prepare('SELECT `username` FROM user WHERE `username` = :username');
        $user->bindParam(':username', $username, PDO::PARAM_STR);
        $user->execute();
        $data = $user->fetch(PDO::FETCH_ASSOC);

        if ($data->rowCount() > 0) {
            return false;
        } else {
            return true;
        }
    }

    // Create a new user with the provided details
    public function createUser(string $username, string $email, string $password) {

        $user = $this->getDb()->prepare(
            'INSERT INTO
                `user` (`username`, `email`, `password`, `signing_date`)
            VALUES
                (:username, :email, :password, NOW())');

        $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => $this->setCost(0.250)]);
        $user->bindParam(':username', $username, PDO::PARAM_STR);
        $user->bindParam(':email', $email, PDO::PARAM_STR);
        $user->bindParam(':password', $password, PDO::PARAM_STR);
        $user->execute();
    }
}