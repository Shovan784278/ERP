@extends('backEnd.master')
@section('title')
@lang('system_settings.holiday_list')
@endsection 
@section('mainContent')
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('system_settings.holiday_list')</h1>
                <div class="bc-pages">
                    <a href="{{url('dashboard')}}">@lang('common.dashboard')</a>
                    <a href="#">@lang('system_settings.system_settings')</a>
                    <a href="#">@lang('system_settings.holiday_list')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            @if(isset($editData))
                @if(userPermission(441))
                    <div class="row">
                        <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                            <a href="{{url('holiday')}}" class="primary-btn small fix-gr-bg">
                                <span class="ti-plus pr-2"></span>
                                @lang('common.add')
                            </a>
                        </div>
                    </div>
                @endif
            @endif
            <div class="row">
                <div class="col-lg-3">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-title">
                                <h3 class="mb-30">@if(isset($editData))
                                        @lang('system_settings.edit_holiday')
                                    @else
                                        @lang('system_settings.add_holiday')
                                    @endif
                                   
                                </h3>
                            </div>
                            @if(isset($editData))
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'holiday/'.$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                            @else
                                @if(userPermission(441))
                                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'holiday',
                                    'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                @endif
                            @endif
                            <div class="white-box">
                                <div class="add-visitor">
                                    <div class="row">
                                       
                                        <div class="col-lg-12 mb-20">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('holiday_title') ? ' is-invalid' : '' }}"
                                                       type="text" name="holiday_title" autocomplete="off"
                                                       value="{{isset($editData)? $editData->holiday_title : '' }}">
                                                <label>@lang('system_settings.holiday_title') <span>*</span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('holiday_title'))
                                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('holiday_title') }}</strong>
                                            </span>
                                                @endif
                                            </div>
                                        </div>

                                        <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">

                                    </div>
                                    <div class="row no-gutters input-right-icon mb-30">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input date form-control{{ $errors->has('from_date') ? ' is-invalid' : '' }}"
                                                       id="event_from_date" type="text"
                                                       name="from_date"
                                                       value="{{isset($editData)? date('m/d/Y', strtotime($editData->from_date)): date('m/d/Y')}}"
                                                       autocomplete="off">
                                                <label>@lang('system_settings.from_date') <span></span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('from_date'))
                                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('from_date') }}</strong>
                                            </span>
                                                @endif
                                            </div>

                                        </div>
                                        <div class="col-auto">
                                            <button class="" type="button">
                                                <i class="ti-calendar" id="event_start_date"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row no-gutters input-right-icon mb-30">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input date form-control{{ $errors->has('to_date') ? ' is-invalid' : '' }}"
                                                       id="event_to_date" type="text"
                                                       name="to_date"
                                                       value="{{isset($editData)? date('m/d/Y', strtotime($editData->to_date)): date('m/d/Y')}}"
                                                       autocomplete="off">
                                                <label>@lang('system_settings.to_date')<span></span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('to_date'))
                                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('to_date') }}</strong>
                                            </span>
                                                @endif
                                            </div>

                                        </div>
                                        <div class="col-auto">
                                            <button class="" type="button">
                                                <i class="ti-calendar" id="event_end_date"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row mb-20">
                                        <div class="col-lg-12">
                                            <div class="input-effect">
                                                <textarea
                                                        class="primary-input form-control {{ $errors->has('details') ? ' is-invalid' : '' }}"
                                                        cols="0" rows="4"
                                                        name="details">{{isset($editData)? $editData->details: ''}}</textarea>
                                                <label>@lang('common.description') <span>*</span> </label>
                                                <span class="focus-border textarea"></span>
                                                @if ($errors->has('details'))
                                                    <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('details') }}</strong>
                                            </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row no-gutters input-right-icon mb-30">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input class="primary-input form-control" name="upload_file_name"
                                                       type="text"
                                                       placeholder="{{isset($editData->upload_image_file) && $editData->upload_image_file != ""? getFilePath3($editData->upload_image_file):trans('common.attach_file')}}"
                                                       id="placeholderHolidayFile" readonly>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('upload_file_name'))
                                                    <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $errors->first('upload_file_name') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button class="primary-btn-small-input" type="button">
                                                <label class="primary-btn small fix-gr-bg"
                                                       for="upload_holiday_image">@lang('common.browse')</label>
                                                <input type="file" class="d-none form-control" name="upload_file_name"
                                                       id="upload_holiday_image">
                                            </button>

                                        </div>
                                    </div>
                                    @php
                                        $tooltip = "";
                                        if(userPermission(441)){
                                                $tooltip = "";
                                            }else{
                                                $tooltip = "You have no permission to add";
                                            }
                                    @endphp
                                    <div class="row mt-40">
                                        <div class="col-lg-12 text-center">
                                            <button class="primary-btn fix-gr-bg submit" data-toggle="tooltip"
                                                    title="{{@$tooltip}}">
                                                <span class="ti-check"></span>
                                                @if(isset($editData))
                                                    @lang('common.update')
                                                @else
                                                    @lang('common.save')
                                                @endif
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-lg-4 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-0">@lang('system_settings.holiday_list')</h3>
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-lg-12">
                            <table id="table_id" class="display school-table" cellspacing="0" width="100%">

                                <thead>
                               
                                <tr>
                                    <th>@lang('common.sl')</th>
                                    <th>@lang('system_settings.holiday_title')</th>
                                    <th>@lang('system_settings.from_date')</th>
                                    <th>@lang('system_settings.to_date')</th>
                                    <th>@lang('common.days')</th>
                                    <th>@lang('system_settings.details')</th>
                                    <th>@lang('common.action')</th>
                                </tr>
                                </thead>

                                <tbody>
                                @if(isset($holidays))
                                    @foreach($holidays as $key=>$value)

                                        @php

                                            $start = strtotime($value->from_date);
                                            $end = strtotime($value->to_date);

                                            $days_between = ceil(abs($end - $start) / 86400);
                                            $days = $days_between + 1;

                                        @endphp
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{$value->holiday_title}}</td>
                                            <td data-sort="{{strtotime($value->from_date)}}">
                                                {{$value->from_date != ""? dateConvert($value->from_date):''}}

                                            </td>
                                            <td data-sort="{{strtotime($value->to_date)}}">
                                                {{$value->to_date != ""? dateConvert($value->to_date):''}}

                                            </td>
                                            <td>{{$days == 1? $days.' day':$days.' days'}}</td>
                                            <td>{{Illuminate\Support\Str::limit(@$value->details, 50)}}</td>


                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn dropdown-toggle"
                                                            data-toggle="dropdown">
                                                        @lang('common.select')
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        @if(userPermission(442))
                                                            <a class="dropdown-item"
                                                               href="{{url('holiday/'.$value->id.'/edit')}}">@lang('common.edit')</a>
                                                        @endif
                                                        @if(userPermission(443))
                                                            <a class="deleteUrl dropdown-item"
                                                               data-modal-size="modal-md" title="@lang('system_settings.delete_holiday')"
                                                               href="{{url('delete-holiday-data-view/'.$value->id)}}">@lang('common.delete')</a>
                                                        @endif
                                                        @if($value->upload_image_file != "")
                                                            <a class="dropdown-item"
                                                               href="{{url($value->upload_image_file)}}" download>
                                                                @lang('system_settings.download') <span
                                                                        class="pl ti-download"></span>
                                                        @endif

                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
