<tr class="htmx-table-tr">
    @foreach ($columns as $column)
        <td class="htmx-table-td">{{ $column->resolveCell($item) }}</td>
    @endforeach
</tr>