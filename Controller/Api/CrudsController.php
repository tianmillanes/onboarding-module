<?php

App::uses('AppController', 'Controller');

class CrudsController extends AppController {

  public $components = array('Paginator', 'RequestHandler');

  public function beforeFilter() {
    parent::beforeFilter();
    $this->RequestHandler->ext = 'json';
  }


  public $uses = array('Crud');

  private function _buildConditions($query) {
    $conditions = array('Crud.visible' => true);

    // Basic keyword search
    if (isset($query['search']) && !empty($query['search'])) {
      $search = strtolower($query['search']);
      $conditions['OR'] = array(
        'LOWER(Crud.first_name) LIKE'    => '%' . $search . '%',
        'LOWER(Crud.middle_name) LIKE'   => '%' . $search . '%',
        'LOWER(Crud.last_name) LIKE'     => '%' . $search . '%',
        'LOWER(Crud.contact_number) LIKE'=> '%' . $search . '%',
        'LOWER(Crud.email) LIKE'         => '%' . $search . '%',
        'LOWER(Crud.address) LIKE'       => '%' . $search . '%',
      );
    }

    // Advanced Search: Status filter
    if (isset($query['status']) && !empty($query['status']) && $query['status'] !== 'ALL') {
      $conditions['Crud.status'] = $query['status'];
    }

    // Advanced Search: First Name filter
    if (isset($query['search_first_name']) && !empty($query['search_first_name'])) {
      $firstName = strtolower(trim($query['search_first_name']));
      $conditions['LOWER(Crud.first_name) LIKE'] = '%' . $firstName . '%';
    }

    // Advanced Search: Middle Name filter
    if (isset($query['search_middle_name']) && !empty($query['search_middle_name'])) {
      $middleName = strtolower(trim($query['search_middle_name']));
      $conditions['LOWER(Crud.middle_name) LIKE'] = '%' . $middleName . '%';
    }

    // Advanced Search: Last Name filter
    if (isset($query['search_last_name']) && !empty($query['search_last_name'])) {
      $lastName = strtolower(trim($query['search_last_name']));
      $conditions['LOWER(Crud.last_name) LIKE'] = '%' . $lastName . '%';
    }

    // Advanced Search: Birth Date filter
    if (isset($query['search_birth_date']) && !empty($query['search_birth_date'])) {
      $birthDate = date('Y-m-d', strtotime($query['search_birth_date']));
      $conditions['Crud.birth_date'] = $birthDate;
    }

    // Advanced Search: Age filter
    if (isset($query['search_age']) && !empty($query['search_age'])) {
      $conditions['Crud.age'] = intval($query['search_age']);
    }

    // Advanced Search: Contact Number filter
    if (isset($query['search_contact_number']) && !empty($query['search_contact_number'])) {
      $contact = strtolower(trim($query['search_contact_number']));
      $conditions['LOWER(Crud.contact_number) LIKE'] = '%' . $contact . '%';
    }

    // Advanced Search: Email filter
    if (isset($query['search_email']) && !empty($query['search_email'])) {
      $email = strtolower(trim($query['search_email']));
      $conditions['LOWER(Crud.email) LIKE'] = '%' . $email . '%';
    }

    // Advanced Search: Address filter
    if (isset($query['search_address']) && !empty($query['search_address'])) {
      $address = strtolower(trim($query['search_address']));
      $conditions['LOWER(Crud.address) LIKE'] = '%' . $address . '%';
    }

    return $conditions;
  }

  public function index() {
    $page = isset($this->request->query['page']) ? $this->request->query['page'] : 1;
    $conditions = $this->_buildConditions($this->request->query);

    $this->paginate = array('Crud' => array(
      'limit' => 51,
      'page'  => $page,

      'conditions' => $conditions,
      'order' => array('Crud.id' => 'ASC')
    ));

    $tmpData = $this->paginate('Crud');

    $cruds = array();
    if (!empty($tmpData)) {
      foreach ($tmpData as $val) {
        $cruds[] = array(
          'id'             => $val['Crud']['id'],
          'first_name'     => $val['Crud']['first_name'],
          'middle_name'    => $val['Crud']['middle_name'],
          'last_name'      => $val['Crud']['last_name'],
          'contact_number' => $val['Crud']['contact_number'],
          'email'          => $val['Crud']['email'],
          'address'        => $val['Crud']['address'],
          'birth_date'     => $this->fDate($val['Crud']['birth_date']),
          'age'            => $val['Crud']['age'],
          'status'         => isset($val['Crud']['status']) ? $val['Crud']['status'] : 'PENDING',
          'created'        => date('m/d/Y H:i:s', strtotime($val['Crud']['created'])),
          'modified'       => date('m/d/Y H:i:s', strtotime($val['Crud']['modified'])),
        );
      }
    }

    $response = array(
      'ok'        => true,
      'msg'       => 'index',
      'data'      => $cruds,
      'paginator' => $this->request->params['paging']['Crud']
    );

    $this->set(array(
      'response'    => $response,
      '_serialize'  => 'response'
    ));
  }

