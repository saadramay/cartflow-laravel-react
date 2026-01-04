<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailySalesReport extends Mailable
{
    use Queueable, SerializesModels;

    public $orders;
    public $totalOrders;
    public $totalRevenue;
    public $totalItems;

    public function __construct($orders, $totalOrders, $totalRevenue, $totalItems)
    {
        $this->orders = $orders;
        $this->totalOrders = $totalOrders;
        $this->totalRevenue = $totalRevenue;
        $this->totalItems = $totalItems;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Sales Report - ' . now()->format('M d, Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-sales-report',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
