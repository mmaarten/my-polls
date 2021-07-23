import Chart from 'chart.js/auto';

(function(){

  jQuery('#my-polls-result-chart').each(function(){
    var $elem = jQuery(this);
    const chart = new Chart($elem, $elem.data('options'));
  });

})();
