@props([
    'align' => 'end'
])

<div @class([
    'd-flex',
    'justify-content-' . $align,
    'gap-2'
])>
    {{ $slot }}
</div>
