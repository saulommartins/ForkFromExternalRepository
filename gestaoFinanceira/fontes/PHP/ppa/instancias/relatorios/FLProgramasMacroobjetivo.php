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
    * Página de Formulario que filtra de Relatórios de Estimativa de Receitas PPA
    * Data de Criação: 11/05/2008

    * @author Analista: Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor: Henrique Girardi dos Santos <henrique.santos@cnm.org.br>

    * @package URBEM
    * @subpackage PPA

    * $Id: $
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_PPA_MAPEAMENTO.'TPPA.class.php';
require_once CAM_GF_PPA_MAPEAMENTO.'TPPATipoPrograma.class.php';
include_once CAM_GF_PPA_COMPONENTES.'MontaOrgaoUnidade.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ProgramasMacroobjetivo";
$pgFilt     = "FL".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";

$obForm = new Form;
$obForm->setAction('OCGeraRelatorio'.$stPrograma.'.php');
$obForm->setTarget('telaPrincipal');

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName('stCtrl');
$obHdnCtrl->setValue($stCtrl);

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

$obTPPATipoPrograma = new TPPATipoPrograma;
$obTPPATipoPrograma->recuperaTodos($rsTipoPrograma, ' ORDER BY cod_tipo_programa ');

$obSelectPPATipoPrograma = new Select;
$obSelectPPATipoPrograma->setRotulo    ('Tipo de Programa');
$obSelectPPATipoPrograma->setTitle     ('Informe o Tipo de Programa.');
$obSelectPPATipoPrograma->setName      ('inCodTipoPrograma');
$obSelectPPATipoPrograma->setId        ('inCodTipoPrograma');
$obSelectPPATipoPrograma->addOption    (0, 'Todos os Tipos');
$obSelectPPATipoPrograma->setCampoID   ('cod_tipo_programa');
$obSelectPPATipoPrograma->setCampoDesc ('descricao');
$obSelectPPATipoPrograma->preencheCombo($rsTipoPrograma);
$obSelectPPATipoPrograma->setNull      (false);

$obSelectTipoRelatorio = new Select;
$obSelectTipoRelatorio->setRotulo('Tipo de Relatório');
$obSelectTipoRelatorio->setTitle ('Informe o Tipo de Relatório a ser gerado.');
$obSelectTipoRelatorio->setName  ('stTipoRelatorio');
$obSelectTipoRelatorio->setId    ('stTipoRelatorio');
$obSelectTipoRelatorio->addOption('sintetico', 'Sintético');
$obSelectTipoRelatorio->addOption('analitico', 'Analítico');
$obSelectTipoRelatorio->setNull  (false);


// Define unidade orçamentária responsável
$obIMontaUnidadeOrcamentaria = new MontaOrgaoUnidade();
$obIMontaUnidadeOrcamentaria->setRotulo('Unidade Executora');
$obIMontaUnidadeOrcamentaria->setTarget('telaPrincipal');
$obIMontaUnidadeOrcamentaria->setActionPosterior('OCGeraRelatorio'.$stPrograma.'.php');

$obIMontaUnidadeOrcamentaria->setExecutaFrame( true );

$obIMontaUnidadeOrcamentaria->setNull(true);


$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addTitulo('Dados para Filtro');
$obFormulario->addComponente($obTextBoxSelectPPA);
$obFormulario->addComponente($obSelectPPATipoPrograma);
$obFormulario->addComponente($obSelectTipoRelatorio);
$obIMontaUnidadeOrcamentaria->geraFormulario($obFormulario);


$obFormulario->Ok();
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
