<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BorrowBookMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sach;
    public $hantra;

    public function __construct($sach, $hantra)
    {
        $this->sach = $sach;
        $this->hantra = $hantra;
    }

    public function build()
    {
        return $this->subject('Thông báo mượn sách từ thư viện')
            ->view('emails.borrowBook');
    }
}
