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
    * Página de Formulário de Adiantamentos/Subvenções
    * Data de Criação : 21/10/2006

    $Id: FMManterAdiantamentosSubvencoes.php 59612 2014-09-02 12:00:51Z gelson $

    * @author Analista     : Gelson Golçalves
    * @author Desenvolvedor: Rodrigo

    * @ignore

    * Casos de uso: uc-02.03.31
*/

//Include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include mapeamento do Empenho
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php"             );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php"  );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoPagamentoLiquidacao.class.php" );
include_once ( TEMP."TEmpenhoPrestacaoContas.class.php"                   );
include_once ( TEMP."TEmpenhoResponsavelAdiantamento.class.php"           );
//Include componentes
include_once( CAM_GF_ORC_COMPONENTES.'ISelectMultiploEntidadeUsuario.class.php'                       );
include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoTipoDocumento.class.php"                                 );
include_once ( CAM_GF_EMP_COMPONENTES."IPopUpCredor.class.php"                                        );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAdiantamentosSubvencoes";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgCons = "FMConsultarEmpenho.php";
$pgCred = "OCManterEmpenho.php";

include_once($pgJS );

//Inicio da listagem de dados para prestação de contas

$stFiltro = '';
if ( Sessao::read('filtro') ) {
    $arFiltro = Sessao::read('filtro');
    $stFiltro = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        $stFiltro .= "&".$stCampo."=".@urlencode( $stValor );
    }
    $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
}

$obTEmpenhoPrestacaoContas = new TEmpenhoPrestacaoContas();
$obTEmpenhoPrestacaoContas->setDado('exercicio',$_REQUEST['stExercicioEmpenho'  ]);
$obTEmpenhoPrestacaoContas->setDado('cod_empenho',$_REQUEST['inCodEmpenho'      ]);
$obTEmpenhoPrestacaoContas->setDado('cod_entidade',$_REQUEST['inCodEntidade'    ]);
$obTEmpenhoPrestacaoContas->recuperaDataPagamentoEmpenho($rsDataPagamentoEmpenho);

$stDataPagamentoEmpenho = $rsDataPagamentoEmpenho->getCampo('data');

$obRAutorizacao = new REmpenhoEmpenhoAutorizacao;

$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setExercicio          ( Sessao::getExercicio() );
$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm')    );
$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade        );

$obRAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->listar               ( $rsTipo            );

$obRAutorizacao->obREmpenhoEmpenho->obREmpenhoHistorico->listar                 ( $rsHistorico       );

$obRAutorizacao->obREmpenhoEmpenho->listarUnidadeMedida                         ( $rsUnidade         );

$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio(Sessao::getExercicio());
$stMascaraRubrica = $obRAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();

$obRAutorizacao->obREmpenhoEmpenho->setCodEmpenho                          ( $_REQUEST['inCodEmpenho'      ] );
$obRAutorizacao->obREmpenhoEmpenho->setExercicio                           ( $_REQUEST['stExercicioEmpenho'] );
$obRAutorizacao->obREmpenhoAutorizacaoEmpenho->setCodAutorizacao           ( $_REQUEST['inCodAutorizacao'  ] );
$obRAutorizacao->obREmpenhoEmpenho->setCodPreEmpenho                       ( $_REQUEST['inCodPreEmpenho'   ] );
$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'     ] );
$obRAutorizacao->obREmpenhoEmpenho->obRUsuario->obRCGM->setNumCGM          ( Sessao::read('numCgm')                 );
$obRAutorizacao->obREmpenhoEmpenho->listar( $rsSaldos );

$stDespesa = $obRAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->getCodDespesa();
$obRAutorizacao->obREmpenhoEmpenho->consultarValorItem();

$nuValorSaldoAnterior     = $obRAutorizacao->obREmpenhoEmpenho->getVlSaldoAnterior();
$obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;

$obREmpenhoOrdemPagamento->setExercicio                    ( $_REQUEST['stExercicioEmpenho'] );
$obREmpenhoOrdemPagamento->obREmpenhoEmpenho->setCodEmpenho( $_REQUEST['inCodEmpenho']       );
$obREmpenhoOrdemPagamento->listarValorPago                 ( $rsValorPago                    );

