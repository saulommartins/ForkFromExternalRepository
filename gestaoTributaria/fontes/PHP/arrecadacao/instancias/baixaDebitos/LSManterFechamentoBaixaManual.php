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
    * Página de Formulario para listar registros de pagamentos
    * Data de Criação   : 24/05/2006

    * @author Analista      : Fabio Bertoldi Rodrigues
    * @author Desenvolvedor : Fernando Piccini Cercato

    * @ignore

    * $Id: LSManterFechamentoBaixaManual.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.03.10
*/

/*
$Log$
Revision 1.5  2006/09/15 10:55:12  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterBaixaManual";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FMManterFechamentoBaixaManual.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write ( "link", "" );

$obRMONAgencia = new RMONAgencia();
$rsBanco = new recordSet();
$rsAgencia = new recordSet();

//DEFINICAO DOS COMPONENTES
$obHdnTodosBancos = new Hidden;
$obHdnTodosBancos->setName( "inTodosBancos" );
$obHdnTodosBancos->setValue( $_REQUEST["inTodosBancos"] );

if ($_REQUEST["inTodosBancos"]) {
    $obRARRPagamento = new RARRPagamento;
    $obRARRPagamento->listarPagamentosManuais( $rsLista );

    $dtdiaHOJE = date ("d-m-Y");
    $arBancos = array();
    $inTotalBancos = 0;
    $rsLista->ordena( "cod_banco" );
    while ( !$rsLista->eof() ) {
        $boBancoNaLista = false;
        for ($inX=0; $inX<$inTotalBancos; $inX++) {
            if ( $arBancos[$inX]["cod_banco"] == $rsLista->getCampo("cod_banco") &&
               $arBancos[$inX]["cod_agencia"] == $rsLista->getCampo("cod_agencia") ) {
                $arBancos[$inX]["total_pago"] += $rsLista->getCampo("valor_pago");
                $boBancoNaLista = true;
                break;
            }
        }

        if (!$boBancoNaLista) {
            $obRMONAgencia = new RMONAgencia;
            $obRMONAgencia->obRMONBanco->setCodBanco( $rsLista->getCampo("cod_banco") );
            $obRMONAgencia->setCodAgencia( $rsLista->getCampo("cod_agencia") );
            $obErro = $obRMONAgencia->consultarAgencia( $rsDadosAgencia );
            if ( !$obErro->ocorreu() ) {
                $arBancos[$inTotalBancos]["total_pago"] = $rsLista->getCampo("valor_pago");

                $arBancos[$inTotalBancos]["cod_banco"] = $rsLista->getCampo("cod_banco");
                $arBancos[$inTotalBancos]["cod_agencia"] = $rsLista->getCampo("cod_agencia");

                $arBancos[$inTotalBancos]["num_banco"] = $obRMONAgencia->obRMONBanco->getNumBanco();
                $arBancos[$inTotalBancos]["num_agencia"] = $obRMONAgencia->getNumAgencia();
                $arBancos[$inTotalBancos]["nom_banco"] = $obRMONAgencia->obRMONBanco->getNomBanco();
                $arBancos[$inTotalBancos]["nom_agencia"] = $obRMONAgencia->getNomAgencia();
                $arBancos[$inTotalBancos]["data_fechamento"] = $dtdiaHOJE;
                $inTotalBancos++;
            }
        }

        $rsLista->proximo();
    }

    $rsLista = new RecordSet;
    $rsLista->preenche( $arBancos );
    $rsLista->addFormatacao("total_pago","NUMERIC_BR");

    $obLista = new Lista;
    $obLista->setRecordSet ( $rsLista  );
    $obLista->setTitulo ( "Registros de Pagamento" );
    $obLista->setMostraPaginacao ( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Banco");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Agência");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Data de Fechamento");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo          ( "[num_banco] - [nom_banco]" );
    $obLista->ultimoDado->setAlinhamento    ( "CENTRO"                  );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo          ( "[num_agencia] - [nom_agencia]" );
    $obLista->ultimoDado->setAlinhamento    ( "CENTRO"  );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo          ( "data_fechamento" );
    $obLista->ultimoDado->setAlinhamento    ( "CENTRO"  );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo          ( "total_pago" );
    $obLista->ultimoDado->setAlinhamento    ( "CENTRO"  );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "Visualizar" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:visualizarRegistro();" );
    $obLista->ultimaAcao->addCampo( "1", "num_banco" );
    $obLista->ultimaAcao->addCampo( "2", "num_agencia" );
    $obLista->commitAcao();

    $obLista->show();
} else {
    $obHdnAgencia = new Hidden;
    $obHdnAgencia->setName( "inNumAgencia" );
    $obHdnAgencia->setValue( $_REQUEST["inNumAgencia"] );

    $obHdnBanco = new Hidden;
    $obHdnBanco->setName( "inNumbanco" );
    $obHdnBanco->setValue( $_REQUEST["inNumbanco"] );

    $obRMONAgencia->obRMONBanco->setNumBanco ( $_REQUEST['inNumbanco'] );
    $obRMONAgencia->setNumAgencia( $_REQUEST['inNumAgencia'] );
    $obRMONAgencia->consultarAgencia( $rsLista );

    $obLblBanco = new Label;
    $obLblBanco->setName   ( 'labelBanco' );
    $obLblBanco->setTitle  ( 'Banco.' );
    $obLblBanco->setRotulo ( 'Banco' );
    $obLblBanco->setValue  ( $_REQUEST['inNumbanco'] . ' - ' . $obRMONAgencia->obRMONBanco->getNomBanco() );

    $obLblAgencia = new Label;
    $obLblAgencia->setName   ( 'labelAgencia' );
    $obLblAgencia->setTitle  ( 'Agência.' );
    $obLblAgencia->setRotulo ( 'Agência' );
    $obLblAgencia->setValue  ( $_REQUEST['inNumAgencia'] . ' - ' . $obRMONAgencia->getNomAgencia() );
}

$obSpnLista = new Span;
$obSpnLista->setID("spnLista");

$obSpnListaBanco = new Span;
$obSpnListaBanco->setID("spnListaBanco");

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obFormulario = new Formulario;
$obFormulario->addForm  ( $obForm          );
$obFormulario->addHidden( $obHdnCtrl       );
$obFormulario->addHidden( $obHdnAcao       );
$obFormulario->addHidden( $obHdnTodosBancos );

if ($_REQUEST["inTodosBancos"]) {
    //$obFormulario->addComponenteComposto ( $obTxtBanco, $obCmbBanco );
    //$obFormulario->addComponenteComposto ( $obTxtAgencia, $obCmbAgencia );
    $obFormulario->addSpan ( $obSpnListaBanco );
} else {
    $obFormulario->addHidden( $obHdnAgencia );
    $obFormulario->addHidden( $obHdnBanco );
    $obFormulario->addComponente ( $obLblBanco );
    $obFormulario->addComponente ( $obLblAgencia );
}

$obFormulario->addSpan ( $obSpnLista );
$obFormulario->Cancelar();
$obFormulario->show();

sistemaLegado::executaFrameOculto("buscaValor('AgenciaLista')");
?>
