<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ServiceTemplate;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Helpers\NumberToWordsHelper;

class InvoiceController extends Controller
{
    // Display a listing of invoices
    public function index()
    {
        $invoices = Invoice::with('items')->get();
        return view('invoices.index', compact('invoices'));
    }

    // Show the form for creating a new invoice
    public function create()
    {
        $customers = Customer::orderBy('first_name')->get();
        $serviceTemplates = ServiceTemplate::orderBy('name_en')->get();
        return view('invoices.create', compact('customers', 'serviceTemplates'));
    }    

    // Store a newly created invoice in storage
    public function store(Request $request)
    {
        $request->validate([
            'invoice_type' => 'required|in:credit,sales',
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'customer_id' => 'required|integer',
            'currency' => 'required|in:USD,SAR,EGP,AUD',
            'status' => 'required|in:draft,pending,paid',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.service_name' => 'required|string',
            'items.*.details' => 'nullable|string',
            'items.*.icon' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Calculate totals
        $subtotal = 0;
        $vatableAmount = 0;

        foreach ($request->items as $item) {
            $itemTotal = (int)$item['quantity'] * (float)$item['unit_price'];
            $subtotal += $itemTotal;

            // Check if service is VAT-free
            if (isset($item['service_template_id'])) {
                $serviceTemplate = ServiceTemplate::find($item['service_template_id']);
                if (!$serviceTemplate || !$serviceTemplate->is_vat_free) {
                    $vatableAmount += $itemTotal;
                }
            } else {
                // If no service template, assume it's vatable
                $vatableAmount += $itemTotal;
            }
        }

        // Discount logic
        $discountAmount = 0;
        $discountPercentage = null;
        if ($request->filled('discount_amount')) {
            $discountAmount = (float) $request->discount_amount;
        } elseif ($request->filled('discount_percentage')) {
            $discountPercentage = (float) $request->discount_percentage;
            $discountAmount = $subtotal * ($discountPercentage / 100);
        }

        // Apply discount to vatable amount proportionally
        if ($subtotal > 0) {
            $vatableAmount = $vatableAmount * (($subtotal - $discountAmount) / $subtotal);
        }

        $vatRate = 14.00; // 14% VAT
        $vatAmount = $vatableAmount * ($vatRate / 100);
        $total = $subtotal - $discountAmount + $vatAmount;

        $invoice = Invoice::create([
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'customer_id' => $request->customer_id,
            'invoice_type' => $request->invoice_type,
            'currency' => $request->currency,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'discount_percentage' => $discountPercentage,
            'vat_rate' => $vatRate,
            'vat_amount' => $vatAmount,
            'total' => $total,
            'status' => $request->status
        ]);

        // Create invoice items
        foreach ($request->items as $item) {
            // Convert details from string to array if not empty
            if (!empty($item['details'])) {
                $item['details'] = array_filter(explode("\n", $item['details']));
            } else {
                $item['details'] = [];
            }
            
            $total = (int)$item['quantity'] * (float)$item['unit_price'];
            
            $invoice->items()->create([
                'service_name' => $item['service_name'],
                'description' => $item['description'],
                'details' => $item['details'],
                'icon' => $item['icon'],
                'quantity' => (int)$item['quantity'],
                'unit_price' => (float)$item['unit_price'],
                'total' => $total,
                'service_template_id' => $item['service_template_id'] ?? null
            ]);
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice created successfully.');
    }

    // Display the specified invoice
    public function show($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);
        return view('invoices.show', compact('invoice'));
    }

    // Show the form for editing the specified invoice
    public function edit(Invoice $invoice)
    {
        $customers = Customer::all();
        $serviceTemplates = ServiceTemplate::all();
        return view('invoices.edit', compact('invoice', 'customers', 'serviceTemplates'));
    }

    // Update the specified invoice in storage
    public function update(Request $request, $id)
    {
        $request->validate([
            'invoice_type' => 'required|in:credit,sales',
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . $id,
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'customer_id' => 'required|integer',
            'currency' => 'required|in:USD,SAR,EGP,AUD',
            'status' => 'required|in:draft,pending,paid,cancelled',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.service_name' => 'required|string',
            'items.*.details' => 'nullable|string',
            'items.*.icon' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            // Find the invoice
            $invoice = Invoice::findOrFail($id);

            // Calculate totals
            $subtotal = 0;
            $vatableAmount = 0;

            foreach ($request->items as $item) {
                $itemTotal = (int)$item['quantity'] * (float)$item['unit_price'];
                $subtotal += $itemTotal;

                // Check if service is VAT-free
                if (isset($item['service_template_id'])) {
                    $serviceTemplate = ServiceTemplate::find($item['service_template_id']);
                    if (!$serviceTemplate || !$serviceTemplate->is_vat_free) {
                        $vatableAmount += $itemTotal;
                    }
                } else {
                    // If no service template, assume it's vatable
                    $vatableAmount += $itemTotal;
                }
            }

            // Discount logic
            $discountAmount = 0;
            $discountPercentage = null;
            if ($request->filled('discount_amount')) {
                $discountAmount = (float) $request->discount_amount;
            } elseif ($request->filled('discount_percentage')) {
                $discountPercentage = (float) $request->discount_percentage;
                $discountAmount = $subtotal * ($discountPercentage / 100);
            }

            // Apply discount to vatable amount proportionally
            if ($subtotal > 0) {
                $vatableAmount = $vatableAmount * (($subtotal - $discountAmount) / $subtotal);
            }

            $vatRate = 14.00; // 14% VAT
            $vatAmount = $vatableAmount * ($vatRate / 100);
            $total = $subtotal - $discountAmount + $vatAmount;

            // Update invoice details
            $invoice->update([
                'invoice_number' => $request->invoice_number,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'customer_id' => $request->customer_id,
                'invoice_type' => $request->invoice_type,
                'currency' => $request->currency,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'discount_percentage' => $discountPercentage,
                'vat_rate' => $vatRate,
                'vat_amount' => $vatAmount,
                'total' => $total,
                'status' => $request->status
            ]);

            // Delete existing items
            $invoice->items()->delete();

            // Create invoice items
            foreach ($request->items as $item) {
                // Convert details from string to array if not empty
                if (!empty($item['details'])) {
                    $item['details'] = array_filter(explode("\n", $item['details']));
                } else {
                    $item['details'] = [];
                }
                
                $total = (int)$item['quantity'] * (float)$item['unit_price'];
                
                $invoice->items()->create([
                    'service_name' => $item['service_name'],
                    'description' => $item['description'],
                    'details' => $item['details'],
                    'icon' => $item['icon'],
                    'quantity' => (int)$item['quantity'],
                    'unit_price' => (float)$item['unit_price'],
                    'total' => $total,
                    'service_template_id' => $item['service_template_id'] ?? null
                ]);
            }

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Invoice Update Error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update invoice. ' . $e->getMessage()]);
        }
    }

    // Remove the specified invoice from storage
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->items()->delete(); // Delete associated items first
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully');
    }

    public function print($id)
    {
        $invoice = Invoice::with('items', 'customer')->findOrFail($id);

        // Convert amount to words with currency
        $amountInWords = NumberToWordsHelper::convertToArabicWords($invoice->total_amount, $invoice->currency);

        // Pass amount in words to the view
        return view('invoices.print', compact('invoice', 'amountInWords'));
    }
}
