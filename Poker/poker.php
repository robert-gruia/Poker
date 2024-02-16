<?php
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=§, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/pokerStyle.css">
    <script src="Scripts/flipCards.js" defer></script>
    <title>Poker Game</title>
</head>

<body>
    <?php
    session_start();
    require_once "card.php";
    require_once "player.php";
    require_once "deck.php";
    require_once "hand.php";
    require_once "handEvaluator.php";

    //player data
    $host = 'localhost';
    $db = 'poker';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $opt);

    $user = $_SESSION["username"];
    $sql = "SELECT * FROM utenti WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user]);
    $row = $stmt->fetch();

    print_r($row);
    if ($row !== null /*&& $row->num_rows > 0*/) {
        $player = new Player($row["username"], $row["balance"]);
        //deck creation
        $deck = Deck::createDoubleDeck();
        $deck->shuffle();
        //card distribution to players
        $tableCards = array();
        $players = array($player, new Player("Bot 1"), new Player("Bot 2"));
        for ($i = 0; $i < 5; $i++) {
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
    ?>
    <div class="game">
        <div class="table">
            <div class="deck">
                <img src="<?= $deck->cardBack(); ?>">
            </div>
            <?php
            for ($i = 0; $i < count($tableCards); $i++) {
                if ($i < count($tableCards) - 2) { ?>
                    <div class="card">
                        <img src="<?= $tableCards[$i]->img_Path ?>" alt="">
                    </div>
                <?php } 
                else { ?>
                    <div class="card flippable-table">
                        <img src="<?= $tableCards[$i]->cardBack_Path ?>" alt="" data-img="'<?= $tableCards[$i]->img_Path ?>'">
                    </div>
                    <?php } ?>
            <?php } ?>
        </div>
        <div class="players">
            <?php foreach ($players as $player) { ?>
                <div class="player">
                    <h2>
                        <?= $player->name ?>
                    </h2>
                    <div class="cards">
                        <?php foreach ($player->getHand()->getCards() as $card) { ?>
                            <?php
                                if ($player->name == $_SESSION["username"]) {
                                    ?>
                                    <div class="card">
                                    <div class="card-face front">
                                        <img src=<?= $card->img_Path ?> alt="">
                                    </div>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="card flippable" data-img="<?= $card->img_Path ?>">
                                    <div class="card-face back">
                                        <img src=<?= $card->cardBack_Path ?> alt="" id="back">
                                    </div>
                                </div>
                                <?php } ?>
                        <?php } ?>
                    </div>
                    <h3>Balance:
                        <?= $player->money ?>
                    </h3>
                </div>
            <?php } ?>
        </div>
        <div class="buttons">
            <button onclick="flipPlayers()">Flip</button>
        </div>
    </div>

</body>

</html>