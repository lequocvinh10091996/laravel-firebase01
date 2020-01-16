@extends('layouts.main')

@section('title', 'Section')
@section('title-current', 'List section')
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
          <h5>List section</h5>
          <div style="float: right;margin: 8px; margin-right: 16px;">
            <button type="button" id="btnThemMoi" class="btn btn-primary btn" ng-click="insertSection()">Insert</button>
          </div>
        </div>
        <label style="margin: 5px 0px -15px 5px;"><b>Search:</b> <input ng-model="search.$"></label><br>
        <div class="widget-content nopadding" >
            <span id="listMessage"></span>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th>Topic</th>
                        <th>Section vietnamese</th>
                        <th>Section japanese</th>
                        <th>Section description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr  dir-paginate="section in listSection | itemsPerPage: pageSize | filter:search:strict" current-page="currentPage">
                        <td class="center" style="text-align: center; width: 5%;"><% pageSize *(currentPage - 1) + $index + 1 %></td>
                        <td style="width: 15%;"><% section.tp_vietnamese %></td>
                        <td style="width: 15%;"><% section.sec_vietnamese %></td>
                        <td style="width: 15%;"><% section.sec_japanese %></td>
                        <td><% section.sec_description  %></td>
                        <td class="center" style="text-align: center; width: 5%;white-space: nowrap;">
                            <button class="badge badge-info" ng-click="updateSection(pageSize *(currentPage - 1) + $index)" >Update</button>&nbsp;&nbsp;
                            <button class="badge badge-important" ng-click="deleteSection(pageSize *(currentPage - 1) + $index)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" >
        </dir-pagination-controls>
      <script >
        appName.controller('SectionController', function($scope, $http, MainUrl) {
          $scope.listSection = [];
          $scope.currentPage = 1;
          $scope.pageSize = 10;
          $scope.section = {};
          let map = new Map();
          
            $http.get(MainUrl+'/section').then(function(response){
              data = response.data.data.listSection;
              if(data){
                  var index = 0;
                  $.each(data, function( key, value ) {
                    $scope.listSection.push(value);
                    map.set(index, key);
                    index++;
                  });
              }
            });
            //insertSection
            $scope.insertSection = function(){
              $('.modal-title').html('Insert section');
              $("#tp_id").select2("val", "");
              $('#mgs_tp_id').addClass('hidden');
              $scope.section = {};
              $('.control-group').removeClass('error');
              $('#mgs_sec_vietnamese').addClass('hidden');
              $('#mgs_sec_japanese').addClass('hidden');
              $('.mgs_modal').addClass('hidden');
              $('#myModal').modal('show');
            }
            //end insertSection

            //updateSection
            $scope.updateSection = function(index){
                $('.modal-title').html('Update section');
                $('.mgs_modal').addClass('hidden');
                $scope.section = {};
                $scope.section = angular.copy($scope.listSection[index]);
                $scope.section.index = index;
                $("#tp_id").select2("val", $scope.listSection[index].tp_id);
                $('#mgs_tp_id').addClass('hidden');
                $('.control-group').removeClass('error');
                $('#mgs_sec_vietnamese').addClass('hidden');
                $('#mgs_sec_japanese').addClass('hidden');
                $('#myModal').modal('show');
            }
            //end updateSection

            //actionSave insert|edit
            $scope.actionSave = function(){
              $('.loader').removeClass('hidden');
              $('.control-group').removeClass('error');
              $('#mgs_tp_id').addClass('hidden');
              $('#mgs_sec_vietnamese').addClass('hidden');
              $('#mgs_sec_japanese').addClass('hidden');
              $('.mgs_modal').addClass('hidden');
              var flag_ok = true;
              var Url = MainUrl+'/section';
              if(map.get($scope.section.index)){
                Url += '/update';
                $scope.section.keySection = map.get($scope.section.index);
              }
              if(angular.isUndefined($scope.section.tp_id) && 
                 angular.isUndefined($scope.section.sec_vietnamese) &&
                  angular.isUndefined($scope.section.sec_japanese)) {
                    $('.control-group').addClass('error');
                    $('#mgs_tp_id').removeClass('hidden');
                    $('#mgs_sec_vietnamese').removeClass('hidden');
                    $('#mgs_sec_japanese').removeClass('hidden');
                    flag_ok= false;
              } else if(angular.isUndefined($scope.section.tp_id)){
                $('#tp_id').parents('.control-group').addClass('error');
                $('#mgs_tp_id').removeClass('hidden');
                flag_ok= false;
              } else if(angular.isUndefined($scope.section.sec_vietnamese)){
                    $('#sec_vietnamese').parents('.control-group').addClass('error');
                    $('#mgs_sec_vietnamese').removeClass('hidden');
                    flag_ok= false;
              } else if(angular.isUndefined($scope.section.sec_japanese)){
                    $('#sec_japanese').parents('.control-group').addClass('error');
                    $('#mgs_sec_japanese').removeClass('hidden');
                    flag_ok= false;
              }
              if(flag_ok == false){
                  $('.loader').addClass('hidden');
              }
              
              if(flag_ok == true){
                var reData = $.param($scope.section);
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
                    if(map.get($scope.section.index)){
                      $scope.listSection[$scope.section.index] = rowNew;
                      alertify.set('notifier', 'position', 'top-center');
                      alertify.success('Update row complete.');
                    } else{
                      alertify.set('notifier', 'position', 'top-center');
                      alertify.success('Insert row complete.');
                      $scope.listSection.push(rowNew);
                      map.set($scope.listSection.length-1, response.data.key);
                    }
                  }
                });
              }
            }
            //end actionSave
                   
            //deleteSection
            $scope.deleteSection = function(index){
                alertify.confirm('Confirm delete', 'Do you want to delete ?', function(){ 
                    var Url = MainUrl+'/section/delete';
                    $scope.section.keySection = map.get(index);
                    var reData = $.param($scope.section);
                    $http.post(Url, reData,
                    {headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'}}
                    ).then(function (response){
                       if(response.data.error == false){
                          alertify.set('notifier', 'position', 'top-center');
                          alertify.success('Delete row complete.').dismissOthers();
                          $scope.listSection.splice(index, 1);
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
                        } else if(response.data.error == true){
                            alertify.set('notifier', 'position', 'top-center');
                            alertify.error(response.data.data).dismissOthers();
                        }
                    });
                    }, function(){});
            }
            //end actionDelete
            //get list topic
            $scope.listTopic = [];
            $http.get(MainUrl+'/topic').then(function(response){
              data = response.data.data.listTopic;
                if(data){
                    $.each(data, function( key, value ) {
                      $scope.listTopic.push({tp_id: key,
                                                 value: value});
                    });
                }
            });
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
          <form name="frmInsertSection" action="#" class="form-horizontal" novalidate="novalidate">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="control-group">
              <label class="control-label">Topic <i class="icon icon-asterisk" style="color: red;"></i>:</label>
              <div class="controls">
                <select id="tp_id" name="tp_id" placeholder="Topic" ng-model="section.tp_id" ng-required="true" style="width: 51% !important;">
                  <option   value="" > --- Please choose --- </option>
                  <option  ng-repeat="topic in listTopic" value="<% topic.tp_id %>" >
                    <% topic.value.tp_vietnamese %>
                  </option>
                </select>
                <span for="tp_id" generated="true" id="mgs_tp_id"
                class="help-inline hidden"
                >Topic is required and can't be empty</span>
              </div>
            </div>
            <div class="control-group">
                <label class="control-label">Section vietnamese  <i class="icon icon-asterisk" style="color: red;"></i>:</label>
              <div class="controls">
                <input type="t" class="span6" id="sec_vietnamese" name="sec_vietnamese" placeholder="Section vietnamese "
                ng-model="section.sec_vietnamese"
                ng-required="true" />
                <span for="sec_vietnamese" generated="true" id="mgs_sec_vietnamese"
                class="help-inline hidden"
                >Section vietnamese  is required and can't be empty</span>
              </div>
            </div>
            <div class="control-group">
                <label class="control-label">Section japanese  <i class="icon icon-asterisk" style="color: red;"></i>:</label>
              <div class="controls">
                <input type="t" class="span6" id="sec_japanese" name="sec_japanese" placeholder="Section japanese "
                ng-model="section.sec_japanese"
                ng-required="true" />
                <span for="sec_japanese" generated="true" id="mgs_sec_japanese"
                class="help-inline hidden"
                >Section japanese  is required and can't be empty</span>
              </div>
            </div>
            <label class="control-label">Section description :</label>
            <div class="controls">
                <textarea rows="4" cols="50" class="span6" id="sec_description " name="sec_description " placeholder="Section description"
                          ng-model="section.sec_description" ng-required="false" >
                </textarea>
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