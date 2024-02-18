<?php
session_start();

if (isset($_SESSION['connect'])) {
	header('location: index.php');
	exit();
}

require('src/connection.php');

// CONNEXION
if (!empty($_POST['email']) && !empty($_POST['password'])) {

	// VARIABLES
	$email 		= $_POST['email'];
	$password 	= $_POST['password'];
	$error		= 1;

	// CRYPTER LE PASSWORD
	$password = "aq1" . sha1($password . "1254") . "25";

	echo $password;

	$req = $db->prepare('SELECT * FROM users WHERE email = ?');
	$req->execute(array($email));

	while ($user = $req->fetch()) {

		if ($password == $user['password']) {
			$error = 0;
			$_SESSION['connect'] = 1;
			$_SESSION['pseudo']	 = $user['pseudo'];

			if (isset($_POST['connect'])) {
				setcookie('log', $user['secret'], time() + 365 * 24 * 3600, '/', null, false, true);
			}

			header('location: connection.php?success=1');
			exit();
		}
	}

	if ($error == 1) {
		header('location: connection.php?error=1');
		exit();
	}
}

?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Connexion</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
	<header>
		<h1>Connexion</h1>
	</header>

	<div class="container">
		<p id="info">Bienvenue sur mon site,si vous n'êtes pas inscrit, </p>

		<?php
		if (isset($_GET['error'])) {
			echo '<p id="error">Nous ne pouvons pas vous authentifier.</p>';
		} else if (isset($_GET['success'])) {
			echo '<p id="success">Vous êtes maintenant connecté.</p>';
		}
		?>

		<div id="form" class="w-64 bg-indigo-50 rounded shadow flex flex-col justify-between p-3">
			<form class="text-indigo-500" method="POST" action="connection.php">
				<fieldset class="border-4 border-dotted border-indigo-500 p-5">
					<legend class="px-2 italic -mx-2">Bienvenue !</legend>

					<label class="text-xs font-bold after:content-['*'] after:text-red-400" for="email">Email</label>
					<input class="w-full p-2 mb-2 mt-1 outline-none ring-none focus:ring-2 focus:ring-indigo-500" type="email" name="email" placeholder="Ex : example@google.com" required>

					<label class="text-xs font-bold after:content-['*'] after:text-red-400" for="password">Mot de passe</label>
					<input class="w-full p-2 mb-2 mt-1 outline-none ring-none focus:ring-2 focus:ring-indigo-500" type="password" name="password" placeholder="Ex : ********" required>

					<div class="checkbox">
						<input class="nw rx adp afv ayg bnp" type="checkbox" name="connect" checked>
						<p>Connexion automatique</p>
					</div>
					<button class="w-full rounded bg-indigo-500 text-indigo-50 p-2 text-center font-bold hover:bg-indigo-400" type='submit'>Connexion</button>

				</fieldset>
			</form>
		</div>
		<a href="index.php">inscrivez-vous.</a>
	</div>
</body>

</html>