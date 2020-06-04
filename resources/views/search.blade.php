@extends('layouts.main')

@section('title', 'Search')
@section('title-current', 'Search')
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
                border: 7px solid #f3f3f3;
                border-radius: 69%;
                border-top: 7px solid #3498db;
                width: 50px;
                height: 50px;
                -webkit-animation: spin 2s linear infinite;
                animation: spin 2s linear infinite;
                position: fixed;
                background: rgba(255, 255, 255, 0.6);
		top: 40%;
		left: 50%;
                z-index: 9999;
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
            
            div.checker input {
                opacity: 1 !important;
                margin-top: -4px;
                margin-left: 2px;
            }
        </style>
        <div class="loader hidden"></div>
        <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
            <h5>Search</h5>
        </div>
        <div class="widget-content nopadding" >
            <span id="listMessage"></span>
            <div class="control-group">
                @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
                @endif
            </div>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <td colspan="5" style="text-align: center;">
                            <select id="searchAll" style="width: 7% !important;">
                                <option>Search by</option>
                                <option>JA-VI</option>
                                <option>VI-JA</option>
                            </select>
                            <input ng-model="search" class="form-control search" type="text" placeholder="Search" aria-label="Search" style="width: 25%; border-radius: 6px;">
                            <button type="submit" class="btn btn-info" style="margin-bottom: 10px; height: 28px;" ng-click="actionSearch(null)">Search</button>
                        </td>
                    </tr>
                </thead>
            </table>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th colspan="2" style="width: 15%;"></th>
                        <th style="text-align: center;" ng-repeat="topic in listTopic">
                            <b><p style="color: red;"><input type="checkbox" ng-click="checkSearch(topic.tp_key)" id="<% topic.tp_key %>"> <%topic.tp_vietnamese%></p></b>
                        </th>
                    </tr>
                </thead>
            </table><br>
            <ul class="thumbnails">
                <li class="span4">
                    <b><p style="color: red;">Kết quả</p></b>
                </li>
                <li class="span8">
                    <b><p style="color: red;">Chi tiết</p></b>
                </li>
            </ul>
            <ul class="thumbnails" dir-paginate="terminology in listTerminology | filter: filterOnLocation  | itemsPerPage: pageSize" current-page="currentPage">
                <li class="span4">
                    <div class="thumbnail" style="border: 1px solid #3db7f0; background-color: white;">
                        <p style="color: red;"><% terminology.tm_japanese_translate %></p>
                        <p><% terminology.tm_japanese_higarana %></p>
                        <p style="color: #3498db;"><% terminology.tm_vietnamese_translate %></p>
                    </div>
                </li>
                <li class="span8">
                    <div class="thumbnail" style="background-color: white;">
                        <p style="color: red;"><% terminology.tm_japanese_translate %></p>
                        <p><% terminology.tm_japanese_higarana %></p>
                        <p><% terminology.tm_vietnamese_translate %></p>
                        <p><% terminology.tm_english_translate %></p>
                        <p><% terminology.tm_english_translate %></p><br>
                        <p>Người tạo: <% terminology.tm_insert_user %></p>
                    </div>
                </li>
            </ul>
        </div><br>
        <dir-pagination-controls max-size="5" direction-links="true" boundary-links="true" >
        </dir-pagination-controls>
<script>
        appName.controller('SearchController', function($scope, $http, MainUrl) {
            $scope.listTopic = [];
            $scope.listTerminology = [];
            $scope.listTerminologyBackup = [];
            $scope.currentPage = 1;
            $scope.pageSize = 50;
            $scope.topicData = [];
            $scope.reData = {};
            
            
            $(".search").keydown(function(){
                if($("#searchAll" ).val() == "JA-VI"){
                    $scope.listTerminology = [];
                }
            });
            $( "#searchAll" ).click(function() {
                $(".search").val("");
                $scope.search = "";
            });
            
            let mapTopic = new Map();
            $http.get(MainUrl+'/search').then(function(response){
                dataTopic = response.data.data.listTopic;
                if(dataTopic){
                    var index = 0;
                    $.each(dataTopic, function( key, value ) {
                      $scope.listTopic.push(value);
                      mapTopic.set(index, key);
                      index++;
                    });
                }
                dataTerminology = response.data.data.listTerminology;
                if(dataTerminology){
                    $.each(dataTerminology, function( key, value ) {
                      $scope.listTerminologyBackup.push(value);
                    });
                }
            });
            
            $scope.checkSearch = function(topKey)
            {
                if($('#'+topKey).is(":checked")){
                    $scope.topicData.push(topKey);
                } else {
                    var indexCheckForDete = $scope.topicData.indexOf(topKey);
                    $scope.topicData.splice(indexCheckForDete, 1);
                }
            }
            
            $scope.actionSearch = function()
            {
                if(!angular.isUndefined($scope.search) && $scope.search != ""){
                    $('.loader').removeClass('hidden');
                    //xu ly checkbox
                    var Url = MainUrl+'/search';
                    $scope.reData.topicData = $scope.topicData;
                    var reData = $.param($scope.reData);
                    $http.post(Url, reData,
                    {headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'}}
                    ).then(function (response){
                      $('.loader').addClass('hidden');
                      if(response.data.error == true) {
                            $('.loader').addClass('hidden');
                            $scope.listTerminology = $scope.listTerminologyBackup;
                            if ($("#searchAll" ).val() == "Search by") {
                                $scope.filterOnLocation = $scope.search;
                            } else if ($("#searchAll" ).val() == "JA-VI") {
                                $scope.filterOnLocation =  function(terminology) {
                                      return terminology.tm_japanese_translate.toString().indexOf($scope.search) > -1 || terminology.tm_japanese_higarana.toString().indexOf($scope.search) > -1;
                                };
                            } else if ($("#searchAll" ).val() == "VI-JA") {
                                $scope.filterOnLocation = {tm_vietnamese_translate: $scope.search};
                            }
                      } else if(response.data.error == false) {
                            $('.loader').addClass('hidden');
                            rowNew = $.parseJSON(response.data.data);
                            $scope.listTerminology = [];
                            if(rowNew){
                                $.each(rowNew, function( key, value ) {
                                  $scope.listTerminology.push(value);
                                });
                            }
                            //xu ly search
                            if ($("#searchAll" ).val() == "Search by") {
                                $scope.filterOnLocation = $scope.search;
                            } else if ($("#searchAll" ).val() == "JA-VI") {
                                $scope.filterOnLocation =  function(terminology) {
                                      return terminology.tm_japanese_translate.toString().indexOf($scope.search) > -1 || terminology.tm_japanese_higarana.toString().indexOf($scope.search) > -1;
                                };
                            } else if ($("#searchAll" ).val() == "VI-JA") {
                                $scope.filterOnLocation = {tm_vietnamese_translate: $scope.search};
                            }
                      }
                    });
                } else {
                    $scope.listTerminology = [];
                }
            }
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