<?php
/**
 * Articles CRUD
 */

//Adds a new article
$app->post('/api/articles', function() use ($app) {

    $article = $app->request->getJsonRawBody();

    $phql = "INSERT INTO Articles (author_id, title, content) VALUES (:author_id:, :title:, :content:)";

    $status = $app->modelsManager->executeQuery($phql, array(
        'author_id'   => $article->author_id,
        'title'   => $article->title,
        'content' => $article->content,
    ));

    //Create a response
    $response = new JsonResponse();

    //Check if the insertion was successful
    if ($status->success() == true) {

        //Change the HTTP status
        $response->setStatusCode(201, "Created");
        $article->id               = $status->getModel()->id;
        $article->create_timestamp = $status->getModel()->create_timestamp;
        $article->update_timestamp = $status->getModel()->update_timestamp;
        $response->setJsonContent(array('status' => 'OK', 'data' => $article));

    } else {

        //Change the HTTP status
        $response->setStatusCode(409, "Conflict");

        //Send errors to the client
        $errors = array();
        foreach ($status->getMessages() as $message) {
            $errors[] = $message->getMessage();
        }
        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
    }
    return $response;
});

//Retrieves articles based on primary key
$app->get('/api/articles/{id:[0-9]+}', function($id) use ($app) {
    if(!empty($_GET['limit'])){
        $limit = intval($_GET['limit']);
    } else {
        $limit = 1;
    }
    $phql = "SELECT * FROM Articles WHERE id <= :id: ORDER BY id DESC LIMIT $limit";
    $articles = $app->modelsManager->executeQuery($phql, array(
        'id' => $id
    ));

    //Create a response
    $response = new JsonResponse();

    $data = array();

    foreach ($articles as $article) {
        $data[] = array(
            'id'               => $article->id,
            'title'            => $article->title,
            'content'          => $article->content,
            'like_count'       => $article->like_count,
            'create_timestamp' => $article->create_timestamp,
            'update_timestamp' => $article->update_timestamp,
        );
    }
    
    $response = new JsonResponse();
    if ($articles == false) {
        $response->setJsonContent(array('status' => 'NOT-FOUND'));
    } elseif($limit == 1 && (!isset($data[0]) || $data[0]['id'] !== $id)){
        $response->setJsonContent(array('status' => 'NOT-FOUND'));
    } else {
        $response->setJsonContent(array(
            'status' => 'FOUND',
            'data'   => $data,
        ));
    }
    return $response;
});

//Retrieves articles
$app->get('/api/articles', function() use ($app) {
    if(!empty($_GET['limit'])){
        $limit = intval($_GET['limit']);
    } else {
        $limit = 1;
    }
    $phql = "SELECT * FROM Articles ORDER BY id DESC LIMIT $limit";
    $articles = $app->modelsManager->executeQuery($phql, array());

    //Create a response
    $response = new JsonResponse();

    $data = array();

    foreach ($articles as $article) {
        $data[] = array(
            'id'               => $article->id,
            'title'            => $article->title,
            'content'          => $article->content,
            'like_count'       => $article->like_count,
            'create_timestamp' => $article->create_timestamp,
            'update_timestamp' => $article->update_timestamp,
        );
    }
    
    $response = new Phalcon\Http\Response();
    if ($articles == false) {
        $response->setJsonContent(array('status' => 'NOT-FOUND'));
    } else {
        $response->setJsonContent(array(
            'status' => 'FOUND',
            'data'   => $data,
        ));
    }
    return $response;
});

//Updates articles based on primary key
$app->put('/api/articles/{id:[0-9]+}', function($id) use($app) {

    $article = $app->request->getJsonRawBody();

    $phql = "UPDATE Articles SET title = :title:, content = :content: WHERE id = :id:";
    $status = $app->modelsManager->executeQuery($phql, array(
        'id'      => $id,
        'title'   => $article->title,
        'content' => $article->content,
    ));

    //Create a response
    $response = new JsonResponse();

    //Check if the insertion was successful
    if ($status->success() == true) {
        $response->setJsonContent(array('status' => 'OK'));
    } else {

        //Change the HTTP status
        $response->setStatusCode(409, "Conflict");

        $errors = array();
        foreach ($status->getMessages() as $message) {
            $errors[] = $message->getMessage();
        }

        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
    }

    return $response;
});

//Deletes article based on primary key
$app->delete('/api/articles/{id:[0-9]+}', function($id) use ($app) {

    $phql = "DELETE FROM Articles WHERE id = :id:";
    $status = $app->modelsManager->executeQuery($phql, array(
        'id' => $id
    ));

    //Create a response
    $response = new JsonResponse();

    if ($status->success() == true) {
        $response->setJsonContent(array('status' => 'OK'));
    } else {

        //Change the HTTP status
        $response->setStatusCode(409, "Conflict");

        $errors = array();
        foreach ($status->getMessages() as $message) {
            $errors[] = $message->getMessage();
        }

        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));

    }

    return $response;
});

