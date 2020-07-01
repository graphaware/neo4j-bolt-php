<?php

namespace PTS\Bolt\Protocol;

class PipelineMessage
{
    private string $statement;
    private array $parameters;
    private ?string $tag;

    public function __construct(string $statement, array $parameters, ?string $tag)
    {
        $this->parameters = $parameters;
        $this->statement = $statement;
        $this->tag = $tag;
    }

    public function getStatement()
    {
        return $this->statement;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getTag()
    {
        return $this->tag;
    }
}
