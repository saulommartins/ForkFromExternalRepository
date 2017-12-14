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
    * Pagina de filtro para o moudlo RECIBO DESPESA EXTRA
    * Data de Criação   : 04/09/2006

    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-03-27 16:29:48 -0300 (Ter, 27 Mar 2007) $

    * Casos de uso: uc-02.04.30
*/

/*
$Log$
Revision 1.6  2007/03/27 19:29:48  luciano
#8848#

Revision 1.5  2006/11/21 21:37:48  cleisson
Bug #7224#

Revision 1.4  2006/10/23 12:51:13  larocca
Bug #7224#

Revision 1.3  2006/10/04 15:14:54  bruce
colocada tag de log

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeUsuario.class.php'                        );
include_once ( CAM_GF_ORC_COMPONENTES.'IPopUpRecurso.class.php'                                        );
include_once ( CAM_GF_ORC_COMPONENTES.'IIntervaloPopUpDotacao.class.php'                               );
include_once ( CAM_GF_EMP_COMPONENTES.'IPopUpCredor.class.php'                                         );
include_once ( CAM_GF_CONT_COMPONENTES.'IPopUpContaAnalitica.class.php'                                );
include_once ( CAM_GF_ORC_COMPONENTES.'ISelectMultiploEntidadeUsuario.class.php'                       );
include_once ( CAM_FW_HTML."MontaOrgaoUnidade.class.php"                                               );

//Define o nome dos arquivos PHP
$stPrograma = "ReciboDespesaExtra";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js";

$stAcao = $_GET['stAcao'] ? $_GET['stAcao'] : $_POST['stAcao'];

$obForm = new Form;
$obForm->setAction( $pgList  );
$obForm->setTarget( "telaPrincipal");

/// Entidade
$obEntidadeUsuario = new ISelectMultiploEntidadeUsuario;

///Data Emissão
$obTextData = new Data;
$obTextData->setRotulo ( 'Data Emissão' );
$obTextData->setTitle  ( 'Informe a data de emissão.' );

/// número do recibo
$obNumeroRecibo = new TextBox;
$obNumeroRecibo->setID   ( 'txtNumeroRecibo' );
$obNumeroRecibo->setName ( 'txtNumeroRecibo' );
$obNumeroRecibo->setRotulo ( 'Número do Recibo' );
$obNumeroRecibo->setTitle  ( 'Informe o número do recibo.' );

/// busca de conta Despesa
$obPopUpContaDespesa = new IPopUpContaAnalitica ( $obEntidadeUsuario->obSelect );
$obPopUpContaDespesa->setID              ( 'innerContaDespesa'           );
$obPopUpContaDespesa->setName            ( 'innerContaDespesa'           );
$obPopUpContaDespesa->obCampoCod->setName( "inCodContaDespesa"           );
$obPopUpContaDespesa->setRotulo          ( 'Conta de Despesa'            );
$obPopUpContaDespesa->setTitle           ( 'Informe a conta de despesa.'  );
$obPopUpContaDespesa->setTipoBusca       ( 'tes_pagamento_extra_despesa' );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                 );
$obFormulario->addHidden     ( $obHdnAcao              );
$obFormulario->addHidden     ( $obHdnCtrl              );

$obFormulario->addComponente ( $obEntidadeUsuario      );
$obFormulario->addComponente ( $obTextData             );
$obFormulario->addComponente ( $obNumeroRecibo         );
$obFormulario->addComponente ( $obPopUpContaDespesa    );

$obFormulario->ok();
$obFormulario->show();

?>
