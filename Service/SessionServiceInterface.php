<?php

declare(strict_types = 1);

namespace WeProvide\SessionCleaner\Service;

interface SessionServiceInterface
{
    /**
     * Cleans expired sessions and returns the number of sessions that were expired.
     *
     * @return int number of sessions that were expired
     */
    public function cleanExpiredSessions(): int;
}
