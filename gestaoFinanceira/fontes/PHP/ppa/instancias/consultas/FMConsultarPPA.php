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
    * Formulário para consulta de PPA
    * Data de Criação   : 22/05/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_PPA_COMPONENTES.'ITextBoxSelectPPA.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once CAM_GF_PPA_MAPEAMENTO.'TPPAPrograma.class.php';

# Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

# Define o nome dos arquivos PHP
$stProjeto = 'ConsultarPPA';
$pgFilt = 'FL' . $stProjeto . '.php';
$pgList = 'LS' . $stProjeto . '.php';
$pgForm = 'FM' . $stProjeto . '.php';
$pgProc = 'PR' . $stProjeto . '.php';
$pgOcul = 'OC' . $stProjeto . '.php';
$pgJS   = 'JS' . $stProjeto . '.php';

$arLink = Sessao::read('link');

$stFiltro = '';
$arFiltro = Sessao::read('filtro');
if ( is_array($arFiltro) ) {
    foreach ($arFiltro AS $stCampo => $stValor) {
        $stFiltro .= "&".$stCampo2."=".@urlencode( $stValor2 );
    }
    $stFiltro .= 'pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando='.Sessao::read('paginando');
}

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;

$stFiltroConsulta = " WHERE ppa.cod_ppa = ".$_REQUEST['inCodPPA'];
$stOrder = " ORDER BY programa.cod_programa ";

$obTPPAPrograma = new TPPAPrograma;
$obTPPAPrograma->recuperaPrograma($rsProgramas, $stFiltroConsulta, $stOrder);

# Definição do form
$obForm = new Form();
$obForm->setAction($pgProc);
$obForm->setTarget('telaPrincipal');

# Definição de dados ocultos padrão
$obHdnAcao = new Hidden();
$obHdnAcao->setName('stAcao');
$obHdnAcao->setID('stAcao');
$obHdnAcao->setValue($stAcao);

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName('stCtrl');
$obHdnCtrl->setValue($stCtrl);

$obITextBoxSelectPPA = new ITextBoxSelectPPA();
$obITextBoxSelectPPA->setNull(false);
$obITextBoxSelectPPA->setLabel(true);
$obITextBoxSelectPPA->obTextBox->setValue($_REQUEST['inCodPPA']);
$obITextBoxSelectPPA->obSelect->setValue ($_REQUEST['inCodPPA']);

//Instancia uma TableTree para demonstrar os programas
$obTableTree = new TableTree;
$obTableTree->setRecordset            ($rsProgramas);
$obTableTree->setArquivo              ($pgOcul);
$obTableTree->setParametros           (array('cod_ppa' => 'cod_ppa', 'cod_programa' => 'cod_programa'));
$obTableTree->setComplementoParametros('stCtrl=montaAcoes');
$obTableTree->setSummary              ('Lista de Programas');
$obTableTree->Head->addCabecalho      ('Código',10);
$obTableTree->Head->addCabecalho      ('Identificador',75);
$obTableTree->Head->addCabecalho      ('Natureza',15);
$obTableTree->Body->addCampo          ('num_programa','C');
$obTableTree->Body->addCampo          ('identificacao', 'E');
$obTableTree->Body->addCampo          ('continuo','C');
$obTableTree->montaHTML();

//Instancia um span para os programas
$obSpnProgramas = new Span();
$obSpnProgramas->setId   ('spnProgramas');
$obSpnProgramas->setValue($obTableTree->getHTML());

$obBtnVoltar = new Button;
$obBtnVoltar->setName ('Voltar');
$obBtnVoltar->setValue('Voltar');
$obBtnVoltar->obEvento->setOnClick("Cancelar('".$stLocation."');");

$arBotoes = array($obBtnVoltar);

# Definição do Formulário
$obFormulario = new Formulario();
$obFormulario->addForm($obForm);

$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obITextBoxSelectPPA->geraFormulario($obFormulario);
$obFormulario->addSpan($obSpnProgramas);
$obFormulario->defineBarra($arBotoes);
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
