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

error_reporting();
Sessao::open();
//------- PHP TRACK VARS ---------------------------------//
//Captura variávis do POST
$posts = $_POST;
if (is_array($posts)) {
    while (list($keyPost,$valPost) = each($posts)) {
        $valorPost = $valPost;
        $keyPost  = $$valorPost;
    }
}

//Captura variávis do GET
$gets = $_GET;
if (is_array($gets)) {
    while (list($keyGets,$valGets) = each($gets)) {
        $valorGets = $valGets;
        $keyGets  = $$valorGets;
    }
}
//Captura variávis dos COOKIES
$cookies = $_COOKIE;
if (is_array($cookies)) {
    while (list($keyCookies,$valCookies) = each($cookies)) {
        $valorCookies = $valCookies;
        $keyCookies  = $$valorCookies;
    }
}
//Captura variávis do SESSION
$sessions = $_SESSION;
if (is_array($sessions)) {
    while (list($keySessions,$valSessions) = each($sessions)) {
        $valorSessions = $valSessions;
        $keySessions  = $$valorSessions;
    }
}
//------- FIM PHP TRACK VARS ----------------------------//

header("Expires: Tue, 23 Jun 2002 01:46:05 GMT"); // qualquer data no passado
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0

//     include($stCaminho."sistema/setup.inc.php");
     include '../../../../../gestaoAdministrativa/fontes/PHP/framework/include/tabelas.inc.php';
     include 'sessaoLegado.class.php';
     include 'dataBaseLegado.class.php';
     include 'funcoesLegado.lib.php';
/*
    Adicionado por Diego Barbosa Victoria
    Data: 13/05/2004
    COMENTADO PORQUE ESTAVA DANDO ERRO.
*/
/*
     include_once( $stCaminho."includes/Constante.inc.php");
     include_once( $stCaminho."classes/mapeamento/TAuditoria.class.php");
     include_once( $stCaminho."classes/mapeamento/TConfiguracao.class.php");
*/
if (isset($acao))
Sessao::write('acao', $acao);
/*
    Adicionado por Diego Barbosa Victoria
    Data: 13/05/2004
    COMENTADO PORQUE ESTAVA DANDO ERRO.
*/
/*
$obTConfiguracao = new TConfiguracao;
$obTConfiguracao->pegaConfiguracao(&$stParametro, 'versao_sistema');
sessao->stNomeProgramaVersao = $PHP_SELF;
sessao->nmVersaoSistema      = $stParametro;
*/
function mostraTitulo()
{
    global $gera;
    print "
       <table width='100%'>
       <tr>
       <!-- <td class='labelcenter' height='5' width='100%'><font size='1' color='#535453'><b>".$gera."</b></font></td> -->
       <td class='titulocabecalho' height='5' width='100%'>".$gera."</td>
       </tr>
       </table>";
}
/*
$sSQL = "SELECT count(cod_acao) as contar FROM administracao.permissao WHERE cod_acao = ".Sessao::read('acao')." AND numcgm = ".Sessao::read('numCgm')."
 And ano_exercicio = ".Sessao::getExercicio();
//echo $sSQL;
$dbEmp = new dataBaseLegado;
$dbEmp->abreBD();
$dbEmp->abreSelecao($sSQL);
$dbEmp->vaiPrimeiro();
$contar  = trim($dbEmp->pegaCampo("contar"));
$dbEmp->vaiProximo();
$dbEmp->limpaSelecao();
$dbEmp->fechaBD();
if ($contar == 0) {
    //Em caso de erro de permissão envia para a página de login que exibe o erro e grava auditoria
    header("location:".$stCaminho."login.php?erro=permissao&codUsuario=".Sessao::read('numCgm')."&codAcao=".Sessao::read('acao'));
    exit();
}
*/
//$sSQL .= " SELECT nom_acao FROM administracao.acao WHERE cod_acao =".Sessao::read('acao');
/*
    * Data Alteração: 21/05/2004
    * Desenvolvedor: Diego Barbosa Victoria
    * Efetuada alteração no Select para suportar novo cabeçalho de identificação.
*/
$sSQL = "";
$sSQL .= " SELECT   nom_acao                                    \n";
$sSQL .= "         ,complemento_acao                            \n";
$sSQL .= "         ,nom_funcionalidade                          \n";
$sSQL .= "         ,nom_modulo                                  \n";
$sSQL .= "         ,M.cod_modulo as cod_modulo                  \n";
$sSQL .= " FROM     sw_acao            as A                     \n";
$sSQL .= "         ,sw_funcionalidade  as F                     \n";
$sSQL .= "         ,sw_modulo          as M                     \n";
$sSQL .= " WHERE   A.cod_funcionalidade = F.cod_funcionalidade  \n";
$sSQL .= " AND     F.cod_modulo         = M.cod_modulo          \n";
$sSQL .= " AND     A.cod_acao = ".Sessao::read('acao')."               \n";

