<?php
namespace Gruia\Poker;

class Hand{
    private $cards = array();
    private $cardLimit;
    private $handVals;
    public static $handTypes = [
        'High Card',
        'One Pair',
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
} 
?>