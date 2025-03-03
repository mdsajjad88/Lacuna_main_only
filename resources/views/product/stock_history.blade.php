@extends('layouts.app')
@section('title', __('lang_v1.product_stock_history'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang('lang_v1.product_stock_history')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        {{-- @php
    dd($priceHistory);
    @endphp --}}
        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['title' => $product->name])
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('product_id', __('sale.product') . ':') !!}
                            {!! Form::select('product_id', [$product->id => $product->name . ' - ' . $product->sku], $product->id, [
                                'class' => 'form-control',
                                'style' => 'width:100%',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                            {!! Form::select('location_id', $business_locations, request()->input('location_id', null), [
                                'class' => 'form-control select2',
                                'style' => 'width:100%',
                            ]) !!}
                        </div>
                    </div>
                    @if ($product->type == 'variable')
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="variation_id">@lang('product.variations'):</label>
                                <select class="select2 form-control" name="variation_id" id="variation_id">
                                    @foreach ($product->variations as $variation)
                                        <option value="{{ $variation->id }}" @if (request()->input('variation_id', null) == $variation->id) selected @endif>
                                            {{ $variation->product_variation->name }} - {{ $variation->name }}
                                            ({{ $variation->sub_sku }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @else
                        <input type="hidden" id="variation_id" name="variation_id"
                            value="{{ $product->variations->first()->id }}">
                    @endif
                @endcomponent
                @component('components.widget')
                    <div id="product_stock_history" style="display: none;"></div>
                @endcomponent
            </div>
        </div>
    </section>

    <div class="row box box-solid">
        <div style="margin-left: 20px" class="col-md-12">
            <h4 style="padding: 7px 0;" class="text-center"><b>Product Price History</b></h4>
            <table class="table table-bordered table-striped box-body" id="price_history_table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Purchase Price</th>
                        <th>Selling Price</th>
                        <th>Updated By</th>
                        <th>Reference No.</th>
                        <th>Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($priceHistory as $history)
                        <tr>
                            <td>{{ $history->h_type }}</td>
                            <td>
                                @can('view_purchase_price')
                                    {!! $history->old_price !!}
                                @endcan
                            </td>
                            <td>{!! $history->new_price !!}</td>
                            <td>{{ \App\User::find($history->updated_by)->first_name }}
                                {{ \App\User::find($history->updated_by)->last_name }}</td>
                            <td>{!! $history->ref_no !!}</td>
                            <td>{{ Carbon::parse($history->updated_at)->format('d-m-Y, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No price history available for this Product.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- /.content class="table table-slim" id="stock_history_table" -->
@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            load_stock_history($('#variation_id').val(), $('#location_id').val());

            $('#product_id').select2({
                ajax: {
                    url: '/products/list-no-variation',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term, // search term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data,
                        };
                    },
                },
                minimumInputLength: 1,
                escapeMarkup: function(m) {
                    return m;
                },
            }).on('select2:select', function(e) {
                var data = e.params.data;
                window.location.href = "{{ url('/') }}/products/stock-history/" + data.id
            });
        });
        $(document).on('change', '#variation_id, #location_id', function() {
            load_stock_history($('#variation_id').val(), $('#location_id').val());
        });
        function load_stock_history(variation_id, location_id) {
            $('#product_stock_history').fadeOut();
            $.ajax({
                url: '/products/stock-history/' + variation_id + "?location_id=" + location_id,
                dataType: 'html',
                success: function(result) {
                    $('#product_stock_history')
                        .html(result)
                        .fadeIn();
                    // Ensure DataTable is properly initialized
                    if ($.fn.DataTable.isDataTable('#stock_history_table')) {
                        $('#stock_history_table').DataTable().destroy();
                    }
                    __currency_convert_recursively($('#product_stock_history'));

                    $('#stock_history_table').DataTable({
                        searching: false,
                        ordering: false
                    });
                },
            });
        }
        $('#price_history_table').DataTable({
            searching: false,
            ordering: false
        });
        
    </script>
@endsection
