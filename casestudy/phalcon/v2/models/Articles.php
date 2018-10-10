<?php
define("DESIGN_DOCUMENT_NAME", "viewtest");
define("VIEW_NAME",            "doctype_id");

class Articles extends CBModel
{
    public function create($author_id, $title, $content){
        $now = time();
        $id = parent::getId("articles");
        $body = [
            "id"        => $id,
            "doctype"   => "articles",
            "author_id" => $author_id,
            "title"     => $title,
            "content"   => $content,
            "likes"     => [],
            "create_timestamp" => $now,
            "update_timestamp" => $now,
        ];
        $this->bucket->upsert($this->getKey($id), $body);
        return $body;
    }
    
    public function addLikes($articles, $user_id, $cas){
        $now = time();
        if($this->getLikes($articles, $user_id)){
            return true;
        }else{
            $likeKey = $this->getLikeKey($articles["id"], $user_id);
            $articles["likes"]=(array)$articles["likes"];
            $articles["likes"][$likeKey] = $now;
            $this->update($articles, $cas);
            return true;
        }
    }

    public function getLikes($articles, $user_id){
        $likeKey = $this->getLikeKey($articles["id"], $user_id);
        $likes = (array)$articles["likes"];
        if(isset($likes[$likeKey])){
            return $likes[$likeKey];
        }else{
            return false;
        }
    }

    public function deleteLikes($articles, $user_id, $cas){
        $now = time();
        if(!$this->getLikes($articles, $user_id)){
            return true;
        }else{
            $likeKey = $this->getLikeKey($articles["id"], $user_id);
            $articles["likes"] = (array)$articles["likes"];
            unset($articles["likes"][$likeKey]);
            $this->update($articles, $cas);
            return true;
        }
    }

    public function getFromView($article_id, $limit){
        $startkey = "articles:".sprintf("%010d", $article_id);
        $custom = ["startkey" => "\"$startkey\""];
        
        if(rand(1,100) == 1){
            $query = CouchbaseViewQuery::from(DESIGN_DOCUMENT_NAME, VIEW_NAME)->limit($limit)->custom($custom)->order(CouchbaseViewQuery::ORDER_DESCENDING)->stale(CouchbaseViewQuery::UPDATE_AFTER);
        }else{
            $query = CouchbaseViewQuery::from(DESIGN_DOCUMENT_NAME, VIEW_NAME)->limit($limit)->custom($custom)->order(CouchbaseViewQuery::ORDER_DESCENDING);
        }
        $results = $this->bucket->query($query);
        
        $ids = array();
        foreach($results->rows as $row){
            $ids[]=$row->id;
        }
        $results = $this->bucket->get($ids);
        return($results);
        
    }

    protected function getKey($id) {
        return "A::{$id}";
    }
    
    protected function getLikeKey($article_id, $user_id){
        return "${article_id}:${user_id}";
    }

}