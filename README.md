
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
Inicie o contêiner com o docker, nesse primeiro momento vai demorar um pouco quando executado pela primeira vez.

```bash
  docker-compose up -d
```

Acessando os serviços

```
  Acesse a api no Postman através da porta 2050 do localhost: https://localhost:2050/
```

## Stack utilizada
- **Ambiente desenvolvimento:** Docker
- **Abordagem arquitetônica:** Microsserviço
- **Back-end:** Laravel
    - Inversão de dependência
    - Repositório
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
Para acesso a documentação completa da API consulta a [documentação Postman] (https://documenter.getpostman.com/view/5807678/2sAY52dKUX)