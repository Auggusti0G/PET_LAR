# Engenharia de Requisitos – Pet Lar

Resumo da seção 3 e da seção 4 da [documentação completa](../01_documentacao/Documentacao_PetLar.pdf).

## 1. Elicitação: brainstorming

A técnica de elicitação usada foi o **brainstorming** (Osborn, 1953), feito pela equipe (Rafael Augusto Santana Gil e Ana Cecília Batista) em quatro passos:

1. **Escolha do domínio**: biblioteca, academia, clínica veterinária, campeonato esportivo e ONG de adoção. Venceu a ONG de adoção, por ter mais informações relacionadas e impacto social.
2. **Geração livre de ideias**: cerca de 20 minutos anotando funcionalidades, sem críticas.
3. **Agrupamento** por assunto: animais, pessoas, adoção, saúde, relatórios e acesso.
4. **Priorização**: essencial, desejável ou futura.

| Grupo | Ideias levantadas | Decisão |
|---|---|---|
| Animais | Cadastrar animal com espécie, porte e idade; marcar situação; observações; foto | Essencial (foto: futura) |
| Pessoas | Adotantes e voluntários; CPF único; maioridade | Essencial |
| Adoção | Registrar adoção com voluntário; mudar situação automaticamente; impedir adotar animal já adotado | Essencial |
| Saúde | Registrar vacinas; avisar próxima dose; histórico | Desejável (incluído) |
| Relatórios | Totais por espécie; adoções por mês; ranking; PDF | Essencial |
| Acesso | Login; sair; sessão que expira; vários perfis | Essencial (perfis: futura) |
| Comunidade | Página pública; formulário de interesse; lembrete por e-mail | Futura |

## 2. Requisitos funcionais

| ID | Descrição | Prioridade |
|---|---|---|
| RF01 | Entrar no sistema com usuário e senha. | Essencial |
| RF02 | Sair do sistema, encerrando a sessão. | Essencial |
| RF03 | Painel com total de animais, disponíveis, adoções, adotantes e últimas adoções. | Desejável |
| RF04 | Cadastrar, listar, editar e excluir espécies. | Essencial |
| RF05 | Cadastrar, listar, editar e excluir animais. | Essencial |
| RF06 | Cadastrar, listar, editar e excluir adotantes. | Essencial |
| RF07 | Cadastrar, listar, editar e excluir voluntários. | Essencial |
| RF08 | Registrar, listar, editar e excluir adoções (animal + adotante + voluntário). | Essencial |
| RF09 | Registrar, listar, editar e excluir vacinas aplicadas. | Essencial |
| RF10 | Busca avançada de animais por nome, espécie, situação, porte e data de entrada. | Essencial |
| RF11 | Listagem de animais em PDF, respeitando os filtros da busca. | Essencial |
| RF12 | Relatório consolidado: totais por situação e espécie, adoções por mês, ranking de voluntários e vacinas a vencer. | Essencial |
| RF13 | Filtrar o relatório por período e baixá-lo em PDF. | Desejável |
| RF14 | Filtrar vacinas por animal e sinalizar doses atrasadas ou próximas. | Desejável |
| RF15 | Mensagens de sucesso ou erro após cada operação. | Desejável |
| RF16 | Confirmação antes de excluir qualquer registro. | Desejável |

## 3. Requisitos não funcionais

| ID | Categoria | Descrição |
|---|---|---|
| RNF01 | Tecnologia | Desenvolvido em PHP, com acesso ao banco por PDO. |
| RNF02 | Banco de dados | Dados em MySQL, com script compatível com o ambiente acadêmico. |
| RNF03 | Interface | Bootstrap e layout responsivo (computador, tablet e celular). |
| RNF04 | Segurança | Páginas internas exigem sessão ativa. |
| RNF05 | Segurança | Sessão expira após 30 minutos sem uso. |
| RNF06 | Segurança | Senhas armazenadas com MD5, nunca em texto puro. |
| RNF07 | Segurança | Consultas com parâmetros (`bindParam`/`bindValue`) contra SQL Injection. |
| RNF08 | Segurança | Textos exibidos com `htmlspecialchars` contra XSS. |
| RNF09 | Usabilidade | Todos os módulos no mesmo formato: formulário em cima, tabela embaixo. |
| RNF10 | Manutenibilidade | Uma pasta por módulo, código comentado. |
| RNF11 | Portabilidade | PDF sem instalar extensões no servidor. |
| RNF12 | Desempenho | Telas carregam em menos de 2 segundos com a carga inicial. |

