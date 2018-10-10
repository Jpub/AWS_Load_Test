<?php
/**
 * Users CRUD
 */

//Adds a new user
$app->post('/api/users', function() use ($app) {

    $user = $app->request->getJsonRawBody();

    $phql = "INSERT INTO Users (name) VALUES (:name:)";

    $status = $app->modelsManager->executeQuery($phql, array(
        'name' => $user->name,
    ));

    //Create a response
    $response = new JsonResponse();

    //Check if the insertion was successful
    if ($status->success() == true) {

        //Change the HTTP status
        $response->setStatusCode(201, "Created");
        $user->id = $status->getModel()->id;
        $user->cerate_timestamp = $status->getModel()->create_timestamp;
        $user->update_timestamp = $status->getModel()->update_timestamp;
        $response->setJsonContent(array('status' => 'OK', 'data' => $user));

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

//Retrieves user based on primary key
$app->get('/api/users/{id:[0-9]+}', function($id) use ($app) {

    $phql = "SELECT * FROM Users WHERE id = :id: LIMIT 1";
    $user = $app->modelsManager->executeQuery($phql, array(
        'id' => $id
    ))->getFirst();

    //Create a response
    $response = new JsonResponse();

    if ($user == false) {
        $response->setJsonContent(array('status' => 'NOT-FOUND'));
    } else {
        $response->setJsonContent(array(
            'status' => 'FOUND',
            'data' => array(
                'id' => $user->id,
                'name' => $user->name,
                'create_timestamp' => $user->create_timestamp,
                'update_timestamp' => $user->update_timestamp,
            )
        ));
    }
    return $response;
});

//Updates users based on primary key
$app->put('/api/users/{id:[0-9]+}', function($id) use($app) {

    $user = $app->request->getJsonRawBody();

    $phql = "UPDATE Users SET name = :name: WHERE id = :id:";
    $status = $app->modelsManager->executeQuery($phql, array(
        'id' => $id,
        'name' => $user->name,
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

//Deletes users based on primary key
$app->delete('/api/users/{id:[0-9]+}', function($id) use ($app) {

    $result = false;
    $app->db->begin();
    try{
        $phql = "UPDATE Articles SET like_count=like_count-1 WHERE id IN (SELECT article_id FROM Likes WHERE user_id = :id:) ";
        $status = $app->modelsManager->executeQuery($phql, array(
            'id' => $id
        ));

        if ($status->success() == true) {
            $phql = "DELETE FROM Users WHERE id = :id:";
            $status = $app->modelsManager->executeQuery($phql, array(
                'id' => $id
            ));
            if ($status->success() == true) {
                $result = true;
            }
        }
    }catch (PDOException $e){
        $errors[] = $e->getMessage();
        $result = false;
    }

    //Create a response
    $response = new JsonResponse();

    if ($result) {
        $app->db->commit();
        $response->setJsonContent(array('status' => 'OK'));
    } else {
        $app->db->rollback();
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

