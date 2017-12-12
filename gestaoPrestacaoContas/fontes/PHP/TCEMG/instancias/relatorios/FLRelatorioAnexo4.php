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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "RelatorioAnexo4";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
$pgGera = "OCGera".$stPrograma.".php";

include_once $pgJs;

$stAcao = $request->get('stAcao');

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//

$obForm = new Form;
$obForm->setTarget('telaPrincipal');
$obForm->setAction(CAM_GPC_TCEMG_RELATORIOS . $pgGera);

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue(CAM_GPC_TCEMG_RELATORIOS . $pgOcul);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName('stCtrl');
$obHdnCtrl->setValue('');

$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

// Define objeto Select para tipo do valor da despesa
$obCmbTipoRelatorio = new Select;
$obCmbTipoRelatorio->setRotulo( 'Situação'               );
$obCmbTipoRelatorio->setName  ( 'stTipoRelatorio'        );
$obCmbTipoRelatorio->setId    ( 'stTipoRelatorio'        );
$obCmbTipoRelatorio->addOption( ''         , 'Selecione' );
$obCmbTipoRelatorio->addOption( '1'        , 'Empenhado' );
$obCmbTipoRelatorio->addOption( '2'        , 'Liquidado' );
$obCmbTipoRelatorio->addOption( '3'        , 'Pago'      );
$obCmbTipoRelatorio->setNull  ( false                    );
$obCmbTipoRelatorio->obEvento->setOnChange("validaRestos(this.value);");

// define objeto Periodicidade
$obPeriodo = new Select;
$obPeriodo->setRotulo            ('Periodicidade');
$obPeriodo->setName              ('stPeriodicidade');
$obPeriodo->addOption            ('', 'Selecione');
$obPeriodo->addOption            ('Bimestre', 'Bimestre');
$obPeriodo->addOption            ('Trimestre', 'Trimestre');
$obPeriodo->addOption            ('Semestre', 'Semestre');
$obPeriodo->setNull              (false);
$obPeriodo->setStyle             ('width: 220px');
$obPeriodo->obEvento->setOnChange("buscaDado('preencheSpan')");

$obRestosSim = new Radio;
$obRestosSim->setRotulo    ("Considerar Restos a Pagar");
$obRestosSim->setName      ("boRestos");
$obRestosSim->setId        ("boRestos");
$obRestosSim->setLabel     ("Sim");
$obRestosSim->setValue     ('t');
$obRestosSim->setChecked   (false);

$obRestosNao = new Radio;
$obRestosNao->setRotulo    ("Considerar Restos a Pagar");
$obRestosNao->setName      ("boRestos");
$obRestosSim->setId        ("boRestos");
$obRestosNao->setLabel     ("Não");
$obRestosNao->setValue     ('f');
$obRestosNao->setChecked   (true);

// Define Objeto Span para Tipo de Relatorio
$obSpnPeriodo = new Span();
$obSpnPeriodo->setId('spnPeriodicidade');

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCaminho);

$obFormulario->addTitulo('Dados para o Filtro');

$obFormulario->addComponente ( $obCmbTipoRelatorio  );
if (isset($obPeriodo)) {
    $obFormulario->addComponente($obPeriodo);
}
$obFormulario->addSpan($obSpnPeriodo);
$obFormulario->agrupaComponentes(array($obRestosSim,$obRestosNao));

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
