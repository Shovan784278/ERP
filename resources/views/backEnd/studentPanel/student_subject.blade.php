@extends('backEnd.master')
@section('title')
@lang('academics.subject_list') 
@endsection
@section('mainContent')
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('common.subject')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="{{route('student_subject')}}">@lang('common.subject')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area up_admin_visitor">
    <div class="container-fluid p-0">
       
        <div class="row">
            <div class="col-lg-12 student-details up_admin_visitor">
                <ul class="nav nav-tabs tabs_scroll_nav ml-0" role="tablist">

                @foreach($records as $key => $record) 
                    <li class="nav-item">
                        <a class="nav-link @if($key== 0) active @endif " href="#tab{{$key}}" role="tab" data-toggle="tab">{{$record->class->class_name}} ({{$record->section->section_name}}) </a>
                    </li>
                    @endforeach

                </ul>
                <!-- Tab panes -->
                <div class="tab-content mt-40">
                    @foreach($records as $key => $record) 
                        <div role="tabpanel" class="tab-pane fade  @if($key== 0) active show @endif" id="tab{{$key}}">
                            <table id="table_id" class="display school-table" cellspacing="0" width="100%">

                                <thead>
                                    <tr>
                                        <th>@lang('common.subject')</th>
                                        <th>@lang('common.teacher')</th>
                                        <th>@lang('academics.subject_type')</th>
                                    </tr>
                                </thead>
    
                                <tbody>
                                    @foreach($record->AssignSubject as $assignSubject)
                                    <tr>
                                        <td>{{@$assignSubject->subject!=""?@$assignSubject->subject->subject_name:""}}</td>
                                        <td>{{@$assignSubject->teacher!=""?@$assignSubject->teacher->full_name:""}}</td>
                                        <td>
                                            @if(!empty(@$assignSubject->subject))
                                            {{@$assignSubject->subject->subject_type == "T"? 'Theory': 'Practical'}}
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
