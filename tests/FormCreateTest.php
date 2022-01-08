<?php
    
    use GeordieJackson\Address\Tests\TestModels\Supplier;
    
    it('ignores addresses when not present', function() {
        $this->migrate();
        $this->put('suppliers/store', [
            'name' => 'John Smith Ltd',
        ]);
        $this->assertDatabaseHas('suppliers', ['name' => 'John Smith Ltd']); // Model creates this
        $supplier = Supplier::with('addresses')->first();
        $this->assertEquals(0, $supplier->addresses->count());
    });
    
    it('stores and attaches an address', function() {
        $this->migrate();
        $this->put('suppliers/store', [
            'name' => 'John Smith Ltd',
            'addresses' => [
                $this->getKey() => $this->testAddress(postcode: 'AB12 3CD')
            ]
        ]);
    
        $this->assertDatabaseHas('suppliers', ['name' => 'John Smith Ltd']); // Model creates this
        $this->assertDatabaseHas('addresses', ['postcode' => 'AB12 3CD']); // Trait creates these
        
        $supplier = Supplier::with('addresses')->first();
        $this->assertEquals(1, $supplier->addresses->count());
        $this->assertEquals('AB12 3CD', $supplier->addresses->first()->postcode);
    });