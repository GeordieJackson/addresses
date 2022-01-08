<?php
    
    namespace GeordieJackson\Address\Database\Factories;
    
    use GeordieJackson\Address\Tests\TestModels\User;
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