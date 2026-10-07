@extends('layouts.master')

@section('title', $title ?? __('Cart'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb('', [
            '#' => 'Order Management',
            '##' => _trans('keyword.My') . ' ' . _trans('keyword.Order'),
            'cart' => 'My Cart List',
        ]) !!}

        <div class="app-ecommerce-category">
            <!-- Category List Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ _trans('keyword.Cart List') }}</h5>
                </div>
                <div class="card-datatable table-responsive">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{ _trans('keyword.SL') }}</th>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Seller</th>
                            <th>Variant</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($carts as $cart)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ getFilePath($cart->product->thumbnail_img) }}" alt="Product Image"
                                         class="img-fluid" style="height:80px; width:80px">
                                </td>

                                <td>
                                    @if($cart->product->relationLoaded('shop'))
                                        <a href="{{env('APP_FRONTEND_URL').'/designer/'.@$cart->product->shop->slug.'/product/'.@$cart->product->id.'-' .@$cart->product->slug }}"
                                           target="_blank">{{ optional($cart->product)->name }}</a>
                                    @else
                                        <a href="{{env('APP_FRONTEND_URL').'/product/'.@$cart->product->id.'-' .@$cart->product->slug}}"
                                           target="_blank">{{ optional($cart->product)->name }}</a>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex;
                                        max-width: 300px;
                                        flex-direction: column">
                                        <img src="{{ getFilePath(@$cart->seller->shop->logo) }}" alt="Shop Logo"
                                             class="img-fluid" style="height:80px; width:80px">
                                        <span>Name:{{ @$cart->seller->name }}</span>
                                        <span>Shop Name: {{ @$cart->seller->shop->shop_name }} </span>
                                        <span>Location:{{ @$cart->seller->shop->location }}</span>
                                    </div>

                                </td>
                                <td>
                                    @foreach ($cart->variation as $variation)
                                        {{ @$variation['attribute'] }}: {{ @$variation['value'] }} <br>
                                    @endforeach
                                </td>
                                <td>{{ $cart->quantity }}</td>
                                <td>{{ getPriceFormat($cart->price) }}</td>
                                <td>
                                    @if(hasPermission('my_order_cart_list_delete'))
                                        <a href="javascript:0;" class="brn text-danger cartItemDelete"
                                           data-id="{{$cart->id}}"><i class="ti ti-trash"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center">No data found</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).on("click", ".cartItemDelete", function () {

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
                        url: '{{ route('myOrder.cart.destroy') }}',
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            cart_id: id,
                        },
                        success: function (response) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Delete Successfully!',
                                text: 'Your cart item has been deleted.',
                                customClass: {
                                    confirmButton: 'btn btn-success waves-effect waves-light'
                                }
                            }).then(function (result) {
                                if (result.value) {
                                    location.reload('.data-table');
                                }
                            });
                        },
                        error: function (error) {
                            console.log(error.responseJSON.message);
                        }
                    });
                }
            });
        });
    </script>
@endpush
