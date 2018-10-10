<?php

class JsonResponse extends Phalcon\Http\Response
{
    /**
     * @codeCoverageIgnore
     */
     public function send(){
        $this->addXHProfURL();
        return parent::send();
    }

    /**
     * @codeCoverageIgnore
     */
    protected function addXHProfURL(){
        if (USE_XHPROF) {
            $xhprof_data        = xhprof_disable();
            $XHPROF_ROOT        = XHPROF_ROOT;
            $XHPROF_SOURCE_NAME = XHPROF_SOURCE_NAME;  
            include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_lib.php";
            include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_runs.php";
            $xhprof_runs = new XHProfRuns_Default(XHPROF_SOURCE_DIR);
            $run_id = $xhprof_runs->save_run($xhprof_data, $XHPROF_SOURCE_NAME);
            
            // cd [docroot]
            // sudo ln -s /usr/share/pear/xhprof_html xhprof_html
            
            $content = $this->getContent();
            $content = json_decode($content, true);
            $content['XHprof_URL'] = "http://".$_SERVER["SERVER_NAME"]."/xhprof_html/index.php?run=$run_id&source=$XHPROF_SOURCE_NAME";
            $this->setContent(json_encode($content));
        }
    }

}