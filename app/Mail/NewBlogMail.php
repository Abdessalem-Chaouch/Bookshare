<?php

namespace App\Mail;

use App\Models\Blog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewBlogMail extends Mailable
{
    use Queueable, SerializesModels;

    public $blog;

    public function __construct(Blog $blog)
    {
        $this->blog = $blog;
    }

    public function build()
    {
        return $this->subject("Nouveau blog publié : {$this->blog->title}")
                    ->view('emails.newBlog');
    }
}
