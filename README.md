## MFČR Ares
Knižnica na načítanie údajov firiem z českého registra spoločností systému ARES.

## Požiadavky
- PHP >= 5.4
- [Bootstrap (odporúčaný 5.1)](https://getbootstrap.com/)
- [SimpleXML](https://www.php.net/manual/en/simplexml.setup.php)

## Použitie
Následujúci kód predstavuje demonštráciu použitia knižnice v praxi za pomoci Bootstrap-u.
```php
<?php
namespace classes;
include "classes/singleton.php";

// Po POST requeste ziskat data spolocnosti
if (isset($_POST['searchico']))
    Handler::getInstance()->getCompany($_POST['searchico']);
?>
<!doctype html>
<html lang="en">
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
            // Init
            $Displayer = new Displayer();
            // Vyvolanie zakladneho zobrazenia vsetkych udajov
            $Displayer->Display();
            
            /** Do funkcie Display mozeme doplnit array udajov, ktore chceme zobrazit.
             *
             * Dostupne moznosti:
             * --UDAJ------POPIS--
             *  'ZAU' = Základní údaje
             *  'PSU' = Stav v registrech
             *  'ROR' = Registrace v obchodním rejstříku
             *  'RRZ' = Registrace v registry živnostenského podnikání
             *  'NCE' = Klasifikace ekonomických činností
             *  'PPI' = Předměty podnikání
             *  'OBC' = Obory činnosti
            **/
            $Displayer->Display(['ZAU', 'PSU']);
            ?>
        </main>
    </body>
</html>
```
