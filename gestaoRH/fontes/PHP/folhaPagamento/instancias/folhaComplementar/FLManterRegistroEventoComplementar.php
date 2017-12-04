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
    * Formulario de Manter Registro de Evento (Folha Complementar)
    * Data de Criação: 20/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30766 $
    $Name$
    $Author: souzadl $
    $Date: 2006-11-21 15:16:50 -0200 (Ter, 21 Nov 2006) $

    * Casos de uso: uc-04.05.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaComplementar.class.php"                         );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                              );
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroEventoComplementar";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::remove("link");
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);
if ( $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao") != "" ) {
    $obRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao($rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao'));
} else {
    $obRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao(0);
}
$obRFolhaPagamentoFolhaComplementar = new RFolhaPagamentoFolhaComplementar( $obRFolhaPagamentoPeriodoMovimentacao );
$obRFolhaPagamentoFolhaComplementar->consultarFolhaComplementarAberta();

include_once($pgJS);
include_once($pgOcul);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                              );
$obHdnAcao->setValue                            ( $stAcao                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                              );
$obHdnCtrl->setValue                            ( $stStrl                                               );

if ( $obRFolhaPagamentoFolhaComplementar->getCodComplementar() != "" ) {
    $obHdnComplementar =  new Hidden;
    $obHdnComplementar->setName                 ( "inCodComplementar"                                       );
    $obHdnComplementar->setValue                ( $obRFolhaPagamentoFolhaComplementar->getCodComplementar() );

    $arData = explode("/",$rsUltimaMovimentacao->getCampo('dt_final'));

    $obHdnCompetencia =  new Hidden;
    $obHdnCompetencia->setName                  ( "stCompetencia"                                           );
    $obHdnCompetencia->setValue                 ( $arData[1]                                                );

    //Define objeto SELECT para o campo Filtrar
    $arFiltrar = array();
    $arFiltrar[0] = array('cod_filtrar'=>0,'descricao'=>'Matrícula');
    $arFiltrar[1] = array('cod_filtrar'=>1,'descricao'=>'CGM/Matrícula');
    $arFiltrar[2] = array('cod_filtrar'=>2,'descricao'=>'Cargo');
    $arFiltrar[3] = array('cod_filtrar'=>3,'descricao'=>'Função');
    $arFiltrar[4] = array('cod_filtrar'=>4,'descricao'=>'Padrão');
    $arFiltrar[5] = array('cod_filtrar'=>5,'descricao'=>'Lotação');
    $arFiltrar[6] = array('cod_filtrar'=>6,'descricao'=>'Local');
    $rsFiltrar = new recordset;
    $rsFiltrar->preenche($arFiltrar);
    $obCmbFiltrar = new Select;
    $obCmbFiltrar->setName                          ( "inFiltrar"                                           );
    $obCmbFiltrar->setTitle                         ( "Selecione o tipo de filtro a ser utilizado para listar o(s) contrato(s)." );
    $obCmbFiltrar->setStyle                         ( "width: 250px"                                        );
    $obCmbFiltrar->setRotulo                        ( "Filtrar"                                             );
    $obCmbFiltrar->setValue                         ( $inFiltrar                                            );
    $obCmbFiltrar->addOption                        ( "", "Selecione"                                       );
    $obCmbFiltrar->setCampoID                       ( "[cod_filtrar]"                                       );
    $obCmbFiltrar->setCampoDesc                     ( "[descricao]"                                         );
    $obCmbFiltrar->preencheCombo                    ( $rsFiltrar                                            );
    $obCmbFiltrar->obEvento->setOnChange            ( "buscaValorFiltro('habilitaSpanFiltro');"             );
} else {
    $boMensagem = true;
}

//Define objeto SPAN para os campos Spans
$obSpnFiltrar = new Span;
$obSpnFiltrar->setId                            ( "spnFiltrar"                                          );

$obHdnFiltrar = new HiddenEval;
$obHdnFiltrar->setName                          ( "hdnFiltrar"                                          );
$obHdnFiltrar->setValue                         ( ""                                                    );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgList                                               );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                               );
if ( $obRFolhaPagamentoFolhaComplementar->getCodComplementar() != "" ) {
    $obFormulario->addTitulo                    ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
    $obFormulario->addTitulo                    ( "Seleção do Filtro"                                   );
} else {
    $obFormulario->addTitulo                    ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
    $obFormulario->addTitulo                    ( "Folha Complementar"                                  );
}
$obFormulario->addHidden                        ( $obHdnAcao                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                            );
if ( $obRFolhaPagamentoFolhaComplementar->getCodComplementar() != "" ) {
    $obFormulario->addHidden                    ( $obHdnComplementar                                    );
    $obFormulario->addHidden                    ( $obHdnCompetencia                                     );
    $obFormulario->addComponente                ( $obCmbFiltrar                                         );
}
$obFormulario->addSpan                          ( $obSpnFiltrar                                         );
$obFormulario->addHidden                        ( $obHdnFiltrar,true                                    );
if ( $obRFolhaPagamentoFolhaComplementar->getCodComplementar() != "" ) {
    $obBtnOk     = new Ok();
    $obBtnLimpar = new Limpar();
    $obBtnLimpar->obEvento->setOnClick("buscaValorFiltro('limparForm');");
    $obFormulario->defineBarra( array( $obBtnOk, $obBtnLimpar ) );
}
$obFormulario->show();

processarFiltro(true,$boMensagem);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
