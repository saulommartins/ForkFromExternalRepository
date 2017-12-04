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
    * @author Desenvolvedor Anderson R. M. Buzo

    * @ignore

    $Revision: 31087 $
    $Name:  $
    $Author: eduardoschitz $
    $Date: 2008-02-12 08:57:23 -0200 (Ter, 12 Fev 2008) $

    * Casos de uso: uc-02.03.18
                    uc-02.03.04

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php" );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoNotaLiquidacao.class.php"     );
include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacaoPagaAnulada.class.php"     );
include_once ( CAM_FW_HTML."MontaAtributos.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "AnularLiquidacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}
$inCodNota = $_REQUEST['inCodNota'];
$inCodEmpenho = $_REQUEST['inCodEmpenho'];
$inCodPreEmpenho = $_REQUEST['inCodPreEmpenho'];
$inCodEntidade = $_REQUEST['inCodEntidade'];

$stFiltroEntidade = " AND entidade.cod_entidade = ( SELECT valor
                            FROM administracao.configuracao
                           WHERE cod_modulo = 8
                             AND parametro='cod_entidade_prefeitura'
                             AND exercicio = '".Sessao::getExercicio()."' )::integer
                  AND entidade.exercicio = '".Sessao::getExercicio()."' ";

$obTEntidade = new TEntidade;
$obTEntidade->recuperaEntidades( $rsRecordSetEntidade, $stFiltroEntidade, '', $boTransacao );

$obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;

//$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicio( Sessao::getExercicio() );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setExercicio( $_REQUEST['dtExercicioEmpenho']  );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->listar( $rsTipo );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoHistorico->listar( $rsHistorico );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listarUnidadeMedida( $rsUnidade );

//$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio( $_REQUEST['dtExercicioEmpenho'] );
$stMascaraRubrica = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();

Sessao::remove('arItens');

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodEmpenho( $_REQUEST['inCodEmpenho'] );
//$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicio( Sessao::getExercicio() );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicio( $_REQUEST['dtExercicioEmpenho'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setCodAutorizacao( $_REQUEST['inCodAutorizacao'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodPreEmpenho( $_REQUEST['inCodPreEmpenho'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
if ($_REQUEST['boImplantado'] == 't') {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->consultarRestosAPagar();
} else {
    $obREmpenhoEmpenhoAutorizacao->consultar();
}
//$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->consultar();
$nuSaldoDotacao = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getVlSaldoAnterior();

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodLiquidacaoInicial( $_REQUEST["inCodNota"] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->consultarValorNotaItem();

$inCodCategoria = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodCategoria();
if ($inCodCategoria == '2' || $inCodCategoria == '3') {
    $boAdiantamento = true;
}

$stNomEmpenho       = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getDescricao();
$stNomEntidade      = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->getNomCGM();
$inCodTipo          = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->getCodTipo();
$stNomTipo          = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->getNomTipo();
$inNumUnidade       = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade();
$stNomUnidade       = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNomUnidade();
$inNumOrgao         = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
$stNomOrgao         = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNomeOrgao();
$inCodDespesa       = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->getCodDespesa();
$stNomDespesa       = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getDescricao();
$stCodClassificacao = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->getMascClassificacao();
$stNomClassificacao = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->getDescricao();
$inCodFornecedor    = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obRCGM->getNumCGM();
$stNomFornecedor    = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obRCGM->getnomCGM();
$stDtLiquidacao     = $_REQUEST["stDtLiquidacao"];
$stDtVencimento     = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getDtVencimento();
$inCodHistorico     = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoHistorico->getCodHistorico();

 $stNomHistorico     = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoHistorico->getNomHistorico();
$stDtEmpenho        = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getDtEmpenho();
$arItemPreEmpenho = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getItemPreEmpenho();

$obREmpenhoNotaLiquidacao = new REmpenhoNotaLiquidacao( $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho );
$obREmpenhoNotaLiquidacao->setCodNota   ( $_REQUEST["inCodNota"] );
$obREmpenhoNotaLiquidacao->setExercicio ( $_REQUEST["stExercicioNota"] );
$obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodEntidade"] );
$obREmpenhoNotaLiquidacao->listarItensAnulacao( $rsRecordSet );

Sessao::write('arItens', $rsRecordSet->getElementos());

SistemaLegado::executaFramePrincipal("buscaDado('montaListaItemPreEmpenho');");
$arChaveAtributo =  array( "cod_pre_empenho" => $_REQUEST["inCodPreEmpenho"],
                           "exercicio"       => $_REQUEST['dtExercicioEmpenho']  );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos     );

