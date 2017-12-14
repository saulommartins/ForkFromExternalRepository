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
 * Página que lista os Processos Fiscais para emitir Auto de Infração
 * Data de Criacao: 21/08/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage
 * @ignore

 $Id: LSEmitirAutoInfracao.php 59612 2014-09-02 12:00:51Z gelson $

 *Casos de uso:
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

require_once( CAM_GT_FIS_NEGOCIO.'RFISProrrogarRecebimentoDocumentos.class.php');
require_once( CAM_GT_FIS_NEGOCIO.'RFISEmitirAutoInfracao.class.php');
require_once( CAM_GT_FIS_VISAO.  'VFISProrrogarRecebimentoDocumentos.class.php');
require_once( CAM_GT_FIS_VISAO.  'VFISEmitirAutoInfracao.class.php');

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

if ( empty( $stAcao ) ) {
    $stAcao = "emitir";
}

# Controle de listagem de acordo com a ação
if (!$_REQUEST['stAcao'] || $_REQUEST['stAcao'] == 'emitir') {
    $stAcao  = 'notificar';
    $obRegra = new RFISProrrogarRecebimentoDocumentos();
    $obVisao = new VFISProrrogarRecebimentoDocumentos( $obRegra );

    $stFiltro = $obVisao->filtrosDocumentos( $_REQUEST );

    switch ($inTipoFiscalizacao) {
        case 1:
            $rsRecordSet    = $obVisao->recuperarListaInicioFiscalizacaoEconomica( $stFiltro );
            $stTituloColuna = "Inscrição Econômica";
            break;

        case 2:
            $rsRecordSet    = $obVisao->recuperarListaInicioFiscalizacaoObra( $stFiltro );
            $stTituloColuna = "Inscrição Imobiliária";
            break;

        default:
            $rsRecordSet    = $obVisao->recuperarListaInicioFiscalizacaoEconomicaObra( $stFiltro );
            $stTituloColuna = "Inscrição Econômica/Imobiliária";
            break;
    }
    $stValorColuna = "inscricao";
    $stTitulo = "Registros de Processo Fiscal";
} else {
    $stAcao  = 'imprimir';
    $obRegra = new RFISEmitirAutoInfracao();
    $obVisao = new VFISEmitirAutoInfracao( $obRegra );

    $rsRecordSet = $obVisao->recuperaAutoFiscalizacao( $_REQUEST['inCodProcesso'] );

    $stTituloColuna = "Auto de Infração";
    $stValorColuna  = "cod_auto_fiscalizacao";
    $stTitulo       = "Registros de Auto de Infração";
}

$inTipoFiscalizacao = $_GET['inTipoFiscalizacao'] ?  $_GET['inTipoFiscalizacao'] : $_POST['inTipoFiscalizacao'];

//Define o nome dos arquivos PHP
$stPrograma = "EmitirAutoInfracao";
$pgFilt     = "FL" . $stPrograma . ".php";
$pgList     = "LS" . $stPrograma . ".php";
$pgForm     = "FM" . $stPrograma . ".php";
$pgProc     = "PR" . $stPrograma . ".php";
$pgOcul     = "OC" . $stPrograma . ".php";
$pgJs       = "JS" . $stPrograma . ".php";
$stCaminho  = CAM_GT_FIS_INSTANCIAS . "processoFiscal/";

# Define arquivos PHP para cada acao

# Mantem filtro e paginação
$stLink .= "&stAcao=" . $stAcao;

if ( $_GET["pg"] and $_GET["pos"] )    $obFormulario->show();

{
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

# Define lista de Processos
$obLista = new Lista;
$obLista->setMostraPaginacao( true );
$obLista->setTitulo( $stTitulo );
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

# Campo dinamico
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( $stTituloColuna );
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
$obLista->ultimoDado->setCampo( $stValorColuna );
$obLista->commitDado();

# Define ação
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inTipoFiscalizacao", "cod_tipo" );
$obLista->ultimaAcao->addCampo( "stDescricao", "descricao" );
$obLista->ultimaAcao->addCampo( "inCodProcesso", "cod_processo" );
$obLista->ultimaAcao->addCampo( "inInscricao", "inscricao" );
$obLista->ultimaAcao->addCampo( "inCodFiscal", "cod_fiscal" );

if ($stAcao == 'imprimir') {
    $obLista->ultimaAcao->addCampo( "inCodAutoFiscalizacao", "cod_auto_fiscalizacao" );
    $obLista->ultimaAcao->setLink( $pgProc . "?" . Sessao::getId() . $stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgList . "?" . Sessao::getId() . $stLink );
}

$obLista->commitAcao();

$obLista->show();

if ($stAcao == 'imprimir') {
    $obForm = new Form();
    $obForm->setAction( $pgForm );

    $obFormulario = new Formulario();
    $obFormulario->addForm( $obForm );

    $obHdnTipoFiscalizacao = new Hidden();
    $obHdnTipoFiscalizacao->setName( "inTipoFiscalizacao" );
    $obHdnTipoFiscalizacao->setValue( $_GET['inTipoFiscalizacao']);

    $obHdnDescricao = new Hidden();
    $obHdnDescricao->setName( "stDescricao" );
    $obHdnDescricao->setValue( $_GET['stDescricao'] );

    $obHdnCodProcesso = new Hidden();
    $obHdnCodProcesso->setName( "inCodProcesso" );
    $obHdnCodProcesso->setValue( $_GET['inCodProcesso']  );

    $obHdnInscricao = new Hidden();
    $obHdnInscricao->setName( "inInscricao" );
    $obHdnInscricao->setValue( $_GET['inInscricao']  );

    $obHdnCodFiscal = new Hidden();
    $obHdnCodFiscal->setName( "inCodFiscal" );
    $obHdnCodFiscal->setValue( $_GET['inCodFiscal']  );

    $obFormulario->addHidden($obHdnTipoFiscalizacao);
    $obFormulario->addHidden($obHdnDescricao);
    $obFormulario->addHidden($obHdnCodProcesso);
    $obFormulario->addHidden($obHdnInscricao);
    $obFormulario->addHidden($obHdnCodFiscal);

    $obBtnNovo = new Button();
    $obBtnNovo->setName( "btnNovo" );
    $obBtnNovo->setValue( "Novo" );
    $obBtnNovo->setTipo( "button" );
    $obBtnNovo->obEvento->setOnClick("Salvar();");
    $obBtnNovo->setDisabled( false );

    $obFormulario->defineBarra( array($obBtnNovo), "left", "" );

    $obFormulario->show();
}

# Para corrigir o Cache do Navegador
unset( $inTipoFiscalizacao );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
