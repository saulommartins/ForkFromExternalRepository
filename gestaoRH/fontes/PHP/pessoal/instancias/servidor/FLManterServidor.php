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
* Página de Formulario de filtro de servidor
* Data de Criação   : 26/01/2005

* @author Analista: ???
* @author Desenvolvedor: Rafael Almeida

* @ignore

$Revision: 30857 $
$Name$
$Author: alex $
$Date: 2007-12-13 11:24:00 -0200 (Qui, 13 Dez 2007) $

* Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php";
include_once CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";


//Define o nome dos arquivos PHP
$stPrograma = "ManterServidor";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";


Sessao::write("stOrigem","FL");

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
} else {
    if ( strpos($stAcao,"_") ) {
        $arAcao = explode("_",$stAcao);
        $stAcao = $arAcao[0];
        $inAba  = $arAcao[1];
    }
}

$obRConfiguracaoPessoal = new RConfiguracaoPessoal;
$obRConfiguracaoPessoal->Consultar();
$stMascaraRegistro = $obRConfiguracaoPessoal->getMascaraRegistro();
Sessao::remove("link");

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao );

$obHdnAba =  new Hidden;
$obHdnAba->setName   ( "inAba" );
$obHdnAba->setValue  ( isset($inAba) ? $inAba : "" );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( ""       );

$obCkbSituacao1 = new Checkbox();
$obCkbSituacao1->setRotulo("Situação");
$obCkbSituacao1->setName("stSituacao1");
$obCkbSituacao1->setTitle("Marque as opções de cadastro dos servidores para configuração: Ativos, Rescindidos, Afastados.");
$obCkbSituacao1->setValue("ativo");
$obCkbSituacao1->setLabel("Ativos");

$obCkbSituacao2 = new Checkbox();
$obCkbSituacao2->setRotulo("Situação");
$obCkbSituacao2->setName("stSituacao2");
$obCkbSituacao2->setTitle("Marque as opções de cadastro dos servidores para configuração: Ativos, Rescindidos, Afastados.");
$obCkbSituacao2->setValue("rescindidos");
$obCkbSituacao2->setLabel("Rescindidos");

$obCkbSituacao3 = new Checkbox();
$obCkbSituacao3->setRotulo("Situação");
$obCkbSituacao3->setName("stSituacao3");
$obCkbSituacao3->setTitle("Marque as opções de cadastro dos servidores para configuração: Ativos, Rescindidos, Afastados.");
$obCkbSituacao3->setValue("afastados");
$obCkbSituacao3->setLabel("Afastados");

//Para caso ainda não tenha periodo movimentação cadastrado
$obLblMensagem = new Label;
$obLblMensagem->setName   ( "stMensagem" );
$obLblMensagem->setRotulo ( "Mensagem" );
$obLblMensagem->setValue  ( "Necessário criar o primeiro período de movimentação em <b>Gestão Recursos Humanos :: Folha de Pagamento :: Rotina Mensal :: Abrir Período de Movimentação</b>" );

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo           ( "CGM" );
$obBscCGM->setTitle            ( "Informe o CGM do servidor.");
if( $stAcao == 'incluir' )
$obBscCGM->setNull             ( false );
$obBscCGM->setId               ( "inNomCGM" );
$obBscCGM->obCampoCod->setName ( "inNumCGM" );
$obBscCGM->obCampoCod->setId   ( "inNumCGM" );
$obBscCGM->obCampoCod->setValue( isset($inNumCGM) ? $inNumCGM : "" );
$obBscCGM->obCampoCod->obEvento->setOnChange("buscaValor('buscaCGM')" );

$obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarCgm.php','frm','inNumCGM','inNomCGM','fisica','".Sessao::getId()."','800','550')" );

if ($stAcao == 'alterar' || $stAcao == 'excluir') {
    Sessao::write('stTipoListagem','geral');
    Sessao::write('stAcaoFormulario',$stAcao);
}

$obIContratoDigitoVerificador = new IContratoDigitoVerificador("", true);
$obIContratoDigitoVerificador->setPagFiltro(true);

//DEFINICAO DO FORM
$obForm = new Form;
if ($stAcao == "incluir") {
    $obForm->setAction( $pgForm );
} else {
    $obForm->setAction( $pgList );
}

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm  ( $obForm );
$obFormulario->addTitulo( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addTitulo( "Filtro para Servidor" );

$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao;
$obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsFolhaPagamentoPeriodoMovimentacao);

if ($rsFolhaPagamentoPeriodoMovimentacao->getNumLinhas() < 1) {
    $obFormulario->addComponente( $obLblMensagem );
    
} else {
    $obFormulario->addHidden    ( $obHdnAcao );
    $obFormulario->addHidden    ( $obHdnAba  );
    $obFormulario->addHidden    ( $obHdnCtrl );
    if ($stAcao == 'alterar') {
        $arSituacao = array($obCkbSituacao1,$obCkbSituacao2,$obCkbSituacao3);
        $obFormulario->agrupaComponentes($arSituacao);
    }
    $obFormulario->addComponente( $obBscCGM  );
    if ($stAcao != 'incluir') {
        $obIContratoDigitoVerificador->geraFormulario( $obFormulario );
    }
    
    $obFormulario->ok();
    $obFormulario->setFormFocus( $obBscCGM->obCampoCod->getId() );
}

$obFormulario->show();

include_once( $pgJS );