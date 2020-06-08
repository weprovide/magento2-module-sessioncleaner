<?php

declare(strict_types = 1);

namespace WeProvide\SessionCleaner\Cron;

use Exception;
use Psr\Log\LoggerInterface;
use WeProvide\SessionCleaner\Service\SessionServiceInterface;

class CleanSessions
{
    /** @var SessionServiceInterface */
    protected $sessionService;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * CleanSessions constructor.
     *
     * @param SessionServiceInterface $sessionService
     * @param LoggerInterface         $logger
     */
    public function __construct(SessionServiceInterface $sessionService, LoggerInterface $logger)
    {
        $this->sessionService = $sessionService;
        $this->logger         = $logger;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $startTime       = microtime(true);
        $cleanedSessions = 0;
        $this->logger->info('------- Start Cleanup ---------');

        try {
            $cleanedSessions = $this->sessionService->cleanExpiredSessions();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        $executionTime = round((microtime(true) - $startTime), 2);
        $this->logger->info(sprintf('------- End Cleanup (execution time: %s sec, cleaned sessions: %d)  ---------', $executionTime, $cleanedSessions));

        return $this;
    }
}
