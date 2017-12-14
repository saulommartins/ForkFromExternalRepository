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
 * Pagina de Lista tipo do uc-02.10.04
 * Data de Criação: 09/03/2009
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
include_once CAM_GA_NORMAS_COMPONENTES . 'IPopUpNorma.class.php';
include_once CAM_GF_LDO_VISAO          . 'VLDOManterLDO.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".php";

include_once $pgJS;

$obIPopUpNorma = new IPopUpNorma();
$obIPopUpNorma->obInnerNorma->setTitle('Define norma de alteração.');
$obIPopUpNorma->obInnerNorma->obCampoCod->stId = 'inCodNorma';
$obIPopUpNorma->obInnerNorma->setRotulo("Número da Norma");
$obIPopUpNorma->obLblDataNorma->setRotulo( "Data da Norma" );
$obIPopUpNorma->obLblDataPublicacao->setRotulo( "Data da Publicação" );
$obIPopUpNorma->setExibeDataNorma(true);
$obIPopUpNorma->setExibeDataPublicacao(true);

$obHdnNumReceita = new Hidden();
$obHdnNumReceita->setName('inNumReceita');
$obHdnNumReceita->setValue($_REQUEST['inNumReceita']);

$obHdnCodReceita = new Hidden();
$obHdnCodReceita->setName('inCodReceita');
$obHdnCodReceita->setValue($_REQUEST['inCodReceita']);

$obHdnCodPPA = new Hidden();
$obHdnCodPPA->setName('inCodPPA');
$obHdnCodPPA->setValue($_REQUEST['inCodPPA']);

$obHdnTotalReceita = new Hidden();
$obHdnTotalReceita->setName('inTotalReceita');
$obHdnTotalReceita->setValue($_REQUEST['inTotalLancado']);

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" ); //oculto - telaPrincipal

$rsLDO = VLDOManterLDO::recuperarInstancia()->recuperarLDO();

$obHdnAnoLDO = new Hidden();
$obHdnAnoLDO->setName('stAnoLDO');
$obHdnAnoLDO->setValue($rsLDO->getCampo('ano'));

$obHdnAcao = new Hidden();
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($_REQUEST['stAcao']);

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addTitulo('Dados para Exclusão da receita ');
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnAnoLDO);
$obFormulario->addHidden($obHdnTotalReceita);
$obFormulario->addHidden($obHdnNumReceita);
$obFormulario->addHidden($obHdnCodReceita);
$obFormulario->addHidden($obHdnCodPPA);
$obIPopUpNorma->geraFormulario($obFormulario);
//$obFormulario->Cancelar($pgFilt."?stAcao=excluir");

$stLink = CAM_GF_LDO_INSTANCIAS . 'receita/'.$pgProc.'?';

foreach($_REQUEST as $stKey => $stValue)
    $stLink.= '*_*'.$stKey.'='.$stValue;

$obBtnOK = new OK();
 $stJs.="if (validarNorma()) confirmPopUp('Exclusão de Receita do LDO','Deseja realmente excluir esta receita (".$_REQUEST['stDescQuestao'].")?','document.forms[0].submit()');";
$obBtnOK->obEvento->setOnClick($stJs);

$obBtnLimpar = new Cancelar();
$obBtnLimpar->obEvento->setOnClick('cancelarReceita();');

$arBotoes = array($obBtnOK, $obBtnLimpar);

$obFormulario->defineBarra($arBotoes);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
