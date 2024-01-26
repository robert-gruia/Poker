<?php
namespace Gruia\Poker;

class HandEvaluator
{

    public static function evaluate($playerCards, $tableCards)
    {
        $cards = array_merge($playerCards, $tableCards);
        $cards_ranks = array_column($cards, "rank");
        $handValues = array(
            "strength" => 0,
            "cardValue" => 0,
            "kickerValue" => 0
        );


        //royal flush
        if (self::isStraightFlush($cards, true) !== null) {
            $handValues['strength'] = 9;
            $cardValues = array_column(self::isStraightFlush($cards, true), 'rank');
            $handValues['cardValue'] = array_sum($cardValues);
        }

        //straight flush
        else if (self::isStraightFlush($cards, false) !== null) {
            $handValues['strength'] = 8;
            $cardValues = array_column(self::isStraightFlush($cards, false), 'rank');
            $handValues['cardValue'] = array_sum($cardValues);
        }

        //four of a kind
        else if (self::findLargestRepeatingElement($cards_ranks, 4) !== null) {
            $handValues['strength'] = 7;
            $cardValue = self::findLargestRepeatingElement($cards_ranks, 3);
            $handValues["cardValue"] = $cardValue;
            $handValues['kickerValue'] = self::findExclusiveLargestElement($cards_ranks, array($cardValue));
        }
        //full house
        else if (self::findLargestRepeatingElement($cards_ranks, 3) !== null && self::findLargestRepeatingElement($cards_ranks, 2) !== null) {
            $handValues['strength'] = 6;
            $handValues["cardValue"] = self::findLargestRepeatingElement($cards_ranks, 3) + self::findLargestRepeatingElement($cards_ranks, 2);
        }
        //flush
        else if (self::findLargestSameSuit($cards) !== null) {
            $handValues['strength'] = 5;
            $sameSuitCards = self::findLargestSameSuit($cards);
            $handValues['cardValue'] = array_sum(array_column($sameSuitCards, 'rank'));
        }
        //straight
        else if (self::isStraight($cards) !== null) {
            $handValues['strength'] = 4;
            $straightCards = self::isStraight($cards);
            $handValues['cardValue'] = array_sum(array_column($straightCards, 'rank'));
        }
        //three of a kind
        else if (self::findLargestRepeatingElement($cards_ranks, 3) !== null) {
            $handValues['strength'] = 3;
            $handValues["cardValue"] = self::findLargestRepeatingElement($cards_ranks, 3);
            $firstKicker = self::findExclusiveLargestElement($cards_ranks, array($handValues["cardValue"]));
            $secondKicker = self::findExclusiveLargestElement($cards_ranks, array($handValues["cardValue"], $firstKicker));
            $handValues['kickerValue'] = $firstKicker + $secondKicker;
        }
        //two pair
        else if (self::findNLargestRepeatingElements($cards_ranks, 2, 2) !== null) {
            $handValues['strength'] = 2;
            $cardValues = self::findNLargestRepeatingElements($cards_ranks, 2, 2);
            $handValues['cardValue'] = array_sum($cardValues);
            $handValues['kickerValue'] = self::findExclusiveLargestElement($cards_ranks, $cardValues);
        }
        //pair
        else if (self::findLargestRepeatingElement($cards_ranks, 2) !== null) {
            $handValues['strength'] = 1;
            $handValues["cardValue"] = self::findLargestRepeatingElement($cards_ranks, 2);
            $firstKicker = self::findExclusiveLargestElement($cards_ranks, array($handValues["cardValue"]));
            $secondKicker = self::findExclusiveLargestElement($cards_ranks, array($handValues["cardValue"], $firstKicker));
            $thirdKicker = self::findExclusiveLargestElement($cards_ranks, array($handValues["cardValue"], $firstKicker, $secondKicker));
            $handValues['kickerValue'] = $firstKicker + $secondKicker + $thirdKicker;
        }
        //high card
        else {
            $handValues['strength'] = 0;
            $handValues["cardValue"] = self::findLargestRepeatingElement($cards_ranks, 1);
            $firstKicker = self::findExclusiveLargestElement($cards_ranks, array($handValues["cardValue"]));
            $secondKicker = self::findExclusiveLargestElement($cards_ranks, array($handValues["cardValue"], $firstKicker));
            $thirdKicker = self::findExclusiveLargestElement($cards_ranks, array($handValues["cardValue"], $firstKicker, $secondKicker));
            $fourthKicker = self::findExclusiveLargestElement($cards_ranks, array($handValues["cardValue"], $firstKicker, $secondKicker, $thirdKicker));
            $handValues['kickerValue'] = $firstKicker + $secondKicker + $thirdKicker + $fourthKicker;
        }

        return $handValues;

    }

