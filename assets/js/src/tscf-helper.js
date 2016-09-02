/**
 * Metabox helper.
 */
/* global TSCF:false */
/* global wp:false */
(function ($) {
  'use strict';


  //
  // Date Picker
  //
  // ----------------------------------
  //

  function dateTimePicker($elem) {
    $elem.datetimepicker({
      dateFormat: $elem.attr('data-date-format'),
      timeFormat: $elem.attr('data-time-format'),
      separator : $elem.attr('data-separator')
    });
  }

  function datePicker($elem) {
    $elem.datepicker({
      dateFormat: $elem.attr('data-date-format')
    });
  }

  // Datepicker
  $(document).ready(function () {
    // Date time picker
    $('.tscf__datetimepicker').each(function (i, elt) {
      dateTimePicker($(elt));
    });
    // Date picker
    $('.tscf__datepicker').each(function (i, elt) {
      datePicker($(elt));
    });
    // Activate datepicker if newly created.
    $('.tscf--iterator').on('created.tscf', '.tscf__child', function () {
      var $elem;
      if (( $elem = $(this).find('.tscf__datetimepicker') ) && $elem.length) {
        $elem.each(function (i, elt) {
          dateTimePicker($(elt));
        });
      }
      if (( $elem = $(this).find('.tscf__datepicker') ) && $elem.length) {
        $elem.each(function (i, elt) {
          datePicker($(elt));
        });
      }
    });
  });


  //
  // Image picker
  //
  // ----------------------------------
  //
  $(document).ready(function () {

    var imageEditor,
        $currentHolder;

    function imageChange($container) {
      var ids = [];
      $container.find('img').each(function (index, img) {
        ids.push($(img).attr('data-image-id'));
      });
      $container.prev('input[type=hidden]').val(ids.join(','));
      $container.effect('highlight', {}, 1000);
    }

    $('.tscf')
      .on('click', '.tscf__image--add', function (e) {
        e.preventDefault();
        $currentHolder = $(this).prev('.tscf__placeholder');
        var currentCount = $currentHolder.find('img').length;
        var limit = parseInt($currentHolder.attr('data-limit'), 10);
        if (currentCount >= limit) {
          return;
        }
        if (!imageEditor) {
          // Create editor if not exists
          imageEditor = wp.media({
            className: 'media-frame tscf__imageEditor',
            frame    : 'select',
            multiple : true,
            title    : $(this).text(),
            library  : {
              type: 'image'
            },
            button   : {
              text: TSCF.select
            }
          });
          // Bind event
          imageEditor.on('select', function () {
            var currentCount = $currentHolder.find('img').length;
            var limit = parseInt($currentHolder.attr('data-limit'), 10);
            var repeatLimit = limit - currentCount;
            var counter = 0;
            imageEditor.state().get('selection').each(function (image) {
              if (counter < repeatLimit) {
                var attachment = image.toJSON();
                var src;
                if (attachment.sizes.thumbnail) {
                  //サムネイルがあればその画像
                  src = attachment.sizes.thumbnail.url;
                } else {
                  //なければフルサイズを取得
                  src = attachment.sizes.full.url;
                }
                var $div = $('<div class="tscf__image">' +
                  '<img data-image-id="' + attachment.id + '" class="tscf__image--object" src="' + src + '" />' +
                  '<a class="button tscf__image--delete" href="#">' + TSCF.delete + '</a></div>');
                $currentHolder.find('.tscf__placeholder--limit').before($div);
              }
              counter++;
            });
            imageChange($currentHolder);
          });
        }
        // Open image editor
        imageEditor.open();
      })
      .on('click', '.tscf__image--delete', function (e) {
        e.preventDefault();
        var $container = $(this).parents('.tscf__placeholder');
        $(this).parents('.tscf__image').remove();
        imageChange($container);
      });
  });


  //
  // URL Selector
  //
  // ----------------------------------
  //
  $(document).ready(function () {

    var fileSelector,
        $currentInput,
        setUrl = function (input) {
          $(input).parents('.tscf__url--wrapper').find('.tscf-preview-button').attr('href', $(input).val());
        };

    // Previewer
    $('.tscf')
      .on('click', '.tscf-select-button', function (e) {
        e.preventDefault();
        $currentInput = $(this).parents('.tscf__url--wrapper').find('.tscf__input--url');
        // Create editor if not exists
        if (!fileSelector) {

          fileSelector = wp.media({
            className: 'media-frame tscf__fileSelector',
            frame    : 'select',
            multiple : false,
            title    : '',
            button   : {
              text: TSCF.select
            }
          });
          // Bind event
          fileSelector.on('select', function () {
            fileSelector.state().get('selection').each(function (file) {
              $currentInput.val(file.get('url'));
              $currentInput.trigger('change');
            });
          });
        }
        fileSelector.open();
      })
      .on('change keyup', '.tscf__input--url', function () {
        setUrl(this);
      });

    $('.tscf__input--url').each(function (index, input) {
      setUrl(input);
    });

    $(".tscf-preview-button").livePreview({
      trigger     : 'click',
      viewWidth   : 400,
      viewHeight  : 300,
      targetWidth : 1200,
      targetHeight: 900,
      scale       : '0.3333',
      offset      : 50,
      position    : 'left'
    });
  });

  //
  // Iterator
  //
  // ----------------------------------
  //
  // Add button for iterator.
  $('.tscf--iterator').on('click', '.tscf__add', function (e) {
    e.preventDefault();
    // Check if max
    var $container = $(this).parents('.tscf--iterator'),
        max        = parseInt($container.attr('data-max'), 10),
        $template  = $container.find('.tscf__template');
    if (!max || $container.find('.tscf__child').length < max) {
      var $newElem = $($template.html());
      $newElem.appendTo($container.find('.tscf__childList'));
      $container.trigger('compute.tscf');
      $newElem.trigger('created.tscf');
    }
  });

  // Remove button for iterator
  $('.tscf--iterator').on('click', '.tscf__button', function (e) {
    e.preventDefault();
    if ($(this).hasClass('tscf__button--delete')) {
      var $parent = $(this).parents('.tscf--iterator');
      $(this).parents('.tscf__child').remove();
      $parent.trigger('compute.tscf');
    }
  });

  // Sortable
  $('.tscf__childList').sortable({
    axis       : 'y',
    handle     : '.tscf__button--move',
    placeholder: 'tscf__child--placeholder',
    update     : function (event, ui) {
      ui.item.parents('.tscf--iterator').trigger('compute.tscf');
    }
  });

  // Change index
  $('.tscf--iterator').on('compute.tscf', function (e, noHighlight) {
    var prefix = $(this).attr('data-prefix'),
        length = 0;
    if (!noHighlight) {
      $(this).effect('highlight', {}, 500);
    }
    $(this).find('.tscf__child').each(function (index, elt) {
      length++;
      $.each(['id', 'for', 'name'], function (nameIndex, prop) {
        $(elt).find('[' + prop + '^=' + prefix + '_]').each(function (i, input) {
          $(input).attr(prop, $(input).attr(prop).replace(/_[0-9]+(\[?)/, function () {
            return '_' + ( index + 1 ) + arguments[1];
          }));
        });
      });
    });
    $(this).find('.tscf__index').val(length);
  });

  // Set initial value
  $(document).ready(function () {
    $('.tscf--iterator').trigger('compute.tscf', [true]);
  });

})(jQuery);
