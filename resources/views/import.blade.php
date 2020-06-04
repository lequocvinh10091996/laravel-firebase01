@extends('layouts.main')

@section('title', 'Setting')
@section('title-current', 'Import CSV')
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
            div.uploader {
                width: 60%;
            }
            /* custom css upload file */
            div.uploader span.action {
                width: 10%;
            }
            div.uploader span.filename {
                width: 53%;
            }
            div.uploader input {
                width: 10%;
                margin-right: 34%;
            }
        </style>
        <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
            <h5>Import CSV</h5>
        </div>
        <div class="widget-content nopadding" >
            @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <span id="listMessage"></span>
            <div class="control-group">
                @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
                @endif
            </div>
            <!--<form action="{{ route('import') }}" enctype="multipart/form-data" method="POST">-->
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
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
                            <td>Account</td>
                            <td class="center" style="text-align: center; width: 80%;white-space: nowrap;">
                                <input type="file" name="mst_account" ng-file="importInput.mst_account">
                            </td>
                        </tr>
                        <tr>
                            <td class="center" style="text-align: center; width: 2%;">2</td>
                            <td>Terminology</td>
                            <td class="center" style="text-align: center; width: 5%;white-space: nowrap;">
                                <input type="file" name="mst_translate_mean" ng-file="importInput.mst_translate_mean">
                            </td>
                        </tr>
                        <tr>
                            <td class="center" style="text-align: center; width: 2%;">3</td>
                            <td>Section</td>
                            <td class="center" style="text-align: center; width: 5%;white-space: nowrap;">
                                <input type="file" name="mst_section" ng-file="importInput.mst_section">
                            </td>
                        </tr>
                        <tr>
                            <td class="center" style="text-align: center; width: 2%;">4</td>
                            <td>Topic</td>
                            <td class="center" style="text-align: center; width: 5%;white-space: nowrap;">
                                <input type="file" name="mst_topic" ng-file="importInput.mst_topic">
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div><br>
                <button type="submit" class="btn btn-info" style="margin-left: 50%; width: 10%; margin-bottom: 10px;" ng-click="actionImport(null)">Import</button>
            <!--</form>-->
<script >
        appName.controller('ImportController', function($scope, $http, MainUrl) {
            $scope.importInput = {};
            
            $scope.actionImport = function()
            {
//                if(!angular.isUndefined($scope.search) && $scope.search != ""){
//                    $('.loader').removeClass('hidden');
                    //xu ly checkbox
                    var fd = new FormData();
                    angular.forEach($scope.importInput.mst_account,function(file){
                        fd.append('file', file);
                    });
                    
                    var Url = MainUrl+'/import/import';
//                    var reData = $.param($scope.importInput);
                    $http.post(Url, fd,
                    {headers:{'Content-Type': undefined}}
                    ).then(function (response){
//                      $('.loader').addClass('hidden');
                      if(response.data.error == true) {
                          console.log('erorr');
//                            $('.loader').addClass('hidden');
//                            $scope.listTerminology = $scope.listTerminologyBackup;
//                            if ($("#searchAll" ).val() == "Search by") {
//                                $scope.filterOnLocation = $scope.search;
//                            } else if ($("#searchAll" ).val() == "JA-VI") {
//                                $scope.filterOnLocation =  function(terminology) {
//                                      return terminology.tm_japanese_translate.toString().indexOf($scope.search) > -1 || terminology.tm_japanese_higarana.toString().indexOf($scope.search) > -1;
//                                };
//                            } else if ($("#searchAll" ).val() == "VI-JA") {
//                                $scope.filterOnLocation = {tm_vietnamese_translate: $scope.search};
//                            }
                      } else if(response.data.error == false) {
                          console.log('success');
//                            $('.loader').addClass('hidden');
//                            rowNew = $.parseJSON(response.data.data);
//                            $scope.listTerminology = [];
//                            if(rowNew){
//                                $.each(rowNew, function( key, value ) {
//                                  $scope.listTerminology.push(value);
//                                });
//                            }
//                            //xu ly search
//                            if ($("#searchAll" ).val() == "Search by") {
//                                $scope.filterOnLocation = $scope.search;
//                            } else if ($("#searchAll" ).val() == "JA-VI") {
//                                $scope.filterOnLocation =  function(terminology) {
//                                      return terminology.tm_japanese_translate.toString().indexOf($scope.search) > -1 || terminology.tm_japanese_higarana.toString().indexOf($scope.search) > -1;
//                                };
//                            } else if ($("#searchAll" ).val() == "VI-JA") {
//                                $scope.filterOnLocation = {tm_vietnamese_translate: $scope.search};
//                            }
                      }
                    });
//                } else {
//                    $scope.listTerminology = [];
//                }
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