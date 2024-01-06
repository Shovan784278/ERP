@extends('backEnd.master')
@section('title')
    @lang('fees.fees_master')
@endsection
@section('mainContent')
    @push('css')
        <style>
            .custom_fees_master {
                border-bottom: 1px solid #d9dce7;
                padding-top: 5px;
            }


            .dloader_img_style {
                width: 40px;
                height: 40px;
            }

            .dloader {
                display: none;
            }

            .pre_dloader {
                display: block;
            }
        </style>
    @endpush
    @php
    $setting = app('school_info');
    if (!empty($setting->currency_symbol)) {
        $currency = $setting->currency_symbol;
    } else {
        $currency = '$';
    }
    @endphp
    <section class="sms-breadcrumb mb-40 white-box">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('fees.fees_master')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('fees.fees_collection')</a>
                    <a href="#">@lang('fees.fees_master')</a>
                </div>
            </div>
        </div>
    </section>
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            @if (isset($fees_master))
                @if (userPermission(119))
                    <div class="row">
                        <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                            <a href="{{ route('fees-master') }}" class="primary-btn small fix-gr-bg">
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
                                    @if (isset($fees_master))
                                        @lang('fees.edit_fees_master')
                                    @else
                                        @lang('fees.add_fees_master')
                                    @endif

                                </h3>
                            </div>

                            @if (isset($fees_master))
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => ['fees-master-update', $fees_master->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'fees_master_form']) }}
                            @else
                                @if (userPermission(119))
                                    {{ Form::open([
                                        'class' => 'form-horizontal',
                                        'files' => true,
                                        'route' => 'fees-master',
                                        'method' => 'POST',
                                        'enctype' => 'multipart/form-data',
                                        'id' => 'fees_master_form',
                                    ]) }}
                                @endif
                            @endif
                            <div class="white-box">
                                <div class="add-visitor">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <select
                                                class="niceSelect w-100 bb form-control{{ $errors->has('fees_type') ? ' is-invalid' : '' }}"
                                                name="fees_type">
                                                <option data-display="@lang('fees.fees_type') *" value="">@lang('fees.fees_type') *
                                                </option>
                                                @foreach ($fees_types as $fees_type)
                                                    @if (!in_array($fees_type->id, $already_assigned))
                                                        @if (isset($fees_master))
                                                            <option value="{{ $fees_type->id }}"
                                                                {{ $fees_type->id == $fees_master->fees_type_id ? 'selected' : '' }}>
                                                                {{ @$fees_type->name }}
                                                                ({{ @$fees_type->fessGroup->name }})</option>
                                                        @else
                                                            <option value="{{ $fees_type->id }}">{{ @$fees_type->name }}
                                                                ({{ @$fees_type->fessGroup->name }})</option>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </select>
                                            @if ($errors->has('fees_type'))
                                                <span class="invalid-feedback invalid-select" role="alert">
                                                    <strong>{{ $errors->first('fees_type') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <input type="hidden" name="id" value="{{ isset($fees_master) ? $fees_master->id : '' }}">
                                    <input type="hidden" name="fees_group_id"
                                        value="{{ isset($fees_master) ? $fees_master->fees_group_id : '' }}">

                                    <div class="row no-gutters input-right-icon mt-25">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input
                                                    class="primary-input date form-control{{ $errors->has('date') ? ' is-invalid' : '' }}"
                                                    id="startDate" type="text" name="date"
                                                    value="{{ isset($fees_master) ? date('m/d/Y', strtotime($fees_master->date)) : date('m/d/Y') }}">
                                                <label>@lang('fees.due_date') <span></span></label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('date'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('date') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button class="" type="button">
                                                <i class="ti-calendar" id="start-date-icon"></i>
                                            </button>
                                        </div>

                                    </div>
                                    @if (isset($fees_master))
                                        <div class="row  mt-25" id="fees_master_amount">
                                            <div class="col-lg-12">
                                                <div class="input-effect">
                                                    <input oninput="numberCheckWithDot(this)"
                                                        class="primary-input form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}"
                                                        type="text" name="amount" autocomplete="off"
                                                        value="{{ isset($fees_master) ? $fees_master->amount : '' }}">
                                                    <label>@lang('fees.amount') <span>*</span></label>
                                                    <span class="focus-border"></span>
                                                    @if ($errors->has('amount'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('amount') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="row  mt-25" id="fees_master_amount">
                                            <div class="col-lg-12">
                                                <div class="input-effect">
                                                    <input oninput="numberCheckWithDot(this)"
                                                        class="primary-input form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}"
                                                        type="text" name="amount" autocomplete="off"
                                                        value="{{ isset($fees_master) ? $fees_master->amount : '' }}">
                                                    <label>@lang('fees.amount') <span>*</span></label>
                                                    <span class="focus-border"></span>
                                                    @if ($errors->has('amount'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('amount') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @php
                                        $tooltip = '';
                                        if (userPermission(119)) {
                                            $tooltip = '';
                                        } else {
                                            $tooltip = 'You have no permission to add';
                                        }
                                    @endphp

                                    <div class="row mt-40">
                                        <div class="col-lg-12 text-center">
                                            <button class="primary-btn fix-gr-bg submit" data-toggle="tooltip"
                                                title="{{ $tooltip }}">
                                                <span class="ti-check"></span>
                                                @if (isset($fees_master))
                                                    @lang('fees.update_fees_master')
                                                @else
                                                    @lang('fees.save_fees_master')
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
                                <h3 class="mb-0">@lang('fees.fees_master_list')</h3>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">

                            <table id="table_id" class="display school-table" cellspacing="0" width="100%">

                                <thead>

                                    <tr>
                                        <th>@lang('fees.group')</th>
                                        <th>@lang('common.type')</th>
                                        <th>@lang('common.action')</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($fees_masters as $values)
                                        <tr>
                                            <td valign="top">
                                                @php $i = 0; @endphp
                                                @foreach ($values as $fees_master)
                                                    @php $i++; @endphp
                                                    @if ($i == 1)
                                                        {{ @$fees_master->feesGroups->name }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($values as $fees_master)
                                                    <div class="row">
                                                        <div class="col-sm-6 custom_fees_master">
                                                            {{ $fees_master->feesTypes != '' ? @$fees_master->feesTypes->name : '' }}
                                                        </div>
                                                        <div class="col-sm-2 custom_fees_master nowrap">
                                                            {{ generalSetting()->currency_symbol }}
                                                            {{ number_format((float) $fees_master->amount, 2, '.', '') }}
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="dropdown mb-20">
                                                                <button type="button" class="btn dropdown-toggle ml-20"
                                                                    data-toggle="dropdown">
                                                                    @lang('common.select')
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    @if (userPermission(120))
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('fees-master-edit', [$fees_master->id]) }}">@lang('common.edit')</a>
                                                                    @endif
                                                                    @if (userPermission(121))
                                                                        <a class="dropdown-item deleteFeesMasterSingle"
                                                                            data-toggle="modal"
                                                                            data-target="#deleteFeesMasterSingle{{ $fees_master->id }}"
                                                                            href="#"
                                                                            data-id="{{ $fees_master->id }}">@lang('common.delete')
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="modal fade admin-query"
                                                        id="deleteFeesMasterSingle{{ $fees_master->id }}">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">@lang('fees.delete_fees_type')</h4>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal">&times;</button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    <div class="text-center">
                                                                        <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                                    </div>

                                                                    <div class="mt-40 d-flex justify-content-between">
                                                                        <button type="button" class="primary-btn tr-bg"
                                                                            data-dismiss="modal">@lang('common.cancel')</button>
                                                                        {{ Form::open(['url' => 'fees-master-single-delete', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                                                        <input type="hidden" name="id" id=""
                                                                            value="{{ $fees_master->id }}">
                                                                        <button class="primary-btn fix-gr-bg"
                                                                            type="submit">@lang('common.delete')</button>
                                                                        {{ Form::close() }}
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </td>
                                            <td valign="top">
                                                @php $i = 0; @endphp
                                                @foreach ($values as $fees_master)
                                                    @php $i++; @endphp
                                                    @if ($i == 1)
                                                        <div class="dropdown">
                                                            <button type="button" class="btn dropdown-toggle"
                                                                data-toggle="dropdown">
                                                                @lang('common.select')
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-right">

                                                                @if ($fees_master->fees_group_id && userPermission(122))
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('fees_assign', [$fees_master->fees_group_id]) }}">@lang('fees.assign')/@lang('common.view')</a>
                                                                @endif
                                                                <a class="dropdown-item deleteFeesMasterGroup"
                                                                    data-toggle="modal" href="#"
                                                                    data-id="{{ $fees_master->fees_group_id }}"
                                                                    data-target="#deleteFeesMasterGroup{{ $fees_master->fees_group_id }}">@lang('common.delete')</a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="modal fade admin-query"
                                                        id="deleteFeesMasterGroup{{ $fees_master->fees_group_id }}">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">@lang('fees.delete_fees_master')</h4>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal">&times;</button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    <div class="text-center">
                                                                        <h4>@lang('common.are_you_sure_to_delete')</h4>
                                                                    </div>

                                                                    <div class="mt-40 d-flex justify-content-between">
                                                                        <button type="button" class="primary-btn tr-bg"
                                                                            data-dismiss="modal">@lang('common.cancel')</button>
                                                                        {{ Form::open(['url' => 'fees-master-group-delete', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                                                        <input type="hidden" name="id"
                                                                            value="{{ $fees_master->fees_group_id }}">
                                                                        <button class="primary-btn fix-gr-bg"
                                                                            type="submit">@lang('common.delete')</button>
                                                                        {{ Form::close() }}
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

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

    {{-- <div class="modal fade admin-query" id="deleteFeesMasterSingle" >
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('common.delete') @lang('fees.item')</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="text-center">
                    <h4>@lang('common.are_you_sure_to_delete')</h4>
                </div>

                <div class="mt-40 d-flex justify-content-between">
                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
                         {{ Form::open(['route' => 'fees-master-single-delete', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                         <input type="hidden" name="id" id="fees_master_single_id">
                        <button class="primary-btn fix-gr-bg" type="submit">@lang('common.delete')</button>
                         {{ Form::close() }}
                </div>
            </div>

        </div>
    </div>
</div> --}}


    {{-- <div class="modal-body">
                <div class="text-center">
                    <h4>@lang('common.are_you_sure_to_delete')</h4>
                </div>

                <div class="mt-40 d-flex justify-content-between">
                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('common.cancel')</button>
                     {{ Form::open(['route' => 'fees-master-group-delete', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                     <input type="hidden" name="id" id="fees_master_group_id">
                    <button class="primary-btn fix-gr-bg" type="submit">@lang('common.delete')</button>
                     {{ Form::close() }}
                </div>
            </div> --}}

    </div>
    </div>
    </div>
@endsection
