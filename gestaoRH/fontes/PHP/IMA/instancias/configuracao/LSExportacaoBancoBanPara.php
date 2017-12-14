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
    * Arquivo de Lista
    * Data de Criação: 01/04/2008

    * @author Alex Cardoso

    * Casos de uso: uc-04.08.18

    $Id: LSExportacaoBancoBanPara.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      			);

$stPrograma  = "ExportacaoBancoBanPara";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

$stCaminho = CAM_GRH_IMA_INSTANCIAS."configuracao/";
$stAcao    = $_REQUEST["stAcao"];

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    default       : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$stLink = "&stAcao=".$stAcao;
if ( $request->get("pg") &&  $request->get("pos") ) {
    $stLink.= "&pg=".$request->get("pg")."&pos=".$request->get("pos");
    $arSessaoLink = array();
    $arSessaoLink["pg"]  = $request->get("pg");
    $arSessaoLink["pos"] = $request->get("pos");
    Sessao::write('link', $arSessaoLink);

}
$stFiltro = "";
$stOrdem  = "";
if (trim($stAcao)=="excluir") {
    $stOrdem = " ORDER BY ultima_vigencia_competencia.vigencia DESC LIMIT 1";
}

include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanpara.class.php" );
$obTIMAConfiguracaoBanpara = new TIMAConfiguracaoBanpara();
$obTIMAConfiguracaoBanpara->recuperaVigencias($rsRegistros, $stFiltro, $stOrdem);

$obLista = new Lista;
$obLista->setRecordSet( $rsRegistros );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Vigência");
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "vigencia" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodEmpresa" , "cod_empresa");
$obLista->ultimaAcao->addCampo( "&dtVigencia"   , "vigencia");

if (trim($stAcao) == "excluir") {
    $obLista->ultimaAcao->addCampo( "&stDescQuestao" , "Codigo Empresa [codigo] - Vigência [vigencia]" );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
