<?php

namespace Doinc\PersonaKyc\Base;

trait PersonaBaseInitializer
{
    protected static ?self $instance = null;

    /**
     * Get or initialize a PersonaAccounts instance
     * @return static
     */
    public static function init(): static
    {
        if (is_null(self::$instance)) {
            static::$instance = new static();
        }
        return self::$instance;
    }
}
