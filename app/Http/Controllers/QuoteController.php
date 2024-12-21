<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\Customer;
use App\Models\QuoteTerm;
use App\Models\ServiceTemplate;
use App\Models\QuoteDefaultNote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index()
    {
        $quotes = Quote::with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('quotes.index', compact('quotes'));
    }

    public function create()
    {
        $customers = Customer::orderBy('first_name')->get();
        $defaultTerms = QuoteTerm::getDefaults();
        $serviceTemplates = ServiceTemplate::where('is_active', true)->get();
        $defaultNotes = QuoteDefaultNote::getDefaults();
        
        return view('quotes.create', compact('customers', 'defaultTerms', 'serviceTemplates', 'defaultNotes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'quote_date' => 'required|date',
            'valid_until' => 'nullable|date|after:quote_date',
            'currency' => 'required|in:USD,SAR,EGP',
            'items' => 'required|array|min:1',
            'items.*.service_name' => 'required|string',
            'items.*.description' => 'nullable|string',
            'items.*.details' => 'nullable|string',
            'items.*.icon' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.service_template_id' => 'nullable|exists:service_templates,id',
            'notes' => 'nullable|string',
            'terms' => 'nullable|array',
            'terms.*' => 'exists:quote_terms,id',
        ]);

        // Calculate totals
        $subtotal = 0;
        $vatableAmount = 0;

        foreach ($request->items as $item) {
            $itemTotal = $item['quantity'] * $item['unit_price'];
            $subtotal += $itemTotal;

            // Check if service is VAT-free
            $isVatFree = false;
            
            if (isset($item['service_template_id'])) {
                $serviceTemplate = ServiceTemplate::find($item['service_template_id']);
                if ($serviceTemplate) {
                    $isVatFree = $serviceTemplate->is_vat_free;
                }
            }
            
            // Override template VAT free setting if explicitly set in the form
            if (isset($item['is_vat_free'])) {
                $isVatFree = (bool)$item['is_vat_free'];
            }

            if (!$isVatFree) {
                $vatableAmount += $itemTotal;
            }
        }

        $vatRate = 14.00; // 14% VAT
        $vatAmount = $vatableAmount * ($vatRate / 100);
        $total = $subtotal + $vatAmount;

        try {
            $result = \DB::transaction(function() use ($request, $subtotal, $vatRate, $vatAmount, $total) {
                $quote = Quote::create([
                    'quote_number' => Quote::generateQuoteNumber(),
                    'customer_id' => $request->customer_id,
                    'quote_date' => $request->quote_date,
                    'valid_until' => $request->valid_until,
                    'currency' => $request->currency,
                    'subtotal' => $subtotal,
                    'vat_rate' => $vatRate,
                    'vat_amount' => $vatAmount,
                    'total' => $total,
                    'notes' => $request->notes,
                    'status' => 'draft',
                ]);

                // Create quote items
                foreach ($request->items as $item) {
                    // Convert details from string to array if not empty
                    if (!empty($item['details'])) {
                        $item['details'] = array_filter(explode("\n", $item['details']));
                    } else {
                        $item['details'] = [];
                    }
                    
                    $itemData = array_merge($item, [
                        'service_template_id' => $item['service_template_id'] ?? null,
                        'amount' => $item['quantity'] * $item['unit_price']
                    ]);
                    
                    $quote->items()->create($itemData);
                }

                // Attach terms
                if ($request->has('terms')) {
                    $quote->terms()->attach($request->terms);
                }

                return $quote;
            });

            return redirect()->route('quotes.show', $result)
                ->with('success', 'Quote created successfully.');
                
        } catch (\Exception $e) {
            \Log::error('Quote creation error: ' . $e->getMessage());
            return back()->withInput()
                ->withErrors(['error' => 'Failed to create quote. Please try again.']);
        }
    }

    public function show(Quote $quote)
    {
        $quote->load(['customer', 'items', 'terms']);
        return view('quotes.show', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        $customers = Customer::orderBy('first_name')->get();
        $defaultTerms = QuoteTerm::getDefaults();
        $serviceTemplates = ServiceTemplate::where('is_active', true)->get();
        $defaultNotes = QuoteDefaultNote::getDefaults();
        $quote->load(['items', 'terms']);
        
        return view('quotes.edit', compact('quote', 'customers', 'defaultTerms', 'serviceTemplates', 'defaultNotes'));
    }

    public function update(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'quote_date' => 'required|date',
            'valid_until' => 'nullable|date|after:quote_date',
            'currency' => 'required|in:USD,SAR,EGP',
            'items' => 'required|array|min:1',
            'items.*.service_name' => 'required|string',
            'items.*.description' => 'nullable|string',
            'items.*.details' => 'nullable|string',
            'items.*.icon' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.service_template_id' => 'nullable|exists:service_templates,id',
            'notes' => 'nullable|string',
            'terms' => 'nullable|array',
            'terms.*' => 'exists:quote_terms,id',
        ]);

        // Calculate totals
        $subtotal = collect($request->items)->sum(function ($item) {
            return $item['quantity'] * $item['unit_price'];
        });
        $vatableAmount = 0;

        foreach ($request->items as $item) {
            // Check if service is VAT-free
            $isVatFree = false;
            
            if (isset($item['service_template_id'])) {
                $serviceTemplate = ServiceTemplate::find($item['service_template_id']);
                if ($serviceTemplate) {
                    $isVatFree = $serviceTemplate->is_vat_free;
                }
            }
            
            // Override template VAT free setting if explicitly set in the form
            if (isset($item['is_vat_free'])) {
                $isVatFree = (bool)$item['is_vat_free'];
            }

            if (!$isVatFree) {
                $vatableAmount += $item['quantity'] * $item['unit_price'];
            }
        }

        $vatRate = 14.00; // 14% VAT
        $vatAmount = $vatableAmount * ($vatRate / 100);
        $total = $subtotal + $vatAmount;

        $quote->update([
            'customer_id' => $request->customer_id,
            'quote_date' => $request->quote_date,
            'valid_until' => $request->valid_until,
            'currency' => $request->currency,
            'subtotal' => $subtotal,
            'vat_rate' => $vatRate,
            'vat_amount' => $vatAmount,
            'total' => $total,
            'notes' => $request->notes,
        ]);

        // Update quote items
        $quote->items()->delete();
        foreach ($request->items as $item) {
            // Convert details from string to array if not empty
            if (!empty($item['details'])) {
                $item['details'] = array_filter(explode("\n", $item['details']));
            } else {
                $item['details'] = [];
            }
            $quote->items()->create($item);
        }

        // Update terms
        $quote->terms()->sync($request->terms ?? []);

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'Quote updated successfully.');
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();
        return redirect()->route('quotes.index')
            ->with('success', 'Quote deleted successfully.');
    }

    public function updateStatus(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,accepted,rejected',
        ]);

        $quote->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', 'Quote status updated successfully.');
    }

    public function print(Quote $quote)
    {
        $quote->load(['customer', 'items', 'terms']);
        return view('quotes.print', compact('quote'));
    }
}
