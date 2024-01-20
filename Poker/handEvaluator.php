<?php

class HandEvaluator
{
    public static function evaluate($playerCards, $tableCards)
    {

        $hand = array_merge($playerCards, $tableCards);

        // Royal Flush
        if (self::isRoyalFlush($hand)) {
            return array('Royal Flush', 9);
        }

        //Straight Flush
        if (self::isStraightFlush($hand)) {
            return array('Straight Flush', 8);
        }

        //Four of a Kind
        if (self::isFourOfAKind($hand)) {
            return array('Four of a Kind', 7);
        }

        //Full House
        if (self::isFullHouse($hand)) {
            return array('Full House', 6);
        }

        //Flush
        if (self::isFlush($hand)) {
            return array('Flush', 5);
        }

        //Straight
        if (self::isStraight($hand)) {
            return array('Straight', 4);
        }

        //Three of a Kind
        if (self::isThreeOfAKind($hand)) {
            return array('Three of a Kind', 3);
        }

        //Two Pair
        if (self::isTwoPair($hand)) {
            return array('Two Pair', 2);
        }

        //One Pair
        if (self::isOnePair($hand)) {
            return array('One Pair', 1);
        }

        //High Card
        return array('High Card', 0);
    }

    private static function isRoyalFlush($hand)
    {
        return self::isStraightFlush($hand) && $hand[0]['rank'] == '10';
    }

    private static function isStraightFlush($hand)
    {
        return self::isFlush($hand) && self::isStraight($hand);
    }

    private static function isFourOfAKind($hand)
    {
        $counts = array_count_values(array_column($hand, 'rank'));
        return in_array(4, $counts);
    }

    private static function isFullHouse($hand)
    {
        $counts = array_count_values(array_column($hand, 'rank'));
        return in_array(3, $counts) && in_array(2, $counts);
    }

    private static function isFlush($hand)
    {
        $suits = array_unique(array_column($hand, 'suit'));
        return count($suits) == 1;
    }

    private static function isStraight($hand)
    {
        $ranks = array_unique(array_column($hand, 'rank'));
        $min = min($ranks);
        $max = max($ranks);
        return ($max - $min) == 4 && count($ranks) == 5;
    }

    private static function isThreeOfAKind($hand)
    {
        $counts = array_count_values(array_column($hand, 'rank'));
        return in_array(3, $counts);
    }

    private static function isTwoPair($hand)
    {
        $counts = array_count_values(array_column($hand, 'rank'));
        $pairs = array_filter($counts, function ($count) {
            return $count >= 2;
        });
        return count($pairs) >= 2;
    }

    private static function isOnePair($hand)
    {
        $counts = array_count_values(array_column($hand, 'rank'));
        return in_array(2, $counts);
    }
}
?>