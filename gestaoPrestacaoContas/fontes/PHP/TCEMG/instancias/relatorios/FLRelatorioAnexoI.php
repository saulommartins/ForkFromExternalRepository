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
  * Página de Formulario de Configuração de Orgão
  * Data de Criação: 07/01/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: FLRelatorioAnexoI.php 59612 2014-09-02 12:00:51Z gelson $
  * $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
  * $Author: gelson $
  * $Rev: 59612 $
  *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioAnexoI";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OCGera".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgOcul );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define objeto Periodicidade
$obPeriodo = new Periodicidade;
$obPeriodo->setExercicio       (  Sessao::getExercicio() );
$obPeriodo->setNull            ( false );
$obPeriodo->setValidaExercicio ( true  );
$obPeriodo->setValue           ( 4 );

//Define objeto Situação
$obCmbSituacao = new Select();
$obCmbSituacao->setName   ( "stSituacao" );
$obCmbSituacao->setId     ( "stSituacao" );
$obCmbSituacao->setRotulo ( "Situação"   );
$obCmbSituacao->setTitle  ( "Informe o Tipo de Situação." );
$obCmbSituacao->setNull   ( false );
$obCmbSituacao->addOption ( "","Selecione"           );
$obCmbSituacao->addOption ( "empenhado" ,"Empenhado" );
$obCmbSituacao->addOption ( "liquidado" ,"Liquidado" );
$obCmbSituacao->addOption ( "pago"      ,"Pago"      );

//Define objeto Situação
$obCmbRestos = new Select();
$obCmbRestos->setName   ( "stRestos" );
$obCmbRestos->setId     ( "stRestos" );
$obCmbRestos->setRotulo ( "Considerar Restos a Pagar"   );
$obCmbRestos->setTitle  ( "Informe se deseja considerar os restos a pagar." );
$obCmbRestos->setNull   ( false );
$obCmbRestos->addOption ( "true"  ,"Sim"      );
$obCmbRestos->addOption ( "false" ,"Não"      );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addTitulo     ( "Filtro Anexo I" );
$obFormulario->addHidden     ( $obHdnAcao       );
$obFormulario->addHidden     ( $obHdnCtrl       );
$obFormulario->addComponente ( $obPeriodo       );
$obFormulario->addComponente ( $obCmbSituacao   );
$obFormulario->addComponente ( $obCmbRestos     );
$obFormulario->OK();
$obFormulario->show();

$jsOnLoad = "jQuery('#stRestos').prop('disabled',true);
             jQuery('#stSituacao').change(function(){
                if( jQuery(this).val() == 'empenhado'){
                    jQuery('#stRestos').prop('disabled',true);
                }else{
                    jQuery('#stRestos').prop('disabled',false);
                }
             });
            ";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>