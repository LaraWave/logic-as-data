<?php

namespace LaraWave\LogicAsData\Actions;

/**
 * * The base class for all side-effects triggered by a successful rule evaluation.
 */
abstract class Action
{
    /**
     * Handle the actions defined in a passing rule.
     *
     * @param string $alias The alias that triggered this action (e.g., 'auth.logout', 'system.log', 'web.redirect' etc)
     * @param array $params The parsed JSON array of action.
     * @param array $context The data payload, in case actions need context.
     * @return mixed The result of the action (e.g., an array of modifications, or firing a Job).
     */
    abstract public function execute(string $alias, array $params, array $context = []): mixed;

    /**
     * Provide the UI configuration for this component.
     * This method returns the metadata required by the frontend to 
     * render the appropriate labels, help text and dynamic input fields.
     *
     * @param  string  $alias  The registered alias for this class (e.g., 'system.config')
     * @return array{label: string, description: string, fields: array}
     */
    public static function metadata(string $alias): array
    {
        return [
            // label to show in the UI
            'label' => Str::headline(str_replace('.', ' ', $alias)),

            // description shown below the field in the UI
            'description' => '',

            // The array of input fields required to configure this specific component
            'fields' => [],
        ];
    }
}
