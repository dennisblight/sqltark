<?php
namespace SqlTark\Clauses\Join;

use Closure;
use SqlTark\Clauses\AbstractClause;

class DeepJoin extends AbstractJoin
{
    /**
     * @var string $type
     */
    protected $type;
    
    /**
     * @var string $expression
     */
    protected $expression;
    
    /**
     * @var string $sourceKeySuffix
     */
    protected $sourceKeySuffix;
    
    /**
     * @var string $targetKey
     */
    protected $targetKey;
    
    /**
     * @var Closure $sourceKeyGenerator
     */
    protected $sourceKeyGenerator;
    
    /**
     * @var Closure $targetKeyGenerator
     */
    protected $targetKeyGenerator;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $value)
    {
        $this->type = $value;
    }

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function setExpression(string $value)
    {
        $this->expression = $value;
    }

    public function getSourceKeySuffix(): string
    {
        return $this->sourceKeySuffix;
    }

    public function setSourceKeySuffix(string $value)
    {
        $this->sourceKeySuffix = $value;
    }

    public function getTargetKey(): string
    {
        return $this->targetKey;
    }

    public function setTargetKey(string $value)
    {
        $this->targetKey = $value;
    }

    public function getSourceKeyGenerator(): Closure
    {
        return $this->sourceKeyGenerator;
    }

    public function setSourceKeyGenerator(Closure $value)
    {
        $this->sourceKeyGenerator = $value;
    }

    public function getTargetKeyGenerator(): Closure
    {
        return $this->targetKeyGenerator;
    }

    public function setTargetKeyGenerator(Closure $value)
    {
        $this->targetKeyGenerator = $value;
    }

    /**
     * @return DeepJoin
     */
    public function clone(): AbstractClause
    {
        /** @var DeepJoin */
        $self = parent::clone();

        $self->type = $this->type;
        $self->expression = $this->expression;
        $self->sourceKeySuffix = $this->sourceKeySuffix;
        $self->targetKey = $this->targetKey;
        $self->sourceKeyGenerator = $this->sourceKeyGenerator;
        $self->targetKeyGenerator = $this->targetKeyGenerator;

        return $self;
    }
}