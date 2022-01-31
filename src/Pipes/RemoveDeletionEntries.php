<?php
    
    namespace GeordieJackson\Addresses\Pipes;
    
    use Closure;
    use GeordieJackson\Addresses\Models\Address;
    use Illuminate\Support\Collection;
    
    class RemoveDeletionEntries
    {
        public function handle(Collection $addresses, Closure $next)
        {
            $filteredAddresses = $addresses->map(function($address) {
                if(isset($address['delete']) && isset($address['address_id']) && $address['delete'] && $address['address_id']) {
                    Address::findOrFail($address['address_id'])->delete();
                    return null;
                }
                return $address;
            })->reject(fn($address) => $address === null);
            
            return $next($filteredAddresses);
        }
    }