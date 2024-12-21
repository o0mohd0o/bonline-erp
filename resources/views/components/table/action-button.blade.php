@props([
    'href' => null,
    'method' => 'GET',
    'icon' => null,
    'title' => '',
    'variant' => 'light',
    'textColor' => null,
    'confirm' => false,
    'confirmMessage' => 'Are you sure you want to perform this action?'
])

@php
    $tag = $href ? 'a' : 'button';
    $type = $tag === 'button' ? 'submit' : null;
    $classes = [
        'btn',
        'btn-sm',
        'btn-' . $variant,
        $textColor ? 'text-' . $textColor : null
    ];
@endphp

@if($method === 'GET')
    <{{ $tag }}
        href="{{ $href }}"
        {{ $attributes->merge(['class' => implode(' ', array_filter($classes))]) }}
        @if($title) title="{{ $title }}" @endif
    >
        @if($icon)<i class="fas fa-{{ $icon }}"></i>@endif
        {{ $slot }}
    </{{ $tag }}>
@else
    <form action="{{ $href }}" method="POST" class="d-inline">
        @csrf
        @method($method)
        <button
            type="{{ $type }}"
            {{ $attributes->merge(['class' => implode(' ', array_filter($classes))]) }}
            @if($title) title="{{ $title }}" @endif
            @if($confirm) onclick="return confirm('{{ $confirmMessage }}')" @endif
        >
            @if($icon)<i class="fas fa-{{ $icon }}"></i>@endif
            {{ $slot }}
        </button>
    </form>
@endif
