<?php

if (empty($_SESSION['id']))
{
	$content = '<div class="alert alert-danger-outline">
		<strong><span class="fa fa-exclamation-triangle"></span> Fehler:</strong> Nicht angemeldet.
	</div>';
	$page->setContent($content);
	return;
}

$page->addJS(URL.'/js/mail.js');

$user = $db->getUser($_SESSION['id']);
$users = $db->getUserList();

$notify = '';

$subject = '';
$message = '';

if ($_POST)
{
	$subject = trim($_POST['subject']);
	$message = trim($_POST['message']);
	
	if ($_POST['receiver'] == 'all')
	{
		$receiver = array();
		foreach ($users as $u)
			$receiver[] = $u->email;
	}
	else
	{
		$receiver = isset($_POST['selection']) ? $_POST['selection'] : array();
	}
	
	if (count($receiver) == 0)
	{
		$notify = '<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<div class="alert alert-danger-outline">
					<strong><span class="fa fa-bolt"></span> Fehler:</strong> Kein Empfänger ausgewählt.
				</div>
			</div>
		</div>';
	}
	else
	{
		foreach ($receiver as $to)
		{
			mail($to, $subject, $message, "From: ".$user->email, '-f'.$user->email);
		}
		
		$content = '<div class="alert alert-success-outline">
			<strong><span class="fa fa-check"></span></strong> E-Mails erfolgreich versendet.
		</div>';
		$page->setContent($content);
		return;
	}
}

$select_list = array();
foreach ($users as $u)
{
	$select_list[] = '
	<label class="checkbox-inline">
		<input type="checkbox" name="selection[]" value="'.$u->email.'"> '.$u->name.' '.(empty($u->class) ? '' : '('.$u->class.')').'
	</label>
	';
}

$content = '
<h1><span class="fa fa-envelope"></span> Rundmail schreiben</h1>
<form method="post" action="'.URL.'/?p=mail" class="form-horizontal">
	'.$notify.'

	<div class="form-group">
		<label class="control-label col-sm-2">Absender</label>
		<div class="col-sm-10 form-control-static">
			'.$user->name.' &lt;'.$user->email.'&gt;
		</div>
	</div>
	
	<div class="form-group">
		<label class="control-label col-sm-2">Empfänger</label>
		<div class="col-sm-10">
			<label class="radio-inline">
				<input type="radio" name="receiver" value="all" checked="checked"> Alle
			</label>
			<label class="radio-inline">
				<input type="radio" name="receiver" value="select"> Auswahl
			</label>
		</div>
	</div>
	
	<div class="form-group" id="receiver-select">
		<label class="control-label col-sm-2 required">Auswahl</label>
		<div class="col-sm-10">
			'.implode(PHP_EOL, $select_list).'
		</div>
	</div>
	
	<div class="form-group">
		<label class="control-label col-sm-2 required">Betreff</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" name="subject" value="'.$subject.'">
		</div>
	</div>
	
	<div class="form-group">
		<label class="control-label col-sm-2 required">Nachricht</label>
		<div class="col-sm-10">
			<textarea rows="4" cols="40" class="form-control" name="message">'.$message.'</textarea>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-8 col-xs-6">
			<button type="submit" class="btn btn-bs-outline"><span class="fa fa-paper-plane"></span> Senden</button>
		</div>
		<div class="col-sm-2 col-xs-6 text-right form-control-static">
			<span class="required"></span> Pflichtfeld
		</div>
	</div>
</form>
';

$page->setContent($content);

?>