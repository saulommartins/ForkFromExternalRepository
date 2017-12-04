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
    * Página de Formulario para Manter Escalas
    * Data de Criação: 02/10/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: $

    * Casos de uso: uc-04.10.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscala.class.php"                                          );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscalaContrato.class.php"                                  );

//Define o nome dos arquivos PHP
$stPrograma = "ManterEscala";
$pgFilt = "FL".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgDeta = "DT".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//**************************************************************************************************************************//
//Define COMPONENTES DO FORMULARIO
//**************************************************************************************************************************//

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao      = $_REQUEST['stAcao'];
$inCodEscala = $_REQUEST['inCodEscala'];

$rsEscala = new RecordSet();

Sessao::remove('arTurnos');

if ($stAcao == 'alterar') {
    $obTPontoEscala = new TPontoEscala();
    $obTPontoEscala->setDado('cod_escala',$inCodEscala);
    $obTPontoEscala->recuperaPorChave($rsEscala);

    $stFiltroEscalaContratos = " AND escala_contrato.cod_escala = ".$inCodEscala;
    $obTPontoEscalaContrato = new TPontoEscalaContrato();
    $obTPontoEscalaContrato->recuperaContratosEscala($rsEscalaContratos, $stFiltroEscalaContratos);

    if ($rsEscalaContratos->getNumLinhas() > 0) {
        $boBloquearEdicao = true;
    }

    $jsOnload .= "executaFuncaoAjax('preencherTurnos', '&inCodEscala=$inCodEscala&boBloqueiaEdicao=$boBloquearEdicao');";
}

if ($boBloquearEdicao == false) {
    $jsOnload .= "executaFuncaoAjax('montaSpanIncluirAlterarTurno');";
}

$obHdnAcao =  new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setId  ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

//Instancia o form
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );

$obHdnCodEscala = new Hidden();
$obHdnCodEscala->setName("inCodEscala");
$obHdnCodEscala->setId("inCodEscala");
$obHdnCodEscala->setValue($inCodEscala);

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo   ( "Descrição"   );
$obTxtDescricao->setTitle    ( "Informe a descrição da Escala de Horário" );
$obTxtDescricao->setName     ( "stDescricao" );
$obTxtDescricao->setId       ( "stDescricao" );
$obTxtDescricao->setMaxLength( 80            );
$obTxtDescricao->setSize     ( 80            );
$obTxtDescricao->setNull     ( false         );
if ($rsEscala->getCampo('descricao')) {
    $obTxtDescricao->setValue( stripslashes($rsEscala->getCampo('descricao')) );
}

$obLblDescricao = new Label();
$obLblDescricao->setRotulo ( "Descrição" );
if ($rsEscala->getCampo('descricao')) {
    $obLblDescricao->setValue( $rsEscala->getCampo('cod_escala')."-".stripslashes($rsEscala->getCampo('descricao')) );
}

$obLblAvisoEdicao = new Label();
$obLblAvisoEdicao->setRotulo ( "" );
$obLblAvisoEdicao->setValue ( "A edição desta escala foi desativada pois a mesma possui contratos vinculados. Para editá-la, desvincule os contratos que a utilizam" );

$obSpanIncluirAlterarTurno = new Span();
$obSpanIncluirAlterarTurno->setId('spnIncluirAlterarTurno');

$obSpanProjetarTurnosData = new Span();
$obSpanProjetarTurnosData->setId('spnProjetarTurnosDatas');

$obProjetarTurnos = new CheckBox();
$obProjetarTurnos->setRotulo("Projetar Turnos");
$obProjetarTurnos->setTitle("Marque para que o sistema faça a projeção do restante da escala automaticamente. Nesse caso, o sistema levára em conta os horários e dias de trabalho ou folga, conforme a sequência lançada até o momento");
$obProjetarTurnos->setName("boProjetarTurnos");
$obProjetarTurnos->setId("boProjetarTurnos");
$obProjetarTurnos->setValue(1);
$obProjetarTurnos->obEvento->setOnClick(" jQuery('#spnIncluirAlterarTurno').html(''); executaFuncaoAjax('montaSpanProjetarTurnosDatas', '&boProjetarTurnos='+ jQuery('#boProjetarTurnos').attr('checked') ); ");
$obProjetarTurnos->setDisabled(true);

$obSpanTurnos = new Span();
$obSpanTurnos->setId('spnTurnos');

$obOk = new Ok();
$obOk->obEvento->setOnClick( " if (Valida()) { montaParametrosGET('submeter'); } " );

$obLimpar  = new Limpar;
$obLimpar->obEvento->setOnClick( "montaParametrosGET('limparFormulario');" );

$obOkLista  = new Button;
$obOkLista->setValue("Ok/Lista");
$obOkLista->obEvento->setOnClick( " jQuery('#stAcao').val('redirecionarLista'); montaParametrosGET('submeter'); " );

$obOkFiltro  = new Button;
$obOkFiltro->setValue("Ok/Filtro");
$obOkFiltro->obEvento->setOnClick( " jQuery('#stAcao').val('redirecionarFiltro'); montaParametrosGET('submeter'); " );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                       );
$obFormulario->addTitulo			( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden            ( $obHdnCtrl                    );
$obFormulario->addHidden            ( $obHdnAcao                    );
$obFormulario->addHidden            ( $obHdnCodEscala               );
$obFormulario->addTitulo			( "Dados da Escala de Horário"  );

if ($boBloquearEdicao) {
    $obFormulario->addComponente        ( $obLblDescricao               );
    $obFormulario->addComponente        ( $obLblAvisoEdicao             );
    $obFormulario->addSpan              ( $obSpanTurnos                 );
    $obFormulario->defineBarra(array($obOkLista, $obOkFiltro));
} else {
    $obFormulario->addComponente        ( $obTxtDescricao               );
    $obFormulario->addTitulo			( "Projetar Turnos"             );
    $obFormulario->addComponente        ( $obProjetarTurnos             );
    $obFormulario->addSpan              ( $obSpanProjetarTurnosData     );
    $obFormulario->addSpan              ( $obSpanIncluirAlterarTurno    );
    $obFormulario->addSpan              ( $obSpanTurnos                 );
    $obFormulario->defineBarra(array($obOk, $obLimpar));
}

$obFormulario->show();

include_once($pgJS);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
