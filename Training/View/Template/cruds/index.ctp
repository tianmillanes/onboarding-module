<div class="crud-glass-container">
  <div class="panel panel-primary">
    <div class="panel-heading"><i class="fa fa-dot-circle-o"></i> PERSONAL DATA DIRECTORY</div>

    <!-- ── Status Tabs ── -->
    <div style="padding: 15px 15px 15px 15px; border-bottom: 1px solid rgba(0, 0, 0, 0.08); margin-bottom: 15px;">
      <div class="crud-tab-bar" style="margin-bottom: 0px !important;">
        <button class="crud-tab-btn" ng-class="{'active': activeTab === 'ALL'}" ng-click="switchTab('ALL')">
          <i class="fa fa-list"></i> ALL
          <span class="tab-count" ng-show="tabCounts.ALL >= 0">{{ tabCounts.ALL }}</span>
        </button>
        <button ng-repeat="st in allStatuses" class="crud-tab-btn" ng-class="{'active': activeTab === st.name, 'tab-pending': st.name === 'PENDING', 'tab-approved': st.name === 'APPROVED', 'tab-disapproved': st.name === 'DISAPPROVED'}" ng-click="switchTab(st.name)">
          <i class="fa" ng-class="{'fa-clock-o': st.name === 'PENDING', 'fa-check-circle': st.name === 'APPROVED', 'fa-times-circle': st.name === 'DISAPPROVED', 'fa-tag': st.name !== 'PENDING' && st.name !== 'APPROVED' && st.name !== 'DISAPPROVED'}"></i> {{ st.name }}
          <span class="tab-count">{{ tabCounts[st.name] || 0 }}</span>
        </button>
      </div>
    </div>

    <div class="panel-body">

      <!-- ── Top Toolbar ── -->
      <div style="display: flex; gap: 12px; align-items: flex-start; margin-bottom: 15px; flex-wrap: wrap;">
        <div style="width: 210px;">
          <a href="#/crud/add" class="btn btn-primary btn-sm btn-block"><i class="fa fa-plus"></i> ADD NEW RECORD</a>
        </div>
        <div style="width: 230px;">
          <button class="btn btn-success btn-sm btn-block" ng-click="printPdf()">
            <i class="fa fa-print"></i> PRINT / EXPORT PDF
          </button>
        </div>
        <div style="width: 420px;">
          <div class="input-group input-group-sm">
            <input type="text" class="form-control search" placeholder="SEARCH by name, email, contact, address…"
                   ng-model="strSearch" ng-keyup="$event.keyCode==13 && search()">
            <span class="input-group-btn">
              <button class="btn btn-default" ng-click="search()" title="Search"><i class="fa fa-search"></i></button>
              <button class="btn btn-default" style="border-left: none;" data-toggle="modal" data-target="#filterModal" title="Advanced Filter"><i class="fa fa-filter"></i></button>
            </span>
          </div>
          <div style="font-size:10px;color:gray; margin-top: 6px;">Press Enter to search</div>
        </div>
      </div>

      <div class="clearfix"></div>

      <div class="col-md-12" style="margin-top:15px;">
        <div class="table-responsive glass-table-container" ng-if="cruds.length > 0">
          <table class="table table-bordered center crud-table">
          <thead>
            <tr>
              <th class="w50px text-center"><strong>#</strong></th>
              <th><strong>FIRST NAME</strong></th>
              <th><strong>MIDDLE NAME</strong></th>
              <th><strong>LAST NAME</strong></th>
              <th><strong>BIRTH DATE</strong></th>
              <th class="w80px text-center"><strong>AGE</strong></th>
              <th><strong>CONTACT NUMBER</strong></th>
              <th><strong>EMAIL ADDRESS</strong></th>
              <th><strong>ADDRESS</strong></th>
              <th class="text-center" style="min-width:110px;"><strong>STATUS</strong></th>
              <th class="text-center" style="min-width:240px;"><strong>ACTIONS</strong></th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="data in cruds" ng-class="{'row-locked': data.status === 'APPROVED' || data.status === 'DISAPPROVED'}">
              <td class="text-center">{{ (paginator.page - 1) * paginator.limit + $index + 1 }}</td>
              <td class="uppercase">{{ data.first_name }}</td>
              <td class="uppercase">{{ data.middle_name }}</td>
              <td class="uppercase">{{ data.last_name }}</td>
              <td>{{ data.birth_date }}</td>
              <td class="text-center">{{ data.age }}</td>
              <td>{{ data.contact_number }}</td>
              <td><a href="mailto:{{ data.email }}">{{ data.email }}</a></td>
              <td>{{ data.address }}</td>
              <td class="text-center">
                <span class="status-badge"
                      ng-class="{
                        'status-pending':      data.status === 'PENDING'      || !data.status,
                        'status-approved':     data.status === 'APPROVED',
                        'status-disapproved':  data.status === 'DISAPPROVED'
                      }"
                      ng-style="data.status !== 'PENDING' && data.status !== 'APPROVED' && data.status !== 'DISAPPROVED' && {'background': '#e8f4fd', 'color': '#0071e3', 'border': 'none'}">
                  {{ data.status || 'PENDING' }}
                </span>
              </td>
              <td class="text-center crud-actions">
                <!-- VIEW: always enabled -->
                <a href="#/crud/view/{{ data.id }}" class="crud-btn crud-btn-view" title="View Record">
                  <i class="fa fa-eye"></i> VIEW
                </a>
                <!-- EDIT: disabled when APPROVED or DISAPPROVED -->
                <a ng-if="data.status !== 'APPROVED' && data.status !== 'DISAPPROVED'"
                   href="#/crud/edit/{{ data.id }}" class="crud-btn crud-btn-edit" title="Edit Record">
                  <i class="fa fa-pencil"></i> EDIT
                </a>
                <span ng-if="data.status === 'APPROVED' || data.status === 'DISAPPROVED'"
                      class="crud-btn crud-btn-disabled" title="Cannot edit — record is {{ data.status }}">
                  <i class="fa fa-pencil"></i> EDIT
                </span>
                <!-- DELETE: disabled when APPROVED or DISAPPROVED -->
                <a ng-if="data.status !== 'APPROVED' && data.status !== 'DISAPPROVED'"
                   href="javascript:void(0)" ng-click="remove(data)" class="crud-btn crud-btn-delete" title="Delete Record">
                  <i class="fa fa-trash"></i> DELETE
                </a>
                <span ng-if="data.status === 'APPROVED' || data.status === 'DISAPPROVED'"
                      class="crud-btn crud-btn-disabled" title="Cannot delete — record is {{ data.status }}">
                  <i class="fa fa-trash"></i> DELETE
                </span>
              </td>
            </tr>
          </tbody>
          </table>
        </div>

        <div ng-if="cruds.length == 0" class="text-center" style="padding: 50px 20px; margin-top: 15px;">
          <i class="fa fa-folder-o" style="font-size: 48px; color: #ccd3e0; margin-bottom: 12px; display: inline-block;"></i>
          <div style="font-size: 15px; color: #7f8c9d; font-style: italic; font-weight: normal;">
            <span ng-if="lastSearch.strSearch">There's no record for "{{ lastSearch.strSearch }}"</span>
            <span ng-if="!lastSearch.strSearch && (lastSearch.advFirstName || lastSearch.advMiddleName || lastSearch.advLastName || lastSearch.advBirthDate || (lastSearch.advAge !== undefined && lastSearch.advAge !== null && lastSearch.advAge !== '') || lastSearch.advContactNumber || lastSearch.advEmail || lastSearch.advAddress || (lastSearch.advStatus && lastSearch.advStatus !== 'ALL'))">
              {{ getAdvancedSearchSummary() }}
            </span>
            <span ng-if="!lastSearch.strSearch && !(lastSearch.advFirstName || lastSearch.advMiddleName || lastSearch.advLastName || lastSearch.advBirthDate || (lastSearch.advAge !== undefined && lastSearch.advAge !== null && lastSearch.advAge !== '') || lastSearch.advContactNumber || lastSearch.advEmail || lastSearch.advAddress || (lastSearch.advStatus && lastSearch.advStatus !== 'ALL'))">
              No records found<span ng-show="lastSearch.activeTab !== 'ALL'"> with status <strong>{{ lastSearch.activeTab }}</strong></span>.
            </span>
          </div>
        </div>
      </div>

    </div><!-- /panel-body -->

    <!-- ── Pagination (tab + filter-aware) ── -->
    <ul class="pagination pull-right" ng-if="paginator.pageCount > 1">
      <li class="pagination-page">
        <a href="javascript:void(0)" ng-click="goPage(1)"><sub>&laquo;&laquo;</sub></a>
      </li>
      <li class="prevPage {{ !paginator.prevPage? 'disabled':'' }}">
        <a href="javascript:void(0)" ng-click="goPage(paginator.page - 1)">&laquo;</a>
      </li>
      <li ng-repeat="page in pages" class="pagination-page {{ paginator.page == page.number ? 'active':''}}">
        <a href="javascript:void(0)" class="text-center" ng-click="goPage(page.number)">{{ page.number }}</a>
      </li>
      <li class="nextPage {{ !paginator.nextPage? 'disabled':'' }}">
        <a href="javascript:void(0)" ng-click="goPage(paginator.page + 1)">&raquo;</a>
      </li>
      <li class="pagination-page">
        <a href="javascript:void(0)" ng-click="goPage(paginator.pageCount)"><sub>&raquo;&raquo;</sub></a>
      </li>
    </ul>

    <div class="clearfix"></div>

    <div class="pull-right" ng-show="paginator.pageCount > 0" style="padding:0 15px 10px;">
      <sup class="text-primary">Page {{ paginator.pageCount > 0 ? paginator.page : 0 }} out of {{ paginator.pageCount }}</sup>
    </div>

  </div><!-- /panel -->

  <!-- ── Advanced Search Modal ── -->
  <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" style="margin-top: 10vh;">
      <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
        <div class="modal-header" style="background-color: #f5f5f7; border-bottom: 1px solid #e5e5ea; padding: 15px 20px;">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="margin-top: 2px;">&times;</button>
          <h4 class="modal-title" style="font-weight: 600; font-size: 15px; color: #1d1d1f; display: flex; align-items: center; gap: 8px;">
            <i class="fa fa-filter" style="color: #0071e3;"></i> ADVANCED SEARCH FILTERS
          </h4>
        </div>
        <div class="modal-body" style="padding: 20px;">
          <div class="row">
            <div class="col-md-6" style="margin-bottom: 15px;">
              <div class="form-group" style="margin-bottom: 0;">
                <label class="adv-label" style="font-size: 10px; font-weight: 600; color: #86868b; text-transform: uppercase; margin-bottom: 4px; display: block;">FIRST NAME</label>
                <input type="text" class="form-control input-sm" ng-model="advFirstName" placeholder="Enter first name…">
              </div>
            </div>
            <div class="col-md-6" style="margin-bottom: 15px;">
              <div class="form-group" style="margin-bottom: 0;">
                <label class="adv-label" style="font-size: 10px; font-weight: 600; color: #86868b; text-transform: uppercase; margin-bottom: 4px; display: block;">MIDDLE NAME</label>
                <input type="text" class="form-control input-sm" ng-model="advMiddleName" placeholder="Enter middle name…">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6" style="margin-bottom: 15px;">
              <div class="form-group" style="margin-bottom: 0;">
                <label class="adv-label" style="font-size: 10px; font-weight: 600; color: #86868b; text-transform: uppercase; margin-bottom: 4px; display: block;">LAST NAME</label>
                <input type="text" class="form-control input-sm" ng-model="advLastName" placeholder="Enter last name…">
              </div>
            </div>
            <div class="col-md-6" style="margin-bottom: 15px;">
              <div class="form-group" style="margin-bottom: 0;">
                <label class="adv-label" style="font-size: 10px; font-weight: 600; color: #86868b; text-transform: uppercase; margin-bottom: 4px; display: block;">BIRTH DATE</label>
                <div class="date-picker-wrapper">
                  <input type="text" class="form-control input-sm" id="adv_birth_date" ng-model="advBirthDate" placeholder="MM/DD/YYYY" readonly style="background-color: #fff; cursor: pointer;">
                  <span class="date-picker-btn"><i class="fa fa-calendar"></i></span>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6" style="margin-bottom: 15px;">
              <div class="form-group" style="margin-bottom: 0;">
                <label class="adv-label" style="font-size: 10px; font-weight: 600; color: #86868b; text-transform: uppercase; margin-bottom: 4px; display: block;">AGE</label>
                <input type="number" class="form-control input-sm" ng-model="advAge" placeholder="Enter age…" min="0" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
              </div>
            </div>
            <div class="col-md-6" style="margin-bottom: 15px;">
              <div class="form-group" style="margin-bottom: 0;">
                <label class="adv-label" style="font-size: 10px; font-weight: 600; color: #86868b; text-transform: uppercase; margin-bottom: 4px; display: block;">CONTACT NUMBER</label>
                <input type="text" class="form-control input-sm" ng-model="advContactNumber" placeholder="Enter contact number…" maxlength="11" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6" style="margin-bottom: 15px;">
              <div class="form-group" style="margin-bottom: 0;">
                <label class="adv-label" style="font-size: 10px; font-weight: 600; color: #86868b; text-transform: uppercase; margin-bottom: 4px; display: block;">EMAIL ADDRESS</label>
                <input type="text" class="form-control input-sm" ng-model="advEmail" placeholder="Enter email address…">
              </div>
            </div>
            <div class="col-md-6" style="margin-bottom: 15px;">
              <div class="form-group" style="margin-bottom: 0;">
                <label class="adv-label" style="font-size: 10px; font-weight: 600; color: #86868b; text-transform: uppercase; margin-bottom: 4px; display: block;">ADDRESS</label>
                <input type="text" class="form-control input-sm" ng-model="advAddress" placeholder="Enter address…">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group" style="margin-bottom: 0;">
                <label class="adv-label" style="font-size: 10px; font-weight: 600; color: #86868b; text-transform: uppercase; margin-bottom: 4px; display: block;">STATUS</label>
                <select class="form-control input-sm" ng-model="advStatus">
                  <option value="ALL">ALL STATUSES</option>
                  <option ng-repeat="st in allStatuses" value="{{st.name}}">{{st.name}}</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="background-color: #f5f5f7; border-top: 1px solid #e5e5ea; padding: 12px 20px; display: flex; justify-content: flex-end; gap: 8px;">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" style="margin: 0;">Cancel</button>
          <button type="button" class="btn btn-default btn-sm" ng-click="resetAdvanced()" data-dismiss="modal" style="margin: 0; background-color: #ffffff;"><i class="fa fa-refresh"></i> Reset</button>
          <button type="button" class="btn btn-primary btn-sm" ng-click="applyAdvanced()" data-dismiss="modal" style="margin: 0;"><i class="fa fa-filter"></i> Apply Filters</button>
        </div>
      </div>
    </div>
  </div>
</div>