$obREmpenhoOrdemPagamento->obREmpenhoEmpenho->setExercicio($_REQUEST['stExercicioEmpenho']);
$obREmpenhoOrdemPagamento->setCodigoEmpenho( $_REQUEST['inCodEmpenho']   );
$obREmpenhoOrdemPagamento->listarDadosPagamento            ( $rsOrdemPagamento               );
$inCodOP = $rsOrdemPagamento->getCampo('cod_ordem');
$inCodLiquidacao = $rsOrdemPagamento->getCampo('nota_empenho');
$inCodLiquidacao = substr($inCodLiquidacao, 0, strpos($inCodLiquidacao, '/'));

$inNumUnidade=$obRAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade();
$stNomUnidade=$obRAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNomUnidade();
$inNumOrgao  =$obRAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
$stNomOrgao  =$obRAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNomeOrgao();

$inNumUnidade=$obRAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade();
$stNomUnidade=$obRAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNomUnidade();
$inNumOrgao  =$obRAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
$stNomOrgao  =$obRAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNomeOrgao();
$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->setCodDespesa( $rsSaldos->getCampo( "cod_despesa" ) );

if ($_REQUEST['boImplantado']!='t') {
    $obErro=$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->consultar($rsDespesa);
    if (!$obErro->ocorreu()) {
        $nuValorOriginal=$rsDespesa->getCampo('vl_original');
    }
}

$flValorPago       =number_format( $rsValorPago->getCampo( "valor_pago" ), 2,',','.' );
$flSaldoAnterior   =number_format( $nuValorOriginal, 2,',','.'                       );

$flEmpenhado       =$rsSaldos->getCampo( "vl_empenhado"         ) - $rsSaldos->getCampo( "vl_empenhado_anulado" );
$flEmpenhado       =$flEmpenhado;
$flLiquidado       =$rsSaldos->getCampo( "vl_liquidado"                                                         );
$flLiquidadoAnulado=$rsSaldos->getCampo( "vl_liquidado_anulado"                                                 );
$flLiquidado       =number_format( $flLiquidado - $flLiquidadoAnulado ,2,',','.'                                );

$dtDataFinal      =$rsSaldos->getCampo( "dt_vencimento"                 );
$nuVlPago         =$rsSaldos->getCampo( "vl_pago"                       );
$nuVlPagoAnulado  =$rsSaldos->getCampo( "vl_pago_anulado"               );
$nuVlTotalPago    =$nuVlPago - $nuVlPagoAnulado;
$nuVlTotalPago    =$nuVlTotalPago;
$nuSaldoDisponivel=$rsSaldos->getCampo( "vl_dotacao"                    );
$nuSaldoDisponivel=number_format      ( (float) $nuSaldoDisponivel, 2, ',', '.' );

$stNomEmpenho     =$obRAutorizacao->obREmpenhoEmpenho->getDescricao();
$stNomEntidade    =$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->getNomCGM();
$inCodTipo        =$obRAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->getCodTipo();
$stNomTipo        =$obRAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->getNomTipo();
$inCodDespesa     =$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->getCodDespesa();
$stNomDespesa     =$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getDescricao();

$stCodClassificacao=$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->getMascClassificacao();
$stNomClassificacao=$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->getDescricao();

$inCodFornecedor =$obRAutorizacao->obREmpenhoEmpenho->obRCGM->getNumCGM();
$stNomFornecedor =$obRAutorizacao->obREmpenhoEmpenho->obRCGM->getnomCGM();
$stDtVencimento  =$obRAutorizacao->obREmpenhoEmpenho->getDtVencimento();
$inCodHistorico  =$obRAutorizacao->obREmpenhoEmpenho->obREmpenhoHistorico->getCodHistorico();
$stNomHistorico  =$obRAutorizacao->obREmpenhoEmpenho->obREmpenhoHistorico->getNomHistorico();
$stDtEmpenho     =$obRAutorizacao->obREmpenhoEmpenho->getDtEmpenho();
$arItemPreEmpenho=$obRAutorizacao->obREmpenhoEmpenho->getItemPreEmpenho();

$inCodRecurso =
$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso();
$stNomRecursoAux = '';
$stRecurso  = $inCodRecurso;
$stRecurso .=" - ";

