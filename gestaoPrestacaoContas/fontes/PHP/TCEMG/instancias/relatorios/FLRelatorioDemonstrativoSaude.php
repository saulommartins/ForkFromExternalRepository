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
    * Página de Filtro para Relatório de Demonstrativo da Aplicação nas Ações e Serviços Púb. de Saúde
    * Data de Criação   : 10/07/2014
    * @author Desenvolvedor: Eduardo Paculski Schitz
    * @ignore
    *   
    * $Id: 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_ORC_COMPONENTES  . 'ISelectMultiploEntidadeUsuario.class.php';
include_once CAM_GA_ADM_COMPONENTES  . 'IMontaAssinaturas.class.php';
include_once CAM_GF_PPA_COMPONENTES  .'ITextBoxSelectOrgao.class.php';

$pgOcul = 'OCRelatorioDemonstrativoSaude.php';
$pgGera = 'OCGeraRelatorioDemonstrativoSaude.php';

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setTarget ( 'telaPrincipal' );
$obForm->setAction($pgGera);

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obISelectEntidade = new ISelectMultiploEntidadeUsuario();
$obISelectEntidade->setNomeLista2('inCodEntidade');

// define objeto Periodicidade
$obPeriodo = new Periodicidade;
$obPeriodo->setExercicio      ( Sessao::getExercicio() );
$obPeriodo->setNull           ( false );
$obPeriodo->setValidaExercicio( true );
$obPeriodo->setValue          ( 4);

$obSelectOrgao = new ITextBoxSelectOrgao;
$obSelectOrgao->setRotulo('Órgão');
$obSelectOrgao->obTextBox->setSize       ( 3 );
$obSelectOrgao->obTextBox->setMaxLength  ( 2 );
$obSelectOrgao->obSelect->setStyle       ( "width: 520"                               );
$obSelectOrgao->obTextBox->obEvento->setOnChange("montaParametrosGET('MontaUnidade');");
$obSelectOrgao->obSelect->obEvento->setOnChange ("montaParametrosGET('MontaUnidade');");

$obCmbTipoRelatorio = new Select;
$obCmbTipoRelatorio->setRotulo('Despesa');
$obCmbTipoRelatorio->setName  ('stDemonstrarDespesa');
$obCmbTipoRelatorio->setId    ('stDemonstrarDespesa');
$obCmbTipoRelatorio->setStyle ('width: 200px'  );
$obCmbTipoRelatorio->addOption('', 'Selecione' );
$obCmbTipoRelatorio->addOption('E', 'Empenhada');
$obCmbTipoRelatorio->addOption('L', 'Liquidada');
$obCmbTipoRelatorio->addOption('P', 'Paga'     );
$obCmbTipoRelatorio->setNull  (false );

$obCmbRestos = new Select();
$obCmbRestos->setName   ( "stRestos" );
$obCmbRestos->setId     ( "stRestos" );
$obCmbRestos->setRotulo ( "Considerar Restos a Pagar"   );
$obCmbRestos->setTitle  ( "Informe se deseja considerar os restos a pagar." );
$obCmbRestos->setNull   ( false );
$obCmbRestos->addOption ( "true"  ,"Sim"      );
$obCmbRestos->addOption ( "false" ,"Não"      );

// Instanciação do objeto Lista de Assinaturas
// Limpa papeis das Assinaturas na Sessão
$arAssinaturas = Sessao::read('assinaturas');
$arAssinaturas['papeis'] = array();
Sessao::write('assinaturas',$arAssinaturas);
Sessao::write('relatorio', $stAcao);

$obMontaAssinaturas = new IMontaAssinaturas;

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden( $obHdnAcao );

$obFormulario->addTitulo( "Dados para o filtro" );

$obFormulario->addComponente($obISelectEntidade );
$obFormulario->addComponente($obPeriodo);
$obFormulario->addComponente($obCmbTipoRelatorio);
$obFormulario->addComponente($obCmbRestos     );

$obMontaAssinaturas->setEventosCmbEntidades($obISelectEntidade);

$obMontaAssinaturas->geraFormulario($obFormulario);
$obOk  = new Ok;
$obOk->setId ("Ok");

$obLimpar = new Button;
$obLimpar->setValue( "Limpar" );
$obLimpar->obEvento->setOnClick( "frm.reset();" );

$obFormulario->defineBarra( array( $obOk, $obLimpar ) );

$obFormulario->show();

$jsOnLoad = "jQuery('#stRestos').prop('disabled',true);
             jQuery('#stDemonstrarDespesa').change(function(){
                if( jQuery(this).val() == 'E' || jQuery(this).val() == ''){
                    jQuery('#stRestos').prop('disabled',true);
                }else{
                    jQuery('#stRestos').prop('disabled',false);
                }
             });
            ";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>