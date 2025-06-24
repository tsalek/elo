<?php

namespace Infrastructure;

use Domain\Player;

class JsonPlayerRepository {

    private string $file;

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function all(): array
    {
        if (!file_exists($this->file))
            return [];
        $data = json_decode(file_get_contents($this->file), true);

        return array_map([Player::class, 'fromArray'], $data);
    }

    public function find(string $name): ?Player
    {
        foreach ($this->all() as $p) {
            if ($p->name() === $name)
                return $p;
        }

        return null;
    }

    public function save(Player $player): void
    {
        $players = $this->all();
        $players = array_filter($players, fn($p) => $p->name() !== $player->name());
        $players[] = $player;
        file_put_contents($this->file, json_encode(array_map(fn($p) => $p->toArray(), $players), JSON_PRETTY_PRINT));
    }
}