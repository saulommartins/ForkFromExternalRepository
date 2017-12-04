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
    * Página de Filtro do Relatório Bancário de Pensão Judicial
    * Data de Criação : 21/03/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @ignore

    $Revision: 30547 $
    $Name$
    $Autor: $
    $Date: 2008-03-31 14:50:43 -0300 (Seg, 31 Mar 2008) $

    * Casos de uso: uc-04.05.57
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploBanco.class.php"                                	);
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploAgencia.class.php"                              	);
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"					);
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php"					);

//Define o nome dos arquivos PHP
$stPrograma = "BancarioPensaoJudicial";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc );
$obForm->setTarget                              ( "telaPrincipal"                                  		);

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                               );

$obBtnLimpar = new Button();
$obBtnLimpar->setValue             	        ( "Limpar"												);
$obBtnLimpar->setTipo				( "button"												);
$obBtnLimpar->obEvento->setOnClick		( "montaParametrosGET('limparForm', 'stCtrl', true);"			);

$obBtnOk = new OK;

$obIFiltroCompetencia = new IFiltroCompetencia(true,"",true);

//Banco e Agencia
$obSelBanco  = new ISelectMultiploBanco;
$obSelAgencia = new ISelectMultiploAgencia;

$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setAtributoServidor();
$obIFiltroComponentes->setGrupoLocal();
$obIFiltroComponentes->setGrupoLotacao();
$obIFiltroComponentes->setGrupoAtributoServidor();
$obIFiltroComponentes->setFiltroPadrao("contrato");
$obIFiltroComponentes->getOnload($jsOnload);

$obIFiltroTipoFolha = new IFiltroTipoFolha();

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden                        ( $obHdnCtrl                                            );
$obFormulario->addTitulo                        ( "Seleção do Filtro");
$obIFiltroCompetencia->geraFormulario           ( $obFormulario                                                    );
$obIFiltroTipoFolha->geraFormulario($obFormulario);
$obIFiltroComponentes->geraFormulario($obFormulario);
$obFormulario->addTitulo                        ( 'Informações Bancárias'                                           );
$obFormulario->addComponente                    ( $obSelBanco                                                       );
$obFormulario->addComponente                    ( $obSelAgencia                                                     );
$obFormulario->defineBarra                      ( array($obBtnOk,$obBtnLimpar)                          );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
