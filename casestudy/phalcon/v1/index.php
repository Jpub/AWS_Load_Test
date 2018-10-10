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


// xhprof開始
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

//Set up the database service
$di->set('db', function(){
    return new DbAdapter(array(
        "host" => "taru8test.cayhlkuryzts.ap-northeast-1.rds.amazonaws.com",
        "username" => "root",
        "password" => "mysqlroot",
        "dbname" => "casestudy_1",
        "persistent"=> true,
    ));
});

//Create and bind the DI to the application
$app = new \Phalcon\Mvc\Micro($di);

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
}catch(PDOException $e){
    $errors[] = $e->getMessage();
    $result = false;
    $response = new JsonResponse();
    $response->setStatusCode(500, "DB error");
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
