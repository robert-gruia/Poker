<?php
require_once "card.php";
class Deck{
    private $cards;

    function __construct($cards){
        $this->cards = $cards;
    }

    public static function createSingleDeck(){
        $cards = array();
        for ($i = 0; $i < 4; $i++) {
            for ($j = 2; $j <= 14; $j++) {
                $suit = "";
                if ($i == 0)
                    $suit = "Hearts";
                else if ($i == 1)
                    $suit = "Diamonds";
                else if ($i == 2)
                    $suit = "Clubs";
                else if ($i == 3)
                    $suit = "Spades";
                array_push($cards, new Card($j, $suit, "cards/back_r.svg"));
            }
        }
        return new Deck($cards);
    }

    public static function createDoubleDeck(){
        $cards = array();
        for ($i = 0; $i < 4; $i++) {
            for ($j = 2; $j <= 14; $j++) {
                $suit = "";
                if ($i == 0)
                    $suit = "Hearts";
                else if ($i == 1)
                    $suit = "Diamonds";
                else if ($i == 2)
                    $suit = "Clubs";
                else if ($i == 3)
                    $suit = "Spades";
                array_push($cards, new Card($j, $suit, "cards/back_r.svg"));
                array_push($cards, new Card($j, $suit, "cards/back_b.svg"));
            }
        }
        return new Deck($cards);
    }

    public function shuffle(){
        shuffle($this->cards);
    }

    public function cardCount(){
        count($this->cards);
    }

    public function takeCard(){
        return array_pop($this->cards);
    }

    public function cardBack(){
        return end($this->cards)->cardBack_Path;
    }

    public function getCards(){
        return $this->cards;
    }
}
?>