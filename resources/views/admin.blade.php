@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/admin.css'>
<link rel='stylesheet' href='/css/datatables.min.css'>
<link rel='stylesheet' href='/css/index.css' />
{{-- <link rel='stylesheet' href='/css/shorten_result.css' /> --}}
@endsection

@section('content')
<div ng-controller="AdminCtrl" class="ng-root">
    <div class='col-md-2'>
        <ul class='nav nav-pills nav-stacked admin-nav' role='tablist'>
            @if(isset($short_url))
            <li role='presentation' aria-controls="home" class='admin-nav-item active'><a href='#result'>Result</a></li>
            @endif
            <li role='presentation' aria-controls="home" class='admin-nav-item {{ isset($short_url) ? '' : 'active' }}'><a href='#create'>Create</a></li>
            <li role='presentation' aria-controls="links" class='admin-nav-item'><a href='#links'>Links</a></li>
            <li role='presentation' aria-controls="settings" class='admin-nav-item'><a href='#settings'>Settings</a></li>

            @if ($role == $admin_role)
            <li role='presentation' class='admin-nav-item'><a href='#admin'>Admin</a></li>
            @endif

            @if ($api_active == 1)
            <li role='presentation' class='admin-nav-item'><a href='#developer'>Developer</a></li>
            @endif
        </ul>
    </div>
    <div class='col-md-10'>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane {{ isset($short_url) ? '' : 'active' }}" id="create">

                <form method='POST' action='/admin' role='form'>
                    <input type='url' autocomplete='off'
                           class='form-control long-link-input' placeholder='http://' name='link-url' />

                    <div class='row' id='options' ng-cloak>
                        <p>Customize link</p>

                        @if (!env('SETTING_PSEUDORANDOM_ENDING'))
                            {{-- Show secret toggle only if using counter-based ending --}}
                            <div class='btn-group btn-toggle visibility-toggler' data-toggle='buttons'>
                                <label class='btn btn-primary btn-sm active'>
                                    <input type='radio' name='options' value='p' checked /> Public
                                </label>
                                <label class='btn btn-sm btn-default'>
                                    <input type='radio' name='options' value='s' /> Secret
                                </label>
                            </div>
                        @endif

                        <div>
                            <div class='custom-link-text'>
                                <h2 class='site-url-field'>{{env('APP_ADDRESS')}}/</h2>
                                <input type='text' autocomplete="off" class='form-control custom-url-field' name='custom-ending' />
                            </div>
                            <div>
                                <a href='#' class='btn btn-success btn-xs check-btn' id='check-link-availability'>Check Availability</a>
                                <div id='link-availability-status'></div>
                            </div>
                        </div>
                    </div>
                    <input type='submit' class='btn btn-info' id='shorten' value='Shorten' />
                    <a href='#' class='btn btn-warning' id='show-link-options'>Link Options</a>
                    <input type="hidden" name='_token' value='{{csrf_token()}}' />
                </form>

                <div id='tips' class='text-muted tips'>
                    <i class='fa fa-spinner'></i> Loading Tips...
                </div>

            </div>

            @if(isset($short_url))
            <div role="tabpanel" class="tab-pane active" id="result">
                <div class="input-group">
                    <input type='text' class='result-box form-control' value='{{$short_url}}' id='short_url' />
                    <div class='input-group-addon' id='clipboard-copy' data-clipboard-target='#short_url' data-toggle='tooltip' data-placement='bottom' data-title='Copied!'>
                        <i class='fa fa-clipboard' aria-hidden='true' title='Copy to clipboard'></i>
                    </div>
                </div>
                {{-- <a id="generate-qr-code" class='btn btn-primary'>Generate QR Code</a> --}}

            </div>
            @endif

            <div role="tabpanel" class="tab-pane" id="links">
                @include('snippets.link_table', [
                    'table_id' => 'user_links_table'
                ])
            </div>

            <div role="tabpanel" class="tab-pane" id="settings">
                <h3>Change Password</h3>
                <form action='/admin/action/change_password' method='POST'>
                    Old Password: <input class="form-control password-box" type='password' name='current_password' />
                    New Password: <input class="form-control password-box" type='password' name='new_password' />
                    <input type="hidden" name='_token' value='{{csrf_token()}}' />
                    <input type='submit' class='btn btn-success change-password-btn'/>
                </form>
            </div>

            @if ($role == $admin_role)
            <div role="tabpanel" class="tab-pane" id="admin">
                <h3>Links</h3>
                @include('snippets.link_table', [
                    'table_id' => 'admin_links_table'
                ])

                <h3 class="users-heading">Users</h3>
                <a ng-click="state.showNewUserWell = !state.showNewUserWell" class="btn btn-primary btn-sm status-display">New</a>

                <div ng-if="state.showNewUserWell" class="new-user-fields well">
                    <table class="table">
                        <tr>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th></th>
                        </tr>
                        <tr id="new-user-form">
                            <td><input type="text" class="form-control" ng-model="newUserParams.username"></td>
                            <td><input type="password" class="form-control" ng-model="newUserParams.userPassword"></td>
                            <td><input type="email" class="form-control" ng-model="newUserParams.userEmail"></td>
                            <td>
                                <select class="form-control new-user-role" ng-model="newUserParams.userRole">
                                    @foreach  ($user_roles as $role_text => $role_val)
                                        <option value="{{$role_val}}">{{$role_text}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <a ng-click="addNewUser($event)" class="btn btn-primary btn-sm status-display new-user-add">Add</a>
                            </td>
                        </tr>
                    </table>
                </div>

                @include('snippets.user_table', [
                    'table_id' => 'admin_users_table'
                ])

            </div>
            @endif

            @if ($api_active == 1)
            <div role="tabpanel" class="tab-pane" id="developer">
                <h3>Developer</h3>

                <p>API keys and documentation for developers.</p>
                <p>
                    Documentation:
                    <a href='http://docs.polr.me/en/latest/developer-guide/api/'>http://docs.polr.me/en/latest/developer-guide/api/</a>
                </p>

                <h4>API Key: </h4>
                <div class='row'>
                    <div class='col-md-8'>
                        <input class='form-control status-display' disabled type='text' value='{{$api_key}}'>
                    </div>
                    <div class='col-md-4'>
                        <a href='#' ng-click="generateNewAPIKey($event, '{{$user_id}}', true)" id='api-reset-key' class='btn btn-danger'>Reset</a>
                    </div>
                </div>


                <h4>API Quota: </h4>
                <h2 class='api-quota'>
                    @if ($api_quota == -1)
                        unlimited
                    @else
                        <code>{{$api_quota}}</code>
                    @endif
                </h2>
                <span> requests per minute</span>
            </div>
            @endif
        </div>
    </div>

    <div class="angular-modals">
        <edit-long-link-modal ng-repeat="modal in modals.editLongLink" link-ending="modal.linkEnding"
            old-long-link="modal.oldLongLink" clean-modals="cleanModals"></edit-long-link-modal>
        <edit-user-api-info-modal ng-repeat="modal in modals.editUserApiInfo" user-id="modal.userId"
            api-quota="modal.apiQuota" api-active="modal.apiActive" api-key="modal.apiKey"
            generate-new-api-key="generateNewAPIKey" clean-modals="cleanModals"></edit-user-api-info>
    </div>
</div>


@endsection

@section('js')
{{-- Include modal templates --}}
@include('snippets.modals')

{{-- Include extra JS --}}
<script src='/js/datatables.min.js'></script>
<script src='/js/api.js'></script>
<script src='/js/AdminCtrl.js'></script>
<script src='/js/index.js'></script>
<script src='/js/qrcode.min.js'></script>
<script src='/js/clipboard.min.js'></script>
<script src='/js/shorten_result.js'></script>
@endsection
