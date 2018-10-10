<?php
/**
 * Likes CRUD
 */

//Adds a new like
$app->put('/api/articles/{article_id:[0-9]+}/likes/{user_id:[0-9]+}', function($article_id, $user_id) use ($app) {
    //Create a response
    $response = new JsonResponse();

    try{
        $result = $app['articles']->get($article_id);
        $articles = (array)$result->value;
        $cas    = $result->cas;
        $app['articles']->addLikes($articles, $user_id, $cas);
        $response->setStatusCode(201, "Created");
        $response->setJsonContent(array('status' => 'OK'));
    }catch (Exception $e){
        $errors[] = $e->getMessage();
        $result = false;
        $response->setStatusCode(500, "Internal Server Error");
        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
    }
    return $response;
});

//Retrieves like based on primary key
$app->get('/api/articles/{article_id:[0-9]+}/likes/{user_id:[0-9]+}', function($article_id, $user_id) use ($app) {
    //Create a response
    $response = new JsonResponse();

    try{
        $result = $app['articles']->get($article_id);
        $articles = (array)$result->value;
        $cas    = $result->cas;
        $like = $app['articles']->getLikes($articles, $user_id);
        if($like){
            $response->setStatusCode(200, "OK");
            $response->setJsonContent(array(
                'status' => 'FOUND',
                'data' => array(
                    'article_id' => $article_id,
                    'user_id'    => $user_id,
                    'create_timestamp' => $like,
                )
            ));
        }else{
            $response->setStatusCode(200, "OK");
            $response->setJsonContent(array('status' => 'NOT-FOUND'));
        }
    }catch (Exception $e){
        $errors[] = $e->getMessage();
        $result = false;
        $response->setStatusCode(500, "Internal Server Error");
        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
    }
    return $response;
});

//Retrieves like based on article_id
$app->get('/api/articles/{article_id:[0-9]+}/likes', function($article_id) use ($app) {

    $result = $app['articles']->get($article_id);
    $articles = (array)$result->value;
    $likes = (array)$articles["likes"];

    //Create a response
    $response = new JsonResponse();

    $data = array();

    foreach ($likes as $key => $create_timestamp) {
        list($aid, $uid) = explode(":", $key);
        $data[] = array(
            'article_id' => $aid,
            'user_id'    => $uid,
            'create_timestamp' => $create_timestamp,
        );
    }
    

    if (!$data) {
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
    $result = $app['articles']->get($article_id);
    $articles = (array)$result->value;
    $cas    = $result->cas;
    $result = $app['articles']->deleteLikes($articles, $user_id, $cas);
    //Create a response
    $response = new JsonResponse();
    $response->setJsonContent(array(
        'status' => 'SUCCESS',
    ));
    return $response;
});

