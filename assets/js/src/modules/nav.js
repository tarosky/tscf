angular.module('tscf').directive('tscfNav', function(){
  "use strict";
  return {
    restrict: "E",
    replace: true,
    scope: {
      settings: '=',
      index: '='
    },
    templateUrl: TSCF.template('nav'),
    link: function($scope, $elem, attr){
      $scope.changeIndex = function(i){
        $scope.index = i;
      };
    }
  };
});