<?php

namespace App\Mail;

use App\Models\BookFetchRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookFetchMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bookFetch;

    public function __construct(BookFetchRequest $bookFetch)
    {
        $this->bookFetch = $bookFetch;
    }

    public function build()
    {
        return $this->from($this->bookFetch->email) // sender = user's email
                    ->subject('📚 New Book Fetch Request')
                    ->view('emails.bookfetch') // create this Blade file
                    ->with(['bookFetch' => $this->bookFetch]);
    }
}
