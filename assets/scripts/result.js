import Chart from 'chart.js/auto';

(function(){

  var chart;

  function update() {
    var $elem = jQuery('#my-polls-result');

    var data = {
      action : $elem.data('action'),
      poll   : $elem.data('poll'),
    }

    data[$elem.data('noncename')] = $elem.data('nonce');

    console.log(data);

    $elem.addClass('is-loading');
    jQuery.post(MyPolls.ajaxurl, data, function(response){
      $elem.removeClass('is-loading');

      if (chart) {
        chart.destroy();
      }
      chart = new Chart($elem, response.data);
    });
  }

  window.addEventListener('DOMContentLoaded', function() {
    update();
  });

  window.myPollsResult = {
    update : update,
  };

})();


