@extends('backEnd.master')
@section('title')
@lang('inventory.item_store_list')
@endsection
@section('mainContent')
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('inventory.item_store_list')</h1>
            <div class="bc-pages">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('inventory.inventory')</a>
                <a href="#">@lang('inventory.item_store_list')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area up_st_admin_visitor">
    <div class="container-fluid p-0">
        @if(isset($editData))
         @if(userPermission(325))
           
        <div class="row">
            <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                <a href="{{route('item-store')}}" class="primary-btn small fix-gr-bg">
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
                                    @lang('inventory.edit_item_store')
                                @else
                                    @lang('inventory.add_item_store')
                                @endif
                               
                            </h3>
                        </div>
                        @if(isset($editData))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => array('item-store-update',$editData->id), 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                        @else
                         @if(userPermission(325))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'item-store',
                        'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @endif
                        @endif
                        <div class="white-box">
                            <div class="add-visitor">
                                <div class="row">                                 

                                    <div class="col-lg-12 mb-20">
                                        <div class="input-effect">
                                            <input class="primary-input form-control{{ $errors->has('store_name') ? ' is-invalid' : '' }}"
                                            type="text" name="store_name" autocomplete="off" value="{{isset($editData)? $editData->store_name : '' }}">
                                            <label> @lang('inventory.store_name') <span>*</span> </label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('store_name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('store_name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                     <div class="col-lg-12 mb-20">
                                        <div class="input-effect">
                                            <input oninput="numberCheckWithDot(this)" class="primary-input form-control{{ $errors->has('store_no') ? ' is-invalid' : '' }}"
                                            type="text" name="store_no" autocomplete="off" value="{{isset($editData)? $editData->store_no : '' }}">
                                            <label> @lang('inventory.number') *</label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('store_no'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('store_no') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                     <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <textarea class="primary-input form-control" cols="0" rows="4" name="description" id="description">{{isset($editData) ? $editData->description : ''}}</textarea>
                                    <label> @lang('common.description') <span></span> </label>
                                    <span class="focus-border textarea"></span>

                                </div>
                            </div>

                                </div>
                                	  @php 
                                  $tooltip = "";
                                  if(userPermission(325)){
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
                    <h3 class="mb-0">@lang('inventory.item_store_list')</h3>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-lg-12">
                <table id="table_id" class="display school-table" cellspacing="0" width="100%">

                    <thead>
                        
                        <tr>
                            <th>@lang('inventory.store_name')</th>
                            <th>@lang('inventory.no')</th>
                            <th>@lang('common.description')</th>
                            <th>@lang('common.action')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if(isset($itemstores))
                        @foreach($itemstores as $value)
                        <tr>
                            <td>{{$value->store_name}}</td>
                            <td>{{$value->store_no}}</td>
                            <td>{{$value->description}}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                        @lang('common.select')
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                    @if(userPermission(326))
                                        <a class="dropdown-item" href="{{route('item-store-edit',@$value->id)}}">@lang('common.edit')</a>
                                    @endif
                                    @if(userPermission(327))
                                        <a class="deleteUrl dropdown-item" data-modal-size="modal-md" title="@lang('inventory.delete_store')" href="{{route('delete-store-view',$value->id)}}">@lang('common.delete')</a>
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
