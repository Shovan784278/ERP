@extends('backEnd.master')
@section('title') 
@lang('fees.fees_group')
@endsection
@section('mainContent')
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('fees.fees_group')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('fees.fees_collection')</a>
                <a href="#">@lang('fees.fees_group')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area up_st_admin_visitor">
    <div class="container-fluid p-0">
        @if(isset($fees_group))
         @if(userPermission(124))
                       
        <div class="row">
            <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                <a href="{{route('fees_group')}}" class="primary-btn small fix-gr-bg">
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
                            <h3 class="mb-30">
                                @if(isset($fees_group))
                                    @lang('fees.edit_fees_group')
                                @else
                                    @lang('fees.add_fees_group')
                                @endif
                               
                            </h3>
                        </div>
                        @if(isset($fees_group))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'fees_group_update',
                        'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @else
                          @if(userPermission(124))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'fees_group_store',
                        'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @endif
                        @endif
                        <div class="white-box">
                            <div class="add-visitor">
                                <div class="row">
                                    <div class="col-lg-12">
                                      
                                        <div class="input-effect">
                                            <input class="primary-input form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                                type="text" name="name" autocomplete="off" value="{{isset($fees_group)? $fees_group->name: old('name')}}">
                                            <input type="hidden" name="id" value="{{isset($fees_group)? $fees_group->id: ''}}">
                                            <label>@lang('common.name') <span>*</span></label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                                <div class="row mt-25">
                                    <div class="col-lg-12">
                                        <div class="input-effect">
                                            <textarea class="primary-input form-control" cols="0" rows="4"
                                                name="description">{{isset($fees_group)? $fees_group->description: old('description')}}</textarea>
                                                <label>@lang('fees.description')</label>
                                            <span class="focus-border textarea"></span>
                                        </div>
                                    </div>
                                </div>
                                  @php 
                                  $tooltip = "";
                                  if(userPermission(124)){
                                        $tooltip = "";
                                    }else{
                                        $tooltip = "You have no permission to add";
                                    }
                                @endphp
                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                        <button class="primary-btn fix-gr-bg submit" data-toggle="tooltip" title="{{$tooltip}}">
                                            <span class="ti-check"></span>
                                            @if(isset($fees_group))
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
                            <h3 class="mb-0"> @lang('fees.fees_group_list')</h3>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">

                        <table id="table_id" class="display school-table" cellspacing="0" width="100%">

                            <thead>

                                <tr>
                                    <th> @lang('common.name')</th>
                                    <th> @lang('common.description')</th>
                                    <th> @lang('common.action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($fees_groups as $fees_group)
                                <tr>
                                    <td>{{@$fees_group->name}}</td>
                                    <td>{{@$fees_group->description}}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                @lang('common.select')
                                            </button>
                                         
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @if(userPermission(125))

                                                <a class="dropdown-item" href="{{route('fees_group_edit', [$fees_group->id])}}"> @lang('common.edit')</a>
                                                @endif
                                                @if(userPermission(126))
                                                <a class="dropdown-item deleteFeesGroupModal" data-toggle="modal" data-target="#deleteFeesGroupModal"
                                                    href="#" data-id="{{$fees_group->id}}">@lang('common.delete')</a>
                                            @endif
                                                </div> 
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade admin-query" id="deleteFeesGroupModal" >
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"> @lang('fees.delete_fees_group')</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="text-center">
                    <h4> @lang('common.are_you_sure_to_delete')</h4>
                </div>

                <div class="mt-40 d-flex justify-content-between">
                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
                     {{ Form::open(['route' => 'fees_group_delete', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                     <input type="hidden" name="id" id="fees_group_id">
                    <button class="primary-btn fix-gr-bg" type="submit"> @lang('common.delete')</button>
                     {{ Form::close() }}
                </div>
            </div>

        </div>
    </div>
</div>
@endsection