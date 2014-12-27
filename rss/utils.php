<?php
// fonction de nettoyage du texte recupere

function clean_text($text, $encodage = '') {
	if ($encodage != 'utf-8') {
		$text = utf8_encode($text);
	}
	$avant = array(
		'&lt;',
		'&gt;',
		'&quot;',
		'&amp;',
		'|@|'
		);
	$apres = array(
		'<',
		'>',
		'"',
		'&',
		'&#'
		);
	$text = str_replace($avant, $apres, $text);
	/* pour supprimer les images */
	/* $text = preg_replace("`<img.*?>`si",'',$text); */
	return $text;
}

// nettoyage plus avancé
function nettoyage ($text) {
 	$avant = array ('`<div.*?>`si','`</div>`si','`<font.*?>`si','`</font>`si');
 	$apres = '';
 	$text = preg_replace($avant, $apres, $text);
 	return $text;
}


// traite les dates au format
// '2004-06-07T11:13:19+01:00'
// ou 'Sun, 26 Jun 2005 10:16:26 GMT'
function clean_date($date, $encodage='') 
{
	// si 'AAAA-MM-JJTHH:MM:SS+00:00' ou 'AAAA-MM-JJTHH:MM:SSZ'
    if (ereg("^[0-9]",$date) and  ereg("(([[:digit:]]|-)*)T(([[:digit:]]|:)*)[^[:digit:]].*",$date,$temp)) {
		$date = $temp[1].' '.$temp[3];
	}
	// affichage au format francais
	setlocale(LC_TIME, 'fr_FR');
	// regler ici le format d'affichage des dates
	$date = strftime("%A %d %B %Y : %Hh", strtotime($date));
	
	if ($encodage != 'utf-8') {
		$date = utf8_encode($date);
	}
	
	return $date;	
}

?>