<?php
//error_reporting( E_ALL );
//ini_set( 'display_errors', 1 );

require_once "lib/autoload.php";

$html = file_get_contents("templates/head.html");
$html .= file_get_contents("templates/header.html");
$html .= file_get_contents("templates/songs_form.html");
$html .= file_get_contents('templates/footer.html');

// stel header in voor de form
$action = "./lib/save_songs.php?alb_id=" . $_GET['alb_id'];
$html = str_replace("%action%", $action, $html);

// return
$return = "album.php?alb_id=" . $_GET['alb_id'];
$html = str_replace("%return%", $return, $html);

// CSRF
$CSRF = GenerateCSRF("album.php");
$html = str_replace("%csrf_token%", $CSRF , $html);


$query = "select son_title, alb_naam from songs ";
$query .= "left join album on son_alb_id = alb_id ";
$query .= "where alb_id = " . $_GET['alb_id'];

$data= GetData($query);

$songs = MakeSongform( $data );

$html = str_replace("%songs%", $songs, $html);

$title = "Pas Nummers aan";
$html = str_replace("%title%", $title, $html);
$html = str_replace('%alb_naam%', $data[0]['alb_naam'], $html);


print $html;