<?php
    
    use GeordieJackson\Addresses\Models\Address;
    use GeordieJackson\Addresses\Pipes\GetAddressModels;
    use GeordieJackson\Addresses\Pipes\RemoveBlankEntries;
    use GeordieJackson\Addresses\Pipes\RemoveDeletionEntries;
    use Illuminate\Pipeline\Pipeline;
    
    beforeEach(function() {
        $this->migrate();
    });
    
    it('removes blank entries from collection', function() {
        $addresses = collect([
            $this->getKey() => $this->testAddress(),
            $this->getKey() => $this->testAddress(code: 'AB12 3CD'),
            $this->getKey() => $this->testAddress(),
            $this->getKey() => $this->testAddress(address: "221B Baker Street", code: 'ZY98 7XW'),
            $this->getKey() => $this->testAddress(),
        ]);
        
        $pipeline = app(Pipeline::class);
        $addresses = $pipeline->send($addresses)->through([RemoveBlankEntries::class])->thenReturn();
        
        expect($addresses->count())->toBe(2);
    });
    
    it("deletes deletable entries from the DB and collection", function() {
        $addresses = collect([
            $this->getKey() => $this->testAddress(name: "test 1"),
            $this->getKey() => $this->testAddress(name: "test 2"),
            $this->getKey() => $this->testAddress(name: "test 3"),
        ]);
        Address::insert($addresses->toArray());
        $firstAddress = Address::first();
        $addresses[$addresses->keys()[0]] = array_merge($addresses[$addresses->keys()[0]],
            ['address_id' => $firstAddress->id, 'delete' => 1]);
        
        $pipeline = app(Pipeline::class);
        $addresses = $pipeline->send($addresses)->through([RemoveDeletionEntries::class])->thenReturn();
        
        $this->assertDatabaseMissing('addresses', ['name' => $firstAddress->name]);
        expect($addresses->count())->toBe(2);
        expect(Address::count())->toBe(2);
    });
    
    it('returns address models from addresses', function() {
        $addresses = collect([
            $this->getKey() => $this->testAddress(code: 'AB12 3CD'),
            $this->getKey() => $this->testAddress(address: "221B Baker Street", code: 'ZY98 7XW'),
        ]);
        
        $pipeline = app(Pipeline::class);
        $addresses = $pipeline->send($addresses)->through([GetAddressModels::class])->thenReturn();
        
        $addresses->each(function($address) {
            expect($address)->toBeInstanceOf(Address::class);
        });
    });
    
    