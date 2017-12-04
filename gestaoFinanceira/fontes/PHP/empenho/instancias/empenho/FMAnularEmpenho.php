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

    $Revision: 32186 $
    $Name$
    $Autor: $
    $Date: 2008-01-04 12:57:12 -0200 (Sex, 04 Jan 2008) $

    * Casos de uso: uc-02.03.03
                    uc-02.03.15
*/

/*
$Log$
Revision 1.12  2007/03/09 20:46:29  luciano
#8379#

Revision 1.11  2006/07/17 12:56:44  jose.eduardo
Bug #6546#

Revision 1.10  2006/07/05 20:48:34  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php" );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoNotaLiquidacao.class.php" );
include_once ( CAM_FW_HTML."MontaAtributos.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "AnularEmpenho";
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
    $stAcao = "anular";
}

$obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicio( $_REQUEST['stExercicioEmpenho'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->listarEntidadeRestos( $rsEntidade );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->listar( $rsTipo );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoHistorico->listar( $rsHistorico );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listarUnidadeMedida( $rsUnidade );

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
$stMascaraRubrica = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();

Sessao::remove('arItens');
Sessao::remove('inRestos');

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodEmpenho( $_REQUEST['inCodEmpenho'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setCodAutorizacao( $_REQUEST['inCodAutorizacao'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodPreEmpenho( $_REQUEST['inCodPreEmpenho'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
if ($_REQUEST['boImplantado'] =='t') {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->consultarRestosAPagar();
} else {
    $obREmpenhoEmpenhoAutorizacao->consultar();
}
$stDespesa = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->getCodDespesa();
//$obREmpenhoEmpenhoAutorizacao->consultar();
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->consultarValorItem();

$stNomEmpenho       = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getDescricao();
$inCodEntidade      = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade();
$stNomEntidade      = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->getNomCGM();
$inCodTipo          = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->getCodTipo();
$stNomTipo          = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->getNomTipo();
$inNumUnidade = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade();
$stNomUnidade = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNomUnidade();
$inNumOrgao   = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
$stNomOrgao   = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNomeOrgao();
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
$arItens = array();

foreach ($arItemPreEmpenho as $inCount => $obItemPreEmpenho) {
    $nuVlLiquidado = $obItemPreEmpenho->getValorLiquidado() - $obItemPreEmpenho->getValorLiquidadoAnulado();
    $nuVlAAnular   = $obItemPreEmpenho->getValorTotal() - ( $obItemPreEmpenho->getValorEmpenhadoAnulado() + $nuVlLiquidado );
    $arItens[$inCount]['num_item']     = $obItemPreEmpenho->getNumItem();
    if($obItemPreEmpenho->getCodItemPreEmp()!='')
        $arItens[$inCount]['nom_item'] = $obItemPreEmpenho->getCodItemPreEmp()." - ".stripslashes($obItemPreEmpenho->getNomItem());
    else    
        $arItens[$inCount]['nom_item'] = stripslashes($obItemPreEmpenho->getNomItem());
    $arItens[$inCount]['complemento']  = $obItemPreEmpenho->getComplemento();
    $arItens[$inCount]['quantidade']   = $obItemPreEmpenho->getQuantidade();
    $arItens[$inCount]['cod_unidade']  = $obItemPreEmpenho->obRUnidadeMedida->getCodUnidade();
    $arItens[$inCount]['cod_grandeza'] = $obItemPreEmpenho->obRUnidadeMedida->obRGrandeza->getCodGrandeza();
    $arItens[$inCount]['nom_unidade']  = $obItemPreEmpenho->getNomUnidade();
    $arItens[$inCount]['vl_total']     = $obItemPreEmpenho->getValorTotal();
    $arItens[$inCount]['vl_unitario']  = bcdiv( $obItemPreEmpenho->getValorTotal(), $obItemPreEmpenho->getQuantidade(),2 );
    $arItens[$inCount]['vl_empenhado_anulado']  =  $obItemPreEmpenho->getValorEmpenhadoAnulado();
    $arItens[$inCount]['vl_liquidado']          =  $obItemPreEmpenho->getValorLiquidado() - $obItemPreEmpenho->getValorLiquidadoAnulado();
    $arItens[$inCount]['vl_a_anular']           =  number_format($nuVlAAnular,2,',','.');
    Sessao::write('arItens', $arItens);
    $nuVTotal = $nuVTotal + $obItemPreEmpenho->getValorTotal();
}
SistemaLegado::executaFramePrincipal("buscaDado('montaListaItemPreEmpenho');");
$arChaveAtributo =  array( "cod_pre_empenho" => $_REQUEST["inCodPreEmpenho"],
                           "exercicio"       => $_REQUEST['stExercicioEmpenho'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos     );

//$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->consultaSaldoAnterior( $nuSaldoDotacao );

// FAZER ESTE MONTE DE RECUPERACAO DE VALORES!
$nuVlAnulacao = $nuVTotal + $nuSaldoAnterior - $nuVlLiquidacao + $nuVlLiquidacaoAnulada - $nuVlAnulado;

if ($_REQUEST['boImplantado'] != 't') {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->consultaSaldoAnterior( $nuSaldoDotacao );
    Sessao::write('nuSaldoDotacao', $nuSaldoDotacao);
    $stDespesa = $inCodDespesa.' - '.$stNomDespesa;
    $stOrgao   = $inNumOrgao.' - '.$stNomOrgao;
    $stUnidade = $inNumUnidade.' - '.$stNomUnidade;
} else {
//    $stDespesa = $inCodDespesa;
    $stOrgao   = $inNumOrgao;
    $stUnidade = $inNumUnidade;
}

//sessao->transf5['nuSaldoDotacao'] = $nuSaldoDotacao;

$nuSaldoDotacao = number_format( $nuSaldoDotacao, 2, ',', '.');

if ( Sessao::read('filtro') ) {
    $arFiltro = Sessao::read('filtro');
    $stFiltro = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        $stFiltro .= "&".$stCampo."=".@urlencode( $stValor );
    }
    $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
}

$obREmpenhoNotaLiquidacao = new REmpenhoNotaLiquidacao($obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho);
$obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
$obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setCodEmpenho($_REQUEST['inCodEmpenho']);
$obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setExercicio ($_REQUEST['stDtExercicioEmpenho']);
$obREmpenhoNotaLiquidacao->listarMaiorDataAnulacaoEmpenho($rsMaiorDataLiquidacao);

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade'] );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicio( Sessao::getExercicio() );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listarMaiorDataAnulacao( $rsMaiorData );

$stMaiorDataLiquidacaoEmpenho = $rsMaiorDataLiquidacao->getCampo("dataanulacao");
$stMaiorDataEmpenho = $rsMaiorData->getCampo("dataanulacao");

if (SistemaLegado::comparaDatas($stMaiorDataLiquidacaoEmpenho, $stMaiorDataEmpenho)) {
    $stMaiorDataAnulacao = $stMaiorDataLiquidacaoEmpenho;
} else {
    $stMaiorDataAnulacao = $stMaiorDataEmpenho;
}

if ( $stMaiorDataAnulacao && strlen($stMaiorDataAnulacao) > 0 ) {
    $anoUltimaAnulacao = (int) substr($stMaiorDataAnulacao, 6, strlen($stMaiorDataAnulacao));
    if ($anoUltimaAnulacao > Sessao::getExercicio()) {
        $stMaiorData = '31/12/' . Sessao::getExercicio();
    } elseif ($anoUltimaAnulacao < Sessao::getExercicio()) {
        $stMaiorData = '01/01/' . Sessao::getExercicio();
    } else {
        $stMaiorData = $stMaiorDataAnulacao;
    }
} else {
    $stMaiorData = '01/01/'.Sessao::getExercicio();
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

$inCodEmpenho = $_REQUEST['inCodEmpenho'];
$inCodPreEmpenho = $_REQUEST['inCodPreEmpenho'];

// Define objeto Hidden para Codigo da Empenho
$obHdnCodEmpenho = new Hidden;
$obHdnCodEmpenho->setName ( "inCodEmpenho" );
$obHdnCodEmpenho->setValue( $inCodEmpenho );

// Define objeto Hidden para Codigo da Empenho
$obHdnDtEmpenho = new Hidden;
$obHdnDtEmpenho->setName ( "stDtEmpenho" );
$obHdnDtEmpenho->setValue( $stDtEmpenho );

// Define objeto Hidden para Codigo da Pre Empenho
$obHdnCodPreEmpenho = new Hidden;
$obHdnCodPreEmpenho->setName ( "inCodPreEmpenho" );
$obHdnCodPreEmpenho->setValue( $inCodPreEmpenho );

// Define objeto Hidden para Codigo da Entidade
$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ( "inCodEntidade" );
$obHdnCodEntidade->setValue( $inCodEntidade );

// Define objeto Hidden para Codigo da Entidade
$obHdnExercicioEmpenho = new Hidden;
$obHdnExercicioEmpenho->setName ( "stDtExercicioEmpenho" );
$obHdnExercicioEmpenho->setValue( $_REQUEST['stExercicioEmpenho'] );

// Define objeto Hidden para Codigo da Entidade
$obHdnImplantado = new Hidden;
$obHdnImplantado->setName ( "boImplantado" );
$obHdnImplantado->setValue( $_REQUEST['boImplantado'] );

// Define objeto Hidden para Codigo da Despesa
$obHdnImplantado = new Hidden;
$obHdnImplantado->setName ( "inCodDespesa" );
$obHdnImplantado->setValue( $inCodDespesa );

// Define objeto Hidden para Codigo da Empenho
$obHdnBoImplantado = new Hidden;
$obHdnBoImplantado->setName ( "boImplantado" );
$obHdnBoImplantado->setValue( $_REQUEST['boImplantado'] );

$obHdnValidacao = new HiddenEval;
$obHdnValidacao->setName("boValidacao");
$obHdnValidacao->setValue( " " ); //Preenchido a partir do JS

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
$obLblOrgao = new Label;
$obLblOrgao->setRotulo ( "Orgão Orçamentário" );
$obLblOrgao->setId     ( "inCodOrgao" );
$obLblOrgao->setValue  ( $stOrgao );

// Define Objeto Label para Codigo do Unidade Orçamentária
$obLblUnidade = new Label;
$obLblUnidade->setRotulo ( "Unidade Orçamentária" );
$obLblUnidade->setId     ( "inCodUnidade" );
$obLblUnidade->setValue  ( $stUnidade );

// Define Objeto Label para Fornecedor
$obLblFornecedor = new Label;
$obLblFornecedor->setRotulo ( "Fornecedor" );
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

// VALIDA SE O EMPENHO É DE RESTOS PARA FORÇAR PREENCHER O ATRIBUTO DE RESTOS

$stValidaRestos = "if ((document.frm.stDtExercicioEmpenho.value<'".Sessao::getExercicio()."') && (eval(document.frm.Atributo_102_1))){ if (document.frm.Atributo_102_1.value=='') { erro = true; mensagem += '@Informe o campo Restos antes de Anular o Empenho!';}}";

$obHdnValidaRestos = new HiddenEval;
$obHdnValidaRestos->setName("hdnValidaRestos");
$obHdnValidaRestos->setValue( $stValidaRestos );

// Atributos Dinamicos
$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );
/*
if ($_REQUEST['stExercicioEmpenho']<Sessao::getExercicio()) {
   $obMontaAtributos->setSortLabel      ( true );
} else {
   $obMontaAtributos->setLabel      ( true );
}
*/
$obMontaAtributos->setLabel      ( true );
// Define Objeto Span Para lista de itens
$obSpan = new Span;
$obSpan->setId( "spnLista" );

