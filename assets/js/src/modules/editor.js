angular.module('tscf').controller( 'tscfEditor', [ '$scope', '$http', '$window', function($scope, $http, $window){

  "use strict";

  $scope.index    = 0;
  $scope.settings = TSCF.settings;
  $scope.errors   = TSCF.errors || [];

  /**
   * Change index
   *
   * @param i
   */
  $scope.changeIndex = function(i){
    $scope.index = i;
  };

  /**
   * Add Field Group
   */
  $scope.addGroup = function(){
    var index = $scope.settings.length + 2;
    $scope.settings.push({
      name: "new_group_" + index,
      label: TSCF.new + ' ' + index,
      type: "post",
      post_types: [],
      context: 'side',
      priority: "default",
      description: "",
      fields: []
    });
  };

  /**
   * Clear per-field errors recursively.
   *
   * @param {Object} field
   */
  function clearFieldErrors(field){
    if (! field) {
      return;
    }
    field._errors = [];
    if ("iterator" === field.type && Array.isArray(field.fields)) {
      angular.forEach(field.fields, function(child){
        clearFieldErrors(child);
      });
    }
  }

  /**
   * Check recursively whether a field (or any of its children) has errors.
   *
   * @param {Object} field
   * @returns {boolean}
   */
  function hasFieldErrors(field){
    if (! field) {
      return false;
    }
    if (field._errors && field._errors.length) {
      return true;
    }
    if ("iterator" === field.type && Array.isArray(field.fields)) {
      var found = false;
      angular.forEach(field.fields, function(child){
        if (! found && hasFieldErrors(child)) {
          found = true;
        }
      });
      return found;
    }
    return false;
  }

  /**
   * Validate single field (recursive for iterator).
   *
   * - Label / Key are required.
   * - Iterator key must not contain underscore `_`.
   *
   * @param {Object} field
   * @param {Object} hasErrorRef { value: boolean } for aggregate flag.
   * @param {boolean} inIterator true if this field is under an iterator.
   */
  function validateField(field, hasErrorRef, inIterator){
    if (! field) {
      return;
    }

    var label = (field.label || "").toString().trim();
    var name  = (field.name  || "").toString().trim();

    if (! label) {
      field._errors.push("Label is required.");
      hasErrorRef.value = true;
    }
    if (! name) {
      field._errors.push("Key is required.");
      hasErrorRef.value = true;
    }

    // Keys of fields under iterator must not contain "_".
    // 親 iterator 自体のキーは許可し、配下の子・孫フィールドのみ禁止する。
    if (inIterator && -1 !== name.indexOf("_")) {
      field._errors.push("Keys of fields under an iterator must not contain '_'.");
      hasErrorRef.value = true;
    }

    // Recurse into iterator children.
    if ("iterator" === field.type && Array.isArray(field.fields)) {
      angular.forEach(field.fields, function(child){
        // iterator 直下の子から inIterator = true を伝播させる
        validateField(child, hasErrorRef, true);
      });
    }
  }

  /**
   * Save settings with validation and formatting.
   *
   * JSONエディタの保存時にバリデーションと整形を行う
   */
  $scope.saveFields = function(){

    var hasError = { value: false };

    // まず既存の per-field エラーをクリア
    angular.forEach($scope.settings, function(group){
      if (group && Array.isArray(group.fields)) {
        angular.forEach(group.fields, function(field){
          clearFieldErrors(field);
        });
      }
    });

    // 全グループ / 全フィールドを検査
    angular.forEach($scope.settings, function(group){
      if (group && Array.isArray(group.fields)) {
        angular.forEach(group.fields, function(field){
          // ルートレベルでは inIterator = false から開始し、
          // iterator 配下の子・孫フィールドのみ "_" 禁止ルールを適用する。
          validateField(field, hasError, false);
        });
      }
    });

    // 実際に _errors を持つフィールドが1つでもあるかどうかで判定する
    var anyError = false;
    angular.forEach($scope.settings, function(group){
      if (group && Array.isArray(group.fields)) {
        angular.forEach(group.fields, function(field){
          // iterator の子孫がエラーを持っている場合は、親 iterator 自体にも注意メッセージを付ける。
          if (field && "iterator" === field.type && Array.isArray(field.fields)) {
            var childHasError = false;
            angular.forEach(field.fields, function(child){
              if (! childHasError && hasFieldErrors(child)) {
                childHasError = true;
              }
            });
            if (childHasError) {
              if (! field._errors) {
                field._errors = [];
              }
              var parentMsg = "This iterator contains fields with errors. Please fix its child fields.";
              if (field._errors.indexOf(parentMsg) === -1) {
                field._errors.push(parentMsg);
              }
            }
          }

          if (! anyError && hasFieldErrors(field)) {
            anyError = true;
          }
        });
      }
    });

    if (anyError) {
      if ($window && $window.alert) {
        $window.alert("There are errors in your field definitions.\nPlease check the red messages near each field.");
      }
      return;
    }

    // 既存のサーバー側エラー情報はクリアしておく
    $scope.errors = [];

    // JSON出力順と不要キー削除をここで制御する
    var payload = [];
    angular.forEach($scope.settings, function(s){
      var o = {};
      // 基本情報
      o.name  = s.name || '';
      o.label = s.label || '';
      o.type  = s.type || 'post';
      // 出力: post のときのみ post_types、term のときは taxonomies を出力
      if (o.type === 'term') {
        // term のときは taxonomies を出力し、context/priority は出力しない（プロパティ削除）
        o.taxonomies = s.taxonomies || [];
      } else {
        // post のときのみ post_types を出力
        o.post_types = s.post_types || [];
        // post のときは context/priority が設定されていれば出力（未設定ならデフォルト適用のため出力しない）
        if (s.context) {
          o.context = s.context;
        }
        if (s.priority) {
          o.priority = s.priority;
        }
      }
      // 任意項目
      if (s.description) {
        o.description = s.description;
      }
      o.fields = s.fields || [];
      payload.push(o);
    });

    $http({
      method: 'POST',
      url: TSCF.endpoint.save,
      data: payload
    }).then(
      function(response){
        // Success
        console.log(response);
        if ($window && $window.alert) {
          $window.alert("Config file has been saved.");
        }
      },
      function(response){
        // Error
      }
    ).then(function(){
      // Always
    });
  };

} ] );
