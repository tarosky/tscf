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


  // Datepicker
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


  //
  // Iterator
  //
  // ----------------------------------
  //

  // Add button for iterator.
  $('.tscf--iterator').on('click', '.tscf__add', function(e){
    e.preventDefault();
    // Check if max
    var $container = $(this).parents('.tscf--iterator'),
        max = parseInt($container.attr('data-max'), 10),
        $template  = $container.find('.tscf__template');
    if( ! max || $container.find('.tscf__child').length < max ){
      $container.find('.tscf__childList').append($template.html());
      $container.trigger('compute.tscf');
    }
  });

  // Remove button for iterator
  $('.tscf--iterator').on('click', '.tscf__button', function(e){
    e.preventDefault();
    if( $(this).hasClass('tscf__button--delete') ){
      var $parent = $(this).parents('.tscf--iterator');
      $(this).parents('.tscf__child').remove();
      $parent.trigger('compute.tscf');
    }
  });

  // Sortable
  $('.tscf__childList').sortable({
    axis: 'y',
    handle: '.tscf__button--move',
    placeholder: 'tscf__child--placeholder',
    update: function(event, ui){
      ui.item.parents('.tscf--iterator').trigger('compute.tscf');
    }
  });

  // Change index
  $('.tscf--iterator').on('compute.tscf', function(e, noHighlight){
    var prefix = $(this).attr('data-prefix'),
        length = 0;
    if(! noHighlight){
      $(this).effect('highlight', {}, 500);
    }
    $(this).find('.tscf__child').each(function(index, elt){
      length++;
      $.each(['id', 'for', 'name'], function(nameIndex, prop){
        $(elt).find('[' + prop + '^=' + prefix + '_]').each(function (i, input) {
          $(input).attr(prop, $(input).attr(prop).replace(/_[0-9]+(\[?)/, function(){
            return '_' + ( index + 1 ) + arguments[1];
          }));
        });
      });
    });
    $(this).find('.tscf__index').val(length);
  });

  // Set initial value
  $(document).ready(function(){
    $('.tscf--iterator').trigger('compute.tscf', [true]);
  });

})(jQuery);
