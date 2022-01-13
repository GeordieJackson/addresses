<?php
    
    use GeordieJackson\Addresses\Models\Address;
    use GeordieJackson\Addresses\Tests\TestModels\Supplier;
    
    it('ignores addresses when not present', function() {
        $this->migrate();
        $this->post('suppliers/store', [
            'name' => 'John Smith Ltd',
        ]);
        $this->assertDatabaseHas('suppliers', ['name' => 'John Smith Ltd']); // Model creates this
        $supplier = Supplier::with('addresses')->first();
        $this->assertEquals(0, $supplier->addresses->count());
    });
    
    it('creates a model and attaches an address', function() {
        $this->migrate();
        $this->post('suppliers/store', [
            'name' => 'John Smith Ltd',
            'addresses' => [
                $this->getKey() => $this->testAddress(postcode: 'AB12 3CD')
            ]
        ]);
    
        $this->assertDatabaseHas('suppliers', ['name' => 'John Smith Ltd']); // Model creates this
        $this->assertDatabaseHas('addresses', ['postcode' => $postcode =  'AB12 3CD']); // Trait creates these
        $supplier = Supplier::with('addresses')->first();
        $this->assertEquals(1, $supplier->addresses->count());
        $this->assertEquals($postcode, $supplier->addresses->first()->postcode);
    });
    
    it('creates a model and attaches multiple addresses', function() {
        $this->migrate();
        $this->post('suppliers/store', [
            'name' => 'John Smith Ltd',
            'addresses' => [
                $this->getKey() => $this->testAddress(postcode: 'AB12 3CD'),
                $this->getKey() => $this->testAddress(postcode: 'ZY98 7XW'),
            ]
        ]);
        
        $this->assertDatabaseHas('suppliers', ['name' => 'John Smith Ltd']);
        $this->assertDatabaseHas('addresses', ['postcode' => 'AB12 3CD']);
        $this->assertDatabaseHas('addresses', ['postcode' => 'ZY98 7XW']);
        
        $supplier = Supplier::with('addresses')->first();
        $this->assertEquals(2, $supplier->addresses->count());
        $this->assertEquals('AB12 3CD', $supplier->addresses->first()->postcode);
        $this->assertEquals('ZY98 7XW', $supplier->addresses->last()->postcode);
    });
    
    it('creates a model and attaches an existing address', function() {
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
    
    it('creates a model detects and attaches an existing address from partial fields', function() {
        $this->migrate();
        $address = Address::factory()->create([
            Address::KEYS['name'] => null,
            Address::KEYS['address'] => 'London',
            Address::KEYS['postcode'] => 'SW1 1SW',
        ]);
        $this->assertEquals(1, Address::count());
        $this->post('suppliers/store', [
            'name' => 'John Smith Ltd',
            'addresses' => [
                $this->getKey() => $this->testAddress(address_id: $address->id, address: $address->address, postcode: $address->postcode)
            ]
        ]);
    
        $supplier = Supplier::with('addresses')->first();
        $this->assertEquals(1, $supplier->addresses->count());
        $this->assertEquals($address->name, $supplier->addresses->first()->name);
    });
    
    it('creates a model and creates a new address when partial fields dont all match and the addressID is missing', function() {
        $this->migrate();
        $address = Address::factory()->create([
            Address::KEYS['name'] => null,
            Address::KEYS['address'] => 'London',
            Address::KEYS['postcode'] => 'SW1 1SW',
        ]);
        $this->assertEquals(1, Address::count());
        $this->post('suppliers/store', [
            'name' => 'John Smith Ltd',
            'addresses' => [
                $this->getKey() => $this->testAddress(
                    name: $name =  '221B Baker Street', // Does not match with $address->name
                    address: $address->address,
                    postcode: $address->postcode
                )
            ]
        ]);
    
        $supplier = Supplier::with('addresses')->first();
        $this->assertEquals(1, $supplier->addresses->count());
        $this->assertEquals($name, $supplier->addresses->first()->name);
    });
    
    /**
     * @NOTE: This probably won't occur in practice, but this would be the correct behaviour.
     */
    it('creates a model and attaches an address and updates fields when partial fields dont all match but the addressID is present', function() {
        $this->migrate();
        $address = Address::factory()->create([
            Address::KEYS['name'] => null,
            Address::KEYS['address'] => 'London',
            Address::KEYS['postcode'] => 'SW1 1SW',
        ]);
        $this->assertEquals(1, Address::count());
        $this->post('suppliers/store', [
            'name' => 'John Smith Ltd',
            'addresses' => [
                $this->getKey() => $this->testAddress(
                    address_id: $address->id,
                    name: $name =  '221B Baker Street', // Does not match with $address->name
                    address: $address->address,
                    postcode: $address->postcode
                )
            ]
        ]);
        
        $supplier = Supplier::with('addresses')->first();
        $this->assertEquals(1, $supplier->addresses->count());
        $this->assertEquals($name, $supplier->addresses->first()->name);
    });