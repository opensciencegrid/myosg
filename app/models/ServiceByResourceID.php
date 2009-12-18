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

class ServiceByResourceID extends CachedIndexedModel
{
    public function ds() { return "oim"; }
    public function sql($param)
    {
        //return "SELECT rs.*, s.* FROM resource_service rs join service s on rs.service_id = s.id WHERE service_id IN (SELECT id FROM service WHERE service_group_id=1)";
        return "SELECT rs.*, s.* FROM resource_service rs join service s on rs.service_id = s.id WHERE service_id IN (SELECT id FROM service)";
    }
    public function key() { return "resource_id"; }
}
