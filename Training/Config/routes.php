<?php

  Router::connect('/', array(
    'controller' => 'main',
    'action'     => 'index',
  ));

  Router::connect('/login', array(
    'controller' => 'main',
    'action' => 'login',
  ));

  Router::connect('/logout', array(
    'controller' => 'main',
    'action'     => 'logout'
  ));

  // Custom CRUD actions MUST come BEFORE mapResources so they are matched first
  Router::connect('/api/cruds/approve/:id',
    array('controller' => 'cruds', 'action' => 'approve', 'prefix' => 'api', '[method]' => 'POST'),
    array('pass' => array('id'))
  );
  Router::connect('/api/cruds/reject/:id',
    array('controller' => 'cruds', 'action' => 'reject',   'prefix' => 'api', '[method]' => 'POST'),
    array('pass' => array('id'))
  );
  Router::connect('/api/cruds/print_pdf',
    array('controller' => 'cruds', 'action' => 'print_pdf', 'prefix' => 'api', '[method]' => 'GET')
  );
  Router::connect('/api/cruds/print_record/:id/*',
    array('controller' => 'cruds', 'action' => 'print_record', 'prefix' => 'api', '[method]' => 'GET'),
    array('pass' => array('id'))
  );
  Router::connect('/api/cruds/upload_file',
    array('controller' => 'cruds', 'action' => 'upload_file', 'prefix' => 'api', '[method]' => 'POST')
  );
  Router::connect('/api/cruds/delete_file/:id',
    array('controller' => 'cruds', 'action' => 'delete_file', 'prefix' => 'api', '[method]' => 'POST'),
    array('pass' => array('id'))
  );

  // api resources
  $resources = array(
    'users','select',
    'cruds', 'crud_statuses', 'assigns', 'names', 'suppliers', 'shipments', 'equips', 'trucks'
  );

  Router::mapResources($resources, array('prefix' => 'api'));
  Router::parseExtensions('json');

CakePlugin::routes();
require CAKE . 'Config' . DS . 'routes.php';
