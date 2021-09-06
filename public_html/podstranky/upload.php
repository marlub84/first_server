<?php
$target_dir = "/home/web/" . $_SESSION['uzivatel_jmeno'] . "/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
if (isset($_POST['submit'])) {
	if (file_exists($target_file)) {
		echo "File exists\n" . $target_file;
		$uploadOk = 0;
	}
	if (!$uploadOk) {
		echo "\nUpload souboru se nezdařil, soubor už existuje.";
	} else {
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			echo "Soubor " . basename($_FILES["fileToUpload"]["name"]) . " uspěšně nahran.";
		} else {
			echo "Nastala chyba při nahravani souboru " . $_FILES["fileToUpload"]["name"];
		}
	}
}
?>

<!DOCTYPE HTML>

<HTML LANG="CS">
	<HEAD>
		<META CHARSET="UTF-8" />
		<link rel="stylesheet" href="../styl.css" type="text/css" />
	</HEAD>	

	<BODY>
	<H2>Nahravani souboru</FONT></H2>
	<hr width="80%" noshade size=4>
	<BR>
	<BR>
	<form  method="post" enctype="multipart/form-data">
		<div class="odstavec">
			<input type="file" name="fileToUpload" id="fileToUpload">
			<input type="submit" name="submit" value="Odeslat soubor">
		</div>
	</form>
	<br>
	<br>
	</BODY>
</HTML>
