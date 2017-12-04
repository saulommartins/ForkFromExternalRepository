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
    * Página de Formulario que filtra de Relatórios de Despesas Previstas por Função
    * Data de Criação: 21/05/2008

    * @author Analista: Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor: Henrique Girardi dos Santos <henrique.santos@cnm.org.br>

    * @package URBEM
    * @subpackage PPA

    * $Id: $
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_PPA_MAPEAMENTO.'TPPA.class.php';
require_once CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "DespesasPrevistasFuncao";
$pgFilt     = "FL".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";

$obForm = new Form;
$obForm->setAction('OCGeraRelatorio'.$stPrograma.'.php');
$obForm->setTarget('telaPrincipal');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($_REQUEST['stAcao']);

//Recupera os ppas para o select
$obTPPA = new TPPA;
$obTPPA->recuperaTodos($rsPPA, ' ORDER BY ano_inicio ');

//Instancia um textboxSelect para a PPA
$obTextBoxSelectPPA = new TextBoxSelect;
$obTextBoxSelectPPA->setRotulo              ('PPA');
$obTextBoxSelectPPA->setTitle               ('Informe o PPA.');
$obTextBoxSelectPPA->setName                ('inCodPPA');
$obTextBoxSelectPPA->obTextBox->setName     ('inCodPPATxt');
$obTextBoxSelectPPA->obTextBox->setId       ('inCodPPATxt');
$obTextBoxSelectPPA->obSelect->setName      ('inCodPPA');
$obTextBoxSelectPPA->obSelect->setId        ('inCodPPA');
$obTextBoxSelectPPA->obSelect->addOption    ('','Selecione');
$obTextBoxSelectPPA->obSelect->setDependente(true);
$obTextBoxSelectPPA->obSelect->setCampoID   ('cod_ppa');
$obTextBoxSelectPPA->obSelect->setCampoDesc ('[ano_inicio] - [ano_final]');
$obTextBoxSelectPPA->obSelect->preencheCombo($rsPPA);
$obTextBoxSelectPPA->setNull                (false);
if ($rsPPA->getNumLinhas() == 1) {
    $obTextBoxSelectPPA->obTextBox->setValue($rsPPA->getCampo('cod_ppa'));
    $obTextBoxSelectPPA->obSelect->setValue($rsPPA->getCampo('cod_ppa'));
}

//Monta combo para seleção de FUNÇÃO
$obROrcamentoDespesa = new ROrcamentoDespesa;
$obROrcamentoDespesa->obROrcamentoFuncao->listar($rsFuncao);
$obCmbFuncao = new Select;
$obCmbFuncao->setTitle     ('Selecione a função.');
$obCmbFuncao->setName      ('inCodFuncao');
$obCmbFuncao->setValue     ('');
$obCmbFuncao->setRotulo    ('Função');
$obCmbFuncao->setCampoId   ('cod_funcao');
$obCmbFuncao->setCampoDesc ('[cod_funcao] - [descricao]');
$obCmbFuncao->addOption    (0, 'Selecione');
$obCmbFuncao->preencheCombo($rsFuncao);

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addTitulo('Dados para Filtro');
$obFormulario->addComponente($obTextBoxSelectPPA);
$obFormulario->addComponente($obCmbFuncao);
$obFormulario->Ok();
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
