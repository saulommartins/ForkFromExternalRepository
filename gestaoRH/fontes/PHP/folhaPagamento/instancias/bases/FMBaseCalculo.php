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
    * Formulário
    * Data de Criação: 06/08/2008

    * @author Desenvolvedor: Rafael Garbin

    * Casos de uso: uc-04.05.67

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoBases.class.php"                                 );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );

$stPrograma = 'BaseCalculo';
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

include_once $pgJs;

$stAcao    = $_REQUEST["stAcao"];
$stCtrl    = $request->get("stCtrl");
$inCodBase = $_REQUEST["inCodBase"];
$stLink    = "&stAcao=".$stAcao."&inCodBase=".$inCodBase;

$jsOnload = "executaFuncaoAjax('gerarSpanInfEventoBase', '".$stLink."'); executaFuncaoAjax('gerarSpanListaEvento', '".$stLink."');";

$boBloquea = false;
if (trim($stAcao) == "alterar") {
    //Seta dados form
    $stFiltro = " WHERE cod_base = ".$inCodBase;
    $obTFolhaPagamentoBases = new TFolhaPagamentoBases();
    $obTFolhaPagamentoBases->recuperaTodos( $rsBases, $stFiltro );

    $stNomBase  = $rsBases->getCampo('nom_base');
    $boTipoBase = $rsBases->getCampo('tipo_base');
    $boApresetacaoValor   = $rsBases->getCampo('apresentacao_valor') == 'f' ? 'N' : 'S';
    $boInsercaoAutomatica = $rsBases->getCampo('insercao_automatica') == 'f' ? 'N' : 'S';

    $boBloquea = true;

    $obHdnInsercaoAutomatica = new Hidden;
    $obHdnInsercaoAutomatica->setName  ( "boInsercaoAutomatica" );
    $obHdnInsercaoAutomatica->setId    ( "boInsercaoAutomatica" );
    $obHdnInsercaoAutomatica->setValue ( $boInsercaoAutomatica  );
}

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( 'oculto' );

$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( $stCtrl );

$obHdnCodBase = new Hidden;
$obHdnCodBase->setName     ( "inCodBase" );
$obHdnCodBase->setValue    ( $inCodBase );

// Bases de Cálculo
$obTxtNomBase = new TextBox;
$obTxtNomBase->setRotulo            ( "Nome da base" );
$obTxtNomBase->setTitle             ( "Informe o nome da base de cálculo, sem espaços entre os nomes. Exemplo: BaseHorasExtras." );
$obTxtNomBase->setName              ( "stNomBase" );
$obTxtNomBase->setId                ( "stNomBase" );
$obTxtNomBase->setValue             ( $stNomBase );
$obTxtNomBase->setSize              ( 30 );
$obTxtNomBase->setMaxLength         ( 30 );
$obTxtNomBase->setNull              ( false );
$obTxtNomBase->setInteiro           ( false  );
$obTxtNomBase->setReadOnly          ( $boBloquea );

$obRdExibeBaseFFSim = new Radio();
$obRdExibeBaseFFSim->setName        ( "boApresetacaoValor" );
$obRdExibeBaseFFSim->setRotulo      ( "Apresentar valor da Base na Ficha Financeira" );
$obRdExibeBaseFFSim->setTitle       ( "Marque Sim, para que o valor da base de cálculo seja apresentado na ficha financeira do contrato." );
$obRdExibeBaseFFSim->setLabel       ( "Sim" );
$obRdExibeBaseFFSim->setValue       ( "S" );
$obRdExibeBaseFFSim->setDisabled    ( $boBloquea );
if ($boApresetacaoValor == 'S') {
    $obRdExibeBaseFFSim->setChecked ( true );
}

$obRdExibeBaseFFNao = new Radio();
$obRdExibeBaseFFNao->setName        ( "boApresetacaoValor" );
$obRdExibeBaseFFNao->setRotulo      ( "Apresentar valor da Base na Ficha Financeira" );
$obRdExibeBaseFFNao->setTitle       ( "Marque Sim, para que o valor da base de cálculo seja apresentado na ficha financeira do contrato." );
$obRdExibeBaseFFNao->setLabel       ( "Não" );
$obRdExibeBaseFFNao->setValue       ( "N" );
$obRdExibeBaseFFNao->setDisabled    ( $boBloquea );
if ($boApresetacaoValor != 'S') {
    $obRdExibeBaseFFNao->setChecked ( true );
}

