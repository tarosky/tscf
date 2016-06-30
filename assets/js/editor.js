/**
 * Editor for TSCF
 */

/*global TSCF: false*/
/*global ace: false*/

jQuery(document).ready(function($){

  "use strict";

  var editor = ace.edit("tscf-editor");
  editor.setTheme("ace/theme/github");
  editor.session.setMode("ace/mode/json");

  var timer;

  /**
   * Show message
   *
   * @param {String} msg
   * @param {String} className
   */
  function message(msg, className){
    if( timer ){
      clearTimeout(timer);
    }
    $('#tscf-message').html('<span class="' + className + '">' + msg +  '</span>').effect('highlight', {}, 500);
    timer = setTimeout(function(){
      $('#tscf-message').html('').effect('highlight', {}, 500).toggleClass('toggle');
    }, 5000);
  }

  // Save action
  $('#tscf-submit').click(function(e){
    var $button = $(this);
    e.preventDefault();
    editor.setReadOnly(true);
    $('#tscf-message').toggleClass('toggle');
    message('保存中……', 'loading');
    $.ajax({
      type: "POST",
      url: TSCF.endpoint,
      processData: false,
      contentType: 'application/json',
      data: editor.getValue()
    }).done(function(response){
      message(response.message, 'success');
    }).fail(function(xhr){
      message(xhr.responseJSON.message, 'error');
    }).always(function(){
      editor.setReadOnly(false);
    });
  });

});
