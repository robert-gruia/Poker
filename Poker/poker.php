<?php
namespace Gruia\Poker;

use mysqli; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=§, initial-scale=1.0">
    <script src="Scripts/cardsDistribution.js" defer></script>
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
    session_start();
    require_once("card.php");
    require_once("player.php");
    require_once("deck.php");
    require_once("hand.php");
    require_once("handEvaluator.php");

    //player data
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "poker";
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }

    $user = $_SESSION["username"];
    $sql = "SELECT * FROM utenti WHERE username = '$user'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    print_r($row);
    if ($result->num_rows > 0) {
        $player = new Player($row["username"], $row["balance"]);
        //deck creation
        $deck = Deck::createDoubleDeck();
        $deck->shuffle();
        //card distribution to players
        $tableCards = array();
        $players = array($player, new Player("Bot 1"), new Player("Bot 2"));
        for ($i = 0; $i < 3; $i++) {
            array_push($tableCards, $deck->takeCard());
        }
        for ($i = 0; $i < 2; $i++) {
            for ($j = 0; $j < count($players); $j++) {
                $players[$j]->getHand()->addCard($deck->takeCard());
            }
        }

    } else {
        header("Location: index.php");
    }
    $conn->close();


    ?>
    <div class="game">

        <div class="table">
            <div class="deck">
                <img src=<?php echo $deck->cardBack(); ?>>
            </div>
            <?php
            for ($i = 0; $i < count($tableCards); $i++) {
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
            for ($i = 0; $i < count($players); $i++) {
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
                                <?php
                                if ($players[$i]->name == $_SESSION["username"]) {
                                    ?>
                                    <img src=<?= $card->img_Path ?> alt="">
                                    <?php
                                } else {
                                    ?>
                                    <img src=<?= $card->cardBack_Path ?> alt="">
                                <?php } ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <h3>
                        <?php
                        echo "Balance: " . $players[$i]->money;
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