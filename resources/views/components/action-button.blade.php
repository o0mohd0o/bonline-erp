@props([
    'href' => null,
    'method' => 'GET',
    'icon' => null,
    'variant' => 'primary',
    'outline' => false,
    'size' => null,
    'confirm' => false,
    'confirmMessage' => 'Are you sure you want to perform this action?'
])

@php
    $tag = $href ? 'a' : 'button';
    $type = $tag === 'button' ? 'submit' : null;
    $classes = [
        'btn',
        $size ? 'btn-' . $size : '',
        $outline ? 'btn-outline-' . $variant : 'btn-' . $variant
    ];
@endphp

@if($method === 'GET')
    <{{ $tag }}
        href="{{ $href }}"
        {{ $attributes->merge(['class' => implode(' ', array_filter($classes))]) }}
    >
        @if($icon)<i class="fas fa-{{ $icon }} me-2"></i>@endif
        {{ $slot }}
    </{{ $tag }}>
@else
    <form action="{{ $href }}" method="POST" class="d-inline">
        @csrf
        @method($method)
        <button
            type="{{ $type }}"
            {{ $attributes->merge(['class' => implode(' ', array_filter($classes))]) }}
            @if($confirm) onclick="return confirm('{{ $confirmMessage }}')" @endif
        >
            @if($icon)<i class="fas fa-{{ $icon }} me-2"></i>@endif
            {{ $slot }}
        </button>
    </form>
@endif