$stNomRecurso =
$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getNome();
if ($stNomRecurso != '') {
    $stNomRecursoAux = $stNomRecurso;
} else {
    $stNomRecursoAux =
SistemaLegado::pegaDado('nom_recurso',"orcamento.recurso('".Sessao::getExercicio()."')","
WHERE cod_recurso = ".$inCodRecurso." AND exercicio = '".Sessao::getExercicio()."'
");
}
$stRecurso .= $stNomRecursoAux;

$flDotacaoInicial=$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->getValorOriginal();
$flDotacaoInicial=number_format($flDotacaoInicial,2,',','.');
$inCodPao        =$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoProjetoAtividade->getNumeroProjeto();
$stNomPao        =$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoProjetoAtividade->getNome();
if($_REQUEST['inCodAutorizacao'])
    $stAutorizacao   =$_REQUEST['inCodAutorizacao']." / ".$_REQUEST['stExercicioEmpenho'];

$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->consultarSaldoDotacao();
$obRAutorizacao->obREmpenhoEmpenho->consultarSaldoAnterior();

$nuSaldoDotacao   = number_format($obRAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->getSaldoDotacao(),2,',','.');
$nuSaldoAnterior  = $obRAutorizacao->obREmpenhoEmpenho->getSaldoAnterior();

$flLiquidado        = 0;
$flEmpenhado        = 0;
$flEmpenhadoAnulado = 0;

foreach ($arItemPreEmpenho as $inCount => $obItemPreEmpenho) {
    $flLiquidado          += $obItemPreEmpenho->getValorLiquidado() - $obItemPreEmpenho->getValorLiquidadoAnulado();
    $flEmpenhado          += $obItemPreEmpenho->getValorTotal();
    $flEmpenhadoAnulado   += $obItemPreEmpenho->getValorEmpenhadoAnulado();
    $nuVTotal = $nuVTotal + $obItemPreEmpenho->getValorTotal();
}

$arChaveAtributo =  array( "cod_pre_empenho" => $obRAutorizacao->obREmpenhoEmpenho->getCodPreEmpenho(),
                           "exercicio"       => Sessao::getExercicio()         );
$obRAutorizacao->obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->setChavePersistenteValores          ($arChaveAtributo);
$obRAutorizacao->obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->recuperaAtributosSelecionadosValores($rsAtributos    );

$obRAutorizacao->obREmpenhoEmpenho->consultaSaldoAnterior( $nuSaldoDisponivel );
$stDespesa         = $inCodDespesa.' - '.$stNomDespesa;
$stOrgao           = $inNumOrgao.' - '.$stNomOrgao;
$stUnidade         = $inNumUnidade.' - '.$stNomUnidade;
$nuSaldoAnterior   = number_format($nuSaldoAnterior,2,',','.'  );
$nuSaldoDisponivel = number_format($nuSaldoDisponivel,2,',','.');

//Final da listagem de dados para prestação de contas

Sessao::remove('arValores');
Sessao::remove('inCountValores');

$stCaminho = CAM_GF_EMP_INSTANCIAS."empenho/";
$inCodEntidade = $_REQUEST['inCodEntidade'];
$inCodEmpenho = $_REQUEST['inCodEmpenho'];
$stDtPrestacaoContas = $_REQUEST['stDtPrestacaoContas'];
$stDataDocumento = $_REQUEST['stDataDocumento'];
$stJustificativa = $_REQUEST['stJustificativa'];
$nuValor = $_REQUEST['nuValor'];

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao"  );
$obHdnAcao->setValue( "incluir" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "stCtrl" );
$obHdnCtrl->setValue( "incluir" );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName ( "exercicio"                     );
$obHdnExercicio->setValue( $_REQUEST['stExercicioEmpenho'] );

$obHdnVlEmpenhado = new Hidden;
$obHdnVlEmpenhado->setName ( "inVlEmpenhado" );
$obHdnVlEmpenhado->setValue( $flEmpenhado );

$obHdnVlPago = new Hidden;
$obHdnVlPago->setName ( "inVlPago" );
$obHdnVlPago->setValue( $nuVlTotalPago );

$obHdnVlPagoTMP = new Hidden;
$obHdnVlPagoTMP->setName ('inVlPagoTMP');
$obHdnVlPagoTMP->setId ('inVlPagoTMP');
$obHdnVlPagoTMP->setValue('');

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ( "inCodEntidade" );
$obHdnCodEntidade->setValue( $inCodEntidade  );
$obHdnCodEntidade->setId   ( "inCodEntidade" );

$obLblDataEmpenho = new Label;
$obLblDataEmpenho->setRotulo( "Data de Empenho" );
$obLblDataEmpenho->setValue ( $stDtEmpenho      );

// Define objeto Hidden para Codigo Empenho
$obHdnCodEmpenho = new Hidden;
$obHdnCodEmpenho->setName ( "inCodEmpenho" );
$obHdnCodEmpenho->setValue( $inCodEmpenho  );

$obHdnContrapartida = new Hidden;
$obHdnContrapartida->setName ( "inCodContrapartida" );
$obHdnContrapartida->setValue( $_REQUEST['inCodContrapartida'] );

// Define objeto Hidden para Data de Empenho
$obHdnDataEmpenho = new hidden;
$obHdnDataEmpenho->setName ( "stDataEmpenho" );
$obHdnDataEmpenho->setValue( $stDtEmpenho );

// Define objeto Hidden para Data Atual
$obHdnDataAtual = new hidden;
$obHdnDataAtual->setName ( "stDataAtual" );
$obHdnDataAtual->setValue( date('d/m/Y') );

// Define objeto Hidden para Data de Pagamento do Empenho
$obHdnDataPagamentoEmpenho = new hidden;
$obHdnDataPagamentoEmpenho->setName( "stDataPagamentoEmpenho" );
$obHdnDataPagamentoEmpenho->setId  ( "stDataPagamentoEmpenho" );
$obHdnDataPagamentoEmpenho->setValue( $stDataPagamentoEmpenho );

// Define Objeto Label para Fornecedor
$obHdnCodFornecedor = new Hidden;
$obHdnCodFornecedor->setName ( "inCodCredor"    );
$obHdnCodFornecedor->setValue( $inCodFornecedor );

// Define Objeto Label para Nome Fornecedor
$obHdnNomFornecedor = new Hidden;
$obHdnNomFornecedor->setName ("stNomFornecedor"   );
$obHdnNomFornecedor->setValue($stNomFornecedor);

// Define Objeto Label para Codigo Dotação
$obHdnCodDotacao = new Hidden;
$obHdnCodDotacao->setName ("inCodDespesa");
$obHdnCodDotacao->setValue($inCodDespesa );

// Define Objeto Label para Nome Dotação
$obHdnNomDotacao = new Hidden;
$obHdnNomDotacao->setName ("stNomeDespesa");
$obHdnNomDotacao->setValue($stNomDespesa );

// Define Objeto Label para Codigo Liquidação
$obHdnCodLiquidacao = new Hidden;
$obHdnCodLiquidacao->setName ("inCodLiquidacao");
$obHdnCodLiquidacao->setValue($inCodLiquidacao);

// Define Objeto Label para Código Ordem de Pagamento
$obHdnCodOP = new Hidden;
$obHdnCodOP->setName ("inCodOP");
$obHdnCodOP->setValue($inCodOP);

$obHdnCodItem = new Hidden;
$obHdnCodItem->setName ( "HdnCodItem" );
$obHdnCodItem->setValue( ""           );

$obLblEmpenho = new Label;
$obLblEmpenho->setRotulo( "Nr. Empenho"                                        );
$obLblEmpenho->setValue ( $inCodEmpenho .' / '.$_REQUEST['stExercicioEmpenho'] );

//Define objeto label entidade
$obLblEntidade = new Label;
$obLblEntidade->setRotulo( "Entidade"                             );
$obLblEntidade->setId    ( "stNomeEntidade"                       );
$obLblEntidade->setValue ( $inCodEntidade.' - '.$stNomEntidade    );

$obLblDotacao = new Label;
$obLblDotacao->setRotulo( "Dotação"      );
$obLblDotacao->setId    ( "stNomDespesa" );
$obLblDotacao->setValue ( $stDespesa     );

$obLblDesdobramento = new Label;
$obLblDesdobramento->setRotulo( "Desdobramento"                               );
$obLblDesdobramento->setId    ( "stNomClassificacao"                          );
$obLblDesdobramento->setValue ( $stCodClassificacao.' - '.$stNomClassificacao );

$obLblOrgao = new Label;
$obLblOrgao->setRotulo( "Orgão Orçamentário" );
$obLblOrgao->setId    ( "inCodOrgao"         );
$obLblOrgao->setValue ( $stOrgao             );

$obLblUnidade = new Label;
$obLblUnidade->setRotulo( "Unidade Orçamentária" );
$obLblUnidade->setId    ( "inCodUnidade"         );
$obLblUnidade->setValue ( $stUnidade             );

// Define objeto Label para PAO
$obLblPAO = new Label;
$obLblPAO->setRotulo( 'PAO'                     );
$obLblPAO->setId    ( 'pao'                     );
$obLblPAO->setValue ( $inCodPao.' - '.$stNomPao );

$obLblRecurso = new Label;
$obLblRecurso->setRotulo( "Recurso"   );
$obLblRecurso->setId    ( "stRecurso" );
$obLblRecurso->setValue ( $stRecurso  );

$obLblCredor = new Label;
$obLblCredor->setRotulo( "Credor"                                );
$obLblCredor->setValue ( $inCodFornecedor.' - '.$stNomFornecedor );

$obLblDescricao = new Label;
$obLblDescricao->setRotulo( "Descrição do Empenho" );
$obLblDescricao->setId    ( "stNomEmpenho"         );
$obLblDescricao->setValue ( $stNomEmpenho          );

$obLblAutorizacao = new Label;
$obLblAutorizacao->setRotulo( "Autorização"      );
$obLblAutorizacao->setId    ( "inCodAutorizacao" );
$obLblAutorizacao->setValue ( $stAutorizacao     );

// Atributos Dinamicos
$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );
$obMontaAtributos->setLabel      ( true         );

