<?php

namespace App\Http\Controllers;

use App\Models\ServiceTemplate;
use Illuminate\Http\Request;

class ServiceTemplateController extends Controller
{
    public function index()
    {
        $serviceTemplates = ServiceTemplate::orderBy('created_at', 'desc')->paginate(10);
        return view('service-templates.index', compact('serviceTemplates'));
    }

    public function create()
    {
        $serviceTemplate = new ServiceTemplate();
        return view('service-templates.create', compact('serviceTemplate'));
    }

    public function store(Request $request)
    {
        \Log::info('Service Template Create Request', [
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'details_ar' => 'nullable|array',
            'details_ar.*' => 'string',
            'details_en' => 'nullable|array',
            'details_en.*' => 'string',
            'icon' => 'required|string|max:50',
            'default_price' => 'required|numeric|min:0',
            'currency' => 'required|in:USD,SAR,EGP,AUD',
            'subscription_type' => 'required|in:one_time,monthly,every_6_months,yearly',
            'is_active' => 'boolean',
            'is_vat_free' => 'boolean'
        ]);

        \Log::info('Service Template Validation Passed', [
            'validated_data' => $validated
        ]);

        try {
            // Filter out empty details and null values
            $validated['details_ar'] = array_values(array_filter($validated['details_ar'] ?? [], function($detail) {
                return !is_null($detail) && trim($detail) !== '';
            }));
            
            $validated['details_en'] = array_values(array_filter($validated['details_en'] ?? [], function($detail) {
                return !is_null($detail) && trim($detail) !== '';
            }));

            // If all details are empty, set to empty array instead of null
            $validated['details_ar'] = $validated['details_ar'] ?: [];
            $validated['details_en'] = $validated['details_en'] ?: [];

            // Set default values for boolean fields
            $validated['is_active'] = $validated['is_active'] ?? false;
            $validated['is_vat_free'] = $validated['is_vat_free'] ?? false;

            \Log::info('Service Template Details Filtered', [
                'filtered_details' => [
                    'ar' => $validated['details_ar'],
                    'en' => $validated['details_en']
                ],
                'vat_status' => $validated['is_vat_free']
            ]);

            ServiceTemplate::create($validated);

            \Log::info('Service Template Created Successfully', [
                'new_data' => ServiceTemplate::latest()->first()->toArray()
            ]);

            return redirect()->route('service-templates.index')
                ->with('success', 'Service template created successfully.');
        } catch (\Exception $e) {
            \Log::error('Service Template Create Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['create_error' => 'Failed to create service template. Please try again.']);
        }
    }

    public function edit(ServiceTemplate $serviceTemplate)
    {
        return view('service-templates.edit', compact('serviceTemplate'));
    }

    public function update(Request $request, ServiceTemplate $serviceTemplate)
    {
        \Log::info('Service Template Update Request', [
            'id' => $serviceTemplate->id,
            'request_data' => $request->all(),
            'original_details' => [
                'ar' => $serviceTemplate->details_ar,
                'en' => $serviceTemplate->details_en
            ]
        ]);

        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'details_ar' => 'nullable|array',
            'details_ar.*' => 'string',
            'details_en' => 'nullable|array',
            'details_en.*' => 'string',
            'icon' => 'required|string|max:50',
            'default_price' => 'required|numeric|min:0',
            'currency' => 'required|in:USD,SAR,EGP,AUD',
            'subscription_type' => 'required|in:one_time,monthly,every_6_months,yearly',
            'is_active' => 'boolean',
            'is_vat_free' => 'boolean'
        ]);

        \Log::info('Service Template Validation Passed', [
            'validated_data' => $validated
        ]);

        try {
            // Filter out empty details and null values
            $validated['details_ar'] = array_values(array_filter($validated['details_ar'] ?? [], function($detail) {
                return !is_null($detail) && trim($detail) !== '';
            }));
            
            $validated['details_en'] = array_values(array_filter($validated['details_en'] ?? [], function($detail) {
                return !is_null($detail) && trim($detail) !== '';
            }));

            // If all details are empty, set to empty array instead of null
            $validated['details_ar'] = $validated['details_ar'] ?: [];
            $validated['details_en'] = $validated['details_en'] ?: [];

            // Set default values for boolean fields
            $validated['is_active'] = $validated['is_active'] ?? false;
            $validated['is_vat_free'] = $validated['is_vat_free'] ?? false;

            \Log::info('Service Template Details Filtered', [
                'filtered_details' => [
                    'ar' => $validated['details_ar'],
                    'en' => $validated['details_en']
                ],
                'vat_status' => $validated['is_vat_free']
            ]);

            $serviceTemplate->update($validated);

            \Log::info('Service Template Updated Successfully', [
                'id' => $serviceTemplate->id,
                'new_data' => $serviceTemplate->fresh()->toArray()
            ]);

            return redirect()->route('service-templates.index')
                ->with('success', 'Service template updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Service Template Update Failed', [
                'id' => $serviceTemplate->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['update_error' => 'Failed to update service template. Please try again.']);
        }
    }

    public function destroy(ServiceTemplate $serviceTemplate)
    {
        \Log::info('Service Template Delete Request', [
            'id' => $serviceTemplate->id
        ]);

        try {
            $serviceTemplate->delete();

            \Log::info('Service Template Deleted Successfully', [
                'id' => $serviceTemplate->id
            ]);

            return redirect()->route('service-templates.index')
                ->with('success', 'Service template deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Service Template Delete Failed', [
                'id' => $serviceTemplate->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withErrors(['delete_error' => 'Failed to delete service template. Please try again.']);
        }
    }

    /**
     * Duplicate an existing service template
     */
    public function duplicate(ServiceTemplate $serviceTemplate)
    {
        try {
            // Create a duplicate with modified names
            $duplicateData = $serviceTemplate->toArray();
            
            // Remove timestamps and id
            unset($duplicateData['id'], $duplicateData['created_at'], $duplicateData['updated_at']);
            
            // Add "Copy of" prefix to names with timestamp to ensure uniqueness
            $timestamp = now()->format('Y-m-d H:i');
            $duplicateData['name_ar'] = 'نسخة من ' . $serviceTemplate->name_ar . ' (' . $timestamp . ')';
            $duplicateData['name_en'] = 'Copy of ' . $serviceTemplate->name_en . ' (' . $timestamp . ')';
            
            // Set as inactive by default for review
            $duplicateData['is_active'] = false;
            
            $duplicate = ServiceTemplate::create($duplicateData);
            
            \Log::info('Service template duplicated successfully', [
                'original_id' => $serviceTemplate->id,
                'duplicate_id' => $duplicate->id,
                'original_name' => $serviceTemplate->name_en,
                'duplicate_name' => $duplicate->name_en
            ]);
            
            return redirect()
                ->route('service-templates.edit', $duplicate)
                ->with('success', 'Service template duplicated successfully! Please review and activate when ready.');
                
        } catch (\Exception $e) {
            \Log::error('Failed to duplicate service template: ' . $e->getMessage(), [
                'service_template_id' => $serviceTemplate->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to duplicate service template: ' . $e->getMessage());
        }
    }
}
