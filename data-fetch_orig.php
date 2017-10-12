<?php

class Article {
		public $title;
		public $url;
		public $summary;
		
		public function __construct($Title, $Url, $Summary){
			$this->title = $Title;
			$this->url = $Url;
			$this->summary = $Summary;
		}
}

// $test = array();
// $url = 'www.example.com';
// $summary = 'This is a website';
// $article = new Article($url, $summary);
// $test[] = $article;

/*--- Make API call to reddit ---*/
$ch = curl_init("https://www.reddit.com/r/worldnews.json");
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,20);
curl_setopt($ch, CURLOPT_TIMEOUT,20);
$reddit = json_decode(curl_exec($ch), true);
curl_close($ch);

/*-- Store URLs from Reddit API call for summarization --*/
$urls = array();
for($i=0; $i<3; $i++){
	$urls[] = $reddit['data']['children'][$i]['data']['url'];
}


// /*-- Send URLs to SMMRY API and store returned summaries in array --*/
$summaries = array();
foreach($urls as $url){
	$targetURL = ("http://api.smmry.com/&SM_API_KEY=E372D48607&SM_LENGTH=1&SM_URL=" . $url);
	$ch = curl_init($targetURL);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,20);
	curl_setopt($ch, CURLOPT_TIMEOUT,20);
	$return = json_decode(curl_exec($ch), true);//You're summary is now stored in $return['sm_api_content'].
	curl_close($ch);
	
	$article = new Article($return['sm_api_title'], $url, $return['sm_api_content']);
	$summaries[] = $article;
}

/*-- Output summaries as JSON --*/
$json = json_encode($summaries);
header('Content-type: application/json');
echo $json;

?>