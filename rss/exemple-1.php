<?php header('Content-Type: text/html; charset=utf-8'); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>fil rss : exemple de lecture</title>
		<style type="text/css" media="screen"><!--
.cadre  { float: left; margin-right: auto; margin-left: auto; padding: 3px; width: 100%;list-style: none; }
--></style>
	</head>
<body>
<!--<p><a href="/"> Accueil</a></p>
		<p><b>Exemple-1</b></p>-->
<?php
// ---------------------------------------------------------
// rss_read - classe pour lire des fichiers RSS/RDF/Atom
// Dominique WOJYLAC <http://wojylac.free.fr>
// Distribue sous GNU General Public License.
// 
// Version 2.1d
// ---------------------------------------------------------

// la classe rss_read
include 'rss_read.inc.php';
// fonctions utiles pour affichage
include 'utils.php';

// creer l'instance
$rss = new rss_read();

// caracteres parasites pouvant etre contenus dans le fils rss et a remplacer par d'autres
// doit etre invoque avant parsefile
// cette fonction est optionnelle et ne doit Ãtre utilisee que pour certains fils.
$avant = array('&#','&bull;');
$apres = array('|@|','-');
$rss -> to_replace_with($avant, $apres);

// parser le fichier news avec eventuellement un nbre max
// parser = mettre toutes les infos dans les tableaux associatifs reuperes plus bas

// quelques fils divers, choix non exhaustif
$flux = 'http://www.who.int/feeds/entity/mediacentre/news/fr/rss.xml';
// $flux = 'http://www.annonay.org/agenda.rss';
// $flux = 'http://rezo.net/backend/portail';
// $flux = 'http://www.macbidouille.com/macbidouille.rss';
// $flux = 'http://www.zdnet.fr/feeds/rss/actualites/';
// format atom
// $flux = 'http://ledroitpourlajustice.blogspirit.com/atom.xml';
// $flux = 'http://www.cinesoumoud.net/spip.php?page=backend';
// $flux = 'http://www.clubic.com/xml/news.xml';
// $flux = 'http://www.monde-diplomatique.fr/rss/carnet/';


$res = $rss -> parsefile($flux);
//$res = $rss -> curl_parsefile($flux);

if ($res === false) {
	echo $rss -> erreur, '<br />';
	exit;
}

// recuperation de l'encodage du fil iso ou utf
// vide si celui-ci n'est pas indique
$encode = $rss -> get_encoding();

if ($encode === false) {
	echo $rss -> erreur, '<br />';
	exit;
}

// on affiche les infos dans un cadre centre mis en forme par le style interne .cadre
echo '<marquee DIRECTION="up" SCROLLDELAY="200" ><div class="cadre">';

// informations fil, encodage, last_modified
$date_modif = $rss -> get_last_modified($flux, false);
// $date_modif = $rss -> curl_get_last_modified($flux, false);

echo '<p><i>Bomoyi ', '<br/>Flux : ',$flux, '<br />Encodage : ', $encode, '<br /> Date modification : ', $date_modif, '</i></p>';
// recuperation des donnees sur le channel
$channel = $rss -> get_channel();

echo '<p align="center"><b><a href="', $channel['link'],'">', clean_text($channel['title'],$encode), '</a><br />', clean_text($channel['description'],$encode), '</b></p>';

if ($rss -> exist_image()) {
	$image = $rss -> get_image();
	$img =  '<div align="center">';
	if (!empty($image['link'])) {
		$img .= '<a href="'.$image['link'].'">';
	} else {
		$img .= '<a href="'.$channel['link'].'">';
	}
	$img .= '<img src="'.$image['url'].'"';
	
	if (!empty($image['width'])) {
		$img .= ' width="'.$image['width'].'"';
	}
	
	if (!empty($image['height'])) {
		$img .= ' height="'.$image['height'].'"';
	}
	
	if (!empty($image['title'])) {
		$title = clean_text($image['title'], $encode);
	} else {
		$title = clean_text($channel['title'], $encode);
	} 
	
	$img .= 'border="0" alt="'.$title.'"></a></div>';
	echo $img."\n";
}


// nombre d'items     
$nbnews = $rss -> get_num_items();

// recup array des donnees
$items = $rss -> get_items();

echo '<dl>';
for($i = 0; $i < $nbnews; $i++) {
	echo '<dt><b>.:: <a href="',$items[$i]['link'],'" target="_blank" rel="nofollow">',clean_text($items[$i]['title'], $encode),'</a> ::.</b></dt>',"\n";
	echo '<dd><ul>';
	if (!empty($items[$i]['pubdate'])) {
		echo '<li>Date : ',clean_date($items[$i]['pubdate'], $encode),'</li>';
	}
	if (!empty($items[$i]['modified'])) {
		echo '<li>Modification : ',clean_date($items[$i]['modified'], 'iso-8859-1'),'</li>';
	}
	if (!empty($items[$i]['author'])) {
		echo '<li>Auteur : ',clean_text($items[$i]['author'], $encode),'</li>';
	}
	if (!empty($items[$i]['category'])) {
		echo '<li>Categorie : ',clean_text($items[$i]['category'], $encode),'</li>';
	}
	echo '</ul></dd>';
	if (!empty($items[$i]['description'])) {
		echo '<dd>', clean_text($items[$i]['description'], $encode), '</dd>';
	}
	if (!empty($items[$i]['enclosure'])) {
		echo '<dd>Enclosure : <a href="',$items[$i]['enclosure']['url'],'" target="_blank" rel="nofollow">',$items[$i]['enclosure']['url'],'</a> (', $items[$i]['enclosure']['type'],')</dd>' ;
	}
}
echo '</dl>';

// formulaire si textinput existe
if($rss -> exist_textinput()) {
	$textinput = $rss -> get_textinput();
		echo '<form  method="get" action="',$textinput['link'],'">',
		$textinput['description'],'&nbsp;
		<input type="text" name="',$textinput['name'],
		'"size="10" maxlength="10">&nbsp;<input type="submit" name="Submit" 
		value="',$textinput['title'],'">
		</form>';
}	      

echo '</div></marquee>';
?>
</body>
</html>