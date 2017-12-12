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
    * Formulario para Empenho - Ordem de Pagamento
    * Data de Criação   : 29/03/2005

    * @author Analista: Diego B. Victória
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: luciano $
    $Date: 2007-09-03 12:45:47 -0300 (Seg, 03 Set 2007) $

    * Casos de uso: uc-02.03.23
*/

/*
$Log$
Revision 1.21  2007/09/03 15:44:00  luciano
Bug#9663#

Revision 1.20  2007/09/03 13:14:24  luciano
Bug#9663#

Revision 1.19  2007/08/27 19:18:19  luciano
Bug#9663#

Revision 1.18  2007/08/16 15:54:59  luciano
Bug#9663#,Bug#9921#

Revision 1.17  2007/08/14 14:30:06  luciano
Bug#9663#

Revision 1.16  2007/05/30 13:12:18  luciano
#9090#

Revision 1.15  2007/04/05 20:08:03  cako
Bug #9022#

Revision 1.14  2007/04/05 15:16:08  cako
Bug #8996#

Revision 1.13  2006/11/25 15:12:01  cleisson
Bug #7589#

Revision 1.12  2006/10/20 21:40:08  cako
Bug #6995#

Revision 1.11  2006/10/11 17:28:35  cako
Ajustes

Revision 1.10  2006/09/28 16:37:10  eduardo
Bug #7060#

Revision 1.9  2006/07/21 19:10:54  jose.eduardo
Bug #6616#

Revision 1.8  2006/07/14 14:33:42  leandro.zis
Bug #6193#

Revision 1.7  2006/07/05 20:48:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoPagamentoLiquidacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "ManterEstornoPagamentoOrdem";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}
$stFiltro = "";
$arFiltro = Sessao::read('filtro');
foreach ($arFiltro as $key => $value) {
    $stFiltro .= '&'.$key.'='.$value;
}

// DEFINE OBJETOS DAS CLASSES
$obREmpenhoPagamentoLiquidacao = new REmpenhoPagamentoLiquidacao;
$obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setCodigoOrdem ( $_REQUEST["inCodigoOrdem"] );
$obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setExercicio( $_REQUEST['stExercicio'] );
$obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodigoEntidade"] );
$obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setCodigoOrdem( $_REQUEST["inCodigoOrdem"] );
$obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setEstorno( true );
$obREmpenhoPagamentoLiquidacao->listarNotaLiquidacaoPaga( $rsLiquidacaoPaga );

$obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->consultar();
$obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->consultarValorAPagar();

$boAdiantamento = false;
if ($obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getAdiantamento()) {
    $boAdiantamento = true;
}

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName         ( "stCtrl"                      );
$obHdnCtrl->setValue        ( $_REQUEST["stCtrl"]           );

$obHdnAcao = new Hidden;
$obHdnAcao->setName         ( "stAcao"                      );
$obHdnAcao->setValue        ( $_REQUEST["stAcao"]           );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName    ( "stExercicio"                 );
$obHdnExercicio->setValue   ( $_REQUEST["stExercicio"]      );

$obHdnExercicioEmp = new Hidden;
$obHdnExercicioEmp->setName  ( "stExercicioEmpenho"               );
$obHdnExercicioEmp->setValue ( $_REQUEST["stExercicioEmpenho"]    );

$obHdnCodOrdem = new Hidden;
$obHdnCodOrdem->setName     ( "inCodigoOrdem"               );
$obHdnCodOrdem->setValue    ( $_REQUEST["inCodigoOrdem"]    );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName  ( "inCodigoEntidade"            );
$obHdnCodEntidade->setValue ( $_REQUEST["inCodigoEntidade"] );

$obHdnCodRecurso = new Hidden;
$obHdnCodRecurso->setName   ( "inCodigoRecurso"             );
$obHdnCodRecurso->setValue  ( ""                            );

$obHdnAdiantamento = new Hidden;
$obHdnAdiantamento->setId    ( "boAdiantamento"            );
$obHdnAdiantamento->setName  ( "boAdiantamento"            );
$obHdnAdiantamento->setValue ( $boAdiantamento );

$obVlPrestado = new Hidden;
$obVlPrestado->setId         ( "nuValorPrestado"           );
$obVlPrestado->setName       ( "nuValorPrestado"           );

// DEFINE OBJETOS DO FORMULARIO
$obLblEntidade = new Label;
$obLblEntidade->setRotulo   ( "Entidade"                    );
$obLblEntidade->setName     ( "stEntidade"                  );
$obLblEntidade->setValue    ( $_REQUEST["inCodigoEntidade"]." - ".$_REQUEST["stNomeEntidade"] );

$obLblNumeroOrdem = new Label;
$obLblNumeroOrdem->setRotulo( "Número da Ordem"             );
$obLblNumeroOrdem->setName  ( "inCodigoOrdem"               );
$obLblNumeroOrdem->setValue ( $_REQUEST["inCodigoOrdem"]."/".$_REQUEST["stExercicio"] );

$obLblValorTotal = new Label;
$obLblValorTotal->setName   ( "flValorTotal"                );
$obLblValorTotal->setRotulo ( "Valor Pago"                       );
$obLblValorTotal->setValue  ( number_format($obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getValorPago(), 2, ",", ".") );

/* MELHORIA
$obLblValorPrestado = new Label;
$obLblValorPrestado->setName   ( "flValorPrestado"                );
$obLblValorPrestado->setRotulo ( "Valor Prestado Contas"          );
$obLblValorPrestado->setValue  ( number_format($rsLiquidacaoPaga->getCampo('vl_prestado'), 2, ",", ".") );
*/

