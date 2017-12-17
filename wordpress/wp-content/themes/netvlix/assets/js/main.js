(function($) {
  $('#genre-select-list').change(function(e) {
    const selectedGenre = e.target.value;
    if (selectedGenre == -1) {
      window.location.href = '/';
      return;
    }

    window.location.href = "/genres/" + selectedGenre;
  });
})(jQuery);
