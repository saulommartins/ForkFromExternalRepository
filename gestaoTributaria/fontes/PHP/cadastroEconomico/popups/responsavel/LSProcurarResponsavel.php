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
    * Página do Frame oculto para Popup de Responsavel
    * Data de Criação   : 01/03/2004

    * @author Diego Barbosa Victoria
    * @author Marcelo B. Paulino

    * @ignore

    * $Id: LSProcurarResponsavel.php 59612 2014-09-02 12:00:51Z gelson $

    Casos de uso: uc-05.02.04
*/

/*
$Log$
Revision 1.8  2006/09/15 13:50:41  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCgm";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stFncJavaScript .= " function insereCGM(num,nom) {  \n";
$stFncJavaScript .= " var sNum;                  \n";
$stFncJavaScript .= " var sNom;                  \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " sNom = nom;                \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".value = sNum; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".focus(); \n";
//$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNom"].".value = sNom; \n";
$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " }                          \n";

$obTCGM = new TCGM;
$obMascara = new Mascara;
$stFiltro = "";
$stLink   = "";

//Definição do filtro de acordo com os valores informados
$stLink .= "&stTipoPessoa=".$_REQUEST["stTipoPessoa"];

$stFiltro .= " AND CGM.numcgm IN (select numcgm from sw_cgm_pessoa_fisica ) ";

if ($_REQUEST["stNomeCgm"]) {
    $stFiltro .= " AND lower(nom_cgm) like lower('%".$_REQUEST["stNomeCgm"]."%') ";
    $stLink   .= "&stNomeCgm=".$_REQUEST["stNomeCgm"];
}

if ($_REQUEST["stCPF"]) {
    $inCPF = $_REQUEST["stCPF"];
    $obMascara->desmascaraDado( $inCPF );
    $stFiltro .= " AND CGM.numcgm in ( select numcgm from sw_cgm_pessoa_fisica where cpf = '".$inCPF."') ";
    $stLink   .= "&stCPF=".$_REQUEST["stCPF"];
}

/*if ($stFiltro) {
    $stFiltro = substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
}*/

//faz busca dos CGM's utilizando o filtro setado
$stLink .= "&stAcao=".$stAcao;
$rsLista = new RecordSet;
//$obTCGM->recuperaRelacionamento( $rsLista, $stFiltro, " ORDER BY CGM.nom_cgm" );
$obTCGM->recuperaRelacionamentoSintetico( $rsLista, $stFiltro, " ORDER BY CGM.nom_cgm" );
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "numcgm" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();
//$obLista->addDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereCGM();" );
$obLista->ultimaAcao->addCampo("1","numcgm");
$obLista->ultimaAcao->addCampo("2","nom_cgm");
$obLista->commitAcao();
$obLista->show();

$obBtnFiltro = new Button;
$obBtnFiltro->setName              ( "btnFiltrar" );
$obBtnFiltro->setValue             ( "Filtrar"    );
$obBtnFiltro->setTipo              ( "button"     );
$obBtnFiltro->obEvento->setOnClick ( "filtrar();" );
$obBtnFiltro->setDisabled          ( false        );

$botoes = array ( $obBtnFiltro );

$obFormulario = new Formulario;
$obFormulario->defineBarra ( $botoes, 'left', '' );
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();
?>
