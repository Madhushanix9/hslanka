<div class="modal fade" id="helapos_qr_modal" tabindex="-1" role="dialog" aria-labelledby="helaposQrModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="helaposQrModalLabel"><i class="fas fa-qrcode"></i> HelaPay QR</h4>
      </div>
      <div class="modal-body text-center">
        <div id="helapos_qr_loading" style="display:none;">
            <i class="fas fa-sync fa-spin fa-3x"></i>
            <p class="mt-2">Generating QR...</p>
        </div>
        <div id="helapos_qr_container" style="display:none;">
            <img id="helapos_qr_img" src="" class="img-thumbnail" style="width: 200px; height: 200px; margin: 0 auto; display: block;">
            <p class="mt-2 text-bold">Scan with HelaPay or any LankaQR App</p>
        </div>
        <div id="helapos_qr_status" style="margin-top: 15px;">
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    var helapos_check_interval = null;

    $(document).on('hide.bs.modal', '#helapos_qr_modal', function() {
        if (helapos_check_interval) {
            clearInterval(helapos_check_interval);
            helapos_check_interval = null;
        }
    });

    $(document).on('click', '.pos-helapay-qr', function(e) {
        e.preventDefault();
        var button = $(this);
        
        console.log("HelaPay button clicked. Utilizing dedicated modal pop-up.");

        //Check if product is present or not.
        if ($('table#pos_table tbody').find('.product_row').length <= 0) {
            toastr.warning(LANG.no_products_added);
            return false;
        }

        // Validate the form
        if (typeof isValidPosForm === 'function') {
            var is_valid = isValidPosForm();
            if (is_valid != true) {
                console.log("POS form validation failed.");
                return false;
            }
        }

        var amount = __read_number($('input#final_total_input'));
        var transaction_id = $('input#transaction_id').val();
        
        var showQrModal = function(t_id) {
            $('#helapos_qr_container').hide();
            $('#helapos_qr_status').html('');
            $('#helapos_qr_loading').show();
            $('#helapos_qr_modal').modal('show');

            $.ajax({
                method: 'POST',
                url: '/helapos/generate-qr-pos',
                data: {
                    transaction_id: t_id,
                    amount: amount
                },
                dataType: 'json',
                success: function(result) {
                    $('#helapos_qr_loading').hide();
                    
                    if (result.statusCode == "200") {
                        var qr_data = result.qr_data || result.qrData;
                        var qr_ref = result.qr_reference || result.qrReference;
                        var invoice_no = result.reference;

                        if (!qr_data) {
                            $('#helapos_qr_status').html('<p class="text-danger">QR data missing from API response.</p>');
                            return;
                        }

                        var qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" + encodeURIComponent(qr_data);
                        $('#helapos_qr_img').attr('src', qr_url);
                        $('#helapos_qr_container').show();

                        // Start polling
                        startHelaPosPolling(invoice_no, qr_ref);
                    } else {
                        $('#helapos_qr_status').html('<p class="text-danger">' + (result.message || 'Failed to generate QR') + '</p>');
                    }
                },
                error: function(jqXHR) {
                    $('#helapos_qr_loading').hide();
                    $('#helapos_qr_status').html('<p class="text-danger">Failed to communicate with QR server.</p>');
                }
            });
        };

        if (!transaction_id) {
            console.log("Saving draft via AJAX before generating QR...");
            var pos_form = $('form#add_pos_sell_form').length ? $('form#add_pos_sell_form') : $('form#edit_pos_sell_form');
            var data = pos_form.serialize();
            data = data + '&status=draft';
            var url = pos_form.attr('action');

            button.attr('disabled', true).html('<i class="fas fa-sync fa-spin"></i> Loading...');
            
            $.ajax({
                method: 'POST',
                url: url,
                data: data,
                dataType: 'json',
                success: function(result) {
                    button.attr('disabled', false).html('<i class="fas fa-qrcode"></i> HelaPay');
                    if (result.success == 1) {
                        if ($('input#transaction_id').length === 0) {
                            pos_form.append('<input type="hidden" id="transaction_id" name="transaction_id" value="' + result.transaction_id + '">');
                        } else {
                            $('input#transaction_id').val(result.transaction_id);
                        }
                        showQrModal(result.transaction_id);
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function(jqXHR) {
                    toastr.error('Failed to save transaction state.');
                    button.attr('disabled', false).html('<i class="fas fa-qrcode"></i> HelaPay');
                }
            });
        } else {
            showQrModal(transaction_id);
        }
    });

    function startHelaPosPolling(invoice_no, qr_ref, div_context = null) {
        if (helapos_check_interval) clearInterval(helapos_check_interval);

        var statusElem = div_context ? div_context.find('.helapos_status_display') : $('#helapos_qr_status');
        statusElem.html('<p class="text-info"><i class="fas fa-sync fa-spin"></i> Waiting for payment...</p>');

        helapos_check_interval = setInterval(function() {
            $.ajax({
                method: 'POST',
                url: '/helapos/check-status',
                data: {
                    invoice_no: invoice_no,
                    qr_reference: qr_ref
                },
                dataType: 'json',
                success: function(result) {
                    if (result.statusCode == "200" && result.sale && result.sale.payment_status == 2) {
                        clearInterval(helapos_check_interval);
                        statusElem.html('<p class="text-success"><i class="fas fa-check-circle"></i> Payment Confirmed!</p>');
                        toastr.success('Payment Confirmed via HelaPOS');

                        if (div_context) {
                            // Split payment mode: Update the specific existing row
                            var row = div_context.closest('.payment_row');
                            var row_index = row.find('.payment_row_index').val();
                            
                            $('select[name="payment[' + row_index + '][method]"]').val('hela_qr');
                            // Amount is already set by user in the field
                            $('input[name="payment[' + row_index + '][transaction_no_1]"]').val(result.sale.reference_id || '');
                        } else {
                            // Express Check-out mode: Force single payment row for full amount
                            var finalString = $('input#final_total_input').val();
                            var methodSelect = $('select[name="payment[0][method]"]');
                            if (methodSelect.length > 0) {
                                methodSelect.val('hela_qr').trigger('change');
                                $('input[name="payment[0][amount]"]').val(finalString);
                                $('input[name="payment[0][transaction_no_1]"]').val(result.sale.reference_id || '');
                            }

                            // Remove extra rows to ensure clean sync
                            $('.payment_row').each(function(i) {
                                if (i > 0) $(this).remove();
                            });
                        }

                        setTimeout(function() {
                            $('#helapos_qr_modal').modal('hide');
                            pos_form_obj.submit();
                        }, 1500);
                    }
                }
            });
        }, 5000); 
    }

    $(document).on('click', '.generate_helapos_qr', function() {
        var btn = $(this);
        var div = btn.closest('.payment_details_div');
        var row_index = div.closest('.payment_row').find('.payment_row_index').val();
        var amount = $('input#amount_' + row_index).val();
        var transaction_id = $('input#transaction_id').val();
        
        if (!transaction_id) {
            toastr.error('Please save as draft or use HelaPay express button first.');
            return;
        }

        btn.attr('disabled', true).text('Generating...');

        $.ajax({
            method: 'POST',
            url: '/helapos/generate-qr-pos',
            data: { transaction_id: transaction_id, amount: amount },
            dataType: 'json',
            success: function(result) {
                if (result.statusCode == "200" && (result.qr_data || result.qrData)) {
                    var qr_data = result.qr_data || result.qrData;
                    var qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" + encodeURIComponent(qr_data);
                    div.find('.helapos_qr_display').html('<img src="' + qr_url + '" class="img-thumbnail"><br><strong>Scan with HelaPay App</strong>');
                    btn.hide();
                    startHelaPosPolling(result.reference, result.qr_reference || result.qrReference, div);
                } else {
                    toastr.error('Failed to generate QR');
                    btn.attr('disabled', false).text('Generate HelaPOS QR');
                }
            },
            error: function() {
                toastr.error('API Error');
                btn.attr('disabled', false).text('Generate HelaPOS QR');
            }
        });
    });
});
</script>

