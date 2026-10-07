@extends('layouts.master')

@section('title', $title ?? __('Invoice'))

@section('content')
    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Payment History'), [
            '#' => _trans('keyword.Orders'),
            'Payment History' => _trans('keyword.Payment History'),
        ]) !!}

        <div class="app-ecommerce-category">
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic hrm_datatable selectable table">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('ID') }}</th>
                                <th scope="col">{{ __('Order ID') }}</th>
                                <th scope="col">{{ __('Payment Date') }}</th>
                                <th scope="col">{{ __('Amount') }}</th>
                                <th scope="col">{{ __('Current Due') }}</th>
                                <th scope="col">{{ __('Payment Method') }}</th>
                                <th scope="col">{{ __('Created At') }}</th>
                                <th scope="col">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $key => $payment)
                                <tr>
                                    <th>{{ $key + 1 }}</th>
                                    <td>{{ $payment->order->code }}</td>
                                    <td>{{ dateFormat($payment->payment_date) }}</td>
                                    <td>{{ getPriceFormat($payment->amount) }}</td>
                                    <td>{{ getPriceFormat($payment->current_due) }}</td>
                                    <td>{{ $payment->paymentMethod->name }}
                                        @if ($payment->tnx_id != null)
                                            <p> TnxID: <strong>{{ $payment->tnx_id }}</strong></p>
                                        @endif
                                    </td>
                                    <td>{{ dateFormatwithTime($payment->created_at) }} </td>
                                    <td>
                                        @if ( $key == 0  && $payment->order->seller_id == getUserId())
                                        <a class="btn btn-outline-danger btn-icon m-1 confirm-delete"
                                           title="Delete" data-id="{{$payment->id}}">
                                            <span class="ul-btn__icon"><i class="ti ti-trash"></i></span>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                    </table>
                    <div class="col-md-12">
                        <div class="center text-center" style="display: table; margin-top: 25px; ">
                            {{ $payments->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection
@push('scripts')

            <script>
                $(document).on("click", ".confirm-delete", function () {

                    let id = $(this).attr("data-id");
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it!',
                        customClass: {
                            confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                            cancelButton: 'btn btn-label-secondary waves-effect waves-light'
                        },
                        buttonsStyling: false
                    }).then(function (result) {
                        if (result.value) {
                            $.ajax({
                                url: '{{ route('order.deletePaymentHistory') }}',
                                method: 'POST',
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                    payment_id: id,
                                },
                                success: function (response) {
                                    if (response.status == 200){
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Deleted!',
                                            text: response.message,
                                            customClass: {
                                                confirmButton: 'btn btn-success waves-effect waves-light'
                                            }
                                        });
                                        location.reload()
                                    }else{
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error!',
                                            text: response.message,
                                            customClass: {
                                                confirmButton: 'btn btn-danger waves-effect waves-light'
                                            }
                                        });
                                    }

                                },
                                error: function (error) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: 'Something went wrong.',
                                        customClass: {
                                            confirmButton: 'btn btn-danger waves-effect waves-light'
                                        }
                                    });
                                    console.error(error.responseJSON.message);
                                },
                            });
                        }
                    });
                });

            </script>

@endpush
