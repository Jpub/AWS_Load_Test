<?php

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Message,
    Phalcon\Mvc\Model\Validator\InclusionIn,
    Phalcon\Mvc\Model\Validator\Uniqueness;

class Likes extends Model
{
    public $id;
    public $user_id;
    public $article_id;

    public function initialize()
    {
        $this->belongsTo(
            "user_id",
            "Users",
            "id",
            array(
                "foreignKey" => true
            )
        );
        $this->belongsTo(
            "article_id",
            "Articles",
            "id",
            array(
                "foreignKey" => true
            )
        );
    }

    public function beforeCreate()
    {
        // 등록일시 설정
        $this->create_timestamp = date('Y-m-d H:i:s');
    }

    public function validation()
    {
        //Check if any messages have been produced
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

}
