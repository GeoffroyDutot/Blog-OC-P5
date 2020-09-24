<?php


namespace App\DTO;


class EmailDTO {
    protected string $name;
    protected string $message;
    protected ?int $phone;
    protected string $sender;
    protected string $receiver;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return EmailDTO
     */
    public function setName(string $name): EmailDTO {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string {
        return $this->message;
    }

    /**
     * @param string $message
     * @return EmailDTO
     */
    public function setMessage(string $message): EmailDTO {
        $this->message = $message;
        return $this;
    }

    /**
     * @return int
     */
    public function getPhone(): ?int {
        return $this->phone;
    }

    /**
     * @param int $phone
     * @return EmailDTO
     */
    public function setPhone(?int $phone): EmailDTO {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return string
     */
    public function getSender(): string {
        return $this->sender;
    }

    /**
     * @param string $sender
     * @return EmailDTO
     */
    public function setSender(string $sender): EmailDTO {
        $this->sender = $sender;
        return $this;
    }

    /**
     * @return string
     */
    public function getReceiver(): string {
        return $this->receiver;
    }

    /**
     * @param string $receiver
     * @return EmailDTO
     */
    public function setReceiver(string $receiver): EmailDTO {
        $this->receiver = $receiver;
        return $this;
    }
}