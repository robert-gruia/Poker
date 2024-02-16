<?php

class Card
{
    public $rank;
    public $suit;
    public $img_Path;
    public $cardBack_Path;

    function __construct($rank = null, $suit = null, $cardBack_Path)
    {
        $this->rank = $rank;
        $this->suit = $suit;
        $this->cardBack_Path = $cardBack_Path;
        $this->img_Path = "cards/" . $rank . $suit . ".svg";
    }

}
?>