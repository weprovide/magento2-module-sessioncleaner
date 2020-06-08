<?php

declare(strict_types = 1);

namespace WeProvide\SessionCleaner\Service;

use Magento\Framework\Exception\SessionException;

class SessionService implements SessionServiceInterface
{
    protected $scopeConfig;
    protected $connection;
    protected $sessionTable;
    protected $logger;

    const SCOPE_CONFIG_COOKIE_LIFETIME_PATH = 'web/cookie/cookie_lifetime';

    /**
     * SessionService constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\ResourceConnection          $resource
     * @param \Psr\Log\LoggerInterface                           $logger
     * @throws SessionException
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->scopeConfig  = $scopeConfig;
        $this->connection   = $resource->getConnection();
        $this->sessionTable = $resource->getTableName('session');
        $this->logger       = $logger;

        $this->checkConnection();
    }

    /**
     * Checks database connection and throws exception if failed.
     *
     * @return void
     * @throws \Magento\Framework\Exception\SessionException
     */
    protected function checkConnection(): void
    {
        if (!$this->connection) {
            throw new SessionException(__('Write DB connection is not available'));
        }
        if (!$this->connection->isTableExists($this->sessionTable)) {
            throw new SessionException(__('DB storage table does not exist'));
        }
    }

    /**
     * @inheritDoc
     */
    public function cleanExpiredSessions(): int
    {
        $expired = $this->connection->delete($this->sessionTable, ['session_expires < UNIX_TIMESTAMP() - ?' => $this->scopeConfig->getValue(self::SCOPE_CONFIG_COOKIE_LIFETIME_PATH)]);
        $this->connection->query('OPTIMIZE TABLE ' . $this->sessionTable);

        return $expired;
    }
}
