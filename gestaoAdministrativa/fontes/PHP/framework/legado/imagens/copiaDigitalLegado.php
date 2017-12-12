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

//include("cabecalho.php");
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_FW_LEGADO."funcoesLegado.lib.php");

$tmp = explode("=", Sessao::getId());
$dirSession = $tmp[1];

$acao = $_REQUEST['acao'];
$ctrl = $_REQUEST['ctrl'];
$codDoc = $_REQUEST['codDoc'];
$codProcesso = $_REQUEST['codProcesso'];
$anoExercicio = $_REQUEST['anoExercicio'];
$imagem = $_REQUEST['imagem'];
$file = $_REQUEST['file'];

if (isset($acao))
    Sessao::write('acao', $acao);

$diretorio = pegaConfiguracao("diretorio");

$anoE = pegaConfiguracao("ano_exercicio");

$sSQL = "SELECT nom_acao FROM administracao.acao WHERE cod_acao =".Sessao::read('acao');

$dbEmp = new dataBaseLegado;
$dbEmp->abreBD();
$dbEmp->abreSelecao($sSQL);
$dbEmp->vaiPrimeiro();
$gera="";
while (!$dbEmp->eof()) {
   $nomeacao  = trim($dbEmp->pegaCampo("nom_acao"));
   $dbEmp->vaiProximo();
   $gera .= $nomeacao;
}
$dbEmp->limpaSelecao();
$dbEmp->fechaBD();
?>
<html><head>
<link rel=STYLESHEET type=text/css href=stylos_ns.css>

<meta http-equiv='Pragma' content='no-cache'>
<meta http-equiv='Cache-Control' content='no-store, no-cache, must-revalidate'>
<meta http-eqiv='Expires' content='10 mar 1967 09:00:00 GMT'>
<script type="text/javascript">
    var flag=false;
    function alertaAvisos(objeto,tipo,chamada)
    {
        var x = 400;
        var y = 300;
        var sessao   = '<?=Sessao::getId()?>';
        var sessaoid = sessao.substr(10,6);
        var sArq = '../../popups/alerta/alerta.php?'+sessao+'&tipo='+tipo+'&chamada='+chamada+'&obj='+objeto;

        var sAux = "window.open(sArq,'msga"+ sessaoid +"','width=300px,height=200px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
        eval(sAux);
    }

    function alertaQuestao(pagina,chave,valor,objeto,tipo)
    {
        var x = 400;
        var y = 300;
        var sessao   = '<?=Sessao::getId()?>';
        var sessaoid = sessao.substr(10,6);
        var sArq = '../../popups/alerta/alerta.php?'+sessao+'&tipo='+tipo+'&chamada=sn&chave='+chave+'&valor='+valor+'&pag='+pagina+'&obj='+objeto;

        var sAux = "window.open(sArq,'msga"+ sessaoid +"','width=300px,height=200px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
        eval(sAux);
    }
    
    function removeSelecionados()
    {
        var combo = document.frm.arquivosAnexos;
        newList = new Array ( combo.options.length );
        for (var i = combo.options.length - 1; i >= 0; i--) {
            if (combo.options[i].selected == true) {
                combo.options[i] = null;
                document.frm.rm.value = 1;
            }
        }
        document.frm.action = "<?=CAM_FW_LEGADO.'imagens/copiaDigitalLegado.php';?>?<?=Sessao::getId()?>&ctrl=2&codDoc=<?=$codDoc?>";
        document.frm.submit();
    }

    function submeter()
    {
        flag = true;
        document.frm.action = "<?=CAM_FW_LEGADO.'imagens/copiaDigitalLegado.php';?>?<?=Sessao::getId()?>&ctrl=1&codDoc=<?=$codDoc;?>&acao=<?=Sessao::read('acao')?>";
        document.frm.submit();
    }

    function exclusao(arq)
    {
        flag = true;
        document.frm.action = "<?=CAM_FW_LEGADO.'imagens/copiaDigitalLegado.php';?>?<?=Sessao::getId()?>&ctrl=2&file="+arq+"&codDoc=<?=$codDoc;?>";
        //alert(arq);
        document.frm.submit();
    }

    function finalizar()
    {
        flag = true;
        document.frm.action = "<?=CAM_FW_LEGADO.'imagens/copiaDigitalLegado.php';?>?<?=Sessao::getId()?>&ctrl=3&codDoc=<?=$codDoc?>";
        document.frm.submit();
    }
</script>
</head><body leftmargin=0 topmargin=0>

<form name="frm" action="<?=CAM_FW_LEGADO.'copiaDigitalLegado.php';?>?<?=Sessao::getId()?>" method="post" enctype="multipart/form-data">
<?php

if (!(isset($ctrl)))
    $ctrl = 0;

