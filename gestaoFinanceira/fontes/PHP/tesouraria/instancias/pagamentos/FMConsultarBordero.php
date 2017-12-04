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
    * Página de Formulário para Arrecadação Receita
    * Data de Criação   : 06/01/2006

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 30835 $
    $Name$
    $Autor:$
    $Date: 2007-04-30 16:21:28 -0300 (Seg, 30 Abr 2007) $

    * Casos de uso: uc-02.04.20,uc-02.03.28

*/

/*
$Log$
Revision 1.7  2007/04/30 19:21:27  cako
implementação uc-02.03.28

Revision 1.6  2007/03/30 21:58:02  cako
Bug #7884#

Revision 1.5  2006/07/05 20:39:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CLA_IAPPLETTERMINAL                                                                   );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"                                     );
include_once( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );
include_once( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarBordero";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//include_once( $pgJs );
$arFiltro = Sessao::read('filtro');

$stFiltro = '';
if ( count($arFiltro) > 0 ) {
    $stFiltro = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        if ( is_array($stValor) ) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                 if ( is_array($stValor2) ) {
                    foreach ($stValor2 as $stCampo3 => $stValor3) {
                        $stFiltro .= "&".$stCampo3."=".@urlencode( $stValor3 );
                    }
                 } else {
                    $stFiltro .= "&".$stCampo2."=".@urlencode( $stValor2 );
                 }
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->addBordero();

$obRTesourariaBoletim->roUltimoBordero->setCodBordero($_REQUEST['inCodBordero']);
$obRTesourariaBoletim->roUltimoBordero->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
$obRTesourariaBoletim->roUltimoBordero->setExercicio($_REQUEST['stExercicio']);
$stOrdensPagamento = $obRTesourariaBoletim->roUltimoBordero->getListaOP();
$obRTesourariaBoletim->roUltimoBordero->setCodOrdem ( $stOrdensPagamento );
$obRTesourariaBoletim->roUltimoBordero->consultar( $rsBordero );

if ($_REQUEST['stTipoBordero']=="T") {

    $obRTesourariaBoletim->roUltimoBordero->addTransacaoTransferencia();
    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoTransferencia->roRTesourariaBordero->setCodBordero($_REQUEST['inCodBordero']);
    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoTransferencia->roRTesourariaBordero->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoTransferencia->roRTesourariaBordero->setExercicio($_REQUEST['stExercicio']);
    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoTransferencia->listar( $rsTransacao );
} elseif ($_REQUEST['stTipoBordero']=="P") {

    $obRTesourariaBoletim->roUltimoBordero->addTransacaoPagamento();
    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->roRTesourariaBordero->setCodBordero($_REQUEST['inCodBordero']);
    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->roRTesourariaBordero->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->roRTesourariaBordero->setExercicio($_REQUEST['stExercicio']);
    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->listar( $rsTransacao );
}
$inCount = 0;

$arDados = array();

$valorTotal = 0;

while (!$rsTransacao->eof()) {

    switch ($rsTransacao->getCampo("cod_tipo")) {

        case "2":
                $tipo = "TCC";
            break;

        case "3":
                $tipo = "TCP";
            break;

        case "4":
                $tipo = "DOC";
            break;

        case "5":
                $tipo = "TED";
            break;

        default:
                $tipo = "TCC";
            break;
    }

    if ($_REQUEST['stTipoBordero']=="T") {

        $credor    =  $rsTransacao->getCampo("numcgm_transferencia")  ." - ". $rsTransacao->getCampo("nom_cgm_transferencia");
        $banco     =  $rsTransacao->getCampo("num_banco_transferencia") ." / ".$rsTransacao->getCampo("num_agencia_transferencia") ." / ".$rsTransacao->getCampo("conta_corrente_transferencia");
        $valor     =  $rsTransacao->getCampo("valor");
        $documento =  $rsTransacao->getCampo("documento");
    } else {

        $credor    =  $rsTransacao->getCampo("num_cgm_pagamento")  ." - ". $rsTransacao->getCampo("nom_cgm_pagamento");
        $banco     =  "Banco: ".$rsTransacao->getCampo("num_banco_pagamento") ."<br>Ag.: ".$rsTransacao->getCampo("num_agencia_pagamento") ."<br>CC:".$rsTransacao->getCampo("conta_corrente_pagamento");
        $valor     =  $rsTransacao->getCampo("vl_pagamento");
        $documento =  $rsTransacao->getCampo("documento");
    }
    $arDados[$inCount]['tipo']       = $tipo;
    $arDados[$inCount]['credor']     = $credor;
    $arDados[$inCount]['banco']      = $banco;
    $arDados[$inCount]['valor']      = number_format($valor,"2",",",".");
    $arDados[$inCount]['documento']  = $documento;

    $valorTotal += $valor;

    $rsTransacao->proximo();

    $inCount++;

}

$arDados[$inCount]['tipo']       = "";
$arDados[$inCount]['credor']     = "";
$arDados[$inCount]['banco']      = "Total do Borderô";
$arDados[$inCount]['valor']      = "";
$arDados[$inCount]['documento']  = number_format($valorTotal,"2",",",".");

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obForm = new Form;
$obForm->setAction ( $pgProc    );
$obForm->setTarget ( "oculto"   );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao"   );
$obHdnAcao->setValue( $stAcao   );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl"   );
$obHdnCtrl->setValue( ""        );

$obApplet = new IAppletTerminal( $obForm );

// Define Objeto Select para Entidade

$obLblNumBordero = new Label;
$obLblNumBordero->setRotulo( "Número Borderô"       );
$obLblNumBordero->setId    ( "stNumBordero"           );
$obLblNumBordero->setValue ( str_pad($rsBordero->getCampo("cod_bordero"), 3, "0", STR_PAD_LEFT) ." / ". $rsBordero->getCampo("exercicio_bordero") );

$obLblDtBordero = new Label;
$obLblDtBordero->setRotulo( "Data do Borderô"       );
$obLblDtBordero->setId    ( "stDtBordero"           );
$obLblDtBordero->setValue ( $rsBordero->getCampo("dt_bordero")  );

$obLblEntidade = new Label;
$obLblEntidade->setRotulo( "Entidade"       );
$obLblEntidade->setId    ( "stEntidade"           );
$obLblEntidade->setValue ( $rsBordero->getCampo("nom_cgm")  );

$obLblNumBoletim = new Label;
$obLblNumBoletim->setRotulo( "Número Boletim"       );
$obLblNumBoletim->setId    ( "stNumBoletim"           );
$obLblNumBoletim->setValue ( str_pad($rsBordero->getCampo("cod_boletim"), 3, "0", STR_PAD_LEFT) ." / ". substr($rsBordero->getCampo("exercicio_boletim"),2,4)   );

$obLblDtBoletim = new Label;
$obLblDtBoletim->setRotulo( "Data do Boletim"       );
$obLblDtBoletim->setId    ( "stDtBoletim"           );
$obLblDtBoletim->setValue ( $rsBordero->getCampo("dt_boletim")  );

$obLblContaPagadora = new Label;
$obLblContaPagadora->setRotulo( "Conta Pagadora"       );
$obLblContaPagadora->setId    ( "stContaPagadora"           );
$obLblContaPagadora->setValue ( $rsBordero->getCampo("cod_plano") . " / " . $rsBordero->getCampo("nom_banco")  );

$obLblRecurso = new Label;
$obLblRecurso->setRotulo( "Recurso"       );
$obLblRecurso->setId    ( "stRecurso"           );
$obLblRecurso->setValue ( $rsBordero->getCampo("cod_recurso") . " - " . $rsBordero->getCampo("nom_recurso")  );

$inCount_ = 0;
while (!$rsBordero->eof()) {
    if ($rsBordero->getCampo('vl_retencoes') != 0.00) {
        $boRetencoes = true;
        $arRetencoes[$inCount_]['cod_ordem'] = $rsBordero->getCampo('cod_ordem');
        $arRetencoes[$inCount_]['exercicio'] = $rsBordero->getCampo('exercicio_ordem');
        $arRetencoes[$inCount_]['credor']    = $rsBordero->getCampo('num_credor')." - ".$rsBordero->getCampo('credor');
        $arRetencoes[$inCount_]['valor_op']  = bcadd($rsBordero->getCampo('vl_pagamento'), $rsBordero->getCampo('vl_retencoes'),2);
        $arRetencoes[$inCount_]['valor_ret'] = $rsBordero->getCampo('vl_retencoes');
        $arRetencoes[$inCount_]['valor_liq'] = $rsBordero->getCampo('vl_pagamento');
        $inCount_++;
    }
    $rsBordero->proximo();
}

if ($boRetencoes) {
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche($arRetencoes);
    $rsRecordSet->addFormatacao('valor_op','NUMERIC_BR');
    $rsRecordSet->addFormatacao('valor_ret','NUMERIC_BR');
    $rsRecordSet->addFormatacao('valor_liq','NUMERIC_BR');

    $obLista = new Lista;
    $obLista->setTitulo ( "Retençoes de OP ");
    $obLista->setRecordSet ($rsRecordSet );
    $obLista->setMostraPaginacao( false );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "OP" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Credor" );
    $obLista->ultimoCabecalho->setWidth( 45 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Valor OP" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Retenções" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Valor Líquido OP" );
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_ordem] / [exercicio]" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[credor]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[valor_op]" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[valor_ret]" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[valor_liq]" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    $obLista->montaInnerHTML();

    $obSpanRetencoes = new Span;
    $obSpanRetencoes->setId ('spnRet');
    $obSpanRetencoes->setValue( $obLista->getHTML() );
}

$rsRecordSet = new RecordSet;
$rsRecordSet->preenche($arDados);

$obLista = new Lista;
$obLista->setTitulo( "Dados do Borderô" );
$obLista->setRecordSet( $rsRecordSet );
$obLista->setMostraPaginacao( false );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo" );
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Credor" );
$obLista->ultimoCabecalho->setWidth( 38 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Dados Bancários" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Valor" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "NF/Docum." );
$obLista->ultimoCabecalho->setWidth( 12 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "tipo" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "credor" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "banco" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "valor" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "documento" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->montaHTML();
$stHTML = $obLista->getHTML();
$stHTML = str_replace( "\n" ,"" ,$stHTML );
$stHTML = str_replace( "  " ,"" ,$stHTML );
$stHTML = str_replace( "'","\\'",$stHTML );
$stHTML = str_replace( "\\\'","\\'",$stHTML );

$obSpanLista = new Span;
$obSpanLista->setId( "spnLista" );
$obSpanLista->setValue( $stHTML );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;

$obVoltar = new Button;
$obVoltar->setName  ( "Voltar" );
$obVoltar->setValue ( "Voltar" );
$obVoltar->obEvento->setOnClick("Cancelar('".$stLocation."');");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo     ( "Dados da Consulta do Borderô" );
$obFormulario->addForm       ( $obForm                  );

$obFormulario->addComponente ( $obLblNumBordero );
$obFormulario->addComponente ( $obLblDtBordero );
$obFormulario->addComponente ( $obLblEntidade );
$obFormulario->addComponente ( $obLblNumBoletim );
$obFormulario->addComponente ( $obLblDtBoletim );
$obFormulario->addComponente ( $obLblContaPagadora );
$obFormulario->addComponente ( $obLblRecurso );
if($boRetencoes)
    $obFormulario->addSpan   ( $obSpanRetencoes );
$obFormulario->addSpan       ( $obSpanLista );
$obFormulario->definebarra( Array( $obVoltar ));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
