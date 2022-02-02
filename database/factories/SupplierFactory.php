<?php
    
    namespace GeordieJackson\Addresses\Database\Factories;
    
    use GeordieJackson\Addresses\Tests\TestModels\Supplier;
    use Illuminate\Database\Eloquent\Factories\Factory;
    
    class SupplierFactory extends Factory
    {
        protected $model = Supplier::class;
        
        /**
         * @inheritDoc
         */
        public function definition()
        {
            return [
                'name' => $this->faker->company,
            ];
        }
    }