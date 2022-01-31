<?php
    
    namespace GeordieJackson\Addresses\Tests;
    
    use GeordieJackson\Addresses\AddressServiceProvider;
    use GeordieJackson\Addresses\Models\Address;
    use GeordieJackson\Addresses\Models\Extension;
    use GeordieJackson\Addresses\Tests\TestControllers\SupplierController;
    use GeordieJackson\Addresses\Tests\TestModels\Supplier;
    use GeordieJackson\Addresses\Tests\TestModels\User;
    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Schema;
    use Orchestra\Testbench\TestCase as Orchestra;
    
    use function config;
    use function random_int;
    
    class TestCase extends Orchestra
    {
        public function getEnvironmentSetUp($app)
        {
            config()->set('addresses', [
                User::class => 'users',
                Supplier::class => 'suppliers',
            ]);
            
            Route::macro('suppliers', function(string $baseUrl = 'suppliers') {
                Route::prefix($baseUrl)->group(function() {
                    Route::get('create', [SupplierController::class, 'create']);
                    Route::post('store', [SupplierController::class, 'store']);
                    Route::put('update', [SupplierController::class, 'update']);
                    Route::patch('update', [SupplierController::class, 'update']);
                });
            });
        }
        
        protected function setUp() : void
        {
            parent::setUp();
            
            Factory::guessFactoryNamesUsing(
                fn(string $modelName
                ) => 'GeordieJackson\\Addresses\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
            );
        }
        
        protected function getPackageProviders($app)
        {
            return [
                AddressServiceProvider::class,
            ];
        }
        
        protected function migrate()
        {
            Schema::dropAllTables();
            
            $migration = include __DIR__ . '/../database/migrations/1_create_addresses_table.php.stub';
            $migration->up();
            
            $migration = include __DIR__ . '/../database/migrations/2_create_addressables_table.php.stub';
            $migration->up();
            
            $migration = include __DIR__ . '/TestMigrations/create_users_table.php.stub';
            $migration->up();
            
            $migration = include __DIR__ . '/TestMigrations/create_suppliers_table.php.stub';
            $migration->up();
        }
        
        protected function getKey()
        {
            return random_int(100000000000, 999999999999);
        }
        
        protected function testAddress(
            int $address_id = null,
            string $name = null,
            string $address = null,
            string $code = null,
            string $country = null,
            string $delete = null,
        ) {
            $address = Address::factory()->raw([
                Address::KEYS['name'] => $name,
                Address::KEYS['address'] => $address,
                Address::KEYS['code'] => $code,
                Address::KEYS['country'] => $country,
            ]);
            
            if($address_id) {
                $address['address_id'] = $address_id;
            }
            
            if($delete) {
                $address['delete'] = 1;
            }
            
            return $address;
        }
    }