// Saldos
$obLblEmpenhado = new Label;
$obLblEmpenhado->setRotulo( "Valor Empenhado"   );
$obLblEmpenhado->setId    ( "flEmpenhado"       );
$obLblEmpenhado->setValue ( number_format($flEmpenhado,2,',','.') );

$obLblLiquidado = new Label;
$obLblLiquidado->setRotulo( "Valor Liquidado"   );
$obLblLiquidado->setId    ( "flLiquidado"       );
$obLblLiquidado->setValue ( number_format($flLiquidado,2,',','.') );

$obLblPago = new Label;
$obLblPago->setRotulo( "Valor Pago"   );
$obLblPago->setId    ( "flPago"       );
$obLblPago->setValue ( number_format($nuVlTotalPago,2,',','.') );

//Campo de Situação com o valor SIM
$obRadDevolucaoSim = new Radio;
$obRadDevolucaoSim->setName   ('boDevolucao');
$obRadDevolucaoSim->setRotulo ('Devolução Integral');
$obRadDevolucaoSim->setLabel  ('Sim');
$obRadDevolucaoSim->setValue  ('Sim');
$obRadDevolucaoSim->setChecked(false);
$obRadDevolucaoSim->setNull   (false);
$obRadDevolucaoSim->setId     ('Sim');
$obRadDevolucaoSim->obEvento->setOnClick("montaParametrosGET('mudaVisibilidadeON');");

