<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 5.3.17.
 * Time: 21.29
 */

namespace App\MateyModels;


class FeedEntry
{
    protected $postId;
    protected $seen;

    public function __construct($properties, $createFrom = 'array')
    {
        if($createFrom == 'json') json_decode($properties);

        $this->postId = $properties['post_id'];
        $this->seen = $properties['seen'];
    }

    /**
     * @return mixed
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @return mixed
     */
    public function getSeen()
    {
        return $this->seen;
    }

    /**
     * @param mixed $seen
     */
    public function setSeen($seen)
    {
        $this->seen = $seen;
    }

    public function getMysqlValues()
    {
        $keyValues = array ();

        return $keyValues;
    }

    public function asArray()
    {
        $keyValues = $this->getMysqlValues();

        empty($this->postId) ? : $keyValues['post_id'] = $this->postId;
        empty($this->seen) ? : $keyValues['seen'] = $this->seen;

        return $keyValues;
    }

}