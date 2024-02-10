<?php
namespace Gruia\Poker;

class Player
{

    public $name;
    private $hand;
    public $money;
    function __construct($name, $money = 1000)
    {
        $this->name = $name;
        $this->money = $money;
        $this->hand = new Hand();
    }

    public function getHand()
    {
        return $this->hand;
    }

}

?>