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
    * Página de Formulario de Configuração de Leis do PPA
  * Data de Criação: 14/01/2014

  * @author Analista: Eduardo Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore
  *
  * $Id: $

  * $Revision: $
  * $Name: $
  * $Author: $
  * $Date: $

*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';
include_once(CAM_GA_NORMAS_COMPONENTES."IBuscaInnerNorma.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoLeisPPA";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once ($pgJs);
include_once ($pgOcul);

if ($request->get('stAcao') == '') {
    $stAcao = 'incluir';
} else {
    $stAcao = $request->get('stAcao');
}

$rsTTCEMGConfiguracaoLeisPPA = new RecordSet();

include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoLeisPPA.class.php");
$obTTCEMGConfiguracaoLeisPPA = new TTCEMGConfiguracaoLeisPPA();
$stFiltro  = " AND tipo_configuracao = 'alteracao' ";
$stFiltro .= " AND status = true ";
$obTTCEMGConfiguracaoLeisPPA->recuperaRelacionamento($rsTTCEMGConfiguracaoLeisPPA,$stFiltro);

while (!$rsTTCEMGConfiguracaoLeisPPA->eof()) {
    $arNorma['inCodNorma'] = $rsTTCEMGConfiguracaoLeisPPA->getCampo("cod_norma");
    $arNorma['inCodTipoNorma'] = $rsTTCEMGConfiguracaoLeisPPA->getCampo("cod_tipo_norma");

    $arNormas[] = $arNorma;

    $rsTTCEMGConfiguracaoLeisPPA->proximo();
}

Sessao::write("arCodNorma", $arNormas);

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "stCtrl" );

### Lei do PPA ###
$obIPopUpLeiPPA = new IPopUpNorma();
$obIPopUpLeiPPA->obInnerNorma->setId('stNomeLeiPPA');
$obIPopUpLeiPPA->obInnerNorma->obCampoCod->stId = 'inCodLeiPPA';
$obIPopUpLeiPPA->obInnerNorma->obCampoCod->setName( "inCodLeiPPA" );
$obIPopUpLeiPPA->obInnerNorma->setRotulo("Lei do PPA");

### Leis de Alteracao do PPA ###
$obIPopUpLeiAlteracaoPPA = new IBuscaInnerNorma(false,true);
$obIPopUpLeiAlteracaoPPA->obBscNorma->setRotulo('Lei Alteração PPA');

$obSpnNormasFundamentacaoLegal = new Span();
$obSpnNormasFundamentacaoLegal->setId("spnFundamentacaoLegal");

$obBtnIncluirNorma = new Button;
$obBtnIncluirNorma->setName             ( "btIncluirNorma"                                                       );
$obBtnIncluirNorma->setId               ( "btIncluirNorma"                                                       );
$obBtnIncluirNorma->setValue            ( "Incluir"                                                              );
$obBtnIncluirNorma->obEvento->setOnClick( "buscaValor('incluirNorma');"                                          );
$obBtnIncluirNorma->setTitle            ( "Clique para incluir a norma na lista de Normas/Fundamentação Legal"   );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addTitulo     ( "Dados para Configuração de Leis do PPA" );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );

$obIPopUpLeiPPA->geraFormulario($obFormulario);
$obFormulario->addTitulo     ( "Dados para Configuração de Leis de Alteração do PPA" );
$obIPopUpLeiAlteracaoPPA->geraFormulario($obFormulario);
$obFormulario->addComponente($obBtnIncluirNorma);
$obFormulario->addSpan($obSpnNormasFundamentacaoLegal);
$obFormulario->OK();
$obFormulario->show();

processarForm(true,"Form",$stAcao);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
