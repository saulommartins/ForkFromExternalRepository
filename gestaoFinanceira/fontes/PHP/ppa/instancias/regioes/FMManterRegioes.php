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
    * Página de Formulario de Inclusao/Alteracao de Regiões
    * Data de Criação   : 22/09/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Marcio Medeiros

    * @package URBEM
    * @subpackage

    * @ignore

    * Casos de uso: uc-02.09.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_PPA_MAPEAMENTO."TPPARegiao.class.php");
include_once(CAM_GF_PPA_VISAO."VPPAManterRegiao.class.php");
include_once(CAM_GF_PPA_NEGOCIO."RPPAManterRegiao.class.php");

//Define o nome dos arquivos PHP
$stProjeto = "ManterRegioes";
$pgFilt = "FL".$stProjeto.".php";
$pgList = "LS".$stProjeto.".php";
$pgForm = "FM".$stProjeto.".php";
$pgProc = "PR".$stProjeto.".php";
$pgOcul = "OC".$stProjeto.".php";
$pgJS   = "JS".$stProjeto.".php";

include_once($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

if (empty($stAcao)) {
    $stAcao = "incluir";
}

if ($stAcao == "alterar") {
   $obTPPARegiao = new TPPARegiao();
   $obTPPARegiao->setDado('cod_regiao', $_GET['inCodRegiao']);
   $obTPPARegiao->recuperaPorChave($rsRegiao);
   $inCodRegiao = $rsRegiao->getCampo('cod_regiao');
   $stNome      = $rsRegiao->getCampo('nome');
   $stDescricao = $rsRegiao->getCampo('descricao');
}

#$jsOnload = "executaFuncaoAjax('montaSpanLista')";

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//

//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue($stAcao);

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue("");

$obTxtNome = new TextBox;
$obTxtNome->setName("stNome");
$obTxtNome->setId("stNomeRegiao");
$obTxtNome->setRotulo("Nome da Região"  );
$obTxtNome->setTitle("Informe o Nome da Região.");
$obTxtNome->setSize(80);
$obTxtNome->setMaxLength(80);
$obTxtNome->setNull(false);
$obTxtNome->obEvento->setOnBlur("checarCadastroRegiao('".$stNome."');");

$obTxtDescricao = new TextBox;
$obTxtDescricao->setName("stDescricao");
$obTxtDescricao->setRotulo("Descrição/Área Abrangência");
$obTxtDescricao->setTitle("Informe Descrição/Àrea Abrangência da Região.");
$obTxtDescricao->setSize(80);
$obTxtDescricao->setMaxLength(240);
$obTxtDescricao->setNull(true);

$obSpanLista = new Span();
$obSpanLista->setId("spnListaRegioes");

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->setAjuda("UC-02.09.03");
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);

if ($stAcao == "alterar") {
    $obFormulario->addTitulo("Dados para Alteração de Regiões do PPA");
    $obTxtNome->setValue($stNome);
    $obTxtDescricao->setValue($stDescricao);
    $obHdnCodRegiao = new Hidden;
    $obHdnCodRegiao->setName("inCodRegiao");
    $obHdnCodRegiao->setValue($inCodRegiao);
    $obLblCodigo = new Label();
    $obLblCodigo->setRotulo('Código');
    $obLblCodigo->setTitle('Código da Região');
    $obLblCodigo->setValue($inCodRegiao);
    $obFormulario->addComponente($obLblCodigo);
    $obFormulario->addHidden($obHdnCodRegiao);
} else {
    $obFormulario->addTitulo("Dados para Cadastro de Regiões do PPA");
}

$obFormulario->addComponente($obTxtNome);
$obFormulario->addComponente($obTxtDescricao);

// Botao de incluir/alterar
$obBtnAcao = new ok;
$obBtnAcao2 = new Button;

if ($stAcao == 'incluir') {
    $obBtnAcao->obEvento->setOnClick("incluir();");
    $obBtnAcao2->setName("btnLimpar");
    $obBtnAcao2->setValue("Limpar");
    $obBtnAcao2->obEvento->setOnClick("limpar();");
} elseif ($stAcao == 'alterar') {
    $obBtnAcao->obEvento->setOnClick("alterar();");
    $obBtnAcao2->setName("btnCancelar");
    $obBtnAcao2->setValue("Cancelar");
    $obBtnAcao2->obEvento->setOnClick("CancelarCL();");
}

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao;

$arBtn = array();
$arBtn[] = $obBtnAcao;
$arBtn[] = $obBtnAcao2;
$obFormulario->defineBarra($arBtn);
$obFormulario->addSpan ($obSpanLista);

$obFormulario->show();

# Cria a lista de regiões.
$obRegra = new RPPAManterRegiao();
$obVisao = new VPPAManterRegiao($obRegra);
$rsRegioes = $obVisao->getListaRegioes();

$obLista = new Lista();

$obLista->setMostraPaginacao(true);
$obLista->setTitulo('Lista de Regiões');
$obLista->setRecordSet($rsRegioes);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Código da Região');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Nome da Região');
$obLista->ultimoCabecalho->setWidth(80);
$obLista->commitCabecalho();

if ($stAcao == 'alterar') {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo('&nbsp;');
    $obLista->ultimoCabecalho->setWidth(5);
    $obLista->commitCabecalho();
}

# Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('cod_regiao');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('nome');
$obLista->commitDado();

if ($stAcao == 'alterar') {
    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao($stAcao);
    $obLista->ultimaAcao->addCampo('&inCodRegiao', 'cod_regiao');
    $obLista->ultimaAcao->addCampo('stNome', 'nome');
    $obLista->ultimaAcao->addCampo('stDescricao', 'descricao');
    $stCaminho = 'FMManterRegioes.php?' . Sessao::getID() . '&stAcao=' . $stAcao;
    $obLista->ultimaAcao->setLink($stCaminho);
    $obLista->commitAcao();
}

#atendendo ao ticket #15065
//$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
