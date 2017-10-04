var SnippetPreview = require( "yoastseo" ).SnippetPreview;
var App = require( "yoastseo" ).App;

window.onload = function() {
	var focusKeywordField = document.getElementById( "focusKeyword" );
	var contentField = document.getElementById( "content" );

	var snippetPreview = new SnippetPreview({
		targetElement: document.getElementById( "snippet" )
	});

	var app = new App({
		snippetPreview: snippetPreview,
		targets: {
			output: "output"
		},
		callbacks: {
			getData: function() {
				return {
					keyword: focusKeywordField.value,
					text: contentField.value
				};
			}
		}
	});

	app.refresh();

	focusKeywordField.addEventListener( 'change', app.refresh.bind( app ) );
	contentField.addEventListener( 'change', app.refresh.bind( app ) );
};