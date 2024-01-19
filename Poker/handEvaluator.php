<?php
class HandEvaluator
{
    public static function evaluate($playerCards, $tableCards) {

        $hand = array_merge($playerCards, $tableCards);
    
        // Royal Flush
        if (self::isRoyalFlush($hand)) {
            return 'Royal Flush';
        }
    
        //Straight Flush
        if (self::isStraightFlush($hand)) {
            return 'Straight Flush';
        }
    
        //Four of a Kind
        if (self::isFourOfAKind($hand)) {
            return 'Four of a Kind';
        }
    
        //Full House
        if (self::isFullHouse($hand)) {
            return 'Full House';
        }
    
        //Flush
        if (self::isFlush($hand)) {
            return 'Flush';
        }
    
        //Straight
        if (self::isStraight($hand)) {
            return 'Straight';
        }
    
        //Three of a Kind
        if (self::isThreeOfAKind($hand)) {
            return 'Three of a Kind';
        }
    
        //Two Pair
        if (self::isTwoPair($hand)) {
            return 'Two Pair';
        }
    
        //One Pair
        if (self::isOnePair($hand)) {
            return 'One Pair';
        }
    
        //High Card
        return 'High Card';
    }
    
    private static function isRoyalFlush($hand) {
        return self::isStraightFlush($hand) && $hand[0]['rank'] == '10';
    }
    
    private static function isStraightFlush($hand) {
        return self::isFlush($hand) && self::isStraight($hand);
    }
    
    private static function isFourOfAKind($hand) {
        $counts = array_count_values(array_column($hand, 'rank'));
        return in_array(4, $counts);
    }
    
    private static function isFullHouse($hand) {
        $counts = array_count_values(array_column($hand, 'rank'));
        return in_array(3, $counts) && in_array(2, $counts);
    }
    
    private static function isFlush($hand) {
        $suits = array_unique(array_column($hand, 'suit'));
        return count($suits) == 1;
    }
    
    private static function isStraight($hand) {
        $ranks = array_unique(array_column($hand, 'rank'));
        $min = min($ranks);
        $max = max($ranks);
        return ($max - $min) == 4 && count($ranks) == 5;
    }
    
    private static function isThreeOfAKind($hand) {
        $counts = array_count_values(array_column($hand, 'rank'));
        return in_array(3, $counts);
    }
    
    private static function isTwoPair($hand) {
        $counts = array_count_values(array_column($hand, 'rank'));
        $pairs = array_filter($counts, function ($count) {
            return $count == 2;
        });
        return count($pairs) == 2;
    }
    
    private static function isOnePair($hand) {
        $counts = array_count_values(array_column($hand, 'rank'));
        return in_array(2, $counts);
    }
} 
?>