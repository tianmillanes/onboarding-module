app.controller('CrudsController', function ($scope, Crud, CrudStatus) {

  $scope.strSearch = '';
  $scope.activeTab = 'ALL';
  $scope.advFirstName = '';
  $scope.advMiddleName = '';
  $scope.advLastName = '';
  $scope.advBirthDate = '';
  $scope.advAge = '';
  $scope.advContactNumber = '';
  $scope.advEmail = '';
  $scope.advAddress = '';
  $scope.advStatus = 'ALL';
  $scope.showAdvanced = false;
  $scope.tabCounts = { ALL: 0 };
  $scope.allStatuses = [];
  $scope.lastSearch = {
    strSearch: '',
    advFirstName: '',
    advMiddleName: '',
    advLastName: '',
    advBirthDate: '',
    advAge: '',
    advContactNumber: '',
    advEmail: '',
    advAddress: '',
    advStatus: 'ALL',
    activeTab: 'ALL'
  };

  $scope.loadStatuses = function () {
    CrudStatus.query({ limit: 1000 }, function (e) {
      if (e.ok) {
        $scope.allStatuses = e.data;
        $scope.loadTabCounts();
      }
    });
  };
  $scope.loadStatuses();

  //date pickers for advanced search filters
  setTimeout(function () {
    if (document.getElementById('adv_birth_date')) {
      new CustomDatePicker('adv_birth_date', {
        format: 'MM/DD/YYYY',
        onChange: function (date) {
          $scope.$apply(function () {
            if (date) {
              var day = ('0' + date.getDate()).slice(-2);
              var month = ('0' + (date.getMonth() + 1)).slice(-2);
              var year = date.getFullYear();
              $scope.advBirthDate = month + '/' + day + '/' + year;
            } else {
              $scope.advBirthDate = '';
            }
          });
        }
      });
    }
  }, 200);

  // Build query params — advStatus overrides tab when explicitly set via Apply
  $scope.buildParams = function (extra) {
    var params = {};
    if ($scope.strSearch && $scope.strSearch.length > 0) {
      params.search = $scope.strSearch;
    }
    // Use advStatus if it's set and not ALL, else fall back to activeTab
    var effectiveStatus = ($scope.advStatus && $scope.advStatus !== 'ALL') ? $scope.advStatus : $scope.activeTab;
    if (effectiveStatus && effectiveStatus !== 'ALL') {
      params.status = effectiveStatus;
    }

    // Advanced search filters
    if ($scope.advFirstName && $scope.advFirstName.length > 0) {
      params.search_first_name = $scope.advFirstName;
    }
    if ($scope.advMiddleName && $scope.advMiddleName.length > 0) {
      params.search_middle_name = $scope.advMiddleName;
    }
    if ($scope.advLastName && $scope.advLastName.length > 0) {
      params.search_last_name = $scope.advLastName;
    }
    if ($scope.advBirthDate && $scope.advBirthDate.length > 0) {
      params.search_birth_date = $scope.advBirthDate;
    }
    if ($scope.advAge !== undefined && $scope.advAge !== null && $scope.advAge !== '') {
      params.search_age = $scope.advAge;
    }
    if ($scope.advContactNumber && $scope.advContactNumber.length > 0) {
      params.search_contact_number = $scope.advContactNumber;
    }
    if ($scope.advEmail && $scope.advEmail.length > 0) {
      params.search_email = $scope.advEmail;
    }
    if ($scope.advAddress && $scope.advAddress.length > 0) {
      params.search_address = $scope.advAddress;
    }

    return angular.extend(params, extra || {});
  };

  $scope.load = function (options) {
    $scope.lastSearch = {
      strSearch: $scope.strSearch,
      advFirstName: $scope.advFirstName,
      advMiddleName: $scope.advMiddleName,
      advLastName: $scope.advLastName,
      advBirthDate: $scope.advBirthDate,
      advAge: $scope.advAge,
      advContactNumber: $scope.advContactNumber,
      advEmail: $scope.advEmail,
      advAddress: $scope.advAddress,
      advStatus: $scope.advStatus,
      activeTab: $scope.activeTab
    };

    Crud.query(options || $scope.buildParams(), function (e) {
      if (e.ok) {
        $scope.cruds = e.data;
        $scope.paginator = e.paginator;
        $scope.pages = paginator($scope.paginator, 5);
      }
    });
  };

  $scope.loadTabCounts = function () {
    var statuses = ['ALL'];
    $scope.allStatuses.forEach(function (st) {
      statuses.push(st.name);
    });

    statuses.forEach(function (s) {
      var p = { page: 1 };
      if ($scope.strSearch && $scope.strSearch.length > 0) { p.search = $scope.strSearch; }
      if (s !== 'ALL') { p.status = s; }

      // Propagate advanced filters to counts
      if ($scope.advFirstName && $scope.advFirstName.length > 0) { p.search_first_name = $scope.advFirstName; }
      if ($scope.advMiddleName && $scope.advMiddleName.length > 0) { p.search_middle_name = $scope.advMiddleName; }
      if ($scope.advLastName && $scope.advLastName.length > 0) { p.search_last_name = $scope.advLastName; }
      if ($scope.advBirthDate && $scope.advBirthDate.length > 0) { p.search_birth_date = $scope.advBirthDate; }
      if ($scope.advAge !== undefined && $scope.advAge !== null && $scope.advAge !== '') { p.search_age = $scope.advAge; }
      if ($scope.advContactNumber && $scope.advContactNumber.length > 0) { p.search_contact_number = $scope.advContactNumber; }
      if ($scope.advEmail && $scope.advEmail.length > 0) { p.search_email = $scope.advEmail; }
      if ($scope.advAddress && $scope.advAddress.length > 0) { p.search_address = $scope.advAddress; }

      Crud.query(p, function (e) {
        if (e.ok) { $scope.tabCounts[s] = e.paginator ? e.paginator.count : 0; }
      });
    });
  };

  $scope.load();

  $scope.switchTab = function (tab) {
    $scope.activeTab = tab;
    $scope.advStatus = 'ALL';   // reset advanced filter when switching tabs
    $scope.load($scope.buildParams({ page: 1 }));
    $scope.loadTabCounts();
  };

  var searchTimeout = null;
  $scope.search = function () {
    if (searchTimeout) {
      clearTimeout(searchTimeout);
    }
    $scope.load($scope.buildParams({ page: 1 }));
    $scope.loadTabCounts();
  };

  $scope.$watch('strSearch', function (newVal, oldVal) {
    if (newVal !== oldVal) {
      if (searchTimeout) {
        clearTimeout(searchTimeout);
      }
      searchTimeout = setTimeout(function () {
        $scope.$apply(function () {
          $scope.search();
        });
      }, 250);
    }
  });

  $scope.applyAdvanced = function () {
    // Sync activeTab to match advStatus so tabs highlight correctly
    if ($scope.advStatus && $scope.advStatus !== 'ALL') {
      $scope.activeTab = $scope.advStatus;
    } else {
      $scope.activeTab = 'ALL';
    }
    $scope.load($scope.buildParams({ page: 1 }));
    $scope.loadTabCounts();
  };

  $scope.resetAdvanced = function () {
    $scope.strSearch = '';
    $scope.advFirstName = '';
    $scope.advMiddleName = '';
    $scope.advLastName = '';
    $scope.advBirthDate = '';
    $scope.advAge = '';
    $scope.advContactNumber = '';
    $scope.advEmail = '';
    $scope.advAddress = '';
    $scope.advStatus = 'ALL';
    $scope.activeTab = 'ALL';
    $scope.showAdvanced = false;

    // Reset date picker DOM fields
    if (document.getElementById('adv_birth_date')) {
      document.getElementById('adv_birth_date').value = '';
    }

    $scope.load();
    $scope.loadTabCounts();
  };

  $scope.getAdvancedSearchSummary = function () {
    var categories = [];
    var nameParts = [];
    if ($scope.lastSearch.advFirstName) nameParts.push($scope.lastSearch.advFirstName);
    if ($scope.lastSearch.advMiddleName) nameParts.push($scope.lastSearch.advMiddleName);
    if ($scope.lastSearch.advLastName) nameParts.push($scope.lastSearch.advLastName);

    if (nameParts.length > 0) {
      categories.push(nameParts.join(' '));
    }

    if ($scope.lastSearch.advBirthDate) categories.push($scope.lastSearch.advBirthDate);
    if ($scope.lastSearch.advAge !== undefined && $scope.lastSearch.advAge !== null && $scope.lastSearch.advAge !== '') categories.push($scope.lastSearch.advAge);
    if ($scope.lastSearch.advContactNumber) categories.push($scope.lastSearch.advContactNumber);
    if ($scope.lastSearch.advEmail) categories.push($scope.lastSearch.advEmail);
    if ($scope.lastSearch.advAddress) categories.push($scope.lastSearch.advAddress);
    if ($scope.lastSearch.advStatus && $scope.lastSearch.advStatus !== 'ALL') categories.push($scope.lastSearch.advStatus);

    if (categories.length === 1) {
      return '"' + categories[0] + '" not found';
    } else {
      return 'No record matching the search criteria found';
    }
  };

  $scope.goPage = function (page) {
    $scope.load($scope.buildParams({ page: page }));
  };

  $scope.remove = function (data) {
    bootbox.confirm('Are you sure you want to delete ' + data.first_name + ' ' + data.last_name + ' ?', function (c) {
      if (c) {
        Crud.remove({ id: data.id }, function (e) {
          if (e.ok) {
            $.gritter.add({ title: 'Successful!', text: e.msg });
            $scope.load($scope.buildParams());
            $scope.loadTabCounts();
          }
        });
      }
    });
  };

  $scope.printPdf = function () {
    var params = [];
    if ($scope.strSearch && $scope.strSearch.length > 0) {
      params.push('search=' + encodeURIComponent($scope.strSearch));
    }
    var effectiveStatus = ($scope.advStatus && $scope.advStatus !== 'ALL') ? $scope.advStatus : $scope.activeTab;
    if (effectiveStatus && effectiveStatus !== 'ALL') {
      params.push('status=' + encodeURIComponent(effectiveStatus));
    }
    if ($scope.advName && $scope.advName.length > 0) {
      params.push('search_name=' + encodeURIComponent($scope.advName));
    }
    if ($scope.advEmail && $scope.advEmail.length > 0) {
      params.push('search_email=' + encodeURIComponent($scope.advEmail));
    }
    if ($scope.advAge && $scope.advAge.length > 0) {
      params.push('search_age=' + encodeURIComponent($scope.advAge));
    }
    if ($scope.advCreated && $scope.advCreated.length > 0) {
      params.push('search_created=' + encodeURIComponent($scope.advCreated));
    }
    if ($scope.advModified && $scope.advModified.length > 0) {
      params.push('search_modified=' + encodeURIComponent($scope.advModified));
    }
    var url = api + 'cruds/print_pdf' + (params.length > 0 ? '?' + params.join('&') : '');
    window.open(url, '_blank');
  };
});


