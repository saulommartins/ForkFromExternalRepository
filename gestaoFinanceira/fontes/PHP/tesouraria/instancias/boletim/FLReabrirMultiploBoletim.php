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
    * Filtro para Alteração de Terminais - Tesouraria
    * Data de Criação   : 03/11/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-09-10 14:32:41 -0300 (Seg, 10 Set 2007) $

    * Casos de uso: uc-02.04.17 , uc-02.04.25
*/

/*
$Log$
Revision 1.9  2007/09/10 17:32:41  rodrigo_sr
Limpa a lista quando o código da entidade é alterado pela textbox.

Revision 1.8  2007/09/10 15:18:06  rodrigo_sr
Ticket#9982#

Revision 1.7  2007/08/30 16:05:10  cako
Bug#9982#

Revision 1.6  2007/08/24 20:24:51  cako
Bug#9982#

Revision 1.5  2007/08/23 21:14:08  cako
Bug#9982#

Revision 1.4  2007/01/10 16:07:08  cako
Bug #8034#

Revision 1.3  2006/10/23 18:34:58  domluc
Add Caso de Uso Boletim

Revision 1.2  2006/10/23 17:38:14  domluc
Adicionada verificação a configuração

Revision 1.1  2006/10/23 16:33:08  domluc
Add opção para multiplos boletins

Revision 1.11  2006/07/05 20:39:03  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CLA_IAPPLETTERMINAL );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );
SistemaLegado::LiberaFrames();

//Define o nome dos arquivos PHP
$stPrograma      = "ReabrirBoletim";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PRReabrirMultiploBoletim.php";
$pgOcul          = "OCReabrirMultiploBoletim.php";
$pgJs            = "JSReabrirMultiploBoletim.js";

include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "reabrir";
}

$rsBoletimAberto = new Recordset;
$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
$obRTesourariaBoletim->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRTesourariaBoletim->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRTesourariaBoletim->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

$boMultiploBoletim = $obRTesourariaBoletim->multiploBoletim();

// DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget ( "oculto" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl"            );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao"            );
$obHdnAcao->setValue ( $stAcao );

$obIApplet = new IAppletTerminal( $obForm );

include_once ( CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeUsuario.class.php'                        );
$obCmbEntidade = new ITextBoxSelectEntidadeUsuario;
$obCmbEntidade->obSelect->obEvento->setOnChange("montaParametrosGET('Limpar');");
if($inCodEntidade)
    $obCmbEntidade->inCodEntidade = $inCodEntidade;
    $obCmbEntidade->obTextBox->obEvento->setOnChange("montaParametrosGET('Limpar');");

$obHdnCgmUsuario = new Hidden;
$obHdnCgmUsuario->setName( "cgmUsuario" );
$obHdnCgmUsuario->setValue( Sessao::read('numCgm') );

$obHiddenTimestampFechamento = new Hidden;
$obHiddenTimestampFechamento->setId     ( 'stTimestampFechamento' );
$obHiddenTimestampFechamento->setName   ( 'stTimestampFechamento' );
$obHiddenTimestampFechamento->setValue  ( ''                      );

$obLblAte = new Label;
$obLblAte->setName      ( "stAte" );
$obLblAte->setValue     ( "&nbsp;até&nbsp;"   );
$obLblAte->setRotulo    ( ""      );

//Define Objeto Text para Data do Boletim
$obTxtDataBoletim = new Data;
$obTxtDataBoletim->setName      ( "stDtBoletim"     );
$obTxtDataBoletim->setId        ( "stDtBoletim"     );
$obTxtDataBoletim->setValue     ( $stDtBoletim      );
$obTxtDataBoletim->setRotulo    ( "Data do Boletim" );
$obTxtDataBoletim->setTitle     ( "Informe um intervalo de datas dos Boletins a ser reaberto." );
$obTxtDataBoletim->setNull      ( true              );

$obTxtDataBoletimFinal = new Data;
$obTxtDataBoletimFinal->setName      ( "stDtBoletimFinal"     );
$obTxtDataBoletimFinal->setId        ( "stDtBoletimFinal"     );
$obTxtDataBoletimFinal->setValue     ( $stDtBoletimFinal      );
$obTxtDataBoletimFinal->setRotulo    ( "Data do Boletim" );
$obTxtDataBoletimFinal->setTitle     ( "Informe um intervalo de datas dos Boletins a ser reaberto." );
$obTxtDataBoletimFinal->setNull      ( true              );

//Define Objeto Text para Codigo do Boletim
$obTxtCodBoletim = new Inteiro;
$obTxtCodBoletim->setName      ( "inCodBoletim"                                );
$obTxtCodBoletim->setId        ( "inCodBoletim"                                );
$obTxtCodBoletim->setValue     ( $inCodBoletim                                 );
$obTxtCodBoletim->setRotulo    ( "Número do Boletim"                           );
$obTxtCodBoletim->setTitle     ( "Informe um intervalo de Boletins a ser reaberto."  );
$obTxtCodBoletim->setNull      ( true                                          );
$obTxtCodBoletim->setMaxLength ( 3                                             );
$obTxtCodBoletim->setSize      ( 4                                             );

$obTxtCodBoletimFinal = new Inteiro;
$obTxtCodBoletimFinal->setName      ( "inCodBoletimFinal"                           );
$obTxtCodBoletimFinal->setId        ( "inCodBoletimFinal"                           );
$obTxtCodBoletimFinal->setValue     ( $inCodBoletimFinal                            );
$obTxtCodBoletimFinal->setRotulo    ( "Número do Boletim"                           );
$obTxtCodBoletimFinal->setTitle     ( "Informe um intervalo de Boletins a ser reaberto."  );
$obTxtCodBoletimFinal->setNull      ( true                                          );
$obTxtCodBoletimFinal->setMaxLength ( 3                                             );
$obTxtCodBoletimFinal->setSize      ( 4                                             );

$obListar = new Button;
$obListar->setValue( "Listar" );
$obListar->obEvento->setOnClick( "montaParametrosGET('montaListaReabertura');");

$obSpaBoletins = new Span();
$obSpaBoletins->setId( "spnBoletins" );

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
$obHdnEval->setValue( $stHdnValor );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm        );
$obFormulario->addTitulo    ( "Dados para Filtro" );
$obFormulario->addHidden    ( $obHdnCtrl     );
$obFormulario->addHidden    ( $obHdnAcao     );
$obFormulario->addHidden    ( $obIApplet     );
$obFormulario->addHidden    ( $obHdnEval, true );
$obFormulario->addComponente( $obCmbEntidade );
$obFormulario->agrupaComponentes( array ( $obTxtDataBoletim, $obLblAte, $obTxtDataBoletimFinal) );
$obFormulario->agrupaComponentes( array ( $obTxtCodBoletim, $obLblAte, $obTxtCodBoletimFinal) );
$obFormulario->addComponente ( $obListar );
$obFormulario->addHidden    ( $obHiddenTimestampFechamento );
$obFormulario->addSpan      ( $obSpaBoletins );
$obOk = new Ok();
$obOk->setId('Ok');
$obLimpar = New Limpar();
$obLimpar->setValue ("Limpar");
$obLimpar->obEvento->setOnClick("montaParametrosGET('Limpar');");
//$obFormulario->Ok();

if ($boMultiploBoletim) {
    $obFormulario->defineBarra( array( $obOk, $obLimpar) );
    $obFormulario->show();
    $jsOnload = "document.getElementById('Ok').disabled = true;";
    if ($jsSL) $jsOnload .=  $jsSL;
} else {
    $obLblAviso = new Label;
    $obLblAviso->setRotulo ( 'Configuração' );
    $obLblAviso->setValue ( 'Não é permitida a abertura de multiplos Boletins' );
    $obFormulario = new Formulario;
    $obFormulario->addTitulo ( 'Abertura de multiplos Boletins ' );
    $obFormulario->addComponente($obLblAviso);
    $obFormulario->show();

    SistemaLegado::exibeAviso('Não é permitida a abertura de multiplos Boletins' , 'n_alerta' , 'alerta');
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
