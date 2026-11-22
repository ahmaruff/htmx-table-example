# HTMX Table Example

Demo application for [`ahmaruff/laravel-htmx-table`](https://github.com/ahmaruff/laravel-htmx-table), a lightweight server-driven table for Laravel built with Blade and HTMX.

This repository is the **demo app**, not the package. The package renders the table fragment on the server; this app shows how to use it and how to add application-level inline editing on top of it.

[![PHP](https://img.shields.io/badge/php-8.3%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/laravel-13-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![HTMX](https://img.shields.io/badge/htmx-2.x-3D72D7)](https://htmx.org/)
[![License](https://img.shields.io/badge/license-MIT-blue)](https://opensource.org/license/mit)

## What this demo shows

- A server-rendered table backed by `HtmxTable::make()`.
- Debounced search across searchable columns.
- Sortable columns with ascending/descending state in the query string.
- Filters for category, brand, status, and active flag.
- Per-page selector and pagination.
- Inline row editing: edit, save, cancel, and server-side validation.
- 500 seeded items so search, sorting, and pagination are easy to try.
- No Livewire or Alpine — HTMX only.

## Requirements

- PHP 8.3+
- Composer
- Node.js and npm
- SQLite
- `ahmaruff/laravel-htmx-table` v0.1.x (installed via Composer)
- HTMX 2.x (loaded from CDN in the layout)

## Quick start

```bash
git clone https://github.com/ahmaruff/htmx-table-example.git
cd htmx-table-example
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Open <http://localhost:8000/items>.

`php artisan migrate --seed` creates 500 demo items via `ItemSeeder`.

For development, `composer run dev` starts the Laravel dev processes (application server, Vite, and friends).

## How the table works

The index route builds a query from the request, configures the table columns, and renders a full page or a table fragment:

```php
$table = $this->service->buildDatatable($request);

if (HtmxTable::isFragmentRequest($request)) {
    return $table->render($request);
}

return view('items.index', [
    'table' => $table,
    'filters' => $this->service->filters($request),
]);
```

Search, sorting, pagination, and per-page state travel in the query string. The table wrapper owns `hx-target="closest .htmx-table-wrapper"` and `hx-swap="outerHTML"`, so every trigger swaps its own table fragment.

See [`app/Services/ItemDatatableService.php`](app/Services/ItemDatatableService.php) for the query, filters, and column definitions.

## Inline editing

Inline editing is implemented in this app, not in the package. Each read row has an **Edit** button rendered by the `actions` column.

```
read row --[Edit]--> GET /items/{item}/edit --> <tr> edit form
    ^                                                  |
    |                                            [Save] hx-put
    |                                                  v
    +---- <tr> read-only <-- PUT /items/{item} (validate + update)
    |
    +---- <tr> read-only <-- GET /items/{item}/row (Cancel)
```

| Method | URI                    | Name           | Purpose                                      |
| ------ | ---------------------- | -------------- | -------------------------------------------- |
| `GET`  | `/items`               | `items.index`  | Full page or table fragment                  |
| `GET`  | `/items/{item}/edit`   | `items.edit`   | Editable `<tr>` fragment                     |
| `GET`  | `/items/{item}/row`    | `items.row`    | Read-only `<tr>` fragment                    |
| `PUT`  | `/items/{item}`        | `items.update` | Validate, persist, and return a read-only row |

### Implementation notes

- **No `<form>` inside the row.** The Save button uses `hx-put` with `hx-include="closest tr"`. For non-GET requests, HTMX collects every descendant `input`, `textarea`, and `select` inside the included row.
- **`hx-validate="true"`** on the Save button enables HTML5 validation without a form. Number inputs use `step` so decimal prices and weights are valid.
- **Explicit `hx-target` / `hx-swap`.** The table wrapper sets inherited `hx-target` and `hx-swap`, so the edit and save controls override both with `closest tr` and `outerHTML`.
- **Validation errors are fragments.** `UpdateItemRequest::failedValidation()` returns a `422` response containing the edit row with errors instead of redirecting. The `responseHandling` entry in `resources/views/layouts/app.blade.php` tells HTMX to swap `422` responses.
- **CSRF token is part of the row.** A hidden `_token` input lives in the actions cell and is collected by `hx-include`.
- **`is_active` uses a hidden field and a checkbox.** The hidden input sends `0`; the checkbox sends `1` when checked.
- **Dates use `Y-m-d`.** `manufactured_at` is formatted for `<input type="date">`; passing the raw Carbon value would render an invalid value and clear the date on save.
- **`sku` must be rendered in the edit row.** It is required by validation, so every editable field that is validated needs a corresponding input in the row.
- **Errors are visible per field.** Inline messages appear under the offending input, with a summary in the actions cell.

## Project structure

| Path                                                         | Responsibility                                      |
| ------------------------------------------------------------ | --------------------------------------------------- |
| `app/Services/ItemDatatableService.php`                      | Query, filters, and column definitions              |
| `app/Http/Controllers/ItemController.php`                    | `index`, `edit`, `row`, and `update` actions        |
| `app/Http/Requests/UpdateItemRequest.php`                    | Validation rules and the 422 edit-row response      |
| `resources/views/items/index.blade.php`                      | Page layout and filter form                         |
| `resources/views/items/partials/row.blade.php`               | Read-only `<tr>` fragment                           |
| `resources/views/items/partials/edit-row.blade.php`          | Single-row edit form                                |
| `resources/views/vendor/htmx-table/table.blade.php`          | Published table view                                |
| `resources/views/layouts/app.blade.php`                      | HTMX config, scripts, and styles                    |
| `routes/web.php`                                             | Demo routes                                         |

## Column customization

The package does not expose a per-column width API. For presentation changes, use the column renderer.

The demo's description column stays on one line with ellipsis and a native tooltip:

```php
Column::make('description')
    ->dataKey('description')
    ->label('Deskripsi')
    ->searchable()
    ->render(fn ($row): HtmlString => new HtmlString(
        '<div class="max-w-xs truncate" title="'.e((string) $row->description).'">'
        .e((string) $row->description)
        .'</div>'
    ));
```

For structural changes to the table markup, publish and customize the package views. See the package documentation for custom views, CSS variables, and column rendering.

## Testing

```bash
php artisan test --compact
vendor/bin/pint --dirty --format agent
```

The demo includes feature tests for the table rendering, filters, search, sorting, and the inline editing flow in [`tests/Feature/ItemInlineEditTest.php`](tests/Feature/ItemInlineEditTest.php).

## The library

- GitHub: <https://github.com/ahmaruff/laravel-htmx-table>
- Packagist: <https://packagist.org/packages/ahmaruff/laravel-htmx-table>

## License

This demo is open-source software licensed under the [MIT license](https://opensource.org/license/mit).
