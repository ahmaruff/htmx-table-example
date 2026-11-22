<?php

namespace App\Services;

use Ahmaruff\HtmxTable\Column;
use Ahmaruff\HtmxTable\HtmxTable;
use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;

class ItemDatatableService
{
    public function buildQuery(Request $request): Builder
    {
        $category = $this->filterValue($request, 'category', Item::CATEGORIES);
        $brand = $this->filterValue($request, 'brand', Item::BRANDS);
        $status = $this->filterValue($request, 'status', Item::STATUSES);
        $isActive = $this->filterValue($request, 'is_active', ['1', '0']);

        return Item::query()
            ->when($category !== '', fn (Builder $query) => $query->where('category', $category))
            ->when($brand !== '', fn (Builder $query) => $query->where('brand', $brand))
            ->when($status !== '', fn (Builder $query) => $query->where('status', $status))
            ->when($isActive !== '', fn (Builder $query) => $query->where('is_active', $isActive === '1'));
    }

    /**
     * @return array{category: string, brand: string, status: string, is_active: string}
     */
    public function filters(Request $request): array
    {
        return [
            'category' => $this->filterValue($request, 'category', Item::CATEGORIES),
            'brand' => $this->filterValue($request, 'brand', Item::BRANDS),
            'status' => $this->filterValue($request, 'status', Item::STATUSES),
            'is_active' => $this->filterValue($request, 'is_active', ['1', '0']),
        ];
    }

    /**
     * @param  list<string>  $allowed
     */
    private function filterValue(Request $request, string $key, array $allowed): string
    {
        $value = $request->string($key)->toString();

        return in_array($value, $allowed, true) ? $value : '';
    }

    public function buildDatatable(Request $request): HtmxTable
    {
        $q = $this->buildQuery($request);

        $columns = $this->columns();
        $table = HtmxTable::make($q)
            ->id('item-table')
            ->locale('id')
            ->searchPlaceholder('Cari Item')
            ->perPageOptions([5, 10, 15, 25, 100, 200])
            ->columns($columns)
            ->paginate(5);

        return $table;
    }

    public function columns(): array
    {
        return [
            Column::make('id')
                ->dataKey('id')
                ->label('ID')
                ->sortable()
                ->render(fn ($row): string => (string) $row->id),
            Column::make('sku')
                ->dataKey('sku')
                ->label('SKU')
                ->sortable()
                ->searchable()
                ->render(fn ($row): HtmlString => new HtmlString(
                    '<div class="max-w-xs truncate" title="'.e((string) $row->sku).'">'
                    .e((string) $row->sku)
                    .'</div>'
                )),
            Column::make('name')
                ->dataKey('name')
                ->label('Nama')
                ->sortable()
                ->searchable()
                ->render(fn ($row): HtmlString => new HtmlString(
                    '<div class="max-w-xs truncate" title="'.e((string) $row->name).'">'
                    .e((string) $row->name)
                    .'</div>'
                )),
            Column::make('description')
                ->dataKey('description')
                ->label('Deskripsi')
                ->searchable()
                ->render(fn ($row): HtmlString => new HtmlString(
                    '<div class="max-w-xs truncate" title="'.e((string) $row->description).'">'
                    .e((string) $row->description)
                    .'</div>'
                )),
            Column::make('category')
                ->dataKey('category')
                ->label('Kategori')
                ->sortable()
                ->searchable()
                ->render(fn ($row): HtmlString => new HtmlString($this->categoryBadge($row))),
            Column::make('brand')
                ->dataKey('brand')
                ->label('Brand')
                ->sortable()
                ->searchable()
                ->render(fn ($row): string => (string) $row->brand),
            Column::make('price')
                ->dataKey('price')
                ->label('Harga')
                ->sortable()
                ->render(fn ($row): string => (string) $row->price),
            Column::make('stock')
                ->dataKey('stock')
                ->label('Stok')
                ->sortable()
                ->render(fn ($row): string => (string) $row->stock),
            Column::make('unit')
                ->dataKey('unit')
                ->label('Unit')
                ->sortable()
                ->searchable()
                ->render(fn ($row): string => (string) $row->unit),
            Column::make('status')
                ->dataKey('status')
                ->label('Status')
                ->sortable()
                ->searchable()
                ->render(fn ($row): HtmlString => new HtmlString($this->statusBadge($row))),
            Column::make('is_active')
                ->dataKey('is_active')
                ->label('Status Aktif')
                ->sortable()
                ->render(fn ($row): HtmlString => new HtmlString($this->isActiveBadge($row))),
            Column::make('weight')
                ->dataKey('weight')
                ->label('Berat')
                ->sortable()
                ->render(fn ($row): string => (string) $row->weight),
            Column::make('manufactured_at')
                ->dataKey('manufactured_at')
                ->label('Tanggal Manufaktur')
                ->sortable()
                ->render(fn ($row): string => $row->manufactured_at ? Carbon::parse($row->manufactured_at)->format('d F Y') : '-'),
            Column::make('actions')
                ->label('Aksi')
                ->render(fn ($row): HtmlString => new HtmlString(
                    '<button type="button"'
                    .' class="rounded-md border border-gray-300 px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-100"'
                    .' hx-get="'.e(route('items.edit', $row)).'"'
                    .' hx-target="closest tr"'
                    .' hx-swap="outerHTML"'
                    .'>Edit</button>'
                )),
        ];
    }

    private function categoryBadge(object $row): string
    {
        $category = $row->category ?? '';

        $categoryClass = match ($category) {
            'Electronics' => 'bg-blue-100 text-blue-800',
            'Furniture' => 'bg-purple-100 text-purple-800',
            'Office Supplies' => 'bg-indigo-100 text-indigo-800',
            'Accessories' => 'bg-pink-100 text-pink-800',
            'Appliances' => 'bg-orange-100 text-orange-800',
            'Tools' => 'bg-teal-100 text-teal-800',
            default => 'bg-gray-100 text-gray-800',
        };

        return "<span class='inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {$categoryClass}'>".e($category).'</span>';
    }

    private function statusBadge(object $row): string
    {
        $status = $row->status ?? '';

        $statusClass = match ($status) {
            'available' => 'bg-green-100 text-green-800',
            'low_stock' => 'bg-yellow-100 text-yellow-800',
            'out_of_stock' => 'bg-red-100 text-red-800',
            'discontinued' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };

        return "<span class='inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {$statusClass}'>".e($status).'</span>';
    }

    private function isActiveBadge(object $row): string
    {
        $isActive = $row->is_active ?? false;

        $label = $isActive ? 'Active' : 'Inactive';
        $statusClass = $isActive
            ? 'bg-green-100 text-green-800'
            : 'bg-gray-100 text-gray-800';

        return "<span class='inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {$statusClass}'>".e($label).'</span>';
    }
}
