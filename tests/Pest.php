<?php
    
    use GeordieJackson\Addresses\Tests\TestCase;
    use Illuminate\Support\Facades\Route;
    
    uses(TestCase::class)
        ->beforeEach(function() {
            Route::suppliers();
        })
        ->in(__DIR__);
