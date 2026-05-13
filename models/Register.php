<?php
class Register{
    //----------register
    /*public static function registerUser() {
        $controll=array(0=>false, 1=>'error');
        if(isset($_POST['save'])) {
            $errorString="";
            $name = $_POST['name'];
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            if(!$email) {
                $errorString.="Invalid email address<br />";
            }
            $password = $_POST['password'];
            $confirm = $_POST['confirm'];
            if (!$password || !$confirm || mb_strlen($password) < 6) {
                $errorString.="Password must be at least 6 characters long<br />";
            }
            if($password!=$confirm) {
                $errorString.="Passwords do not match<br />";
            }
            if ( mb_strlen($errorString) ==0 ) {
                $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $date=Date("Y-m-d");
                $sql="INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES (NULL, ?, ?, ?, 'user', ?)";
                $db = new Database();
                $params = array($name, $email, $passwordHash, $date);
                $item = $db->executeRun($sql, $params);
                if($item)
                    $controll=array(0=>true);
                else
                    $controll=array(0=>false, 1=>'error'); 
            }
            else
            {
                $controll=array(0=>false, 1=>$errorString);
            }
        }
        return $controll;
    }*/
    public static function saveUser($cleanData, $db = null) {
        $db = $db ?? new Database();

        // Проверка уникальности email
        $user = $db->getOne("SELECT * FROM users WHERE email = ?", [$cleanData['email']]);
        if ($user) {
            return ['success' => false, 'errors' => ['Email exists already']];
        }

        // Проверка уникальности username
        $user = $db->getOne("SELECT * FROM users WHERE username = ?", [$cleanData['name']]);
        if ($user) {
            return ['success' => false, 'errors' => ['Username exists already']];
        }

        $query = "INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES (NULL, ?, ?, ?, 'user', ?)";
        $params = [
            $cleanData['name'],
            $cleanData['email'],
            password_hash($cleanData['password'], PASSWORD_DEFAULT),
            date("Y-m-d")
        ];
        $result = $db->executeRun($query, $params);
        if ($result) {
            $userId = (int)$db->getLastInsertId();
            return [
                'success' => true,
                'user' => [
                    'id' => $userId,
                    'username' => $cleanData['name'],
                    'role' => 'user'
                ]
            ];
        } else {
            return ['success' => false, 'errors' => ['Database error: Unable to save user']];
        }
    }
}
?>