app.controller('CrudsAddController', function ($scope, Crud, CrudStatus) {
  $('#form').validationEngine('attach');

  $scope.allStatuses = [];
  $scope.loadStatuses = function () {
    CrudStatus.query({ limit: 1000 }, function (e) {
      if (e.ok) {
        $scope.allStatuses = e.data;
      }
    });
  };
  $scope.loadStatuses();

  $scope.data = {
    Crud: {
      birth_date: '',
      age: '',
      status: 'PENDING'
    }
  };

  $scope.beneficiaries = [];
  $scope.tmpBeneficiary = null;
  $scope.modalMode = 'add';
  $scope.modalIndex = null;

  // Initialize custom date picker for birth date
  setTimeout(function () {
    if (document.getElementById('birth_date')) {
      new CustomDatePicker('birth_date', {
        format: 'MM/DD/YYYY',
        onChange: function (date) {
          $scope.$apply(function () {
            if (date) {
              var day = ('0' + date.getDate()).slice(-2);
              var month = ('0' + (date.getMonth() + 1)).slice(-2);
              var year = date.getFullYear();
              $scope.data.Crud.birth_date = month + '/' + day + '/' + year;
            }
          });
        }
      });
    }
  }, 100);

  // Watch birth date and compute age
  $scope.$watch('data.Crud.birth_date', function (newVal) {
    if (newVal && $scope.data.Crud) {
      var parts = newVal.split('/');
      if (parts.length === 3) {
        var month = parseInt(parts[0], 10);
        var day = parseInt(parts[1], 10);
        var year = parseInt(parts[2], 10);
        if (month >= 1 && month <= 12 && day >= 1 && day <= 31 && year > 1900) {
          var birthDate = new Date(year, month - 1, day);
          var today = new Date();
          var age = today.getFullYear() - birthDate.getFullYear();
          var m = today.getMonth() - birthDate.getMonth();
          if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) { age--; }
          $scope.data.Crud.age = age >= 0 ? age : 0;
          return;
        }
      }
    }
    if ($scope.data.Crud) { $scope.data.Crud.age = ''; }
  });

  // Watch beneficiary birth date and compute age
  $scope.$watch('tmpBeneficiary.birth_date', function (newVal) {
    if (newVal && $scope.tmpBeneficiary) {
      var parts = newVal.split('/');
      if (parts.length === 3) {
        var month = parseInt(parts[0], 10);
        var day = parseInt(parts[1], 10);
        var year = parseInt(parts[2], 10);
        if (month >= 1 && month <= 12 && day >= 1 && day <= 31 && year > 1900) {
          var birthDate = new Date(year, month - 1, day);
          var today = new Date();
          var age = today.getFullYear() - birthDate.getFullYear();
          var m = today.getMonth() - birthDate.getMonth();
          if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) { age--; }
          $scope.tmpBeneficiary.age = age >= 0 ? age : 0;
          return;
        }
      }
    }
    if ($scope.tmpBeneficiary) { $scope.tmpBeneficiary.age = ''; }
  });

  $scope.openBeneficiaryModal = function (index) {
    if (typeof index !== 'undefined') {
      $scope.modalMode = 'edit';
      $scope.modalIndex = index;
      $scope.tmpBeneficiary = angular.copy($scope.beneficiaries[index]);
    } else {
      $scope.modalMode = 'add';
      $scope.modalIndex = null;
      $scope.tmpBeneficiary = { first_name: '', middle_name: '', last_name: '', relationship: '', email: '', birth_date: '', age: '' };
    }
    $('#beneficiaryForm').validationEngine('hideAll');
    $('#beneficiaryModal').modal('show');
    setTimeout(function () {
      if (document.getElementById('beneficiary_birth_date')) {
        new CustomDatePicker('beneficiary_birth_date', {
          format: 'MM/DD/YYYY',
          onChange: function (date) {
            $scope.$apply(function () {
              if (date) {
                var day = ('0' + date.getDate()).slice(-2);
                var month = ('0' + (date.getMonth() + 1)).slice(-2);
                var year = date.getFullYear();
                $scope.tmpBeneficiary.birth_date = month + '/' + day + '/' + year;
              }
            });
          }
        });
        if ($scope.tmpBeneficiary.birth_date) {
          var picker = new CustomDatePicker('beneficiary_birth_date');
          picker.setDate(picker.parseDate($scope.tmpBeneficiary.birth_date));
        }
      }
    }, 200);
  };

  $scope.saveBeneficiary = function () {
    var valid = $('#beneficiaryForm').validationEngine('validate');
    if (valid) {
      if ($scope.modalMode === 'add') {
        $scope.beneficiaries.push($scope.tmpBeneficiary);
      } else {
        $scope.beneficiaries[$scope.modalIndex] = $scope.tmpBeneficiary;
      }
      $('#beneficiaryModal').modal('hide');
    }
  };

  $scope.removeBeneficiary = function (index) {
    bootbox.confirm('Are you sure you want to delete this beneficiary?', function (c) {
      if (c) {
        $scope.$apply(function () { $scope.beneficiaries.splice(index, 1); });
      }
    });
  };

  $scope.uploadedFiles = [];
  $scope.uploading = false;

  $scope.uploadFile = function (files) {
    if (!files || files.length === 0) return;
    var file = files[0];

    var formData = new FormData();
    formData.append('file', file);

    $scope.$apply(function () {
      $scope.uploading = true;
    });

    $.ajax({
      url: api + 'cruds/upload_file.json',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        $scope.$apply(function () {
          $scope.uploading = false;
          if (response && response.ok) {
            $scope.uploadedFiles.push({
              filename: response.filename,
              filepath: response.filepath
            });
            var fileInput = document.getElementById('file_attachment');
            if (fileInput) fileInput.value = '';
          } else {
            $.gritter.add({ title: 'Upload Failed!', text: response.msg || 'Could not upload file.' });
          }
        });
      },
      error: function () {
        $scope.$apply(function () {
          $scope.uploading = false;
          $.gritter.add({ title: 'Upload Error!', text: 'An error occurred during file upload.' });
        });
      }
    });
  };

  $scope.removeUploadedFile = function (index) {
    $scope.uploadedFiles.splice(index, 1);
  };

  $scope.save = function () {
    console.log("SAVE CLICKED!");
    // Show loader immediately
    console.log("Showing loading modal...");
    $('#apple-loading-modal').addClass('active');

    // Wrap validation and API call in setTimeout to allow the browser to paint the loader
    setTimeout(function () {
      var valid = $("#form").validationEngine('validate');
      console.log("Validation result:", valid);
      if (valid) {
        $scope.data.Beneficiaries = $scope.beneficiaries;
        $scope.data.Files = $scope.uploadedFiles;
        var startTime = Date.now();
        Crud.save($scope.data, function (e) {
          var elapsed = Date.now() - startTime;
          var delay = Math.max(0, 1000 - elapsed);
          setTimeout(function () {
            $('#apple-loading-modal').removeClass('active');
            $scope.$apply(function () {
              if (e.ok) {
                $.gritter.add({ title: 'Successful!', text: e.msg });
                window.location = '#/crud';
              } else {
                $.gritter.add({ title: 'Warning!', text: e.msg });
              }
            });
          }, delay);
        }, function () {
          var elapsed = Date.now() - startTime;
          var delay = Math.max(0, 1000 - elapsed);
          setTimeout(function () {
            $('#apple-loading-modal').removeClass('active');
            $scope.$apply(function () {
              $.gritter.add({ title: 'Error!', text: 'An error occurred while saving.' });
            });
          }, delay);
        });
      } else {
        // Hide loader if validation fails
        $('#apple-loading-modal').removeClass('active');
      }
    }, 50);
  };
});

