<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

$public_access = true;

// Haalt de formules binnen
require_once "lib/autoload.php";

if (isset($_SESSION['user']['use_id'])) {
    header("Location: ./collection.php");
}

$title = 'Registreer';
$html = file_get_contents('./templates/head.html');

if ( !empty($old_post) )
{
    $data = [ 0 => [
        "use_voornaam" => $old_post['use_voornaam'],
        "use_naam" => $old_post['use_naam'],
        "use_email" => $old_post['use_email'],
        "use_password" => $old_post['use_password'],
        "use_password2" => $old_post['use_password2']
        ]
    ];
}
else $data = [ 0 => [ "use_voornaam" => "", "use_naam" => "", "use_email" => "", "use_password" => "", "use_password2" => "" ]];

//get template
$html .= file_get_contents("templates/register.html");

//add extra elements
$extra_elements['csrf_token'] = GenerateCSRF( "register.php"  );

//merge
$html = str_replace("%title%", $title, $html);
$html = MergeViewWithData( $html, $data );
$html = MergeViewWithExtraElements( $html, $extra_elements );
$html = MergeViewWithErrors( $html, $errors );
$html = RemoveEmptyErrorTags( $html, $data );
$html .= file_get_contents('./templates/footer.html');

print $html;
