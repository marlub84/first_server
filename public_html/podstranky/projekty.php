<?php
//include("../index.php");

function file_upload($folder){
    // save uploaded files
    $count_file = count($_FILES['cl_files']['name']);

    for ($i = 0; $i < $count_file; $i++) {
        $tmps = $_FILES['cl_files']['tmp_name'][$i];
        $tmp = $folder . "/" . $_FILES['cl_files']['name'][$i];
    	move_uploaded_file($tmps, $tmp);
		echo "Soubor " . $tmp . " nahran uspěšně";
    }

}

///////////////////////////////////////////////////////////////////////////////

    if (isset($_GET['clanek_id'])) {
        // query database to display project/s
        $cl_id = $_GET['clanek_id'];
        if ($cl_id > 0){
            $conn = openDB();
            $sql = $conn->prepare("SELECT clanek_titul, clanek_url, jmeno FROM clanky INNER JOIN uzivatele ON clanky.uzivatel_id=uzivatele.uzivatel_id WHERE clanek_id = ?");
            $sql->bind_param("i", $cl_id);
            $sql->execute();
            $sql->bind_result($cl_titul, $cl_url, $cl_autor);
            $sql->store_result();
            $sql->fetch();
//echo "open " . $cl_id; 
            $file = fopen($cl_url, "r");
            if (!$file) {
                echo "Projekt nelze otevřit!";
            }
            $cl_content = fread($file, filesize($cl_url));
            fclose($file);
			//echo $cl_url;
            $folder_count = mb_strlen($cl_url);
			// this is use too in update projekt !!!
            $folder = mb_strcut($cl_url, 0, ($folder_count - 3), "UTF-8");

            $sql->close();
            $conn->close();
        }
    }
    if (isset($_SESSION['uzivatel_jmeno'])) {
        if (isset($_POST['save'])){
            $cl_id = $_POST['clanek_id'];
            //$cl_nazev = $_POST['cl_nazev'];
            $cl_titul = $_POST['clanek_titul'];
            $cl_content = $_POST['cl_content'];

            if ($cl_id == 0) {
                // create new project
                // generate name for project, remove white space and tab
                $gen_trim = trim($cl_titul, " \t");
                $gen_low = strtolower($gen_trim);
                //create folder for project
                $folder = "projekty/" . $_SESSION['uzivatel_jmeno'] . "/" . $gen_low;
                $file_cl = "projekty/" . $_SESSION['uzivatel_jmeno'] . "/" . $gen_low . ".html";
                if (!mkdir($folder)) {
                    echo "Nepovedlo se vytvořit soubor projektu";
                }
                $file_create = fopen($file_cl, "w");
                if (!$file_create){
                    echo "Chyba souboru ($file_create)";
                }
                if (!fwrite($file_create, $cl_content)) {
                    echo "Chyba zapisu do souboru ($file_create)";
                }
                fclose($file_create);
                // copy uploaded files
                file_upload($folder);

                // write data to database
                $conn = openDB();
				// get user id
				$sql = $conn->prepare("SELECT uzivatel_id FROM uzivatele WHERE jmeno = ?");
				$sql->bind_param("s", $_SESSION['uzivatel_jmeno']);
				$sql->execute();
				$sql->bind_result($uz_id);
				$sql->store_result();
				$sql->fetch();
				$sql->reset();

                $sql = $conn->prepare("INSERT INTO clanky(clanek_id, uzivatel_id, clanek_titul, clanek_url) VALUE (?, ?, ?, ?)");
                $cl_url = $file_cl;

                $sql->bind_param("iiss", $cl_id, $uz_id, $cl_titul, $cl_url);
                $sql->execute();

                $sql->close();
                $conn->close();

				header("Location: index.php?stranka=projekty");
				exit();
            } else {
                // update current project
                $conn = openDB();
                $sql = $conn->prepare("SELECT clanek_url FROM clanky WHERE clanek_id = ?");
                $sql->bind_param("i", $cl_id);
                $sql->bind_result($cl_url);
                $sql->execute();
                $sql->store_result();
                $sql->fetch();

                $file_cl = $cl_url;
                // count string length and remove last text '.html'
                $folder_count = mb_strlen($file_cl);
                $folder = mb_strcut($file_cl, 0, ($folder_count - 3), "UTF-8");

                // open project to update data
                $file_update = fopen($file_cl, "w");
                if (!$file_update) {
                    echo "Soubor nelze otevřit";
                }
                if (!fwrite($file_update, $cl_content)) {
                    echo "Soubor nejde uložit";
                }
                fclose($file_update);
                // update uploaded files
                file_upload($folder);

				header("Location: index.php?stranka=projekty");
				exit();
            }
        }else if (isset($_GET['delete']) && isset($_GET['clanek_id'])) {
            // delete project
			if (is_dir($folder)) {
				// check if folder is empty
				$files = scandir($folder);
				$files_count = count($files);
				if ($files_count > 2) {
				// remove files
					$j = 2;
					for ($j; $j < $files_count; $j++) {
						unlink($folder . "/" . $files[$j]);
					}
				}
				rmdir($folder);
			}

			unlink($cl_url);
			// remove data from database
			$conn = openDB();
			$sql = $conn->prepare("DELETE FROM clanky WHERE clanek_id = ?");
			$sql->bind_param("i", $cl_id);
			$sql->execute();
			if ($sql->sqlstate != '00000') {
				echo "Error remove row : " . $sql->sqlstate;
			}
			$sql->close();
			$conn->close();
			header("Location: index.php?stranka=projekty");
			exit();
		}
    }
