<?php

namespace Doinc\PersonaKyc\Base;

trait PersonaBaseInitializer
{
    protected static ?self $instance = null;

    /**
     * Get or initialize a PersonaAccounts instance
     * @return self
     */
    public static function init(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
