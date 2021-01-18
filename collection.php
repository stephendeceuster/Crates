<?php
// Checkt of er errors zijn
//error_reporting( E_ALL );
//ini_set( 'display_errors', 1 );

// Haalt de formules binnen
require_once "lib/autoload.php";

// Haalt zoekresultaat uit form
$result = '';
if ($_GET['search']) {
    $result= $_GET['search'];
}
// str replace zoekresultaat
$html = file_get_contents("./templates/zoekresultaten.html");

$html = str_replace("%ZOEKTERM%", $result, $html);
//print $searchbar;

// Haalt de albumresultaten uit de db, volgens het het id van de user
$sql = "select use_id from user ";
$sql .= "where use_email = '" . $_SESSION['user']['use_email'] . "'";
// use_id
$id = GetData($sql);
$id[0]['use_id'];

//get data
$sql = "select inh_use_id, alb_naam, alb_img, art_naam,  inh_lis_id, alb_id, art_id from user_album ";
$sql .= "left join album on inh_alb_id = alb_id ";
$sql .= "left join artist on alb_art_id = art_id ";
$sql .= "where inh_use_id = " . $id[0]['use_id'] . " and inh_lis_id = 1 ";
$sql .= "order by alb_naam asc ";
$data = GetData($sql);
//get template
$template = file_get_contents("templates/zoekresultaten-kolom.html");

//merge
$output = MergeViewWithData($template, $data);
$html = str_replace("%searchresult%", $output, $html);
print $html;



