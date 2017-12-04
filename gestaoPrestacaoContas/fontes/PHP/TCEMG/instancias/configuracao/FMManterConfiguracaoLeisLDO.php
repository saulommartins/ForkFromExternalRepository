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
    * Página de Formulario de Configuração de Leis do LDO
  * Data de Criação: 15/01/2014

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
$stPrograma = "ManterConfiguracaoLeisLDO";
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

$rsTTCEMGConfiguracaoLeisLDO = new RecordSet();

include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoLeisLDO.class.php");
$obTTCEMGConfiguracaoLeisLDO = new TTCEMGConfiguracaoLeisLDO();
$stFiltro  = " AND tipo_configuracao = 'alteracao' ";
$stFiltro .= " AND status = true ";
$obTTCEMGConfiguracaoLeisLDO->recuperaRelacionamento($rsTTCEMGConfiguracaoLeisLDO,$stFiltro);

while (!$rsTTCEMGConfiguracaoLeisLDO->eof()) {
    $arNorma['inCodNorma'] = $rsTTCEMGConfiguracaoLeisLDO->getCampo("cod_norma");
    $arNorma['inCodTipoNorma'] = $rsTTCEMGConfiguracaoLeisLDO->getCampo("cod_tipo_norma");

    $arNormas[] = $arNorma;

    $rsTTCEMGConfiguracaoLeisLDO->proximo();
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

### Lei do LDO ###
$obIPopUpLeiLDO = new IPopUpNorma();
$obIPopUpLeiLDO->obInnerNorma->setId('stNomeLeiLDO');
$obIPopUpLeiLDO->obInnerNorma->obCampoCod->stId = 'inCodLeiLDO';
$obIPopUpLeiLDO->obInnerNorma->obCampoCod->setName( "inCodLeiLDO" );
$obIPopUpLeiLDO->obInnerNorma->setRotulo("Lei do LDO");

### Leis de Alteracao do LDO ###
$obIPopUpLeiAlteracaoLDO = new IBuscaInnerNorma(false,true);
$obIPopUpLeiAlteracaoLDO->obBscNorma->setRotulo('Lei Alteração LDO');

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
$obFormulario->addTitulo     ( "Dados para Configuração de Leis do LDO" );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );

$obIPopUpLeiLDO->geraFormulario($obFormulario);
$obFormulario->addTitulo     ( "Dados para Configuração de Leis de Alteração do LDO" );
$obIPopUpLeiAlteracaoLDO->geraFormulario($obFormulario);
$obFormulario->addComponente($obBtnIncluirNorma);
$obFormulario->addSpan($obSpnNormasFundamentacaoLegal);
$obFormulario->OK();
$obFormulario->show();

processarForm(true,"Form",$stAcao);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
