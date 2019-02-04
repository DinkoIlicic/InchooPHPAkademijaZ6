<?php

class Post
{
    private $id;

    private $content;

    private $image;

    private $post_created;

    public function __get($name)
    {
        return isset($this->$name) ? $this->$name : null;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __call($name, $arguments)
    {
        $function = substr($name, 0,3);
        if($function === 'set'){
            $this->__set(strtolower(substr($name,3)),$arguments[0]);
            return $this;
        }elseif ($function=== 'get'){
            return $this->__get(strtolower(substr($name,3)));
        }
        return $this;
    }


    public function __construct($id, $content, $image, $post_created)
    {
        $this->setId($id);
        $this->setContent($content);
        $this->setImage($image);
        $this->setPost_created($post_created);
    }

    public static function all()
    {
        $list = [];
        $db = Db::connect();
        $statement = $db->prepare('select * from post ORDER BY id desc ');
        $statement->execute();
        foreach ($statement->fetchAll() as $post){
            $list[] = new Post($post->id, $post->content, $post->image, $post->post_created);
        }
        return $list;
    }

    public static function find($id)
    {
        $id = (int) $id;
        $db = Db::connect();
        $statement = $db->prepare('select * from post where id = :id');
        $statement->bindValue('id', $id);
        $statement->execute();
        $post = $statement->fetch();
        return new Post($post->id, $post->content, $post->image, $post->post_created);
    }
}