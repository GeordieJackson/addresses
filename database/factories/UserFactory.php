<?php
    
    namespace GeordieJackson\Addresses\Database\Factories;
    
    use GeordieJackson\Addresses\Tests\TestModels\User;
    use Illuminate\Database\Eloquent\Factories\Factory;

    class UserFactory extends Factory
    {
        protected $model = User::class;
        
        public function definition()
        {
            return [
                'name' => $this->faker->firstName,
            ];
        }
    }