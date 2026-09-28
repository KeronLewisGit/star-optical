@props(['status'])
@php
    $map = [
        'new' => 'bg-blue-100 text-blue-800',
        'contacted' => 'bg-amber-100 text-amber-800',
        'scheduled' => 'bg-purple-100 text-purple-800',
        'completed' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-gray-200 text-gray-700',
        'active' => 'bg-green-100 text-green-800',
        'inactive' => 'bg-gray-200 text-gray-700',
        'admin' => 'bg-gold-soft text-gold-dark',
        'staff' => 'bg-gray-100 text-gray-700',
    ];
@endphp
<span {{ $attributes->merge(['class' => 'badge '.($map[$status] ?? 'bg-gray-100 text-gray-700')]) }}>{{ ucfirst($status) }}</span>
