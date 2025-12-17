/**
 * Metabox helper.
 */
/* global TSCF:false */
/* global wp:false */
(function ($) {
  'use strict';

  // Timepicker
  var dateTimePicker = function ($elem) {
    $elem.datetimepicker({
      dateFormat : $elem.attr('data-date-format'),
      timeFormat : $elem.attr('data-time-format'),
      separator  : $elem.attr('data-separator'),
      changeYear : true,
      changeMonth: true
    });
  };

  // Date picker
  var datePicker = function ($elem) {
    $elem.datepicker({
      dateFormat : $elem.attr('data-date-format'),
      changeYear : true,
      changeMonth: true
    });
  };

  // Select2
  var select2 = function ($elem) {
    var max = parseInt($elem.attr('data-limit'), 10);
    var postType = $elem.attr('data-post-type');
    var config = {
      minimumInputLength: 1,
      //placeholder       : "", // @see {https://github.com/select2/select2/issues/3497}
      allowClear        : true,
      ajax              : {
        url           : TSCF.root + '/posts',
        dataType      : 'json',
        delay         : 250,
        data          : function (params) {
          return {
            q        : params.term, // search term
            post_type: postType,
            _wpnonce : TSCF.nonce
          };
        },
        processResults: function (data, params) {
          return {
            results: data.posts
          };
        },
        cache         : false,
      }
    };
    if (0 < max) {
      config.maximumSelectionLength = max;
    }
    $elem.select2(config);
    $elem.change(function (e) {
      var value = $(this).val();
      if ( null === value ) {
        value = '';
      } else if ('string' !== typeof value) {
        value = value.join(',');
      }
      $(this).prev('input').val(value);
    });
  };

  // Code Editor
  var codeEditor = function ($textArea) {
    var lang = $textArea.attr('data-language');
    var theme = $textArea.attr('data-theme');
    var editor = ace.edit($textArea.next('.tscf__ace').get(0));
    editor.setTheme('ace/theme/' + theme);
    editor.getSession().setMode('ace/mode/' + lang);
    editor.setOptions({
      maxLines: 500
    });
    var save = function () {
      $textArea.val(editor.getValue());
    };
    var timer = null;
    editor.getSession().on('change', function (e) {
      if (timer) {
        clearTimeout(timer);
      }
      timer = setTimeout(save, 1000);
    });
  };


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
    // select2
    $('.tscf__input--token').each(function (i, elt) {
      select2($(elt));
    });
    // Ace
    $('.tscf__input--ace').each(function (i, elt) {
      codeEditor($(elt));
    });
    // Activate datepicker if newly created.
    $('.tscf--iterator').on('created.tscf', '.tscf__child', function () {
      var $elem;
      $.each([
        ['datetimepicker', dateTimePicker],
        ['datepicker', datePicker],
        ['input--token', select2],
        ['input--ace', codeEditor]
      ], function (index, config) {
        if (( $elem = $(this).find($('.tscf__'+config[0])).selector ) && $elem.length ) {
          $elem.each(function (i, elt) {
            config[1]($(elt));
          });
        }
      });
    });
  });


  //
  // Media picker
  //
  // ----------------------------------
  //
  $(document).ready(function () {

    var imageEditor,
        videoEditor,
        $currentHolder;

    function imageChange($container) {
      var ids = [];
      $container.find('img').each(function (index, img) {
        ids.push($(img).attr('data-image-id'));
      });
      $container.prev('input[type=hidden]').val(ids.join(','));
      $container.effect('highlight', {}, 1000);
    }

    function videoChange($container) {
      var ids = [];
      $container.find('video').each(function (index, video) {
        ids.push($(video).attr('data-video-id'));
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

    $('.tscf')
      .on('click', '.tscf__video--add', function (e) {
        e.preventDefault();
        $currentHolder = $(this).prev('.tscf__placeholder');
        var currentCount = $currentHolder.find('video').length;
        var limit = parseInt($currentHolder.attr('data-limit'), 10);
        if (currentCount >= limit) {
          return;
        }
        if (!videoEditor) {
          // Create editor if not exists
          videoEditor = wp.media({
            className: 'media-frame tscf__videoEditor',
            frame    : 'select',
            multiple : true,
            title    : $(this).text(),
            library  : {
              type: 'video'
            },
            button   : {
              text: TSCF.select
            }
          });
          // Bind event
          videoEditor.on('select', function () {
            var currentCount = $currentHolder.find('video').length;
            var limit = parseInt($currentHolder.attr('data-limit'), 10);
            var repeatLimit = limit - currentCount;
            var counter = 0;
            videoEditor.state().get('selection').each(function (video) {
              if (counter < repeatLimit) {
                var attachment = video.toJSON();
                var src;
                src = attachment.url;
                var $div = $('<div class="tscf__video">' +
                  '<video data-video-id="' + attachment.id + '" class="tscf__video--object" src="' + src + '" />' +
                  '<a class="button tscf__video--delete" href="#">' + TSCF.delete + '</a></div>');
                $currentHolder.find('.tscf__placeholder--limit').before($div);
              }
              counter++;
            });
            videoChange($currentHolder);
          });
        }
        // Open video editor
        videoEditor.open();
      })
      .on('click', '.tscf__video--delete', function (e) {
        e.preventDefault();
        var $container = $(this).parents('.tscf__placeholder');
        $(this).parents('.tscf__video').remove();
        videoChange($container);
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
  // document 委譲にして、動的に追加される入れ子iteratorでの多重発火を防ぐ
  $(document).on('click', '.tscf--iterator .tscf__add', function (e) {
    e.preventDefault();
    // Check if max
    var $container = $(this).closest('.tscf--iterator'),
        max        = parseInt($container.attr('data-max'), 10),
        // 自身の iterator 直下のテンプレートのみを対象にする。ネストした iterator のテンプレートは無視する
        $template  = $container.children('.tscf__template'),
        $list      = $container.children('.tscf__childList');

    if (!max || $list.children('.tscf__child').length < max) {
      var $newElem = $($template.html());

      // 親の行を追加するときは、ネストしたiteratorの中身は空の状態から始める。既存行で入力済みの繰り返し値がそのまま複製されないようにリセットする
      $newElem.find('.tscf--iterator').each(function () {
        var $it = $(this);
        $it.find('.tscf__childList').empty();
        $it.find('.tscf__index').val(0);
      });

      $newElem.appendTo($list);
      $container.trigger('compute.tscf');
      $newElem.trigger('created.tscf');
    }
  });

  // Remove button for iterator
  $(document).on('click', '.tscf--iterator .tscf__button', function (e) {
    e.preventDefault();
    if ($(this).hasClass('tscf__button--delete')) {
      var $parent = $(this).closest('.tscf--iterator');
      // 最も近い .tscf__child（この行）だけを削除する。入れ子の親行までは削除しない
      $(this).closest('.tscf__child').remove();
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
  // いま処理している iterator 直下のindexだけを更新する。ネストしたiteratorのフィールド（孫）はスキップする
  $( document ).on( 'compute.tscf', '.tscf--iterator', function( e, noHighlight ) {
    var prefix    = $( this ).attr( 'data-prefix' ),
        length    = 0,
        // prefix を正規表現用にエスケープ
        escPrefix = prefix.replace( /[-[\]/{}()*+?.\\^$|]/g, '\\$&' ),
        // 先頭が prefix_フィールド名_数字 もしくはその後ろに [ _ のどちらかが続くものだけが対象。iterator.php の Iterator::get_field_indexes() と同じ条件
        // 例: repeater_test_aaa_1, repeater_test_bbb_1_ccc_9999 など
        re        = new RegExp( '^' + escPrefix + '_[^_]+_[0-9]+(\\[|_|$)' ),
        // 例: repeater_test_bbb_1_ccc_9999 の repeater_test_bbb_ までをキャプチャ
        headRe    = new RegExp( '^(' + escPrefix + '_[^_]+_)\\d+' );

    if (!noHighlight) {
      $(this).effect('highlight', {}, 500);
    }

    // 直下の .tscf__childList 配下にある子行 .tscf__child のみを対象にする。
    $(this).find('> .tscf__childList > .tscf__child').each(function (index, elt) {
      length++;
      $.each(['id', 'for', 'name'], function (nameIndex, prop) {
        $(elt).find('[' + prop + '^=' + prefix + '_]').each(function (i, input) {
          var current = $(input).attr(prop);
          // この iterator 直下のフィールドでなければスキップ
          if ( ! re.test(current) ) {
            return;
          }

          // いま処理している iterator 自身の直下の行 index だけを並び順に合わせて振り直す
          $( input ).attr( prop, current.replace( headRe, function( match, p1 ) {
            return p1 + ( index + 1 );
          } ) );
        } );
      } );
    } );

    // いま処理している iterator 自身の index hidden (_index_of_xxx) だけを更新する。ネストした iterator の index hidden までは書き換えない
    $( this ).find( '> .tscf__index' ).val( length );
  } );

  // Set initial value
  $(document).ready(function () {
    $('.tscf--iterator').trigger('compute.tscf', [true]);
  });


})(jQuery);
