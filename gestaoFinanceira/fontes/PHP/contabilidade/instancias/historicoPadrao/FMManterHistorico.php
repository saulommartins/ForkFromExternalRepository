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
    * Página de Formulário Tipo de Norma
    * Data de Criação   : 01/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    * $Id: FMManterHistorico.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_CONT_NEGOCIO. "RContabilidadeHistoricoPadrao.class.php");

$stPrograma = "ManterHistorico";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stFiltro = "&pos=".Sessao::read('pos');
$stFiltro .= "&pg=".Sessao::read('pg');
$stFiltro .= "&paginando=".Sessao::read('paginando');
$filtro = Sessao::read('filtro');
if ( is_array($filtro) ) {
    foreach ($filtro as $stCampo => $stValor) {
        if (is_array($stValor)) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                if (is_array($stValor2)) {
                    foreach ($stValor2 as $stCampo3 => $stValor3) {
                        $stFiltro .= "&".$stCampo3."=".urlencode( $stValor3 );
                    }
                } else {
                    $stFiltro .= "&".$stCampo2."=".urlencode( $stValor2 );
               }
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

$stLocation = $pgList."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'].$stFiltro;

$obRegra = new RContabilidadeHistoricoPadrao;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
if ( empty( $stAcao ) || $stAcao=="incluir") {
    $stAcao = "incluir";
}

$stComplemento = "S";
$boReadOnly    = false;

if ($stAcao == 'alterar') {
    $obRegra->setCodHistorico( $_REQUEST['inCodHistorico'] );
    $obRegra->setExercicio   ( Sessao::getExercicio() );
    $obRegra->listar( $rsLista );

    $inCodHistorico         = $_REQUEST['inCodHistorico'];
    $stNomistorico          = $rsLista->getCampo( "nom_historico" );
    $boComplemento          = $rsLista->getCampo( "complemento"   );

    if ($boComplemento=="t") {
        $stComplemento = "S";
    } else {
        $stComplemento = "N";
    }

    $boReadOnly = true;
} elseif ($stAcao == 'incluir') {
    $obRegra->recuperaCodHistoricoInclusao();
    $inCodHistorico = $obRegra->getCodHistoricoInclusao();
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodHistorico = new Hidden;
$obHdnCodHistorico->setName ( "inCodHistorico" );
$obHdnCodHistorico->setValue( $_REQUEST['inCodHistorico'] );
$obLblCodHistorico = new Label;
$obLblCodHistorico->setRotulo( "Código" );
$obLblCodHistorico->setValue ( $_REQUEST['inCodHistorico'] );

$obTxtCodHistorico = new TextBox;
$obTxtCodHistorico->setRotulo        ( "Código" );
$obTxtCodHistorico->setName          ( "inCodHistoricoInclusao" );
$obTxtCodHistorico->setTitle         ( "Código do histórico" );
$obTxtCodHistorico->setValue         ( $inCodHistorico );
$obTxtCodHistorico->setSize          ( 10 );
$obTxtCodHistorico->setMaxLength     ( 10 );
$obTxtCodHistorico->setReadOnly      ( $boReadOnly );
$obTxtCodHistorico->setInteiro       ( true  );
$obTxtCodHistorico->setNull          ( false );

$obTxtNomHistorico = new TextBox;
$obTxtNomHistorico->setRotulo        ( "Descrição" );
$obTxtNomHistorico->setTitle         ( "Descrição do histórico" );
$obTxtNomHistorico->setName          ( "stNomHistorico" );
$obTxtNomHistorico->setValue         ( $stNomistorico  );
$obTxtNomHistorico->setSize          ( 80 );
$obTxtNomHistorico->setMaxLength     ( 80 );
$obTxtNomHistorico->setNull          ( false );

$obRdnComplemento = new SimNao;
$obRdnComplemento->setRotulo ( "Com Complemento"   );
$obRdnComplemento->setName   ( "boComplemento" );
$obRdnComplemento->setTitle  ( "Informe se este histórico necessita ou não de complemento" );
$obRdnComplemento->setChecked( $stComplemento );
$obRdnComplemento->obRadioSim->setValue  ("Sim");
$obRdnComplemento->obRadioNao->setValue  ("Não");

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-02.02.03');
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );

$obFormulario->addTitulo            ( "Dados para Histórico Padrão" );

if ($stAcao=='incluir') {
    $obFormulario->addComponente        ( $obTxtCodHistorico );
} else {
    $obFormulario->addHidden        ( $obHdnCodHistorico );
    $obFormulario->addComponente    ( $obLblCodHistorico );
}
$obFormulario->addComponente        ( $obTxtNomHistorico );
$obFormulario->addComponente        ( $obRdnComplemento  );

if($stAcao=='incluir')
    $obFormulario->OK();
else
    $obFormulario->Cancelar( $stLocation );

$obFormulario->show                 ();

//include_once($pgJs);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
