<?php
include '../includes/Guard.php';
include '../includes/basics/settings.php';

$Res = [];
$query = "SELECT id, firstN, midN, lastN, email, id as appln, '3' as inpro, at from students WHERE %l ORDER BY at desc %l";
$where = "uid = " . $_SESSION["user"]["id"];
$limit = DB::parse("LIMIT %i, %i", 0, Settings::$PERPAGE);
$Res["data"] = DB::query($query, $where, $limit);
$Res["meta"] = DB::queryFirstRow("SELECT count(*) as total FROM students where uid=%i", $_SESSION["user"]["id"]);
$Res["meta"]["per"] = Settings::$PERPAGE;

echo file_get_contents("../templates/_main/header-1.html");
echo file_get_contents("../templates/_main/sidebar-1.html");
echo file_get_contents("../templates/_main/nav-1.html");
echo str_replace("{{Res}}", json_encode($Res), file_get_contents("../templates/r/students.html"));
echo file_get_contents("../templates/_main/footer-1.html");
exit;
