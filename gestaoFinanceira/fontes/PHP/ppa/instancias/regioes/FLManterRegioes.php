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

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegioes";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::write("link","");

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if (empty($stAcao)) {
    $stAcao = "excluir";
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgList);
$obForm->setTarget("telaPrincipal"); //oculto - telaPrincipal

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

$obTxtNome = new TextBox;
$obTxtNome->setName("stNome");
$obTxtNome->setRotulo("Nome Região");
$obTxtNome->setSize(80);
$obTxtNome->setMaxLength(80);
$obTxtNome->setNull(true);
$obTxtNome->setTitle('Informe o Nome da Região.');

$obTxtDescricao = new TextBox;
$obTxtDescricao->setName("stDescricao");
$obTxtDescricao->setRotulo("Descrição da Região");
$obTxtDescricao->setSize(80);
$obTxtDescricao->setMaxLength(240);
$obTxtDescricao->setNull(true);
$obTxtDescricao->setTitle('Informe o Descrição da Região.');

//****************************************//
//Monta FORMULARIO
//****************************************//

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->setAjuda("UC-02.09.03");
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addTitulo("Dados para Filtro");
$obFormulario->addComponente($obTxtNome);
$obFormulario->addComponente($obTxtDescricao);

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
