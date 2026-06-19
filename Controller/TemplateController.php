<?php
class TemplateController extends AppController {
  
  public $layout = null;
  public $autoRender = false;

  public function beforeFilter() {
    parent::beforeFilter();
    
  }
  
  public function afterFilter() {
    $view = str_replace('__', '/', $this->request->params['action']);
    $view = str_replace('_', '-', $view);
    $this->render($view);
  }
  
  // DASHBOARD
  public function dashboard__index() {}
  
  // users
  public function users__index() {}
  public function users__view() {}
  public function users__edit() {}
  public function users__add() {}

  // cruds
  public function cruds__index() {}
  public function cruds__view() {}
  public function cruds__edit() {}
  public function cruds__add() {}

  // crud statuses
  public function crud_statuses__index() {}

}
