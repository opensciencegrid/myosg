<?

class StatuschangesController extends ControllerBase
{ 
    public function pagename() { return "statuschanges"; }
    public function load()
    {
        $params = array();
        if(isset($_REQUEST["resource_id"])){
            $dirty_resource_id = $_REQUEST["resource_id"];
            $resource_id = (int)$dirty_resource_id;
            $params["resource_id"] = $resource_id;
        }

        $model = new Resource();
        $this->view->resources = $model->get();

        $model = new ServiceTypes();
        $this->view->services = $model->getindex();

        $model = new StatusChangesService();
        $this->view->status_changes = $model->get();

        $this->view->page_title = "Latest Status Changes";
    }
    public function rssAction()
    {
        $this->load();

        //pull last status change (first row)
        $this->view->last_timestamp = $this->view->status_changes[0]->timestamp;

        header("Content-type: text/xml");
    }
} 