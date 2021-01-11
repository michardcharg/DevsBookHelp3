<?php
namespace src\handlers;

use \src\models\User;

class LoginHandler{

    public static function checkLogin() {

        // 1º CHECAGEM - se tem uma SESSION COM TOKEN
        if(!empty($_SESSION['token'])) {

            // Se tiver, pego esse TOKEN para VALIDAR se é igual ao BD
            $token = $_SESSION['token'];

            // Procuro no BD se tem um TOKEN igual
            $data = User::select()->where('token', $token)->one();

            // Vou CHECAR se achou no BD algum TOKEN igual ao que ta guardado na SESSION
            if(count($data) > 0) {


                $loggedUser = new User();
                $loggedUser->id = $data['id'];
                $loggedUser->email = $data['email'];
                $loggedUser->name = $data['name'];
                $loggedUser->avatar = $data['avatar'];

                return $loggedUser; //Retorno os dados do USER para usar depois

            }
            
        }

        return false;
    }

    public static function verifyLogin($email, $password) {

        //Busco no BD se tem USER com esse EMAIL
        $user = User::select()->where('email', $email)->one();

        //Se retorna algum resultado, só depois vamos para segunda validação
        if($user) {

            //Uso a PASSWORD_VERIFY para comparar a SENHA enviada do FORM e a que veio do BD
            if(password_verify($password, $user['password'])) {

                //Gero um TOKEN aleatório do USER
                $token = md5(time().rand(0,999).time()); //Uso até o Tempo atual e Numero aleatório

                //MUITO IMPORTANTE, guardar esse novo TOKEN no BD no cadastro deste usuário
                User::update()
                    ->set('token', $token)
                    ->where('email', $email)
                ->execute();

                //Retorno esse TOken para ser guardado na SESSION do usuário
                return $token;
            }
        }

    }

    public static function emailExists($email) {
        
        
        $user = User::select()->where('email', $email)->one();
        return $user ? true : false;


    }

    public static function addUser($name, $email, $password, $birthdate) {

        //Crio o HASH do Password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        //Crio o Token para retornar e ja deixar o user logado
        $token = md5(time().rand(0,999).time());

        //Faço a inserção no BD
        User::insert([
            'email' => $email,
            'password' => $hash,
            'name' => $name,
            'birthdate' => $birthdate,
            'token' => $token
            ])->execute();

            return $token;
    }

}