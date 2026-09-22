<?php
// Zawody z karami — zadanie B: formularz
// Plik jest kopią zadania A.

// ===== FUNKCJE =====

// DANE OD PROWADZĄCEGO — wzorzec, nie ruszaj.
function formatujCzas($sekundy, $separator = ':') {
    return floor($sekundy / 60) . $separator . str_pad($sekundy % 60, 2, '0', STR_PAD_LEFT);
}

function czasZKarami($okrazenie) {
    return $okrazenie['czas'] + $okrazenie['kary'] * 5;
}

function najlepsze($okrazenia) {
    if (count($okrazenia) == 0) {
        return null;
    }

    $najlepszy = null;

    foreach ($okrazenia as $okrazenie) {
        $czas = czasZKarami($okrazenie);

        if ($najlepszy === null || $czas < $najlepszy) {
            $najlepszy = $czas;
        }
    }

    return $najlepszy;
}

function srednia($okrazenia) {
    $suma = 0;

    foreach ($okrazenia as $okrazenie) {
        $suma += czasZKarami($okrazenie);
    }

    return round($suma / count($okrazenia));
}

function kategoria($czas) {
    if ($czas === null) {
        return 'brak';
    } elseif ($czas <= 290) {
        return 'elita';
    } elseif ($czas <= 310) {
        return 'zaawansowany';
    } else {
        return 'amator';
    }
}

// ===== DANE — gotowe =====
$zawodnicy = [
    ['nazwisko' => 'Ewa Barańska',   'okrazenia' => [['czas' => 280, 'kary' => 1], ['czas' => 283, 'kary' => 0], ['czas' => 279, 'kary' => 1]]],
    ['nazwisko' => 'Marek Cichoń',   'okrazenia' => [['czas' => 283, 'kary' => 1], ['czas' => 291, 'kary' => 0], ['czas' => 288, 'kary' => 0]]],
    ['nazwisko' => 'Zofia Grabiec',  'okrazenia' => [['czas' => 292, 'kary' => 0], ['czas' => 312, 'kary' => 3], ['czas' => 299, 'kary' => 1]]],
    ['nazwisko' => 'Ola Dudek',      'okrazenia' => [['czas' => 318, 'kary' => 0], ['czas' => 325, 'kary' => 2]]],
    ['nazwisko' => 'Piotr Fijałek',  'okrazenia' => []],
];

// ===== OBSŁUGA FORMULARZA POST =====

// Jeżeli wysłano formularz dodawania okrążenia,
// najpierw dodajemy nowe okrążenie do zawodnika.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numerZawodnika = (int) $_POST['zawodnik'];
    $czas = (int) $_POST['czas'];
    $kary = (int) $_POST['kary'];

    if (isset($zawodnicy[$numerZawodnika])) {
        $zawodnicy[$numerZawodnika]['okrazenia'][] = [
            'czas' => $czas,
            'kary' => $kary
        ];
    }
}

// ===== RESZTA OBLICZEŃ =====

// Wszystkie okrążenia zawodów w jednej liście.
$wszystkieOkrazenia = [];

foreach ($zawodnicy as $zawodnik) {
    foreach ($zawodnik['okrazenia'] as $okrazenie) {
        $wszystkieOkrazenia[] = $okrazenie;
    }
}

$liczbaElita = 0;

foreach ($zawodnicy as $zawodnik) {
    $n = najlepsze($zawodnik['okrazenia']);
    $kat = kategoria($n);

    if ($kat == 'elita') {
        $liczbaElita++;
    }
}

// ===== FILTR GET =====

