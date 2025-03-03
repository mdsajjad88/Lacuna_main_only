@extends('layouts.app')
@section('title', __('sale.products'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('sale.products')
        <small>@lang('lang_v1.manage_products')</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
@component('components.filters', ['title' => __('report.filters')])
<div class="row">
    <div class="col-md-12">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('type[]', __('product.product_type') . ':') !!}
                {!! Form::select('type[]', ['single' => __('lang_v1.single'), 'variable' => __('lang_v1.variable'), 'combo' => __('lang_v1.combo')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_type','multiple' => 'multiple']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('category_id[]', __('product.category') . ':') !!}
                {!! Form::select('category_id[]', $categories, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_category_id', 'multiple' => 'multiple']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('unit_id[]', __('product.unit') . ':') !!}
                {!! Form::select('unit_id[]', $units, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_unit_id', 'multiple' => 'multiple']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('tax_id[]', __('product.tax') . ':') !!}
                {!! Form::select('tax_id[]', $taxes, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_tax_id', 'multiple' => 'multiple']); !!}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('brand_id[]', __('product.brand') . ':') !!}
                {!! Form::select('brand_id[]', $brands, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_brand_id', 'multiple' => 'multiple']); !!}
            </div>
        </div>
        <div class="col-md-3" id="location_filter">
            <div class="form-group">
                {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('active_state', __('Product Status') . ':') !!}
                {!! Form::select('active_state', ['active' => __('business.is_active'), 'inactive' => __('lang_v1.inactive')], request('active_state', 'active'), ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'active_state', 'placeholder' => __('lang_v1.all')]) !!}
            </div>            
        </div>

        <!-- include module filter -->
        @if(!empty($pos_module_data))
            @foreach($pos_module_data as $key => $value)
                @if(!empty($value['view_path']))
                    @includeIf($value['view_path'], ['view_data' => $value['view_data']])
                @endif
            @endforeach
        @endif

        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('selling_state', __('Selling Status') . ':') !!}
                {!! Form::select('selling_state', ['all' => __('lang_v1.all'), '0' => __('For selling'), '1' => __('lang_v1.not_for_selling')], 'all', ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'selling_state']) !!}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        @if($is_woocommerce)
        <div class="col-md-3">
            <div class="form-group">
                <br>
                <label>
                  {!! Form::checkbox('woocommerce_enabled', 1, false, 
                  [ 'class' => 'input-icheck', 'id' => 'woocommerce_enabled']); !!} {{ __('lang_v1.woocommerce_enabled') }}
                </label>
            </div>
        </div>
    @endif
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('stock_status', __('Stock Status') . ':') !!}
                {!! Form::select('stock_status', ['in_stock' => __('In stock'), 'out_of_stock' => __('Out of stock')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'id' => 'product_list_filter_stock_status', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
    </div>
</div>
@endcomponent
@can('product.view')
    <div class="row">
        <div class="col-md-12">
           <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#product_list_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-cubes" aria-hidden="true"></i> @lang('lang_v1.all_products')</a>
                    </li>
                    <li>
                        <a href="#product_sell_tab" data-toggle="tab" aria-expanded="true"><i class="fas fa-calendar-check"></i> Today Sell Details</a>
                    </li>
                    <li>
                        <a href="#product_return_tab" data-toggle="tab" aria-expanded="true"><i class="fas fa-undo-alt"></i> Today Return Products</a>
                    </li>
                    @can('stock_report.view')
                    <li>
                        <a href="#product_stock_report" data-toggle="tab" aria-expanded="true"><i class="fa fa-hourglass-half" aria-hidden="true"></i> @lang('report.stock_report')</a>
                    </li>
                    @endcan
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="product_list_tab">
                        @if($is_admin)
                            <a class="btn btn-success pull-right margin-left-10" href="{{action([\App\Http\Controllers\ProductController::class, 'downloadExcel'])}}"><i class="fa fa-download"></i> @lang('lang_v1.download_excel')</a>
                        @endif
                        @can('product.create')                            
                            <a class="btn btn-primary pull-right" href="{{action([\App\Http\Controllers\ProductController::class, 'create'])}}">
                                        <i class="fa fa-plus"></i> @lang('messages.add')</a>
                            <br><br>
                        @endcan
                        @include('product.partials.product_list')
                    </div>
                    <div class="tab-pane" id="product_sell_tab">
                        @include('product.partials.product_sell_report')
                    </div>
                    <div class="tab-pane" id="product_return_tab">
                        @include('product.partials.sell_return')
                    </div>
                    @can('stock_report.view')
                    <div class="tab-pane" id="product_stock_report">
                        @include('report.partials.stock_report_table')
                    </div>
                  
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endcan
<input type="hidden" id="is_rack_enabled" value="{{$rack_enabled}}">

<div class="modal fade product_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade" id="view_product_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade" id="opening_stock_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
@if($is_woocommerce)
    @include('product.partials.toggle_woocommerce_sync_modal')
@endif
@include('product.partials.edit_product_location_modal')

</section>
<!-- /.content -->

@endsection

@section('javascript')
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(document).ready( function(){
            $('#active_state').val($('#active_state').val() || 'active');
            product_table = $('#product_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[3, 'asc']],
                scrollY:        "75vh",
                scrollX:        true,
                scrollCollapse: true,
                "ajax": {
                    "url": "/products",
                    "data": function ( d ) {
                        d.type = $('#product_list_filter_type').val();
                        d.category_id = $('#product_list_filter_category_id').val();
                        d.brand_id = $('#product_list_filter_brand_id').val();
                        d.unit_id = $('#product_list_filter_unit_id').val();
                        d.tax_id = $('#product_list_filter_tax_id').val();
                        d.active_state = $('#active_state').val();
                        d.selling_state = $('#selling_state').val();
                        d.location_id = $('#location_id').val();
                        d.stock_status = $('#product_list_filter_stock_status').val();
                        if ($('#repair_model_id').length == 1) {
                            d.repair_model_id = $('#repair_model_id').val();
                        }

                        if ($('#woocommerce_enabled').length == 1 && $('#woocommerce_enabled').is(':checked')) {
                            d.woocommerce_enabled = 1;
                        }

                        d = __datatable_ajax_callback(d);
                    }
                },
                columnDefs: [ {
                    "targets": [0, 1, 2],
                    "orderable": false,
                    "searchable": false
                } ],
                columns: [
                        { data: 'mass_delete'  },
                        { data: 'image', name: 'products.image'  },
                        { data: 'action', name: 'action'},
                        { data: 'product', name: 'products.name'  },
                        { data: 'product_locations', name: 'product_locations'  },
                        @can('view_purchase_price')
                            { data: 'purchase_price', name: 'max_purchase_price', searchable: false},
                        @endcan
                        @can('access_default_selling_price')
                            { data: 'selling_price', name: 'max_price', searchable: false},
                        @endcan
                        @can('view_purchase_price')
                            { data: 'profit_margin', name: 'profit_margin', searchable: false},
                        @endcan
                        { data: 'current_stock', searchable: false},
                        { data: 'type', name: 'products.type'},
                        { data: 'category', name: 'c1.name'},
                        { data: 'brand', name: 'brands.name'},
                        { data: 'tax', name: 'tax_rates.name', searchable: false},
                        { data: 'sku', name: 'products.sku'},
                        { data: 'product_custom_field1', name: 'products.product_custom_field1', visible: $('#cf_1').text().length > 0  },
                        { data: 'product_custom_field2', name: 'products.product_custom_field2' , visible: $('#cf_2').text().length > 0},
                        { data: 'product_custom_field3', name: 'products.product_custom_field3', visible: $('#cf_3').text().length > 0},
                        { data: 'product_custom_field4', name: 'products.product_custom_field4', visible: $('#cf_4').text().length > 0 },
                        { data: 'product_custom_field5', name: 'products.product_custom_field5', visible: $('#cf_5').text().length > 0  },
                        { data: 'product_custom_field6', name: 'products.product_custom_field6', visible: $('#cf_6').text().length > 0  },
                        { data: 'product_custom_field7', name: 'products.product_custom_field7', visible: $('#cf_7').text().length > 0  },
                    ],
                    createdRow: function( row, data, dataIndex ) {
                        if($('input#is_rack_enabled').val() == 1){
                            var target_col = 0;
                            @can('product.delete')
                                target_col = 1;
                            @endcan
                            $( row ).find('td:eq('+target_col+') div').prepend('<i style="margin:auto;" class="fa fa-plus-circle text-success cursor-pointer no-print rack-details" title="' + LANG.details + '"></i>&nbsp;&nbsp;');
                        }
                        $( row ).find('td:eq(0)').attr('class', 'selectable_td');
                    },
                    fnDrawCallback: function(oSettings) {
                        __currency_convert_recursively($('#product_table'));
                    },
            });

$(document).ready(function() {
    var product_sell_table = $('#product_sell_table').DataTable({
    processing: true,
    serverSide: true,
    aaSorting: [[4, 'desc']],  // Sort by date
    ajax: {
        url: '/reports/product-sell-grouped-report',
        data: function(d) {
            var today = new Date();
            var day = String(today.getDate()).padStart(2, '0');
            var month = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
            var year = today.getFullYear();
            var todayDate = year + '-' + month + '-' + day;

            d.variation_id = $('#variation_id').val();
            d.customer_id = $('select#customer_id').val();
            d.customer_group_id = $('#psr_customer_group_id').val();
            d.type = $('#product_list_filter_type').val();
            d.category_id = $('#product_list_filter_category_id').val();
            d.brand_id = $('#product_list_filter_brand_id').val();
            d.unit_id = $('#product_list_filter_unit_id').val();
            d.tax_id = $('#product_list_filter_tax_id').val();
            d.active_state = $('#active_state').val();
            d.selling_state = $('#selling_state').val();
            d.location_id = $('#location_id').val();
            d.transaction_date = todayDate; // Add transaction_date filter
            d.stock_status = $('#product_list_filter_stock_status').val();
        },
    },
    columns: [
        { data: 'product_name', name: 'p.name' },
        { data: 'sub_sku', name: 'v.sub_sku' },
        { data: 'category_name', name: 'cat.name' },
        { data: 'brand_name', name: 'b.name' },
        { data: 'current_stock', name: 'current_stock', searchable: false, orderable: false },
        { data: 'total_qty_sold', name: 'total_qty_sold', searchable: false },
        { data: 'subtotal', name: 'subtotal', searchable: false },
    ],
    fnDrawCallback: function(oSettings) {
        let api = this.api();

        // Calculate the total quantity sold
        let totalQtySold = api.column(5, { page: 'current' }).data().reduce(function(a, b) {
            let numericValueB = parseFloat(b.replace(/[^\d.]/g, ''));
            return parseFloat(a) + numericValueB;
        }, 0);

        // Calculate the total sold subtotal
        let totalSubtotal = api.column(6, { page: 'current' }).data().reduce(function(a, b) {
            let numericValueB = parseFloat(b.replace(/[^\d.]/g, ''));
            return parseFloat(a) + numericValueB;
        }, 0);

        // Update the footer with the totals
        $('#footer_today_subtotal').text(totalSubtotal.toFixed(2));

        __currency_convert_recursively($('#product_sell_table'));
    },
    buttons: [
        {
            extend: 'csv',
            text: '<i class="fa fa-file-csv" aria-hidden="true"></i> ' + LANG.export_to_csv,
            className: 'btn-sm',
            exportOptions: {
                columns: ':visible',
            },
            footer: true,
        },
        {
            extend: 'excel',
            text: '<i class="fa fa-file-excel" aria-hidden="true"></i> ' + LANG.export_to_excel,
            className: 'btn-sm',
            exportOptions: {
                columns: ':visible',
            },
            footer: true,
        },
        {
            extend: 'print',
            text: '<i class="fa fa-print" aria-hidden="true"></i> ' + LANG.print,
            className: 'btn-sm',
            exportOptions: {
            columns: ':visible',
            stripHtml: true,
            },
            footer: true,
        },
        {
            extend: 'colvis',
            text: '<i class="fa fa-columns" aria-hidden="true"></i> ' + LANG.col_vis,
            className: 'btn-sm',
        },
        {
            extend: 'pdf',
            text: '<i class="fa fa-file-pdf" aria-hidden="true"></i> ' + LANG.export_to_pdf,
            className: 'btn-sm',
            exportOptions: {
                columns: ':visible',
            },
            footer: true,
        },
        {
            extend: 'print',
            text: '<i class="fa fa-print" aria-hidden="true"></i> ' + 'Custom Print',
            className: 'btn-sm',
            action: function(e, dt, button, config) {
                var oldStart = dt.settings()[0]._iDisplayStart;  // Get the current start position
                dt.page.len(-1).draw().one('draw', function() {
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    dt.page.len(25).draw().one('draw', function() {
                        dt.settings()[0]._iDisplayStart = oldStart;  // Restore the start position
                        dt.draw(false);
                    });
                });
            },
            exportOptions: {
                columns: [0, 2, 3, 4], // Columns: product name, category, brand, total quantity sold, subtotal
                format: {
                    body: function(data, row, column, node) {
                        return data;
                    }
                },
            customize: function (win) {
            if ($('.print_table_part').length > 0) {
                $($('.print_table_part').html()).insertBefore(
                    $(win.document.body).find('table')
                );
            }
            if ($(win.document.body).find('table.hide-footer').length) {
                $(win.document.body).find('table.hide-footer tfoot').remove();
            }
            __currency_convert_recursively($(win.document.body).find('table'));
            },
            },
            customize: function(win) {
                var data = product_sell_table.rows({ search: 'applied' }).data().toArray();
                data.sort(function(a, b) {
                    if (a.category_name < b.category_name) return -1;
                    if (a.category_name > b.category_name) return 1;
                    if (a.brand_name < b.brand_name) return -1;
                    if (a.brand_name > b.brand_name) return 1;
                    return 0;
                });

                var body = $(win.document.body).find('table tbody');
                body.empty();
                data.forEach(function(row) {
                    var tr = $('<tr></tr>');
                    var categoryName = row.category_name.length > 8 
                        ? row.category_name.substring(0, 6) + '..' + row.category_name.slice(-2) 
                        : row.category_name;
                    var brandName = row.brand_name.length > 6 ? row.brand_name.substring(0, 6) + '..' : row.brand_name;
                    tr.append('<td style="padding: 2px; margin: 2px;">' + row.product_name + '</td>');
                    tr.append('<td style="padding: 2px; margin: 2px;">' + categoryName + '</td>');
                    tr.append('<td style="padding: 2px; margin: 2px;">' + brandName + '</td>');
                    tr.append('<td style="padding: 2px; margin: 2px;">' + row.current_stock + '</td>');
                    body.append(tr);
                });
                $(win.document.body).find('table')
                    .addClass('compact')
                    .css({
                        'font-size': '10px',
                        'margin-left': '0.5px',
                        'margin-right': '3px',
                        'padding-left': '0px',
                        'padding-right': '2px'
                    });
                $(win.document.body).css({
                        'margin-left': '0.5px',
                        'margin-right': '3px',
                        'padding-left': '0px',
                        'padding-right': '2px'
                    });
                
                $(win.document.body).find('table').parent()
                    .css({
                        'margin-left': '2px',
                        'margin-right': '3px',
                        'padding-left': '2px',
                        'padding-right': '3px'
                    });
                
                // Hide default title if present
                $(win.document.body).find('h1').first().hide();

                // Insert custom title
                var customTitle = '<h1 style="font-size: 16px; text-align: center;margin-top:0px;padding-top:0px;">AWC Today sell details</h1>';
                $(win.document.body).prepend(customTitle);
            }
        }
    ]
});

    // Trigger DataTable reload on filter change
    $('#product_list_filter_category_id, #product_list_filter_brand_id, #product_list_filter_type, #product_list_filter_unit_id, #product_list_filter_tax_id, #active_state, #location_id, #product_list_filter_stock_status, #selling_state').change(function() {
        product_sell_table.draw();
    });
});


$(document).ready(function() {
    var sell_return_table = $('#sell_return_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        "ajax": {
            "url": "/today-sell-return",
            data: function(d) {
                var today = new Date();
                var day = String(today.getDate()).padStart(2, '0');
                var month = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
                var year = today.getFullYear();
                var todayDate = year + '-' + month + '-' + day;

                d.variation_id = $('#variation_id').val();
                d.customer_id = $('select#customer_id').val();
                d.customer_group_id = $('#psr_customer_group_id').val();
                d.type = $('#product_list_filter_type').val();
                d.category_id = $('#product_list_filter_category_id').val();
                d.brand_id = $('#product_list_filter_brand_id').val();
                d.unit_id = $('#product_list_filter_unit_id').val();
                d.tax_id = $('#product_list_filter_tax_id').val();
                d.active_state = $('#active_state').val();
                d.selling_state = $('#selling_state').val();
                d.location_id = $('#location_id').val();
                d.transaction_date = todayDate; // Add transaction_date filter
                d.stock_status = $('#product_list_filter_stock_status').val();
            },
        },
        columnDefs: [{
            "targets": [6, 7],
            "orderable": false,
            "searchable": false
        }],
        columns: [
            { data: 'product', name: 'product' },
            { data: 'sku', name: 'sku' },
            { data: 'category', name: 'category' },
            { data: 'brand', name: 'brand' },
            { data: 'parent_sale', name: 'T1.invoice_no' },
            { data: 'payment_status', name: 'payment_status' },
            { data: 'current_stock', name: 'current_stock' },
            { data: 'total_return_qty', name: 'total_return_qty' },
            { data: 'final_total', name: 'final_total' }
        ],
        "fnDrawCallback": function(oSettings) {
            var total_sell = sum_table_col($('#sell_return_table'), 'final_total');
            $('#footer_sell_return_total').text(total_sell);

            $('#footer_payment_status_count_sr').html(__sum_status_html($('#sell_return_table'), 'payment-status-label'));

            var total_due = sum_table_col($('#sell_return_table'), 'payment_due');
            $('#footer_total_due_sr').text(total_due);

            __currency_convert_recursively($('#sell_return_table'));
        },
        createdRow: function(row, data, dataIndex) {
            $(row).find('td:eq(2)').attr('class', 'clickable_td');
        }
    });
    // Trigger DataTable reload on filter change
    $('#product_list_filter_category_id, #product_list_filter_brand_id, #product_list_filter_type, #product_list_filter_unit_id, #product_list_filter_tax_id, #active_state, #location_id, #product_list_filter_stock_status, #selling_state').change(function() {
        sell_return_table.draw();
    });
});

    $(document).on('click', 'a.delete_sell_return', function(e) {
        e.preventDefault();
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).attr('href');
                var data = $(this).serialize();

                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            sell_return_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });

// Array to track the ids of the details displayed rows
            var detailRows = [];

            $('#product_table tbody').on( 'click', 'tr i.rack-details', function () {
                var i = $(this);
                var tr = $(this).closest('tr');
                var row = product_table.row( tr );
                var idx = $.inArray( tr.attr('id'), detailRows );

                if ( row.child.isShown() ) {
                    i.addClass( 'fa-plus-circle text-success' );
                    i.removeClass( 'fa-minus-circle text-danger' );

                    row.child.hide();
         
                    // Remove from the 'open' array
                    detailRows.splice( idx, 1 );
                } else {
                    i.removeClass( 'fa-plus-circle text-success' );
                    i.addClass( 'fa-minus-circle text-danger' );

                    row.child( get_product_details( row.data() ) ).show();
         
                    // Add to the 'open' array
                    if ( idx === -1 ) {
                        detailRows.push( tr.attr('id') );
                    }
                }
            });

            $('#opening_stock_modal').on('hidden.bs.modal', function(e) {
                product_table.ajax.reload();
                product_sell_table.ajax.reload();
                sell_return_table.ajax.reload();
            });

            $('table#product_table tbody').on('click', 'a.delete-product', function(e){
                e.preventDefault();
                swal({
                  title: LANG.sure,
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var href = $(this).attr('href');
                        $.ajax({
                            method: "DELETE",
                            url: href,
                            dataType: "json",
                            success: function(result){
                                if(result.success == true){
                                    toastr.success(result.msg);
                                    product_table.ajax.reload();
                                    product_sell_table.ajax.reload();
                                    sell_return_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });

            $(document).on('click', '#delete-selected', function(e){
                e.preventDefault();
                var selected_rows = getSelectedRows();
                
                if(selected_rows.length > 0){
                    $('input#selected_rows').val(selected_rows);
                    swal({
                        title: LANG.sure,
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('form#mass_delete_form').submit();
                        }
                    });
                } else{
                    $('input#selected_rows').val('');
                    swal('@lang("lang_v1.no_row_selected")');
                }    
            });

            $(document).on('click', '#deactivate-selected', function(e){
                e.preventDefault();
                var selected_rows = getSelectedRows();
                
                if(selected_rows.length > 0){
                    $('input#selected_products').val(selected_rows);
                    swal({
                        title: LANG.sure,
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            var form = $('form#mass_deactivate_form')

                            var data = form.serialize();
                                $.ajax({
                                    method: form.attr('method'),
                                    url: form.attr('action'),
                                    dataType: 'json',
                                    data: data,
                                    success: function(result) {
                                        if (result.success == true) {
                                            toastr.success(result.msg);
                                            product_table.ajax.reload();
                                            product_sell_table.ajax.reload();
                                            sell_return_table.ajax.reload();
                                            form
                                            .find('#selected_products')
                                            .val('');
                                        } else {
                                            toastr.error(result.msg);
                                        }
                                    },
                                });
                        }
                    });
                } else{
                    $('input#selected_products').val('');
                    swal('@lang("lang_v1.no_row_selected")');
                }    
            })

            $(document).on('click', '#edit-selected', function(e){
                e.preventDefault();
                var selected_rows = getSelectedRows();
                
                if(selected_rows.length > 0){
                    $('input#selected_products_for_edit').val(selected_rows);
                    $('form#bulk_edit_form').submit();
                } else{
                    $('input#selected_products').val('');
                    swal('@lang("lang_v1.no_row_selected")');
                }    
            })

            $('table#product_table tbody').on('click', 'a.activate-product', function(e){
                e.preventDefault();
                var href = $(this).attr('href');
                $.ajax({
                    method: "get",
                    url: href,
                    dataType: "json",
                    success: function(result){
                        if(result.success == true){
                            toastr.success(result.msg);
                            product_table.ajax.reload();
                            product_sell_table.ajax.reload();
                            sell_return_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });

            $(document).on('change', '#product_list_filter_type, #product_list_filter_category_id, #product_list_filter_brand_id, #product_list_filter_unit_id, #product_list_filter_tax_id, #product_list_filter_stock_status, #location_id, #active_state, #repair_model_id, #selling_state', 
                function() {
                    if ($("#product_list_tab").hasClass('active')) {
                        product_table.ajax.reload();
                    }
                    if ($("#product_stock_report").hasClass('active')) {
                        stock_report_table.ajax.reload();
                    }
                    if ($("#product_sell_tab").hasClass('active')) {
                        product_sell_table.ajax.reload();
                    }
                    if ($("#product_return_tab").hasClass('active')) {
                        sell_return_table.ajax.reload();
                    }
            });

            $(document).on('ifChanged', '#not_for_selling, #woocommerce_enabled', function(){
                if ($("#product_list_tab").hasClass('active')) {
                    product_table.ajax.reload();
                }

                if ($("#product_stock_report").hasClass('active')) {
                    stock_report_table.ajax.reload();
                }
                if ($("#product_sell_tab").hasClass('active')) {
                        product_sell_table.ajax.reload();
                }
                if ($("#product_return_tab").hasClass('active')) {
                        sell_return_table.ajax.reload();
                }
            });

            $('#product_location').select2({dropdownParent: $('#product_location').closest('.modal')});

            @if($is_woocommerce)
                $(document).on('click', '.toggle_woocomerce_sync', function(e){
                    e.preventDefault();
                    var selected_rows = getSelectedRows();
                    if(selected_rows.length > 0){
                        $('#woocommerce_sync_modal').modal('show');
                        $("input#woocommerce_products_sync").val(selected_rows);
                    } else{
                        $('input#selected_products').val('');
                        swal('@lang("lang_v1.no_row_selected")');
                    }    
                });

                $(document).on('submit', 'form#toggle_woocommerce_sync_form', function(e){
                    e.preventDefault();
                    var url = $('form#toggle_woocommerce_sync_form').attr('action');
                    var method = $('form#toggle_woocommerce_sync_form').attr('method');
                    var data = $('form#toggle_woocommerce_sync_form').serialize();
                    var ladda = Ladda.create(document.querySelector('.ladda-button'));
                    ladda.start();
                    $.ajax({
                        method: method,
                        dataType: "json",
                        url: url,
                        data:data,
                        success: function(result){
                            ladda.stop();
                            if (result.success) {
                                $("input#woocommerce_products_sync").val('');
                                $('#woocommerce_sync_modal').modal('hide');
                                toastr.success(result.msg);
                                product_table.ajax.reload();
                                product_sell_table.ajax.reload();
                                sell_return_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                });
            @endif
        });

        $(document).on('shown.bs.modal', 'div.view_product_modal, div.view_modal, #view_product_modal', 
            function(){
                var div = $(this).find('#view_product_stock_details');
            if (div.length) {
                $.ajax({
                    url: "{{action([\App\Http\Controllers\ReportController::class, 'getStockReport'])}}"  + '?for=view_product&product_id=' + div.data('product_id'),
                    dataType: 'html',
                    success: function(result) {
                        div.html(result);
                        __currency_convert_recursively(div);
                    },
                });
            }
            __currency_convert_recursively($(this));
        });
        var data_table_initailized = false;
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if ($(e.target).attr('href') == '#product_stock_report') {
                if (!data_table_initailized) {
                    //Stock report table
                    var stock_report_cols = [
                        { data: 'action', name: 'action', searchable: false, orderable: false },
                        { data: 'sku', name: 'variations.sub_sku' },
                        { data: 'product', name: 'p.name' },
                        { data: 'variation', name: 'variation' },
                        { data: 'category_name', name: 'c.name' },
                        { data: 'location_name', name: 'l.name' },
                        { data: 'unit_price', name: 'variations.sell_price_inc_tax' },
                        { data: 'stock', name: 'stock', searchable: false },
                    ];
                    if ($('th.stock_price').length) {
                        stock_report_cols.push({ data: 'stock_price', name: 'stock_price', searchable: false });
                        stock_report_cols.push({ data: 'stock_value_by_sale_price', name: 'stock_value_by_sale_price', searchable: false, orderable: false });
                        stock_report_cols.push({ data: 'potential_profit', name: 'potential_profit', searchable: false, orderable: false });
                    }

                    stock_report_cols.push({ data: 'total_sold', name: 'total_sold', searchable: false });
                    stock_report_cols.push({ data: 'total_transfered', name: 'total_transfered', searchable: false });
                    stock_report_cols.push({ data: 'total_adjusted', name: 'total_adjusted', searchable: false });
                    stock_report_cols.push({ data: 'product_custom_field1', name: 'p.product_custom_field1'});
                    stock_report_cols.push({ data: 'product_custom_field2', name: 'p.product_custom_field2'});
                    stock_report_cols.push({ data: 'product_custom_field3', name: 'p.product_custom_field3'});
                    stock_report_cols.push({ data: 'product_custom_field4', name: 'p.product_custom_field4'});

                    if ($('th.current_stock_mfg').length) {
                        stock_report_cols.push({ data: 'total_mfg_stock', name: 'total_mfg_stock', searchable: false });
                    }
                    stock_report_table = $('#stock_report_table').DataTable({
                        order: [[1, 'asc']],
                        processing: true,
                        serverSide: true,
                        scrollY: "75vh",
                        scrollX:        true,
                        scrollCollapse: true,
                        ajax: {
                            url: '/reports/stock-report',
                            data: function(d) {
                                d.location_id = $('#location_id').val();
                                d.category_id = $('#product_list_filter_category_id').val();
                                d.brand_id = $('#product_list_filter_brand_id').val();
                                d.unit_id = $('#product_list_filter_unit_id').val();
                                d.type = $('#product_list_filter_type').val();
                                d.active_state = $('#active_state').val();
                                d.selling_state = $('#selling_state').val();
                                if ($('#repair_model_id').length == 1) {
                                    d.repair_model_id = $('#repair_model_id').val();
                                }
                            }
                        },
                        columns: stock_report_cols,
                        fnDrawCallback: function(oSettings) {
                            __currency_convert_recursively($('#stock_report_table'));
                        },
                        "footerCallback": function ( row, data, start, end, display ) {
                            var footer_total_stock = 0;
                            var footer_total_sold = 0;
                            var footer_total_transfered = 0;
                            var total_adjusted = 0;
                            var total_stock_price = 0;
                            var footer_stock_value_by_sale_price = 0;
                            var total_potential_profit = 0;
                            var footer_total_mfg_stock = 0;
                            for (var r in data){
                                footer_total_stock += $(data[r].stock).data('orig-value') ? 
                                parseFloat($(data[r].stock).data('orig-value')) : 0;

                                footer_total_sold += $(data[r].total_sold).data('orig-value') ? 
                                parseFloat($(data[r].total_sold).data('orig-value')) : 0;

                                footer_total_transfered += $(data[r].total_transfered).data('orig-value') ? 
                                parseFloat($(data[r].total_transfered).data('orig-value')) : 0;

                                total_adjusted += $(data[r].total_adjusted).data('orig-value') ? 
                                parseFloat($(data[r].total_adjusted).data('orig-value')) : 0;

                                total_stock_price += $(data[r].stock_price).data('orig-value') ? 
                                parseFloat($(data[r].stock_price).data('orig-value')) : 0;

                                footer_stock_value_by_sale_price += $(data[r].stock_value_by_sale_price).data('orig-value') ? 
                                parseFloat($(data[r].stock_value_by_sale_price).data('orig-value')) : 0;

                                total_potential_profit += $(data[r].potential_profit).data('orig-value') ? 
                                parseFloat($(data[r].potential_profit).data('orig-value')) : 0;

                                footer_total_mfg_stock += $(data[r].total_mfg_stock).data('orig-value') ? 
                                parseFloat($(data[r].total_mfg_stock).data('orig-value')) : 0;
                            }

                            $('.footer_total_stock').html(__currency_trans_from_en(footer_total_stock, false));
                            $('.footer_total_stock_price').html(__currency_trans_from_en(total_stock_price));
                            $('.footer_total_sold').html(__currency_trans_from_en(footer_total_sold, false));
                            $('.footer_total_transfered').html(__currency_trans_from_en(footer_total_transfered, false));
                            $('.footer_total_adjusted').html(__currency_trans_from_en(total_adjusted, false));
                            $('.footer_stock_value_by_sale_price').html(__currency_trans_from_en(footer_stock_value_by_sale_price));
                            $('.footer_potential_profit').html(__currency_trans_from_en(total_potential_profit));
                            if ($('th.current_stock_mfg').length) {
                                $('.footer_total_mfg_stock').html(__currency_trans_from_en(footer_total_mfg_stock, false));
                            }
                        },
                                    });
                    data_table_initailized = true;
                } else {
                    stock_report_table.ajax.reload();
                }
            } else {
                product_table.ajax.reload();
                product_sell_table.ajax.reload();
                sell_return_table.ajax.reload();
            }
        });

        $(document).on('click', '.update_product_location', function(e){
            e.preventDefault();
            var selected_rows = getSelectedRows();
            
            if(selected_rows.length > 0){
                $('input#selected_products').val(selected_rows);
                var type = $(this).data('type');
                var modal = $('#edit_product_location_modal');
                if(type == 'add') {
                    modal.find('.remove_from_location_title').addClass('hide');
                    modal.find('.add_to_location_title').removeClass('hide');
                } else if(type == 'remove') {
                    modal.find('.add_to_location_title').addClass('hide');
                    modal.find('.remove_from_location_title').removeClass('hide');
                }

                modal.modal('show');
                modal.find('#product_location').select2({ dropdownParent: modal });
                modal.find('#product_location').val('').change();
                modal.find('#update_type').val(type);
                modal.find('#products_to_update_location').val(selected_rows);
            } else{
                $('input#selected_products').val('');
                swal('@lang("lang_v1.no_row_selected")');
            }    
        });

    $(document).on('submit', 'form#edit_product_location_form', function(e) {
        e.preventDefault();
        var form = $(this);
        var data = form.serialize();

        $.ajax({
            method: $(this).attr('method'),
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            beforeSend: function(xhr) {
                __disable_submit_button(form.find('button[type="submit"]'));
            },
            success: function(result) {
                if (result.success == true) {
                    $('div#edit_product_location_modal').modal('hide');
                    toastr.success(result.msg);
                    product_table.ajax.reload();
                    product_sell_table.ajax.reload();
                    $('form#edit_product_location_form')
                    .find('button[type="submit"]')
                    .attr('disabled', false);
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
    </script>
@endsection