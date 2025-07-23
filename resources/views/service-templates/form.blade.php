@php
    $icons = [
        'fas fa-server' => 'Server',
        'fas fa-shield-virus' => 'Security',
        'fas fa-lock' => 'Lock',
        'fas fa-globe' => 'Globe',
        'fas fa-code' => 'Code',
        'fas fa-database' => 'Database',
        'fas fa-cloud' => 'Cloud',
        'fas fa-cogs' => 'Settings',
        'fas fa-tools' => 'Tools',
        'fas fa-laptop-code' => 'Development',
        'fas fa-mobile-alt' => 'Mobile',
        'fas fa-network-wired' => 'Network',
        'fas fa-envelope' => 'Email',
        'fas fa-chart-line' => 'Analytics',
        'fas fa-search' => 'Search',
        'fas fa-users' => 'Users',
        'fas fa-file-alt' => 'Document',
        'fas fa-check' => 'Check',
        'fas fa-clock' => 'Time',
        'fas fa-calendar' => 'Calendar',
        'fas fa-shopping-cart' => 'E-commerce',
    ];
@endphp

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row g-4">
            <!-- Icon Selection -->
            <div class="col-md-12">
                <label class="form-label">Icon</label>
                <div class="d-flex flex-wrap gap-2 border rounded p-3">
                    @foreach($icons as $class => $name)
                        <div class="form-check">
                            <input type="radio" 
                                   class="btn-check" 
                                   name="icon" 
                                   id="icon_{{ $loop->index }}" 
                                   value="{{ $class }}"
                                   autocomplete="off"
                                   {{ old('icon', $serviceTemplate->icon ?? '') === $class ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary" 
                                   for="icon_{{ $loop->index }}" 
                                   title="{{ $name }}">
                                <i class="{{ $class }}"></i>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('icon')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Arabic Name -->
            <div class="col-md-6">
                <label class="form-label">Name (Arabic)</label>
                <input type="text" 
                       class="form-control @error('name_ar') is-invalid @enderror" 
                       name="name_ar" 
                       value="{{ old('name_ar', $serviceTemplate->name_ar ?? '') }}" 
                       required>
                @error('name_ar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- English Name -->
            <div class="col-md-6">
                <label class="form-label">Name (English)</label>
                <input type="text" 
                       class="form-control @error('name_en') is-invalid @enderror" 
                       name="name_en" 
                       value="{{ old('name_en', $serviceTemplate->name_en ?? '') }}" 
                       required>
                @error('name_en')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Arabic Description -->
            <div class="col-md-6">
                <label class="form-label">Description (Arabic)</label>
                <textarea class="form-control @error('description_ar') is-invalid @enderror" 
                          name="description_ar" 
                          rows="3">{{ old('description_ar', $serviceTemplate->description_ar ?? '') }}</textarea>
                @error('description_ar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- English Description -->
            <div class="col-md-6">
                <label class="form-label">Description (English)</label>
                <textarea class="form-control @error('description_en') is-invalid @enderror" 
                          name="description_en" 
                          rows="3">{{ old('description_en', $serviceTemplate->description_en ?? '') }}</textarea>
                @error('description_en')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Arabic Details -->
            <div class="col-md-6">
                <label class="form-label">Details (Arabic)</label>
                <div class="details-wrapper">
                    <div class="details-container">
                        @if(old('details_ar', $serviceTemplate->details_ar ?? []))
                            @foreach(old('details_ar', $serviceTemplate->details_ar) as $detail)
                                <div class="input-group mb-2">
                                    <input type="text" 
                                           class="form-control" 
                                           name="details_ar[]" 
                                           value="{{ $detail }}">
                                    <button type="button" class="btn btn-outline-danger remove-detail">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="details_ar[]">
                                <button type="button" class="btn btn-outline-danger remove-detail">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm add-detail" data-target="ar">
                        <i class="fas fa-plus"></i> Add Detail
                    </button>
                </div>
                @error('details_ar')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- English Details -->
            <div class="col-md-6">
                <label class="form-label">Details (English)</label>
                <div class="details-wrapper">
                    <div class="details-container">
                        @if(old('details_en', $serviceTemplate->details_en ?? []))
                            @foreach(old('details_en', $serviceTemplate->details_en) as $detail)
                                <div class="input-group mb-2">
                                    <input type="text" 
                                           class="form-control" 
                                           name="details_en[]" 
                                           value="{{ $detail }}">
                                    <button type="button" class="btn btn-outline-danger remove-detail">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="details_en[]">
                                <button type="button" class="btn btn-outline-danger remove-detail">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm add-detail" data-target="en">
                        <i class="fas fa-plus"></i> Add Detail
                    </button>
                </div>
                @error('details_en')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Price -->
            <div class="col-md-6">
                <label class="form-label">Default Price</label>
                <input type="number" 
                       class="form-control @error('default_price') is-invalid @enderror" 
                       name="default_price" 
                       value="{{ old('default_price', $serviceTemplate->default_price ?? '') }}" 
                       step="0.01" 
                       min="0" 
                       required>
                @error('default_price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Currency -->
            <div class="col-md-6">
                <label class="form-label">Currency</label>
                <select class="form-select @error('currency') is-invalid @enderror" 
                        name="currency" 
                        required>
                    <option value="">Select Currency</option>
                    <option value="USD" {{ old('currency', $serviceTemplate->currency ?? '') === 'USD' ? 'selected' : '' }}>USD</option>
                    <option value="SAR" {{ old('currency', $serviceTemplate->currency ?? '') === 'SAR' ? 'selected' : '' }}>SAR</option>
                    <option value="EGP" {{ old('currency', $serviceTemplate->currency ?? '') === 'EGP' ? 'selected' : '' }}>EGP</option>
                    <option value="AUD" {{ old('currency', $serviceTemplate->currency ?? '') === 'AUD' ? 'selected' : '' }}>AUD</option>
                </select>
                @error('currency')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Subscription Type -->
            <div class="col-md-6">
                <label class="form-label">Subscription Type</label>
                <select class="form-select @error('subscription_type') is-invalid @enderror" 
                        name="subscription_type" 
                        required>
                    <option value="">Select Subscription Type</option>
                    <option value="one_time" {{ old('subscription_type', $serviceTemplate->subscription_type ?? '') === 'one_time' ? 'selected' : '' }}>One Time</option>
                    <option value="monthly" {{ old('subscription_type', $serviceTemplate->subscription_type ?? '') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="every_6_months" {{ old('subscription_type', $serviceTemplate->subscription_type ?? '') === 'every_6_months' ? 'selected' : '' }}>Every 6 Months</option>
                    <option value="yearly" {{ old('subscription_type', $serviceTemplate->subscription_type ?? '') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                </select>
                @error('subscription_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-4">
            <!-- Status -->
            <div class="col-md-12">
                <div class="d-flex gap-4">
                    <div class="form-check form-switch">
                        <input type="checkbox" 
                               class="form-check-input" 
                               name="is_active" 
                               id="is_active" 
                               value="1" 
                               {{ old('is_active', $serviceTemplate->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                    <div class="form-check form-switch">
                        <input type="checkbox" 
                               class="form-check-input" 
                               name="is_vat_free" 
                               id="is_vat_free" 
                               value="1" 
                               {{ old('is_vat_free', $serviceTemplate->is_vat_free ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_vat_free">VAT Free (for services outside Egypt)</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const debug = {
        log: function(action, data) {
            const timestamp = new Date().toISOString();
            console.group(`%c Service Template Debug: ${action} [${timestamp}]`, 'color: #0066cc; font-weight: bold;');
            console.log('%c Data:', 'color: #666666', data);
            console.trace('Call Stack:');
            console.groupEnd();
        },
        error: function(action, error, context = {}) {
            console.group(`%c Service Template Error: ${action}`, 'color: #ff0000; font-weight: bold;');
            console.error('Error:', error);
            console.log('Context:', context);
            console.trace('Error Stack:');
            console.groupEnd();
        }
    };

    // Log initial state
    debug.log('Initial State', {
        form: {
            found: $('form').length > 0,
            action: $('form').attr('action'),
            method: $('form').attr('method')
        },
        buttons: {
            addButtons: $('.add-detail').map(function() {
                return {
                    target: $(this).data('target'),
                    html: this.outerHTML
                };
            }).get(),
            removeButtons: $('.remove-detail').map(function() {
                return {
                    html: this.outerHTML,
                    parentInput: $(this).closest('.input-group').find('input').attr('name')
                };
            }).get()
        },
        containers: $('.details-container').map(function() {
            return {
                fields: $(this).find('.input-group').length,
                html: this.outerHTML
            };
        }).get()
    });

    // Add detail field
    $('.add-detail').on('click', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const target = $btn.data('target');
        const $wrapper = $btn.closest('.details-wrapper');
        const $container = $wrapper.find('.details-container');

        debug.log('Add Detail Clicked', {
            target: target,
            wrapperFound: $wrapper.length > 0,
            containerFound: $container.length > 0,
            buttonHtml: $btn[0].outerHTML
        });

        if (!$container.length) {
            debug.error('Container Not Found', 'Container element missing', {
                target: target,
                wrapperHtml: $wrapper[0]?.outerHTML,
                buttonHtml: $btn[0].outerHTML
            });
            return;
        }

        const newField = `
            <div class="input-group mb-2">
                <input type="text" class="form-control" name="details_${target}[]">
                <button type="button" class="btn btn-outline-danger remove-detail">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        $container.append(newField);
        
        debug.log('Detail Added Successfully', {
            newHtml: newField,
            containerChildrenCount: $container.find('.input-group').length,
            allInputs: $container.find('input').map(function() {
                return {
                    name: $(this).attr('name'),
                    value: $(this).val()
                };
            }).get()
        });
    });

    // Remove detail field
    $(document).on('click', '.remove-detail', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const $btn = $(this);
        const $inputGroup = $btn.closest('.input-group');
        const $container = $inputGroup.closest('.details-container');
        const $input = $inputGroup.find('input');

        debug.log('Remove Button Clicked', {
            buttonHtml: $btn[0].outerHTML,
            inputValue: $input.val(),
            containerChildrenCount: $container.find('.input-group').length,
            eventTarget: e.target.tagName
        });

        if (!$inputGroup.length || !$container.length) {
            debug.error('Remove Detail Failed', 'Required elements not found', {
                inputGroupFound: $inputGroup.length > 0,
                containerFound: $container.length > 0,
                buttonHtml: $btn[0].outerHTML
            });
            return;
        }

        if ($container.find('.input-group').length > 1) {
            $inputGroup.remove();
            debug.log('Detail Removed', {
                remainingFields: $container.find('.input-group').length,
                containerHtml: $container[0].outerHTML
            });
        } else {
            $input.val('');
            debug.log('Last Detail Cleared', {
                inputField: $input[0].outerHTML,
                containerHtml: $container[0].outerHTML
            });
        }
    });

    // Form submission
    $('form').on('submit', function(e) {
        const $form = $(this);
        const formData = new FormData(this);
        
        debug.log('Form Submission', {
            action: $form.attr('action'),
            method: $form.attr('method'),
            details: {
                ar: $form.find('input[name="details_ar[]"]').map(function() {
                    return $(this).val();
                }).get(),
                en: $form.find('input[name="details_en[]"]').map(function() {
                    return $(this).val();
                }).get()
            }
        });
    });

    // Log all click events on the form
    $('form').on('click', function(e) {
        debug.log('Click Event', {
            target: e.target.tagName,
            targetClasses: e.target.className,
            targetId: e.target.id,
            closestButton: $(e.target).closest('button').prop('outerHTML'),
            path: $(e.target).parents().map(function() {
                return {
                    tag: this.tagName,
                    classes: this.className,
                    id: this.id
                };
            }).get()
        });
    });
});
</script>
@endpush
