<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 700px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .summary {
            display: flex;
            justify-content: space-around;
            padding: 30px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
        }
        .summary-item {
            text-align: center;
        }
        .summary-item .number {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            display: block;
        }
        .summary-item .label {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 30px;
        }
        .order {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        .order-id {
            font-weight: bold;
            color: #667eea;
            font-size: 16px;
        }
        .order-total {
            font-weight: bold;
            font-size: 18px;
            color: #28a745;
        }
        .order-customer {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        .order-items {
            margin-top: 10px;
        }
        .item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #ddd;
        }
        .item:last-child {
            border-bottom: none;
        }
        .item-name {
            font-weight: 500;
        }
        .item-quantity {
            color: #666;
            font-size: 14px;
        }
        .item-price {
            font-weight: bold;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e0e0e0;
        }
        .no-orders {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .no-orders svg {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Daily Sales Report</h1>
            <p>{{ now()->format('l, F j, Y') }}</p>
        </div>

        @if($totalOrders > 0)
            <div class="summary">
                <div class="summary-item">
                    <span class="number">{{ $totalOrders }}</span>
                    <span class="label">Orders</span>
                </div>
                <div class="summary-item">
                    <span class="number">${{ number_format($totalRevenue, 2) }}</span>
                    <span class="label">Revenue</span>
                </div>
                <div class="summary-item">
                    <span class="number">{{ $totalItems }}</span>
                    <span class="label">Items Sold</span>
                </div>
            </div>

            <div class="content">
                <h2 style="margin-top: 0; color: #333;">Today's Orders</h2>
                
                @foreach($orders as $order)
                    <div class="order">
                        <div class="order-header">
                            <div>
                                <div class="order-id">Order #{{ $order->id }}</div>
                                <div class="order-customer">
                                    👤 {{ $order->user->name }} ({{ $order->user->email }})
                                </div>
                                <div style="font-size: 12px; color: #999;">
                                    🕐 {{ $order->created_at->format('g:i A') }}
                                </div>
                            </div>
                            <div class="order-total">
                                ${{ number_format($order->total, 2) }}
                            </div>
                        </div>

                        <div class="order-items">
                            @foreach($order->orderItems as $item)
                                <div class="item">
                                    <div>
                                        <div class="item-name">{{ $item->product->name }}</div>
                                        <div class="item-quantity">Quantity: {{ $item->quantity }}</div>
                                    </div>
                                    <div class="item-price">
                                        ${{ number_format($item->price * $item->quantity, 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-orders">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 style="margin: 10px 0; color: #999;">No Orders Today</h3>
                <p style="margin: 5px 0;">There were no sales recorded for today.</p>
            </div>
        @endif

        <div class="footer">
            <p style="margin: 0;">This is an automated daily report from CartFlow</p>
            <p style="margin: 5px 0 0 0;">Generated on {{ now()->format('M d, Y \a\t g:i A') }}</p>
        </div>
    </div>
</body>
</html>