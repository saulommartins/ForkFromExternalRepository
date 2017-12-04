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
    * Formulario para Fechamento de Terminal de Caixa - Tesouraria
    * Data de Criação   : 04/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-05-08 18:53:01 -0300 (Ter, 08 Mai 2007) $

    * Casos de uso: uc-02.04.06
*/

/*
$Log$
Revision 1.25  2007/05/08 21:53:01  cako
Bug #9218#

Revision 1.24  2007/03/19 14:36:06  rodrigo_sr
Bug #7855#

Revision 1.23  2006/09/04 14:34:01  jose.eduardo
Bug #6539#

Revision 1.22  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaTerminal.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma      = "FechamentoTerminal";
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

$obRTesourariaTerminal = new RTesourariaTerminal();
$obRTesourariaTerminal->addUsuarioTerminal();

$obROrcamentoEntidade = new ROrcamentoEntidade();
$obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );

// OBJETOS HIDDEN

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl"            );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao"            );
$obHdnAcao->setValue ( $stAcao             );

$stEval = "
    stCampo = document.frm.stFecharTerminal;
    if (stCampo.value=='I') {
        stCampo = document.frm.inCodTerminal;
        if (trim(stCampo.value)=='') {
            erro = true;
            mensagem += '@Campo Selecione Terminal inválido!()';
        }
    }
/*
    else if (stCampo.value=='T') {
        stCampo = document.frm.stDataMovimentacao;
        if (trim(stCampo.value)=='') {
            erro = true;
            mensagem += '@Campo Data Movimentação inválido!()';
        }
    }
*/
";

$obHdnEval = new HiddenEval;
$obHdnEval->setName  ( "stEval"            );
$obHdnEval->setValue ( $stEval             );

// DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

// DEFINE OBJETOS DO FORMULARIO

if ( Sessao::read('numCgm') > 0 ) {

    $obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

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
        $obCmbEntidade->obEvento->setOnChange( "buscaDado('buscaTerminaisAbertos');" );
    } else $jsSL = "buscaDado('buscaTerminaisAbertos');";
    $obCmbEntidade->preencheCombo( $rsEntidade                );
}

//Define Objeto Select para definir que forma serão fechados os terminais
$obCmbFecharTerminal = new Select;
$obCmbFecharTerminal->setRotulo ( "Fechar Terminal"                 );
$obCmbFecharTerminal->setName   ( "stFecharTerminal"                );
$obCmbFecharTerminal->addOption ( "T","Todos"                       );
$obCmbFecharTerminal->addOption ( "I","Individual"                  );
$obCmbFecharTerminal->setValue  ( "T"                               );
$obCmbFecharTerminal->setStyle  ( "width: 120px"                    );
$obCmbFecharTerminal->setNull   ( false                             );
$obCmbFecharTerminal->setTitle  ( "Selecione o modo de fechamento"  );
$obCmbFecharTerminal->obEvento->setOnChange( "mostraSpan(this.value);" );

//Define Objeto Select para definir qual formalário será fechado
$obCmbTerminais = new Select;
$obCmbTerminais->setRotulo      ( "Selecione Terminal"                       );
$obCmbTerminais->setName        ( "inCodTerminal"                            );
$obCmbTerminais->addOption      ( "","Selecione"                             );
$obCmbTerminais->setNull        ( false                                      );
$obCmbTerminais->setTitle       ( "Selecione o Terminal que deseja fechar"   );
$obCmbTerminais->obEvento->setOnChange( "mostraDataLabel(".Sessao::read('numCgm').");" );

// Define objeto span para lista de usuários
$obSpnTerminais = new Span();
$obSpnTerminais->setId( "spnTerminais" );
$obSpnTerminais->setValue( "" );

// Define objeto span para lista de usuários
$obSpnDataMovimentacao = new Span();
$obSpnDataMovimentacao->setId( "spnDataMovimentacao" );
//if(Sessao::read('numCgm') > 0)
    $obSpnDataMovimentacao->setValue( "" );
/*
else
    $obSpnDataMovimentacao->setValue( $stHTML );
*/

$obOk = new Ok;
if (Sessao::read('numCgm') > 0) {
    $obOk->setDisabled(true);
}

// Define Objeto Button para limpar
$obBtnLimpar = new Button;
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->obEvento->setOnClick( "limparTerminal(".Sessao::read('numCgm').");" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm                                   );
$obFormulario->addHidden    ( $obHdnCtrl                                );
$obFormulario->addHidden    ( $obHdnAcao                                );
if(Sessao::read('numCgm') == 0)
    $obFormulario->addHidden    ( $obHdnEval, true                          );

$obFormulario->addTitulo    ( "Dados para Fechamento de Caixa"          );

if (Sessao::read('numCgm') > 0) {
    $obFormulario->addComponente( $obCmbEntidade                            );
    $obFormulario->addComponente( $obCmbTerminais                           );
    $obFormulario->addSpan      ( $obSpnDataMovimentacao                    );
} else {
    $obFormulario->addComponente( $obCmbFecharTerminal                      );
    $obFormulario->addSpan      ( $obSpnTerminais                           );
    $obFormulario->addSpan      ( $obSpnDataMovimentacao                    );
}
$obFormulario->defineBarra  ( array( $obOk, $obBtnLimpar )                            );

$obFormulario->show();

if ($jsSL) SistemaLegado::executaFrameOculto($jsSL);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
