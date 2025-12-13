# g6cloud
Repositorio para os Dev da G6 Cloud


# G6 Cloud Website

![G6 Cloud Logo](g6cloud/g6cloud_logo.png.png)

Repositório oficial de desenvolvimento do site da G6 Cloud, empresa de tecnologia focada em computação em nuvem, migração, infraestrutura, DevOps e serviços gerenciados para pequenas e médias empresas.

---

## 📋 Índice
- Sobre o Projeto
- Tecnologias Utilizadas
- Estrutura de Pastas
- Ambiente e Pré-requisitos
- Como Executar Localmente
- Deploy em Produção
- Boas Práticas de Contribuição
- Responsáveis
- Licença

---

## Sobre o Projeto

Este repositório contém o código-fonte do site institucional da G6 Cloud, publicado em produção em https://g6cloud.com.br.  
O objetivo do site é apresentar os serviços de migração, infraestrutura em nuvem, DevOps e serviços gerenciados, além de fornecer canais de contato para clientes e parceiros.

Repositório GitHub: https://github.com/flashfeather/g6cloud

---

## Tecnologias Utilizadas

- HTML: estrutura das páginas.  
- CSS: estilos e responsividade.  
- JavaScript (JS): interações e comportamentos no frontend.  
- PHP: funcionalidades no backend (por exemplo, envio de formulários).  

---

## Estrutura de Pastas

(ajuste conforme for criando os diretórios)

/
- index.php  – Página inicial do site  
- /assets    – Imagens, ícones, logos (inclui g6cloud_logo.png)  
- /css       – Arquivos de estilo CSS  
- /js        – Scripts JavaScript  
- /php       – Scripts e helpers PHP (se aplicável)  
- /docs      – Documentos internos, materiais da empresa  

---

## Ambiente e Pré-requisitos

- Servidor web com suporte a PHP (Apache, Nginx, XAMPP, Laragon ou similar).  
- PHP instalado (versão 7.4 ou superior recomendada).  
- Git para versionamento.  
- Navegador moderno (Chrome, Brave, Edge, etc.).

Opcional (desenvolvimento):

- VS Code (ou outro editor) com suporte a PHP/HTML/CSS/JS.  

---

## Como Executar Localmente

1. Clonar o repositório:

git clone https://github.com/flashfeather/g6cloud.git
cd g6cloud

2. Editar localmente usndo vscode


## Deploy em Produção

- Domínio: https://g6cloud.com.br  
- Servidor: hospedagem com suporte a PHP.  
- Sugestão de fluxo:

1. Fazer commit das alterações na branch `main`.  
2. Enviar os arquivos para o servidor (via Git pull, FTP ou SSH).  
3. Validar páginas, formulários, links e responsividade.  

---

## Boas Práticas de Contribuição

- Branch principal: `main`.  

Fluxo sugerido:

1. Criar uma branch descritiva a partir de `main`:

git checkout -b feature/nome-da-feature

2. Fazer commits com mensagens claras:

git commit -m "feat: adiciona seção de serviços"

3. Abrir Pull Request para `main` e solicitar revisão.  

Antes de abrir PR:

- Testar o site localmente.  
- Verificar visualização em desktop e mobile.  
- Conferir links, formulários e navegação.  

Licença
Todo o código deste repositório é propriedade intelectual da G6 Cloud e não pode ser copiado, distribuído ou reutilizado sem autorização prévia por escrito da empresa.