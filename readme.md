# StarRate 🎬

Aplicação simples para avaliação de filmes, desenvolvida para fins acadêmicos. Permite cadastrar filmes, registrar notas e comentários.

## Funcionalidades

*   **Autenticação**: Cadastro e login de usuários.
*   **Catálogo de Filmes**: Exibe filmes recentes e permite busca.
*   **Avaliações**: Permite dar uma nota (0-100) e um comentário.
*   **Perfil**: O usuário pode atualizar seus dados e ver suas avaliações.

## Stack utilizada

- PHP 8.2+
- Doctrine ORM (mapeamento e persistência)
- MySQL
- Composer (dependências e scripts)
- dotenv (configuração via `.env`)

## Configuração do .env

Para o projeto rodar, crie um arquivo `.env` na raiz com as suas credenciais do MySQL. Exemplo:

```env
DB_DRIVER=pdo_mysql
DB_HOST=127.0.0.1
DB_USER=root
DB_PASSWORD=
DB_DBNAME=starrate
```

## Rotas Principais

- `/public/index.php`: Página inicial.
- `/public/filmes.php`: Lista e busca de filmes.
- `/public/filme.php`: Detalhes de um filme.
- `/public/auth.php`: Login e cadastro.
- `/public/perfil.php`: Perfil do usuário.
- `/public/minhas-avaliacoes.php`: Suas avaliações.

## Créditos

*   **Desenvolvido por**: Murilo, Izaac e Eduarda.