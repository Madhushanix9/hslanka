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
                        <table class="table table-bordered" id="outstanding_report_table">
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
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
@endsection
