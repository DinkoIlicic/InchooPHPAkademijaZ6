<?php

class Comment
{
    private $id;

    private $post_id;

    private $content;


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

    public function __construct($id, $post_id, $content)
    {
        $this->setId($id);
        $this->setPost_id($post_id);
        $this->setContent($content);
    }

    public static function find($id)
    {
        $id = (int) $id;
        $db = Db::connect();
        $statement = $db->prepare('select * from comment where id = :id');
        $statement->bindValue('id', $id);
        $statement->execute();
        $post = $statement->fetch();
        return new Post($post->id, $post->content, $post->image, $post->post_created);
    }

    public static function all($post_id)
    {
        $list = [];
        $db = Db::connect();
        $statement = $db->prepare('select * from comment WHERE post_id= ' . $post_id . 'ORDER BY id desc ');
        $statement->execute();
        foreach ($statement->fetchAll() as $comment){
            $list[] = new Comment($comment->id, $comment->post_id, $comment->content);
        }
        return $list;
    }
}