app.config(function($routeProvider) {
  var v = new Date().getTime();
  $routeProvider
  .when('/crud-status', {
    templateUrl: tmp + 'crud_statuses__index?v=' + v,
    controller: 'CrudStatusesController',
  });
});
