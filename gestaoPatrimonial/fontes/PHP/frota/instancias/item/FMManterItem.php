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
    * Data de Criação: 22/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    $Id: FMManterItem.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.12

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GP_FRO_MAPEAMENTO.'TFrotaItem.class.php';
include_once CAM_GP_FRO_MAPEAMENTO.'TFrotaTipoItem.class.php';
include_once CAM_GP_FRO_MAPEAMENTO.'TFrotaCombustivel.class.php';

include_once CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php";
include_once CAM_GP_ALM_COMPONENTES."IMontaCatalogoClassificacao.class.php";

$stPrograma = "ManterItem";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$rsItem = new RecordSet;

if ($stAcao == 'alterar') {
    $obTFrotaItem = new TFrotaItem;
    $obTFrotaItem->setDado('cod_item', $_REQUEST['inCodItem']);
    $obTFrotaItem->recuperaRelacionamento($rsItem);

    # Método que verifica se o ítem não está sendo ou foi utilizado por alguma
    # manutenção ou alguma autorização.
    $obTFrotaItem->recuperaPermissaoAlterarItem( $rsPermissaoAlterar );
    $boPermissaoAlterar = $rsPermissaoAlterar->getCampo('permissao');
}

$obSelectTipoCadastro = new Select;
$obSelectTipoCadastro->setId                 ('inCodTipoCadastro');
$obSelectTipoCadastro->setName               ('inCodTipoCadastro');
$obSelectTipoCadastro->setRotulo             ('Tipo de Cadastro');
$obSelectTipoCadastro->setNull               (false);
$obSelectTipoCadastro->addOption             ('', 'Selecione', 'selected');
$obSelectTipoCadastro->addOption             ('1', 'Único Item', '');
$obSelectTipoCadastro->addOption             ('2', 'Classificação do Catálogo', '');
$obSelectTipoCadastro->obEvento->setOnChange ("montaParametrosGET('montaFormCadastro', '');");

$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget ("oculto");

$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

$obHdPermissaoAlterar = new Hidden;
$obHdPermissaoAlterar->setName('obHdPermissaoAlterar');
$obHdPermissaoAlterar->setValue($boPermissaoAlterar);

$obSpnForm = new Span;
$obSpnForm->setId( 'spnForm' );

$obSpnCombustivel = new Span;
$obSpnCombustivel->setId( 'spnCombustivel' );

$obBtnVoltar = new Button;
$obBtnVoltar->setValue("Voltar");
$obBtnVoltar->obEvento->setOnClick("document.location.href = '".$pgList."?stAcao=alterar';");

$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->addHidden ( $obHdPermissaoAlterar );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );

$obFormulario->addTitulo ( 'Dados do Item' );

if ($stAcao == 'incluir') {
    $obFormulario->addComponente( $obSelectTipoCadastro );
}

$obFormulario->addSpan ( $obSpnForm );
$obFormulario->addSpan ( $obSpnCombustivel );

if ($stAcao == 'alterar' && $boPermissaoAlterar == 'false') {
    $obFormulario->defineBarra ( array($obBtnVoltar), "left", "&nbsp;" );
} elseif ($stAcao == 'alterar' && $boPermissaoAlterar == 'true') {
    $obFormulario->Cancelar($pgList."?stAcao=".$stAcao);
} else {
    $obFormulario->OK();
}

$obFormulario->show();

include_once $pgJs;

if ($stAcao == 'alterar') {
    $jsOnLoad = "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodItem=".$_REQUEST['inCodItem']."&stAcao=alterar','montaFormCadastro' );";
    $jsOnLoad .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&slTipoItem=".$rsItem->getCampo('cod_tipo')."&inCodItem=".$_REQUEST['inCodItem']."&inCodCombustivel=".$rsItem->getCampo('cod_combustivel')."&obHdPermissaoAlterar=".$boPermissaoAlterar."','montaCombustivel' );";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
