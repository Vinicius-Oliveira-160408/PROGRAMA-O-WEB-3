# CRUD Mundo

**Aluno(a):** _(coloque seu nome aqui)_
**Curso:** Desenvolvimento de Sistemas — Etec/Cps, São José dos Campos
**Disciplina:** Programação Web

## Descrição do projeto

Aplicação web para gerenciamento de informações geográficas do mundo:
**continentes, países, cidades e governantes**, com relacionamentos entre
essas entidades. O sistema permite cadastrar, listar, editar e excluir
registros, mantendo a integridade referencial do banco de dados (não é
possível excluir um país com cidades vinculadas, por exemplo).

Funcionalidades extras implementadas:
- Busca dinâmica (JavaScript) por nome nas tabelas de países, cidades,
  continentes e governantes.
- Painel inicial com estatísticas: total de registros, cidades cadastradas
  por continente e a cidade mais populosa de cada país.
- Confirmação de exclusão via JavaScript e validação de campos obrigatórios
  no front-end antes do envio dos formulários.

## Tecnologias utilizadas

- **Front-end:** HTML5, CSS3, JavaScript (vanilla)
- **Back-end:** PHP 8 (PDO)
- **Banco de dados:** MySQL (banco `bd_mundo`)
- **Ambiente local:** XAMPP

## Estrutura de pastas

```
crud-mundo/
├── backend/                  # Camada de acesso a dados (PHP + PDO)
│   ├── config/conexao.php    # Conexão única com o MySQL
│   ├── continentes/funcoes.php
│   ├── paises/funcoes.php
│   ├── cidades/funcoes.php
│   └── governantes/funcoes.php
├── frontend/                 # Páginas e apresentação
│   ├── css/style.css
│   ├── js/script.js
│   ├── includes/header.php
│   ├── includes/footer.php
│   ├── index.php             # Painel / estatísticas
│   ├── continentes.php
│   ├── paises.php
│   ├── cidades.php
│   └── governantes.php
└── database/
    └── bd_mundo.sql          # Script de criação do banco e tabelas
```

A separação é por **responsabilidade**: as páginas em `frontend/` cuidam da
apresentação e do roteamento das ações do usuário (inserir, editar, excluir),
enquanto toda a comunicação com o banco (queries SQL) fica isolada nas
funções dentro de `backend/`.

## Modelo de dados

- **continentes** (1) → (N) **paises**
- **paises** (1) → (N) **cidades**
- **governantes** (1) → (N) **paises** _e_ (1) → (N) **cidades**

As chaves estrangeiras usam o comportamento padrão do MySQL (`RESTRICT`):
não é possível excluir um continente com países vinculados, nem um país com
cidades vinculadas. A aplicação trata esse erro e exibe uma mensagem amigável
ao usuário em vez de uma falha técnica.

## Como instalar e executar (XAMPP)

1. Instale o [XAMPP](https://www.apachefriends.org/) e inicie os módulos
   **Apache** e **MySQL** no painel de controle.
2. Copie a pasta `crud-mundo` para `C:\xampp\htdocs\` (Windows) ou
   `/Applications/XAMPP/htdocs/` (Mac).
3. Crie o banco de dados:
   - Acesse `http://localhost/phpmyadmin`.
   - Vá em **Importar** e selecione o arquivo `database/bd_mundo.sql`,
     **ou** abra a aba SQL e cole o conteúdo do arquivo e execute.
4. Confira as credenciais de acesso ao banco em
   `backend/config/conexao.php` (por padrão, usuário `root` e senha vazia,
   que é o padrão do XAMPP).
5. Acesse a aplicação em:
   ```
   http://localhost/crud-mundo/frontend/index.php
   ```

## Como usar

1. Cadastre primeiro os **continentes**.
2. Cadastre os **governantes** (opcional, podem ser vinculados depois).
3. Cadastre os **países**, escolhendo o continente e, se quiser, um
   governante.
4. Cadastre as **cidades**, vinculando-as a um país e, se quiser, a um
   governante.
5. Use o campo de busca em cada tela para filtrar os registros pelo nome.
6. Acompanhe o resumo geral e as estatísticas na tela inicial.

## Versionamento

Este projeto deve ser versionado com Git, com commits descritivos e
organização em branches (ex.: `main` para a versão estável e branches de
feature como `feature/cidades`, `feature/estatisticas`, etc.), e publicado
em um repositório no GitHub.
