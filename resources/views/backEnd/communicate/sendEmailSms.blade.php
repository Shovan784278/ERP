@extends('backEnd.master')
@section('title')
    @lang('communicate.Send_Email_Sms')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('communicate.send_email') </h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                    <a href="#">@lang('communicate.communicate')</a>
                    <a href="#"> @lang('communicate.Send_Email_Sms')</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="main-title">
                        <h3 class="mb-30">@lang('communicate.Send_Email_Sms') </h3>
                    </div>
                </div>
            </div>
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'send-email-sms', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="white-box sm2_mb_20">
                                <div class="">
                                    <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="input-effect mb-30">
                                                <input class="primary-input form-control{{ $errors->has('email_sms_title') ? ' is-invalid' : '' }}" type="text" name="email_sms_title" autocomplete="off" value="{{ old('email_sms_title') }}">
                                                <label>@lang('common.title') <span>*</span> </label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('email_sms_title'))
                                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('email_sms_title') }}</strong>
                                                        </span>
                                                @endif
                                            </div>
                                            <div class="col-lg-12 d-flex mb-20">
                                                <div class="row">
                                                    <p class="text-uppercase fw-500 mb-10">@lang('communicate.send_through')</p>
                                                    <div class="d-flex radio-btn-flex ml-40">
                                                        <div class="mr-30">
                                                            <input type="radio" name="send_through" id="Email" value="E" class="common-radio relationButton" checked>
                                                            <label for="Email">@lang('communicate.email')</label>
                                                        </div>
                                                        <div class="mr-30">
                                                            <input type="radio" name="send_through" id="Sms" value="S" class="common-radio relationButton">
                                                            <label for="Sms">@lang('communicate.sms')</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="input-effect">
                                                <textarea class="primary-input form-control {{ $errors->has('description') ? ' is-invalid' : '' }}" cols="0" rows="4" name="description" id="details">{{ old('description') }}</textarea>
                                                <label>@lang('common.description') <span>*</span> </label>
                                                <span class="focus-border textarea"></span>
                                                @if ($errors->has('description'))
                                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('description') }}</strong>
                                                        </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="row student-details mt_0_sm">
                                <!-- Start Sms Details -->
                                <div class="col-lg-12">
                                    <ul class="nav nav-tabs mt_0_sm mb-20" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#group_email_sms" selectTab="G" role="tab" data-toggle="tab">@lang('communicate.group')</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" selectTab="I" href="#indivitual_email_sms" role="tab" data-toggle="tab">@lang('communicate.individual')</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" selectTab="C" href="#class_section_email_sms" role="tab" data-toggle="tab">@lang('common.class')</a>
                                        </li>
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        <input type="hidden" name="selectTab" id="selectTab" value="G">
                                        <div role="tabpanel" class="tab-pane fade show active" id="group_email_sms">
                                            <div class="white-box">
                                                <div class="col-lg-12">
                                                    <label>@lang('communicate.message_to') *</label>
                                                    <br>
                                                    @foreach($roles as $role)
                                                        <div class="">
                                                            <input type="checkbox" id="role_{{@$role->id}}" class="common-checkbox" value="{{@$role->id}}" name="role[]">
                                                            <label for="role_{{@$role->id}}">{{@$role->name}}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div role="tabpanel" class="tab-pane fade" id="indivitual_email_sms">
                                            <div class="white-box">
                                                <div class="row mb-35">
                                                    <div class="col-lg-12">
                                                        <select class="niceSelect w-100 bb form-control{{ $errors->has('role_id') ? ' is-invalid' : '' }}" name="role_id" id="staffsByRoleCommunication">
                                                            <option data-display="@lang('communicate.role')  *" value="">@lang('communicate.role') *</option>
                                                            @foreach($roles as $value)
                                                                @if(isset($editData))
                                                                    <option value="{{@$value->id}}" {{@$value->id == @$editData->role_id? 'selected':''}}>{{ @$value->name}}</option>
                                                                @else
                                                                    <option value="{{ @$value->id}}" {{ old($value->id) ? 'selected':''}} >{{ @$value->name}}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('leave_type'))
                                                            <span class="invalid-feedback invalid-select" role="alert">
                                                                <strong>{{ $errors->first('leave_type') }}</strong>
                                                            </span> @endif
                                                    </div>
                                                    <div class="col-lg-12 mt-30" id="selectStaffsDiv">
                                                        <label for="checkbox" class="mb-2">@lang('common.name')</label>
                                                        <select multiple id="selectStaffss" name="message_to_individual[]" style="width:300px"></select>
                                                        <div class="">
                                                            <input type="checkbox" id="checkbox" class="common-checkbox">
                                                            <label for="checkbox" class="mt-3">@lang('common.select_all') </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Individual Tab -->

                                        <!-- Start Class Section Tab -->
                                        <div role="tabpanel" class="tab-pane fade" id="class_section_email_sms">
                                            <div class="white-box">
                                                <div class="row mb-35">
                                                    <div class="col-lg-12">
                                                        <select class="niceSelect w-100 bb form-control{{ $errors->has('role_id') ? ' is-invalid' : '' }}" name="class_id" id="class_id_email_sms">
                                                            <option data-display="@lang('common.class')  *" value="">@lang('common.class') *</option>
                                                            @if(isset($classes))
                                                                @foreach($classes as $value)
                                                                    <option value="{{@$value->id}}">{{@$value->class_name}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        @if ($errors->has('leave_type'))
                                                            <span class="invalid-feedback invalid-select" role="alert">
                                                                    <strong>{{ $errors->first('leave_type') }}</strong>
                                                                </span>
                                                        @endif
                                                    </div>

                                                    <div class="col-lg-12 mt-30" id="selectSectionsDiv">
                                                        <label for="checkbox" class="mb-2">@lang('common.section')</label>
                                                        <select multiple id="selectSectionss" name="message_to_section[]" style="width:300px"></select>
                                                        <div class="">
                                                            <input type="checkbox" id="checkbox_section" class="common-checkbox">
                                                            <label for="checkbox_section" class="mt-3">@lang('common.select_all')</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alert alert-warning mt-30">
                @lang('communicate.For_Sending_Email')
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
            </div>
            @if(userPermission(292))
                <div class="white-box mt-30">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <button class="primary-btn fix-gr-bg">
                                <span class="ti-check"></span> @lang('communicate.send')
                            </button>
                        </div>
                    </div>
                </div>
            @endif
            {{ Form::close() }}
        </div>
    </section>
@endsection
