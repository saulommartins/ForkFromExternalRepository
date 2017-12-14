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
    * Página de Filtro para Relatório do Cadastro Economico
    * Data de Criação   : 05/04/2006

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: FLCadastroEconomico.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.17
*/

/*
$Log$
Revision 1.7  2007/03/02 12:58:27  rodrigo
Bug #8042#

Revision 1.6  2007/02/27 11:48:54  rodrigo
Bug #8042#

Revision 1.5  2007/01/11 10:23:07  dibueno
Bug #8042#

Revision 1.4  2006/09/15 14:33:30  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresaIntervalo.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."ITextLicencaIntervalo.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtividade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "CadastroEconomico";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";
$pgRel = CAM_FW_POPUPS."relatorio/OCRelatorio.php";

if (!isset($sessao)) {
    $sessao = new stdClass;
}

$sessao->filtro = null;

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $request->get("stCtrl")  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $request->get("stAcao")  );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_CEM_INSTANCIAS."relatorios/OCCadastroEconomico.php" );

$obHdnTipoRelatorio = new Hidden;
$obHdnTipoRelatorio->setName("stTipoRelatorioSubmit");

// CONSULTA CONFIGURACAO DO MODULO ECONOMICO
$obRCEMConfiguracao = new RCEMConfiguracao;
$obRCEMConfiguracao->setCodigoModulo( 14 );
$obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCEMConfiguracao->consultarConfiguracao();
$stMascaraInscricaoEconomico = $obRCEMConfiguracao->getMascaraInscricao();

$obTCEMAtividade = new TCEMAtividade;
$obTCEMAtividade->recuperaMaxCodEstrutural( $rsListaEstrutura );

$arEstrutura = explode( ".", $rsListaEstrutura->getCampo("cod_estrutural") );
$stEstrutura = "";
for ( $inX=0; $inX<count($arEstrutura); $inX++ ) {
    if ( $inX )
        $stEstrutura .= ".";

    for ( $inY=0; $inY<strlen($arEstrutura[$inX]); $inY++ ) {
        $stEstrutura .= "9";
    }
}

$obCodInicio = new TextBox;
$obCodInicio->setName  ( "inCodInicio" );
$obCodInicio->setRotulo( "Atividade" );
$obCodInicio->setTitle ( "Informe um período" ) ;
$obCodInicio->setMascara( $stEstrutura );

$obLblPeriodo = new Label;
$obLblPeriodo->setValue( " até " );

$obCodTermino = new TextBox;
$obCodTermino->setName     ( "inCodTermino" );
$obCodTermino->setRotulo   ( "Atividade" );
$obCodTermino->setTitle    ( "Informe um período" );
$obCodTermino->setMascara( $stEstrutura );

$obBscSocio = new BuscaInner;
$obBscSocio->setRotulo           ( "Sócio"        );
$obBscSocio->setId               ( "stNomeSocio"  );
$obBscSocio->obCampoCod->setName ("inCodSocio" );
$obBscSocio->obCampoCod->setValue( $request->get("inCodigoSocio") );
$obBscSocio->setFuncaoBusca      ( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodigoSocio','stNomeSocio','todos','".Sessao::getId()."','800','550');" );
$obBscSocio->obCampoCod->obEvento->setOnChange( "buscaValor('buscaSocio');" );

/* LISTAS */

$obCmbTipoEmpresa = new Select;
$obCmbTipoEmpresa->setName      ( "stTipoInscricao"             );
$obCmbTipoEmpresa->setRotulo    ( "Tipo da Inscrição"           );
$obCmbTipoEmpresa->setTitle     ( "Tipo da Inscrição Econômica" );
$obCmbTipoEmpresa->addOption    ( ""          , "Selecione"     );
$obCmbTipoEmpresa->addOption    ( "fato" , "Fato"               );
$obCmbTipoEmpresa->addOption    ( "direito" , "Direito"         );
$obCmbTipoEmpresa->addOption    ( "autonomo" , "Autônoma"       );
$obCmbTipoEmpresa->setNull      ( true                          );
$obCmbTipoEmpresa->setStyle     ( "width: 200px"                );

