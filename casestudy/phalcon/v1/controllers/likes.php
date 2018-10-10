<?php
/**
 * Likes CRUD
 */

//Adds a new like
$app->put('/api/articles/{article_id:[0-9]+}/likes/{user_id:[0-9]+}', function($article_id, $user_id) use ($app) {

    $errors = array();
    $app->db->begin();

    try{
        $phql = "INSERT INTO Likes (article_id, user_id) VALUES (:article_id:, :user_id:)";

        $status = $app->modelsManager->executeQuery($phql, array(
            'article_id' => $article_id,
            'user_id'    => $user_id,
        ));

        //Check if the insertion was successful
        $result = true;
        if ($status->success() == true) {
            $phql = "UPDATE Articles SET like_count=like_count+1 WHERE id = :id:";
            $status = $app->modelsManager->executeQuery($phql, array(
                'id' => $article_id,
            ));
            if ($status->success() !== true) {
                $result = false;
            }
        } else {
            $result = false;
        }
    }catch (PDOException $e){
        $errors[] = $e->getMessage();
        $result = false;
    }

    //Create a response
    $response = new JsonResponse();
    
    if ($result) {
        $app->db->commit();
        //Change the HTTP status
        $response->setStatusCode(201, "Created");
        $response->setJsonContent(array('status' => 'OK'));
    } else {
        $app->db->rollback();
        //Change the HTTP status
        $response->setStatusCode(409, "Conflict");

        //Send errors to the client
        if(isset($status)){
            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }
        }
        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
    }
    return $response;
});

//Retrieves like based on primary key
$app->get('/api/articles/{article_id:[0-9]+}/likes/{user_id:[0-9]+}', function($article_id, $user_id) use ($app) {

    $phql = "SELECT * FROM Likes WHERE article_id = :article_id: AND user_id = :user_id: LIMIT 1";
    $like = $app->modelsManager->executeQuery($phql, array(
        'article_id' => $article_id,
        'user_id'    => $user_id,
    ))->getFirst();

    //Create a response
    $response = new JsonResponse();

    if ($like == false) {
        $response->setJsonContent(array('status' => 'NOT-FOUND'));
    } else {
        $response->setJsonContent(array(
            'status' => 'FOUND',
            'data' => array(
                'article_id' => $like->article_id,
                'user_id'    => $like->user_id,
                'create_timestamp' => $like->create_timestamp,
            )
        ));
    }
    return $response;
});

//Retrieves like based on article_id
$app->get('/api/articles/{article_id:[0-9]+}/likes', function($article_id) use ($app) {

    $phql = "SELECT * FROM Likes WHERE article_id = :article_id:";
    $likes = $app->modelsManager->executeQuery($phql, array(
        'article_id' => $article_id,
    ));

    //Create a response
    $response = new JsonResponse();

    $data = array();

    foreach ($likes as $like) {
        $data[] = array(
            'article_id' => $like->article_id,
            'user_id'    => $like->user_id,
            'create_timestamp' => $like->create_timestamp,
        );
    }
    

    if (@$like == false) {
        $response->setJsonContent(array('status' => 'NOT-FOUND'));
    } else {
        $response->setJsonContent(array(
            'status' => 'FOUND',
            'data'   => $data,
        ));
    }
    return $response;
});

//Deletes likes based on primary key
$app->delete('/api/articles/{article_id:[0-9]+}/likes/{user_id:[0-9]+}', function($article_id, $user_id) use ($app) {
    $errors = array();
    $app->db->begin();

    try {
        $phql = "DELETE FROM Likes WHERE article_id = :article_id: AND user_id = :user_id: ";
        $status = $app->modelsManager->executeQuery($phql, array(
            'article_id' => $article_id,
            'user_id'    => $user_id,
        ));

        $result = true;
        if ($status->success() == true && $app->db->affectedRows()) {
            $phql = "UPDATE Articles SET like_count=like_count-1 WHERE id = :id: ";
            $status = $app->modelsManager->executeQuery($phql, array(
                'id' => $article_id,
            ));
            if ($status->success() !== true) {
                $result = false;
            }
        } else {
            $result = false;
        }
    }catch (PDOException $e){
        $errors[] = $e->getMessage();
        $result = false;
    }
    
    //Create a response
    $response = new JsonResponse();

    
    if($result){
        $app->db->commit();
        $response->setJsonContent(array('status' => 'OK'));
    } else {
        $app->db->rollback();
        //Change the HTTP status
        $response->setStatusCode(409, "Conflict");

        if(isset($status)){
            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }
        }

        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));

    }
    return $response;
});

