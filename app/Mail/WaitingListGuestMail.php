<?php

<?php

namespace App\Mail;

use App\Models\WaitingList;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WaitingListGuestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $entry;

    public function __construct(WaitingList $entry)
    {
        $this->entry = $entry;
    }

    public function build()
    {
        return $this->subject('You have joined the Waiting List')
                    ->markdown('emails.waitinglist.guest');
    }
}