app.controller('CrudsViewController', function ($scope, $routeParams, Crud) {
  $scope.id = $routeParams.id;
  $scope.data = {};
  $scope.beneficiaries = [];

  $scope.files = [];
  $scope.basePath = base;

  $scope.load = function () {
    Crud.get({ id: $scope.id }, function (e) {
      if (e.ok) {
        $scope.data = e.data;
        $scope.beneficiaries = e.data.Beneficiaries || [];
        $scope.files = e.data.Files || [];
      }
    });
  };

  $scope.printRecord = function () {
    var firstName = ($scope.data && $scope.data.Crud && $scope.data.Crud.first_name) ? $scope.data.Crud.first_name.trim() : '';
    var lastName = ($scope.data && $scope.data.Crud && $scope.data.Crud.last_name) ? $scope.data.Crud.last_name.trim() : '';
    var fullName = (firstName + '_' + lastName).replace(/\s+/g, '_');
    if (fullName === '_') fullName = 'Personal_Record';

    var url = api + 'cruds/print_record/' + $scope.id + '/' + fullName + '_Report.pdf';
    window.open(url, '_blank');
  };

  $scope.load();

  $scope.remove = function (data) {
    bootbox.confirm('Are you sure you want to remove ' + data.first_name + ' ' + data.last_name + ' ?', function (c) {
      if (c) {
        Crud.remove({ id: data.id }, function (e) {
          if (e.ok) {
            $.gritter.add({ title: 'Successful!', text: e.msg });
            window.location = "#/crud";
          }
        });
      }
    });
  };

  $scope.processing = false;

  var safeApply = function (fn) {
    var phase = $scope.$root ? $scope.$root.$$phase : null;
    if (phase === '$apply' || phase === '$digest') {
      if (fn && (typeof (fn) === 'function')) {
        fn();
      }
    } else {
      $scope.$apply(fn);
    }
  };

  $scope.approve = function () {
    bootbox.confirm('Are you sure you want to APPROVE this record?', function (c) {
      if (c) {
        safeApply(function () { $scope.processing = true; });
        Crud.approve({ id: $scope.id }, {}, function (e) {
          if (e.ok) {
            $.gritter.add({ title: 'Approved!', text: e.msg });
            safeApply(function () {
              $scope.load();
              $scope.processing = false;
            });
          } else {
            $.gritter.add({ title: 'Error!', text: e.msg });
            safeApply(function () { $scope.processing = false; });
          }
        }, function () {
          safeApply(function () { $scope.processing = false; });
        });
      }
    });
  };

  $scope.reject = function () {
    bootbox.confirm('Are you sure you want to DISAPPROVE this record?', function (c) {
      if (c) {
        safeApply(function () { $scope.processing = true; });
        Crud.reject({ id: $scope.id }, {}, function (e) {
          if (e.ok) {
            $.gritter.add({ title: 'Disapproved!', text: e.msg });
            safeApply(function () {
              $scope.load();
              $scope.processing = false;
            });
          } else {
            $.gritter.add({ title: 'Error!', text: e.msg });
            safeApply(function () { $scope.processing = false; });
          }
        }, function () {
          safeApply(function () { $scope.processing = false; });
        });
      }
    });
  };
});

