$(function () {
  $('.sidebar-nav .nav-item').each(function () {
    var href = this.getAttribute('href');
    if (href && window.location.href.indexOf(href) !== -1) {
      $('.sidebar-nav .nav-item').removeClass('active');
      $(this).addClass('active');
    }
  });
});
