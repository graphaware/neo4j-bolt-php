<?php

namespace GraphAware\Bolt\Protocol\Message;

interface MessageInterface
{
    public function getSignature();

    public function getMessageType();

    public function getFields();

    public function isSuccess();

    public function isFailure();

    /*
    public function isIgnored();

    public function isRecord();
    */
}