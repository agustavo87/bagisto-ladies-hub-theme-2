<?php

declare(strict_types = 1);

namespace Arete\LadiesHub\Generators;

use Faker\Factory as Faker;
use \Illuminate\Contracts\Foundation\Application as LaravelApplication;

abstract class GenerateEntity {

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    public function __construct(
        LaravelApplication $app,
        Faker $faker
    ) {
        $this->faker = $faker->create();
        $this->app = $app;
        $this->boot();
    }

    /**
     * Bootstraps class properties
     */
    protected function boot() {}

    /**
     * Creates the entity
     * 
     * @return mixed
     */
    abstract public function create();
}