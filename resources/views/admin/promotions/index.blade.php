<x-admin-layout title="Promotions">
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-600">These appear in the promotions carousel on the website. Only active promotions within their dates are shown.</p>
        <a href="{{ route('admin.promotions.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> New promotion</a>
    </div>
    <div class="card overflow-x-auto">
        <table class="table">
            <thead><tr><th>Order</th><th>Promotion</th><th>Theme</th><th>Dates</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($promotions as $p)
                @php $live = $p->is_active && (! $p->starts_at || $p->starts_at->lte(today())) && (! $p->ends_at || $p->ends_at->gte(today())); @endphp
                <tr>
                    <td class="text-gray-500">{{ $p->sort_order }}</td>
                    <td class="flex items-center gap-3">
                        @if($p->image_url)<img src="{{ $p->image_url }}" alt="" class="h-12 w-12 rounded-lg object-cover">@else<span class="grid h-12 w-12 place-items-center rounded-lg bg-brand-soft text-brand"><i class="fa-solid fa-image"></i></span>@endif
                        <div><a href="{{ route('admin.promotions.edit', $p) }}" class="font-medium text-gray-900 hover:text-brand">{{ $p->title }}</a>@if($p->kicker)<br><span class="text-xs text-gray-500">{{ $p->kicker }}</span>@endif</div>
                    </td>
                    <td><span class="badge bg-gray-100 text-gray-700">{{ $p->theme }}</span></td>
                    <td class="text-gray-500 whitespace-nowrap">{{ $p->starts_at?->format('j M Y') ?? '—' }} → {{ $p->ends_at?->format('j M Y') ?? 'no end' }}</td>
                    <td>@if($live)<span class="badge bg-green-100 text-green-800">Live</span>@elseif($p->is_active)<span class="badge bg-amber-100 text-amber-800">Outside dates</span>@else<span class="badge bg-gray-200 text-gray-700">Inactive</span>@endif</td>
                    <td class="text-right whitespace-nowrap">
                        <a href="{{ route('admin.promotions.edit', $p) }}" class="btn-secondary btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.promotions.destroy', $p) }}" class="inline" data-confirm="Delete this promotion?">@csrf @method('DELETE')<button class="btn-danger btn-sm">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-gray-500 py-10">No promotions yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
