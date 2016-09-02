/**
 * Description
 */

/*global wpApiSettings: false*/
/*global Hametuha: false*/
/*global HameditorPost: true*/
/*global tinyMCE: false*/


(function ($) {
  'use strict';

  var timer;

  /**
   * Resize Tiny MCE
   *
   * @param {Number} [delay]
   */
  function resize(delay) {
    if (!delay) {
      delay = 200;
    }
    if (timer) {
      clearTimeout(timer);
    }
    timer = setTimeout(function () {
      var winHeight  = $(window).height(),
          $container = $('.container--hameditor'),
          $editor    = $('.mce-edit-area iframe'),
          height     = $editor.height(),
          pad        = winHeight - $container.outerHeight();
      $editor.height(height + pad - 1);
    }, delay);
  }


  // Initialize
  $(document).ready(function () {

    var initTimer = setInterval(function () {
      if ($('.mce-edit-area iframe').length) {
        clearInterval(initTimer);
        resize(20);
      }
    }, 100);

    $('#quit-editting').click(function (e) {
      e.preventDefault();
      var url = $(this).attr('href');
      Hametuha.confirm('編集をやめてこのページを移動しますか？ 保存していない変更は失われます。', function () {
        window.location.href = url;
      });
    });
  });

  // Resize on window size change
  $(window).on('resize', function () {
    resize();
  });

  $(document).ready(function () {
    angular.module('hametuha', ['ui.bootstrap'])
      .filter('i18n', [function () {
        return function (string, context) {
          switch( context ){
            case 'postStatus':
              switch( string.toLowerCase() ){
                case 'publish':
                  return '公開済み';
                case 'future':
                  return '公開予約';
                case 'private':
                  return '非公開';
                case 'auto-draft':
                case 'draft':
                  return '下書き';
                case 'pending':
                  return 'レビュー待ち';
                default:
                  return string;
              }
              break;
            case 'postStatusLabel':
              switch(string.toLowerCase()){
                case 'publish':
                case 'future':
                  return 'success';
                case 'private':
                  return 'danger';
                case 'pending':
                  return 'warning';
                default:
                  return 'default';
              }
              break;
            default:
              return string;
          }
        };
      }])
      .directive('postStatus', function(){
        return {
          restrict: 'E',
          replace: true,
          scope: {
            status: '@'
          },
          templateUrl: Hametuha.template('post-status.html')
        };
      })
      .directive('postPublisher', ['$uibModal', function($uibModal){
        return {
          restrict: 'E',
          replace: false,
          scope: {
            postDate: '=',
            postStatus: '@',
            postCallback: '@'
          },
          templateUrl: Hametuha.template('post-publisher.html'),
          link: function($scope, $elem, attr){

            $scope.ask = function(){
              var modal = $uibModal.open({
                animation: true,
                templateUrl: Hametuha.template('post-date-selector.html'),
                controller: 'postDateSelector',
                size: 'sm'
              });
              modal.result.then(
                function(result){
                },
                function(){
                  // Do nothing.
                }
              );
            };
          }
        };
      }])
      .controller('postDateSelector', ['$scope', '$uibModalInstance', function($scope, $uibModalInstance){

        $scope.type = '';

        $scope.errorMsg = '';

        $scope.ok = function(){
          $scope.errorMsg = 'だめじゃん';
          // $uibModalInstance.close('来年の4月');
        };

        $scope.cancel = function(){
          $scope.errorMsg = '';
          $uibModalInstance.dismiss('cancel');
        };
      }])
      .controller('hameditor', ['$scope', '$http', '$timeout', function ($scope, $http, $timeout) {

        /**
         * Request endpoint
         *
         * @param {String} method
         * @param {String} endpoint
         * @param {Object} [data]
         * @returns {*}
         */
        function api(method, endpoint, data) {
          var request = {
            method : method,
            url    : wpApiSettings.root + endpoint,
            headers: {
              'X-WP-Nonce': wpApiSettings.nonce
            }
          };
          if (data) {
            switch (method) {
              case 'POST':
              case 'PUT':
                request.data = data;
                break;
              default:
                request.params = data;
                break;
            }
          }
          return $http(request);
        }

        /**
         * Show indicator
         */
        function start() {
          $('.hameditor__actions').addClass('hameditor__actions--loading');
        }

        /**
         * Hide indicator
         */
        function end() {
          $('.hameditor__actions').removeClass('hameditor__actions--loading');
        }

        /**
         * Show error message
         *
         * @param {Object} response
         */
        function errorHandler(response) {
          Hametuha.alert(response.data.message, true);
        }

        /**
         * Get initial data
         *
         * @param {Object} data
         * @returns {Object}
         */
        function postData(data){
          data.title = $scope.post.title;
          tinyMCE.activeEditor.save();
          data.content = $('#hamce').val();
          data.cat = {
            taxonomy: 'anpi_cat',
            term_id : $scope.post.cat
          };
          return data;
        }

        $scope.post = HameditorPost;

        HameditorPost.categories.forEach(function(option){
          if(option.active){
            $scope.post.cat = option.id;
          }
        });

        /**
         * Save post
         */
        $scope.save = function () {
          start();
          var now = (new Date()).toISOString();
          api('POST', 'wp/v2/' + $scope.post.type + '/' + $scope.post.id, postData({
            modified: now
          })).then(
            function(response){
              $scope.post.modified = now;
              $scope.post.url = response.data.link;
              Hametuha.alert('保存しました。', 'success', 2000);
            },
            errorHandler
          ).then(end);
        };

        /**
         * Publish post
         */
        $scope.publish = function () {
          Hametuha.confirm('公開してよろしいですか？', function () {
            start();
            var now = (new Date()).toISOString();
            api('POST', 'wp/v2/' + $scope.post.type + '/' + $scope.post.id, postData({
              status: 'publish',
              date  : now
            })).then(
              function (response) {
                $scope.post.status = 'publish';
                $scope.post.modified = now;
                $scope.post.date = now;
                $scope.post.url = response.data.link;
                Hametuha.alert('安否情報を公開しました！', 'success', 2000);
              },
              errorHandler
            ).then(end);
          });
        };

        /**
         * Make it private
         */
        $scope.private = function () {
          start();
          api('POST', 'wp/v2/' + $scope.post.type + '/' + $scope.post.id, postData({
            status: 'private'
          })).then(
            function (response) {
              $scope.post.status = 'private';
              $scope.post.url = response.data.link;
              Hametuha.alert('投稿を非公開にしました。', 'warning', 2000);
            },
            errorHandler
          ).then(end);
        };

        /**
         * Delete post
         *
         * @param {String} redirect
         */
        $scope.delete = function (redirect) {
          Hametuha.confirm('この投稿を削除してよろしいですか？ この操作は取り消せません。', function () {
            start();
            api('DELETE', 'wp/v2/' + $scope.post.type + '/' + $scope.post.id).then(
              function () {
                Hametuha.alert('削除しました。一覧に移動します。', 'warning', 2000);
                $timeout(function () {
                  window.location.href = redirect;
                }, 2000);
              },
              errorHandler
            ).then(end);
          }, true);
        };

      }]);

  });
})(jQuery);


