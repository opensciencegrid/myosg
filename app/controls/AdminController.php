<?

class AdminController extends ControllerBase
{ 
    public function breads() { return array("admin"); }
    public static function default_title() { return "Administration"; }
    public static function default_url($query) { return ""; }

    public function load()
    {
        //make sure the request originated from localhost
        if($_SERVER["REMOTE_ADDR"] != $_SERVER["SERVER_ADDR"]) {
            //pretend that this page doesn't exist
            $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
            echo "404";
            exit;
        }
        $this->setpagetitle(AdminController::default_title());
    }

    public function logrotateAction()
    {
        $this->load();
        dlog("Writing config file for logrotate...");
        $root = getcwd()."/";
        $statepath = "/tmp/viewer.rotate.state";
        $config = "compress \n".
            $root.config()->logfile. " ". 
            $root.config()->error_logfile. " ". 
            $root.config()->audit_logfile." {\n".
            "   rotate 5\n".
            "   size=50M\n".
            "}";
        $confpath = "/tmp/viewer.rotate.conf";
        $fp = fopen($confpath, "w");
        fwrite($fp, $config);
        
        dlog("running logroate with following config\n$config");
        passthru("/usr/sbin/logrotate -s $statepath $confpath");

        $this->render("none");
    }

    public function optimizeAction()
    {
        $model = new Admin();
        $model->optimize();
        $this->render("none");
    }
} 
