app.config(function($routeProvider) {
  var v = new Date().getTime();
  $routeProvider
  .when('/crud', {
    templateUrl: tmp + 'cruds__index?v=' + v,
    controller: 'CrudsController',
  })
  .when('/crud/add', {
    templateUrl: tmp + 'cruds__add?v=' + v,
    controller: 'CrudsAddController',
  })
  .when('/crud/edit/:id', {
    templateUrl: tmp + 'cruds__edit?v=' + v,
    controller: 'CrudsEditController',
  })
  .when('/crud/view/:id', {
    templateUrl: tmp + 'cruds__view?v=' + v,
    controller: 'CrudsViewController',
  })
  ;
});
