
$("a[data-toggle]").click(function(event) {
  
  var toggleElement = $($(this).attr("data-toggle"));
  
  event.preventDefault();
  
  toggleElement.toggleClass("hidden");
  
});