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
    * Página de Formulário Classificacao Contabil
    * Data de Criação   : 10/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-08-15 11:15:55 -0300 (Qua, 15 Ago 2007) $

    * Casos de uso: uc-02.02.01
*/

/*
$Log$
Revision 1.5  2007/08/15 14:15:13  hboaventura
Bug#9914#

Revision 1.4  2006/07/05 20:50:46  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_CONT_NEGOCIO . "RContabilidadeClassificacaoContabil.class.php");

$stPrograma = "ManterClassificacaoContabil";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRegra = new RContabilidadeClassificacaoContabil;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
if ( empty( $stAcao )) {
    $stAcao = "alterar";

}

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodClassificacao = new Hidden;
$obHdnCodClassificacao->setName( "inCodClassificacao" );
$obHdnCodClassificacao->setValue($_REQUEST['inCodClassificacao']);

$obLblCodClassificacao = new Label;
$obLblCodClassificacao->setRotulo( "Código" );
$obLblCodClassificacao->setValue($_REQUEST['inCodClassificacao']);

$obTxtCodClassificacao = new TextBox;
$obTxtCodClassificacao->setRotulo        ( "Código" );
$obTxtCodClassificacao->setTitle         ( "Informe um código de classificação contábil" );
$obTxtCodClassificacao->setName          ( "inCodClassificacao" );
$obTxtCodClassificacao->setValue         ( $inCodClassificacao);
$obTxtCodClassificacao->setSize          ( 5 );
$obTxtCodClassificacao->setMaxLength     ( 5 );
$obTxtCodClassificacao->setNull          ( false );
$obTxtCodClassificacao->setInteiro       ( true  );

$obTxtNomClassificacao = new TextBox;
$obTxtNomClassificacao->setRotulo        ( "Descrição" );
$obTxtNomClassificacao->setTitle         ( "Informe uma descrição de classificação contábil" );
$obTxtNomClassificacao->setName          ( "stNomClassificacao" );
$obTxtNomClassificacao->setValue         ( $stNomClassificacao);
$obTxtNomClassificacao->setSize          ( 40 );
$obTxtNomClassificacao->setMaxLength     ( 80 );
$obTxtNomClassificacao->setNull          ( false );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->setAjuda             ('UC-02.02.01');
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );

$obFormulario->addTitulo            ( "Dados para Classificação Contábil" );

if ($stAcao=="incluir") {
   $obFormulario->addComponente        ( $obTxtCodClassificacao   );
} else {
    $obTxtNomClassificacao->setValue( $_REQUEST['stNomClassificacao']);

   $obFormulario->addHidden($obHdnCodClassificacao);
   $obFormulario->addComponente($obLblCodClassificacao);
}

$obFormulario->addComponente        ( $obTxtNomClassificacao   );

if ($stAcao == 'alterar') {
    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao;
    $obFormulario->Cancelar( $stLocation );
} else {
    $obFormulario->OK();
}
$obFormulario->show();

//include_once($pgJs);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
