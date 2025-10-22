<?php
    define("hostname","localhost");
    define("database","banquedesang");
    define("username","root");
    define("password","");

    $dsn='mysql:dbname='.database.';host='.hostname.';charset=utf8';

    $bdd= new PDO($dsn,username,password);

    $bdd->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $bdd->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
?>