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
 * Página de formulário de exclusão de Ação
 * Data de Criação: 24/10/2008

 * Copyright CNM - Confederação Nacional de Municípios

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @ignore

 * $Id: FMExcluirAcao.php 39027 2009-03-23 20:47:37Z fellipe.santos $

 * Caso de Uso: uc-02.09.04
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GA_NORMAS_COMPONENTES . 'IPopUpNorma.class.php';

include_once CAM_GF_PPA_VISAO   . 'VPPAManterAcao.class.php';
include_once CAM_GF_PPA_NEGOCIO . 'RPPAManterAcao.class.php';
include_once CAM_GF_PPA_VISAO   . 'VPPAUtils.class.php';

# Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ? $_GET['stAcao'] : $_POST['stAcao'];

if (empty($stAcao)) {
    $stAcao = 'excluir';
}

# Define o nome dos arquivos PHP
$stProjeto = 'ManterAcao';
$pgFilt = 'FL' . $stProjeto . '.php';
$pgList = 'LS' . $stProjeto . '.php';
$pgForm = 'FM' . $stProjeto . '.php';
$pgProc = 'PR' . $stProjeto . '.php';
$pgOcul = 'OC' . $stProjeto . '.php';
$pgJS   = 'JS' . $stProjeto . '.php';

include_once $pgJS;

# Recupera todos os dados da ação necessários.
$obRPPAManterAcao = new RPPAManterAcao();
$obVPPAManterAcao = new VPPAManterAcao($obRPPAManterAcao);

$rsAcao = $obVPPAManterAcao->recuperaAcao($_REQUEST);

# Definição do form
$obForm = new Form();
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');
$obForm->setEncType('multipart/form-data');

# Definição do Formulário
$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addTitulo('Detalhamento das Ações');

if (!$rsAcao->eof()) {
    $_REQUEST['inCodPPA']     = $rsAcao->getCampo('cod_ppa');
    $_REQUEST['boHomologado'] = $rsAcao->getCampo('homologado') == 't' ? true : false;

    # Se Ação não estiver homologada, apenas exclui direto.
    if ($_REQUEST['boHomologado'] === false) {
        include_once $pgProc;
        exit;
    }

    # Define PPA da ação.
    $obHdnPPA = new Hidden();
    $obHdnPPA->setName('inCodPPA');
    $obHdnPPA->setValue($_REQUEST['inCodPPA']);
    $obFormulario->addHidden($obHdnPPA);
}

# Definição de dados ocultos
$obHdnAcao = new Hidden();
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);
$obFormulario->addHidden($obHdnAcao);

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName('stCtrl');
$obHdnCtrl->setValue($stCtrl);
$obFormulario->addHidden($obHdnCtrl);

$obHdnCodAcao = new Hidden();
$obHdnCodAcao->setName('inCodAcao');
$obHdnCodAcao->setValue($_REQUEST['inCodAcao']);
$obFormulario->addHidden($obHdnCodAcao);

$obHdnCodProg = new Hidden();
$obHdnCodProg->setName('inCodPrograma');
$obHdnCodProg->setValue($_REQUEST['inCodPrograma']);
$obFormulario->addHidden($obHdnCodProg);

$obHdnCodAcao = new Hidden();
$obHdnCodAcao->setName('inCodAcao');
$obHdnCodAcao->setValue($_REQUEST['inCodAcao']);
$obFormulario->addHidden($obHdnCodAcao);

$obHdnStDesc = new Hidden();
$obHdnStDesc->setName('stDescricao');
$obHdnStDesc->setValue($_REQUEST['stDescricao']);
$obFormulario->addHidden($obHdnStDesc);

$obHdnTsAcaoDados= new Hidden();
$obHdnTsAcaoDados->setName('tsAcaoDados');
$obHdnTsAcaoDados->setValue($_REQUEST['tsAcaoDados']);
$obFormulario->addHidden($obHdnTsAcaoDados);

# Define label do programa.
$obLblPrograma = new Label();
$obLblPrograma->setRotulo('Programa');
$obLblPrograma->setValue($_REQUEST['inNumPrograma']);
$obFormulario->addComponente($obLblPrograma);

# Define label da ação.
$obLblAcao = new Label();
$obLblAcao->setRotulo('Ação');
$obLblAcao->setValue(sprintf('%03d', $_REQUEST['inCodAcao']));
$obFormulario->addComponente($obLblAcao);

# Define popup da norma para exclusão da ação.
$boHomologado = $obVPPAManterAcao->isPPAHomologado($_REQUEST['inCodPPA']);
if ($boHomologado) {
    $obIPopUpNorma = new IPopUpNorma();
    $obIPopUpNorma->obInnerNorma->obCampoCod->stId = 'inCodNorma';
    $obIPopUpNorma->obInnerNorma->setTitle('Define norma de exclusão.');
    $obIPopUpNorma->setExibeDataNorma(true);
    $obIPopUpNorma->setExibeDataPublicacao(true);
    $obIPopUpNorma->geraFormulario($obFormulario);
}

# Define botoes de ação.
$obFormulario->Cancelar($pgList."?stAcao=excluir");
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
