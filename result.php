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

// Haalt de albumresultaten uit de db,
// alles dus als er geen zoekopdracht wordt uitgevoerd

//get data
$sql = "select * from album
left join artist on alb_art_id = art_id 
where art_naam like '%$result%' or alb_naam like '%$result%'";
$data = GetData($sql);
//get template
$template = file_get_contents("templates/zoekresultaten-kolom.html");

//merge
$output = MergeViewWithData($template, $data);
$html = str_replace("%searchresult%", $output, $html);
print $html;
