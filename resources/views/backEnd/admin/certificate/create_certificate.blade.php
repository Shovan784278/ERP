@extends('backEnd.master')
@section('title')
    @if (isset($certificate))
        @lang('admin.edit_certificate')
    @else
        @lang('admin.create_certificate')
    @endif
@endsection
@push('css')
    <style>
        .user_id_card_header {
            padding: 10px;
            background: #c738d8;
        }

        .user_id_card_header h4 {
            font-size: 18px;
            font-weight: 500;
            text-align: center;
            margin-bottom: 0;
            color: #fff;
        }

        #showCertificatePreview img{
            width: 100% !important;
        }

        .certificate_box_wrapper {
            background-image: url(./img/bg.jpg);
            width: 165.1mm;
            min-height: 144.19791667mm;
            /* display: flex; */
            justify-content: center;
            margin: auto;
            padding: 70px 0 70px 0;
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center center;
            padding-top: 5mm;
            padding-left: 5mm;
            padding-right: 5mm;
            padding-bottom: 5mm;
        }
    </style>
@endpush
@section('mainContent')
    <section class="sms-breadcrumb mb-40 white-box up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>
                    @if (isset($certificate))
                        @lang('admin.edit_certificate')
                    @else
                        @lang('admin.create_certificate')
                    @endif
                </h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('admin.admin_section')</a>
                    <a href="{{route('certificate')}}">@lang('admin.certificate')</a>
                    <a href="#">
                        @if (isset($certificate))
                            @lang('admin.edit_certificate')
                        @else
                            @lang('admin.create_certificate')
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0">
            @if (isset($certificate))
                <div class="row">
                    <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                        <a href="{{route('create-certificate')}}" class="primary-btn small fix-gr-bg">
                            <span class="ti-plus pr-2"></span>
                                @lang('admin.create_certificate')
                        </a>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-lg-7">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-title">
                                <h3 class="mb-30">
                                    @if (isset($certificate))
                                        @lang('admin.edit_certificate')
                                    @else
                                        @lang('admin.create_certificate')
                                    @endif
                                </h3>
                            </div>
                            @if (isset($certificate))
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'student-certificate-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                <input type="hidden" name="id" value="{{$certificate->id }}">
                            @else
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'student-certificate-store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                            @endif

                            <div class="white-box">
                                <div class="add-visitor">
                                    @if (moduleStatusCheck('Lms')== True)
                                        <div class="row mb-25">
                                            <div class="col-lg-12">
                                                <div class="input-effect">
                                                    <select class="niceSelect w-100 bb form-control{{ $errors->has('type') ? ' is-invalid' : '' }}" name="type" id="lmsCertificateType">
                                                        <option data-display="@lang('admin.certificate') @lang('admin.type')*" value="">@lang('admin.certificate') @lang('common.select')*</option>
                                                        <option value="School" {{isset($certificate)? ($certificate->type =="School" ?'selected':''): (old('type') == 'School' ? 'selected' : '') }}>@lang('common.school')</option>
                                                        <option value="Lms" {{isset($certificate)? ($certificate->type =="Lms" ?'selected':''): (old('type') == 'Lms' ? 'selected' : '') }}>@lang('lms::lms.lms')</option>
                                                    </select>
                                                    <span class="focus-border"></span>
                                                    @if ($errors->has('type'))
                                                        <span class="invalid-feedback invalid-select" role="alert">
                                                            <strong>{{ $errors->first('type') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <input type="hidden" name="type" value="School">
                                    @endif
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('title') ? ' is-invalid' : '' }}"
                                                    type="text" name="title" autocomplete="off" value="{{isset($certificate)? $certificate->title: old('title')}}" id="certificateTitle">
                                                <label>@lang('admin.certificate_title') <span>*</span></label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('title'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('title') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-25">
                                        <div class="col-lg-6">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('background_height') ? ' is-invalid' : '' }}"
                                                    type="text" name="background_height" autocomplete="off" value="{{isset($certificate)? $certificate->background_height:144}}" id="bgHeight">
                                                <label>@lang('admin.certificate_height') (mm) <span>*</span></label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('background_height'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('background_height') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('background_width') ? ' is-invalid' : '' }}"
                                                    type="text" name="background_width" autocomplete="off" value="{{isset($certificate)?$certificate->background_width:165}}" id="bgwidth">
                                                <label>@lang('admin.certificate_weight') (mm) <span>*</span></label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('background_width'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('background_width') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-25">
                                        <div class="col-lg-3">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('padding_top') ? ' is-invalid' : '' }}"
                                                    type="text" name="padding_top" autocomplete="off" value="{{isset($certificate)? $certificate->padding_top: old('padding_top', 5)}}" id="paddingTop">
                                                <label>@lang('admin.padding_top') (mm)</label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('padding_top'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('padding_top') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('padding_right') ? ' is-invalid' : '' }}"
                                                    type="text" name="padding_right" autocomplete="off" value="{{isset($certificate)? $certificate->padding_right: old('padding_right', 5) }}" id="paddingRight">
                                                <label>@lang('admin.padding_right') (mm)</label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('padding_right'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('padding_right') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('padding_bottom') ? ' is-invalid' : '' }}"
                                                    type="text" name="padding_bottom" autocomplete="off" value="{{isset($certificate)? $certificate->padding_bottom: old('padding_bottom', 5) }}" id="paddingBottom">
                                                <label>@lang('admin.padding_bottom') (mm)</label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('padding_bottom'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('padding_bottom') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('pading_left') ? ' is-invalid' : '' }}"
                                                    type="text" name="pading_left" autocomplete="off" value="{{isset($certificate)? $certificate->pading_left: old('pading_left', 5)}}" id="paddingLeft">
                                                <label>@lang('admin.pading_left') (mm)</label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('pading_left'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('pading_left') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                            
                                    <div class="d-flex mt-25">
                                        <div class="row flex-grow-1 d-flex justify-content-between input-right-icon no-gutters">
                                            <div class="col">
                                                <div class="input-effect">
                                                    <input class="primary-input form-control{{ $errors->has('background_image') ? ' is-invalid' : '' }}" type="text" id="certificateBackgroundImage" placeholder="{{isset($certificate)? ($certificate->background_image != ""? getFilePath3($certificate->background_image): trans('admin.background_image')):trans('admin.background_image')}}"
                                                        readonly>
                                                    <span class="focus-border"></span>
                                                    @if ($errors->has('background_image'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('background_image') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <button class="primary-btn-small-input cust-margin" type="button">
                                                    <label class="primary-btn small fix-gr-bg" for="certificateBackGroundImage">@lang('common.browse')</label>
                                                    <input type="file" class="d-none" name="background_image" id="certificateBackGroundImage" onchange="certificateBackground(this)" value="{{isset($certificate)? ($certificate->background_image != ""? getFilePath3($certificate->background_image):''): ''}}">
                                                </button>
                                            </div>
                                        </div>
                                        <button class="primary-btn icon-only fix-gr-bg ml-2" type="button" id="deleteCertificate">
                                            <span class="ti-trash"></span>
                                        </button>
                                    </div>
                            
                                    <div class="row mt-25 applicable_user">
                                        <div class="col-lg-12">
                                            <div class="input-effect">
                                                <select class="niceSelect w-100 bb form-control{{ $errors->has('applicable_user') ? ' is-invalid' : '' }}" name="applicable_user" id="applicableUser">
                                                    <option data-display="@lang('admin.applicable_user') *" value="">@lang('common.select')*</option>
                                                    @foreach($roles as $role)
                                                        <option value="{{$role->id}}" {{isset($certificate) && $certificate->role  == $role->id? 'selected':'' }}>{{$role->name}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('applicable_user'))
                                                    <span class="invalid-feedback invalid-select" role="alert">
                                                        <strong>{{ $errors->first('applicable_user') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-25">
                                        <div class="col-lg-12">
                                            <div class="input-effect mt-20">
                                                <label>@lang('communicate.body')*</label>
                                                    <textarea class="primary-input summer_note form-control{{$errors->has('body') ? ' is-invalid' : '' }}" cols="0" rows="4" name="body" maxlength="500">
                                                        {{isset($certificate)? $certificate->body: old('body')}}
                                                    </textarea>
                                                <span class="focus-border textarea"></span>
                                                @if($errors->has('body'))
                                                    <span class="error text-danger"><strong>{{ $errors->first('body')}}</strong></span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-40">
                                        <div class="col-lg-12 studentVariable d-none">
                                            <h4>
                                                <strong>@lang('communicate.variables') :</strong>
                                                <span class="text-primary">
                                                    [name] [date_of_birth] [present_address] [guardian] [created_at] 
                                                    [admission_no] [roll_no] [class] [section] [gender] [admission_date] 
                                                    [category] [cast] [father_name] [mother_name] [religion] [email] [phone], 
                                                    </br>
                                                    @lang('admin.use') [profile_image] @lang('admin.variable_for_showing_static_profile_image')
                                                </span>
                                            </h4>
                                        </div>

                                        <div class="col-lg-12 parentVariable d-none">
                                            <h4>
                                                <strong>@lang('communicate.variables') :</strong>
                                                <span class="text-primary">
                                                    [parent_name] [parent_mobile] [parent_email] [parent_occupation] [parent_address], 
                                                    </br>
                                                    @lang('admin.use') [profile_image] @lang('admin.variable_for_showing_static_profile_image')
                                                </span>
                                            </h4>
                                        </div>

                                        <div class="col-lg-12 staffVariable d-none">
                                            <h4>
                                                <strong>@lang('communicate.variables') :</strong>
                                                <span class="text-primary">
                                                    [staff_name] [dob] [present_address] [date_of_joining] [email] [mobile] [qualification]
                                                    [experience], 
                                                    </br>
                                                    @lang('admin.use') [profile_image] @lang('admin.variable_for_showing_static_profile_image')
                                                </span>
                                            </h4>
                                        </div>

                                        <div class="col-lg-12 lmsVariable d-none">
                                            <h4>
                                                <strong>@lang('communicate.variables') :</strong>
                                                <span class="text-primary">
                                                    [student_name] [course_name] [course_complete_date],
                                                </br>
                                                    @lang('admin.use') [profile_image] @lang('admin.variable_for_showing_static_profile_image')
                                                </span>
                                            </h4>
                                        </div>
                                    </div>

                                    <div class="row mt-40">
                                        <div class="col-lg-12 text-center">
                                            <button class="primary-btn fix-gr-bg submit savaIdCard" type="submit">
                                                <span class="ti-check"></span>
                                                @if(isset($id_card))
                                                    @lang('common.update')
                                                @else
                                                    @lang('common.save')
                                                @endif
                                            @lang('admin.certificate')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="row">
                        <div class="col-lg-4 no-gutters">
                            <div class="main-title">
                                <h3 class="mb-0">@lang('admin.preview_cretificate') </h3>
                            </div>
                        </div>
                    </div>
                    <div class="sticky_card">
                        <div class="user_id_card_header mt-30">
                            <h4 id="showCertificateTitle">@lang('admin.certificate_title')</h4>
                        </div>
                        <div class="mt-10">
                            <div class="certificate_box_wrapper" id='showCertificatePreview' style="background-image: url('{{ isset($certificate)? asset($certificate->background_image): ''}}');">
                                {!! isset($certificate)? $certificate->body: '' !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        $( document ).ready(function() {

            let getTitleValue = $('#certificateTitle').val();
            let getBgHeight = $('#bgHeight').val();
            let getBbgWidth = $('#bgwidth').val();
            let getApplicableUser = $('#applicableUser').val();
            let getLmsCertificate = $('#lmsCertificateType').val();
            let getPaddingTop = $('#paddingTop').val();
            let getPaddingRight = $('#paddingRight').val();
            let getPaddingLeft = $('#paddingLeft').val();
            let getPaddingBottom = $('#paddingBottom').val();
            
            if(getTitleValue){
                showTitleValue(getTitleValue);
            }

            showbgHeight(getBgHeight);
            showbgwidth(getBbgWidth);
            showApplicableUser(getApplicableUser);
            showLmsCertificate(getLmsCertificate);
            showPaddingTop(getPaddingTop);
            showPaddingRight(getPaddingRight);
            showPaddingLeft(getPaddingLeft);
            showgPaddingBottom(getPaddingBottom);

            $(document).on("keyup", "#certificateTitle", function(event) {
                showTitleValue($(this).val());
            });

            $(document).on("keyup", "#bgHeight", function(event) {
                showbgHeight($(this).val());
            });

            $(document).on("keyup", "#bgwidth", function(event) {
                showbgwidth($(this).val());
            });

            $(document).on("change", "#applicableUser", function(event) {
                showApplicableUser($(this).val());
            });

            $(document).on("change", "#lmsCertificateType", function(event) {
                showLmsCertificate($(this).val());
            });

            $(document).on("keyup", "#paddingTop", function(event) {
                showPaddingTop($(this).val());
            });

            $(document).on("keyup", "#paddingRight", function(event) {
                showPaddingRight($(this).val());
            });

            $(document).on("keyup", "#paddingLeft", function(event) {
                showPaddingLeft($(this).val());
            });

            $(document).on("keyup", "#paddingBottom", function(event) {
                showgPaddingBottom($(this).val());
            });
        });

        function showTitleValue(titleValuePass){
            $("#showCertificateTitle").html(titleValuePass);
        }

        function showbgHeight(bgHeightPass){
            $(".certificate_box_wrapper").css({"height": bgHeightPass + "mm"});
        }

        function showbgwidth(bgWidthPass){
            $(".certificate_box_wrapper").css({"width": bgWidthPass + "mm"});
        }

        function showPaddingTop(paddingTopPass){
            $(".certificate_box_wrapper").css({"padding-top": paddingTopPass + "mm"});
        }

        function showPaddingRight(paddingRightPass){
            $(".certificate_box_wrapper").css({"padding-right": paddingRightPass + "mm"});
        }

        function showPaddingLeft(paddingLeftPass){
            $(".certificate_box_wrapper").css({"padding-left": paddingLeftPass + "mm"});
        }

        function showgPaddingBottom(paddingBottomPass){
            $(".certificate_box_wrapper").css({"padding-bottom": paddingBottomPass + "mm"});
        }

        function showApplicableUser(applicableUserPass){
            if(applicableUserPass == 2){
                $('.studentVariable').removeClass('d-none');
                $('.parentVariable').addClass('d-none');
                $('.staffVariable').addClass('d-none');
            }else if(applicableUserPass == 3){
                $('.studentVariable').addClass('d-none');
                $('.parentVariable').removeClass('d-none');
                $('.staffVariable').addClass('d-none');
            }else if (applicableUserPass != 2 && applicableUserPass!= 3){
                $('.studentVariable').addClass('d-none');
                $('.parentVariable').addClass('d-none');
                $('.staffVariable').removeClass('d-none');
            }
        }

        function showLmsCertificate(lmsCertificatePass){
            if(lmsCertificatePass == "Lms"){
                $('.studentVariable').addClass('d-none');
                $('.lmsVariable').removeClass('d-none');
                $('.applicable_user').addClass('d-none');
                $('.parentVariable').addClass('d-none');
                $('.staffVariable').addClass('d-none');
            }else{
                $('.studentVariable').removeClass('d-none');
                $('.lmsVariable').addClass('d-none');
                $('.applicable_user').removeClass('d-none');
            }
        }

        function certificateBackground(input, srcBack) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.certificate_box_wrapper').css('background-image', 'url(' + e.target.result +')');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).on("click", "#deleteCertificate", function(event) {
            $('#certificateBackgroundImage').removeAttr('placeholder');
            $('#certificateBackgroundImage').attr("placeholder", "@lang('admin.background_image')");
            $('.certificate_box_wrapper').css('background-image', '');
        });

        $('.summer_note').summernote({
            tabsize: 2,
            height: 500,
            callbacks: {
            onChange: function(e) {
                    let string = $('.summer_note').val();
                    string = string.replace("[profile_image]", '<img src="http://localhost/lms/public/uploads/staff/demo/staff.jpg" height="165px" width="165px"> ');
                    $("#showCertificatePreview").html(string);
                }
            }
        });
    </script>
@endpush
