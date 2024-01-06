<div class="modal-header">
    <h4 class="modal-title">@lang('fees::feesModule.view_payment_of') - ({{$feesinvoice->invoice_id}})</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
    <table class="display school-table school-table-style shadow-none p-0" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>@lang('common.sl')</th>
                <th>@lang('common.date')</th>
                <th>@lang('fees::feesModule.payment_method')</th>
                <th>@lang('fees::feesModule.change_method')</th>
                <th>@lang('fees::feesModule.paid_amount')</th>
                <th>@lang('fees::feesModule.waiver')</th>
                <th>@lang('fees.fine')</th>
                <th>@lang('common.action')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($feesTranscations as $key=>$feesTranscation)
                @if($feesTranscation->paid_amount > 0)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{dateConvert($feesTranscation->created_at)}}</td>
                        <td>{{$feesTranscation->payment_method}}</td>
                        <td>
                            @if($feesTranscation->payment_method == "Cash" || $feesTranscation->payment_method == "Cheque" || $feesTranscation->payment_method == "Bank")
                                {{ Form::open(['class' => 'form-horizontal', 'route' => 'fees.change-method', 'method' => 'POST' ,'id'=>'feesChangeMethod']) }}
                                    <input type="hidden" name="feesInvoiceId" value="{{$feesTranscation->id}}">
                                    <div class="mt-30-md">
                                        <div class="row">
                                            <div class="com-md-10">
                                                <select class="w-100 bb niceSelect1 form-control {{ $errors->has('change_method') ? ' is-invalid' : '' }} changeMethod" name="change_method">
                                                    <option data-display="@lang('fees::feesModule.change_method')" value="">@lang('fees::feesModule.change_method')</option>
                                                    @foreach($paymentMethods as $paymentMethod)
                                                        @if($paymentMethod->method != $feesTranscation->payment_method)
                                                            <option value="{{$paymentMethod->method}}">{{$paymentMethod->method}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('change_method'))
                                                    <span class="invalid-feedback invalid-select" role="alert">
                                                        <strong>{{ $errors->first('change_method') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="com-md-2">
                                                <button class="primary-btn icon-only submit fix-gr-bg changeMethodSubmit" title="@lang('common.submit')">
                                                    <span class="ti-check"></span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="bankInfo mt-20 d-none">
                                                <select class="w-100 bb niceSelect1 form-control {{ $errors->has('bank_id') ? ' is-invalid' : '' }} bankId" name="bank_id">
                                                    <option data-display="@lang('fees::feesModule.select_bank')" value="">@lang('fees::feesModule.select_bank')</option>
                                                        @foreach($banks as $bank)
                                                            <option value="{{$bank->id}}" data-id="{{$feesTranscation->id}}">{{$bank->bank_name}} ({{$bank->account_number}})</option>
                                                        @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mt-25">
                                            <div class="input-effect">
                                                <textarea class="primary-input form-control" cols="0" rows="2" name="payment_note"></textarea>
                                                <label>@lang('common.note') <span></span> </label>
                                                <span class="focus-border textarea"></span>
                                            </div>
                                        </div>
                                    </div>
                                {{ Form::close() }}
                            @endif
                        </td>
                        <td>{{$feesTranscation->paid_amount}}</td>
                        <td>{{$feesTranscation->weaver}}</td>
                        <td>{{$feesTranscation->fine}}</td>
                        <td>
                            <a class="primary-btn icon-only fix-gr-bg" type="button" href="{{route('fees.single-payment-view',['id'=>$feesTranscation->id])}}" title="@lang('common.view')">
                                <span class="ti-eye"></span>
                            </a>
                            @if($feesTranscation->payment_method== "Cash" || $feesTranscation->payment_method == "Cheque" || $feesTranscation->payment_method == "Bank" || $feesTranscation->payment_method == "Wallet")
                                <a class="primary-btn icon-only fix-gr-bg" type="button" href="{{route('fees.delete-single-fees-transcation',$feesTranscation->id)}}" data-tooltip="tooltip" title="@lang('common.delete')">
                                    <span class="ti-trash"></span>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>
<script>
    if ($(".niceSelect1").length) {
        $(".niceSelect1").niceSelect();
    }

    $(".changeMethod").on("change", function() {
        if($(this).val() == "Bank"){
            $(this).parents('tr').find('.bankInfo').removeClass('d-none');
        }else{
            $(this).parents('tr').find('.bankInfo').addClass('d-none');
            $(this).parents('tr').find('.bankId').val("");
        }
    });

    // Change Payment Method
    $(document).on('click','.changeMethodSubmit', function(e){
        e.preventDefault();
        let feesChangeMethodForm = $(this).parents('form');

        const submit_url = feesChangeMethodForm.attr('action');
        const method = feesChangeMethodForm.attr('method');
    //Start Ajax
    const formData = new FormData(feesChangeMethodForm[0]);
        $.ajax({
            url: submit_url,
            type: method,
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            dataType: 'JSON',
            success: function(response) {
                toastr.success("Save Successfully","Successful", { timeOut: 5000,});
                location.reload();
            },
        });
    });

    // $("#changeMethod").on("change", function() {
    //     let methodName = $(this).val();
    //     let feesInvoiceId = $("#feesInvoiceId").val();
    //     let bankId = $("#bankId").val();
    //     let url = $("#changeMethodRoute").val();
    //     let formData = {
    //         methodName: methodName,
    //         feesInvoiceId: feesInvoiceId,
    //     };
    //     if(methodName == "Cash" && bankId != ''){

    //     }
    //     $.ajax({
    //         type: "GET",
    //         data: formData,
    //         dataType: "json",
    //         url: url,
    //         success: function(data) {
    //             location.reload();
    //             setTimeout(function() {
    //                 toastr.success( "Operation Success!", "Success", { iconClass: "customer-info",}, { timeOut: 2000,});
    //             }, 500);
    //         },
    //         error: function(data) {
    //             console.log("Error:", data);
    //         },
    //     });
    // });
</script>