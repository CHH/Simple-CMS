$(document).ready(function() {
  
  $("#request_params").submit(function(event) {
    var uri = $(this).find("#uri").val();
    var method = $(this).find("#method").val();
    var postBody = $(this).find("#body").val();
    
    var errorMessages = {403: "Forbidden"}
    
    event.preventDefault();
    
    $.ajax({
      url: uri,
      type: method,
      dataType: "text",
      data: postBody,
      success: function(data) {
        var results = $("#results");
        
        results.text(data);
      },
      error: function(request, status, errorThrown) {
        $("#results").text("The Request failed with the error \"" + request.status + " " + errorMessages[request.status] + "\"");
      }
    });
    
  });
  
});