<?php
session_start();

//Logowanie
function login($login, $pass){
   $users = json_decode(file_get_contents('users.json'),true);
   foreach($users as $user){
      if($user['login']==$login&&$user['password']==md5($pass)){
         $_SESSION['login']=$login;
         $_SESSION['username']=$user['username'];
         $_SESSION['level']=$user['level'];
         $_SESSION['zalogowano']=true;
         header('Location: index.php');
         return;
      }
   }
   $_SESSION['error']='Błędny login lub hasło!';
   header('Location: index.php');
   return;
}

//Rejestracja
function register($login, $username, $pass1, $pass2){
   $users = json_decode(file_get_contents('users.json'),true);
   foreach($users as $user){
      if($user['login']==$login){
         $_SESSION['error_register']="Użytkownik o podanym loginie już istnieje! Użyj innego!";
         return header('Location: index.php');
      }
   }
   if($pass1!=$pass2){
      $_SESSION['error_register']="Podane hasłą różnią się od siebie!";
      return header('Location: index.php');
   }else{
      $new_user = array('login' => $login, 'username'=> $username, 'password'=> md5($pass1), 'level'=> 'user');
      $users = json_decode(file_get_contents('users.json'),true);
      $users[]=$new_user;
      file_put_contents('users.json', json_encode($users));
      $_SESSION['login']=$login;
      $_SESSION['username']=$username;
      $_SESSION['level']='user';
      $_SESSION['zalogowano']=true;
      return header('Location: index.php');
   }
}

//wylogowanie
function logout(){
   $_SESSION = array();
  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
         $params["path"], $params["domain"],
         $params["secure"], $params["httponly"]
     );
  }
  session_destroy();
  session_unset();
  return header("Location: index.php");
}

// ---------------------------------------------------------------------------
// Topics - funkcje zarzadzania tematami
//------------------------------------------------------------------------------
// funkcja zapisu do pliku
function put_topic($topic, $topic_body, $username, 
                   $datafile="tematy.txt", $separator=":-:" )
{
   // ostatni wiersz zawiera najmłodszy wpis
   if( is_file($datafile) ){
      // odczyt pliku
      $data=file( $datafile );
      // pobranie danych z ostatniego elementu tablicy $data
      $record = explode( $separator, trim(array_pop($data))); 
      $id = (count($record)>1)?($record[0] + 1):1;
   }else{
      $id = 1;    
   }
   // utworzenie nowego wiersz danych
   // zakodowanie przez bin2hex() danych przesłanych przez użtykownika
   $data = implode( $separator, 
                     array( $id, 
                            bin2hex($topic),
                            bin2hex($topic_body), 
                            bin2hex($_SESSION['login']), 
                            date("Y-m-d H:i:s") 
                  ));
   // zapis danych na końcu pliku
   if( $fh = fopen( $datafile, "a+" )){
      fwrite($fh, $data."\n");
      fclose($fh);
      return $postid;
   }else{
      return FALSE;
   };                               
}

//------------------------------------------------------------------------------
// funkcja odczytu z pliku wszystkich tematów
function get_topics( $datafile="tematy.txt", $separator=":-:" )
{
   // wczytanie pliku do tablicy stringów
   if( $data=file( $datafile ) ){
      // utworzenie pustej tablicy wynikowej
      $topics=array();
      // dla każdego elementu tablicy $data
      //    $k - klucz ementu,  $v - wartość elementu
      foreach($data as $k=>$v){
          // umieszcza kolejne elementy wiersza rozdzielone separatoerm 
          // w kolejnych elementach zwracanej tablicy
          $record = explode( $separator, trim($v));
          // jesli pasuje identyfikator tematu
          // przepakowanie do $posts[] i dekodowanie danych użytkownika
          $topics[$record[0]]=array( 
             "topicid"    => $record[0],
             "topic"      => hex2bin($record[1]),
             "topic_body" => hex2bin($record[2]),
             "login"   => hex2bin($record[3]),
             "date"       => $record[4]
          );
      }
      // zwraca tablice z wynikami
      return $topics;   
   }else{
      // zwraca kod błędu
      return FALSE;
   }
}

