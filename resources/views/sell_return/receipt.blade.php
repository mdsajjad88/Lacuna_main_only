@if ($receipt_details->transaction_type == 'sell_return')

    <style>
        @media print {
            @page {
                margin: 0 2px;
            }

            body {
                margin: 0 2px;
                /* Adjust or remove as needed */
            }
        }

        .watermark-container {
            position: relative;
            width: 100%;
            height: fit-content;
            /* Full viewport height */
            overflow: hidden;
        }

        .watermark {
            position: absolute;
            bottom: 60%;
            left: 20%;
            font-size: 50px;
            color: rgba(80, 74, 74, 0.245) !important;
            /* Adjust the opacity as needed */
            transform: rotate(-45deg);
            transform-origin: bottom right;
            white-space: nowrap;
            z-index: 10;
            /* Prevent text from wrapping */
        }

        .border-bottom-dotted {
            border-bottom: 1px dotted darkgray;
        }

        .border-top {
            border-top: 1px solid #242424;
        }

        .border-bottom {
            border-bottom: 1px solid #242424;
        }

        .flex-box {
            display: flex;
            width: 100%;
        }

        .flex-box p {
            width: 50%;
            margin-bottom: 0px;
            white-space: nowrap;
        }

        .sub-headings {
            font-size: 15px !important;
            font-weight: 700 !important;
        }
    </style>
    <div style="width: 100%; margin-top:0px; font-size: 12px;">

        @if (!empty($receipt_details->header_text))
            <div style="text-align: center;">
                {!! $receipt_details->header_text !!}
            </div>
        @endif

        <div style="margin: 10px 0;">
            @if (!empty($receipt_details->logo))
                <div style="text-align: center;">
                    <img src="{{ $receipt_details->logo }}" style="max-width: 80%; height: auto;"><br>
                </div>
            @endif

            @if (!empty($receipt_details->display_name))
                <p style="text-align: center;">
                    {{ $receipt_details->display_name }}<br>
                    {!! $receipt_details->address !!}

                    @if (!empty($receipt_details->contact))
                        <br>{{ $receipt_details->contact }}
                    @endif

                    @if (!empty($receipt_details->website))
                        <br>{{ $receipt_details->website }}
                    @endif

                    @if (!empty($receipt_details->tax_info1))
                        <br>{{ $receipt_details->tax_label1 }} {{ $receipt_details->tax_info1 }}
                    @endif

                    @if (!empty($receipt_details->tax_info2))
                        <br>{{ $receipt_details->tax_label2 }} {{ $receipt_details->tax_info2 }}
                    @endif

                    @if (!empty($receipt_details->location_custom_fields))
                        <br>{{ $receipt_details->location_custom_fields }}
                    @endif
                </p>
            @endif
        </div>
        <div>
            @if (!empty($receipt_details->table_label) || !empty($receipt_details->table))
                <p>
                    @if (!empty($receipt_details->table_label))
                        {!! $receipt_details->table_label !!}
                    @endif
                    {{ $receipt_details->table }}
                </p>
            @endif

            @if (!empty($receipt_details->waiter_label) || !empty($receipt_details->waiter))
                <p>
                    @if (!empty($receipt_details->waiter_label))
                        {!! $receipt_details->waiter_label !!}
                    @endif
                    {{ $receipt_details->waiter }}
                </p>
            @endif
        </div>
        <div style="margin: 5px auto; width:fit-content; font-size:15px;">
            <div class="">
                <b>Return With</b>
                @if ($receipt_details->sell_method == 'custom_pay_1')
                    <b>BKASH</b>
                @elseif($receipt_details->sell_method == 'card')
                    <b>CARD</b>
                @elseif($receipt_details->sell_method == 'cash')
                    <b>CASH</b>
                @endif
            </div>
        </div>
        <div class="border-top textbox-info">
            <p class="f-left"><strong>{!! $receipt_details->invoice_no_prefix !!} Invoice No:{{ $receipt_details->invoice_no }}</strong>
            </p>
            <p class="f-right">
                <strong>{{ $receipt_details->customer_label ?? '' }}</strong>
            </p>
        </div>
        <div class="textbox-info">
            <p class="f-left"><strong>{!! $receipt_details->date_label !!} : {{ $receipt_details->invoice_date }}</strong> <br>
                <strong>Counter:</strong> {{ Auth::user()->username }}
            </p>

            <p style="text-align: right" class="f-right">
                @if (!empty($receipt_details->customer_info))
                    {!! $receipt_details->customer_info !!}
                @endif
            </p>
        </div>


        @if (!empty($receipt_details->sales_person_label))
            <div class="textbox-info">
                <p class="f-left"><strong>{{ $receipt_details->sales_person_label }}</strong></p>

                <p class="f-right">{{ $receipt_details->sales_person }}</p>
            </div>
        @endif
        <!-- Waiter info -->
        @if (!empty($receipt_details->service_staff_label) || !empty($receipt_details->service_staff))
            <div class="textbox-info">
                <p class="f-left"><strong>
                        {!! $receipt_details->service_staff_label !!}
                    </strong></p>
                <p class="f-right">
                    {{ $receipt_details->service_staff }}
                </p>
            </div>
        @endif

        @if (!empty($receipt_details->table_label) || !empty($receipt_details->table))
            <div class="textbox-info">
                <p class="f-left"><strong>
                        @if (!empty($receipt_details->table_label))
                            <b>{!! $receipt_details->table_label !!}</b>
                        @endif
                    </strong></p>
                <p class="f-right">
                    {{ $receipt_details->table }}
                </p>
            </div>
        @endif

        @if (!empty($receipt_details->sell_custom_field_1_value))
            <div class="textbox-info">
                <p class="f-left"><strong>{!! $receipt_details->sell_custom_field_1_label !!}</strong></p>
                <p class="f-right">
                    {{ $receipt_details->sell_custom_field_1_value }}
                </p>
            </div>
        @endif
        @if (!empty($receipt_details->sell_custom_field_2_value))
            <div class="textbox-info">
                <p class="f-left"><strong>{!! $receipt_details->sell_custom_field_2_label !!}</strong></p>
                <p class="f-right">
                    {{ $receipt_details->sell_custom_field_2_value }}
                </p>
            </div>
        @endif
        @if (!empty($receipt_details->sell_custom_field_3_value))
            <div class="textbox-info">
                <p class="f-left"><strong>{!! $receipt_details->sell_custom_field_3_label !!}</strong></p>
                <p class="f-right">
                    {{ $receipt_details->sell_custom_field_3_value }}
                </p>
            </div>
        @endif
        @if (!empty($receipt_details->sell_custom_field_4_value))
            <div class="textbox-info">
                <p class="f-left"><strong>{!! $receipt_details->sell_custom_field_4_label !!}</strong></p>
                <p class="f-right">
                    {{ $receipt_details->sell_custom_field_4_value }}
                </p>
            </div>
        @endif
        <div class="border-bottom">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <td style="width: 5% !important">
                            #</td>

                        @php
                            $p_width = 35;
                        @endphp
                        @if ($receipt_details->show_cat_code != 1)
                            @php
                                $p_width = 45;
                            @endphp
                        @endif
                        <td style="text-align: center; width: {{ $p_width }}% !important">
                            {{ $receipt_details->table_product_label }}
                        </td>

                        @if ($receipt_details->show_cat_code == 1)
                            <td style=" width: 10% !important">
                                {{ $receipt_details->cat_code_label }}</td>
                        @endif

                        <td style=" width:
                        15% !important">
                            {{ $receipt_details->table_qty_label }}
                        </td>
                        <td style=" width: 15% !important">
                            {{ $receipt_details->table_unit_price_label }}
                        </td>
                        <td style="width: 20% !important">
                            {{ $receipt_details->table_subtotal_label }}
                        </td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($receipt_details->lines as $line)
                        <tr>
                            <td class="text-center">
                                {{ $loop->iteration }}
                            </td>
                            <td>
                                {{ $line['name'] }} {{ $line['variation'] }}
                                @if (!empty($line['sub_sku']))
                                    , {{ $line['sub_sku'] }}
                                    @endif @if (!empty($line['brand']))
                                        , {{ $line['brand'] }}
                                    @endif
                                    @if (!empty($line['sell_line_note']))
                                        ({{ $line['sell_line_note'] }})
                                    @endif
                            </td>

                            @if ($receipt_details->show_cat_code == 1)
                                <td>
                                    @if (!empty($line['cat_code']))
                                        {{ $line['cat_code'] }}
                                    @endif
                                </td>
                            @endif

                            <td class="">
                                {{ number_format($line['quantity']) }}
                            </td>
                            <td class="">
                                {{ number_format($line['unit_price_exc_tax']) }}
                            </td>
                            <td class="">
                                {{ number_format($line['line_total']) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="width: 100%; max-width: 100%;">
            <div class="flex-box">
                <p class="width-70 left text-right sub-headings">
                    {!! $receipt_details->subtotal_label !!}
                </p>
                <p class="width-30 text-right sub-headings">
                    ৳ {{ number_format($receipt_details->subtotal) }}
                </p>
            </div>
            <!-- Discount -->
            @if (!empty($receipt_details->discount))
                <div class="flex-box">
                    <p class="width-70 text-right">
                        {!! $receipt_details->discount_label !!}
                    </p>

                    <p class="width-30 text-right">
                        (-) {{ number_format($receipt_details->discount) }}
                    </p>
                </div>
            @endif
            <div class="flex-box">
                <p class="width-70 text-right sub-headings">
                    {!! $receipt_details->total_label !!}
                </p>
                <p class="width-30 text-right sub-headings">
                    ৳ {{ number_format($receipt_details->total) }}
                </p>
            </div>
        </div>

        @if (!empty($receipt_details->additional_notes))
            <div>
                {{ $receipt_details->additional_notes }}
            </div>
        @endif

        @if ($receipt_details->show_barcode)
            <div style="margin:0 auto; width:90%;">
                <img
                    src="data:image/png;base64,{{ DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2, 30, [39, 48, 54], true) }}">
            </div>
        @endif

        @if (!empty($receipt_details->footer_text))
            <div style="text-align: center;">
                {!! $receipt_details->footer_text !!}
            </div>
        @endif
    </div>
@else
<style type="text/css">
    .f-8 {
        font-size: 8px !important;
    }

    body {
        color: #000000;
    }

    @media print {
        * {
            font-size: 12px;
            font-family: 'Times New Roman';
            word-break: break-all;
        }

        .f-8 {
            font-size: 8px !important;
        }

        .headings {
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .sub-headings {
            font-size: 15px !important;
            font-weight: 700 !important;
        }

        .border-top {
            border-top: 1px solid #242424;
        }

        .border-bottom {
            border-bottom: 1px solid #242424;
        }

        .border-bottom-dotted {
            border-bottom: 1px dotted darkgray;
        }

        td.serial_number,
        th.serial_number {
            width: 5%;
            max-width: 5%;
        }

        td.description,
        th.description {
            width: 35%;
            max-width: 35%;
        }

        td.quantity,
        th.quantity {
            width: 15%;
            max-width: 15%;
            word-break: break-all;
        }

        td.unit_price,
        th.unit_price {
            width: 25%;
            max-width: 25%;
            word-break: break-all;
        }

        td.price,
        th.price {
            width: 20%;
            max-width: 20%;
            word-break: break-all;
        }

        .centered {
            text-align: center;
            align-content: center;
        }

        .ticket {
            width: 100%;
            max-width: 100%;
        }

        img {
            max-width: inherit;
            width: auto;
        }

        .hidden-print,
        .hidden-print * {
            display: none !important;
        }
    }

    .table-info {
        width: 100%;
    }

    .table-info tr:first-child td,
    .table-info tr:first-child th {
        padding-top: 8px;
    }

    .table-info th {
        text-align: left;
    }

    .table-info td {
        text-align: right;
    }

    .logo {
        float: left;
        width: 35%;
        padding: 10px;
    }

    .text-with-image {
        float: left;
        width: 65%;
    }

    .text-box {
        width: 100%;
        height: auto;
    }

    .textbox-info {
        clear: both;
    }

    .textbox-info p {
        margin-bottom: 0px
    }

    .flex-box {
        display: flex;
        width: 100%;
    }

    .flex-box p {
        width: 50%;
        margin-bottom: 0px;
        white-space: nowrap;
    }

    .table-f-12 th,
    .table-f-12 td {
        font-size: 12px;
        word-break: break-word;
    }

    .bw {
        word-break: break-word;
    }
    @media print {
            @page {
                margin: 2px;
            }

            body {
                margin: 2px;
                /* Adjust or remove as needed */
            }
        }

        .watermark-container {
            position: relative;
            width: 100%;
            height: fit-content;
            overflow: hidden;
        }

        .content {
            width: 500px !important;
            margin: 0 auto;
        }

        .watermark {
            position: absolute;
            bottom: 60%;
            left: 20%;
            font-size: 50px;
            color: rgba(80, 74, 74, 0.245) !important;
            /* Adjust the opacity as needed */
            transform: rotate(-45deg);
            transform-origin: bottom right;
            white-space: nowrap;
            z-index: 10;
            /* Prevent text from wrapping */
        }
        .watermark_due {
            position: absolute;
            bottom: 60%;
            left: 25%;
            font-size: 80px;
            color: rgba(80, 74, 74, 0.318) !important;
            /* Adjust the opacity as needed */
            transform: rotate(-45deg);
            transform-origin: bottom right;
            white-space: nowrap;
            z-index: 10;
            /* Prevent text from wrapping */
        }
</style>
    <div class="watermark-container">
        <div class="ticket">
            @if (empty($receipt_details->letter_head))
                @if (!empty($receipt_details->logo))
                    <div class="text-box centered">
                        <img style="max-height: 100px; width: auto;" src="{{ $receipt_details->logo }}" alt="Logo">
                    </div>
                @endif
                <div class="text-box">
                    <!-- Logo -->
                    <p class="centered">
                        <!-- Header text -->
                        @if (!empty($receipt_details->header_text))
                            <span class="headings">{!! $receipt_details->header_text !!}</span>
                            <br />
                        @endif

                        <!-- business information here -->
                        @if (!empty($receipt_details->display_name))
                            <span class="headings">
                                {{ $receipt_details->display_name }}
                            </span>
                            <br />
                        @endif

                        @if (!empty($receipt_details->address))
                            {!! $receipt_details->address !!}
                            <br />
                        @endif

                        @if (!empty($receipt_details->contact))
                            {!! $receipt_details->contact !!}
                        @endif
                        @if (!empty($receipt_details->contact) && !empty($receipt_details->website))
                            ,
                        @endif
                        @if (!empty($receipt_details->website))
                            {{ $receipt_details->website }}
                        @endif
                        @if (!empty($receipt_details->location_custom_fields))
                            <br>{{ $receipt_details->location_custom_fields }}
                        @endif

                        @if (!empty($receipt_details->sub_heading_line1))
                            {{ $receipt_details->sub_heading_line1 }}<br />
                        @endif
                        @if (!empty($receipt_details->sub_heading_line2))
                            {{ $receipt_details->sub_heading_line2 }}<br />
                        @endif
                        @if (!empty($receipt_details->sub_heading_line3))
                            {{ $receipt_details->sub_heading_line3 }}<br />
                        @endif
                        @if (!empty($receipt_details->sub_heading_line4))
                            {{ $receipt_details->sub_heading_line4 }}<br />
                        @endif
                        @if (!empty($receipt_details->sub_heading_line5))
                            {{ $receipt_details->sub_heading_line5 }}<br />
                        @endif

                        @if (!empty($receipt_details->tax_info1))
                            <br><b>{{ $receipt_details->tax_label1 }}</b> {{ $receipt_details->tax_info1 }}
                        @endif

                        @if (!empty($receipt_details->tax_info2))
                            <b>{{ $receipt_details->tax_label2 }}</b> {{ $receipt_details->tax_info2 }}
                        @endif
            @endif
            <br>
            <!-- Title of receipt -->
            @if (!empty($receipt_details->invoice_heading))
                <br /><span class="sub-headings">{!! $receipt_details->invoice_heading !!}</span>
            @endif
            </p>
        </div>
        @if (!empty($receipt_details->letter_head))
            <div class="text-box">
                <img style="width: 100%;margin-bottom: 10px;" src="{{ $receipt_details->letter_head }}">
            </div>
        @endif

        @if (!empty($receipt_details->status))
            <div class="watermark">
                {{ $receipt_details->status }}
            </div>
        @endif
        {{-- <div class="textbox-info">
        <p style="vertical-align: top;"><strong>
                {{ $receipt_details->customer_label ?? '' }}
            </strong></p>

        <p>
            @if (!empty($receipt_details->customer_info))
                <div class="bw">
                    {!! $receipt_details->customer_info !!}
                </div>
            @endif
        </p>
    </div> --}}
        <div class="border-top textbox-info">
            <p class="f-left"><strong>{!! $receipt_details->invoice_no_prefix !!} : {{ $receipt_details->invoice_no }}</strong> </p>
            <p class="f-right">
                <strong>{{ $receipt_details->customer_label ?? '' }}</strong>
            </p>
        </div>
        <div class="textbox-info">
            <p class="f-left"><strong>{!! $receipt_details->date_label !!} : {{ $receipt_details->invoice_date }}</strong> <br>
                <strong>Counter:</strong> {{ Auth::user()->username }}
            </p>

            <p style="text-align: right" class="f-right">
                @if (!empty($receipt_details->customer_info))
                    {!! $receipt_details->customer_info !!}
                @endif
            </p>
        </div>

        @if (!empty($receipt_details->due_date_label))
            <div class="textbox-info">
                <p class="f-left"><strong>{{ $receipt_details->due_date_label }}</strong></p>
                <p class="f-right">{{ $receipt_details->due_date ?? '' }}</p>
            </div>
        @endif

        @if (!empty($receipt_details->sales_person_label))
            <div class="textbox-info">
                <p class="f-left"><strong>{{ $receipt_details->sales_person_label }}</strong></p>

                <p class="f-right">{{ $receipt_details->sales_person }}</p>
            </div>
        @endif
        @if (!empty($receipt_details->commission_agent_label))
            <div class="textbox-info">
                <p class="f-left"><strong>{{ $receipt_details->commission_agent_label }}</strong></p>

                <p class="f-right">{{ $receipt_details->commission_agent }}</p>
            </div>
        @endif

        @if (!empty($receipt_details->brand_label) || !empty($receipt_details->repair_brand))
            <div class="textbox-info">
                <p class="f-left"><strong>{{ $receipt_details->brand_label }}</strong></p>

                <p class="f-right">{{ $receipt_details->repair_brand }}</p>
            </div>
        @endif

        @if (!empty($receipt_details->device_label) || !empty($receipt_details->repair_device))
            <div class="textbox-info">
                <p class="f-left"><strong>{{ $receipt_details->device_label }}</strong></p>

                <p class="f-right">{{ $receipt_details->repair_device }}</p>
            </div>
        @endif

        @if (!empty($receipt_details->model_no_label) || !empty($receipt_details->repair_model_no))
            <div class="textbox-info">
                <p class="f-left"><strong>{{ $receipt_details->model_no_label }}</strong></p>

                <p class="f-right">{{ $receipt_details->repair_model_no }}</p>
            </div>
        @endif

        @if (!empty($receipt_details->serial_no_label) || !empty($receipt_details->repair_serial_no))
            <div class="textbox-info">
                <p class="f-left"><strong>{{ $receipt_details->serial_no_label }}</strong></p>

                <p class="f-right">{{ $receipt_details->repair_serial_no }}</p>
            </div>
        @endif

        @if (!empty($receipt_details->repair_status_label) || !empty($receipt_details->repair_status))
            <div class="textbox-info">
                <p class="f-left"><strong>
                        {!! $receipt_details->repair_status_label !!}
                    </strong></p>
                <p class="f-right">
                    {{ $receipt_details->repair_status }}
                </p>
            </div>
        @endif

        @if (!empty($receipt_details->repair_warranty_label) || !empty($receipt_details->repair_warranty))
            <div class="textbox-info">
                <p class="f-left"><strong>
                        {!! $receipt_details->repair_warranty_label !!}
                    </strong></p>
                <p class="f-right">
                    {{ $receipt_details->repair_warranty }}
                </p>
            </div>
        @endif

        <!-- Waiter info -->
        @if (!empty($receipt_details->service_staff_label) || !empty($receipt_details->service_staff))
            <div class="textbox-info">
                <p class="f-left"><strong>
                        {!! $receipt_details->service_staff_label !!}
                    </strong></p>
                <p class="f-right">
                    {{ $receipt_details->service_staff }}
                </p>
            </div>
        @endif

        @if (!empty($receipt_details->table_label) || !empty($receipt_details->table))
            <div class="textbox-info">
                <p class="f-left"><strong>
                        @if (!empty($receipt_details->table_label))
                            <b>{!! $receipt_details->table_label !!}</b>
                        @endif
                    </strong></p>
                <p class="f-right">
                    {{ $receipt_details->table }}
                </p>
            </div>
        @endif

        @if (!empty($receipt_details->sell_custom_field_1_value))
            <div class="textbox-info">
                <p class="f-left"><strong>{!! $receipt_details->sell_custom_field_1_label !!}</strong></p>
                <p class="f-right">
                    {{ $receipt_details->sell_custom_field_1_value }}
                </p>
            </div>
        @endif
        @if (!empty($receipt_details->sell_custom_field_2_value))
            <div class="textbox-info">
                <p class="f-left"><strong>{!! $receipt_details->sell_custom_field_2_label !!}</strong></p>
                <p class="f-right">
                    {{ $receipt_details->sell_custom_field_2_value }}
                </p>
            </div>
        @endif
        @if (!empty($receipt_details->sell_custom_field_3_value))
            <div class="textbox-info">
                <p class="f-left"><strong>{!! $receipt_details->sell_custom_field_3_label !!}</strong></p>
                <p class="f-right">
                    {{ $receipt_details->sell_custom_field_3_value }}
                </p>
            </div>
        @endif
        @if (!empty($receipt_details->sell_custom_field_4_value))
            <div class="textbox-info">
                <p class="f-left"><strong>{!! $receipt_details->sell_custom_field_4_label !!}</strong></p>
                <p class="f-right">
                    {{ $receipt_details->sell_custom_field_4_value }}
                </p>
            </div>
        @endif

        <!-- customer info -->
        {{-- <div class="textbox-info">
        <p style="vertical-align: top;"><strong>
                {{ $receipt_details->customer_label ?? '' }}
            </strong></p>

        <p>
            @if (!empty($receipt_details->customer_info))
                <div class="bw">
                    {!! $receipt_details->customer_info !!}
                </div>
            @endif
        </p>
    </div> --}}

        @if (!empty($receipt_details->client_id_label))
            <div class="textbox-info">
                <p class="f-left"><strong>
                        {{ $receipt_details->client_id_label }}
                    </strong></p>
                <p class="f-right">
                    {{ $receipt_details->client_id }}
                </p>
            </div>
        @endif

        @if (!empty($receipt_details->customer_tax_label))
            <div class="textbox-info">
                <p class="f-left"><strong>
                        {{ $receipt_details->customer_tax_label }}
                    </strong></p>
                <p class="f-right">
                    {{ $receipt_details->customer_tax_number }}
                </p>
            </div>
        @endif

        @if (!empty($receipt_details->customer_custom_fields))
            <div class="textbox-info">
                <p class="centered">
                    {!! $receipt_details->customer_custom_fields !!}
                </p>
            </div>
        @endif

        @if (!empty($receipt_details->customer_rp_label))
            <div class="textbox-info">
                <p class="f-left"><strong>
                        {{ $receipt_details->customer_rp_label }}
                    </strong></p>
                <p class="f-right">
                    {{ $receipt_details->customer_total_rp }}
                </p>
            </div>
        @endif
        @if (!empty($receipt_details->shipping_custom_field_1_label))
            <div class="textbox-info">
                <p class="f-left"><strong>
                        {!! $receipt_details->shipping_custom_field_1_label !!}
                    </strong></p>
                <p class="f-right">
                    {!! $receipt_details->shipping_custom_field_1_value ?? '' !!}
                </p>
            </div>
        @endif
        @if (!empty($receipt_details->shipping_custom_field_2_label))
            <div class="textbox-info">
                <p class="f-left"><strong>
                        {!! $receipt_details->shipping_custom_field_2_label !!}
                    </strong></p>
                <p class="f-right">
                    {!! $receipt_details->shipping_custom_field_2_value ?? '' !!}
                </p>
            </div>
        @endif
        @if (!empty($receipt_details->shipping_custom_field_3_label))
            <div class="textbox-info">
                <p class="f-left"><strong>
                        {!! $receipt_details->shipping_custom_field_3_label !!}
                    </strong></p>
                <p class="f-right">
                    {!! $receipt_details->shipping_custom_field_3_value ?? '' !!}
                </p>
            </div>
        @endif
        @if (!empty($receipt_details->shipping_custom_field_4_label))
            <div class="textbox-info">
                <p class="f-left"><strong>
                        {!! $receipt_details->shipping_custom_field_4_label !!}
                    </strong></p>
                <p class="f-right">
                    {!! $receipt_details->shipping_custom_field_4_value ?? '' !!}
                </p>
            </div>
        @endif
        @if (!empty($receipt_details->shipping_custom_field_5_label))
            <div class="textbox-info">
                <p class="f-left"><strong>
                        {!! $receipt_details->shipping_custom_field_5_label !!}
                    </strong></p>
                <p class="f-right">
                    {!! $receipt_details->shipping_custom_field_5_value ?? '' !!}
                </p>
            </div>
        @endif
        @if (!empty($receipt_details->sale_orders_invoice_no))
            <div class="textbox-info">
                <p class="f-left"><strong>
                        @lang('restaurant.order_no')
                    </strong></p>
                <p class="f-right">
                    {!! $receipt_details->sale_orders_invoice_no ?? '' !!}
                </p>
            </div>
        @endif

        @if (!empty($receipt_details->sale_orders_invoice_date))
            <div class="textbox-info">
                <p class="f-left"><strong>
                        @lang('lang_v1.order_dates')
                    </strong></p>
                <p class="f-right">
                    {!! $receipt_details->sale_orders_invoice_date ?? '' !!}
                </p>
            </div>
        @endif
        <table style="margin-top: 25px !important" class="border-bottom width-100 table-f-12 mb-10">
            <thead class="border-bottom-dotted">
                <tr>
                    <th class="serial_number">#</th>
                    <th class="description" width="30%">
                        {{ $receipt_details->table_product_label }}
                    </th>
                    <th class="quantity text-right">
                        {{ $receipt_details->table_qty_label }}
                    </th>
                    @if (empty($receipt_details->hide_price))
                        <th class="unit_price text-right">
                            {{ $receipt_details->table_unit_price_label }}
                        </th>
                        @if (!empty($receipt_details->discounted_unit_price_label))
                            <th class="text-right">
                                {{ $receipt_details->discounted_unit_price_label }}
                            </th>
                        @endif
                        @if (!empty($receipt_details->item_discount_label))
                            <th class="text-right">{{ $receipt_details->item_discount_label }}</th>
                        @endif
                        <th class="price text-right">{{ $receipt_details->table_subtotal_label }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($receipt_details->lines as $line)
                    <tr>
                        <td class="serial_number" style="vertical-align: top;">
                            {{ $loop->iteration }}
                        </td>
                        <td class="description">
                            {{ $line['name'] }} {{ $line['product_variation'] }} {{ $line['variation'] }}
                            @if (!empty($line['sub_sku']))
                                , {{ $line['sub_sku'] }}
                                @endif @if (!empty($line['brand']))
                                    , {{ $line['brand'] }}
                                    @endif @if (!empty($line['cat_code']))
                                        , {{ $line['cat_code'] }}
                                    @endif
                                    @if (!empty($line['product_custom_fields']))
                                        , {{ $line['product_custom_fields'] }}
                                    @endif
                                    @if (!empty($line['product_description']))
                                        <div class="f-8">
                                            {!! $line['product_description'] !!}
                                        </div>
                                    @endif
                                    @if (!empty($line['sell_line_note']))
                                        <br>
                                        <span class="f-8">
                                            {!! $line['sell_line_note'] !!}
                                        </span>
                                    @endif
                                    @if (!empty($line['lot_number']))
                                        <br> {{ $line['lot_number_label'] }}: {{ $line['lot_number'] }}
                                    @endif
                                    @if (!empty($line['product_expiry']))
                                        , {{ $line['product_expiry_label'] }}: {{ $line['product_expiry'] }}
                                    @endif
                                    @if (!empty($line['warranty_name']))
                                        <br>
                                        <small>
                                            {{ $line['warranty_name'] }}
                                        </small>
                                    @endif
                                    @if (!empty($line['warranty_exp_date']))
                                        <small>
                                            - {{ @format_date($line['warranty_exp_date']) }}
                                        </small>
                                    @endif
                                    @if (!empty($line['warranty_description']))
                                        <small> {{ $line['warranty_description'] ?? '' }}</small>
                                    @endif

                                    @if ($receipt_details->show_base_unit_details && $line['quantity'] && $line['base_unit_multiplier'] !== 1)
                                        <br><small>
                                            1 {{ $line['units'] }} = {{ $line['base_unit_multiplier'] }}
                                            {{ $line['base_unit_name'] }} <br>
                                            {{ $line['base_unit_price'] }} x {{ $line['orig_quantity'] }} =
                                            {{ $line['line_total'] }}
                                        </small>
                                    @endif
                        </td>
                        <td class="quantity text-right">{{ number_format($line['quantity']) }} ‍ @if ($receipt_details->show_base_unit_details && $line['quantity'] && $line['base_unit_multiplier'] !== 1)
                                <br><small>
                                    {{ $line['quantity'] }} x {{ $line['base_unit_multiplier'] }} =
                                    {{ $line['orig_quantity'] }} {{ $line['base_unit_name'] }}
                                </small>
                            @endif
                        </td>
                        @if (empty($receipt_details->hide_price))
                            <td class="unit_price text-right">
                                {{ number_format($line['unit_price_before_discount']) }}
                            </td>

                            @if (!empty($receipt_details->discounted_unit_price_label))
                                <td class="text-right">
                                    {{ number_format($line['unit_price_inc_tax']) }}
                                </td>
                            @endif

                            @if (!empty($receipt_details->item_discount_label))
                                <td class="text-right">
                                    ৳ {{ number_format($line['total_line_discount'] ?? '0.00') }}
                                    @if (!empty($line['line_discount_percent']))
                                        ({{ $line['line_discount_percent'] }}%)
                                    @endif
                                </td>
                            @endif
                            <td class="price text-right">{{ number_format($line['line_total']) }}</td>
                        @endif
                    </tr>
                    @if (!empty($line['modifiers']))
                        @foreach ($line['modifiers'] as $modifier)
                            <tr>
                                <td>
                                    &nbsp;
                                </td>
                                <td>
                                    {{ $modifier['name'] }} {{ $modifier['variation'] }}
                                    @if (!empty($modifier['sub_sku']))
                                        , {{ $modifier['sub_sku'] }}
                                        @endif @if (!empty($modifier['cat_code']))
                                            , {{ $modifier['cat_code'] }}
                                        @endif
                                        @if (!empty($modifier['sell_line_note']))
                                            ({!! $modifier['sell_line_note'] !!})
                                        @endif
                                </td>
                                <td class="text-right">{{ $modifier['quantity'] }} {{ $modifier['units'] }} </td>
                                @if (empty($receipt_details->hide_price))
                                    <td class="text-right">{{ $modifier['unit_price_inc_tax'] }}</td>
                                    @if (!empty($receipt_details->discounted_unit_price_label))
                                        <td class="text-right">{{ $modifier['unit_price_exc_tax'] }}</td>
                                    @endif
                                    @if (!empty($receipt_details->item_discount_label))
                                        <td class="text-right">0.00</td>
                                    @endif
                                    <td class="text-right">{{ $modifier['line_total'] }}</td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                @endforeach
                <tr>
                    <td @if (!empty($receipt_details->item_discount_label)) colspan="6" @else colspan="5" @endif>&nbsp;</td>
                    @if (!empty($receipt_details->discounted_unit_price_label))
                        <td></td>
                    @endif
                </tr>
            </tbody>
        </table>
        @if (!empty($receipt_details->total_quantity_label))
            <div class="flex-box">
                <p class="width-70 left text-right">
                    {!! $receipt_details->total_quantity_label !!}
                </p>
                <p class="width-30 text-right">
                    {{ $receipt_details->total_quantity }}
                </p>
            </div>
        @endif
        @if (!empty($receipt_details->total_items_label))
            <div class="flex-box">
                <p class="width-70 left text-right">
                    {!! $receipt_details->total_items_label !!}
                </p>
                <p class="width-30 text-right">
                    {{ $receipt_details->total_items }}
                </p>
            </div>
        @endif
        @if (empty($receipt_details->hide_price))
            <div class="flex-box">
                <p class="width-70 left text-right sub-headings">
                    {!! $receipt_details->subtotal_label !!}
                </p>
                <p class="width-30 text-right sub-headings">
                    ৳ {{ number_format($receipt_details->subtotal) }}
                </p>
            </div>

            <!-- Shipping Charges -->
            @if (!empty($receipt_details->shipping_charges))
                <div class="flex-box">
                    <p class="width-70 left text-right">
                        {!! $receipt_details->shipping_charges_label !!}
                    </p>
                    <p class="width-30 text-right">
                        {{ $receipt_details->shipping_charges }}
                    </p>
                </div>
            @endif

            @if (!empty($receipt_details->packing_charge))
                <div class="flex-box">
                    <p class="width-70 left text-right">
                        {!! $receipt_details->packing_charge_label !!}
                    </p>
                    <p class="width-30 text-right">
                        {{ $receipt_details->packing_charge }}
                    </p>
                </div>
            @endif

            <!-- Discount -->
            @if (!empty($receipt_details->discount))
                <div class="flex-box">
                    <p class="width-70 text-right">
                        {!! $receipt_details->discount_label !!}
                    </p>

                    <p class="width-30 text-right">
                        (-) {{ $receipt_details->discount }}
                    </p>
                </div>
            @endif

            @if (!empty($receipt_details->total_line_discount))
                <div class="flex-box">
                    <p class="width-70 text-right">
                        {!! $receipt_details->line_discount_label !!}
                    </p>

                    <p class="width-30 text-right">
                        (-) {{ $receipt_details->total_line_discount }}
                    </p>
                </div>
            @endif

            @if (!empty($receipt_details->additional_expenses))
                @foreach ($receipt_details->additional_expenses as $key => $val)
                    <div class="flex-box">
                        <p class="width-70 text-right">
                            {{ $key }}:
                        </p>

                        <p class="width-30 text-right">
                            (+)
                            {{ $val }}
                        </p>
                    </div>
                @endforeach
            @endif

            @if (!empty($receipt_details->reward_point_label))
                <div class="flex-box">
                    <p class="width-70 text-right">
                        {!! $receipt_details->reward_point_label !!}
                    </p>

                    <p class="width-30 text-right">
                        (-) {{ $receipt_details->reward_point_amount }}
                    </p>
                </div>
            @endif

            @if (!empty($receipt_details->tax))
                <div class="flex-box">
                    <p class="width-70 text-right">
                        {!! $receipt_details->tax_label !!}
                    </p>
                    <p class="width-30 text-right">
                        (+) {{ $receipt_details->tax }}
                    </p>
                </div>
            @endif

            @if ($receipt_details->round_off_amount > 0)
                <div class="flex-box">
                    <p class="width-70 text-right">
                        {!! $receipt_details->round_off_label !!}
                    </p>
                    <p class="width-30 text-right">
                        {{ $receipt_details->round_off }}
                    </p>
                </div>
            @endif

            <div class="flex-box">
                <p class="width-70 text-right sub-headings">
                    {!! $receipt_details->total_label !!}
                </p>
                <p class="width-30 text-right sub-headings">
                    ৳ {{ number_format($receipt_details->total) }}
                </p>
            </div>
            @if (!empty($receipt_details->total_in_words))
                <p colspan="2" class="text-right mb-0">
                    <small>
                        ({{ $receipt_details->total_in_words }})
                    </small>
                </p>
            @endif
            @if (!empty($receipt_details->payments))
                @foreach ($receipt_details->payments as $payment)
                    <div class="flex-box">
                        <p class="width-70 text-right">Method {{ $payment['method'] }} @if (!empty($receipt_details->total_due))
                                ({{ $payment['date'] }})
                            @endif
                        </p>
                        <p class="width-30 text-right">৳ {{ number_format($payment['amount']) }}</p>
                    </div>
                @endforeach
            @endif

            <!-- Total Paid-->
            @if (!empty($receipt_details->total_paid))
                <div class="flex-box">
                    <p class="width-70 text-right">
                        {!! $receipt_details->total_paid_label !!}
                    </p>
                    <p class="width-30 text-right">
                        ৳ {{ number_format($receipt_details->total_paid) }}
                    </p>
                </div>
            @endif

            <!-- Total Due-->
            @if (!empty($receipt_details->total_due) && !empty($receipt_details->total_due_label))
                <div class="watermark_due">
                    DUE
                </div>

                <div class="flex-box">
                    <p class="width-70 text-right">
                        {!! $receipt_details->total_due_label !!}
                    </p>
                    <p class="width-30 text-right">
                        {{ $receipt_details->total_due }}
                    </p>
                </div>
            @endif

            @if (!empty($receipt_details->all_due))
                <div class="flex-box">
                    <p class="width-70 text-right">
                        {!! $receipt_details->all_bal_label !!}
                    </p>
                    <p class="width-30 text-right">
                        {{ $receipt_details->all_due }}
                    </p>
                </div>
            @endif
        @endif
        <div class="border-bottom width-100">&nbsp;</div>
        @if (empty($receipt_details->hide_price) && !empty($receipt_details->tax_summary_label))
            <!-- tax -->
            @if (!empty($receipt_details->taxes))
                <table class="border-bottom width-100 table-f-12">
                    <tr>
                        <th colspan="2" class="text-center">{{ $receipt_details->tax_summary_label }}</th>
                    </tr>
                    @foreach ($receipt_details->taxes as $key => $val)
                        <tr>
                            <td class="left">{{ $key }}</td>
                            <td class="right">{{ $val }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        @endif

        @if (!empty($receipt_details->additional_notes))
            <p class="centered">
                {!! nl2br($receipt_details->additional_notes) !!}
            </p>
        @endif

        {{-- Barcode --}}
        @if ($receipt_details->show_barcode)
            <br />
            <img class="center-block"
                src="data:image/png;base64,{{ DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2, 30, [39, 48, 54], true) }}">
        @endif

        @if ($receipt_details->show_qr_code && !empty($receipt_details->qr_code_text))
            <img class="center-block mt-5"
                src="data:image/png;base64,{{ DNS2D::getBarcodePNG($receipt_details->qr_code_text, 'QRCODE') }}">
        @endif

        @if (!empty($receipt_details->footer_text))
            <p class="centered">
                {!! $receipt_details->footer_text !!}
            </p>
        @endif

    </div>
    </div>
@endif
