<?php
class Card
{
    public $rank;
    public $suit;
    public $img_Path;

    function __construct($rank, $suit)
    {
        $this->rank = $rank;
        $this->suit = $suit;
        $this->img_Path = "carte/" . $rank . $suit . ".svg";
    }
}
?>