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
    * Página de Formulário Histórico de Empenho
    * Data de Criação   : 01/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @ignore

    $Id: FMManterHistorico.php 66483 2016-09-02 17:16:31Z michel $

    * Casos de uso: uc-02.03.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GF_INCLUDE."validaGF.inc.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_NEGOCIO. "REmpenhoHistorico.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoPreEmpenho.class.php";

$stPrograma = "ManterHistorico";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stFiltro = "";
if ( Sessao::read('filtro') ) {
    $arFiltro = Sessao::read('filtro');
    $stFiltro = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        $stFiltro .= "&".$stCampo."=".@urlencode( $stValor );
    }
    $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
}

$stLocation = $pgList."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'].$stFiltro;

$obRegra = new REmpenhoHistorico;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
if ( empty( $stAcao ) || $stAcao=="incluir") {
    $stAcao = "incluir";
}

$stComplemento = "S";
$boReadOnly    = false;
$boNaoAltera   = false;

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

    $obREmpenhoPreEmpenho = new REmpenhoPreEmpenho;
    $obREmpenhoPreEmpenho->setExercicio(Sessao::getExercicio());
    $obREmpenhoPreEmpenho->obREmpenhoHistorico->setCodHistorico($inCodHistorico);
    $obREmpenhoPreEmpenho->obRUsuario->obRCGM->setNumCGM(NULL);
    $obREmpenhoPreEmpenho->listar($rsPreEmpenho);

    if($rsPreEmpenho->getNumLinhas() > 1 || $inCodHistorico === '0')
        $boNaoAltera = true;
} elseif ($stAcao == 'incluir') {
    $obRegra->proximoCodHistorico();
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
$obHdnCodHistorico->setValue( $inCodHistorico );
$obLblCodHistorico = new Label;
$obLblCodHistorico->setRotulo( "Código" );
$obLblCodHistorico->setValue ( " ".$inCodHistorico );

$obTxtCodHistorico = new TextBox;
$obTxtCodHistorico->setRotulo        ( "Código" );
$obTxtCodHistorico->setName          ( "inCodHistoricoInclusao" );
$obTxtCodHistorico->setTitle         ( "Código do histórico" );
$obTxtCodHistorico->setValue         ( $inCodHistorico );
$obTxtCodHistorico->setSize          ( 11 );
$obTxtCodHistorico->setMaxLength     ( 9  );
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
if($boNaoAltera)
    $obTxtNomHistorico->setLabel     ( true );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );

$obFormulario->addTitulo            ( "Dados para Histórico" );

if ($stAcao=='incluir') {
    $obFormulario->addComponente        ( $obTxtCodHistorico );
} else {
    $obFormulario->addHidden        ( $obHdnCodHistorico );
    $obFormulario->addComponente    ( $obLblCodHistorico );
}
$obFormulario->addComponente        ( $obTxtNomHistorico );

if($stAcao=='incluir')
    $obFormulario->OK();
else
    $obFormulario->Cancelar( $stLocation );

$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
