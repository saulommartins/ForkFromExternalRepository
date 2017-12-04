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
    * Página de Formulario de Alteração de Natureza Jurídica para uma Inscrição Econômica
    * Data de Criação   : 30/12/2004

    * @author  Tonismar Régis Bernardo

    * @ignore

    * $Id: FMManterInscricaoNaturezaAlteracao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.10  2006/09/15 14:33:07  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeDireito.class.php"   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNaturezaJuridica.class.php"   );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma = "ManterInscricao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgNatAlt = "FM".$stPrograma."NaturezaAlteracao.php";
$pgSocAlt = "FM".$stPrograma."SociedadeAlteracao.php";
$pgAtvAlt = "FM".$stPrograma."AtividadeAlteracao.php";
$pgDomAlt = "FM".$stPrograma."DomicilioAlteracao.php";
$pgEleAlt = "FM".$stPrograma."ElementosAlteracao.php";
$pgHorAlt = "FM".$stPrograma."HorarioAlteracao.php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRCEMInscricaoEconomica = new RCEMEmpresaDeDireito;

if ($_REQUEST[ 'inInscricaoEconomica' ]) {
    $obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST[ 'inInscricaoEconomica' ] );
}

$obRCEMInscricaoEconomica->listarEmpresaDireitoNatureza( $rsInscricao );

$inCodigoNaturezaAtual = $rsInscricao->getCampo( "cod_natureza" );
$stNomeNatureza        = $rsInscricao->getCampo( "nom_natureza" );

$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( $stCtrl );

$obHdnInscricaoEconomica = new Hidden;
$obHdnInscricaoEconomica->setName ( 'inInscricaoEconomica' );
$obHdnInscricaoEconomica->setValue( $_REQUEST[ 'inInscricaoEconomica' ] );

$obLblNatureza = new Label;
$obLblNatureza->setRotulo ( "Natureza Jurídica Atual"  );
$obLblNatureza->setValue  ( $inCodigoNaturezaAtual."  ".$stNomeNatureza );

$stMascaraNatureza = "999-9";
$obBscNatureza = new BuscaInner;
$obBscNatureza->setRotulo( "*Natureza Jurídica" );
$obBscNatureza->setId( "stNomeNatureza" );
$obBscNatureza->obCampoCod->setName("inCodigoNatureza");
$obBscNatureza->obCampoCod->setValue( $inCodigoNatureza );
$obBscNatureza->obCampoCod->setMascara( $stMascaraNatureza);
$obBscNatureza->obCampoCod->setMaxLength( strlen($stMascaraNatureza));
$obBscNatureza->obCampoCod->setMinLength( strlen($stMascaraNatureza));
$obBscNatureza->obCampoCod->obEvento->setOnChange("buscaValor('buscaNatureza');");
$obBscNatureza->setFuncaoBusca( "abrePopUp('".CAM_GT_CEM_POPUPS."naturezajuridica/FLProcurarNaturezaJuridica.php','frm','inCodigoNatureza','stNomeNatureza','todos','".Sessao::getId()."','800','550');" );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle ( "Processo do protocolo referente à alteração de natureza jurídica de inscrição econômica" );
$obBscProcesso->setNull ( true );
$obBscProcesso->obCampoCod->setName ("inNumProcesso");
$obBscProcesso->obCampoCod->setId   ("inNumProcesso");
$obBscProcesso->obCampoCod->setValue( $inNumProcesso );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('99999/9999', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inNumProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.02.10");
$obFormulario->addTitulo     ( "Dados para Inscrição Econômica" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnInscricaoEconomica );
$obFormulario->addComponente ( $obLblNatureza );
$obFormulario->addComponente ( $obBscNatureza );
$obFormulario->addComponente ( $obBscProcesso );
$obFormulario->Ok();
$obFormulario->show();