$obRdInsercaoEventoAutomaticoSim = new Radio();
$obRdInsercaoEventoAutomaticoSim->setName        ( "boInsercaoAutomatica" );
$obRdInsercaoEventoAutomaticoSim->setRotulo      ( "Inserir automaticamente o evento da base" );
$obRdInsercaoEventoAutomaticoSim->setTitle       ( "Marque Sim, para que seja criado o evento de base automaticamente." );
$obRdInsercaoEventoAutomaticoSim->setLabel       ( "Sim" );
$obRdInsercaoEventoAutomaticoSim->setValue       ( "S" );
$obRdInsercaoEventoAutomaticoSim->setDisabled    ( $boBloquea );
$obRdInsercaoEventoAutomaticoSim->obEvento->setOnClick("montaParametrosGET('gerarSpanInfEventoBase', 'boInsercaoAutomatica,stAcao');");
if ($boInsercaoAutomatica != 'N') {
    $obRdInsercaoEventoAutomaticoSim->setChecked ( true );
}

$obRdInsercaoEventoAutomaticoNao = new Radio();
$obRdInsercaoEventoAutomaticoNao->setName        ( "boInsercaoAutomatica" );
$obRdInsercaoEventoAutomaticoNao->setRotulo      ( "Inserir automaticamente o evento da base" );
$obRdInsercaoEventoAutomaticoNao->setTitle       ( "Marque Sim, para que seja criado o evento de base automaticamente." );
$obRdInsercaoEventoAutomaticoNao->setLabel       ( "Não" );
$obRdInsercaoEventoAutomaticoNao->setValue       ( "N" );
$obRdInsercaoEventoAutomaticoNao->setDisabled    ( $boBloquea );
$obRdInsercaoEventoAutomaticoNao->obEvento->setOnClick("montaParametrosGET('gerarSpanInfEventoBase', 'boInsercaoAutomatica,stAcao');");
if ($boInsercaoAutomatica == 'N') {
    $obRdInsercaoEventoAutomaticoNao->setChecked ( true );
}

$obRdTipoBaseSim = new Radio();
$obRdTipoBaseSim->setName        ( "boTipoBase" );
$obRdTipoBaseSim->setRotulo      ( "Tipo Base" );
$obRdTipoBaseSim->setTitle       ( "Informe se a base utilizará valores ou quantidades dos eventos." );
$obRdTipoBaseSim->setLabel       ( "Valor" );
$obRdTipoBaseSim->setValue       ( "V" );
$obRdTipoBaseSim->setDisabled    ( $boBloquea );
if ($boTipoBase != 'Q') {
    $obRdTipoBaseSim->setChecked ( true );
}

$obRdTipoBaseNao = new Radio();
$obRdTipoBaseNao->setName        ( "boTipoBase" );
$obRdTipoBaseNao->setRotulo      ( "Tipo Base" );
$obRdTipoBaseNao->setTitle       ( "Informe se a base utilizará valores ou quantidades dos eventos." );
$obRdTipoBaseNao->setLabel       ( "Quantidade" );
$obRdTipoBaseNao->setValue       ( "Q" );
$obRdTipoBaseNao->setDisabled    ( $boBloquea );
if ($boTipoBase == 'Q') {
    $obRdTipoBaseNao->setChecked ( true );
}

$obSpnInfEventoBaseCalculo = new Span;
$obSpnInfEventoBaseCalculo->setId ( "spnInfEventoBaseCalculo" );

$obHdnInfEventoBaseCalculo =  new HiddenEval;
$obHdnInfEventoBaseCalculo->setName ( "hdnInfEventoBaseCalculo" );
$obHdnInfEventoBaseCalculo->setId   ( "hdnInfEventoBaseCalculo" );

$obSpnListaEventos = new Span;
$obSpnListaEventos->setId ( "spnListaEventos" );

$obFormulario = new Formulario;
$obFormulario->addForm            ( $obForm );
$obFormulario->addTitulo          ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden          ( $obHdnInfEventoBaseCalculo ,true );
$obFormulario->addHidden          ( $obHdnAcao );
$obFormulario->addHidden          ( $obHdnCtrl );
$obFormulario->addHidden          ( $obHdnCodBase );
if (trim($stAcao) == "alterar") {
    $obFormulario->addHidden      ( $obHdnInsercaoAutomatica );
}
$obFormulario->addTitulo          ( "Bases de Cálculo" );
$obFormulario->addComponente      ( $obTxtNomBase );
$obFormulario->agrupaComponentes  ( array($obRdExibeBaseFFSim, $obRdExibeBaseFFNao));
$obFormulario->agrupaComponentes  ( array($obRdInsercaoEventoAutomaticoSim, $obRdInsercaoEventoAutomaticoNao));
$obFormulario->agrupaComponentes  ( array($obRdTipoBaseSim, $obRdTipoBaseNao));
$obFormulario->addSpan            ( $obSpnInfEventoBaseCalculo );
$obFormulario->addSpan            ( $obSpnListaEventos );
if($stAcao == incluir ){
    $stAcaoCancelar = $pgForm;
}else{
    $stAcaoCancelar = $pgFilt;
}
$obFormulario->Cancelar           ( $stAcaoCancelar );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