?>
<!doctype html>
<html lang="cs">
    <head>
		<script src="https://cdn.tiny.cloud/1/1vc3iam5fg0v0nl5kbo2900qk5xyzj9ykg19uewbp3mvdfwl/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
		<script src="podstranky/init-tinymce.js"></script>
    </head>
    <body>
    <div class="row">
        <div class="column side">
        <?php
            if (isset($cl_id)) {
                echo "<ul>";
                    echo "<li>Seznam souboru</li><br />";
					if (isset($_GET['edit'])){
						echo '<form method="post" enctype="multipart/form-data" />';
						echo '<li>';
						echo '<input type="file" name="cl_files[]" multiple/>';
						echo '</li>';
					}
					if ($cl_id > 0){
						if (is_dir($folder)) {
	                    	$ls_files = scandir($folder);
	                    	$j = 2;
	                    	for ($j; $j < count($ls_files); $j++) {
	                        	echo "<li>";
	                        	echo '<a href=' . $folder . '/' . $ls_files[$j] . '>' . $ls_files[$j] .'</a>';

                        		echo "</li>";
                    		}
						}
					}
                echo "</ul>";
            }

        ?>
        </div>

        <div class="column middle">
            <?php
                if (isset($cl_id)){
                // if edit than read editor
                if (isset($_SESSION['uzivatel_jmeno']) && isset($_GET['edit'])) {
                    // user is logined and edit was press
                    // project edit or create new
                    if ($cl_id <= 0) {
                        // create new project
                        $cl_id = 0;
                        $cl_name = "";
                        $cl_content = "";
                        $uz_jmeno = $_SESSION['uzivatel_jmeno'];
                    }
//                    echo '<form method="post">';
                    echo '<input type="hidden" name="clanek_id" value="' . $cl_id . '"/><br />';
                    echo '<input type="text" name="clanek_titul" value="' . $cl_titul . '"/><br />Titulek<br />';
//                    echo '<input type="file" name="cl_files" multiple/><br />Přídat soubory<br />';
                    echo '<br>';
                    echo '<textarea id="cl_content" name="cl_content">' . $cl_content .'</textarea>';
                    echo '<input type="submit" name="save" />';
                } else {
                    // show project
                    echo $cl_content;
                }
            } else {
                // show all project
                $conn = openDB();
                $sql = $conn->prepare("SELECT clanek_id, clanek_titul, clanek_url, jmeno FROM clanky INNER JOIN uzivatele ON clanky.uzivatel_id=uzivatele.uzivatele_id");
                $sql->execute();
                $sql->bind_result($cl_id, $cl_titul, $cl_url, $uz_jmeno);
                $sql->store_result();
                // check if exist any project 
                if ($sql->num_rows > 0){
                    echo "<h1>Seznam projektu</h1><br />";
                    echo "<table>";
                    echo "<tr><th>Nazev projektu</th><th>Autor</th><tr>";
                    while ($sql->fetch()) {
                        echo "<tr>";
                        echo '<td><a href="index.php?stranka=projekty&clanek_id=' . $cl_id . '"/>' . $cl_titul . '</td><td>' . $uz_jmeno . '</td>';
                        echo "</tr>";
                    }
                    echo "</table>";

                } else {
                    echo "<h1>Nejsou žádné projekty</h1>";
                }
                $sql->close();
                $conn->close();
            }

            ?>
        </div>

        <div class="column side">
        <?php
            if (isset($_SESSION['uzivatel_jmeno'])) {
                echo "<ul>";
                    echo "<li>";
                        if ($_SESSION['uzivatel_jmeno'] == $cl_autor) {
                            echo '<a href="index.php?stranka=projekty&edit=1&clanek_id=' . $cl_id .'" >';
                            echo "Úpravit članek</a>";
                        } else {
                            echo "Úpravit članek";
                        }
                    echo "</li>";
                    echo '<li><a href="index.php?stranka=projekty&edit=1&clanek_id=0" >';
                        echo "Vložit nový članek</a>";
                    echo "</li>";
                    echo "<li>";
                        if ($_SESSION['uzivatel_jmeno'] == $cl_autor) {
                            echo '<a href="index.php?stranka=projekty&delete=1&clanek_id=' . $cl_id . '" />';
                            echo "Smazat članek</a>";
                        } else {
                            echo "Smazat članek";
                        }
                    echo "</li>";

                echo "</ul>";
            }
        ?>
        </div>
    </div>
    <footer>

    </footer>
    </body>
</html>
