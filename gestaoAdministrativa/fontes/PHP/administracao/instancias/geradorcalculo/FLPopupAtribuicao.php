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
* Arquivo de instância para manutenção de funções
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3347 $
$Name$
$Author: pablo $
$Date: 2005-12-05 11:05:04 -0200 (Seg, 05 Dez 2005) $

Casos de uso: uc-01.03.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."RFuncao.class.php");

$stPrograma = "PopupAtribuicao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$rsVariavel = new RecordSet;
Sessao::remove('Atribuicao');
Sessao::remove('Condicao');

$rsVariavel->preenche(Sessao::read('VariaveisTipo'));

$stAcao = $request->get('stAcao');

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( $_REQUEST['stCtrl'] );

$obHdnPosicao = new Hidden;
$obHdnPosicao->setName( "stPosicao" );
$obHdnPosicao->setValue( $_REQUEST['stPosicao'] );

$obCmbVariavel = new Select;
$obCmbVariavel->setRotulo        ( "Variável" );
$obCmbVariavel->setName          ( "stVariavelInicial" );
$obCmbVariavel->setStyle         ( "width: 200px");
$obCmbVariavel->setCampoID       ( "-[stNomeVariavel]" );
$obCmbVariavel->setCampoDesc     ( "#[stNomeVariavel]" );
$obCmbVariavel->addOption        ( "", "Selecione" );
$obCmbVariavel->setValue         ( $stVariavelInicial );
$obCmbVariavel->setNull          ( false );
$obCmbVariavel->preencheCombo    ( $rsVariavel );

$obRdbAtribuicaoSimples = new Radio;
$obRdbAtribuicaoSimples->setRotulo     ( "Tipo de Atribuição" );
$obRdbAtribuicaoSimples->setName       ( "stTipoAtribuicao" );
$obRdbAtribuicaoSimples->setLabel      ( "Simples" );
$obRdbAtribuicaoSimples->setValue      ( "Simples" );
$obRdbAtribuicaoSimples->setChecked    ( (!$stTipoAtribuicao) );
$obRdbAtribuicaoSimples->setTitle      ( "" );

$obRdbAtribuicaoFuncao = new Radio;
$obRdbAtribuicaoFuncao->setRotulo     ( "Tipo de Atribuição" );
$obRdbAtribuicaoFuncao->setName       ( "stTipoAtribuicao" );
$obRdbAtribuicaoFuncao->setLabel      ( "Funcao" );
$obRdbAtribuicaoFuncao->setValue      ( "Funcao" );
$obRdbAtribuicaoFuncao->setChecked    ( ($stTipoAtribuicao) );
$obRdbAtribuicaoFuncao->setTitle      ( "" );

$obRdbAtribuicaoTrataErro = new Radio;
$obRdbAtribuicaoTrataErro->setRotulo     ( "Tipo de Atribuição" );
$obRdbAtribuicaoTrataErro->setName       ( "stTipoAtribuicao" );
$obRdbAtribuicaoTrataErro->setLabel      ( "Tratamento de Erros" );
$obRdbAtribuicaoTrataErro->setValue      ( "Erros" );
$obRdbAtribuicaoTrataErro->setChecked    ( ($stTipoAtribuicao) );
$obRdbAtribuicaoTrataErro->setTitle      ( "" );

$obBtnProximo = new Button;
$obBtnProximo->setName ( "btnProximo" );
$obBtnProximo->setValue( "Próximo" );
//$obBtnProximo->obEvento->setOnClick ( "if(document.frm.stVariavelInicial.value!=0)document.frm.submit();" );
$obBtnProximo->obEvento->setOnClick ( "Salvar();" );

$obBtnCancelar = new Button;
$obBtnCancelar->setName ( "btnCancelar" );
$obBtnCancelar->setValue( "Cancelar" );
$obBtnCancelar->obEvento->setOnClick ( "window.close();" );

$obForm = new Form;
$obForm->setAction                  ( $pgForm );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnPosicao );

$obFormulario->addTitulo            ( "Dados para atribuição" );

$obFormulario->addComponente        ( $obCmbVariavel );
$obFormulario->agrupaComponentes    ( array( $obRdbAtribuicaoSimples, $obRdbAtribuicaoFuncao, $obRdbAtribuicaoTrataErro) );

$obFormulario->addLinha();
$obFormulario->ultimaLinha->addCelula();
$obFormulario->ultimaLinha->ultimaCelula->setColSpan( 2 );
$obFormulario->ultimaLinha->ultimaCelula->setClass( "field" );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnProximo  );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnCancelar );
$obFormulario->ultimaLinha->commitCelula();
$obFormulario->commitLinha();

$obIFrame = new IFrame;
$obIFrame->setName("telaMensagem");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("50");

$obIFrameOculto = new IFrame;
$obIFrameOculto->setName("oculto");
$obIFrameOculto->setWidth("100%");
$obIFrameOculto->setHeight("50");

include_once($pgJs);
$obFormulario->show                 ();
$obIFrame->show();
$obIFrameOculto->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
