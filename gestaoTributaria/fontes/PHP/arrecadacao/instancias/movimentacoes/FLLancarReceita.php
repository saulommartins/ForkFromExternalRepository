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
  * Página de Filtro Lançar Receita
  * Data de criação : 17/06/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @ignore

    * $Id: FLLancarReceita.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.06
**/

/*
$Log$
Revision 1.6  2006/09/15 11:14:47  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRAvaliacaoEconomica.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "LancarReceita";
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

$obRARRAvaliacaoEconomica = new RARRAvaliacaoEconomica;
$obMontaLocalizacao       = new MontaLocalizacao;
$obMontaLocalizacao->setObrigatorio( false );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( '' );

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo( "Contribuinte" );
$obBscCGM->setTitle( "Informe o CGM do proprietário da empresa." );
$obBscCGM->setId( "stNomCGM" );
$obBscCGM->obCampoCod->setName("inNumCGM");
$obBscCGM->obCampoCod->setValue( $_REQUEST["inNumCGM"] );
$obBscCGM->obCampoCod->obEvento->setOnChange("buscaValor('buscaCGM');");
$obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','stNomCGM','geral','".Sessao::getId()."','800','550');" );

$obBscInscricaoEconomica = new BuscaInner;
$obBscInscricaoEconomica->setTitle ( "Informe a inscrição econômica da empresa." );
$obBscInscricaoEconomica->setRotulo( "Inscrição Econômica" );
$obBscInscricaoEconomica->obCampoCod->setName( "inInscricaoEconomica" );
$obBscInscricaoEconomica->obCampoCod->setId  ( "inInscricaoEconomica" );
$obBscInscricaoEconomica->setFuncaoBusca("abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inInscricaoEconomica','stCampo','todos','".Sessao::getId()."','800','550');");

$obBscAtividade = new BuscaInner;
$obBscAtividade->setTitle ( "Informe a atividade exercida pela empresa." );
$obBscAtividade->setRotulo( "Atividade" );
$obBscAtividade->obCampoCod->setName( "inCodigoAtividade" );
$obBscAtividade->obCampoCod->setId  ( "inCodigoAtividade" );
$obBscAtividade->obCampoCod->obEvento->setOnChange("buscaValor('buscaAtividade');");
$obBscAtividade->setId ( "stAtividade" );
$obBscAtividade->setNull( false );
$obBscAtividade->setFuncaoBusca("abrePopUp('".CAM_GT_CEM_POPUPS."atividadeeconomica/FLProcurarAtividade.php','frm','inCodigoAtividade','stAtividade','todos','".Sessao::getId()."','800','550');");

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
$obFormulario->addComponente ( $obBscCGM );
$obFormulario->addComponente ( $obBscInscricaoEconomica );
$obFormulario->addComponente ( $obBscAtividade );
$obMontaLocalizacao->geraFormulario( $obFormulario );
$obFormulario->defineBarra( array( $obBtnOK, $obBtnLimpar ) );
$obFormulario->Show();
