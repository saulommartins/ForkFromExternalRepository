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
  * $Id: FMManterConfiguracaoLeisPPA.php 61668 2015-02-24 13:48:38Z michel $

  * $Revision: 61668 $
  * $Name: $
  * $Author: michel $
  * $Date: 2015-02-24 10:48:38 -0300 (Tue, 24 Feb 2015) $

*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';
include_once CAM_GA_NORMAS_COMPONENTES.'IBuscaInnerNorma.class.php';
include_once CAM_GA_CGM_COMPONENTES.'IPopUpCGMVinculado.class.php';

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

if ($request->get('stAcao') == '' || $request->get('stAcao') == 'manter') {
    $stAcao = 'incluir';
} else {
    $stAcao = $request->get('stAcao');
}

$rsTTCMGOConfiguracaoLeisPPA = new RecordSet();

include_once(CAM_GPC_TGO_MAPEAMENTO."TTCMGOConfiguracaoLeisPPA.class.php");
$obTTCMGOConfiguracaoLeisPPA = new TTCMGOConfiguracaoLeisPPA();
$stFiltro  = " AND tipo_configuracao = 'alteracao' ";
$stFiltro .= " AND status = true ";
$obTTCMGOConfiguracaoLeisPPA->recuperaRelacionamento($rsTTCMGOConfiguracaoLeisPPA,$stFiltro);

while (!$rsTTCMGOConfiguracaoLeisPPA->eof()) {
    $arNorma['inCodNorma'] = $rsTTCMGOConfiguracaoLeisPPA->getCampo("cod_norma");
    $arNorma['inCodTipoNorma'] = $rsTTCMGOConfiguracaoLeisPPA->getCampo("cod_tipo_norma");

    $arNormas[] = $arNorma;

    $rsTTCMGOConfiguracaoLeisPPA->proximo();
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
$obLblLeiPPA = new Label;
$obLblLeiPPA->setRotulo      ( "Lei do PPA" );
$obLblLeiPPA->setId          ( "stNomeLeiPPA" );
$obLblLeiPPA->setName        ( "stNomeLeiPPA" );

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

//Lista de Publicidade TCM
$arPubLei = array();
$arPubLei[0]['cod_publicidade'] = 1;
$arPubLei[0]['descricao'] = 'Diário Oficial do Estado';
$arPubLei[1]['cod_publicidade'] = 2;
$arPubLei[1]['descricao'] = 'Diário Oficial do Município';
$arPubLei[2]['cod_publicidade'] = 3;
$arPubLei[2]['descricao'] = 'Placar da Prefeitura ou da Câmara Municipal';
$arPubLei[3]['cod_publicidade'] = 4;
$arPubLei[3]['descricao'] = 'Jornal de grande circulação';
$arPubLei[4]['cod_publicidade'] = 5;
$arPubLei[4]['descricao'] = 'Diário Oficial da União';
$arPubLei[5]['cod_publicidade'] = 9;
$arPubLei[5]['descricao'] = 'Endereço eletrônico completo (Internet)';

$rsPubLei = new RecordSet();
$rsPubLei->preenche($arPubLei);

//Painel veiculos de publicidade 
$obCmbPubLeiAlteracao = new Select;
$obCmbPubLeiAlteracao->setName      ( "codPubLeiAlteracao"          );
$obCmbPubLeiAlteracao->setRotulo    ( "*Veículo de Publicação"      );
$obCmbPubLeiAlteracao->setId        ( "codPubLeiAlteracao"          );
$obCmbPubLeiAlteracao->setCampoId   ( "cod_publicidade"             );
$obCmbPubLeiAlteracao->setCampoDesc ( "descricao"                   );
$obCmbPubLeiAlteracao->addOption    ( '','Selecione'                );
$obCmbPubLeiAlteracao->preencheCombo( $rsPubLei                     );
$obCmbPubLeiAlteracao->setNull      ( true                          );
$obCmbPubLeiAlteracao->setValue     ( ''                            );
$obCmbPubLeiAlteracao->obEvento->setOnChange  ("montaParametrosGET('carregaLeiAlteracao','codPubLeiAlteracao');");


$obSpnPubLeiAlteracao = new Span();
$obSpnPubLeiAlteracao->setId("spnPubLeiAlteracao");

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addTitulo     ( "Dados para Configuração de Leis do PPA" );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );

$obFormulario->addComponente    ( $obLblLeiPPA  );
$obFormulario->addTitulo        ( "Dados para Configuração de Leis de Alteração do PPA" );
$obIPopUpLeiAlteracaoPPA->geraFormulario($obFormulario              );
$obFormulario->addComponente    ( $obCmbPubLeiAlteracao             );
$obFormulario->addSpan          ($obSpnPubLeiAlteracao              );
$obFormulario->addComponente    ($obBtnIncluirNorma                 );
$obFormulario->addSpan          ($obSpnNormasFundamentacaoLegal     );
$obFormulario->OK();
$obFormulario->show();

processarForm(true,"Form",$stAcao);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
