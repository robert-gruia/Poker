<?php
class Player
{

    public $name;
    public $hand = array();
    public $finalValues;

    public $money;
    function __construct($name, $money = 1000)
    {
        $this->name = $name;
        $this->money = $money;
    }

}

?>