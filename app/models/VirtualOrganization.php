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

class VirtualOrganization extends CachedIndexedModel
{
    public function ds() { return "oim"; }
    public function sql($params)
    {
        $where = "where 1=1";
        if(isset($params["sc_id"])) {
            $where .= " and sc_id = ".$params["sc_id"];
        }
        if(isset($params["vo_id"])) {
            $where .= " and id = ".$params["vo_id"];
        }
        $sql = "SELECT * FROM vo $where order by name";
        return $sql;
    }
    public function key() { return "id"; }
}
