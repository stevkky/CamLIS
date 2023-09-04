// Create a jquery plugin that prints the given element.
function printpage(src) {

 $(function() {
	// ASSERT: At this point, we know that the current jQuery
	// collection (as defined by THIS), contains only one
	// printable element.
 
	// Create a random name for the print frame.
	var strFrameName = ("printer-" + (new Date()).getTime());
 
	// Create an iFrame with the new name.
	var jFrame = $( "<iframe name='" + strFrameName + "' src='"+src+"'>" );
 
	// Hide the frame (sort of) and attach to the body.
	jFrame.css( "width", "1px" )
		  .css( "height", "1px" )
		  .css( "position", "absolute" )
		  .css( "left", "-9999px" )
		  .appendTo( $( "body:first" ) )
	;

	var objFrame = window.frames[ strFrameName ];

	setTimeout(function() {
		jFrame.remove();
	}, (60 * 1000));
	
});
	
}