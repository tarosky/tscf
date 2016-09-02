angular.module('tscf').directive( 'tscfAlert',function(){
  "use strict";
  return {
    restrict: 'E',
    replace: false,
    scope: {
      errors: '='
    },
    templateUrl: TSCF.template('alert'),
    link: function( $scope, $elem, attr ){

    }
  };
});



