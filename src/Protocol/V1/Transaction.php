<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) GraphAware Ltd <christophe@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\Protocol\V1;

use GraphAware\Bolt\Exception\MessageFailureException;
use GraphAware\Common\Cypher\Statement;
use GraphAware\Common\Transaction\TransactionInterface;

class Transaction implements TransactionInterface
{
    private static $NO_ROLLBACK_STATUS_CODE = 'ClientNotification';

    const OPENED = 'OPEN';

    const COMMITED = 'COMMITED';

    const ROLLED_BACK = 'TRANSACTION_ROLLED_BACK';

    /**
     * @var string|null
     */
    protected $state;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var bool
     */
    protected $closed = false;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->session->transaction = $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isOpen()
    {
        return $this->state === self::OPENED;
    }

    /**
     * {@inheritdoc}
     */
    public function isCommited()
    {
        return $this->state === self::COMMITED;
    }

    /**
     * {@inheritdoc}
     */
    public function isRolledBack()
    {
        return $this->state === self::ROLLED_BACK;
    }

    /**
     * {@inheritdoc}
     */
    public function rollback()
    {
        $this->assertNotClosed();
        $this->assertStarted();
        $this->session->run('ROLLBACK');
        $this->closed = true;
        $this->state = self::ROLLED_BACK;
        $this->session->transaction = null;
    }

    /**
     * {@inheritdoc}
     */
    public function status()
    {
        return $this->getStatus();
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        $this->success();
    }

    /**
     * {@inheritdoc}
     */
    public function push($query, array $parameters = array(), $tag = null)
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function begin()
    {
        $this->assertNotStarted();
        $this->session->run('BEGIN');
        $this->state = self::OPENED;
    }

    /**
     * {@inheritdoc}
     */
    public function run(Statement $statement)
    {
        try {
            return $this->session->run($statement->text(), $statement->parameters(), $statement->getTag());
        } catch (MessageFailureException $e) {
            $spl = explode('.', $e->getStatusCode());
            if (self::$NO_ROLLBACK_STATUS_CODE !== $spl[1]) {
                $this->state = self::ROLLED_BACK;
                $this->closed = true;
            }
            throw $e;
        }
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->state;
    }

    /**
     * @param Statement[] $statements
     *
     * @return \GraphAware\Common\Result\ResultCollection
     */
    public function runMultiple(array $statements)
    {
        $pipeline = $this->session->createPipeline();

        foreach ($statements as $statement) {
            $pipeline->push($statement->text(), $statement->parameters(), $statement->getTag());
        }

        return $pipeline->run();
    }

    public function success()
    {
        $this->assertNotClosed();
        $this->assertStarted();
        $this->session->run('COMMIT');
        $this->state = self::COMMITED;
        $this->closed = true;
        $this->session->transaction = null;
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    private function assertStarted()
    {
        if ($this->state !== self::OPENED) {
            throw new \RuntimeException('This transaction has not been started');
        }
    }

    private function assertNotStarted()
    {
        if (null !== $this->state) {
            throw new \RuntimeException(sprintf('Can not begin transaction, Transaction State is "%s"', $this->state));
        }
    }

    private function assertNotClosed()
    {
        if (false !== $this->closed) {
            throw new \RuntimeException('This Transaction is closed');
        }
    }
}
