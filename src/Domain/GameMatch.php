<?php

namespace Domain;

class GameMatch {

    public string $winner;

    public string $loser;

    public string $date;

    public int $winner_rating;

    public int $loser_rating;

    public function __construct(string $winner, string $loser, int $winner_rating, int $loser_rating)
    {
        $this->winner = $winner;
        $this->loser = $loser;
        $this->winner_rating = $winner_rating;
        $this->loser_rating = $loser_rating;
        $this->date = date('Y-m-d H:i:s');
    }

    public function toArray(): array
    {
        return [
            'winner'        => $this->winner,
            'loser'         => $this->loser,
            'date'          => $this->date,
            'winner_rating' => $this->winner_rating,
            'loser_rating'  => $this->loser_rating,
        ];
    }

    public static function fromArray(array $data): self
    {
        $match = new self($data['winner'], $data['loser'], $data['winner_rating'], $data['loser_rating']);
        $match->date = $data['date'];

        return $match;
    }
}