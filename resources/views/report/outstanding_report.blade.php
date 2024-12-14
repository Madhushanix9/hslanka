@extends('layouts.app')
@section('title', __('report.outstanding_report'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>{{ __('report.outstanding_report') }}</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                @component('components.filters', ['title' => __('report.filters')])
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('ir_customer_id', __('contact.customer') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                {!! Form::select('ir_customer_id', $customers, null, [
                                    'class' => 'form-control select2',
                                    'placeholder' => __('lang_v1.all'),
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('ir_sale_date_filter', __('lang_v1.sell_date') . ':') !!}
                            {!! Form::text('ir_sale_date_filter', null, [
                                'placeholder' => __('lang_v1.select_a_date_range'),
                                'class' => 'form-control',
                                'readonly',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('ir_location_id', __('purchase.business_location') . ':') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-map-marker"></i>
                                </span>
                                {!! Form::select('ir_location_id', $business_locations, null, [
                                    'class' => 'form-control select2',
                                    'placeholder' => __('messages.please_select'),
                                    'required',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    @if (Module::has('Manufacturing'))
                        <div class="col-md-3">
                            <div class="form-group">
                                <br>
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('only_mfg', 1, false, ['class' => 'input-icheck', 'id' => 'only_mfg_products']) !!} {{ __('manufacturing::lang.only_mfg_products') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif
                @endcomponent
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-primary'])
                    <div class="table-responsive">
                        <table class="table table-bordered ajax_view" id="outstanding_report_table">
                            <thead>
                                <tr>
                                    <th>@lang('messages.date')</th>
                                    <th>@lang('sale.invoice_no')</th>
                                    <th>@lang('contact.customer')</th>
                                    <th>@lang('sale.total_amount')</th>
                                    <th>@lang('sale.total_paid')</th>
                                    <th>@lang('lang_v1.sell_due')</th>
                                    <th>@lang('lang_v1.sell_return_due')</th>
                                    <th>@lang('lang_v1.all_due')</th>
                                    <th>@lang('lang_v1.total_due')</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="bg-gray font-17 text-center footer-total">
                                    <td colspan="3"><strong>@lang('sale.total'):</strong></td>
                                    <td id="footer_total_amount" class="display_currency" data-currency_symbol="true"></td>
                                    <td id="footer_total_paid" class="display_currency" data-currency_symbol="true"></td>
                                    <td id="footer_sell_due" class="display_currency" data-currency_symbol="true"></td>
                                    <td id="footer_sell_return_due" class="display_currency" data-currency_symbol="true"></td>
                                    <td id="footer_all_due" class="display_currency" data-currency_symbol="true"></td>
                                    <td id="footer_total_due" class="display_currency" data-currency_symbol="true"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endcomponent
            </div>
        </div>
    </section>
    <!-- /.content -->
    <div class="modal fade view_register" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {

            //Outstanding Report
            if ($('#ir_sale_date_filter').length == 1) {
                $('#ir_sale_date_filter').daterangepicker(dateRangeSettings, function(start, end) {
                    $('#ir_sale_date_filter').val(
                        start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                    );
                    outstanding_report_table.ajax.reload();
                });
                $('#ir_sale_date_filter').on('cancel.daterangepicker', function(ev, picker) {
                    $('#ir_sale_date_filter').val('');
                    outstanding_report_table.ajax.reload();
                });
            }

            //Outstanding Report
            outstanding_report_table = $('#outstanding_report_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '/reports/outstanding-report',
                    data: function(d) {
                        var sale_start = '';
                        var sale_end = '';
                        if ($('#ir_sale_date_filter').val()) {
                            sale_start = $('input#ir_sale_date_filter')
                                .data('daterangepicker')
                                .startDate.format('YYYY-MM-DD');
                            sale_end = $('input#ir_sale_date_filter')
                                .data('daterangepicker')
                                .endDate.format('YYYY-MM-DD');
                        }
                        d.sale_start = sale_start;
                        d.sale_end = sale_end;
                        d.customer_id = $('select#ir_customer_id').val();
                        d.location_id = $('select#ir_location_id').val();
                    }
                },
                columns: [{
                        data: 'transaction_date',
                        name: 'transaction_date',
                        orderable: false
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no',
                        orderable: false
                    },
                    {
                        data: 'first_name',
                        name: 'first_name',
                        orderable: false
                    },
                    {
                        data: 'final_total',
                        name: 'final_total',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'total_paid',
                        name: 'total_paid',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'total_remaining',
                        name: 'total_remaining',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'return_due',
                        name: 'return_due',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'total_due',
                        name: 'total_due',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'total_due_amount',
                        name: 'total_due_amount',
                        searchable: false,
                        orderable: false
                    },
                ],
                fnDrawCallback: function(oSettings) {
            
                    var total_amount = sum_table_col($('#outstanding_report_table'), 'final-total');
                    $('#footer_total_amount').text(total_amount);

                    var total_paid = sum_table_col($('#outstanding_report_table'), 'total-paid');
                    $('#footer_total_paid').text(total_paid);

                    var sell_due = sum_table_col($('#outstanding_report_table'), 'payment_due');
                    $('#footer_sell_due').text(sell_due);

                    var sell_return_due = sum_table_col($('#outstanding_report_table'),
                        'sell_return_due');
                    $('#footer_sell_return_due').text(sell_return_due);

                    var all_due = sum_table_col($('#outstanding_report_table'), 'total_due');
                    $('#footer_all_due').text(all_due);

                    var total_due = sum_table_col($('#outstanding_report_table'), 'total_due_amount');
                    $('#footer_total_due').text(total_due);
                    __currency_convert_recursively($('#outstanding_report_table'));
                },
                buttons: [{
                        extend: 'csv',
                        text: '<i class="fa fa-file-csv" aria-hidden="true"></i> Export to CSV',
                        className: 'btn-sm',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel" aria-hidden="true"></i> Export to Excel',
                        className: 'btn-sm',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fa fa-file-pdf" aria-hidden="true"></i> Export to PDF',
                        className: 'btn-sm',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                        customize: function(doc) {
                            // Increase the height of empty rows
                            doc.content[1].table.body.forEach((row, rowIndex) => {
                                if (row.every(cell => cell.text === '' || cell === '')) {
                                    row.forEach(cell => {
                                        cell.fillColor = '#f9f9f9';
                                        cell.text = ' ';
                                        cell.style = {
                                            fontSize: 8
                                        };
                                    });
                                    row[0].margin = [0, 0, 0, 2];
                                }
                            });

                            // Adjust styles globally to avoid overlapping or issues
                            doc.styles.tableBodyEven = {
                                margin: [0, 2, 0, 0]
                            }; // Even-row margin
                            doc.styles.tableBodyOdd = {
                                margin: [0, 2, 0, 0]
                            }; // Odd-row margin
                        }
                    },

                    {
                        extend: 'print',
                        text: '<i class="fa fa-print" aria-hidden="true"></i> Print',
                        className: 'btn-sm',
                        exportOptions: {
                            columns: ':visible',
                        },
                        footer: true,
                    }
                ],
            });

            $(document).on('change', '#ir_customer_id, #ir_location_id', function() {
                outstanding_report_table.ajax.reload();
            });
        });
    </script>
@endsection
