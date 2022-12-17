<?php
/**
 * 
 */
include '../../../includes/basics/authReq.php';
include '../../../includes/GeneralFunctions.php';

$Req = sanitizeArrayValues($_POST, [
    101,102,103,104,105,106,107,108
]);
$error = false;
$error = $error || lengthCheck(
    [
        101 => 300,
        102 => 300,
        103 => 300,
        104 => 1,
        105 => 12,
        106 => 7,
        107 => 12,
        108 => 250
    ],
    $Req
);
if($error){
    printError("Length Check Failed!");
    die();
}
//Validate that same email v/s user pair doesnot exist
$error = $error || DB::queryFirstField("SELECT count(*) FROM students where uid = %i AND email = %s", $_SESSION["user"]["id"],$Req[108]);
if($error){
    printError("Student already exists!");
    die();
}
//Daily insert rate limit
$error = $error || DB::queryFirstField("SELECT count(*) FROM students where uid = %i AND at > DATE_SUB(NOW(), INTERVAL 24 HOUR)", 1,$Req[108]) > 20;
if($error){
    printError("Daily limit reached!");
    die();
}
//Insert into db
$insert = DB::insert("students",[
    "uid" => $_SESSION["user"]["id"],
    "firstN" => $Req[101],
    "midN" => $Req[102],
    "lastN" => $Req[103],
    "email" => $Req[108]
]);
if($insert){
    $id = DB::queryFirstField("SELECT id from students WHERE uid=%i AND email=%s", $_SESSION["user"]["id"], $Req[108]);
    DB::insertUpdate("stu_data",[
        "text_fields" => json_encode($Req)
    ]);
}
//print success
printError("Student created!", 0);
?>