$obLblFornecedor = new Label;
$obLblFornecedor->setRotulo ( "Fornecedor"                  );
$obLblFornecedor->setName   ( "stFornecedor"                );
$obLblFornecedor->setValue  ( $_REQUEST["inNumCGM"]." - ".$_REQUEST["stNomeCGM"] );

/*$obHdnDataPagamento = new Hidden;
$obHdnDataPagamento->setName   ( "stDtPagamento"            );
$obHdnDataPagamento->setId     ( "stDtPagamento"            );
*/
/*
$obLblDataPagamento = new Label;
$obLblDataPagamento->setRotulo ( "Data do Pagamento"        );
$obLblDataPagamento->setValue  ( $stDtPagamento             );
*/
/*$obLblContaBanco = new Label;
$obLblContaBanco->setRotulo ( "*Conta Banco"                       );
$obLblContaBanco->setName   ( "stContaBanco"                      );
$obLblContaBanco->setValue  ( $stContaBanco . " - " . $stNomConta );

$obHdnContaBanco = new Hidden;
$obHdnContaBanco->setName    ( "inCodContaBanco"            );
$obHdnContaBanco->setValue   ( $stContaBanco                ); */

$obSpnListaItem = new Span;
$obSpnListaItem->setID("spnListaItem");

$stDtAnulacao = date( 'd/m/Y' );
$obDtAnulacao = new Data;
$obDtAnulacao->setName   ( "stDtAnulacao"     );
$obDtAnulacao->setId     ( "stDtAnulacao"     );
$obDtAnulacao->setValue  ( $stDtAnulacao      );
$obDtAnulacao->setRotulo ( "Data do Estorno" );
$obDtAnulacao->setTitle  ( "Informe a data do Estorno." );
$obDtAnulacao->setNull   ( false               );

// Define objeto TextArea para Motivo da Anulação
$obTxtObservacao = new TextArea;
$obTxtObservacao->setId       ( "stObservacao" );
$obTxtObservacao->setName     ( "stObservacao" );
$obTxtObservacao->setValue    ( $stObservacao  );
$obTxtObservacao->setRotulo   ( "Observação"   );
$obTxtObservacao->setTitle    ( "Informe a observação." );
$obTxtObservacao->setCols     ( 100        );
$obTxtObservacao->setRows     ( 3          );

if ($_REQUEST["stAcao"] == "estornar") {
    $js = "recuperaItem();";
    SistemaLegado::executaFramePrincipal($js);
}

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm              );
$obFormulario->addHidden     ( $obHdnCtrl           );
$obFormulario->addHidden     ( $obHdnAcao           );
$obFormulario->addHidden     ( $obHdnExercicio      );
$obFormulario->addHidden     ( $obHdnExercicioEmp   );
$obFormulario->addHidden     ( $obHdnCodEntidade    );
$obFormulario->addHidden     ( $obHdnCodOrdem       );
$obFormulario->addHidden     ( $obHdnCodRecurso     );
$obFormulario->addHidden     ( $obHdnAdiantamento   );
$obFormulario->addHidden     ( $obVlPrestado        );
//$obFormulario->addHidden     ( $obHdnContaBanco     );
$obFormulario->addTitulo     ( "Dados da ordem"     );
$obFormulario->addComponente ( $obLblEntidade       );
$obFormulario->addComponente ( $obLblNumeroOrdem    );
$obFormulario->addComponente ( $obLblValorTotal     );
/* Melhoria
if ($boAdiantamento) {
    $obFormulario->addComponente ( $obLblValorPrestado  );
}
*/
$obFormulario->addComponente ( $obLblFornecedor     );
//$obFormulario->addComponente ( $obLblDataPagamento  );
//$obFormulario->addHidden     ( $obHdnDataPagamento  );
$obFormulario->addSpan       ( $obSpnListaItem      );
//$obFormulario->addComponente ( $obLblContaBanco     );
$obFormulario->addComponente ( $obDtAnulacao        );
$obFormulario->addComponente ( $obTxtObservacao     );
$stLocation = $pgList . '?' . Sessao::getId() . $stFiltro.'&stAcao='.$stAcao;
//$obFormulario->Cancelar($stLocation);

$obOk = new Ok();
$obOk->obEvento->setOnClick("
        var erro = false;
        if ( document.getElementById('nuValorPrestado').value > 0 ) {
            if ( confirm('Este empenho é de adiantamentos/subvenções, caso seja estornado não poderá ser pago novamente. Deseja continuar?')) { \n
                  erro = false;      \n
            } else { erro = true; } \n
        }
        if ( Valida() && erro == false) {
            Salvar();
        }");
$obCancelar = new Button();
$obCancelar->setValue ("Cancelar");
$obCancelar->obEvento->setOnclick("Cancelar('".$stLocation."');");

$obFormulario->defineBarra ( Array( $obOk, $obCancelar));

$obFormulario->show ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
