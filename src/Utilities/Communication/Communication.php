<?php

namespace MyDramLibrary\Utilities\Communication;

interface Communication
{
    public function addRecipient(string $recipient);
    public function setSender(string $sender);
    public function setSubject(string $subject);
    public function setContent(string $content);
    public function send(): void;
}
