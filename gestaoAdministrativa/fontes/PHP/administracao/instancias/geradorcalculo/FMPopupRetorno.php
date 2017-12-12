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

$stPrograma = "PopupRetorno";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$arVariaveis    = array();
$rsVariavel     = new RecordSet;

$arFuncao = Sessao::read('Funcao');
$stVariavel     = $arFuncao['RetornoVar'];
$stTipoVariavel = $arFuncao['Retorno'];
$arVariaveisTipo = Sessao::read('VariaveisTipo');

for ($inCount=0; $inCount<count($arVariaveisTipo); $inCount++) {
    if ($arVariaveisTipo[$inCount]['stTipoVariavel']==$stTipoVariavel) {
        $arVariaveis[] = $arVariaveisTipo[$inCount];
    }
}

$rsVariavel->preenche( $arVariaveis );

$stAcao = $request->get('stAcao');

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnPosicao = new Hidden;
$obHdnPosicao->setName( "stPosicao" );
$obHdnPosicao->setValue( $_REQUEST['stPosicao'] );

$obCmbVariavel = new Select;
$obCmbVariavel->setRotulo        ( "Variável" );
$obCmbVariavel->setName          ( "stVariavel" );
$obCmbVariavel->setStyle         ( "width: 200px");
$obCmbVariavel->setCampoID       ( "#[stNomeVariavel]" );
$obCmbVariavel->setCampoDesc     ( "#[stNomeVariavel]" );
$obCmbVariavel->addOption        ( "", "Selecione" );
$obCmbVariavel->setValue         ( $stVariavel );
$obCmbVariavel->setNull          ( false );
$obCmbVariavel->preencheCombo    ( $rsVariavel );

$obBtnOk = new Button;
$obBtnOk->setName ( "btnOk" );
$obBtnOk->setValue( "OK" );
$obBtnOk->obEvento->setOnClick ( "if(document.frm.stVariavel.value!=0)Ok();" );

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

$obFormulario->addTitulo            ( "Dados para o Retorno" );

$obFormulario->addComponente        ( $obCmbVariavel );

$obFormulario->addLinha();
$obFormulario->ultimaLinha->addCelula();
$obFormulario->ultimaLinha->ultimaCelula->setColSpan( 2 );
$obFormulario->ultimaLinha->ultimaCelula->setClass( "field" );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnOk  );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnCancelar );
$obFormulario->ultimaLinha->commitCelula();
$obFormulario->commitLinha();

include_once($pgJs);
$obFormulario->show                 ();

$obIFrame = new IFrame;
$obIFrame->setName("telaMensagem");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("50");
$obIFrame->show();

if (!$arFuncao['Retorno']) {
    SistemaLegado::exibeAviso(urlencode("Deve ser selecionado o tipo de Retorno."),"n_aviso","erro");
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
