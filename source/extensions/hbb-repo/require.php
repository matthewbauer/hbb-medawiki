<?php
if (!defined('MEDIAWIKI')) die();

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'HBB Repository',
	'author' => 'Matthew Bauer', 
	'url' => 'http://www.wiibrew.org/wiki/Homebrew Browser', 
	'description' => 'This extension creates the page Special:Repo in the Wiki, which can be used by the Homebrew Browser to load Homebrew Apps.',
	'version' => 0.1
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

		echo $newline;

		$title = $wgRequest->getText('title');
		if (strpos($title,'/')) {$title = str_replace("Special:".$this->name."/", "", $title);
		} else $title = $this->name;

		$title = Title::newFromText($title);
		$article = new Article($title);
		$content = $article->getContent();

		$a = explode("{|",$content);
		$b = explode("|}", $a[1]);
		$rows = explode("|-", $b[0]);

		$headers = array();
		
		foreach($rows as $key => $row){
			if ($key == 0) {
				$columns = explode('!', $row);
				foreach($columns as $columnkey => $column){
					if ($columnkey==0) continue;
					$column = strtolower(str_replace(" ","",str_replace("]","",str_replace("[","",str_replace("\n","",$column)))));
					if ($column == 'title' || $column == 'name'){
						$headers[0] = $columnkey;
					} else if ($column == 'author'){
						$headers[1] = $columnkey;
					} else if ($column == 'version'){
						$headers[2] = $columnkey;
					} else if ($column == 'size'){
						$headers[3] = $columnkey;
					} else if ($column == 'shortdescription'){
						$headers[4] = $columnkey;
					} else if ($column == 'longdescription' || $column == 'description'){
						$headers[5] = $columnkey;
					} else if ($column == 'format') {
						$headers[6] = $columnkey;
					} else if ($column == 'directory' || $column == 'directories'){
						$headers[7] = $columnkey;
					} else if ($column == 'rating'){
						$headers[8] = $columnkey;
					} else if ($column == 'downloads' || $column == 'hits'){
						$headers[9] = $columnkey;
					} else if ($column == 'imagesize'){
						$headers[10] = $columnkey;
					} else if ($column == 'timestamp'){
						$headers[11] = $columnkey;
					} else if ($column == 'releasedate' || $column == 'date'){
						$headers[12] = $columnkey;
					} else if ($column == 'zipsize'){
						$headers[13] = $columnkey;
					} else if ($column == 'bootsize'){
						$headers[14] = $columnkey;
					} else if ($column == 'controllers' || $column == 'peripherals'){
						$headers[15] = $columnkey;
					}
				}
				if ($headers[5] == 5) $headers[5] = $headers[4];
			} else {
				$columns = explode("|", $row);
				$name = strtolower(str_replace(" ", "_",trim(
						str_replace("\n","",str_replace("]","",str_replace("[","",$columns[$headers[0]]))))));

				if (strpos($columns[$headers[15]], "{{")!==false && strpos($columns[$headers[15]], "}}")!==false){
					$controllers = '';
					if (strpos($columns[$headers[15]], '{{Wiimote1}}')!==false || strpos($columns[$headers[15]], '{{Wiimote}}')!==false)
						$controllers = $controllers . "w";
					if (strpos($columns[$headers[15]], '{{Wiimote2}}')!==false) $controllers = $controllers . "ww";
					if (strpos($columns[$headers[15]], '{{Wiimote3}}')!==false) $controllers = $controllers . "www";
					if (strpos($columns[$headers[15]], '{{Wiimote4}}')!==false) $controllers = $controllers . "wwww";
					if (strpos($columns[$headers[15]], '{{FrontSD}}')!==false 
							|| strpos($columns[$headers[15]], '{{FrontSDHC}}')!==false)
						$controllers = $controllers . "s";
					if (strpos($columns[$headers[15]], '{{Nunchuk}}')!==false) $controllers = $controllers . "n";
					if (strpos($columns[$headers[15]], '{{ClassicController}}')!==false) $controllers = $controllers . "c";
					if (strpos($columns[$headers[15]], '{{GCNController}}')!==false) $controllers = $controllers . "g";
					if (strpos($columns[$headers[15]], '{{USBKeyboard}}')!==false) $controllers = $controllers . "k";
					if (strpos($columns[$headers[15]], '{{WiiZapper}}')!==false) $controllers = $controllers . "z";
				} else $controllers = $columns[$headers[15]];

				if ($columns[$headers[11]]){
					$timestamp = $columns[$headers[11]];
				} else if ($columns[$headers[12]]){
					$timestamp = strtotime($columns[$headers[12]]);
					if ($timestamp === false) $timestamp = mktime();
				} else {
					$timestamp = mktime();
				}

				if ($columns[$headers[6]]){
					$format = strtolower($columns[$headers[6]]);
				} else {
					$format = 'dol';
				}

				if ($columns[$headers[7]]){
					$dirs = $columns[$headers[7]];
				} else {
					$dirs = '';
				}

				if (strpos($columns[$headers[8]], "{{")!==false && strpos($columns[$headers[8]], "}}")!==false){
					$rating = 0;
					if (strpos($columns[$headers[8]], "{{Star1}}")!==false) $rating+=1;
					if (strpos($columns[$headers[8]], "{{Star2}}")!==false) $rating+=2;
					if (strpos($columns[$headers[8]], "{{Star3}}")!==false) $rating+=3;
					if (strpos($columns[$headers[8]], "{{Star4}}")!==false) $rating+=4;
				} else if ($columns[$headers[8]]){
					$rating = $columns[$headers[8]];
				} else {
					$rating = 0;
				}

				if ($columns[$headers[9]]){
					$downloads = $columns[$headers[9]];
				} else {
					$downloads = 0;
				}

				if ($columns[$headers[10]]){
					$imageSize = $columns[$headers[10]];
				} else {
					$imageSize = 0;
				}

				if ($columns[$headers[13]]){
					$zipSize = $columns[$headers[13]];
				} else {
					$zipSize = 0;
				}
			
				if ($columns[$headers[14]]){
					$bootSize = $columns[$headers[14]];
				} else {
					$bootSize = 0;
				}

				echo trim(str_replace("\n","",str_replace("]","",str_replace("[","",$name . ' ' . $timestamp . ' ' . $imageSize . ' ' . $bootSize . ' ' . $format . ' ' . $zipSize . ' ' . $downloads . ' ' . $rating . ' ' . $controllers . " " . $dirs)))) . $newline;
				for ($headerkey=0;$headerkey<6;$headerkey++){
					echo trim(str_replace("]","",str_replace("[","",str_replace("\n","",$columns[$headers[$headerkey]])))) . $newline;
				}
			}
		}
	}
}

new SpecialRepo('HBB');
?>
