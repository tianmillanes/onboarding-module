<div class="crud-glass-container">
  <div class="panel panel-primary">
    <div class="panel-heading" style="display: flex; align-items: center; gap: 10px;">
      <a href="#/crud" class="back-btn-heading"><i class="fa fa-arrow-left"></i></a>
      <span><i class="fa fa-dot-circle-o"></i> EDIT PERSONAL RECORD</span>
    </div>
    <div class="panel-body">
    	<div class="col-md-12">
    	  <form id="form">
          <div class="row">

            <div class="col-md-4">
              <div class="form-group">
                <label>First Name <i class="required">*</i></label>
                <input type="text" class="form-control" ng-model="data.Crud.first_name" data-validation-engine="validate[required]" ng-disabled="originalStatus === 'APPROVED' || originalStatus === 'DISAPPROVED'">
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label>Middle Name <i class="required">*</i></label>
                <input type="text" class="form-control" ng-model="data.Crud.middle_name" data-validation-engine="validate[required]" ng-disabled="originalStatus === 'APPROVED' || originalStatus === 'DISAPPROVED'">
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label>Last Name <i class="required">*</i></label>
                <input type="text" class="form-control" ng-model="data.Crud.last_name" data-validation-engine="validate[required]" ng-disabled="originalStatus === 'APPROVED' || originalStatus === 'DISAPPROVED'">
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Contact Number <i class="required">*</i></label>
                <input type="text" class="form-control" ng-model="data.Crud.contact_number" data-validation-engine="validate[required,custom[integer],minSize[11],maxSize[11]]" maxlength="11" onkeypress="return event.charCode >= 48 && event.charCode <= 57" ng-disabled="originalStatus === 'APPROVED' || originalStatus === 'DISAPPROVED'">
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Email Address <i class="required">*</i></label>
                <input type="email" class="form-control" ng-model="data.Crud.email" data-validation-engine="validate[required,custom[email]]" ng-disabled="originalStatus === 'APPROVED' || originalStatus === 'DISAPPROVED'">
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="birth_date">Birth Date <i class="required">*</i></label>
                <div class="date-picker-wrapper">
                  <input type="text" class="date-input form-control" id="birth_date" ng-model="data.Crud.birth_date" data-validation-engine="validate[required]" readonly style="background-color: #ffffff; cursor: pointer;" ng-disabled="originalStatus === 'APPROVED' || originalStatus === 'DISAPPROVED'">
                  <button type="button" class="date-picker-toggle" tabindex="-1" ng-disabled="originalStatus === 'APPROVED' || originalStatus === 'DISAPPROVED'">📅</button>
                  <div class="date-picker-container" id="birth_date_picker"></div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Age <i class="required">*</i></label>
                <input type="text" class="form-control" ng-model="data.Crud.age" data-validation-engine="validate[required]" readonly ng-disabled="originalStatus === 'APPROVED' || originalStatus === 'DISAPPROVED'">
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Record Status <i class="required">*</i></label>
                <select class="form-control" ng-model="data.Crud.status" data-validation-engine="validate[required]" ng-disabled="originalStatus === 'APPROVED' || originalStatus === 'DISAPPROVED'">
                  <option ng-repeat="st in allStatuses" value="{{st.name}}">{{st.name}}</option>
                </select>
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group">
                <label>Home Address <i class="required">*</i></label>
                <textarea class="form-control" ng-model="data.Crud.address" rows="4" data-validation-engine="validate[required]" ng-disabled="originalStatus === 'APPROVED' || originalStatus === 'DISAPPROVED'"></textarea>
              </div>
            </div>

            <div class="clearfix"></div><hr>
            <div class="col-md-3 pull-left">
              <button type="button" class="btn btn-warning btn-sm btn-block" id="save" ng-click="openBeneficiaryModal()" ng-disabled="originalStatus === 'APPROVED' || originalStatus === 'DISAPPROVED'">ADD BENEFICIARY</button><br/>
            </div>

            <div class="clearfix"></div>
 
            <div class="col-md-12">
              <table class="table table-bordered table-striped table-hover">
                <thead>
                  <tr>
                    <th class="w30px text-center">#</th>
                    <th class="text-center">FIRST NAME</th>
                    <th class="text-center">MIDDLE NAME</th>
                    <th class="text-center">LAST NAME</th>
                    <th class="text-center">RELATIONSHIP</th>
                    <th class="text-center">EMAIL</th>
                    <th class="text-center">BIRTH DATE</th>
                    <th class="text-center">AGE</th>
                    <th class="w100px"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr ng-repeat="b in beneficiaries" ng-if="b.visible != 0">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="uppercase text-left">{{ b.first_name }}</td>
                    <td class="uppercase text-left">{{ b.middle_name || '-' }}</td>
                    <td class="uppercase text-left">{{ b.last_name }}</td>
                    <td class="text-left">{{ b.relationship }}</td>
                    <td class="text-left">{{ b.email || '-' }}</td>
                    <td class="text-center">{{ b.birth_date }}</td>
                    <td class="text-center">{{ b.age }}</td>
                    <td class="text-center">
                      <div class="btn-group btn-group-xs">
                        <button type="button" ng-click="openBeneficiaryModal($index)" class="btn btn-success" title="EDIT" ng-disabled="originalStatus === 'APPROVED' || originalStatus === 'DISAPPROVED'"><i class="fa fa-edit"></i></button>
                        <button type="button" ng-click="removeBeneficiary($index)" class="btn btn-danger" title="DELETE" ng-disabled="originalStatus === 'APPROVED' || originalStatus === 'DISAPPROVED'"><i class="fa fa-trash"></i></button>
                      </div>
                    </td>
                  </tr>
                </tbody>
                <tbody ng-if="beneficiaries.length <= 0">
                  <td colspan="9" class="text-center">No data available</td>
                </tbody>
              </table>
            </div>

            <!-- File Attachments Section (Edit) -->
            <div class="clearfix"></div>
            <div class="row" style="margin-top: 15px; margin-bottom: 20px;">
              <div class="col-md-12">
                <div style="background: #f8f9fc; border: 1px solid #e3e6f0; border-radius: 6px; padding: 15px;">
                  <h5 style="margin-top: 0; margin-bottom: 15px; color: #4e73df; font-weight: bold; display: flex; align-items: center; gap: 8px;">
                    <i class="fa fa-paperclip"></i> FILE ATTACHMENTS
                  </h5>
                  <div class="row">
                    <div class="col-md-6">
                      <!-- Existing Files -->
                      <label>Existing Files</label>
                      <ul class="list-group" style="margin-bottom: 15px; max-height: 150px; overflow-y: auto;">
                        <li class="list-group-item" ng-repeat="file in existingFiles" style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px;">
                          <span style="word-break: break-all;">
                            <i class="fa fa-file-o" style="margin-right: 5px;"></i>
                            <a href="{{ basePath + file.filepath }}" target="_blank" style="color: #4e73df; font-weight: 600;">{{file.filename}}</a>
                          </span>
                          <button type="button" class="btn btn-danger btn-xs" ng-click="deleteExistingFile(file.id, $index)" ng-disabled="originalStatus === 'APPROVED' || originalStatus === 'DISAPPROVED'" style="padding: 2px 6px;">
                            <i class="fa fa-trash-o"></i>
                          </button>
                        </li>
                        <li class="list-group-item text-center text-muted" ng-if="existingFiles.length === 0" style="padding: 15px; font-style: italic; background: transparent; border-style: dashed;">
                          No existing files attached.
                        </li>
                      </ul>
                    </div>

                    <div class="col-md-6">
                      <!-- New Uploads -->
                      <div class="form-group" style="margin-bottom: 10px;">
                        <label>Upload New File</label>
                        <input type="file" id="file_attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" class="form-control" onchange="angular.element(this).scope().uploadFile(this.files)" ng-disabled="originalStatus === 'APPROVED' || originalStatus === 'DISAPPROVED'" style="padding: 4px 12px; height: auto;">
                      </div>

                      <div ng-if="uploading" style="color: #4e73df; font-style: italic; margin-bottom: 10px;">
                        <i class="fa fa-spinner fa-spin"></i> Uploading file, please wait...
                      </div>
                      
                      <label ng-if="uploadedFiles.length > 0">Pending Uploads</label>
                      <ul class="list-group" ng-if="uploadedFiles.length > 0" style="margin-bottom: 0; max-height: 120px; overflow-y: auto;">
                        <li class="list-group-item" ng-repeat="file in uploadedFiles" style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px;">
                          <span style="word-break: break-all;"><i class="fa fa-file-o" style="margin-right: 5px;"></i> {{file.filename}}</span>
                          <button type="button" class="btn btn-danger btn-xs" ng-click="removeUploadedFile($index)" style="padding: 2px 6px;">
                            <i class="fa fa-times"></i>
                          </button>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </form>
		    <hr>
			<div class="row">
				<div class="col-md-3 pull-right">
					<button type="button" class="btn btn-primary btn-sm btn-block" ng-click="update()" ng-disabled="originalStatus === 'APPROVED' || originalStatus === 'DISAPPROVED'">UPDATE</button>
				</div>
			</div>
    	</div>
    </div>
  </div>
