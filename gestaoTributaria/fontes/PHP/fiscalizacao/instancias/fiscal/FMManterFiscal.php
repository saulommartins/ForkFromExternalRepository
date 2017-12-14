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
    * Página de Formulario de Inclusao/Alteracao de Fiscal

    * Data de Criação   : 17/07/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: FMManterFiscal.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.07.02
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_NORMAS_CLASSES."componentes/IPopUpNorma.class.php"                               );
//include_once( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                     );
include_once( CAM_GT_FIS_COMPONENTES."ITextBoxSelectTipoFiscalizacao.class.php"                       );
include_once( CAM_GT_FIS_MAPEAMENTO."TFISFiscal.class.php"                                            );
include_once( CAM_GT_FIS_COMPONENTES."IFiltroContratoAposentadoPensionista.class.php"                );

$stAcao = $request->get('stAcao');
Sessao::write( 'arValores', array() );
if ( empty( $stAcao ) ) { $stAcao = "incluir"; }

//echo Sessao::getEntidade()," entidade";
//Define o nome dos arquivos PHP
$stPrograma = "ManterFiscal";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$pgJs       = "JS".$stPrograma.".php";

include_once( $pgJs );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc               );
$obForm->settarget ( "oculto"              );
$obForm->setEncType( "multipart/form-data" );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl  );

if ($stAcao=="incluir") {
    $obIFiltroMatricula = new IFiltroContratoAposentadoPensionista();
    $obIFiltroMatricula->obLblCGM->setRotulo( "Servidor" );
    $obIFiltroMatricula->obIContratoDigitoVerificador->setNull( false );
    $obIFiltroMatricula->setInformacoesFuncao(true);
    $obIFiltroMatricula->setTituloFormulario("");
} else {
    $obLblServidor = new Label;
    $obLblServidor->setName  ( "inNomCGM" );
    $obLblServidor->setId    ( "inNomCGM" );
    $obLblServidor->setRotulo( "Servidor" );
    $obLblServidor->setValue ( ""         );

    $obHdnFiscal =  new Hidden;
    $obHdnFiscal->setName ( "inFiscal"              );
    $obHdnFiscal->setValue( $_REQUEST['cod_fiscal'] );

    $obHdnContrato =  new Hidden;
    $obHdnContrato->setName ( "inContrato"              );
    $obHdnContrato->setValue( $_REQUEST['cod_contrato'] );

    $obHdnCGM =  new Hidden;
    $obHdnCGM->setName ( "hdnCGM" );
    $obHdnCGM->setValue( ""       );

    $obLblMatricula = new Label;
    $obLblMatricula->setName  ( "inContrato"           );
    $obLblMatricula->setId    ( "inContrato"           );
    $obLblMatricula->setRotulo( "Matrícula"            );
    $obLblMatricula->setValue ( $_REQUEST['matricula'] );

    $obLblInformacoes = new Label;
    $obLblInformacoes->setName  ( "stInformacoesFuncao"   );
    $obLblInformacoes->setId    ( "stInformacoesFuncao"   );
    $obLblInformacoes->setRotulo( "Informações da Função" );
    $obLblInformacoes->setValue ( ""                      );
}

$obRadioAtivoSim = new Radio;
$obRadioAtivoSim->setName   ( "boAtivo"                                   );
$obRadioAtivoSim->setRotulo ( "Ativo"                                     );
$obRadioAtivoSim->setTitle  ( "Informe se o fiscal está ativo ou inativo" );
$obRadioAtivoSim->setValue  ( "Sim"                                       );
$obRadioAtivoSim->setLabel  ( "Sim"                                       );
$obRadioAtivoSim->setNull   ( false                                       );

$obRadioAtivoNao = new Radio;
$obRadioAtivoNao->setName ( "boAtivo" );
$obRadioAtivoNao->setValue( "Não"     );
$obRadioAtivoNao->setLabel( "Não"     );
$obRadioAtivoNao->setNull ( false     );

if ($stAcao=="alterar") {
    if ($_REQUEST['ativo']=="Ativo") {
        $obRadioAtivoSim->setChecked( true );
    } else {
        $obRadioAtivoNao->setChecked( true );
    }
}

if ($stAcao=="incluir") {
    $obRadioAtivoSim->setChecked( true );
}

