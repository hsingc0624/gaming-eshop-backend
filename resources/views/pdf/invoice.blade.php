<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Invoice #{{ $order->number }}</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#222; }
    h1 { font-size: 20px; margin: 0 0 10px; }
    .muted { color: #666; }
    .grid { display: flex; gap: 24px; }
    .card { border: 1px solid #ddd; border-radius: 8px; padding: 10px; }
    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
    th { background: #f7f7f7; }
    .right { text-align: right; }
    .totals td { border: none; padding: 4px 0; }
    .totals tr:last-child td { font-weight: bold; border-top: 1px solid #ddd; padding-top: 8px; }
  </style>
</head>
<body>
  <h1>Invoice #{{ $order->number }}</h1>
  <div class="muted">Date: {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</div>

  <div class="grid" style="margin-top:12px">
    <div class="card" style="flex:1">
      <strong>Shipping Address</strong><br>
      @php $s = $order->addresses->firstWhere('type','shipping'); @endphp
      @if($s)
        {{ $s->name }}<br>
        {{ $s->line1 }}<br>
        @if($s->line2) {{ $s->line2 }}<br>@endif
        {{ $s->city }}, {{ $s->postcode }}<br>
        {{ $s->country }}
      @else
        <span class="muted">N/A</span>
      @endif
    </div>
    <div class="card" style="flex:1">
      <strong>Billing Address</strong><br>
      @php $b = $order->addresses->firstWhere('type','billing'); @endphp
      @if($b)
        {{ $b->name }}<br>
        {{ $b->line1 }}<br>
        @if($b->line2) {{ $b->line2 }}<br>@endif
        {{ $b->city }}, {{ $b->postcode }}<br>
        {{ $b->country }}
      @else
        <span class="muted">N/A</span>
      @endif
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:55%">Item</th>
        <th style="width:10%">Qty</th>
        <th style="width:15%">Price</th>
        <th style="width:20%">Line Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($order->items as $item)
        <tr>
          <td>{{ $item->name }}</td>
          <td class="right">{{ $item->qty }}</td>
          <td class="right">£{{ number_format(($item->price_cents ?? 0)/100, 2) }}</td>
          <td class="right">£{{ number_format((($item->price_cents ?? 0) * $item->qty)/100, 2) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  @php
    $subtotal = $order->subtotal_cents ?? 0;
    $shipping = $order->shipping_cents ?? 0;
    $tax      = $order->tax_cents ?? 0;
    $total    = $order->total_cents ?? ($subtotal + $shipping + $tax);
  @endphp

  <table class="totals" style="margin-top: 10px;">
    <tr>
      <td class="right" style="width:80%"><strong>Subtotal</strong></td>
      <td class="right" style="width:20%">£{{ number_format($subtotal/100, 2) }}</td>
    </tr>
    <tr>
      <td class="right"><strong>Shipping</strong></td>
      <td class="right">£{{ number_format($shipping/100, 2) }}</td>
    </tr>
    <tr>
      <td class="right"><strong>VAT</strong></td>
      <td class="right">£{{ number_format($tax/100, 2) }}</td>
    </tr>
    <tr>
      <td class="right"><strong>Total</strong></td>
      <td class="right">£{{ number_format($total/100, 2) }}</td>
    </tr>
  </table>
</body>
</html>
