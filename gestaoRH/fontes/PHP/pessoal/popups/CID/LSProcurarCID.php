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
* Página de lista do CID
* Data de Criação: 04/01/2006

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 30892 $
$Name$
$Author: andre $
$Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

* Casos de uso: uc-04.04.38
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCID.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCID";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

$stCaminho = CAM_GRH_PES_INSTANCIAS."CID/";

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$stCampoNum = $request->get("stCampoNum");
$stCampoNom = $request->get("stCampoNom");
$inCodCID   = $request->get("inCodCID");

if($inCodCID == '') {
    $inCodCID = 'inCodCID';
}

$stFncJavaScript  = " function insereCID(inSiglaCID,stDescricaoCID,inCodCID) {                                                             \n";
$stFncJavaScript .= "    var siglaCID;                                                                                                     \n";
$stFncJavaScript .= "    var descricaoCID;                                                                                                 \n";
$stFncJavaScript .= "    var codCID;                                                                                                       \n";
$stFncJavaScript .= "    siglaCID = inSiglaCID;                                                                                            \n";
$stFncJavaScript .= "    descricaoCID = stDescricaoCID;                                                                                    \n";
$stFncJavaScript .= "    codCID = inCodCID;                                                                                                \n";
$stFncJavaScript .= "    window.opener.parent.frames['telaPrincipal'].document.getElementById('".$stCampoNom."').innerHTML = descricaoCID; \n";
$stFncJavaScript .= "    window.opener.parent.frames['telaPrincipal'].document.frm.".$stCampoNum.".value = siglaCID;                       \n";
$stFncJavaScript .= "    window.opener.parent.frames['telaPrincipal'].document.frm.".$inCodCID.".value = codCID;                           \n";
$stFncJavaScript .= "    window.close();                                                                                                   \n";
$stFncJavaScript .= " }                                                                                                                    \n";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "selecionar";
}

//MANTEM FILTRO E PAGINACAO
$arLink = Sessao::read("stLink");
$stLink = "&stAcao=".$stAcao;

if ( $request->get("pg") and  $request->get("pos") ) {
    $stLink.= "&pg=".$request->get("pg")."&pos=".$request->get("pos");
    $arLink["pg"]  = $request->get("pg");
    $arLink["pos"] = $request->get("pos");
}
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($arLink) ) {
    $_REQUEST = $arLink;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $arLink[$key] = $valor;
    }
}

Sessao::write("stLink",$arLink);

$rsLista = new RecordSet;
$obTPessoalCID = new TPessoalCID;
$stFiltro = isset($stFiltro) ? $stFiltro : null;

if ($_REQUEST["stFiltroSigla"]) {
    $stFiltro = " AND cid.sigla ilike '".$_REQUEST["stFiltroSigla"]."%'";
}
if ($_REQUEST["stFiltroDescricao"]) {
    $stFiltro = " AND cid.descricao ilike '".$_REQUEST["stFiltroDescricao"]."%'";
}

$obTPessoalCID->recuperaRelacionamento($rsLista,$stFiltro);

$obLista = new Lista;
$obLista->setRecordSet          ( $rsLista );
//$obLista->setTitulo             ("Registros");
$stTitulo = ' </div></td></tr><tr><td colspan="5" class="alt_dados">Registros';
$obLista->setTitulo             ('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia().$stTitulo);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Sigla" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "sigla" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:window.close(); insereCID();"   );
$obLista->ultimaAcao->addCampo( "&stSigla"  , "sigla"      );
$obLista->ultimaAcao->addCampo( "&descricao" , "descricao" );
$obLista->ultimaAcao->addCampo( "&inCodCid" , "cod_cid" );
$obLista->commitAcao();

$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();
?>
