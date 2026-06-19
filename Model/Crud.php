<?php
App::uses('AppModel', 'Model');
class Crud extends AppModel {
  public $virtualFields = array(
    'age' => 'TIMESTAMPDIFF(YEAR, Crud.birth_date, CURDATE())'
  );

  public $hasMany = array(
    'CrudBeneficiary' => array(
      'className' => 'CrudBeneficiary',
      'foreignKey' => 'crud_id',
      'dependent' => true
    )
  );
}
