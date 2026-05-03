@props(['status'])

@php
    $colors = [
        // Organizer
        'published' => 'bg-green-100 text-green-600',
        'draft' => 'bg-gray-100 text-gray-600',
        'completed' => 'bg-blue-100 text-blue-600',

        // Admin
        'accepted' => 'bg-green-100 text-green-600',
        'rejected' => 'bg-red-100 text-red-600',
        'pending' => 'bg-yellow-100 text-yellow-600',

        // Volunteer
        'upcoming' => 'bg-yellow-100 text-yellow-600',
    ];

    $class = $colors[$status] ?? 'bg-gray-100 text-gray-600';
@endphp

<span class="px-2 py-1 text-xs rounded {{ $class }}">
    {{ ucfirst($status) }}
</span>