$wybranaKategoria = $_GET['kategoria'] ?? 'wszystkie';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>Zawody z karami</title>
<style>
    body { font-family: sans-serif; margin: 2rem; }
    table { border-collapse: collapse; }
    th, td { border: 1px solid #999; padding: 0.4rem 0.8rem; text-align: left; }
    caption { font-weight: bold; margin-bottom: 0.5rem; }
    tfoot td { font-weight: bold; }
    .elita        { background: #d4f5d4; }
    .zaawansowany { background: #fff3c4; }
    .amator       { background: #f5d4d4; }
</style>
</head>
<body>

<h1>Zawody z karami: czasy okrążeń</h1>

<!-- ===== FORMULARZ POST ===== -->
<h2>Dodaj okrążenie</h2>

<form method="post">
    <label>
        Zawodnik:
        <select name="zawodnik">
            <?php foreach ($zawodnicy as $numer => $zawodnik): ?>
                <option value="<?= $numer ?>">
                    <?= $zawodnik['nazwisko'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Czas:
        <input type="number" name="czas" required>
    </label>

    <label>
        Kary:
        <input type="number" name="kary" value="0" required>
    </label>

    <button type="submit">Dodaj okrążenie</button>
</form>

<!-- ===== FORMULARZ GET ===== -->
<h2>Filtr kategorii</h2>

<form method="get">
    <select name="kategoria">
        <option value="wszystkie" <?= $wybranaKategoria == 'wszystkie' ? 'selected' : '' ?>>
            Wszystkie
        </option>
        <option value="elita" <?= $wybranaKategoria == 'elita' ? 'selected' : '' ?>>
            Elita
        </option>
        <option value="zaawansowany" <?= $wybranaKategoria == 'zaawansowany' ? 'selected' : '' ?>>
            Zaawansowany
        </option>
        <option value="amator" <?= $wybranaKategoria == 'amator' ? 'selected' : '' ?>>
            Amator
        </option>
        <option value="brak" <?= $wybranaKategoria == 'brak' ? 'selected' : '' ?>>
            Brak
        </option>
    </select>

    <button type="submit">Pokaż</button>
</form>

<p>
    Zawodników: <?= count($zawodnicy) ?>,
    w kategorii elita: <?= $liczbaElita ?>
</p>

<table>
    <caption>Wyniki zawodników (czasy razem z karami)</caption>

    <thead>
        <tr>
            <th scope="col">Zawodnik</th>
            <th scope="col">Okrążenia</th>
            <th scope="col">Najlepsze</th>
            <th scope="col">Średnie</th>
            <th scope="col">Kategoria</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($zawodnicy as $zawodnik): ?>

            <?php
            if (count($zawodnik['okrazenia']) == 0) {
                $kat = 'brak';

                if ($wybranaKategoria != 'wszystkie' && $wybranaKategoria != $kat) {
                    continue;
                }

                echo '<tr><td>' . $zawodnik['nazwisko'] . '</td><td colspan="4">brak ukończonych okrążeń</td></tr>';
                continue;
            }

            $najlepszyCzas = najlepsze($zawodnik['okrazenia']);
            $kat = kategoria($najlepszyCzas);

            // Filtr kategorii
            if ($wybranaKategoria != 'wszystkie' && $wybranaKategoria != $kat) {
                continue;
            }

            $czasy = [];

            foreach ($zawodnik['okrazenia'] as $okrazenie) {
                $czasy[] = formatujCzas(czasZKarami($okrazenie));
            }

            $sredniCzas = srednia($zawodnik['okrazenia']);
            ?>

            <tr class="<?= $kat ?>">
                <td><?= $zawodnik['nazwisko'] ?></td>
                <td><?= implode(', ', $czasy) ?></td>
                <td><?= formatujCzas($najlepszyCzas) ?></td>
                <td><?= formatujCzas($sredniCzas) ?></td>
                <td><?= $kat ?></td>
            </tr>

        <?php endforeach; ?>
    </tbody>

    <tfoot>
        <?php
        $sredniaZawodow = srednia($wszystkieOkrazenia);
        $najlepszeZawodow = najlepsze($wszystkieOkrazenia);
        ?>

        <tr>
            <td colspan="5">
                Okrążeń łącznie: <?= count($wszystkieOkrazenia) ?>,
                najlepsze okrążenie zawodów: <?= formatujCzas($najlepszeZawodow) ?>,
                średnie okrążenie zawodów: <?= formatujCzas($sredniaZawodow) ?>
            </td>
        </tr>
    </tfoot>
</table>

</body>
</html>