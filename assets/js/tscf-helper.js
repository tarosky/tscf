/**
 * Metabox helper.
 */

(function ($) {
  'use strict';

  function dateTimePicker($elem){
    $elem.datetimepicker({
      dateFormat: $elem.attr('data-date-format'),
      timeFormat: $elem.attr('data-time-format'),
      separator: $elem.attr('data-separator')
    });
  }

  function datePicker($elem){
    $elem.datepicker({
      dateFormat: $elem.attr('data-date-format')
    });
  }


  $(document).ready(function(){
    // Date time picker
    $('.tscf__datetimepicker').each(function(i, elt){
      dateTimePicker($(elt));
    });
    // Date picker
    $('.tscf__datepicker').each(function(i, elt){
      datePicker($(elt));
    });
  });





})(jQuery);
