<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    {!! Form::open(['url' => action('ProductController@saveQuickProduct'), 'method' => 'post', 'id' => 'quick_add_product_form' ]) !!}

    <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      <h4 class="modal-title" id="modalTitle">@lang( 'product.add_new_product' )</h4>
    </div>
    <div class="modal-body">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('name', __('product.product_name') . ':*') !!}
              {!! Form::text('name', $product_name, ['class' => 'form-control', 'required',
              'placeholder' => __('product.product_name')]); !!}
              {!! Form::select('type', ['single' => 'Single', 'variable' => 'Variable'], 'single', ['class' => 'hide', 'id' => 'type']); !!}
          </div>
        </div>

        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('sku', __('product.sku') . ':') !!} @show_tooltip(__('tooltip.sku'))
            {!! Form::text('sku', null, ['class' => 'form-control',
              'placeholder' => __('product.sku')]); !!}
          </div>
        </div>

        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('unit_id', __('product.unit') . ':*') !!}
            <div class="input-group">
              {!! Form::select('unit_id', $units, !empty($duplicate_product->unit_id) ? $duplicate_product->unit_id : session('business.default_unit'), ['class' => 'form-control select2', 'required']); !!}
              <span class="input-group-btn">
                <button type="button" @if(!auth()->user()->can('unit.create')) disabled @endif class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action('UnitController@create', ['quick_add' => true])}}" title="@lang('unit.add_unit')" data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
              </span>
            </div>
          </div>
        </div>

        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('brand_id', __('product.brand') . ':') !!}
              {!! Form::select('brand_id', $brands, null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!}
          </div>
        </div>
        
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('category_id', __('product.category') . ':') !!}
              {!! Form::select('category_id', $categories, null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2']); !!}
          </div>
        </div>


        <div class="col-sm-4">
          <div class="form-group">
          <br>
            <label>
              {!! Form::checkbox('enable_stock', 1, true, ['class' => 'input-icheck', 'id' => 'enable_stock']); !!} <strong>@lang('product.manage_stock')</strong>
            </label>@show_tooltip(__('tooltip.enable_stock')) <p class="help-block"><i>@lang('product.enable_stock_help')</i></p>
          </div>
        </div>

        @if(!empty($common_settings['enable_product_warranty']))
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('warranty_id', __('lang_v1.warranty') . ':') !!}
            {!! Form::select('warranty_id', $warranties, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
          </div>
        </div>
        @endif
        @if(session('business.enable_product_expiry'))
          @if(session('business.expiry_type') == 'add_expiry')
              @php
                $expiry_period = 12;
                $hide = true;
              @endphp
          @else
              @php
                $expiry_period = null;
                $hide = false;
              @endphp
          @endif

        @endif
        @php
          $default_location = null;
          if(count($business_locations) == 1){
            $default_location = array_key_first($business_locations->toArray());
          }
        @endphp
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('product_locations', __('business.business_locations') . ':') !!} @show_tooltip(__('lang_v1.product_location_help'))
              {!! Form::select('product_locations[]', $business_locations, $default_location, ['class' => 'form-control select2', 'multiple', 'id' => 'product_locations']); !!}
          </div>
        </div>
        

        <div class="col-sm-4">
          <div class="checkbox">
          <br>
            <label>
              {!! Form::checkbox('enable_sr_no', 1, false, ['class' => 'input-icheck']); !!} <strong>@lang('lang_v1.enable_imei_or_sr_no')</strong>
            </label>@show_tooltip(__('lang_v1.tooltip_sr_no'))
          </div>
        </div>



        <div class="clearfix"></div>
        @if(!empty($module_form_parts))
          @foreach($module_form_parts as $key => $value)
            @if(!empty($value['template_path']))
              @php
                $template_data = $value['template_data'] ?: [];
              @endphp
              @include($value['template_path'], $template_data)
            @endif
          @endforeach
        @endif
      </div>
      <div class="row">
        <div class="form-group col-sm-11 col-sm-offset-1">
          @include('product.partials.single_product_form_part', ['profit_percent' => $default_profit_percent, 'quick_add' => true ])
        </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary" id="submit_quick_product">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
  $(document).ready(function(){
    $("form#quick_add_product_form").validate({
      rules: {
          sku: {
              remote: {
                  url: "/products/check_product_sku",
                  type: "post",
                  data: {
                      sku: function() {
                          return $( "#sku" ).val();
                      },
                      product_id: function() {
                          if($('#product_id').length > 0 ){
                              return $('#product_id').val();
                          } else {
                              return '';
                          }
                      },
                  }
              }
          },
          expiry_period:{
              required: {
                  depends: function(element) {
                      return ($('#expiry_period_type').val().trim() != '');
                  }
              }
          }
      },
      messages: {
          sku: {
              remote: LANG.sku_already_exists
          }
      },
      submitHandler: function (form) {
        
        var form = $("form#quick_add_product_form");
        var url = form.attr('action');
        form.find('button[type="submit"]').attr('disabled', true);
        $.ajax({
            method: "POST",
            url: url,
            dataType: 'json',
            data: $(form).serialize(),
            success: function(data){
                $('.quick_add_product_modal').modal('hide');
                if( data.success){
                    toastr.success(data.msg);
                    if (typeof get_purchase_entry_row !== 'undefined') {
                      var selected_location = $('#location_id').val();
                      var location_check = true;
                      if (data.locations && selected_location && data.locations.indexOf(selected_location) == -1) {
                        location_check = false;
                      }
                      if (location_check) {
                        get_purchase_entry_row( data.product.id, 0 );
                      }
                      
                    }
                    $(document).trigger({type: "quickProductAdded", 'product': data.product, 'variation': data.variation });
                } else {
                    toastr.error(data.msg);
                }
            }
        });
        return false;
      }
    });
  });
</script>