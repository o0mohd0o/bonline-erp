<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // Display a listing of the customers
    public function index()
    {
        $customers = Customer::all();
        return view('customers.index', compact('customers'));
    }

    // Show the form for creating a new customer
    public function create()
    {
        return view('customers.create');
    }

    // Store a newly created customer in storage
    public function store(Request $request)
    {
        // Validate shared fields
        $request->validate([
            'customer_type' => 'required|in:individual,company',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        // Conditionally validate based on customer type
        if ($request->input('customer_type') === 'individual') {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
            ]);
        } else if ($request->input('customer_type') === 'company') {
            $request->validate([
                'company_name' => 'required|string|max:255',
                'contact_person_name' => 'required|string|max:255',
                'contact_person_phone' => 'nullable|string|max:15',
            ]);
        }

        // Prepare data without null values using array_filter
        $data = array_filter([
            'customer_type' => $request->input('customer_type'),
            'first_name' => $request->input('customer_type') === 'individual' ? $request->input('first_name') : null,
            'last_name' => $request->input('customer_type') === 'individual' ? $request->input('last_name') : null,
            'company_name' => $request->input('customer_type') === 'company' ? $request->input('company_name') : null,
            'contact_person_name' => $request->input('customer_type') === 'company' ? $request->input('contact_person_name') : null,
            'contact_person_phone' => $request->input('customer_type') === 'company' ? $request->input('contact_person_phone') : null,
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'status' => $request->input('status'),
        ], fn($value) => !is_null($value)); // Remove all null values

        // Save the customer
        Customer::create($data);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }
    

    // Display the specified customer
    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.show', compact('customer'));
    }

    // Show the form for editing the specified customer
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    // Update the specified customer in storage
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        // Validate shared fields
        $request->validate([
            'customer_type' => 'required|in:individual,company',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        // Conditionally validate based on customer type
        if ($request->input('customer_type') === 'individual') {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
            ]);
        } else if ($request->input('customer_type') === 'company') {
            $request->validate([
                'company_name' => 'required|string|max:255',
                'contact_person_name' => 'required|string|max:255',
                'contact_person_phone' => 'nullable|string|max:30',
            ]);
        }

        // Prepare data without null values using array_filter
        $data = array_filter([
            'customer_type' => $request->input('customer_type'),
            'first_name' => $request->input('customer_type') === 'individual' ? $request->input('first_name') : null,
            'last_name' => $request->input('customer_type') === 'individual' ? $request->input('last_name') : null,
            'company_name' => $request->input('customer_type') === 'company' ? $request->input('company_name') : null,
            'contact_person_name' => $request->input('customer_type') === 'company' ? $request->input('contact_person_name') : null,
            'contact_person_phone' => $request->input('customer_type') === 'company' ? $request->input('contact_person_phone') : null,
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'status' => $request->input('status'),
        ], fn($value) => !is_null($value)); // Remove all null values

        $customer->update($data);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    // Update the status of the specified customer in storage
    public function updateStatus(Request $request, Customer $customer)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $customer->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', 'Customer status updated successfully.');
    }

    // Remove the specified customer from storage
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
