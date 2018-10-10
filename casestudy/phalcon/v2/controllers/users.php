<?php
/**
 * Users CRUD
 */

//Adds a new user
$app->post('/api/users', function() use ($app) {

    $user = $app['users']->create($app->request->getJsonRawBody()->name);

    //Create a response
    $response = new JsonResponse();
    $response->setJsonContent(array('status' => 'OK', 'data' => $user));
    return $response;
});

//Retrieves user based on primary key
$app->get('/api/users/{id:[0-9]+}', function($id) use ($app) {
    
    //Create a response
    $response = new JsonResponse();
    try{
        $result = $app['users']->get($id);
        $user   = (array)$result->value;
        $response->setJsonContent(array(
            'status' => 'FOUND',
            'data'   => $user
        ));
    }catch (Exception $e){
        $response->setJsonContent(array('status' => 'NOT-FOUND'));
        $errors[] = $e->getMessage();
        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
    }
    return $response;
});

//Updates users based on primary key
$app->put('/api/users/{id:[0-9]+}', function($id) use($app) {
    //Create a response
    $response = new JsonResponse();
    try{
        $result = $app['users']->get($id);
        $user   = (array)$result->value;
        $cas    = $result->cas;
        $user["name"] = $app->request->getJsonRawBody()->name;
        $user = $app['users']->update($user, $cas);
        $response->setJsonContent(array(
            'status' => 'SUCCESS',
            'data' => $user
        ));
    }catch (Exception $e){
        $response->setJsonContent(array('status' => 'NOT-FOUND'));
        $errors[] = $e->getMessage();
        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
    }
    return $response;

});

//Deletes users based on primary key
// Articleやlikeを連動して消さない
$app->delete('/api/users/{id:[0-9]+}', function($id) use ($app) {
    //Create a response
    $response = new JsonResponse();
    try{
        $result = $app['users']->delete($id);
        $response->setJsonContent(array(
            'status' => 'SUCCESS'
        ));
    }catch (Exception $e){
        $response->setJsonContent(array('status' => 'NOT-FOUND'));
        $errors[] = $e->getMessage();
        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
    }
    return $response;

});
