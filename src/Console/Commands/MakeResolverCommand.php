<?php

namespace LaraWave\LogicAsData\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeResolverCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     * 
     * @var string
     */
    protected $name = 'make:logic-resolver';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Logic-as-Data Target Resolver class';

    /**
     * The type of class being generated
     *
     * @var string
     */
    protected $type = 'Target Resolver';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../../../stubs/resolver.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\LogicAsData\Resolvers';
    }
}
