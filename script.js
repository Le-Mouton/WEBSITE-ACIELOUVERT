var slideIndex = 0;
function showSlides() {
  var i;
  var slides = document.getElementsByClassName("mySlides");
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  slideIndex++;
  if (slideIndex > slides.length) {
    slideIndex = 1;
  }
  slides[slideIndex - 1].style.display = "block";
  setTimeout(showSlides, 5000); // Change image every 10 seconds
}

//TODO : search bar
function fill(Value) {
    $('#search-input').val(Value);
    $('#searchResults').hide();
}
$(document).ready(function() {
    $("#search-input").keyup(function() {
        var name = $('#search-input').val();
        if (name === "") {
            $("#searchResults").html("");
        }
        else {
            $.ajax({
                type: "POST",
                url: "../search.php",
                data: {
                    search: name
                },
                success: function(html) {
                    $("#searchResults").html(html).show();
                }
            });
        }
    });
});

