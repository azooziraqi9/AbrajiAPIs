<?php
namespace Modules\Invoice\Services;

use Illuminate\Support\Facades\DB;
use Modules\Invoice\Interfaces\InvoiceServiceInterface;
use Modules\Invoice\Models\Invoice;
use Exception;

class InvoiceService implements InvoiceServiceInterface
{
    public function create(array $data)
    {
        try {
            $data['invoice_number'] = \Nette\Utils\Random::generate(6, '0-9'); // Generate a random invoice number

            DB::beginTransaction();

            $invoice = Invoice::create([
                'user_id' => $data['user_id'],
                'tower' => $data['tower'] ?? null,
                'invoice_number' => $data['invoice_number'],
                'subscriber_name' => $data['subscriber_name'],
                'subs_type' => $data['subs_type'],
                'subs_price' => $data['subs_price'],
                'activation_date' => $data['activation_date'],
                'expiry_date' => $data['expiry_date'],
                'payment_date' => $data['payment_date'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'payed_price' => $data['payed_price'] ?? 0,
                'remaining_price' => $data['remaining_price'] ?? 0,
                'created_by' => $data['created_by'],
                'status' => $data['status'] ?? 'pending',
            ]);

            DB::commit();
            return $invoice;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('An error occurred while creating the invoice: ' . $e->getMessage());
        }
    }

    public function update(array $data, int $id)
    {
        try {
            DB::beginTransaction();

            $invoice = Invoice::findOrFail($id);

            $invoice->update([
                'user_id' => $data['user_id']??$invoice->user_id,
                'tower' => $data['tower'] ?? $invoice->tower,
                'invoice_number' => $data['invoice_number'] ?? $invoice->invoice_number,
                'subscriber_name' => $data['subscriber_name'],
                'subs_type' => $data['subs_type'],
                'subs_price' => $data['subs_price'],
                'activation_date' => $data['activation_date'],
                'expiry_date' => $data['expiry_date'],
                'payment_date' => $data['payment_date'] ?? $invoice->payment_date,
                'payment_method' => $data['payment_method'] ?? $invoice->payment_method,
                'payed_price' => $data['payed_price'] ?? $invoice->payed_price,
                'remaining_price' => $data['remaining_price'] ?? $invoice->remaining_price,
                'created_by' => $data['created_by'] ??$invoice->created_by,
                'status' => $data['status'] ?? $invoice->status,
            ]);

            DB::commit();
            return $invoice;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('An error occurred while updating the invoice: ' . $e->getMessage());
        }
    }

    public function delete(int $id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $invoice->delete();
            return response()->json(['message' => 'Invoice deleted successfully.'], 200);
        } catch (Exception $e) {
            throw new Exception('An error occurred while deleting the invoice: ' . $e->getMessage());
        }
    }

    public function get(int $id)
    {
        try {
            return Invoice::with("invoiceItems")->findOrFail($id);
        } catch (Exception $e) {
            throw new Exception('An error occurred while retrieving the invoice: ' . $e->getMessage());
        }
    }

    public function getAll(int $perPage = 15, int $user_id = null)
    {
        try {
            return Invoice::where("user_id",$user_id)->paginate($perPage);
        } catch (Exception $e) {
            throw new Exception('An error occurred while retrieving the invoices: ' . $e->getMessage());
        }
    }

    public function approve(int $id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $invoice->update(['status' => 'approved']);
            return $invoice;
        } catch (Exception $e) {
            throw new Exception('An error occurred while approving the invoice: ' . $e->getMessage());
        }
    }
}
