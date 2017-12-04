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
    * Página de Filtro do Consultar Registro de Evento de Férias
    * Data de Criação: 23/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30766 $
    $Name$
    $Author: rgarbin $
    $Date: 2008-03-31 11:26:27 -0300 (Seg, 31 Mar 2008) $

    * Casos de uso: uc-04.05.53
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarRegistroEventoFerias";
$pgForm     = "FM".$stPrograma.".php";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
$jsOnload   = "executaFuncaoAjax('montaSpanContrato');";

Sessao::write("link","");
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

$obHdnEval =  new HiddenEval;
$obHdnEval->setName                             ( "stEval"                                                              );
$obHdnEval->setId                               ( "stEval"                                                              );
$obHdnEval->setValue                            ( $stEval                                                               );

$obRdoContrato = new Radio;
$obRdoContrato->setName                         ( "boOpcao"                                                             );
$obRdoContrato->setId                           ( "boOpcao"                                                             );
$obRdoContrato->setTitle                        ( "Selecione a opção para o filtro"                                     );
$obRdoContrato->setRotulo                       ( "Opções"                                                              );
$obRdoContrato->setLabel                        ( "Matrícula"                                                           );
$obRdoContrato->setValue                        ( "contrato"                                                            );
$obRdoContrato->setChecked                      ( true                                                                  );
$obRdoContrato->obEvento->setOnChange           ( "montaParametrosGET('montaSpanContrato', 'boOpcao', true)"                              );

$obRdoCGMContrato = new Radio;
$obRdoCGMContrato->setName                      ( "boOpcao"                                                             );
$obRdoCGMContrato->setId                        ( "boOpcao"                                                             );
$obRdoCGMContrato->setTitle                     ( "Selecione a opção para o filtro"                                     );
$obRdoCGMContrato->setRotulo                    ( "Opções"                                                              );
$obRdoCGMContrato->setLabel                     ( "CGM/Matrícula"                                                       );
$obRdoCGMContrato->setValue                     ( "cgm_contrato"                                                        );
$obRdoCGMContrato->obEvento->setOnChange        ( "montaParametrosGET('montaSpanCGMContrato', 'boOpcao',true)"                           );

$obRdoEvento = new Radio;
$obRdoEvento->setName                           ( "boOpcao"                      );
$obRdoEvento->setId                             ( "boOpcao"                      );
$obRdoEvento->setTitle                          ( "Selecione a opção para o filtro"    );
$obRdoEvento->setRotulo                         ( "Opções"                             );
$obRdoEvento->setLabel                          ( "Evento"                       );
$obRdoEvento->setValue                          ( "evento"                       );
$obRdoEvento->obEvento->setOnChange             ( "montaParametrosGET('montaSpanEvento', 'boOpcao',true)" );

$obSpnFiltro = new Span;
$obSpnFiltro->setid                             ( "spnFiltro"                                                           );
$obSpnFiltro->setValue                          ( ""                                                                    );

$obSpnLista = new Span;
$obSpnLista->setId                              ( "spnSpanLista"  );

$obSpnBotao = new Span;
$obSpnBotao->setId                              ( "spnSpanBotao"  );

$obIFiltroCompetencia = new IFiltroCompetencia();

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( "../../popups/ferias/".$pgForm                                        );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addHidden                        ( $obHdnEval,true                                                       );
$obFormulario->addTitulo                        ( "Parâmetros para Consulta"                                            );
$obFormulario->agrupaComponentes                ( array($obRdoContrato,$obRdoCGMContrato,$obRdoEvento)                               );
$obFormulario->addSpan                          ( $obSpnFiltro                                                          );
$obIFiltroCompetencia->geraFormulario           ( $obFormulario                                                         );
$obFormulario->addSpan                          ( $obSpnBotao                                           );
$obFormulario->addSpan                          ( $obSpnLista                                           );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