## 4. Regras de negócio

| ID | Regra | Onde é aplicada |
|---|---|---|
| RN01 | Só animais "Disponível" podem ser adotados. | `adocao/index.php`, `adocao/inserir.php` |
| RN02 | Ao registrar a adoção, o animal vira "Adotado". | `adocao/inserir.php` |
| RN03 | Ao excluir a adoção (ou trocar o animal), ele volta a "Disponível". | `adocao/excluir.php`, `adocao/inserir.php` |
| RN04 | "Adotado" não pode ser escolhido manualmente no cadastro do animal. | `animal/index.php`, `animal/inserir.php` |
| RN05 | Adotante com 18 anos ou mais. | `adotante/inserir.php` |
| RN06 | CPF único. | `adotante/inserir.php` + `UNIQUE` |
| RN07 | Nome de espécie único. | `especie/inserir.php` + `UNIQUE` |
| RN08 | Não exclui espécie com animais. | `especie/excluir.php` + `RESTRICT` |
| RN09 | Não exclui animal, adotante ou voluntário que está em adoção. | `excluir.php` dos três módulos |
| RN10 | Excluir animal apaga as vacinas dele. | `ON DELETE CASCADE` |
| RN11 | Datas não podem estar no futuro; adoção não antes da entrada do animal. | `inserir.php` de animal, adoção e vacinação |
| RN12 | Próxima dose depois da data de aplicação. | `vacinacao/inserir.php` |

## 5. Histórias de usuário

| ID | História | Critérios de aceitação |
|---|---|---|
| HU01 | Como **membro da equipe**, quero entrar com usuário e senha, para que só pessoas autorizadas vejam os dados. | Senha errada mostra aviso; sem login, volta ao login; sessão expira em 30 min. |
| HU02 | Como **membro da equipe**, quero ver um painel ao entrar, para saber como está o abrigo. | Total de animais, disponíveis, adoções, adotantes e 5 últimas adoções. |
| HU03 | Como **cuidador**, quero cadastrar um animal resgatado, para que ele entre na fila de adoção. | Campos obrigatórios; data futura recusada. |
| HU04 | Como **coordenador**, quero manter as espécies, para classificar os animais. | Nome único; não exclui espécie com animais. |
| HU05 | Como **entrevistador**, quero cadastrar adotantes, para registrar quem leva cada animal. | CPF único no formato 000.000.000-00; menores de 18 recusados. |
| HU06 | Como **coordenador**, quero cadastrar voluntários e funções, para saber quem faz o quê. | Função em lista; com adoções não pode ser excluído. |
| HU07 | Como **entrevistador**, quero registrar uma adoção, para formalizar a entrega do animal. | Só animais disponíveis; ao salvar vira "Adotado"; ao excluir volta a "Disponível". |
| HU08 | Como **veterinária**, quero registrar vacinas, para manter o histórico de saúde. | Próxima dose opcional e posterior; avisos "Em breve" e "Atrasada". |
| HU09 | Como **membro da equipe**, quero buscar animais combinando filtros, para achar o perfil que o adotante procura. | 5 filtros combináveis. |
| HU10 | Como **coordenador**, quero a listagem em PDF, para imprimir ou enviar. | Mesmos filtros da tela; data, usuário e páginas no rodapé. |
| HU11 | Como **coordenador**, quero um relatório por período, para prestar contas. | Totais, taxa de adoção, por mês, ranking e PDF. |
| HU12 | Como **membro da equipe**, quero sair do sistema, para ninguém usar minha sessão. | Sair destrói a sessão e volta ao login. |
