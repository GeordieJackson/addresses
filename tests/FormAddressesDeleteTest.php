<?php
    
    use GeordieJackson\Addresses\Models\Address;
    use GeordieJackson\Addresses\Tests\TestModels\Supplier;
    
    beforeEach(function() {
        $this->migrate();
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
    
    it("detaches an address when its input fields are all blank, but does not delete the address from more than 1 input", function() {
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