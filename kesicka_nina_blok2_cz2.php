<?php
// Wyniki zawodów biegowych. Czasy okrążeń w sekundach, po jednym na ukończone okrążenie.
$zawodnicy = [
    ['nazwisko' => 'Anna Kowalska', 'okrazenia' => [312, 298, 305]],
    ['nazwisko' => 'Piotr Nowak', 'okrazenia' => [355, 430, 341]],
    ['nazwisko' => 'Marek Zieliński', 'okrazenia' => []],
    ['nazwisko' => 'Ewa Wiśniewska', 'okrazenia' => [402, 377, 395]],
];
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="utf-8">
    <title>Wyniki zawodów</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 2rem;
        }

        table {
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 0.4rem 0.8rem;
            text-align: left;
        }

        caption {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        tfoot td {
            font-weight: bold;
        }

        .elita {
            background: #d4f5d4;
        }

        .zaawansowany {
            background: #fff3c4;
        }

        .amator {
            background: #f5d4d4;
        }
    </style>
</head>

<body>
    <h1>Zawody biegowe: czasy okrążeń</h1>
    <table>
        <caption>Wyniki zawodników</caption>
        <thead>
            <tr>
                <th scope="col">Zawodnik</th>
                <th scope="col">Okrążenia</th>
                <th scope="col">Najlepsze</th>
                <th scope="col">Średnie</th>
                <th scope="col">Kategoria</th>
                <th scope="col">Uwagi</th>
            </tr>
        </thead>
        <tbody>
            <!-- tu pętla: jeden <tr> na zawodnika -->
            <?php
            function formatczasu($czas, $separator = ':')
            {
                $minuty = floor($czas / 60);
                $sekundy = $czas % 60;

                return $minuty . $separator . str_pad($sekundy, 2, '0', STR_PAD_LEFT);
            }

            function uwagi($okrazenia)
            {
                $uwagi = [];
                $najlepsze = min($okrazenia);

                foreach ($okrazenia as $czas) {
                    if ($czas >= 420) {
                        $uwagi[] = 'słabe okrążenie';
                        break;
                    }
                }

                $rownetempo = true;

                foreach ($okrazenia as $czas) {
                    if ($czas > $najlepsze + 15) {
                        $rownetempo = false;
                        break;
                    }
                }

                if ($rownetempo) {
                    $uwagi[] = 'równe tempo';
                }

                return $uwagi;
            }

            $liczbaZawodnikow = 0;
            $liczbaElita = 0;
            $najlepszeZawody = 0;
            $liczbaOkrazen = 0;
            foreach ($zawodnicy as $zawodnik) {
                $liczbaZawodnikow++;
                $nazwisko = $zawodnik['nazwisko'];
                $okrazenia = $zawodnik['okrazenia'];
                if (count($okrazenia) == 0) {
                    echo "<tr>";
                    echo "<td>$nazwisko</td>";
                    echo "<td colspan='5'>brak ukończonych okrążeń</td>";
                    echo "</tr>";

                    continue;
                }
                $liczbaOkrazen += count($okrazenia);
                $najlepsze = min($okrazenia);
                $srednie = round(array_sum($okrazenia) / count($okrazenia));
                if ($najlepszeZawody == 0 || $najlepsze < $najlepszeZawody) {
                    $najlepszeZawody = $najlepsze;
                }
                $formatowaneOkrazenia = [];
                foreach ($okrazenia as $czas) {
                    $formatowaneOkrazenia[] = formatczasu($czas);
                }
                $najlepszeFormat = formatczasu($najlepsze);
                $srednieFormat = formatczasu($srednie);
                if ($najlepsze < 300) {
                    $kategoria = 'elita';
                    $liczbaElita++;
                } elseif ($najlepsze < 360) {
                    $kategoria = 'zaawansowany';
                } else {
                    $kategoria = 'amator';
                }
                $uwagi = uwagi($okrazenia);
                echo "<tr class='$kategoria'>";
                echo "<td>$nazwisko</td>";
                echo "<td>" . implode(', ', $formatowaneOkrazenia) . "</td>";
                echo "<td>$najlepszeFormat</td>";
                echo "<td>$srednieFormat</td>";
                echo "<td>$kategoria</td>";
                echo "<td>" . implode('; ', $uwagi) . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
        <tfoot>
            <!-- tu podsumowanie: ilu zawodników, ilu w kategorii "elita", najlepsze okrążenie zawodów, ile okrążeń łącznie -->
            <?php
            $najlepszeZawodyFormat = formatczasu($najlepszeZawody);
            ?>

            <tr>
                <td colspan="6">
                    Zawodników: <?php echo $liczbaZawodnikow ?>
                    Elita: <?php echo $liczbaElita ?>
                    Najlepsze okrążenie zawodów: <?php echo $najlepszeZawodyFormat ?>
                    Okrążeń łącznie: <?php echo $liczbaOkrazen ?>
                </td>
            </tr>
        </tfoot>
    </table>
</body>

</html>