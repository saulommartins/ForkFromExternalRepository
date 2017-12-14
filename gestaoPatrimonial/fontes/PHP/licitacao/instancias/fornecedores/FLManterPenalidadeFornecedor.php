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
    * Pagina de filtro para Penalidade Fornecedor
    * Data de Criação   : 03/10/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 18941 $
    $Name$
    $Autor: $
    $Date: 2006-12-21 15:30:39 -0200 (Qui, 21 Dez 2006) $

    * Casos de uso: uc-03.05.28
*/

/*

$Log:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_COM_COMPONENTES."IPopUpFornecedor.class.php" );

$stPrograma = "ManterPenalidadeFornecedor";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
        $stAcao = "alterar";
}

$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

$obForm = new Form;
$obForm->setAction ( $pgList  );

$obFornecedor = new IPopUpFornecedor($obForm);
$obFornecedor->setId ( "stNomFornecedor" );
$obFornecedor->setTitle( "Selecione o Fornecedor que deseja pesquisar." );

$obTxtNumCertificacao = new TextBox;
$obTxtNumCertificacao->setRotulo ( "Número da Certificação"   );
$obTxtNumCertificacao->setName   ( "inNumCertificacao" );
$obTxtNumCertificacao->setValue  ( $inNumCertificacao  );
$obTxtNumCertificacao->setTitle ( "Informe o Número da Certficação do Fornecedor que deseja pesquisar." );
$obTxtNumCertificacao->setMaxLength(11);
$obTxtNumCertificacao->setMascara( '999999/9999' );

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addComponente( $obFornecedor );
$obFormulario->addComponente( $obTxtNumCertificacao );
$obFormulario->Ok();
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