// Define Objeto Label para Valor Total dos Itens
$obLblVlTotal = new Label;
$obLblVlTotal->setId( "nuValorTotal" );
$obLblVlTotal->setRotulo( "TOTAL EMPENHADO: " );

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

// Define objeto Data para validade final
$obDtAnulacao = new Data;
$obDtAnulacao->setName     ( "stDtAnulacao" );
$obDtAnulacao->setValue    ( $stMaiorData  );
$obDtAnulacao->setRotulo   ( "Data de Anulação");
$obDtAnulacao->setTitle    ( 'Informe a data da anulação de empenho' );
$obDtAnulacao->setNull     ( false );
$obDtAnulacao->obEvento->setOnBlur( "validaDataAnulacao();" );

if (Sessao::getExercicio() >= 2014) {
    // Define Objeto Select para Restos a pagar, SOMENTE APARTIR DE 2014
    $obCmbRestos = new Select;
    $obCmbRestos->setRotulo    ( "Posição em 31/12/".(Sessao::getExercicio()-1));
    $obCmbRestos->setTitle     ( 'Informe a posição deste empenho' );
    $obCmbRestos->setName      ('inRestos');
    $obCmbRestos->setId        ('inRestos');
    $obCmbRestos->setValue     ($inRestos);
    $obCmbRestos->addOption    ('', 'Selecione');
    $obCmbRestos->addOption    ('0', 'Não Processado');
    $obCmbRestos->addOption    ('1', 'Não Processado Liquidado');
    $obCmbRestos->addOption    ('2', 'Processado');
    $obCmbRestos->setStyle     ('width: 300');
    $obCmbRestos->setNull      (false);
}

