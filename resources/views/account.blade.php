@extends('layouts.main')

@section('title', 'Account')
@section('title-current', 'List account')
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
          <h5>List account</h5>
          <div style="float: right;margin: 8px; margin-right: 16px;">
            <button type="button" id="btnThemMoi" class="btn btn-primary btn" ng-click="insertAccount()">Insert</button>
          </div>
        </div>
        <label style="margin: 5px 0px -15px 5px;"><b>Search:</b> <input ng-model="search.$"></label><br>
        <div class="widget-content nopadding" >
            <span id="listMessage"></span>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr  dir-paginate="account in listAccount | filter:search:strict | itemsPerPage: pageSize" current-page="currentPage">
                        <td class="center" style="text-align: center; width: 5%;"><% pageSize * (currentPage - 1) + $index + 1 %></td>
                        <td style="width: 15%;"><% account.acc_username %></td>
                        <td><% account.acc_email %></td>
                        <td class="center" style="text-align: center; width: 5%;white-space: nowrap;">
<!--                            <button class="badge badge-info" ng-click="updateAccount(listAccount.indexOf(account))" >Update</button>&nbsp;&nbsp;
                            <button class="badge badge-important" ng-click="deleteAccount(listAccount.indexOf(account))">Delete</button>-->
                            <div class="btn-group">
                                <div class="btn-group dropleft" role="group">
                                    <button type="button" class="btn btn-warning">Action</button>
                                    <button type="button" class="btn btn-warning dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="icon icon-sort-down"></i>
                                    </button>
                                    <div class="dropdown-menu" style="min-width: 103px !important;">
                                        <li>
                                            <a href="" ng-click="updateAccount(listAccount.indexOf(account))"><b style="font-size: 14px;">Update</b></a>
                                        </li>
                                        <li>
                                            <a href="" ng-click="deleteAccount(listAccount.indexOf(account))"><b style="font-size: 14px;">Delete</b></a>
                                        </li>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" >
        </dir-pagination-controls>
      <script >
        appName.controller('AccountController', function($scope, $http, MainUrl) {
          $scope.listAccount = [];
          $scope.currentPage = 1;
          $scope.pageSize = 10;
          $scope.account = {};
          let map = new Map();
          
            $http.get(MainUrl+'/account').then(function(response){
              data = response.data.data.listAccount;
              if(data){
                  var index = 0;
                  $.each(data, function( key, value ) {
                      if(key != 'admin01'){
                          $scope.listAccount.push(value);
                          map.set(index, key);
                          index++;
                      }
                  });
              }
            });
            //insertAccount
            $scope.insertAccount = function(){
              $('.modal-title').html('Insert account');
              $('.mgs_modal').addClass('hidden');
              $scope.account = {};
              $('.control-group').removeClass('error');
              $('#mgs_username').addClass('hidden');
              $('#mgs_password').addClass('hidden');
              $('#mgs_email').addClass('hidden');
              $('#myModal').modal('show');
            }
            //end insertAccount

            //updateAccount
            $scope.updateAccount = function(index){
                $('.modal-title').html('Update account');
                $('.mgs_modal').addClass('hidden');
                $scope.account = {};
                $scope.account = angular.copy($scope.listAccount[index]);
                $scope.account.index = index;
                $('.control-group').removeClass('error');
                $('#mgs_username').addClass('hidden');
                $('#mgs_password').addClass('hidden');
                $('#mgs_email').addClass('hidden');
                $('#myModal').modal('show');
            }
            //end updateAccount

            //actionSave insert|edit
            $scope.actionSave = function(){
              $('.loader').removeClass('hidden');
              $('.control-group').removeClass('error');
              $('#mgs_username').addClass('hidden');
              $('#mgs_password').addClass('hidden');
              $('#mgs_email').addClass('hidden');
              $('.mgs_modal').addClass('hidden');
              var flag_ok = true;
              var Url = MainUrl+'/account';
              if(map.get($scope.account.index)){
                Url += '/update';
                $scope.account.keyAccount = map.get($scope.account.index);
              }
              if((angular.isUndefined($scope.account.acc_username) &&
                  angular.isUndefined($scope.account.acc_password) &&
                  angular.isUndefined($scope.account.acc_email))) {
                    $('.control-group').addClass('error');
                    $('#mgs_username').removeClass('hidden');
                    $('#mgs_password').removeClass('hidden');
                    $('#mgs_email').removeClass('hidden');
                    flag_ok= false;
              } else if(angular.isUndefined($scope.account.acc_username)){
                $('#username').parents('.control-group').addClass('error');
                $('#mgs_username').removeClass('hidden');
                flag_ok= false;
              } else if(angular.isUndefined($scope.account.acc_password)){
                $('#password').parents('.control-group').addClass('error');
                $('#mgs_password').removeClass('hidden');
                flag_ok= false;
              } else if(angular.isUndefined($scope.account.acc_email)){
                $('#email').parents('.control-group').addClass('error');
                $('#mgs_email').removeClass('hidden');
                flag_ok= false;
              }
              
              if(flag_ok == false){
                  $('.loader').addClass('hidden');
              }
              
              if(flag_ok == true){
                var reData = $.param($scope.account);
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
                    if(map.get($scope.account.index)){
                      $scope.listAccount[$scope.account.index] = rowNew;
                      alertify.set('notifier', 'position', 'top-center');
                      alertify.success('Update row complete.');
                    } else{
                      alertify.set('notifier', 'position', 'top-center');
                      alertify.success('Insert row complete.');
                      $scope.listAccount.push(rowNew);
                      map.set($scope.listAccount.length-1, response.data.key);
                    }
                  }
                });
              }
            }
            //end actionSave
                   
            //actionDelete
            $scope.deleteAccount = function(index){
                alertify.confirm('Confirm delete', 'Do you want to delete ?', function(){ 
                    var Url = MainUrl+'/account/delete';
                    $scope.account.keyAccount = map.get(index);
                    var reData = $.param($scope.account);
                    $http.post(Url, reData,
                    {headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'}}
                    ).then(function (response){
                       if(response.data.error == false){
                          alertify.set('notifier', 'position', 'top-center');
                          alertify.success('Delete row complete.').dismissOthers();
                          $scope.listAccount.splice(index, 1);
                           //delete map key
                          map.delete(index);
                          //clear map and set map
                          $scope.listMapCurent = [];
                          map.forEach(function (item, key, mapObj) {  
                           $scope.listMapCurent.push(item);
                          });
                          map.clear();
                          if($scope.listMapCurent){
                            $.each($scope.listMapCurent, function(key, value ) {
                              map.set(key, value);
                            });  
                          }
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
          <form name="frmInsertAccount" action="#" class="form-horizontal" novalidate="novalidate">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="control-group">
                <label class="control-label">Username <i class="icon icon-asterisk" style="color: red;"></i>:</label>
              <div class="controls">
                <input type="text" class="span6" id="username" name="username" placeholder="Username"
                ng-model="account.acc_username"
                ng-required="true" />
                <span for="username" generated="true" id="mgs_username"
                class="help-inline hidden"
                >Username is required and can't be empty</span>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Password <i class="icon icon-asterisk" style="color: red;"></i>:</label>
              <div class="controls">
                  <input type="password" class="span6" id="password" name="password" placeholder="Password"
                ng-model="account.acc_password"
                ng-required="true" />
                <span for="password" generated="true" id="mgs_password"
                class="help-inline hidden"
                >Password is required and can't be empty</span>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Email <i class="icon icon-asterisk" style="color: red;"></i>:</label>
              <div class="controls">
                  <input type="email" class="span6" id="email" name="email" placeholder="Email"
                ng-model="account.acc_email"
                ng-required="true" />
                <span for="email" generated="true" id="mgs_email"
                class="help-inline hidden"
                >Email is required and can't be empty</span>
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