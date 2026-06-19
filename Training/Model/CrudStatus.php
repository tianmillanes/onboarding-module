<?php
App::uses('AppModel', 'Model');

class CrudStatus extends AppModel {
  public $useTable = 'crud_statuses';

  public $validate = array(
    'name' => array(
      'required' => array(
        'rule' => 'notBlank',
        'message' => 'Status name is required.'
      ),
      'unique' => array(
        'rule' => 'isUnique',
        'message' => 'This status already exists.'
      )
    )
  );
}
