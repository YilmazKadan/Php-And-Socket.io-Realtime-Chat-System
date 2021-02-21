<?php
    session_start();
    header('Content-type: text/html; charset=utf-8');
    try{
        $db = new PDO("mysql:host=localhost;dbname=chat_application",'root','');
        $db->query("SET NAMES UTF8");
    }
    catch (PDOException $e){
        echo $e->getMessage();
    }
