<?php
/*
** fichier parse_flux-1.php
** utilise par la page exemple-2.php
*/


// chemin relatif vers le dossier cache
// le repertoire "cache" doit etre autorise en ecriture
// ne pas oublier / a la fin
$dir_cache = 'cache/';
if (!is_dir($dir_cache)) {
	exit ('Repertoire cache "'.$dir_cache.'" inexistant !');
}
// le flux a afficher
// quelques fils divers, choix non exhaustif
// $flux = 'http://www.arteradio.com/servlet/RSSMakerServlet';
// $flux = 'http://www.annonay.org/agenda.rss';
// $flux = 'http://rezo.net/backend/portail';
// $flux = 'http://www.macbidouille.com/macbidouille.rss';
// $flux = 'http://www.zdnet.fr/feeds/rss/actualites/';
// format atom
// $flux = 'http://ledroitpourlajustice.blogspirit.com/atom.xml';
// $flux = 'http://www.cinesoumoud.net/spip.php?page=backend';
// $flux = 'http://www.clubic.com/xml/news.xml';
$flux = 'http://www.monde-diplomatique.fr/rss/carnet/';


// le nom du fichier cache est fabriqué à partir de l'url du flux
$file_cache = $dir_cache. md5($flux).'.html';

include 'utils.php';
include 'rss_read.inc.php';

$rss = new rss_read();

// voir la doc pour cette fonction
$date_modif = $rss -> get_last_modified($flux);

if ($date_modif > 0) {
	// le serveur distant a retourne une date de dermiere modification
	// on remettra a jour immediatement en cas de modification posterieure
	// a la date du fichier en cache
	$delai = 0;
}
else {
	// on impose la mise a jour avec une certaine periodicite
	$date_modif = time();
	// le delai entre deux rafraichissements en secondes a regler suivant le type de fil ici 2 heures
	$delai = 2*3600;
}

// le fichier est-il en cache et suffisamment jeune

$en_cache = file_exists($file_cache);
if ($en_cache) {
	$en_cache = ($date_modif < filemtime($file_cache) + $delai);
}

if (!$en_cache) {
	// il est considere comme n'etant pas en cache on le genere
	$data = '';
	
	// caracteres parasites pouvant etre contenus dans le fils rss et a remplacer par d'autres
	// doit Ítre invoque avant parsefile
	// cette fonction est optionnelle et ne doit etre utilisee que pour certains fils.
	$avant = array('&#','&bull;');
	$apres = array('|@|','-');
	$rss -> to_replace_with($avant, $apres);

	// parser le fichier news
	$res = $rss -> parsefile($flux);
	
	if ($res) {
		$encode = $rss -> get_encoding();
		
		// recupÈration des donnees sur le channel
		$channel = $rss -> get_channel();
		
		// affichage site, url, description 
		$data =  '<p><a href="'. $channel['link'].'" target="_blank" rel="nofollow">'. clean_text($channel['title'], $encode). '</a></p>';
		
		// nombre d'items 
		$nbnews = $rss -> get_num_items();
		
		// recup array des donnees 
		$items = $rss -> get_items();
		
		$data .= '<ul>';
		for($i = 0; $i < $nbnews; $i++) { 
			$data .= '<li>- <a href="'.$items[$i]['link'].'" target="_blank" rel="nofollow">'.clean_text($items[$i]['title'], $encode).'</a></li>'."\n"; 
		}
		
		$data .= '</ul>';
	} // fin if $rss
	
	$fd = fopen($file_cache, "w");
	fputs($fd, $data);
	fclose($fd);

} // fin if !$en_cache

include $file_cache;
?>