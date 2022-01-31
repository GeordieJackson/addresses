<?php
    
    namespace GeordieJackson\Addresses;
    
    use GeordieJackson\Addresses\Pipes\GetAddressModels;
    use GeordieJackson\Addresses\Pipes\RemoveBlankEntries;
    use GeordieJackson\Addresses\Pipes\RemoveDeletionEntries;
    use Illuminate\Pipeline\Pipeline;
    
    use function app;
    use function collect;
    use function request;
    
    class Addresses
    {
        public static function viaRequest()
        {
            return (new static())->process();
        }
        
        protected function process()
        {
            $addresses = collect(request()->addresses);
            
            return app(Pipeline::class)
                ->send($addresses)
                ->through([
                    RemoveBlankEntries::class,
                    RemoveDeletionEntries::class,
                    GetAddressModels::class,
                ])
                ->thenReturn();
        }
    }