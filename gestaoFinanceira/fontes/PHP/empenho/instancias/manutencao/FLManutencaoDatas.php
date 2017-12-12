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
    * Página de Alteração das datas
    * Data de Criação  : 08/06/2005

    * @author Analista: Diego B Victória
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.03.16
*/

/*
$Log$
Revision 1.5  2006/07/05 20:48:49  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoManutencaoDatas.class.php"     );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma = "ManutencaoDatas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::write('paginando', array());

$rsRecordset = new RecordSet;
$obREmpenhoEmpenho = new REmpenhoEmpenho;
$obREmpenhoEmpenho->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );
//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl  );

// Define SELECT multiplo para codigo da entidade
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "" );
$obCmbEntidades->setNull   ( false );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidade->getNumLinhas()==1) {
       $rsRecordset = $rsEntidade;
       $rsEntidade = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsEntidade );
// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodEntidade');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsRecordset );

// Define objeto Data para Exercicio
$obTxtExercicio = new TextBox();
$obTxtExercicio->setName     ( "stExercicioEmpenho" );
$obTxtExercicio->setRotulo   ( "Informe o Exercício" );
$obTxtExercicio->setTitle    ( 'stExercicioEmpenho' );
$obTxtExercicio->setMaxLength(4);
$obTxtExercicio->setNull     ( false );
$obTxtExercicio->setValue    ( $stExercicio );

$obBtnOK = new Ok;
$botoesForm     = array ( $obBtnOK);

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm                    );
$obFormulario->addTitulo( "Manutenção de Datas"    );
$obFormulario->addHidden( $obHdnCtrl               );
$obFormulario->addHidden( $obHdnAcao               );

$obFormulario->addComponente( $obCmbEntidades     );
$obFormulario->addComponente( $obTxtExercicio     );

$obFormulario->defineBarra( $botoesForm );
$obFormulario->show();

$obIFrame = new IFrame;
$obIFrame->setName("telaMensagem");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("65");
$obIFrame->show();

?>
