<?php
require_once __DIR__ . '/../src/bootstrap.php';

use Application\PlayerService;
use Infrastructure\JsonPlayerRepository;
use Infrastructure\JsonMatchRepository;
use Infrastructure\Auth;

$playerRepo = new JsonPlayerRepository('players.json');
$matchRepo = new JsonMatchRepository('matches.json');
$auth = new Auth();
$service = new PlayerService($playerRepo, $matchRepo);

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $auth->login($_POST['password']);
    } elseif (isset($_POST['logout'])) {
        $auth->logout();
    } elseif ($auth->isLoggedIn()) {
        if (isset($_POST['add'])) {
            $service->addPlayer($_POST['name']);
        } elseif (isset($_POST['match'])) {
            $service->recordMatch($_POST['winner'], $_POST['loser']);
        } elseif (isset($_POST['reset'])) {
            $service->resetRankings();
        }
    }
    header("Location: index.php");
    exit;
}

$players = $playerRepo->all();
$matches = $matchRepo->all();

function sortPlayersByRating($players)
{
    usort($players, fn($a, $b) => $b->rating() <=> $a->rating());

    return $players;
}

function exportCSV($players)
{
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="ranking.csv"');
    $f = fopen('php://output', 'w');
    fputcsv($f, ['Imie', 'Ranking']);
    foreach ($players as $p) {
        fputcsv($f, [$p->name(), $p->rating()]);
    }
    fclose($f);
    exit;
}

if (isset($_GET['export'])) {
    exportCSV($players);
}

include 'template.php';