<?php
/**
 * 
 */
include '../../../includes/basics/authReq.php';
include '../../../includes/GeneralFunctions.php';
include '../../../includes/basics/settings.php';
/**
 * Params: 
 *  a. 101 : Page number
 *  b. 102 : query
 *  c. 103 : sort
 * Types of request
 * 1. The very first request
 *    - Will send the list, meta data
*     - Identified by page < 0/is null & query = null
 * 2. Requests after first one/with query
 *    - Identified by page number > 0
 * 3. Query requests
 *    - Search will be done on DB
 *    - Identified by Query != null
 * 
* Control::
 */
$Req = sanitizeArrayValues($_POST, [
    101,
    102,
    103
]);
$Res = [];
$query = "SELECT id, firstN, midN, lastN, email, id as appln, '3' as inpro, at from students WHERE %l ORDER BY at desc %l";
$where = new WhereClause('and');
$where->add("uid=%i", $_SESSION["user"]["id"]);

if($Req[102] != null){
    if(strlen($Req[102])> 50){die();}
    $q = explode(" ", $Req[102], 3);
    $subclause = $where->addClause('or');
    for ($i=0; $i < count($q); $i++) {
        $subclause->add('firstN LIKE %ss', $q[$i]);
        $subclause->add('midN LIKE %ss', $q[$i]);
        $subclause->add('lastN LIKE %ss', $q[$i]);
        $subclause->add('email LIKE %ss', $q[$i]);
    }
}

$Req[101] = abs((int)$Req[101]);
$limit = DB::parse("LIMIT %i, %i", Settings::$OFFSET * $Req[101], Settings::$PERPAGE);
$Res["data"] = DB::query($query, $where, $limit);

if($Req[101] == 0){
    $Res["meta"] = DB::queryFirstRow("SELECT count(*) as total FROM students where %l", $where);
    $Res["meta"]["per"] = Settings::$PERPAGE;
}

echo json_encode($Res);