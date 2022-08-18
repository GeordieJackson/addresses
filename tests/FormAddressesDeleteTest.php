<?php
    
    use GeordieJackson\Addresses\Models\Address;
    use GeordieJackson\Addresses\Tests\TestModels\Supplier;
    use Illuminate\Support\Facades\DB;
    
    beforeEach(function() {
        $this->migrate();
    });
    
    it("can detach ALL addresses at once", function() {
        $supplier = Supplier::factory()->create();
        $id = $supplier->id;
        $address1 = Address::factory()->create();
        $address2 = Address::factory()->create();
        $supplier->addresses()->attach($address1);
        $supplier->addresses()->attach($address2);
        
        /*        We're checking for all addresses to be detached
                   We're using this scenario as this is when it's most likely to be used. i.e. before a parent model's deletion
         */
    
        expect(DB::table('addressables')->where('addressable_id',
            $id)->count())->toBe(2); // Addresses not detached
        
        $supplier->addresses()->detach();
        $supplier->delete();
        
        expect(Supplier::count())->toBe(0); // Check supplier is deleted
        expect(Address::count())->toBe(2); // Check addresses remain in Address table
        expect(DB::table('addressables')->where('addressable_id',
            $id)->count())->toBe(0); // Check that the addresses were detached before the model was deleted
        
    });
    
    it("detaches an address when its input fields are all blank, but does not delete the address", function() {
        $supplier = Supplier::factory()->create();
        $address1 = Address::factory()->create();
        $supplier->addresses()->attach($address1);
        
        $this->patch('suppliers/update', [
            'id' => $supplier->id,
            'name' => $supplier->name,
            'addresses' => [
                $this->getKey() => $this->testAddress(
                    address_id: $address1->id,
                ),
                $this->getKey() => $this->testAddress(), // Simulate the blank add address form row
            ],
        ]);
        
        $supplier = Supplier::with('addresses')->first();
        expect($supplier->addresses->count())->toBe(0);
        expect(Address::count())->toBe(1);
    });
    
    it("detaches an address when its input fields are all blank, but does not delete the address from more than 1 input",
        function() {
            $supplier = Supplier::factory()->create();
            $address1 = Address::factory()->create();
            $address2 = Address::factory()->create();
            $supplier->addresses()->attach($address1);
            $supplier->addresses()->attach($address2);
            
            $this->patch('suppliers/update', [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'addresses' => [
                    $this->getKey() => $this->testAddress(
                        address_id: $address1->id,
                    ),
                    $this->getKey() => $this->testAddress(
                        address_id: $address2->id,
                        name: $address2->name,
                        address: $address2->address,
                        code: $address2->code
                    ),
                    $this->getKey() => $this->testAddress(), // Simulate the blank add address form row
                ],
            ]);
            
            $supplier = Supplier::with('addresses')->first();
            expect($supplier->addresses->count())->toBe(1);
            expect(Address::count())->toBe(2);
        });
    
    it("deletes an address when its delete checkbox is checked", function() {
        $supplier = Supplier::factory()->create();
        $address1 = Address::factory()->create();
        $supplier->addresses()->attach($address1);
        
        $this->patch('suppliers/update', [
            'id' => $supplier->id,
            'name' => $supplier->name,
            'addresses' => [
                $this->getKey() => $this->testAddress(
                    address_id: $address1->id,
                    name: $address1->name,
                    delete: 1,
                ),
                $this->getKey() => $this->testAddress(), // Simulate the blank add address form row
            ],
        ]);
        
        $supplier = Supplier::with('addresses')->first();
        expect($supplier->addresses->count())->toBe(0);
        expect(Address::count())->toBe(0);
    });