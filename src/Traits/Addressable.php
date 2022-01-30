<?php
    
    namespace GeordieJackson\Addresses\Traits;
    
    use GeordieJackson\Addresses\Models\Address;
    use GeordieJackson\Addresses\Models\Extension;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    use Illuminate\Support\Collection;
    
    use function collect;
    use function is_iterable;
    use function is_nameeger;
    use function is_null;
    use function is_object;
    use function request;
    
    trait Addressable
    {
        public function addresses() : MorphToMany
        {
            return $this->morphToMany(Address::class, 'addressable');
        }
        
        /*
|--------------------------------------------------------------------------
| Public interface
|--------------------------------------------------------------------------
|
| Methods callable by the consumer
|
*/
        public function attachAddresses() : void
        {
            $addresses = $this->processAddresses();
            $this->addresses()->attach($this->extractIdsFrom($addresses));
        }
        
        public function syncAddresses() : void
        {
            $addresses = $this->processAddresses();
            $this->addresses()->sync($this->extractIdsFrom($addresses));
        }
        
        /*
        |--------------------------------------------------------------------------
        | Ends public interface
        |--------------------------------------------------------------------------
        */
        
        protected function processAddresses()
        {
            return collect(request()->addresses)
                ->reject(fn($addressEntry) => $this->isBlank($addressEntry))
                ->map(function($addressEntry) {
                    $addressModel = $this->addressModelFrom($addressEntry);
                    $this->saveOrDelete($addressEntry, $addressModel);
                    return $addressModel;
                })
                ->reject(fn($addressModel) => is_null($addressModel->id));
        }
        
        protected function addressModelFrom(array $addressEntry) : Address
        {
            if(empty($addressEntry['address_id'])) {
                return Address::firstOrCreate([
                    Address::KEYS['name'] => $addressEntry[Address::KEYS['name']],
                    Address::KEYS['address'] => $addressEntry[Address::KEYS['address']],
                    Address::KEYS['code'] => $addressEntry[Address::KEYS['code']],
                ], $addressEntry);
            }
            
            return Address::findOrFail($addressEntry['address_id']);
        }
        
        protected function saveOrDelete(array $addressEntry, Address $addressModel) : void
        {
            if(empty($addressEntry['delete'])) {
                $addressModel->fill($addressEntry);
                $addressModel->save();
            } else {
                $addressModel->delete();
                $addressModel->id = null;
            }
        }
        
        protected function isBlank($address)
        {
            return ($address[Address::KEYS['code']] || $address[Address::KEYS['address']] || $address[Address::KEYS['name']])
                ? false
                : true;
        }
        
        /**
         *  This is used to allow the passing of nameegers, objects, arrays, and collections
         *  as parameters for updating BelongsToMany relationships.
         *
         *  It simply returns the ID(s) of the passed in parameters as they work
         *  in all circumstances.
         *
         * @param $input
         * @return collection|name
         */
        protected function extractIdsFrom($input)
        {
            if(is_iterable($input)) { // Collections or arrays
                return collect($input)->map(function($value) {
                    return $value->id ?? $value; // object or nameeger
                });
            } elseif(is_nameeger($input)) { // Single Id
                return $input;
            } elseif(is_object($input)) { // Single object
                return $input->id;
            }
        }
    }