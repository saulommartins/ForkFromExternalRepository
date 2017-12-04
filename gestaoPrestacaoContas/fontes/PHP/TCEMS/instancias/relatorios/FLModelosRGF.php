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
    * Página de Filtro para relatorio Modelo RGF
    * Data de Criação   : 01/08/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 42520 $
    $Name$
    $Author: hboaventura $
    $Date: 2009-09-30 16:18:31 -0300 (Qua, 30 Set 2009) $

    * Casos de uso: uc-06.01.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO . 'ROrcamentoEntidade.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/Quadrimestre.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/Bimestre.class.php';
include_once CAM_GA_ADM_COMPONENTES . 'IMontaAssinaturas.class.php';

$stPrograma = "ModelosRGF";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::remove('filtroRelatorio');
Sessao::remove('rsDadosAnexo1');
Sessao::remove('rsDadosAnexo4');
//sessao->filtro  = array();
//sessao->transf5 = "";

$arNomFiltro = Sessao::read('nomFiltroRelatorio');

$rsRecordset          = new RecordSet;
$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$stOrdem = "ORDER BY cod_entidade";
$obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );
while ( !$rsEntidades->eof() ) {
    $arNomFiltro['entidade'][$rsEntidades->getCampo( 'cod_entidade' )] = $rsEntidades->getCampo( 'nom_cgm' );
    $rsEntidades->proximo();
}
$rsEntidades->setPrimeiroElemento();

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//

$stAcao = $request->get('stAcao');
$obForm = new Form;
if ($_REQUEST['stAcao'] == 'anexo1') {
    $obForm->setAction('OCGeraRGFAnexo1.php');
    $obForm->setTarget('telaPrincipal');
} elseif ($_REQUEST['stAcao'] == 'anexo4') {
    $obForm->setAction('OCGeraRGFAnexo4.php');
    $obForm->setTarget('telaPrincipal');
} elseif ($_REQUEST['stAcao'] == 'anexo3') {
    $obForm->setAction('OCGeraRGFAnexo3.php');
    $obForm->setTarget('telaPrincipal');
} elseif ($_REQUEST['stAcao'] == 'anexo2') {
    $obForm->setAction('OCGeraRGFAnexo2.php');
    $obForm->setTarget('telaPrincipal');
} elseif ($_REQUEST['stAcao'] == 'anexo5') {
    $obForm->setAction('OCGeraRGFAnexo5.php');
    $obForm->setTarget('telaPrincipal');
} elseif ($_REQUEST['stAcao'] == 'anexo6') {
    $obForm->setAction('OCGeraRGFAnexo6.php');
    $obForm->setTarget('telaPrincipal');
} elseif ($_REQUEST['stAcao'] = 'anexo7') {
    $obForm->setAction('OCGeraRGFAnexo7.php');
    $obForm->setTarget('telaPrincipal');
} else {
    $obForm->setAction(CAM_FW_POPUPS . 'relatorio/OCRelatorio.php');
    $obForm->setTarget('oculto');
}

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue(CAM_GPC_STN_INSTANCIAS . 'relatorios/OCModelosRGF.php');

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName('stCtrl');
$obHdnCtrl->setValue('');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

if ($stAcao == 'anexo3') {

    $stEval = "
        if (document.frm.stTipoRelatorio.value == 'Quadrimestre') {
            if (document.frm.cmbQuadrimestre.value == '') {
                erro = true;
                mensagem += '@Escolha um Quadrimestre!()';
            }
        }
        if (document.frm.stTipoRelatorio.value == 'Semestre') {
            if (document.frm.cmbSemestre.value == '') {
                erro = true;
                mensagem += '@Escolha um Semestre!()';
            }
        }
    ";

}

$obHdnEval = new HiddenEval;
$obHdnEval->setName  ('stEval');
$obHdnEval->setValue ($stEval);

// Define SELECT multiplo para codigo da entidade
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName  ('inCodEntidade');
$obCmbEntidades->setRotulo('Entidades');
$obCmbEntidades->setTitle ('');
$obCmbEntidades->setNull  (false);
// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas() == 1) {
       $rsRecordset = $rsEntidades;
       $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1   ('cod_entidade');
$obCmbEntidades->setCampoDesc1 ('nom_cgm');
$obCmbEntidades->SetRecord1    ($rsEntidades);
// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodEntidade');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsRecordset );

//Define Objeto Text para o Exercicio
$obTxtExercicio = new TextBox;
$obTxtExercicio->setName      ('stExercicio');
$obTxtExercicio->setValue     (Sessao::getExercicio());
$obTxtExercicio->setRotulo    ('Exercício');
$obTxtExercicio->setTitle     ('Informe o Exercício para o Extrato de Conta');
$obTxtExercicio->setNull      (false);
$obTxtExercicio->setMaxLength (4);
$obTxtExercicio->setSize      (5);
$obTxtExercicio->setLabel     (true);

$obCmbTipoRelatorio = new Select;
$obCmbTipoRelatorio->setRotulo('Tipo Relatorio');
$obCmbTipoRelatorio->setName  ('stTipoRelatorio');
$obCmbTipoRelatorio->addOption('', 'Selecione');

if ($_REQUEST['stAcao'] == 'anexo6') {
    $obCmbTipoRelatorio->addOption('UltimoQuadrimestre', 'Quadrimestre');
    $obCmbTipoRelatorio->addOption('UltimoSemestre', 'Semestre');
} elseif ($_REQUEST['stAcao'] == 'anexo5') {
    $obCmbTipoRelatorio->addOption('UltimoQuadrimestre', 'Quadrimestre');
    $obCmbTipoRelatorio->addOption('UltimoSemestre', 'Semestre');
} elseif ($_REQUEST['stAcao'] == 'anexo1') {
    //$obCmbTipoRelatorio->addOption('Semestre', 'Semestre');
    $obCmbTipoRelatorio->addOption('Quadrimestre', 'Quadrimestre');
}

$obCmbTipoRelatorio->setNull              (false);
$obCmbTipoRelatorio->setStyle             ('width: 220px');
$obCmbTipoRelatorio->obEvento->setOnChange("buscaDado('preencheSpan')");

$obCmbTipoRelatorio->setNull              (false);
$obCmbTipoRelatorio->setStyle             ('width: 220px');
$obCmbTipoRelatorio->obEvento->setOnChange("buscaDado('preencheSpan')");

// Define Objeto Span para Tipo de Relatorio
$obSpnTipoRelatorio = new Span();
$obSpnTipoRelatorio->setId('spnTipoRelatorio');

$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades($obCmbEntidades);

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden($obHdnCaminho);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnEval, true);

$obFormulario->addTitulo('Dados para Filtro');
$obFormulario->addComponente($obCmbEntidades);
$obFormulario->addComponente($obTxtExercicio);
if (isset($obCmbTipoRelatorio)) {
    $obFormulario->addComponente($obCmbTipoRelatorio);
}
$obFormulario->addSpan($obSpnTipoRelatorio);
$obMontaAssinaturas->geraFormulario($obFormulario);

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
