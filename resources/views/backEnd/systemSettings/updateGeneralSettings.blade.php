@extends('backEnd.master')
@section('title')
@lang('system_settings.general_settings')
@endsection
@section('mainContent')
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('system_settings.update_general_settings')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="{{route('general-settings')}}">@lang('system_settings.general_settings_view')</a>
              </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-6">
                <div class="main-title">
                    <h3 class="mb-30">
                        @lang('common.update')
                   </h3>
                </div>
            </div>
        </div>
        @if(Illuminate\Support\Facades\Config::get('app.app_sync'))
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'admin-dashboard', 'method' => 'GET', 'enctype' => 'multipart/form-data']) }}
        @else
            @if(userPermission(409))
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'update-general-settings-data', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            @endif
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="white-box">
                    <div class="">
                        <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">


                        <div class="row mb-40">
                            <div class="col-lg-4">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('school_name') ? ' is-invalid' : '' }}"
                                    type="text" name="school_name" autocomplete="off" value="{{isset($editData)? @$editData->school_name : old('school_name')}}">
                                    <label>@lang('common.school_name') <span>*</span></label>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('school_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('school_name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('site_title') ? ' is-invalid' : '' }}"
                                    type="text" name="site_title" autocomplete="off" value="{{isset($editData)? @$editData->site_title : old('site_title')}}">
                                    <label>@lang('system_settings.site_title') <span>*</span></label>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('site_title'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('site_title') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="input-effect">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('session_id') ? ' is-invalid' : '' }}" name="session_id" id="session_id">
                                        <option data-display="@lang('common.select_academic_year') *" value="">@lang('common.select_academic_year')</option>
                                        @foreach(academicYears() as $key=>$value)
                                        <option value="{{@$value->id}}"
                                        @if(isset($editData))
                                        @if(@$editData->session_id == @$value->id)
                                        selected
                                        @endif
                                        @endif
                                        >{{@$value->year}} ({{@$value->title}})</option>
                                        @endforeach
                                    </select>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('session_id'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('session_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb-40">
                            <div class="col-lg-3">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('school_code') ? ' is-invalid' : '' }}"
                                    type="text" name="school_code" autocomplete="off" value="{{isset($editData)? @$editData->school_code: old('school_code')}}">
                                    <label>@lang('system_settings.school_code') <span>*</span></label>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('school_code'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('school_code') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}"
                                    type="text" name="phone" autocomplete="off" value="{{ isset($editData) ? @$editData->phone : old('phone')}}">
                                    <label>@lang('common.phone') <span>*</span></label>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('phone'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                    type="text" name="email" autocomplete="off" value="{{isset($editData)? @$editData->email: old('email')}}">
                                    <label>@lang('common.email') <span>*</span></label>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-effect">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('income_head') ? ' is-invalid' : '' }}" name="income_head" id="income_head_id">
                                        <option data-display="@lang('system_settings.fees_income_head') *" value="">@lang('common.select')</option>
                                        @foreach($sell_heads as $sell_head)
                                            <option value="{{$sell_head->id}}"
                                            {{isset($editData)? ($editData->income_head_id == $sell_head->id? 'selected':''):''}}
                                            >{{$sell_head->head}}</option>
                                        @endforeach
                                    </select>
                                    <span class="focus-border"></span>
                                        @if ($errors->has('income_head'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('income_head') }}</strong>
                                    </span>
                                        @endif
                                    <span class="modal_input_validation red_alert"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-40">
                            

                           <div class="col-lg-2">
                                <div class="input-effect">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('language_id') ? ' is-invalid' : '' }}" name="language_id" id="language_id">
                                        <option data-display="@lang('system_settings.language') *" value="">@lang('common.select') <span>*</span></option>
                                        @php $lang = App\SmLanguage::all(); @endphp
                                        @if(isset($lang))
                                        @foreach($lang as $key=>$value)
                                        <option value="{{@$value->id}}"
                                        @if(isset($editData))
                                        @if(@$editData->language_id == @$value->id)
                                        selected
                                        @endif
                                        @endif
                                        >{{@$value->language_name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('language_id'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('language_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                           <div class="col-lg-2">
                                <div class="input-effect">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('week_start_id') ? ' is-invalid' : '' }}" name="week_start_id" id="week_start_id">
                                        <option data-display="@lang('system_settings.week_start_day') *" value="">@lang('system_settings.week_start_day') <span>*</span></option>
                                        @foreach ($weekends as $weekend)
                                            <option value="{{$weekend->id}}" @if(isset($editData)) @if(@$editData->week_start_id == @$weekend->id) selected @endif  @endif>{{$weekend->name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('week_start_id'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('week_start_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="input-effect">
                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('date_format_id') ? ' is-invalid' : '' }}" name="date_format_id" id="date_format_id">
                                        <option data-display="@lang('system_settings.select_date_format') *" value="">@lang('common.select') <span>*</span></option>
                                        @if(isset($dateFormats))
                                        @foreach($dateFormats as $key=>$value)
                                        <option value="{{@$value->id}}"
                                        @if(isset($editData))
                                        @if(@$editData->date_format_id == @$value->id)
                                        selected
                                        @endif
                                        @endif
                                        >{{@$value->normal_view}} [{{@$value->format}}]</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('date_format_id'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('date_format_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="input-effect">
                                     <select name="time_zone" class="niceSelect w-100 bb form-control {{ $errors->has('time_zone') ? ' is-invalid' : '' }}" id="time_zone">
                                        <option data-display="@lang('common.select_time_zone') *" value="">@lang('common.select_time_zone') *</option>

                                        @foreach($time_zones as $time_zone)
                                        <option value="{{@$time_zone->id}}" {{@$time_zone->id == @$editData->time_zone_id? 'selected':''}}>{{@$time_zone->time_zone}}</option>
                                        @endforeach



                                    </select>

                                    <span class="focus-border"></span>
                                        @if ($errors->has('time_zone'))
                                        <span class="invalid-feedback invalid-select" role="alert">
                                            <strong>{{ $errors->first('time_zone') }}</strong>
                                        </span>
                                        @endif


                                 </div>
                            </div>
                        </div>

                        </div>

                        <div class="row mb-40">

                            <div class="col-lg-3">
                                <div class="input-effect">
                                     <select name="currency" class="niceSelect w-100 bb form-control {{ $errors->has('currency') ? ' is-invalid' : '' }}" id="currency">
                                        <option data-display="@lang('system_settings.select_currency')" value="">@lang('system_settings.select_currency')</option>
                                         @foreach($currencies as $currency)
                                            <option value="{{@$currency->code}}" {{isset($editData)? (@$editData->currency  == @$currency->code? 'selected':''):''}}>{{$currency->name}} ({{$currency->code}})</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('currency'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('currency') }}</strong>
                                    </span>
                                    @endif

                                 </div>
                            </div>

                                

                                <div class="col-lg-2">
                                    <div class="input-effect">
                                        <input class="primary-input form-control{{ $errors->has('currency_symbol') ? ' is-invalid' : '' }}"
                                        type="text" name="currency_symbol" autocomplete="off" value="{{isset($editData)? @$editData->currency_symbol : old('currency_symbol')}}" id="currency_symbol" readonly="">
                                        <label>@lang('system_settings.currency_symbol') <span>*</span></label>
                                        <span class="focus-border"></span>
                                        @if ($errors->has('currency_symbol'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('currency_symbol') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
    
                            <div class="col-lg-3">
                                <div class="input-effect">
                                    <input oninput="numberCheck(this)" class="primary-input form-control{{ $errors->has('file_size') ? ' is-invalid' : '' }}"
                                    type="text" name="file_size" {{moduleStatusCheck('Saas')== TRUE && Auth::user()->is_administrator != "yes" ? 'readonly':''}} autocomplete="off" value="{{isset($editData)? @$editData->file_size : old('file_size')}}" id="file_size" >
                                    <label>@lang('system_settings.max_upload_file_size') (MB) <span>*</span></label>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('file_size'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('file_size') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-4 d-flex relation-button">
                                <p class="text-uppercase mb-0">@lang('student.multiple_roll_number')</p>
                                <div class="d-flex radio-btn-flex ml-30">
                                    <div class="mr-20">
                                        <input type="radio" name="multiple_roll" id="roll_yes" value="1" class="common-radio relationButton" {{@$editData->multiple_roll == "1"? 'checked': ''}}>
                                        <label for="roll_yes">@lang('common.enable')</label>
                                    </div>
                                    <div class="mr-20">
                                        <input type="radio" name="multiple_roll" id="roll_no" value="0" class="common-radio relationButton" {{@$editData->multiple_roll == "0"? 'checked': ''}}>
                                        <label for="roll_no">@lang('common.disable')</label>
                                    </div>
                                </div>
                            </div>
                            

                        </div>
                        <div class="row mb-30 mt-20">
                            <div class="col-lg-6 d-flex relation-button">
                                <p class="text-uppercase mb-0">@lang('system_settings.promossion_without_exam')</p>
                                <div class="d-flex radio-btn-flex ml-30">
                                    <div class="mr-20">
                                        <input type="radio" name="promotionSetting" id="relationMother" value="0" class="common-radio relationButton" {{@$editData->promotionSetting == "0"? 'checked': ''}}>
                                        <label for="relationMother">@lang('common.enable')</label>
                                    </div>
                                    <div class="mr-20">
                                        <input type="radio" name="promotionSetting" id="relationFather" value="1" class="common-radio relationButton" {{@$editData->promotionSetting == "1"? 'checked': ''}}>
                                        <label for="relationFather">@lang('common.disable')</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 d-flex relation-button">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('ss_page_load') ? ' is-invalid' : '' }}"
                                    type="text" oninput="numberCheck(this)" name="ss_page_load" autocomplete="off" value="{{isset($editData)? @$editData->ss_page_load : old('ss_page_load')}}" id="ss_page_load" >
                                    <label>@lang('system_settings.ss_page_load') <span>*</span></label>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('ss_page_load'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('ss_page_load') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    
                        <div class="row mb-30 mt-20">
                            <div class="col-lg-6 d-flex relation-button">
                                <p class="text-uppercase mb-0">@lang('system_settings.subject_attendance_layout')</p>
                                <div class="d-flex radio-btn-flex ml-30">
                                    <div class="mr-20">
                                        {{-- <label for=""> --}}
                                            <input type="radio" name="attendance_layout" id="first_layout" value="1" class="common-radio relationButton attendance_layout"  {{@$editData->attendance_layout == "1"? 'checked': ''}}>
                                            <label for="first_layout">
                                                <img src="{{asset('public/backEnd/img/first_layout.png')}}" width="200px" height="auto" class="layout_image" for="first_layout" alt="">
                                            </label>
                                            {{-- </label> --}}
                                    </div>
                                    <div class="mr-20">
                                        <input type="radio" name="attendance_layout" id="second_layout" value="0" class="common-radio relationButton attendance_layout" {{@$editData->attendance_layout == "0"? 'checked': ''}}>
                                        <label for="second_layout">
                                            <img src="{{asset('public/backEnd/img/second_layout.png')}}" width="200px" height="auto" class="layout_image" for="second_layout" alt="">
                                        </label>
                                        </div>
                                </div>
                            </div>
                            @if(moduleStatusCheck('Fees'))
                            <div class="col-lg-6 d-flex relation-button">
                                <p class="text-uppercase mb-0">@lang('fees::feesModule.new_fees_module')</p>
                                <div class="d-flex radio-btn-flex ml-30">
                                    <div class="mr-20">
                                        <input type="radio" name="fees_status" id="fees_enable" value="1" class="common-radio relationButton" {{@$editData->fees_status == "1"? 'checked': ''}}>
                                        <label for="fees_enable">@lang('system_settings.enable')</label>
                                    </div>
                                    <div class="mr-20">
                                        <input type="radio" name="fees_status" id="fees_disable" value="0" class="common-radio relationButton" {{@$editData->fees_status == "0"? 'checked': ''}}>
                                        <label for="fees_disable">@lang('common.disable')</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade admin-query" id="newFees" >
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">@lang('fees::feesModule.confirmation')</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                            
                                        <div class="modal-body">
                                            <div class="text-center">
                                                <h4>
                                                    <strong>Mention:</strong> Only one fees could work, 
                                                    if you enable news fees old fees can see but cant collect fees or others, 
                                                    clear all adjustment before enable new fees.
                                                </h4>
                                            </div>
                                        </br>
                                            <div class="text-center">
                                                <button type="button" class="primary-btn fix-gr-bg" data-dismiss="modal">@lang('fees::feesModule.agree')</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                             </div>
                        
                            </div>
                            @endif
                        <div class="row md-30">
                            <div class="col-lg-12">
                                <div class="input-effect">
                                <textarea class="primary-input form-control" cols="0" rows="4" name="address" id="address">{{isset($editData) ? @$editData->address : old('address')}}</textarea>
                                    <label>@lang('system_settings.school_address') <span></span> </label>
                                    <span class="focus-border textarea"></span>

                                </div>
                            </div>
                            
                        </div>
                        <div class="row md-30 mt-40">
                            <div class="col-lg-12">
                                <div class="input-effect">
                                <textarea class="primary-input form-control" cols="0" rows="4" name="copyright_text" id="copyright_text">{{isset($editData) ? @$editData->copyright_text : old('copyright_text')}}</textarea>
                                    <label>@lang('system_settings.copyright_text') <span></span> </label>
                                    <span class="focus-border textarea"></span>

                                </div>
                            </div>
                        </div>

        
                    <div class="row mt-40">
                        <div class="col-lg-12 text-center">

                            @if(env('APP_SYNC')==TRUE)
                                <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Disabled For Demo "> <button class="primary-btn small fix-gr-bg  demo_view" style="pointer-events: none;" type="button" > @lang('common.update')</button></span>
                            @else
                                @if(userPermission(409))
                                <button type="submit" class="primary-btn fix-gr-bg submit">
                                    <span class="ti-check"></span>
                                    @lang('common.update')
                                </button>
                                @endif
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        {{ Form::close() }}
    </div>

</div>
</section>
<div class="modal fade admin-query question_image_preview"  >
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('system_settings.layout_image')</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <img src="" width="100%" class="question_image_url" alt="">
            </div>

        </div>
    </div>
</div>
<script>
    $(document).on('click', '.layout_image', function(){
        // $('.question_image_url').src(this.src);
        $('.question_image_url').attr('src',this.src);   
        $('.question_image_preview').modal('show');
    })

    $('#fees_enable').change(function() {
        $('#newFees').modal('show');
    });
</script>
@endsection
