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
            <div class="control-group">
                @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
                @endif
            </div>
            <form action="{{ route('export') }}">
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
                            <td style="width: 40%;">Terminology</td>
                            <td class="center" style="text-align: center; width: 5%;white-space: nowrap;">
                                <input type="checkbox" name="mst_translate_mean" ng-model="exportCheck.mst_translate_mean">
                            </td>
                        </tr>
                        <tr>
                            <td class="center" style="text-align: center; width: 2%;">2</td>
                            <td style="width: 40%;">Section</td>
                            <td class="center" style="text-align: center; width: 5%;white-space: nowrap;">
                                <input type="checkbox" name="mst_section" ng-model="exportCheck.mst_section">
                            </td>
                        </tr>
                        <tr>
                            <td class="center" style="text-align: center; width: 2%;">3</td>
                            <td style="width: 40%;">Topic</td>
                            <td class="center" style="text-align: center; width: 5%;white-space: nowrap;">
                                <input type="checkbox" name="mst_topic" ng-model="exportCheck.mst_topic">
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div><br>
                <button type="submit" class="btn btn-info" style="margin-left: 50%; width: 10%; margin-bottom: 10px;" ng-click="actionExport(null)">Export</button>
            </form>
<script >
        appName.controller('ExportController', function($scope, $http, MainUrl) {
            $scope.exportCheck = {};
        });
</script>

@endsection