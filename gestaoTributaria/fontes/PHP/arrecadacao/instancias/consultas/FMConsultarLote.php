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
    * Formulario de Consulta de Lotes de Pagamentos
    * Data de Criação   : 22/12/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: FMConsultarLote.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.20  2007/08/20 14:35:09  dibueno
Bug#9959#

Revision 1.19  2007/08/08 15:16:18  dibueno
Bug#9852#

Revision 1.18  2007/08/02 21:22:53  dibueno
*** empty log message ***

Revision 1.17  2007/05/02 18:27:33  cercato
Bug #9138#

Revision 1.16  2007/02/09 11:29:33  dibueno
Bug #8341#

Revision 1.15  2007/02/08 18:03:32  dibueno
Bug #8341#

Revision 1.14  2006/09/15 11:04:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRLancamento.class.php"                                             );
include_once( CAM_GT_ARR_NEGOCIO."RARRParcela.class.php"                                                );
include_once( CAM_GT_MON_NEGOCIO."RMONCredito.class.php"                                               );
include_once( CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php"                                          );
include_once( CAM_GA_CGM_NEGOCIO . "RCGM.class.php"     );

//$boPaginacao = true;

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarLote";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

include_once($pgJs);
$obRCGM                     = new RCGM;
$obRARRPagamento    = new RARRPagamento;

// passar do request pra variaveis
$inCodLote          = $_REQUEST["inCodLote"   ];
$inCgmResponsavel   = $_REQUEST["inNumCgm"        ];
$stNomResponsavel   = $_REQUEST["inNomCgm"        ];
$inCgmContribuinte  = $_REQUEST["cgm_contribuinte"];
$stNomContribuinte  = $_REQUEST["nom_contribuinte"];
$dtDataLote         = $_REQUEST['dtDataLote'];
$dtDataBaixa        = $_REQUEST['dtDataBaixa'];
$inNumBanco         = $_REQUEST['inNumBanco'];
$stNomBanco         = $_REQUEST['stNomBanco'];
$inNumAgencia       = $_REQUEST['inNumAgencia'];
$stNomAgencia       = $_REQUEST['stNomAgencia'];
$stNomTipo          = $_REQUEST['stNomTipo'];
$stExercicio        = $_REQUEST['stExercicio'];
$stNomArquivo       = $_REQUEST["stNomArquivo"];
$flValorLote        = $_REQUEST["flValorLote"];

$arDadosLote[0] = array(
    "cod_lote" => $inCodLote
    , "data" => $dtDataLote
    , "responsavel_cgm" => $inCgmResponsavel
    , "responsavel_cgmnome" => $stNomResponsavel
    , "num_banco" => $inNumBanco
    , "nom_banco" => $stNomBanco
    , "num_agencia" => $inNumAgencia
    , "nom_agencia" => $stNomAgencia
    , "tipo" => $stNomTipo
    , "cgm_contribuinte" => $inCgmContribuinte
    , "ocorrencia_pagamento" =>  $_REQUEST['inOcorrenciaPagamento']
    , "nom_arquivo" => $stNomArquivo
    , "valor_lote" => $flValorLote
);

$rsDadosLote = new RecordSet;
$rsDadosLote->preenche( $arDadosLote );

// HIDDENS
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnNumCgm = new Hidden;
$obHdnNumCgm->setName    ( "inNumCgm"   );
$obHdnNumCgm->setValue   ( $inNumCgm    );

$obHdnCodGrupo = new Hidden;
$obHdnCodGrupo->setName    ( "inCodGrupo"   );
$obHdnCodGrupo->setValue   ( $inCodGrupo    );

// COMPONENTES
$obLabelLote = new Label;
$obLabelLote->setRotulo ( "Lote"                );
$obLabelLote->setValue ( $inCodLote );

$obLabelNomeArquivo = new Label;
$obLabelNomeArquivo->setRotulo ( "Nome do Arquivo"   );
$obLabelNomeArquivo->setValue ( $stNomArquivo );

$obLabelDataLote = new Label;
$obLabelDataLote->setRotulo ( "Data do Lote" );
$obLabelDataLote->setValue  ( $dtDataLote );

$obLabelDataBaixa = new Label;
$obLabelDataBaixa->setRotulo ( "Data de Baixa"       );
$obLabelDataBaixa->setValue  ( $dtDataBaixa );

$obLabelValorLote = new Label;
$obLabelValorLote->setRotulo ( "Valor do Lote"       );
$obLabelValorLote->setValue  ( number_format ( $flValorLote, 2, ',', '.') );

$obLabelResponsavel = new Label;
$obLabelResponsavel->setRotulo ( "Responsável"                );
$obLabelResponsavel->setValue ( $inCgmResponsavel . ' - ' . $stNomResponsavel );

if ($inCgmContribuinte) {
    $obLabelContribuinte = new Label;
    $obLabelContribuinte->setRotulo ( "Filtro por Contribuinte"                );
    $obLabelContribuinte->setValue ( $inCgmContribuinte . ' - ' . $stNomContribuinte );
}

$obLabelBanco = new Label;
$obLabelBanco->setRotulo ( "Banco"                );
$obLabelBanco->setValue ( $inNumBanco . ' - ' . $stNomBanco );

$obLabelAgencia = new Label;
$obLabelAgencia->setRotulo ( "Agência"                );
$obLabelAgencia->setValue ( $inNumAgencia . ' - ' . $stNomAgencia );

$obLabelTipoBaixa = new Label;
$obLabelTipoBaixa->setRotulo ( "Tipo"                );
$obLabelTipoBaixa->setValue ( $stNomTipo );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm                   );
$obFormulario->addHidden    ( $obHdnAcao                );
$obFormulario->addHidden ( $obHdnCtrl );

