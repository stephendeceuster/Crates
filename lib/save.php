<?php
//error_reporting( E_ALL );
//ini_set( 'display_errors', 1 );
require_once "autoload.php";

SaveFormData();

function SaveFormData()
{
    if ( $_SERVER['REQUEST_METHOD'] == "POST" )
    {
        //controle CSRF token
        if ( ! key_exists("csrf", $_POST)) die("Missing CSRF");
        if ( ! hash_equals( $_POST['csrf'], $_SESSION['lastest_csrf'] ) ) die("Problem with CSRF");

        $_SESSION['lastest_csrf'] = "";

        //sanitization
        $_POST = StripSpaces($_POST);
        $_POST = ConvertSpecialChars($_POST);

        $table = $pkey = $update = $insert = $where = $str_keys_values = "";

        //get important metadata
        if ( ! key_exists("table", $_POST)) die("Missing table");
        if ( ! key_exists("pkey", $_POST)) die("Missing pkey");

        $table = $_POST['table'];
        $pkey = $_POST['pkey'];

        //validation
        $sending_form_uri = $_SERVER['HTTP_REFERER'];
        CompareWithDatabase( $table, $pkey );

        //Validaties voor het registratieformulier
        if ( $table == "user" )
        {
                ValidateUsrPassword( $_POST['use_password'] );
                ValidateUsrEmail( $_POST['use_email'] );
                CheckUniqueUsrEmail( $_POST['use_email'] );
                if ($_POST['use_password'] !== $_POST['use_password2']) {
                    $_SESSION['errors']['use_password2_error'] = "Gelieve 2x hetzelfde wachtwoord in te geven.";
                }
                if (strlen($_POST['use_voornaam']) === 0) {
                    $_SESSION['errors']['use_voornaam_error'] = "Gelieve uw voornaam in te vullen.";
                }
                if (strlen($_POST['use_naam']) === 0) {
                    $_SESSION['errors']['use_naam_error'] = "Gelieve uw achternaam in te vullen.";
                }
        }

        //terugkeren naar afzender als er een fout is
        if ( isset($_SESSION['errors'] ) and count($_SESSION['errors']) > 0 )
        {
            $_SESSION['old_post'] = $_POST;
            header( "Location: " . $sending_form_uri ); exit();
        }

        //insert or update?
        if ( isset($_POST["$pkey"]) and $_POST["$pkey"] > 0 ) $update = true;
        else $insert = true;

        if ( $update ) $sql = "UPDATE $table SET ";
        if ( $insert ) $sql = "INSERT INTO $table SET ";

        //make key-value string part of SQL statement
        $keys_values = [];

        foreach ( $_POST as $field => $value )
        {
            //skip non-data fields
            if ( in_array( $field, [ 'table', 'pkey', 'afterinsert', 'afterupdate', 'csrf', 'use_password2' ] ) ) continue;

            //handle primary key field
            if ( $field == $pkey )
            {
                if ( $update ) $where = " WHERE $pkey = $value ";
                continue;
            }

            if ( $field == "use_password" ) //encrypt usr_password
            {
                $value = password_hash( $value, PASSWORD_BCRYPT );
            }

            $keys_values[] = " $field = '$value' " ;
        }

        $str_keys_values = implode(" , ", $keys_values );

        //extend SQL with key-values
        $sql .= $str_keys_values;

        //extend SQL with WHERE
        $sql .= $where;

        //run SQL
        $result = ExecuteSQL( $sql );

        if ( $result AND $table == "user" )
        {
            $_SESSION['message'][0] = "Hey, " . ucwords(strtolower($_POST['use_voornaam'])) . "! <br>Bedankt voor uw registratie, <br> je kan nu hier inloggen.";
        }

        if ( $result AND $table == "user_album" )
        {
            $_SESSION['message'][0] = "Score/commentaar aangepast";
        }

        //redirect after insert or update
        if ( $insert AND $_POST["afterinsert"] > "" ) header("Location: ../" . $_POST["afterinsert"] );
        if ( $update AND $_POST["afterupdate"] > "" ) header("Location: ../" . $_POST["afterupdate"] );
    }
}
