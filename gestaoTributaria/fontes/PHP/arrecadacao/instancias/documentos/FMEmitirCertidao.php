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
  * Página de Formulário para emissão de certidão
  * Data de criação : 16/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: Tonismar R. Bernardo

    * $Id: FMEmitirCertidao.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.11
**/

/*
$Log$
Revision 1.4  2006/09/15 11:50:45  fabio
corrigidas tags de caso de uso

Revision 1.3  2006/09/15 11:08:05  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "EmitirCertidao";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgFormVinculo   = "FM".$stPrograma."Vinculo.php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";
//include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obRdbGeral = new Radio;
$obRdbGeral->setRotulo     ( "Tipo de Emissão"    );
$obRdbGeral->setName       ( "stCtrl"             );
$obRdbGeral->setLabel      ( "Geral"              );
$obRdbGeral->setValue      ( "geral"              );
$obRdbGeral->setTitle      ( "Tipo de Emissão"    );
$obRdbGeral->setNull       ( false                );
$obRdbGeral->setChecked    ( false                );

$obRdbParcial = new Radio;
$obRdbParcial->setRotulo   ( "Tipo de Emissão"    );
$obRdbParcial->setName     ( "stCtrl"             );
$obRdbParcial->setLabel    ( "Parcial"            );
$obRdbParcial->setValue    ( "parcial"            );
$obRdbParcial->setNull     ( false                );
$obRdbParcial->setChecked  ( false                );

$obRdbIndividual = new Radio;
$obRdbIndividual->setRotulo   ( "Tipo de Emissão"    );
$obRdbIndividual->setName     ( "stCtrl"             );
$obRdbIndividual->setLabel    ( "Individual"         );
$obRdbIndividual->setValue    ( "individual"         );
$obRdbIndividual->setNull     ( false                );
$obRdbIndividual->setChecked  ( false                );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction           ( $pgOcul    );
$obForm->setTarget           ( "oculto"   );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                  );
//$obFormulario->addHidden   ( $obHdnCtrl               );
$obFormulario->addHidden     ( $obHdnAcao               );
$obFormulario->addTitulo     ( "Dados para emissão"     );
$obFormulario->agrupaComponentes     ( array( $obRdbGeral, $obRdbParcial, $obRdbIndividual) );

$obFormulario->Ok();
$obFormulario->show();
