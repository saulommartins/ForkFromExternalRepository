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
/*
 * Formulario de Cadastro de Macro Objetivos
 * Data de Criação   : 06/05/2009

 * @author Analista      Tonismar Régis Bernardo
 * @author Desenvolvedor Eduardo Paculski Schitz

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_PPA_COMPONENTES . 'ITextBoxSelectPPA.class.php';

$stCtrl = $_REQUEST["stCtrl"];
$stAcao = $request->get('stAcao');

($stAcao=="")?$stAcao="incluir":$stAcao=$stAcao;

$stPrograma = "ManterMacroobjetivos";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//Definição do Form
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//Define o objeto de controle
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

//Recupera os ppas para o select
$obTPPA = new TPPA;
$obTPPA->recuperaTodos($rsPPA, ' ORDER BY ano_inicio');

//Instancia um textboxSelect para a PPA
$obTextBoxSelectPPA = new TextBoxSelect;
$obTextBoxSelectPPA->setRotulo                       ('PPA');
$obTextBoxSelectPPA->setTitle                        ('Informe o PPA.');
$obTextBoxSelectPPA->setName                         ('inCodPPA');
$obTextBoxSelectPPA->obTextBox->setName              ('inCodPPATxt');
$obTextBoxSelectPPA->obTextBox->setId                ('inCodPPATxt');
$obTextBoxSelectPPA->obSelect->setName               ('inCodPPA');
$obTextBoxSelectPPA->obSelect->setId                 ('inCodPPA');
$obTextBoxSelectPPA->obSelect->addOption             ('','Selecione');
$obTextBoxSelectPPA->obSelect->setDependente         (true);
$obTextBoxSelectPPA->obSelect->setCampoID            ('cod_ppa');
$obTextBoxSelectPPA->obSelect->setCampoDesc          ('[ano_inicio] - [ano_final]');
$obTextBoxSelectPPA->obSelect->preencheCombo         ($rsPPA);
if ($stAcao == 'alterar') {
    $obTextBoxSelectPPA->obTextBox->setValue($_REQUEST['inCodPPA']);
    $obTextBoxSelectPPA->obSelect->setValue ($_REQUEST['inCodPPA']);

    $obTextBoxSelectPPA->setLabel(true);

    //Define o objeto de controle
    $obHdnCodMacro = new Hidden;
    $obHdnCodMacro->setName ("inCodMacro");
    $obHdnCodMacro->setValue($_REQUEST['inCodMacro']);
} else {
    if ($rsPPA->getNumLinhas() == 1) {
        $obTextBoxSelectPPA->obTextBox->setValue($rsPPA->getCampo('cod_ppa'));
        $obTextBoxSelectPPA->obSelect->setValue ($rsPPA->getCampo('cod_ppa'));
    }
}

//Informar código
$obTxtDescricao = new TextArea;
$obTxtDescricao->setRotulo       ('Descrição');
$obTxtDescricao->setTitle        ('Descrição');
$obTxtDescricao->setName         ('stDescricao');
$obTxtDescricao->setId           ('stDescricao');
$obTxtDescricao->setNull         (false);
$obTxtDescricao->setMaxCaracteres(450);
if ($stAcao == 'alterar') {
    include_once CAM_GF_PPA_MAPEAMENTO."TPPAMacroObjetivo.class.php";
    $obTPPAMacroObjetivo = new TPPAMacroObjetivo;

    $stFiltroMacro  = " WHERE cod_macro = ".$_REQUEST['inCodMacro'];
    $stFiltroMacro .= "   AND cod_ppa = ".$_REQUEST['inCodPPA'];
    $obTPPAMacroObjetivo->recuperaTodos($rsMacroObjetivo, $stFiltroMacro);
    $obTxtDescricao->setValue        ($rsMacroObjetivo->getCampo('descricao'));
}

$obFormulario = new Formulario;
$obFormulario->addForm  ($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addTitulo("Dados para Macro Objetivos");
$obFormulario->addComponente($obTextBoxSelectPPA);
if ($stAcao == 'alterar') {
    $obFormulario->addHidden($obHdnCodMacro);
}
$obFormulario->addComponente($obTxtDescricao);

if ($stAcao == 'incluir') {
    $obFormulario->OK();
} else {
    $stFiltro = '';
    if ( Sessao::read('filtro') ) {
        $arFiltro = Sessao::read('filtro');
        $stFiltro = '';
        foreach ($arFiltro as $stCampo => $stValor) {
            $stFiltro .= $stCampo."=".$stValor."&";
        }

        $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
    }

    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao."&".$stFiltro;

    $obOk = new Ok();
    $obCancelar = new Button();
    $obCancelar->setValue ("Cancelar");
    $obCancelar->obEvento->setOnclick("Cancelar('".$stLocation."');");

    $obFormulario->defineBarra( array( $obOk, $obCancelar ) );
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
