<?php

class Post
{
    private $id;

    private $content;

    private $image;

    private $post_created;

    private $comment_count;

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


    public function __construct($id, $content, $image, $post_created, $comment_count)
    {
        $this->setId($id);
        $this->setContent($content);
        $this->setImage($image);
        $this->setPost_created($post_created);
        $this->setComment_count($comment_count);
    }

    public static function all()
    {
        $list = [];
        $db = Db::connect();
        $statement = $db->prepare('select *, (select count(*) from comment where comment.post_id=post.id) as commentCount from post ORDER BY id desc ');
        $statement->execute();
        foreach ($statement->fetchAll() as $post){
            $list[] = new Post($post->id, $post->content, $post->image, $post->post_created, $post->commentCount);
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
        return new Post($post->id, $post->content, $post->image, $post->post_created, null);
    }
}