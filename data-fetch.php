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


// Get popular news stories from Reddit, send to summarizer, and return summaries
function getSummaries(){
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
	for($i=0; $i<25; $i++){
		$urls[] = $reddit['data']['children'][$i]['data']['url'];
	}

	// /*-- Send URLs to SMMRY API and store returned summaries in array --*/
	$summaries = array();
	foreach($urls as $url){
		/* Number of sentences is set with SM_Length parameter in url */
		$targetURL = ("http://api.smmry.com/&SM_API_KEY=E372D48607&SM_LENGTH=2&SM_QUOTE_AVOID&SM_URL=" . $url);
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

	/*-- Output summaries as JSON
	$json = json_encode($summaries);
	header('Content-type: application/json');
	echo $json; --*/
	return $summaries;
}
// /*--- end getSummaries() function ---*/

/*--- Caching function - taken from http://www.kevinleary.net/api-request-caching-json-php/ ---*/
function json_cached_api_results( $cache_file = NULL, $expires = NULL ) {
    global $request_type, $purge_cache, $limit_reached, $request_limit;

    if( !$cache_file ) $cache_file = dirname(__FILE__) . '/api-cache.json';
    if( !$expires) $expires = time() - 2*60*60;

    if( !file_exists($cache_file) ) die("Cache file is missing: $cache_file");

    /*Check that the file is older than the expire time and that it's not empty*/
    if ( filectime($cache_file) < $expires || file_get_contents($cache_file)  == '' || $purge_cache && intval($_SESSION['views']) <= $request_limit ) {

        /*File is too old, refresh cache*/
        $api_results = getSummaries();
        $json_results = json_encode($api_results);

        /*Remove cache file on error to avoid writing wrong xml*/
        if ( $api_results && $json_results )
            file_put_contents($cache_file, $json_results);
        else
            unlink($cache_file);
    } else {
        /*Check for the number of purge cache requests to avoid abuse*/
        if( intval($_SESSION['views']) >= $request_limit ) 
            $limit_reached = " <span class='error'>Request limit reached ($request_limit). Please try purging the cache later.</span>";
        /*Fetch cache*/
        $json_results = file_get_contents($cache_file);
        $request_type = 'JSON';
    }
    
	header('Content-type: application/json');
	echo $json_results;
}
// /*--- End caching function ---*/

/*-- Call all of the above --*/
json_cached_api_results();

?>