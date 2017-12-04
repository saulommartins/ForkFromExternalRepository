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
  * Página de Filtro de Prorrogar/Revogar Desoneração
  * Data de criação : 05/09/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: FLProrrogarDesoneracao.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.04
**/

/*
$Log$
Revision 1.3  2006/09/15 11:50:40  fabio
corrigidas tags de caso de uso

Revision 1.2  2006/09/15 11:04:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRDesoneracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterDesoneracao";
$pgList = "LSProrrogarDesoneracao.php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write( "link", "" );

$obRARRDesoneracao = new RARRDesoneracao;
$obRARRDesoneracao->listarTipoDesoneracao( $rsTipo );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( '' );

$obBscDesoneracao = new BuscaInner;
$obBscDesoneracao->setTitle            ( "Informe a desoneração." );
$obBscDesoneracao->setRotulo           ( "Desoneração"     );
$obBscDesoneracao->setId               ( "stDesoneracao"   );
$obBscDesoneracao->obCampoCod->setName ( "inCodigoDesoneracao" );
$obBscDesoneracao->obCampoCod->setValue( $inCodigoDesoneracao  );
$obBscDesoneracao->setNull ( true );
$obBscDesoneracao->obCampoCod->setSize (  9                );
$obBscDesoneracao->obCampoCod->obEvento->setOnChange( "buscaValor('buscaDesoneracao');" );
$obBscDesoneracao->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."desoneracao/FLProcurarDesoneracao.php','frm','inCodigoDesoneracao','stDesoneracao','','".Sessao::getId()."','800','550');");

$obBscCGM = new BuscaInner;
$obBscCGM->setTitle ( "CGM do contribuinte." );
$obBscCGM->setRotulo( "CGM" );
$obBscCGM->setId( "stNomCGM" );
$obBscCGM->obCampoCod->setName("inNumCGM");
$obBscCGM->obCampoCod->setValue( $inNumCGM );
$obBscCGM->setNull ( true );
$obBscCGM->obCampoCod->obEvento->setOnChange("buscaValor('buscaCGM');");
$obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','stNomCGM','geral','".Sessao::getId()."','800','550');" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList  );
$obForm->setTarget( "telaPrincipal" );

$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addTitulo ( "Dados para Filtro" );
$obFormulario->addComponente( $obBscDesoneracao );
$obFormulario->addComponente( $obBscCGM );
$obFormulario->OK();
$obFormulario->Show();
