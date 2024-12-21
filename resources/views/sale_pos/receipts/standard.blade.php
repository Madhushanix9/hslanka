<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .invoice-container {
            width: 9.5in;
            height: 5.5in;
            margin: auto;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid black;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }

        .col-custom {
            padding: 0 10px;
            box-sizing: border-box;
        }

        .col-8 {
            flex: 0 0 66.666%;
            max-width: 66.666%;
        }

        .col-4 {
            flex: 0 0 33.333%;
            max-width: 33.333%;
        }

        .col-1 {
            flex: 0 0 8.333%;
            max-width: 8.333%;
        }

        .col-11 {
            flex: 0 0 91.666%;
            max-width: 91.666%;
        }

        .col-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }

        .col-12 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .invoice-header {
            margin-bottom: 10px;
        }

        .invoice-logo {
            max-width: 120px;
            height: auto;
        }

        .border-box {
            border: 1px solid black;
            padding: 10px;
        }

        .checkbox-area input {
            border: 1px solid black;
            width: 1.25rem;
            height: 1.25rem;
            margin-bottom: 5px;
        }

        .text-small {
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .invoice-table,
        .invoice-table th,
        .invoice-table td {
            border: 1px solid black;
        }

        .invoice-table td {
            padding: 4px;
            text-align: right;
            vertical-align: middle;
        }

        .invoice-table-details {
            width: 60%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .invoice-table-details,
        .invoice-table-details th,
        .invoice-table-details td {
            border: 1px solid black;
        }

        .invoice-table-details td {
            padding: 4px;
            text-align: left;
            vertical-align: middle;
        }

        .total-table {
            width: 50%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .total-table,
        .total-table th,
        .total-table td {
            border: 1px solid black;
        }

        .total-table td {
            padding: 4px;
            vertical-align: middle;
        }


        .no-margin {
            margin: 0;
            padding: 0;
        }

        @media print {
            .invoice-container {
                width: 9.5in;
                height: 5.5in;
                margin: 0;
                padding: 0;
                border: none;
            }
        }
    </style>

</head>

<body>
    <div class="invoice-container">
        <div class="row invoice-header" style="margin-top: 10px; margin-left: 50px; ">
            <div class="col-custom col-12">
                <div style="display: flex; align-items: center;">
                    @if (!empty($receipt_details->logo))
                        <img src="{{ $receipt_details->logo }}" alt="Logo" class="invoice-logo">
                    @endif

                    <div style="margin-left: 15px;">
                        @if (!empty($receipt_details->header_text))
                            <div class="col-custom col-12">
                                {!! $receipt_details->header_text !!}
                            </div>
                        @endif
                        <h2 class="text-center">
                            @if (!empty($receipt_details->business_name))
                                {{ $receipt_details->business_name }}
                            @endif
                        </h2>
                        <p>
                            @if (!empty($receipt_details->address))
                                <small class="text-center">
                                    {!! $receipt_details->address !!}
                                </small>
                            @endif
                            @if (!empty($receipt_details->contact))
                                <br />{!! $receipt_details->contact !!}
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
                        </p>
                        <p>
                            @if (!empty($receipt_details->sub_heading_line1))
                                {{ $receipt_details->sub_heading_line1 }}
                            @endif
                            @if (!empty($receipt_details->sub_heading_line2))
                                <br>{{ $receipt_details->sub_heading_line2 }}
                            @endif
                            @if (!empty($receipt_details->sub_heading_line3))
                                <br>{{ $receipt_details->sub_heading_line3 }}
                            @endif
                            @if (!empty($receipt_details->sub_heading_line4))
                                <br>{{ $receipt_details->sub_heading_line4 }}
                            @endif
                            @if (!empty($receipt_details->sub_heading_line5))
                                <br>{{ $receipt_details->sub_heading_line5 }}
                            @endif
                        </p>
                    </div>
                </div>

            </div>

        </div>
        <div class="row invoice-header">
            <div class="col-custom col-6">
                <div style="margin-left: 80px;">
                    @if (!empty($receipt_details->customer_info))
                        <br />
                        <b>{{ $receipt_details->customer_label }}</b> <br> {!! $receipt_details->customer_info !!} <br>
                    @endif
                    @if (!empty($receipt_details->client_id_label))
                        <br />
                        <b>{{ $receipt_details->client_id_label }}</b> {{ $receipt_details->client_id }}
                    @endif
                    @if (!empty($receipt_details->customer_tax_label))
                        <br />
                        <b>{{ $receipt_details->customer_tax_label }}</b> {{ $receipt_details->customer_tax_number }}
                    @endif
                    @if (!empty($receipt_details->customer_custom_fields))
                        <br />{!! $receipt_details->customer_custom_fields !!}
                    @endif
                    @if (!empty($receipt_details->sales_person_label))
                        <br />
                        <b>{{ $receipt_details->sales_person_label }}</b> {{ $receipt_details->sales_person }}
                    @endif

                    @if (!empty($receipt_details->customer_rp_label))
                        <br />
                        <strong>{{ $receipt_details->customer_rp_label }}</strong>
                        {{ $receipt_details->customer_total_rp }}
                    @endif
                </div>
            </div>
            <div class="col-custom col-6">
                @if (!empty($receipt_details->invoice_heading))
                    <h3 class="text-center">
                        {!! $receipt_details->invoice_heading !!}
                    </h3>
                @endif
                <table class="invoice-table-details pull-right">
                    <tbody>
                        <tr>
                            <td width="25%"><b>{{ $receipt_details->date_label }}</b></td>
                            <td width="35%">{{ $receipt_details->invoice_date }}</td>
                        </tr>
                        <tr>
                            <td width="25%"><b>{{ $receipt_details->invoice_no_prefix }}</b></td>
                            <td width="35%">{{ $receipt_details->invoice_no }}</td>
                        </tr>
                        @if (!empty($receipt_details->due_date_label))
                            <tr>
                                <td width="25%"><b>{{ $receipt_details->due_date_label }}</b></td>
                                <td width="35%">{{ $receipt_details->due_date ?? '' }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-custom col-1 checkbox-area">
                <div class="text-center">
                    <input type="checkbox" id="cashCheck" /><br />
                    <label for="cashCheck">Cash</label><br />
                    <input type="checkbox" id="creditCheck" /><br />
                    <label for="creditCheck">Credit</label><br />
                    <input type="checkbox" id="dealerCheck" /><br />
                    <label for="dealerCheck">Dealer</label><br />
                </div>
            </div>
            <div class="col-custom col-11">
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th class="text-center" width="4%">#</th>
                            <th width="40%">Description</th>
                            <th class="text-center" width="10%">Feet</th>
                            <th class="text-center" width="10%">Rate</th>
                            <th class="text-center" width="10%">Qty</th>
                            <th class="text-center" width="10%">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receipt_details->lines as $line)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td style=" text-align: left">{{ $line['name'] }}
                                    @if (!empty($line['sell_line_note']))
                                        <br>
                                        <small>{!! $line['sell_line_note'] !!}</small>
                                    @endif
                                </td>
                                <td class="text-center">{{ $line['quantity'] }} {{ $line['units'] }}</td>
                                <td class="text-right">{{ $line['unit_price_inc_tax'] }}</td>
                                <td class="text-center">{{ $line['quantity'] }} {{ $line['units'] }}</td>
                                <td class="text-right">{{ $line['line_total'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">No items found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-custom col-6"></div>
            <div class="col-custom col-6">
                <table class="total-table pull-right">
                    <tbody>
                        <tr>
                            <td style=" text-align: left;"><b>Total</b></td>
                            <td class="text-right">{{ $receipt_details->total }}</td>
                        </tr>
                        <tr>
                            <td style=" text-align: left;""><b>Total Paid</b></td>
                            <td class="text-right">{{ $receipt_details->total_paid }}</td>
                        </tr>
                        @if (!empty($receipt_details->total_due) && !empty($receipt_details->total_due_label))
                            <tr>
                                <th style=" text-align: left;">
                                    {!! $receipt_details->total_due_label !!}
                                </th>
                                <td class="text-right">
                                    {{ $receipt_details->total_due }}
                                </td>
                            </tr>
                        @endif

                        @if (!empty($receipt_details->all_due))
                            <tr>
                                <th style=" text-align: left;">
                                    {!! $receipt_details->all_bal_label !!}
                                </th>
                                <td class="text-right">
                                    {{ $receipt_details->all_due }}
                                </td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        @if (!empty($receipt_details->footer_text))
            <div class="col-custom col-12">
                {!! $receipt_details->footer_text !!}
            </div>
        @endif
    </div>
</body>

</html>
