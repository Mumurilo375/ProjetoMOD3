## StarRate — Projeto Acadêmico

Aplicação simples de avaliação de filmes e séries desenvolvida para fins acadêmicos. O objetivo é cadastrar filmes, registrar notas/comentários e visualizar listagens básicas. Projeto focado em aprendizado com PHP + Doctrine.

---

### Funcionalidades

- Landing com carrosséis (capas mais recentes)
- Destaques 2025 (até 6 capas do ano de 2025, direto do banco)
- Busca de filmes (formulário e endpoint em `public/api/search_filmes.php`)
- Autenticação básica (login/cadastro e logout)
- Avaliações (nota e comentário; página de "minhas avaliações")
- Perfil (editar dados e upload de foto)

---

### Stack utilizada

- PHP 8.2+
- Doctrine ORM (mapeamento e persistência)
- MySQL
- Composer (dependências e scripts)
- dotenv (configuração via `.env`)

---

### Como executar

Opção A — Servidor embutido do PHP:
```bash
# 1) Instale as dependências
composer install

# 2) Configure o .env (veja a seção ".env" abaixo)

# 3) Crie/atualize as tabelas
composer create-db   # primeira vez
# ou, se já existe base, para sincronizar alterações de schema
composer update-db

# 4) Suba o servidor de dev (usa a pasta public/ como docroot)
composer start
# abre em http://localhost:8000
```

Opção B — XAMPP/Apache:
1) Coloque o projeto em `htdocs/ProjetoMOD3-limpo/`
2) Acesse em `http://localhost/ProjetoMOD3-limpo/public` (ou crie um vhost apontando `DocumentRoot` para `public/`)
3) Ainda assim rode `composer install` e configure o `.env`

---

### Configuração do .env

O projeto usa `vlucas/phpdotenv`. Crie um arquivo `.env` na raiz do projeto com as suas credenciais do MySQL. Exemplo:
```env
DB_DRIVER=pdo_mysql
DB_HOST=127.0.0.1
DB_USER=root
DB_PASSWORD=
DB_DBNAME=starrate
```
Observação: o `.env` é lido em `src/Core/Database.php`.

---

### Banco de dados

Tem dois jeitos de criar as tabelas:

1) Pelos scripts do Composer/Doctrine (gera pelo mapeamento das entidades):
```bash
composer create-db   # cria o schema
composer update-db   # atualiza o schema, se mudar algo nas entidades
```

2) Importando o SQL pronto (se quiser dados exemplo):
- Arquivo: `projeto_starrate.sql`
- Importe no MySQL (Workbench, phpMyAdmin, etc.)

Se misturar as abordagens, pode ocorrer conflito de schema. Em caso de problema, escolha uma e siga nela (ou recrie as tabelas do zero).

---

### Estrutura do projeto

- `public/` — arquivos acessíveis pelo navegador (index, rotas, CSS/JS/imagens)
- `public/partials/` — header, modal de avaliação, etc.
- `public/api/` — endpoints simples (ex.: busca de filmes)
- `src/Model/` — entidades Doctrine (`Filme`, `User`, `Avaliacao`)
- `src/Core/Database.php` — configuração do EntityManager / Doctrine
- `bin/doctrine` — CLI do Doctrine usada pelos scripts do Composer
- `vendor/` — dependências (não mexer)

---

### Rotas/páginas úteis

- `/public/index.php` — Landing com carrosséis + destaques 2025
- `/public/filmes.php` — Lista/busca de filmes
- `/public/filme.php` — Detalhe do filme
- `/public/minhas-avaliacoes.php` — Avaliações do usuário logado
- `/public/perfil.php`, `/public/atualizarPerfil.php`, `/public/atualizarFotoPerfil.php` — perfil/edições
- `/public/auth.php` e `/public/logout.php` — autenticação
- `/public/api/search_filmes.php` — endpoint de busca

---

### Dicas

- Se as imagens não aparecerem, confere os caminhos salvos no banco (`Filme->capa`) e se o `public/` é o docroot.
- Se der erro de conexão, revisa o `.env` (host, user, senha, DB) e se o MySQL tá rodando.
- Fez mudança na entidade e não refletiu no banco? `composer update-db`.
- Use `htmlspecialchars` ao imprimir dados do banco no HTML (principalmente em atributos) para evitar XSS.
- Em desenvolvimento, o Doctrine está com `isDevMode = true` para facilitar (proxies automáticos).

---

### Créditos

Projeto acadêmico desenvolvido por (Murilo, Izaac e Eduarda) para fins de estudo. Baseado em template com Doctrine e ajustado conforme as necessidades da disciplina.