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
    * Página de Filtro do Registro de Evento de Férias
    * Data de Criação: 19/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30766 $
    $Name$
    $Author: melo $
    $Date: 2006-11-20 07:49:17 -0200 (Seg, 20 Nov 2006) $

    * Casos de uso: uc-04.05.53
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroEventoFerias";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
$jsOnload   = "executaFuncaoAjax('processarFiltro');";

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

$obCmbFiltrar = new Select;
$obCmbFiltrar->setRotulo                        ( "Filtrar"                                                             );
$obCmbFiltrar->setTitle                         ( "Selecione o tipo de filtro."                                         );
$obCmbFiltrar->setName                          ( "stTipoFiltro"                                                        );
$obCmbFiltrar->setValue                         ( $stTipoFiltro                                                         );
$obCmbFiltrar->setStyle                         ( "width: 200px"                                                        );
$obCmbFiltrar->addOption                        ( "", "Selecione"                                                       );
$obCmbFiltrar->addOption                        ( "contrato","Matrícula"                                                 );
$obCmbFiltrar->addOption                        ( "cgm_contrato","CGM/Matrícula"                                         );
$obCmbFiltrar->addOption                        ( "cargo","Cargo"                                                       );
$obCmbFiltrar->addOption                        ( "funcao","Função"                                                     );
$obCmbFiltrar->addOption                        ( "padrao","Padrão"                                                     );
$obCmbFiltrar->addOption                        ( "lotacao","Lotação"                                                   );
$obCmbFiltrar->addOption                        ( "local","Local"                                                       );
$obCmbFiltrar->obEvento->setOnChange            ( "executaFuncaoAjax('gerarSpan','&stTipoFiltro='+this.value);"         );

$obSpnFiltro = new Span;
$obSpnFiltro->setid                             ( "spnFiltro"                                                           );
$obSpnFiltro->setValue                          ( ""                                                                    );

$obBtnOk = new Ok();

$obBtnLimpar = new Button;
$obBtnLimpar->setName                           ( "btnLimpar"                                                           );
$obBtnLimpar->setValue                          ( "Limpar"                                                              );
$obBtnLimpar->setTipo                           ( "button"                                                              );
$obBtnLimpar->obEvento->setOnClick              ( "executaFuncaoAjax('limparFiltro');"                                  );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgList                                                               );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addHidden                        ( $obHdnEval,true                                                       );
$obFormulario->addTitulo                        ( "Seleção do Filtro"                                                   );
$obFormulario->addComponente                    ( $obCmbFiltrar                                                         );
$obFormulario->addSpan                          ( $obSpnFiltro                                                          );
$obFormulario->defineBarra                      ( array($obBtnOk,$obBtnLimpar)                                          );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
