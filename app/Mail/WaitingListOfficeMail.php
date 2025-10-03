<?php

<?php

namespace App\Mail;

use App\Models\WaitingList;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WaitingListOfficeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $entry;

    public function __construct(WaitingList $entry)
    {
        $this->entry = $entry;
    }

    public function build()
    {
        return $this->subject('New Waiting List Signup')
                    ->markdown('emails.waitinglist.office');
    }
}
