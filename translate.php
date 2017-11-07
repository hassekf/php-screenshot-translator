<?
// Hora de traduzir texto pessoal.

// Incluindo o que instalamos via composer
# Includes the autoloader for libraries installed with composer
require __DIR__ . '/vendor/autoload.php';

// Importa a biblioteca do translate...
# Imports the Google Cloud client library
use Google\Cloud\Translate\TranslateClient;

// O id do projeto
# Your Google Cloud Platform project ID
$projectId = 'id-do-projeto';
// A service key em json
putenv("GOOGLE_APPLICATION_CREDENTIALS=service_api_key.json");

// Instaciamos o cliente do tradutor
# Instantiates a client
$translate = new TranslateClient([
    'projectId' => $projectId
]);

// Esse é o texto a ser traduzid. Aqui deixei tudo minúsculo por um "bug" do tradutor. Quando as palavras estão em caixa alta por exemplo, ele
// volta e meia identifica como nome próprio e não traduz.
$text = strtolower($_POST['text']);
// Para qual linguagem vamos traduzir. Modifique a vontade.
$target = 'pt-BR';

# Usando o cliente, mandamos traduzir no Google....
$translation = $translate->translate($text, [
    'target' => $target
]);

// Retornamos o texto traduzido para a página principal.
echo $translation['text'];