  public function add() {
    $crud = $this->request->data['Crud'];
    if (isset($crud['birth_date']) && !empty($crud['birth_date'])) {
      $crud['birth_date'] = $this->mDate($crud['birth_date']);
    }
    $crud['visible'] = true;
    if (!isset($crud['status']) || empty($crud['status'])) {
      $crud['status']  = 'PENDING';
    }

    // Email format validation (Person only)
    if (isset($crud['email']) && !empty($crud['email'])) {
      if (!filter_var($crud['email'], FILTER_VALIDATE_EMAIL)) {
        $response = array(
          'ok'  => false,
          'msg' => 'Please provide a valid email address.'
        );
        $this->set(array('response' => $response, '_serialize' => 'response'));
        return;
      }
    }

    // Unique email check
    if (isset($crud['email']) && !empty($crud['email'])) {
      $existing = $this->Crud->find('first', array(
        'conditions' => array(
          'LOWER(Crud.email)' => strtolower(trim($crud['email'])),
          'Crud.visible'      => true
        )
      ));
      if (!empty($existing)) {
        $response = array(
          'ok'  => false,
          'msg' => 'The email address "' . $crud['email'] . '" is already in use by another record.'
        );
        $this->set(array('response' => $response, '_serialize' => 'response'));
        return;
      }
    }

    if ($this->Crud->save($crud)) {
      $crudId = $this->Crud->id;
      if (isset($this->request->data['Beneficiaries']) && is_array($this->request->data['Beneficiaries'])) {
        $this->loadModel('CrudBeneficiary');
        foreach ($this->request->data['Beneficiaries'] as $beneficiary) {
          $beneficiary['crud_id'] = $crudId;
          if (isset($beneficiary['birth_date']) && !empty($beneficiary['birth_date'])) {
            $beneficiary['birth_date'] = $this->mDate($beneficiary['birth_date']);
          }
          $beneficiary['visible'] = true;
          $this->CrudBeneficiary->create();
          $this->CrudBeneficiary->save($beneficiary);
        }
      }

      // Save associated files
      if (isset($this->request->data['Files']) && is_array($this->request->data['Files'])) {
        $this->loadModel('CrudFile');
        foreach ($this->request->data['Files'] as $fileData) {
          $this->CrudFile->create();
          $this->CrudFile->save(array(
            'crud_id'  => $crudId,
            'filename' => $fileData['filename'],
            'filepath' => $fileData['filepath'],
            'visible'  => true
          ));
        }
      }
      
      // Send email notification on add
      $fullName = $crud['first_name'] . ' ' . $crud['last_name'];
      
      $emailBody = "Dear " . $fullName . ",\n\n";
      if (isset($crud['status']) && strtoupper($crud['status']) === 'APPROVED') {
        $emailBody .= "Congratulations: " . $fullName . "! Your account has just been created.\n\n";
      } else if (isset($crud['status']) && strtoupper($crud['status']) === 'DISAPPROVED') {
        $emailBody .= "We regret to inform you that your personal record registration status has been DISAPPROVED at this time. Please contact our support team or login to check the requirements for updating your record.\n\n";
      } else {
        $emailBody .= "Congratulations! Your personal record has been successfully created in our system. Please expect 1-3 working days for the processing and verification of your record status.\n\n";
      }
      $emailBody .= "Below are the details registered in your account:\n";
      $emailBody .= "--------------------------------------------------\n";
      $emailBody .= "Full Name      : " . strtoupper($crud['first_name'] . ' ' . (isset($crud['middle_name']) ? $crud['middle_name'] : '') . ' ' . $crud['last_name']) . "\n";
      $emailBody .= "Contact Number : " . (isset($crud['contact_number']) ? $crud['contact_number'] : '-') . "\n";
      $emailBody .= "Email Address  : " . (isset($crud['email']) ? $crud['email'] : '-') . "\n";
      $emailBody .= "Birth Date     : " . (isset($crud['birth_date']) ? $this->fDate($crud['birth_date']) : '-') . "\n";
      $emailBody .= "Age            : " . (isset($crud['age']) ? $crud['age'] : '-') . " yrs old\n";
      $emailBody .= "Home Address   : " . (isset($crud['address']) ? $crud['address'] : '-') . "\n";
      $emailBody .= "Record Status  : " . strtoupper(isset($crud['status']) ? $crud['status'] : 'PENDING') . "\n";
      $emailBody .= "--------------------------------------------------\n";
      
      if (isset($this->request->data['Beneficiaries']) && is_array($this->request->data['Beneficiaries']) && !empty($this->request->data['Beneficiaries'])) {
        $emailBody .= "\nRegistered Beneficiaries:\n";
        $emailBody .= "--------------------------------------------------\n";
        foreach ($this->request->data['Beneficiaries'] as $i => $beneficiary) {
          $bName = strtoupper($beneficiary['first_name'] . ' ' . (isset($beneficiary['middle_name']) ? $beneficiary['middle_name'] : '') . ' ' . $beneficiary['last_name']);
          $emailBody .= ($i + 1) . ". " . $bName . "\n";
          $emailBody .= "   Relationship : " . strtoupper($beneficiary['relationship']) . "\n";
          $emailBody .= "   Email        : " . (isset($beneficiary['email']) && !empty($beneficiary['email']) ? $beneficiary['email'] : '-') . "\n";
          $emailBody .= "   Birth Date   : " . (isset($beneficiary['birth_date']) && !empty($beneficiary['birth_date']) ? $beneficiary['birth_date'] : '-') . "\n";
          $emailBody .= "   Age          : " . (isset($beneficiary['age']) ? $beneficiary['age'] : '-') . " yrs old\n\n";
        }
        $emailBody .= "--------------------------------------------------\n";
      }
      
      $emailBody .= "\nIf you find any incorrect information in the details listed above, please contact our support team immediately.\n\n";
      $emailBody .= "Best Regards,\n";
      $emailBody .= "E D & C Solutions";

      $this->_sendEmailNotification(
        isset($crud['email']) ? $crud['email'] : null,
        "Record Registration Created - " . $fullName,
        $emailBody
      );

      $response = array(
        'ok'  => true,
        'msg' => 'Personal record has been saved.',
        'data' => $crud
      );
    } else {
      $response = array(
        'ok'  => false,
        'msg' => 'Personal record could not be saved.'
      );
    }

    $this->set(array(
      'response'    => $response,
      '_serialize'  => 'response'
    ));
  }

