@extends('backEnd.master')
<style>
    span#twilio_account_sid-error,span#twilio_authentication_token-error,span#twilio_registered_no-error{
    color: red;
}
</style>
@section('title')
@lang('system_settings.sms_settings')
@endsection
@section('mainContent')
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('system_settings.sms_settings')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('system_settings.system_settings')</a>
                <a href="#">@lang('system_settings.sms_settings')</a>
            </div>
        </div>
    </div>
</section>
<section class="mb-40 student-details">
    <div class="container-fluid p-0">
        <div class="row">
            <!-- Start Sms Details -->
            <div class="col-lg-12">
                <ul class="nav nav-tabs tab_column" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link @if (Session::get('twilio_settings') != 'active' && Session::get('msg91_settings') != 'active' && Session::get('textlocal_settings') != 'active' && Session::get('HimalayaSms') != 'active' && Session::get('africatalking_settings') != 'active') active @endif" href="#select_sms_service" role="tab" data-toggle="tab">@lang('system_settings.select_a_SMS_service')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if (Session::get('twilio_settings') == 'active') active @endif " href="#twilio_settings" role="tab" data-toggle="tab">@lang('system_settings.twilio') </a>
                    </li> 
                    <li class="nav-item">
                        <a class="nav-link @if (Session::get('msg91_settings') == 'active') active @endif " href="#msg91_settings" role="tab" data-toggle="tab">@lang('system_settings.msg91') </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if (Session::get('textlocal_settings') == 'active') active @endif " href="#textlocal_settings" role="tab" data-toggle="tab">@lang('system_settings.textlocal') </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if (Session::get('africatalking_settings') == 'active') active @endif " href="#africatalking_settings" role="tab" data-toggle="tab">@lang('system_settings.africatalking') </a>
                    </li>  

                    @if(moduleStatusCheck('HimalayaSms'))
                    <li class="nav-item">
                        <a class="nav-link @if (Session::get('HimalayaSms') == 'active') active @endif " href="#HimalayaSms" role="tab" data-toggle="tab">HimalayaSms </a>
                    </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link @if (Session::get('Mobile_SMS') == 'active') active @endif " href="#Mobile_SMS" role="tab" data-toggle="tab">@lang('system_settings.mobile_sms')</a>
                    </li> 
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade @if (Session::get('twilio_settings') != 'active' && Session::get('msg91_settings') != 'active' && Session::get('HimalayaSms') != 'active'  && Session::get('Mobile_SMS') != 'active' && Session::get('textlocal_settings') != 'active' && Session::get('africatalking_settings') != 'active') show active @endif" id="select_sms_service">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'update-clickatell-data', 'id' => 'select_a_service']) }}
                        <div class="white-box mt-2">
                            <div class="row">
                                <div class="col-lg-4 select_sms_services">
                                    <div class="input-effect">
                                        <select class="niceSelect w-100 bb form-control{{ $errors->has('book_category_id') ? ' is-invalid' : '' }}" name="sms_service" id="sms_service">
                                            <option data-display="@lang('system_settings.select_a_SMS_service')" value="">@lang('system_settings.select_a_SMS_service')</option>
                                            @if(isset($all_sms_services))
                                            @foreach($all_sms_services as $value)
                                            <option value="{{@$value->id}}"  @if(isset($active_sms_service)) @if(@$active_sms_service->id == @$value->id) selected @endif @endif >{{@$value->gateway_name}}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                        <span class="focus-border"></span>
                                        @if ($errors->has('book_category_id'))
                                        <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ $errors->first('book_category_id') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div> 
                                <div class="col-lg-8">
                                </div>
                            </div>   
                        </div>
                        {{ Form::close()}}
                    </div>

            <!-- Start Exam Tab -->
            <div role="tabpanel" class="tab-pane fade @if (Session::get('twilio_settings') == 'active') show active @endif " id="twilio_settings">
            @if(userPermission(446))
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'update-twilio-data', 'id' => 'twilio_form']) }}
            @endif
                <div class="white-box">
                        <div class="">
                            <input type="hidden" name="twilio_form" id="twilio_form_url" value="update-twilio-data">
                            <input type="hidden" name="gateway_id" id="gateway_id" value="1"> 
                            <input type="hidden" name="gateway_name" value="Twilio">
                            <div class="row mb-30">

                                <div class="col-md-5">
                                    <div class="row">
                                        <div class="col-lg-12 mb-30">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('twilio_account_sid') ? ' is-invalid' : '' }}"
                                                type="text" name="twilio_account_sid" autocomplete="off" value="{{isset($sms_services)? @$sms_services['Twilio']->twilio_account_sid : ''}}" id="twilio_account_sid">
                                                <label>@lang('system_settings.twilio_account_sid') <span>*</span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('twilio_account_sid'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('twilio_account_sid') }}</strong>
                                                </span>
                                                @endif
                                                </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 mb-30">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('twilio_authentication_token') ? ' is-invalid' : '' }}"
                                                type="text" name="twilio_authentication_token" autocomplete="off" value="{{isset($sms_services)? @$sms_services['Twilio']->twilio_authentication_token : ''}}" id="twilio_authentication_token">
                                                <label>@lang('system_settings.authentication_token') <span>*</span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('twilio_authentication_token'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('twilio_authentication_token') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('twilio_registered_no') ? ' is-invalid' : '' }}"
                                                type="text" name="twilio_registered_no" autocomplete="off" value="{{isset($sms_services)? @$sms_services['Twilio']->twilio_registered_no : ''}}" id="twilio_registered_no">
                                                <label>@lang('system_settings.registered_phone_number') <span>*</span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('twilio_registered_no'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('twilio_registered_no')}}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <div class="row justify-content-center">
                                         <img class="logo" width="250" height="90" src="{{ URL::asset('public/backEnd/img/twilio.png') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php 
                            $tooltip = "";
                            if(userPermission(446)){
                                    $tooltip = "";
                                }else{
                                    $tooltip = "You have no permission to add";
                                }
                        @endphp
                        <div class="row mt-40">
                            <div class="col-lg-12 text-center">
                                <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="{{@$tooltip}}">
                                    <span class="ti-check"></span>
                                    @lang('common.update')
                                </button>
                            </div>
                        </div>
                    </div>
            {{ Form::close() }}
            </div>
            

            <div role="tabpanel" class="tab-pane fade @if (Session::get('msg91_settings') == 'active') show active @endif " id="msg91_settings"> 
                @if(userPermission(447))
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'update-msg91-data', 'method'=>'POST']) }}
                @endif
                <div class="white-box">  
                    <input type="hidden" name="msg91_form" id="msg91_form_url" value="update-msg91-data">
                    <input type="hidden" name="gateway_id" id="gateway_id" value="2"> 
                    <input type="hidden" name="gateway_name" value="Msg91">
                            <div class="row mb-30">
                               <div class="col-md-5">
                                    <div class="row">
                                        <div class="col-lg-12 mb-30">
                                            
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('msg91_authentication_key_sid') ? ' is-invalid' : '' }}"
                                                type="text" id="msg91_authentication_key_sid" name="msg91_authentication_key_sid" autocomplete="off" value="{{isset($sms_services)? @$sms_services['Msg91']->msg91_authentication_key_sid : ''}}"> 
                                                <label>@lang('system_settings.authentication_key_sid') <span>*</span> </label> 
                                                <span class="focus-border"></span>
                                                @if ($errors->has('msg91_authentication_key_sid'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('msg91_authentication_key_sid') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 mb-30">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('msg91_sender_id') ? ' is-invalid' : '' }}"
                                                type="text" name="msg91_sender_id" autocomplete="off" value="{{isset($sms_services)? @$sms_services['Msg91']-> msg91_sender_id : ''}}" id="msg91_sender_id">
                                                <label>@lang('system_settings.sender_id') <span>*</span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('msg91_sender_id'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('msg91_sender_id') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 mb-30">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('msg91_route') ? ' is-invalid' : '' }}"
                                                type="text" name="msg91_route" autocomplete="off" value="{{isset($sms_services)? @$sms_services['Msg91']-> msg91_route : ''}}" id="msg91_route">
                                                <label>@lang('common.route') <span>*</span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('msg91_route'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('msg91_route') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('msg91_country_code') ? ' is-invalid' : '' }}"
                                                type="text" name="msg91_country_code" autocomplete="off" value="{{isset($sms_services)? @$sms_services['Msg91']-> msg91_country_code : ''}}" id="msg91_country_code">
                                                <label>@lang('system_settings.country_code') <span>*</span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('msg91_country_code'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('msg91_country_code') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <div class="row justify-content-center">
                                         <img class="logo" width="" height="" src="{{ URL::asset('public/backEnd/img/MSG91-logo.png') }}">
                                    </div>
                                </div>
                            </div>
                        
                        @php 
                            $tooltip = "";
                            if(userPermission(447)){
                                    $tooltip = "";
                                }else{
                                    $tooltip = "You have no permission to add";
                                }
                        @endphp
                        <div class="row mt-40">
                            <div class="col-lg-12 text-center"> 
                                <button class="primary-btn fix-gr-bg" type="submit" data-toggle="tooltip" title="{{@$tooltip}}"> 
                                    <span class="ti-check"></span>
                                    @lang('common.update')
                                    
                                </button>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>

                    <!-- Start Exam Tab -->
            <div role="tabpanel" class="tab-pane fade @if (Session::get('textlocal_settings') == 'active') show active @endif " id="textlocal_settings">
            @if(userPermission(446))
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'update-textlocal-data', 'id' => 'textlocal_form', 'method'=>'POST']) }}
            @endif
                <div class="white-box">
                        <div class="">
                            <input type="hidden" name="gateway_id" id="gateway_id" value="3"> 
                            <input type="hidden" name="gateway_name" value="TextLocal">
                            <div class="row mb-30">

                                <div class="col-md-5">
                                    <div class="row">
                                        <div class="col-lg-12 mb-30">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('textlocal_username') ? ' is-invalid' : '' }}"
                                                type="text" name="textlocal_username" autocomplete="off" value="{{isset($sms_services)? @$sms_services['TextLocal']->textlocal_username : ''}}" id="textlocal_username">
                                                <label>@lang('system_settings.textlocal_username')<span>*</span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('textlocal_username'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('textlocal_username') }}</strong>
                                                </span>
                                                @endif
                                                </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 mb-30">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('textlocal_hash') ? ' is-invalid' : '' }}"
                                                type="text" name="textlocal_hash" autocomplete="off" value="{{isset($sms_services)? @$sms_services['TextLocal']->textlocal_hash : ''}}" id="textlocal_hash">
                                                <label>@lang('system_settings.textlocal_hash') <span>*</span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('textlocal_hash'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('textlocal_hash') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('textlocal_sender') ? ' is-invalid' : '' }}"
                                                type="text" name="textlocal_sender" autocomplete="off" value="{{isset($sms_services)? @$sms_services['TextLocal']->textlocal_sender : ''}}" id="textlocal_sender">
                                                <label>@lang('system_settings.textlocal_sender')<span>*</span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('textlocal_sender'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('textlocal_sender') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- <div class="col-md-7">
                                    <div class="row justify-content-center">
                                         <img class="logo" width="250" height="90" src="{{ URL::asset('public/backEnd/img/twilio.png') }}">
                                    </div>
                                </div> -->
                            </div>
                        </div>
                        <!-- Start Exam Tab -->
                        @php 
                            $tooltip = "";
                            if(userPermission(447)){
                                    $tooltip = "";
                                }else{
                                    $tooltip = "You have no permission to add";
                                }
                        @endphp
                        <div class="row mt-40">
                            <div class="col-lg-12 text-center"> 
                                <button class="primary-btn fix-gr-bg" type="submit" data-toggle="tooltip" title="{{@$tooltip}}"> 
                                    <span class="ti-check"></span>
                                    @lang('common.update')
                                    
                                </button>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>

            <!-- Mobile_SMS Tab -->
            <div role="tabpanel" class="tab-pane fade @if (Session::get('Mobile_SMS') == 'active') show active @endif " id="Mobile_SMS">
                <div class="white-box">
                    <div class="row mb-30">
                        <ul class="list-unstyled">
                        @php    
                            if( $sms_services['Mobile SMS']->device_info){
                                $data = json_decode( $sms_services['Mobile SMS']->device_info );
                            }
                        @endphp

                        @if($sms_services['Mobile SMS']->device_info)
                            
                                
                            <li>Device ID : {{($data->deviceId)}}</li>
                            <br>
                            <li>Device Model :{{($data->deviceName)}} </li>
                            <br>
                            <li>
                                <strong>@lang('common.status') :</strong>
                                @if ($data->status==0)
                                    <button class="primary-btn small bg-danger text-white border-0">offline</button>
                                @else
                                    <button class="primary-btn small bg-success text-white border-0  tr-bg">@lang('common.online')</button>
                                @endif
                            </li>
                        
                        @else
                            <li>No Device Connected !</li>
                        
                        @endif
                                
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Start Himalaya Sms  -->
            <div role="tabpanel" class="tab-pane fade @if (Session::get('HimalayaSms') == 'active') show active @endif " id="HimalayaSms">
            @if(userPermission(446) && moduleStatusCheck('HimalayaSms') )
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'himalayasms.update-setting', 'id' => 'textlocal_form', 'method'=>'POST']) }}
            @endif
                <div class="white-box">
                        <div class="">

                            <div class="row mb-30">

                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-lg-6 mb-30">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('himalayasms_senderId') ? ' is-invalid' : '' }}"
                                                type="text" name="himalayasms_senderId" autocomplete="off" value="{{isset($sms_services)? @$sms_services['HimalayaSms']->himalayasms_senderId : ''}}" id="himalayasms_senderId">
                                                <label>@lang('system_settings.sender_id')<span>*</span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('himalayasms_senderId'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('himalayasms_senderId') }}</strong>
                                                </span>
                                                @endif
                                                </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-30">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('himalayasms_key') ? ' is-invalid' : '' }}"
                                                type="text" name="himalayasms_key" autocomplete="off" value="{{isset($sms_services)? @$sms_services['HimalayaSms']->himalayasms_key : ''}}" id="himalayasms_key">
                                                <label>@lang('system_settings.api_key') <span>*</span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('himalayasms_key'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('himalayasms_key') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('himalayasms_routeId') ? ' is-invalid' : '' }}"
                                                type="text" name="himalayasms_routeId" autocomplete="off" value="{{isset($sms_services)? @$sms_services['HimalayaSms']->himalayasms_routeId : ''}}" id="himalayasms_routeId">
                                                <label>@lang('system_settings.route_id')<span>*</span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('himalayasms_routeId'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('himalayasms_routeId') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-20">
                                        <div class="col-lg-6 mb-30">
                                                <div class="input-effect">
                                                    <input class="primary-input form-control{{ $errors->has('himalayasms_campaign') ? ' is-invalid' : '' }}"
                                                    type="text" name="himalayasms_campaign" autocomplete="off" value="{{isset($sms_services)? @$sms_services['HimalayaSms']->himalayasms_campaign : ''}}" id="himalayasms_campaign">
                                                    <label> @lang('system_settings.campaign_id')<span>*</span> </label>
                                                    <span class="focus-border"></span>
                                                    @if ($errors->has('himalayasms_campaign'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('himalayasms_campaign') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                    </div>

                                </div>

                                <!-- <div class="col-md-7">
                                    <div class="row justify-content-center">
                                         <img class="logo" width="250" height="90" src="{{ URL::asset('public/backEnd/img/twilio.png') }}">
                                    </div>
                                </div> -->
                            </div>
                        </div>
                        <!-- Start Exam Tab -->
                        @php 
                            $tooltip = "";
                            if(userPermission(447)){
                                    $tooltip = "";
                                }else{
                                    $tooltip = "You have no permission to add";
                                }
                        @endphp
                        <div class="row mt-40">
                            <div class="col-lg-12 text-center"> 
                                <button class="primary-btn fix-gr-bg" type="submit" data-toggle="tooltip" title="{{@$tooltip}}"> 
                                    <span class="ti-check"></span>
                                    @lang('common.update')
                                    
                                </button>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>

            <div role="tabpanel" class="tab-pane fade @if (Session::get('africatalking_settings') == 'active') show active @endif" id="africatalking_settings">
            @if(userPermission(446))
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'update-africatalking-data', 'id' => 'textlocal_form', 'method'=>'POST']) }}
            @endif
                <div class="white-box">
                        <div class="">
                            <input type="hidden" name="gateway_id" id="gateway_id" value="4"> 
                            <input type="hidden" name="gateway_name" value="AfricaTalking">
                            <div class="row mb-30">

                                <div class="col-md-5">
                                    <div class="row">
                                        <div class="col-lg-12 mb-30">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('africatalking_username') ? ' is-invalid' : '' }}"
                                                type="text" name="africatalking_username" autocomplete="off" value="{{isset($sms_services)? @$sms_services['AfricaTalking']->africatalking_username : ''}}" id="textlocal_username">
                                                <label>@lang('system_settings.africatalking_username')<span>*</span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('africatalking_username'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('africatalking_username') }}</strong>
                                                </span>
                                                @endif
                                                </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 mb-30">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('africatalking_api_key') ? ' is-invalid' : '' }}"
                                                type="text" name="africatalking_api_key" autocomplete="off" value="{{isset($sms_services)? @$sms_services['AfricaTalking']->africatalking_api_key : ''}}" id="africatalking_api_key">
                                                <label>@lang('system_settings.africatalking_api_key') <span>*</span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('africatalking_api_key'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('africatalking_api_key') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- <div class="col-md-7">
                                    <div class="row justify-content-center">
                                         <img class="logo" width="250" height="90" src="{{ URL::asset('public/backEnd/img/twilio.png') }}">
                                    </div>
                                </div> -->
                            </div>
                        </div>
                        @php 
                            $tooltip = "";
                            if(userPermission(446)){
                                    $tooltip = "";
                                }else{
                                    $tooltip = "You have no permission to add";
                                }
                        @endphp
                        <div class="row mt-40">
                            <div class="col-lg-12 text-center">
                                <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="{{@$tooltip}}">
                                    <span class="ti-check"></span>
                                    @lang('common.update')
                                </button>
                            </div>
                        </div>
                    </div>
            {{ Form::close() }}
            </div>
                   </div>
     
                </div>
            </div>
          </div>
    </div>
</section>
@endsection
