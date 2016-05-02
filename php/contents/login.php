<?php

if ($p == 'logout')
{
	unset($_SESSION['id']);
	unset($_SESSION['name']);
	unset($_SESSION['remote_addr']);
	session_destroy();
	header('Refresh: 0; '.URL);
	exit;
}

$error = '';

if ($_POST)
{
	$mail = trim($_POST['email']);
	$pass = trim($_POST['password']);
	
	$user = $db->getUserLogin($mail);
	if ($user == null)
	{
		$error = '<div class="form-group">
			<div class="alert alert-danger-outline">
				<strong><span class="fa fa-bolt"></span> Fehler:</strong> Benutzer nicht gefunden.
			</div>
		</div>';
	}
	else if ($_POST['action'] == 'lost')
	{
		$user = $db->getUser($user->id);
		$pw = substr(hash_hmac('md5', $user->email, time()), 0, 8);
		$user->password = $pw;
		
		$db->updateUser($user);
		
		$text = 'Hallo '.$user->name.',

Dein Passwort wurde zurückgesetzt.
Dein neues Passwort lautet: '.$pw.'

Freundliche Grüße
  Der Admin';
		
		mail($email, '[SSD] Passwort vergessen', $text, "From: no-reply@".$_SERVER['HTTP_HOST'], '-fno-reply@'.$_SERVER['HTTP_HOST']);
		
		$error = '<div class="form-group">
			<div class="alert alert-success-outline">
				<span class="fa fa-check"></span> Neues Passwort versendet.
			</div>
		</div>';
	}
	else if (hash_equals($user->password, crypt($pass, $user->password)))
	{
		$_SESSION['id'] = $user->id;
		$_SESSION['name'] = $user->name;
		$_SESSION['permissions'] = $user->permissions;
		$_SESSION['remote_addr'] = $_SERVER['REMOTE_ADDR'];
		header('Refresh: 0; '.URL);
		exit;
	}
	else
	{
		$error = '<div class="form-group">
			<div class="alert alert-danger-outline">
				<strong>Fehler:</strong> Passwort nicht gültig.
			</div>
		</div>';
	}
}

$content = '
<div class="col-sm-4 col-sm-offset-4">
	<form method="post" action="'.URL.'/?p=login">
		'.$error.'
		
		<div class="form-group">
			<label>E-Mail Adresse</label>
			<input type="text" class="form-control" name="email">
		</div>
		
		<div class="form-group">
			<label>Passwort</label>
			<input type="password" class="form-control" name="password">
		</div>
		
		<div class="form-group">
			<button type="submit" class="btn btn-bs-outline" style="width: 100%" name="action" value="go">Login</button>
		</div>
		
		<div class="form-group">
			<button type="submit" class="btn btn-warning-outline" style="width: 100%" name="action" value="lost">Passwort vergessen</button>
		</div>
		
	</form>
</div>
';

$page->setContent($content);

?>