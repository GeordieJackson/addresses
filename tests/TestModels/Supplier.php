<?php
    
    namespace GeordieJackson\Address\Tests\TestModels;
    
    use GeordieJackson\Address\Traits\Addressable;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Supplier extends Model
    {
        use HasFactory, Addressable;
    
        protected $fillable = ['name'];
    }