$obDtInicio = new Data;
$obDtInicio->setName      ( "dtInicio"       );
$obDtInicio->setValue     ( $_REQUEST["dtInicio"] );
$obDtInicio->setRotulo    ( "Data de Inicio" );
$obDtInicio->setNull      ( true               );

$obCmbTipo = new Select;
$obCmbTipo->setName      ( "stTipoRelatorio"                );
$obCmbTipo->setRotulo    ( "Tipo de Relatório"              );
$obCmbTipo->setTitle     ( "Selecione o tipo de relatório"  );
$obCmbTipo->addOption    ( ""          , "Selecione"        );
$obCmbTipo->addOption    ( "analitico" , "Analítico"        );
$obCmbTipo->addOption    ( "sintetico" , "Sintético"        );
$obCmbTipo->setCampoDesc ( "stTipo"                         );
$obCmbTipo->setNull      ( false                            );
$obCmbTipo->setStyle     ( "width: 200px"                   );

$obCodLogradouroInicio = new TextBox;
$obCodLogradouroInicio->setName  ( "inCodInicioLogradouro" );
$obCodLogradouroInicio->setInteiro( true );
$obCodLogradouroInicio->setRotulo( "Código do Logradouro" );
$obCodLogradouroInicio->setTitle ( "Informe um período" ) ;

$obCodLogradouroTermino = new TextBox;
$obCodLogradouroTermino->setName     ( "inCodTerminoLogradouro" );
$obCodLogradouroTermino->setInteiro  ( true );
$obCodLogradouroTermino->setRotulo   ( "Código do Logradouro" );
$obCodLogradouroTermino->setTitle    ( "Informe um período" );

$obBtnOK = new OK;
//$obBtnOK->obEvento->setOnClick( "if (Valida()) {AtualizaTipoRelatorio();}" );
$obBtnOK->obEvento->setOnClick( "Valida()" );
$onBtnLimpar = new Limpar;

//****************************************//
//Monta FORMULARIO
//****************************************//
$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo    ( "Dados para filtro"   );

$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnTipoRelatorio );
$obFormulario->addHidden ( $obHdnCaminho );

$IPopUpEmpresaIntervalo = new IPopUpEmpresaIntervalo;
$IPopUpEmpresaIntervalo->setVerificaInscricao ( false );
$IPopUpEmpresaIntervalo->geraFormulario( $obFormulario );

$obFormulario->agrupaComponentes( array( $obCodInicio, $obLblPeriodo ,$obCodTermino) );
$obFormulario->agrupaComponentes( array( $obCodLogradouroInicio, $obLblPeriodo, $obCodLogradouroTermino ) );
$obITextLicencaIntervalo = new ITextLicencaIntervalo;
$obITextLicencaIntervalo->setNULL( true );
$obITextLicencaIntervalo->geraFormulario ( $obFormulario );

$obFormulario->addComponente ( $obBscSocio );
$obFormulario->addComponente ( $obCmbTipoEmpresa );
$obFormulario->addComponente ( $obDtInicio );
$obFormulario->addComponente ( $obCmbTipo );

$obRdbAtivo = new Radio();
$obRdbAtivo->setName                            ( "stSituacao"                                   );
$obRdbAtivo->setRotulo                          ( "Situação"                                     );
$obRdbAtivo->setLabel                           ( "Ativos"                                       );
$obRdbAtivo->setTitle                           ( "Selecione a situação do cadastro da empresa." );
$obRdbAtivo->setValue                           ( "Ativo"                                        );
$obRdbAtivo->setChecked                         ( true                                           );

$obRdbBaixado = new Radio();
$obRdbBaixado->setName                          ( "stSituacao"                                   );
$obRdbBaixado->setLabel                         ( "Baixados"                                     );
$obRdbBaixado->setValue                         ( "Baixado"                                      );

$obRdbTodos = new Radio();
$obRdbTodos->setName                            ( "stSituacao"                                   );
$obRdbTodos->setLabel                           ( "Todos"                                        );
$obRdbTodos->setValue                           ( "Todos"                                        );

$obFormulario->agrupaComponentes                ( array($obRdbAtivo,$obRdbBaixado,$obRdbTodos ) );

$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );

//$obFormulario->OK();
$obFormulario->show();

include_once( $pgJs );
//sistemaLegado::executaFrameOculto ( $stJs );
?>
