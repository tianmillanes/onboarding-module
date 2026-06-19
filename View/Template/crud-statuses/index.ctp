<div class="panel panel-primary">
  <div class="panel-heading"><i class="fa fa-dot-circle-o"></i> CRUD STATUS MANAGEMENT </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-md-3">
        <button type="button" class="btn btn-primary btn-sm btn-block" ng-click="openModal()"><i class="fa fa-plus"></i> ADD CRUD STATUS</button>
      </div>
        
      <div class="col-md-4 pull-right">
    	  <input type="text" class="form-control search" placeholder="SEARCH HERE" ng-model="strSearch" ng-keyup="$event.keyCode==13 && search()">
        <sup style="font-size:10px;color:gray">Press Enter to search</sup>
    	</div>
			
      <div class="clearfix"></div><hr>

      <div class="col-md-12">
        <table class="table table-bordered center" ng-if="statuses.length > 0">
          <thead>
            <tr>
              <th class="w50px text-center">#</th>
              <th>STATUS NAME</th>
              <th>DATE CREATED</th>
              <th>DATE MODIFIED</th>
              <th class="w120px text-center">ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="data in statuses">
              <td class="text-center">{{ (paginator.page - 1) * paginator.limit +  $index + 1 }}</td>
              <td class="uppercase"><strong>{{ data.name }}</strong></td>
              <td>{{ data.created }}</td>
              <td>{{ data.modified }}</td>
              <td class="text-center">
                <div class="btn-group btn-group-xs">
                  <button type="button" ng-click="openModal(data)" class="btn btn-primary" title="EDIT"><i class="fa fa-edit"></i></button> 
                  <button type="button" ng-click="remove(data)" class="btn btn-danger" title="DELETE"><i class="fa fa-trash"></i></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        
        <div ng-if="statuses.length == 0" class="text-center" style="padding: 50px 20px;">
          <i class="fa fa-folder-o" style="font-size: 48px; color: #ccd3e0; margin-bottom: 12px; display: inline-block;"></i>
          <div style="font-size: 15px; color: #7f8c9d; font-style: italic; font-weight: normal;">
            No statuses found.
          </div>
        </div>
      </div>
    </div>
    
    <!-- Pagination -->
    <ul class="pagination pull-right" ng-if="paginator.pageCount > 1">
      <li class="pagination-page">
        <a href="javascript:void(0)" ng-click="goPage(1)"><sub>&laquo;&laquo;</sub></a>
      </li>
      <li class="prevPage {{ !paginator.prevPage? 'disabled':'' }}">
        <a href="javascript:void(0)" ng-click="goPage(paginator.page - 1)">&laquo;</a>
      </li>
      <li ng-repeat="page in pages" class="pagination-page {{ paginator.page == page.number ? 'active':''}}" >
        <a href="javascript:void(0)" class="text-center" ng-click="goPage(page.number)">{{ page.number }}</a>
      </li>
      <li class="nextPage {{ !paginator.nextPage? 'disabled':'' }}">
        <a href="javascript:void(0)" ng-click="goPage(paginator.page + 1)">&raquo;</a>
      </li>
      <li class="pagination-page">
        <a href="javascript:void(0)" ng-click="goPage(paginator.pageCount)"><sub>&raquo;&raquo;</sub> </a>
      </li>
    </ul>

    <div class="clearfix"></div>
        
    <div class="pull-right" ng-show="paginator.pageCount > 0">
      <sup class="text-primary">Page {{ paginator.pageCount > 0 ? paginator.page : 0 }} out of {{ paginator.pageCount }}</sup>
    </div>
  </div>
</div>

<!-- Add/Edit Status Modal Dialog -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm" style="margin-top: 15vh;">
    <div class="modal-content" style="border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background-color: #f5f5f7; border-bottom: 1px solid #e5e5ea; padding: 12px 15px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" style="font-weight: 600; font-size: 14px; color: #1d1d1f;">
          <i class="fa fa-tags" style="color: #0071e3; margin-right: 5px;"></i>
          {{modalMode === 'add' ? 'ADD CRUD STATUS' : 'EDIT CRUD STATUS'}}
        </h4>
      </div>
      <div class="modal-body" style="padding: 15px;">
        <form id="statusForm" ng-submit="saveStatus()" onsubmit="return false;">
          <div class="form-group" style="margin-bottom: 0;">
            <label style="font-size: 11px; font-weight: 600; color: #86868b; text-transform: uppercase; margin-bottom: 5px; display: block;">Status Name <i class="required" style="color:red;">*</i></label>
            <input type="text" class="form-control text-uppercase" ng-model="tmpStatus.name" data-validation-engine="validate[required]" placeholder="e.g. COMPLICATED" style="text-transform: uppercase;">
          </div>
        </form>
      </div>
      <div class="modal-footer" style="background-color: #f5f5f7; border-top: 1px solid #e5e5ea; padding: 10px 15px; display: flex; justify-content: flex-end; gap: 8px;">
        <button type="button" class="btn btn-default btn-xs" data-dismiss="modal" style="margin: 0; padding: 4px 10px;">CANCEL</button>
        <button type="button" class="btn btn-primary btn-xs" ng-click="saveStatus()" style="margin: 0; padding: 4px 10px;">SAVE</button>
      </div>
    </div>
  </div>
</div>

<script>
  $('#statusForm').validationEngine('attach');
</script>
