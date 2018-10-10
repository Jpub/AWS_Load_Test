<?php
/**
 * Helloworld
 */

$app->get('/helloworld', function() use ($app) {

    //Create a response
    $response = new JsonResponse();
    $response->setJsonContent(array('status' => 'OK', 'messages' => "Hello World!"));
    return $response;
});
