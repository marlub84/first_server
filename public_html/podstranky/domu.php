<!doctype html>
<html lang="cs">
	<head>

	</head>
	<body>
		<h2> Vitej na serveru s projekty o mechatronice</h2>
		<br>
		<br>
		<div class="row">
			<div class="column side">

			</div>
			<div class="column middle">
				<h3>Úvod</h3>
				<?php
					$uvod = fopen("podstranky/uvod.txt", "r") or die("Unable to open file 'uvod'");
					echo fread($uvod, filesize("podstranky/uvod.txt"));
					fclose($uvod);
				?>
			</div>
			<div class="column side">

			</div>
		</div>
	</body>
</html>
