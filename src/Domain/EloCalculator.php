<?php

namespace Domain;

class EloCalculator {

    private int $kFactor = 40;

    public function calculate(int $winnerRating, int $loserRating): array
    {
        $expectedWin = 1 / (1 + pow(10, ($loserRating - $winnerRating) / 400));
        $expectedLose = 1 - $expectedWin;

        $newWinnerRating = $winnerRating + $this->kFactor * (1 - $expectedWin);
        $newLoserRating = $loserRating + $this->kFactor * (0 - $expectedLose);

        return [round($newWinnerRating), round($newLoserRating)];
    }
}