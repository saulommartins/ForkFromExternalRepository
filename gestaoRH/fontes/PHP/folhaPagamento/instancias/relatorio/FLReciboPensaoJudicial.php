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
    * Página de Filtro do Recibo de Pensão Judicial
    * Data de Criação: 24/06/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    * Casos de uso: uc-04.05.65

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = "ReciboPensaoJudicial";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
// $jsOnload   = "montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');";

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$stAcao      = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                               );
$obForm->setTarget                              ( "telaPrincipal"                                                              );

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentesDependentes.class.php");
$obIFiltroComponentesDependentes = new IFiltroComponentesDependentes();
$obIFiltroComponentesDependentes->setCGMDependente();
$obIFiltroComponentesDependentes->setMatriculaDependenteDeServidor();
$obIFiltroComponentesDependentes->setLotacao();
$obIFiltroComponentesDependentes->setGrupoLotacao();
$obIFiltroComponentesDependentes->setLocal();
$obIFiltroComponentesDependentes->setGrupoLocal();

include_once(CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php");
$obIFiltroCompetencia = new IFiltroCompetencia();

include_once(CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php");
$obIFiltroTipoFolha = new IFiltroTipoFolha();

$obRdoAlfabetica = new Radio();
$obRdoAlfabetica->setName("stOrdenacao");
$obRdoAlfabetica->setRotulo("Ordenação");
$obRdoAlfabetica->setLabel("Alfabética");
$obRdoAlfabetica->setValue("a");
$obRdoAlfabetica->setChecked(true);
$obRdoAlfabetica->setTitle("Marque para ordenação dos recibos por ordem de nome do dependente ou código.");

$obRdoNumerica = new Radio();
$obRdoNumerica->setName("stOrdenacao");
$obRdoNumerica->setRotulo("Ordenação");
$obRdoNumerica->setLabel("Numérica");
$obRdoNumerica->setValue("n");
$obRdoNumerica->setTitle("Marque para ordenação dos recibos por ordem de nome do dependente ou código.");

$obCkbCopia = new Checkbox();
$obCkbCopia->setRotulo("Emitir Cópia do Recibo");
$obCkbCopia->setName("boCopia");
$obCkbCopia->setValue(true);
$obCkbCopia->setTitle("Marque ma para que seja emitido cópia do recibo (dois iguais).");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario();
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obIFiltroCompetencia->geraFormulario($obFormulario);
$obIFiltroTipoFolha->geraFormulario($obFormulario);
$obIFiltroComponentesDependentes->geraFormulario($obFormulario);
$obFormulario->agrupaComponentes(array($obRdoAlfabetica,$obRdoNumerica));
$obFormulario->addComponente($obCkbCopia);
$obFormulario->ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
