<?php

class IndexController
{
    public function index()
    {
        $view = new View();
        $posts = Post::all();
        $view->render('index', [
            'posts' => $posts
        ]);
    }

    public function newPost()
    {
        $data = $this->validate($_POST);
        if($data === false) {
            header('Location: ' . App::config('url'));
        } else {
            $connection = Db::connect();
            $sql = 'insert into post (content) values (:content)';
            $stmt = $connection->prepare($sql);
            $stmt->bindValue('content', $data['content']);
            $stmt->execute();
            header('Location: ' . App::config('url'));
        }
    }

    public function newComment()
    {
        $data = $this->validate($_POST);
        if($data === false) {
            header('Location: ' . App::config('url'));
        } else {
            $connection = Db::connect();
            $sql = 'insert into comment (post_id, content) values (:post_id, :content)';
            $stmt = $connection->prepare($sql);
            $stmt->bindValue('post_id', $data['post_id']);
            $stmt->bindValue('content', $data['content']);
            $stmt->execute();
            header('Location: ' . App::config('url').'Index/view' . $data['post_id']);
        }
    }

    private function validate($data)
    {
        $required = ['content'];
        foreach ($required as $key){
            if(!isset($data[$key])){
                return false;
            }
            $data[$key] = trim((string)$data[$key]);
            if(empty($data[$key])){
                return false;
            }
        }
        return $data;
    }

    public function view($id)
    {
        $view = new View();
        $view->render('view', [
            'post' => Post::find($id)
        ]);
    }
}