if ($_REQUEST['boImplantado'] != 't') {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->consultaSaldoAnterior( $nuSaldoDotacao );
    $stDespesa = $inCodDespesa.' - '.$stNomDespesa;
    $stOrgao   = $inNumOrgao.' - '.$stNomOrgao;
    $stUnidade = $inNumUnidade.' - '.$stNomUnidade;
} else {
    $stDespesa = $inCodDespesa;
    $stOrgao   = $inNumOrgao;
    $stUnidade = $inNumUnidade;
}
$nuSaldoDotacao = number_format( $nuSaldoDotacao, 2, ',', '.');

$obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($inCodEntidade);
$obREmpenhoNotaLiquidacao->setDtLiquidacao($stDtLiquidacao);
$obREmpenhoNotaLiquidacao->setExercicio( Sessao::getExercicio() );
$obREmpenhoNotaLiquidacao->listarMaiorDataAnulacao( $rsMaiorData );

$stDtEstorno = $rsMaiorData->getCampo( "data_anulacao" );

$obTEmpenhoNotaLiquidacaoPagaAnulada = new TEmpenhoNotaLiquidacaoPagaAnulada;

$obTEmpenhoNotaLiquidacaoPagaAnulada->setDado('cod_empenho' , $_REQUEST['inCodEmpenho']);
$obTEmpenhoNotaLiquidacaoPagaAnulada->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
$obTEmpenhoNotaLiquidacaoPagaAnulada->setDado('exercicio'   , $_REQUEST['dtExercicioEmpenho']);

$obTEmpenhoNotaLiquidacaoPagaAnulada->recuperaRelacionamentoManutencaoDatas($rsAnulados);

$stDtUltimoEstornoLiquidacao = $stDtEstorno;
$stDtEstornoCampo = '';
while (!$rsAnulados->eof()) {
    if ($rsAnulados->getCampo('timestamp') != '') {
        $stDtAnulacao = $rsAnulados->getCampo('timestamp');

        $stDtAnulacaoTMP = implode('',array_reverse(explode('/',$stDtAnulacao)));
        $stDtEstornoTMP = implode('',array_reverse(explode('/',$stDtEstorno)));
        $stDtEstornoCampo = $stDtAnulacao;
        if ($stDtAnulacaoTMP > $stDtEstornoTMP) {
            $stDtEstorno = $stDtAnulacao;
        }
    }
    $rsAnulados->proximo();
}

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

// Define objeto Hidden para boAdiatamento
$obHdnAdiantamento = new Hidden;
$obHdnAdiantamento->setName ( "boAdiantamento" );
$obHdnAdiantamento->setValue( $boAdiantamento  );

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

// Define Objeto Hidden para Codigo do Orgão Orçamentário
$obHdnNumOrgao = new Hidden;
$obHdnNumOrgao->setName   ( "inNumOrgao" );
$obHdnNumOrgao->setValue  ( $inNumOrgao  );

// Define Objeto Hidden para Codigo da Despesa
$obHdnCodDespesa = new Hidden;
$obHdnCodDespesa->setName   ( "inCodDespesa" );
$obHdnCodDespesa->setValue  ( $inCodDespesa  );

// Define objeto Hidden para Codigo da Nota
$obHdnCodNota = new Hidden;
$obHdnCodNota->setName ( "inCodNota" );
$obHdnCodNota->setValue( $_REQUEST["inCodNota"] );

// Define objeto Hidden para Exercicio Nota
$obHdnExercicioNota = new Hidden;
$obHdnExercicioNota->setName ( "dtExercicioNota" );
$obHdnExercicioNota->setValue( $_REQUEST["stExercicioNota"] );

