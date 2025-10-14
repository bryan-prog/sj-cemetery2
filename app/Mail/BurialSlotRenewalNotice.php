<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BurialSlotRenewalNotice extends Mailable
{
    use Queueable, SerializesModels;

    public string $clientName;
    /** @var string[] */
    public array $deceasedNames;
    public ?string $slotReference;
    public string $expirationDateMmDdYyyy;

    /**
     * @param string   $clientName  Applicant full name
     * @param string[] $deceasedNames List of deceased names (may be empty)
     * @param string|null $slotReference Slot label fallback
     * @param string $expirationDateMmDdYyyy e.g. 10/31/2025
     */
    public function __construct(string $clientName, array $deceasedNames, ?string $slotReference, string $expirationDateMmDdYyyy)
    {
        $this->clientName = $clientName;
        $this->deceasedNames = $deceasedNames;
        $this->slotReference = $slotReference;
        $this->expirationDateMmDdYyyy = $expirationDateMmDdYyyy;
    }

    public function build()
    {
        $subject = 'Burial Slot Renewal Notice';
        return $this->subject($subject)
            ->view('emails.renewal_notice');
    }
}
