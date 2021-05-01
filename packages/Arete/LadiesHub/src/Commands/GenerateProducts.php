<?php

namespace Arete\LadiesHub\Commands;

use Arete\LadiesHub\Generators\GenerateDemoBrand;
use Illuminate\Console\Command;
use Arete\LadiesHub\Generators\GenerateGenericProduct;

class GenerateProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ladieshub:generate {value} {quantity}';

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
    public function handle(
        GenerateGenericProduct $generateProduct, 
        GenerateDemoBrand $generateBrand
    ) {
        if (! is_string($this->argument('value')) || ! is_numeric($this->argument('quantity'))) {
            $this->info('Illegal parameters or value of parameters are passed');
        } else {
            
            if (strtolower($this->argument('value')) == 'product' || strtolower($this->argument('value')) == 'products') {
                $quantity = (int)$this->argument('quantity');

                $bar = $this->output->createProgressBar($quantity);

                $this->line("Generating $quantity {$this->argument('value')}.");

                $bar->start();

                $generatedProducts = 0;
                $brand = $generateBrand->create("Ladies Hub Joy Gifts");

                while ($quantity > 0) {
                    try {
                        $result = $generateProduct->create($brand->id);
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
