@extends('layouts.main')

@section('title', 'Translate mean')
@section('title-current', 'List translate mean')
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
          <h5>List translate mean</h5>
          <div style="float: right;margin: 8px; margin-right: 16px;">
            <button type="button" id="btnThemMoi" class="btn btn-primary btn" ng-click="insertTranslateMean()">Insert</button>
          </div>
        </div>
        <div class="widget-content nopadding" >
            <span id="listMessage"></span>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th>Section</th>
                        <th>English Translate</th>
                        <th>Japanese Translate</th>
                        <th>Japanese Higarana</th>
                        <th>Vietnamese Translate </th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr  dir-paginate="translateMean in listTranslateMean|itemsPerPage: pageSize" current-page="currentPage">
                        <td class="center" style="text-align: center; width: 5%;"><% pageSize *(currentPage - 1) + $index + 1 %></td>
                        <td style="width: 15%;"><% translateMean.sec_vietnamese %></td>
                        <td><% translateMean.tm_english_translate %></td>
                        <td><% translateMean.tm_japanese_translate %></td>
                        <td><% translateMean.tm_japanese_higarana %></td>
                        <td><% translateMean.tm_vietnamese_translate %></td>
                        <td class="center" style="text-align: center; width: 5%;white-space: nowrap;">
                            <button class="badge badge-info" ng-click="updateTranslateMean(pageSize *(currentPage - 1) + $index)" >Update</button>&nbsp;&nbsp;
                            <button class="badge badge-important" ng-click="deleteTranslateMean(pageSize *(currentPage - 1) + $index)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" >
        </dir-pagination-controls>
      <script >
        appName.controller('TranslateMeanController', function($scope, $http, MainUrl) {
          $scope.listTranslateMean = [];
          $scope.currentPage = 1;
          $scope.pageSize = 5;
          $scope.translateMean = {};
          let map = new Map();
          
            $http.get(MainUrl+'/translatemean').then(function(response){
              data = response.data.data.listTranslateMean;
              if(data){
                  var index = 0;
                  $.each(data, function( key, value ) {
                    $scope.listTranslateMean.push(value);
                    map.set(index, key);
                    index++;
                  });
              }
            });
            //insertTranslateMean
            $scope.insertTranslateMean = function(){
              $('.modal-title').html('Insert Translate mean');
              $('.mgs_modal').addClass('hidden');
              $("#sec_id").select2("val", "");
              $('.form-actions').removeClass('hidden');
              $scope.translateMean = {};
              $('.control-group').removeClass('error');
              $('#mgs_sec_id').addClass('hidden');
              $('#mgs_tm_english_translate').addClass('hidden');
              $('#mgs_tm_japanese_translate').addClass('hidden');
              $('#mgs_tm_japanese_higarana ').addClass('hidden');
              $('#mgs_tm_vietnamese_translate').addClass('hidden');
              $('#myModal').modal('show');
            }
            //end insertTranslateMean

            //updateTranslateMean
            $scope.updateTranslateMean = function(index){
                $('.modal-title').html('Update Translate mean');
                $('.mgs_modal').addClass('hidden');
                $scope.translateMean = {};
                $scope.translateMean = angular.copy($scope.listTranslateMean[index]);
                $scope.translateMean.index = index;
                $("#sec_id").select2("val", $scope.listTranslateMean[index].sec_id);
                $('.control-group').removeClass('error');
                $('.form-actions').removeClass('hidden');
                $('#mgs_sec_id').addClass('hidden');
                $('#mgs_tm_english_translate').addClass('hidden');
                $('#mgs_tm_japanese_translate').addClass('hidden');
                $('#mgs_tm_japanese_higarana ').addClass('hidden');
                $('#mgs_tm_vietnamese_translate').addClass('hidden');
                $('#myModal').modal('show');
            }
            //end updateTranslateMean

            //actionSave insert|edit
            $scope.actionSave = function(){
              $('.control-group').removeClass('error');
              $('#mgs_sec_id').addClass('hidden');
              $('#mgs_tm_japanese_translate').addClass('hidden');
              $('#mgs_tm_japanese_higarana').addClass('hidden');
              $('#mgs_tm_vietnamese_translate').addClass('hidden');
              $('.mgs_modal').addClass('hidden');
              $('.loader').removeClass('hidden');
              var flag_ok = true;
              var Url = MainUrl+'/translatemean';
              if(map.get($scope.translateMean.index)){
                Url += '/update';
                $scope.translateMean.keyTranslateMean = map.get($scope.translateMean.index);
              }
              if((angular.isUndefined($scope.translateMean.sec_id) &&
                  angular.isUndefined($scope.translateMean.tm_japanese_translate) &&
                  angular.isUndefined($scope.translateMean.tm_japanese_higarana) &&
                  angular.isUndefined($scope.translateMean.tm_vietnamese_translate))) {
                    $('.control-group').addClass('error');
                    $('#tm_english_translate').parents('.control-group').removeClass('error');
                    $('#mgs_sec_id').removeClass('hidden');
                    $('#mgs_tm_japanese_translate').removeClass('hidden');
                    $('#mgs_tm_japanese_higarana').removeClass('hidden');
                    $('#mgs_tm_vietnamese_translate').removeClass('hidden');
                    flag_ok= false;
              } else if(angular.isUndefined($scope.translateMean.sec_id)){
                $('#sec_id').parents('.control-group').addClass('error');
                $('#mgs_sec_id').removeClass('hidden');
                flag_ok= false;
              } else if(angular.isUndefined($scope.translateMean.tm_japanese_translate)){
                $('#tm_japanese_translate').parents('.control-group').addClass('error');
                $('#mgs_tm_japanese_translate').removeClass('hidden');
                flag_ok= false;
              } else if(angular.isUndefined($scope.translateMean.tm_japanese_higarana)){
                $('#tm_japanese_higarana').parents('.control-group').addClass('error');
                $('#mgs_tm_japanese_higarana').removeClass('hidden');
                flag_ok= false;
              } else if(angular.isUndefined($scope.translateMean.tm_vietnamese_translate)){
                $('#tm_vietnamese_translate').parents('.control-group').addClass('error');
                $('#mgs_tm_vietnamese_translate').removeClass('hidden');
                flag_ok= false;
              }
              
              if(flag_ok == false){
                  $('.loader').addClass('hidden');
              }
              
              if(flag_ok == true){
                var reData = $.param($scope.translateMean);
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
                    if(map.get($scope.translateMean.index)){
                      $scope.listTranslateMean[$scope.translateMean.index] = rowNew;
                      alertify.set('notifier', 'position', 'top-center');
                      alertify.success('Update row complete.');
                    } else{
                      alertify.set('notifier', 'position', 'top-center');
                      alertify.success('Insert row complete.');
                      $scope.listTranslateMean.push(rowNew);
                      map.set($scope.listTranslateMean.length-1, response.data.key);
                    }
                  }
                });
              }
            }
            //end actionSave
                   
            //deleteTranslateMean
            $scope.deleteTranslateMean = function(index){
                $('.form-actions').addClass('hidden');
                alertify.confirm('Confirm delete', 'Do you want to delete ['+$scope.listTranslateMean[index].sec_vietnamese+'] ?', function(){ 
                    var Url = MainUrl+'/translatemean/delete';
                    $scope.translateMean.keyTranslateMean = map.get(index);
                    var reData = $.param($scope.translateMean);
                    $http.post(Url, reData,
                    {headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'}}
                    ).then(function (response){
                       if(response.data.error == false){
                          alertify.set('notifier', 'position', 'top-center');
                          alertify.success('Delete ['+$scope.listTranslateMean[index].sec_vietnamese+'] complete.').dismissOthers();
                          $scope.listTranslateMean.splice(index, 1);
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
            
            //get list type config
            $scope.listSection = [];
            $http.get(MainUrl+'/section').then(function(response){
              data = response.data.data.listSection;
                if(data){
                    $.each(data, function( key, value ) {
                      $scope.listSection.push({sec_id: key,
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
          <form name="frmInsertTranslateMean" action="#" class="form-horizontal" novalidate="novalidate">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="control-group">
              <label class="control-label">Section <i class="icon icon-asterisk" style="color: red;"></i>:</label>
              <div class="controls">
                <select id="sec_id" name="sec_id" placeholder="Config type" ng-model="translateMean.sec_id" ng-required="true" style="width: 52% !important;">
                  <option   value="" > --- Please choose --- </option>
                  <option  ng-repeat="section in listSection" value="<% section.sec_id %>" >
                    <% section.value.sec_vietnamese %>
                  </option>
                </select>
                <span for="sec_id" generated="true" id="mgs_sec_id"
                class="help-inline hidden"
                >Config type is required and can't be empty</span>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">English translate :</label>
              <div class="controls">
                <input type="text" class="span6" id="tm_english_translate" name="tm_english_translate" placeholder="English translate"
                ng-model="translateMean.tm_english_translate" ng-required="true"/>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Japanese kanzi <i class="icon icon-asterisk" style="color: red;"></i>:</label>
              <div class="controls">
                <input type="text" class="span6" id="tm_japanese_translate" name="tm_japanese_translate" placeholder="Japanese kanzi"
                ng-model="translateMean.tm_japanese_translate"
                ng-required="true" />
                <span for="tm_japanese_translate" generated="true" id="mgs_tm_japanese_translate"
                class="help-inline hidden"
                >Japanese translate is required and can't be empty</span>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Japanese higarana <i class="icon icon-asterisk" style="color: red;"></i>:</label>
              <div class="controls">
                <input type="text" class="span6" id="tm_japanese_higarana" name="tm_japanese_higarana" placeholder="Japanese higarana"
                ng-model="translateMean.tm_japanese_higarana"
                ng-required="true" />
                <span for="tm_japanese_higarana" generated="true" id="mgs_tm_japanese_higarana"
                class="help-inline hidden"
                >Japanese higarana is required and can't be empty</span>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Vietnamese translate <i class="icon icon-asterisk" style="color: red;"></i>:</label>
              <div class="controls">
                <input type="text" class="span6" id="tm_vietnamese_translate" name="tm_vietnamese_translate" placeholder="Vietnamese translate"
                ng-model="translateMean.tm_vietnamese_translate"
                ng-required="true" />
                <span for="tm_vietnamese_translate" generated="true" id="mgs_tm_vietnamese_translate"
                class="help-inline hidden"
                >Vietnamese translate is required and can't be empty</span>
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