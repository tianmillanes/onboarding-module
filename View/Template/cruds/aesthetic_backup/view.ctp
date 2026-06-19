<link rel="stylesheet" href="assets/css/crud.css">
<div class="crud-body">
  <div class="crud-card">
    <div class="crud-title-wrapper">
      <h2 class="crud-title"><i class="fa fa-info-circle"></i> Personal Record Details</h2>
    </div>

    <div class="row">
      <div class="col-md-12">
        <table class="crud-detail-table">
          <tr>
            <th>First Name</th>
            <td class="uppercase" style="font-weight: 500;">{{ data.Crud.first_name }}</td>
          </tr>
          <tr>
            <th>Middle Name</th>
            <td class="uppercase">{{ data.Crud.middle_name }}</td>
          </tr>
          <tr>
            <th>Last Name</th>
            <td class="uppercase" style="font-weight: 500;">{{ data.Crud.last_name }}</td>
          </tr>
          <tr>
            <th>Birth Date</th>
            <td>{{ data.Crud.birth_date }}</td>
          </tr>
          <tr>
            <th>Age</th>
            <td><span class="crud-badge"><strong>{{ data.Crud.age }}</strong></span></td>
          </tr>
          <tr>
            <th>Contact Number</th>
            <td>{{ data.Crud.contact_number }}</td>
          </tr>
          <tr>
            <th>Email Address</th>
            <td><a href="mailto:{{ data.Crud.email }}">{{ data.Crud.email }}</a></td>
          </tr>
          <tr>
            <th>Home Address</th>
            <td style="line-height: 1.6; color: #475569;">{{ data.Crud.address }}</td>
          </tr>
          <tr>
            <th>Record Created</th>
            <td>{{ data.Crud.created }}</td>
          </tr>
          <tr>
            <th>Last Modified</th>
            <td>{{ data.Crud.modified }}</td>
          </tr>
        </table>
      </div>
    </div>

    <!-- Beneficiaries Read-only Card -->
    <div class="crud-card" style="margin-top: 30px; border: 1px solid #e2e8f0; background: #ffffff;">
      <div class="crud-title-wrapper" style="border-bottom: 2px solid #f0f3f6; padding-bottom: 15px; margin-bottom: 20px;">
        <h3 class="crud-title" style="font-size: 18px; margin: 0; color: #1a252f;"><i class="fa fa-users" style="color: #6366f1;"></i> Beneficiaries</h3>
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
            </tr>
            <tr ng-if="!beneficiaries.length">
              <td colspan="6" style="text-align: center; color: #94a3b8; padding: 30px 15px;">
                <i class="fa fa-users" style="font-size: 28px; display: block; margin-bottom: 8px; color: #cbd5e1;"></i>
                No beneficiaries registered for this record.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <hr style="border-top: 1px solid #f1f5f9; margin: 30px 0;">

    <div class="row">
      <div class="col-md-3">
        <a href="#/crud" class="btn btn-default btn-sm btn-block" style="padding: 10px; border-radius: 10px; font-weight: 600;"><i class="fa fa-arrow-left"></i> Back to Panel</a>
      </div>
      <div class="col-md-3 pull-right">
        <a href="javascript:void(0)" ng-click="remove(data.Crud)" class="btn btn-danger btn-sm btn-block" style="padding: 10px; border-radius: 10px; font-weight: 600;"><i class="fa fa-trash"></i> Delete Record</a>
      </div>
      <div class="col-md-3 pull-right">
        <a href="#/crud/edit/{{ data.Crud.id }}" class="crud-btn-add btn-block" style="justify-content: center;"><i class="fa fa-edit"></i> Edit Record</a>
      </div>
    </div>
  </div>
</div>
