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
  * Página de Filtro para Avaliar Imóvel
  * Data de criação : 10/06/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @ignore

    * $Id: FLAvaliarImovel.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.06
**/

/*
$Log$
Revision 1.7  2006/11/14 15:58:50  dibueno
Alterações devido à utilização de componentes no formulario

Revision 1.6  2006/09/15 11:14:47  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRAvaliacaoImobiliaria.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php"     );
include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovel.class.php"     );
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "AvaliarImovel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write( "link", "" );

$obRARRAvaliacaoImobiliaria = new RARRAvaliacaoImobiliaria;
$obMontaLocalizacao         = new MontaLocalizacao;
$obMontaLocalizacao->setObrigatorio( false );
$obMontaLocalizacao->setEscondeId ( false );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( '' );

$obPopUpImovel = new IPopUpImovel;
$obPopUpImovel->obInnerImovel->setNull (true);

$obBtnOK = new OK;
$obBtnLimpar = new Limpar;
$obBtnLimpar->obEvento->setOnClick( "LimparFL();" );

$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//MONTANDO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addTitulo ( "Dados para Filtro" );
$obPopUpCGM = new IPopUpCGM( $obForm );
$obPopUpCGM->setNull ( true );
$obPopUpCGM->setRotulo ( "Contribuinte" );
$obPopUpCGM->setTitle ( "Codigo do Contribuinte" );
$obFormulario->addComponente( $obPopUpCGM				);
//$obFormulario->addComponente ( $obBscInscricaoMunicipal );
$obPopUpImovel->geraFormulario ( $obFormulario );

$obMontaLocalizacao->geraFormulario( $obFormulario );
$obFormulario->defineBarra( array( $obBtnOK, $obBtnLimpar ) );
$obFormulario->Show();
