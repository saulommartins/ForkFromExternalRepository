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
    * Página de Filtro de membro adicional
    * Data de Criação   : 26/08/2014

    * @author Analista: Eduardo Paculski Schitz
    * @author Desenvolvedor: Arthur Cruz

    * @ignore

    * Casos de uso: uc-03.05.15
*/

include_once ("../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php"      );
include_once ("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php");
include_once (CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php"                                           );
include_once (CAM_GP_COM_COMPONENTES."IPopUpObjeto.class.php"                                        );
include_once (CAM_GP_LIC_COMPONENTES."ISelectTipoLicitacao.class.php"                                );
include_once (CAM_GP_LIC_COMPONENTES."ISelectCriterioJulgamento.class.php"                           );
include_once (CAM_GP_COM_COMPONENTES."ISelectTipoObjeto.class.php"                                   );
include_once (CAM_GF_ORC_COMPONENTES.'ISelectMultiploEntidadeUsuario.class.php'                      );
include_once (CAM_GA_PROT_COMPONENTES."IPopUpProcesso.class.php"                                     );
include_once (CAM_GP_LIC_COMPONENTES."IMontaNumeroLicitacaoMultiploEntidadeUsuario.class.php"        );

$stPrograma = "ManterMembroAdicional";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( "" );

$obForm = new Form;
$obForm->setAction  ( $pgList );

$obMontaLicitacao = new IMontaNumeroLicitacaoMultiploEntidadeUsuario($obForm);

//Definição do Formulário
$obFormulario = new Formulario;
$obFormulario->addTitulo          ( "Dados Para o Filtro"        );
$obFormulario->addForm            ( $obForm                      );
$obFormulario->setAjuda           ("UC-03.05.15"                 );
$obFormulario->addHidden          ( $obHdnAcao                   );
$obFormulario->addHidden          ( $obHdnCtrl                   );
$obMontaLicitacao->geraFormulario ( $obFormulario                );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
