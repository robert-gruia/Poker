<?php
 require_once "card.php";
 require_once "handEvaluator.php";
class Hand{
    private $cards = array();
    private $cardLimit;
    private $handVals;
    private $winner = false;
    public static $handTypes = [
        'High Card',
        'Pair',
        'Two Pair',
        'Three of a Kind',
        'Straight',
        'Flush',
        'Full House',
        'Four of a Kind',
        'Straight Flush',
        'Royal Flush'
    ];

    public function cardCount(){
        return count($this->cards);
    }
    public function __construct($cardLimit = 2){
        $this->cardLimit = $cardLimit;
    }
    public function getCards(){
        return $this->cards;
    }
    
    public function addCard($card){
        array_push($this->cards, $card);
    }

    public static function getHandType($handStrangth){
        return self::$handTypes[$handStrangth];
    }

    public function getHandVals(){
        return $this->handVals;
    }

    public function checkHand($tableCards){
        $this->handVals = HandEvaluator::evaluate($this->cards, $tableCards);
    }

    public function getIfWinner(){
        return $this->winner;
    }

    public function setWinner($winner){
        $this->winner = $winner;
    }
} 
?>