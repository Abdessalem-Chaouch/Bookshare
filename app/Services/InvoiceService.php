<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\SubscriptionPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    /**
     * Generate invoice for a subscription payment
     */
    public function generateInvoice(SubscriptionPayment $payment)
    {
        // Create invoice record
        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'user_id' => $payment->user_id,
            'subscription_payment_id' => $payment->id,
            'subscription_id' => $payment->subscription_id,
            'subtotal' => $payment->amount,
            'tax' => 0,
            'discount' => 0,
            'total' => $payment->amount,
            'currency' => $payment->currency,
            'invoice_date' => now(),
            'due_date' => now(),
            'status' => 'paid',
            'notes' => 'Thank you for your subscription!'
        ]);

        // Generate PDF
        $this->generatePDF($invoice);

        return $invoice;
    }

    /**
     * Generate PDF for an invoice
     */
    public function generatePDF(Invoice $invoice)
    {
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice->load(['user', 'subscription', 'subscriptionPayment'])
        ]);

        // Create invoices directory if it doesn't exist
        if (!Storage::disk('public')->exists('invoices')) {
            Storage::disk('public')->makeDirectory('invoices');
        }

        // Generate filename
        $filename = 'invoices/' . $invoice->invoice_number . '.pdf';
        
        // Save PDF to storage
        Storage::disk('public')->put($filename, $pdf->output());

        // Update invoice with PDF path
        $invoice->update(['pdf_path' => $filename]);

        return $filename;
    }

    /**
     * Download invoice PDF
     */
    public function downloadInvoice(Invoice $invoice)
    {
        if (!$invoice->pdf_path || !Storage::disk('public')->exists($invoice->pdf_path)) {
            // Regenerate PDF if it doesn't exist
            $this->generatePDF($invoice);
        }

        return Storage::disk('public')->download($invoice->pdf_path, $invoice->invoice_number . '.pdf');
    }

    /**
     * Stream invoice PDF for viewing
     */
    public function streamInvoice(Invoice $invoice)
    {
        if (!$invoice->pdf_path || !Storage::disk('public')->exists($invoice->pdf_path)) {
            // Regenerate PDF if it doesn't exist
            $this->generatePDF($invoice);
        }

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice->load(['user', 'subscription', 'subscriptionPayment'])
        ]);

        return $pdf->stream($invoice->invoice_number . '.pdf');
    }

    /**
     * Calculate tax (example: 10% tax rate)
     */
    private function calculateTax($amount)
    {
        $taxRate = 0.10; // 10% tax
        return round($amount * $taxRate, 2);
    }

    /**
     * Send invoice via email
     */
    public function sendInvoiceEmail(Invoice $invoice)
    {
        // TODO: Implement email sending with invoice attachment
        // You can use Laravel Mail with the PDF attached
    }
}