    private static function isStraight($arr)
    {
        sort($arr);
        for ($i = 0; $i < count($arr) - 4; $i++) {
            if ($arr[$i]->rank + 1 == $arr[$i + 1]->rank && $arr[$i]->rank + 2 == $arr[$i + 2]->rank && $arr[$i]->rank + 3 == $arr[$i + 3]->rank && $arr[$i]->rank + 4 == $arr[$i + 4]->rank)
                return array($arr[$i], $arr[$i + 1], $arr[$i + 2], $arr[$i + 3], $arr[$i + 4]);
        }

        for ($i = 0; $i < count($arr); $i++) {
            if ($arr[$i]->rank == 14) {
                $arr[$i]->rank = 1;
            }
        }

        sort($arr);
        for ($i = 0; $i < count($arr) - 4; $i++) {
            if ($arr[$i]->rank + 1 == $arr[$i + 1]->rank && $arr[$i]->rank + 2 == $arr[$i + 2]->rank && $arr[$i]->rank + 3 == $arr[$i + 3]->rank && $arr[$i]->rank + 4 == $arr[$i + 4]->rank)
                return array($arr[$i], $arr[$i + 1], $arr[$i + 2], $arr[$i + 3], $arr[$i + 4]);
        }

        return null;
    }

    private static function isStraightFlush($cards, $royalFlush)
    {
        $cards_copy = $cards;
        if (!$royalFlush) {
            for ($i = 0; $i < count($cards_copy); $i++) {
                if ($cards_copy[$i]->rank == 14) {
                    $cards_copy[$i]->rank = 1;
                }
            }
        }

        $sameSuitCards = self::findLargestSameSuit($cards_copy);
        //if($sameSuitCards !== null) echo implode(" " ,array_column($sameSuitCards, 'rank')) . " ";
        if ($sameSuitCards && self::areConsecutive($sameSuitCards)) {
            if ($royalFlush) {
                return (min(array_column($sameSuitCards, 'rank')) == 10) ? $sameSuitCards : null;
            } else {
                return $sameSuitCards;
            }
        } else {
            return null;
        }
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

            if ($comparisonResult == 0) {
                $winners = array($i);
            } else if ($comparisonResult == -1) {
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

    private static function findLargestRepeatingElement($arr, $numOccurrencies)
    {
        $count = array_count_values($arr);
        $largestRepeatingElement = null;

        foreach ($count as $element => $occurrencies) {
            if ($largestRepeatingElement === null || $element > $largestRepeatingElement) {
                if ($occurrencies == $numOccurrencies) {
                    $largestRepeatingElement = $element;
                }
            }
        }

        return $largestRepeatingElement;
    }

    private static function findNLargestRepeatingElements($arr, $numOccurrencesPerElement, $numElements)
    {
        $count = array_count_values($arr);
        arsort($count);

        $repeatingElements = [];
        foreach ($count as $element => $occurrences) {
            if ($occurrences == $numOccurrencesPerElement) {
                $repeatingElements[] = $element;

                if (count($repeatingElements) === $numElements) {
                    return $repeatingElements;
                }
            }
        }
        return null;
    }

    private static function findExclusiveLargestElement($arr, $values)
    {
        $filteredNumbers = array_diff($arr, $values);

        if (empty($filteredNumbers)) {
            return null;
        }

        $largestNumber = max($filteredNumbers);

        return $largestNumber;
    }

    private static function findLargestSameSuit($cards)
    {
        $groupedCards = [];
        foreach ($cards as $card) {
            $suit = $card->suit;
            $groupedCards[$suit][] = $card;
        }

        $filteredCards = array_filter($groupedCards, function ($arr) {
            return count($arr) == 5;
        });

        if (empty($filteredCards)) {
            return null;
        }

        foreach ($filteredCards as &$cards) {
            usort($cards, function ($a, $b) {
                return $b->rank - $a->rank;
            });
        }

        uasort($filteredCards, function ($a, $b) {
            return reset($b)->rank - reset($a)->rank;
        });

        $result = [];
        foreach ($filteredCards as $cards) {
            $result = array_merge($result, array_slice($cards, 0, 5 - count($result)));
            if (count($result) >= 5) {
                break;
            }
        }

        return $result;
    }

    private static function areConsecutive($arr) {
        sort($arr);
        for ($i = 0; $i < count($arr) - 4; $i++) {
            if ($arr[$i]->rank + 1 == $arr[$i + 1]->rank && $arr[$i]->rank + 2 == $arr[$i + 2]->rank && $arr[$i]->rank + 3 == $arr[$i + 3]->rank && $arr[$i]->rank + 4 == $arr[$i + 4]->rank)
                return true;
        }
        return false;
    }

}
?>