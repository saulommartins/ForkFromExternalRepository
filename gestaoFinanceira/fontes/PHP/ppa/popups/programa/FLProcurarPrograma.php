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
* Página de Filtro de Procura de Programas
* Data de Criação   : 21/10/2008

* @author Analista: Heleno Santos
* @author Desenvolvedor: Fellipe Esteves dos Santos
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once(CAM_GF_PPA_COMPONENTES."ITextBoxSelectPPA.class.php");
require_once(CAM_GF_PPA_NEGOCIO."/RPPAHomologarPPA.class.php");
require_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/visao/VPPAHomologarPPA.class.php';

//Instanciando a Classe de Controle e de Visao de Homologar para Trazer o PPA vigente pelo Exercício
$obController = new RPPAHomologarPPA;
$obVisao = new VPPAHomologarPPA($obController);

$rsRecordSet = $obVisao->pesquisaPPANorma($stFiltro);

$inCount = count($rsRecordSet->arElementos);
$inAnoExercicio = substr(Sessao::getExercicio(), 5,-2);

for ($i = 0; $i < $inCount; $i++) {
    $arCampos = $rsRecordSet->arElementos[$i];

    if ($arCampos['ano_inicio'] <= $inAnoExercicio && $inAnoExercicio <= $arCampos['ano_final']) {
        $inCodPPA = $arCampos['cod_ppa'];
    }
}

//Define o nome dos arquivos PHP
$stPrograma	= "ProcurarPrograma";
$pgFilt 	= "FL".$stPrograma.".php";
$pgList 	= "LS".$stPrograma.".php";
$pgForm 	= "FM".$stPrograma.".php";
$pgProc 	= "PR".$stPrograma.".php";
$pgOcul 	= "OC".$stPrograma.".php";
$pgJS   	= "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgList);

// Definicao dos objetos hidden
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

$obHdnCodPPA = new Hidden();
$obHdnCodPPA->setName ('inCodPPA');
$obHdnCodPPA->setValue($_REQUEST['inCodPPA']);

$obTxtNumero = new TextBox;
$obTxtNumero->setName     ('inNumPrograma');
$obTxtNumero->setRotulo   ('Programa');
$obTxtNumero->setSize     (8);
$obTxtNumero->setMaxLength(4);
$obTxtNumero->setNull     (true);
$obTxtNumero->setTitle    ('Escolha um codigo para programa');
$obTxtNumero->setInteiro  (true);

$obRadioNaturezaTemporalTodos = new Radio;
$obRadioNaturezaTemporalTodos->setName       ('boNatureza');
$obRadioNaturezaTemporalTodos->setRotulo     ('Natureza Temporal');
$obRadioNaturezaTemporalTodos->setTitle      ('Informe a Natureza Temporal do Programa.');
$obRadioNaturezaTemporalTodos->setValue      ('n');
$obRadioNaturezaTemporalTodos->setLabel      ('Todos');
$obRadioNaturezaTemporalTodos->setNull       (false);
$obRadioNaturezaTemporalTodos->setChecked    (true);

$obRadioNaturezaTemporalContinuo = new Radio;
$obRadioNaturezaTemporalContinuo->setName       ('boNatureza');
$obRadioNaturezaTemporalContinuo->setTitle      ('Informe a Natureza Temporal do Programa.');
$obRadioNaturezaTemporalContinuo->setValue      ('t');
$obRadioNaturezaTemporalContinuo->setLabel      ('Continuo');
$obRadioNaturezaTemporalContinuo->setNull       (false);

$obRadioNaturezaTemporalTemporal = new Radio;
$obRadioNaturezaTemporalTemporal->setName       ('boNatureza');
$obRadioNaturezaTemporalTemporal->setTitle      ('Informe a Natureza Temporal do Programa.');
$obRadioNaturezaTemporalTemporal->setValue      ('f');
$obRadioNaturezaTemporalTemporal->setLabel      ('Temporário');
$obRadioNaturezaTemporalTemporal->setNull       (false);

$obTextAreaIdPrograma = new TextArea;
$obTextAreaIdPrograma->setRotulo('Indentificação do Programa');
$obTextAreaIdPrograma->setName  ('inIdPrograma');
$obTextAreaIdPrograma->setNull  (true);

$obFormulario = new Formulario;
$obFormulario->addForm          ($obForm);
$obFormulario->addHidden        ($obHdnAcao);
$obFormulario->addHidden        ($obHdnForm);
$obFormulario->addHidden        ($obHdnCampoNum);
$obFormulario->addHidden        ($obHdnCampoNom);
$obFormulario->addHidden        ($obHdnCodPPA);

$obFormulario->addTitulo        ('Dados para Filtro');
$obFormulario->addComponente    ($obTxtNumero);
$obFormulario->agrupaComponentes(array($obRadioNaturezaTemporalTodos,$obRadioNaturezaTemporalContinuo,$obRadioNaturezaTemporalTemporal));
$obFormulario->addComponente    ($obTextAreaIdPrograma);

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

?>
