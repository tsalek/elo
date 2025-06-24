<?php

namespace Infrastructure;

use Domain\GameMatch;

class JsonMatchRepository {

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

        return array_map([GameMatch::class, 'fromArray'], $data);
    }

    public function save(GameMatch $match): void
    {
        $matches = $this->all();
        $matches[] = $match;
        file_put_contents($this->file, json_encode(array_map(fn($m) => $m->toArray(), $matches), JSON_PRETTY_PRINT));
    }

    public function clear(): void
    {
        file_put_contents($this->file, json_encode([]));
    }
}