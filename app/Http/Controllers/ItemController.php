<?php

namespace App\Http\Controllers;

use Ahmaruff\HtmxTable\HtmxTable;
use App\Services\ItemDatatableService;
use Illuminate\Contracts\View\View;
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
}