$obRadioFuncaoAdministrador = new Radio;
$obRadioFuncaoAdministrador->setName   ( "boFuncao"                                   );
$obRadioFuncaoAdministrador->setRotulo ( "Função"                                     );
$obRadioFuncaoAdministrador->setTitle  ( "Informe se o fiscal tem a função de admistrador ou fiscal" );
$obRadioFuncaoAdministrador->setValue  ( "Administrador"                                       );
$obRadioFuncaoAdministrador->setLabel  ( "Administrador"                                       );
$obRadioFuncaoAdministrador->setNull   ( false                                       );
$obRadioFuncaoAdministrador->setChecked  ( true                                    );

$obRadioFuncaoFiscal = new Radio;
$obRadioFuncaoFiscal->setName ( "boFuncao" );
$obRadioFuncaoFiscal->setValue( "Fiscal"     );
$obRadioFuncaoFiscal->setLabel( "Fiscal"     );
$obRadioFuncaoFiscal->setNull ( false     );

if ($stAcao=="alterar") {

    if ($_REQUEST['administrador'] == 't') {
        $obRadioFuncaoAdministrador->setChecked( true );
    } else {
        $obRadioFuncaoFiscal->setChecked( true );
    }
}

$obItextBoxSelectTipoFiscalizacao = new ItextBoxSelectTipoFiscalizacao;
$obItextBoxSelectTipoFiscalizacao->setTitle("*Tipo de Fiscalização");
$obItextBoxSelectTipoFiscalizacao->obTxtTipoFiscalizacao->setName('stTipoFiscalizacao');
$obItextBoxSelectTipoFiscalizacao->obTxtTipoFiscalizacao->setId('inTipoFiscalizacao');
$obItextBoxSelectTipoFiscalizacao->setNULL (true);

$obBtnIncluirFiscalizacao = new Button;
$obBtnIncluirFiscalizacao->setName             ( "btnIncluir"             );
$obBtnIncluirFiscalizacao->setValue            ( "Incluir"                );
$obBtnIncluirFiscalizacao->setTipo             ( "button"                 );
$obBtnIncluirFiscalizacao->obEvento->setOnClick( "incluirFiscalizacao();" );
$obBtnIncluirFiscalizacao->setDisabled         ( false                    );

$botoesSpanFiscalizacao = array( $obBtnIncluirFiscalizacao );

$obSpnListaFiscalizacao = new Span;
$obSpnListaFiscalizacao->setID( "spnListaFiscalizacao" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm  ( $obForm    );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( "Dados para Fiscal" );

if ($stAcao=="incluir") {
    $obIFiltroMatricula->geraFormulario( $obFormulario );
} else {
    $obFormulario->addComponente( $obLblServidor    );
    $obFormulario->addHidden    ( $obHdnFiscal      );
    $obFormulario->addHidden    ( $obHdnContrato    );
    $obFormulario->addHidden    ( $obHdnCGM         );
    $obFormulario->addComponente( $obLblMatricula   );
    $obFormulario->addComponente( $obLblInformacoes );
}

$obFormulario->agrupaComponentes         ( array($obRadioFuncaoAdministrador,$obRadioFuncaoFiscal) );
$obFormulario->agrupaComponentes         ( array($obRadioAtivoSim,$obRadioAtivoNao) );
$obFormulario->addTitulo                 ( "Dados para Atribuições"                 );
$obItextBoxSelectTipoFiscalizacao->geraFormulario( $obFormulario                    );
$obFormulario->defineBarra               ( $botoesSpanFiscalizacao,'left',''        );
$obFormulario->addSpan                   ( $obSpnListaFiscalizacao                  );

if ($stAcao == "incluir") {
    $obFormulario->Ok();
} else {
    $obFormulario->Cancelar();
    $stJs ="ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&codFiscal=".$_REQUEST['cod_fiscal']."','carregaAtributoFiscal');";
    $stJs.="ajaxJavaScript('".CAM_GRH_PES_PROCESSAMENTO."OCFiltroCGM.php?".Sessao::getId()."";
    $stJs.="&inContrato=".$_REQUEST['matricula']."&boRescindido=', 'preencheCGMContratoExtendido' );";
    $jsOnLoad = $stJs;
}

$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
