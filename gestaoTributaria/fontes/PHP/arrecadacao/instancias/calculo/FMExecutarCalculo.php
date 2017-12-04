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

    * $Id: FMExecutarCalculo.php 59612 2014-09-02 12:00:51Z gelson $

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
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "ManterCalculos";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FMExecutarCalculoGrupo.php";
$pgProc          = "PRManterCalculo.php";
$pgOcul          = "OCManterCalculo.php";
$pgJs            = "JSManterCalculo.js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

Sessao::write( "link", "" );

// instancia objeto
$obRMONCredito = new RMONCredito;
// pegar mascara de credito
$obRMONCredito->consultarMascaraCredito();
$stMascaraCredito = $obRMONCredito->getMascaraCredito();

// OBJETOS HIDDEN
/*$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );*/

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obRdbGrupo = new Radio;
$obRdbGrupo->setRotulo     ( "Calcular por"                                                           );
$obRdbGrupo->setName       ( "stCtrl"                                                                 );
$obRdbGrupo->setLabel      ( "Grupo de Créditos"                                                      );
$obRdbGrupo->setValue      ( "grupo"                                                                  );
$obRdbGrupo->setNull       ( false                                                                    );
$obRdbGrupo->setChecked    ( false                                                                    );

$obRdbCredito = new Radio;
$obRdbCredito->setTitle    ( "Informe a forma de cálculo a ser utilizada." );
$obRdbCredito->setRotulo   ( "Calcular por"                                                       	 );
$obRdbCredito->setName     ( "stCtrl"                                                                );
$obRdbCredito->setLabel    ( "Crédito"                                                               );
$obRdbCredito->setValue    ( "credito"                                                               );
$obRdbCredito->setNull     ( false                                                                   );
$obRdbCredito->setChecked  ( true                                                                    );
//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction           ( $pgOcul    );
$obForm->setTarget           ( "oculto"   );
//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                  );
//$obFormulario->addHidden   ( $obHdnCtrl               );
$obFormulario->addHidden     ( $obHdnAcao               );
$obFormulario->addTitulo     ( "Filtro para Cálculo"  	);
$obFormulario->agrupaComponentes     ( array( $obRdbCredito, $obRdbGrupo) );

$obFormulario->Ok();
$obFormulario->show();

?>
