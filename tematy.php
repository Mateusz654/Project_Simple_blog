<!DOCTYPE html>
<html>
<head>
    <title>WWW i języki skryptowe</title>
    <meta charset="utf-8">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
      <h1>
        Zadanie 1
      </h1>
      <h2>
          Proste forum
      </h2>
    </header>
    <nav>
            <a href="index.php">Home</a>
            <?php for($n=1;$n<=10;$n++) { if( is_dir("../zadanie".$n) ) { ?>
            <a href="../zadanie<?=$n?>">Zadanie <?=$n?></a>
            <?php } } ?>
    </nav>
<section class="user-info">
  <nav>
  <?php if($_SESSION['level']=='admin'){ 
  echo('
  <form method="post" class="list">
                <button type="submit" name="toggle_lista">Lista uczestników</button>
  </form>
  ');
  }
  ?>  
  <p>Zalogowany jako: <?php echo($_SESSION['username'].' ('.$_SESSION['login'].')') ?> <a href="index.php?cmd=logout" >WYLOGUJ</a></p>
  </nav>
  <?php toggle_users_list();?>
</section>
<section>
<?php if( !$topics ){ ?>
  <p>To forum nie zawiera jeszcze żadnych tematów!</p>
<?php }else{ ?>
  <p>Możesz dodac nowy temat za pomocą <a href="#topic_form">formularza</a>.</p>
<?php foreach($topics as $k=>$v){ ?>
  <article class="topic">
    <header> </header>
    <div><a href="?topic=<?=$k?>"><?=htmlentities($v['topic'])?></a></div>
    <nav>
    <?php
    if($_SESSION['level']=='admin'){ 
      echo('<a href="?topicid='.$k.'&cmd=edit_topic">EDYTUJ</a><a class="danger" href="?topicid='.$k.'&cmd=delete_topic">KASUJ</a>');
    }
    ?>
    </nav>
    <footer>ID: <?=$v['topicid']?>, Autor: <?=htmlentities($v['login'])?>, 
        Utworzono: <?=$v['date']?>, Liczba wpisów: <?=isset($posts_count[$v['topicid']])?$posts_count[$v['topicid']]:0;?>
    </footer>
  </article>
<?php } } ?>
<?php 

if(!isset($_GET['cmd']) or $_GET['cmd']!='edit_topic')
{
  $link="index.php";
}
else if(isset($_GET['cmd']) or $_GET['cmd']=='edit_topic')
{ 
  $link = '"index.php?topicid='.$_GET['topicid'].'&edit_form=click"';
}
?>
<form action=<?=$link?> method="post">
     <a name="topic_form"></a>
     <header><h2><?php if(!isset($_GET['cmd']) or $_GET['cmd']!='edit_topic'){echo('Dodaj nowy temat do dyskusji');}else{echo('Edytuj wybrany temat');}?></h2></header>  
     <input type="text" name="topic" placeholder="Nowy temat"><br>
     <textarea name="topic_body" cols="80" rows="10" placeholder="Opis nowego tematu"></textarea><br>
     <button type="submit">Zapisz</button>
  </form>
</section>

<footer>
Ostatni wpis na formu powstał dnia: <?=get_last_post_date($posts_file, $separator);?>
</footer>
</body>
</html>    