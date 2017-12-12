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
    * Filtro de Relatório de Contribuição Previdenciária
    * Data de Criação: 27/04/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30547 $
    $Name$
    $Author: tiago $
    $Date: 2007-09-27 09:25:18 -0300 (Qui, 27 Set 2007) $

    * Casos de uso: uc-04.05.43
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                    );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPrevidencia.class.php"                               );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"									);
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php"										);

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioContribuicaoPrevidenciaria";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRFolhaPagamentoEvento        = new RFolhaPagamentoEvento;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao( new RFolhaPagamentoPeriodoMovimentacao );
$obRFolhaPagamentoPrevidencia   = new RFolhaPagamentoPrevidencia;

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction          ( $pgProc 		  );
$obForm->setTarget          ( "telaPrincipal" );

$obHdnAcao =  new Hidden;
$obHdnAcao->setName         ( "stAcao"        );
$obHdnAcao->setValue        ( $stAcao         );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName         ( "stCtrl"        );
$obHdnCtrl->setValue        ( $stCtrl         );

$obIFiltroCompetencia = new IFiltroCompetencia(true,"",true);
$obIFiltroCompetencia->obCmbMes->setNull( false );
$obIFiltroCompetencia->obTxtAno->setNull( false );

$obIFiltroTipoFolha = new IFiltroTipoFolha();
//$obIFiltroTipoFolha->setValorPadrao("1");
$obIFiltroTipoFolha->setMostraAcumularSalCompl(true);

$obRFolhaPagamentoPrevidencia->listarPrevidenciasOficiais($rsPrevidencia);
$obCmbPrevidencia = new Select;
$obCmbPrevidencia->setRotulo                    ( "Previdência"                                             );
$obCmbPrevidencia->setTitle                     ( "Selecione a previdência."                                );
$obCmbPrevidencia->setName                      ( "inCodPrevidencia"                                        );
$obCmbPrevidencia->setValue                     ( $inCodPrevidencia                                         );
$obCmbPrevidencia->setStyle                     ( "width: 200px"                                            );
$obCmbPrevidencia->addOption                    ( "", "Selecione"                                           );
$obCmbPrevidencia->setCampoID                   ( "[cod_previdencia]"                                       );
$obCmbPrevidencia->setCampoDesc                 ( "[descricao]"                                             );
$obCmbPrevidencia->setNull                      ( false                                                     );
$obCmbPrevidencia->preencheCombo                ( $rsPrevidencia                                            );

include_once(CAM_GRH_PES_COMPONENTES."IRadioCadastroSituacao.class.php");
$obIRadioCadastroSituacao = new IRadioCadastroSituacao();

$obRdoOrdenacao1 = new Radio;
$obRdoOrdenacao1->setName                       ( "stOrdenacao"                                             );
$obRdoOrdenacao1->setRotulo                     ( "Ordenação"                                 				);
$obRdoOrdenacao1->setLabel                      ( "Alfabética"                                              );
$obRdoOrdenacao1->setTitle                      ( "Selecione a ordenação dos contratos no relatório."       );
$obRdoOrdenacao1->setValue                      ( "nom_cgm"                                              	);
$obRdoOrdenacao1->setChecked                    ( true                                                      );

$obRdoOrdenacao2 = new Radio;
$obRdoOrdenacao2->setName                       ( "stOrdenacao"                                             );
$obRdoOrdenacao2->setRotulo                     ( "Ordenação das Matrículas"                                );
$obRdoOrdenacao2->setLabel                      ( "Numérica"                                                );
$obRdoOrdenacao2->setTitle                      ( "Selecione a ordenação dos contratos no relatório."       );
$obRdoOrdenacao2->setValue                      ( "registro"                                                );

$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setRegimeSubDivisao();
$obIFiltroComponentes->setGrupoLotacao();
$obIFiltroComponentes->setGrupoLocal();
$obIFiltroComponentes->setGrupoRegimeSubDivisao();
$obIFiltroComponentes->setTodos();
$obIFiltroComponentes->getOnload($jsOnload);
$obIFiltroComponentes->setProcessarCompetencia();

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm							( $obForm 															);
$obFormulario->addTitulo						( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" 	);
$obFormulario->addHidden						( $obHdnAcao 														);
$obFormulario->addHidden						( $obHdnCtrl 														);
$obFormulario->addTitulo                        ( "Seleção do Filtro"                                       		);
$obIFiltroCompetencia->geraFormulario           ( $obFormulario                                             		);
$obIFiltroTipoFolha->geraFormulario				( $obFormulario														);
$obIFiltroComponentes->geraFormulario			( $obFormulario														);
$obFormulario->addComponente                    ( $obCmbPrevidencia                                         		);
$obIRadioCadastroSituacao->geraFormulario($obFormulario);
$obFormulario->agrupaComponentes                ( array($obRdoOrdenacao1,$obRdoOrdenacao2)                  		);
$obFormulario->Ok();
$obFormulario->setFormFocus                     ( $obIFiltroCompetencia->obCmbMes->getId()                  		);
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
