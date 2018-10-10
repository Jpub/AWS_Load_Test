<?php
/**
 * Articles CRUD
 */

//Adds a new article
$app->post('/api/articles', function() use ($app) {

    $article = $app['articles']->create(
        $app->request->getJsonRawBody()->author_id,
        $app->request->getJsonRawBody()->title,
        $app->request->getJsonRawBody()->content
    );

    //Create a response
    $response = new JsonResponse();
    $article['like_count'] = count($article['likes']);
    unset($article['likes']);
    $response->setJsonContent(array('status' => 'OK', 'data' => $article));
    return $response;
});

//Retrieves articles based on primary key
$app->get('/api/articles/{id:[0-9]+}', function($id) use ($app) {
    $response = new JsonResponse();

    if(!empty($_GET['limit'])){
        $limit = intval($_GET['limit']);
        $results = $app['articles']->getFromView($id, $limit);
        if(!$results){
            $response->setStatusCode(404, "Not Found");
            $response->setJsonContent(array('status' => 'NOT-FOUND'));
            return $response;
        }
        $data = [];
        foreach($results as $result){
            $article = (array)$result->value;
            if(isset($article['likes'])){
                $article['like_count'] = count($article['likes']);
                unset($article['likes']);
            }
            $data[] = $article;
        }
        if($data){
            $response->setJsonContent(array(
                'status' => 'FOUND',
                'data'   => $data,
            ));
        }else{
            $response->setStatusCode(404, "Not Found");
            $response->setJsonContent(array('status' => 'NOT-FOUND'));
        }
        return $response;
    } else {
        try{
            $result = $app['articles']->get($id);
            $data   = (array)$result->value;
            $data['like_count'] = count($data['likes']);
            unset($data['likes']);
        }catch(Exception $e){
            $data = [];
        }
        if($data){
            $response->setJsonContent(array(
                'status' => 'FOUND',
                'data'   => $data,
            ));
        }else{
            $response->setStatusCode(404, "Not Found");
            $response->setJsonContent(array('status' => 'NOT-FOUND'));
        }
        return $response;
    }
});

//Retrieves articles
$app->get('/api/articles', function() use ($app) {
    $response = new JsonResponse();
    if(!empty($_GET['limit'])){
        $limit = intval($_GET['limit']);
    } else {
        $limit = 1;
    }
    $results = $app['articles']->getFromView(100000000, $limit);
    $data = [];
    if($results){
        foreach($results as $result){
            $article = (array)$result->value;
            $article['like_count'] = count($article['likes']);
            unset($article['likes']);
            $data[] = $article;
        }
    }
    if($data){
        if($limit == 1){
            $response->setJsonContent(array(
                'status' => 'FOUND',
                'data'   => $data[0],
            ));
        }else{
            $response->setJsonContent(array(
                'status' => 'FOUND',
                'data'   => $data,
            ));
        }
    }else{
        $response->setStatusCode(404, "Not Found");
        $response->setJsonContent(array('status' => 'NOT-FOUND'));
    }
    return $response;
});

//Updates articles based on primary key
$app->put('/api/articles/{id:[0-9]+}', function($id) use($app) {
    //Create a response
    $response = new JsonResponse();

    try{
        $result = $app['articles']->get($id);
        $data   = (array)$result->value;
        $cas    = $result->cas;
        $data["title"] = $app->request->getJsonRawBody()->title;
        $data["content"] = $app->request->getJsonRawBody()->content;
        $article = $app['articles']->update($data, $cas);
        $response->setJsonContent(array('status' => 'OK'));
    }catch (Exception $e){
        $response->setStatusCode(404, "Not Found");
        $response->setJsonContent(array('status' => 'NOT-FOUND'));
        $errors[] = $e->getMessage();
        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
    }
    return $response;
});

//Deletes article based on primary key
$app->delete('/api/articles/{id:[0-9]+}', function($id) use ($app) {
    $response = new JsonResponse();
    try{
        $result = $app['articles']->delete($id);
        $response->setJsonContent(array(
            'status' => 'SUCCESS',
        ));
    }catch (Exception $e){
        $response->setStatusCode(404, "Not Found");
        $response->setJsonContent(array('status' => 'NOT-FOUND'));
        $errors[] = $e->getMessage();
        $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
    }
    return $response;
});

