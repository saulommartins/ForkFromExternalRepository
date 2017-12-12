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
* Página de Formulario de Inclusao/Alteracao de Entidade
* Data de Criação   : 26/11/2004

* @author Analista: ???
* @author Desenvolvedor: Rafael Almeida

* @ignore

$Revision: 31488 $
$Name$
$Author: souzadl $
$Date: 2008-03-27 08:51:17 -0300 (Qui, 27 Mar 2008) $

* Casos de uso: uc-04.05.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO.'RFolhaPagamentoSindicato.class.php'                                );
include_once ( CAM_GRH_FOL_NEGOCIO.'RFolhaPagamentoConfiguracao.class.php'                             );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );

//Define o nome dos arquivos PHP
$stPrograma = "ManterSindicato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

include_once ($pgJS);
include_once ($pgOcul);

$obRPessoalSindicato = new RFolhaPagamentoSindicato;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}
if ($stAcao == 'alterar' or $stAcao == 'consultar') {
    $stNomeCgm = $_REQUEST['stNomCGM'];
    $obRPessoalSindicato->obRCGM->setNumCGM( $_REQUEST['inNumCGM']);
    $obRPessoalSindicato->consultar( $boTransacao );
    $inNumCGM    = $obRPessoalSindicato->obRCGM->getNumCGM();
    $inCodEvento = $obRPessoalSindicato->obRFolhaPagamentoEvento->getCodigo();

    $obLblFuncao = new Label;
    $obLblFuncao->setRotulo( "Evento" );
    $obLblFuncao->setValue( $inCodEvento ." - ". $obRPessoalSindicato->obRFolhaPagamentoEvento->getDescricao() );

    $inDataBase  = $obRPessoalSindicato->getDataBase();
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
if ($stAcao == 'consultar') {
    $pgProc = $pgList;
}

$obForm = new Form;
$obForm->setAction( $pgProc );
if( $stAcao != "consultar" ) $obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define o objeto INNER para armazenar o ENTIDADE

    $obBscSindicato = new BuscaInner;
    $obBscSindicato->setRotulo           ( "Sindicato"          );
    $obBscSindicato->setTitle            ( "CGM do Sindicato" );
    $obBscSindicato->setNull             ( false );
    $obBscSindicato->setValue            ( $stNomeCgm );
    $obBscSindicato->setId               ( "campoInner" );
    $obBscSindicato->obCampoCod->setName ( "inNumCGM"   );
    $obBscSindicato->obCampoCod->setValue( $inNumCGM    );
    $obBscSindicato->obCampoCod->obEvento->setOnChange("buscaCGM('buscaCGM',this);");
    $obBscSindicato->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','campoInner','juridica','".Sessao::getId()."','800','550')" );

$obLblSindicato = new Label;
$obLblSindicato->setRotulo( "Sindicato" );
$obLblSindicato->setValue( $inNumCGM ." - ". $stNomeCgm );

$obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
$obRFolhaPagamentoConfiguracao->consultar();
$stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

$obInEvento = new BuscaInner;
$obInEvento->setRotulo                        ( 'Evento de Desconto de Imposto Sindical'                                    );
$obInEvento->setTitle                         ( "Informe o Evento de desconto de previdência que será utilizado no cálculo." );
$obInEvento->setId                            ( 'stEvento'                                                                   );
$obInEvento->setNull                          ( false                                                                        );
$obInEvento->obCampoCod->setName              ( 'inCodEvento'                                                                );
$obInEvento->obCampoCod->setValue             ( $inCodEvento                                                                 );
$obInEvento->obCampoCod->setAlign             ( "LEFT"                                                                       );
$obInEvento->obCampoCod->setMascara           ( $stMascaraEvento                                                             );
$obInEvento->obCampoCod->setPreencheComZeros  ( "E"                                                                          );
$obInEvento->obCampoCod->obEvento->setOnChange( "preencherEvento ( inCodEvento.value , 'D');"                                );
$obInEvento->setFuncaoBusca                   ( "abrePopUp('" .CAM_GRH_FOL_POPUPS. "previdencia/FLManterPrevidencia.php','frm','inCodEvento','stEvento', '' ,'".Sessao::getId()."&stNatureza=D','800','550');" );

$obTxtDataBase = new TextBox;
$obTxtDataBase->setRotulo     ( "Data-base" );
$obTxtDataBase->setName       ( "inDataBase" );
$obTxtDataBase->setValue      ( $inDataBase  );
$obTxtDataBase->setTitle      ( "Informe a data-base para o desconto" );
$obTxtDataBase->setNull       ( false );
$obTxtDataBase->setInteiro    ( true );
$obTxtDataBase->setMaxLength  ( 2    );
$obTxtDataBase->obEvento->setOnBlur ("validaDataBase(this);");

$obLblDataBase = new Label;
$obLblDataBase->setRotulo( "Data-base" );
$obLblDataBase->setValue( $inDataBase );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );

$obFormulario->addHidden            ( $obHdnCtrl                );
$obFormulario->addHidden            ( $obHdnAcao                );
$obFormulario->addTitulo            ( "Dados do sindicato"     );

if ($stAcao == 'consultar') {
    $obFormulario->addComponente        ( $obLblSindicato   );
    $obFormulario->addComponente        ( $obLblFuncao      );
    $obFormulario->addComponente        ( $obLblDataBase    );
} else {
    $obFormulario->addComponente        ( $obBscSindicato   );
    $obFormulario->addComponente        ( $obInEvento       );
    $obFormulario->addComponente        ( $obTxtDataBase    );
}

if ($stAcao == "incluir") {
    $obFormulario->OK();
} elseif ($stAcao == "alterar") {
    $obFormulario->Cancelar();
} else {
    $obFormulario->Voltar();

}

$obFormulario->show();

if ($stAcao == "incluir") {
    $js .= "focusIncluir();";
    sistemaLegado::executaFrameOculto($js);
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

if ($busca) {
    sistemaLegado::executaFrameOculto($busca);
}

?>
