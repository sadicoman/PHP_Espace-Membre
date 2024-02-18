<?php
session_start();

require("src/connection.php");

if (!empty($_POST['pseudo']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_confirm'])) {

	// VARIABLE

	$pseudo       = $_POST['pseudo'];
	$email        = $_POST['email'];
	$password     = $_POST['password'];
	$pass_confirm = $_POST['password_confirm'];

	// TEST SI PASSWORD = PASSWORD CONFIRM

	if ($password != $pass_confirm) {
		header('Location: index.php?error=1&pass=1');
		exit();
	}

	// TEST SI EMAIL UTILISE
	$req = $db->prepare("SELECT count(*) as numberEmail FROM users WHERE email = ?");
	$req->execute(array($email));

	while ($email_verification = $req->fetch()) {
		if ($email_verification['numberEmail'] != 0) {
			header('location: index.php?error=1&email=1');
			exit();
		}
	}

	// HASH
	$secret = sha1($email) . time();
	$secret = sha1($secret) . time() . time();

	// CRYPTAGE DU PASSWORD
	$password = "aq1" . sha1($password . "1254") . "25";

	// ENVOI DE LA REQUETE
	$req = $db->prepare("INSERT INTO users(pseudo, email, password, secret) VALUES(?,?,?,?)");
	$value = $req->execute(array($pseudo, $email, $password, $secret));

	header('location: index.php?success=1');
	exit();
}
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>PHP et MySQL : la formation ULTIME</title>
	<link rel="icon" type="image/png" href="/logo.png">
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
	<header>
		<h1>Inscription</h1>
	</header>

	<div class="container">

		<?php
		if (!isset($_SESSION['connect'])) { ?>

			<p id="info">Bienvenue sur mon site, pour en voir plus, inscrivez-vous.</p>

			<?php

			if (isset($_GET['error'])) {

				if (isset($_GET['pass'])) {
					echo '<p id="error">Les mots de passe ne correspondent pas.</p>';
				} else if (isset($_GET['email'])) {
					echo '<p id="error">Cette adresse email est déjà utilisée.</p>';
				}
			} else if (isset($_GET['success'])) {
				echo '<p id="success">Inscription prise correctement en compte.</p>';
				echo '<script>
            setTimeout(function(){
                window.location.href = "connection.php";
            }, 3000);
          </script>';
			}

			?>

			<div id="login" class="w-64 bg-indigo-50 rounded shadow flex flex-col justify-between p-3">
				<form class="text-indigo-500" method="POST" action="index.php">
					<fieldset class="border-4 border-dotted border-indigo-500 p-5">
						<legend class="px-2 italic -mx-2">Bienvenue !</legend>
						<label class="text-xs font-bold after:content-['*'] after:text-red-400" for="pseudo">Pseudo</label>
						<input class="w-full p-2 mb-2 mt-1 outline-none ring-none focus:ring-2 focus:ring-indigo-500" type="text" name="pseudo" placeholder="Ex : Nicolas" required>

						<label class="text-xs font-bold after:content-['*'] after:text-red-400" for="email">Email</label>
						<input class="w-full p-2 mb-2 mt-1 outline-none ring-none focus:ring-2 focus:ring-indigo-500" type="email" name="email" placeholder="Ex : example@google.com" required>

						<label class="text-xs font-bold after:content-['*'] after:text-red-400" for="password">Mot de passe</label>
						<input class="w-full p-2 mb-2 mt-1 outline-none ring-none focus:ring-2 focus:ring-indigo-500" type="password" name="password" placeholder="Ex : ********" required>

						<label class="text-xs font-bold after:content-['*'] after:text-red-400" for="password_confirm">Retaper mot de passe</label>
						<input class="w-full p-2 mb-2 mt-1 outline-none ring-none focus:ring-2 focus:ring-indigo-500" type="password" name="password_confirm" placeholder="Ex : ********" required>

						<button class="w-full rounded bg-indigo-500 text-indigo-50 p-2 text-center font-bold hover:bg-indigo-400" type='submit'>Inscription</button>

					</fieldset>
				</form>

			</div>
			<a class="block text-right text-xs text-indigo-500 text-right mb-4" href="connection.php">Connectez-vous.</a>
		<?php } else { ?>
			<div class="container__connecter">
				<div class="connecter__btn">
					<a class="w-full rounded bg-indigo-500 text-indigo-50 p-2 text-center font-bold hover:bg-indigo-400" href="disconnection.php">Déconnexion</a>
				</div>
				<h2 class="title--niveau2 " id="info">
					Bonjour, <span class="users"><?= $_SESSION['pseudo'] ?></span><br>
				</h2>
			</div>
		<?php } ?>

	</div>
</body>

</html>