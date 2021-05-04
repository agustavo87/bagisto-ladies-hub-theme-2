<?php

namespace Arete\LadiesHub\Commands;

use Illuminate\Console\Command;

class GenerateProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ladieshub:generate {value} {quantity} {--class=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Products for Ladies Hub';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        if($this->option('class') == 'default') {
            $productClass = \Arete\LadiesHub\Generators\GenerateGenericProduct::class;
        } else {
            $productClass = $this->option('class');
        }
        $generateProduct = app($productClass);
        $this->line("generando: {$productClass}") ;

        
        if (! is_string($this->argument('value')) || ! is_numeric($this->argument('quantity'))) {
            $this->info('Illegal parameters or value of parameters are passed');
        } else {
            
            if (strtolower($this->argument('value')) == 'product' || strtolower($this->argument('value')) == 'products') {
                $quantity = (int)$this->argument('quantity');

                $bar = $this->output->createProgressBar($quantity);

                $this->line("Generating $quantity {$this->argument('value')}.");

                $bar->start();

                $generatedProducts = 0;

                while ($quantity > 0) {
                    try {
                        $result = $generateProduct->create();
                        $generatedProducts++;
                        $bar->advance();
                    } catch (\Exception $e) {
                        report($e);
                        continue;
                    }

                    $quantity--;
                }

                if ($result) {
                    $bar->finish();
                    $this->info("\n$generatedProducts Product(s) created successfully.");
                } else {
                    $this->info('Product(s) cannot be created successfully.');
                }
            } else {
                $this->line('Sorry, this generate option is invalid.');
            }
        }
    }
}
