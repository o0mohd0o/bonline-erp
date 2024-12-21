@props([
    'filters' => [],
    'searchPlaceholder' => 'Search...'
])

<div class="filters-wrapper mb-3">
    <!-- Search Bar -->
    <div class="d-flex gap-2 align-items-center mb-2">
        <div class="flex-grow-1">
            <div class="search-box">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-primary"></i>
                    </span>
                    <input 
                        type="search" 
                        class="form-control border-start-0 ps-0" 
                        id="searchInput" 
                        placeholder="{{ $searchPlaceholder }}"
                    >
                </div>
            </div>
        </div>
        
        <!-- Filter Toggle Button (for mobile) -->
        <button 
            class="btn btn-light d-md-none shadow-sm" 
            type="button" 
            data-bs-toggle="collapse" 
            data-bs-target="#filterCollapse"
        >
            <i class="fas fa-filter"></i>
        </button>
    </div>

    <!-- Collapsible Filters -->
    <div class="collapse d-md-block" id="filterCollapse">
        <div class="active-filters d-flex flex-wrap gap-2 mb-2" style="min-height: 32px;">
            <!-- Active filters will be inserted here via JavaScript -->
        </div>

        <div class="filter-options bg-white rounded shadow-sm border">
            <div class="row g-2 p-2">
                @foreach($filters as $filter)
                <div class="col-md-3">
                    <div class="filter-group">
                        <label class="form-label text-muted small mb-1">{{ $filter['placeholder'] }}</label>
                        <select 
                            class="form-select form-select-sm shadow-none border filter" 
                            id="{{ $filter['id'] }}"
                            data-placeholder="{{ $filter['placeholder'] }}"
                        >
                            <option value="">{{ $filter['placeholder'] }}</option>
                            @foreach($filter['options'] as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
.search-box .input-group {
    transition: all 0.2s ease;
}
.search-box .input-group:focus-within {
    transform: translateY(-1px);
    box-shadow: 0 .25rem .5rem rgba(0,0,0,.05) !important;
}
.search-box .form-control:focus {
    box-shadow: none;
}
.active-filters .filter-tag {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    background: var(--bs-primary);
    color: white;
    border-radius: 50rem;
    font-size: 0.8125rem;
    transition: all 0.2s ease;
}
.active-filters .filter-tag:hover {
    transform: translateY(-1px);
}
.active-filters .filter-tag .remove-filter {
    margin-left: 0.35rem;
    cursor: pointer;
    opacity: 0.8;
    transition: opacity 0.2s ease;
}
.active-filters .filter-tag .remove-filter:hover {
    opacity: 1;
}
.filter-options {
    transition: all 0.2s ease;
}
.filter-options:hover {
    box-shadow: 0 .25rem .5rem rgba(0,0,0,.05) !important;
}
.form-select {
    padding-top: 0.3rem;
    padding-bottom: 0.3rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const filterSelects = [
        @foreach($filters as $filter)
            document.getElementById('{{ $filter['id'] }}'),
        @endforeach
    ].filter(Boolean);
    const tableRows = document.querySelectorAll('tbody tr');
    const activeFiltersContainer = document.querySelector('.active-filters');

    function updateActiveFilters() {
        activeFiltersContainer.innerHTML = '';
        
        // Add search filter tag if search is active
        if (searchInput?.value) {
            const searchTag = createFilterTag('Search', searchInput.value, () => {
                searchInput.value = '';
                filterTable();
            });
            activeFiltersContainer.appendChild(searchTag);
        }

        // Add filter tags for active select filters
        filterSelects.forEach(select => {
            if (select?.value) {
                const selectedOption = select.options[select.selectedIndex];
                const placeholder = select.dataset.placeholder;
                const filterTag = createFilterTag(
                    placeholder, 
                    selectedOption.text, 
                    () => {
                        select.value = '';
                        filterTable();
                    }
                );
                activeFiltersContainer.appendChild(filterTag);
            }
        });
    }

    function createFilterTag(label, value, onRemove) {
        const tag = document.createElement('div');
        tag.className = 'filter-tag';
        tag.innerHTML = `
            <span class="me-1 opacity-75">${label}:</span>
            <span class="fw-medium">${value}</span>
            <span class="remove-filter" role="button">
                <i class="fas fa-times ms-1"></i>
            </span>
        `;
        tag.querySelector('.remove-filter').addEventListener('click', onRemove);
        return tag;
    }

    function isMatch(filterId, cellValue, filterValue) {
        // Convert both values to lowercase for case-insensitive comparison
        cellValue = (cellValue || '').toLowerCase();
        filterValue = (filterValue || '').toLowerCase();

        // Handle different types of filters
        switch(filterId) {
            case 'dateFilter':
            case 'amountFilter':
            case 'statusFilter':
            case 'currencyFilter':
                // Exact match for dates, amounts, status, and currency
                return cellValue === filterValue;
            
            default:
                // Partial match for text-based filters (receipt number, customer)
                return cellValue.includes(filterValue);
        }
    }

    function filterTable() {
        console.log('Starting filter operation...');
        
        const searchTerm = (searchInput?.value || '').toLowerCase();
        console.log('Search term:', searchTerm);
        
        const activeFilters = filterSelects
            .filter(select => select?.value)
            .map(select => {
                console.log(`Filter ${select.id}:`, select.value);
                return {
                    id: select.id.toLowerCase(),
                    value: select.value
                };
            });
            
        console.log('Active filters:', activeFilters);

        tableRows.forEach((row, index) => {
            let show = true;
            console.log(`\nChecking row ${index + 1}`);

            // Check filters
            for (const filter of activeFilters) {
                const dataAttr = `data-${filter.id.toLowerCase()}`;
                const cell = row.querySelector(`[${dataAttr}]`);
                
                if (!cell) {
                    console.log(`No cell found for filter: ${filter.id}`);
                    console.log('Looking for:', dataAttr);
                    continue;
                }
                
                const cellValue = cell.getAttribute(dataAttr);
                const filterValue = filter.value;
                
                console.log(`\nChecking filter: ${filter.id}`);
                console.log(`Cell value: "${cellValue}"`);
                console.log(`Filter value: "${filterValue}"`);
                console.log(`Cell HTML:`, cell.outerHTML);
                
                const matched = isMatch(filter.id, cellValue, filterValue);
                console.log(`Match result: ${matched}`);
                
                if (!matched) {
                    show = false;
                    console.log(`Row ${index + 1} hidden due to filter: ${filter.id}`);
                    break;
                }
            }

            // Check search term
            if (show && searchTerm) {
                const searchCells = row.querySelectorAll('[data-search]');
                show = Array.from(searchCells).some(cell => {
                    const searchValue = (cell.getAttribute('data-search') || '').toLowerCase();
                    return searchValue.includes(searchTerm);
                });
                
                if (!show) {
                    console.log(`Row ${index + 1} hidden due to search term`);
                }
            }

            row.style.display = show ? '' : 'none';
            console.log(`Row ${index + 1} final visibility:`, show ? 'visible' : 'hidden');
        });

        updateActiveFilters();
    }

    // Event Listeners
    searchInput?.addEventListener('input', filterTable);
    filterSelects.forEach(select => {
        select?.addEventListener('change', filterTable);
    });

    // Initialize
    updateActiveFilters();
});
</script>
</div>
