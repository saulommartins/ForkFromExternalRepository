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
* Arquivo instância para popup de CGM
* Data de Criação: 12/09/2006

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Diego Barbosa Victoria

$Revision: 19248 $
$Name$
$Author: hboaventura $
$Date: 2007-01-11 09:48:02 -0200 (Qui, 11 Jan 2007) $

Casos de uso: uc-03.04.03
*/

/*
$Log$
Revision 1.4  2007/01/11 11:48:02  hboaventura
Bug #8031#

Revision 1.3  2007/01/05 11:21:13  bruce
Bug #7898#
Bug #7806#

Revision 1.2  2006/09/14 09:11:41  cleisson
Criação do componente fornecedor

Revision 1.1  2006/09/14 09:05:11  cleisson
Criação do componente fornecedor

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_COM_MAPEAMENTO ."TComprasFornecedor.class.php"               				 );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarFornecedor";
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
$stFncJavaScript .= " if ( window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."') ) { window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; } \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".value = sNum; \n";
if ($inner!=0) {
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".Hdn".$_REQUEST["campoNum"].".value = sNum; \n";
}
if ($_REQUEST["campoNom"]) {
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNom"].".value = sNom; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".focus(); \n";
}
$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " }                          \n";

$obTCGM = new TComprasFornecedor();
$obMascara = new Mascara;
$stFiltro = "";
$stLink   = "";

//Definição do filtro de acordo com os valores informados
if ($_REQUEST["campoNom"]) {
    $stLink .= '&campoNom='.$_REQUEST['campoNom'];
}
if ($_REQUEST["nomForm"]) {
    $stLink .= '&nomForm='.$_REQUEST['nomForm'];
}
if ($_REQUEST["campoNum"]) {
    $stLink .= '&campoNum='.$_REQUEST['campoNum'];
}

if ($_REQUEST["stNomeCgm"]) {
    $stFiltro .= " AND  lower(nom_cgm) like lower('".$_REQUEST['stHdnNomeCgm']."') ";
    $stLink   .= "&stNomeCgm=".$_REQUEST["stNomeCgm"]."&stHdnNomeCgm=".$_REQUEST["stHdnNomeCgm"];
}

if ($_REQUEST['stTipoConsulta'] == 'certificados') {
    $stFiltro .= "  and
                    exists (  select 1
                                     from
                                           licitacao.participante_certificacao
                                      where
                                          participante_certificacao.cgm_fornecedor = forn.cgm_fornecedor  ) ";
}

//adicionado este filtro para retornar somente os fornecedores ativos
$stFiltro.= ' AND forn.ativo     = true ';

//faz busca dos CGM's utilizando o filtro setado
$stLink .= "&stAcao=".$stAcao;
$rsLista = new RecordSet;

$obTCGM->recuperaRelacionamento( $rsLista, $stFiltro, " ORDER BY lower(CGM.nom_cgm)" );
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

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();

?>
