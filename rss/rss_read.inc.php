<?php
// ------------------------------------------------------
// rss_read - classe pour lire des fichiers RSS/RDF/Atom
// Dominique WOJYLAC <http://wojylac.free.fr>
// Distribue sous GNU General Public License.
// 
// Version 2.2
// -------------------------------------------------------

class rss_read
{
	var $class_version;
	var $rss_channel;
	var $rss_image;
	var $rss_items;
	var $rss_textinput;
	var $rss_encoding;
	var $to_replace;
	var $replace_with;
	var $mode_agrege;
	var $erreur;
	
	/*
	** initialisation de la classe
	*/
	function rss_read()
	{
		$this -> class_version = '2.2';
		$this -> rss_channel = array();
		$this -> rss_image = array();
		$this -> rss_items = array();
		$this -> rss_textinput = array();
		$this -> rss_encoding = '';
		$this -> to_replace = array();
		$this -> replace_with = array();
		$this -> mode_agrege = true;
		$this -> erreur = '';
	}
	
	/*
	** initialise le mode
	*/	
	function mode_brut() {
		$this -> mode_agrege = false;
	}
	
	function retour_erreur() {
		return $this -> erreur;
	}

	/*
	** initialise les tableau pour remplacement de caracteres avant traitement du flux
	*/	
	function to_replace_with($avant, $apres) {
		if (!empty($avant)) {
			$this -> to_replace = $avant;
		}

		if (!empty($apres)) {
			$this -> replace_with = $apres;
		}
	}
	
	/*
	** analyse les entites pour trouver Last-Modified
	** retourne la donnee sous forme timestamp ou telle quelle
	*/
	function header_last_modified($headers, $format = true) {
		if (preg_match('/HTTP\/1.1 404/', $headers)) {
			return false;
		}
		else if (preg_match("/Last-Modified: ([^\r]*)\r/i", $headers ,$temp)) {
			$date_modif = $temp[1];
		}
		else {
			$date_modif = '';
		}
		
		if ($format == true) {
			if (empty($date_modif)) {
				return 0;
			}
			else {
				return strtotime($date_modif);
			}
		}
		else {
			return $date_modif;
		}
	}
	
	/*
	** date de modification avec fsockopen 
	*/
	function get_last_modified($url = '', $format = true) {
		
		$url_array = parse_url($url);
		$host = $url_array['host'];
		$path = $url_array['path'];
		
		if (empty($url) or empty($host) or empty($path)) {
			$this -> erreur = 'get_last_modified : url erreur';
			return false;
		}
		$fp = @fsockopen($host, 80);
		if (!$fp) {
			$this -> erreur = 'get_last_modified : pb fsockopen';
		   return false;
		}
		
		$out = 'HEAD '.$path.' HTTP/1.1'."\r\n".
			'Host: '.$host."\r\n".
			'Connection: Close'."\r\n\r\n";
		
		$lines = '';
		fwrite($fp, $out);
		while (!feof($fp)) {
			$lines .= fgets($fp, 1024);
		}
		fclose($fp);
		
		if (empty($lines)) {
			$this -> erreur = 'get_last_modified : pas de donnees';
			return false;
		}
		else {
			return $this -> header_last_modified($lines, $format);
		}
	}// fin gest_last_modified
	
	/*
	** date de modification avec bibliotheque curl 
	*/
	function curl_get_last_modified($url = '', $format = true) {
		if (empty ($url)) {
			$this -> erreur = 'curl_get_last_modified : url erreur';
			return false;
		}
		$ch = curl_init($url);
		// curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$lines = curl_exec ($ch);
		curl_close ($ch);
		
		if (empty($lines)) {
			$this -> erreur = 'curl_get_last_modified : pas de donnees';
			return false;
		}
		else {
			return $this -> header_last_modified($lines, $format);
		}
	}
	
	/*
	** analyse le flux
	*/
	function parse_data($data, $max_item = 0)
	{		
		if (preg_match('`encoding="([^"]*)"`i', $data ,$temp)) {
			$this -> rss_encoding = strtolower($temp[1]);
		} elseif (preg_match("`encoding='([^']*)'`i", $data ,$temp)) {
			$this -> rss_encoding = strtolower($temp[1]);
		}
		
		// remplacement eventuel
		if (!empty($this -> to_replace) and !empty($this -> replace_with)) {
			$data = str_replace($this -> to_replace, $this -> replace_with, $data);
		}

		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser, $data, $vals);
		xml_parser_free($xml_parser);
		// $vals = tableau associatif determine par la structure du fil
		// print_r($vals);
		$parent = array();
		$n_item = 0;
		// RSS ou FEED
		$fil_type = $vals[0]['tag'];
		
