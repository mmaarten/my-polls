jQuery('#my-polls-form').on('submit', function(event) {
    event.preventDefault();
    var $elem = jQuery(this);
    $elem.addClass('is-loading');
    jQuery.post(MyPolls.ajaxurl, $elem.serialize(), function(response){
      $elem.removeClass('is-loading');
      $elem.find('.my-polls-output').html(response);
    });
});