$dbEmp = new dataBaseLegado;
$dbEmp->abreBD();
$dbEmp->abreSelecao($sSQL);
$dbEmp->vaiPrimeiro();
$gera="";
while (!$dbEmp->eof()) {
   $nomeacao           = trim($dbEmp->pegaCampo("nom_acao"));
   $complentoacao      = trim($dbEmp->pegaCampo("complemento_acao"));
   $nomefuncionalidade = trim($dbEmp->pegaCampo("nom_funcionalidade"));
   $nomemodulo         = trim($dbEmp->pegaCampo("nom_modulo"));
   $codigomodulo       = trim($dbEmp->pegaCampo("cod_modulo"));
   $dbEmp->vaiProximo();
   $gera .= "$nomemodulo :: $nomefuncionalidade :: $nomeacao $complentoacao";
}
$dbEmp->limpaSelecao();
$dbEmp->fechaBD();
if ($codigomodulo != 0) {
    $sSQL = "SELECT count(cod_acao) as contar FROM administracao.permissao WHERE cod_acao = ".Sessao::read('acao')." AND numcgm = ".Sessao::read('numCgm')." And ano_exercicio = ".Sessao::getExercicio();

    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $dbEmp->vaiPrimeiro();
    $contar  = trim($dbEmp->pegaCampo("contar"));
    $dbEmp->vaiProximo();
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    if ($contar == 0) {
        //Em caso de erro de permissão envia para a página de login que exibe o erro e grava auditoria
        header("location:".$stCaminho."login.php?erro=permissao&codUsuario=".Sessao::read('numCgm')."&codAcao=".Sessao::read('acao'));
        exit();
    }
}

?>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv='Pragma' content='no-cache'>
<meta http-equiv='Cache-Control' content='no-store, no-cache, must-revalidate'>
<META HTTP-EQUIV="expires" CONTENT="Tue, 23 Jun 2002 01:46:05 GMT">
<script src="<?=$stCaminho;?>includes/ifuncoesJs.js" type="text/javascript"></script>
<script type="text/javascript">
      MontaCSS("<?=$stCaminho?>");
</script>

<!-- PONTO 10 -->
<?php
    if ( is_file( "../../../includes/Constante.inc.php" ) ) {
        include_once '../../../includes/Constante.inc.php';
        include_once(CAM_INCLUDES."IncludeClasses.inc.php");
        include_once(CAM_INCLUDES."JavaScript.inc.php");
    }

?>
</head><body leftmargin=0 topmargin=0>
<?php
if (!(isset($_SESSION["sessao"])))
        echo "<script type='text/javascript'>
                window.location='".$stCaminho."index.php'
                </script>";
?>
<?php
global $mostraTitulo;
if (!$mostraTitulo) {
    mostraTitulo();
}
?>

<!-- *******************  Layers para bloquear frames ******************************** -->
<script type="text/javascript">
    function anulaTecla()
    {
    }
</script>
<div id="fundo_carregando">
    <div id="texto_carregando">
    <table width="100%" cellspacing="2" cellpadding="2" border="0">
    <tr >
        <td colspan="2" class="alt_dados">Processando</td>
    </tr>
    </table>

    <table border=0 align="center" valign="middle">
    <tr>
        <td align="center">
            <img id="img_carregando" src="<?php echo CAM_IMAGENS;?>/loading.gif"><br><br>
        </td>
    </tr>
    </table>
    </div>
 </div>
<script>
    document.getElementById('fundo_carregando').style.visibility='hidden';
</script>
<center>
<!-- *********************************************************************************-->

<?php
/*global $mostraTitulo;
if (!$mostraTitulo) {
    mostraTitulo();
}*/

Sessao::geraURLRandomica();
?>
