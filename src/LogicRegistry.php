<?php

namespace LaraWave\LogicAsData;

use LaraWave\LogicAsData\Extractors\SourceExtractor;
use LaraWave\LogicAsData\Resolvers\TargetResolver;
use LaraWave\LogicAsData\Evaluators\Evaluator;
use LaraWave\LogicAsData\Operators\Operator;
use LaraWave\LogicAsData\Actions\Action;
use InvalidArgumentException;

class LogicRegistry
{
    /** @var array<string, string> */
    protected array $extractors = [];

    /** @var array<string, string> */
    protected array $operators = [];

    /** @var array<string, string> */
    protected array $resolvers = [];

    /** @var array<string, string> */
    protected array $evaluators = [];

    /** @var array<string, string> */
    protected array $actions = [];

    // -------------------------------------------------------------------------
    // Registration Methods
    // -------------------------------------------------------------------------

    /**
     * Register a Source Extractor.
     *
     * @param string $key The source key (e.g., 'user.email', 'cart.total', 'order.status')
     * @param string $class The class that handle this source
     */
    public function registerExtractor(string $key, string $class): self
    {
        $this->extractors[$key] = $class;

        return $this;
    }

    /**
     * Register a Logic Operator.
     *
     * @param string $key The operator key (e.g., 'equals', 'contains', 'less_than')
     * @param string $class The class that handle this operator
     */
    public function registerOperator(string $key, string $class): self
    {
        $this->operators[$key] = $class;

        return $this;
    }

    /**
     * Register a Target Resolver.
     *
     * @param string $key The target key (e.g., 'system.config')
     * @param string $class The class that handle this target
     */
    public function registerResolver(string $key, string $class): self
    {
        $this->resolvers[$key] = $class;

        return $this;
    }

    /**
     * Register an Evaluator.
     *
     * @param string $key The evaluator key (e.g., 'default')
     * @param string $class The class that handle this evaluator
     */
    public function registerEvaluator(string $key, string $class): self
    {
        $this->evaluators[$key] = $class;

        return $this;
    }

    /**
     * Register an Action.
     *
     * @param string $key The action key (e.g., 'auth.logout', 'system.log', 'web.redirect')
     * @param string $class The class that handle this action
     */
    public function registerAction(string $key, string $class): self
    {
        $this->actions[$key] = $class;

        return $this;
    }

    // -------------------------------------------------------------------------
    // Resolution Methods (Factory)
    // -------------------------------------------------------------------------

    /**
     * Resolve an Extractor instance.
     *
     * @param string $key The string from JSON 'source' key (e.g., 'cart.total', 'order.status')
     * @throws InvalidArgumentException
     */
    public function extractor(string $key): SourceExtractor
    {
        if (! isset($this->extractors[$key])) {
            throw new InvalidArgumentException("LogicRegistry: Operator [{$key}] not found.");
        }

        return app($this->extractors[$key]);
    }

    /**
     * Resolve an Operator instance.
     *
     * @param string $key The string from JSON 'operator' key (e.g., 'equals', 'ends_with')
     * @throws InvalidArgumentException
     */
    public function operator(string $key): Operator
    {
        if (! isset($this->operators[$key])) {
            throw new InvalidArgumentException("LogicRegistry: Operator [{$key}] not found.");
        }

        return app($this->operators[$key]);
    }

    /**
     * Resolve a Resolver instance.
     *
     * @param string $key The string from JSON 'target.resolver' key (e.g., 'system.config')
     * @throws InvalidArgumentException
     */
    public function resolver(string $key): TargetResolver
    {
        if (! isset($this->resolvers[$key])) {
            throw new InvalidArgumentException("LogicRegistry: Resolver [{$key}] not found.");
        }

        return app($this->resolvers[$key]);
    }

    /**
     * Resolve an Evaluator instance.
     *
     * @param string $key The string alias of evaluator class (e.g., 'system.config')
     * @throws InvalidArgumentException
     */
    public function evaluator(string $key = 'default'): Evaluator
    {
        if (! isset($this->evaluators[$key])) {
            throw new InvalidArgumentException("LogicRegistry: Evaluator [{$key}] not found.");
        }

        return app($this->evaluators[$key]);
    }

    /**
     * Resolve an Action instance.
     *
     * @param string $key The string from JSON 'action' key (e.g., 'web.redirect', 'auth.logout')
     * @throws InvalidArgumentException
     */
    public function action(string $key): Action
    {
        if (! isset($this->actions[$key])) {
            throw new InvalidArgumentException("LogicRegistry: Action [{$key}] not found.");
        }

        return app($this->actions[$key]);
    }

    // -------------------------------------------------------------------------
    // Introspection Methods
    // -------------------------------------------------------------------------

    /**
     * Get all registered extractors.
     */
    public function extractors(): array
    {
        return $this->extractors;
    }

    /**
     * Get all registered operators.
     */
    public function operators(): array
    {
        return $this->operators;
    }

    /**
     * Get all registered resolvers.
     */
    public function resolvers(): array
    {
        return $this->resolvers;
    }

    /**
     * Get all registered evaluators.
     */
    public function evaluators(): array
    {
        return $this->evaluators;
    }

    /**
     * Get all registered actions.
     */
    public function actions(): array
    {
        return $this->actions;
    }
}
