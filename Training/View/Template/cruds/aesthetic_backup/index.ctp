<link rel="stylesheet" href="assets/css/crud.css">
<div class="crud-body">
  <div class="crud-card">
    <div class="crud-title-wrapper">
      <h2 class="crud-title"><i class="fa fa-user"></i> Personal Data Directory</h2>
      <a href="#/crud/add" class="crud-btn-add"><i class="fa fa-user-plus"></i> Add New Record</a>
    </div>

    <div class="row" style="margin-bottom: 20px;">
      <div class="col-md-12">
        <div class="crud-search-wrapper pull-right">
          <input type="text" class="crud-search-input" placeholder="Search name, contact, email..." ng-model="strSearch" ng-enter="search(strSearch)">
          <i class="fa fa-search crud-search-icon"></i>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="crud-table" ng-if="cruds.length > 0">
        <thead>
          <tr>
            <th class="w50px text-center">#</th>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>Birth Date</th>
            <th class="w80px text-center">Age</th>
            <th>Contact Number</th>
            <th>Email Address</th>
            <th>Address</th>
            <th class="w120px text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="data in cruds">
            <td class="text-center"><span class="crud-badge">{{ (paginator.page - 1) * paginator.limit + $index + 1 }}</span></td>
            <td class="uppercase"><strong>{{ data.first_name }}</strong></td>
            <td class="uppercase">{{ data.middle_name }}</td>
            <td class="uppercase"><strong>{{ data.last_name }}</strong></td>
            <td>{{ data.birth_date }}</td>
            <td class="text-center"><span class="crud-badge"><strong>{{ data.age }}</strong></span></td>
            <td>{{ data.contact_number }}</td>
            <td><a href="mailto:{{ data.email }}">{{ data.email }}</a></td>
            <td>{{ data.address }}</td>
            <td class="text-center">
              <div class="crud-action-btns">
                <a href="#/crud/view/{{ data.id }}" class="crud-btn-action crud-btn-view" title="View Record"><i class="fa fa-eye"></i></a>
                <a href="#/crud/edit/{{ data.id }}" class="crud-btn-action crud-btn-edit" title="Edit Record"><i class="fa fa-edit"></i></a>
                <a href="javascript:void(0)" ng-click="remove(data)" class="crud-btn-action crud-btn-delete" title="Delete Record"><i class="fa fa-trash"></i></a>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Empty state when no data exists -->
      <div class="crud-empty-state" ng-if="cruds.length == 0">
        <i class="fa fa-users crud-empty-icon"></i>
        <div class="crud-empty-text">No personal records found. Click "Add New Record" to start.</div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="row" style="margin-top: 20px;" ng-if="paginator.pageCount > 1">
      <div class="col-md-12">
        <ul class="pagination pull-right" style="margin: 0;">
          <li class="pagination-page">
            <a href="javascript:void(0)" ng-click="load({ page: 1, search: searchTxt })"><sub>&laquo;&laquo;</sub></a>
          </li>
          <li class="prevPage {{ !paginator.prevPage? 'disabled':'' }}">
            <a href="javascript:void(0)" ng-click="load({ page: paginator.page - 1, search: searchTxt })">&laquo;</a>
          </li>
          <li ng-repeat="page in pages" class="pagination-page {{ paginator.page == page.number ? 'active':''}}" >
            <a href="javascript:void(0)" class="text-center" ng-click="load({ page: page.number, search: searchTxt })">{{ page.number }}</a>
          </li>
          <li class="nextPage {{ !paginator.nextPage? 'disabled':'' }}">
            <a href="javascript:void(0)" ng-click="load({ page: paginator.page + 1, search: searchTxt })">&raquo;</a>
          </li>
          <li class="pagination-page">
            <a href="javascript:void(0)" ng-click="load({ page: paginator.pageCount, search: searchTxt })"><sub>&raquo;&raquo;</sub></a>
          </li>
        </ul>
        <div class="pull-right" style="margin-right: 15px; margin-top: 8px;">
          <sup class="text-primary">Page {{ paginator.page }} of {{ paginator.pageCount }}</sup>
        </div>
      </div>
    </div>
  </div>
</div>
