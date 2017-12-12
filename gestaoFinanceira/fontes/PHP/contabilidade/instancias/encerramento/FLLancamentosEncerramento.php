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
    * Página de Formulário - Gerar Saldos de Balanço

    * Data de Criação   : 20/12/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-01-04 19:16:02 -0200 (Qui, 04 Jan 2007) $

    * Casos de uso: uc-02.02.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php"                         );

//Define o nome dos arquivos PHP
$stPrograma = "LancamentosEncerramento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//****************************************//
// Define COMPONENTES DO FORMULARIO
//****************************************//

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$stAcao    = $_REQUEST['stAcao'];
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$stCtrl    = $_REQUEST['stCtrl'];
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$stEval = "if (!erro) {BloqueiaFrames(true,false);}";

$obHdnEval = new HiddenEval;
$obHdnEval->setName  ( "stEval"            );
$obHdnEval->setValue ( $stEval             );

//// SelectMultiploEntidadesUsuario
$obISelectEntidade = new ISelectMultiploEntidadeUsuario();
$obISelectEntidade->setNull(false);

// Define Objeto CheckBox
$obChkTodos = new CheckBox;
$obChkTodos->setName ( "boTodos" );
$obChkTodos->setId   ( "boTodos" );
$obChkTodos->setRotulo  ( 'Escolha uma opção ' );
$obChkTodos->setLabel( "Todos" );
// $obChkTodos->obEvento->setOnClick('jQuery(\'.checkbox\').attr(\'checked\', jQuery(\'#boTodos\').attr(\'checked\'));');

if (Sessao::getExercicio() < 2013) {
    $obChkReceita = new CheckBox;
    $obChkReceita->setName ( "boReceita" );
    $obChkReceita->setId   ( "boReceita" );
    $obChkReceita->setRotulo  ( ' ' );
    $obChkReceita->setClass( "checkbox" );
    $obChkReceita->setLabel( "Receita" );

    $obChkDespesa = new CheckBox;
    $obChkDespesa->setName ( "boDespesa" );
    $obChkDespesa->setId   ( "boDespesa" );
    $obChkDespesa->setRotulo ( ' ' );
    $obChkDespesa->setLabel(   "Despesa" );
    $obChkDespesa->setClass( "checkbox" );

    $obChkResultadoApurado = new CheckBox;
    $obChkResultadoApurado->setName ("boResultadoApurado");
    $obChkResultadoApurado->setId   ("boResultadoApurado");
    $obChkResultadoApurado->setRotulo( ' ' );
    $obChkResultadoApurado->setLabel("Resultado Apurado");
    $obChkResultadoApurado->setClass( "checkbox" );
}

$obChkVariacoes = new CheckBox;
$obChkVariacoes->setName ( "boVariacoes" );
$obChkVariacoes->setId   ( "boVariacoes" );
$obChkVariacoes->setRotulo ( ' ' );
$obChkVariacoes->setLabel(   "Variações Patrimoniais" );
$obChkVariacoes->setClass( "checkbox" );

$obChkOrcamentario = new CheckBox;
$obChkOrcamentario->setName ( "boOrcamentario" );
$obChkOrcamentario->setId   ( "boOrcamentario" );
$obChkOrcamentario->setRotulo ( ' ' );
$obChkOrcamentario->setLabel(   "Orçamentário" );
$obChkOrcamentario->setClass( "checkbox" );

if (Sessao::getExercicio() > 2012) {
    $obChkControle = new CheckBox;
    $obChkControle->setName ("boControle");
    $obChkControle->setId   ("boControle");
    $obChkControle->setRotulo( ' ' );
    $obChkControle->setLabel("Controle");
    $obChkControle->setClass( "checkbox" );
}

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );

if ($stAcao == 'incluir') {
    $obFormulario->addTitulo( "Selecione a Entidade e o Grupo que deseja encerrar (a ordem deve ser respeitada)"        );
    $obFormulario->addComponente($obISelectEntidade);
    $obFormulario->addComponente($obChkTodos);
    if (Sessao::getExercicio() > 2012) {
        $obFormulario->addComponente($obChkVariacoes);
        $obFormulario->addComponente($obChkOrcamentario);
        $obFormulario->addComponente($obChkControle);
    } else {
        $obFormulario->addComponente($obChkReceita);
        $obFormulario->addComponente($obChkDespesa);
        $obFormulario->addComponente($obChkVariacoes);
        $obFormulario->addComponente($obChkOrcamentario);
        $obFormulario->addComponente($obChkResultadoApurado);
    }
}

if ($stAcao == 'excluir') {
    $obFormulario->addTitulo( "Selecione as entidades que deseja excluir os lançamentos contábeis do Encerramento ".Sessao::getExercicio() );
    $obFormulario->addComponente($obISelectEntidade);

}
$obFormulario->addHidden( $obHdnEval, true        );
$obBtnOk = new Ok;
$obFormulario->defineBarra( array($obBtnOk) );
//$obFormulario->OK();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
