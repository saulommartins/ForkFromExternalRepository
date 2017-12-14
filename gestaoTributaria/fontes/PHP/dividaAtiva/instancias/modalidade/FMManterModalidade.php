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
    * Página de Formulario de Inclusao/Alteracao de Modalidade

    * Data de Criação   : 21/09/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FMManterModalidade.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.04.07

*/

/*
$Log$
Revision 1.6  2007/04/16 20:48:42  cercato
Bug #9109#

Revision 1.5  2007/02/28 17:07:33  cercato
Bug #8534#

Revision 1.4  2006/09/29 15:53:42  dibueno
Alteração do Numerico para Moeda nos valores da modalidade

Revision 1.3  2006/09/29 15:31:38  cercato
correcao definindo codigo da acao no componente documento.

Revision 1.2  2006/09/29 14:35:46  cercato
alterando forma de utilizar componente de credito.

Revision 1.1  2006/09/25 14:56:20  cercato
implementacao dos formularios de acordo com interface abstrata.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_NORMAS_CLASSES."componentes/IPopUpNorma.class.php" );
include_once ( CAM_GA_ADM_COMPONENTES."IPopUpFuncao.class.php" );
include_once ( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php" );
include_once ( CAM_GT_MON_COMPONENTES."IPopUpCredito.class.php" );
include_once ( CAM_GT_MON_COMPONENTES."IPopUpAcrescimo.class.php" );

$stLink = Sessao::read('stLink');

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterModalidade";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php?".$stLink;
$pgForm        = "FM".$stPrograma.".php";
$pgForm2       = "FM".$stPrograma."Divida.php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php?".Sessao::getId();
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

//tipo (inscricao de divida)
$obRdbInscricaoDivida = new Radio;
$obRdbInscricaoDivida->setRotulo   ( "Tipo" );
$obRdbInscricaoDivida->setTitle    ( "Informe o tipo de modalidade." );
$obRdbInscricaoDivida->setName     ( "stTipoModalidade" );
$obRdbInscricaoDivida->setID     ( "stTipoModalidade" );
$obRdbInscricaoDivida->setLabel    ( "Inscrição de Dívida" );
$obRdbInscricaoDivida->setValue    ( "inscricao" );
$obRdbInscricaoDivida->setNull     ( false );
$obRdbInscricaoDivida->setChecked( true );

//tipo (consolidacao)
$obRdbConsolidacao = new Radio;
$obRdbConsolidacao->setRotulo   ( "Tipo" );
$obRdbConsolidacao->setTitle    ( "Informe o tipo de modalidade." );
$obRdbConsolidacao->setName     ( "stTipoModalidade" );
$obRdbConsolidacao->setID     ( "stTipoModalidade" );
$obRdbConsolidacao->setLabel    ( "Consolidação" );
$obRdbConsolidacao->setValue    ( "consolidacao" );
$obRdbConsolidacao->setNull     ( false );

//tipo (parcelamento)
$obRdbParcelamento = new Radio;
$obRdbParcelamento->setRotulo   ( "Tipo" );
$obRdbParcelamento->setTitle    ( "Informe o tipo de modalidade." );
$obRdbParcelamento->setName     ( "stTipoModalidade" );
$obRdbParcelamento->setID     ( "stTipoModalidade" );
$obRdbParcelamento->setLabel    ( "Parcelamento" );
$obRdbParcelamento->setValue    ( "parcelamento" );
$obRdbParcelamento->setNull     ( false );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgForm2 );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.04.07" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addTitulo     ( "Dados para Modalidade" );
$obFormulario->agrupaComponentes ( array($obRdbInscricaoDivida, $obRdbConsolidacao, $obRdbParcelamento) );
$obFormulario->Ok ();
$obFormulario->setFormFocus( $obRdbInscricaoDivida->getId() );

$obFormulario->show();
