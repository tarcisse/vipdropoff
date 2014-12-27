<?php
header('Content-Type: text/html; charset=utf-8');

$the_flux = array(
	0 => 'http://www.zdnet.fr/feeds/rss/actualites/',
	1 => 'http://rezo.net/backend/portail',
	2 => 'http://www.macbidouille.com/macbidouille.rss',
	3 => 'http://www.php.net/news.rss',
	4 => 'http://www.annonay.org/agenda.rss',
	5 => 'http://www.liberation.fr/rss.php',
	6 => 'http://www.arteradio.com/servlet/RSSMakerServlet',
	7 => 'http://www.monde-diplomatique.fr/rss/carnet/',
	8 => 'http://www.spip-contrib.net/spip.php?page=backend',
	);

function flux_name($url) {
	return preg_replace('`http://(www\.)?([^/]*).*`i', '\\2', $url);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>lecteur flux rss</title>
	<style type="text/css" media="screen"><!--
body,table,input,select,textarea   { font-size: 10px; font-family: Verdana, Arial, Helvetica }
h3 {font-size: 12px;}
a { color: navy; text-decoration: none }
a:hover  { color: #ff4500; text-decoration: underline overline; background-color: #dcdcdc }
.cadre  { float: none; margin-right: auto; margin-left: auto; padding: 3px; border: solid 1px #696969; width: 75% }
--></style>
</head>
<body>
<p><a href="index.html">&lt; Accueil</a></p>
		<p><b>Exemple-0</b></p>
<form method="post" name="FormName">
			<div align="center">
			<table border="1" cellspacing="0" cellpadding="3" align="center">
				<tr>
					<td>Choisir un flux<br>(Choix non exhaustif)</td>
					<td><select name="flux" size="1">
							<option value="">---</option>
							<?php
							$nbre_flux = count($the_flux);
							for ($i = 0; $i < $nbre_flux; $i++) {
								echo '<option value="', $i, '">', flux_name($the_flux[$i]), '</option>';
							}
							?>
						</select></td>
				</tr>
				<tr>
					<td>Ou indiquez une url</td>
					<td><input type="text" name="url_flux" size="32" border="0" /></td>
				</tr>
				<tr>
					<td><input type="checkbox" name="mode_analyse" value="mode_brut" />Donn&eacute;es brutes</td>
						<td align="center"><input type="submit" name="voir" value="Voir" border="0" /></td>
					</tr>
			</table>
		</div>
		</form>
<p>&nbsp;</p>
<?php
	
$flux = '';
$url_flux = '';
$xml = '';

if (isset($_POST['flux']) and $_POST['flux'] != '') {
	$flux = $_POST['flux'];
	$xml = $the_flux[$flux];
}

if (isset($_POST['url_flux']) and $_POST['url_flux'] != '') {
	$xml = $_POST['url_flux'];
}

if (empty($xml)) {
	exit;
}

include 'utils.php';
include 'rss_read.inc.php';
$rss = new rss_read();

$avant = array('&#','&bull;');
$apres = array('|@|','-');
$rss -> to_replace_with($avant, $apres);

$rss -> parsefile($xml);
 
if (!$rss) {
	exit('Fichier rss incorrect !');
}

$encode = $rss -> get_encoding();

$date_modif = $rss -> get_last_modified($xml, false);

echo '<div class="cadre">';

echo '<p><i>Classe fil_LE read ', $rss -> class_version,
	'<br/>Flux : ', $xml,
	'<br />Encodage : ',$encode,
	'<br /> Date modification : ', $date_modif, '</i></p>';

switch ($_POST['mode_analyse']) {
	case 'mode_brut' :
		// recuperation des donnees sur le channel
		$channel = $rss -> get_channel();
		
		echo '<h3 align="center">---- Channel ----</h3><ul>';
			foreach($channel as $element => $val) {
				echo '<li><i>', $element,'</i> : ', clean_text($val, $encode),'</li>';
			}
		echo '</ul>',"\n";
		
		if ($rss -> exist_image()) {
			$image = $rss -> get_image();
			echo '<h3 align="center">---- Image ----</h3><ul>';
			foreach($image as $element => $val) {
				echo '<li><i>', $element,'</i> : ', clean_text($val, $encode),'</li>';
			}
			echo '</ul>',"\n";
		}
		
		
		// nombre d'items     
		$nbnews = $rss -> get_num_items();
		
		// recup array des donnees
		$items = $rss -> get_items();
		echo '<h3 align="center">---- Items ----</h3><dl>';
		for($i = 0; $i < $nbnews; $i++) {
			echo '<dl>';
			foreach($items[$i] as $element => $val) {
				if (is_array($val)) {
					echo '<dl><dt><i>', $element,'</i> :</dt>';
					foreach($val as $key => $key_value) {
						echo '<dd> - <i>', $key,'</i> : ', clean_text($key_value, $encode),'</dd>';
					}
					echo '</dl>';
				} else {
					echo '<dd><i>', $element,'</i> : ', clean_text($val, $encode),'</dd>';
				}
			}
			echo '</dl><br clear="all" />',"\n";
		}
	break;

	default :
		// recuperation des donnees sur le channel
		$channel = $rss -> get_channel();
		
		echo '<h2 align="center"><a href="', $channel['link'],'" target="_blank">',
			clean_text($channel['title'],$encode), '</a></h2><h3>',
			clean_text($channel['description'],$encode), '</h3>';
		
		if ($rss -> exist_image()) {
			$image = $rss -> get_image();
			echo '<div align="center">
				<a href="',$image['link'],'" target="_blank">
				<img src="',$image['url'],'" 
				width="',$image['width'],'" 
				height="',$image['height'],'" 
				border="0" alt="',clean_text($image['title'], $encode),'"></a></div>';
		}
		
		// nombre d'items     
		$nbnews = $rss -> get_num_items();
		
		// recup array des donnees
		$items = $rss -> get_items();
		
		for($i = 0; $i < $nbnews; $i++) {
			echo '<hr noshade size="1" width="80%"><dl>';
			echo '<dt><b>.:: <a href="',$items[$i]['link'],'" target="_blank">',
				clean_text($items[$i]['title'], $encode),'</a> ::.</b></dt>',"\n";
			if (!empty($items[$i]['pubdate'])) {
				echo '<dd>date : ',clean_date($items[$i]['pubdate'], 'iso-8859-1'),'</dd>';
			}
			if (!empty($items[$i]['author'])) {
				echo '<dd>auteur : ',clean_text($items[$i]['author'],$encode),'</dd>';
			}
			if (!empty($items[$i]['category'])) {
				echo '<dd>Categorie : ',clean_text($items[$i]['category'], $encode),'</dd>';
			}
			if (!empty($items[$i]['description'])) {
				echo '<dd>',clean_text($items[$i]['description'], $encode),'<br clear="all" /></dd>';
			}
			if (!empty($items[$i]['enclosure'])) {
				echo '<dd>Enclosure : <a href="',$items[$i]['enclosure']['url'],'" target="_blank" rel="nofollow">',$items[$i]['enclosure']['url'],'</a> (', $items[$i]['enclosure']['type'],')</dd>' ;
			}
			echo '</dl>',"\n";
		}
	break;
} // fin switch
echo '</div>';
?>
</body>
</html>