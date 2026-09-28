@php $editing = $promotion->exists; @endphp
<x-admin-layout :title="$editing ? 'Edit promotion' : 'New promotion'">
    <form method="POST" action="{{ $editing ? route('admin.promotions.update', $promotion) : route('admin.promotions.store') }}" enctype="multipart/form-data" class="grid lg:grid-cols-3 gap-6" data-once>
        @csrf
        @if($editing)@method('PUT')@endif
        <div class="lg:col-span-2 card"><div class="card-body space-y-4">
            <x-form.input name="title" label="Title" :value="$promotion->title" required help="Shown large on the poster, e.g. “Buy 1 frame, get the 2nd 50% off”." />
            <div class="grid sm:grid-cols-2 gap-4">
                <x-form.input name="kicker" label="Small label" :value="$promotion->kicker" placeholder="Limited time" />
                <x-form.input name="cta_text" label="Button text" :value="$promotion->cta_text" required />
            </div>
            <x-form.textarea name="body" label="Short description" :value="$promotion->body" rows="2" />
            <div>
                <label class="form-label">Poster image</label>
                @if($promotion->image_url)
                    <div class="flex items-center gap-3 mb-2"><img src="{{ $promotion->image_url }}" alt="" class="h-20 w-20 rounded-lg object-cover"><label class="text-sm"><input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300"> Remove current image</label></div>
                @endif
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-soft file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand">
                <p class="form-help">JPG, PNG or WebP, at least 400×400, max 2 MB. Portrait (4:5) works best. Leave blank to use a stock photo.</p>
                @error('image')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div></div>
        <div class="space-y-6">
            <div class="card"><div class="card-body space-y-4">
                <x-form.select name="theme" label="Colour theme" :options="\App\Models\Promotion::THEMES" :value="$promotion->theme" required />
                <x-form.input name="sort_order" label="Order" type="number" :value="$promotion->sort_order ?? 0" min="0" max="999" help="Lower numbers show first." />
                <x-form.input name="starts_at" label="Starts" type="date" :value="$promotion->starts_at?->format('Y-m-d')" />
                <x-form.input name="ends_at" label="Ends" type="date" :value="$promotion->ends_at?->format('Y-m-d')" />
                <label class="flex items-center gap-2 text-sm"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-brand" @checked(old('is_active', $promotion->is_active))> Active</label>
            </div></div>
            <div class="flex gap-2"><button class="btn-primary">{{ $editing ? 'Save' : 'Create' }}</button><a href="{{ route('admin.promotions.index') }}" class="btn-secondary">Cancel</a></div>
        </div>
    </form>
</x-admin-layout>
