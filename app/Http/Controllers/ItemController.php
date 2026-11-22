<?php

namespace App\Http\Controllers;

use Ahmaruff\HtmxTable\HtmxTable;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use App\Services\ItemDatatableService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    protected ItemDatatableService $service;

    public function __construct(ItemDatatableService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): View
    {
        $table = $this->service->buildDatatable($request);

        if (HtmxTable::isFragmentRequest($request)) {
            return $table->render($request);
        }

        return view('items.index', [
            'table' => $table,
            'filters' => $this->service->filters($request),
        ]);
    }

    public function edit(Item $item): View
    {
        return view('items.partials.edit-row', [
            'item' => $item,
            'columns' => $this->service->columns(),
        ]);
    }

    public function row(Item $item): View
    {
        return view('items.partials.row', [
            'item' => $item,
            'columns' => $this->service->columns(),
        ]);
    }

    public function update(UpdateItemRequest $request, Item $item): View|RedirectResponse
    {
        $item->update($request->validated());

        if (HtmxTable::isFragmentRequest($request)) {
            return $this->row($item);
        }

        return redirect()->route('items.index');
    }
}
