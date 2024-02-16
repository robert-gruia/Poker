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

        $rankCounts = array();
        $suitCounts = array();
        foreach ($cards as $card) {
            if (!isset($rankCounts[$card->rank])) {
                $rankCounts[$card->rank] = 0;
            }
            if (!isset($suitCounts[$card->suit])) {
                $suitCounts[$card->suit] = 0;
            }
            $rankCounts[$card->rank]++;
            $suitCounts[$card->suit]++;
        }

        // Check for poker hand types
        $isFlush = max($suitCounts) >= 5;
        $isStraight = self::isStraight($rankCounts) !== false;
        $rankCountValues = array_count_values($rankCounts);
        $isFourOfAKind = isset($rankCountValues[4]);
        $isThreeOfAKind = isset($rankCountValues[3]);
        $isPair = isset($rankCountValues[2]) && count(array_keys($rankCountValues, 2)) == 1;
        $isTwoPair = isset($rankCountValues[2]) && count(array_keys($rankCountValues, 2)) >= 2;
        $isFullHouse = $isThreeOfAKind && $isPair;

        // Determine hand type
        $handType = 0;
        $handValue = 0;
        $kickers = 0;
        //four of a kind
        if ($isFourOfAKind) {
            $handType = 7;
            $usedCard = array_keys($rankCounts, 4);
            $cards = array_filter($cards, function ($card) use ($usedCard) {
                return $card->rank != $usedCard[0];
            });
            $possibleKickers = array_column($cards, 'rank');
            rsort($possibleKickers);
            $kickers = $possibleKickers[0];
            print_r($usedCard);
            $handValue = array_sum($usedCard) * 4;
            //full house
        } elseif ($isFullHouse) {
            $handType = 6;
            $handValue = array_sum(array_keys($rankCounts, 3)) * 3 + array_sum(array_keys($rankCounts, 2)) * 2;
            //straight flush / royal flush
        } elseif ($isFlush && $isStraight) {
            $handType = $handValue == 14 ? 9 : 8;
            $handValue = array_sum(self::isStraight($rankCounts));
            //flush
        } elseif ($isFlush) {
            $handType = 5;
            $suit = array_keys($suitCounts, max($suitCounts))[0];
            $suitsArr = array_filter($cards, function ($card) use ($suit) {
                return $card->suit == $suit;
            });
            rsort($suitsArr);
            $handValue = $suitsArr[0] + $suitsArr[1] + $suitsArr[2] + $suitsArr[3] + $suitsArr[4];
            //straight
        } elseif ($isStraight) {
            $handType = 4;
            $handValue = array_sum(self::isStraight($rankCounts));
            //three of a kind
        } elseif ($isThreeOfAKind) {
            $handType = 3;
            $usedCard = array_keys($rankCounts, 3);
            $cards = array_filter($cards, function ($card) use ($usedCard) {
                return $card->rank != $usedCard[0];
            });
            $possibleKickers = array_column($cards, 'rank');
            rsort($possibleKickers);
            $kickers = $possibleKickers[0] + $possibleKickers[1];
            $handValue = array_sum($usedCard) * 3;
            //two pair    
        } elseif ($isTwoPair) {
            $handType = 2;
            $usedCards = array_keys($rankCounts, 2);
            $cards = array_filter($cards, function ($card) use ($usedCards) {
                return array_search($card->rank, $usedCards) != false;
            });
            $possibleKickers = array_column($cards, 'rank');
            $kickers = $possibleKickers[0];
            $handValue = array_sum($usedCards) * 2;
            //pair    
        } elseif ($isPair) {
            $handType = 1;
            $usedCard = array_keys($rankCounts, 2);
            $cards = array_filter($cards, function ($card) use ($usedCard) {
                return $card->rank != $usedCard[0];
            });
            $possibleKickers = array_column($cards, 'rank');
            rsort($possibleKickers);
            $kickers = $possibleKickers[0] + $possibleKickers[1] + $possibleKickers[2];
            $handValue = array_sum($usedCard) * 2;

        } else {
            $handValue = max(array_column($cards, 'rank'));

        }
        //print_r($kickers);

        return array(
            "strength" => $handType,
            "cardValue" => $handValue,
            "kickerValue" => $kickers
        );
    }

    private static function isStraight($rankCounts)
    {
        $ranks = array_keys($rankCounts);
        sort($ranks);
        for ($i = 0; $i <= count($ranks) - 5; $i++) {
            if ($ranks[$i + 4] - $ranks[$i] == 4) {
                return array($ranks[$i + 4], $ranks[$i + 3], $ranks[$i + 2], $ranks[$i + 1], $ranks[$i]);
            }
        }
        // Check for Ace-low straight
        if (in_array(14, $ranks) && in_array(2, $ranks) && in_array(3, $ranks) && in_array(4, $ranks) && in_array(5, $ranks)) {
            return array(15);
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