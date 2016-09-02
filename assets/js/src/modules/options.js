angular.module('tscf').directive('tscfOptions', function () {
  "use strict";
  return {
    restrict: 'E',

    replace: true,

    scope: {
      options: '=',
      hasKey : '@'
    },

    templateUrl: TSCF.template('options'),

    link: function ($scope, $elem, attr) {

      $scope.value = '';

      $scope.store = [];

      $scope.duplicated = false;

      // 初期値を設定
      for (var prop in $scope.options) {
        if ($scope.options.hasOwnProperty(prop)) {

          $scope.store.push({
            value: prop,
            label: $scope.options[prop]
          });
        }
      }
      /**
       * オプションを更新する。
       */
      $scope.$watch('store', function () {
        var options = {};
        $scope.duplicated = false;
        for (var i = 0, l = $scope.store.length; i < l; i++) {
          if (!options.hasOwnProperty($scope.store[i].value)) {
            options[$scope.store[i].value] = $scope.store[i].label;
          } else {
            $scope.duplicated = true;
          }
        }
        $scope.options = options;
      }, true);


      /**
       * 要素を追加する
       * @returns {boolean}
       */
      $scope.add = function () {
        if ($scope.duplicated) {
          return false;
        }
        $scope.store.push({
          value: $scope.value,
          label: ''
        });
      };

      /**
       * 値を削除する
       *
       * @param {Number} index
       */
      $scope.remove = function (index) {
        $scope.store.splice(index, 1);
      };
    }
  };
});



