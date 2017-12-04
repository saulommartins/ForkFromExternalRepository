<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

     error_reporting (0);
     include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
     include_once '../funcoesLegado.lib.php';

     $file = $_FILES['file'];
?>
<html><head>
<title></title>

<link rel=STYLESHEET type=text/css href=stylos_ns.css>

<script type="text/javascript">
 function InsereNomeImagem(sValor)
 {
         window.opener.parent.frames['telaPrincipal'].document.frm.logotipo.value = sValor;
         window.close();
 }
</script>

</head>
<body leftmargin=0 topmargin=0 OnLoad="placeFocus()">
<center>
<table width=100%><tr><td class="labelcenter" height=5 width=100%><font size=1 color=#535453><b>
Inserir Imagem</b></font></td></tr></table>
<br>

<?php
if (!(isset($file))) {
?>

<b>Insira aqui o logotipo de sua Prefeitura.<font color=red>
<br>A imagem deve ter exatamente 60 pixels de largura por 55  pixels de altura.<br><br>
<table width=300 cellspacing=0 border=0 cellpadding=0>
<form action="uploadImageLegado.php?<?=Sessao::getId()?>" method="POST" enctype="multipart/form-data">
<tr>
<td class=label>Imagem: </td><td class=field><input type="file" name="file"></td>
</tr>
<tr>
<td colspan=3 class=field><input type="submit" value="OK" style="width: 60px"></td>
</tr>
</form>
</table>

<?php
} else {
?>

<?php

$dest = CAM_FW_IMAGENS.$file['name'];

list($width, $height, $type, $attr) = getimagesize($file['tmp_name']);

    if ($file['type'] <> 'image/jpeg') {
        echo "<h1>O Arquivo precisa ser JPG</h1><br>";
        exit (0);
    }

     if (($width > 60) AND ($height > 55)) {
         echo "Dimensões da imagem inválida ($width pixels x $height pixels)
                <br><font color=red><b>A imagem deve ter exatamente 60 pixels de largura por 55 pixels de altura.</b><br>
            <br><font color=black><b>Para finalizar clique no botão abaixo.</b><br>
            <input type=button value=Fechar onClick='javascript:window.close();'>";
            exit (0);

    }

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
           echo "Não foi possível enviar o arquivo!;
                <br><b>Para finalizar clique no  botão abaixo.</b><br>
                  <input type=button value=Fechar onClick='javascript:window.close();'>";
    } else {
       echo "<h1>Arquivo ".$file['name']." foi enviado com sucesso!</h1><br>
            <br><b>Para finalizar clique no  botão abaixo.</b><br>
            <input type=button value=Fechar onClick=\"javascript:InsereNomeImagem('".$file['name']."');\">";
    }

}
?>

</center>
</body></html>
