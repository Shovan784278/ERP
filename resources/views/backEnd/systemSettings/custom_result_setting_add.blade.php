@extends('backEnd.master')
@section('title')
    @lang('exam.result_settings')
@endsection

@section('mainContent')
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('exam.setup_exam_rule')</h1>
                <div class="bc-pages">
                    <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                    <a href="#">@lang('exam.examination')</a>
                    <a href="#">@lang('exam.settings')</a>
                    <a href="#">@lang('exam.setup_exam_rule')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-title">
                                <h3 class="mb-30">
                                    @lang('exam.setup_final_exam_rule')
                                </h3>
                            </div>
                            @if($edit_data > 1)
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => array('custom-result-setting/update'), 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                            @else
                                @if(userPermission(437))
                                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'custom-result-setting/store','method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                @endif
                            @endif
                            <div class="white-box">
                                <div class="add-visitor">
                                    <div class="row mb-40 ">
                                        @php
                                            $average=0;
                                        @endphp
                                        @foreach($exams as $exam)
                                            <div class="col-lg-12 mt-15">
                                                <div class="row">
                                                    <div class="col-lg-7 d-flex">
                                                        <p class="text-uppercase fw-300 mb-10">
                                                            @lang('exam.exam_type') {{$exam->title}} (%)
                                                            <input type="hidden" value="{{$exam->id}}" name="exam_type_id[]">
                                                        </p>
                                                    </div>
                                                    <div class="col-lg-5">
                                                        <div class="radio-btn-flex ml-20">
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="input-effect sm2_mb_20 md_mb_20">
                                                                        <input type="text" data-id="{{$exam->id}}"  name="exam_type_percent[{{$exam->id}}]" value="{{ isset($exam->id) == @$exam->examTerm->exam_type_id? $exam->examTerm->exam_percentage: $average }}" class="primary-input form-controll read-only-input has-content maxPercent">
                                                                        <span class="focus-border"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="col-lg-12 mt-15 border-top">
                                            <div class="row">
                                                <div class="col-lg-7 d-flex">
                                                    <strong>
                                                        <p class="text-uppercase fw-300 mb-10">
                                                            @lang('exam.total_mark') 100%
                                                        </p>
                                                    </strong>
                                                </div>
                                                <div class="col-lg-5">
                                                    <div class="radio-btn-flex ml-20">
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="input-effect sm2_mb_20 md_mb_20">
                                                                    <strong>
                                                                        <p id="mark-calculate"></p>
                                                                    </strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        @php
                                            $tooltip = "";
                                            if(userPermission(437)){
                                                    $tooltip = "";
                                                }else{
                                                    $tooltip = "You have no permission to update";
                                                }
                                        @endphp
                                        <div class="col-lg-12 text-center">
                                            <button type="submit" class="primary-btn fix-gr-bg submit result_setup" data-toggle="tooltip" title="{{@$tooltip}}">
                                                @if($edit_data > 1)
                                                    <span class="ti-check"></span>@lang('exam.update')
                                                @else
                                                    <span class="ti-check"></span>@lang('exam.store')
                                                @endif
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="main-title">
                        <h3 class="mb-30">
                            @lang('exam.mark_contribution')
                        </h3>
                    </div>
                    <div class="white-box">
                        <table class="display school-table" cellspacing="0" width="100%">
                            <thead>
                            <tr class="border-bottom">
                                <th>@lang('exam.exam_term')</th>
                                <th class="text-right">@lang('exam.percentage')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $total_percentage = 0;
                            @endphp
                            @foreach($custom_settings as $key=>$custom_setting)
                                @if (!$custom_setting->exam_type_id == 0)
                                    <tr>
                                        <td>{{@$custom_setting->examTypeName->title}}</td>
                                        <td class="text-right">{{@$custom_setting->exam_percentage}}%</td>
                                        @php
                                            $total_percentage+=@$custom_setting->exam_percentage;
                                        @endphp
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr class="border-top">
                                <th>@lang('exam.total')</th>
                                <th class="text-right">{{$total_percentage}}%</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                </div>

                <div class="col-lg-6">
                    <div class="main-title">
                        <h3 class="mb-30">
                            @lang('exam.merit_list_contribution_using')
                        </h3>
                    </div>
                    <div class="white-box">
                        <div class="d-flex radio-btn-flex">
                            <div class="mr-30">
                                <input type="radio" name="trueOrFalse" id="totalMark" value="total_mark" class="common-radio relationButton" {{isset($meritListSettings)? $meritListSettings == "total_mark"? 'checked': '' : 'checked'}}>
                                <label for="totalMark">@lang('exam.total_mark')</label>
                            </div>
                            <div class="mr-30">
                                <input type="radio" name="trueOrFalse" id="grade" value="total_grade" class="common-radio relationButton" {{isset($meritListSettings)? $meritListSettings == "total_grade"? 'checked': '' : 'checked'}}>
                                <label for="grade">@lang('exam.total_grade')</label>
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
        $( document ).ready(function() {
            $( ".relationButton" ).change(function() {
                let value = $( this ).val();
                $.ajax({
                    type: "POST",
                    data: {
                        value: value,
                    },
                    dataType: "json",
                    url: "{{route('merit-list-settings')}}",
                    success: function(data) {
                        if (data == "success") {
                            toastr.success("Operation Successfull", "Successful", {
                                timeOut: 5000,
                            });
                        } else {
                            toastr.error("Operation Failed", "Failed!", {
                                timeOut: 5000,
                            });
                        }
                    },
                    error: function(data) {
                        console.log("Error:", data["error"]);
                    },
                })
            });
        });
    </script>
@endpush
