<?php
class Users extends CBModel
{
    public function create($name){
        $now = time();
        $id = parent::getId("users");
        $body = [
            "id"      => $id,
            "doctype" => "users",
            "name"    => $name,
            "create_timestamp" => $now,
            "update_timestamp" => $now,
        ];
        $this->bucket->upsert($this->getKey($id), $body);
        return $body;
    }
    
    protected function getKey($id) {
        return "U::{$id}";
    }
}