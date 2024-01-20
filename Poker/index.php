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

    .table {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 20px;
    }

    .deck {
        margin-right: 20px;
    }

    .card img {
        width: 160px;
        height: 240px;
        margin-right: 5px;
    }

    .deck img {
        padding-right: 20px;
        width: 160px;
        height: 240px;
    }

    .players {
        display: flex;
        justify-content: space-around;
        margin-top: 20px;
    }

    .player {
        text-align: center;
    }

    .player h2 {
        margin-bottom: 10px;
    }

    .cards {
        display: flex;
        justify-content: center;
    }

    .card img {
        width: 160px;
        height: 240px;
        margin-right: 5px;
    }

    html,
    body,
    .table,
    .players {
        height: 100%;
    }
</style>

<body>
    <?php
    require_once("card.php");
    require_once("player.php");
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
    $tableCards = array();
    for ($i = 0; $i < 5; $i++) {
        array_push($tableCards, array_pop($deck));
    }
    $gCards = array();
    for ($i = 0; $i < 4; $i++) {
        $gCards[$i] = new Player('Player ' . $i);
        array_push($gCards[$i]->hand, array_pop($deck));
    }
    for ($i = 0; $i < 4; $i++) {
        array_push($gCards[$i]->hand, array_pop($deck));
        $gCards[$i]->finalValues = HandEvaluator::evaluate($gCards[$i]->hand, $tableCards);
    }


    ?>
    <div class="game">

        <div class="table">
            <div class="deck">
                <img src=<?php echo end($deck)->cardBack_Path; ?>>
            </div>
            <?php
            for ($i = 0; $i < 5; $i++) {
                $card = $tableCards[$i];
                ?>
                <div class="card">
                    <img src=<?= $card->img_Path ?> alt="">
                </div>
                <?php
            }
            ?>
        </div>
        <div class="players">
            <?php
            for ($i = 0; $i < 4; $i++) {
                ?>
                <div class="player">
                    <h2>Player
                        <?= $i + 1 ?>
                    </h2>
                    <div class="cards">
                        <?php
                        for ($j = 0; $j < 2; $j++) {
                            $card = $gCards[$i]->hand[$j];
                            ?>
                            <div class="card">
                                <img src=<?= $card->img_Path ?> alt="">
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <h3>
                        <?php
                        echo $gCards[$i]->finalValues[0];
                        ?>
                    </h3>
                    <h3>
                        <?php
                        if ($gCards[$i]->finalValues[1] == max(array_column(array_column($gCards, 'finalValues'), '1'))) {
                            echo "\n\rWinner";
                        }
                        ?>
                    </h3>

                </div>
                <?php
            }
            ?>

        </div>
    </div>

</body>

</html>