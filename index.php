<?php
namespace classes;
include "classes/singleton.php";

if (isset($_POST['searchico']))
    Handler::getInstance()->getCompany($_POST['searchico']); //45626499 06503047 27082440
?>

<!doctype html>
<html lang="sk">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MFCR Ares</title>

        <link href="https://getbootstrap.com/docs/5.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    </head>
    <body class="bg-light">
        <main class="container">
            <div class="d-flex align-items-center p-3 my-3 text-white bg-purple rounded shadow-sm">
                <form class="w-100" name="searchform" method="POST">
                    <div class="input-group">
                        <input type="number" name="searchico" class="form-control" placeholder="Zadajte IČO" aria-label="Zadajte IČO" minlength="8" <?php echo (isset($_POST['searchico']) ? " value='".$_POST['searchico']."'" : ""); ?> required>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit" role="button" name="searchsubmit">Vyhľadať</button>
                        </div>
                    </div>
                </form>
            </div>

            <?php
            $Displayer = new Displayer();
            $Displayer->Display();
            ?>
        </main>
    </body>
</html>