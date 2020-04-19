<?php


namespace AcMarche\Mercredi\Message;


class FlashNotification
{
    private $message;
    private $type;

    public function __construct(string $content, string $type = 'success')
    {
        $this->message = $content;
        $this->type = $type;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
