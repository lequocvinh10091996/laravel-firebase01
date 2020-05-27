@extends('layouts.main')

@section('title', 'Setting')
@section('title-current', 'Export CSV')
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
            <h5>Export CSV</h5>
        </div>
        <div class="widget-content nopadding" >
            <span id="listMessage"></span>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th>Name data</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="center" style="text-align: center; width: 2%;">1</td>
                        <td style="width: 40%;">Account</td>
                        <td class="center" style="text-align: center; width: 5%;white-space: nowrap;">
                            <a href="{{ route('terminologyExport') }}" class="btn btn-info"><b style="font-size: 14px;">Export</b></a>
                        </td>
                    </tr>
                    <tr>
                        <td class="center" style="text-align: center; width: 2%;">2</td>
                        <td style="width: 40%;">Terminology</td>
                        <td class="center" style="text-align: center; width: 5%;white-space: nowrap;">
                            <a href="{{ route('terminologyExport') }}" class="btn btn-info"><b style="font-size: 14px;">Export</b></a>
                        </td>
                    </tr>
                    <tr>
                        <td class="center" style="text-align: center; width: 2%;">1</td>
                        <td style="width: 40%;">Section</td>
                        <td class="center" style="text-align: center; width: 5%;white-space: nowrap;">
                            <a href="{{ route('terminologyExport') }}" class="btn btn-info"><b style="font-size: 14px;">Export</b></a>
                        </td>
                    </tr>
                    <tr>
                        <td class="center" style="text-align: center; width: 2%;">1</td>
                        <td style="width: 40%;">Topic</td>
                        <td class="center" style="text-align: center; width: 5%;white-space: nowrap;">
                            <a href="{{ route('terminologyExport') }}" class="btn btn-info"><b style="font-size: 14px;">Export</b></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
      <script >
        appName.controller('TopicController', function($scope, $http, MainUrl) {
          $scope.listTopic = [];
          $scope.currentPage = 1;
          $scope.pageSize = 50;
          $scope.topic = {};
          let map = new Map();
          
            $http.get(MainUrl+'/topic').then(function(response){
              data = response.data.data.listTopic;
              if(data){
                  var index = 0;
                  $.each(data, function( key, value ) {
                    $scope.listTopic.push(value);
                    map.set(index, key);
                    index++;
                  });
              }
            });
            //insertTopic
            $scope.insertTopic = function(){
              $('.modal-title').html('Insert topic');
              $scope.topic = {};
              $('.control-group').removeClass('error');
              $('#mgs_tp_vietnamese').addClass('hidden');
              $('#mgs_tp_japanese').addClass('hidden');
              $('.mgs_modal').addClass('hidden');
              $('#myModal').modal('show');
            }
            //end insertTopic

            //updateTopic
            $scope.updateTopic = function(index){
                $('.modal-title').html('Update topic');
                $('.mgs_modal').addClass('hidden');
                $scope.topic = {};
                $scope.topic = angular.copy($scope.listTopic[index]);
                $scope.topic.index = index;
                $('.control-group').removeClass('error');
                $('#mgs_tp_vietnamese').addClass('hidden');
                $('#mgs_tp_japanese').addClass('hidden');
                $('#myModal').modal('show');
            }
            //end updateTopic

            //actionSave insert|edit
            $scope.actionSave = function(){
              $('.loader').removeClass('hidden');
              $('.control-group').removeClass('error');
              $('#mgs_tp_vietnamese').addClass('hidden');
              $('#mgs_tp_japanese').addClass('hidden');
              $('.mgs_modal').addClass('hidden');
              var flag_ok = true;
              var Url = MainUrl+'/topic';
              if(map.get($scope.topic.index)){
                Url += '/update';
                $scope.topic.keyTopic = map.get($scope.topic.index);
              }
              if(angular.isUndefined($scope.topic.tp_vietnamese) &&
                  angular.isUndefined($scope.topic.tp_japanese)) {
                    $('.control-group').addClass('error');
                    $('#mgs_tp_vietnamese').removeClass('hidden');
                    $('#mgs_tp_japanese').removeClass('hidden');
                    flag_ok= false;
              }else if(angular.isUndefined($scope.topic.tp_vietnamese)){
                    $('#tp_vietnamese').parents('.control-group').addClass('error');
                    $('#mgs_tp_vietnamese').removeClass('hidden');
                    flag_ok= false;
              } else if(angular.isUndefined($scope.topic.tp_japanese)){
                    $('#tp_japanese').parents('.control-group').addClass('error');
                    $('#mgs_tp_japanese').removeClass('hidden');
                    flag_ok= false;
              }
              if(flag_ok == false){
                  $('.loader').addClass('hidden');
              }
              
              if(flag_ok == true){
                var reData = $.param($scope.topic);
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
                    if(map.get($scope.topic.index)){
                      $scope.listTopic[$scope.topic.index] = rowNew;
                      alertify.set('notifier', 'position', 'top-center');
                      alertify.success('Update row complete.');
                    } else{
                      alertify.set('notifier', 'position', 'top-center');
                      alertify.success('Insert row complete.');
                      $scope.listTopic.push(rowNew);
                      map.set($scope.listTopic.length-1, response.data.key);
                    }
                  }
                });
              }
            }
            //end actionSave
                   
            //deleteTopic
            $scope.deleteTopic = function(index){
                alertify.confirm('Confirm delete', 'Do you want to delete ?', function(){ 
                    var Url = MainUrl+'/topic/delete';
                    $scope.topic.keyTopic = map.get(index);
                    var reData = $.param($scope.topic);
                    $http.post(Url, reData,
                    {headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'}}
                    ).then(function (response){
                       if(response.data.error == false){
                          alertify.set('notifier', 'position', 'top-center');
                          alertify.success('Delete row complete.').dismissOthers();
                          $scope.listTopic.splice(index, 1);
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
            //clear cache: php artisan config:cache 
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
          <form name="frmInsertTopic" action="#" class="form-horizontal" novalidate="novalidate">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="control-group">
                <label class="control-label">Topic vietnamese  <i class="icon icon-asterisk" style="color: red;"></i>:</label>
              <div class="controls">
                <input type="t" class="span6" id="tp_vietnamese" name="tp_vietnamese" placeholder="Topic vietnamese "
                ng-model="topic.tp_vietnamese"
                ng-required="true" />
                <span for="tp_vietnamese" generated="true" id="mgs_tp_vietnamese"
                class="help-inline hidden"
                >Topic vietnamese  is required and can't be empty</span>
              </div>
            </div>
            <div class="control-group">
                <label class="control-label">Topic japanese  <i class="icon icon-asterisk" style="color: red;"></i>:</label>
              <div class="controls">
                <input type="t" class="span6" id="tp_japanese" name="tp_japanese" placeholder="Topic japanese "
                ng-model="topic.tp_japanese"
                ng-required="true" />
                <span for="tp_japanese" generated="true" id="mgs_tp_japanese"
                class="help-inline hidden"
                >Topic japanese  is required and can't be empty</span>
              </div>
            </div>
            <label class="control-label">Topic description :</label>
            <div class="controls">
                <textarea rows="4" cols="50" class="span6" id="tp_description " name="tp_description " placeholder="Topic description"
                          ng-model="topic.tp_description" ng-required="false" >
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