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
    * Filtro
    * Data de Criação: 04/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id: FLManterRegistroEvento.php 65896 2016-06-24 20:14:24Z michel $

    * Casos de uso: uc-04.05.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroEvento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::remove("link");
$stAcao = $request->get("stAcao");

include_once($pgJS);
include_once($pgOcul);

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
$obRFolhaPagamentoFolhaSituacao->consultarFolha();

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao" );
$obHdnAcao->setValue                            ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl" );
$obHdnCtrl->setValue                            ( $stCtrl  );

//Define objeto SELECT para o campo Filtrar
$obCmbFiltrar = new Select;
$obCmbFiltrar->setName               ( "inFiltrar"                               );
$obCmbFiltrar->setTitle              ( "Selecione o tipo de filtro a ser utilizado para listar o(s) contrato(s)." );
$obCmbFiltrar->setStyle              ( "width: 250px"                            );
$obCmbFiltrar->setRotulo             ( "Filtrar"                                 );
$obCmbFiltrar->setValue              ( $inFiltrar                                );
$obCmbFiltrar->addOption             ( "", "Selecione"                           );
$obCmbFiltrar->addOption             ( "0", "Matrícula"                          );
$obCmbFiltrar->addOption             ( "1", "CGM/Matrícula"                      );
$obCmbFiltrar->addOption             ( "2", "Cargo"                              );
$obCmbFiltrar->addOption             ( "3", "Função"                             );
$obCmbFiltrar->addOption             ( "4", "Padrão"                             );
$obCmbFiltrar->addOption             ( "5", "Lotação"                            );
$obCmbFiltrar->addOption             ( "6", "Local"                              );
$obCmbFiltrar->addOption             ( "7", "Evento"                             );
$obCmbFiltrar->obEvento->setOnChange ( "buscaValorFiltro('habilitaSpanFiltro');" );

//Define objeto SPAN para os campos Spans
$obSpnFiltrar = new Span;
$obSpnFiltrar->setId                            ( "spnFiltrar"  );

$obHdnFiltrar = new HiddenEval;
$obHdnFiltrar->setName                          ( "hdnFiltrar"  );
$obHdnFiltrar->setValue                         ( ""            );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm             );
if ( $obRFolhaPagamentoFolhaSituacao->getSituacao() == 'Aberto' ) {
    $boMensagem = false;
    $obFormulario->addTitulo                    ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
    $obFormulario->addTitulo                    ( "Seleção do Filtro" );
    $obFormulario->addHidden                    ( $obHdnAcao          );
    $obFormulario->addHidden                    ( $obHdnCtrl          );
    $obFormulario->addComponente                ( $obCmbFiltrar       );
    $obFormulario->addSpan                      ( $obSpnFiltrar       );
    $obFormulario->addHidden                    ( $obHdnFiltrar,true  );

    $obBtnOk     = new Ok();
    $obBtnLimpar = new Limpar();
    $obBtnLimpar->obEvento->setOnClick("buscaValorFiltro('limparForm');");
    $obFormulario->defineBarra( array( $obBtnOk, $obBtnLimpar ) );
} else {
    $boMensagem = true;
    $stMensagem = "A folha salário está fechada. Para efetuar o registro de eventos é necessário reabri-lá.";
    $obLblMensagem = new Label;
    $obLblMensagem->setRotulo               ( "Situação"                                                );
    $obLblMensagem->setValue                ( $stMensagem                                               );

    $obFormulario->addTitulo                    ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
    $obFormulario->addTitulo                    ( "Folha Salário"     );
    $obFormulario->addComponente                ( $obLblMensagem       );
}
$obFormulario->show();

if ($boMensagem) {
    gerarSpan1(true,$boMensagem);
} else {
    $jsOnload = "buscaValorFiltro('habilitaSpanFiltro');";
}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
