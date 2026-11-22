@extends('layouts.app')

@section('title', 'Items')

@section('content')
    <div class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="mb-6 text-2xl font-semibold">Items</h1>

        <form
            class="mb-4 flex flex-wrap items-end gap-3"
            hx-get="{{ route('items.index') }}"
            hx-target="#{{ $table->getId() }}"
            hx-swap="outerHTML"
            hx-push-url="true"
            hx-trigger="change, submit"
        >
            <label class="flex flex-col gap-1 text-sm">
                <span class="font-medium text-gray-700">Category</span>
                <select
                    name="category"
                    class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                >
                    <option value="">All categories</option>
                    @foreach (\App\Models\Item::CATEGORIES as $category)
                        <option value="{{ $category }}" @selected($filters['category'] === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </label>

            <label class="flex flex-col gap-1 text-sm">
                <span class="font-medium text-gray-700">Brand</span>
                <select
                    name="brand"
                    class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                >
                    <option value="">All brands</option>
                    @foreach (\App\Models\Item::BRANDS as $brand)
                        <option value="{{ $brand }}" @selected($filters['brand'] === $brand)>{{ $brand }}</option>
                    @endforeach
                </select>
            </label>

            <label class="flex flex-col gap-1 text-sm">
                <span class="font-medium text-gray-700">Status</span>
                <select
                    name="status"
                    class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                >
                    <option value="">All statuses</option>
                    @foreach (\App\Models\Item::STATUSES as $status)
                        <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </label>

            <label class="flex flex-col gap-1 text-sm">
                <span class="font-medium text-gray-700">Active</span>
                <select
                    name="is_active"
                    class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                >
                    <option value="">All</option>
                    <option value="1" @selected($filters['is_active'] === '1')>Active</option>
                    <option value="0" @selected($filters['is_active'] === '0')>Inactive</option>
                </select>
            </label>

            <button
                type="submit"
                class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700"
            >
                Filter
            </button>

            <a
                href="{{ route('items.index') }}"
                class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
            >
                Reset
            </a>
        </form>

        <x-htmx-table::table :table="$table" />
    </div>
@endsection
