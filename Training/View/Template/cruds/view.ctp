<div class="crud-glass-container">
  <!-- Back Button at the top left -->
  <div class="row" style="margin-bottom: 15px;">
    <div class="col-md-12">
      <a href="#/crud" class="back-link-btn"><i class="fa fa-arrow-left"></i> BACK TO RECORDS</a>
    </div>
  </div>

  <div class="panel panel-custom-detail">
    <div class="panel-heading-custom" style="display:flex; justify-content:space-between; align-items:center; flex-wrap: wrap; gap: 15px; padding: 12px 20px;">
      <div class="header-title-container" style="display:flex; align-items:center; gap:8px; margin: 0;">
        <i class="fa fa-user-circle" style="font-size: 20px; color:#4e73df; margin: 0;"></i>
        <span style="font-weight:700; font-size:15px; color:#4e73df; letter-spacing:0.5px;">PERSONAL RECORD DETAIL</span>
      </div>
      
      <div style="display:flex; align-items:center; gap:8px; flex-wrap: wrap;">
        <!-- Status Badge -->
        <span class="status-badge-detail"
              ng-class="{
                'status-detail-pending':      data.Crud.status === 'PENDING'      || !data.Crud.status,
                'status-detail-approved':     data.Crud.status === 'APPROVED',
                'status-detail-disapproved':  data.Crud.status === 'DISAPPROVED'
              }" style="margin: 0; padding: 5px 12px; font-size: 11px;">
          <i class="fa"
             ng-class="{
               'fa-clock-o':   data.Crud.status === 'PENDING'  || !data.Crud.status,
               'fa-check':     data.Crud.status === 'APPROVED',
               'fa-times':     data.Crud.status === 'DISAPPROVED'
             }"></i>
          {{ data.Crud.status || 'PENDING' }}
        </span>

        <span style="border-left: 1px solid #d1d3e2; height: 18px; margin: 0 4px;"></span>
        
        <a href="#/crud/edit/{{ data.Crud.id }}" class="detail-action-btn btn-edit-premium" ng-class="{'disabled': processing || data.Crud.status === 'APPROVED' || data.Crud.status === 'DISAPPROVED'}" style="padding: 6px 14px; font-size: 11px; margin: 0;">
          <i class="fa fa-edit"></i> EDIT RECORD
        </a>
        <button type="button" ng-click="remove(data.Crud)" class="detail-action-btn btn-delete-premium" ng-disabled="processing || data.Crud.status === 'APPROVED' || data.Crud.status === 'DISAPPROVED'" style="padding: 6px 14px; font-size: 11px; margin: 0;">
          <i class="fa fa-trash-o"></i> DELETE RECORD
        </button>

        <span style="border-left: 1px solid #d1d3e2; height: 18px; margin: 0 4px;"></span>

        <button type="button" ng-click="printRecord()" class="detail-action-btn btn-print-premium" ng-disabled="processing || data.Crud.status !== 'APPROVED'" style="padding: 6px 14px; font-size: 11px; margin: 0;">
          <i class="fa fa-print"></i> Print
        </button>
      </div>
    </div>
    
    <div class="panel-body-custom">
      <div class="row">
        <!-- Left Half: Personal Details & Contact Details -->
        <div class="col-md-6" style="margin-bottom: 20px;">
          <!-- Card 1: Basic Info -->
          <div class="info-card" style="margin-bottom: 20px;">
            <h4 class="card-section-title"><i class="fa fa-id-card-o"></i> Personal Details</h4>
            <div class="info-item">
              <span class="info-item-label">First Name</span>
              <span class="info-item-value text-uppercase">{{ data.Crud.first_name }}</span>
            </div>
            <div class="info-item">
              <span class="info-item-label">Middle Name</span>
              <span class="info-item-value text-uppercase">{{ data.Crud.middle_name || '-' }}</span>
            </div>
            <div class="info-item">
              <span class="info-item-label">Last Name</span>
              <span class="info-item-value text-uppercase">{{ data.Crud.last_name }}</span>
            </div>
            <div class="row" style="margin: 0 -10px;">
              <div class="col-xs-6" style="padding: 0 10px;">
                <div class="info-item" style="border-bottom: none; padding-bottom: 0;">
                  <span class="info-item-label">Birth Date</span>
                  <span class="info-item-value"><i class="fa fa-calendar icon-spaced"></i> {{ data.Crud.birth_date }}</span>
                </div>
              </div>
              <div class="col-xs-6" style="padding: 0 10px;">
                <div class="info-item" style="border-bottom: none; padding-bottom: 0;">
                  <span class="info-item-label">Age</span>
                  <span class="info-item-value"><span class="font-badge">{{ data.Crud.age }} yrs old</span></span>
                </div>
              </div>
            </div>
          </div>

          <!-- Card 2: Contact Info -->
          <div class="info-card">
            <h4 class="card-section-title"><i class="fa fa-envelope-o"></i> Contact Details</h4>
            <div class="info-item">
              <span class="info-item-label">Contact Number</span>
              <span class="info-item-value"><i class="fa fa-phone icon-spaced"></i> {{ data.Crud.contact_number }}</span>
            </div>
            <div class="info-item">
              <span class="info-item-label">Email Address</span>
              <span class="info-item-value"><i class="fa fa-envelope icon-spaced"></i> <a href="mailto:{{ data.Crud.email }}" class="email-link">{{ data.Crud.email }}</a></span>
            </div>
            <div class="info-item" style="border-bottom: none; padding-bottom: 0;">
              <span class="info-item-label">Home Address</span>
              <span class="info-item-value address-box"><i class="fa fa-map-marker icon-spaced"></i> {{ data.Crud.address }}</span>
            </div>
          </div>
        </div>

        <!-- Right Half: Registered Beneficiaries and Attached Documents -->
        <div class="col-md-6" style="margin-bottom: 20px;">
          <!-- Beneficiaries Table Card -->
          <div class="table-card">
            <div class="table-card-header">
              <i class="fa fa-users"></i>
              <span>Registered Beneficiaries</span>
              <span class="badge badge-info-custom">{{ beneficiaries.length || 0 }}</span>
            </div>
            <div class="table-responsive">
              <table class="table table-custom-view">
                <thead>
                  <tr>
                    <th class="w50px text-center">#</th>
                    <th>FULL NAME</th>
                    <th>RELATIONSHIP</th>
                    <th>EMAIL</th>
                    <th class="text-center">BIRTH DATE</th>
                    <th class="text-center w100px">AGE</th>
                  </tr>
                </thead>
                <tbody>
                  <tr ng-repeat="b in beneficiaries">
                    <td class="text-center font-bold" style="color: #858796;">{{ $index + 1 }}</td>
                    <td class="text-uppercase" style="font-weight: 600;">{{ b.first_name }} {{ b.middle_name }} {{ b.last_name }}</td>
                    <td><span class="relationship-label">{{ b.relationship }}</span></td>
                    <td>{{ b.email || '-' }}</td>
                    <td class="text-center"><i class="fa fa-calendar-o icon-spaced"></i> {{ b.birth_date }}</td>
                    <td class="text-center"><span class="age-badge">{{ b.age }}</span></td>
                  </tr>
                </tbody>
                <tbody ng-if="!beneficiaries.length">
                  <tr>
                    <td colspan="5" class="text-center empty-row-placeholder">
                      <i class="fa fa-folder-open-o empty-icon"></i>
                      <div style="font-weight: 300; margin-top: 5px; font-style: italic;">No registered beneficiaries found.</div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Attached Files Card -->
          <div class="table-card" style="margin-top: 20px;">
            <div class="table-card-header" style="background-color: #f8f9fc;">
              <i class="fa fa-paperclip"></i>
              <span>Attached Documents</span>
              <span class="badge badge-info-custom">{{ files.length || 0 }}</span>
            </div>
            <div class="panel-body" style="padding: 15px;">
              <div class="list-group" style="margin-bottom: 0;">
                <a ng-repeat="f in files" href="{{ basePath + f.filepath }}" download="{{ f.filename }}" target="_blank" class="list-group-item" style="display: flex; align-items: center; justify-content: space-between; transition: background 0.2s;">
                  <span style="font-weight: 600; color: #4e73df;"><i class="fa fa-file-o" style="margin-right: 8px;"></i> {{ f.filename }}</span>
                  <span class="btn btn-default btn-xs"><i class="fa fa-download"></i> Download</span>
                </a>
                <div class="text-center text-muted" ng-if="files.length === 0" style="padding: 20px; font-style: italic;">
                  <i class="fa fa-folder-open-o" style="font-size: 24px; color: #dddfeb; display: block; margin-bottom: 5px;"></i>
                  No attachments uploaded for this record.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
