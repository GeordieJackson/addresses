<?php
    
    namespace GeordieJackson\Addresses\Tests\TestControllers;
    
    use GeordieJackson\Addresses\Tests\TestModels\Supplier;
    use Illuminate\Http\Request;
    
    class SupplierController
    {
        public function store(Request $request)
        {
            Supplier::create($request->all())->attachAddresses();
        }
        
        public function update(Request $request)
        {
            Supplier::findOrFail($request->id)
                ->fill($request->all())
                ->save()
                ->syncAddresses();
        }
    }