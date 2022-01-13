<?php
    
    namespace GeordieJackson\Addresses\Tests\TestModels;
    
    use GeordieJackson\Addresses\Traits\Addressable;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Supplier extends Model
    {
        use HasFactory, Addressable;
    
        protected $fillable = ['name'];
    }