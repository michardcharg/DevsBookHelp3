<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\LoginHandler;

class LoginController extends Controller {

   public function signin() {
        //Recebo a Mensagens de Erro da checkagem do Login para Enviar e Mostrar na Página
        $flash = '';

        //Olho se tem alguma mensagem guardada na SESSION
        if(!empty($_SESSION['flash'])) {
            
            //Pego essa Menssagem e Guardo na VAR FLASH
            $flash = $_SESSION['flash'];
            //Apago essa mensagem para aparece apenas uma veez
            $_SESSION['flash'] = '';
        }


       $this->render('signin', [
           'flash' => $flash //Envio para o View e Página Login
       ]);
   }

   public function signinAction() {
       //Vamos receber os dados do formulário de login
       $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
       $password = filter_input(INPUT_POST, 'password');

       //Se todos os 2 tiverem ok
       if($email && $password) {

            //Mando para funçãao VERIFYLOGIN os dados EMAIL e PASS e ele vai retornar ou o TOKEN ou como FALSE
            $token = LoginHandler::verifyLogin($email, $password);

            //Se retornoou correto e não FALSE
            if($token) {
                $_SESSION['token'] = $token; //Guardo na SESSION o TOKEN para futuramente

                //Se tudo tiver ok no Login, vai pra Home da RedeSocial
                $this->redirect('/');


            } else {

                //Armazeno a mensagem para exibir na Tela, guardado na SESSION
                $_SESSION['flash'] = 'E-mail e/ou senha não conferem';
                $this->redirect('/login'); //Mando pra página de login

            }

       } else {
           $this->redirect('/login');
       }

   }

   public function signup() {
        //Recebo a Mensagens de Erro da checkagem do Login para Enviar e Mostrar na Página
        $flash = '';

        //Olho se tem alguma mensagem guardada na SESSION
        if(!empty($_SESSION['flash'])) {
            
            //Pego essa Menssagem e Guardo na VAR FLASH
            $flash = $_SESSION['flash'];
            //Apago essa mensagem para aparece apenas uma veez
            $_SESSION['flash'] = '';
        }


       $this->render('signup', [
           'flash' => $flash //Envio para o View e Página Login
       ]);
   }

   public function signupAction() {
       //Vamos receber os dados do formulário de login
       $name = filter_input(INPUT_POST, 'name');
       $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
       $password = filter_input(INPUT_POST, 'password');
       $birthdate = filter_input(INPUT_POST, 'birthdate');

       if($name && $email && $password && $birthdate) {

                //Validar a Data de Nascimento, transformando em formato Universal
                $birthdate = explode('/', $birthdate);
                    if(count($birthdate) != 3){
                        $_SESSION['flash'] = 'Data de Nascimento INVÁLIDA 1';
                        $this->redirect('/cadastro');
                    }
                    //inverto a ordem para deixar formato Universal
                    $birthdate = $birthdate[2].'-'.$birthdate[1].'-'.$birthdate[0];

                    //A função SRTROTIME transforma a string da data acima em tempo. 
                    //Se retornar false, tem problema na dta nascimento
                    if(strtotime($birthdate) === false){
                        $_SESSION['flash'] = 'Data de Nascimento INVÁLIDA 2';
                        $this->redirect('/cadastro');
                    }
                
                    //Depois de validado a DT NASCI segue o cadastro

                    //Ver se tem um E-mail Cadastrado igual
                    if(LoginHandler::emailExists($email) === false){
                        //Envio para func as infos para add no BD
                        //Ja retorno com o Token atual para o user seguir navegando depois do cadastro
                        $token = LoginHandler::addUser($name, $email, $password, $birthdate);
                        $_SESSION['token'] = $token; //Ja gravo o Token na SESSION
                        $this->redirect('/');
                    
                        
                    } else {
                        $_SESSION['flash'] = 'E-mail ja cadastrado!';
                        $this->redirect('/cadastro');
                    }


       } else {
           $this->redirect('/cadastro');
       }


   }
    

}