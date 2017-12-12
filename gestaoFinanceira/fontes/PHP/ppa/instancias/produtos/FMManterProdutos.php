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
    * Página de Formulario de Inclusao/Alteracao de Produtos
    * Data de Criação: 22/09/2008

    * @author Analista: Heleno Santos
    * @author Desenvolvedor: Marcio Medeiros

    * @ignore

    * Casos de uso: uc-02.09.11
*/

/*
$Log: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_PPA_MAPEAMENTO."TPPAProduto.class.php");
include_once( CAM_GF_PPA_VISAO."VPPAManterProduto.class.php");
include_once( CAM_GF_PPA_NEGOCIO."RPPAManterProduto.class.php");

//Define o nome dos arquivos PHP
$stProjeto = "ManterProdutos";
$pgFilt = "FL".$stProjeto.".php";
$pgList = "LS".$stProjeto.".php";
$pgForm = "FM".$stProjeto.".php";
$pgProc = "PR".$stProjeto.".php";
$pgOcul = "OC".$stProjeto.".php";
$pgJS   = "JS".$stProjeto.".php";
include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

if (empty($stAcao)) {
    $stAcao = "incluir";
}

if ($stAcao == 'alterar') {
   $obTPPAProduto = new TPPAProduto();
   $obTPPAProduto->setDado('cod_produto', $_GET['inCodProduto']);
   $obTPPAProduto->recuperaPorChave($rsProduto);
} else {
    $rsProduto = new RecordSet;
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl");
$obHdnCtrl->setValue("");

if ($stAcao == 'alterar') {
    $obTxtCodProduto = new TextBox;
    $obTxtCodProduto->setName  ('inCodProduto');
    $obTxtCodProduto->setRotulo('Código');
    $obTxtCodProduto->setTitle ('Código da Produto');
    $obTxtCodProduto->setId    ('inCodProduto');
    $obTxtCodProduto->setValue ($rsProduto->getCampo('cod_produto'));
    $obTxtCodProduto->setLabel (true);
}

$obTxtDescricao = new TextBox;
$obTxtDescricao->setName     ( "stDescricao"           );
$obTxtDescricao->setId       ( "stDescricao"           );
$obTxtDescricao->setRotulo   ( "Descrição do Produto"   );
$obTxtDescricao->setTitle    ( "Informe a Descrição do Produto." );
$obTxtDescricao->setSize     ( 80                 );
$obTxtDescricao->setMaxLength( 80                 );
$obTxtDescricao->setNull     ( false              );
$obTxtDescricao->setValue    ($rsProduto->getCampo('descricao'));

//instancia um textarea para a especificacao
$obTxtEspecificacao = new TextArea;
$obTxtEspecificacao->setName  ('stEspecificacao');
$obTxtEspecificacao->setId    ('stEspecificacao');
$obTxtEspecificacao->setRotulo('Especificação do Produto');
$obTxtEspecificacao->setTitle ('Informe a especificação do produto');
$obTxtEspecificacao->setNull  (false);
$obTxtEspecificacao->setValue ($rsProduto->getCampo('especificacao'));

// Botao de incluir/alterar
$obBtnAcao = new ok(true);

$obBtnLimpar = new Button;
$obBtnLimpar->setName             ('btnLimpar');
$obBtnLimpar->setValue            ('Limpar');
$obBtnLimpar->obEvento->setOnClick('limpar();');

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao;

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm          ($obForm);
$obFormulario->setAjuda         ('UC-02.09.11');
$obFormulario->addHidden        ($obHdnCtrl);
$obFormulario->addHidden        ($obHdnAcao);

$obFormulario->addTitulo        ('Dados para Cadastro de Produtos do PPA');

if ($stAcao == 'alterar') {
    $obFormulario->addComponente($obTxtCodProduto);
}
$obFormulario->addComponente    ($obTxtDescricao);
$obFormulario->addComponente    ($obTxtEspecificacao);

if ($stAcao == 'alterar') {
    $obFormulario->Cancelar         ($stLocation);
} else {
    $obFormulario->defineBarra      (array($obBtnAcao,$obBtnLimpar));
}
$obFormulario->show             ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
