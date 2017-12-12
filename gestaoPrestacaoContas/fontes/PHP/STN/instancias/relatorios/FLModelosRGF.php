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

 * Casos de uso: uc-06.01.22

 $Id: FLModelosRGF.php 61096 2014-12-08 16:56:24Z arthur $
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

include_once $pgJs;

Sessao::remove('filtroRelatorio');
Sessao::remove('rsDadosAnexo1');
Sessao::remove('rsDadosAnexo4');

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

switch ($stAcao) {
    case 'anexo1':
    case 'anexo1novo':
        $obForm->setAction('OCGeraRGFAnexo1.php');
        $obForm->setTarget('telaPrincipal');
    break;
    
    case 'anexo2':
    case 'anexo2novo':
        $obForm->setAction('OCGeraRGFAnexo2.php');
        $obForm->setTarget('telaPrincipal');
    break;

    case 'anexo3':
    case 'anexo3novo':
        $obForm->setAction('OCGeraRGFAnexo3.php');
        $obForm->setTarget('telaPrincipal');
    break;

    case 'anexo4':
    case 'anexo4novo':
        $obForm->setAction('OCGeraRGFAnexo4.php');
        $obForm->setTarget('telaPrincipal');
    break;

    case 'anexo5':
    case 'anexo5novo':
        $obForm->setAction('OCGeraRGFAnexo5.php');
        $obForm->setTarget('telaPrincipal');
    break;

    case 'anexo6':
    case 'anexo6novo':
        $obForm->setAction('OCGeraRGFAnexo6.php');
        $obForm->setTarget('telaPrincipal');
    break;

    case 'anexo7':
    case 'anexo7novo':
        $obForm->setAction('OCGeraRGFAnexo7.php');
        $obForm->setTarget('telaPrincipal');
    break;

    default:
        $obForm->setAction(CAM_FW_POPUPS . 'relatorio/OCRelatorio.php');
        $obForm->setTarget('oculto');
    break;
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

$stEval = '';

if ($stAcao == 'anexo3'  || $stAcao == 'anexo3novo') {

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
        if (document.frm.stTipoRelatorio.value == 'Mensal') {
            if (document.frm.cmbMensal.value == '') {
                erro = true;
                mensagem += '@Escolha um Mês!()';
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
$inCodUF = SistemaLegado::pegaConfiguracao( 'cod_uf', 2, Sessao::getExercicio() );

switch ($stAcao) {
    case 'anexo1novo':
        $obCmbTipoRelatorio->addOption('Bimestre', 'Bimestre');
        $obCmbTipoRelatorio->addOption('Semestre', 'Semestre');
        $obCmbTipoRelatorio->addOption('Quadrimestre', 'Quadrimestre');
    break;

    case 'anexo2novo':
        //Estado de Minas Gerais(MG)
        if ( ($inCodUF == 11) && (Sessao::getExercicio() >= '2014') ) {
            $obCmbTipoRelatorio->addOption('Mes', 'Mês');
            $obCmbTipoRelatorio->addOption('Semestre', 'Semestre');
            $obCmbTipoRelatorio->addOption('Quadrimestre', 'Quadrimestre');
        }else{
            $obCmbTipoRelatorio->addOption('Semestre', 'Semestre');
            $obCmbTipoRelatorio->addOption('Quadrimestre', 'Quadrimestre');
        }
    break;

    case 'anexo3':
    case 'anexo3novo':
    case 'anexo4novo':
        //Estado de Minas Gerais(MG)
        if ( ($inCodUF == 11) && (Sessao::getExercicio() >= '2014') ) {
            $obCmbTipoRelatorio->addOption('Mes', 'Mês');
            $obCmbTipoRelatorio->addOption('Semestre', 'Semestre');
            $obCmbTipoRelatorio->addOption('Quadrimestre', 'Quadrimestre');
        }else{
            $obCmbTipoRelatorio->addOption('Semestre', 'Semestre');
            $obCmbTipoRelatorio->addOption('Quadrimestre', 'Quadrimestre');
        }
    break;

    case 'anexo5':
    case 'anexo5novo':
        //Estado de Minas Gerais(MG)
        if ( ($inCodUF == 11) && (Sessao::getExercicio() >= '2014') ) {
            $obCmbTipoRelatorio->addOption('Mes', 'Mês');
            $obCmbTipoRelatorio->addOption('Quadrimestre', 'Quadrimestre');
            $obCmbTipoRelatorio->addOption('Semestre'    , 'Semestre'    );
        }else{
            $obCmbTipoRelatorio->addOption('Quadrimestre', 'Quadrimestre');
            $obCmbTipoRelatorio->addOption('Semestre'    , 'Semestre'    );
        }
    break;

    case 'anexo6':
    case 'anexo6novo':
        $obCmbTipoRelatorio->addOption('UltimoQuadrimestre', 'Quadrimestre');
        $obCmbTipoRelatorio->addOption('UltimoSemestre', 'Semestre');
    break;
    
    default:
        $obCmbTipoRelatorio->addOption('Semestre', 'Semestre');
        $obCmbTipoRelatorio->addOption('Quadrimestre', 'Quadrimestre');
    break;
}

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
