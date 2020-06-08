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
        <div class="loader hidden"></div>
        <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
            <h5>Import CSV</h5>
        </div>
        <div class="widget-content nopadding" >
            <span id="listMessage"></span>
            <div class="control-group">
                <div class="alert alert-danger alert-block importError hidden">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>Import failed!</strong>
                </div>
            </div>
            <div class="control-group">
                <div class="alert alert-success alert-block importSuccess hidden">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong><p id="messageImportSuccess"></p></strong>
                    <strong><p id="messageImportTerminology"></p></strong>
                    <strong><p id="messageImportSection"></p></strong>
                    <strong><p id="messageImportTopic"></p></strong>
                </div>
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
<!--                        <tr>
                            <td class="center" style="text-align: center; width: 2%;">1</td>
                            <td>Account</td>
                            <td class="center" style="text-align: center; width: 80%;white-space: nowrap;">
                                <input type="file" name="mst_account" ng-file="importInput.mst_account">
                            </td>
                        </tr>-->
                        <tr>
                            <td class="center" style="text-align: center; width: 2%;">1</td>
                            <td>Terminology</td>
                            <td class="center" style="text-align: center; width: 80%;white-space: nowrap;">
                                <input type="file" name="mst_translate_mean" ng-file="importInput.mst_translate_mean" id="mst_translate_mean">
                            </td>
                        </tr>
                        <tr>
                            <td class="center" style="text-align: center; width: 2%;">2</td>
                            <td>Section</td>
                            <td class="center" style="text-align: center; width: 5%;white-space: nowrap;">
                                <input type="file" name="mst_section" ng-file="importInput.mst_section" id="mst_section">
                            </td>
                        </tr>
                        <tr>
                            <td class="center" style="text-align: center; width: 2%;">3</td>
                            <td>Topic</td>
                            <td class="center" style="text-align: center; width: 5%;white-space: nowrap;">
                                <input type="file" name="mst_topic" ng-file="importInput.mst_topic" id="mst_topic">
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
                    $('.loader').removeClass('hidden');
                    $('.importSuccess').addClass('hidden');
                    $('#messageImportSuccess').text('');
                    $('#messageImportTerminology').text('');
                    $('#messageImportSection').text('');
                    $('#messageImportTopic').text('');
                    
                    $('.importError').addClass('hidden');
                    
                    //xu ly checkbox
                    var fd = new FormData();
//                    angular.forEach($scope.importInput.mst_account,function(file){
//                        fd.append('mst_account', file);
//                    });
                    angular.forEach($scope.importInput.mst_translate_mean,function(file){
                        fd.append('mst_translate_mean', file);
                    });
                    angular.forEach($scope.importInput.mst_section,function(file){
                        fd.append('mst_section', file);
                    });
                    angular.forEach($scope.importInput.mst_topic,function(file){
                        fd.append('mst_topic', file);
                    });
                    
                    var Url = MainUrl+'/import/import';
                    $http.post(Url, fd,
                    {headers:{'Content-Type': undefined}}
                    ).then(function (response){
                        $('.filename').text('No file selected');
                        $('#mst_translate_mean').val('');
                        $('#mst_section').val('');
                        $('#mst_topic').val('');
                        $('.loader').addClass('hidden');
                      if (response.data.error == true) {
                            $('.importError').removeClass('hidden');
                      } else if(response.data.error == false) {
                            $('.importSuccess').removeClass('hidden');
                            listImport = $.parseJSON(response.data.listImport);
                            if(listImport){
                                $('#messageImportSuccess').text('Import success:');
                                if(listImport.mst_translate_mean != undefined){
                                    $('#messageImportTerminology').text('  - '+listImport.mst_translate_mean+' (row succes: '+listImport.count_success_mst_translate_mean+', row failed: '+listImport.count_failed_mst_translate_mean+')');
                                }
                                if(listImport.mst_section != undefined){
                                    $('#messageImportSection').text('  - '+listImport.mst_section+' (row succes: '+listImport.count_success_mst_section+', row failed: '+listImport.count_failed_mst_section+')');
                                }
                                if(listImport.mst_topic != undefined){
                                    $('#messageImportTopic').text('  - '+listImport.mst_topic+' (row succes: '+listImport.count_success_mst_topic+', row failed: '+listImport.count_failed_mst_topic+')');
                                }
                            }
                      }
                    });
            }
        });
</script>

@endsection