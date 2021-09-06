<!doctype html>
<html>
    <head>
        
    </head>
    <body>
        <!--TODO search bar -->
        <?php
        if (isset($clanek_id)) {
            $conn = openDB();
            $slqs = $conn->prepare("SELECT clanek_url, clanek_nazev FROM clanky WHERE clanek_id = ?");
            $sqls->bind_param('i', $clanek_id);
            $sqls->execute();
            $sqls->bind_result($url, $nazev);
            if ($sqls->fetch()) {
                $project = fopen ($url, 'r');
                if (!$project) {
                    echo ("Soubor nelze otevřit.");
                } else {
                    echo ("<h1>" . $nazev . "</h1>");
                }
                echo fread ($project, filesize($url));
                fclose($project);
            } else {
                echo("Zadany članek ne existuje.");
            }
            $sqls->close();
            $conn->close();
        } else {
            echo ("<h2>Seznam članku<h2>");
        }
        ?>
        <table>
            <?php
            // if clanek does´t choise
                if (!isset($clanek_id)) {
                    $conn = openDB();
                    $sqls = $conn->prepare("SELECT * FROM clanky");
                    //$sqls->bind_param('s', $clanek);
                    $sqls->execute();
                    $sqls->bind_result($clanek_id, $clanek_uzivatel, $clanek_nazev, $clanek_url);
                    $sqls->store_results();
                    if ($sqls->num_row == NULL) {
                        echo ("V databaze nejsou žadné članky.");
                    } else {
                        echo ("<tr><th>Članek</th><th>autor</th></tr>");
                        while($sqls->ftech()) {
                            echo ('<tr><th><a href="index.php?clanek_id='. $clanek_id '">'. $clanek_nazev . 
                            '</a></th><th>' . $clanek_uzivatel . '</th></tr>');
                        }
                    }
                }
            ?>
        </table>
    </body>
</html>