# php-screenshot-translator
A php script that verifies new screenshots taken form apps or games, identify texts and translate them for the user.

Esse script verifica de tempo em tempo uma pasta selecionada, para ver se há novas imagens na mesma. Ao encontrar, utilizando a API do Google Vision, ele reconhece os textos na imagem e provém uma forma de você traduzir o mesmo sem sair da tela.

O caso de uso original para o projeto, era para, ao jogar algum jogo em modo "Windowed Fullscreen" em um monitor em uma tela, a aplicação fica aberta no segundo monitor, traduzindo o que o usuário está vendo na tela principal (ao tirar print), auxiliando pessoas que não tem conhecimento na lingua.

A ideia era para ajudar minha namorada que não tem conhecimento em inglês, a jogar junto comigo e aproveitar os jogos de forma que sem conhecer inglês, ela não poderia.  

O projeto pode ser usado para reconhecer texto em qualquer imagem, não limitado a print de jogos.  Sua criatividade é o limite aqui.

# Requerimentos:

- Composer
- PHP fopen permission
- PHP GD extension

# Instalação

- Rode "composer update"

- Crie um projeto no console do Google Cloud (https://console.cloud.google.com)

- Ative as API's "Google Vision" e "Google Translate"

- Crie o arquivo JSON de autenticação Service Key no console do google em credenciais/criar credenciais/chave da conta de serviço/json. Pode colocar como permissão completa do projeto para testes. (https://console.cloud.google.com/apis/credentials)

- Renomeie seu arquivo json para "service_api_key.json" e coloque na raiz do projeto

- Feito :)

# Problemas e Idéias

- O projeto precisa ser rodado localmente, no computador do usuário. Caso queira rodar ele como um serviço online, uma opção é apontar o sistema para um folder virtual do Drive ou Dropbox, e sincronizar essa pasta com uma pasta no seu computador, aonde as imagens estarão sendo salvas. É relativamente simples de fazer, e há varios packages para usar a api do Drive dessa forma. 

- Se a pasta selecionada estiver vazia de início, vai dar erro, é necessário modificar o código para ignorar caso esteja sem arquivos na pasta. Se tiver tempo, modifico aqui o código pra isso.

# Considerações

Esse projeto foi feito totalmente sem intuito de levar adiante ou transformar em algo polido. Use como base para suas ideias e se possível, de um fork para eu ver no que você está trabalhando!

Eu comentei linha por linha para ajudar quem quer entender o que foi feito. Lembre-se que esse projeto foi feito em poucas horas como forma de testar uma ideia, então os código não seguiram nenhum design patern, não está orientado a objetos e não há testes nem nada do tipo. Mas está funcional.

ps: Valeu pessoal do grupo Laravel Brasil no facebook pelo feedback o/

Divirta-se!