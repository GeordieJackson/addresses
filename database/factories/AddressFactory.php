<?php
    
    namespace GeordieJackson\Addresses\Database\Factories;
    
    use GeordieJackson\Addresses\Models\Address;
    use Illuminate\Database\Eloquent\Factories\Factory;
    
    class AddressFactory extends Factory
    {
        protected $model = Address::class;
        
        public function definition()
        {
            return [
                Address::KEYS['name'] => $this->faker->streetAddress,
                Address::KEYS['address'] => $this->faker->address,
                Address::KEYS['postcode'] => $this->faker->postcode,
                Address::KEYS['country'] => $this->faker->country,
            ];
        }
    }

