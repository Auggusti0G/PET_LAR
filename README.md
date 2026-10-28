# Pet Lar – Sistema de Gestão de Adoção de Animais

Sistema web em **PHP + MySQL + Bootstrap** para ONGs e abrigos de animais controlarem
espécies, animais, adotantes, voluntários, adoções e vacinação.

> Trabalho A3 · Desenvolvimento Web com PHP · UniCEUB · Prof. Francisco Molina · 2026
> Integrantes: **Rafael Augusto Santana Gil** e **Ana Cecília Batista**

---

## Sumário

1. [O que o sistema faz](#1-o-que-o-sistema-faz)
2. [Como instalar e rodar](#2-como-instalar-e-rodar)
3. [Estrutura de pastas](#3-estrutura-de-pastas)
4. [Arquivos da raiz](#4-arquivos-da-raiz)
5. [Módulos de cadastro (CRUD)](#5-módulos-de-cadastro-crud)
6. [Relatório e PDFs](#6-relatório-e-pdfs)
7. [Banco de dados](#7-banco-de-dados)
8. [Como uma página é montada](#8-como-uma-página-é-montada)
9. [Regras de negócio no código](#9-regras-de-negócio-no-código)
10. [Segurança](#10-segurança)

---

## 1. O que o sistema faz

| Exigência do Trabalho A3 | Onde está |
|---|---|
| CRUD completo em PHP | 6 módulos: `especie/`, `animal/`, `adotante/`, `voluntario/`, `adocao/`, `vacinacao/` |
| 6 tipos de informação relacionados | 6 tabelas ligadas por chave estrangeira (+ tabela `usuario`) |
| Autenticação com sessão | `login.php`, `verifica_login.php`, `sair.php` |
| Busca avançada com 3+ filtros | `animal/index.php` (5 filtros: nome, espécie, situação, porte e data de entrada) |
| Relatório consolidado | `relatorio/index.php` |
| Listagem em PDF | `pdf/animais.php` e `pdf/relatorio.php` |
| Script MySQL com carga inicial | `banco.sql` |
| Interface com Bootstrap | `cabecalho.php` (Bootstrap 5.3 via CDN) + `css/estilo.css` |

---

## 2. Como instalar e rodar

1. Instale o **XAMPP** (ou outro pacote com Apache, PHP 7.4+ e MySQL) e inicie **Apache** e **MySQL**.
2. Copie a pasta `petlar` para `C:\xampp\htdocs\`.
3. Abra o **MySQL Workbench** (ou phpMyAdmin), abra o arquivo `petlar/banco.sql` e execute tudo.
   O banco `petlar` é criado do zero, já com dados de exemplo.
4. Em `conexao.php`, ajuste `$user` e `$pass` com o usuário e a senha do seu MySQL
   (no XAMPP padrão: usuário `root` e senha vazia `''`).
5. Acesse **http://localhost/petlar/** no navegador.
6. Entre com um dos usuários abaixo (senha de todos: `123456`):

| Usuário | Nome |
|---|---|
| `rafael.ag` | Rafael Augusto Santana Gil |
| `ana.cecilia` | Ana Cecília Batista |
| `admin` | Administrador |

> O Bootstrap e os ícones vêm da internet (CDN). Sem conexão o sistema funciona, mas sem o visual.
> Os PDFs funcionam sem internet.

---

## 3. Estrutura de pastas

```
petlar/
│
├── README.md              este arquivo
├── banco.sql              script do banco (MySQL) com a carga inicial
│
├── conexao.php            conexão com o banco (PDO) → variável $pdo
├── index.php              TELA de login
├── login.php              processa o login e cria a sessão
├── verifica_login.php     protege as páginas internas (sessão + tempo limite)
├── sair.php               logout (destrói a sessão)
├── cabecalho.php          topo das páginas: <head>, Bootstrap, menu, alertas
├── rodape.php             fim das páginas: rodapé e JavaScript do Bootstrap
├── painel.php             página inicial depois do login
│
├── css/
│   └── estilo.css         cores e ajustes visuais do Pet Lar
│
├── especie/               CRUD de espécies
│   ├── index.php          tela: formulário + listagem
│   ├── inserir.php        salva (INSERT ou UPDATE)
│   └── excluir.php        apaga (DELETE)
│
├── animal/                CRUD de animais + BUSCA AVANÇADA
│   ├── index.php
│   ├── inserir.php
│   └── excluir.php
│
├── adotante/              CRUD de adotantes
│   ├── index.php
│   ├── inserir.php
│   └── excluir.php
│
├── voluntario/            CRUD de voluntários
│   ├── index.php
│   ├── inserir.php
│   └── excluir.php
│
├── adocao/                CRUD de adoções (liga animal + adotante + voluntário)
│   ├── index.php
│   ├── inserir.php
│   └── excluir.php
│
├── vacinacao/             CRUD de vacinas aplicadas
│   ├── index.php
│   ├── inserir.php
│   └── excluir.php
│
├── relatorio/
│   └── index.php          relatório consolidado (COUNT, SUM, GROUP BY)
│
├── pdf/
│   ├── modelo_pdf.php     classe PdfPetLar: cabeçalho e rodapé dos PDFs
│   ├── animais.php        listagem de animais em PDF (usa os filtros da busca)
│   └── relatorio.php      relatório consolidado em PDF
│
└── lib/
    └── fpdf/              biblioteca FPDF 1.86 (gera PDF com PHP puro)
        ├── fpdf.php
        └── font/          definições das fontes Helvetica
```

Fora o CSS, o script SQL e este README, todos os arquivos são `.php`.

---

## 4. Arquivos da raiz

| Arquivo | Função | Usado por |
|---|---|---|
| `conexao.php` | Define host, banco, usuário e senha, cria o objeto `$pdo` e acerta o fuso horário de Brasília. | Todos os arquivos que acessam o banco (`include`) |
| `index.php` | Tela de login com Bootstrap. Mostra avisos de senha errada, acesso negado ou sessão expirada (`?erro=`). Se já houver sessão, vai direto para o painel. | Primeira página do sistema |
| `login.php` | Recebe usuário e senha por **POST**, converte a senha em `md5`, busca no banco e, se achar, grava `$_SESSION['usuario']`, `['nome']` e `['ultimo_acesso']`. | Formulário do `index.php` |
| `verifica_login.php` | Incluído no topo de **toda** página interna. Sem sessão → volta ao login. Parado mais de 30 minutos → encerra a sessão. | Todas as páginas internas |
| `sair.php` | `session_unset()` + `session_destroy()` e volta ao login. | Botão **Sair** do menu |
| `cabecalho.php` | Abre o HTML, carrega Bootstrap, ícones e `estilo.css`, monta o menu superior, mostra o nome do usuário e os alertas (`?msg=` e `?tipo=`). Também define as funções `ativo()`, `classeStatus()`, `dataBR()` e `selecionado()`. | Todas as telas internas |
| `rodape.php` | Fecha o `<main>`, mostra o rodapé e carrega o JavaScript do Bootstrap. | Todas as telas internas |
| `painel.php` | Cartões com números (COUNT), atalhos para os módulos e as 5 últimas adoções. | Destino depois do login |

---

## 5. Módulos de cadastro (CRUD)

Todos os módulos seguem **o mesmo padrão visto em aula** (exemplo `aluno/`):

| Arquivo | Operação | O que faz |
|---|---|---|
| `index.php` | **READ** | Lista os registros com `SELECT` e mostra o formulário. Quando abre com `?id=X` (link **Editar**), busca esse registro e preenche o formulário. |
| `inserir.php` | **CREATE / UPDATE** | Recebe o formulário (GET), confere as regras de negócio e grava. **`id` vazio → `INSERT`**, **`id` preenchido → `UPDATE`**. |
| `excluir.php` | **DELETE** | Confere se o registro pode ser apagado (vínculos) e executa o `DELETE`. |

Depois de salvar ou excluir, o arquivo volta para a listagem com
`header("Location: index.php?msg=...")`, e o `cabecalho.php` mostra a mensagem.

### O que cada módulo tem de especial

| Pasta | Tabela | Destaques |
|---|---|---|
| `especie/` | `especie` | Mostra quantos animais há de cada espécie; não aceita nome repetido; não exclui espécie com animais. |
| `animal/` | `animal` | **Busca avançada** com 5 filtros e botão **Gerar PDF**; situação "Adotado" só muda pelo módulo de adoções; exclusão bloqueada se houver adoção. |
| `adotante/` | `adotante` | CPF único no formato `000.000.000-00`; exige 18 anos ou mais; mostra quantas adoções cada pessoa fez. |
| `voluntario/` | `voluntario` | Função escolhida em lista; mostra quantas adoções acompanhou; exclusão bloqueada se tiver adoções. |
| `adocao/` | `adocao` | Três `<select>` (animal, adotante, voluntário); só lista animais disponíveis; ao salvar o animal vira "Adotado"; ao excluir volta a "Disponível". |
| `vacinacao/` | `vacinacao` | Filtro por animal; próxima dose opcional; etiquetas "Em breve" (30 dias) e "Atrasada". |

### Padrão de uma consulta (igual em todos os arquivos)

```php
$sql = "UPDATE especie SET nome =:nome WHERE id =:id";   // 1. SQL com parâmetros
$stmt = $pdo->prepare($sql);                              // 2. prepara
$stmt->bindParam(":nome", $nome);                         // 3. liga cada parâmetro
$stmt->bindParam(":id", $id);
$stmt->execute();                                         // 4. executa
header("Location: index.php?msg=" . urlencode("Salvo!")); // 5. volta para a lista
exit;
```

---

## 6. Relatório e PDFs

| Arquivo | O que gera |
|---|---|
| `relatorio/index.php` | Filtro de período; totais por situação e taxa de adoção; tabela por espécie (`SUM(CASE WHEN ...)` + `GROUP BY`); adoções por mês (`YEAR()`, `MONTH()`); ranking de voluntários; vacinas a vencer em 30 dias. |
| `pdf/modelo_pdf.php` | Classe `PdfPetLar` (filha de `FPDF`) com `Header()`, `Footer()` e `cabecalhoTabela()`. A função `txt()` converte UTF-8 para os acentos aparecerem no PDF. |
| `pdf/animais.php` | PDF em paisagem com a listagem de animais, aplicando **os mesmos filtros** da busca avançada. |
| `pdf/relatorio.php` | PDF em retrato com os números do abrigo, a tabela por espécie e as adoções do período. |
| `lib/fpdf/` | Biblioteca **FPDF 1.86** (licença livre), em PHP puro, sem instalar nada no servidor. |

---

## 7. Banco de dados

Banco **`petlar`**, criado pelo `banco.sql` (pode ser executado várias vezes: ele apaga e recria tudo).

| Tabela | Guarda | Chaves estrangeiras |
|---|---|---|
| `usuario` | Login do sistema (senha em md5) | – |
| `especie` | Cão, Gato, Coelho, Ave, Roedor | – |
| `animal` | Animais do abrigo e sua situação | `especie_id` → `especie` (RESTRICT) |
| `adotante` | Pessoas que adotam (CPF único) | – |
| `voluntario` | Equipe da ONG | – |
| `adocao` | Adoções realizadas | `animal_id`, `adotante_id`, `voluntario_id` (RESTRICT) |
| `vacinacao` | Vacinas aplicadas | `animal_id` → `animal` (CASCADE) |

Carga inicial: 3 usuários, 5 espécies, 15 animais, 7 adotantes, 5 voluntários, 6 adoções e 13 vacinas.

---

## 8. Como uma página é montada

Toda página interna segue esta ordem:

```php
<?php
    $raiz = '../';                     // caminho até a pasta principal
    include '../verifica_login.php';   // 1. só entra quem está logado
    include '../conexao.php';          // 2. abre o banco ($pdo)

    // 3. consultas: listagem e "selecionar para edição"

    $titulo = 'Animais';               // texto da aba
    $menu   = 'animal';                // item do menu destacado
    include '../cabecalho.php';        // 4. <head>, menu e alertas
?>
    <!-- 5. HTML da tela: formulário + tabela -->
<?php include '../rodape.php'; ?>      <!-- 6. rodapé e JavaScript -->
```

Fluxo de navegação:

```
index.php (login) ──POST──► login.php ──► painel.php
                                             │
         ┌───────────┬───────────┬───────────┼───────────┬───────────┬───────────┐
     animal/     especie/    adotante/  voluntario/   adocao/   vacinacao/  relatorio/
         │                                                                      │
   pdf/animais.php                                                     pdf/relatorio.php

Em qualquer página:  Sair ──► sair.php ──► index.php
```

---

## 9. Regras de negócio no código

| Regra | Arquivo |
|---|---|
| RN01 – Só animal "Disponível" pode ser adotado | `adocao/index.php`, `adocao/inserir.php` |
| RN02 – Adoção registrada → animal vira "Adotado" | `adocao/inserir.php` |
| RN03 – Adoção excluída (ou animal trocado) → volta a "Disponível" | `adocao/excluir.php`, `adocao/inserir.php` |
| RN04 – "Adotado" não pode ser escolhido no cadastro do animal | `animal/index.php`, `animal/inserir.php` |
| RN05 – Adotante com 18 anos ou mais | `adotante/inserir.php` |
| RN06 – CPF único | `adotante/inserir.php` + `UNIQUE` no banco |
| RN07 – Nome de espécie único | `especie/inserir.php` + `UNIQUE` no banco |
| RN08 – Não exclui espécie com animais | `especie/excluir.php` + `RESTRICT` |
| RN09 – Não exclui animal, adotante ou voluntário que está em adoção | `excluir.php` dos três módulos |
| RN10 – Excluir animal apaga as vacinas dele | `ON DELETE CASCADE` no banco |
| RN11 – Datas não podem estar no futuro; adoção não antes da entrada | `inserir.php` de animal, adoção e vacinação |
| RN12 – Próxima dose depois da aplicação | `vacinacao/inserir.php` |

---

## 10. Segurança

| Proteção | Como |
|---|---|
| Acesso só com login | `verifica_login.php` no topo de toda página interna |
| Sessão expira | 30 minutos sem uso (`$_SESSION['ultimo_acesso']`) |
| Roubo de sessão | `session_regenerate_id(true)` no login |
| Senha protegida | Guardada em `md5`, enviada por `POST` |
| SQL Injection | Toda consulta usa `bindParam` / `bindValue` |
| XSS | Todo texto exibido passa por `htmlspecialchars()` |
| Exclusão por engano | `confirm()` antes de cada Excluir |

---

Documentação completa (requisitos, histórias de usuário, protótipo, MER, dicionário de dados e manual):
arquivo **Documentacao_PetLar.pdf**.
