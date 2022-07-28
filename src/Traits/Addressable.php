<?php
    
    namespace GeordieJackson\Addresses\Traits;
    
    use GeordieJackson\Addresses\Addresses;
    use GeordieJackson\Addresses\Helpers\ExtractIds;
    use GeordieJackson\Addresses\Models\Address;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    
    trait Addressable
    {
        public function addresses() : MorphToMany
        {
            return $this->morphToMany(Address::class, 'addressable');
        }
        
        public function attachAddresses() : static
        {
            $addresses = Addresses::viaRequest();
            $this->addresses()->attach(ExtractIds::from($addresses));
            
            return $this;
        }
        
        public function syncAddresses() : static
        {
            $addresses = Addresses::viaRequest();
            $this->addresses()->sync(ExtractIds::from($addresses));
            
            return $this;
        }
    }