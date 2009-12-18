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

function getLastNDayRange($nday = 1)
{
    return array(time()-3600*24*$nday, time());
}

function getLastNDayRangeProper($nday = 1)
{
    $today = (int)(time() / 86400) * 86400;
    $before = $today - 3600*24*$nday;
    return array($before, $today);
}

function computePeriodStartEnd($dirty_period)
{
    switch($dirty_period) {
    case "week":
        $end_time = (int)(time() / 86400) * 86400 - 86400;
        $start_time = $end_time - 86400*6;
        break;
    case "30days":
        $end_time = (int)(time() / 86400) * 86400 - 86400;
        $start_time = $end_time - 86400*29;
        break;
    default:
        throw new exception("bad period: $dirty_period");
    }

    return array($start_time, $end_time);
}

