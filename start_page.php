<!DOCTYPE html>
<html>
<head>
    <title>Zadanie 1 - WWW i języki skryptowe</title>
    <meta charset="utf-8">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
      <h1>
          Zadanie 2
      </h1>
      <h2>
          Rejestracja użytkowników
      </h2>
    </header>
    <nav>
            <a href="index.php">Home</a>
            <?php for($n=1;$n<=10;$n++) { if( is_dir("../zadanie".$n) ) { ?>
            <a href="../zadanie<?=$n?>">Zadanie <?=$n?></a>
            <?php } } ?>
    </nav>

    <section id="login">
        <form action="index.php" method="post">
            <header><h2>Zaloguj się do forum</h2></header>  
            <input type="text" name="login" placeholder="Nazwa logowania" pattern="[A-Za-z0-9\-]*" required><br>
            <input type="password" name="password" placeholder="Hasło" required><br>
            <div class="error">
                <?php 
                if (!empty($_SESSION['error'])) {
                    echo($_SESSION['error']);
                    unset($_SESSION['error']);
                }
                ?>
            </div>
            <button type="submit" name="login_submit">Zaloguj się</button>
        </form>
  
        <form action="index.php" method="post">
            <header><h2>Jeśli nie jesteś zarejestrowany, to możesz zapisać się do forum.</h2></header>  
            <input type="text" name="login_register" placeholder="Nazwa logowania (dozwolone są tylko: litery, cyfry i znak '-')" pattern="[A-Za-z0-9\-]*" autofocus required><br>
            <input type="text" name="username" placeholder="Imię autora" required><br>
            <input type="password" name="pass1" placeholder="Hasło" required><br>
            <input type="password" name="pass2" placeholder="Powtórz hasło" required><br>
            <div class="error">
                <?php 
                if (!empty($_SESSION['error_register'])) {
                    echo($_SESSION['error_register']);
                    unset($_SESSION['error_register']);
                }
                ?>
            </div>
            <button type="submit" name="register_submit">Zapisz się do forum</button>
        </form>
    </section>    

    <footer>
        Ostatni wpis na forum powstał dnia:<?=get_last_post_date($posts_file, $separator);?>
    </footer>
</body>
</html>