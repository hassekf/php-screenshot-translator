<?
// Parte boa aqui, vamos checar a imagem com o Google Vision

// Vamos incluir o autoload do que instalamos com o composer
# Includes the autoloader for libraries installed with composer
require __DIR__ . '/vendor/autoload.php';

// Importar as bibliotecas que vamos usar
# Imports the Google Cloud client library
use Google\Cloud\Vision\VisionClient;
// import the Intervention Image Manager Class
use Intervention\Image\ImageManager;
# Imports the Google Cloud client library (Não usado aqui)
// use Google\Cloud\Translate\TranslateClient;

// O image manager é usado para manipular imagens, você pode redimensionar para caber dentro da tela do seu projeto, adicionar elementos, etc
// Primariamente, estaremos apenas usando ele para pegar as dimensões da imagem, você vai entender o motivo.
$img = new ImageManager(array('driver' => 'gd'));

// Aqui é o slug/ID do seu projeto no console do Google Cloud. Normalmente é o nome com traços
# Your Google Cloud Platform project ID
$projectId = 'id-do-seu-projeto';
// Para não ter trabalho com autenticação, crie um service key no console do google e aponte para o json na env abaixo.
putenv("GOOGLE_APPLICATION_CREDENTIALS=service_api_key.json");

// Instanciando um novo cliente do google Vision
# Instantiates a client
$vision = new VisionClient([
    'projectId' => $projectId
]);

// Nome do arquivo que veio no ajax
$fileName = $_POST['current'];

// Usando ImageManager no arquivo
$img = $img->make($fileName);

// Manda o arquivo para o Google Verificar os textos
# Prepare the image to be annotated
$image = $vision->image(fopen($fileName, 'r'), [
    'TEXT_DETECTION'
]);
// Resultado da verificação
$result = $vision->annotate($image)->text();

// Usamos o ImageManager para pegar largura e altura da imagem
$image_width = $img->width();
$image_height = $img->height();

// Vamos salvar a imagem no folder localmente para usar ela no html.
$img_path = 'images/'.uniqid().'.jpg';
// Salvar...
$img->save($img_path);

// Boxes é um array aonde vamos salvar cada palavra encontrada, e colocar elas dentro de uma div nas coordenadas que o Google Identificou elas.
$boxes = array();

// Caso tenha algum texto encontrado pelo google.
if ($result) {
	// Para cada resultado
	foreach ($result as $key => $text) {
			// Não sendo o primeiro resultado (ps: O primeiro é todos os textos juntos, o que vira uma bagunça)
		if ($key != '0') {
			// Palavra encontrada
			$boxes[$key]['text'] = $text->description();
			// Quantos píxeis o canto superior esquerdo está do topo (posição do texto)
			$boxes[$key]['top'] = $text->boundingPoly()['vertices'][0]['y'];
			// Quantos píxeis o canto superior esquerdo está da esquerda (posição do texto)
			$boxes[$key]['left'] = $text->boundingPoly()['vertices'][0]['x'];
			// Qual a largura do texto na imagem? (para fazermos uma div que cobre o texto na posição exata que ele se encontra)
			// Aqui estou pegando a posição do topo/direita do texto em pixels e diminuindo pelo topo/esquerda. A diferença é a largura em pixels.
			$boxes[$key]['width'] = $text->boundingPoly()['vertices'][1]['x']-$text->boundingPoly()['vertices'][0]['x'];
			// Qual a altura do texto na imagem?
			// Mesmo que o acima, só que usando baixo/esquerda, menos topo/esquerda, 
			$boxes[$key]['height'] = $text->boundingPoly()['vertices'][2]['y']-$text->boundingPoly()['vertices'][0]['y'];


		}
	}
}
// Agora, vamos montar o html para retornar como resposta
$data['html'] = '';
// Concatena uma div, a qual o fundo é nossa queria imagem. Aqui colocamos ela com largura e altura usando as variaveis que criamos antes.
$data['html'] .= '<div style="overflow:hidden;background:url(\''.$img_path.'\') center center no-repeat;position:relative;width:'.$image_width.'px;height:'.$image_height.'px;" class="selectable">';
// Só para confirmar que tem pelo menos 1 texto na imagem
if(count($boxes)>0){	
	// Para cada texto encontrado
	foreach ($boxes as $key => $box) {
		// Cria um span, selecionável, em posição absoluta, topo e esquerda (lembra?), e largura e altura definidas antes (lembra?)
		$data['html'] .= '<span style="width:'.$box['width'].'px;height:'.$box['height'].'px;background:rgba(255, 255, 255, 0.5);border:2px solid black;position:absolute;top:'.$box['top'].'px;left:'.$box['left'].'px;">'.$box['text'].'</span>';
	}
}
// Fecha a div da imagem
$data['html'] .= '</div>';

// Retorna como Json
echo json_encode($data);
?>