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
    * Data de Criação   : 24/03/2005

    * @author Analista: Diego B. Victória
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2007-08-31 12:41:22 -0300 (Sex, 31 Ago 2007) $

    * Casos de uso: uc-02.03.23,uc-02.03.28
*/

/*
$Log$
Revision 1.28  2007/08/31 15:41:22  luciano
Bug#9663#

Revision 1.27  2007/08/27 19:18:47  luciano
Bug#9663#

Revision 1.26  2007/08/16 15:48:17  luciano
Bug#9921#

Revision 1.25  2007/08/14 14:29:37  luciano
Bug#9663#

Revision 1.24  2007/07/13 19:06:31  cako
Bug#9383#, Bug#9384#

Revision 1.23  2007/06/28 15:29:52  luciano
Bug#9108#

Revision 1.22  2007/06/20 18:22:30  cako
Bug#9378#

Revision 1.21  2007/05/18 14:45:58  luciano
#9108#

Revision 1.20  2007/04/30 19:20:46  cako
implementação uc-02.03.28

Revision 1.19  2006/09/28 09:51:34  eduardo
Bug #7060#

Revision 1.18  2006/07/14 14:33:42  leandro.zis
Bug #6193#

Revision 1.17  2006/07/05 20:48:56  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoOrdemPagamento.class.php" );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoPagamentoLiquidacao.class.php" );
//Define o nome dos arquivos PHP
$stPrograma      = "ManterPagamentoOrdem";
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
$obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
$obREmpenhoOrdemPagamento->obROrcamentoEntidade->setExercicio( Sessao::getExercicio()    );
$obREmpenhoOrdemPagamento->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm')  );
$obREmpenhoOrdemPagamento->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

$obREmpenhoOrdemPagamento->setCodigoOrdem ( $_REQUEST["inCodigoOrdem"] );
$obREmpenhoOrdemPagamento->setExercicio( $_REQUEST['stExercicio'] );
$obREmpenhoOrdemPagamento->obREmpenhoEmpenho->setExercicio($_REQUEST['stExercicioEmpenho']);
$obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodigoEntidade"] );
$obREmpenhoOrdemPagamento->consultar();

$boAdiantamento = false;
//Verifica se tem prestacao de contas pendentes
if ($obREmpenhoOrdemPagamento->getAdiantamento()) {
    $boAdiantamento = true;
    $jsOnLoad = "montaParametrosGET('verificaFornecedor');";
}

if ($obREmpenhoOrdemPagamento->getRetencao()) {
    if ($_REQUEST['stAcao'] == 'pagar') {
        SistemaLegado::exibeAviso('Esta OP possui retenções: O Pagamento não poderá ser parcial.','','');
        $arRetencoes = $obREmpenhoOrdemPagamento->getRetencoes();
        $inCount = 0;
        foreach ($arRetencoes as $item) {
            if($item['tipo'] == 'O')
                 $arRetencoesTemp[$inCount]['cod_plano'] = $item['cod_receita'];
            else $arRetencoesTemp[$inCount]['cod_plano'] = $item['cod_plano'];
            $arRetencoesTemp[$inCount]['vl_retencao']    = $item['vl_retencao'];
            $arRetencoesTemp[$inCount]['nom_conta']      = $item['nom_conta'];
            $arRetencoesTemp[$inCount]['tipo']           = $item['tipo'];
            $inCount++;
            $flValorRetencoes = bcadd($flValorRetencoes,$item['vl_retencao'],2);
        }

        $arRetencoes = $arRetencoesTemp;
    }
}

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl"            );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao"            );
$obHdnAcao->setValue ( $_REQUEST["stAcao"] );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName  ( "stExercicio"               );
$obHdnExercicio->setValue ( $_REQUEST["stExercicio"]    );

$obHdnExercicioEmp = new Hidden;
$obHdnExercicioEmp->setName  ( "stExercicioEmpenho"               );
$obHdnExercicioEmp->setValue ( $_REQUEST["stExercicioEmpenho"]    );

$obHdnCodOrdem = new Hidden;
$obHdnCodOrdem->setName  ( "inCodigoOrdem"              );
$obHdnCodOrdem->setValue ( $_REQUEST["inCodigoOrdem"]   );

$obHdnDataOrdem = new Hidden;
$obHdnDataOrdem->setName  ( "stDataOrdem"              );
$obHdnDataOrdem->setValue ( $obREmpenhoOrdemPagamento->getDataEmissao());

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName  ( "inCodigoEntidade"            );
$obHdnCodEntidade->setValue ( $_REQUEST["inCodigoEntidade"] );

$obHdnImplantado = new Hidden;
$obHdnImplantado->setName  ( "boImplantado"            );
$obHdnImplantado->setValue ( $_REQUEST['boImplantado'] );

$obHdnAdiantamento = new Hidden;
$obHdnAdiantamento->setName  ( "boAdiantamento"            );
$obHdnAdiantamento->setValue ( $boAdiantamento             );

$obHdnCodFornecedor = new Hidden;
$obHdnCodFornecedor->setName  ( "inCodFornecedor"          );
$obHdnCodFornecedor->setValue ( $_REQUEST['inNumCGM'] );

$obHdnCodRecurso = new Hidden;
$obHdnCodRecurso->setName  ( "inCodigoRecurso"  );
$obHdnCodRecurso->setValue ( ""                 );

// DEFINE OBJETOS DO FORMULARIO
$obLblEntidade = new Label;
$obLblEntidade->setRotulo      ( "Entidade"           );
$obLblEntidade->setName        ( "stEntidade"         );
$obLblEntidade->setValue       ( $_REQUEST["inCodigoEntidade"]." - ".$_REQUEST["stNomeEntidade"] );

$obLblNumeroOrdem = new Label;
$obLblNumeroOrdem->setRotulo   ( "Número da Ordem"    );
$obLblNumeroOrdem->setName     ( "inCodigoOrdem"      );
$obLblNumeroOrdem->setValue    ( $_REQUEST["inCodigoOrdem"]."/".$_REQUEST["stExercicio"] );

$obHdnDataOrdem = new Hidden;
$obHdnDataOrdem->setName     ( "stDataOrdem"      );
$obHdnDataOrdem->setValue    ( $obREmpenhoOrdemPagamento->getDataEmissao() );

$obLblDataOrdem = new Label;
$obLblDataOrdem->setRotulo   ( "Data da Ordem"    );
$obLblDataOrdem->setName     ( "stDataOrdem"      );
$obLblDataOrdem->setValue    ( $obREmpenhoOrdemPagamento->getDataEmissao() );

$obLblFornecedor = new Label;
$obLblFornecedor->setRotulo    ( "Fornecedor"         );
$obLblFornecedor->setName      ( "stFornecedor"       );
$obLblFornecedor->setValue     ( $_REQUEST["inNumCGM"]." - ".$_REQUEST["stNomeCGM"] );

$obLblValorTotal = new Label;
$obLblValorTotal->setName             ( "flValorTotal"                );
$obLblValorTotal->setRotulo           ( "Valor a Pagar"               );
$obLblValorTotal->setValue            ( number_format($_REQUEST["flValorTotal"], "2", ",", ".") );

if ($arRetencoes) {
    $inCountExt = 0;
    $inCountOrc = 0;
    $stListaExt = '';
    $stListaOrc = '';
    foreach ($arRetencoes as $item) {
        if ($item['tipo'] == 'O') {
            $arTmpRetOrc[$inCountOrc] = $item;
            $inCountOrc++;
        }
        if ($item['tipo'] == 'E') {
            $arTmpRetExt[$inCountExt] = $item;
            $inCountExt++;
        }
    }
    $obLblValorRetencoes = new Label;
    $obLblValorRetencoes->setRotulo( "Total de Retenções" );
    $obLblValorRetencoes->setValue ( number_format( $flValorRetencoes, 2, ',','.') );

    $obLblValorLiquido = new Label;
    $obLblValorLiquido->setRotulo( "Valor Líquido da OP" );
    $obLblValorLiquido->setValue ( number_format( bcsub($_REQUEST["flValorTotal"],$flValorRetencoes,2),2,',','.') );

    if (isset($arTmpRetOrc)) {
        $rsRecordSetOrc = new RecordSet;
        $rsRecordSetOrc->preenche($arTmpRetOrc);
        $rsRecordSetOrc->addFormatacao('vl_retencao','NUMERIC_BR');

        $obLista = new Lista;
        $obLista->setTitulo ( "Retenções Orçamentárias");
        $obLista->setRecordSet ($rsRecordSetOrc );
        $obLista->setMostraPaginacao( false );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Conta de Retenção" );
        $obLista->ultimoCabecalho->setWidth( 65 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor da Retenção" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[cod_plano] - [nom_conta]" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[vl_retencao]" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->montaInnerHTML();
        $stListaOrc = $obLista->getHTML();
    }

    if (isset($arTmpRetExt)) {
        $rsRecordSetExt = new RecordSet;
        $rsRecordSetExt->preenche($arTmpRetExt);
        $rsRecordSetExt->addFormatacao('vl_retencao','NUMERIC_BR');

        $obLista = new Lista;
        $obLista->setTitulo ( "Retenções Extra-Orçamentárias");
        $obLista->setRecordSet ($rsRecordSetExt );
        $obLista->setMostraPaginacao( false );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Conta de Retenção" );
        $obLista->ultimoCabecalho->setWidth( 65 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor da Retenção" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[cod_plano] - [nom_conta]" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[vl_retencao]" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->montaInnerHTML();
        $stListaExt = $obLista->getHTML();
    }

    $obSpnRetencoes = new Span;
    $obSpnRetencoes->setId ('spnRet');
    $obSpnRetencoes->setValue( $stListaOrc."".$stListaExt );
}

$obSpnListaItem = new Span;
$obSpnListaItem->setID("spnListaItem");

// Define Objeto BuscaInner para Conta
$obBscContaBanco = new BuscaInner;
$obBscContaBanco->setRotulo ( "Conta Pagadora"         );
$obBscContaBanco->setTitle  ( "Informe a conta pagadora."                    );
$obBscContaBanco->setNulL   ( false                 );
$obBscContaBanco->setId     ( "stContaBanco"        );
$obBscContaBanco->setValue  ( $stContaBanco         );
$obBscContaBanco->obCampoCod->setName       ( "inCodContaBanco" );
$obBscContaBanco->obCampoCod->setSize       ( 10                );
$obBscContaBanco->obCampoCod->setMaxLength  ( 5                 );
$obBscContaBanco->obCampoCod->setValue      ( $inCodContaBanco  );
$obBscContaBanco->obCampoCod->setAlign      ( "left"            );
$obBscContaBanco->obCampoCod->obEvento->setOnBlur("buscaDado('buscaContaBanco');");
$obBscContaBanco->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodContaBanco','stContaBanco','tes_pag&inCodEntidade=".$_REQUEST['inCodigoEntidade']."','".Sessao::getId()."','800','550');");

$obREmpenhoPagamentoLiquidacao = new REmpenhoPagamentoLiquidacao;
$obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade($_REQUEST["inCodigoEntidade"]);
$obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setDataEmissao($obREmpenhoOrdemPagamento->getDataEmissao());
$obREmpenhoPagamentoLiquidacao->setExercicio( Sessao::getExercicio() );
$obREmpenhoPagamentoLiquidacao->listarMaiorData( $rsMaiorData );

$stDtPagamento = $rsMaiorData->getCampo("data_op");
$obTxtDtPagamento = new Data;
$obTxtDtPagamento->setName   ( "stDtPagamento"     );
$obTxtDtPagamento->setId     ( "stDtPagamento"     );
$obTxtDtPagamento->setValue  ( $stDtPagamento      );
$obTxtDtPagamento->setRotulo ( "Data do Pagamento" );
$obTxtDtPagamento->setTitle   ( "Informe a data do pagamento." );
$obTxtDtPagamento->obEvento->setOnBlur( "validaDataPagamento();" );
if ($boAdiantamento) {
    $obTxtDtPagamento->obEvento->setOnChange( "montaParametrosGET('verificaFornecedor');");
}

// Define objeto TextArea para Motivo da Anulação
$obTxtObservacao = new TextArea;
$obTxtObservacao->setId       ( "stObservacao" );
$obTxtObservacao->setName     ( "stObservacao" );
$obTxtObservacao->setValue    ( $stObservacao  );
$obTxtObservacao->setRotulo   ( "Observação"   );
$obTxtObservacao->setTitle    ( "Informe a observação."   );
$obTxtObservacao->setCols     ( 100        );
$obTxtObservacao->setRows     ( 3          );
$obTxtObservacao->setMaxCaracteres    ( 170 );

if ($_REQUEST["stAcao"] == "pagar") {
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
$obFormulario->addHidden     ( $obHdnDataOrdem      );
$obFormulario->addHidden     ( $obHdnImplantado     );
$obFormulario->addHidden     ( $obHdnAdiantamento   );
$obFormulario->addHidden     ( $obHdnCodFornecedor  );
$obFormulario->addHidden     ( $obHdnCodRecurso     );
$obFormulario->addTitulo     ( "Dados da ordem"     );
$obFormulario->addComponente ( $obLblEntidade       );
$obFormulario->addComponente ( $obLblNumeroOrdem    );
$obFormulario->addComponente ( $obLblDataOrdem      );
$obFormulario->addComponente ( $obLblValorTotal     );
if ($arRetencoes) {
    $obFormulario->addComponente ( $obLblValorRetencoes );
    $obFormulario->addComponente ( $obLblValorLiquido   );
}
$obFormulario->addComponente ( $obLblFornecedor     );
if ($arRetencoes) {
    $obFormulario->addSpan       ( $obSpnRetencoes      );
}
$obFormulario->addSpan       ( $obSpnListaItem      );
$obFormulario->addComponente ( $obBscContaBanco     );
$obFormulario->addComponente ( $obTxtDtPagamento    );
$obFormulario->addComponente ( $obTxtObservacao     );
$stLocation = $pgList.'?'.Sessao::getId().$stFiltro;
$obFormulario->Cancelar( $stLocation );
$obFormulario->show ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
