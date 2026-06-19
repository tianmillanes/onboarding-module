<?php
App::uses('AppController', 'Controller');

class CrudStatusesController extends AppController {
  public $components = array('Paginator', 'RequestHandler');

  public function beforeFilter() {
    parent::beforeFilter();
    $this->RequestHandler->ext = 'json';
  }

  public $uses = array('CrudStatus');

  public function index() {
    $conditions = array('CrudStatus.visible' => true);

    if (isset($this->request->query['search']) && !empty($this->request->query['search'])) {
      $search = strtolower($this->request->query['search']);
      $conditions['LOWER(CrudStatus.name) LIKE'] = '%' . $search . '%';
    }

    $page = isset($this->request->query['page']) ? $this->request->query['page'] : 1;

    $this->paginate = array('CrudStatus' => array(
      'limit' => 25,
      'page'  => $page,
      'conditions' => $conditions,
      'order' => array('CrudStatus.id' => 'ASC')
    ));

    $tmpData = $this->paginate('CrudStatus');

    $statuses = array();
    if (!empty($tmpData)) {
      foreach ($tmpData as $val) {
        $statuses[] = array(
          'id'       => $val['CrudStatus']['id'],
          'name'     => $val['CrudStatus']['name'],
          'created'  => date('m/d/Y H:i:s', strtotime($val['CrudStatus']['created'])),
          'modified' => date('m/d/Y H:i:s', strtotime($val['CrudStatus']['modified'])),
        );
      }
    }

    $response = array(
      'ok'        => true,
      'msg'       => 'index',
      'data'      => $statuses,
      'paginator' => $this->request->params['paging']['CrudStatus']
    );

    $this->set(array(
      'response'   => $response,
      '_serialize' => 'response'
    ));
  }

  public function view($id = null) {
    $status = $this->CrudStatus->find('first', array(
      'conditions' => array(
        'CrudStatus.id'      => $id,
        'CrudStatus.visible' => true
      )
    ));

    $response = array(
      'ok'   => !empty($status),
      'data' => $status
    );

    $this->set(array(
      'response'   => $response,
      '_serialize' => 'response'
    ));
  }

  public function add() {
    $statusData = $this->request->data['CrudStatus'];
    $statusData['visible'] = true;
    if (isset($statusData['name'])) {
      $statusData['name'] = strtoupper(trim($statusData['name']));
    }

    // Check if a status with the same name already exists (including soft-deleted ones)
    $existing = $this->CrudStatus->find('first', array(
      'conditions' => array(
        'LOWER(CrudStatus.name)' => strtolower($statusData['name'])
      )
    ));

    if (!empty($existing)) {
      if (!$existing['CrudStatus']['visible']) {
        // If it exists but was soft-deleted, reactivate it!
        $this->CrudStatus->id = $existing['CrudStatus']['id'];
        if ($this->CrudStatus->save(array('visible' => true))) {
          $response = array(
            'ok'   => true,
            'msg'  => 'Status has been reactivated.',
            'data' => $this->CrudStatus->data
          );
        } else {
          $response = array(
            'ok'   => false,
            'msg'  => 'Could not reactivate status.'
          );
        }
      } else {
        // If it is already visible, return unique validation error
        $response = array(
          'ok'   => false,
          'msg'  => 'This status already exists.'
        );
      }
    } else {
      // Normal add flow
      $this->CrudStatus->create();
      if ($this->CrudStatus->save($statusData)) {
        $response = array(
          'ok'   => true,
          'msg'  => 'Status has been saved.',
          'data' => $this->CrudStatus->data
        );
      } else {
        $errors = $this->CrudStatus->validationErrors;
        $msg = 'Status could not be saved.';
        if (isset($errors['name'][0])) {
          $msg = $errors['name'][0];
        }
        $response = array(
          'ok'  => false,
          'msg' => $msg
        );
      }
    }

    $this->set(array(
      'response'   => $response,
      '_serialize' => 'response'
    ));
  }

  public function edit($id = null) {
    $statusData = $this->request->data['CrudStatus'];
    $statusData['id'] = $id;
    if (isset($statusData['name'])) {
      $statusData['name'] = strtoupper(trim($statusData['name']));
    }

    if ($this->CrudStatus->save($statusData)) {
      $response = array(
        'ok'   => true,
        'msg'  => 'Status has been updated.',
        'data' => $statusData
      );
    } else {
      $errors = $this->CrudStatus->validationErrors;
      $msg = 'Status could not be updated.';
      if (isset($errors['name'][0])) {
        $msg = $errors['name'][0];
      }
      $response = array(
        'ok'  => false,
        'msg' => $msg
      );
    }

    $this->set(array(
      'response'   => $response,
      '_serialize' => 'response'
    ));
  }

  public function delete($id = null) {
    $existing = $this->CrudStatus->find('first', array(
      'conditions' => array('CrudStatus.id' => $id)
    ));
    
    if (empty($existing)) {
      $response = array('ok' => false, 'msg' => 'Status not found.');
    } else {
      if ($this->CrudStatus->hide($id)) {
        $response = array(
          'ok'  => true,
          'msg' => 'Status has been deleted.'
        );
      } else {
        $response = array(
          'ok'  => false,
          'msg' => 'Status could not be deleted.'
        );
      }
    }

    $this->set(array(
      'response'   => $response,
      '_serialize' => 'response'
    ));
  }
}
