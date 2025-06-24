<?php

namespace Application;

use Domain\Player;
use Domain\GameMatch;
use Domain\EloCalculator;

class PlayerService {

    private $players;

    private $matches;

    private EloCalculator $elo;

    public function __construct($playerRepo, $matchRepo)
    {
        $this->players = $playerRepo;
        $this->matches = $matchRepo;
        $this->elo = new EloCalculator();
    }

    public function addPlayer(string $name): void
    {
        if ($this->players->find($name) === null) {
            $this->players->save(new Player($name));
        }
    }

    public function recordMatch(string $winnerName, string $loserName): void
    {
        $winner = $this->players->find($winnerName);
        $loser = $this->players->find($loserName);

        if ($winner && $loser && $winnerName !== $loserName) {
            [$newWinnerRating, $newLoserRating] = $this->elo->calculate($winner->rating(), $loser->rating());
            $winner->setRating($newWinnerRating);
            $loser->setRating($newLoserRating);
            $this->players->save($winner);
            $this->players->save($loser);
            $this->matches->save(new GameMatch($winnerName, $loserName, $newWinnerRating, $newLoserRating));
        }
    }

    public function resetRankings(): void
    {
        foreach ($this->players->all() as $player) {
            $player->setRating(1300);
            $this->players->save($player);
        }
        $this->matches->clear();
    }
}