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
            <p class="mt-2 text-info">Generating QR code...</p>
        </div>
        <div id="helapos_qr_container" style="display:none;">
            <img id="helapos_qr_img" src="" class="img-thumbnail" style="width: 200px; height: 200px; margin: 0 auto; display: block;">
            <p class="mt-2 text-bold">Scan & Pay</p>
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
            clearTimeout(helapos_check_interval);
            helapos_check_interval = null;
        }
    });

    $(document).on('click', '.pos-helapay-qr', function(e) {
        e.preventDefault();
        var button = $(this);
        
        if ($('table#pos_table tbody').find('.product_row').length <= 0) {
            toastr.warning(LANG.no_products_added);
            return false;
        }

        if (typeof isValidPosForm === 'function' && isValidPosForm() != true) return false;

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
                data: { transaction_id: t_id, amount: amount },
                dataType: 'json',
                success: function(result) {
                    $('#helapos_qr_loading').hide();
                    if (result.statusCode == "200") {
                        var qr_data = result.qr_data || result.qrData;
                        var qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" + encodeURIComponent(qr_data);
                        $('#helapos_qr_img').attr('src', qr_url);
                        $('#helapos_qr_container').show();
                        startHelaPosPolling(result.reference, result.qr_reference || result.qrReference);
                    } else {
                        $('#helapos_qr_status').html('<p class="text-danger">Failed to generate QR</p>');
                    }
                },
                error: function(jqXHR) {
                    $('#helapos_qr_loading').hide();
                    var errorMsg = 'API Connection Error';
                    if (jqXHR.responseJSON && jqXHR.responseJSON.error) {
                        errorMsg = jqXHR.responseJSON.error;
                    }
                    $('#helapos_qr_status').html('<p class="text-danger">' + errorMsg + '</p>');
                    toastr.error(errorMsg);
                }
            });
        };

        if (!transaction_id) {
            var pos_form = $('form#add_pos_sell_form').length ? $('form#add_pos_sell_form') : $('form#edit_pos_sell_form');
            var data = pos_form.serialize() + '&status=draft';
            button.attr('disabled', true).html('<i class="fas fa-sync fa-spin"></i>');
            $.ajax({
                method: 'POST',
                url: pos_form.attr('action'),
                data: data,
                dataType: 'json',
                success: function(result) {
                    button.attr('disabled', false).html('<i class="fas fa-qrcode"></i> HelaPay');
                    if (result.success == 1) {
                        // Switch form to "Update" mode to avoid duplicates on final submit
                        var update_url = pos_form.attr('action') + '/' + result.transaction_id;
                        pos_form.attr('action', update_url);
                        
                        if (pos_form.find('input[name="_method"]').length === 0) {
                            pos_form.append('<input type="hidden" name="_method" value="PUT">');
                        }

                        if ($('input#transaction_id').length === 0) {
                            pos_form.append('<input type="hidden" id="transaction_id" name="transaction_id" value="' + result.transaction_id + '">');
                        } else {
                            $('input#transaction_id').val(result.transaction_id);
                        }
                        showQrModal(result.transaction_id);
                    } else { toastr.error(result.msg); }
                },
                error: function(jqXHR) {
                    button.attr('disabled', false).html('<i class="fas fa-qrcode"></i> HelaPay');
                    toastr.error('Failed to create draft sale. Please check logs.');
                }
            });
        } else { showQrModal(transaction_id); }
    });

    function startHelaPosPolling(invoice_no, qr_ref) {
        if (helapos_check_interval) clearTimeout(helapos_check_interval);
        $('#helapos_qr_status').html('<p class="text-info"><i class="fas fa-sync fa-spin"></i> Waiting for payment confirmation...</p>');
        var pollInterval = 7000;

        var pollHelaPos = function() {
            $.ajax({
                method: 'POST',
                url: '/helapos/check-status',
                data: { invoice_no: invoice_no, qr_reference: qr_ref },
                dataType: 'json',
                success: function(result) {
                    if (result.statusCode == "200" && result.sale && result.sale.payment_status == 2) {
                        try {
                            if (helapos_check_interval) clearTimeout(helapos_check_interval);
                            
                            // 1. Close Modal Instantly (Standard Cash Behavior)
                            $('#helapos_qr_modal').modal('hide');
                            
                            var final_form = $('form#add_pos_sell_form, form#edit_pos_sell_form').first();
                            var total_payable = $('input#final_total_input').val();

                            // 2. Clear other payment rows and inject HelaPay
                            $('#payment_rows_div').find('.payment_row').each(function(i) {
                                if (i > 0) $(this).remove();
                            });
                            
                            var payment_row = $('#payment_rows_div').find('.payment_row').first();
                            if (payment_row.length > 0) {
                                payment_row.find('.payment_types_dropdown').val('hela_qr').trigger('change');
                                var amount_field = payment_row.find('.payment-amount');
                                if (typeof __write_number !== 'undefined') {
                                    __write_number(amount_field, total_payable);
                                } else {
                                    amount_field.val(total_payable);
                                }
                                amount_field.trigger('change').trigger('input');
                                payment_row.find('input[name*="[transaction_no_1]"]').val(result.sale.reference_id || '');
                            }

                            // 3. Set Status to Final (to move to All Sales)
                            if (final_form.find('input[name="status"]').length == 0) {
                                final_form.append('<input type="hidden" name="status" value="final">');
                            } else {
                                final_form.find('input[name="status"]').val('final');
                            }

                            // 4. Force calculate & Submit (Native AJAX finalize)
                            if (typeof calculate_balance_due !== 'undefined') calculate_balance_due();
                            
                            console.log('HelaPay: Finalizing transaction via native AJAX logic...');
                            final_form.submit();
                        } catch (e) {
                            console.error('Finalize Error:', e);
                            $('form#add_pos_sell_form, form#edit_pos_sell_form').first().submit();
                        }
                    } else {
                        helapos_check_interval = setTimeout(pollHelaPos, pollInterval);
                    }
                },
                error: function(jqXHR) {
                    if (jqXHR.status == 429) pollInterval = 20000;
                    helapos_check_interval = setTimeout(pollHelaPos, pollInterval);
                }
            });
        };
        helapos_check_interval = setTimeout(pollHelaPos, pollInterval);
    }
});
</script>
