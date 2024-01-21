<?php
namespace Gruia\Poker;

class HandEvaluator
{
    public static function evaluate($playerCards, $tableCards)
    {

        $cards = array_merge($playerCards, $tableCards);
        $strength = self::checkHand($cards);
        return $strength;
    }

    private static function checkHand($cards)
    {
        $arr_ranks = array_count_values(array_column($cards, 'rank'));
        $vals_ranks = array_values($arr_ranks);
        $ranks = array_keys($arr_ranks);
        rsort($vals_ranks);
        rsort($ranks);
        $arr_suits = array_count_values(array_column($cards, 'suit'));
        $vals_suits = array_values($arr_suits);
        $suits = array_keys($arr_suits);
        rsort($vals_suits);
        rsort($suits);

        //Based on suit
        $consecutive = self::isConsecutive($cards);
        if (is_array($consecutive)) {
            if (count(array_count_values(array_column($consecutive, 'suit'))) == 1) {
                if (min(array_column($consecutive, 'rank')) == 10) {
                    //Royal Flush
                    return array(
                        "strength" => 9,
                        "cardValue" => array_sum($consecutive),
                        "kickerValue" => 0
                    );
                } else {
                    //Straight Flush
                    return array(
                        "strength" => 8,
                        "cardValue" => array_sum($consecutive),
                        "kickerValue" => 0
                    );
                }

            }
            //Straight
            return array(
                "strength" => 4,
                "cardValue" => array_sum($consecutive),
                "kickerValue" => 0
            );
        } else if ($vals_suits[0] >= 5) {
            $suit = array_search($vals_suits[0], $arr_suits);
            $suitCards = array();
            for ($i = 0; $i < count($cards); $i++) {
                if ($cards[$i]->suit == $suit)
                    array_push($suitCards, $cards[$i]->rank);

            }
            //Flush
            return array(
                "strength" => 5,
                "cardValue" => array_sum($suitCards),
                "kickerValue" => 0
            );
        }
        //Based on rank
        //Four of a Kind
        else if ($vals_ranks[0] >= 4) {
            $usedRanks = array_search($vals_ranks[0], $arr_ranks);
            return array(
                "strength" => 7,
                "cardValue" => $usedRanks,
                "kickerValue" => self::getKickerValue($ranks, $usedRanks)
            );
        } else if ($vals_ranks[0] == 3) {

            //Full House(no kickers)
            if ($vals_ranks[1] == 2) {
                return array(
                    "strength" => 6,
                    "cardValue" => array_search($vals_ranks[0], $arr_ranks) + array_search($vals_ranks[1], $arr_ranks),
                    "kickerValue" => 0
                );
            }
            //Three of a Kind
            else {
                $usedRanks = array(array_search($vals_ranks[0], $arr_ranks));
                $kickerVal = 0;
                for ($i = 0; $i < 5 - $vals_ranks[0]; $i++) {
                    array_push($usedRanks, self::getKickerValue($ranks, $usedRanks));
                    $kickerVal += end($usedRanks);
                }
                return array(
                    "strength" => 3,
                    "cardValue" => array_search($vals_ranks[0], $arr_ranks),
                    "kickerValue" => $kickerVal
                );
            }
        }

        //Two Pair
        else if ($vals_ranks[0] == 2 && $vals_ranks[1] == 2) {
            $usedRanks = array(array_search($vals_ranks[0], $arr_ranks), array_search($vals_ranks[1], $arr_ranks));
            $cardValues = array_keys($arr_ranks, 2);
            rsort($cardValues);
            return array(
                "strength" => 2,
                "cardValue" => $cardValues[0] + $cardValues[1],
                "kickerValue" => self::getKickerValue($ranks, $usedRanks)
            );
        }

        //One Pair
        else if ($vals_ranks[0] == 2) {
            $usedRanks = array(array_search($vals_ranks[0], $arr_ranks));
            $kickerVal = 0;
            for ($i = 0; $i < 5 - $vals_ranks[0]; $i++) {
                array_push($usedRanks, self::getKickerValue($ranks, $usedRanks));
                $kickerVal += end($usedRanks);
            }
            return array(
                "strength" => 1,
                "cardValue" => array_search($vals_ranks[0], $arr_ranks),
                "kickerValue" => $kickerVal
            );
        } else {
            $vals = array_column($cards, 'rank');
            rsort($vals);
            return array(
                "strength" => 0,
                "cardValue" => array_shift($vals),
                "kickerValue" => array_sum($vals)
            );
        }

    }



    private static function getKickerValue($ranks, $excludedRanks)
    {
        for ($i = 0; $i < count($ranks); $i++) {
            if (!is_array($excludedRanks)) {
                if ($ranks[$i] != $excludedRanks)
                    return $ranks[$i];
            } else {
                if (!in_array($ranks[$i], $excludedRanks))
                    return $ranks[$i];
            }

        }
    }

    private static function isConsecutive($arr_ranks)
    {
        sort($arr_ranks);
        for ($i = 0; $i < count($arr_ranks) - 4; $i++) {
            if ($arr_ranks[$i]->rank + 1 == $arr_ranks[$i + 1]->rank && $arr_ranks[$i]->rank + 2 == $arr_ranks[$i + 2]->rank && $arr_ranks[$i]->rank + 3 == $arr_ranks[$i + 3]->rank && $arr_ranks[$i]->rank + 4 == $arr_ranks[$i + 4]->rank)
                return array($arr_ranks[$i], $arr_ranks[$i + 1], $arr_ranks[$i + 2], $arr_ranks[$i + 3], $arr_ranks[$i + 4]);
        }
        return false;
    }


    //returns index of the winner
    public static function winnerHands($players)
    {
        $winners = array(0);
        for ($i = 1; $i < count($players); $i++) {
            $comparisonResult = self::compareHands(
                $players[end($winners)]->getHand()->getHandVals(),
                $players[$i]->getHand()->getHandVals()
            );

            if($comparisonResult == 0){
                $winners = array($i);
            }
            else if($comparisonResult == -1){
                $winners[] = $i;
            }
        }
        return array_unique($winners);
    }

    //return 1 if first wins, returns 0 if second wins, returns -1 if equivalent
    private static function compareHands($p1HandVals, $p2HandVals)
    {
        if ($p1HandVals['strength'] > $p2HandVals['strength'])
            return 1;
        else if ($p1HandVals['strength'] < $p2HandVals['strength'])
            return 0;
        else {
            if ($p1HandVals['cardValue'] > $p2HandVals['cardValue'])
                return 1;
            if ($p1HandVals['cardValue'] < $p2HandVals['cardValue'])
                return 0;
            else {
                if ($p1HandVals['kickerValue'] > $p2HandVals['kickerValue'])
                    return 1;
                if ($p1HandVals['kickerValue'] < $p2HandVals['kickerValue'])
                    return 0;
                else
                    return -1;
            }
        }
    }

}
?>