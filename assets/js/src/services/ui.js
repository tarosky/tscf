angular.module('tscf').factory('ui', function(){
  "use strict";
  return {
    toggle: function(target){
      jQuery( target ).toggleClass( 'toggle' );
    }
  };
});
