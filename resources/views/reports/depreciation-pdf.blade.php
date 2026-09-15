<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>Asset Depreciation Report</h1>
    <p>Generated {{ now()->format('M j, Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Tag</th>
                <th>Name</th>
                <th>Category</th>
                <th>Purchase Date</th>
                <th>Purchase Cost</th>
                <th>Accum. Depreciation</th>
                <th>Book Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assets as $asset)
                <tr>
                    <td>{{ $asset->asset_tag }}</td>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->category?->name }}</td>
                    <td>{{ $asset->purchase_date?->format('Y-m-d') }}</td>
                    <td>{{ number_format($asset->purchase_cost, 2) }}</td>
                    <td>{{ number_format($asset->accumulated_depreciation, 2) }}</td>
                    <td>{{ number_format($asset->current_book_value, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