$obFormulario->addTitulo    ( "Dados para Emissão"      );
$obFormulario->addHidden    ( $obHdnNumCgm              );

$obFormulario->addComponente    ( $obLabelLote );
$obFormulario->addComponente    ( $obLabelDataLote );
$obFormulario->addComponente    ( $obLabelDataBaixa );
$obFormulario->addComponente    ( $obLabelResponsavel      );
$obFormulario->addComponente    ( $obLabelNomeArquivo   );
$obFormulario->addComponente    ( $obLabelBanco );
$obFormulario->addComponente    ( $obLabelAgencia );
$obFormulario->addComponente    ( $obLabelTipoBaixa );
if ($inCgmContribuinte) {
    $obFormulario->addComponente    ( $obLabelContribuinte );
}
$obFormulario->addComponente    ($obLabelValorLote);

$obFormulario->show();
flush();

$obRARRPagamento->setOcorrenciaPagamento ( $_REQUEST['inOcorrenciaPagamento'] );

//if ($_REQUEST['stNomTipo']) {
    //$obRARRPagamento->obRARRTipoPagamento->setNomeTipo ( $_REQUEST['stNomTipo'] );
//}
$obRARRPagamento->setCodLote ( $inCodLote );
if ($inCgmContribuinte) {
    $obRARRPagamento->obRMONAgencia->obRCGM->setNumCGM ( $inCgmContribuinte );
}

$obRARRPagamento->listarPagamentosLote ( $rsPagamentos );

$rsPagamentos->addFormatacao("valor_pago_normal", "NUMERIC_BR");
$rsPagamentos->addFormatacao("valor_pago_calculo", "NUMERIC_BR");
$rsPagamentos->addFormatacao("juros", "NUMERIC_BR");
$rsPagamentos->addFormatacao("multa", "NUMERIC_BR");
$rsPagamentos->addFormatacao("diferenca", "NUMERIC_BR");

Sessao::write( 'dadoslote', $rsDadosLote );

$obLista = new Lista;
$obLista->setRecordSet( $rsPagamentos );

$obLista->setTitulo             ( "Lista de Pagamentos do Lote"   );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Numeração");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Parcela");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Origem");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Inscrição");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Contribuinte");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor (R$)");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Juros (R$)");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Multa (R$)");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Diferença (R$)");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor Total (R$)");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Pagamento");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Situação");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numeracao]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[info_parcela]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[origem]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[inscricao]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "contribuinte" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[valor_pago_calculo]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[juros]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[multa]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[diferenca]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[valor_pago_normal]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[data_pagamento_br]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[nom_tipo]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->show();

$stFiltro = Sessao::read( 'filtro' );
$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;

$obButtonVoltar = new Button;
$obButtonVoltar->setName  ( "Voltar" );
$obButtonVoltar->setValue ( "Voltar" );
$obButtonVoltar->obEvento->setOnClick( "CancelarCL();");//"Cancelar('".$stLocation."');" );

Sessao::write('acao', 1474);
$stLocation2  = '../relatorios/OCGeraRelatorioResumoLote.php?'.Sessao::getId().'&stAcao='.$stAcao;
$stLocation2 .= '&inCodLoteInicio='.$inCodLote.'&stExercicio='.$stExercicio.'&tipo=Pagamento&stTipoRelatorio=analitico&descricao=Relatório de Baixa do Lote';

$stLocation3  = 'OCGeraRelatorioRegistrosLote.php?'.Sessao::getId().'&stAcao='.$stAcao.'&descricao=Relatório de Registros do Lote';

$obButtonRelatorio = new Button;
$obButtonRelatorio->setName  ( "Relatorio" );
$obButtonRelatorio->setValue ( "Relatorio de Baixa do Lote" );
$obButtonRelatorio->obEvento->setOnClick( "window.parent.frames['oculto'].location='".$stLocation2."';");

$obButtonRelatorioRegistros = new Button;
$obButtonRelatorioRegistros->setName  ( "Relatorio2" );
$obButtonRelatorioRegistros->setValue ( "Relatório de Registros do Lote" );
$obButtonRelatorioRegistros->obEvento->setOnClick( "window.parent.frames['oculto'].location='".$stLocation3."';");

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->defineBarra( array( $obButtonRelatorio, $obButtonRelatorioRegistros ), "left", "" );
$obFormulario->defineBarra( array( $obButtonVoltar), "left", "" );

$obFormulario->show()

?>
