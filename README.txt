SISTEMA DE ASSISTÊNCIA TÉCNICA — PHP + XAMPP + MySQL na porta 3307

REQUISITOS
- XAMPP com Apache, MySQL/MariaDB e PHP (PDO MySQL e OpenSSL habilitados).
- VS Code para editar os arquivos.
- MySQL/MariaDB escutando na porta 3307.

INSTALAÇÃO
1. Extraia a pasta sistema_assistencia_tecnica.
2. Copie a pasta "sistema" para C:\xampp\htdocs\sistema (ou para a pasta htdocs do seu XAMPP).
3. Abra o XAMPP Control Panel e inicie Apache e MySQL.
4. Abra http://localhost/phpmyadmin
   Se o phpMyAdmin estiver configurado para a porta 3307, selecione Importar e importe saep_db.sql.
   Se o phpMyAdmin não conectar, confira a porta em C:\xampp\mysql\bin\my.ini e a configuração do phpMyAdmin.
5. Confira sistema/includes/config.php:
   DB_HOST = 127.0.0.1
   DB_PORT = 3307
   DB_NAME = saep_db
   DB_USER = root
   DB_PASS = '' (vazio, se não definiu senha)
6. Acesse http://localhost/sistema/login.php

LOGIN DE DEMONSTRAÇÃO
E-mail: admin@local.test
Senha: password

O QUE O PROJETO CONTÉM
- Login com validação de falha e senha verificada por password_verify.
- Sessão expira após 30 minutos de inatividade.
- Painel com nome do usuário, atalhos e opção de sair.
- Cadastro e busca de clientes.
- CPF de novos clientes criptografado antes de salvar no banco.
- Cadastro de equipamentos associado a um cliente.
- Ordens de serviço com cliente/equipamento relacionados e ordenação por data de abertura.
- Script SQL com quatro tabelas e três registros de demonstração em cada tabela.

IMPORTANTE
- Os dados de demonstração do CPF são marcadores fictícios; novos CPFs cadastrados são criptografados pela aplicação.
- APP_KEY em config.php é uma chave didática local. Troque-a antes de qualquer uso real e não a publique.
- Este é um projeto acadêmico básico, não uma solução pronta para produção. Não use dados reais de clientes durante testes.
- Se sua porta 3307 for a do Apache e não a do MySQL, ajuste DB_PORT para a porta do MySQL/MariaDB. A porta do banco é a que importa para a conexão PDO.
