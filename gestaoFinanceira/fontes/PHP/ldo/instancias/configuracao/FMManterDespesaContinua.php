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
 * Página de formulário da Expansão das Despesas de Caráter Continuado
 * Data de Criação: 23/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Analista: Bruno Ferreira <bruno.ferreira>
 * @author Desenvolvedor: Pedro Vaz de Mello de Medeiros <pedro.medeiros>
 * @package gestaoFinanceira
 * @subpackage ldo
 * @uc uc-02.10.14
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GF_LDO_COMPONENTES . 'ISelectLDO.class.php';

$stAcao = $request->get('stAcao');

# Define o nome dos arquivos PHP
$stProjeto = 'ManterDespesaContinua';
$pgFilt = 'FL' . $stProjeto . '.php';
$pgList = 'LS' . $stProjeto . '.php';
$pgForm = 'FM' . $stProjeto . '.php';
$pgProc = 'PR' . $stProjeto . '.php';
$pgOcul = 'OC' . $stProjeto . '.php';
$pgJS   = 'JS' . $stProjeto . '.php';

# Incluindo JS no formulário
include_once $pgJS;

# Define formulário
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

# Define formulário
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);

# Define campos hidden
$obHdnAcao = new Hidden();
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);
$obFormulario->addHidden($obHdnAcao);

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName('stCtrl');
$obHdnCtrl->setValue($stAcao);
$obFormulario->addHidden($obHdnCtrl);

# Define o componente LDO
if ($stAcao == 'incluir') {
    $obISelectLDO = new ISelectLDO();
    $obISelectLDO->setVigenciaPPA(true);
    $obISelectLDO->setObrigatorio(true);
    $obISelectLDO->setValue($_REQUEST['inAnoLDO']);
} else {
    $obISelectLDO = new Label();
    $obISelectLDO->setValue($_REQUEST['inAnoLDO']);
    $obISelectLDO->setRotulo('*LDO');

    $obHdnAnoLDO = new Hidden();
    $obHdnAnoLDO->setName('inAnoLDO');
    $obHdnAnoLDO->setValue($_REQUEST['inAnoLDO']);
    $obFormulario->addHidden($obHdnAnoLDO);

    $obHdnCodDespesa = new Hidden();
    $obHdnCodDespesa->setName('inCodDespesa');
    $obHdnCodDespesa->setValue($_REQUEST['inCodDespesa']);
    $obFormulario->addHidden($obHdnCodDespesa);
}

# Define campo Aumento Permanente de Receita
$obTxtAumentoPermanente = new Moeda();
$obTxtAumentoPermanente->setName('flAumentoPermanente');
$obTxtAumentoPermanente->setID('flAumentoPermanente');
$obTxtAumentoPermanente->setRotulo('Aumento Permanente da Receita');
$obTxtAumentoPermanente->setValue($_REQUEST['flAumentoPermanente']);
$obTxtAumentoPermanente->setObrigatorio(true);

# Define campo Transferências Constitucionais
$obTxtTransConstitucionais = new Moeda();
$obTxtTransConstitucionais->setName('flTransConstitucional');
$obTxtTransConstitucionais->setID('flTransConstitucional');
$obTxtTransConstitucionais->setRotulo('Transferências Constitucionais');
$obTxtTransConstitucionais->setValue($_REQUEST['flTransConstitucional']);
$obTxtTransConstitucionais->setObrigatorio(true);

# Define campo Transferências ao FUNDEB
$obTxtTransFUNDEB = new Moeda();
$obTxtTransFUNDEB->setName('flTransFUNDEB');
$obTxtTransFUNDEB->setID('flTransFUNDEB');
$obTxtTransFUNDEB->setRotulo('Transferências ao FUNDEB');
$obTxtTransFUNDEB->setValue($_REQUEST['flTransFUNDEB']);
$obTxtTransFUNDEB->setObrigatorio(true);

# Define campo Redução Permanente de Despesa
$obTxtReducaoDespesa = new Moeda();
$obTxtReducaoDespesa->setName('flReducaoPermanente');
$obTxtReducaoDespesa->setID('flReducaoPermanente');
$obTxtReducaoDespesa->setRotulo('Redução Permanente de Despesa');
$obTxtReducaoDespesa->setValue($_REQUEST['flReducaoPermanente']);
$obTxtReducaoDespesa->setObrigatorio(true);

$obTxtMargemBruta = new Moeda();
$obTxtMargemBruta->setName('flMargemBruta');
$obTxtMargemBruta->setID('flMargemBruta');
$obTxtMargemBruta->setRotulo('Saldo Utilizado da Margem Bruta');
$obTxtMargemBruta->setValue($_REQUEST['flMargemBruta']);
$obTxtMargemBruta->setObrigatorio(true);

# Define campo Redução Permanente de Despesa
$obTxtDOCC= new Moeda();
$obTxtDOCC->setName('flDOCC');
$obTxtDOCC->setID('flDOCC');
$obTxtDOCC->setRotulo('Novas DOCC');
$obTxtDOCC->setValue($_REQUEST['flDOCC']);
$obTxtDOCC->setObrigatorio(true);

# Define campo Redução Permanente de Despesa
$obTxtDOCCPPP= new Moeda();
$obTxtDOCCPPP->setName('flDOCCPPP');
$obTxtDOCCPPP->setID('flDOCCPPP');
$obTxtDOCCPPP->setRotulo('Novas DOCC geradas por PPP');
$obTxtDOCCPPP->setValue($_REQUEST['flDOCCPPP']);
$obTxtDOCCPPP->setObrigatorio(true);

# Define título
$stComando = ($stAcao == 'incluir') ? 'Inclusão' : 'Alteração';
$obFormulario->addTitulo('Dados para ' . $stComando . ' da expansão das despesas de caráter continuado');

# Adicionando os Componentes
$obFormulario->addComponente($obISelectLDO);
$obFormulario->addComponente($obTxtAumentoPermanente);
$obFormulario->addComponente($obTxtTransConstitucionais);
$obFormulario->addComponente($obTxtTransFUNDEB);
$obFormulario->addComponente($obTxtReducaoDespesa);
$obFormulario->addComponente($obTxtMargemBruta);
$obFormulario->addComponente($obTxtDOCC);
$obFormulario->addComponente($obTxtDOCCPPP);

# Define botões do formulário.
$obBtnOK = new OK(true);

if ($stAcao == 'alterar') {
    $obBtnAlt = new Cancelar();
    $stCaminho = $pgList . '?' . Sessao::getID() . '&stAcao=' . $stAcao;
    $obBtnAlt->obEvento->setOnClick("Cancelar('$stCaminho', 'telaPrincipal');");
} else {
    $obBtnAlt = new Limpar();
}

$arBtnForm = array($obBtnOK, $obBtnAlt);
$obFormulario->defineBarra($arBtnForm);

# Gerando a tela
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
