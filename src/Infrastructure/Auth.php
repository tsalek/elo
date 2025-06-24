<?php

namespace Infrastructure;

class Auth {

    private string $password;

    public function __construct()
    {

        $jsonPath = '../auth.json';
        $data = json_decode(file_get_contents($jsonPath), true);

        if (!is_array($data) || !isset($data[0]['password'])) {
            throw new \RuntimeException("Błąd w pliku auth.json");
        }

        $this->password = $data[0]['password'];
    }

    public function login(string $password): void
    {
        if ($password === $this->password) {
            $_SESSION['logged_in'] = true;
        }
    }

    public function logout(): void
    {
        unset($_SESSION['logged_in']);
    }

    public function isLoggedIn(): bool
    {
        return $_SESSION['logged_in'] ?? false;
    }
}