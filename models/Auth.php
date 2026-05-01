<?php
class Auth {
    // АВТОРИЗАЦИЯ 
    public static function findUserByEmail($email) {
        $sql='SELECT * FROM `users` WHERE `email` ="'.$email.'"';
        $db = new Database();
        return $db->getOne($sql);
    }
}

?>