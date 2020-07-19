# api_ctrl_fin
 
Projeto desenvolvido como requisito do processo seletivo da empresa Lightup.

Api foi construída utilizando PHP 7.2 OO sem o uso de frameworks e o SGBD MySQL.

Para rodar a apliacão:

a. faça o download dos arquivos ou clone o repositório;
b. crie o banco de dados conforme o arquivo ctrl_fin.sql;
c. altere as credenciais do banco de dados no arquivo classes/Conexao.sql.

Ao acessar o endereço inicial será apresentada mensagem de boas vindas.

Foram cadastradas 3 pessoas para testes:
- CPF 12312312312;
- CPF 32132132132;
- CPF 11122233300.

Os endpoints conforme solicitado são:

- Saldo = http://localhost/{local_do_arquivo}/pessoas/saldo/{cpf} (será apresentado o valor do saldo da pessoa);
- Extrato = http://localhost/{local_do_arquivo}/pessoas/extrato/{cpf} (será apresentada a movimentação da pessoa, sendo apresentados os débitos, créditos e transferências efetuadas);
- Débito = http://localhost/{local_do_arquivo}/pessoas/debito/{cpf}/{valor} (caso a pessoa tenha saldo será efetuado o débito do valor informado. O valor pode ser informado no formato integer ou float);
- Transferências = http://localhost/{local_do_arquivo}/pessoas/transferencia/{cpf}/{valor}/{cpf_beneficiario} (caso a pessoa tenha saldo será efetuado o débito em seu CPF e será efetuado um crédito para o beneficiário. O valor pode ser informado no formato integer ou float);

Obrigado.


