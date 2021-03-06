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

//global variable to store list of filter variables
$g_filters = array();

function uwa()
{
    if(isset($_REQUEST["uwa"])) {
        return true;
    }
    return false;
}

//returns a unique id number for div element (only valid for each session - don't store!)
function getuid()
{
    if(isset($_SESSION['next_uid'])) {
        $next_uid = $_SESSION['next_uid'];
        $_SESSION["next_uid"] = $next_uid + 1;
        return "id".($next_uid+rand()); //add random number to avoid case when 2 different sessions are used
    } else {
        $_SESSION["next_uid"] = 1000; //let's start from 1000
        return $_SESSION["next_uid"];
    }
}

function outputAjaxToggle($title, $url)
{
    $out = "";
    $divid = getuid();
    $hide_script = "$('#$divid').slideUp();$('#${divid}_hide').hide();$('#${divid}_show').show();";
    $show_script = "$('#$divid').slideDown();$('#${divid}_hide').show();$('#${divid}_show').hide();";

    //load button
    $out .= "<div id=\"${divid}_load\" class=\"button\" onclick=\"\$('#${divid}_loading').show(); \$('#$divid').load('$url', function() {\$('#${divid}_load').hide();\$('#${divid}_hide').show();\$('#${divid}').slideDown();});\"><img src='".fullbase()."/images/plusbutton.gif'/> $title";
    $out .= " <span id=\"${divid}_loading\" class=\"hidden\"><img src='".fullbase()."/images/loading_animation_small.gif' height='10'/></span>";
    $out .= "</div>";

    //hide button
    $out .= "<div id=\"${divid}_hide\" class=\"button hidden\" onclick=\"$hide_script\"><img src='".fullbase()."/images/minusbutton.gif'/> $title</div>";

    //show button
    $out .= "<div id=\"${divid}_show\" class=\"button hidden\" onclick=\"$show_script\"><img src='".fullbase()."/images/plusbutton.gif'/> $title</div>";

    //content area
    $out .= "<div id=\"${divid}\" class=\"hidden\"></div>";
    return $out;
}

