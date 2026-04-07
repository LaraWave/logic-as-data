<?php

namespace LaraWave\LogicAsData\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeExtractorCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     * 
     * @var string
     */
    protected $name = 'make:logic-extractor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Logic-as-Data Source Extractor class';

    /**
     * The type of class being generated
     *
     * @var string
     */
    protected $type = 'Source Extractor';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../../../stubs/extractor.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\LogicAsData\Extractors';
    }
}
