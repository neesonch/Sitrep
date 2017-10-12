var app = (function($){
	$(document).ready(function(){
		/*-- Get data from PHP middleware --*/
		$.getJSON("data-fetch.php", function (d){
			var data = d;
			for (var i = 0, len = data.length; i<len; i++){
				// Parse data for unescaped quotes
				var title = data[i]['title'];
				if (title != null){
					title = title.replace(/\\/g, "");
				}
				var url = data[i]['url'];
				var content = data[i]['summary'];
				var domain = extractDomain(url);
				var article = ('<article> <h1>' + title + '</h1><a target="blank" href="' + url +'">' + domain + '</a><p>'+ content +'</p></article>' );
				$('#content').append(article);
			}
		});
		/*-- End PHP middleware call --*/
		
			'<article> <h1>Test article with multiline headline to see how headlines look when said headline takes up multiple lines as opposed to merely one</h1> <a target="blank" href = "http://www.google.com">Google</a> <p>Test content of article. Ipsem lorum blah blah blah. Mike check one two, one two three. How much wood would a woodchuck chuck if a woodchuck could chuck wood? This is probably about long enough now.</p> </article>'
			
		/*-- Show/hide loading gif --*/
		 $(document).ajaxStart(function () {
				$("#loading").show();
			}).ajaxStop(function () {
				$("#loading").hide();
			});
		/*-- End show/hide loading gif --*/
			
		
		/*-- Define function to extract domain name from URL 
		(from http://stackoverflow.com/questions/8498592/extract-root-domain-name-from-string, with modifications) --*/
		function extractDomain(url) {
			var domain;
			//find & remove protocol (http, ftp, etc.) and get domain
			if (url.indexOf("://") > -1) {
				domain = url.split('/')[2];
			}
			else {
				domain = url.split('/')[0];
			}
			//find & remove port number
			domain = domain.split(':')[0];
			//return domain name between first dot and first slash
			console.log(domain);
			//remove 'WWW' if present at start of domain name
			if (domain.indexOf('www') == 0){
				domain = domain.substring((domain.indexOf('.')+1));
			}
			return domain;
		}
		/*-- End domain extractor --*/

	});
}(jQuery));