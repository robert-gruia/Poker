<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=§, initial-scale=1.0">
    <title>Poker Game</title>
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #35654d;
        align-items: center;
        justify-content: center;
    }

    .banco {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 20px;
    }

    .mazzo {
        margin-right: 20px;
    }

    .carta img {
        width: 160px;
        height: 240px;
        margin-right: 5px;
    }

    .mazzo img {
        padding-right: 20px;
        width: 160px;
        height: 240px;
    }

    .giocatori {
        display: flex;
        justify-content: space-around;
        margin-top: 20px;
    }

    .giocatore {
        text-align: center;
    }

    .giocatore h2 {
        margin-bottom: 10px;
    }

    .carte {
        display: flex;
        justify-content: center;
    }

    .carta img {
        width: 160px;
        height: 240px;
        margin-right: 5px;
    }

    html,
    body,
    .banco,
    .giocatori {
        height: 100%;
    }
</style>

<body>
    <?php
    require_once("card.php");
    require_once("handEvaluator.php");

    //deck creation
    $deck = array();
    for ($i = 0; $i < 4; $i++) {
        for ($j = 2; $j <= 14; $j++) {
            $suit = "";
            if ($i == 0)
                $suit = "c";
            else if ($i == 1)
                $suit = "q";
            else if ($i == 2)
                $suit = "f";
            else if ($i == 3)
                $suit = "p";
            array_push($deck, new Card($j, $suit, "carte/back_r.svg"));
            array_push($deck, new Card($j, $suit, "carte/back_b.svg"));
        }
    }
    shuffle($deck);
    //card distribution to players
    $bancoCards = array();
    for($i = 0; $i < 5; $i++){
        array_push($bancoCards, array_pop($deck));
    }
    $gCards = array();
    for($i = 0; $i < 4; $i++){
        $gCards[$i] = array(array_pop($deck));
    }
    for($i = 0; $i < 4; $i++){
        array_push($gCards[$i], array_pop($deck));
    }
    
    
    ?>
    <div class="game">

        <div class="banco">
            <div class="mazzo">
                <img src=<?php echo end($deck)->cardBack_Path?>>
            </div>
            <?php
            for ($i = 0; $i < 5; $i++) {
                $card = $bancoCards[$i];
                ?>
                <div class="carta">
                    <img src=<?= $card->img_Path ?> alt="">
                </div>
                <?php
            }
            ?>
        </div>
        <div class="giocatori">
            <?php
            for ($i = 0; $i < 4; $i++) {
                ?>
                <div class="giocatore">
                    <h2>Giocatore
                        <?= $i + 1 ?>
                    </h2>
                    <div class="carte">
                        <?php
                        for ($j = 0; $j < 2; $j++) {
                            $card = $gCards[$i][$j];
                            ?>
                            <div class="carta">
                                <img src=<?= $card->img_Path ?> alt="">
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <h3>
                        <?= HandEvaluator::evaluate($gCards[$i], $bancoCards) ?>
                    </h3>
                    
                </div>
                <?php
            }
            ?>

        </div>
    </div>

</body>

</html>