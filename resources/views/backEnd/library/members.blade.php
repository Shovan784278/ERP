@extends('backEnd.master')
@section('title')
@lang('library.add_member')
@endsection
@section('mainContent')
@push('css')
<style type="text/css">
    #selectStaffsDiv, .forStudentWrapper{
        display: none;
    }
</style>
@endpush
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('library.add_member')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('library.library')</a>
                <a href="#">@lang('library.add_member')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area up_st_admin_visitor">
    <div class="container-fluid p-0">
        @if(isset($editData))
         @if(userPermission(309) )

        <div class="row">
            <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                <a href="{{route('library-member')}}" class="primary-btn small fix-gr-bg">
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
                                    @lang('library.edit_member')
                                @else
                                    @lang('library.add_member')
                                @endif
                             
                            </h3>
                        </div>
                        @if(isset($editData))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'holiday/'.$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                        @else
                        @if(userPermission(309) )
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'library-member',
                        'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @endif
                        @endif
                        <div class="white-box">
                            <div class="add-visitor">
                                <div class="row">
                                   
                                    <div class="col-lg-12 mb-30">
                                        <select class="niceSelect w-100 bb form-control{{ $errors->has('member_type') ? ' is-invalid' : '' }}" name="member_type" id="member_type">
                                            <option data-display=" @lang('library.member_type') *" value="">@lang('library.member_type') *</option>
                                            @foreach($roles as $value)
                                                @if(isset($editData))
                                                    <option value="{{$value->id}}" {{$value->id == $editData->role_id? 'selected':''}}>{{$value->full_name}}</option>
                                                @else
                                                    <option value="{{$value->id}}">{{$value->name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @if ($errors->has('member_type'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('member_type') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="forStudentWrapper col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-12 mb-30">
                                                <select class="w-100 bb niceSelect form-control {{ $errors->has('class') ? ' is-invalid' : '' }}" id="class_library_member" name="class">
                                                    <option data-display="@lang('common.select_class')" value="">@lang('common.select_class')*</option>
                                                    @foreach($classes as $class)
                                                    <option value="{{$class->id}}"  {{( old("class") == $class->id ? "selected":"")}}>{{$class->class_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-lg-12 mb-30" id="select_section__member_div">
                                                <select class="w-100 bb niceSelect form-control{{ $errors->has('section') ? ' is-invalid' : '' }}" id="select_section_member" name="section">
                                                    <option data-display="@lang('common.select_section')" value="">@lang('common.select_section') *</option>
                                                </select>
                                                <div class="pull-right loader loader_style" id="select_section_loader">
                                                    <img class="loader_img_style" src="{{asset('public/backEnd/img/demo_wait.gif')}}" alt="loader">
                                                </div>
                                            </div>


                                            <div class="col-lg-12 mb-30" id="select_student_div">
                                                <select class="w-100 bb niceSelect form-control{{ $errors->has('student') ? ' is-invalid' : '' }}" id="select_student" name="student">
                                                    <option data-display="@lang('library.select_student') *" value="">@lang('library.select_student') *</option>
                                                </select>
                                                <div class="pull-right loader loader_style" id="select_student_loader">
                                                    <img class="loader_img_style" src="{{asset('public/backEnd/img/demo_wait.gif')}}" alt="loader">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 mb-30" id="selectStaffsDiv">
                                        <select class="niceSelect w-100 bb form-control{{ $errors->has('staff_id') ? ' is-invalid' : '' }}" name="staff" id="selectStaffs">
                                            <option data-display="@lang('common.name') *" value="">@lang('common.name') *</option>

                                            @if(isset($staffsByRole))
                                            @foreach($staffsByRole as $value)
                                            <option value="{{$value->id}}" {{$value->id == $editData->staff_id? 'selected':''}}>{{$value->full_name}}</option>
                                            @endforeach
                                            @else

                                            @endif
                                        </select>
                                    </div>

                                    <div class="col-lg-12 mb-30">
                                        <div class="input-effect">
                                            <input class="primary-input form-control{{ $errors->has('member_ud_id') ? ' is-invalid' : '' }}"
                                            type="text" name="member_ud_id" autocomplete="off" value="{{isset($content_title)? $leave_type->type:''}}">
                                            <label>@lang('library.member_id') <span>*</span> </label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('member_ud_id'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('member_ud_id') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                    </div>

                                    <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">

                                </div>
                                 @php
                                  $tooltip = "";
                                  if(userPermission(309) ){
                                        $tooltip = "";
                                    }else{
                                        $tooltip = "You have no permission to add";
                                    }
                                @endphp
                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                       <button class="primary-btn fix-gr-bg submit" data-toggle="tooltip" title="{{$tooltip}}">
                                            <span class="ti-check"></span>

                                            @if(isset($editData))
                                                @lang('library.update_member')
                                            @else
                                                @lang('library.save_member')
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
                        <h3 class="mb-0">@lang('library.member_list')</h3>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-lg-12">
                    <table id="table_id" class="display school-table" cellspacing="0" width="100%">

                        <thead>
                            
                            <tr>
                                <th>@lang('common.sl')</th>
                                <th>@lang('common.name')</th>
                                <th>@lang('library.member_type')</th>
                                <th>@lang('library.member_id')</th>
                                <th>@lang('common.email')</th>
                                <th>@lang('common.mobile')</th>
                                <th>@lang('common.action')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if(isset($libraryMembers))
                            @foreach($libraryMembers as $key=>$value)
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>
                                    <?php
                                    if($value->member_type == '2'){
                                        if(!empty($value->studentDetails) && !empty($value->studentDetails->full_name)) { echo $value->studentDetails->full_name; }
                                    }elseif($value->member_type == '3'){
                                        if(!empty($value->parentsDetails) && !empty($value->parentsDetails->fathers_name)) { echo $value->parentsDetails->fathers_name; }
                                    }else{
                                        if(!empty($value->staffDetails) && !empty($value->staffDetails->full_name)) { echo $value->staffDetails->full_name; }
                                    }

                                    ?>

                                </td>
                                <td>{{!empty($value->roles)?$value->roles->name:''}}</td>
                                <td>{{$value->member_ud_id}}</td>
                                <td>
                                 <?php
                                 if($value->member_type == '2'){
                                    if(!empty($value->studentDetails) && !empty($value->studentDetails->email)) {   echo $value->studentDetails->email;}
                                }elseif($value->member_type == '3'){
                                   if(!empty($value->parentsDetails) && !empty($value->parentsDetails->guardians_email)) { echo $value->parentsDetails->guardians_email;}
                                }else{
                                   if(!empty($value->staffDetails) && !empty($value->staffDetails->email)) {  echo $value->staffDetails->email;
                                }
                                }

                                ?>

                            </td>
                            <td>
                             <?php
                             if($value->member_type == '2'){
                                    if(!empty($value->studentDetails) && !empty($value->studentDetails->mobile)) {   echo $value->studentDetails->mobile;}
                            }elseif($value->member_type == '3'){
                                   if(!empty($value->parentsDetails) && !empty($value->parentsDetails->fathers_mobile)) {   echo $value->parentsDetails->fathers_mobile; }
                            }else{
                                   if(!empty($value->staffDetails) && !empty($value->staffDetails->mobile)) {  echo $value->staffDetails->mobile; }
                            }

                            ?>
                        </td>
                        <td>
                        @if(userPermission(310))
                            <a class="primary-btn fix-gr-bg nowrap" href="{{route('cancel-membership',@$value->id)}}">@lang('library.cancel_membership')</a>
                        @endif
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