//Campo de Situação com valor NÃO
$obRadDevolucaoNao = new Radio;
$obRadDevolucaoNao->setName   ('boDevolucao');
$obRadDevolucaoNao->setRotulo ('Devolução Integral');
$obRadDevolucaoNao->setLabel  ('Não');
$obRadDevolucaoNao->setValue  ('Nao');
$obRadDevolucaoNao->setChecked(true );
$obRadDevolucaoNao->setNull   (false);
$obRadDevolucaoNao->setId     ('Nao');
$obRadDevolucaoNao->obEvento->setOnClick("montaParametrosGET('mudaVisibilidadeOFF');");

// Define Objeto Data para Data das Notas Fiscais
$obDtPrestacaoContas = new Data;
$obDtPrestacaoContas->setName   ( "stDtPrestacaoContas"                                   );
$obDtPrestacaoContas->setId     ( "stDtPrestacaoContas"                                   );
$obDtPrestacaoContas->setValue  ( $stDtPrestacaoContas                                    );
$obDtPrestacaoContas->setRotulo ( "*Data Prestação de Contas"                             );
$obDtPrestacaoContas->setTitle  ( "Informe a data de prestação de contas."                );
$obDtPrestacaoContas->setNull   ( true                                                    );
$obDtPrestacaoContas->obEvento->setOnChange("montaParametrosGET('validaDataPrestacao');");