// Define objeto Hidden para data de liquidação
$obHdnDtLiquidacao = new Hidden;
$obHdnDtLiquidacao->setName ( "stDtLiquidacao" );
$obHdnDtLiquidacao->setValue( $stDtLiquidacao );

// Define objeto Hidden para Exercicio Nota
$obHdnDesdobramento = new Hidden;
$obHdnDesdobramento->setName ( "inCodContaContabilFinanc" );
$obHdnDesdobramento->setValue( $stCodClassificacao );

// Define objeto Hidden para a data da OP se já houve
$obHdnDtAnulacao = new Hidden;
$obHdnDtAnulacao->setName ( "stDtAnulacaoOP" );
$obHdnDtAnulacao->setId   ( "stDtAnulacaoOP" );
$obHdnDtAnulacao->setValue( $stDtEstornoCampo );

// Se o exercício logado é diferente do corrente, a data limite para anulação é 31/12
if (date('Y') > Sessao::getExercicio()) {
    $obHdnExercicioLimite = new Hidden;
    $obHdnExercicioLimite->setName('stExercicioLimite');
    $obHdnExercicioLimite->setId('stExercicioLimite');
    $obHdnExercicioLimite->setValue(Sessao::getExercicio());
}

// Define objeto Hidden para a data da OP se já houve
$obHdnDtUltimaAnulacao = new Hidden;
$obHdnDtUltimaAnulacao->setName ( "stDtUltimaAnulacao" );
$obHdnDtUltimaAnulacao->setId   ( "stDtUltimaAnulacao" );
$obHdnDtUltimaAnulacao->setValue( $stDtUltimoEstornoLiquidacao );

// Define objeto Label para Empenho
$obLblEmpenho = new Label;
$obLblEmpenho->setRotulo( "N° Empenho" );
$obLblEmpenho->setValue ( $inCodEmpenho );

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
$obLblDespesa->setValue  ( $stDespesa );

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
$obLblCodOrgao->setId     ( "inCodOrgao" );
$obLblCodOrgao->setValue  ( $stOrgao );

// Define Objeto Label para Codigo do Unidade Orçamentária
$obLblCodUnidade = new Label;
$obLblCodUnidade->setRotulo ( "Unidade Orçamentária" );
$obLblCodUnidade->setId     ( "inCodUnidade" );
$obLblCodUnidade->setValue  ( $stUnidade );

// Define Objeto Label para Fornecedor
$obLblFornecedor = new Label;
$obLblFornecedor->setRotulo ( "Credor" );
$obLblFornecedor->setId     ( "stNomFornecedor" );
$obLblFornecedor->setValue  ( $inCodFornecedor.' - '.$stNomFornecedor  );

// Define Objeto Label para Vencimento
$obLblDtLiquidacao = new Label;
$obLblDtLiquidacao->setRotulo ( "Data de Liquidação" );
$obLblDtLiquidacao->setId     ( "stDtLiquidacao" );
$obLblDtLiquidacao->setValue  ( $stDtLiquidacao  );

// Define objeto Data para validade final
$obDtEstorno = new Data;
$obDtEstorno->setName     ( "stDtEstorno" );
$obDtEstorno->setValue    ( $stDtEstorno );
$obDtEstorno->setRotulo   ( "Data de Anulação");
$obDtEstorno->setTitle    ( 'Informe a data de anulação de liquidação' );
$obDtEstorno->setNull     ( false );
$obDtEstorno->obEvento->setOnBlur( "validaDataEstorno();" );

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

// Atributos Dinamicos
$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );
$obMontaAtributos->setLabel      ( true );

