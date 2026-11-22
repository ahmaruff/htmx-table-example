@php($field = 'w-full rounded-md border border-gray-300 bg-white px-2 py-1 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500')

<tr class="htmx-table-tr bg-gray-50">
    @foreach ($columns as $column)
        @php($key = $column->getKey())

        <td class="htmx-table-td">
            @switch($key)
                @case('id')
                    <span class="text-sm text-gray-500">{{ $item->id }}</span>
                    @break

                @case('sku')
                    <input name="sku" value="{{ old('sku', $item->sku) }}" class="{{ $field }} min-w-32">
                    @error('sku') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                    @break

                @case('name')
                    <input name="name" value="{{ old('name', $item->name) }}" class="{{ $field }} min-w-36">
                    @error('name') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                    @break

                @case('description')
                    <input name="description" value="{{ old('description', $item->description) }}" class="{{ $field }} min-w-56">
                    @error('description') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                    @break

                @case('category')
                    <select name="category" class="{{ $field }} min-w-36">
                        @foreach (\App\Models\Item::CATEGORIES as $cat)
                            <option value="{{ $cat }}" @selected(old('category', $item->category) === $cat)>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                    @break

                @case('brand')
                    <select name="brand" class="{{ $field }} min-w-32">
                        @foreach (\App\Models\Item::BRANDS as $brand)
                            <option value="{{ $brand }}" @selected(old('brand', $item->brand) === $brand)>{{ $brand }}</option>
                        @endforeach
                    </select>
                    @error('brand') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                    @break

                @case('price')
                    <input name="price" type="number" step="0.01" min="0" value="{{ old('price', $item->price) }}" class="{{ $field }} min-w-28">
                    @error('price') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                    @break

                @case('stock')
                    <input name="stock" type="number" step="1" min="0" value="{{ old('stock', $item->stock) }}" class="{{ $field }} min-w-24">
                    @error('stock') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                    @break

                @case('unit')
                    <select name="unit" class="{{ $field }} min-w-24">
                        @foreach (\App\Models\Item::UNITS as $unit)
                            <option value="{{ $unit }}" @selected(old('unit', $item->unit) === $unit)>{{ $unit }}</option>
                        @endforeach
                    </select>
                    @error('unit') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                    @break

                @case('status')
                    <select name="status" class="{{ $field }} min-w-32">
                        @foreach (\App\Models\Item::STATUSES as $status)
                            <option value="{{ $status }}" @selected(old('status', $item->status) === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('status') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                    @break

                @case('is_active')
                    <input type="hidden" name="is_active" value="0">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        @checked(old('is_active', $item->is_active))
                    >
                    @error('is_active') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                    @break

                @case('weight')
                    <input name="weight" type="number" step="0.01" min="0" value="{{ old('weight', $item->weight) }}" class="{{ $field }} min-w-24">
                    @error('weight') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                    @break

                @case('manufactured_at')
                    <input name="manufactured_at" type="date" value="{{ old('manufactured_at', $item->manufactured_at?->format('Y-m-d')) }}" class="{{ $field }} min-w-40">
                    @error('manufactured_at') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                    @break

                @case('actions')
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="flex flex-col items-end gap-1">
                        <div class="flex items-center gap-1.5">
                            <button
                                type="button"
                                hx-put="{{ route('items.update', $item) }}"
                                hx-include="closest tr"
                                hx-validate="true"
                                hx-target="closest tr"
                                hx-swap="outerHTML"
                                hx-disabled-elt="this"
                                class="inline-flex items-center rounded-md bg-gray-900 px-3 py-1 text-xs font-medium text-white hover:bg-gray-700 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                Simpan
                                <span class="htmx-indicator ml-1">…</span>
                            </button>
                            <button
                                type="button"
                                hx-get="{{ route('items.row', $item) }}"
                                hx-target="closest tr"
                                hx-swap="outerHTML"
                                class="rounded-md border border-gray-300 px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-100"
                            >
                                Batal
                            </button>
                        </div>
                        @if ($errors->any())
                            <span class="text-xs text-red-600">Periksa input yang error.</span>
                        @endif
                    </div>
                    @break

                @default
                    <span class="text-sm">{{ $column->resolveCell($item) }}</span>
            @endswitch
        </td>
    @endforeach
</tr>
