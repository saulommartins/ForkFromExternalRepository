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
    * Página de Filtro do Recibo de Férias
    * Data de Criação: 02/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza, Vandré Miguel Ramos

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: tiago $
    $Date: 2007-09-26 18:57:40 -0300 (Qua, 26 Set 2007) $

    * Casos de uso: uc-04.05.56
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php"                           );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"									);

//Define o nome dos arquivos PHP
$stPrograma = "ReciboFerias";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

Sessao::remove("link");
$stAcao = $request->get('stAcao');

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName          ( "stAcao" );
$obHdnAcao->setValue         ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName          ( "stCtrl" );
$obHdnCtrl->setValue         ( $stCtrl  );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName       ( "stCaminho" );
$obHdnCaminho->setValue      ( CAM_GRH_FOL_INSTANCIAS."relatorio/".$pgProc );

$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setFiltroPadrao("contrato");
$obIFiltroComponentes->getOnload($jsOnload);

$obIFiltroCompetencia = new IFiltroCompetencia(true,"",true);

$obCmbOrdenacaoLotacao = new Select;
$obCmbOrdenacaoLotacao->setName                         ( "stOrdenacaoLotacao"                  );
$obCmbOrdenacaoLotacao->setValue                        ( 'A'                                   );
$obCmbOrdenacaoLotacao->setRotulo                       ( "Ordenação"                           );
$obCmbOrdenacaoLotacao->setTitle                        ( "Selecione a ordenação para lotação." );
$obCmbOrdenacaoLotacao->addOption                       ( "A","Alfabética"                      );
$obCmbOrdenacaoLotacao->addOption                       ( "N","Numérica"                        );
$obCmbOrdenacaoLotacao->setStyle                        ( "width: 250px"                        );

$obChkOrdenacaoLotacao = new CheckBox;
$obChkOrdenacaoLotacao->setName                         ( 'boOrdenacaoLotacao' 					);
$obChkOrdenacaoLotacao->setId                           ( 'boOrdenacaoLotacao' 					);
$obChkOrdenacaoLotacao->setValue                        ( 't'                  					);
$obChkOrdenacaoLotacao->setChecked                      ( false                					);
$obChkOrdenacaoLotacao->setLabel                        ( 'Lotação' 							);

$obCmbOrdenacaoLocal = new Select;
$obCmbOrdenacaoLocal->setName                          ( "stOrdenacaoLocal"                   	);
$obCmbOrdenacaoLocal->setValue                         ( 'A'                                  	);
$obCmbOrdenacaoLocal->setRotulo                        ( ""                                   	);
$obCmbOrdenacaoLocal->setTitle                         ( "Selecione a ordenação para local." 	);
$obCmbOrdenacaoLocal->addOption                        ( "A","Alfabética"                     	);
$obCmbOrdenacaoLocal->addOption                        ( "N","Numérica"                       	);
$obCmbOrdenacaoLocal->setStyle                         ( "width: 250px"                       	);

$obChkOrdenacaoLocal = new CheckBox;
$obChkOrdenacaoLocal->setName                          ( 'boOrdenacaoLocal' 					);
$obChkOrdenacaoLocal->setId                            ( 'boOrdenacaoLocal' 					);
$obChkOrdenacaoLocal->setValue                         ( 't'                 					);
$obChkOrdenacaoLocal->setChecked                       ( false               					);
$obChkOrdenacaoLocal->setLabel                         ( 'Local' 								);

$obCmbOrdenacaoCGM = new Select;
$obCmbOrdenacaoCGM->setName                            ( "stOrdenacaoCGM"             			);
$obCmbOrdenacaoCGM->setValue                           ( 'A'                          			);
$obCmbOrdenacaoCGM->setRotulo                          ( ""                           			);
$obCmbOrdenacaoCGM->setTitle                           ( "Selecione a ordenação CGM." 			);
$obCmbOrdenacaoCGM->addOption                          ( "A","Alfabética"             			);
$obCmbOrdenacaoCGM->addOption                          ( "N","Numérica"               			);
$obCmbOrdenacaoCGM->setStyle                           ( "width: 250px"               			);

$obChkOrdenacaoCGM = new CheckBox;
$obChkOrdenacaoCGM->setName                            ( 'boOrdenacaoCGM' 						);
$obChkOrdenacaoCGM->setId                              ( 'boOrdenacaoCGM' 						);
$obChkOrdenacaoCGM->setValue                           ( 't'              						);
$obChkOrdenacaoCGM->setChecked                         ( false            						);
$obChkOrdenacaoCGM->setLabel                           ( 'CGM' 									);

$obBtnOk = new Ok;

$obBtnLimpar = new Button;
$obBtnLimpar->setName                           	   ( "btnLimpar"                           				 );
$obBtnLimpar->setValue                          	   ( "Limpar"                              				 );
$obBtnLimpar->setTipo                           	   ( "button"                              				 );
$obBtnLimpar->obEvento->setOnClick               	   ( "montaParametrosGET('limparFormulario', '', true);" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addHidden                        ( $obHdnCaminho                                             			);
$obFormulario->addTitulo                        ( "Seleção do Filtro"                                                   );
$obIFiltroCompetencia->geraFormulario			( $obFormulario															);
$obIFiltroComponentes->geraFormulario			( $obFormulario															);
$obFormulario->agrupaComponentes                ( array( $obCmbOrdenacaoLotacao, $obChkOrdenacaoLotacao )               );
$obFormulario->agrupaComponentes                ( array( $obCmbOrdenacaoLocal , $obChkOrdenacaoLocal  )               	);
$obFormulario->agrupaComponentes                ( array( $obCmbOrdenacaoCGM    , $obChkOrdenacaoCGM     )               );
$obFormulario->defineBarra                      ( array($obBtnOk,$obBtnLimpar)                                          );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
