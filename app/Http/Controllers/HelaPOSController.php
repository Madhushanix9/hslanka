<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\Business;
use App\Utils\TransactionUtil;
use App\Services\HelaPOSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class HelaPOSController extends Controller
{
    protected $transactionUtil;

    public function __construct(TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Get HelaPOS Service for a specific business
     */
    private function getHelaPOSService($business_id)
    {
        $business = Business::find($business_id);
        $helapos_settings = !empty($business->common_settings['helapos_settings']) ? $business->common_settings['helapos_settings'] : [];

        $appId = !empty($helapos_settings['app_id']) ? $helapos_settings['app_id'] : null;
        $appSecret = !empty($helapos_settings['app_secret']) ? $helapos_settings['app_secret'] : null;
        $businessId = !empty($helapos_settings['business_id']) ? $helapos_settings['business_id'] : null;

        return new HelaPOSService($appId, $appSecret, $businessId);
    }

    /**
     * Generate QR for a transaction
     */
    public function generateQR(Request $request)
    {
        $transaction_id = $request->input('transaction_id');
        $transaction = Transaction::findOrFail($transaction_id);

        if ($transaction->payment_status == 'paid') {
            return response()->json(['error' => 'Transaction already paid'], 400);
        }

        $amount = $transaction->final_total - $transaction->payment_lines->sum('amount');
        
        if (Cache::has('helapos_auth_throttled')) {
            Log::warning('HelaPOS: Skipping QR Generation due to active cooling period.');
            return response()->json(['error' => 'HelaPOS is cooling down. Please wait 5 minutes.'], 429);
        }

        $helaPOSService = $this->getHelaPOSService($transaction->business_id);
        $qrData = $helaPOSService->generateQR($transaction->invoice_no, $amount);

        if ($qrData) {
            return response()->json($qrData);
        }

        return response()->json(['error' => 'Failed to generate QR'], 500);
    }

    /**
     * HelaPOS Webhook Callback
     */
    public function callback(Request $request)
    {
        Log::info('HelaPOS Callback received: ' . json_encode($request->all()));

        $payload = $request->all();

        if (isset($payload['statusCode']) && $payload['statusCode'] == "200" && isset($payload['sale'])) {
            $sale = $payload['sale'];
            $invoice_no = $payload['reference']; 
            
            if ($sale['payment_status'] == 2) {
                // We don't have business_id in payload, so we find transaction first
                $transaction = Transaction::where('invoice_no', $invoice_no)->first();

                if ($transaction) {
                    try {
                        DB::beginTransaction();

                        $payment_data = [
                            [
                                'amount' => $sale['amount'],
                                'method' => 'hela_qr',
                                'paid_on' => $sale['timestamp'],
                                'note' => 'HelaPOS QR Payment. Sale ID: ' . $sale['sale_id'],
                                'transaction_no' => $sale['reference_id']
                            ]
                        ];

                        $this->transactionUtil->createOrUpdatePaymentLines($transaction, $payment_data, $transaction->business_id);
                        $this->transactionUtil->updatePaymentStatus($transaction->id);

                        if ($transaction->status == 'draft') {
                            $transaction->status = 'final';
                            $transaction->save();
                        }

                        DB::commit();
                        return response()->json(['status' => 'success'], 200);

                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('HelaPOS Callback processing error: ' . $e->getMessage());
                        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
                    }
                } else {
                    Log::warning('HelaPOS Callback: Transaction not found for invoice_no: ' . $invoice_no);
                }
            }
        }

        return response()->json(['status' => 'ignored'], 200);
    }

    /**
     * Check payment status manually (triggered from frontend)
     */
    public function checkStatus(Request $request)
    {
        $invoice_no = $request->input('invoice_no');
        $qr_reference = $request->input('qr_reference');

        if (empty($invoice_no)) {
             return response()->json(['error' => 'Empty invoice number'], 400);
        }

        $transaction = Transaction::where('invoice_no', $invoice_no)->first();
       
        if (!$transaction) {
             Log::error('HelaPOS CheckStatus: Transaction not found for invoice_no: ' . $invoice_no);
             return response()->json(['error' => 'Transaction not found: ' . $invoice_no], 404);
        }
        
        $helaPOSService = $this->getHelaPOSService($transaction->business_id);
        $status = $helaPOSService->getPaymentStatus($invoice_no, $qr_reference);

        if (isset($status['statusCode']) && $status['statusCode'] == '429') {
            return response()->json($status, 429);
        }

        return response()->json($status);
    }

    /**
     * Generate QR from POS (saves draft if needed)
     */
    public function generateQRFromPOS(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $input = $request->input();
            $business_id = $request->session()->get('user.business_id');
            
            $transaction_id = !empty($input['transaction_id']) ? $input['transaction_id'] : null;
            
            if (!$transaction_id) {
                return response()->json(['error' => 'Please save as draft first or provide transaction_id'], 400);
            }

            $transaction = Transaction::where('business_id', $business_id)->findOrFail($transaction_id);
            
            $amount = $transaction->final_total - $transaction->payment_lines->sum('amount');
            
            if (Cache::has('helapos_auth_throttled')) {
                Log::warning('HelaPOS: Skipping POS QR Generation due to active cooling period.');
                return response()->json(['error' => 'HelaPOS is cooling down. Please wait 5 minutes.'], 429);
            }

            $helaPOSService = $this->getHelaPOSService($transaction->business_id);
            $qrData = $helaPOSService->generateQR($transaction->invoice_no, $amount);

            DB::commit();
            
            if ($qrData) {
                return response()->json($qrData);
            }
            return response()->json(['error' => 'HelaPOS API Error'], 500);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('HelaPOS generateQRFromPOS error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
