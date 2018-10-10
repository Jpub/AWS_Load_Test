<?php

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Message,
    Phalcon\Mvc\Model\Validator\InclusionIn,
    Phalcon\Mvc\Model\Validator\Uniqueness;

class Articles extends Model
{
    public $id;
    public $author_id;

    public function initialize()
    {
        $this->belongsTo(
            "author_id",
            "Users",
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
        $this->update_timestamp = date('Y-m-d H:i:s');
        $this->like_count = 0;
    }

    public function beforeUpdate()
    {
        // 갱신일시 설정
        $this->update_timestamp = date('Y-m-d H:i:s');
    }

    public function validation()
    {
        //author_id must not null
        if (empty($this->author_id)) {
            $this->appendMessage(new Message("The author_id cannot be null"));
        }

        //title must not null
        if (empty($this->title)) {
            $this->appendMessage(new Message("The title cannot be null"));
        }

        //content must not null
        if (empty($this->content)) {
            $this->appendMessage(new Message("The content cannot be null"));
        }

        //Check if any messages have been produced
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

}
