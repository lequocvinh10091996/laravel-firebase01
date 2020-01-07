@extends('layouts.main')

@section('title', 'Config type')
@section('title-current', 'List config type')
@section('content')
        <style>
            .pagination {
                margin-top: 20px;
            }
            .pagination>li {
                display: inline;
            }
            .pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover {
                z-index: 3;
                color: #fff;
                cursor: default;
                background-color: #337ab7;
                border-color: #337ab7;
            }
            .pagination>li>a{
                position: relative;
                float: left;
                padding: 6px 12px;
                margin-left: -1px;
                line-height: 1.42857143;
                color: #337ab7;
                text-decoration: none;
                background-color: #fff;
                border: 1px solid #ddd;
            }
            .loader {
                border: 5px solid #f3f3f3;
                border-radius: 69%;
                border-top: 5px solid #3498db;
                width: 21px;
                height: 18px;
                -webkit-animation: spin 2s linear infinite;
                animation: spin 2s linear infinite;
            }

              /* Safari */
            @-webkit-keyframes spin {
                0% { -webkit-transform: rotate(0deg); }
                100% { -webkit-transform: rotate(360deg); }
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
        <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
          <h5>List config type</h5>
          <div style="float: right;margin: 8px; margin-right: 16px;">
            <button type="button" id="btnThemMoi" class="btn btn-primary btn" ng-click="insertConfigType()">Insert</button>
          </div>
        </div>

        <div class="widget-content nopadding" >
            <span id="listMessage"></span>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th>Config Name</th>
                        <th>Config Descrip</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr  dir-paginate="configType in listConfigType|itemsPerPage: pageSize" current-page="currentPage">
                        <td class="center" style="text-align: center; width: 5%;"><% pageSize *(currentPage - 1) + $index + 1 %></td>
                        <td style="width: 15%;"><% configType.cty_config_name %></td>
                        <td><% configType.cty_config_descrip %></td>
                        <td class="center" style="text-align: center; width: 5%;white-space: nowrap;">
                            <button class="badge badge-info" ng-click="updateConfigType(pageSize *(currentPage - 1) + $index)" >Update</button>&nbsp;&nbsp;
                            <button class="badge badge-important" ng-click="deleteConfigType(pageSize *(currentPage - 1) + $index)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" >
        </dir-pagination-controls>
      <script >
        appName.controller('ConfigTypeController', function($scope, $http, MainUrl) {
          $scope.listConfigType = [];
          $scope.currentPage = 1;
          $scope.pageSize = 5;
          $scope.configType = {};
          let map = new Map();
          
            $http.get(MainUrl+'/configtype').then(function(response){
              data = response.data.data.listConfigType;
              if(data){
                  var index = 0;
                  $.each(data, function( key, value ) {
                    $scope.listConfigType.push(value);
                    map.set(index, key);
                    index++;
                  });
              }
            });
            console.log($scope.listConfigType);
            //insertConfigType
            $scope.insertConfigType = function(){
              $('.modal-title').html('Insert Config Type');
              $scope.configType = {};
              $('.control-group').removeClass('error');
              $('#mgs_cty_config_name').addClass('hidden');
              $('#myModal').modal('show');
            }
            //end insertConfigType

            //updateConfigType
            $scope.updateConfigType = function(index){
                $('.modal-title').html('Update Config Type');
                $scope.configType = {};
                $scope.configType = angular.copy($scope.listConfigType[index]);
                $scope.configType.index = index;
                $('.control-group').removeClass('error');
                $('#mgs_cty_config_name').addClass('hidden');
                $('#myModal').modal('show');
            }
            //end updateConfigType

            //actionSave insert|edit
            $scope.actionSave = function(){
              $('.loader').removeClass('hidden');
              $('.control-group').removeClass('error');
              $('#mgs_cty_config_name').addClass('hidden');
              $('.mgs_modal').addClass('hidden');
              var flag_ok = true;
              var Url = MainUrl+'/configtype';
              if(map.get($scope.configType.index)){
                Url += '/update';
                $scope.configType.keyConfigType = map.get($scope.configType.index);
              }
              if(angular.isUndefined($scope.configType.cty_config_name)){
                $('#cty_config_name').parents('.control-group').addClass('error');
                $('#mgs_cty_config_name').removeClass('hidden');
                flag_ok= false;
              }
              
              if(flag_ok == false){
                  $('.loader').addClass('hidden');
              }
              
              if(flag_ok == true){
                var reData = $.param($scope.configType);
                $http.post(Url, reData,
                {headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'}}
                ).then(function (response){
                  $('.loader').addClass('hidden');
                  if(response.data.error == true) {
                    $('#mgs_modal').html(response.data.data);
                    $('.mgs_modal').removeClass('hidden');
                  } else if(response.data.error == false) {
                    $('#myModal').modal('hide');
                    rowNew = $.parseJSON(response.data.data);
                    if(map.get($scope.configType.index)){
                      $scope.listConfigType[$scope.configType.index] = rowNew;
                      alertify.set('notifier', 'position', 'top-center');
                      alertify.success('Update row complete.');
                    } else{
                      alertify.set('notifier', 'position', 'top-center');
                      alertify.success('Insert row complete.');
                      $scope.listConfigType.push(rowNew);
                      map.set($scope.listConfigType.length-1, response.data.key);
                    }
                  }
                });
              }
            }
            //end actionSave
                   
            //deleteConfigType
            $scope.deleteConfigType = function(index){
                alertify.confirm('Confirm delete', 'Do you want to delete ['+$scope.listConfigType[index].cty_config_name+'] ?', function(){ 
                    var Url = MainUrl+'/configtype/delete';
                    $scope.configType.keyConfigType = map.get(index);
                    var reData = $.param($scope.configType);
                    $http.post(Url, reData,
                    {headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'}}
                    ).then(function (response){
                       if(response.data.error == false){
                          alertify.set('notifier', 'position', 'top-center');
                          alertify.success('Delete ['+$scope.listConfigType[index].cty_config_name+'] complete.').dismissOthers();
                          $scope.listConfigType.splice(index, 1);
                        }else if(response.data.error == true){
                            alertify.set('notifier', 'position', 'top-center');
                            alertify.error(response.data.data).dismissOthers();
                        }
                    });
                    }, function(){});
            }
            //end actionDelete
        });
</script>

@endsection

@section('modal-content')
<!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog" style="display: none;">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"></h4>
        </div>

        <div class="modal-body">
          <div class="widget-content nopadding">
            <div class="loader hidden"></div>
            <div class="mgs_modal alert alert-error hidden">
              <strong id="mgs_modal" ></strong>
            </div>
          <form name="frmInsertConfigType" action="#" class="form-horizontal" novalidate="novalidate">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="control-group">
                <label class="control-label">Config name <i class="icon icon-asterisk" style="color: red;"></i>:</label>
              <div class="controls">
                <input type="t" class="span6" id="cty_config_name" name="cty_config_name" placeholder="Config name"
                ng-model="configType.cty_config_name"
                ng-required="true" />
                <span for="cty_config_name" generated="true" id="mgs_cty_config_name"
                class="help-inline hidden"
                >Config name is required and can't be empty</span>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Config descrip :</label>
              <div class="controls">
                <textarea rows="4" cols="50" class="span6" id="cty_config_descrip" name="cty_config_descrip" placeholder="Config descrip"
                ng-model="configType.cty_config_descrip" ng-required="false" >
                </textarea>
              </div>
            </div>
          </form>
        </div>
        </div>
        <div class="form-actions">
              <button type="button" class="btn btn-success"
              ng-click="actionSave(null)">Submit</button>
        </div>
      </div>
      
    </div>
  </div>
  @endsection