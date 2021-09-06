<?php
mb_internal_encoding("UTF-8");
	$hlaska = '';
	if ($_POST)
	{
		if (isset($_POST['jmeno']) && $_POST['jmeno'] &&
			isset($_POST['email']) && $_POST['email'] &&
			isset($_POST['zprava']) && $_POST['zprava'] &&
			isset($_POST['rok']) && $_POST['rok'] == date('Y'))
			{
				$hlavicka = "From:" . "<" . $_POST['email'] . ">";
				$hlavicka .= "\nMIME-Version: 1.0\n";
				$hlavicka .= "Content-Type: text/html; charset=\"utf-8\"\n";
				$hlavicka .= "Content-Transfer-Encoding: BASE64\n";
				$adresa = 'mario@mappi.cz';
				$predmet = "Nová zpráva z mailformu";
				$uspech = mb_send_mail($adresa, $predmet, $_POST['zprava'], $hlavicka);
				if ($uspech)
				{
					$hlaska = 'Email byl úspěšně odeslán, brzy se vám ozvu.:';
					$_POST['jmeno'] = '';
					$_POST['email'] = '';
					$_POST['zprava'] = '';
				}
				else
					$hlaska = 'Email se nepodařilo odeslat. Zkontrolujte adresu.';
			}
			else
				$hlaska = 'Formulář není spavně vyplněný!';
	}

	if ($hlaska)
		echo('<p>' . htmlspecialchars($hlaska) . '</p>');

	$jmeno = (isset($_POST['jmeno'])) ? $_POST['jmeno'] : '';
	$email = (isset($_POST['email'])) ? $_POST['email'] : '';
	$zprava = (isset($_POST['zprava'])) ? $_POST['zprava'] : '';
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" href="../styl.css" type="text/css"/>
	</head>
	<body>
		<br>
		<br>
		<h3 style="margin-left: 30px"> Můžete mě kontaktovat pomocí formuláře níže.</h3>

		<form method="POST">
			<div class="odstavec">
			<table>
				<tr>
					<td>Vaše jméno</td>
					<td><input name="jmeno" type="text" value="<?= htmlspecialchars($jmeno) ?>"/></td>
				</tr>
				<tr>
					<td>Váš email</td>
					<td><input name="email" type="email" value="<?= htmlspecialchars($email) ?>"/></td>
				</tr>
				<tr>
					<td>Aktuální rok</td>
					<td><input name="rok" type="number" /></td>
				</tr>
			</table>
			<textarea name="zprava"><?= htmlspecialchars($zprava) ?></textarea>
			<br />

			<input type="submit" value="Odeslat" />
			</div>
		</form>
		<br>
		<br>
	</body>
</html>

