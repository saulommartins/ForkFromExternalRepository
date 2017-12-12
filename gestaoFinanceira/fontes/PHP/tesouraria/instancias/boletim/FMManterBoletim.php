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
    * Formulario para Inclusao de Boletins - Tesouraria
    * Data de Criação   : 31/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-08-30 13:16:14 -0300 (Qui, 30 Ago 2007) $

    * Casos de uso: uc-02.04.17 , uc-02.04.25
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CLA_IAPPLETTERMINAL );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );
SistemaLegado::LiberaFrames();

//Define o nome dos arquivos PHP
$stPrograma      = "ManterBoletim";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}
//$rsBoletimAberto = new Recordset;
$obRTesourariaBoletim = new RTesourariaBoletim();

$boMultiploBoletim = $obRTesourariaBoletim->multiploBoletim();

$obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
$obRTesourariaBoletim->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRTesourariaBoletim->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRTesourariaBoletim->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );
/*
$obErro = $obRTesourariaBoletim->listarBoletimAberto( $rsBoletimAberto , " tb.cod_boletim " );
if( $obErro->ocorreu() ) SistemaLegado::exibeAviso( urlencode($obErro->getDescricao()),"","erro" );
$rsLista = new RecordSet;*/

// DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

$obIApplet = new IAppletTerminal( $obForm );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl"            );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obHdnMultiBoletim = new Hidden;
$obHdnMultiBoletim->setName  ( "boMultiBoletim"            );
$obHdnMultiBoletim->setValue ( $boMultiploBoletim );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao"            );
$obHdnAcao->setValue ( $stAcao );

$stHdnValor = "
    erroCheck = true;
    for (i=0;i<document.frm.elements.length;i++) {
        if (document.frm.elements[i].type == 'checkbox' && document.frm.elements[i].checked == true) {
            erroCheck = false;
        }
    }
    if (erroCheck == true) {
        erro = true;
        mensagem = '@Selecione pelo menos um boletim!';
    }
    ";
$obHdnEval = new HiddenEval;
$obHdnEval->setName( "stEval" );
if ($boMultiploBoletim) {
$obHdnEval->setValue( $stHdnValor );
}

// Define Objeto Select para Entidade
$obCmbEntidade = new Select();
$obCmbEntidade->setRotulo    ( "Entidade"                 );
$obCmbEntidade->setName      ( "inCodEntidade"            );
$obCmbEntidade->setTitle     ( "Selecione a Entidade"     );
$obCmbEntidade->setCampoId   ( "cod_entidade"             );
$obCmbEntidade->setCampoDesc ( "nom_cgm"                  );
$obCmbEntidade->setValue     ( $inCodEntidade             );
$obCmbEntidade->setNull      ( false                      );
if ($rsEntidade->getNumLinhas() > 1) {
    $obCmbEntidade->addOption    ( ""            ,"Selecione" );
    $obCmbEntidade->obEvento->setOnChange( "buscaDado('buscaBoletim');" );
} else $jsSL = "buscaDado('buscaBoletim');";
$obCmbEntidade->preencheCombo( $rsEntidade                );

$obSpam = new Span();
$obSpam->setId( "spnBoletim" );

$obOk = new Ok;

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm            );
$obFormulario->addHidden    ( $obHdnCtrl         );
$obFormulario->addHidden    ( $obHdnAcao         );
$obFormulario->addHidden    ( $obHdnMultiBoletim );
$obFormulario->addHidden    ( $obIApplet         );
$obFormulario->addHidden    ( $obHdnEval, true   );

$obFormulario->addTitulo    ( "Dados do Boletim" );
$obFormulario->addComponente( $obCmbEntidade     );
$obFormulario->addSpan      ( $obSpam            );
$obFormulario->defineBarra  ( array( $obOk )     );

$obFormulario->show();
if ($jsSL) SistemaLegado::executaFrameOculto($jsSL);
// SistemaLegado::exibeAviso("Não há Boletins para serem Fechados","","alerta" );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
