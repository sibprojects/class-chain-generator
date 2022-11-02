# class-chain-generator
Symfony class chain generator example

With console command:
```
symfony console cache:clear
```
rebuild class of chain of resposibility in directory: Entity/Classes/StatusDTO/Compiled
and after all can use chain classes like this:
```
            (new StatusFactory())
                ->makeStatusPendingDTO()
                ->setStatusText('Status text')
                ->setIntParam(123)
                ->setDateParam(new \DateTime())
                ->setStringParam('Details information')
                ->finalize()
                ->apply();
```
