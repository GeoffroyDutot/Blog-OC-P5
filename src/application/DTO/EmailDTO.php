<?php


namespace App\DTO;


class EmailDTO
{
    protected string $name;
    protected string $message;
    protected ?int $phone = null;
    protected string $sender;
    protected string $receiver;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): EmailDTO
    {
        $this->name = $name;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): EmailDTO
    {
        $this->message = $message;

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(?int $phone): EmailDTO
    {
        $this->phone = $phone;

        return $this;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function setSender(string $sender): EmailDTO
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): string
    {
        return $this->receiver;
    }

    public function setReceiver(string $receiver): EmailDTO
    {
        $this->receiver = $receiver;

        return $this;
    }
}