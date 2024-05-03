<?php
error_reporting(E_ALL & ~E_WARNING);

class HandEvaluator
{

    public static function evaluate($playerCards, $tableCards)
    {
        $cards = array_merge($playerCards, $tableCards);
        usort($cards, function ($a, $b) {
            return $b->rank - $a->rank;
        });

        $rankCounts = self::getCardCounts($cards, 'rank');
        $suitCounts = self::getCardCounts($cards, 'suit');

        $handType = self::getHandType($rankCounts, $suitCounts);
        $handValue = self::getHandValue($handType, $cards, $rankCounts, $suitCounts);
        $kickers = self::getKickers($handType, $cards, $rankCounts);

        return array(
            "strength" => $handType,
            "cardValue" => $handValue,
            "kickerValue" => $kickers
        );
    }

    private static function getCardCounts($cards, $property)
    {
        $counts = array();
        foreach ($cards as $card) {
            if (!isset($counts[$card->$property])) {
                $counts[$card->$property] = 0;
            }
            $counts[$card->$property]++;
        }
        return $counts;
    }

    private static function getHandType($rankCounts, $suitCounts)
    {
        $isFlush = max($suitCounts) >= 5;
        $isStraight = self::isStraight($rankCounts) !== false;
        $rankCountValues = array_count_values($rankCounts);
        $isFourOfAKind = isset($rankCountValues[4]);
        $isThreeOfAKind = isset($rankCountValues[3]);
        $isPair = isset($rankCountValues[2]);
        $isTwoPair = count(array_filter($rankCounts, function ($count) {
            return $count >= 2; })) == 2;
        $isFullHouse = $isThreeOfAKind && $isPair;

        if ($isFlush && $isStraight) {
            return 8; // Straight flush
        } elseif ($isFourOfAKind) {
            return 7; // Four of a kind
        } elseif ($isFullHouse) {
            return 6; // Full house
        } elseif ($isFlush) {
            return 5; // Flush
        } elseif ($isStraight) {
            return 4; // Straight
        } elseif ($isThreeOfAKind) {
            return 3; // Three of a kind
        } elseif ($isTwoPair) {
            return 2; // Two pair
        } elseif ($isPair && !$isTwoPair) {
            return 1; // One pair
        } else {
            return 0; // High card
        }
    }

    private static function isStraight($rankCounts)
    {
        $ranks = array_keys($rankCounts);
        sort($ranks);
        for ($i = 0; $i <= count($ranks) - 5; $i++) {
            if ($ranks[$i + 4] - $ranks[$i] == 4) {
                return true;
            }
        }
        return in_array(14, $ranks) && in_array(2, $ranks) && in_array(3, $ranks) && in_array(4, $ranks) && in_array(5, $ranks);
    }

    private static function getHandValue($handType, $cards, $rankCounts, $suitCounts)
    {
        switch ($handType) {
            case 8: // Straight flush
            case 4: // Straight
                return array_sum(array_column($cards, 'rank'));
            case 5: // Flush
                return max(array_column($cards, 'rank'));
            case 7: // Four of a kind
            case 6: // Full house
            case 3: // Three of a kind
                return array_keys($rankCounts, max($rankCounts))[0];
            case 2: // Two pair
                $pairs = array_keys($rankCounts, 2);
                return max($pairs);
            case 1: // One pair
                return array_keys($rankCounts, 2)[0];
            default: // High card
                return max(array_column($cards, 'rank'));
        }
    }

    private static function getKickers($handType, $cards, $rankCounts)
    {
        $kickers = array_column($cards, 'rank');
        rsort($kickers);
        switch ($handType) {
            case 8: // Straight flush
            case 5: // Flush
            case 4: // Straight
                return 0;
            case 7: // Four of a kind
            case 6: // Full house
            case 3: // Three of a kind
                return array_sum(array_slice($kickers, 1, 3));
            case 2: // Two pair
                return array_sum(array_slice($kickers, 2, 3));
            case 1: // One pair
                return array_sum(array_slice($kickers, 2, 4));
            default: // High card
                return array_sum(array_slice($kickers, 1, 4));
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

}




?>