//// Atributos Dinamicos - LIQUIDACAO
//$arChaveAtributoLiquidacao =  array( "cod_entidade" => $inCodEntidade,
//                                     "cod_nota"     => $inCodNota    ,
//                                     "exercicio"    =>  $stExercicioNota );
//$obREmpenhoNotaLiquidacao->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLiquidacao       );
//$obREmpenhoNotaLiquidacao->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosLiquidacao );
//
//$obMontaAtributosLiquidacao = new MontaAtributos;
//$obMontaAtributosLiquidacao->setTitulo     ( "Atributos"            );
//$obMontaAtributosLiquidacao->setName       ( "Atributo_Liq"         );
//$obMontaAtributosLiquidacao->setRecordSet  ( $rsAtributosLiquidacao );
//$obMontaAtributosLiquidacao->setLabel      ( true                   );

// Define Objeto ILabel para Conta Debito e Crédito de Incorporação Patrimonial

include_once ( CAM_GF_CONT_COMPONENTES . "ILabelContaAnalitica.class.php" );
$obREmpenhoNotaLiquidacao->recuperaContasIncorporacaoPatrimonial();

if ( strlen($obREmpenhoNotaLiquidacao->obRContabilidadePlanoContaAnaliticaDebito->getCodPlano()) > 0 ) {

    // Define Objeto TextBox para Codigo do Historico Padrao
    $obBscHistorico = new BuscaInner;
    $obBscHistorico->setRotulo ( "Histórico" );
    $obBscHistorico->setTitle ( "Informe o histórico." );
    $obBscHistorico->setNull ( true );
    $obBscHistorico->setId ( "stNomHistoricoPatrimon" );
    //$obBscHistorico->setValue( $stNomHistorico );
    $obBscHistorico->obCampoCod->setName ( "inCodHistoricoPatrimon" );
    $obBscHistorico->obCampoCod->setSize ( 10 );
    $obBscHistorico->obCampoCod->setMaxLength( 5 );
    //$obBscHistorico->obCampoCod->setValue ( $inCodHistoricoPatrimon );
    $obBscHistorico->obCampoCod->setAlign ("left");
    $obBscHistorico->obCampoCod->obEvento->setOnBlur("buscaDado('buscaHistorico');");
    $obBscHistorico->setFuncaoBusca("abrePopUp('".CAM_GF_EMP_POPUPS."historicoPadrao/FLHistoricoPadrao.php','frm','inCodHistoricoPatrimon','stNomHistoricoPatrimon','','".Sessao::getId()."','800','550');");

    // Define Objeto TextBox para Complemento
    $obTxtComplemento = new TextArea;
    $obTxtComplemento->setName   ( "stComplemento" );
    $obTxtComplemento->setId     ( "stComplemento" );
    $obTxtComplemento->setValue  ( $stComplemento  );
    $obTxtComplemento->setRotulo ( "Complemento" );
    $obTxtComplemento->setTitle  ( "Informe o complemento." );
    $obTxtComplemento->setNull   ( true );
    $obTxtComplemento->setRows   ( 3 );

    // Conta Débito
    $obILabelContaAnaliticaDeb = new ILabelContaAnalitica($obFormulario);
    $obILabelContaAnaliticaDeb->setRotulo('Conta Débito');
    $obILabelContaAnaliticaDeb->setName ("stContaAnaliticaDeb" );
    $obILabelContaAnaliticaDeb->setId ("stContaAnaliticaDeb" );
    $obILabelContaAnaliticaDeb->setCodPlano( $obREmpenhoNotaLiquidacao->obRContabilidadePlanoContaAnaliticaDebito->getCodPlano() );
    $obILabelContaAnaliticaDeb->setMostraCodigo( true );
    // Conta Crédito
    $obILabelContaAnaliticaCred = new ILabelContaAnalitica($obFormulario);
    $obILabelContaAnaliticaCred->setRotulo('Conta Crédito');
    $obILabelContaAnaliticaCred->setName ("stContaAnaliticaCred" );
    $obILabelContaAnaliticaCred->setId ("stContaAnaliticaCred" );
    $obILabelContaAnaliticaCred->setCodPlano( $obREmpenhoNotaLiquidacao->obRContabilidadePlanoContaAnaliticaCredito->getCodPlano() );
    $obILabelContaAnaliticaCred->setMostraCodigo( true );
    // Código de Histórico Patrimonial passa a ser obrigatório
    $obBscHistorico->setNull ( false );
}

