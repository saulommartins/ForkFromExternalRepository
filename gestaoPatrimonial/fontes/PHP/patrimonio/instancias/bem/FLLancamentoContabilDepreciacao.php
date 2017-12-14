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
  * Data de Criação: 04/09/2013
  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Evandro Melos
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                                     );

$stPrograma = "LancamentoContabilDepreciacao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio           ( Sessao::getExercicio() );
$obROrcamentoEntidade->obRCGM->setNumCGM      ( Sessao::read('numCgm') );
$obROrcamentoEntidade->listarUsuariosEntidade ( $rsEntidade, "E.numcgm" );

//Cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget ("oculto");

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//Define o objeto COMBO para Entidade
$obCmbEntidade = new Select;
$obCmbEntidade->setName      ( "inCodEntidade" );
$obCmbEntidade->setRotulo    ( "Entidade" );
$obCmbEntidade->addOption    ( "", "Selecione" );
$obCmbEntidade->setCampoId   ( "[cod_entidade]" );
$obCmbEntidade->setCampoDesc ( "[cod_entidade] - [nom_cgm]" );
$obCmbEntidade->preencheCombo( $rsEntidade );
$obCmbEntidade->setNull      ( false );
$obCmbEntidade->setTitle     ( 'Selecione uma Entidade' );

$arDescCompetencia = array(
    1 => 'Mês',
    2 => 'Bimestre',
    3 => 'Trimestre',
    4 => 'Quadrimestre',
    6 => 'Semestre',
    7 => 'Ano'
);

$arOrdinal = array('Primeiro', 'Segundo', 'Terceiro', 'Quarto', 'Quinto','Sexto');
$arCompetencia = array();

$obSlcCompentecia = new SelectMeses;
$obSlcCompentecia->setName('inCompetencia');
$obSlcCompentecia->setRotulo('Competência');
$obSlcCompentecia->setTitle('Selecione a competência em que ocorrerá a depreciação.');
$obSlcCompentecia->setValue(date('n'));
$obSlcCompentecia->setNull(false);

$obIntExercicio = new Exercicio;
$obIntExercicio->setName('inExercicio');
$obIntExercicio->setValue(Sessao::getExercicio());
$obIntExercicio->setNull(false);
$obIntExercicio->setReadOnly(true);

//monta o formulário Para Gerar Lançamentos
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
if ($stAcao == 'incluir') {
  $obFormulario->addTitulo    ( "Dados para o Lançamento Contábil de Depreciação" );
} else {
  $obFormulario->addTitulo    ( "Dados para Estorno de Lançamento de Depreciação" );
}

$obFormulario->addComponente( $obCmbEntidade );
$obFormulario->agrupaComponentes(array($obSlcCompentecia,$obIntExercicio));

$obFormulario->OK(true);
$obFormulario->show();

?>