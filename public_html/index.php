<?php
session_start();
function openDB()
{
	$servername = "localhost";
	$username = "phpmyadmin";
	$password = "mydb";
	$databaze = "databaze_pro_web";
	// create connection in MySQLi Object Oriented
	$conn = new mysqli($servername, $username, $password, $databaze);

	//Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	//echo "Connected successfuly";
	return $conn;
}

function add_user($conn, $jmeno, $primeni, $datum)
{
	$sql = "INSERT INTO uzivatele (jmeno, primeni, datum_narozeni) VALUE ('$jmeno', '$primeni', '$datum')";
	if ($conn->query($sql) == TRUE){
		echo "Přidan nový uřivatel";
	} else {
		echo "Chyba přídaní uživatele";
	}
	$conn->close();
}

if (isset($_SESSION['uzivatel_id'])) {
//	echo $_SESSION['uzivatel_id'] . " " . $_SESSION['uzivatel_jmeno'];
}

if (isset($_GET['logout'])) {
//	echo "logout";
	session_unset();
	session_destroy();
	header('Location: index.php');
	exit();
}

if (isset($_POST)){
	if (!isset($_SESSION['uzivatel_id'])) {
	if (isset($_POST['register'])) {
		// check password
		if ($_POST['password'] != $_POST['password_check']) {
			$message = "Hesla nesouhlasí";
		} else {
			// check if user does´t exist
			$conn = openDB();
			$sqlreg = $conn->prepare("SELECT jmeno FROM uzivatele WHERE jmeno = ?");
			if (!$sqlreg) {
				trigger_error("Chyba: " . $conn->error, E_USER_ERROR);
			}
			// TODO check email format and if email exist

			$sqlreg->bind_param('s', $_POST['reg_user']);
			$sqlreg->execute();
			$sqlreg->bind_result($exist);
			$sqlreg->fetch();
			if ($exist == $_POST['register']) {
				$zprava = "Uživatel " . $_POST['register'] . " už existuje";
			} else {
				// registrtion
				$heslo = password_hash($_POST['password'], PASSWORD_DEFAULT);
				$sqlreg = $conn->prepare("INSERT INTO uzivatele(jmeno, email, heslo) VALUE (?, ?, ?)");
				$sqlreg->bind_param('sss', $_POST['register'], $_POST['email'], $heslo);
				$sqlreg->execute();
				$_SESSION['uzivatel_id'] = $sqlreg->insert_id;
				$_SESSION['uzivatel_jmeno'] = $_POST['register'];
//				echo $cesta = "/home/web/" . $_SESSION['uzivatel_jmeno'];
				if (!mkdir($cesta)){
					echo("Can`t create folder");
				}
//				echo $E_WORRNING;
				header('Location: index.php?stranka=domu');
				exit();
			}
		}
	}else if (isset($_POST['login'])) {
		$conn = openDB();
		$sqls = $conn->prepare("SELECT uzivatel_id, jmeno, heslo FROM uzivatele WHERE jmeno = ?");

		if (!$sqls) {
			trigger_error('Error: ' . $conn->error, E_USER_ERROR);
		}
		$sqls->bind_param('s', $_POST['user']);
		$sqls->execute();
		$sqls->bind_result($id, $uzivatel, $user_password);
		$sqls->store_result();
		//echo $uzivatel;
		if ($sqls->fetch()) {
//			echo "verify";
			if (password_verify($_POST['password'], $user_password)) {
				// user exist and password is pass
				$_SESSION['uzivatel_id'] = $id;
				$_SESSION['uzivatel_jmeno'] = $uzivatel;
				header('Location: index.php?stranka=domu');
				exit();
			}else
				echo "bad password";
		} else {
			$zprava = "Neplatný uživatel nebo heslo";
		}
		$sqls->close();
		$conn->close();
	}
	// end login
	}
}

//$db = openDB();
//$jmeno = 'Mariusz';
//$primeni = 'Lubina';
//$datum = '1984.03.29';
//add_user($db, $jmeno, $primeni, $datum);
?>

<!DOCTYPE html>
<html lang="cs-cz">

	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="style/styl.css" type="text/css" />
		<link rel="stylesheet" href="style/webStyle.css" type="text/css" />
		<title>Server Mappi</title>
	</head>

	<body>
		<header>
			<div><h1>Server mappi - mechatronika projekty</h1></div>
			<nav>
				<ul class="topmenu">
					<li><a href="index.php?stranka=domu">Domů</a></li>
					<li><a href="index.php?stranka=omne">O mně</a></li>
					<li><a href="index.php?stranka=projekty">Projekty</a></li>
					<li><a href="index.php?stranka=napady">Různe nápady</a></li>
					<li><a href="index.php?stranka=kontakt">Kontakt</a></li>
					<?php
					if (isset($_SESSION['uzivatel_id'])) {
						echo ('<li><a href="index.php?stranka=upload">Nahrat soubor</a></li>');
					}else {
						echo ('<li style="float: right"><a href="index.php?stranka=registrace">Registrace</a></li>');
					}
					if (!isset($_SESSION['uzivatel_id'])) {
						echo ('<li style="float: right"><a href="index.php?stranka=login">Příhlásit se</a></li>');
					} else {
						echo ('<li style="float: right"><a href="index.php?logout">Odhlasit se</a></li>');
					}
					?>
				</ul>
			</nav>
		</header>

		<article>
			<div>
				<header>

				</header>

				<section>
					<?php
						if (isset($_GET['stranka'])){
							$stranka = $_GET['stranka'];
						}
						else if (!isset($_GET['stranka'])){
							$stranka = 'domu';
						}
						else if (isset($_GET['clanek_id']))
							$stranka = 'domu';
						if (preg_match('/^[a-z0-9]+$/', $stranka))
						{
							$vlozeno = include('./podstranky/' . $stranka . '.php');
							if (!$vlozeno)
								echo ('<br><p>Podstranka nenalezena</p><br>');
						}
						else
							echo ('Neplatný parametr.');

					?>
				</section>

			</div>
		</article>


		<footer>
			<div class="footer">
				<p>Vytvořil &copy;Mariusz Lubina 2019</p>
			</div>
		</footer>
	</body>
</html>



