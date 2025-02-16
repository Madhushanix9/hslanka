<!-- default value -->
@php
    $go_back_url = action('SellPosController@index');
    $transaction_sub_type = '';
    $view_suspended_sell_url = action('SellController@index') . '?suspended=1';
    $pos_redirect_url = action('SellPosController@create');
@endphp

@if (!empty($pos_module_data))
    @foreach ($pos_module_data as $key => $value)
        @php
            if (!empty($value['go_back_url'])) {
                $go_back_url = $value['go_back_url'];
            }

            if (!empty($value['transaction_sub_type'])) {
                $transaction_sub_type = $value['transaction_sub_type'];
                $view_suspended_sell_url .= '&transaction_sub_type=' . $transaction_sub_type;
                $pos_redirect_url .= '?sub_type=' . $transaction_sub_type;
            }
        @endphp
    @endforeach
@endif
<input type="hidden" name="transaction_sub_type" id="transaction_sub_type" value="{{ $transaction_sub_type }}">
@inject('request', 'Illuminate\Http\Request')
<div class="col-md-12 no-print pos-header"
    style="background-image: linear-gradient(90deg, rgba(0,165,218,1) 0%, rgba(1,56,139,1) 100%);padding-bottom: 5px;">
    <input type="hidden" id="pos_redirect_url" value="{{ $pos_redirect_url }}">
    <div class="row">
        <div class="col-md-6">
            <div class="m-6 mt-5" style="display: flex; color:#fff;">
                <p><strong>@lang('sale.location'): &nbsp;</strong>
                    @if (empty($transaction->location_id))
                        @if (count($business_locations) > 1)
                            <div style="width: 28%;margin-bottom: 5px;">
                                {!! Form::select(
                                    'select_location_id',
                                    $business_locations,
                                    $default_location->id ?? null,
                                    ['class' => 'form-control input-sm', 'id' => 'select_location_id', 'required', 'autofocus'],
                                    $bl_attributes,
                                ) !!}
                            </div>
                        @else
                            {{ $default_location->name }}
                        @endif
                    @endif

                    @if (!empty($transaction->location_id))
                        {{ $transaction->location->name }}
                    @endif &nbsp; 
                    <i style="color:#fff;" class="fa fa-keyboard hover-q text-muted" aria-hidden="true" data-container="body"
                        data-toggle="popover" data-placement="bottom" data-content="@include('sale_pos.partials.keyboard_shortcuts_details')"
                        data-html="true" data-trigger="hover" data-original-title="" title=""></i>
                </p>
                <b><span style="margin-left:250px;" class="curr_datetime">{{ @format_datetime('now') }}</b></span>
            </div>
            
        </div>
        <div class="col-md-6">
            <a href="{{ $go_back_url }}" title="{{ __('lang_v1.go_back') }}"
                class="btn btn-info btn-flat m-6 btn-xs m-5 pull-right button-padding">
                <strong><i class="fa fa-backward button-size"></i></strong>
            </a>
            @can('close_cash_register')
                <button type="button" id="close_register" title="{{ __('cash_register.close_register') }}"
                    class="btn btn-success btn-flat m-6 btn-xs m-5 btn-modal pull-right button-padding"
                    data-container=".close_register_modal"
                    data-href="{{ action('CashRegisterController@getCloseRegister') }}">
                    <strong><i class="fa fa-window-close button-size"></i></strong>
                </button>
            @endcan

            @can('view_cash_register')
                <button type="button" id="register_details" title="{{ __('cash_register.register_details') }}"
                    class="btn btn-success btn-flat m-6 btn-xs m-5 btn-modal pull-right button-padding"
                    data-container=".register_details_modal"
                    data-href="{{ action('CashRegisterController@getRegisterDetails') }}">
                    <strong><i class="fa fa-briefcase button-size" aria-hidden="true"></i></strong>
                </button>
            @endcan

            <button title="@lang('lang_v1.calculator')" id="btnCalculator" type="button"
                class="btn btn-success btn-flat pull-right button-padding m-5 btn-xs mt-10 popover-default"
                data-toggle="popover" data-trigger="click" data-content='@include('layouts.partials.calculator')' data-html="true"
                data-placement="bottom">
                <strong><i class="fa fa-calculator button-size" aria-hidden="true"></i></strong>
            </button>

            <button type="button" class="btn btn-success btn-flat m-6 btn-xs m-5 pull-right button-padding"
                id="return_sale" title="@lang('lang_v1.sell_return')" data-toggle="popover" data-trigger="click"
                data-content='<div class="m-8"><input type="text" class="form-control" placeholder="@lang('sale.invoice_no')" id="send_for_sell_return_invoice_no"></div><div class="w-100 text-center"><button type="button" class="btn btn-success" id="send_for_sell_return">@lang('lang_v1.send')</button></div>'
                data-html="true" data-placement="bottom">
                <strong><i class="fas fa-undo button-size"></i></strong>
            </button>

            <button type="button" title="{{ __('lang_v1.full_screen') }}"
                class="btn btn-primary btn-flat m-6 hidden-xs btn-xs m-5 pull-right button-padding" id="full_screen">
                <strong><i class="fa fa-window-maximize button-size"></i></strong>
            </button>

            <button type="button" id="view_suspended_sales" title="{{ __('lang_v1.view_suspended_sales') }}"
                class="btn btn-info btn-flat m-6 btn-xs m-5 btn-modal pull-right button-padding hidden-xs"
                data-container=".view_modal" data-href="{{ $view_suspended_sell_url }}">
                <strong><i class="fa fa-pause-circle button-size"></i></strong>
            </button>
            @if (empty($pos_settings['hide_product_suggestion']) && isMobile())
                <button type="button" title="{{ __('lang_v1.view_products') }}" data-placement="bottom"
                    class="btn btn-success btn-flat m-6 btn-xs m-5 btn-modal pull-right button-padding"
                    data-toggle="modal" data-target="#mobile_product_suggestion_modal">
                    <strong><i class="fa fa-cubes button-size"></i></strong>
                </button>
            @endif

            @if (Module::has('Repair') && $transaction_sub_type != 'repair')
                @include('repair::layouts.partials.pos_header')
            @endif

            @if (in_array('pos_sale', $enabled_modules) && !empty($transaction_sub_type))
                @can('sell.create')
                    <a href="{{ action('SellPosController@create') }}" title="@lang('sale.pos_sale')"
                        class="btn btn-success btn-flat m-6 btn-xs m-5 pull-right button-padding">
                        <strong><i class="fa fa-th-large"></i> &nbsp; @lang('sale.pos_sale')</strong>
                    </a>
                @endcan
            @endif
            @can('expense.add')
                <button type="button" id="add_expense" title="{{ __('expense.add_expense') }}"
                    class="btn btn-info btn-flat m-6 btn-xs m-5 btn-modal pull-right button-padding hidden-xs">
                    <strong><i class="fa fas fa-minus-circle button-size"></i></strong>
                </button>
            @endcan
            
        </div>
    </div>
</div>
<style>
    .time_and_shortcuts {
        margin-top: 5px !important;
        color: white !important;
    }

    .button-padding {
        padding: 4px 6px 4px 6px !important;
        border-radius: 50% !important;
        background: #1eaae7 !important;

    }

    .button-action {
        padding: 9px 5px 3px 5px !important;
        font-size: 12px !important;
        background: #949494 !important;
    }

    .button-size {
        font-size: 18px;
    }

</style>
