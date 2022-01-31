<?php
    
    namespace GeordieJackson\Addresses\Pipes;
    
    use Closure;
    use GeordieJackson\Addresses\Models\Address;
    use Illuminate\Support\Collection;
    
    class GetAddressModels
    {
        public function handle(Collection $addresses, Closure $next)
        {
            $models = $addresses->map(function($address) {
                $model = $this->getModelFrom($address);
                $model->fill($address);
                $model->save();
                return $model;
            });
            
            return $next($models);
        }
        
        protected function getModelFrom(array $address) : Address
        {
            if(empty($address['address_id'])) {
                return Address::firstOrCreate([
                    Address::KEYS['name'] => $address[Address::KEYS['name']],
                    Address::KEYS['address'] => $address[Address::KEYS['address']],
                    Address::KEYS['code'] => $address[Address::KEYS['code']],
                ], $address);
            }
            
            return Address::findOrFail($address['address_id']);
        }
    }