  public function view($id = null) {
    $crud = $this->Crud->find('first', array(
      'conditions' => array(
        'Crud.id'      => $id,
        'Crud.visible' => true
      )
    ));

    if (!empty($crud)) {
      $crud['Crud']['birth_date'] = $this->fDate($crud['Crud']['birth_date']);
      $this->loadModel('CrudBeneficiary');
      $beneficiaries = $this->CrudBeneficiary->find('all', array(
        'conditions' => array(
          'CrudBeneficiary.crud_id' => $id,
          'CrudBeneficiary.visible' => true
        )
      ));
      $crud['Beneficiaries'] = array();
      if (!empty($beneficiaries)) {
        foreach ($beneficiaries as $val) {
          $crud['Beneficiaries'][] = array(
            'id'           => $val['CrudBeneficiary']['id'],
            'crud_id'      => $val['CrudBeneficiary']['crud_id'],
            'first_name'   => $val['CrudBeneficiary']['first_name'],
            'middle_name'  => $val['CrudBeneficiary']['middle_name'],
            'last_name'    => $val['CrudBeneficiary']['last_name'],
            'relationship' => $val['CrudBeneficiary']['relationship'],
            'email'        => isset($val['CrudBeneficiary']['email']) ? $val['CrudBeneficiary']['email'] : '',
            'birth_date'   => $this->fDate($val['CrudBeneficiary']['birth_date']),
            'age'          => $val['CrudBeneficiary']['age']
          );
        }
      }

      // Load Files
      $this->loadModel('CrudFile');
      $files = $this->CrudFile->find('all', array(
        'conditions' => array(
          'CrudFile.crud_id' => $id,
          'CrudFile.visible' => true
        )
      ));
      $crud['Files'] = array();
      if (!empty($files)) {
        foreach ($files as $val) {
          $crud['Files'][] = array(
            'id'       => $val['CrudFile']['id'],
            'crud_id'  => $val['CrudFile']['crud_id'],
            'filename' => $val['CrudFile']['filename'],
            'filepath' => $val['CrudFile']['filepath']
          );
        }
      }
    }

    $response = array(
      'ok'   => !empty($crud),
      'data' => $crud
    );

    $this->set(array(
      'response'    => $response,
      '_serialize'  => 'response'
    ));
  }

