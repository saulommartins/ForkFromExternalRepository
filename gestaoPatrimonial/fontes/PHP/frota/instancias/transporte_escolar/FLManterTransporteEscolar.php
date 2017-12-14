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
    * Data de Criação: 11/04/2014

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Michel Teixeira

    $Id: FLManterTransporteEscolar.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_FRO_COMPONENTES.'IPopUpVeiculo.class.php' );
include_once ( CAM_GA_CGM_COMPONENTES.'IPopUpCGMVinculado.class.php' );

$stPrograma = "ManterTransporteEscolar";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

include_once ($pgJs);

$stAcao = $request->get('stAcao');

//cria um novo formulario
$obForm = new Form;
$obForm->setAction($pgForm);
$obForm->setTarget("telaPrincipal");

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//instancia o componente IPopUpVeiculo
$obIPopUpVeiculo = new IPopUpVeiculo($obForm);
$obIPopUpVeiculo->setNull   ( false     );

//instancia o componente IPopUpCGMVinculado
$obIPopUpEscola = new IPopUpCGMVinculado( $obForm );
$obIPopUpEscola->setTabelaVinculo       ( 'frota.escola' 	    );
$obIPopUpEscola->setCampoVinculo  	( 'numcgm'                  );
$obIPopUpEscola->setNomeVinculo         ( 'CGM Escola'              );
$obIPopUpEscola->setRotulo      	( 'CGM Escola'		    );
$obIPopUpEscola->setTitle        	( 'Informe o CGM da Escola.');
$obIPopUpEscola->setName         	( 'stNomCgmEscola'          );
$obIPopUpEscola->setId           	( 'stNomCgmEscola'     	    );
$obIPopUpEscola->obCampoCod->setName    ( 'inCgmEscola'             );
$obIPopUpEscola->obCampoCod->setId	( 'inCgmEscola'             );
$obIPopUpEscola->obCampoCod->setSize    (10                         );
$obIPopUpEscola->obCampoCod->setNull    ( false                     );
$obIPopUpEscola->setNull		( false                     );
$obIPopUpEscola->setTipo                ( 'vinculado'               );
$obIPopUpEscola->obCampoCod->obEvento->setOnBlur  ("montaParametrosGET('verificaEscola', 'inCgmEscola');");

$obBtnOK = new Ok(true);
$obBtnOK->setId('ok');
$obBtnOK->setName('ok');
$arBotoes = array($obBtnOK);

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->addTitulo    ( 'Dados da Filtro'             );
$obFormulario->addForm      ( $obForm                       );
$obFormulario->addHidden    ( $obHdnAcao                    );
$obFormulario->addHidden    ( $obHdnCtrl                    );
$obFormulario->addComponente( $obIPopUpVeiculo              );
$obFormulario->addComponente( $obIPopUpEscola               );
$obFormulario->defineBarra  ( $arBotoes                     );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
