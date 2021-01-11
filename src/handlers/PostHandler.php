<?php
namespace src\handlers;

use \src\models\Post;
use \src\models\User;
use \src\models\UserRelation;

class PostHandler { 

    public static function addPost($idUser, $type, $body) {
        //Limpo o texto da postagem
        //$body = trim($body);
        $body = trim(strip_tags($body));

        if(!empty($idUser) && !empty($body)) {
            
            Post::insert([
                'id_user' => $idUser,
                'type' => $type,
                'created_at' => date('Y-m-d H:i:s'),
                'body' => $body
            ])->execute();
        }

    }

    public static function getHomeFeed($idUser, $page) {
        //VAR para armazenar o número de POSTAGENS que eu quero que apareça POR PÁGINA
        $perPage = 2;


        // 1. pegar lista de usuários que EU sigo.
        $userList = UserRelation::select()->where('user_from', $idUser)->get();
        $users = [];

        foreach($userList as $userItem) {
            $users[] = $userItem['user_to']; //Adiciono no ARRAY os IDS dos user que EU sigo
        }
            //Eu tb posso ver minhas próprias postagens, então adiciono meu PRÓPRIO ID no ARRAY
        $users[] = $idUser;

        //print_r($users);


        // 2. pegar os posts dessa galera ordenado pela data.
            //Pego do BD na tabelaas POSTS todas as postagens dos Users (ID 1 ou ID 2 ou etc) que são os User que EU sigo
            //E ORDENO pela DATA DECRESCENTE
        $postList = Post::select()
            ->where('id_user', 'in', $users)
            ->orderBy('created_at', 'desc')
            ->page($page, $perPage) //O segundo parametro diz QUANTOS ELEMENTOS POR PÁGINA
        ->get();

        //faço uma OUTRA PESQUISA com os mesmos PARÂMETROS e faço a contagem dos resultados
        //Para poder gerar o número de PÁGINAS correto
        $total = Post::select()
            ->where('id_user', 'in', $users)
            ->count();
        $pageCount = ceil($total / $perPage); //Arredondo o resultado pra cima / Agora sei quantas páginas vou precisar

        // 3. transformar o resultado em objetos dos models.

        $posts = []; //Criei o ARRAY para armazenar as postagens

        foreach($postList as $postItem) {
            $newPost = new Post();
            $newPost->id = $postItem['id'];
            $newPost->type = $postItem['type'];
            $newPost->created_at = $postItem['created_at'];
            $newPost->body = $postItem['body'];
            $newPost->mine = false; //Retorna FALSE por padrão

            // Teste para saber se o Post Exibido no Feed é do User Logado
            //Para poder ativar a opção de Excluir o Post por exemplo
            if($postItem['id_user'] == $idUser) {
                $newPost->mine = true;
            }
            

            // 4. preencher as informações adicionais no post.
                //Busco no BD Tabela USERS os dados do USER que fez a Postagem
            $newUser = User::select()->where('id', $postItem['id_user'])->one();
                //Preencho com os Dados Completo do User que fez cada Postagem
            $newPost->user = new User();
            $newPost->user->id = $newUser['id'];
            $newPost->user->name = $newUser['name'];
            $newPost->user->avatar = $newUser['avatar'];

            // TODO 4.1 preencher informações de LIKE
            $newPost->likeCount = 0;
            $newPost->liked = false;
            
            // TODO 4.2 preencher informações de COMMENTS
            $newPost->comments = [];

            
            $posts[] = $newPost; //Coloco isso tudo dentro do Array

        }

        // 5. retornar o resultado.
        return [
            'posts' => $posts,
            'pageCount' => $pageCount,
            'correntPage' => $page
        ];


    }




    


}
