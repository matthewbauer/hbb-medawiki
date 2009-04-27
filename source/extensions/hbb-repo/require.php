<?php
if (!defined('MEDIAWIKI')) die();

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'HBB Repo',
	'author' => 'Matthew Bauer', 
	'url' => 'http://www.wiibrew.org/wiki/Homebrew Browser', 
	'description' => 'This extension creates the page Special:Repo in the Wiki, which can be used by the Homebrew Browser to load Homebrew Apps.',
	'version' => 0.01
);

require_once('includes/SpecialPage.php');

class SpecialRepo extends SpecialPage {
	var $name;
	function __construct($name) {
		$this->name = $name;
		$this->SpecialPage($name, $includable = true);
		SpecialPage::$mList[$name] = $this;
	}

	function execute($par) {
		global $wgRequest, $wgOut;

		$wgOut->clearHTML();
		$wgOut->disable();

		$newline = "\n";

		$title = $wgRequest->getText('title');
		if (strpos($title,'/')) {$title = str_replace("Special:".$this->name."/", "", $title);
		} else $title = $this->name;
	
		echo 'From: http://'.$IP.'/mediawiki/index.php/' . $title . $newline;

		$title = Title::newFromText($title);
		$article = new Article($title);
		$content = $article->getContent();

		$rows = explode('|-', $content);
		
		foreach($rows as $key => $row){
			if ($key == 0){
			} else if ($key == 1) {
				$columns = explode('!', $row);
				$headers = array(0, 1, 2, 3, 4, 5);
				foreach($columns as $columnkey => $column){
					$column = strtolower(str_replace(" ","",str_replace("\n","",$column)));
					if ($column == 'title' || $column == 'name'){
						$headers[0] = $columnkey;
					} else if ($column == 'author'){
						$headers[1] = $columnkey;
					} else if ($column == 'version'){
						$headers[2] = $columnkey;
					} else if ($column == 'size'){
						$headers[3] = $columnkey;
					} else if ($column == 'shortdescription' || $column == 'description'){
						$headers[4] = $columnkey;
					} else if ($column == 'longdescription'){
						$headers[5] = $columnkey;
					} else if ($column == 'format') {
						$headers[6] = $columnkey;
					} else if ($column == 'date') {
						$headers[7] = $columnkey;
					} else if ($column == 'features' || $column == 'controllers' || $column == 'peripherals'){
						$headers[8] = $columnkey;
					} else if ($column == 'directory' || $colmn == 'directories'){
						$headers[9] = $columnkey;
					} else if ($column == 'rating'){
						$headers[10] = $columnkey;
					} else if ($column == 'downloads' || $column == 'hits'){
						$headers[11] = $columnkey;
					}
				}
				if ($headers[5] == 5) $headers[5] = $headers[4];
			} else {
				$columns = explode('|', $row);
				$controllers = '';
				if (strpos($columns[$headers[8]], '{{') && strpos($columns[$headers[8]], '}}')){
					if (strpos($columns[$headers[8]], '{{Wiimote1}}') || strpos($columns[$headers[8]], '{{Wiimote}}'))
						$controllers = $controllers . "w";
					if (strpos($columns[$headers[8]], '{{Wiimote2}}')) $controllers = $controllers . "ww";
					if (strpos($columns[$headers[8]], '{{Wiimote3}}')) $controllers = $controllers . "www";
					if (strpos($columns[$headers[8]], '{{Wiimote4}}')) $controllers = $controllers . "wwww";
					if (strpos($columns[$headers[8]], '{{FrontSD}}') || strpos($columns[$headers[8]], '{{FrontSDHC}}'))
						$controllers = $controllers . "s";
					if (strpos($columns[$headers[8]], '{{Nunchuk}}')) $controllers = $controllers . "n";
					if (strpos($columns[$headers[8]], '{{ClassicController}}')) $controllers = $controllers . "c";
					if (strpos($columns[$headers[8]], '{{GCNController}}')) $controllers = $controllers . "g";
					if (strpos($columns[$headers[8]], '{{USBKeyboard}}')) $controllers = $controllers . "k";
					if (strpos($columns[$headers[8]], '{{WiiZapper}}')) $controllers = $controllers . "z";
				} else $controllers = $columns[$headers[8]];
				$format = $columns[$headers[6]];
				$imageSize = 0;
				$numberOfFiles = 0;
				$timestamp = 0;
				$bootSize = 0;
				$zipSize = 0;
				$dirs = $columns[$headers[9]];
				$rating = $columns[$headers[10]];
				$downloads = $columns[$headers[11]];
				echo strtolower(trim(str_replace("\n","",str_replace("]","",str_replace("[","",$columns[$headers[0]]))))) . ' ' . $timestamp . ' ' . $imageSize . ' ' . $bootSize . ' ' . $format . ' ' . $zipSize . ' ' . $downloads . ' ' . $rating . ' ' . $controllers . ' ' . $dirs . $newline;
				foreach($headers as $key => $header){
					if ($key == 6) break;
					if (!($header == $key)){
						echo trim(str_replace("]","",str_replace("[","",$columns[$header]))) . $newline;
					} else {
						echo "";
					}
				}
			}
		}
		$this->setHeaders();
	}
}

new SpecialRepo('HBB');
?>