//tipo de documento
$rsTipoDocumento = new RecordSet();
$obTAdministracaoTipoDocumento = new TEmpenhoTipoDocumento();
$obTAdministracaoTipoDocumento->recuperaTodos ( $rsTipoDocumento );

$obCmbTipoDocumento = new Select;
$obCmbTipoDocumento->setRotulo     ( "*Tipo de Documento"             );
$obCmbTipoDocumento->setTitle      ( "Selecione o tipo de documento." );
$obCmbTipoDocumento->setName       ( "inCodTipoDocumento"             );
$obCmbTipoDocumento->setId         ( "inCodTipoDocumento"             );
$obCmbTipoDocumento->setCampoID    ( "cod_documento"                  );
$obCmbTipoDocumento->setCampoDesc  ( "descricao"                      );
$obCmbTipoDocumento->addOption     ( "", "Selecione"                  );
$obCmbTipoDocumento->preencheCombo ( $rsTipoDocumento                 );
$obCmbTipoDocumento->setStyle      ( "width: 200px"                   );

$obTxtNroDocumento = new Inteiro;
$obTxtNroDocumento->setName   ( "inNroDocumento"            );
$obTxtNroDocumento->setId     ( "inNroDocumento"            );
$obTxtNroDocumento->setRotulo ( "Número do Documento"       );
$obTxtNroDocumento->setTitle  ( "Informe o número do documento." );
$obTxtNroDocumento->setMaxLength( 9 );
$obTxtNroDocumento->setNull   ( true                        );
$obTxtNroDocumento->obEvento->setOnBlur( "validaInteiroKeyUp(this);" );

$obDtDocumento = new Data;
$obDtDocumento->setName   ( "stDataDocumento"             );
$obDtDocumento->setId     ( "stDataDocumento"             );
$obDtDocumento->setValue  ( $stDataDocumento              );
$obDtDocumento->setRotulo ( "*Data do Documento"          );
$obDtDocumento->setTitle  ( "Informe a data do documento.");
$obDtDocumento->setNull   ( true                          );
$obDtDocumento->obEvento->setOnChange("montaParametrosGET('validaDataDocumento');");

$obBscCredor = new IPopUpCredor( $obForm );
$obBscCredor->setRotulo ( "*Fornecedor"  );
$obBscCredor->setTitle  ( "Informe o fornecedor." );
$obBscCredor->setNull( true );
$obBscCredor->obCampoCod->setName( "inCodFornecedor" );
$obBscCredor->obCampoCod->setId  ( "inCodFornecedor" );
$obBscCredor->obCampoCod->obEvento->setOnBlur( "validaInteiroKeyUp(this);" );

$obTxtJustificativa = new TextArea;
$obTxtJustificativa->setName   ( "stJustificativa"                     );
$obTxtJustificativa->setId     ( "stJustificativa"                     );
$obTxtJustificativa->setValue  ( $stJustificativa                      );
$obTxtJustificativa->setRotulo ( "Justificativa"                       );
$obTxtJustificativa->setTitle  ( "Informe a justificativa." );
$obTxtJustificativa->setNull   ( true                                  );
$obTxtJustificativa->setRows   ( 2                                     );
$obTxtJustificativa->setCols   ( 64                                    );
$obTxtJustificativa->setMaxCaracteres(80);

// Define Objeto Moeda para Valor
$obTxtValor = new Moeda;
$obTxtValor->setName     ( "nuValor" );
$obTxtValor->setId       ( "nuValor" );
$obTxtValor->setValue    ( $nuValor  );
$obTxtValor->setRotulo   ( "*Valor"  );
$obTxtValor->setTitle    ( "Informe o valor."        );
$obTxtValor->setNull     ( true      );
$obTxtValor->setMaxLength( 15        );
$obTxtValor->setSize     ( 22        );

// Define Objeto Button para  Incluir Item
$obBtnIncluir = new Button;
$obBtnIncluir->setValue             ("Incluir"    );
$obBtnIncluir->setId                ("incluirNota");
$obBtnIncluir->obEvento->setOnClick("montaParametrosGET(document.getElementById('stCtrl').value);");

// Define Objeto Button para Limpar
$obBtnLimpar = new Button;
$obBtnLimpar->setValue             ("Limpar"      );
$obBtnLimpar->setId                ("Limpar"      );
$obBtnLimpar->obEvento->setOnClick ("limpaDado();");

