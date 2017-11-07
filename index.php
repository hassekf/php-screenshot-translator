<!-- Checando se tem POST, ou seja, se foi escolhido uma pasta para checar -->
<?php if(!isset($_POST['folder'])) {?>
<!-- Se não foi, renderiza um form simples usando bootstrap. -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Html5</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  </head>
  <body>
    <div class="container">
		<form action="" method="POST" role="form">
			<legend>Selecione a pasta com as imagens para checar</legend>
			<div class="form-group">
				<label for="">Pasta (Use o caminho completo. Ex: C:/Fraps/Screenshots)</label>
				<input type="text" class="form-control" name="folder" value="C:/Fraps/Screenshots">
			</div>
			<button type="submit" class="btn btn-primary">Usar Pasta Selecionada</button>
		</form>
    </div>
  </body>
</html>
<!-- Veio o POST -->
<?php } else { 

/*  
	Esse código abaixo é o que checa pela primeira vez, qual o aquivo mais antigo na pasta. É a partir da data
	de modificação dele que vamos checar a existência dos próximos
*/

// Monta a url concatenando a pasta escolhida no formulário com um separador (garantia)
$dir = $_POST['folder'].DIRECTORY_SEPARATOR;
$lastMod = 0;
$lastModFile = '';
// Para cada arquivo compara com o último e sua data de modificação, se for mais atual, modifica o lastModFile para o atual.
foreach (scandir($dir) as $entry) {
    if (is_file($dir.$entry) && filectime($dir.$entry) > $lastMod) {
        $lastMod = filectime($dir.$entry);
        $lastModFile = $entry;
    }
}

// Aqui criamos o caminho completo, concatenando o diretório com o nome do arquivo mais antigo na pasta
$last= $dir.$lastModFile;

?>

<!-- ========================================================================================== -->
<!-- VEIO FOLDER -->
<!-- ========================================================================================== -->
<!-- Jquery UI, usado para selecionar os textos (CSS) -->
<link rel="stylesheet" href="jquery-ui.min.css">
<!-- Jquery, duh -->
<script type="text/javascript" src="jquery.js"></script>
<!-- Jquery UI, usado para selecionar os textos (JS) -->
<script type="text/javascript" src="jquery-ui.min.js"></script>
 <!-- Um estilo rápido para mostrar visivelmente o que está sendo selecionado.
 Pelo amor de deus, coloca isso em um css separado antes de usar caso for estilizar de verdade -->
 <style>
  .selectable .ui-selecting { background: #FECA40!important; }
  .selectable .ui-selected { background: #F39814!important; color: white!important; }
 /* O overflow hidden aqui tira as barras de rolagem da página do projeto, para quando carregar a imagem, ele preencher a tela toda quando usando
 da mesma resolução de onde foi tirado o print. As vezes tem textos na borda da tela o qual a barra de rolagem fica por cima, atrapalhando o uso */
  html,body{margin:0;padding:0;overflow:hidden;}
  </style>

<!-- É aqui que vamos jogar o print já com a verificação das imagens -->
<div class="body"></div>

<!-- Esse é uma div de ajuda, nela vamos jogar a string selecionada para traduzir. Caso não queira mostrar ela, descomente o estilo. -->
<div id="dialog" title="Tradução" style="/*display:none;*/">
  	Nenhuma imagem nova na pasta.
</div>



<script>
// Pega o arquivo atual (lembra que pegamos ele no php lá em cima?)
var current = "<?= $last ?>";
// Pega a pasta atual
var folder = "<?=$_POST['folder'] ?>";

$(function () {
	// Roda a primeira vez o heartbeat
	heartbeat();
});
function heartbeat() {
	// Heartbeat é a função que vai rodar a cada X segundos, checando por uma nova imagem na pasta escolhida.
    setTimeout( function() {

    		// Essa requisição ajax no arquivo last_file.php, vai só pedir "Qual o nome do arquivo mais antigo?"
			$.ajax({
				url: 'last_file.php',
				method: 'post',
				data:{folder:"<?=str_replace('\\', '\\\\', str_replace('/', '//', $_POST['folder'])); ?>"},
				success: function(data) {
					// Trim é só para tirar os espaços que as vezes vem na string. 
					// Se você fizer json_encode no php, isso não vai acontecer após o decode aqui. 
					if (data.trim() == current) {
						// O nome do arqui é igual ao que temos, não faz nada
						console.log('Não tem print novo!');
					} else {
						// é diferente
						console.log('Tem print novo!');
						// muda o atual na variavel do Javascript só para sabermos quando for checar novamente em alguns segundos, para não repitir
						current = data.trim();
						// Limpa o body 
						$( ".body" ).empty();
						// Mostra uma mensagem. Aqui é aonde você pode ser criativo, coloque um loader, ou mostre a imagem semi-transparente no fundo, etc. 
						// A mensagem abaixo é só um feedback visual, pois como a imagem está sendo enviada como upload para o google abaixo, conforme
						// o tamanho do print, pode demorar um pouco.
						$('.body').html('Checando novo print');

						// Bom para debugar.
						console.log('Checando Imagem');

						// Faz o request, enviado como parametro, o caminho completo da imagem atual para reconhecer texto e trazer o html
						$.ajax({
							url: 'check_image.php',
							method: 'post',
							data: {current:current},
							success: function(result) {
								// Deu tudo certo, recebemos o resultado
								console.log('Imagem checada, colocando resultado na tela');
								// O resultado está em json
								result = $.parseJSON(result);
								// Limpa o body novamente para tirar o loader ou a mensagem
								$( ".body" ).empty();
								// E insere o que veio de resultado do check_image.php
								$( ".body" ).html(result.html);
								// Vamos rodar o selectable do Jquery UI para transformar o que veio em selecionável. 
								pode_selecionar();
							}
						});


					};
					// Recursivo. Mandando rodar novamente o heartbeat. 
					heartbeat();
				}
			});


    }, 2000);
}
</script>

  <script>
  // Função simples que faz todas as divs com texto, selecionáveis com drag n' drop como no windows. 
function pode_selecionar(){
    $( ".selectable" ).selectable({
      // Ao soltar o mouse
      stop: function() {
      	// Limpa a div com id dialog, que é aonde guardamos o texto a ser traduzido
        var result = $( "#dialog" ).empty();
        // Se algo foi selecionado...
        if ($( ".ui-selected", this ).length>0) {
        	// Para cada palavra selecionada...
	        $( ".ui-selected", this ).each(function() {
	        	//concatena ela e adiciona dentro do #dialog
	          result.append( " " + $(this).html() );
	        });

	        // Agora vamos colocar o texto todo selecionado dentro dessa variavel text
	        var text = $( "#dialog" ).html();

	        // Só para podermos usar o dialog como feedback visual (opcional)
	        $( "#dialog" ).html('Traduzindo...');

	        // Agora vamos requisitar a tradução da string para o Google Translate
			$.ajax({
				url: 'translate.php',
				method: 'post',
				data: {text:text},
				success: function(data) {
					// Retornando os dados, vamos limpar a div
					$( "#dialog" ).empty();
					// Colocar o que veio
					$( "#dialog" ).html(data);
					// E abrir como modal. 
					// (Estou usando o jquery ui só por questão de tempo, 
					// mas tem sweetalert, modal do bootstrap ou você criar 
					// uma nova forma de mostrar a tradução para o usuário)
					$( "#dialog" ).dialog();
				}
			});
        };
      }
    });
}
  </script>

<?php } ?>