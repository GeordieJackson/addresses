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
            $supplier = Supplier::findOrFail($request->id);
            $supplier->fill($request->all())->save();
            
            $supplier->syncAddresses();
        }
    }