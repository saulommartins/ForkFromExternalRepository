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
 * Formulario de filtro de Programa Setorial
 *
 * @category    Urbem
 * @package     PPA
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id: $
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPA.class.php';

$stPrograma = 'ManterProgramasSetoriais';
$pgOcul     = 'OC' . $stPrograma . '.php';

$stAcao = $request->get('stAcao');

//Instancia um objeto Form
$obForm = new Form;
$obForm->setAction('LSManterProgramasSetoriais.php');
//$obForm->setTarget('oculto');

//Instancia um objeto hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao );

//Recupera os ppas para o select
$obTPPA = new TPPA;
$obTPPA->recuperaTodos($rsPPA, ' ORDER BY ano_inicio ');

//Instancia um textboxSelect para a PPA
$obTextBoxSelectPPA = new TextBoxSelect;
$obTextBoxSelectPPA->setRotulo                       ('PPA');
$obTextBoxSelectPPA->setTitle                        ('Informe o PPA.');
$obTextBoxSelectPPA->setName                         ('inCodPPA');
$obTextBoxSelectPPA->obTextBox->setName              ('inCodPPATxt');
$obTextBoxSelectPPA->obTextBox->setId                ('inCodPPATxt');
$obTextBoxSelectPPA->obTextBox->obEvento->setOnChange("montaParametrosGET('preencheMacroObjetivo','inCodPPATxt');");
$obTextBoxSelectPPA->obSelect->setName               ('inCodPPA');
$obTextBoxSelectPPA->obSelect->setId                 ('inCodPPA');
$obTextBoxSelectPPA->obSelect->addOption             ('','Selecione');
$obTextBoxSelectPPA->obSelect->setDependente         (true);
$obTextBoxSelectPPA->obSelect->setCampoID            ('cod_ppa');
$obTextBoxSelectPPA->obSelect->setCampoDesc          ('[ano_inicio] - [ano_final]');
$obTextBoxSelectPPA->obSelect->preencheCombo         ($rsPPA);
$obTextBoxSelectPPA->obSelect->obEvento->setOnChange ("montaParametrosGET('preencheMacroObjetivo','inCodPPA');");
$obTextBoxSelectPPA->setNull                         (true);

//Instancia um textboxSelect para o macro objetivo
$obTextBoxSelectMacroObjectivo = new TextBoxSelect;
$obTextBoxSelectMacroObjectivo->setRotulo              ('Macro Objetivo');
$obTextBoxSelectMacroObjectivo->setTitle               ('Informe o Macro Objetivo.');
$obTextBoxSelectMacroObjectivo->setName                ('inCodMacroObjetivo');
$obTextBoxSelectMacroObjectivo->obTextBox->setName     ('inCodMacroObjetivoTxt');
$obTextBoxSelectMacroObjectivo->obTextBox->setId       ('inCodMacroObjetivoTxt');
$obTextBoxSelectMacroObjectivo->obSelect->setName      ('inCodMacroObjetivo');
$obTextBoxSelectMacroObjectivo->obSelect->setId        ('inCodMacroObjetivo');
$obTextBoxSelectMacroObjectivo->obSelect->addOption    ('','Selecione');
$obTextBoxSelectMacroObjectivo->obSelect->setDependente(true);
$obTextBoxSelectMacroObjectivo->setNull                (true);

$obTxtCodSetorial = new TextBox;
$obTxtCodSetorial->setName   ('inCodSetorial');
$obTxtCodSetorial->setId     ('inCodSetorial');
$obTxtCodSetorial->setRotulo ('Código do Programa Setorial');
$obTxtCodSetorial->setTitle  ('Informe o código do programa setorial.');
$obTxtCodSetorial->setInteiro(true);
$obTxtCodSetorial->setNull   (true);

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo          ('Descrição');
$obTxtDescricao->setTitle           ('Descrição');
$obTxtDescricao->setName            ('stDescricao');
$obTxtDescricao->setId              ('stDescricao');
$obTxtDescricao->setName            ('stDescricao');
$obTxtDescricao->setNull            (true);
$obTxtDescricao->setSize            (50);

$obSlTipoBuscaDescricao = new TipoBusca($obTxtDescricao);

//Instancia um objeto Formulario
$obFormulario = new Formulario       ();
$obFormulario->addForm               ($obForm           );
$obFormulario->addHidden             ($obHdnAcao        );

$obFormulario->addTitulo             ('Dados do Programa Setorial' );

$obFormulario->addComponente         ($obTextBoxSelectPPA);
$obFormulario->addComponente         ($obTextBoxSelectMacroObjectivo);
$obFormulario->addComponente         ($obTxtCodSetorial);
$obFormulario->addComponente         ($obSlTipoBuscaDescricao);

$obFormulario->OK();
$obFormulario->show                  ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
