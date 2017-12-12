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
    * Página de Filtro para Classificar receitas
    * Data de Criação : 08/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30835 $
    $Name$
    $Author: domluc $
    $Date: 2007-06-14 11:26:55 -0300 (Qui, 14 Jun 2007) $

    * Casos de uso: uc-02.04.03
*/

/*
$Log$
Revision 1.9  2007/06/14 14:26:55  domluc
Ajuste na Ordenação

Revision 1.8  2007/04/05 20:15:32  luciano
#8913#

Revision 1.7  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterReceitas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obForm = new Form;
$obForm->setAction ( $pgForm );
$obForm->setTarget ( "telaPrincipal" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

// Define objeto Exercicio
$obTxtExercicio = new Exercicio();
$obTxtExercicio->setName       ( 'stExercicio'                );
$obTxtExercicio->setTitle      ( 'Informe o Exercício'        );
$obTxtExercicio->setValue      ( Sessao::getExercicio()           );
$obTxtExercicio->setNull       ( false                        );

// Define Objeto Select para Ripo de receita
$obCmbTipoReceita = new Select();
$obCmbTipoReceita->setRotulo ( "Tipo Receita"                      );
$obCmbTipoReceita->setName   ( "stTipoReceita"                     );
$obCmbTipoReceita->setTitle  ( "Selecione o Tipo de Receita a Classificar" );
$obCmbTipoReceita->addOption ( ""            ,"Selecione"          );
$obCmbTipoReceita->addOption ( "orcamentaria","Orçamentária"       );
$obCmbTipoReceita->addOption ( "extra"       ,"Extra-Orçamentária" );
$obCmbTipoReceita->setValue  ( $stTipoReceita                      );
$obCmbTipoReceita->setNull   ( false                               );
$obCmbTipoReceita->obEvento->setOnChange( "buscaDado('mostraSpanContas');"         );

// Define Objeto Span para Itens da ordem ou liquidacao
$obSpnContas = new Span();
$obSpnContas->setId( 'spnContas' );

//DEFINICAO DOS COMPONENTES

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm            ( $obForm            );
$obFormulario->addHidden          ( $obHdnAcao         );
$obFormulario->addHidden          ( $obHdnCtrl         );

$obFormulario->addTitulo          ( "Filtro"               );
$obFormulario->addComponente      ( $obTxtExercicio        );
$obFormulario->addComponente      ( $obCmbTipoReceita      );
$obFormulario->addSpan            ( $obSpnContas            );

$obFormulario->Ok();

$obFormulario->show                 ();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
