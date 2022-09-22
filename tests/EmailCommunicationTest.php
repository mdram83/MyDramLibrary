<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use MyDramLibrary\Utilities\Communication\EmailCommunication;

class EmailCommunicationTest extends TestCase
{
    private EmailCommunication $email;

    final public function setUp(): void
    {
        $this->email = new EmailCommunication();
    }

    public function testEmailObjectCreatedPartOfInterface()
    {
        $this->assertInstanceOf('MyDramLibrary\Utilities\Communication\Communication', $this->email);
    }

    public function testEmailObjectCreated()
    {
        $this->assertInstanceOf('MyDramLibrary\Utilities\Communication\EmailCommunication', $this->email);
    }

    public function testThrowExceptionIfGettingRecipientsBeforeSetting()
    {
        $this->expectException('Exception');
        $this->email->getRecipients();
    }

    public function testRecipientAddedAndReturned()
    {
        $recipient = 'test1recipient@TestEmail.com';
        $this->email->addRecipient($recipient);
        $this->assertTrue(in_array($recipient, $this->email->getRecipients()));
    }

    public function testThrowExceptionIfInvalidEmailAddressAddedToRecipients()
    {
        $this->expectException('Exception');
        $this->email->addRecipient('invalid email address');
    }

    public function testRecipientsAddedAndReturned()
    {
        $recipient1 = 'test1recipient@TestEmail.com';
        $recipient2 = 'test1recipient@TestEmail.com';
        $this->email->addRecipient($recipient1);
        $this->email->addRecipient($recipient2);
        $recipients = $this->email->getRecipients();
        $this->assertTrue(in_array($recipient1, $recipients) && in_array($recipient2, $recipients));
    }

    public function testSameRecipientIsAddedOnlyOnce()
    {
        $recipient = 'test1recipient@TestEmail.com';
        $this->email->addRecipient($recipient);
        $this->email->addRecipient($recipient);
        $this->assertCount(1, $this->email->getRecipients());
    }

    public function testThrowExceptionIfInvalidEmailAddressAddedToSender()
    {
        $this->expectException('Exception');
        $this->email->setSender('invalid email address');
    }

    public function testThrowExceptionIfSenderAlreadySet()
    {
        $this->expectException('Exception');
        $this->email->setSender('test1sender@TestEmail.com');
        $this->email->setSender('test1sender@TestEmail.com');
    }

    public function testSetSenderAndReturn()
    {
        $sender = 'test1sender@TestEmail.com';
        $this->email->setSender($sender);
        $this->assertEquals($sender, $this->email->getSender());
    }

    public function testThrowExceptionIfGettingSenderBeforeIsSet()
    {
        $this->expectException('Exception');
        $this->email->getSender();
    }

    public function testThrowExceptionIfSendingBeforeSettingRecipient()
    {
        $this->expectException('Exception');
        $this->email->setSender('test1sender@TestEmail.com');
        $this->email->setContent('Test content');
        $this->email->send();
    }

    public function testThrowExceptionIfSendingBeforeSettingSender()
    {
        $this->expectException('Exception');
        $this->email->addRecipient('test1recipient@TestEmail.com');
        $this->email->setContent('Test content');
        $this->email->setSubject('Test subject');
        $this->email->send();
    }

    public function testThrowExceptionIfSendingBeforeSettingContent()
    {
        $this->expectException('Exception');
        $this->email->addRecipient('test1recipient@TestEmail.com');
        $this->email->setSender('test1sender@TestEmail.com');
        $this->email->setSubject('Test subject');
        $this->email->send();
    }

    public function testThrowExceptionIfSendingBeforeSettingSubject()
    {
        $this->expectException('Exception');
        $this->email->addRecipient('test1recipient@TestEmail.com');
        $this->email->setSender('test1sender@TestEmail.com');
        $this->email->setContent('Test content');
        $this->email->send();
    }

    public function testSetAndReturnContent()
    {
        $content = 'Test content';
        $this->email->setContent($content);
        $this->assertEquals($content, $this->email->getContent());
    }

    public function testThrowExceptionIfGettingContentBeforeItsSet()
    {
        $this->expectException('Exception');
        $this->email->getContent();
    }

    public function testThrowExceptionIfGettingSubjectBeforeItsSet()
    {
        $this->expectException('Exception');
        $this->email->getSubject();
    }

    public function testSetAndReturnSubject()
    {
        $subject = 'Test subject';
        $this->email->setSubject($subject);
        $this->assertEquals($subject, $this->email->getSubject());
    }

    public function testThrowExceptionWhenOverwritingSubject()
    {
        $this->expectException('Exception');
        $this->email->setSubject('Test subject');
        $this->email->setSubject('Test subject');        
    }


    public function testEmailSentWithoutGeneratingException() {
        $this->email->addRecipient('validemail13jsjjjs@mdram83.cos');
        $this->email->setSender('testSender@localhost.com');
        $this->email->setSubject('Test subject - PHP Devs');
        $this->email->setContent('Test email content');
        $this->email->send();
        $this->addToAssertionCount(1);
    }

    final public function tearDown(): void
    {
        unset ($this->EmailCommunication);
    }
}