</div>
<script>
$('#form').validationEngine('attach');
</script>

<!-- Beneficiary Add/Edit Modal Dialog -->
<div class="modal fade" id="beneficiaryModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">{{modalMode === 'add' ? 'ADD BENEFICIARY' : 'EDIT BENEFICIARY'}}</h4>
      </div>
      <div class="modal-body">
        <form id="beneficiaryForm">

          <div class="col-md-6">
            <div class="form-group">
              <label>FIRST NAME<i class="required">*</i></label>
              <input type="text" class="form-control" ng-model="tmpBeneficiary.first_name" data-validation-engine="validate[required]">
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>MIDDLE NAME</label>
              <input type="text" class="form-control" ng-model="tmpBeneficiary.middle_name">
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>LAST NAME<i class="required">*</i></label>
              <input type="text" class="form-control" ng-model="tmpBeneficiary.last_name" data-validation-engine="validate[required]">
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>RELATIONSHIP<i class="required">*</i></label>
              <select class="form-control" ng-model="tmpBeneficiary.relationship" data-validation-engine="validate[required]">
                <option value="">-- SELECT RELATIONSHIP --</option>
                <option value="Legitimate child">Legitimate child</option>
                <option value="Illegitimate child">Illegitimate child</option>
                <option value="Adopted child">Adopted child</option>
                <option value="Surviving spouse">Surviving spouse</option>
                <option value="Legitimate parent">Legitimate parent</option>
                <option value="Grandparent">Grandparent</option>
                <option value="Brother">Brother</option>
                <option value="Sister">Sister</option>
                <option value="Nephew">Nephew</option>
                <option value="Niece">Niece</option>
                <option value="Cousin">Cousin</option>
              </select>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>EMAIL ADDRESS</label>
              <input type="email" class="form-control" ng-model="tmpBeneficiary.email" data-validation-engine="validate[optional,custom[email]]" placeholder="Optional">
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label for="beneficiary_birth_date">BIRTH DATE<i class="required">*</i></label>
              <div class="date-picker-wrapper">
                <input type="text" class="date-input form-control" id="beneficiary_birth_date" ng-model="tmpBeneficiary.birth_date" data-validation-engine="validate[required]" readonly style="background-color: #ffffff; cursor: pointer;">
                <button type="button" class="date-picker-toggle" tabindex="-1">📅</button>
                <div class="date-picker-container" id="beneficiary_birth_date_picker"></div>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>AGE<i class="required">*</i></label>
              <input type="text" class="form-control" ng-model="tmpBeneficiary.age" data-validation-engine="validate[required]" readonly>
            </div>
          </div>

        </form>
       </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-sm btn-min" data-dismiss="modal">CANCEL</button>
        <button type="button" class="btn btn-primary btn-sm btn-min" ng-click="saveBeneficiary()">SAVE</button>
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
        var focusable = form.find('input, select, textarea').filter(':visible').not('[readonly]');
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
