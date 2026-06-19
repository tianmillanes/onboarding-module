<link rel="stylesheet" href="assets/css/crud.css">
<div class="crud-body">
  <div class="crud-card">
    <div class="crud-title-wrapper">
      <h2 class="crud-title"><i class="fa fa-pencil-square-o"></i> Edit Personal Record</h2>
    </div>

    <form id="form">
      <div class="row">
        <div class="col-md-4">
          <div class="crud-form-group">
            <label class="crud-form-label">First Name <i class="required">*</i></label>
            <input type="text" class="crud-form-control" ng-model="data.Crud.first_name" data-validation-engine="validate[required]" placeholder="Enter first name">
          </div>
        </div>
        <div class="col-md-4">
          <div class="crud-form-group">
            <label class="crud-form-label">Middle Name <i class="required">*</i></label>
            <input type="text" class="crud-form-control" ng-model="data.Crud.middle_name" data-validation-engine="validate[required]" placeholder="Enter middle name">
          </div>
        </div>
        <div class="col-md-4">
          <div class="crud-form-group">
            <label class="crud-form-label">Last Name <i class="required">*</i></label>
            <input type="text" class="crud-form-control" ng-model="data.Crud.last_name" data-validation-engine="validate[required]" placeholder="Enter last name">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="crud-form-group">
            <label class="crud-form-label">Contact Number <i class="required">*</i></label>
            <input type="text" class="crud-form-control" ng-model="data.Crud.contact_number" data-validation-engine="validate[required,custom[integer],minSize[11],maxSize[11]]" maxlength="11" onkeypress="return event.charCode >= 48 && event.charCode <= 57" placeholder="e.g. 09171234567">
          </div>
        </div>
        <div class="col-md-6">
          <div class="crud-form-group">
            <label class="crud-form-label">Email Address <i class="required">*</i></label>
            <input type="email" class="crud-form-control" ng-model="data.Crud.email" data-validation-engine="validate[required,custom[email]]" placeholder="Enter email address">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="crud-form-group">
            <label class="crud-form-label">Birth Date <i class="required">*</i></label>
            <input type="text" class="crud-form-control datepicker" ng-model="data.Crud.birth_date" data-validation-engine="validate[required]" placeholder="Select birth date" readonly style="background-color: #ffffff; cursor: pointer;">
          </div>
        </div>
        <div class="col-md-6">
          <div class="crud-form-group">
            <label class="crud-form-label">Age <i class="required">*</i></label>
            <input type="text" class="crud-form-control" ng-model="data.Crud.age" data-validation-engine="validate[required]" placeholder="Auto-calculated" readonly style="background-color: #f8fafc;">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="crud-form-group">
            <label class="crud-form-label">Home Address <i class="required">*</i></label>
            <textarea class="crud-form-control" ng-model="data.Crud.address" rows="4" data-validation-engine="validate[required]" placeholder="Enter full address..."></textarea>
          </div>
        </div>
      </div>
    </form>

    <!-- Beneficiaries Sub-table Card -->
    <div class="crud-card" style="margin-top: 30px; border: 1px solid #e2e8f0; background: #ffffff;">
      <div class="crud-title-wrapper" style="border-bottom: 2px solid #f0f3f6; padding-bottom: 15px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <h3 class="crud-title" style="font-size: 18px; margin: 0; color: #1a252f;"><i class="fa fa-users" style="color: #6366f1;"></i> Beneficiaries</h3>
        <button type="button" class="crud-btn-add" ng-click="openBeneficiaryModal()" style="padding: 6px 14px; font-size: 13px; height: auto; border-radius: 8px;"><i class="fa fa-user-plus"></i> Add Beneficiary</button>
      </div>

      <div class="table-responsive">
        <table class="crud-table" style="margin-top: 0; border: none; width: 100%;">
          <thead>
            <tr style="background-color: #f8fafc;">
              <th style="border-bottom: 1px solid #e2e8f0; padding: 12px 16px;">First Name</th>
              <th style="border-bottom: 1px solid #e2e8f0; padding: 12px 16px;">Middle Name</th>
              <th style="border-bottom: 1px solid #e2e8f0; padding: 12px 16px;">Last Name</th>
              <th style="border-bottom: 1px solid #e2e8f0; padding: 12px 16px;">Relationship</th>
              <th style="border-bottom: 1px solid #e2e8f0; padding: 12px 16px;">Birth Date</th>
              <th style="border-bottom: 1px solid #e2e8f0; padding: 12px 16px;">Age</th>
              <th style="border-bottom: 1px solid #e2e8f0; padding: 12px 16px; width: 100px; text-align: center;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="b in beneficiaries" style="transition: background-color 0.2s;">
              <td style="border-bottom: 1px solid #e2e8f0; padding: 12px 16px;">{{b.first_name}}</td>
              <td style="border-bottom: 1px solid #e2e8f0; padding: 12px 16px;">{{b.middle_name || '-'}}</td>
              <td style="border-bottom: 1px solid #e2e8f0; padding: 12px 16px;">{{b.last_name}}</td>
              <td style="border-bottom: 1px solid #e2e8f0; padding: 12px 16px;">{{b.relationship}}</td>
              <td style="border-bottom: 1px solid #e2e8f0; padding: 12px 16px;">{{b.birth_date}}</td>
              <td style="border-bottom: 1px solid #e2e8f0; padding: 12px 16px;">{{b.age}}</td>
              <td style="border-bottom: 1px solid #e2e8f0; padding: 12px 16px; text-align: center;">
                <div class="crud-action-btns" style="justify-content: center;">
                  <button type="button" class="crud-btn-action crud-btn-edit" ng-click="openBeneficiaryModal($index)" title="Edit"><i class="fa fa-pencil"></i></button>
                  <button type="button" class="crud-btn-action crud-btn-delete" ng-click="removeBeneficiary($index)" title="Delete"><i class="fa fa-trash"></i></button>
                </div>
              </td>
            </tr>
            <tr ng-if="!beneficiaries.length">
              <td colspan="7" style="text-align: center; color: #94a3b8; padding: 30px 15px;">
                <i class="fa fa-users" style="font-size: 28px; display: block; margin-bottom: 8px; color: #cbd5e1;"></i>
                No beneficiaries added yet.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <hr style="border-top: 1px solid #f1f5f9; margin: 30px 0;">

    <div class="row">
      <div class="col-md-3 pull-right">
        <button class="crud-btn-add btn-block" ng-click="save()" style="justify-content: center;"><i class="fa fa-save"></i> Save Changes</button>
      </div>
      <div class="col-md-3 pull-right">
        <a href="#/crud" class="btn btn-default btn-sm btn-block" style="padding: 10px; border-radius: 10px; font-weight: 600;"><i class="fa fa-times"></i> Cancel</a>
      </div>
    </div>
  </div>
