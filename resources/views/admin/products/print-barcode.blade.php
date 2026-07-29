<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cetak Barcode</title>

    <style>
    @page {
        size: A4;
        margin: 10mm;
    }

    body{
        margin:0;
        padding:20px;
        background:#ffffff;
        font-family:Arial, Helvetica, sans-serif;
    }

    .label{
        width:220px;
        height:120px;
        border:1px dashed #bfbfbf;
        box-sizing:border-box;
        padding:8px 10px;

        display:flex;
        flex-direction:column;
        align-items:center;
        justify-content:space-between;
    }

    /* Nama Produk */
    .product-name{
        font-size:14px;
        font-weight:bold;
        text-align:center;
        line-height:18px;
        color:#111;
    }

    /* SKU */
    .sku{
        font-size:10px;
        color:#4b6cb7;
        margin-top:-3px;
    }

    /* Barcode */
    .barcode{
        display:flex;
        justify-content:center;
        align-items:center;
        width:100%;
    }

    .barcode svg{
        width:170px;
        height:42px;
    }

    /* Nomor Barcode */
    .barcode-number{
        font-size:11px;
        letter-spacing:2px;
        margin-top:3px;
    }

    /* Harga */
    .price{
        font-size:16px;
        font-weight:bold;
        color:#000;
    }
    </style>
</head>

<body onload="window.print()">

<div class="label">

    <div class="product-name">
        {{ $product->name }}
    </div>

    <div class="sku">
        SKU : {{ $product->sku ?? $product->barcode }}
    </div>

    <div class="barcode">
        {!! $barcode !!}
    </div>

    <div class="barcode-number">
        {{ $product->barcode }}
    </div>

    <div class="price">
        Rp {{ number_format($product->sell_price,0,',','.') }}
    </div>

</div>

</body>
</html>