  public function edit($id = null) {
    // Enforce business logic: Locked records (APPROVED/DISAPPROVED) cannot be edited
    $existingRecord = $this->Crud->find('first', array(
      'conditions' => array('Crud.id' => $id, 'Crud.visible' => true)
    ));
    if (empty($existingRecord)) {
      $response = array('ok' => false, 'msg' => 'Record not found.');
      $this->set(array('response' => $response, '_serialize' => 'response'));
      return;
    }
    $status = isset($existingRecord['Crud']['status']) ? $existingRecord['Crud']['status'] : 'PENDING';
    if ($status === 'APPROVED' || $status === 'DISAPPROVED') {
      $response = array('ok' => false, 'msg' => 'Locked records (Approved/Disapproved) cannot be updated.');
      $this->set(array('response' => $response, '_serialize' => 'response'));
      return;
    }

    $crud = $this->request->data['Crud'];
    $crud['id'] = $id;
    if (isset($crud['birth_date']) && !empty($crud['birth_date'])) {
      $crud['birth_date'] = $this->mDate($crud['birth_date']);
    }

    // Email format validation (Person only)
    if (isset($crud['email']) && !empty($crud['email'])) {
      if (!filter_var($crud['email'], FILTER_VALIDATE_EMAIL)) {
        $response = array(
          'ok'  => false,
          'msg' => 'Please provide a valid email address.'
        );
        $this->set(array('response' => $response, '_serialize' => 'response'));
        return;
      }
    }

    // Unique email check (exclude the current record itself)
    if (isset($crud['email']) && !empty($crud['email'])) {
      $emailExists = $this->Crud->find('first', array(
        'conditions' => array(
          'LOWER(Crud.email)' => strtolower(trim($crud['email'])),
          'Crud.visible'      => true,
          'Crud.id !='        => $id
        )
      ));
      if (!empty($emailExists)) {
        $response = array(
          'ok'  => false,
          'msg' => 'The email address "' . $crud['email'] . '" is already in use by another record.'
        );
        $this->set(array('response' => $response, '_serialize' => 'response'));
        return;
      }
    }

    if ($this->Crud->save($crud)) {
      $this->loadModel('CrudBeneficiary');
      
      $existing = $this->CrudBeneficiary->find('list', array(
        'conditions' => array(
          'CrudBeneficiary.crud_id' => $id,
          'CrudBeneficiary.visible' => true
        ),
        'fields' => array('CrudBeneficiary.id', 'CrudBeneficiary.id')
      ));
      
      $keepIds = array();
      if (isset($this->request->data['Beneficiaries']) && is_array($this->request->data['Beneficiaries'])) {
        foreach ($this->request->data['Beneficiaries'] as $beneficiary) {
          $beneficiary['crud_id'] = $id;
          if (isset($beneficiary['birth_date']) && !empty($beneficiary['birth_date'])) {
            $beneficiary['birth_date'] = $this->mDate($beneficiary['birth_date']);
          }
          $beneficiary['visible'] = true;
          
          if (isset($beneficiary['id']) && !empty($beneficiary['id'])) {
            $keepIds[] = $beneficiary['id'];
            $this->CrudBeneficiary->id = $beneficiary['id'];
          } else {
            $this->CrudBeneficiary->create();
          }
          
          if ($this->CrudBeneficiary->save($beneficiary)) {
            if (!isset($beneficiary['id'])) {
              $keepIds[] = $this->CrudBeneficiary->id;
            }
          }
        }
      }
      
      $removeIds = array_diff($existing, $keepIds);
      if (!empty($removeIds)) {
        $this->CrudBeneficiary->updateAll(
          array('CrudBeneficiary.visible' => false),
          array('CrudBeneficiary.id' => $removeIds)
        );
      }

      // Save associated files
      if (isset($this->request->data['Files']) && is_array($this->request->data['Files'])) {
        $this->loadModel('CrudFile');
        foreach ($this->request->data['Files'] as $fileData) {
          if (!isset($fileData['id'])) {
            $this->CrudFile->create();
            $this->CrudFile->save(array(
              'crud_id'  => $id,
              'filename' => $fileData['filename'],
              'filepath' => $fileData['filepath'],
              'visible'  => true
            ));
          }
        }
      }

      $oldStatus = isset($existingRecord['Crud']['status']) ? $existingRecord['Crud']['status'] : 'PENDING';
      $newStatus = isset($crud['status']) ? $crud['status'] : $oldStatus;
      
      if (strtoupper($oldStatus) !== strtoupper($newStatus)) {
        $fullName = $existingRecord['Crud']['first_name'] . ' ' . $existingRecord['Crud']['last_name'];
        if (isset($crud['first_name']) && isset($crud['last_name'])) {
          $fullName = $crud['first_name'] . ' ' . $crud['last_name'];
        }
        
        $emailBody = "Dear " . $fullName . ",\n\n";
        $emailBody .= "We are writing to inform you that your personal record status has been updated.\n\n";
        $emailBody .= "--------------------------------------------------\n";
        $emailBody .= "Previous Status : " . strtoupper($oldStatus) . "\n";
        $emailBody .= "New Status      : " . strtoupper($newStatus) . "\n";
        $emailBody .= "--------------------------------------------------\n\n";
        $emailBody .= "If you have any questions or require further assistance, please contact our support team.\n\n";
        $emailBody .= "Best Regards,\n";
        $emailBody .= "E D & C Solutions";
        
        $recipientEmail = isset($crud['email']) ? $crud['email'] : (isset($existingRecord['Crud']['email']) ? $existingRecord['Crud']['email'] : null);
        
        $this->_sendEmailNotification(
          $recipientEmail,
          "Record Status Updated - " . $fullName,
          $emailBody
        );
      }

      $response = array(
        'ok'  => true,
        'msg' => 'Personal record has been updated.',
        'data' => $crud
      );
    } else {
      $response = array(
        'ok'  => false,
        'msg' => 'Personal record could not be updated.'
      );
    }

    $this->set(array(
      'response'    => $response,
      '_serialize'  => 'response'
    ));
  }

