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
    * Página de Formulário Filtro para o Lançamento do Imposto de Transferência
    * Data de Criação   : 03/10/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: FLLancarTransferencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.21
*/
/*
$Log$
Revision 1.1  2006/10/10 15:17:57  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovel.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "LancarTransferencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write( "link", "" );

$obIPopUpImovel = new IPopUpImovel;
$obIPopUpImovel->obInnerImovel->setNull( true );

$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->setCadastroLocalizacao( false );
$obMontaLocalizacao->setPopUp( true );
$obMontaLocalizacao->setObrigatorio( false );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm               );
$obFormulario->setAjuda             ( "UC-05.01.09"         );
$obFormulario->addHidden            ( $obHdnCtrl            );
$obFormulario->addHidden            ( $obHdnAcao            );
$obFormulario->addTitulo            ( "Dados para Filtro"   );
$obIPopUpImovel->geraFormulario     ( $obFormulario         );
$obMontaLocalizacao->geraFormulario ( $obFormulario         );
$obFormulario->Ok();
$obFormulario->show();
?>
