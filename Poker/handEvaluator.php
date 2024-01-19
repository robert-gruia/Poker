<?php
class HandEvaluator
{
    public static function evaluate($holeCards, $communityCards)
    {
        $allCards = array_merge($holeCards, $communityCards);

        usort($allCards, function ($a, $b) {
            return $b->rank - $a->rank;
        });

        //straight flush & royal flush
        $straight = self::checkForStraight($allCards);
        $flush = self::checkForFlush($allCards);

        if ($straight && $flush) {
            if(self::checkForRoyalFlush($allCards)) return "Royal Flush";
            else return "Straight Flush";
        }

        //four of a kind
        if (self::checkForNofAKind($allCards, 4)) {
            return "Four of a Kind";
        }

        //full house
        if (self::checkForFullHouse($allCards)) {
            return "Full House";
        }

        //flush
        if ($flush) {
            return "Flush";
        }

        //straight
        if ($straight) {
            return "Straight";
        }

        //three of a kind
        if (self::checkForNofAKind($allCards, 3)) {
            return "Three of a Kind";
        }

        //two pair
        if (self::checkForTwoPair($allCards)) {
            return "Two Pair";
        }

        //pair
        if (self::checkForNofAKind($allCards, 2)) {
            return "One Pair";
        }

        // High Card
        return "High Card";
    }

    private static function checkForRoyalFlush($cards){
        return min(array_unique(array_column($cards, 'rank'))) == 10;

    }

    private static function checkForStraight($cards)
    {
        $uniqueRanks = array_unique(array_column($cards, 'rank'));
        sort($uniqueRanks);

        $straight = false;
        $consecutive = 0;

        foreach ($uniqueRanks as $index => $rank) {
            if (isset($uniqueRanks[$index + 1]) && $uniqueRanks[$index + 1] == $rank - 1) {
                $consecutive++;
                if ($consecutive == 4) {
                    $straight = true;
                    break;
                }
            } else {
                $consecutive = 0;
            }
        }

        return $straight;
    }

    private static function checkForFlush($cards)
    {
        return in_array(5, array_count_values(array_column($cards, 'suit')));
    }

    private static function checkForNofAKind($cards, $n)
    {
        return in_array($n, array_count_values(array_column($cards, 'rank')));
    }

    private static function checkForFullHouse($cards)
    {
        $rankCounts = array_count_values(array_column($cards, 'rank'));
        return in_array(3, $rankCounts) && in_array(2, $rankCounts);
    }

    private static function checkForTwoPair($cards)
    {
        $pairs = array_filter(array_count_values(array_column($cards, 'rank')), function ($count) {
            return $count == 2;
        });

        return count($pairs) == 2;
    }
} 
?>