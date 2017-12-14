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
 * Pagina de PopUp Receita tipo do uc-02.10.04
 * Data de Criação: 17/02/209
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author <analista> Bruno Ferreira Santos <bruno.ferreira>
 * @author <desenvolvedor> Jânio Eduardo Vasconcellos de Magalhães <janio.magalhaes>
 * @package GF
 * @subpackage ldo
 * @uc uc-02.10.04
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
$inAnoExercicio = substr($_SESSION['exercicio'], 5,-2);

for ($i = 0; $i < $inCount; $i++) {
    $arCampos = $rsRecordSet->arElementos[$i];

    if ($arCampos['ano_inicio'] <= $inAnoExercicio && $inAnoExercicio <= $arCampos['ano_final']) {
        $inCodPPA = $arCampos['cod_ppa'];
    }
}

//Define o nome dos arquivos PHP
$stPrograma	= "ProcurarReceita";
$pgFilt 	= "FL".$stPrograma.".php";
$pgList 	= "LS".$stPrograma.".php";
$pgForm 	= "FM".$stPrograma.".php";
$pgProc 	= "PR".$stPrograma.".php";
$pgOcul 	= "OC".$stPrograma.".php";
$pgJS   	= "JS".$stPrograma.".js";

$sessao->link = "";

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

$obHdnTipoBusca = new Hidden();
$obHdnTipoBusca->setName ('tipoBusca');
$obHdnTipoBusca->setValue($_REQUEST['tipoBusca']);

$obITextBoxSelectPPA = new ITextBoxSelectPPA();
$obITextBoxSelectPPA->setCodPPA($inCodPPA);
$obITextBoxSelectPPA->setNull(false);

$obHdnExibeValorReceita = new Hidden();
$obHdnExibeValorReceita->setName ('boExibeValorReceita');
$obHdnExibeValorReceita->setValue($_REQUEST['boExibeValorReceita']);

$obTxtReceita = new TextBox;
$obTxtReceita->setName           ('inDescricaoReceita');
$obTxtReceita->setRotulo         ('Descrição da receita');
$obTxtReceita->setSize           (50);
$obTxtReceita->setMaxLength      (50);
$obTxtReceita->setNull           (true);
$obTxtReceita->setTitle          ('Informe a descrição da receita');

$obFormulario = new Formulario;
$obFormulario->addForm          ($obForm);
$obFormulario->addHidden        ($obHdnAcao);
$obFormulario->addHidden        ($obHdnForm);
$obFormulario->addHidden        ($obHdnCampoNum);
$obFormulario->addHidden        ($obHdnCampoNom);
$obFormulario->addHidden        ($obHdnTipoBusca);
$obFormulario->addHidden        ($obHdnExibeValorReceita);

$obFormulario->addTitulo        ('Dados para Filtro');
$obFormulario->addComponente    ($obTxtReceita);

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
