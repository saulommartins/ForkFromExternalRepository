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
    * Página de Filtro do Calculo de 13º Salário
    * Data de Criação: 09/09/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.11

    $Id: FLManterCalculoDecimo.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCalculoDecimo";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

Sessao::remove("calculados");
Sessao::write("link","");
Sessao::write("boExcluirCalculados",true);
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao();
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);
$stTitulo = $obRFolhaPagamentoFolhaSituacao->consultarCompetencia();

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setId                               ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();

$obBntOk = new ok();
$stJs  = "var url = '".CAM_GRH_FOL_INSTANCIAS."decimo/OCManterCalculoDecimo.php?".Sessao::getId()."' \n";
$obBntOk->obEvento->setOnClick( " $stJs
                                  jQuery('#stCtrl').val('submeter');
                                  jQuery.post(url, jQuery('#frm').serialize(),function (data) {eval(data);},'html');
                                ");

$obBtnLimpar = new Button;
$obBtnLimpar->setName                           ( "btnLimparFiltro"                                                     );
$obBtnLimpar->setValue                          ( "Limpar"                                                              );
$obBtnLimpar->setTipo                           ( "button"                                                              );
$obBtnLimpar->obEvento->setOnClick              ( "document.frm.reset();"                                  );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                               );
$obForm->setTarget                              ( "oculto"                                                              );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $stTitulo ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addTitulo                        ( "Seleção do Filtro"                                                   );
$obIFiltroComponentes->geraFormulario($obFormulario);
$obFormulario->defineBarra                      ( array($obBntOk,$obBtnLimpar)                                          );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
