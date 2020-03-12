# Software Inova Veículos
Esse app é desenvolvido em Laravel, Vue.js e React-native e é propriedade da empresa Inova Veículos.

##Endpoints da API:
------------------------------

Sem o id retorna todos os itens da lista.
Com o id retorna só o item.

------------------------------
Para fazer um join com outra tabela como um objeto usa-se:
?join=
Por exemplo:
/api/carros?join=marca

------------------------------
Você sempre precisa do api_token pra fazer o acesso, por exemplo:
/api/carros?join=marca&api_token=<token>
ou
/api/carros/{id}?api_token=<token>

Para conseguir o token de usuário, você precisa fazer login:
POST
-----------------------------
/login
-----------------------------
Data: 
{
  email: 'email ou username',
  senha: 'senha'
}

Var ser retornado:
{
  status: 1,
  token: saodnin4n53in34nffndfsdfsdf232q...
}

Se o login der errado, retornará status 0.

------------------------------
GET
------------------------------
/api/carros
/api/carros/{id}
/api/clientes
/api/clientes/{id}
/api/estoques
/api/estoques/{id}
/api/interesses
/api/interesses/{id}

------------------------------
Mais informações no arquivo de rotas 'api.php' e nos controller dentro da pasta 'Api'
