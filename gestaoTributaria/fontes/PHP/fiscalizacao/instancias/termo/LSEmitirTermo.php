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
 * Página que lista os Processos Fiscais para emitir Termo
 * Data de Criacao: 11/11/2008
 *
 *
 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Marcio Medeiros
 *
 * @package URBEM
 * @subpackage
 * @ignore
 *
 * $Id: LSEmitirTermo.php 59612 2014-09-02 12:00:51Z gelson $
 *
 *Casos de uso:
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

require_once( CAM_GT_FIS_NEGOCIO."RFISProrrogarRecebimentoDocumentos.class.php" );
require_once( CAM_GT_FIS_VISAO."VFISProrrogarRecebimentoDocumentos.class.php" );

//Instanciando a Classe de Controle e de Visao
$obRegra = new RFISProrrogarRecebimentoDocumentos();
$obVisao = new VFISProrrogarRecebimentoDocumentos( $obRegra );

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$inTipoFiscalizacao = $_GET['inTipoFiscalizacao'] ?  $_GET['inTipoFiscalizacao'] : $_POST['inTipoFiscalizacao'];

//Define o nome dos arquivos PHP
$stPrograma = "EmitirTermo";
$pgFilt     = "FL" . $stPrograma . ".php";
$pgList     = "LS" . $stPrograma . ".php";
$pgForm     = "FM" . $stPrograma . ".php";
$pgProc     = "PR" . $stPrograma . ".php";
$pgOcul     = "OC" . $stPrograma . ".php";
$pgJs       = "JS" . $stPrograma . ".php";
$stCaminho  = CAM_GT_FIS_INSTANCIAS . "termo/";

$stLink .= "&stAcao=" . $stAcao;

# Mantem filtro e paginação
if ($_GET["pg"] and $_GET["pos"]) {
    $stLink .= "&pg=" . $_GET["pg"] . "&pos=" . $_GET["pos"];
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

# Define filtros da pesquisa
$stFiltro = $obVisao->filtrosDocumentos( $_REQUEST );

// Pesquisar apenas tipo 2 (Obra)
$rsRecordSet = $obVisao->recuperarListaInicioFiscalizacaoObra( $stFiltro );
$stTipoInscricao = "Inscrição Imobiliária";

# Define lista de Processos
$obLista = new Lista;
$obLista->setMostraPaginacao( true );
$obLista->setTitulo( "Registros de Processo Fiscal" );
$obLista->setRecordSet( $rsRecordSet );

# Campo numérico
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 5 );
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

# Campo tipo de inscrição
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( $stTipoInscricao );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

# Campo da ação
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

#
# Dados
#

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->ultimoDado->setCampo( "[cod_tipo] - [descricao]" );
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

$obLista->ultimaAcao->setLink( $pgForm . "?" . Sessao::getId() . $stLink );

$obLista->commitAcao();

$obLista->show();

# Para corrigir o Cache do Navegador
unset( $inTipoFiscalizacao );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
