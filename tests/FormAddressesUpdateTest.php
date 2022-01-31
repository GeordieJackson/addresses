<?php

    use GeordieJackson\Addresses\Models\Address;
    use GeordieJackson\Addresses\Tests\TestModels\Supplier;
    
    beforeEach(function() {
        $this->migrate();
    });
    
    it("can update an address", function() {
        $this->post('suppliers/store', [
            'name' => 'John Smith Ltd',
            'addresses' => [
                $this->getKey() => $this->testAddress(code: 'AB12 3CD'),
            ]
        ]);
        
        $supplier = Supplier::with('addresses')->first();
        $address = $supplier->addresses->first();
        $this->patch('suppliers/update', [
            'id' => $supplier->id,
            'name' => 'John Smith updated Ltd',
            'addresses' => [
                $this->getKey() => $this->testAddress(address_id: $address->id, address: "221B Baker Street", code: $address->code),
            ],
        ]);
    
        $supplier->refresh()->with('addresses')->first();
        expect($supplier->addresses->first()->address)->toBe("221B Baker Street");
    });
    
    it("can update multiple addresses", function() {
        $this->post('suppliers/store', [
            'name' => 'John Smith Ltd',
            'addresses' => [
                $this->getKey() => $this->testAddress(address: 'Address 1'),
                $this->getKey() => $this->testAddress(address: 'Address 2'),
                $this->getKey() => $this->testAddress(address: 'Address 3'),
                $this->getKey() => $this->testAddress(address: 'Address 4'),
                $this->getKey() => $this->testAddress(address: 'Address 5'),
            ]
        ]);
        
        $supplier = Supplier::with('addresses')->first();
        $addresses = $supplier->addresses;

        $this->patch('suppliers/update', [
            'id' => $supplier->id,
            'name' => 'John Smith updated Ltd',
            'addresses' => [
                $this->getKey() => $this->testAddress(address_id: $addresses[0]->id, address: "221B Baker Street", code: $addresses[0]->code),
                $this->getKey() => $this->testAddress(address_id: $addresses[1]->id, address: $addresses[1]->address, code: $addresses[1]->code),
                $this->getKey() => $this->testAddress(address_id: $addresses[2]->id, address: "10, Rillington Place", code: $addresses[2]->code),
                $this->getKey() => $this->testAddress(address_id: $addresses[3]->id, address: $addresses[3]->address, code: $addresses[3]->code),
                $this->getKey() => $this->testAddress(address_id: $addresses[4]->id, address: $addresses[4]->address, code: $addresses[4]->code),
            ],
        ]);
        
        $supplier->refresh()->with('addresses')->get();
        
        expect($supplier->addresses[0]->address)->toBe("221B Baker Street");
        expect($supplier->addresses[2]->address)->toBe("10, Rillington Place");
        expect($supplier->addresses[4]->address)->toBe($addresses[4]->address);
        expect($supplier->addresses->count())->toBe(5);
    });
    
    
    it("will match an existing model by partial fields and attach it to current model", function() {
        $address = Address::factory()->create();
    
        $this->post('suppliers/store', [
            'name' => 'John Smith Ltd',
            'addresses' => [
                $this->getKey() => $this->testAddress(name: $address->name, address: $address->address, code: $address->code),
            ]
        ]);
        
        $supplier = Supplier::with('addresses')->first();
        expect($supplier->addresses->first()->address)->toBe($address->address);
        expect(Address::count())->toBe(1);
    });
    
    /**
     *  Should the fields be updated too?
     *
     *  Answer: Yes, as the fields would be prefilled from an existing address
     *  i.e. if they're different, they must have been edited.
     */
    it('matches and attaches an existing address with an ID', function() {
        $address = Address::factory()->create([
            Address::KEYS['name'] => '221B Baker Street',
        ]);
        $this->assertDatabaseHas('addresses', ['name' => '221B Baker Street']);
        $this->post('suppliers/store', [
            'name' => 'John Smith Ltd',
            'addresses' => [
                $this->getKey() => $this->testAddress(address_id: $address->id, name: $address->name)
            ]
        ]);
        $supplier = Supplier::with('addresses')->first();
        $this->assertEquals(1, $supplier->addresses->count());
        $this->assertEquals($address->name, $supplier->addresses->first()->name);
    });
    
    /**
     * @NOTE: This probably won't occur in practice, but this would be the correct behaviour.
     */
    it('attaches and updates an address when addressID is present and form is not empty', function() {
        
        $address = Address::factory()->create([
            Address::KEYS['name'] => null,
            Address::KEYS['address'] => 'London',
            Address::KEYS['code'] => 'SW1 1SW',
        ]);
        
        $this->post('suppliers/store', [
            'name' => 'John Smith Ltd',
            'addresses' => [
                $this->getKey() => $this->testAddress(
                    address_id: $address->id,
                    name: $name =  '221B Baker Street', // Does not match with $address->name
                    address: $address->address,
                    code: $address->code
                )
            ]
        ]);
        
        $this->assertEquals(1, Address::count());
        $supplier = Supplier::with('addresses')->first();
        $this->assertEquals(1, $supplier->addresses->count());
        $this->assertEquals($name, $supplier->addresses->first()->name);
    });