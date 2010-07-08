$(document).ready(function(event) {

	$("#user_login").find(":input").bind("hastext", function(e) {
		$(this).parent("dd").prev("dt").find("label").fadeOut(50);
	}).bind("notext", function(e) {
		$(this).parent("dd").prev("dt").find("label").fadeIn(50);
	});
	
});