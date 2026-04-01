<?php

namespace LaraWave\LogicAsData\Enums;

/**
 * Represents the lifecycle stages of a Logic Rule.
 */
enum RuleStatus: string
{
    /**
     * The rule is incomplete and should not be evaluated.
     */
    case DRAFT = 'draft';

    /**
     * The rule is fully operational and evaluated by the engine.
     */
    case ACTIVE = 'active';

    /**
     * The rule is temporarily disabled but might be turned back on.
     */
    case INACTIVE = 'inactive';

    /**
     * The rule is only evaluated in testing/staging environments, 
     * or for specific test users.
     */
    case TESTING = 'testing';

    /**
     * The rule is permanently retired but kept for historical records.
     */
    case ARCHIVED = 'archived';
}
