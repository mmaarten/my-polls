import Chart from 'chart.js/auto';

const $elem = jQuery('#my-polls-result');
const chart = new Chart($elem, $elem.data('options'));
