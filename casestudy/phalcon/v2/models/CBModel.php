<?php

abstract class CBModel
{
    protected $bucket;

    abstract protected function getKey($primarykey);

    public function __construct($bucket){
        $this->bucket = $bucket;
    }
    
    public function get($id){
        $result = $this->bucket->get($this->getKey($id));
        return $result;
    }
    
    public function delete($id){
        $result = $this->bucket->remove($this->getKey($id));
        return $result;
    }

    public function update($body, $cas){
        $now = time();
        $body["update_timestamp"] = $now;
        $this->bucket->replace($this->getKey($body['id']), $body, ["cas" => $cas]);
        return $body;
    }

    protected function getId($key){
        $res = $this->bucket->counter("COUNTER::".$key, 1, array('initial'=>1));
        return $res->value;
    }

}