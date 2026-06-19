app.controller('CrudStatusesController', function ($scope, CrudStatus) {

  $scope.strSearch = '';
  $scope.statuses = [];
  $scope.paginator = {};
  $scope.pages = [];

  $scope.load = function (options) {
    var params = { page: 1 };
    if ($scope.strSearch && $scope.strSearch.length > 0) {
      params.search = $scope.strSearch;
    }
    angular.extend(params, options || {});
    CrudStatus.query(params, function (e) {
      if (e.ok) {
        $scope.statuses = e.data;
        $scope.paginator = e.paginator;
        $scope.pages = paginator($scope.paginator, 5);
      }
    });
  };

  $scope.load();

  $scope.search = function () {
    $scope.load({ page: 1 });
  };

  $scope.goPage = function (page) {
    $scope.load({ page: page });
  };

  $scope.tmpStatus = { name: '' };
  $scope.modalMode = 'add';

  $scope.openModal = function (status) {
    $('#statusForm').validationEngine('hideAll');
    if (status) {
      $scope.modalMode = 'edit';
      $scope.tmpStatus = angular.copy(status);
    } else {
      $scope.modalMode = 'add';
      $scope.tmpStatus = { name: '' };
    }
    $('#statusModal').modal('show');
  };

  $scope.saving = false;
  $scope.saveStatus = function () {
    if ($scope.saving) return;
    var valid = $('#statusForm').validationEngine('validate');
    if (valid) {
      $scope.saving = true;
      $('#apple-loading-modal').addClass('active');
      
      setTimeout(function () {
        if ($scope.modalMode === 'add') {
          CrudStatus.save({ CrudStatus: $scope.tmpStatus }, function (e) {
            $scope.saving = false;
            $('#apple-loading-modal').removeClass('active');
            if (e.ok) {
              $.gritter.add({ title: 'Successful!', text: e.msg });
              $('#statusModal').modal('hide');
              $scope.load();
            } else {
              $.gritter.add({ title: 'Warning!', text: e.msg });
            }
          }, function () {
            $scope.saving = false;
            $('#apple-loading-modal').removeClass('active');
            $.gritter.add({ title: 'Error!', text: 'An error occurred while saving.' });
          });
        } else {
          CrudStatus.update({ id: $scope.tmpStatus.id }, { CrudStatus: $scope.tmpStatus }, function (e) {
            $scope.saving = false;
            $('#apple-loading-modal').removeClass('active');
            if (e.ok) {
              $.gritter.add({ title: 'Successful!', text: e.msg });
              $('#statusModal').modal('hide');
              $scope.load();
            } else {
              $.gritter.add({ title: 'Warning!', text: e.msg });
            }
          }, function () {
            $scope.saving = false;
            $('#apple-loading-modal').removeClass('active');
            $.gritter.add({ title: 'Error!', text: 'An error occurred while updating.' });
          });
        }
      }, 50);
    }
  };

  $scope.remove = function (status) {

    bootbox.confirm('Are you sure you want to delete status "' + status.name + '"?', function (c) {
      if (c) {
        CrudStatus.remove({ id: status.id }, function (e) {
          if (e.ok) {
            $.gritter.add({ title: 'Successful!', text: e.msg });
            $scope.load();
          } else {
            $.gritter.add({ title: 'Warning!', text: e.msg });
          }
        });
      }
    });
  };
});