// Define Objeto Span Para lista de itens
$obSpan = new Span;
$obSpan->setId( "spnLista" );

//// Define Objeto Label para Valor Total dos Itens
//$obLblVlTotal = new Label;
//$obLblVlTotal->setId( "nuValorTotal" );
//$obLblVlTotal->setRotulo( "TOTAL: " );

$obHdnValorTotal = new Hidden;
$obHdnValorTotal->setName ("nuValorTotal");
$obHdnValorTotal->setId   ("nuValorTotal");
$obHdnValorTotal->setValue("");

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

$obBtnOk = new Ok();
$obBtnOk->setId( 'Ok' );
$obBtnOk->obEvento->setOnCLick('if(Valida())BloqueiaFrames(true,true);Salvar();');

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
$obBtnCancelar = new Cancelar();
//$obBtnCancelar->obEvento->setOnClick("Cancelar('".$stLocation."','telaPrincipal');");
$obBtnCancelar->obEvento->setOnClick("Cancelar();");

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados do empenho" );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCodEmpenho     );
$obFormulario->addHidden( $obHdnAdiantamento   );
$obFormulario->addHidden( $obHdnCodPreEmpenho  );
$obFormulario->addHidden( $obHdnCodEntidade    );
$obFormulario->addHidden( $obHdnExercicioEmpenho );
$obFormulario->addHidden( $obHdnCodNota          );
$obFormulario->addHidden( $obHdnDtLiquidacao     );
$obFormulario->addHidden( $obHdnExercicioNota     );
$obFormulario->addHidden( $obHdnNumOrgao          );
$obFormulario->addHidden( $obHdnCodDespesa        );
$obFormulario->addHidden( $obHdnDesdobramento     );
$obFormulario->addHidden( $obHdnDtAnulacao        );
$obFormulario->addHidden( $obHdnDtUltimaAnulacao  );
$obFormulario->addHidden( $obHdnValorTotal );
if (date('Y') > Sessao::getExercicio()) {
    $obFormulario->addHidden($obHdnExercicioLimite);
}
$obFormulario->addComponente( $obLblEmpenho       );
$obFormulario->addComponente( $obLblEntidade      );
$obFormulario->addComponente( $obLblDespesa       );
$obFormulario->addComponente( $obLblClassificacao );
$obFormulario->addComponente( $obLblSaldoDotacao  );
$obFormulario->addComponente( $obLblCodOrgao      );
$obFormulario->addComponente( $obLblCodUnidade    );
$obFormulario->addComponente( $obLblFornecedor    );
$obFormulario->addComponente( $obLblNomEmpenho    );
$obFormulario->addComponente( $obLblDtEmpenho     );
$obFormulario->addComponente( $obLblDtLiquidacao  );
$obFormulario->addComponente( $obDtEstorno        );
$obFormulario->addComponente( $obLblDtVencimento  );
$obFormulario->addComponente( $obLblTipo          );
$obFormulario->addComponente( $obLblHistorico     );

$obMontaAtributos->geraFormulario ( $obFormulario );

if ( strlen($obREmpenhoNotaLiquidacao->obRContabilidadePlanoContaAnaliticaDebito->getCodPlano()) > 0 && Sessao::getExercicio() > '2012') {
    $obFormulario->addTitulo( "Dados dos lançamentos contábeis" );
    $obFormulario->addTitulo( "Incorporação Patrimonial/Amortização" );
    $obFormulario->addComponente( $obILabelContaAnaliticaDeb );
    $obFormulario->addComponente( $obILabelContaAnaliticaCred );
    $obFormulario->addComponente( $obBscHistorico );
    $obFormulario->addComponente( $obTxtComplemento );
}

$obFormulario->addTitulo( "Itens do empenho" );
$obFormulario->addSpan( $obSpan );

//$obMontaAtributosLiquidacao->geraFormulario ( $obFormulario );

//$obFormulario->Cancelar( $stLocation );
$obFormulario->defineBarra( array($obBtnOk, $obBtnCancelar) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
