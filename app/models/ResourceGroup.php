<?php
/**************************************************************************************************

Copyright 2009 The Trustees of Indiana University

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in
compliance with the License. You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License
is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
implied. See the License for the specific language governing permissions and limitations under the
License.

**************************************************************************************************/

class ResourceGroup extends CachedIndexedModel
{
    public function ds() { return "oim"; }
    public function sql($params)
    {
        //select resource group that is used by at least one resource (TODO.. really?? why?)
        $where = "where rg.id IN (select resource_group_id from resource group by resource_group_id)"; 

        if(isset($params["resource_group_ids"])) {
            if(count($params["resource_group_ids"]) == 0) {
                $where .= "and 1 = 2";
            } else {
                $where .= " and rg.id in (".implode(",", $params["resource_group_ids"]).")";
            }
        }
        if(isset($params["osg_grid_type_id"])) {
            $where .= " and osg_grid_type_id = ".$params["osg_grid_type_id"];
        }
        if(isset($params["resourcegroup"])) {
            $where .= " and id = ".$params["resourcegroup"];
        }
        if(isset($params["site_id"])) {
            $where .= " and site_id = ".$params["site_id"];
        }
        $sql = "SELECT rg.*, t.name as grid_type, t.description as grid_type_description FROM resource_group rg JOIN osg_grid_type t ON rg.osg_grid_type_id = t.id $where order by name";
        return $sql;
    }
    public function key() { return "id"; }


    //return resource gruops IDS that user is listed in the resource_contact
    public function getMyResourceGroupIDS() {
        $uid = user()->getPersonID();
        $rows = db("oim")->query("SELECT resource_group_id FROM resource JOIN resource_contact rc ON rc.resource_id = resource.id WHERE rc.contact_id = $uid");
        $ids = array();
        foreach($rows as $row) {
            $rgid = (int)$row->resource_group_id;
            if(!in_array($rgid, $ids)) {
                $ids[] = $rgid;
            }
        }
        return $ids;
    }
}
