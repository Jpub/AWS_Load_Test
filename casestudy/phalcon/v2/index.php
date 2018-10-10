<?php
use Phalcon\Loader;
use Phalcon\DI\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

require_once('lib/JsonResponse.php');

date_default_timezone_set('asia/tokyo');

define('STATUS_FILE',         'http://yumemi-s3.s3.amazonaws.com/taru8/status.txt');
define('XHPROF_ROOT',         '/usr/share/pear');
define('XHPROF_SOURCE_NAME',  'xhprof');
define('XHPROF_SOURCE_DIR',   '/var/log/xhprof');

define("CB_HOST_NAME",          "172.31.5.109");
define("CB_BUCKET_NAME",        "sample");
define("CB_BUCKET_PASSWD",      "password");


// xhprof시작
if(isset($_GET['use_xhprof']) && $_GET['use_xhprof'] && XHPROF_ROOT && XHPROF_SOURCE_NAME && XHPROF_SOURCE_DIR){
    define ('USE_XHPROF', true);
    xhprof_enable();
}else{
    define ('USE_XHPROF', false);
}

// Register an autoloader
$loader = new Loader();
$loader->registerDirs(
    array(
        dirname(__FILE__).'/models/',
    )
)->register();

// check maintenance mode
if (trim(file_get_contents_cache(STATUS_FILE))!=="OK"){
    $response = new JsonResponse();
    $response->setStatusCode(503, "Service Unavailable");
    $errors = array('Sorry this API is under maintenance');
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
    $response->send();
}

$di = new \Phalcon\DI\FactoryDefault();
//Create and bind the DI to the application
$app = new \Phalcon\Mvc\Micro($di);

//Set up the database service
$cluster = new CouchbaseCluster(CB_HOST_NAME);
$bucket = $cluster->openBucket(CB_BUCKET_NAME, CB_BUCKET_PASSWD);

$di->setShared('users', function() use ($bucket){
    return new Users($bucket);
});

$di->setShared('articles', function() use ($bucket){
    return new Articles($bucket);
});


require_once('controllers/helloworld.php');
require_once('controllers/users.php');
require_once('controllers/articles.php');
require_once('controllers/likes.php');

$app->notFound(function () use ($app) {
    //Create a response
    $response = new JsonResponse();
    $response->setStatusCode(404, "Not imprement");
    $errors = array('Not Impremented.');
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
    return $response;
});
try {
    $app->handle();
}catch(CouchbaseException $e){
    $errors[] = $e->getMessage();
    $result = false;
    $response = new JsonResponse();
    $response->setStatusCode(500, "Couchbase error");
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => $errors));
    $response->send();
}

function file_get_contents_cache($url){
    $key = "url_${url}";
    if($cache = apc_fetch($key)){
        return $cache;
    }
    $contents = @file_get_contents($url);
    apc_store($key, $contents, 10);
    return $contents;
}
