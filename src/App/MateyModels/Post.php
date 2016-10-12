<?php

namespace App\Models;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.10.16.
 * Time: 16.45
 */
class Post implements \Serializable
{

    protected $post_id;
    protected $text;

    /**
     * @return mixed
     */
    public function getPostId()
    {
        return $this->post_id;
    }

    /**
     * @param mixed $post_id
     */
    public function setPostId($post_id)
    {
        $this->post_id = $post_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function serialize()
    {
        return serialize(
            array(
                "post_id" => $this->post_id,
                "text" => $this->text
            )
        );
    }

    public function unserialize($serialized)
    {
        list($this->post_id, $this->text) = unserialize($serialized);
    }


}