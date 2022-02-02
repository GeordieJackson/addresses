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
        
        public function attachAddresses() : void
        {
            $addresses = Addresses::viaRequest();
            $this->addresses()->attach(ExtractIds::from($addresses));
        }
        
        public function syncAddresses() : void
        {
            $addresses = Addresses::viaRequest();
//            dd($addresses);
            $this->addresses()->sync(ExtractIds::from($addresses));
        }
    }