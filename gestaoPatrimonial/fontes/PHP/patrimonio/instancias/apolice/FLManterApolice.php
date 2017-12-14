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
    * Data de Criação: 15/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 26727 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-11-12 16:31:31 -0200 (Seg, 12 Nov 2007) $

    * Casos de uso: uc-03.01.08
*/

/*
$Log$
Revision 1.1  2007/10/17 13:41:48  hboaventura
correção dos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );

$stPrograma = "ManterApolice";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgList);

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//instancia um componente para o numero da apolice
$obTxtNumApolice = new TextBox();
$obTxtNumApolice->setName( 'stNumApolice' );
$obTxtNumApolice->setRotulo( 'Número' );
$obTxtNumApolice->setTitle( 'Informe o número da apólice.' );
$obTxtNumApolice->setNull( true );
$obTxtNumApolice->setMaxLength( 15 );
$obTxtNumApolice->setSize( 15 );

//instancia um busca inner para a seguradora
$obIPopUpCGM = new IPopUpCGMVinculado( $obForm );
$obIPopUpCGM->setRotulo           ( 'Seguradora'     );
$obIPopUpCGM->setTitle            ( 'Informe a seguradora.' );
$obIPopUpCGM->setTabelaVinculo    ( 'sw_cgm_pessoa_juridica' );
$obIPopUpCGM->setCampoVinculo     ( 'numcgm' );
$obIPopUpCGM->setNomeVinculo      ( 'seguradora' );
$obIPopUpCGM->setName             ( 'stNomCGM'       );
$obIPopUpCGM->setId               ( 'stNomCGM'       );
$obIPopUpCGM->obCampoCod->setName ( 'inNumCGM'       );
$obIPopUpCGM->obCampoCod->setId   ( 'inNumCGM'       );
$obIPopUpCGM->setNull             ( true             );

//instancia um componente data para o vencimento
$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setNull( true );

//instancia um textbox para o contato
$obTxtContato = new TextBox();
$obTxtContato->setRotulo( 'Contato' );
$obTxtContato->setTitle( 'Informe o contato da apólice.' );
$obTxtContato->setName( 'stContato' );
$obTxtContato->setMaxLength( 40 );
$obTxtContato->setSize( 40 );
$obTxtContato->setNull( true );

$obTipoBuscaContato = new TipoBusca( $obTxtContato );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda     ('UC-03.01.08');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addComponente( $obTxtNumApolice );
$obFormulario->addComponente( $obIPopUpCGM );
$obFormulario->addComponente( $obPeriodicidade );
$obFormulario->addComponente( $obTipoBuscaContato );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
