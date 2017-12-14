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
    * Página de Filtro para Relatório de Demonstrativo de Gastos com Pessoal
    * Data de Criação   : 09/07/2014
    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal
    * @ignore
    *   
    * $Id: 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO   . 'TAdministracaoConfiguracao.class.php';

Sessao::remove('arValores');
Sessao::remove('filtroRelatorio');

$pgOcul = 'OCRelatorioDemonstrativoPessoal.php';

$jsOnLoad = isset($jsOnLoad) ? $jsOnLoad : '';
$stJs     = isset($stJs)     ? $stJs     : '';

$stAcao = $request->get('stAcao');

$obForm = new Form;

$obForm->setTarget ( 'telaPrincipal' );
$pgGera = 'OCGeraRelatorioDemonstrativoPessoal.php';

$obForm->setAction($pgGera);

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnPgGera = new Hidden;
$obHdnPgGera->setName ( "pgGera" );
$obHdnPgGera->setValue( $pgGera );

$obPeriodo = new Periodicidade;
$obPeriodo->setExercicio   (  Sessao::getExercicio() );
$obPeriodo->setNull            (false );
$obPeriodo->setValidaExercicio ( true );
$obPeriodo->setValue           ( 4);

$obSpnLista = new Span;
$obSpnLista->setID("spnLista");

// Defini objeto select para o tipo de periodo: Ex.: Bimestre, Trimestre e Semestre
$obCmbTipoPeriodo = new Select;
$obCmbTipoPeriodo->setRotulo( 'Período'        );
$obCmbTipoPeriodo->setName  ( 'inTipoPeriodo'  );
$obCmbTipoPeriodo->setId    ( 'inTipoPeriodo'  );
$obCmbTipoPeriodo->addOption( ''         , 'Selecione' );
$obCmbTipoPeriodo->addOption( 'Semestre' , 'Semestre'  );
$obCmbTipoPeriodo->addOption( 'Bimestre' , 'Bimestre'  );
$obCmbTipoPeriodo->addOption( 'Trimestre', 'Trimestre' );
$obCmbTipoPeriodo->setNull  ( false            );
$obCmbTipoPeriodo->obEvento->setOnChange("montaParametrosGET( 'preencheSpan' );");

// Defini o Span para montar o perido referente selecionado acima
$spnCmbPeriodo = new Span();
$spnCmbPeriodo->setId( 'spnPeriodo' );

// Define objeto Select para tipo do valor da despesa
$obCmbTipoRelatorio = new Select;
$obCmbTipoRelatorio->setRotulo( 'Situação'            );
$obCmbTipoRelatorio->setName  ( 'inTipoRelatorio'     );
$obCmbTipoRelatorio->setId    ( 'inTipoRelatorio'     );
$obCmbTipoRelatorio->addOption( ''      , 'Selecione' );
$obCmbTipoRelatorio->addOption( '1'     , 'Empenhado' );
$obCmbTipoRelatorio->addOption( '2'     , 'Liquidado' );
$obCmbTipoRelatorio->addOption( '3'     , 'Pago'      );
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

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnPgGera );

$obFormulario->addTitulo( "Dados para o filtro" );
$obFormulario->addComponente($obCmbTipoPeriodo);
$obFormulario->addSpan($spnCmbPeriodo);

$obFormulario->addComponente($obCmbTipoRelatorio);
$obFormulario->addComponente ( $obCmbRestos     );

$obOk  = new Ok;
$obOk->setId ("Ok");

$obLimpar = new Button;
$obLimpar->setValue( "Limpar" );
$obLimpar->obEvento->setOnClick( "frm.reset();" );

$obFormulario->defineBarra( array( $obOk, $obLimpar ) );

$obFormulario->show();

$jsOnLoad = "jQuery('#stRestos').prop('disabled',true);
             jQuery('#inTipoRelatorio').change(function(){
                if( jQuery(this).val() == 1 || jQuery(this).val() == '' || jQuery(this).val() == 2 ){
                    jQuery('#stRestos').prop('disabled',true);
                }else{
                    jQuery('#stRestos').prop('disabled',false);
                }
             });
            ";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
