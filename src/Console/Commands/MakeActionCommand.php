<?php

namespace LaraWave\LogicAsData\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeActionCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     * 
     * @var string
     */
    protected $name = 'make:logic-action';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Logic-as-Data Action class';

    /**
     * The type of class being generated
     *
     * @var string
     */
    protected $type = 'Action';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../../../stubs/action.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\LogicAsData\Actions';
    }
}