  public function delete($id = null) {
    // Enforce business logic: Locked records (APPROVED/DISAPPROVED) cannot be deleted
    $existingRecord = $this->Crud->find('first', array(
      'conditions' => array('Crud.id' => $id, 'Crud.visible' => true)
    ));
    if (empty($existingRecord)) {
      $response = array('ok' => false, 'msg' => 'Record not found.');
      $this->set(array('response' => $response, '_serialize' => 'response'));
      return;
    }
    $status = isset($existingRecord['Crud']['status']) ? $existingRecord['Crud']['status'] : 'PENDING';
    if ($status === 'APPROVED' || $status === 'DISAPPROVED') {
      $response = array('ok' => false, 'msg' => 'Locked records (Approved/Disapproved) cannot be deleted.');
      $this->set(array('response' => $response, '_serialize' => 'response'));
      return;
    }

    if ($this->Crud->hide($id)) {
      // 1. Delete associated beneficiaries physically
      $this->loadModel('CrudBeneficiary');
      $this->CrudBeneficiary->deleteAll(
        array('CrudBeneficiary.crud_id' => $id),
        false
      );

      // 2. Delete associated file attachments physically from both disk and database
      $this->loadModel('CrudFile');
      $files = $this->CrudFile->find('all', array(
        'conditions' => array('CrudFile.crud_id' => $id)
      ));
      if (!empty($files)) {
        foreach ($files as $file) {
          $filePath = WWW_ROOT . $file['CrudFile']['filepath'];
          if (file_exists($filePath)) {
            @unlink($filePath);
          }
        }
        $this->CrudFile->deleteAll(
          array('CrudFile.crud_id' => $id),
          false
        );
      }

      $response = array(
        'ok'  => true,
        'msg' => 'Personal record has been deleted.'
      );
    } else {
      $response = array(
        'ok'  => false,
        'msg' => 'Personal record could not be deleted.'
      );
    }

    $this->set(array(
      'response'    => $response,
      '_serialize'  => 'response'
    ));
  }

