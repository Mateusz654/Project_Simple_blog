<?php

 // Konfiguracja
 $posts_file = 'wypowiedzi.txt';
 $topic_file = 'tematy.txt';
 $separator = ",";

// Załadowanie funkcji
include("data.php");


//Utworzenie plików
if( !is_file($posts_file) ) file_put_contents($posts_file,'');
if( !is_file($topic_file) ) file_put_contents($topic_file,'');

if(!isset($_SESSION['zalogowano'])){
  if(isset($_POST['login_register'])){
    register($_POST['login_register'], $_POST['username'], $_POST['pass1'], $_POST['pass2']);
  }
  else if(isset($_POST['login'])&&isset($_POST['password'])){
    login($_POST['login'], $_POST['password']);
  }
  else{
    include('start_page.php');
    exit();
  }
}
else if(isset($_SESSION['zalogowano'])&&$_SESSION['zalogowano']==true){
 

// zapis tematu
if( isset($_POST['topic']) and $_POST['topic']!="" and $_POST['topic_body']!="" and !isset($_GET['edit_form'])){
  $res = put_topic($_POST['topic'], $_POST['topic_body'], $_SESSION['username'], $topic_file, $separator);
  header("Location: index.php");exit;
}   

//kasowanie tematu

if(isset($_GET['topicid'])&&isset($_GET['cmd'])&&$_GET['cmd']=='delete_topic'){
  delete_topic($_GET['topicid'],$topic_file,$posts_file, $separator);
  header('Location: index.php');
  exit();
}

//edytowanie tematu
if(isset($_GET['topicid'])&&isset($_GET['edit_form'])&&$_GET['edit_form']=='click'){
   edit_topic($_GET['topicid'],$topic_file,$separator);
   exit();
}



// zapis lub aktualizacjia postu
if( isset($_POST['post']) and $_POST['post']!=""){
  if( $_POST['postid']!='' ){
     $res = update_post( $_POST['postid'], $_POST['post'], $_SESSION['login'],  $posts_file, $separator );
  }else{
     $res = put_post( $_GET['topic'], $_POST['post'],  $_SESSION['login'], $posts_file, $separator);
  }
  header("Location: index.php?topic=".$_GET['topic'] );exit;
}   

// kasowanie postu
if( isset($_GET['cmd']) and $_GET['cmd']=="delete" and $_GET['id']!="" and $_GET['topic']!=""){
  delete_post($_GET['id'], $posts_file, $separator);
  header("Location: index.php?topic=".$_GET['topic'] );exit;
}

// pobranie danych postu w celu ich edycji
if( isset($_GET['cmd']) and $_GET['cmd']=="edit" and $_GET['id']!="" and $_GET['topic']!=""){
  $post = get_post($_GET['id'], $posts_file, $separator);
}else{
  $post=false;
}  

// Pobranie wszystkich tematów
$topics = get_topics($topic_file, $separator);

//logout
if(isset($_GET['cmd'])&&$_GET['cmd']=="logout"){
  logout();
}
}
//usuwanie użytkownika
if(isset($_GET['cmd']) && $_GET['cmd']=="deleteuser"){
  delete_user($_GET['login']);
  exit();
}
//zmiana poziomu użytkownika
if(isset($_GET['cmd']) && $_GET['cmd']=="changelevel"){
  change_level($_GET['login']);
  exit();
}

//-------------------------------------------------------------
// Prezentacja
//-------------------------------------------------------------
if( isset($_GET["topic"]) and $_GET["topic"]!='' ) {  
   $posts = get_posts($_GET["topic"], $posts_file, $separator);
   $topic= $topics[$_GET["topic"]];
   include('wypowiedzi.php');
} else { // widok tematów  
   // policz posty w tematach
   $posts_count = get_posts_count($posts_file, $separator);
   include('tematy.php'); 
}
?>