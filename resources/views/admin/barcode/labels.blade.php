<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Shelf Labels</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 10mm;
        }

        .labels-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .label-cell {
            width: 33.33%;
            padding: 5mm;
            border: 1px dashed #ccc;
            text-align: center;
            vertical-align: top;
            height: 40mm;
        }

        .product-name {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 5px;
            height: 24px;
            overflow: hidden;
        }

        .sku {
            font-size: 8px;
            color: #666;
            margin-bottom: 5px;
        }

        .barcode {
            margin-top: 5px;
        }

        .price {
            font-size: 12px;
            font-weight: bold;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <table class="labels-grid">
        @php $count = 0; @endphp
        @foreach($products as $product)
            @if($count % 3 == 0)
                <tr>
            @endif

                <td class="label-cell">
                    <div class="product-name">{{ $product->name }}</div>
                    <div class="sku">SKU: {{ $product->sku }}</div>
                    <div class="barcode">
                        {!! DNS1D::getBarcodeHTML($product->sku, 'C128', 1.5, 33) !!}
                    </div>
                    <div class="price">{{ Number::currency($product->regular_price) }}</div>
                </td>

                @php $count++; @endphp

                @if($count % 3 == 0)
                    </tr>
                @endif
        @endforeach

        {{-- Fill remaining cells in the last row if necessary --}}
        @if($count % 3 != 0)
            @while($count % 3 != 0)
                <td class="label-cell"></td>
                @php $count++; @endphp
            @endwhile
            </tr>
        @endif
    </table>
</body>

</html>