<?php
    
    namespace GeordieJackson\Addresses;
    
    use GeordieJackson\Addresses\Commands\AddressCommand;
    use GeordieJackson\Addresses\Models\Address;
    use GeordieJackson\Phone\Models\Phone;
    use Illuminate\Database\Eloquent\Relations\Relation;
    use Illuminate\Support\Collection;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Str;
    use Spatie\LaravelPackageTools\Package;
    use Spatie\LaravelPackageTools\PackageServiceProvider;
    
    use function class_basename;
    use function collect;
    use function config;
    
    class AddressServiceProvider extends PackageServiceProvider
    {
        public function boot()
        {
            $morphMap = $this->getMorphMap();
            $this->resolveRelationsUsing($morphMap);
            Relation::morphMap($morphMap->toArray());
            Schema::defaultStringLength(191); // For index string length with Maria DBs
            
            return parent::boot();
        }
        
        public function configurePackage(Package $package) : void
        {
            $package
                ->name('addresses')
                ->hasConfigFile()
                ->hasViews()
                ->hasMigration('1_create_addresses_table')
                ->hasMigration('2_create_addressables_table');
        }
        
        protected function getMorphMap() : Collection
        {
            $morphMap = collect();
            foreach(config('addresses') as $class => $name) {
                $key = ! empty($name) ? Str::plural($name) : Str::lower(Str::plural(class_basename($class)));
                $morphMap [$key] = $class;
            }
            
            return $morphMap;
        }
        
        protected function resolveRelationsUsing(Collection $morphMap) : void
        {
            $morphMap->each(function($className, $relationName) {
                Address::resolveRelationUsing($relationName, function($addressModel) use ($className) {
                    return $addressModel->morphedByMany($className, 'addressable');
                });
            });
        }
    }