app.controller('CrudsEditController', function ($scope, $routeParams, Crud, CrudStatus) {
  $scope.id = $routeParams.id;
  $scope.data = {};
  $scope.allStatuses = [];

  $scope.loadStatuses = function () {
    CrudStatus.query({ limit: 1000 }, function (e) {
      if (e.ok) {
        $scope.allStatuses = e.data;
      }
    });
  };
  $scope.loadStatuses();

  $scope.beneficiaries = [];
  $scope.tmpBeneficiary = null;
  $scope.modalMode = 'add';
  $scope.modalIndex = null;

  $("#form").validationEngine('attach');

  $scope.existingFiles = [];
  $scope.uploadedFiles = [];
  $scope.uploading = false;
  $scope.basePath = base;

  $scope.load = function () {
    Crud.get({ id: $scope.id }, function (e) {
      if (e.ok) {
        $scope.data = e.data;
        $scope.originalStatus = e.data.Crud.status;
        $scope.beneficiaries = e.data.Beneficiaries || [];
        $scope.existingFiles = e.data.Files || [];
        setTimeout(function () {
          if (document.getElementById('birth_date')) {
            var picker = new CustomDatePicker('birth_date', {
              format: 'MM/DD/YYYY',
              onChange: function (date) {
                $scope.$apply(function () {
                  if (date) {
                    var day = ('0' + date.getDate()).slice(-2);
                    var month = ('0' + (date.getMonth() + 1)).slice(-2);
                    var year = date.getFullYear();
                    $scope.data.Crud.birth_date = month + '/' + day + '/' + year;
                  }
                });
              }
            });
            if ($scope.data.Crud.birth_date) {
              picker.setDate(picker.parseDate($scope.data.Crud.birth_date));
            }
          }
        }, 100);
      }
    });
  };

  $scope.uploadFile = function (files) {
    if (!files || files.length === 0) return;
    var file = files[0];

    var formData = new FormData();
    formData.append('file', file);

    $scope.$apply(function () {
      $scope.uploading = true;
    });

    $.ajax({
      url: api + 'cruds/upload_file.json',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        $scope.$apply(function () {
          $scope.uploading = false;
          if (response && response.ok) {
            $scope.uploadedFiles.push({
              filename: response.filename,
              filepath: response.filepath
            });
            var fileInput = document.getElementById('file_attachment');
            if (fileInput) fileInput.value = '';
          } else {
            $.gritter.add({ title: 'Upload Failed!', text: response.msg || 'Could not upload file.' });
          }
        });
      },
      error: function () {
        $scope.$apply(function () {
          $scope.uploading = false;
          $.gritter.add({ title: 'Upload Error!', text: 'An error occurred during file upload.' });
        });
      }
    });
  };

  $scope.removeUploadedFile = function (index) {
    $scope.uploadedFiles.splice(index, 1);
  };

  $scope.deleteExistingFile = function (fileId, index) {
    bootbox.confirm('Are you sure you want to delete this file?', function (c) {
      if (c) {
        $.ajax({
          url: api + 'cruds/delete_file/' + fileId + '.json',
          type: 'POST',
          success: function (response) {
            $scope.$apply(function () {
              if (response && response.ok) {
                $scope.existingFiles.splice(index, 1);
                $.gritter.add({ title: 'Success', text: 'File has been deleted.' });
              } else {
                $.gritter.add({ title: 'Error', text: 'Could not delete file.' });
              }
            });
          },
          error: function () {
            $.gritter.add({ title: 'Error', text: 'An error occurred while deleting the file.' });
          }
        });
      }
    });
  };

  $scope.load();

  // Watch birth date and compute age
  $scope.$watch('data.Crud.birth_date', function (newVal) {
    if (newVal && $scope.data.Crud) {
      var parts = newVal.split('/');
      if (parts.length === 3) {
        var month = parseInt(parts[0], 10);
        var day = parseInt(parts[1], 10);
        var year = parseInt(parts[2], 10);
        if (month >= 1 && month <= 12 && day >= 1 && day <= 31 && year > 1900) {
          var birthDate = new Date(year, month - 1, day);
          var today = new Date();
          var age = today.getFullYear() - birthDate.getFullYear();
          var m = today.getMonth() - birthDate.getMonth();
          if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) { age--; }
          $scope.data.Crud.age = age >= 0 ? age : 0;
          return;
        }
      }
    }
    if ($scope.data.Crud) { $scope.data.Crud.age = ''; }
  });

  // Watch beneficiary birth date and compute age
  $scope.$watch('tmpBeneficiary.birth_date', function (newVal) {
    if (newVal && $scope.tmpBeneficiary) {
      var parts = newVal.split('/');
      if (parts.length === 3) {
        var month = parseInt(parts[0], 10);
        var day = parseInt(parts[1], 10);
        var year = parseInt(parts[2], 10);
        if (month >= 1 && month <= 12 && day >= 1 && day <= 31 && year > 1900) {
          var birthDate = new Date(year, month - 1, day);
          var today = new Date();
          var age = today.getFullYear() - birthDate.getFullYear();
          var m = today.getMonth() - birthDate.getMonth();
          if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) { age--; }
          $scope.tmpBeneficiary.age = age >= 0 ? age : 0;
          return;
        }
      }
    }
    if ($scope.tmpBeneficiary) { $scope.tmpBeneficiary.age = ''; }
  });

  $scope.openBeneficiaryModal = function (index) {
    if (typeof index !== 'undefined') {
      $scope.modalMode = 'edit';
      $scope.modalIndex = index;
      $scope.tmpBeneficiary = angular.copy($scope.beneficiaries[index]);
    } else {
      $scope.modalMode = 'add';
      $scope.modalIndex = null;
      $scope.tmpBeneficiary = { first_name: '', middle_name: '', last_name: '', relationship: '', email: '', birth_date: '', age: '' };
    }
    $('#beneficiaryForm').validationEngine('hideAll');
    $('#beneficiaryModal').modal('show');
    setTimeout(function () {
      if (document.getElementById('beneficiary_birth_date')) {
        new CustomDatePicker('beneficiary_birth_date', {
          format: 'MM/DD/YYYY',
          onChange: function (date) {
            $scope.$apply(function () {
              if (date) {
                var day = ('0' + date.getDate()).slice(-2);
                var month = ('0' + (date.getMonth() + 1)).slice(-2);
                var year = date.getFullYear();
                $scope.tmpBeneficiary.birth_date = month + '/' + day + '/' + year;
              }
            });
          }
        });
        if ($scope.tmpBeneficiary.birth_date) {
          var picker = new CustomDatePicker('beneficiary_birth_date');
          picker.setDate(picker.parseDate($scope.tmpBeneficiary.birth_date));
        }
      }
    }, 200);
  };

  $scope.saveBeneficiary = function () {
    var valid = $('#beneficiaryForm').validationEngine('validate');
    if (valid) {
      if ($scope.modalMode === 'add') {
        $scope.beneficiaries.push($scope.tmpBeneficiary);
      } else {
        $scope.beneficiaries[$scope.modalIndex] = $scope.tmpBeneficiary;
      }
      $('#beneficiaryModal').modal('hide');
    }
  };

  $scope.removeBeneficiary = function (index) {
    bootbox.confirm('Are you sure you want to delete this beneficiary?', function (c) {
      if (c) {
        $scope.$apply(function () { $scope.beneficiaries.splice(index, 1); });
      }
    });
  };

  var submitCrudUpdate = function () {
    console.log("UPDATE CLICKED!");
    // Show loader immediately
    console.log("Showing loading modal...");
    $('#apple-loading-modal').addClass('active');

    // Wrap validation and API call in setTimeout to allow the browser to paint the loader
    setTimeout(function () {
      var valid = $("#form").validationEngine('validate');
      console.log("Validation result:", valid);
      if (valid) {
        $scope.data.Beneficiaries = $scope.beneficiaries;
        $scope.data.Files = $scope.uploadedFiles;
        var startTime = Date.now();
        Crud.update({ id: $scope.id }, $scope.data, function (e) {
          var elapsed = Date.now() - startTime;
          var delay = Math.max(0, 1000 - elapsed);
          setTimeout(function () {
            $('#apple-loading-modal').removeClass('active');
            $scope.$apply(function () {
              if (e.ok) {
                $.gritter.add({ title: 'Successful!', text: e.msg });
                window.location = '#/crud';
              } else {
                $.gritter.add({ title: 'Warning!', text: e.msg });
              }
            });
          }, delay);
        }, function () {
          var elapsed = Date.now() - startTime;
          var delay = Math.max(0, 1000 - elapsed);
          setTimeout(function () {
            $('#apple-loading-modal').removeClass('active');
            $scope.$apply(function () {
              $.gritter.add({ title: 'Error!', text: 'An error occurred while updating.' });
            });
          }, delay);
        });
      } else {
        // Hide loader if validation fails
        $('#apple-loading-modal').removeClass('active');
      }
    }, 50);
  };

  $scope.save = submitCrudUpdate;
  $scope.update = submitCrudUpdate;
});
