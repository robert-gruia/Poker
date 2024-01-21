<?php namespace Gruia\Poker; ?>
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
    require_once("deck.php");
    require_once("hand.php");
    require_once("handEvaluator.php");
    //deck creation
    $deck = Deck::createDoubleDeck();
    $deck->shuffle();
    //card distribution to players
    $tableCards = array();
    for ($i = 0; $i < 5; $i++) {
        array_push($tableCards, $deck->takeCard());
    }
    $players = array(new Player("Player 1"), new Player("Player 2"), new Player("Player 3"), new Player("Player 4"));
    for ($i = 0; $i < 2; $i++) {
        for ($j = 0; $j < count($players); $j++) {
            $players[$j]->getHand()->addCard($deck->takeCard());
        }
    }
    for ($i = 0; $i < count($players); $i++) {
        $players[$i]->getHand()->checkHand($tableCards);
    }
    $winners = HandEvaluator::winnerHands($players);
    $winners = implode(",", $winners);
    $winners = explode(",", $winners); 
    for ($i = 0; $i < count($winners); $i++) {
        $ind = $winners[$i];
        $players[$ind]->getHand()->setWinner(true);
    }

    ?>
    <div class="game">

        <div class="table">
            <div class="deck">
                <img src=<?php echo $deck->cardBack(); ?>>
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
                    <h2>
                        <?= $players[$i]->name ?>
                    </h2>
                    <div class="cards">
                        <?php
                        for ($j = 0; $j < 2; $j++) {
                            $card = $players[$i]->getHand()->getCards()[$j];
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
                           
                            $playerHandVals = $players[$i]->getHand()->getHandVals();
                            echo "Hand Type: ". Hand::getHandType($playerHandVals['strength']);
                        ?>
                    </h3>
                    <h3>
                        <?php
                        echo "Card Value: ". $playerHandVals['cardValue'];
                        ?>
                    </h3>
                    <h3>
                        <?php
                        echo "Kicker: ". $playerHandVals['kickerValue'];
                        ?>
                    </h3>
                    <h3>
                        <?php
                        echo ($players[$i]->getHand()->getIfWinner()) ? "Winner" : "";
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