  // APPROVE action — sets status to APPROVED
  // Named api_approve because CakePHP 2.x prefix routing requires api_ prefix
  public function api_approve($id = null) {
    $crud = $this->Crud->find('first', array(
      'conditions' => array('Crud.id' => $id, 'Crud.visible' => true)
    ));

    $result = $this->Crud->updateAll(
      array('Crud.status' => "'APPROVED'"),
      array('Crud.id' => $id, 'Crud.visible' => true)
    );

    if ($result) {
      if (!empty($crud)) {
        $fullName = $crud['Crud']['first_name'] . ' ' . $crud['Crud']['last_name'];
        
        $emailBody = "Dear " . $fullName . ",\n\n";
        $emailBody .= "Congratulations! We are pleased to inform you that your personal record and registration status have been successfully APPROVED.\n\n";
        $emailBody .= "Best Regards,\n";
        $emailBody .= "E D & C Solutions";

        $this->_sendEmailNotification(
          isset($crud['Crud']['email']) ? $crud['Crud']['email'] : null,
          "Record Registration Approved - " . $fullName,
          $emailBody
        );
      }
      $response = array('ok' => true,  'msg' => 'Record has been approved.');
    } else {
      $response = array('ok' => false, 'msg' => 'Could not approve the record.');
    }

    $this->set(array('response' => $response, '_serialize' => 'response'));
  }

  // DISAPPROVE action — sets status to DISAPPROVED
  // Named api_reject because CakePHP 2.x prefix routing requires api_ prefix
  public function api_reject($id = null) {
    $crud = $this->Crud->find('first', array(
      'conditions' => array('Crud.id' => $id, 'Crud.visible' => true)
    ));

    $result = $this->Crud->updateAll(
      array('Crud.status' => "'DISAPPROVED'"),
      array('Crud.id' => $id, 'Crud.visible' => true)
    );

    if ($result) {
      if (!empty($crud)) {
        $fullName = $crud['Crud']['first_name'] . ' ' . $crud['Crud']['last_name'];
        
        $emailBody = "Dear " . $fullName . ",\n\n";
        $emailBody .= "We regret to inform you that your personal record registration status has been DISAPPROVED at this time.\n\n";
        $emailBody .= "Please contact our support team or login to check the requirements for updating your record.\n\n";
        $emailBody .= "Best Regards,\n";
        $emailBody .= "E D & C Solutions";

        $this->_sendEmailNotification(
          isset($crud['Crud']['email']) ? $crud['Crud']['email'] : null,
          "Record Registration Disapproved - " . $fullName,
          $emailBody
        );
      }
      $response = array('ok' => true,  'msg' => 'Record has been disapproved.');
    } else {
      $response = array('ok' => false, 'msg' => 'Could not disapprove the record.');
    }

    $this->set(array('response' => $response, '_serialize' => 'response'));
  }

  private function _sendEmailNotification($recipient, $subject, $body) {
    App::uses('CakeEmail', 'Network/Email');
    try {
      $email = new CakeEmail('smtp');
      $config = $email->config();
      
      // Dynamic recipient setup
      if (empty($recipient)) {
        $recipient = 'christianllamesmillanes@gmail.com';
      }
      
      // Check if SMTP password is still placeholder
      if (isset($config['password']) && $config['password'] === 'YOUR_GMAIL_APP_PASSWORD') {
        $email->transport('Debug');
        $this->log("Email SMTP password not configured yet. Falling back to Debug transport.", 'warning');
      }
      
      $email->to($recipient);
      
      // BCC the user's personal email for copies of notifications
      if (strtolower(trim($recipient)) !== 'christianllamesmillanes@gmail.com') {
        $email->bcc('christianllamesmillanes@gmail.com');
      }
      
      $email->subject($subject);
      $result = $email->send($body);
      $this->log("Email Sent to {$recipient}:\n" . $result['headers'] . "\n\n" . $result['message'], 'debug');
      return true;
    } catch (Exception $e) {
      $this->log('Email sending failed: ' . $e->getMessage(), 'error');
      $this->log("Email content (could not send): To: {$recipient} | Subject: {$subject}\n{$body}", 'error');
      return false;
    }
  }

