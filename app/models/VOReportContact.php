<?php
/*#################################################################################################

Copyright 2009 The Trustees of Indiana University

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in
compliance with the License. You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License
is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
implied. See the License for the specific language governing permissions and limitations under the
License.

#################################################################################################*/

class VOReportContact extends CachedIndexedModel
{
    public function ds() { return "oim"; }
    public function sql($params)
    {
        $where = "";
        if(isset($params["vo_report_name_id"])) {
            $where = " AND vo_report_name_id = ".$params["vo_report_name_id"];
        }
        return 
        "SELECT dn.dn_string as dn, v.*, c.* FROM vo_report_contact v ".
        " join contact c on v.contact_id = c.id ".
        " left join dn on dn.contact_id = c.id WHERE dn.disable = 0 $where";
    }
    public function key() { return "vo_report_name_id"; }
}