		foreach ($vals as $row) {
			// $row = tableau associatif correspondant a une ligne de $vals
			// 4 indices : tag, type, level, value
			foreach ($row as $key => $val) {
				${$key} = $val;
			}
			
			$tag = strtolower($tag);
			
			switch ($type) {	
			case 'open' :
				$parent[$level] = $tag ;
				if (($tag == 'item') or ($tag == 'entry')) {
					$n_item++;
				}
			break;
			
			case 'close' :
				if ($parent[$level] == $tag) {
					$parent[$level] = '';
				}
			break;
			
			case 'complete' :
				// cas specifiques a atom
				if (($fil_type == 'FEED') and ($tag == 'link')) {
					$value = $attributes['HREF'];
				}
				
				if (($fil_type == 'FEED') and ($tag == 'category')) {
					$value = $attributes['LABEL'];
				}
				// cas non specifiques
				if ($this -> mode_agrege) {
					switch ($tag) {
						case 'dc:subject' :
							$tag = 'category';
						break;
						
						case 'date':
						case 'dc:date':
						case 'published':
						case 'issued':
							$tag = 'pubdate';
						break;
						
						case 'updated':
							$tag = 'modified';
						break;
						
						case 'dc:description':
						case 'summary':
						case 'content:encoded':
						case 'content':
							$tag = 'description';
						break;
						
						case 'dc:rights':
						case 'rights':
							$tag = 'copyright';
						break;
						
						case 'dc:creator':
							$tag = 'author';
						break;
					} // fin switch
				} // fin if mode agrege
				
				// cas des valeurs obtenues sur attribut des tag
				switch ($tag) {
					case 'enclosure';
						$value = array(
							'url' => $attributes['URL'],
							'lenght' => $attributes['LENGTH'], 
							'type' => $attributes['TYPE'],
						);
					break;
				} // fin
				
				switch ($parent[$level - 1]) {
					case 'feed':
					case 'channel':
						$this -> rss_channel[$tag] = $value;
					break;
					
					case 'entry':
					case 'item':
						if(($n_item <= $max_item) or ($max_item == 0)) {
							$this -> rss_items[$n_item - 1][$tag] = $value;
						}
					break;
					
					case 'image':
						$this -> rss_image[$tag] = $value;
					break;
					
					case 'textinput':
						$this -> rss_textinput[$tag] = $value;
					break;
				} // fin switch $parent
			break;
			} // fin switch $type
		} // fin foreach
		
	} // fin parse_data
	
	/*
	** ouvrir et lire le flux avec fopen
	*/
	function parsefile($filename, $max_item = 0) {
		// si file_get_contants desactivee ou inoperante
		if(!$fp = @fopen($filename, 'r')) {
				$this -> erreur = 'parsefile : probleme fopen';
				return false;
			}
			
			$lines = '';
			
			while (!feof($fp)) {
				$lines .= fread($fp, 4096);
			}
			@fclose($fp);

		// si file_get_contents ok decommenter ligne suivante et commenter les precedentes
		// $lines = file_get_contents($filename);
				
		if (empty($lines)) {
			$this -> erreur = 'parsefile : pas de donnees';
			return false;
		}
		else {
			$this -> parse_data($lines, $max_item);
			return true;
		}
	} // fin parsefile
	
	/*
	** ouvrir et lire le flux avec bibliotheque curl
	*/
	function curl_parsefile($filename ='', $max_item = 0) {
		if (empty($filename)) {
			$this -> erreur = 'curl_parsefile : pas de nom de flux';
			return false;
		}
		
		$lines = '';
		$ch = curl_init($filename);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$lines = curl_exec($ch);
		curl_close($ch);

		if (empty($lines)) {
			$this -> erreur = 'curl_parsefile : pas de donnees';
			return false;
		}
		else {
			$this -> parse_data($lines, $max_item);
			return true;
		}
	} // fin curl_parsefile

	
	// encoding
	function get_encoding() {
		return $this -> rss_encoding;
	}
	
	// channel
	function exist_channel() {
		return (!empty($this -> rss_channel) and 
			!empty($this -> rss_channel['title']) and 
			!empty($this -> rss_channel['link']));
	}
	
	function get_channel() {
		return $this -> rss_channel;
	}
	
	// image
	function exist_image() {
		return (!empty($this -> rss_image['url']));
	}
	
	function get_image() {
		return $this -> rss_image;
	}
		
	// items
	function exist_items() {
		return (count($this -> rss_items) > 0);
	}
	
	function get_items() {
		return $this -> rss_items;
	}
	
	function get_num_items() {
		return count($this -> rss_items);
	}
	
	// input
	function exist_textinput() {
		return (!empty($this -> rss_textinput) and 
			!empty($this -> rss_textinput['title']) and 
			!empty($this -> rss_textinput['link']) and
			!empty($this -> rss_textinput['description']) and
			!empty($this -> rss_textinput['name']));
	}
	
	function get_textinput() {
		return $this -> rss_textinput;
	}
}

?>