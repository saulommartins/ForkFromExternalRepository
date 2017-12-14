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
 * Página que lista os Processos Fiscais para consulta
 * Data de Criacao: 20/04/2009

 * @author Fernando Cercato

 * @package URBEM
 * @subpackage
 * @ignore

 $Id: $

 *Casos de uso:
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_FIS_MAPEAMENTO.'TFISProcessoFiscal.class.php' );

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}

# Controle de listagem de acordo com a ação

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarProcesso";
$pgFilt     = "FL" . $stPrograma . ".php";
$pgList     = "LS" . $stPrograma . ".php";
$pgForm     = "FM" . $stPrograma . ".php";

# Mantem filtro e paginação
$stLink .= "&stAcao=".$stAcao;

if ($_GET["pg"] && $_GET["pos"]) {
    $stLink .= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"] = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
    Sessao::write( 'link', $link );
}

# Quando existir filtro na página FL, a variável link deve ser resetada.
$link = Sessao::read( 'link' );
if ( is_array( $link ) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }

    Sessao::write( 'link', $link );
}

$stFiltro = "";
if ($_REQUEST["cmbTipoFiscalizacao"]) {
    $stFiltro .= " processo_fiscal.cod_tipo = ".$_REQUEST["cmbTipoFiscalizacao"]." AND ";
}

if ($_REQUEST["inCodProcesso"]) {
    $stFiltro .= " processo_fiscal.cod_processo = ".$_REQUEST["inCodProcesso"]." AND ";
}

if ( $stFiltro )
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );

$obTFISProcessoFiscal = new TFISProcessoFiscal;
$obTFISProcessoFiscal->recuperaListaParaConsultaProcessoFiscal( $rsRecordSet, $stFiltro );

# Define lista de Processos
$obLista = new Lista;
$obLista->setMostraPaginacao( true );
$obLista->setTitulo( "Consulta Processo" );
$obLista->setRecordSet( $rsRecordSet );

# Campo numérico
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

# Campo tipo de fiscalização
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo de Fiscalização" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

# Campo processo fiscal
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Processo Fiscal" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

# Inscricao
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Inscrição Economica/Municipal" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

# Campo da ação
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->ultimoDado->setCampo( " [cod_tipo] - [descricao] " );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "CENTRO" );
$obLista->ultimoDado->setCampo( "cod_processo" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "CENTRO" );
$obLista->ultimoDado->setCampo( "inscricao" );
$obLista->commitDado();

# Define ação
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inTipoFiscalizacao", "cod_tipo" );
$obLista->ultimaAcao->addCampo( "stDescricao", "descricao" );
$obLista->ultimaAcao->addCampo( "inCodProcesso", "cod_processo" );
$obLista->ultimaAcao->addCampo( "inInscricao", "inscricao" );
$obLista->ultimaAcao->addCampo( "inCodFiscal", "cod_fiscal" );
$obLista->ultimaAcao->addCampo( "inCodProcProt", "cod_processo_protocolo" );
$obLista->ultimaAcao->addCampo( "inAnoExercicioProt", "ano_exercicio" );
$obLista->ultimaAcao->addCampo( "dtPerIni", "periodo_inicio" );
$obLista->ultimaAcao->addCampo( "dtPerTerm", "periodo_termino" );
$obLista->ultimaAcao->addCampo( "dtPrevIni", "previsao_inicio" );
$obLista->ultimaAcao->addCampo( "dtPrevTerm", "previsao_termino" );
$obLista->ultimaAcao->addCampo( "stNomFiscal", "nom_fiscal" );
$obLista->ultimaAcao->addCampo( "stStatusProc", "status" );

$obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
