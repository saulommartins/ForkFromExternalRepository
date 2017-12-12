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
    * Página de Filtro para Ajustes de Modelos
    * Data de Criação   : 04/11/2004

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2006-10-27 16:37:56 -0300 (Sex, 27 Out 2006) $

    * Casos de uso uc-02.05.01, uc-02.01.35

    * @ignore
*/

/*
$Log$
Revision 1.8  2006/10/27 19:37:33  cako
Bug #6773#

Revision 1.7  2006/08/25 17:49:51  fernando
Bug #6773#

Revision 1.6  2006/07/21 14:13:48  cleisson
Bug #6624#

Revision 1.5  2006/07/05 20:45:22  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_LRF_NEGOCIO."RLRFTCERSModelo.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma = "AjustesModelos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRLRFTCEModelo = new RLRFTCERSModelo();
$obRLRFTCEModelo->setExercicio( Sessao::getExercicio() );
$obRLRFTCEModelo->listarModelosAjuste( $rsModelo );
$obRLRFTCEModelo->addQuadro();
$obRLRFTCEModelo->roUltimoQuadro->addContaPlano();
$obRLRFTCEModelo->roUltimoQuadro->roUltimaContaPlano->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRLRFTCEModelo->roUltimoQuadro->roUltimaContaPlano->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRLRFTCEModelo->roUltimoQuadro->roUltimaContaPlano->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );
$arMes = array( '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março'    , '04' => 'Abril'  , '05' => 'Maio'    , '06' => 'Junho',
                '07' => 'Julho'  , '08' => 'Agosto'   , '09' => 'Setembro' , '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro' );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

$sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction("../../../../../../gestaoFinanceira/fontes/PHP/LRF/instancias/tceRS/".$pgForm );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

//Define o objeto SELECT para entidade
$obCmbEntidade = new Select();
$obCmbEntidade->setName      ( "inCodEntidade"        );
$obCmbEntidade->setValue     ( $inCodEntidade         );
$obCmbEntidade->setRotulo    ( "Entidade"             );
$obCmbEntidade->setNull      ( false                  );
$obCmbEntidade->setTitle     ( 'Selecione a entidade' );
// Caso o usuário tenha permissão para mais de uma entidade, exibe o selecionar.
// Se tiver apenas uma, evita o addOption forçando a primeira e única opção ser selecionada.
if ($rsEntidade->getNumLinhas()>1) {
    $obCmbEntidade->addOption              ( "", "Selecione"               );
}

$obCmbEntidade->setCampoId   ( 'cod_entidade'         );
$obCmbEntidade->setCampoDesc ( 'nom_cgm'              );
$obCmbEntidade->preencheCombo( $rsEntidade            );

//Define o objeto SELECT para Modelo
$obCmbModelo = new Select();
$obCmbModelo->setName      ( "inCodModelo"        );
$obCmbModelo->setValue     ( $inCodModelo         );
$obCmbModelo->setRotulo    ( "Modelo"             );
$obCmbModelo->setNull      ( false                );
$obCmbModelo->setTitle     ( 'Selecione o modelo' );
$obCmbModelo->addOption    ( '', 'Selecione'      );
$obCmbModelo->setCampoId   ( 'cod_modelo'         );
if (Sessao::read('modulo') != 8) {
    $obCmbModelo->setCampoDesc ( 'nom_modelo');
} else {
    $obCmbModelo->setCampoDesc ( 'nom_modelo_orcamento');
}
$obCmbModelo->preencheCombo( $rsModelo            );

//Define o objeto SELECT para mes
$obCmbMes = new Select();
$obCmbMes->setName      ( "inMes"           );
$obCmbMes->setValue     ( $inMes            );
$obCmbMes->setRotulo    ( "Mês"             );
$obCmbMes->setNull      ( false             );
$obCmbMes->setTitle     ( 'Selecione o mês' );
$obCmbMes->addOption    ( '', 'Selecione'   );
foreach ($arMes as $cod_mes => $nom_mes) {
    $obCmbMes->addOption    ( $cod_mes, $nom_mes );
}

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );

$obFormulario->addTitulo( "Dados para filtro" );
$obFormulario->addComponente( $obCmbEntidade  );
$obFormulario->addComponente( $obCmbModelo    );
$obFormulario->addComponente( $obCmbMes       );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