// Define objeto TextArea para Motivo da Anulação
$obTxtMotivoAnulacao = new TextArea;
$obTxtMotivoAnulacao->setId       ( "stMotivo" );
$obTxtMotivoAnulacao->setName     ( "stMotivo" );
$obTxtMotivoAnulacao->setValue    ( $stMotivo  );
$obTxtMotivoAnulacao->setRotulo   ( "Motivo"   );
$obTxtMotivoAnulacao->setTitle    ( "Motivo da Anulação" );
$obTxtMotivoAnulacao->setCols     ( 100        );
$obTxtMotivoAnulacao->setRows     ( 3          );
$obTxtMotivoAnulacao->setNull     ( false      );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados do empenho" );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCodEmpenho       );
$obFormulario->addHidden( $obHdnDtEmpenho        );
$obFormulario->addHidden( $obHdnCodPreEmpenho    );
$obFormulario->addHidden( $obHdnCodEntidade      );
$obFormulario->addHidden( $obHdnExercicioEmpenho );
$obFormulario->addHidden( $obHdnImplantado       );
$obFormulario->addHidden( $obHdnBoImplantado     );
$obFormulario->addHidden( $obHdnValidacao,true   );
$obFormulario->addHidden( $obHdnValidaRestos, true);

$obFormulario->addComponente( $obLblEmpenho       );
$obFormulario->addComponente( $obLblEntidade      );
$obFormulario->addComponente( $obLblDespesa       );
$obFormulario->addComponente( $obLblClassificacao );
$obFormulario->addComponente( $obLblSaldoDotacao  );
$obFormulario->addComponente( $obLblOrgao         );
$obFormulario->addComponente( $obLblUnidade       );
$obFormulario->addComponente( $obLblFornecedor    );
$obFormulario->addComponente( $obLblNomEmpenho    );
$obFormulario->addComponente( $obLblDtEmpenho     );
$obFormulario->addComponente( $obDtAnulacao       );
$obFormulario->addComponente( $obLblDtVencimento  );
$obFormulario->addComponente( $obLblTipo          );
$obFormulario->addComponente( $obLblHistorico     );

$obMontaAtributos->geraFormulario ( $obFormulario );

$obFormulario->addTitulo( "Itens do empenho" );
$obFormulario->addSpan( $obSpan );
$obFormulario->addComponente( $obLblVlTotal        );

if (Sessao::getExercicio() >= 2014) {
    if ($_REQUEST['stExercicioEmpenho'] < Sessao::getExercicio()) {
        $obFormulario->addTitulo( "Restos a Pagar" );
        $obFormulario->addComponente( $obCmbRestos );
    }
}

$obFormulario->addTitulo( "Anulação" );
$obFormulario->addComponente( $obTxtMotivoAnulacao );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
$obOk = new Ok(true);
$obCancelar = new Cancelar();
$obCancelar->obEvento->setOnClick("Cancelar('".$stLocation."','telaPrincipal');");
$obFormulario->defineBarra( array( $obOk, $obCancelar ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
