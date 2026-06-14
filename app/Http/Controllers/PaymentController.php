<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerRequest;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function getToken($id)
    {
        $cr = CustomerRequest::with('details')->findOrFail($id);

        if ($cr->status !== 'approved') {
            return response()->json(['error' => 'Pesanan belum diapprove atau sudah diproses.'], 400);
        }

        if ($cr->snap_token) {
            $url = Config::$isProduction 
                ? "https://app.midtrans.com/snap/v3/redirection/{$cr->snap_token}"
                : "https://app.sandbox.midtrans.com/snap/v3/redirection/{$cr->snap_token}";
            return response()->json(['redirect_url' => $url]);
        }

        $grossAmount = $cr->grand_total > 0 ? (float) $cr->grand_total : (float) $cr->details->sum('total');
        
        if ($grossAmount <= 0) {
            return response()->json(['error' => 'Total pesanan tidak valid.'], 400);
        }

        $itemDetails = [];
        foreach ($cr->details as $item) {
            // Midtrans mewajibkan quantity berupa integer.
            // Karena Qty beton bisa desimal, kita set quantity Midtrans = 1, 
            // dan price = total harga baris tersebut. Info qty asli ditaruh di nama.
            $itemDetails[] = [
                'id' => 'item-' . $item->id,
                'price' => round($item->total),
                'quantity' => 1,
                'name' => $item->grade->name_grade . ' (' . $item->qty . ' vol)',
            ];
        }

        if ($cr->delivery_distance > 0) {
            $itemDetails[] = [
                'id' => 'delivery-' . $cr->id,
                'price' => round($cr->delivery_fee),
                'quantity' => 1,
                'name' => 'Biaya Pengantaran (' . number_format($cr->delivery_distance, 1) . ' km)',
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id' => $cr->request_code . '-' . time(), // unique order id
                'gross_amount' => $grossAmount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $cr->customer_name,
                'email' => $cr->email ?? 'customer@example.com',
                'phone' => $cr->phone ?? '-',
            ],
        ];

        try {
            $transaction = Snap::createTransaction($params);
            $cr->snap_token = $transaction->token;
            $cr->save();

            return response()->json(['redirect_url' => $transaction->redirect_url]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memproses pembayaran.'], 500);
        }
    }

    public function notificationHandler(Request $request)
    {
        Log::info('Midtrans Webhook Received:', $request->all());
        
        try {
            $notif = new Notification();
        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            
            // Jika ini dari tombol "Tes URL Notifikasi" di dashboard Midtrans, transaksinya memang fiktif (tidak ada di API).
            // Midtrans SDK akan melempar exception "Transaction doesn't exist". Kita return 200 OK agar tesnya dibilang berhasil.
            if (strpos($e->getMessage(), "Transaction doesn't exist") !== false) {
                return response()->json(['message' => 'Tes Notifikasi Berhasil (Ignored)'], 200);
            }

            return response()->json(['error' => 'Invalid notification'], 400);
        }

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $orderId = $notif->order_id;
        $fraud = $notif->fraud_status;

        // order_id format is request_code-timestamp (where request_code itself might contain a hyphen like REQ-123)
        $parts = explode('-', $orderId);
        array_pop($parts); // remove the timestamp at the end
        $requestCode = implode('-', $parts);
        
        $cr = CustomerRequest::where('request_code', $requestCode)->first();

        if (!$cr) {
            // Return 200 OK supaya Midtrans tidak mengira request ini gagal (terutama saat "Tes URL Notifikasi")
            return response()->json(['message' => 'Pesanan tidak ditemukan (Diabaikan)'], 200);
        }

        if ($transaction == 'capture') {
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    // ignore
                } else {
                    $cr->status = 'paid';
                    $cr->is_paid = true;
                }
            }
        } else if ($transaction == 'settlement') {
            $cr->status = 'paid';
            $cr->is_paid = true;
        } else if ($transaction == 'pending') {
            // Wait for payment
        } else if ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
            // Token bisa direset agar user bisa membuat transaksi baru
            $cr->snap_token = null;
        }

        $cr->save();

        return response()->json(['message' => 'Status berhasil diupdate']);
    }
}
