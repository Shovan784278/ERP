@extends('backEnd.master')
@section('title')
@lang('exam.exam_setup')
@endsection
@section('mainContent')
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('exam.exam_setup')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('exam.examinations')</a>
                <a href="#">@lang('exam.exam_setup')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area up_st_admin_visitor">
    <div class="container-fluid p-0">
        @if(isset($exam))
            @if(userPermission(215))
                <div class="row">
                    <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                        <a href="{{route('exam')}}" class="primary-btn small fix-gr-bg">
                            <span class="ti-plus pr-2"></span>
                            @lang('common.add')
                        </a>
                    </div>
                </div>
            @endif
        @endif
    @if(isset($exam))
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => array('exam-update',$exam->id), 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
    @else
     @if(userPermission(215))
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'exam',
    'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
    @endif
    @endif
        <div class="row">
            <div class="col-lg-3">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-title">
                            <h3 class="mb-30">
                                @if(isset($exam))
                                    @lang('exam.edit_exam')
                                @else
                                    @lang('exam.add_exam')
                                @endif

                            </h3>
                        </div>
                        <div class="white-box">
                            <div class="add-visitor">
                                <div class="row">
                                    <div class="col-lg-12" id="error-message">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>@lang('common.select_exam_type') *</label>
                                        @foreach($exams_types as $exams_type)
                                            <div class="input-effect">
                                                <input type="checkbox" id="exams_types_{{@$exams_type->id}}" class="common-checkbox exam-checkbox" name="exams_types[]" value="{{@$exams_type->id}}" {{isset($selected_exam_type_id)? ($exams_type->id == $selected_exam_type_id? 'checked':''):''}}>
                                                <label for="exams_types_{{@$exams_type->id}}">{{@$exams_type->title}}</label>
                                            </div>
                                        @endforeach
                                    <div class="input-effect">
                                        <input type="checkbox" id="all_exams" class="common-checkbox" name="all_exams[]" value="0" {{ (is_array(old('class_ids')) and in_array($class->id, old('class_ids'))) ? ' checked' : '' }}>
                                        <label for="all_exams">@lang('exam.all_select')</label>
                                    </div>
                                    </div>
                                    <div class="col-lg-12">
                                        @if($errors->has('exams_types'))
                                            <span class="text-danger validate-textarea-checkbox" role="alert">
                                                <strong>{{ $errors->first('exams_types') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <select class="w-100 bb niceSelect form-control {{ $errors->has('class_ids') ? ' is-invalid' : '' }}" id="exam_class" name="class_ids">
                                            <option data-display="@lang('common.select_class') *" value="">@lang('common.select_class') *</option>
                                            @foreach($classes as $class)
                                            <option value="{{@$class->id}}">{{@$class->class_name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('class_ids'))
                                            <span class="invalid-feedback invalid-select" role="alert">
                                                <strong>{{ $errors->first('class_ids') }}</strong>
                                            </span>
                                        @endif
                                </div>
                            </div>
                                <div class="row mt-25" id="exam_subejct">
                                </div>
                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <input oninput="numberMinCheck(this)" class="primary-input form-control{{ $errors->has('exam_marks') ? ' is-invalid' : '' }}"
                                            type="text" name="exam_marks" id="exam_mark_main" autocomplete="off" onkeypress="return isNumberKey(event)" value="{{isset($exam)? $exam->exam_mark: 0}}" required="">
                                            <label>@lang('exam.exam_mark') *</label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('exam_marks'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('exam_marks') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="white-box mt-10">
                            <div class="row">
                                 <div class="col-lg-10">
                                    <div class="main-title">
                                        <h5>@lang('exam.add_mark_distributions') </h5>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="primary-btn icon-only fix-gr-bg" onclick="addRowMark();" id="addRowBtn">
                                    <span class="ti-plus pr-2"></span></button>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <table class="table" id="productTable">
                                    <thead>
                                    <tr>
                                        <th>@lang('exam.exam_title')</th>
                                        <th>@lang('exam.exam_mark')</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <tr id="row1" class="mt-40">
                                        <td class="border-top-0">
                                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                            <div class="input-effect">
                                                <input type="hidden" value="@lang('exam.title')" id="lang" >
                                                <input class="primary-input form-control{{ $errors->has('exam_title') ? ' is-invalid' : '' }}"
                                                       type="text" id="exam_title" name="exam_title[]" autocomplete="off" value="{{isset($editData)? $editData->exam_title : '' }}">
                                                <label>@lang('exam.title')</label>
                                            </div>
                                        </td>
                                        <td class="border-top-0">
                                            <div class="input-effect">
                                                <input oninput="numberCheck(this)" class="primary-input form-control{{ $errors->has('exam_mark') ? ' is-invalid' : '' }} exam_mark"
                                                       type="text" id="exam_mark" name="exam_mark[]" autocomplete="off"  onkeypress="return isNumberKey(event)"  value="{{isset($editData)? $editData->exam_mark : 0 }}">
                                            </div>
                                        </td>
                                        <td class="border-0">
                                            <button class="primary-btn icon-only fix-gr-bg" type="button">
                                                <span class="ti-trash"></span>
                                            </button>
                                        </td>
                                    </tr>



                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td class="border-top-0">@lang('exam.total')</td>
                                        <td class="border-top-0" id="totalMark">
                                            <input type="text" class="primary-input form-control" name="totalMark" readonly="true">
                                        </td>
                                        <td class="border-top-0"></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                	           @php
                                  $tooltip = "";
                                  if(userPermission(215)){
                                        $tooltip = "";
                                    }else{
                                        $tooltip = "You have no permission to add";
                                    }
                                @endphp
                                <div class="row mt-40">
                                    <div class="col-lg-12">
                                        <div class="white-box">
                                            <div class="row mt-40">
                                                <div class="col-lg-12 text-center">
                                                  <button class="primary-btn fix-gr-bg submit" data-toggle="tooltip" title="{{ @$tooltip}}">
                                                        <span class="ti-check"></span>
                                                        @if(isset($exam))
                                                            @lang('exam.update_marks')
                                                        @else
                                                            @lang('exam.save_marks')
                                                        @endif

                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
            {{ Form::close() }}

    <div class="col-lg-9">
        <div class="row">
            <div class="col-lg-4 no-gutters">
                <div class="main-title">
                    <h3 class="mb-0">@lang('exam.exam_list')</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table id="table_id" class="display school-table" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>@lang('common.sl')</th>
                            <th>@lang('exam.exam_title')</th>
                            <th>@lang('common.class')</th>
                            <th>@lang('common.section')</th>
                            <th>@lang('exam.subject')</th>
                            <th>@lang('exam.total_mark')</th>
                            <th>@lang('exam.mark_distribution')</th>
                            <th>@lang('common.action')</th>
                        </tr>
                    </thead>

                    <tbody>
                    @php $count =1 ; @endphp
                                @foreach($exams as $exam)
                                <tr>
                                    <td>{{$count++}}</td>

                                    <td>{{$exam->GetExamTitle !=""?$exam->GetExamTitle->title:""}}</td>
                                    <td>{{$exam->class !=""?$exam->class->class_name:""}}</td>
                                    <td>{{$exam->section !=""?$exam->section->section_name:""}}</td>
                                    <td>{{$exam->subject !=""?$exam->subject->subject_name:""}}</td>
                                    <td>{{$exam->exam_mark}}</td>

                                   <td>



                                        @foreach($exam->markDistributions as $row)
                                        <div class="row">
                                           <div class="col-sm-6"> {{$row->exam_title}} </div> <div class="col-sm-4"><strong> {{$row->exam_mark}} </strong></div>
                                       </div>
                                        @endforeach
                                    </td>

                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                @lang('common.select')
                                            </button>

                                                @if($exam->markRegistered == "")
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @if(userPermission(397))
                                                    <a class="dropdown-item"
                                                        href="{{route('exam-edit', $exam->id)}}">@lang('common.edit')</a>
                                                 @endif
                                                @if(userPermission(216))
                                                    <a class="dropdown-item" data-toggle="modal" data-target="#deleteExamModal{{$exam->id}}"
                                                        href="#">@lang('common.delete')</a>
                                                 @endif
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                    <div class="modal fade admin-query" id="deleteExamModal{{$exam->id}}" >
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">@lang('exam.delete_exam')</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="text-center">
                                                        <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                    </div>
                                                    <div class="mt-40 d-flex justify-content-between">
                                                        <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
                                                         {{ Form::open(['route' => array('exam-delete',$exam->id), 'method' => 'DELETE', 'enctype' => 'multipart/form-data']) }}
                                                        <button class="primary-btn fix-gr-bg" type="submit">@lang('common.delete')</button>
                                                         {{ Form::close() }}
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