function outputToggle($show, $hide, $content, $open_by_default = false)
{
    $divid = getuid();
    ob_start();

    if(true) {
        $showbutton_style = "button";
        $hidebutton_style = "button";
        $detail_style = "detail";
        if($open_by_default) {
            $showbutton_style .= " hidden";
        } else {
            $hidebutton_style .= " hidden";
            $detail_style .= " hidden";
        }
        ?>
        <div id='show_<?php echo $divid?>' class='my-toggler <?php echo $showbutton_style?>'><img src='<?php echo fullbase()?>/images/plusbutton.gif'/> <?php echo $show?></div>
        <?php if(!is_null($hide)) {?>
            <div id='hide_<?php echo $divid?>' class='my-toggler <?php echo $hidebutton_style?>'><img src='<?php echo fullbase()?>/images/minusbutton.gif'/> <?php echo $hide?></div>
        <?php } ?>
        <div class='<?php echo $detail_style?>' id='detail_<?php echo $divid?>'><?php echo $content?></div>
        <script type='text/javascript'>
        $('#show_<?php echo $divid?>').click(function() {
            $('#detail_<?php echo $divid?>').slideDown("normal");
            $('#show_<?php echo $divid?>').hide();
            $('#hide_<?php echo $divid?>').show();
        });
        $('#hide_<?php echo $divid?>').click(function() {
            $('#detail_<?php echo $divid?>').slideUp();
            $('#hide_<?php echo $divid?>').hide();
            $('#show_<?php echo $divid?>').show();
        });
        </script>
        <?php
    }

    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

function agoCalculation($timestamp)
{
    if($timestamp === null) return null;
    $ago = time() - $timestamp;
    return humanDuration($ago);
}
function humanDuration($ago)
{
    if($ago < 60) return $ago." seconds";
    if($ago < 60*60) return floor($ago/60)." minutes";
    if($ago < 60*60*24) return floor($ago/(60*60))." hours";
    return floor($ago/(60*60*24))." days";
}

function outputSelectBox2($field_id, $kv)
{
    global $g_pagename;
    global $g_filters;

    $g_filters[] = $field_id;
?>
    <select name="<?php echo $field_id?>">
<?php
    $current_value = @$_REQUEST[$field_id];
    foreach($kv as $value=>$name) {
        $selected = "";
        if($value == $current_value) {
            $selected = "selected=selected";
        }
        echo "<option value=\"$value\" $selected>$name</option>\n";
    }
?>
    </select>
<?php
}

function outputSelectBox($field_id, $field_name, $model, $value_field, $name_field)
{
    global $g_pagename;
    global $g_filters;

    $g_filters[] = $field_id;

?>
    <?php echo $field_name?>:
    <p>
    <select id="filter_<?php echo $field_id?>" onchange="query.<?php echo $field_id?>=$(this).val(); document.location='<?php echo fullbase()."/$g_pagename?";?>'+jQuery.param(query);">
    <option value="">(All)</option>
    <?php
    $rows = $model->get();
    $current_value = @$_REQUEST[$field_id];
    foreach($rows as $row) {
        $value = $row->$value_field;
        $name = $row->$name_field;
        $selected = "";
        if($value == $current_value) {
            $selected = "selected=selected";
        }

        //truncate name so that it won't be too long
        if(strlen($name) > 25) $name = substr($name, 0, 25)."...";

        echo "<option value=\"$value\" $selected>$name</option>\n";
    }
    ?>
    </select>
    </p>
<?php
}

function outputCheckboxList($prefix, $items)
{
    global $g_pagename;
    global $g_filters;


    echo "<p>";
    foreach($items as $id=>$value) {
        $name = $prefix."_".$id;

        $g_filters[] = $name;
        $current_value = @$_REQUEST[$name];
        $selected = "";
        if($current_value == "true") {
            $selected = "checked=\"checked\"";
        }
        $script = "query.$name=this.checked;document.location='".fullbase()."/$g_pagename?"."'+jQuery.param(query);";
        echo "<input type=\"checkbox\" name=\"$name\" onclick=\"$script\" $selected/> $value<br/>";
    } 
    echo "</p>";
}

function outputClearFilterButton()
{
    global $g_pagename;
    global $g_filters;

    //we need to clear only the variables that are set via filters
    $query = $_SERVER["QUERY_STRING"];
    $query_s = split("&", $query);
    $cleared_q = "";
    foreach($query_s as $q) {
        $q_s = split("=", $q);
        if(!in_array($q_s[0], $g_filters)) {
            if($cleared_q == "") {
                $cleared_q .= "?";
            } else {
                $cleared_q .= "&amp;";
            }
            $cleared_q .= $q;
        }
    }

    ?>
    <p><a href="#" onclick="document.location='<?php echo fullbase()."/$g_pagename$cleared_q";?>';">Clear All Filters</a></p>
    <?php
}

function checklist($id, $title, $kv)
{
    //output title check box
    $checked = "";
    if(isset($_REQUEST[$id])) { 
        $checked = "checked=checked"; 
        ?>
        <script type="text/javascript">
        $(document).ready(function() {
            $("#<?php echo $id?>__list").show();
        });
        </script>
        <?php
    }
    $title = "<input type=\"checkbox\" name=\"$id\" $checked onclick=\"if(this.checked) {\$('#${id}__list').show('normal');} else {\$('#${id}__list').hide('normal');}\"/> <span>$title</span><br/>";

    //determine list class
    $c = "hidden ";
    if(count($kv) > 8) { 
        $c .= "scrolled_list ";
    }

    //output list
    $list = "";
    $list .= "<div class=\"$c list\" id=\"${id}__list\">";
    foreach($kv as $key=>$value) {
        $name = "${id}_$key";
        $checked = "";
        if(isset($_REQUEST[$name])) {
            $checked = "checked=checked";
        }
        $list .= "<input type=\"checkbox\" name=\"$name\" $checked/> <span>$value</span><br/>";
    }
    $list .= "</div>";

    return "<div id=\"$id\">".$title.$list."</div>";
}

//should be renamed to select2
function fblist($id, $title, $kv)
{
    $out = "";

    //output select2 toggler
    $checked = "";
    if(isset($_REQUEST[$id])) { 
        $checked = "checked=checked"; 
        ?>
        <script type="text/javascript">
        $(document).ready(function() {
            $("#<?php echo $id?>__list").show();
        });
        </script>
        <?php
    }
    $out .= "<input type=\"checkbox\" name=\"$id\" $checked onclick=\"if(this.checked) {\$('#${id}__list').show('normal');} else {\$('#${id}__list').hide();}\"/> <span>$title</span><br/>";

    //output select2
    $out .= "<div class=\"hidden fblist_container\" id=\"${id}__list\">\n";
    $out .= "<select multiple id=\"${id}__select2\" name=\"${id}_sel[]\">\n";
    /*
    if(isset($_REQUEST[$id])) {
        error_log("################################################################################# $id");
        error_log(print_r($_REQUEST[$id], true));
    }
    */
    foreach($kv as $key=>$value) {
        $itemid = "${id}_$key";
        $selected = "";
        if(isset($_REQUEST[$id."_sel"]) && in_array($key, $_REQUEST[$id."_sel"])) {
            $selected = "selected";
        }
        $name = str_replace(array("\n", "\r"), "", htmlsafe($value[0]));
        //$desc = str_replace(array("\n", "\r"), "", htmlsafe($value[1]));
        $first = false;
        $out .= "<option $selected value=\"$key\">$name</option>\n";
    }
    $out .= "</select>\n";
    $out .= "<script>$(function() { $(\"#${id}__select2\").select2({width: '100%', placeholder: 'Select Items'}); });</script>";
    $out .= "</div>\n";//fblist_container

    return $out;
}

/* just a quick backup 
function fblist($id, $title, $kv)
{
    $out = "";

    //output title check box
    $checked = "";
    if(isset($_REQUEST[$id])) { 
        $checked = "checked=checked"; 
        ?>
        <script type="text/javascript">
        $(document).ready(function() {
            $("#<?php echo $id?>__list").show();
        });
        </script>
        <?php
    }
    $out .= "<input type=\"checkbox\" name=\"$id\" $checked onclick=\"if(this.checked) {\$('#${id}__list').show('normal');} else {\$('#${id}__list').hide();}\"/> <span>$title</span><br/>";

    //output list editor
    $out .= "<div class=\"hidden fblist_container\" id=\"${id}__list\"><div class=\"fblist\" style=\"position: relative;\" onclick=\"$(this).find('.autocomplete').focus(); return false;\">";

    //output script
    $delete_url = "images/delete.png";
    $script = "<script type='text/javascript'>$(document).ready(function() {";
    $script .= "var ${id}__listdata = [";
    $first = true;
    $pre_selected ="";
    foreach($kv as $key=>$value) {
        $itemid = "${id}_$key";
        if(isset($_REQUEST[$itemid])) {
            $pre_selected .= "<div><img onclick=\"$(this).parent().remove();\" src=\"$delete_url\"/>".$value[0]."<input type=\"hidden\" name=\"$itemid\"/ value=\"on\"></div>";
        }
        $name = str_replace(array("\n", "\r"), "", htmlsafe($value[0]));
        $desc = str_replace(array("\n", "\r"), "", htmlsafe($value[1]));
        if(!$first) {
            $script .= ",\n";
        }
        $first = false;
        $script .= "{ id: \"$itemid\", name: \"$name\", desc: \"$desc\" }";
    }
    $script .= "];";
    $script .= <<<BLOCK
    $("#${id}__list input.autocomplete").autocomplete(${id}__listdata, {
        max: 9999999,
        minChars: 0,
        mustMatch: true,
        matchContains: true,
        width: 280,
        formatItem: function(item) {
            if(item.desc == "") return item.name; 
            return item.name + " (" + item.desc + ")";
        }
    }).result(function(event, item) {
        if(item != null) {
            $(this).val("");
            $(this).before("<div><img onclick=\"$(this).parent().remove();\" src=\"$delete_url\"/>"+item.name+"<input type=\"hidden\" name=\""+item.id+"\" value=\"on\"/></div>");
        }
    });
});</script>
BLOCK;

    $out .= $pre_selected;
    $out .= "<input type='text' class='autocomplete' style='background-color: transparent;' onfocus='$(\"#${id}__acnote\").fadeIn(\"slow\");' onblur='$(\"#${id}__acnote\").fadeOut(\"slow\");'/>";
    $out .= $script;

    //display note
    $out .= "<p id=\"${id}__acnote\" class=\"hidden\" style=\"z-index: -1; position: absolute; color: #999; font-size: 9px; right: 5px; bottom: 5px; text-align: right; font-size: 10px;line-height: 100%;\">Double click to show all</p>";

    $out .= "</div>";
    $out .= "</div>";

    return $out;
}
*/

function radiolist($id, $title, $kv, $default)
{
    //output title check box
    $checked = "";
    if(isset($_REQUEST[$id])) { 
        $checked = "checked=checked"; 
        ?>
        <script type="text/javascript">
        $(document).ready(function() {
            $("#<?php echo $id?>__list").show();
        });
        </script>
        <?php
    }
    $title = "<input type=\"checkbox\" name=\"$id\" $checked onclick=\"if(this.checked) {\$('#${id}__list').show('normal');} else {\$('#${id}__list').hide();}\"/> <span>$title</span><br/>";

    //determine list class
    $c = "hidden ";
    if(count($kv) > 8) { 
        $c .= "scrolled_list ";
    }

    //output list
    $list = "";
    $list .= "<div class=\"$c list\" id=\"${id}__list\">";

    //set default 
    $current_value = $default; 
    $name = "${id}_value";
    if(isset($_REQUEST[$name])) {
        $current_value = $_REQUEST[$name];
    }

    foreach($kv as $key=>$value) {
        $checked = "";
        if($current_value == $key) {
            $checked = "checked=checked";
        }
        $list .= "<input type=\"radio\" name=\"$name\" value=\"$key\" $checked/> <span>$value</span><br/>";
    }
    $list .= "</div>";

    return "<div id=\"$id\">".$title.$list."</div>";
}

function helpbutton($type)
{
    return "<a target=\"_help\" href=\"https://twiki.grid.iu.edu/bin/view/Operations/OIMTermDefinition#$type\">".tooltip("Click to show documentation")."</a>";
}

function tooltip($msg) {
    //return "<img style=\"cursor: pointer;\" align=\"top\" src=\"".fullbase()."/images/help.png\" alt=\"$msg\" title=\"$msg\"/>";
    return "<img align=\"top\" src=\"".fullbase()."/images/help.png\" alt=\"$msg\" title=\"$msg\"/>";
}


function nullImage() {
    return "<img alt=\"null\" src=\"".fullbase()."/images/null.png\"/>";
}

function externalurl($url)
{
    if($url == "") {
        return "";
    }
    return "<a target=\"_blank\" href=\"$url\">".htmlsafe($url)."</a>";
}

function emailaddress($email)
{
    if($email == "") {
        return "";
    }
    return "<a class=\"mailto\" href=\"mailto:$email\">".htmlsafe($email)."</a>";
}

function htmlsafe($str)
{
    return htmlspecialchars($str, ENT_NOQUOTES, "UTF-8");
}
