<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Helpers\NumberToWordsHelper;

class ReceiptController extends Controller
{
    // Display a listing of receipts
    public function index()
    {
        $receipts = Receipt::with('customer')->get();
        return view('receipts.index', compact('receipts'));
    }

    // Show the form for creating a new receipt
    public function create()
    {
        $customers = Customer::all();
        return view('receipts.create', compact('customers'));
    }

    // Store a newly created receipt in storage
    public function store(Request $request)
    {
        $request->validate([
            'receipt_number' => 'required|numeric|digits:6|unique:receipts,receipt_number',
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|in:USD,EGP,SAR',
            'description' => 'nullable|string|max:255',
            'receipt_date' => 'required|date',
        ]);

        // Create the receipt
        Receipt::create($request->all());

        return redirect()->route('receipts.index')->with('success', 'Receipt created successfully.');
    }

    // Show a specific receipt
    public function show($id)
    {
        $receipt = Receipt::with('customer')->findOrFail($id);
        return view('receipts.show', compact('receipt'));
    }

    // Show the form for editing a receipt
    public function edit($id)
    {
        $receipt = Receipt::findOrFail($id);
        $customers = Customer::all();
        return view('receipts.edit', compact('receipt', 'customers'));
    }

    // Update a specific receipt in storage
    public function update(Request $request, $id)
    {
        $request->validate([
            'receipt_number' => 'required|numeric|digits:6|unique:receipts,receipt_number,' . $id,
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|in:USD,EGP,SAR',
            'description' => 'nullable|string|max:255',
            'receipt_date' => 'required|date',
        ]);

        $receipt = Receipt::findOrFail($id);
        $receipt->update($request->all());

        return redirect()->route('receipts.index')->with('success', 'Receipt updated successfully.');
    }

    // Remove a specific receipt from storage
    public function destroy($id)
    {
        $receipt = Receipt::findOrFail($id);
        $receipt->delete();

        return redirect()->route('receipts.index')->with('success', 'Receipt deleted successfully.');
    }

    public function print($id)
    {
        $receipt = Receipt::with('customer')->findOrFail($id);

        // Convert amount to Arabic words with currency
        $amountInWords = NumberToWordsHelper::convertToArabicWords($receipt->amount, $receipt->currency);

        return view('receipts.print', compact('receipt', 'amountInWords'));
    }
}