//------------------------------------------------------------------------------
// funkcja wyznacza id poprzedniego tematu
function get_previous_topic_id( $topicid, 
                                $datafile="tematy.txt", $separator=":-:")
{
    $data=file( $datafile );
    $pre=0;
    if( count($data) ){
       foreach($data as $k=>$v ){
          $r = explode( $separator, trim($v));
          if( $r[0]<$topicid) $pre=$r[0];
          if( $r[0]==$topicid ) break;  
       }
    }
    return $pre;
}

//------------------------------------------------------------------------------
// funkcja wyznacza id następnego tematu
function get_next_topic_id( $topicid, 
                            $datafile="tematy.txt", $separator=":-:")
{
    $data=file( $datafile );
    $next=0;
    if( count($data) ){
       foreach($data as $k=>$v ){
          $r = explode( $separator, trim($v));
          if( $r[0]<$topicid ) continue;
          if( $r[0]>$topicid) {
             $next=$r[0];
             break;
          }     
       }
    }
    return $next;
}

//kasowanie tematu 
function delete_topic($topic_id_or_login, $topic_file='tematy.txt', $posts_file='wypowiedzi.txt', $separator=','){
   if($data=file($topic_file)){
      foreach($data as $k=>$v){
         $r = explode($separator, trim($v));
            if($r[0]==$topic_id_or_login){
               unset($data[$k]);
               $post_data=file($posts_file);
               if($post_data=file($posts_file)){
                  foreach($post_data as $k=>$v){
                     $r = explode($separator, trim($v));
                  if($r[1]==$id_topic){
                     $id_post = $r[0];
                     unset($post_data[$k]);  
                  }elseif(isset($id_post) and $r[0]>$id_post){
                     $post_data[$k] =  implode($separator, [intval($r[0])-1, $r[1], $r[2], $r[3],$r[4],"\n"])."\n";
                  }
                  if($r[1]>$id_topic){
                     $post_data[$k] =  implode($separator, [$r[0], intval($r[1])-1, $r[2], $r[3],$r[4]])."\n";
                  }
               }
               file_put_contents($posts_file,implode("", $post_data));
            }
         }
         else if($r[3]==$topic_id_or_login){
               $flag = true;
               $id_post = $r[0];
               if($post_data = file($posts_file)){
                  foreach($post_data as $k => $value){
                     $r = explode($separator, $v);
                     if($r[1]==$id_post and $r[3]!=$topic_id_or_login){
                        $flag=false;
                     }
                  }
                  if($flag==true){
                     unset($data[$k]);
                     $post_data=file($posts_file);
                     if($post_data=file($posts_file)){
                        foreach($post_data as $k=>$v){
                           $r = explode($separator, trim($v));
                           if($r[1]==$id_topic){
                              $id_post = $r[0];
                              unset($post_data[$k]);  
                     }elseif(isset($id_post) and $r[0]>$id_post){
                           $post_data[$k] =  implode($separator, [intval($r[0])-1, $r[1], $r[2], $r[3],$r[4]])."\n";
                     }
                        if($r[1]>$id_topic){
                           $post_data[$k] =  implode($separator, [$r[0], intval($r[1])-1, $r[2], $r[3],$r[4]])."\n";
                     }
                     }
                  file_put_contents($posts_file,implode("", $post_data));
                  }
               }
         }
      }
         else if(isset($id_topic) and $r[0]>$id_topic){
            $data[$k][0] = intval($data[$k][0]) - 1; 
         }
      }
      return file_put_contents($topic_file,implode("", $data));
   }  
   else{
      return false;
   }   
}
//Edytowanie tematu
function edit_topic($topic_id, $topic_file, $separator){
   if(isset($_POST['topic'])&&isset($_POST['topic_body'])){
   if($data=file($topic_file)){
      foreach($data as $k => $topic){
         $topic = explode($separator, trim($topic));
         if($topic[0]==$topic_id){
            $newTopic = array($topic_id, bin2hex($_POST['topic']),bin2hex($_POST['topic_body']),$topic[3],date('Y-m-d H:i:s'));
            $newTopic = implode(',', $newTopic)."\n";
            $newData[] = $newTopic;
         }else{
         $newData[] = implode(',', $topic)."\n";
         }
      }
   $data = implode("", $newData);
   file_put_contents($topic_file,$data);
   return header('Location: index.php');
   }
   return FALSE;
}
}


