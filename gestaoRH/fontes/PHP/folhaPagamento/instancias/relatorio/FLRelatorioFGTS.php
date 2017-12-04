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
    * Filtro de Relatório de FGTS
    * Data de Criação: 05/05/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore
    * Casos de uso: uc-04.05.25
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php"										);
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"									);

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioFGTS";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::remove("link");
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
Sessao::write('arContratos',array());
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction   		( $pgProc  );
$obForm->setTarget   		( "oculto" );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName         ( "stCtrl" );
$obHdnCtrl->setValue        ( $stCtrl  );

//FILTRO DE COMPETENCIA
$obIFiltroCompetencia = new IFiltroCompetencia(true,"",true);

$obCmbOrdenacao = new Select;
$obCmbOrdenacao->setName    ( "stOrdenacao"            );
$obCmbOrdenacao->setValue   ( $stOrdenacao             );
$obCmbOrdenacao->setRotulo  ( "Ordenação"              );
$obCmbOrdenacao->setTitle   ( "Selecione a ordenação." );
$obCmbOrdenacao->addOption  ( "", "Selecione"          );
$obCmbOrdenacao->addOption  ( "A","Alfabética"         );
$obCmbOrdenacao->addOption  ( "N","Numérica"           );
$obCmbOrdenacao->setStyle   ( "width: 250px"           );

$obIFiltroTipoFolha = new IFiltroTipoFolha();
$obIFiltroTipoFolha->setValorPadrao("1");

$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setGrupoLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setGrupoLocal();
$obIFiltroComponentes->setFiltroPadrao("contrato");
$obIFiltroComponentes->getOnload($jsOnload);

//DEFINICAO DE BOTOES
$obBtnLimpar = new Button;
$obBtnLimpar->setName                       ( "btnLimparCampos"                        			);
$obBtnLimpar->setValue                      ( "Limpar"                                 			);
$obBtnLimpar->setTipo                       ( "button"                                 			);
$obBtnLimpar->obEvento->setOnClick          ( "javaScript:montaParametrosGET('limparForm', '', true);"     );

$obBtnOk = new OK;
$obBtnOk->obEvento->setOnClick              ( "javaScript:montaParametrosGET('OK', 'stTipoFiltro', true);" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                ( $obForm                                                          );
$obFormulario->addHidden              ( $obHdnCtrl                                                       );
$obFormulario->addTitulo              ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addTitulo              ( 'Seleção do Filtro'                                              );
$obIFiltroCompetencia->geraFormulario ( $obFormulario                                                    );
$obIFiltroTipoFolha->geraFormulario	  ( $obFormulario													 );
$obIFiltroComponentes->geraFormulario ( $obFormulario													 );
$obFormulario->addComponente          ( $obCmbOrdenacao                                                  );
$obFormulario->defineBarra            ( array($obBtnOk,$obBtnLimpar)                                     );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
