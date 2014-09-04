<?php
include_once 'db_connect.php';

if(!isset($_POST)) {
    echo "false";
    die();
}
$uid = intval($_POST['uid']);
$followid = intval($_POST['followid']);
$type = intval($_POST['type']);
$action = $_POST['action'];

if(0 == $action)
    $sql = "INSERT INTO relation(uid,follow_id,type) VALUES(".$uid.",".$followid.",".$type.")";
else
    $sql = "DELETE FROM relation WHERE uid=".$uid." AND follow_id=".$followid." AND type=".$type;
if($mysqli->query($sql)) {
    echo "true";
} else {
    echo "false: ".$mysqli->error;
} 
