<?

class MapController extends ControllerBase
{
    public function pagename() { return "map"; }
    public function load()
    {

        //pull sites
        $site_model = new Site();
        $sites = $site_model->get();
        $this->view->sites = $sites;

        //pull resource groups
        $rgroup_model = new ResourceGroup();
        $rgroups = $rgroup_model->get();
        $this->view->rgs = array();
        foreach($sites as $site) {
            $rgs = array();
            foreach($rgroups as $rgroup) {
                //elog(print_r($rgroup, true));
                if($rgroup->site_id == $site->site_id) {
                    $rgs[] = $rgroup;
                }
            }
            $this->view->rgs[$site->site_id] = $rgs;
        }

        //pull resources (grouped by resource group id)
        $rgrouped_model = new ResourceByGroupID();
        $this->view->resources_bygid = $rgrouped_model->getindex();

        ///////////////////////////////////////////////////////////////////////
        //get resource status cache
        $cache_filename_template = config()->current_resource_status_xml_cache;
        $cache_filename = str_replace("<ResourceID>", "all", $cache_filename_template); 
        $cache_xml = file_get_contents($cache_filename);

        $cache = new SimpleXMLElement($cache_xml);
        //index resource status list by resource ID
        $this->view->resource_status = array();
        foreach($cache->ResourceStatus as $resource_status) {
            $id = (int)$resource_status->ResourceID[0];
            $this->view->resource_status[$id] = $resource_status;
        }

        $this->view->page_title = "OSG Site Map";
    }
    public function uwaAction()
    {
        $this->load();
    }
    public function iframeAction()
    {
        $this->load();
    }

}