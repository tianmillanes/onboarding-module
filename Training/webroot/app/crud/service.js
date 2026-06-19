app.factory("Crud", function($resource) {
  return $resource(api + "cruds/:id", { id: '@id', search: '@search' }, {
    query:   { method: 'GET',  isArray: false },
    update:  { method: 'PUT' },
    approve: { method: 'POST', url: api + 'cruds/approve/:id', params: { id: '@id' } },
    reject:  { method: 'POST', url: api + 'cruds/reject/:id',  params: { id: '@id' } },
  });
});
