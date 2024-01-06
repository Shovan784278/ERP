@extends('backEnd.master')
@section('title')
    @lang('student.student_settings')
@endsection
@section('mainContent')
    <style type="text/css">
        #selectStaffsDiv,
        .forStudentWrapper {
            display: none;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 55px;
            height: 26px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 24px;
            width: 24px;
            left: 4px;
            bottom: 1px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background: linear-gradient(90deg, #7c32ff 0%, #c738d8 51%, #7c32ff 100%);
        }

        input:focus+.slider {
            box-shadow: 0 0 1px linear-gradient(90deg, #7c32ff 0%, #c738d8 51%, #7c32ff 100%);
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .school-table-style tr th {
            padding: 10px 18px 10px 10px;
        }
        .school-table-style tr td{
            padding: 10px 10px 0px 10px;
            color: #415094;
        }
        .school-table-style table, th, td {
        border: 1px solid #d6d2d2;
        border-collapse: collapse;
        }
        .school-table-style {
            background: #ffffff;
             padding: 0px; 
            border-radius: 0px;
            /* box-shadow: 0px 10px 15px rgb(236 208 244 / 30%); */
            margin: 0 auto;
            clear: both;
            /* border-collapse: separate; */
            border-spacing: 0;
        }

        .buttonColor {
            color: #a336eb;
        }

    </style>
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('student.settings')</h1>
                <div class="bc-pages">
                    <a href="{{ url('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('student.student_info')</a>
                    <a href="#">@lang('common.settings')</a>
                </div>
            </div>
        </div>
    </section>

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">           
            <div class="row mt-20">                
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-title">
                                <h3 class="mb-30 text-center">
                                        @lang('student.student_admission_field')
                                </h3>
                            </div>                   
                          
                            <div class="white-box">
                                <div class="add-visitor">
                                    <div class="row">
                                        @php
                                            $count=$student_settings->count();
                                            $half = round($count / 2);
                                        @endphp
                                      @foreach($student_settings as $field)
                                      @if($loop->iteration == 1 or $loop->iteration == $half+1)
                                        <div class="col-lg-6"> 
                                            <table class="display school-table school-table-style" cellspacing="0" width="100%">
                                                <thead>
                                                        <tr>
                                                            <th>@lang('student.registration_field')</th>
                                                            <th>@lang('student.student_edit')</th>
                                                            <th>@lang('student.parent_edit')</th>
                                                            <th>@lang('student.required')</th>
                                                           
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @endif   
                                                   
                                                        <tr>
                                                            <td> 
                                                                <strong>
                                                                    @if(moduleStatusCheck('Lead'))
                                                                        @if($field->field_name!="roll_number")
                                                                        {{ __('student.'.$field->field_name) }} @else 
                                                                        {{ __('student.id_number') }} 
                                                                        @endif 
                                                                    @else
                                                                    {{ __('student.'.$field->field_name) }}      
                                                                    @endif
                                                                   
                                                                </strong> 
                                                            </td>
                                                            <td>
                                                                <label class="switch">
                                                                    <input type="checkbox" data-id="{{ $field->id }}" class="student_switch_btn"
                                                                        {{ @$field->student_edit == 0 ? '' : 'checked' }}>
                                                                    <span class="slider round"></span>
                                                                </label>
                                                            </td>
                                                            <td>
                                                                <label class="switch">
                                                                    <input type="checkbox" data-id="{{ $field->id }}" class="parent_switch_btn"
                                                                        {{ @$field->parent_edit == 0 ? '' : 'checked' }}>
                                                                    <span class="slider round"></span>
                                                                </label>
                                                            </td>
                                                            <td>
                                                                <label class="switch">
                                                                    <input type="checkbox" {{ in_array($field->field_name, ['admission_number']) ? 'disabled' : ''}}
                                                                        data-id="{{ $field->id }}" data-value="{{ $field->field_name }}"
                                                                        class="required_switch_btn" {{ @$field->is_required == 0 ? '' : 'checked' }} >
                                                                        <span class="slider round"></span>
                                                                        </label>
                                                            </td>
                                                        </tr>
                                                        @if($loop->iteration == $half or $loop->iteration == $count)
                                                    </tbody>
                                            </table>
                                        </div>
                                        @endif
                                        @endforeach 
                                    </div>
                                  
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
              
            </div>
        </div>
    </section>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $(".required_switch_btn").on("change", function() {
                let filed_id = $(this).data("id");
                let filed_value = $(this).data('value');

                let type = 'required';
                if ($(this).is(":checked")) {
                    var field_status = "1";
                } else {
                    var field_status = "0";
                }
                changeStatus(field_status, filed_id, filed_value, type);
            });
            $(".student_switch_btn").on("change", function() {
                let filed_id = $(this).data("id");
                let filed_value = $(this).data('value');
                let type = 'student';
                if ($(this).is(":checked")) {
                    var field_status = "1";
                } else {
                    var field_status = "0";
                }
                changeStatus(field_status, filed_id, filed_value, type);
            });
            $(".parent_switch_btn").on("change", function() {
                let filed_id = $(this).data("id");
                let filed_value = $(this).data('value');
                let type = 'parent';
                if ($(this).is(":checked")) {
                    var field_status = "1";
                } else {
                    var field_status = "0";
                }
                changeStatus(field_status, filed_id, filed_value, type);
            });
        });

        function changeStatus(field_status, filed_id, filed_value, type) {
            let url = $("#url").val();
            $.ajax({
                type: "POST",
                data: {
                    'field_status': field_status,
                    'filed_id': filed_id,
                    'filed_value': filed_value,
                    'type': type
                },
                dataType: "json",
                url: url + "/" + "student/field/switch",
                success: function(data) {

                    if (data.message) {
                        setTimeout(function() {
                            toastr.success(data.message, "Success")
                        }, 500);
                      
                    }
                    if (data.error) {
                        setTimeout(function() {
                            toastr.success(data.error, "Failed")
                        }, 500);
                     
                    }

                },
                error: function(data) {

                    setTimeout(function() {
                        toastr.error("Operation Not Done!", "Failed", {
                            timeOut: 5000,
                        });
                    }, 500);
                },
            });
        }
    </script>
@endpush
