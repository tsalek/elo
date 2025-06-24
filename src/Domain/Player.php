<?php

namespace Domain;

class Player {

    private string $name;

    private int $rating;

    public function __construct(string $name, int $rating = 1300)
    {
        $this->name = $name;
        $this->rating = $rating;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function rating(): int
    {
        return $this->rating;
    }

    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    public function toArray(): array
    {
        return ['name' => $this->name, 'rating' => $this->rating];
    }

    public static function fromArray(array $data): self
    {
        return new self($data['name'], $data['rating']);
    }
}