// ---------------------------------------------------------------------------
// Posts - funkcje zarzadzania wypowiedziami
//------------------------------------------------------------------------------
// funkcja wyszukująca wypowiedzi na określony temat
//   $topicid - identyfikator tematu
//   $datafile - ścieżka do pliku zawierającego dane
//   $separator - znaki tworzące separator pól rekordu
//
// format pliku danych:
// postid:-:topicid:-:post:-:username:-:date
// 
function get_posts( $topicid, 
                    $datafile="wypowiedzi.txt", $separator=":-:")
{
   // wczytanie pliku do tablicy stringów
   if( $data=file( $datafile ) ){
      // utworzenie pustej tablicy wynikowej
      $posts=array();
      // dla każdego elementu tablicy $data
      //    $k - klucz ementu,  $v - wartość elementu
      foreach($data as $k=>$v){
          // umieszcza kolejne elementy wiersza rozdzielone separatoerm 
          // w kolejnych elementach zwracanej tablicy
          $record = explode( $separator, trim($v));
          // jesli pasuje identyfikator tematu
          if( $record[1]==$topicid ){
              // przepakowanie do $posts[] i dekodowanie danych użytkownika
              $posts[]=array( 
                 "postid"  => $record[0],
                 "topicid" => $record[1],
                 "post"    => hex2bin($record[2]),
                 "username"=> hex2bin($record[3]),
                 "date"    => $record[4]
              );
          }
      }
      // zwraca tablice z wynikami
      return $posts;   
   }else{
      // zwraca kod błędu
      return FALSE;
   }
}

//------------------------------------------------------------------------------
// funkcja zapisu wypowiedzi do pliku
function put_post( $topicid, $post, $username, 
                   $datafile="wypowiedzi.txt", $separator=":-:")
{
   // ostatni wiersz zawiera najmłodszy wpis
   if( is_file($datafile) ){
      // odczyt pliku
      $data=file( $datafile );
      $postid = 1;
      // pobranie danych z ostatniego elementu tablicy $data
      if( $last = trim(array_pop($data)) ){
         $record = explode( $separator, $last); 
         $postid = $record[0]+1;
      }
   }      
   // utworzenie nowego wiersz danych
   // zakodowanie przez bin2hex() danych przesłanych przez użtykownika
   $data = implode( $separator, 
                     array( $postid, 
                            $topicid, 
                            bin2hex($post), 
                            bin2hex($username), 
                            date("Y-m-d H:i:s") 
                     )
                  );
   // zapis danych na końcu pliku
   if( $fh = fopen( $datafile, "a+" )){
      fwrite($fh, $data."\n");
      fclose($fh);
      return $postid;
   }else{
      return FALSE;
   };                               
}

//------------------------------------------------------------------------------
// funkcja pobiera z pliku wypowiedz o danym $id
function get_post( $id, 
                   $datafile="wypowiedzi.txt", $separator=":-:" )
{
    $data = file( $datafile );
    $post=FALSE;
    foreach($data as $v ){
       $r = explode( $separator, trim($v));
       if( $r[0]==$id ){
           $post = array( 
                 "postid"  => $r[0],
                 "topicid" => $r[1],
                 "post"    => hex2bin($r[2]),
                 "username"=> hex2bin($r[3]),
                 "date"    => $r[4]
              );
            break;  
       }
    }
    return $post; 
}

//------------------------------------------------------------------------------
// funkcja aktualizuje w pliku dane dla wypowiedzi o danym $postid
function update_post( $postid, $post, $username, 
                      $datafile="wypowiedzi.txt", $separator=":-:")
{
    $data=file( $datafile ); 
    $new_post=FALSE;
    foreach($data as $k=>$v ){
       $r = explode( $separator, trim($v));
       if( $r[0]==$postid ){
           $new_post = array( 
                 "postid"  => $r[0],
                 "topicid" => $r[1],
                 "post"    => bin2hex($post),
                 "username"=> bin2hex($username),
                 "date"    => date("Y-m-d H:i:s")
              );
              $data[$k] = implode($separator,$new_post)."\n";
              file_put_contents($datafile, implode("", $data));  
            break;  
       }
    }
    return $new_post; 
}

