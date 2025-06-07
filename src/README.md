## Слои

`App\Symfony` -> `App\Infrastructure` -> `App\Domain`

* `App\Symfony` Ввод/Вывод
* `App\Infrastructure` Реализация интерфейсов и вспомогательные компоненты
* `App\Domain` Доменная область (Entities + UseCases)

```mermaid
flowchart LR
    App ==> Domain
    subgraph App
        Controller_1 -- Not allowed x--x Controller_2
    end
    subgraph Domain
        UseCases ==> Entities
    end
    subgraph UseCases
        UseCase_1 -- Not allowed x--x UseCase_2
    end
    Controller_1 ---> UseCase_1
    Controller_2 ---> UseCase_2
    subgraph UseCase_1
        Command_1 --> Handler_1 --> ViewModel_1
    end
    subgraph UseCase_2
        Command_2 --> Handler_2 --> ViewModel_2
    end
    subgraph Entities
        Entity
        Message
        RepositoryInterface
        MsgBusInterface
    end
    subgraph Infrastructure
        SqlRepository --> RepositoryInterface
        HttpClient --> RepositoryInterface
        Kafka --> MsgBusInterface
        JsonEncoder -- Serialize response --> Controller_1
        JsonEncoder -- Serialize response --> Controller_2
    end
```
