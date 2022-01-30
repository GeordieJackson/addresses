<?php
    
    namespace GeordieJackson\Addresses\Models;
    
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Collection;
    use Illuminate\Support\Facades\DB;
    
    use function collect;
    use function config;
    
    class Address extends Model
    {
        use HasFactory;
        
        public const KEYS = [
            'name' => 'name', 'address' => 'address', 'code' => 'code', 'country' => 'country',
        ];
        
        protected $fillable = [
            self::KEYS['name'], self::KEYS['address'], self::KEYS['code'], self::KEYS['country'],
        ];
        
        /**
         *  Return the owners/parent models for an address
         *
         * @return \Illuminate\Support\Collection
         */
        public function owners() : Collection
        {
            $owners = collect();
            foreach(collect(config('addresses'))->values() as $relationship) {
                foreach($this->$relationship as $owner) {
                    $owners->push($owner);
                }
            }
            
            return $owners;
        }
        
        /**
         *  Delete polymorphic relations when an address is deleted
         */
        protected static function boot() : void
        {
            parent::boot();
            
            static::deleting(function($address) {
                DB::table('addressables')->where('address_id', $address->id)->delete();
            });
        }
    }