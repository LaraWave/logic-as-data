<?php

namespace LaraWave\LogicAsData\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeOperatorCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     * 
     * @var string
     */
    protected $name = 'make:logic-operator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Logic-as-Data Operator class';

    /**
     * The type of class being generated
     *
     * @var string
     */
    protected $type = 'Operator';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../../../stubs/operator.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\LogicAsData\Operators';
    }
}