  public function api_print_pdf() {
    $this->autoRender = false;
    $this->RequestHandler->enabled = false;

    $conditions = $this->_buildConditions($this->request->query);

    $tmpData = $this->Crud->find('all', array(
      'conditions' => $conditions,
      'order'      => array('Crud.id' => 'ASC')
    ));

    require_once(APP . 'lib' . DS . 'fpdf' . DS . 'ReportPdf.php');

    $pdf = new ReportPdf('L', 'mm', 'A4');
    $pdf->SetMargins(12, 42, 12);
    $pdf->setDocumentTitle('PERSONAL DATA DIRECTORY', 'Official Registry Report');
    $pdf->AddPage();

    $statusFilter = isset($this->request->query['status']) && !empty($this->request->query['status'])
      ? $this->request->query['status']
      : 'ALL';

    $metaRows = array(
      array('Report Date', date('F d, Y')),
      array('Report Time', date('h:i A')),
      array('Status Filter', $statusFilter),
      array('Total Records', count($tmpData))
    );

    if (isset($this->request->query['search']) && !empty($this->request->query['search'])) {
      $metaRows[] = array('Keyword', $this->request->query['search']);
    }
    if (isset($this->request->query['search_name']) && !empty($this->request->query['search_name'])) {
      $metaRows[] = array('Name Filter', $this->request->query['search_name']);
    }
    if (isset($this->request->query['search_email']) && !empty($this->request->query['search_email'])) {
      $metaRows[] = array('Email Filter', $this->request->query['search_email']);
    }
    if (isset($this->request->query['search_age']) && !empty($this->request->query['search_age'])) {
      $metaRows[] = array('Age Filter', $this->request->query['search_age']);
    }
    if (isset($this->request->query['search_created']) && !empty($this->request->query['search_created'])) {
      $metaRows[] = array('Created Filter', $this->request->query['search_created']);
    }
    if (isset($this->request->query['search_modified']) && !empty($this->request->query['search_modified'])) {
      $metaRows[] = array('Modified Filter', $this->request->query['search_modified']);
    }

    $pdf->drawMetaBox($metaRows);

    $colWidths = array(8, 25, 25, 25, 22, 10, 25, 45, 70, 22);
    $headers   = array('#', 'FIRST NAME', 'MIDDLE NAME', 'LAST NAME', 'BIRTH DATE', 'AGE', 'CONTACT', 'EMAIL', 'ADDRESS', 'STATUS');
    $aligns    = array('C', 'L', 'L', 'L', 'C', 'C', 'L', 'L', 'L', 'C');

    $pdf->beginTable($headers, $colWidths, $aligns);

    foreach ($tmpData as $i => $val) {
      $status = isset($val['Crud']['status']) ? $val['Crud']['status'] : 'PENDING';

      $pdf->drawTableRow(array(
        $i + 1,
        strtoupper($pdf->truncate($val['Crud']['first_name'], 14)),
        strtoupper($pdf->truncate($val['Crud']['middle_name'], 14)),
        strtoupper($pdf->truncate($val['Crud']['last_name'], 14)),
        $this->fDate($val['Crud']['birth_date']),
        $val['Crud']['age'],
        $val['Crud']['contact_number'],
        $pdf->truncate($val['Crud']['email'], 26),
        $pdf->truncate($val['Crud']['address'], 42),
        $status
      ), $i);
    }

    $pdf->endTable();

    $tmpFile  = tempnam(sys_get_temp_dir(), 'crud_pdf_');
    $pdf->Output($tmpFile, 'F');

    $prevError = ini_get('display_errors');
    ini_set('display_errors', 0);

    while (ob_get_level() > 0) {
      ob_end_clean();
    }

    $filename = 'PersonalDataDirectory_' . date('Ymd') . '.pdf';
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($tmpFile));
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');

    readfile($tmpFile);
    unlink($tmpFile);

