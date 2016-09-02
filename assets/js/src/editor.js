/**
 * Editor for TSCF
 */

/*global TSCF: false*/
/*global ace: false*/

jQuery(document).ready(function ($) {

  "use strict";

  /*
   $.ajax({
   type       : "POST",
   url        : TSCF.endpoint,
   processData: false,
   contentType: 'application/json',
   data       : editor.getValue()
   */
});

angular.module('tscf', [ 'ui.sortable' ] );

/**
 * Get template URL
 * @param {string} name
 * @returns {string}
 */
TSCF.template = function(name){
  "use strict";
  return this.endpoint.template + '&file=' + name;
};

//=require services/*.js
//=require modules/*.js
