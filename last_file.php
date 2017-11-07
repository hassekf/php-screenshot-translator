<?php 

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
// Retorna o caminho completo
echo $dir.$lastModFile;
 ?>