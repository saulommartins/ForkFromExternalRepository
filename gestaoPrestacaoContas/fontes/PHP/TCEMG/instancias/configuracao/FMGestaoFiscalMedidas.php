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
   /*
    * Página de Formulario de Gestao Fiscal Medidas
    * Data de Criação: 29/07/2013

    * @author Analista:
    * @author Desenvolvedor: Carolina Schwaab Marcal

    * @ignore

    * Casos de uso:

    * $Id:

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGMedidas.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoPoderPublico.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "GestaoFiscalMedidas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$jsOnload   = "executaFuncaoAjax( 'configuracoesIniciais' );";

Sessao::remove('arMedidas');
Sessao::write('arMedidas',array());

Sessao::remove('arMedidasExcluidas');
Sessao::write('arMedidasExcluidas',array());

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnId= new Hidden;
$obHdnId->setId ("inId");
$obHdnId->setName("inId");

if (!empty($_REQUEST['inTipoPoder']) && !empty($_REQUEST['inMes'])) {
    $filtro = "WHERE medidas.cod_poder =".$_REQUEST['inTipoPoder']. " AND medidas.cod_mes =".$_REQUEST['inMes'];
} elseif (!empty($_REQUEST['inTipoPoder']) && empty($_REQUEST['inMes'])) {
    $filtro = "WHERE medidas.cod_poder =".$_REQUEST['inTipoPoder'];
} elseif (empty($_REQUEST['inTipoPoder']) && !empty($_REQUEST['inMes'])) {
    $filtro = "WHERE medidas.cod_mes =".$_REQUEST['inMes'];
}

$rsMedidas = new RecordSet();
$obMedidas = new TTCEMGMedidas();
$obMedidas->recuperaDados($rsMedidas, $filtro);

Sessao::write('rsMedidas',$rsMedidas);
$obPoderPublico = new TAdministracaoPoderPublico();
$rsPoderPublico = new RecordSet();
$stFiltro= '';
$obPoderPublico->recuperaTodos($rsPoderPublico,$stFiltro);
 $arMedida = Sessao::read('arMedidas');

 $rsMes = new RecordSet();
 $obMes = new TTCEMGMedidas();
 if ($_REQUEST['inMes'] != '') {
     $obMes->setDado('cod_mes', $_REQUEST['inMes']);
     Sessao::write('cod_mes',$_REQUEST['inMes']);
 } else {
     $obMes->setDado('cod_mes', $arMedida['inMes'] );
     Sessao::write('cod_mes', $arMedida['inMes'] );
 }
 $obMes->recuperaMes($rsMes);

 $rsPoder = new RecordSet();
 $obPoder = new TTCEMGMedidas();
 if ($_REQUEST['inTipoPoder'] != '') {
    $obPoder->setDado('cod_poder', $_REQUEST['inTipoPoder']);
    Sessao::write('cod_poder', $_REQUEST['inTipoPoder'] );
 } else {
    $obPoder->setDado('cod_poder', $arMedida['cod_poder']);
    Sessao::write('cod_poder', $arMedida['cod_poder'] );
 }
 $obPoder->recuperaPoder($rsPoder);

$obTipoPoder = new Select();
$obTipoPoder->setRotulo         ( "Tipo Poder"                 );
$obTipoPoder->setName          ('inTipoPoder');
$obTipoPoder->setNull             (false);
$obTipoPoder->addOption($_REQUEST['inTipoPoder'], $rsPoder->getCampo('poder_publico')  );
$obTipoPoder->setId          ('inTipoPoder');
$obTipoPoder->setCampoId      ( 'cod_poder'      );
$obTipoPoder->setCampoDesc ( 'nome'      );
$obTipoPoder->setTitle   ('Selecione o tipo de poder');

$obMes = new Mes();
$obMes->setId                 ('inMes');
$obMes->setValue($_REQUEST['inMes']);
$obMes->setNull  (false);

$obRiscosFiscaisSim = new Radio;
$obRiscosFiscaisSim->setRotulo  ( "Nada a declarar s/ Riscos Fiscais" );
$obRiscosFiscaisSim->setName    ( "boRiscosFiscais" );
$obRiscosFiscaisSim->setChecked(false );
$obRiscosFiscaisSim->setLabel   ( "Sim" );
$obRiscosFiscaisSim->setValue   ( "true");

$obRiscosFiscaisNao = new Radio;
$obRiscosFiscaisNao->setName    ( "boRiscosFiscais" );
$obRiscosFiscaisNao->setChecked(false );
$obRiscosFiscaisNao->setLabel   ( "Não" );
$obRiscosFiscaisNao->setValue   ( "false");

$obMetasFiscaisSim = new Radio;
$obMetasFiscaisSim->setRotulo  ( "Nada a declarar s/ Metas Fiscais " );
$obMetasFiscaisSim->setName    ( "boMetasFiscais" );
$obMetasFiscaisSim->setChecked(false );
$obMetasFiscaisSim->setLabel   ( "Sim" );
$obMetasFiscaisSim->setValue   ( "true");

$obMetasFiscaisNao = new Radio;
$obMetasFiscaisNao->setName    ( "boMetasFiscais" );
$obMetasFiscaisNao->setChecked(false );
$obMetasFiscaisNao->setLabel   ( "Não" );
$obMetasFiscaisNao->setValue   ( "false" );

$obContratacaoAROSim = new Radio;
$obContratacaoAROSim->setRotulo  ( "Contratação ARO " );
$obContratacaoAROSim->setName    ( "boContratacaoARO" );
$obContratacaoAROSim->setChecked(false);
$obContratacaoAROSim->setLabel   ( "Sim" );
$obContratacaoAROSim->setValue   ("true");

$obContratacaoARONao = new Radio;
$obContratacaoARONao->setName    ( "boContratacaoARO" );
$obContratacaoARONao->setChecked(false);
$obContratacaoARONao->setLabel   ( "Não" );
$obContratacaoARONao->setValue   ( "false ");

$obTxtMedida = new TextArea;
$obTxtMedida->setRotulo              ( "*Medidas"                 );
$obTxtMedida->setName               ( "stMedida"                          );
$obTxtMedida->setValue               (  $_REQUEST['stMedida']              );
//$obTxtMedida->setNull                  ( false                                  );
$obTxtMedida->setMaxCaracteres ( 4000                                   );
$obTxtMedida->setTitle              ( "Indicação das medidas adotadas ou a adotar.");

$obSpnListaMedidas = new Span;
$obSpnListaMedidas->setId    ( "spnListaMedidas"   );
$obSpnListaMedidas->setValue ( ""                      );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( 'Dados para Configuração de Medidas' );
$obFormulario->addHidden( $obHdnId );
$obFormulario->addComponente     ( $obTipoPoder                    );
$obFormulario->addComponente     ( $obMes                              );
if ($_REQUEST['inTipoPoder'] == 1 || $arMedida['cod_poder'] == 1) {
    $obFormulario->agrupaComponentes     ( array($obRiscosFiscaisSim , $obRiscosFiscaisNao ));
    $obFormulario->agrupaComponentes     ( array($obMetasFiscaisSim , $obMetasFiscaisNao ));
}
if (($_REQUEST['inTipoPoder'] == 1 || $arMedida['cod_poder']==1) && ($_REQUEST['inMes'] == 12 || $arMedida['inMes']==12)) {
    $obFormulario->agrupaComponentes     ( array($obContratacaoAROSim , $obContratacaoARONao ));
}
$obFormulario->addComponente     ( $obTxtMedida       );
$obFormulario->IncluirAlterar( '', array( $obTipoPoder, $obMes, $obTxtMedida  ) );
$obFormulario->addSpan( $obSpnListaMedidas );

$obBtnOk = new Ok;

//$obBtnLimpar = new Button;
//$obBtnLimpar->setName( "Limpar" );
//$obBtnLimpar->setValue( "Limpar" );
//$obBtnLimpar->setTipo( "Reset" );
//$obBtnLimpar->obEvento->setOnClick( "executaFuncaoAjax('configuracoesIniciais')" );
//
$obFormulario->defineBarra( array ( $obBtnOk  ),"","" );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
