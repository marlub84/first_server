<!doctype html>
<html lang="cs">
	<head>
	<link rel="stylesheet" href="../styl.css" type="text/css">
	</head>
	<body>
		<h1>Příhlášení</h1>
		<?php
			echo $zprava;
		?>
		<form method="post">
		<ul class="login">
			<li><p>Uživatel</p></li>
			<li><input name="user" type="text" /></li>
		</ul>
		<ul class="login">
			<li><p>Heslo</p>
			<li><input type="password" name="password" /></li>
			<br>
			<li><input type="submit" name="login" value="Příhlasit" /></li>
		</ul>
		<br>
		<div class="lognote">
			<p><a href="index.php?stranka=resetheslo">Zapomněl jsi heslo?</a></p>
		</div>
		</form>
	</body>
<html>

