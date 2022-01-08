<?php
    
    namespace GeordieJackson\Address\Tests;
    
    use GeordieJackson\Address\AddressServiceProvider;
    use GeordieJackson\Address\Models\Address;
    use GeordieJackson\Address\Models\Extension;
    use GeordieJackson\Address\Tests\TestModels\Supplier;
    use GeordieJackson\Address\Tests\TestModels\User;
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
                    Route::put('store', [SupplierController::class, 'store']);
                    Route::put('update', [SupplierController::class, 'update']);
                });
            });
        }
        
        protected function setUp() : void
        {
            parent::setUp();
            
            Factory::guessFactoryNamesUsing(
                fn(string $modelName
                ) => 'GeordieJackson\\Address\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
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
            string $postcode = null,
            array $country = null,
            string $delete = null,
        ) {
            $address = Address::factory()->raw([
                'name' => $name,
                'address' => $address,
                'postcode' => $postcode,
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
