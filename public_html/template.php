<?php

function kolorRangi(string $ranga): string
{
    return match ($ranga) {
        'IMPERATORY' => 'bg-warning text-dark',          // Złoto
        'CESARZOWIE' => 'bg-danger text-light',           // Czerwony
        'KRÓLE' => 'bg-success text-light',          // Zielony
        'MAGIKI' => 'bg-info text-dark',              // Błękit
        'MISZCZE' => 'bg-purple text-light',           // Niestandardowy (dodamy styl niżej)
        'LESZCZE' => 'bg-dark text-light',             // Szary
        'OLABOGA' => 'bg-pink text-dark',              // Różowy
        'LAMUSY' => 'bg-light text-dark border',      // Biały z obramowaniem
        default => 'bg-muted text-muted'
    };
}

function nazwaRangi(string $ranga): string
{
    return match ($ranga) {
        'IMPERATORY' => '👑 IMPERATORY',
        'CESARZOWIE' => '🦅 CESARZOWIE',
        'KRÓLE' => '🤴 KRÓLE',
        'MAGIKI' => '🪄 MAGIKI',
        'MISZCZE' => '🥋 MISZCZE',
        'LESZCZE' => '🐟 LESZCZE',
        'OLABOGA' => '😱 OLABOGA',
        'LAMUSY' => '🧢 LAMUSY',
        default => '🫥 ANDRZEJE'
    };
}

function isLightMode()
{
    return isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light';
}

function playerStats($playerName, $matches)
{
    $played = $wins = 0;
    foreach ($matches as $m) {
        if ($m->winner === $playerName || $m->loser === $playerName) {
            $played++;
            if ($m->winner === $playerName)
                $wins++;
        }
    }
    $winrate = $played > 0 ? round(100 * $wins / $played, 1) : 0;

    return [$played, $wins, $winrate];
}

?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="<?= isLightMode() ? 'light' : 'dark' ?>">
<head>
    <meta charset="UTF-8">
    <title>Ranking Bilardowy (Elo)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
</head>
<body class="bg-body text-body">
<div class="container elo-container py-5">
    <h1 class="mb-4">Ranking Bilardowy (Elo)</h1>

    <form method="post" class="mb-4">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <?php if (!$auth->isLoggedIn()): ?>
                <input type="password" name="password" class="form-control w-auto" placeholder="Hasło admina">
                <button name="login" class="btn btn-primary">Zaloguj</button>
                <button type="button" onclick="toggleTheme()" class="btn btn-outline-secondary">🌙/☀️</button>
            <?php else: ?>
                <button name="logout" class="btn btn-secondary">Wyloguj</button>
                <button name="reset" class="btn btn-danger" onclick="return confirm('Na pewno resetować ranking?')">
                    Reset rankingów
                </button>
                <a href="?export=1" class="btn btn-success">📥 Eksport CSV</a>
                <button type="button" onclick="toggleTheme()" class="btn btn-outline-secondary">🌙/☀️</button>
            <?php endif; ?>
        </div>
    </form>

    <?php if ($auth->isLoggedIn()): ?>
        <form method="post" class="mb-4">
            <h4>Dodaj gracza</h4>
            <div class="input-group mb-2">
                <input type="text" name="name" class="form-control" required>
                <button name="add" class="btn btn-primary">Dodaj</button>
            </div>
        </form>

        <form method="post" class="mb-4">
            <h4>Dodaj mecz</h4>
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <select name="winner" class="form-select">
                        <?php foreach ($players as $p): ?>
                            <option><?= $p->name() ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">WYGRAŁ Z</div>
                <div class="col-auto">
                    <select name="loser" class="form-select">
                        <?php foreach ($players as $p): ?>
                            <option><?= $p->name() ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button name="match" class="btn btn-primary">Zapisz</button>
                </div>
            </div>
        </form>
    <?php endif; ?>
    <h4>Ranking</h4>
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Gracz</th>
            <th>Ranking</th>
            <th>Ranga</th>
            <?php if ($auth->isLoggedIn()): ?>
                <th>Rozegrane</th>
                <th>Wygrane</th>
            <?php endif; ?>
            <th>Win%</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        foreach (sortPlayersByRating($players) as $p):
            [$played, $wins, $winrate] = playerStats($p->name(), $matches);
            $elo = $p->rating();
            $ranga = match (true) {
                $elo > 1800 => "IMPERATORY",
                $elo > 1600 => "CESARZOWIE",
                $elo > 1500 => "KRÓLE",
                $elo > 1400 => "MAGIKI",
                $elo > 1300 => "MISZCZE",
                $elo > 1200 => "LESZCZE",
                $elo > 1100 => "OLABOGA",
                $elo > 1000 => "LAMUSY",
                default => "ANDRZEJE"
            };
            ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= $p->name() ?></td>
                <td><?= $elo ?></td>
                <td><span class="badge <?= kolorRangi($ranga) ?>"><?= nazwaRangi($ranga) ?></span></td>
                <?php if ($auth->isLoggedIn()): ?>
                    <td><?= $played ?></td>
                    <td><?= $wins ?></td>
                <?php endif; ?>
                <td><?= $winrate ?>%</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>


    <h4>Historia ostatnich 20 meczów</h4>
    <ul class="list-group">
        <?php foreach (array_reverse(array_slice($matches, -20)) as $m): ?>
            <li class="list-group-item"><?= $m->date ?> - <strong><?= $m->winner ?></strong> pokonał <?= $m->loser ?>
                (<?= $m->winner_rating ?> : <?= $m->loser_rating ?>)
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<div class="container pb-5">
    <h4>📈 Historia rankingów graczy</h4>
    <div id="charts" class="d-flex flex-wrap" style="width: 100%;"></div>
</div>

<script>
    function toggleTheme() {
        const html = document.documentElement;
        const current = html.getAttribute('data-bs-theme');
        const next = current === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-bs-theme', next);
        document.cookie = 'theme=' + next + ';path=/';
    }

    const history = <?php
        $playerHistories = [];
        foreach ($players as $p) {
            $playerHistories[$p->name()] = [];
        }
        foreach ($matches as $m) {
            $playerHistories[$m->winner][] = ['rating' => $m->winner_rating, 'date' => $m->date];
            $playerHistories[$m->loser][] = ['rating' => $m->loser_rating, 'date' => $m->date];
        }
        echo json_encode($playerHistories);
        ?>;

    document.addEventListener("DOMContentLoaded", () => {
        const container = document.getElementById("charts");
        Object.entries(history).forEach(([player, entries]) => {
            if (entries.length === 0) return;
            const canvas = document.createElement("canvas");
            const card = document.createElement("div");
            card.className = "card my-3 p-3 col-12 col-lg-4";
            card.innerHTML = `<h5>${player}</h5>`;
            card.appendChild(canvas);
            container.appendChild(card);

            const labels = entries.map(e => e.date);
            const data = entries.map(e => e.rating);

            new Chart(canvas, {
                type   : 'line',
                data   : {
                    labels  : labels,
                    datasets: [{
                        label          : 'Ranking Elo',
                        data           : data,
                        fill           : false,
                        borderColor    : 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension        : 0.2
                    }]
                },
                options: {
                    scales: {
                        y: {beginAtZero: false}
                    }
                }
            });
        });
    });
</script>
</body>
</html>
