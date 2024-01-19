<?php
class Card
{
    public $rank;
    public $suit;
    public $img_Path;
    public $cardBack_Path;

    function __construct($rank, $suit, $cardBack_Path)
    {
        $this->rank = $rank;
        $this->suit = $suit;
        $this->backColor = $cardBack_Path;
        $this->img_Path = "carte/" . $rank . $suit . ".svg";
    }
}
?>