//------------------------------------------------------------------------------
// funkcja usuwa z pliku dane dla wypowiedzi o danym $id albo loginie
function delete_post( $id_or_login, $datafile="wypowiedzi.txt", $separator=",")
{
   if($data=file( $datafile )){
      foreach($data as $k=>$v){
         $r = explode( $separator, trim($v));
         if(($r[0]==$id_or_login) or ($r[3]==$id_or_login)){
            $id_post = $r[0];
            unset($data[$k]);  
         }else if((isset($id_post) and $r[0]>$id_post)){
            $data[$k][0] = intval($data[$k][0])-1;
         }   
      }
      return file_put_contents($datafile,implode("", $data)); 
   }else{
      return FALSE;
   }   
}

//------------------------------------------------------------------------------
// funkcja zlicza wypowiedzi na każdy z tematów
function get_posts_count( $datafile="wypowiedzi.txt", $separator=":-:" )
{
   if( !is_file($datafile) ) 
      return FALSE;
   $post_count = array();   
   if( $data=file( $datafile ) ){
      foreach( $data as $v ){
         if( strlen(trim($v))>0 ){
           $p = explode( $separator, trim($v));
           if( isset($post_count[$p[1]]) )
             $post_count[$p[1]] = $post_count[$p[1]] + 1;
           else
             $post_count[$p[1]] = 1;
         }
      }
      return $post_count; 
   }else{
      return FALSE;
   }
}

//------------------------------------------------------------------------------
// funkcja pobiera date ostatniej wypowiedzi
function get_last_post_date($datafile="wypowiedzi.txt", $separator=":-:")
{
    if( $data=file( $datafile ) ){
        $record = explode( $separator, trim(array_pop($data)));
        return $record[4];
    }else{
        return '- brak postów -';
    } 
}

//usunięcie użytkownika
function delete_user($login){
   $users = json_decode(file_get_contents('users.json'),true);
   foreach($users as $k => $user){
   if($user['login']==$login){
      $user_id = $k;
      delete_post(bin2hex($login));
      delete_topic(bin2hex($login));
      unset($users[$k]);
      break;   
   }else if(isset($user_id) and $k>$user_id){
      
   }
   }
   file_put_contents('users.json', json_encode($users));
   if($_SESSION['login']==$login){
      return logout();
   }
   if($_GET['topic']==''){
      return header('Location: index.php');
   }else{
      return header('Location: index.php?topic='.$_GET['topic']);
   }
}

//Zmiana poziomu użytkownika
function change_level($login){
   $users = json_decode(file_get_contents('users.json'),true);
   foreach($users as $k => &$user){
   if($user['login']==$login){
      if($user['level']=='user') $user['level']='admin';
      else if($user['level']=='admin') $user['level']='user';
      if($_SESSION['login']==$login && $_SESSION['level']=='user') $_SESSION['level']='admin';
      if($_SESSION['login']==$login && $_SESSION['level']=='admin'){
         $_SESSION['level']='user'; 
         if(isset($_SESSION['list'])){
            $_SESSION['list']='hidden';
         }
      }
      break;   
   }
   }
   file_put_contents('users.json', json_encode($users));
   if($_GET['topic']==''){
      return header('Location: index.php');
   }else{
      return header('Location: index.php?topic='.$_GET['topic']);
   }
}
//Wyświetelenie lub schowanie listy
function toggle_users_list(){
   if (isset($_POST['toggle_lista'])) {
      if(!isset($_SESSION['list']) or $_SESSION['list'] == 'hidden'){
         $_SESSION['list'] = 'visible';
      }else if($_SESSION['list']=='visible'){
         $_SESSION['list'] = 'hidden';
      }
   }
   if (isset($_SESSION['list']) and $_SESSION['list']=='visible') {
      $users = json_decode(file_get_contents('users.json'),true);
      
      echo('<table><tr><th>Imię</th> <th>Nazwa</th> <th>Poziom</th></tr>');
      
      if(isset($_GET['topic'])){
         $topic_link = '&topic='.$_GET['topic'];
      }else{
         $topic_link = '';
      }

      foreach($users as $user){
      if($user['login']!='admin'){
         $btns='<td><a href="index.php?login='.$user['login'].'&cmd=changelevel'.$topic_link.'">Zmień</a><a href="index.php?login='.$user['login'].'&cmd=deleteuser'.$topic_link.'">Kasuj</a></td>';
      }else{
         $btns='';
      }
      echo('<tr><td>'.$user['username'].'</td> <td>'.$user['login'].'</td> <td>'.$user['level'].'</td> '.$btns.'</tr>');
   }
      echo('</table>');
   }
   return;
}
  
?>