//Span da Listagem de itens de Dados das Notas Fiscais
$obSpnListaNotasFiscal = new Span;
$obSpnListaNotasFiscal->setID("spnListaNotaFiscal");

$obLblTotalPrestacaoContas = new Label;
$obLblTotalPrestacaoContas->setRotulo( "Total Prestação de Contas" );
$obLblTotalPrestacaoContas->setId    ( "flTotalPrestacaoContas"    );
$obLblTotalPrestacaoContas->setName  ( "flTotalPrestacaoContas"    );
$obLblTotalPrestacaoContas->setValue ( "&nbsp;"                    );

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setCodEntidade($inCodEntidade);

$stLocation = $pgList.'?'.Sessao::getId().$stFiltro;

$obFormulario = new Formulario();
$obFormulario->addForm      ($obForm                   );
$obFormulario->addHidden    ($obHdnAcao                );
$obFormulario->addHidden    ($obHdnCtrl                );
$obFormulario->addHidden    ($obHdnExercicio           );
$obFormulario->addHidden    ($obHdnVlEmpenhado         );
$obFormulario->addHidden    ($obHdnVlPago              );
$obFormulario->addHidden    ($obHdnVlPagoTMP           );
$obFormulario->addHidden    ($obHdnCodEmpenho          );
$obFormulario->addHidden    ($obHdnCodEntidade         );
$obFormulario->addHidden    ($obHdnCodFornecedor       );
$obFormulario->addHidden    ($obHdnNomFornecedor       );
$obFormulario->addHidden    ($obHdnCodDotacao          );
$obFormulario->addHidden    ($obHdnNomDotacao          );
$obFormulario->addHidden    ($obHdnCodLiquidacao       );
$obFormulario->addHidden    ($obHdnCodOP               );
$obFormulario->addHidden    ($obHdnContrapartida       );
$obFormulario->addHidden    ($obHdnDataEmpenho         );
$obFormulario->addHidden    ($obHdnDataAtual           );
$obFormulario->addHidden    ($obHdnDataPagamentoEmpenho);
$obFormulario->addHidden    ($obHdnCodItem             );
$obFormulario->addTitulo    ('Dados da Prestação de Contas de Adiantamentos/Subvenções');
$obFormulario->addComponente($obLblEmpenho            );
$obFormulario->addComponente($obLblDataEmpenho        );
$obFormulario->addComponente($obLblEntidade           );
$obFormulario->addComponente($obLblOrgao              );
$obFormulario->addComponente($obLblUnidade            );
$obFormulario->addComponente($obLblDotacao            );
$obFormulario->addComponente($obLblDesdobramento      );
$obFormulario->addComponente($obLblPAO                );
$obFormulario->addComponente($obLblRecurso            );
$obFormulario->addComponente($obLblCredor             );
$obFormulario->addComponente($obLblDescricao          );
$obFormulario->addComponente($obLblAutorizacao        );
$obFormulario->addComponente($obLblEmpenhado          );
$obFormulario->addComponente($obLblLiquidado          );
$obFormulario->addComponente($obLblPago               );
$obFormulario->agrupaComponentes(array($obRadDevolucaoSim, $obRadDevolucaoNao));
$obFormulario->addTitulo    ('Dados das Notas Fiscais');
$obFormulario->addComponente($obDtPrestacaoContas     );
$obFormulario->addComponente($obDtDocumento           );
$obFormulario->addComponente($obCmbTipoDocumento      );
$obFormulario->addComponente($obTxtNroDocumento       );
$obFormulario->addComponente($obBscCredor             );
$obFormulario->addComponente($obTxtJustificativa      );
$obFormulario->addComponente($obTxtValor              );

$obFormulario->agrupaComponentes( array( $obBtnIncluir,$obBtnLimpar )                                  );
$obFormulario->addSpan      ( $obSpnListaNotasFiscal                                                   );
$obFormulario->addComponente( $obLblTotalPrestacaoContas                                               );
$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->Cancelar($stLocation);
$obFormulario->Show();

$stLink = "&inCodEntidade=".$_REQUEST['inCodEntidade'];
$stLink.= "&inCodEmpenho=".$_REQUEST['inCodEmpenho'];

$stJs     = "ajaxJavaScript('".$pgOcul."?".Sessao::getId().$stLink."','montaListaPrestacaoContas');";
$jsOnLoad = $stJs;
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
