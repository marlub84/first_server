<!doctype html>
<html lang="cs">
	<head>
	<meta http-equiv="Content-Type" content="multi-part/mixed">

	</head>
	<body>
		<div class="row">
			<div class="column side">

			</div>
			<div class="column middle">
				<h2>O mně</h2>
				<br>
				<?php
					$file = "podstranky/omne.txt";
					if(!$pfile = fopen($file, "r")){
						echo "Can't open file";
					}
//					echo "<pre>";
					echo fread($pfile, filesize($file));
//					echo "<pre>";
				?>
			</div>
			<div class="column side">

			</div>
		</div>
	</body>
</html>

