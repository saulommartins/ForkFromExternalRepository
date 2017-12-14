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
    * Página de Filtro de Mapa de Compras
    * Data de Criação   :06/10/2006

    * @author Analista: Cleisson Barbosa
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    * Casos de uso:uc-06.04.00
*/

/**
$Log$
Revision 1.2  2007/10/10 21:37:59  bruce
*** empty log message ***

Revision 1.1  2007/10/10 15:40:02  bruce
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterObra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTxtCodObra = new Inteiro;
$obTxtCodObra->setName   ( 'inCodObra'      );
$obTxtCodObra->setId     ( 'inCodObra'      );
$obTxtCodObra->setRotulo ( 'Código da Obra' );
$obTxtCodObra->setNull   ( true            );
$obTxtCodObra->setMaxLength ( 4 );

$stExercicio = new Exercicio;
$stExercicio->setNull ( true );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setName   ( 'stDescricao' );
$obTxtDescricao->setId     ( 'stDescricao' );
$obTxtDescricao->setRotulo ( 'Descrição'   );
$obTxtDescricao->setNull   ( true         );
$obTxtDescricao->setSize   ( 100 );
$obTxtDescricao->setMaxLength ( 100 );

$obFormulario = new Formulario();
$obFormulario->addForm              ($obForm);

$obFormulario->addHidden            ($obHdnAcao       );
$obFormulario->addHidden            ($obHdnCtrl       );
$obFormulario->addComponente ( $obTxtCodObra          );
$obFormulario->addComponente ( $stExercicio           );
$obFormulario->addComponente ( $obTxtDescricao        );

$obFormulario->ok();
$obFormulario->show();

?>
