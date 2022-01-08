<?php
    
    use GeordieJackson\Address\Models\Address;
    use GeordieJackson\Address\Tests\TestModels\User;
    
    it('can attach an address to a model', function() {
        $this->migrate();
    
        $user = User::factory()->create();
        $addresses = Address::factory()->count(3)->create();
    
        $user->addresses()->attach($addresses[1]);
    
        $user = User::with('addresses')->first();
    
        $this->assertEquals(1, $user->addresses->count());
    });
    
    it('can attach multiple addresses to a model', function() {
        $this->migrate();
    
        $user = User::factory()->create();
        $addresses = Address::factory()->count(3)->create();
        
        $user->addresses()->attach($addresses);
        $user = User::with('addresses')->first();
    
        $this->assertEquals(3, $user->addresses->count());
    });
    
    it('can sync an address to a model', function() {
       $this->migrate();
        $user = User::factory()->create();
        $addresses = Address::factory()->count(3)->create();
        $user->addresses()->attach($addresses);
        $user->refresh()->load('addresses');
        $this->assertEquals(3, $user->addresses->count());
        $address = Address::whereId(2)->first();
        $user->addresses()->sync($address);
        $user->refresh()->load('addresses');
        $this->assertEquals(1, $user->addresses->count());
    });
    
    it('can sync multiple addresses to a model', function() {
        $this->migrate();
        $user = User::factory()->create();
        $addresses = Address::factory()->count(6)->create();
        $user->addresses()->attach($addresses);
        $user->refresh()->load('addresses');
        $this->assertEquals(6, $user->addresses->count());
        $addresses = Address::where('id', '>', 2)->get();
        $user->addresses()->sync($addresses);
        $user->refresh()->load('addresses');
        $this->assertEquals(4, $user->addresses->count());
    });
    
    it('can detach an address from a model', function() {
        $this->migrate();
        $user = User::factory()->create();
        $addresses = Address::factory()->count(3)->create();
        $user->addresses()->attach($addresses);
        $user->refresh()->load('addresses');
        $this->assertEquals(3, $user->addresses->count());
        $address = Address::whereId(2)->first();
        $user->addresses()->detach($address);
        $user->refresh()->load('addresses');
        $this->assertEquals(2, $user->addresses->count());
    });
    
    it('can detach multiple addresses from a model', function() {
        $this->migrate();
        $user = User::factory()->create();
        $addresses = Address::factory()->count(6)->create();
        $user->addresses()->attach($addresses);
        $user->refresh()->load('addresses');
        $this->assertEquals(6, $user->addresses->count());
        $addresses = Address::whereIn('id', [1,3,4,6])->get();
        $user->addresses()->detach($addresses);
        $user->refresh()->load('addresses');
        $this->assertEquals(2, $user->addresses->count());
    });