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
    * Página de Listagem de Cep
    * Data de Criação   : 14/09/2015

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Michel Teixeira

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: LSCep.php 63721 2015-10-01 19:52:33Z arthur $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoCEP.class.php';

$stPrograma = "Cep";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stLink = '';
//Monta sessae com os valores do filtro
$arFiltro = Sessao::read('filtroPopUp');

if($_REQUEST['pg']&&$_REQUEST['pos']){
    if ( is_array($arFiltro) ) {
        $_REQUEST = $arFiltro;
    }
}

foreach ($_REQUEST as $key => $valor) {
    $arFiltro[$key] = $valor;
}
Sessao::write('filtroPopUp',$arFiltro);

$stFncJavaScript  = " function insereCep(num,nom) {                                                                                                 \n";
$stFncJavaScript .= "   var sNum;                                                                                                                   \n";
$stFncJavaScript .= "   var sNom;                                                                                                                   \n";
$stFncJavaScript .= "   sNum = num;                                                                                                                 \n";
$stFncJavaScript .= "   sNom = nom;                                                                                                                 \n";
$stFncJavaScript .= "   window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom;         \n";
$stFncJavaScript .= "   window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".value = sNum;      \n";
$stFncJavaScript .= "   window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".focus();           \n";
$stFncJavaScript .= "   window.close();                                                                                                             \n";
$stFncJavaScript .= " }                                                                                                                             \n";

$obTCEP = new TCEP();
$stFiltro = " WHERE EXISTS ( SELECT sw_cep_logradouro.cep FROM sw_cep_logradouro where sw_cep_logradouro.cep=sw_cep.cep) ";

if($_REQUEST['inCep']!='')
    $stFiltro .= " AND sw_cep.cep LIKE ('".$_REQUEST['inHdnCep']."') ";

$stOrder = " ORDER BY cep ";
$obTCEP->recuperaTodos($rsCEP, $stFiltro, $stOrder);

$arCep = array();
$inCount = 0;
while (!$rsCEP->eof()) {
    $cep = $rsCEP->getCampo('cep');
    $stCep = "";
    for($i = 0; $i<=strlen($cep)-1; $i++){
        if($i==5)
            $stCep .= '-'.$cep[$i];
        else
            $stCep .= $cep[$i];
    }
    
    $arCep[$inCount]['cep'] = $rsCEP->getCampo('cep');
    $arCep[$inCount]['stCep'] = $stCep;
    $inCount++;
    $rsCEP->proximo();
}
$rsCEP = new RecordSet();
$rsCEP->preenche($arCep);

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsCEP );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("CEP");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "stCep" );
$obLista->ultimoDado->setTitle( "Cep" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:window.close();insereCep();" );
$obLista->ultimaAcao->addCampo("1","cep");
$obLista->ultimaAcao->addCampo("2","stCep");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();

Sessao::write('paginando',false);

?>