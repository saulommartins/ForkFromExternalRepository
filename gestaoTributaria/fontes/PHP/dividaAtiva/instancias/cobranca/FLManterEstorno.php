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
    * Página de Formulario de Filtro para Estornar Cobranca Administrativa

    * Data de Criação   : 27/02/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FLManterEstorno.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.04.04

*/

/*
$Log$
Revision 1.4  2007/07/19 21:01:07  cercato
Bug #9705#

Revision 1.3  2007/04/24 19:32:48  cercato
inserindo campo "cobranca" no filtro de estorno

Revision 1.2  2007/03/26 21:27:53  cercato
Bug #8891#

Revision 1.1  2007/02/27 19:53:30  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpDocumento.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpCobranca.class.php" );

if ( empty( $_REQUEST['stAcao'] ) || $_REQUEST['stAcao'] == "incluir" ) {
    $_REQUEST['stAcao'] = "Estornar";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterCobranca";
$pgFilt        = "FL".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgList        = "LSManterEstorno.php";

Sessao::remove('sessao_transf4');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $request->get('stCtrl')  );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.04.04" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addTitulo     ("Dados para Filtro");

$obIPopUpCobranca = new IPopUpCobranca;
$obIPopUpCobranca->obInnerCobranca->setNull ( true );
$obIPopUpCobranca->geraFormulario( $obFormulario );

$obIPopUpDocumento = new IPopUpDocumento;
$obIPopUpDocumento->obInnerDocumento->setNull ( true );
$obIPopUpDocumento->obInnerDocumento->setTitle ( "Informe o termo número do termo de parcelamento." );
$obIPopUpDocumento->obInnerDocumento->setRotulo ( "Termo do Parcelamento" );
$obIPopUpDocumento->geraFormulario( $obFormulario );

$obPopUpCGM = new IPopUpCGM( $obForm );
$obPopUpCGM->setNull ( true );
$obPopUpCGM->setRotulo ( "Contribuinte" );
$obPopUpCGM->setTitle ( "Contribuinte" );

$obFormulario->addComponente( $obPopUpCGM );

$obValorParcelamento = new Numerico;
$obValorParcelamento->setNull ( true );
$obValorParcelamento->setRotulo ( "Valor do Parcelamento" );
$obValorParcelamento->setName ( "ValorParcela" );

$obFormulario->addComponente( $obValorParcelamento );

$obNroParcela = new Inteiro;
$obNroParcela->setNull ( true );
$obNroParcela->setRotulo ( "Número de Parcelas" );
$obNroParcela->setName ( "NroParcela" );

$obFormulario->addComponente( $obNroParcela );

$obNroParcelaAtraso = new Inteiro;
$obNroParcelaAtraso->setNull ( true );
$obNroParcelaAtraso->setRotulo ( "Número de Parcelas em Atraso" );
$obNroParcelaAtraso->setName ( "NroParcelaAtraso" );

$obFormulario->addComponente( $obNroParcelaAtraso );

$obNroDiasAtraso = new Inteiro;
$obNroDiasAtraso->setNull ( true );
$obNroDiasAtraso->setRotulo ( "Número de Dias em Atraso" );
$obNroDiasAtraso->setName ( "NroDiasAtraso" );

$obFormulario->addComponente( $obNroDiasAtraso );

$obFormulario->Ok ();
$obFormulario->show();
