<?php
App::uses('AppModel', 'Model');
class CrudBeneficiary extends AppModel {
  public $useTable = 'crud_beneficiaries';
  public $virtualFields = array(
    'age' => 'TIMESTAMPDIFF(YEAR, CrudBeneficiary.birth_date, CURDATE())'
  );
}
