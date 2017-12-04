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
  * Página de Formulario para EXECUTAR CALCULOS	 - MODULO ARRECADACAO
  * Data de criação : 01/06/2005

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Lucas Teixeira Stephanou

    * $Id: FLRelatorioDevedores.php 59881 2014-09-17 20:44:49Z carlos.silva $

    Caso de uso: uc-05.03.05
**/

/*
$Log$
Revision 1.9  2006/09/15 11:50:26  fabio
corrigidas tags de caso de uso

Revision 1.8  2006/09/15 10:57:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma      = 'RelatorioDevedores';
$pgFilt          = 'FL'.$stPrograma.'.php';
$pgList          = 'LS'.$stPrograma.'.php';
$pgForm          = 'FM'.$stPrograma.'.php';
$pgProc          = 'PR'.$stPrograma.'.php';
$pgOcul          = 'OC'.$stPrograma.'.php';
$pgJs            = 'JS'.$stPrograma.'.js';

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

// instancia objeto
//$obRMONCredito = new RMONCredito;
// pegar mascara de credito
//$obRMONCredito->consultarMascaraCredito();
//$stMascaraCredito = $obRMONCredito->getMascaraCredito();

// OBJETOS HIDDEN
/*$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );*/

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obRdbGrupo = new Radio;
$obRdbGrupo->setRotulo     ( "Filtrar por"       );
$obRdbGrupo->setName       ( "stFiltro"          );
$obRdbGrupo->setLabel      ( "Grupo de Créditos" );
$obRdbGrupo->setValue      ( "grupo"             );
$obRdbGrupo->setNull       ( false               );
$obRdbGrupo->setChecked    ( false               );

$obRdbCredito = new Radio;
$obRdbCredito->setTitle    ( "Informe a forma de cálculo a ser utilizada." );
$obRdbCredito->setRotulo   ( "Filtrar por" );
$obRdbCredito->setName     ( "stFiltro"    );
$obRdbCredito->setLabel    ( "Crédito"     );
$obRdbCredito->setValue    ( "credito"     );
$obRdbCredito->setNull     ( false         );
$obRdbCredito->setChecked  ( true          );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction         ( $pgForm  );
$obForm->setTarget         ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm     ( $obForm    );
//$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden   ( $obHdnAcao );
$obFormulario->addTitulo   ( "Filtro para o Relatório"  	);
$obFormulario->agrupaComponentes ( array( $obRdbCredito, $obRdbGrupo) );

$obFormulario->Ok();
$obFormulario->show();

?>