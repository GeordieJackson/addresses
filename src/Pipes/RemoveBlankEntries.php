<?php
    
    namespace GeordieJackson\Addresses\Pipes;
    
    use Closure;
    use GeordieJackson\Addresses\Models\Address;
    use Illuminate\Support\Collection;
    
    class RemoveBlankEntries
    {
        public function handle(Collection $addresses, Closure $next)
        {
            $addresses = $addresses->reject(fn($address) => $this->isBlank($address));
            
            return $next($addresses);
        }
        
        protected function isBlank($address)
        {
            return (
                $address[Address::KEYS['code']] ||
                $address[Address::KEYS['address']] ||
                $address[Address::KEYS['name']]
            )
                ? false
                : true;
        }
    }