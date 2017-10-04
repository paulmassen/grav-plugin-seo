// var SnippetPreview = require( "yoastseo" ).SnippetPreview;
var App = require( "yoastseo" ).App;
var Researcher = require( "yoastseo" ).Researcher;




window.onload = function() {
	var focusKeywordField = document.getElementById( "focusKeyword" );
	// var contentField = document.getElementById( "content" );
	var editor = CodeMirror.fromTextArea(document.getElementById("content"), {
    lineNumbers: true,
    matchBrackets: true,
    mode: "text/x-csrc"
  });
  	var contentField = editor.getValue();

	/*var snippetPreview = new SnippetPreview({
		targetElement: document.getElementById( "snippet" )
	});*/
    
	var app = new App({
		// snippetPreview: snippetPreview,
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
	var researcher = new Researcher( new Paper( "Text that has been written" ) );
    console.log( researcher.getResearch( "wordCountInText" ) );
};