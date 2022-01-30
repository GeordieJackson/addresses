<?php
    
    use GeordieJackson\Addresses\Models\Address;
    use GeordieJackson\Addresses\Tests\TestModels\Supplier;
    
    /*
     | Address behaviour when an attachable model (e.g. Supplier) is created
     */
    
    it('ignores addresses when form fields are blank', function() {
        $this->migrate();
        $this->post('suppliers/store', [
            'name' => 'John Smith Ltd',
        ]);
        $this->assertDatabaseHas('suppliers', ['name' => 'John Smith Ltd']);
        $supplier = Supplier::with('addresses')->first();
        $this->assertEquals(0, $supplier->addresses->count());
    });
    
    it('creates and saves new address and attaches it to model', function() {
        $this->migrate();
        $this->post('suppliers/store', [
            'name' => 'John Smith Ltd',
            'addresses' => [
                $this->getKey() => $this->testAddress(code: 'AB12 3CD')
            ]
        ]);
    
        $this->assertDatabaseHas('suppliers', ['name' => 'John Smith Ltd']); // Model creates this
        $this->assertDatabaseHas('addresses', ['code' => $code =  'AB12 3CD']); // Trait creates these
        $supplier = Supplier::with('addresses')->first();
        $this->assertEquals(1, $supplier->addresses->count());
        $this->assertEquals($code, $supplier->addresses->first()->code);
    });
    
    it('creates and saves multiple new addresses and attaches them to model', function() {
        $this->migrate();
        $this->post('suppliers/store', [
            'name' => 'John Smith Ltd',
            'addresses' => [
                $this->getKey() => $this->testAddress(code: 'AB12 3CD'),
                $this->getKey() => $this->testAddress(code: 'ZY98 7XW'),
            ]
        ]);
        
        $this->assertDatabaseHas('suppliers', ['name' => 'John Smith Ltd']);
        $this->assertDatabaseHas('addresses', ['code' => 'AB12 3CD', 'code' => 'ZY98 7XW']);
        
        $supplier = Supplier::with('addresses')->first();
        $this->assertEquals(2, $supplier->addresses->count());
        $this->assertEquals('AB12 3CD', $supplier->addresses->first()->code);
        $this->assertEquals('ZY98 7XW', $supplier->addresses->last()->code);
    });
    
    /**
     *  Should the fields be updated too?
     *
     *  Answer: Yes, as the fields would be prefilled from an existing address
     *  i.e. if they're different, they must have been edited.
     */
    it('matches and attaches an existing address with an ID', function() {
        $this->migrate();
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
    
    it('attaches an existing address from partial fields when they all match - no ID', function() {
        $this->migrate();
        $address = Address::factory()->create([
            Address::KEYS['name'] => null,
            Address::KEYS['address'] => 'London',
            Address::KEYS['code'] => 'SW1 1SW',
        ]);
        $this->assertEquals(1, Address::count());
        $this->post('suppliers/store', [
            'name' => 'John Smith Ltd',
            'addresses' => [
                $this->getKey() => $this->testAddress(address: $address->address, code: $address->code)
            ]
        ]);
    
        $supplier = Supplier::with('addresses')->first();
        $this->assertEquals(1, $supplier->addresses->count());
        $this->assertEquals($address->name, $supplier->addresses->first()->name);
    });
    
    it("creates a new address when partial fields don't all match", function() {
        $this->migrate();
        $address = Address::factory()->create([
            Address::KEYS['name'] => null,
            Address::KEYS['address'] => 'London',
            Address::KEYS['code'] => 'SW1 1SW',
        ]);
        
        $this->post('suppliers/store', [
            'name' => 'John Smith Ltd',
            'addresses' => [
                $this->getKey() => $this->testAddress(
                    name: $name =  '221B Baker Street', // Does not match with $address->name
                    address: $address->address,
                    code: $address->code
                )
            ]
        ]);
        
        $this->assertEquals(2, Address::count());
        $supplier = Supplier::with('addresses')->first();
        $this->expect($supplier->addresses->count())->toBe(1);
        $this->assertEquals($name, $supplier->addresses->first()->name);
    });
    
    /**
     * @NOTE: This probably won't occur in practice, but this would be the correct behaviour.
     */
    it('attaches and updates an address when addressID is present and form is not empty', function() {
        $this->migrate();
        
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