    ini_set('display_errors', $prevError);
    exit;
  }

  public function api_print_record($id = null) {
    $this->autoRender = false;
    $this->RequestHandler->enabled = false;

    $crud = $this->Crud->find('first', array(
      'conditions' => array(
        'Crud.id'      => $id,
        'Crud.visible' => true
      )
    ));

    if (empty($crud) || $crud['Crud']['status'] !== 'APPROVED') {
      throw new NotFoundException('Record not found or is not approved.');
    }

    $this->loadModel('CrudBeneficiary');
    $beneficiaries = $this->CrudBeneficiary->find('all', array(
      'conditions' => array(
        'CrudBeneficiary.crud_id' => $id,
        'CrudBeneficiary.visible' => true
      )
    ));

    require_once(APP . 'lib' . DS . 'fpdf' . DS . 'ReportPdf.php');

    $fullName = strtoupper(trim(
      $crud['Crud']['first_name'] . ' ' . $crud['Crud']['middle_name'] . ' ' . $crud['Crud']['last_name']
    ));

    $pdf = new ReportPdf('P', 'mm', 'A4');
    $pdf->SetMargins(15, 42, 15);
    $pdf->setDocumentTitle('PERSONAL RECORD REPORT', 'Individual Record Certification');
    $pdf->AddPage();

    $pdf->drawApprovalStamp('APPROVED');

    $pdf->drawMetaBox(array(
      array('Record ID', 'REC-' . str_pad($id, 5, '0', STR_PAD_LEFT)),
      array('Subject Name', $fullName),
      array('Report Date', date('F d, Y')),
      array('Report Time', date('h:i A'))
    ));

    $pdf->drawSectionHeader('1', 'PERSONAL INFORMATION');

    $pdf->drawDetailRows(array(
      array('Full Name', $fullName),
      array('Birth Date', $this->fDate($crud['Crud']['birth_date'])),
      array('Age', $crud['Crud']['age'] . ' years old'),
      array('Contact Number', $crud['Crud']['contact_number']),
      array('Email Address', $crud['Crud']['email']),
      array('Home Address', $crud['Crud']['address'], 'multiline')
    ));

    $pdf->Ln(6);
    $pdf->drawSectionHeader('2', 'REGISTERED BENEFICIARIES');

    if (empty($beneficiaries)) {
      $pdf->drawEmptyNotice('No beneficiaries registered for this record.');
    } else {
      $colWidths = array(8, 55, 30, 50, 25, 12);
      $headers   = array('#', 'FULL NAME', 'RELATIONSHIP', 'EMAIL', 'BIRTH DATE', 'AGE');
      $aligns    = array('C', 'L', 'L', 'L', 'C', 'C');

      $pdf->beginTable($headers, $colWidths, $aligns);

      foreach ($beneficiaries as $i => $val) {
        $bName = strtoupper(trim(
          $val['CrudBeneficiary']['first_name'] . ' ' .
          $val['CrudBeneficiary']['middle_name'] . ' ' .
          $val['CrudBeneficiary']['last_name']
        ));
        $bEmail = isset($val['CrudBeneficiary']['email']) ? $val['CrudBeneficiary']['email'] : '';

        $pdf->drawTableRow(array(
          $i + 1,
          $pdf->truncate($bName, 38),
          strtoupper($val['CrudBeneficiary']['relationship']),
          $pdf->truncate($bEmail, 28),
          $this->fDate($val['CrudBeneficiary']['birth_date']),
          $val['CrudBeneficiary']['age']
        ), $i);
      }

      $pdf->endTable();
    }

    $pdf->Ln(10);
    $pdf->checkPageBreak(20);
    $pdf->SetFont('Arial', 'I', 7);
    $pdf->SetTextColor(108, 117, 125);
    $pdf->MultiCell(0, 4, 'This report was generated electronically by the E D & C SOLUTIONS Personal Data Management System. It certifies that the above information has been reviewed and approved. For verification inquiries, please contact the system administrator.', 0, 'L');

    $tmpFile  = tempnam(sys_get_temp_dir(), 'record_pdf_');
    $pdf->Output($tmpFile, 'F');

    $prevError = ini_get('display_errors');
    ini_set('display_errors', 0);

    while (ob_get_level() > 0) {
      ob_end_clean();
    }

    $filename = 'RecordReport_' . $id . '_' . date('Ymd') . '.pdf';
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($tmpFile));
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');

    readfile($tmpFile);
    unlink($tmpFile);

    ini_set('display_errors', $prevError);
    exit;

  }

  public function api_upload_file() {
    $this->autoRender = false;
    $this->RequestHandler->enabled = false;
    
    if ($this->request->is('post') && !empty($_FILES['file'])) {
      $file = $_FILES['file'];
      $uploadFolder = WWW_ROOT . 'uploads' . DS;
      
      if (!is_dir($uploadFolder)) {
        mkdir($uploadFolder, 0777, true);
      }
      
      $originalName = $file['name'];
      $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
      
      $allowedExtensions = array('jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx');
      if (!in_array($ext, $allowedExtensions)) {
        $response = array('ok' => false, 'msg' => 'Invalid file format. Only JPG, PNG, PDF, and Word documents (DOC/DOCX) are allowed.');
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
      }

      $uniqueName = uniqid() . '_' . time() . '.' . $ext;
      $targetFile = $uploadFolder . $uniqueName;
      
      if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        $response = array(
          'ok' => true,
          'filename' => $originalName,
          'filepath' => 'uploads/' . $uniqueName
        );
      } else {
        $response = array('ok' => false, 'msg' => 'Could not save the uploaded file.');
      }
    } else {
      $response = array('ok' => false, 'msg' => 'No file was uploaded.');
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
  }

  public function api_delete_file($id = null) {
    $this->loadModel('CrudFile');
    $result = $this->CrudFile->updateAll(
      array('CrudFile.visible' => 0),
      array('CrudFile.id' => $id)
    );
    
    $response = array('ok' => (bool)$result);
    $this->set(array('response' => $response, '_serialize' => 'response'));
  }
}
