<?php

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Message,
    Phalcon\Mvc\Model\Validator\InclusionIn,
    Phalcon\Mvc\Model\Validator\Uniqueness;

class Users extends Model
{

    public function beforeCreate()
    {
        // 등록일시 설정
        $this->create_timestamp = date('Y-m-d H:i:s');
        $this->update_timestamp = date('Y-m-d H:i:s');
    }

    public function beforeUpdate()
    {
        // 갱신일시 설정
        $this->update_timestamp = date('Y-m-d H:i:s');
    }


    public function validation()
    {
        //name must not null
        if (empty($this->name)) {
            $this->appendMessage(new Message("The name cannot be null"));
        }

        //Check if any messages have been produced
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

}
