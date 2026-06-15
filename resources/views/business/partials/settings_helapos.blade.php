<!-- HelaPOS settings -->
<div class="pos-tab-content">
    <div class="row">
        <div class="col-md-12">
            <h4>HelaPOS Merchant QR Settings</h4>
            <p class="help-block">Enter your HelaPOS credentials to enable LankaQR payments.</p>
            <hr>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('helapos_app_id', 'HelaPOS App ID:') !!}
                {!! Form::text('common_settings[helapos_settings][app_id]', !empty($common_settings['helapos_settings']['app_id']) ? $common_settings['helapos_settings']['app_id'] : null, ['class' => 'form-control', 'placeholder' => 'Enter App ID']) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('helapos_app_secret', 'HelaPOS App Secret:') !!}
                {!! Form::password('common_settings[helapos_settings][app_secret]', ['class' => 'form-control', 'placeholder' => 'Enter App Secret']) !!}
                @if(!empty($common_settings['helapos_settings']['app_secret']))
                    <p class="help-block"><i>Credential already saved. Leave blank to keep existing.</i></p>
                @endif
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label('helapos_business_id', 'HelaPOS Business ID:') !!}
                {!! Form::text('common_settings[helapos_settings][business_id]', !empty($common_settings['helapos_settings']['business_id']) ? $common_settings['helapos_settings']['business_id'] : null, ['class' => 'form-control', 'placeholder' => 'Enter Business ID']) !!}
            </div>
        </div>
    </div>
</div>
