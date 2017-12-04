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
* Oculto de Processamento e para PopUp de Empenho
* Data de Criação: 18/10/2006

* @author Analista: Lucas Teixeira Stephanou
* @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    $Revision: 31716 $
    $Name$
    $Autor: $
    $Date: 2008-03-28 16:53:07 -0300 (Sex, 28 Mar 2008) $

    * Casos de uso: uc-02.03.03
*/

/*
$Log$
Revision 1.1  2006/10/18 13:45:48  domluc
PopUp de Empenho, e oculto compartilhado com
componente IPopUpEmpenho

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ProcurarEmpenho";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

Sessao::remove('linkPopUp');

$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST['nomForm'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "tipoBusca" );
$obHdnTipoBusca->setValue( $_REQUEST['tipoBusca'] );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName( "inCodigoEntidade" );
$obHdnCodEntidade->setId  ( "inCodigoEntidade" );
$obHdnCodEntidade->setValue( $_REQUEST['inCodigoEntidade'] );

$obHdnExercicioEmpenho = new Hidden;
$obHdnExercicioEmpenho->setName( "stExercicioEmpenho" );
$obHdnExercicioEmpenho->setValue( $_REQUEST['stExercicioEmpenho'] );

$obHdnCgmCredor = new Hidden;
$obHdnCgmCredor->setName( "cgmCredor" );
$obHdnCgmCredor->setValue( $_REQUEST['cgmCredor'] );

$obHdnDtVigencia = new Hidden;
$obHdnDtVigencia->setName( "dtVigencia" );
$obHdnDtVigencia->setValue( $_REQUEST['dtVigencia'] );

$obHdnDtInicial = new Hidden;
$obHdnDtInicial->setName( "dtInicial" );
$obHdnDtInicial->setValue( $_REQUEST['dtInicial'] );

$obHdnDtFinal = new Hidden;
$obHdnDtFinal->setName( "dtFinal" );
$obHdnDtFinal->setValue( $_REQUEST['dtFinal'] );

$obHdnDtEmissao = new Hidden;
$obHdnDtEmissao->setName( "dtEmissao" );
$obHdnDtEmissao->setValue( $_REQUEST['dtEmissao'] );

$obHdnRegistroPrecos = new Hidden;
$obHdnRegistroPrecos->setName( "registroPrecos" );
$obHdnRegistroPrecos->setValue( $_REQUEST['registroPrecos'] );

//Define o objeto TEXT para Codigo do Empenho Inicial
$obTxtCodEmpenhoInicial = new TextBox;
$obTxtCodEmpenhoInicial->setName     ( "inCodEmpenhoInicial" );
$obTxtCodEmpenhoInicial->setValue    ( $inCodEmpenhoInicial  );
$obTxtCodEmpenhoInicial->setRotulo   ( "Número do Empenho"   );
$obTxtCodEmpenhoInicial->setInteiro  ( true                  );
$obTxtCodEmpenhoInicial->setNull     ( true                  );

//Define objeto Label
$obLblEmpenho = new Label;
$obLblEmpenho->setValue( "a" );

//Define o objeto TEXT para Codigo do Empenho Final
$obTxtCodEmpenhoFinal = new TextBox;
$obTxtCodEmpenhoFinal->setName     ( "inCodEmpenhoFinal" );
$obTxtCodEmpenhoFinal->setValue    ( $inCodEmpenhoFinal  );
$obTxtCodEmpenhoFinal->setRotulo   ( "Número do Empenho" );
$obTxtCodEmpenhoFinal->setInteiro  ( true                );
$obTxtCodEmpenhoFinal->setNull     ( true                );

// Define objeto Data para Periodo
$obDtInicial = new Data;
$obDtInicial->setName     ( "stDtInicial" );
$obDtInicial->setRotulo   ( "Período" );
$obDtInicial->setTitle    ( '' );
$obDtInicial->setNull     ( true );

// Define Objeto Label
$obLabel = new Label;
$obLabel->setValue( " até " );

// Define objeto Data para validade final
$obDtFinal = new Data;
$obDtFinal->setName     ( "stDtFinal" );
$obDtFinal->setRotulo   ( "Período" );
$obDtFinal->setTitle    ( '' );
$obDtFinal->setNull     ( true );

$obLblEntidade = new Label;
$obLblEntidade->setRotulo ( "Entidade" 				   );
$obLblEntidade->setName	  ( "stNomEntidade" 		   );
$obLblEntidade->setValue  ( $_REQUEST['stNomEntidade'] );

$obTxtAno = new Inteiro;
$obTxtAno->setName      ( "stExercicio"         );
$obTxtAno->setId        ( "stExercicio"         );
if ($_REQUEST['stExercicioEmpenho']) {
    $obTxtAno->setValue     ( $_REQUEST['stExercicioEmpenho'] );
} else {
    $obTxtAno->setValue     ( Sessao::getExercicio()    );
}
$obTxtAno->setRotulo    ( "Exercício do Empenho");
$obTxtAno->setTitle     ( "Informe o exercício do empenho.");
$obTxtAno->setNull      ( false                 );
$obTxtAno->setMaxLength ( 4                     );
$obTxtAno->setSize      ( 4                     );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo ( "Dados para filtro"   );
$obFormulario->addForm   ( $obForm               );
$obFormulario->addHidden ( $obHdnAcao            );
$obFormulario->addHidden ( $obHdnCtrl            );
$obFormulario->addHidden ( $obHdnForm            );
$obFormulario->addHidden ( $obHdnCampoNum        );
$obFormulario->addHidden ( $obHdnCampoNom        );
$obFormulario->addHidden ( $obHdnTipoBusca       );
$obFormulario->addHidden ( $obHdnExercicioEmpenho);
$obFormulario->addHidden ( $obHdnCodEntidade     );
$obFormulario->addHidden ( $obHdnCgmCredor		 );
$obFormulario->addHidden ( $obHdnDtVigencia		 );
$obFormulario->addHidden ( $obHdnDtInicial       );
$obFormulario->addHidden ( $obHdnDtFinal         );
$obFormulario->addHidden ( $obHdnDtEmissao       );
$obFormulario->addHidden ( $obHdnRegistroPrecos  );
if ($_REQUEST['stNomEntidade']) {
    $obFormulario->addComponente    ( $obLblEntidade        );
}
$obFormulario->addComponente        ( $obTxtAno             );
$obFormulario->agrupaComponentes    ( array($obTxtCodEmpenhoInicial, $obLblEmpenho, $obTxtCodEmpenhoFinal ) );
$obFormulario->agrupaComponentes    ( array($obDtInicial, $obLabel, $obDtFinal ) );
$obFormulario->OK                   ();
$obFormulario->show                 ();

if ($_REQUEST['stNomEntidade']) {
    echo "<script type='text/javascript'>\r\n";
    echo "var cod  = opener.document.getElementById('inCodEntidadeEmpenho').value;\r\n";
    echo "var sel = opener.document.getElementById('stNomEntidade'); \r\n";
    echo "var tex = sel.options[sel.selectedIndex].text;\r\n";
    echo "document.getElementById('labelEntidade').innerHTML = cod + ' - ' + tex;\r\n";
    echo "document.getElementById('inCodigoEntidade').value = cod;\r\n";
    echo "</script>\r\n";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
