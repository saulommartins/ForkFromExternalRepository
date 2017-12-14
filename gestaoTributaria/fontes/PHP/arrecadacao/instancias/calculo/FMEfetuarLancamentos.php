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
    * Página de Formulario para EFETUAR LANCAMENTOS  - MODULO ARRECADACAO
    * Data de criação : 01/06/2005

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Marcelo Boezzio Paulino

    * $Id: FMEfetuarLancamentos.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.03.05
**/

/*
$Log$
Revision 1.9  2007/04/16 18:06:22  cercato
Bug #9132#

Revision 1.8  2006/12/29 16:01:01  dibueno
Alteração para setagem de variavel de sessao

Revision 1.7  2006/09/15 11:50:26  fabio
corrigidas tags de caso de uso

Revision 1.6  2006/09/15 10:57:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );
include_once '../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/classes/componentes/MontaGrupoCredito.class.php';

//Define o nome dos arquivos PHP
$stPrograma      = "EfetuarLancamentos";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OCManterCalculo.php";
$pgJs            = "JSManterCalculo.js";
include_once( $pgJs );

$stAcao = "lancamentoAutomatico";

Sessao::write('link', '' );

// instancia objeto
$obMontaGrupoCredito = new MontaGrupoCredito;
$obRMONCredito = new RMONCredito;
// pegar mascara de credito
$obRMONCredito->consultarMascaraCredito();
$stMascaraCredito = $obRMONCredito->getMascaraCredito();

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCodModulo = new Hidden;
$obHdnCodModulo->setName  ( "inCodModulo" );
$obHdnCodModulo->setValue ( $_REQUEST["inCodModulo"] );

// DEFINE OBJETOS DO FORMULARIO
$obRdbEmissaoNaoEmitir = new Radio;
$obRdbEmissaoNaoEmitir->setTitle ( "Informe se deverá ou não ser emitido carnê de cobrança." );
$obRdbEmissaoNaoEmitir->setRotulo   ( "Emissão de Carnês"                            );
$obRdbEmissaoNaoEmitir->setName     ( "emissao_carnes"                               );
$obRdbEmissaoNaoEmitir->setId       ( "emissao_carnes"                               );
$obRdbEmissaoNaoEmitir->setLabel    ( "Não Emitir"                                   );
$obRdbEmissaoNaoEmitir->setValue    ( "nao_emitir"                                   );
$obRdbEmissaoNaoEmitir->setNull     ( false                                          );
$obRdbEmissaoNaoEmitir->setChecked  ( true                                           );
$obRdbEmissaoNaoEmitir->obEvento->setOnChange( "montaModeloCarne();"  );

$obRdbEmissaoLocal = new Radio;
$obRdbEmissaoLocal->setRotulo   ( "Emissão de Carnês"                            );
$obRdbEmissaoLocal->setName     ( "emissao_carnes"                               );
$obRdbEmissaoLocal->setId       ( "emissao_carnes"                               );
$obRdbEmissaoLocal->setLabel    ( "Impressão Local"                              );
$obRdbEmissaoLocal->setValue    ( "local"                                         );
$obRdbEmissaoLocal->setNull     ( false                                          );
$obRdbEmissaoLocal->setChecked  ( false                                          );
$obRdbEmissaoLocal->obEvento->setOnChange( "montaModeloCarne();"  );

$obRdbEmissaoGrafica = new Radio;
$obRdbEmissaoGrafica->setRotulo   ( "Emissão de Carnês"                            );
$obRdbEmissaoGrafica->setName     ( "emissao_carnes"                               );
$obRdbEmissaoGrafica->setId       ( "emissao_carnes"                               );
$obRdbEmissaoGrafica->setLabel    ( "Gráfica"                                      );
$obRdbEmissaoGrafica->setValue    ( "grafica"                                      );
$obRdbEmissaoGrafica->setNull     ( false                                          );
$obRdbEmissaoGrafica->setChecked  ( false                                          );
$obRdbEmissaoGrafica->obEvento->setOnChange( "montaModeloCarne();"  );

$obSpnModelo = new Span;
$obSpnModelo->setId( "spnModelo");

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction           ( $pgProc  );
$obForm->setTarget           ( "oculto" );
//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm              );
$obFormulario->addHidden     ( $obHdnCtrl           );
$obFormulario->addHidden     ( $obHdnAcao           );
$obFormulario->addHidden     ( $obHdnCodModulo      );
$obFormulario->addTitulo     ( "Dados para Cálculo" );
$obMontaGrupoCredito->geraFormulario( $obFormulario, true, false );
//$obFormulario->agrupaComponentes( array($obRdbEmissaoNaoEmitir,$obRdbEmissaoLocal,$obRdbEmissaoGrafica));
//$obFormulario->addSpan       ( $obSpnModelo            );

Sessao::write('TipoCalculo', "geral" );
Sessao::write('lancados', -1 );

$obFormulario->Ok();
$obFormulario->show();

?>
