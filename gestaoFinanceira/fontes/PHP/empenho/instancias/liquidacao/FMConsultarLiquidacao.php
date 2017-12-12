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
    * Página de Formulario de Inclusao/Alteracao de Empenho
    * Data de Criação   : 06/12/2004

    * @author Analista Jorge B. Ribarr
    * @author Desenvolvedor Tonismar Régis Bernardo

    * @ignore
    
    $Id: FMConsultarLiquidacao.php 63470 2015-08-31 18:29:24Z jean $

    $Revision: 31087 $
    $Name$
    $Autor:$
    $Date: 2007-08-24 17:21:27 -0300 (Sex, 24 Ago 2007) $

    * Casos de uso: uc-02.03.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php" );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoPagamentoLiquidacao.class.php");
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoNotaLiquidacao.class.php"     );
include_once ( CAM_GPC_TCERN_MAPEAMENTO."TTCERNNotaFiscal.class.php"     );
include_once ( CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALDocumento.class.php'      );
include_once ( CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALTipoDocumento.class.php'  );
include_once ( CAM_FW_HTML."MontaAtributos.class.php"                    );
include_once ( CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEDocumento.class.php'      );
include_once ( CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPETipoDocumento.class.php'  );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarLiquidacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$pgProc = "PRAnularLiquidacao.php";
$pgFilt = "FLAnularLiquidacao.php";
$pgList = "LSAnularLiquidacao.php";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$inCodEmpenho    = $request->get('inCodEmpenho');
$inCodPreEmpenho = $request->get('inCodPreEmpenho');
$inCodNota       = $request->get('inCodNota');
$inCodEntidade   = $request->get('inCodEntidade');

$obREmpenhoEmpenhoAutorizacao  = new REmpenhoEmpenhoAutorizacao;
$obREmpenhoPagamentoLiquidacao = new REmpenhoPagamentoLiquidacao;

//TCEMG
if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 11) {
    include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGNotaFiscal.class.php";
    include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGNotaFiscalEmpenhoLiquidacao.class.php";
    include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGTipoNotaFiscal.class.php";
    
    $obTTCEMGNotaFiscal = new TTCEMGNotaFiscal;
    $obTTCEMGNotaFiscalEmpenho = new TTCEMGNotaFiscalEmpenhoLiquidacao;
    $obTTCEMGTipoNotaFiscal = new TTCEMGTipoNotaFiscal;
    
    $stFiltroNF  = " WHERE nota_fiscal_empenho_liquidacao.cod_entidade        = " .$inCodEntidade;
    $stFiltroNF .= " AND nota_fiscal_empenho_liquidacao.exercicio_liquidacao  = '".$_REQUEST['stExercicioNota']."' ";
    $stFiltroNF .= " AND nota_fiscal_empenho_liquidacao.cod_nota_liquidacao   = " .$inCodNota;

    $obTTCEMGNotaFiscalEmpenho->recuperaTodos($rsNfLiquidacao, $stFiltroNF);

    if($rsNfLiquidacao->getNumLinhas()==1){
        
        $stFiltro  = " WHERE nota_fiscal.cod_entidade   = " .$inCodEntidade;
        $stFiltro .= " AND nota_fiscal.exercicio        = '".$rsNfLiquidacao->getCampo('exercicio')."'";
        $stFiltro .= " AND nota_fiscal.cod_nota         = " .$rsNfLiquidacao->getCampo('cod_nota');
        
        $obTTCEMGNotaFiscal->recuperaTodos($rsNF, $stFiltro);

        if($rsNF->getNumLinhas()==1){
            $stFiltroTipoDoc  = " WHERE tipo_nota_fiscal.cod_tipo   = ".$rsNF->getCampo('cod_tipo');
            $obTTCEMGTipoNotaFiscal->recuperaTodos($rsTipoNota, $stFiltroTipoDoc);            
            
            $stTipoDocumentoFiscal    = $rsTipoNota->getCampo('descricao');
            $inNumDocumentoFiscal     = $rsNF->getCampo('nro_nota');
            $inSerieDocumentoFiscal   = $rsNF->getCampo('nro_serie');
            $dtEmissaoDocumentoFiscal = $rsNF->getCampo('data_emissao');
            
            if($rsNF->getCampo('chave_acesso_municipal')!=''){
                $stChaveAcesDocumentoFiscal = $rsNF->getCampo('chave_acesso_municipal');
            }else{
                $stChaveAcesDocumentoFiscal = $rsNF->getCampo('chave_acesso');
            }
            $stNumAIDFDocumentoFiscal = $rsNF->getCampo('aidf');
            
            $inInscricaoEstadual = $rsNF->getCampo('inscricao_estadual');
            $inInscricaoMunicipal = $rsNF->getCampo('inscricao_municipal');
            
        }
    }
}

switch (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio())) {
    /* Só adiciona o filtro caso seja TCE-RN */
    case 20:
        $stFiltroNF = "   AND nota_fiscal.cod_nota_liquidacao = ".$inCodNota."
                      AND nota_fiscal.cod_entidade = ".$inCodEntidade."
                      AND nota_fiscal.exercicio = '".$_REQUEST['stExercicioNota']."' ";
                      
        $obTTCERNNotaFiscal = new TTCERNNotaFiscal;
        $obTTCERNNotaFiscal->recuperaRelacionamento($rsNF, $stFiltroNF);
        
        $stNumeroNF = $rsNF->getCampo('nro_nota');
        $stSerieNF = $rsNF->getCampo('nro_serie');
        $stDtEmissaoNF = $rsNF->getCampo('data_emissao');
        $stCodValidacaoNF = $rsNF->getCampo('cod_validacao');
        $stModeloNF = $rsNF->getCampo('modelo');
    break;

    //  Apenas se for TCEAL
    case 02:
        $obTTCEALDocumento = new TTCEALDocumento;
        $obTTCEALDocumento->setDado( "cod_nota", $inCodNota );
        $obTTCEALDocumento->setDado( "cod_entidade", $inCodEntidade );
        $obTTCEALDocumento->setDado( "exercicio", $_REQUEST['stExercicioNota'] );
        $obTTCEALDocumento->recuperaPorChave($rsDocumento,$boTransacao);
        
        if ($rsDocumento->getNumLinhas() > 0) {
            $obTTCEALTipoDocumento = new TTCEALTipoDocumento;
            $obTTCEALTipoDocumento->setDado("cod_tipo", $rsDocumento->getCampo( "cod_tipo" ) );
            $obTTCEALTipoDocumento->recuperaPorChave($rsTipoDocumento, $boTransacao);
            
            $stTipoDocumento=$rsTipoDocumento->getCampo( "descricao"     );
            $inTipoDocumento=$rsDocumento->getCampo    ( "cod_tipo"      );
            $inNroDocumento =$rsDocumento->getCampo    ( "nro_documento" );
            $dtDocumento=$rsDocumento->getCampo        ( "dt_documento"  );
            $stDescricao=$rsDocumento->getCampo        ( "descricao"     );
            $stAutorizacao=$rsDocumento->getCampo      ( "autorizacao"   );
            $stModelo=$rsDocumento->getCampo           ( "modelo"        );
            $stNumXmlNFe=$rsDocumento->getCampo        ( "nro_xml_nfe"   );
        }
    break;

    //  Apenas se for TCEPE
    case 16:
        $obTTCEPEDocumento = new TTCEPEDocumento;
        $stFiltro = " WHERE documento.cod_entidade = " .$inCodEntidade;
        $stFiltro.= " AND documento.exercicio      = '".$_REQUEST['stExercicioNota']."'";
        $stFiltro.= " AND documento.cod_nota       = " .$inCodNota;
        
        $obTTCEPEDocumento->recuperaDocumento($rsDocumento, $stFiltro, "", $boTransacao);
        
        if ($rsDocumento->getNumLinhas() > 0) {
            $obTTCEPETipoDocumento = new TTCEPETipoDocumento;
            $obTTCEPETipoDocumento->setDado("cod_tipo", $rsDocumento->getCampo( "cod_tipo" ) );
            $obTTCEPETipoDocumento->recuperaPorChave($rsTipoDocumento, $boTransacao);
            
            $stTipoDocumento=$rsTipoDocumento->getCampo( "descricao"     );
            $inTipoDocumento=$rsDocumento->getCampo    ( "cod_tipo"      );
            $inNroDocumento =$rsDocumento->getCampo    ( "nro_documento" );
            $stSerie=$rsDocumento->getCampo            ( "serie"         );
            $inCodUf=$rsDocumento->getCampo            ( "cod_uf"      );
        }
    break;

    //  Apenas se for TCETO
    case 27:
        include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETONotaLiquidacaoDocumento.class.php';
        $obTTCETONotaLiquidacaoDocumento = new TTCETONotaLiquidacaoDocumento;
        $stFiltro = " WHERE nota_liquidacao_documento.cod_entidade = " .$inCodEntidade;
        $stFiltro.= " AND nota_liquidacao_documento.exercicio      = '".$_REQUEST['stExercicioNota']."'";
        $stFiltro.= " AND nota_liquidacao_documento.cod_nota       = " .$inCodNota;
        
        $obTTCETONotaLiquidacaoDocumento->recuperaTodos($rsDocumento, $stFiltro, "", $boTransacao);
        
        if ($rsDocumento->getNumLinhas() > 0) {
            include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOTipoDocumento.class.php';
            $obTTCETOTipoDocumento = new TTCETOTipoDocumento;
            $obTTCETOTipoDocumento->setDado("cod_tipo", $rsDocumento->getCampo( "cod_tipo" ) );
            $obTTCETOTipoDocumento->recuperaPorChave($rsTipoDocumento, $boTransacao);
            
            $stTipoDocumento=$rsTipoDocumento->getCampo( "descricao"     );
            $inTipoDocumento=$rsDocumento->getCampo    ( "cod_tipo"      );
            $inNroDocumento =$rsDocumento->getCampo    ( "nro_documento" );
            $dtDocumento=$rsDocumento->getCampo        ( "dt_documento"  );
            $stDescricao=$rsDocumento->getCampo        ( "descricao"     );
            $stAutorizacao=$rsDocumento->getCampo      ( "autorizacao"   );
            $stModelo=$rsDocumento->getCampo           ( "modelo"        );
        }
    break;

    //TCM BA
    case 5:
        include_once(CAM_GPC_TCMBA_MAPEAMENTO.Sessao::getExercicio()."/TTCMBANotaFiscalLiquidacao.class.php");
        $obTTCMBANotaFiscalLiquidacao = new TTCMBANotaFiscalLiquidacao();
        
        $obTTCMBANotaFiscalLiquidacao->setDado('cod_nota_liquidacao' , $request->get('inCodNota')       );
        $obTTCMBANotaFiscalLiquidacao->setDado('exercicio_liquidacao', $request->get('stExercicioNota') );
        $obTTCMBANotaFiscalLiquidacao->setDado('cod_entidade'        , $request->get('inCodEntidade')   );
        $obTTCMBANotaFiscalLiquidacao->recuperaPorChave($rsNotasFiscais,"","",$boTransacao);

        $stNumeroNF       = $rsNotasFiscais->getCampo('nro_nota');
        $stExercicioNota  = $rsNotasFiscais->getCampo('exercicio_liquidacao');
        $stSerieNF        = $rsNotasFiscais->getCampo('nro_serie');
        $stSubSerie       = $rsNotasFiscais->getCampo('nro_subserie');
        $stDataEmissaoNF  = $rsNotasFiscais->getCampo('data_emissao');
        $stVlNotas = $rsNotasFiscais->getCampo('vl_nota');

        if (!empty($stVlNotas)) {
            $stValorNF = number_format($stVlNotas,2,",",".");
        }

        $stDescricaoNF    = $rsNotasFiscais->getCampo('descricao');
        $stCodUf = $rsNotasFiscais->getCampo('cod_uf');

        if (!empty($stCodUf)) {
            $stUF = SistemaLegado::pegaDado("sigla_uf","sw_uf"," WHERE cod_uf = '".$stCodUf."'");
        }
        
    break;
}

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicio( $_REQUEST['dtExercicioEmpenho'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setExercicio( $_REQUEST['dtExercicioEmpenho']  );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->listar( $rsTipo );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoHistorico->listar( $rsHistorico );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listarUnidadeMedida( $rsUnidade );

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio( $_REQUEST['dtExercicioEmpenho'] );
$stMascaraRubrica = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();

Sessao::remove('arItens');

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodEmpenho( $_REQUEST['inCodEmpenho'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setCodAutorizacao( $_REQUEST['inCodAutorizacao'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodPreEmpenho( $_REQUEST['inCodPreEmpenho'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
if ($_REQUEST['boImplantado'] == 't') {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->consultarRestosAPagar();
} else {
    $obREmpenhoEmpenhoAutorizacao->consultar();
}
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->consultarValorItem();

$obREmpenhoPagamentoLiquidacao->setExercicio( $_REQUEST['dtExercicioEmpenho'] );

$inNumUnidade = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade();
$stNomUnidade = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNomUnidade();
$inNumOrgao   = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
$stNomOrgao   = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNomeOrgao();

$stNumLiquidacao    = $_REQUEST[ "inCodNota" ]."/".$_REQUEST[ "stExercicioNota" ];
$stNomEmpenho       = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getDescricao();
$stNomEntidade      = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->getNomCGM();
$inCodTipo          = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->getCodTipo();
$stNomTipo          = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->getNomTipo();
$inCodDespesa       = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->getCodDespesa();
$stNomDespesa       = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getDescricao();
$stCodClassificacao = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->getMascClassificacao();
$stNomClassificacao = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->getDescricao();
$inCodFornecedor    = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obRCGM->getNumCGM();
$stNomFornecedor    = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obRCGM->getnomCGM();
$stDtVencimento     = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getDtVencimento();
$inCodHistorico     = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoHistorico->getCodHistorico();
$stNomHistorico     = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoHistorico->getNomHistorico();
$stDtEmpenho        = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getDtEmpenho();
$arItemPreEmpenho = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getItemPreEmpenho();

$obREmpenhoNotaLiquidacao      = new REmpenhoNotaLiquidacao( $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho );
$obREmpenhoNotaLiquidacao->setCodNota       ( $_REQUEST[ "inCodNota" ]          );
$obREmpenhoNotaLiquidacao->setExercicio     ( $_REQUEST[ "stExercicioNota" ] );
//$obREmpenhoNotaLiquidacao->setCodPreEmpenho ( $_REQUEST['inCodPreEmpenho']      );

if ($_REQUEST['boImplantado'] == 't') {
    $obREmpenhoNotaLiquidacao->listarNotaLiquidacaoEmpenhoRestos( $rsNotaLiquidacao );
} else {
    $obREmpenhoNotaLiquidacao->listarNotaLiquidacaoEmpenho( $rsNotaLiquidacao );
}

$stDtLiquidacao = $rsNotaLiquidacao->getCampo( "dt_liquidacao" );

$arItens = array();
$arItens = Sessao::read('arItens');
foreach ($arItemPreEmpenho as $inCount => $obItemPreEmpenho) {
    $arItens[$inCount]['num_item']     = $obItemPreEmpenho->getNumItem();
    $arItens[$inCount]['nom_item']     = $obItemPreEmpenho->getNomItem();
    $arItens[$inCount]['complemento']  = $obItemPreEmpenho->getComplemento();
    $arItens[$inCount]['quantidade']   = $obItemPreEmpenho->getQuantidade();
    $arItens[$inCount]['cod_unidade']  = $obItemPreEmpenho->obRUnidadeMedida->getCodUnidade();
    $arItens[$inCount]['cod_grandeza'] = $obItemPreEmpenho->obRUnidadeMedida->obRGrandeza->getCodGrandeza();
    $arItens[$inCount]['nom_unidade']  = $obItemPreEmpenho->getNomUnidade();
    $arItens[$inCount]['vl_total']     = $obItemPreEmpenho->getValorTotal() - $obItemPreEmpenho->getValorEmpenhadoAnulado();
    $arItens[$inCount]['vl_unitario']  = bcdiv( $obItemPreEmpenho->getValorTotal(), $obItemPreEmpenho->getQuantidade(),2 );
    $arItens[$inCount]['vl_empenhado_anulado']  = $obItemPreEmpenho->getValorEmpenhadoAnulado();
    $arItens[$inCount]['vl_liquidado']          = $rsNotaLiquidacao->getCampo( "liquidado" );
    $arItens[$inCount]['exercicio']          = $rsNotaLiquidacao->getCampo( "exercicio_empenho" );
    $arItens[$inCount]['cod_empenho']          = $rsNotaLiquidacao->getCampo( "cod_empenho" );
    $arItens[$inCount]['cod_entidade']          = $rsNotaLiquidacao->getCampo( "cod_entidade" );
    $arItens[$inCount]['cod_nota']          = $rsNotaLiquidacao->getCampo( "cod_nota" );
    $arItens[$inCount]['possui_estornos']          = $rsNotaLiquidacao->getCampo( "possui_estornos" );

    $rsNotaLiquidacao->proximo();
}
Sessao::write('arItens', $arItens);

SistemaLegado::executaFramePrincipal("buscaDado('montaListaItemPreEmpenho');");
$arChaveAtributo =  array( "cod_pre_empenho" => $_REQUEST["inCodPreEmpenho"],
                           "exercicio"       => $_REQUEST['dtExercicioEmpenho'] );

$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos     );

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->setCodDespesa( $inCodDespesa );
//$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicio( Sessao::getExercicio() );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicio( $_REQUEST['dtExercicioEmpenho'] );

if ( $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getImplantado() != 't' ) {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->consultaSaldoAnterior( $nuSaldoDotacao );
}

// FAZER ESTE MONTE DE RECUPERACAO DE VALORES!
//$nuVlAnulacao = $nuVTotal + $nuSaldoAnterior - $nuVlLiquidacao + $nuVlLiquidacaoAnulada - $nuVlAnulado;

$nuSaldoDotacao = number_format( $nuSaldoDotacao, 2, ',', '.');

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

// Define objeto Hidden para Codigo da Empenho
$obHdnCodEmpenho = new Hidden;
$obHdnCodEmpenho->setName ( "inCodEmpenho" );
$obHdnCodEmpenho->setValue( $inCodEmpenho );

// Define objeto Hidden para Codigo da Pre Empenho
$obHdnCodPreEmpenho = new Hidden;
$obHdnCodPreEmpenho->setName ( "inCodPreEmpenho" );
$obHdnCodPreEmpenho->setValue( $inCodPreEmpenho );

// Define objeto Hidden para Codigo da Entidade
$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ( "inCodEntidade" );
$obHdnCodEntidade->setValue( $inCodEntidade );

// Define objeto Hidden para Exercicio Empenho
$obHdnExercicioEmpenho = new Hidden;
$obHdnExercicioEmpenho->setName ( "dtExercicioEmpenho" );
$obHdnExercicioEmpenho->setValue( $_REQUEST["dtExercicioEmpenho"] );

// Define objeto Label para Empenho
$obLblEmpenho = new Label;
$obLblEmpenho->setRotulo( "N° Empenho" );
$obLblEmpenho->setValue ( $inCodEmpenho );

// Define objeto Label para nro liquidacao
$obLblLiquidacao = new Label;
$obLblLiquidacao->setRotulo( "N° Liquidação" );
$obLblLiquidacao->setValue ( $stNumLiquidacao );

// Define Objeto Label para Data da Liquidacao
$obLblDtLiquidacao = new Label;
$obLblDtLiquidacao->setRotulo ( "Data de Liquidação" );
$obLblDtLiquidacao->setId     ( "stDtLiquidacao" );
$obLblDtLiquidacao->setValue  ( $stDtLiquidacao  );

switch (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio())) {
    // TCEMG
    case 11:
        // Define Objeto Label para Data da Liquidacao
        $obLblTipoDocumento = new Label;
        $obLblTipoDocumento->setRotulo ( "Tipo de Documento Fiscal" );
        $obLblTipoDocumento->setId     ( "stTipoDocumentoFiscal" );
        $obLblTipoDocumento->setValue  ( $stTipoDocumentoFiscal  );
        
        $obLblNumeroDocumento = new Label;
        $obLblNumeroDocumento->setRotulo ( "Número" );
        $obLblNumeroDocumento->setId     ( "inNumDocumentoFiscal" );
        $obLblNumeroDocumento->setValue  ( $inNumDocumentoFiscal  );
        
        $obLblSerieDocumento = new Label;
        $obLblSerieDocumento->setRotulo ( "Série da NF" );
        $obLblSerieDocumento->setId     ( "inSerieDocumentoFiscal" );
        $obLblSerieDocumento->setValue  ( $inSerieDocumentoFiscal  );
        
        $obLblDataDocumento = new Label;
        $obLblDataDocumento->setRotulo ( "Data da NF" );
        $obLblDataDocumento->setId     ( "dtEmissaoDocumentoFiscal" );
        $obLblDataDocumento->setValue  ( $dtEmissaoDocumentoFiscal  );
        
        $obLblChaveAcesso = new Label;
        $obLblChaveAcesso->setRotulo ( "Chave de Acesso" );
        $obLblChaveAcesso->setId     ( "stChaveAcesDocumentoFiscal" );
        $obLblChaveAcesso->setValue  ( $stChaveAcesDocumentoFiscal  );
        
        $obLblNumeroAIDF = new Label;
        $obLblNumeroAIDF->setRotulo ( "Número da AIDF" );
        $obLblNumeroAIDF->setId     ( "stNumAIDFDocumentoFiscal" );
        $obLblNumeroAIDF->setValue  ( $stNumAIDFDocumentoFiscal  );
        
        $obLblInscricaoEstadual = new Label;
        $obLblInscricaoEstadual->setRotulo ( "Inscrição Estadual" );
        $obLblInscricaoEstadual->setId     ( "inInscricaoEstadual" );
        $obLblInscricaoEstadual->setValue  ( $inInscricaoEstadual  );
        
        $obLblInscricaoMunicipal = new Label;
        $obLblInscricaoMunicipal->setRotulo ( "Inscrição Municipal" );
        $obLblInscricaoMunicipal->setId     ( "inInscricaoMunicipal" );
        $obLblInscricaoMunicipal->setValue  ( $inInscricaoMunicipal  );
    break;

    // TCERN
    case 20:
        // Define Objeto Label para Data da Liquidacao
        $obLblNumeroNF = new Label;
        $obLblNumeroNF->setRotulo ( "Número da NF" );
        $obLblNumeroNF->setId     ( "stNumeroNF" );
        $obLblNumeroNF->setValue  ( $stNumeroNF  );
        
        $obLblDtEmissaoNF = new Label;
        $obLblDtEmissaoNF->setRotulo ( "Data de Emissão da NF" );
        $obLblDtEmissaoNF->setId     ( "stDtEmissaoNF" );
        $obLblDtEmissaoNF->setValue  ( $stDtEmissaoNF  );
        
        $obLblSerieNF = new Label;
        $obLblSerieNF->setRotulo ( "Série da NF" );
        $obLblSerieNF->setId     ( "stSerieNF" );
        $obLblSerieNF->setValue  ( $stSerieNF  );
        
        $obLblCodValidacaoNF = new Label;
        $obLblCodValidacaoNF->setRotulo ( "Código de Validação da NF" );
        $obLblCodValidacaoNF->setId     ( "stCodValidacaoNF" );
        $obLblCodValidacaoNF->setValue  ( $stCodValidacaoNF  );
        
        $obLblModeloNF = new Label;
        $obLblModeloNF->setRotulo ( "Modelo da NF" );
        $obLblModeloNF->setId     ( "stModeloNF" );
        $obLblModeloNF->setValue  ( $stModeloNF  );
    break;
    
    // TCEAL
    case 02:
        // Define Objeto Label 
        $obLblTipoDocumento = new Label;
        $obLblTipoDocumento->setRotulo ( "Tipo do Documento" );
        $obLblTipoDocumento->setId     ( "stTpDocumento"     );
        $obLblTipoDocumento->setValue  ( $stTipoDocumento    );
        
        $obLblNroDocumento = new Label;
        $obLblNroDocumento->setRotulo ( "Número do Documento " );
        $obLblNroDocumento->setId     ( "inNroDocumento" );
        $obLblNroDocumento->setValue  ( $inNroDocumento  );
        
        $obLblDtDocumento = new Label;
        $obLblDtDocumento->setRotulo ( "Data "         );
        $obLblDtDocumento->setId     ( "stDtDocumento" );
        $obLblDtDocumento->setValue  ( $dtDocumento    );
        
        $obLblDescricao = new Label;
        $obLblDescricao->setRotulo ( "Descrição"   );
        $obLblDescricao->setId     ( "stDescricao" );
        $obLblDescricao->setValue  ( $stDescricao  );
        
        $obLblAutorizacao = new Label;
        $obLblAutorizacao->setRotulo ( "Autorização" );
        $obLblAutorizacao->setId     ( "stModelo"     );
        $obLblAutorizacao->setValue  (  $stAutorizacao  );
        
        $obLblModeloNF = new Label;
        $obLblModeloNF->setRotulo ( "Modelo da NF" );
        $obLblModeloNF->setId     ( "stModelo" );
        $obLblModeloNF->setValue  ( $stModelo  );
        
        $obLblNumXmlNFe = new Label;
        $obLblNumXmlNFe->setRotulo ( "Número da Chave de Acesso" );
        $obLblNumXmlNFe->setId     ( "stNumXmlNFe" );
        $obLblNumXmlNFe->setValue  ( $stNumXmlNFe  );
    break;

    // TCEPE
    case 16:
        // Define Objeto Label 
        $obLblTipoDocumento = new Label;
        $obLblTipoDocumento->setRotulo ( "Tipo do Documento" );
        $obLblTipoDocumento->setId     ( "stTpDocumento"     );
        $obLblTipoDocumento->setValue  ( $stTipoDocumento    );
        
        $obLblNroDocumento = new Label;
        $obLblNroDocumento->setRotulo ( "Número do Documento " );
        $obLblNroDocumento->setId     ( "inNroDocumento" );
        $obLblNroDocumento->setValue  ( $inNroDocumento  );
        
        $obLblCodUf = new Label;
        $obLblCodUf->setRotulo ( "UF do Documento" );
        $obLblCodUf->setId     ( "inCodUf" );
        $obLblCodUf->setValue  ( $inCodUf  );
        
        $obLblSerieDoc = new Label;
        $obLblSerieDoc->setRotulo ( "Série do Documento" );
        $obLblSerieDoc->setId     ( "stSerie" );
        $obLblSerieDoc->setValue  ( $stSerie  );
    break;

    // TCETO
    case 27:
        // Define Objeto Label 
        $obLblTipoDocumento = new Label;
        $obLblTipoDocumento->setRotulo ( "Tipo do Documento" );
        $obLblTipoDocumento->setId     ( "stTpDocumento"     );
        $obLblTipoDocumento->setValue  ( $stTipoDocumento    );
        
        $obLblNroDocumento = new Label;
        $obLblNroDocumento->setRotulo ( "Número do Documento " );
        $obLblNroDocumento->setId     ( "inNroDocumento" );
        $obLblNroDocumento->setValue  ( $inNroDocumento  );
        
        $obLblDtDocumento = new Label;
        $obLblDtDocumento->setRotulo ( "Data "         );
        $obLblDtDocumento->setId     ( "stDtDocumento" );
        $obLblDtDocumento->setValue  ( $dtDocumento    );
        
        $obLblDescricao = new Label;
        $obLblDescricao->setRotulo ( "Descrição"   );
        $obLblDescricao->setId     ( "stDescricao" );
        $obLblDescricao->setValue  ( $stDescricao  );
        
        $obLblAutorizacao = new Label;
        $obLblAutorizacao->setRotulo ( "Autorização" );
        $obLblAutorizacao->setId     ( "stModelo"     );
        $obLblAutorizacao->setValue  (  $stAutorizacao  );
        
        $obLblModeloNF = new Label;
        $obLblModeloNF->setRotulo ( "Modelo da NF" );
        $obLblModeloNF->setId     ( "stModelo" );
        $obLblModeloNF->setValue  ( $stModelo  );
    break;

    //TCM BA
    case '5':
        $obLblNumeroNF = new Label;
        $obLblNumeroNF->setName     ( 'stNumeroNF' );
        $obLblNumeroNF->setId       ( 'stNumeroNF' );
        $obLblNumeroNF->setRotulo   ( 'Número da Nota Fiscal' );
        $obLblNumeroNF->setValue    ( $stNumeroNF );

        $obLblExercicioNota = new Label();
        $obLblExercicioNota->setRotulo        ( "Ano" );
        $obLblExercicioNota->setName          ( "stAnoNotaFiscal" );
        $obLblExercicioNota->setId            ( "stAnoNotaFiscal" );
        $obLblExercicioNota->setValue         ( $stExercicioNota );

        $obLblSerieNF = new Label;
        $obLblSerieNF->setName      ( 'stSerieNF' );
        $obLblSerieNF->setId        ( 'stSerieNF' );
        $obLblSerieNF->setRotulo    ( 'Série da Nota Fiscal' );
        $obLblSerieNF->setValue     ( $stSerieNF );
        
        $obLblSubSerieNF = new Label;
        $obLblSubSerieNF->setName       ( 'stSubSerieNF' );
        $obLblSubSerieNF->setId         ( 'stSubSerieNF' );
        $obLblSubSerieNF->setRotulo     ( 'SubSérie da Nota Fiscal' );
        $obLblSubSerieNF->setValue      ( $stSubSerie );
        
        $obLblEmissaoNF = new Label;
        $obLblEmissaoNF->setName     ( 'stDtEmissaoNF' );
        $obLblEmissaoNF->setId       ( 'stDtEmissaoNF' );
        $obLblEmissaoNF->setRotulo   ( 'Data da Emissão da Nota Fiscal' );
        $obLblEmissaoNF->setValue    ( $stDataEmissaoNF );
        
        $obLblValorNota = new Label();
        $obLblValorNota->setName     ( 'nuValorNotaFiscal' );
        $obLblValorNota->setId       ( 'nuValorNotaFiscal' );
        $obLblValorNota->setRotulo   ( 'Valor da Nota Fiscal' );
        $obLblValorNota->setValue    ( $stValorNF );
        
        $obLblObjetoNF = new Label;
        $obLblObjetoNF->setName      ( 'stObjetoNF' );
        $obLblObjetoNF->setId        ( 'stObjetoNF' );
        $obLblObjetoNF->setRotulo    ( 'Descrição do objeto da Nota Fiscal' );
        $obLblObjetoNF->setValue     ( $stDescricaoNF );

        $obLblUFDocumento = new Label;
        $obLblUFDocumento->setName      ( 'stUFUnidadeFederacao' );
        $obLblUFDocumento->setId        ( 'stUFUnidadeFederacao' );
        $obLblUFDocumento->setRotulo    ( 'Unidade de Federação do documento' );
        $obLblUFDocumento->setValue     ( $stUF );        
    break;
}

// Define objeto Label para Entidade
$obLblEntidade = new Label;
$obLblEntidade->setRotulo( "Entidade" );
$obLblEntidade->setValue ( $inCodEntidade.' - '.$stNomEntidade );

//Define o objeto Label para Descrição do Empenho
$obLblNomEmpenho = new Label;
$obLblNomEmpenho->setId     ( "stNomEmpenho"         );
$obLblNomEmpenho->setRotulo ( "Descrição do Empenho" );
$obLblNomEmpenho->setValue  ( $stNomEmpenho          );

// Define Objeto Label para Tipo de Empenho
$obLblTipo = new Label;
$obLblTipo->setId     ( "inCodTipo"                 );
$obLblTipo->setValue  ( $inCodTipo.' - '.$stNomTipo );
$obLblTipo->setRotulo ( "Tipo de Empenho"           );

// Define Objeto Label para Despesa
$obLblDespesa = new Label;
$obLblDespesa->setRotulo ( "Dotação Orçamentária" );
$obLblDespesa->setId     ( "stNomDespesa"  );
$obLblDespesa->setValue  ( $inCodDespesa.' - '.$stNomDespesa );

// Define Objeto Label para Classificacao da Despesa
$obLblClassificacao = new Label;
$obLblClassificacao->setRotulo ( "Desdobramento" );
$obLblClassificacao->setId     ( "stNomClassificacao" );
$obLblClassificacao->setValue  ( $stCodClassificacao.' - '.$stNomClassificacao );

// Define Objeto Label para Saldo da Dotação
$obLblSaldoDotacao = new Label;
$obLblSaldoDotacao->setRotulo ( "Saldo Dotação" );
$obLblSaldoDotacao->setId     ( "nuSaldoDotacao" );
$obLblSaldoDotacao->setValue  ( $nuSaldoDotacao );

// Define Objeto Label para Codigo do Orgão Orçamentário
$obLblCodOrgao = new Label;
$obLblCodOrgao->setRotulo ( "Orgão Orçamentário" );
$obLblCodOrgao->setId     ( "inNumOrgao" );
$obLblCodOrgao->setValue  ( $inNumOrgao." - ".$stNomOrgao );

// Define Objeto Label para Codigo do Unidade Orçamentária
$obLblCodUnidade = new Label;
$obLblCodUnidade->setRotulo ( "Unidade Orçamentária" );
$obLblCodUnidade->setId     ( "inNumUnidade" );
$obLblCodUnidade->setValue  ( $inNumUnidade." - ".$stNomUnidade );

// Define Objeto Label para Fornecedor
$obLblFornecedor = new Label;
$obLblFornecedor->setRotulo ( "Credor" );
$obLblFornecedor->setId     ( "stNomFornecedor" );
$obLblFornecedor->setValue  ( $inCodFornecedor.' - '.$stNomFornecedor  );

// Define Objeto Label para Vencimento
$obLblDtVencimento = new Label;
$obLblDtVencimento->setRotulo ( "Data de Vencimento" );
$obLblDtVencimento->setId     ( "stDtVencimento" );
$obLblDtVencimento->setValue  ( $stDtVencimento  );

// Define Objeto Label para Histórico
$obLblHistorico = new Label;
$obLblHistorico->setRotulo    ( "Histórico"      );
$obLblHistorico->setId        ( "stNomHistorico" );
$obLblHistorico->setValue     ( $inCodHistorico.' - '.$stNomHistorico  );

// Define Objeto Label para Histórico
$obLblValorPago = new Label;
$obLblValorPago->setRotulo    ( "Valor Pago"     );
$obLblValorPago->setId        ( "flValorPago"    );
$obLblValorPago->setValue     ( $flValorPago     );

// Atributos Dinamicos
$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );
$obMontaAtributos->setLabel      ( true );

// Atributos Dinamicos - LIQUIDACAO
list( $inCodNota , $stExercicioNota ) = explode ( "/" , $stNumLiquidacao );

$arChaveAtributoLiquidacao =  array( "cod_entidade" => $inCodEntidade,
                                     "cod_nota"     => $inCodNota    ,
                                     "exercicio"    =>  $_REQUEST['stExercicioNota'] );
$obREmpenhoNotaLiquidacao->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLiquidacao       );
$obREmpenhoNotaLiquidacao->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosLiquidacao );

$obMontaAtributosLiquidacao = new MontaAtributos;
$obMontaAtributosLiquidacao->setTitulo     ( "Atributos"            );
$obMontaAtributosLiquidacao->setName       ( "Atributo_Liq"         );
$obMontaAtributosLiquidacao->setRecordSet  ( $rsAtributosLiquidacao );
$obMontaAtributosLiquidacao->setLabel      ( true                   );

// Define Objeto Span Para lista de itens
$obSpan = new Span;
$obSpan->setId( "spnLista" );

// Define Objeto Label para Valor Total dos Itens
$obLblVlTotal = new Label;
$obLblVlTotal->setId( "nuValorTotal" );
$obLblVlTotal->setRotulo( "TOTAL: " );

// Define Objeto Label para Valor Total Anulado
$obLblVlAnulado = new Label;
$obLblVlAnulado->setId( "nuVlAnulado" );
$obLblVlAnulado->setRotulo( "Valor Anulado" );

// Define Objeto Label para Valor Total Liquidado
$obLblVlLiquidado = new Label;
$obLblVlLiquidado->setId( "nuVlLiquidado" );
$obLblVlLiquidado->setRotulo( "Valor Liquidado" );

// Define objeto Label para Data do Empenho
$obLblDtEmpenho = new Label;
$obLblDtEmpenho->setId       ( "stDtEmpenho" );
$obLblDtEmpenho->setValue    ( $stDtEmpenho );
$obLblDtEmpenho->setRotulo   ( "Data de Emissão" );

//Define o objeto data para a Data validade final
$obDataValiadeFinal = new Data;
$obDataValiadeFinal->setName   ( "dtValidadeFinal" );
$obDataValiadeFinal->setRotulo ( "Data Validade Final" );

if (Sessao::read('arFiltro')) {
    $arFiltro = Sessao::read('arFiltro');
    $stLink = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        if (is_array($stValor)) {
            $stLink .= '&'.$stCampo.'='.urlencode(implode(',', $stValor));
        } else {
            $stLink .= '&'.$stCampo.'='.urlencode($stValor);
        }
    }
}

$stLocation =  CAM_GF_EMP_INSTANCIAS.'liquidacao/LSAnularLiquidacao.php?'.Sessao::getId().'&stAcao='.$stAcao.$stLink;
$obButtonVoltar = new Button;
$obButtonVoltar->setName ('Voltar');
$obButtonVoltar->setValue('Voltar');
$obButtonVoltar->obEvento->setOnClick("Cancelar('".$stLocation."','telaPrincipal');");

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados do empenho" );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCodEmpenho     );
$obFormulario->addHidden( $obHdnCodPreEmpenho  );
$obFormulario->addHidden( $obHdnCodEntidade    );
$obFormulario->addHidden( $obHdnExercicioEmpenho );

$obFormulario->addComponente( $obLblEmpenho        );
$obFormulario->addComponente( $obLblLiquidacao     );
$obFormulario->addComponente( $obLblDtLiquidacao   );
$obFormulario->addComponente( $obLblEntidade       );
$obFormulario->addComponente( $obLblDespesa        );
$obFormulario->addComponente( $obLblClassificacao  );
$obFormulario->addComponente( $obLblSaldoDotacao   );
$obFormulario->addComponente( $obLblCodOrgao       );
$obFormulario->addComponente( $obLblCodUnidade     );
$obFormulario->addComponente( $obLblFornecedor     );
$obFormulario->addComponente( $obLblNomEmpenho     );
$obFormulario->addComponente( $obLblDtVencimento   );
$obFormulario->addComponente( $obLblTipo           );
$obFormulario->addComponente( $obLblHistorico      );

switch (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio())) {
    case 11:
        $obFormulario->addTitulo( "Documento Fiscal" );
        $obFormulario->addComponente($obLblTipoDocumento      );
        $obFormulario->addComponente($obLblNumeroDocumento    );
        $obFormulario->addComponente($obLblSerieDocumento     );
        $obFormulario->addComponente($obLblDataDocumento      );
        $obFormulario->addComponente($obLblChaveAcesso        );
        $obFormulario->addComponente($obLblNumeroAIDF         );
        $obFormulario->addComponente($obLblInscricaoEstadual  );
        $obFormulario->addComponente($obLblInscricaoMunicipal );
    break;

    case 20:
        $obFormulario->addComponente( $obLblNumeroNF       );
        $obFormulario->addComponente( $obLblSerieNF        );
        $obFormulario->addComponente( $obLblDtEmissaoNF    );
        $obFormulario->addComponente( $obLblCodValidacaoNF );
        $obFormulario->addComponente( $obLblModeloNF       );
    break;

    // Apresenta Documento apenas se for uma prefeitura do Alagoas
    case 02:
        $obFormulario->addTitulo( "Documentos" );
        $obFormulario->addComponente( $obLblTipoDocumento   );
        $obFormulario->addComponente( $obLblNroDocumento    );
        $obFormulario->addComponente( $obLblDtDocumento     );
        $obFormulario->addComponente( $obLblDescricao       );
        
        if ( $inTipoDocumento == 1 or $inTipoDocumento == 6 or $inTipoDocumento == 7  ) {
            $obFormulario->addComponente( $obLblAutorizacao );
            $obFormulario->addComponente( $obLblModeloNF    );
            
            if( $inTipoDocumento == 6 ) {
                $obFormulario->addComponente($obLblNumXmlNFe);
            }
        }
        
        if ( $inTipoDocumento == 9 ) {
            $obFormulario->addComponente( $obLblAutorizacao );
            $obFormulario->addComponente( $obLblModeloNF    ); 
        }
    break;

    // Apresenta Documento apenas se for uma prefeitura do Pernambuco
    case 16:
        $obFormulario->addTitulo( "Documentos" );
        $obFormulario->addComponente( $obLblTipoDocumento   );
        $obFormulario->addComponente( $obLblNroDocumento    );
        
        if ( $inTipoDocumento == 1 or $inTipoDocumento == 9 ) {
            $obFormulario->addComponente( $obLblSerieDoc    );
            $obFormulario->addComponente( $obLblCodUf       );
        }
    break;

    // prefeitura Tocantins (TCETO)
    case 27:
        $obFormulario->addTitulo( "Documentos" );
        $obFormulario->addComponente( $obLblTipoDocumento   );
        $obFormulario->addComponente( $obLblNroDocumento    );
        $obFormulario->addComponente( $obLblDtDocumento     );
        $obFormulario->addComponente( $obLblDescricao       );
        
        if ( $inTipoDocumento == 1 or $inTipoDocumento == 6 or $inTipoDocumento == 7  ) {
            $obFormulario->addComponente( $obLblAutorizacao );
            $obFormulario->addComponente( $obLblModeloNF    );
        }
        
        if ( $inTipoDocumento == 9 ) {
            $obFormulario->addComponente( $obLblAutorizacao );
            $obFormulario->addComponente( $obLblModeloNF    ); 
        }
    break;
	//TCM BA
    case 5:
        $obFormulario->addTitulo( "Nota Fiscal" );
        $obFormulario->addComponente( $obLblNumeroNF );
        $obFormulario->addComponente( $obLblExercicioNota );
        $obFormulario->addComponente( $obLblSerieNF );
        $obFormulario->addComponente( $obLblSubSerieNF );
        $obFormulario->addComponente( $obLblEmissaoNF );
        $obFormulario->addComponente( $obLblValorNota );
        $obFormulario->addComponente( $obLblObjetoNF );
        $obFormulario->addComponente( $obLblUFDocumento );
    break;
}

//Tipo de Documento
if (strtolower(SistemaLegado::pegaConfiguracao( 'seta_tipo_documento_liq_tceam',30, Sessao::getExercicio()))=='true') {
    $obFormulario->addTitulo( "Tipo de Documento" );

    include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMDocumento.class.php';

    $obTTCEAMDocumento = new TTCEAMDocumento();

    $stFiltro .= " WHERE documento.cod_entidade = " .$_REQUEST['inCodEntidade'];
    $stFiltro .= " AND   documento.exercicio    = '".$_REQUEST['stExercicioNota']."'";
    $stFiltro .= " AND   documento.cod_nota     = " .$_REQUEST['inCodNota'];

    $obTTCEAMDocumento->recuperaTodos( $rsTipoDocumento, $stFiltro );
    $obTTCEAMDocumento->setDado( 'cod_tipo', $rsTipoDocumento->getCampo('cod_tipo'));

    $obTTCEAMDocumento->recuperaRelacionamento( $rsTipoDocumento, $stFiltro);

    //label para tipo documento
    $obLblTipoDocumento = new Label();
    $obLblTipoDocumento->setRotulo( 'Tipo de Documento' );
    $obLblTipoDocumento->setValue( $rsTipoDocumento->getCampo( 'cod_tipo' ).'-'.$rsTipoDocumento->getCampo( 'descricao_tipo' ) );

    $obFormulario->addComponente( $obLblTipoDocumento );

    if ($rsTipoDocumento->getCampo('cod_tipo') == 1) { //bilhete

        //label para numero
        $obLblNumero = new Label();
        $obLblNumero->setRotulo( 'Número' );
        $obLblNumero->setValue( $rsTipoDocumento->getCampo( 'numero' ) );

        //label para data emissao
        $obLblDataEmissao = new Label();
        $obLblDataEmissao->setRotulo( 'Data de Emissão' );
        if ($rsTipoDocumento->getCampo('dt_emissao')<>'') {
            $obLblDataEmissao->setValue( date('d/m/Y',strtotime($rsTipoDocumento->getCampo('dt_emissao'))));
        } else {
            $obLblDataEmissao->setValue('');
        }

        //label para data de saida
        $obLblDataSaida = new Label();
        $obLblDataSaida->setRotulo( 'Data de Saída' );
        if ($rsTipoDocumento->getCampo('dt_saida')<>'') {
            $obLblDataSaida->setValue( date('d/m/Y',strtotime($rsTipoDocumento->getCampo('dt_saida'))));
        } else {
            $obLblDataSaida->setValue('');
        }

        //label para hora saida
        $obLblHoraSaida = new Label();
        $obLblHoraSaida->setRotulo( 'Hora de Saída' );
        $obLblHoraSaida->setValue( $rsTipoDocumento->getCampo( 'hora_saida' ) );

        //label para destino
        $obLblDestino = new Label();
        $obLblDestino->setRotulo( 'Destino' );
        $obLblDestino->setValue( $rsTipoDocumento->getCampo( 'destino' ) );

        //label para data de chegada
        $obLblDataChegada = new Label();
        $obLblDataChegada->setRotulo( 'Data de Chegada' );
        if ($rsTipoDocumento->getCampo('dt_chegada')<>'') {
            $obLblDataChegada->setValue( date('d/m/Y',strtotime($rsTipoDocumento->getCampo('dt_chegada'))));
        } else {
            $obLblDataChegada->setValue('');
        }

        //label para hora chegada
        $obLblHoraChegada = new Label();
        $obLblHoraChegada->setRotulo( 'Hora de Chegada' );
        $obLblHoraChegada->setValue( $rsTipoDocumento->getCampo( 'hora_chegada' ) );

        //label para motivo
        $obLblMotivo = new Label();
        $obLblMotivo->setRotulo( 'Motivo' );
        $obLblMotivo->setValue( $rsTipoDocumento->getCampo( 'motivo' ) );

        $obFormulario->addComponente( $obLblNumero );
        $obFormulario->addComponente( $obLblDataEmissao );
        $obFormulario->addComponente( $obLblDataSaida );
        $obFormulario->addComponente( $obLblHoraSaida );
        $obFormulario->addComponente( $obLblDestino );
        $obFormulario->addComponente( $obLblDataChegada );
        $obFormulario->addComponente( $obLblHoraChegada );
        $obFormulario->addComponente( $obLblMotivo );

    } elseif ($rsTipoDocumento->getCampo('cod_tipo') == 2) { //diaria

        //label para funcionario
        $obLblFuncionario = new Label();
        $obLblFuncionario->setRotulo( 'Funcionário' );
        $obLblFuncionario->setValue( $rsTipoDocumento->getCampo( 'funcionario' ) );

        //label para matricula
        $obLblMatricula = new Label();
        $obLblMatricula->setRotulo( 'Matrícula' );
        $obLblMatricula->setValue( $rsTipoDocumento->getCampo( 'matricula' ) );

        //label para data de saida
        $obLblDataSaida = new Label();
        $obLblDataSaida->setRotulo( 'Data de Saída' );
        if ($rsTipoDocumento->getCampo('dt_saida')<>'') {
            $obLblDataSaida->setValue( date('d/m/Y',strtotime($rsTipoDocumento->getCampo('dt_saida'))));
        } else {
            $obLblDataSaida->setValue('');
        }

        //label para hora saida
        $obLblHoraSaida = new Label();
        $obLblHoraSaida->setRotulo( 'Hora de Saída' );
        $obLblHoraSaida->setValue( $rsTipoDocumento->getCampo( 'hora_saida' ) );

        //label para destino
        $obLblDestino = new Label();
        $obLblDestino->setRotulo( 'Destino' );
        $obLblDestino->setValue( $rsTipoDocumento->getCampo( 'destino' ) );

        //label para data de retorno
        $obLblDataRetorno = new Label();
        $obLblDataRetorno->setRotulo( 'Data de Retorno' );
        if ($rsTipoDocumento->getCampo('dt_retorno')<>'') {
            $obLblDataRetorno->setValue( date('d/m/Y',strtotime($rsTipoDocumento->getCampo('dt_retorno'))));
        } else {
            $obLblDataRetorno->setValue('');
        }

        //label para hora retorno
        $obLblHoraRetorno = new Label();
        $obLblHoraRetorno->setRotulo( 'Hora de Retorno' );
        $obLblHoraRetorno->setValue( $rsTipoDocumento->getCampo( 'hora_retorno' ) );

        //label para motivo
        $obLblMotivo = new Label();
        $obLblMotivo->setRotulo( 'Motivo' );
        $obLblMotivo->setValue( $rsTipoDocumento->getCampo( 'motivo' ) );

        //label para quantidade
        $obLblQuantidade = new Label();
        $obLblQuantidade->setRotulo( 'Quantidade' );
        $obLblQuantidade->setValue( $rsTipoDocumento->getCampo( 'quantidade' ) );

        $obFormulario->addComponente( $obLblFuncionario );
        $obFormulario->addComponente( $obLblMatricula );
        $obFormulario->addComponente( $obLblDataSaida );
        $obFormulario->addComponente( $obLblHoraSaida );
        $obFormulario->addComponente( $obLblDestino );
        $obFormulario->addComponente( $obLblDataRetorno );
        $obFormulario->addComponente( $obLblHoraRetorno );
        $obFormulario->addComponente( $obLblMotivo );
        $obFormulario->addComponente( $obLblQuantidade );

    } elseif ($rsTipoDocumento->getCampo('cod_tipo') == 3) { //diverso

        //label para numero
        $obLblNumero = new Label();
        $obLblNumero->setRotulo( 'Número' );
        $obLblNumero->setValue( $rsTipoDocumento->getCampo( 'numero' ) );

        //label para data
        $obLblData = new Label();
        $obLblData->setRotulo( 'Data' );
        if ($rsTipoDocumento->getCampo('data')<>'') {
            $obLblData->setValue( date('d/m/Y',strtotime($rsTipoDocumento->getCampo('data'))));
        } else {
            $obLblData->setValue('');
        }

        //label para descricao
        $obLblDescricao = new Label();
        $obLblDescricao->setRotulo( 'Descrição' );
        $obLblDescricao->setValue( $rsTipoDocumento->getCampo( 'descricao' ) );

        //label para nome documento
        $obLblNomeDocumento = new Label();
        $obLblNomeDocumento->setRotulo( 'Nome Documento' );
        $obLblNomeDocumento->setValue( $rsTipoDocumento->getCampo( 'nome_documento' ) );

        $obFormulario->addComponente( $obLblNumero );
        $obFormulario->addComponente( $obLblData );
        $obFormulario->addComponente( $obLblDescricao );
        $obFormulario->addComponente( $obLblNomeDocumento );

    } elseif ($rsTipoDocumento->getCampo('cod_tipo') == 4) { //folha

        //label para mes
        $obLblMes = new Label();
        $obLblMes->setRotulo( 'Mês' );
        $obLblMes->setValue( $rsTipoDocumento->getCampo( 'mes' ) );

        //label para numero serie
        $obLblExercicio = new Label();
        $obLblExercicio->setRotulo( 'Exercício' );
        $obLblExercicio->setValue( $rsTipoDocumento->getCampo( 'exercicio' ) );

        $obFormulario->addComponente( $obLblMes );
        $obFormulario->addComponente( $obLblExercicio );

    } elseif ($rsTipoDocumento->getCampo('cod_tipo') == 5) { //nota
        //label para numero nota fiscal
        $obLblNumeroNotaFiscal = new Label();
        $obLblNumeroNotaFiscal->setRotulo( 'Número Nota Fiscal' );
        $obLblNumeroNotaFiscal->setValue( $rsTipoDocumento->getCampo( 'numero_nota_fiscal' ) );

        //label para numero serie
        $obLblNumeroSerie = new Label();
        $obLblNumeroSerie->setRotulo( 'Número Série' );
        $obLblNumeroSerie->setValue( $rsTipoDocumento->getCampo( 'numero_serie' ) );

        //label para numero subserie
        $obLblNumeroSubserie = new Label();
        $obLblNumeroSubserie->setRotulo( 'Número Subsérie' );
        $obLblNumeroSubserie->setValue( $rsTipoDocumento->getCampo( 'numero_subserie' ) );

        //label para data
        $obLblDataNF = new Label();
        $obLblDataNF->setRotulo( 'Data' );
        if ($rsTipoDocumento->getCampo('dt_retorno')<>'') {
            $obLblDataNF->setValue( date('d/m/Y',strtotime($rsTipoDocumento->getCampo('data'))));
        } else {
            $obLblDataNF->setValue('');
        }

        $obFormulario->addComponente( $obLblNumeroNotaFiscal );
        $obFormulario->addComponente( $obLblNumeroSerie );
        $obFormulario->addComponente( $obLblNumeroSubserie );
        $obFormulario->addComponente( $obLblDataNF );

    } elseif ($rsTipoDocumento->getCampo('cod_tipo') == 6) { //recibo

        //label para tipo recibo
        $obLblTipoRecibo = new Label();
        $obLblTipoRecibo->setRotulo( 'Tipo do Recibo' );
        $obLblTipoRecibo->setValue( $rsTipoDocumento->getCampo( 'cod_tipo_recibo' ).'-'.$rsTipoDocumento->getCampo( 'descricao' ) );

        //label para numero
        $obLblNumero = new Label();
        $obLblNumero->setRotulo( 'Número' );
        $obLblNumero->setValue( $rsTipoDocumento->getCampo( 'numero' ) );

        //label para valor
        $obLblValor = new Label();
        $obLblValor->setRotulo( 'Valor' );
        $obLblValor->setValue( number_format($rsTipoDocumento->getCampo('valor'),2,',','.') );

        //label para data
        $obLblData = new Label();
        $obLblData->setRotulo( 'Data' );
        if ($rsTipoDocumento->getCampo('dt_retorno')<>'') {
            $obLblData->setValue( date('d/m/Y',strtotime($rsTipoDocumento->getCampo('data'))));
        } else {
            $obLblData->setValue('');
        }

        $obFormulario->addComponente( $obLblTipoRecibo );
        $obFormulario->addComponente( $obLblNumero );
        $obFormulario->addComponente( $obLblValor );
        $obFormulario->addComponente( $obLblData );

    }
    //label para valor comprometido
    $obLblValorComprometido = new Label();
    $obLblValorComprometido->setRotulo( 'Valor Comprometido' );
    $obLblValorComprometido->setValue( number_format($rsTipoDocumento->getCampo('vl_comprometido'),2,',','.') );

    //label para valor total
    $obLblValorTotal = new Label();
    $obLblValorTotal->setRotulo( 'Valor Total' );
    $obLblValorTotal->setValue( number_format($rsTipoDocumento->getCampo('vl_total'),2,',','.') );

    $obFormulario->addComponente( $obLblValorComprometido );
    $obFormulario->addComponente( $obLblValorTotal );

}

$obMontaAtributos->geraFormulario ( $obFormulario );

$obFormulario->addSpan( $obSpan );
$obMontaAtributosLiquidacao->geraFormulario ( $obFormulario );

$obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>