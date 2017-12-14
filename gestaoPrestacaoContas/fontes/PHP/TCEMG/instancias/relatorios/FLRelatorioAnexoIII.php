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
  * Página de Formulario Relatorio Anexo III - TCEMG
  * Data de Criação: 21/07/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Evandro Melos
  *
  * @ignore
  * $Id: FLRelatorioAnexoIII.php 59612 2014-09-02 12:00:51Z gelson $
  * $Date: $
  * $Author: $
  * $Rev: $
  *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_ORC_COMPONENTES  . 'ISelectMultiploEntidadeUsuario.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioAnexoIII";
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

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obISelectEntidade = new ISelectMultiploEntidadeUsuario();

//Define objeto Periodicidade
$obPeriodo = new Periodicidade;
$obPeriodo->setExercicio   (  Sessao::getExercicio() );
$obPeriodo->setNull            (false );
$obPeriodo->setValidaExercicio ( true );
$obPeriodo->setValue           ( 4);

// Define objeto Select para tipo do valor da despesa
$obCmbTipoRelatorio = new Select;
$obCmbTipoRelatorio->setRotulo( 'Situação'               );
$obCmbTipoRelatorio->setName  ( 'stTipoRelatorio'        );
$obCmbTipoRelatorio->setId    ( 'stTipoRelatorio'        );
$obCmbTipoRelatorio->addOption( ''         , 'Selecione' );
$obCmbTipoRelatorio->addOption( '1'     , 'Empenhado'    );
$obCmbTipoRelatorio->addOption( '2'     , 'Liquidado'    );
$obCmbTipoRelatorio->addOption( '3'     , 'Pago'         );
$obCmbTipoRelatorio->setNull  ( false                    );

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
$obFormulario->addTitulo     ( "Filtro Anexo III"  );
$obFormulario->addHidden     ( $obHdnAcao   );
$obFormulario->addHidden     ( $obHdnCtrl   );
$obFormulario->addComponente ( $obISelectEntidade );
$obFormulario->addComponente ( $obPeriodo   );
$obFormulario->addComponente ( $obCmbTipoRelatorio  );
$obFormulario->addComponente ( $obCmbRestos     );
$obFormulario->OK();
$obFormulario->show();

$jsOnLoad = "jQuery('#stRestos').prop('disabled',true);
             jQuery('#stTipoRelatorio').change(function(){
                if( jQuery(this).val() == 1){
                    jQuery('#stRestos').prop('disabled',true);
                }else{
                    jQuery('#stRestos').prop('disabled',false);
                }
             });
            ";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>