switch ($ctrl) {
    case 0:
        $select = 	"SELECT nom_documento FROM sw_documento WHERE cod_documento = ".$codDoc;
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $dbConfig->abreSelecao($select);
        $nomDocumento = $dbConfig->pegaCampo("nom_documento");
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();
?>
    <table width="90%" align=center>
    <input type="hidden" name="codProcesso" value="<?=$codProcesso?>">
    <input type="hidden" name="anoExercicio" value="<?=$anoExercicio?>">
        <tr>
            <td class=alt_dados colspan=2><?=$nomDocumento?></td>
        </tr>
        <tr>
            <td class=label>Imagem</td>
            <td class=field><input type="radio" name="imagem" value="t" checked>Sim&nbsp;
               <input type="radio" name="imagem" value="f">Não</td>
        </tr>
        <tr>
            <td class=label>Arquivo</td>
            <td class=field><input type="file" name="docDigital"></td>
        </tr>
        <tr>
            <td class=field colspan=2>
                <input type="button" name="ok" value="OK" style="width: 60px" onclick="submeter();">&nbsp;
                <input type="button" name="fechar" value="Fechar" onclick="finalizar();">
            </td>
        </tr>
    </table>
    <table width="70%" align="center">
    <tr>
        <td class=alt_dados colspan=2>Arquivos Anexos</td>
    </tr>
    <?php
        // nao existe a acao 60 !???
        if ($acao == 59 || $acao == 60) {

            $select = 	"SELECT anexo FROM sw_copia_digital WHERE cod_documento=".$codDoc." and cod_processo=".$codProcesso." and exercicio='".$anoExercicio."'";
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();

            $dbConfig->abreSelecao($select);
            while (!($dbConfig->eof())) {
                $arquivo = pathinfo($dbConfig->pegaCampo("anexo"));
                if ($dbConfig->pegaCampo("anexo") != "") {
                    echo "<tr><td class=show_dados colspan=2><a href='".CAM_PROTOCOLO."anexos/".$arquivo["basename"]."' target='_blanck'>".$arquivo["basename"]."</a></td></tr>";
                }
                $dbConfig->vaiProximo();
            }
        }
        //else{
            $diretorio = CAM_PROTOCOLO."tmp/";
            $lista = opendir($diretorio);
            while ($file = readdir($lista)) {
                if ($file == $dirSession) {
                    $dirSessao = CAM_PROTOCOLO."tmp/".$dirSession."/";
                    $listaSessao = opendir($dirSessao);
                    while ($fileSessao = readdir($listaSessao)) {
                        if ($fileSessao == $codDoc) {
                            $dirDoc = CAM_PROTOCOLO."tmp/".$dirSession."/".$codDoc."/";

                            $listaDoc = opendir($dirDoc);
                            while ($fileDoc = readdir($listaDoc)) {
                                if ($fileDoc == '.' || $fileDoc == '..' || $fileDoc == 'naoExclua.txt' || $fileDoc=='CVS') {
                                    continue;
                                }

                                $fileUser = explode("_", $fileDoc);
                                $extensao = explode(".", $fileUser[3]);
                                $arq = substr(Sessao::getId(),10,6);
                                if ($arq == $fileUser[1]) {
                                    echo '<tr><td class=show_dados width=80%>'.$fileUser[2].'.'.$extensao[1].'
                                            </td><td class=show_dados width="10%">
                                            <a href="javascript:exclusao(\''.$fileDoc.'\');">
                                            <img src="'.CAM_FW_IMAGENS.'btnexcluir.gif" border="0"></a></td></tr>';
                                }
                            }
                        }
                    }
                }
            }

    ?>
    </table>
<?php
    break;
        case 1:

            $docDigital = $_FILES['docDigital'];

            $erros = 0;
            if ($imagem == "t") {
                $arqName = explode(".", $docDigital['name']);

                $filemodificada = explode("_", $docDigital['name']);
                for ($i = 0; $i <= count($filemodificada)-1; $i++) {
                    $arquivo_modificado .= $filemodificada[$i];
                }
                $docDigital['name'] = $arquivo_modificado;

                $arqName = explode(".", $arquivo_modificado);

                $arq = substr(Sessao::getId(),10,6);
                $docDigital['name'] = $codDoc."_".$arq."_".date("d-m-Y-H-i-s", time())."§".$arqName[0].".jpg";

                if ($docDigital['type'] <> 'image/jpeg') {
                    $erros++;
                    $errors = $errors."O Arquivo precisa ser JPG";
                }

                if ($docDigital['size'] > 1000000) {
                    $erros++;
                    $errors = $errors."O Arquivo não pode ter mais que 1000KB";
                }

                if (file_exists($docDigital['name'])) {
                    $erros++;
                    $errors = $errors."O arquivo já existe";
                }

                if ($erros == 0) {

                    $pasta = CAM_PROTOCOLO."tmp/".$dirSession;
                    
                    if (!is_dir($pasta)) {
                        mkdir($pasta, 0777);
                    }
                    
                    $pasta = CAM_PROTOCOLO."tmp/".$dirSession."/".$codDoc;

                    if (!is_dir($pasta)) {
                        mkdir($pasta,0777);
                    }
                    
                    $pasta = $pasta."/".$docDigital['name'];

                    copy($docDigital['tmp_name'], $pasta);
                    $controlaTumb = 1 ;
                    unset($ctrl);

                    echo '<script type="text/javascript">
                        window.location = "'.CAM_FW_LEGADO.'imagens/copiaDigitalLegado.php?'.Sessao::getId().'&codDoc='.$codDoc.'&acao='.Sessao::read('acao').'&codProcesso='.$codProcesso.'&anoExercicio='.$anoExercicio.'";
                        </script>';
                } else {

                    unset($ctrl);
                    echo '<script type="text/javascript">
                        alertaAvisos("'.$errors.'","unica","erro");
                        window.location = "'.CAM_FW_LEGADO.'imagens/copiaDigitalLegado.php?'.Sessao::getId().'&codDoc='.$codDoc.'&acao='.Sessao::read('acao').'&codProcesso='.$codProcesso.'&anoExercicio='.$anoExercicio.'";
                        </script>';
                }
            }

            # No images
            if ($imagem == "f") {
                $arqName = explode(".", $docDigital['name']);

                $filemodificada = explode("_", $docDigital['name']);
                for ($i = 0; $i <= count($filemodificada)-1; $i++) {
                    $arquivo_modificado .= $filemodificada[$i];
                }
                $docDigital['name'] = $arquivo_modificado;
                $arqName = explode(".", $arquivo_modificado);

                $arq = substr(Sessao::getId(),10,6);
                $docDigital['name'] = $codDoc."_".$arq."_".date("d-m-Y-H-i-s", time())."§".$arqName[0].".".$arqName[1];

                if ($docDigital['size'] > 1000000) {
                    $erros++;
                    $errors = $errors."O Arquivo não pode ter mais que 1000KB";
                }

                if (file_exists($anexo)) {
                    $erros++;
                    $errors = $errors."O arquivo já existe";
                }

                if ($erros == 0) {
                    $pasta = CAM_PROTOCOLO."tmp/".$dirSession;

                    if (!file_exists ( $pasta )) {
                        mkdir($pasta, 0777);
                    }

                    $pasta = CAM_PROTOCOLO."tmp/".$dirSession."/".$codDoc;

                    if (!file_exists ( $pasta )) {
                        mkdir($pasta, 0777);
                    }

                    $pasta = $pasta."/".$docDigital['name'];

                    copy($docDigital['tmp_name'], $pasta);
                    $controlaTumb = 1 ;
                    unset($ctrl);

                    echo '<script type="text/javascript">
                        window.location = "'.CAM_FW_LEGADO.'imagens/copiaDigitalLegado.php?'.Sessao::getId().'&codDoc='.$codDoc.'&acao='.Sessao::read('acao').'&codProcesso='.$codProcesso.'&anoExercicio='.$anoExercicio.'";
                        </script>';
                } else {
                    unset($ctrl);
                    echo '<script type="text/javascript">
                        alertaAvisos("'.$errors.'","unica","erro");
                        window.location = "'.CAM_FW_LEGADO.'imagens/copiaDigitalLegado.php?'.Sessao::getId().'&codDoc='.$codDoc.'&acao='.Sessao::read('acao').'&codProcesso='.$codProcesso.'&anoExercicio='.$anoExercicio.'";
                        </script>';
                }
            }
    break;
        case 2:

                $diretorio = CAM_PROTOCOLO."tmp/".$dirSession."/".$codDoc."/".$file;
                if (unlink($diretorio)) {
                    $diretorio = CAM_PROTOCOLO."tmp/".$dirSession."/".$codDoc;
                    //Verifica se tem mais algum arquivo dentro da pasta
                    if ( count(scandir($diretorio)) <= 2 ) {
                        rmdir($diretorio);
                    }

                    //$diretorio = CAM_PROTOCOLO."tmp/".$dirSession;
                    //rmdir($diretorio);

                    unset($ctrl);
                    echo '<script type="text/javascript">
                                window.location = "'.CAM_FW_LEGADO.'imagens/copiaDigitalLegado.php?'.Sessao::getId().'&codDoc='.$codDoc.'&acao='.Sessao::read('acao').'&codProcesso='.$codProcesso.'&anoExercicio='.$anoExercicio.'";
                            </script>';
                } else {
                    unset($ctrl);
                    echo '<script type="text/javascript">
                        alertaAvisos("'.$errors.'","unica","erro");
                        window.location = "'.CAM_FW_LEGADO.'imagens/copiaDigitalLegado.php?'.Sessao::getId().'&codDoc='.$codDoc.'&acao='.Sessao::read('acao').'&codProcesso='.$codProcesso.'&anoExercicio='.$anoExercicio.'";
                        </script>';
                }
    break;
        case 3:
             echo '<script type="text/javascript">
                    window.close();
                    </script>';
    break;
    }
?>
</form>
</body>
</html>
