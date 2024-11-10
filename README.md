
# Travel Order API

A Travel Order API permite gerenciar e controlar solicitações de viagem, com funcionalidades para listar, criar, recuperar e atualizar informações das ordens de viagem.

## Requisitos
- [Docker](https://docs.docker.com/compose/install/) e [Docker Composer](https://docs.docker.com/compose/install/standalone/)


## Rodando localmente

Clone o projeto

```bash
git clone https://github.com/Edilsonss123/travel-order.git travel-order
```

Entre no diretório do projeto
```bash
cd travel-order
```

Entre no diretório do projeto ``api-travel-orders`` e configure o arquivo ``.env``, utilize o ``.env.example`` como modelo, substituindo a configuração de conexão com o banco de dadoo
```bash
cd travel-order/api-travel-orders

DB_CONNECTION=mysql
DB_HOST=db-travel-orders
DB_PORT=3306
DB_DATABASE=travel-orders
DB_USERNAME=travel-user
DB_PASSWORD="8teste0rd&"
```

Antes de iniciar o container, será preciso tornar o script de inicialização da aplicação executável

```bash
chmod +x travel-order/data/api/entrypoint.sh
```

Inicie o contêiner com o docker, nesse primeiro momento vai demorar um pouco quando executado pela primeira vez.

```bash
docker-compose up -d
```
Ao iniciar o container as depências, migrations e seeders serão invocadas atraves do script bash, que tem como ultima ação subir o a aplicação na porta 8000 do container, que é mapeada para a rede host na porta 2050.


Acessando o serviço de api

```
Acesse a api através da porta 2050 do localhost: https://localhost:2050/
```

## Stack utilizada
- **Ambiente desenvolvimento:** Docker
- **Abordagem arquitetônica:** Microsserviço
- **Back-end:** Laravel
    - Inversão de dependência 
    - Repositório para acesso a camada de dados
    - Revisionable para revisão de alteração
- **Autenticação:** JWT
- **Banco De Dados:** MySQL


## Funcionalidades API
 - Viagem:
    - Lista de status
    - Lista de solicitações de viagem
    - Lista de uma Solicitação
    - Solicitação de uma nova viagem
    - Atualização de status de uma solicitação de viagem
 - Autenticação:
    - Criação de usuário
    - Geração de token acesso usuário
    - Invalidação de token acesso usuário
Para acesso a documentação completa da API consulte a [documentação Postman](https://documenter.getpostman.com/view/5807678/2sAY52dKUX)