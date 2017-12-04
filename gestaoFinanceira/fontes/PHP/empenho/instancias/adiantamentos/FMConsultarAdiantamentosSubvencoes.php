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

    $Id: FMConsultarAdiantamentosSubvencoes.php 59612 2014-09-02 12:00:51Z gelson $

    * @author Analista     : Gelson Golçalves
    * @author Desenvolvedor: Luciano Hoffmann

    * @ignore

    * Casos de uso: uc-02.03.31
*/
//Include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include mapeamentos
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
$pgOcul = "OCConsultarAdiantamentosSubvencoes.php";
$pgJS   = "JS".$stPrograma.".js";
$pgCons = "FMConsultarAdiantamentosSubvencoes.php";

$stDtPrestacaoContas = isset($stDtPrestacaoContas) ? $stDtPrestacaoContas : null;
$stDevolucaoIntegral = isset($stDevolucaoIntegral) ? $stDevolucaoIntegral : null;

include_once($pgJS);

//Inicio da listagem de dados para prestação de contas

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
$obTEmpenhoPrestacaoContas->recuperaDataPagamentoEmpenho($rsDataPagementoEmpenho);

$stDataPagementoEmpenho = $rsDataPagementoEmpenho->getCampo('data');

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
    $stNomRecursoAux = SistemaLegado::pegaDado('nom_recurso'
                            , "orcamento.recurso('".Sessao::getExercicio()."')","
                        WHERE cod_recurso = ".$inCodRecurso."
                          AND exercicio = '".Sessao::getExercicio()."' ");
}
$stRecurso .= $stNomRecursoAux;

$flDotacaoInicial=$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->getValorOriginal();
$flDotacaoInicial=number_format($flDotacaoInicial,2,',','.');
$inCodPao        =$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoProjetoAtividade->getNumeroProjeto();
$stNomPao        =$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoProjetoAtividade->getNome();
if ($_REQUEST['inCodAutorizacao']) {
    $stAutorizacao = $_REQUEST['inCodAutorizacao']." / ".$_REQUEST['stExercicioEmpenho'];
} else {
    $stAutorizacao = null;
}
$obRAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->consultarSaldoDotacao();
$obRAutorizacao->obREmpenhoEmpenho->consultarSaldoAnterior();

$nuSaldoDotacao   = number_format($obRAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->getSaldoDotacao(),2,',','.');
$nuSaldoAnterior  = $obRAutorizacao->obREmpenhoEmpenho->getSaldoAnterior();

$nuVTotal           = 0;
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

$inCodEntidade = $_REQUEST['inCodEntidade'];
$inCodEmpenho = $_REQUEST['inCodEmpenho'];

//Final da listagem de dados para prestação de contas

Sessao::remove('arValores');

