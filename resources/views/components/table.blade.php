@props([
    'headers' => [],
    'rows' => [],
    'actions' => false,
    'responsive' => true,
    'hover' => true,
    'align' => 'middle'
])

<div @class(['table-responsive' => $responsive])>
    <table @class([
        'table',
        'table-hover' => $hover,
        'align-' . $align => $align,
        'mb-0'
    ])>
        <thead class="bg-light">
            <tr>
                @foreach($headers as $header)
                    <th @class([
                        'text-muted',
                        'small',
                        'fw-500',
                        'border-0',
                        'text-end' => $loop->last && $actions
                    ])>
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="border-top-0">
            {{ $slot }}
        </tbody>
    </table>
</div>

<style>
.fw-500 {
    font-weight: 500;
}
</style>
