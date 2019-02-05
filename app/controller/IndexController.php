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
        $imageName = NULL;
        $uploadDir = App::config('imageurl');
        $allowedExt = ['jpg', 'jpeg'];
        if($data === false) {
            header('Location: ' . App::config('url'));
        } else {
            if($_FILES['imagefile']['type'] != NULL) {
                $imageName = rand(1000000,9999999) . htmlspecialchars(basename($_FILES['imagefile']['name']));
                $imageName = str_replace(' ', '_', $imageName);
                $uploadImage = $uploadDir . $imageName;
                $extansion = pathinfo($imageName, PATHINFO_EXTENSION);
                if(!in_array($extansion, $allowedExt)) {
                    echo 'File must be .jpg or .jpeg';
                    exit();
                } elseif (move_uploaded_file($_FILES["imagefile"]["tmp_name"], $uploadImage)) {
                    echo 'File uploaded';
                } else {
                    echo 'Upload failed';
                    exit();
                }
            }

            $connection = Db::connect();
            $sql = 'insert into post (content, image) values (:content, :image)';
            $stmt = $connection->prepare($sql);
            $stmt->bindValue('content', $data['content']);
            $stmt->bindValue('image', $imageName);
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
            header('Location: ' . App::config('url').'/Index/view/' . $data['post_id']);
        }
    }

    public function deletePost()
    {
        $data = $this->validateId($_POST);
        if($data === false) {
            header('Location: ' . App::config('url'));
        } else {
            $db = Db::connect();
            $statementComment = $db->prepare('delete from comment where post_id = :post_id');
            $statementComment->bindValue('post_id', $data['id']);
            $statementComment->execute();

            $statement = $db->prepare('delete from post where id = :id');
            $statement->bindValue('id', $data['id']);
            $statement->execute();

            header('Location: ' . App::config('url'));
        }
    }

    private function validateId($data)
    {
        $required = ['id'];
        foreach ($required as $key){
            if(!isset($data[$key])){
                return false;
            }
            $data[$key] = trim((int)$data[$key]);
            if(empty($data[$key])){
                return false;
            }
        }
        return $data;
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
        $post_id = Post::find($id);
        $comments = Comment::all($id);
        $view->render('view', [
            'post' => $post_id,
            'comments' => $comments
        ]);
    }
}