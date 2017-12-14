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
 * Página de Filtro do 02.10.03 - Manter Ação
 * Data de Criação: 16/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Jânio Eduardo Vasconcellos de Magalhães <janio.magalhaes>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.03 - Manter Ação
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_LDO_VISAO . 'VLDOManterLDO.class.php';

$rsPPA = VLDOManterLDO::recuperarInstancia()->recuperarPPA();

$stPrograma	= "ProcurarAcao";
$pgFilt 	= "FL".$stPrograma.".php";
$pgList 	= "LS".$stPrograma.".php";
$pgForm 	= "FM".$stPrograma.".php";
$pgProc 	= "PR".$stPrograma.".php";
$pgOcul 	= "OC".$stPrograma.".php";
$pgJS   	= "JS".$stPrograma.".js";

$sessao->link = "";

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$obForm = new Form;
$obForm->setAction($pgList);

$obHdnAcao = new Hidden;
$obHdnAcao->setName     ('stAcao');
$obHdnAcao->setValue    ($stAcao);

$obHdnForm = new Hidden();
$obHdnForm->setName     ('nomForm');
$obHdnForm->setValue    ($_REQUEST['nomForm']);

$obHdnCampoNum = new Hidden();
$obHdnCampoNum->setName ('campoNum');
$obHdnCampoNum->setValue($_REQUEST['campoNum']);

$obHdnCampoNom = new Hidden();
$obHdnCampoNom->setName ('campoNom');
$obHdnCampoNom->setValue($_REQUEST['campoNom']);

$obHdnTipoBusca = new Hidden();
$obHdnTipoBusca->setName ('tipoBusca');
$obHdnTipoBusca->setValue($_REQUEST['tipoBusca']);

$obHdnPPA = new Hidden();
$obHdnPPA->setName ('inCodPPA');
$obHdnPPA->setValue($rsPPA->getCampo('cod_ppa'));

$obTxtNumero = new TextBox;
$obTxtNumero->setName           ('inNumPrograma');
$obTxtNumero->setRotulo         ('Programa');
$obTxtNumero->setSize           (8);
$obTxtNumero->setMaxLength      (4);
$obTxtNumero->setNull           (true);
$obTxtNumero->setTitle          ('Escolha um codigo para programa');

$obRadioNaturezaTemporalContinuo = new Radio;
$obRadioNaturezaTemporalContinuo->setName       ('boNatureza');
$obRadioNaturezaTemporalContinuo->setRotulo     ('Natureza Temporal');
$obRadioNaturezaTemporalContinuo->setTitle      ('Informe a Natureza Temporal do Programa.');
$obRadioNaturezaTemporalContinuo->setValue      ('t');
$obRadioNaturezaTemporalContinuo->setLabel      ('Continuo');
$obRadioNaturezaTemporalContinuo->setNull       (false);
$obRadioNaturezaTemporalContinuo->setChecked    (true);

$obRadioNaturezaTemporalTemporal = new Radio;
$obRadioNaturezaTemporalTemporal->setName       ('boNatureza');
$obRadioNaturezaTemporalTemporal->setTitle      ('Informe a Natureza Temporal do Programa.');
$obRadioNaturezaTemporalTemporal->setValue      ('f');
$obRadioNaturezaTemporalTemporal->setLabel      ('Temporal');
$obRadioNaturezaTemporalTemporal->setNull       (false);

$obTextAreaIdPrograma = new TextArea;
$obTextAreaIdPrograma->setRotulo('Indentificação do Programa');
$obTextAreaIdPrograma->setName  ('inIdPrograma');
$obTextAreaIdPrograma->setNull  (true);

$obFormulario = new Formulario;
$obFormulario->addForm          ($obForm);
$obFormulario->addHidden        ($obHdnPPA);
$obFormulario->addHidden        ($obHdnAcao);
$obFormulario->addHidden        ($obHdnForm);
$obFormulario->addHidden        ($obHdnCampoNum);
$obFormulario->addHidden        ($obHdnCampoNom);
$obFormulario->addHidden        ($obHdnTipoBusca);

$obFormulario->addTitulo        ('Dados para Filtro');
$obFormulario->addComponente    ($obTxtNumero);

$obLblIntervalo = new Label();
$obLblIntervalo->setValue(' até ');

$obTxtAcaoInicio = new TextBox();
$obTxtAcaoInicio->setName('inNumAcaoInicio');
$obTxtAcaoInicio->setRotulo('Código Ação');
$obTxtAcaoInicio->setTitle('Informe o intervalo de Códigos de Ação a consultar.');
$obTxtAcaoInicio->setInteiro(true);

$obTxtAcaoFim= new TextBox();
$obTxtAcaoFim->setName('inNumAcaoFim');
$obTxtAcaoFim->setRotulo('Código Ação');
$obTxtAcaoFim->setInteiro(true);

$arTxtIntervaloAcao = array($obTxtAcaoInicio, $obLblIntervalo, $obTxtAcaoFim);
$obFormulario->agrupaComponentes($arTxtIntervaloAcao);

$obFormulario->ok();
$obFormulario->show();

$obIFrameMensagem = new IFrame;
$obIFrameMensagem->setName   ( "telaMensagem");
$obIFrameMensagem->setWidth  ( "100%"        );
$obIFrameMensagem->setHeight ( "50"          );

$obIFrameOculto = new IFrame;
$obIFrameOculto->setName("oculto");
$obIFrameOculto->setWidth("100%");
$obIFrameOculto->setHeight("0");

$obIFrameMensagem->show();
$obIFrameOculto->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