</div>

<!-- Beneficiary Add/Edit Modal Dialog -->
<div class="modal fade" id="beneficiaryModal" tabindex="-1" role="dialog" aria-hidden="true" style="font-family: 'Outfit', sans-serif;">
  <div class="modal-dialog modal-md">
    <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15); overflow: hidden;">
      <div class="modal-header" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff; padding: 20px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: #ffffff; opacity: 0.8; font-size: 24px; font-weight: 300;">&times;</button>
        <h4 class="modal-title" style="font-weight: 600; display: flex; align-items: center; gap: 8px; color: #ffffff;"><i class="fa fa-user"></i> {{modalMode === 'add' ? 'Add Beneficiary' : 'Edit Beneficiary'}}</h4>
      </div>
      <div class="modal-body" style="padding: 24px; background: #ffffff;">
        <form id="beneficiaryForm">
          <div class="row">
            <div class="col-md-6">
              <div class="crud-form-group">
                <label class="crud-form-label">First Name <i class="required">*</i></label>
                <input type="text" class="crud-form-control" ng-model="tmpBeneficiary.first_name" data-validation-engine="validate[required]" placeholder="Enter first name">
              </div>
            </div>
            <div class="col-md-6">
              <div class="crud-form-group">
                <label class="crud-form-label">Middle Name</label>
                <input type="text" class="crud-form-control" ng-model="tmpBeneficiary.middle_name" placeholder="Enter middle name (optional)">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="crud-form-group">
                <label class="crud-form-label">Last Name <i class="required">*</i></label>
                <input type="text" class="crud-form-control" ng-model="tmpBeneficiary.last_name" data-validation-engine="validate[required]" placeholder="Enter last name">
              </div>
            </div>
            <div class="col-md-6">
              <div class="crud-form-group">
                <label class="crud-form-label">Relationship <i class="required">*</i></label>
                <input type="text" class="crud-form-control" ng-model="tmpBeneficiary.relationship" data-validation-engine="validate[required]" placeholder="e.g. Spouse, Child, Parent">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="crud-form-group">
                <label class="crud-form-label">Birth Date <i class="required">*</i></label>
                <input type="text" id="beneficiary_birth_date" class="crud-form-control" ng-model="tmpBeneficiary.birth_date" data-validation-engine="validate[required]" placeholder="Select birth date" readonly style="background-color: #ffffff; cursor: pointer;">
              </div>
            </div>
            <div class="col-md-6">
              <div class="crud-form-group">
                <label class="crud-form-label">Age <i class="required">*</i></label>
                <input type="text" class="crud-form-control" ng-model="tmpBeneficiary.age" data-validation-engine="validate[required]" placeholder="Auto-calculated" readonly style="background-color: #f8fafc;">
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer" style="padding: 15px 24px; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 10px; border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
        <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 8px; font-weight: 600; padding: 8px 16px; margin: 0;">Cancel</button>
        <button type="button" class="crud-btn-add" ng-click="saveBeneficiary()" style="padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; height: auto; margin: 0;"><i class="fa fa-check"></i> Save Beneficiary</button>
      </div>
    </div>
  </div>
</div>

<script>
  setTimeout(function() {
    $('#form').on('keydown', 'input, textarea, select', function(e) {
      if (e.key === 'Enter') {
        var self = $(this);
        var form = self.parents('form:eq(0)');
        var focusable = form.find('input, select, textarea').filter(':visible').not('[placeholder="Auto-calculated"]');
        var next = focusable.eq(focusable.index(this) + 1);
        if (next.length) {
          next.focus();
        }
        e.preventDefault();
        return false;
      }
    });
  }, 200);
</script>
