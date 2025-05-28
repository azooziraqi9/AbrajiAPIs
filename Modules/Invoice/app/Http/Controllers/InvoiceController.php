<?php

namespace Modules\Invoice\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Invoice\Interfaces\InvoiceServiceInterface;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceServiceInterface $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    protected function validateInvoiceData(array $data)
    {
        $validator = Validator::make($data, [
            'tower' => 'nullable|string',
            'subscriber_name' => 'required|string',
            'subs_type' => 'required|string',
            'subs_price' => 'required|numeric',
            'activation_date' => 'required|date',
            'expiry_date' => 'required|date',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|string',
            'payed_price' => 'required|numeric|min:0',
            'remaining_price' => 'required|numeric|min:0',
        ]);


        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
    private function getAdminIdFromToken($token)
    {
        try {
            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }

            $tokenParts = explode('.', $token);
            $payload = base64_decode($tokenParts[1]);
            $decoded = json_decode($payload);

            if (isset($decoded->sub)) {
                return $decoded->sub;
            } else {
                throw new \Exception('Admin ID not found in token');
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $user_id = $this->getAdminIdFromToken($request->header('Authorization'));
        $invoices = $this->invoiceService->getAll($perPage, $user_id);

        return response()->json($invoices);
    }

    public function show($id)
    {
        try {
            $invoice = $this->invoiceService->get($id);
            return response()->json($invoice);
        } catch (Exception $e) {
            return response()->json(['error' => 'Invoice not found.'], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $this->validateInvoiceData($data);
            $data['user_id'] = $this->getAdminIdFromToken($request->header('Authorization'));
            $data['created_by'] =  $data['user_id'];
            $invoice = $this->invoiceService->create($data);
            return response()->json($invoice, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors(), 'status' => 422], 422);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => 500], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();
            $this->validateInvoiceData($data);
            $invoice = $this->invoiceService->update($data, $id);

            if ($invoice) {
                return response()->json($invoice);
            }

            return response()->json(['error' => 'Invoice not found.'], 404);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors(), 'status' => 422], 422);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => 500], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $result = $this->invoiceService->delete($id);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => 500], 500);
        }
    }

    public function approve($id)
    {
        try {
            $result = $this->invoiceService->approve($id);
            return response()->json($result);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => 500], 500);
        }
    }
}