$stCaminho = CAM_GF_EMP_INSTANCIAS."empenho/";

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao"  );
$obHdnAcao->setValue( "incluir" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( ""       );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName ( "exercicio"                     );
$obHdnExercicio->setValue( $_REQUEST['stExercicioEmpenho'] );

$obHdnVlEmpenhado = new Hidden;
$obHdnVlEmpenhado->setName ( "inVlEmpenhado" );
$obHdnVlEmpenhado->setValue( $flEmpenhado );

$obHdnVlPago = new Hidden;
$obHdnVlPago->setName ( "inVlPago" );
$obHdnVlPago->setValue( $nuVlTotalPago );

$obHdnCodItem = new Hidden;
$obHdnCodItem->setName ( "HdnCodItem" );
$obHdnCodItem->setValue( ""           );

// Define objeto Hidden para Codigo da Autorizacao
$obHdnCodAutorizacao = new Hidden;
$obHdnCodAutorizacao->setName ( "inCodAutorizacao" );
$obHdnCodAutorizacao->setValue( $_REQUEST['inCodAutorizacao']  );

// Define objeto Hidden para Codigo da Pre Empenho
$obHdnCodEmpenho = new Hidden;
$obHdnCodEmpenho->setName ( "inCodEmpenho" );
$obHdnCodEmpenho->setValue( $inCodEmpenho  );

// Define objeto Hidden para Codigo da Entidade
$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ( "inCodEntidade" );
$obHdnCodEntidade->setValue( $inCodEntidade  );
$obHdnCodEntidade->setId   ( "inCodEntidade" );

// Define objeto Hidden para Codigo da Reserva
$obHdnCodReserva = new Hidden;
$obHdnCodReserva->setName ( "inCodReserva" );
$obHdnCodReserva->setValue( $_REQUEST['inCodReserva']  );

// Define objeto Hidden para Codigo da Classificacao
$obHdnCodClassificacao = new Hidden;
$obHdnCodClassificacao->setName ( "stCodClassificacao" );
$obHdnCodClassificacao->setValue( $stCodClassificacao  );

// Define Objeto Label para Unidade
$obHdnCodOrgao = new Hidden;
$obHdnCodOrgao->setName ( "inCodOrgao" );
$obHdnCodOrgao->setValue( $inNumOrgao  );

// Define Objeto Label para Unidade
$obHdnCodUnidade = new Hidden;
$obHdnCodUnidade->setName ( "inCodUnidade" );
$obHdnCodUnidade->setValue( $inNumUnidade  );

// Define Objeto Label para Fornecedor
$obHdnCodFornecedor = new Hidden;
$obHdnCodFornecedor->setName ( "inCodCredor"    );
$obHdnCodFornecedor->setValue( $inCodFornecedor );

// Define objeto Hidden para Codigo da Classificacao
$obHdnCodDespesa = new Hidden;
$obHdnCodDespesa->setName  ( "inCodDespesa" );
$obHdnCodDespesa->setValue ( $inCodDespesa  );

// Define objeto Hidden para Codigo da Classificacao
$obHdnCodHistorico = new Hidden;
$obHdnCodHistorico->setName  ( "inCodHistorico" );
$obHdnCodHistorico->setValue ( $inCodHistorico  );

// Define objeto Label para Empenho
$obLblEmpenho = new Label;
$obLblEmpenho->setRotulo( "Nr. Empenho"                                        );
$obLblEmpenho->setValue ( $inCodEmpenho .' / '.$_REQUEST['stExercicioEmpenho'] );

// Define objeto Label para Data de Empenho
$obLblDataEmpenho = new Label;
$obLblDataEmpenho->setRotulo( "Data de Empenho" );
$obLblDataEmpenho->setValue ( $stDtEmpenho      );

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
$obHdnDataPagamentoEmpenho->setName ( "stDataPagamentoEmpenho" );
$obHdnDataPagamentoEmpenho->setValue( $stDataPagementoEmpenho );

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

$obHdnContrapartida = new Hidden;
$obHdnContrapartida->setName ( "inCodContrapartida" );
$obHdnContrapartida->setValue( $_REQUEST['inCodContrapartida'] );

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

// Define Objeto Data para Data das Notas Fiscais
$obLblDtPrestacaoContas = new Label;
$obLblDtPrestacaoContas->setName   ( "stDtPrestacaoContas"            );
$obLblDtPrestacaoContas->setId     ( "stDtPrestacaoContas"            );
$obLblDtPrestacaoContas->setValue  ( $stDtPrestacaoContas             );
$obLblDtPrestacaoContas->setRotulo ( "Data Prestação de Contas"       );

// Define Objeto Data para Data das Notas Fiscais
$obLblDevolucaoIntegral = new Label;
$obLblDevolucaoIntegral->setName  ('stDevolucaoIntegral');
$obLblDevolucaoIntegral->setId    ('stDevolucaoIntegral');
$obLblDevolucaoIntegral->setValue ($stDevolucaoIntegral);
$obLblDevolucaoIntegral->setRotulo('Devolução Integral');

$stLocation = $pgList.'?'.Sessao::getId().$stFiltro;

// Define Objeto Button para Cancelar
$obBtnCancelar = new Cancelar();
$obBtnCancelar->obEvento->setOnClick("Cancelar('".$stLocation."','telaPrincipal');");

//Span da Listagem de itens de Dados das Notas Fiscais
$obSpnListaNotasFiscal = new Span;
$obSpnListaNotasFiscal->setID("spnListaNotaFiscal");

$obLblTotalPrestacaoContas = new Label;
$obLblTotalPrestacaoContas->setRotulo( "Total Prestação de Contas" );
$obLblTotalPrestacaoContas->setId    ( "flTotalPrestacaoContas"    );
$obLblTotalPrestacaoContas->setValue ( "&nbsp;"                    );

$stAcao = $request->get('stAcao');

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
$obButtonVoltar = new Button;
$obButtonVoltar->setName  ( "Voltar" );
$obButtonVoltar->setValue ( "Voltar" );
$obButtonVoltar->obEvento->setOnClick("Cancelar('".$stLocation."');");

$obFormulario = new Formulario();
$obFormulario->addForm      ( $obForm                                                                  );
$obFormulario->addHidden    ( $obHdnAcao                                                               );
$obFormulario->addHidden    ( $obHdnCtrl                                                               );
$obFormulario->addHidden    ( $obHdnExercicio                                                          );
$obFormulario->addHidden    ( $obHdnVlEmpenhado                                                        );
$obFormulario->addHidden    ( $obHdnVlPago                                                             );
$obFormulario->addHidden    ( $obHdnCodItem                                                            );
$obFormulario->addHidden    ( $obHdnCodAutorizacao                                                     );
$obFormulario->addHidden    ( $obHdnCodAutorizacao                                                     );
$obFormulario->addHidden    ( $obHdnCodEmpenho                                                         );
$obFormulario->addHidden    ( $obHdnCodEntidade                                                        );
$obFormulario->addHidden    ( $obHdnCodReserva                                                         );
$obFormulario->addHidden    ( $obHdnCodClassificacao                                                   );
$obFormulario->addHidden    ( $obHdnCodOrgao                                                           );
$obFormulario->addHidden    ( $obHdnCodUnidade                                                         );
$obFormulario->addHidden    ( $obHdnCodFornecedor                                                      );
$obFormulario->addHidden    ( $obHdnCodDespesa                                                         );
$obFormulario->addHidden    ( $obHdnCodHistorico                                                       );
$obFormulario->addHidden    ( $obHdnContrapartida                                                      );
$obFormulario->addHidden    ( $obHdnDataEmpenho                                                        );
$obFormulario->addHidden    ( $obHdnDataAtual                                                          );
$obFormulario->addHidden    ( $obHdnDataPagamentoEmpenho                                               );
$obFormulario->addTitulo    ( 'Dados da Prestação de Contas de Adiantamentos/Subvenções'               );
$obFormulario->addComponente( $obLblEmpenho                                                            );
$obFormulario->addComponente( $obLblDataEmpenho                                                        );
$obFormulario->addComponente( $obLblEntidade                                                           );
$obFormulario->addComponente( $obLblOrgao                                                              );
$obFormulario->addComponente( $obLblUnidade                                                            );
$obFormulario->addComponente( $obLblDotacao                                                            );
$obFormulario->addComponente( $obLblDesdobramento                                                      );
$obFormulario->addComponente( $obLblPAO                                                                );
$obFormulario->addComponente( $obLblRecurso                                                            );
$obFormulario->addComponente( $obLblCredor                                                             );
$obFormulario->addComponente( $obLblDescricao                                                          );
$obFormulario->addComponente( $obLblAutorizacao                                                        );
$obFormulario->addComponente( $obLblEmpenhado                                                          );
$obFormulario->addComponente( $obLblLiquidado                                                          );
$obFormulario->addComponente( $obLblPago                                                               );

$obFormulario->addTitulo    ( 'Dados das Notas Fiscais'                                                );
$obFormulario->addComponente( $obLblDtPrestacaoContas                                                  );
$obFormulario->addComponente( $obLblDevolucaoIntegral                                                  );

$obFormulario->addSpan      ( $obSpnListaNotasFiscal                                                   );
$obFormulario->addComponente( $obLblTotalPrestacaoContas                                               );

$obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );
$obFormulario->Show();

$stLink = "&inCodEntidade=".$_REQUEST['inCodEntidade'];
$stLink.= "&inCodEmpenho=".$_REQUEST['inCodEmpenho'];
$stLink.= "&stAcao=".$request->get('stAcao');;

$stJs     = "ajaxJavaScript('".$pgOcul."?".Sessao::getId().$stLink."','montaListaPrestacaoContas');";
$jsOnLoad = $stJs;
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
