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
/*
    * Filtro para listagem dos contratos
    * Data de Criação   : 10/02/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor André Machado

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterNotasFiscais";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList  );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao"            );
$obHdnAcao->setValue( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

$obTxtNroNota = new TextBox;
$obTxtNroNota->setName   ( "inNumNota"                  );
$obTxtNroNota->setValue  ( $inNumNota                   );
$obTxtNroNota->setRotulo ( "Número da Nota"             );
$obTxtNroNota->setTitle  ( "Informe o número da nota."  );
$obTxtNroNota->setNull   ( true                         );
$obTxtNroNota->setInteiro( true                         );

$obTxtSerieNota = new TextBox;
$obTxtSerieNota->setName   ( "inNumSerie"                     );
$obTxtSerieNota->setValue  ( $inNumSerie                      );
$obTxtSerieNota->setRotulo ( "Série da Nota Fiscal"           );
$obTxtSerieNota->setTitle  ( "Informe a série da nota fiscal.");
$obTxtSerieNota->setNull   ( true                             );

$obDtEmissao = new Data;
$obDtEmissao->setName   ( "dtEmissao"                          );
$obDtEmissao->setRotulo ( "Data de Emissão"                    ) ;
$obDtEmissao->setValue  ( $dtPublicacao                        );
$obDtEmissao->setTitle  ( 'Informe a data de emissão da nota.' );
$obDtEmissao->setNull   ( true                                 );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden          ( $obHdnAcao      );
$obFormulario->addHidden          ( $obHdnCtrl      );
$obFormulario->addComponente      ( $obTxtNroNota   );
$obFormulario->addComponente      ( $obTxtSerieNota );
$obFormulario->addComponente      ( $obDtEmissao    );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
