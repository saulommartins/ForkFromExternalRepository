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
    * Paginae Oculta para funcionalidade Fechamento Terminal
    * Data de Criação   : 07/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-08-23 18:09:30 -0300 (Qui, 23 Ago 2007) $

    * Casos de uso: uc-02.04.06 , uc-02.04.25

*/

/*
$Log$
Revision 1.13  2007/08/23 21:09:30  cako
Bug#9856#

Revision 1.12  2007/07/27 14:22:44  cako
Bug#9770#

Revision 1.11  2007/01/05 15:22:02  cako
Bug #7798#

Revision 1.10  2006/10/23 18:34:58  domluc
Add Caso de Uso Boletim

Revision 1.9  2006/07/05 20:39:03  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );
//include_once( CAM_GF_TES_NEGOCIO."RTesourariaTerminal.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterBoletim";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

switch ($_REQUEST["stCtrl"]) {

    case 'buscaBoletim':
    if ($_REQUEST['inCodEntidade']) {

        $obRTesourariaBoletim = new RTesourariaBoletim();

        $boMultiploBoletim = $obRTesourariaBoletim->multiploBoletim();

        $obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
        $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );

        $obErro = $obRTesourariaBoletim->listarBoletimAberto( $rsBoletimAberto, ' tb.cod_boletim ' );
        $obFormulario = new Formulario;

        if ( !$rsBoletimAberto->eof() ) {

           if ($boMultiploBoletim) {
                $arBoletimAberto = $rsBoletimAberto->getElementos();
                $inCount = $rsBoletimAberto->getNumLinhas();

                $rsBoletins = new RecordSet;
                $rsBoletins->preenche( $arBoletimAberto );

                $obLista = new Lista;
                $obLista->setMostraPaginacao( false);
                $obLista->setTitulo("Selecione os boletins que deseja fechar");
                $obLista->setRecordSet( $rsBoletins );
                $obLista->setMostraSelecionaTodos( true );
                $obLista->addCabecalho();
                $obLista->ultimoCabecalho->addConteudo("&nbsp;");
                $obLista->ultimoCabecalho->setWidth( 5 );
                $obLista->commitCabecalho();
                $obLista->addCabecalho();
                $obLista->ultimoCabecalho->addConteudo("Boletim ");
                $obLista->ultimoCabecalho->setWidth( 5 );
                $obLista->commitCabecalho();
                $obLista->addCabecalho();
                $obLista->ultimoCabecalho->addConteudo( "Data do Boletim" );
                $obLista->ultimoCabecalho->setWidth( 8 );
                $obLista->commitCabecalho();
                $obLista->addCabecalho();
                $obLista->ultimoCabecalho->addConteudo("Usuário");
                $obLista->ultimoCabecalho->setWidth( 25 );
                $obLista->commitCabecalho();
                $obLista->addCabecalho();
                $obLista->ultimoCabecalho->addConteudo("Terminal");
                $obLista->ultimoCabecalho->setWidth( 8 );
                $obLista->commitCabecalho();
                $obLista->addCabecalho();
                $obLista->ultimoCabecalho->addConteudo("Fechar");
                $obLista->ultimoCabecalho->setWidth( 8 );
                $obLista->commitCabecalho();

                $obLista->addDado();
                $obLista->ultimoDado->setCampo( "[cod_boletim]" );
                $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
                $obLista->commitDado();
                $obLista->addDado();
                $obLista->ultimoDado->setCampo( "[dt_boletim]" );
                $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
                $obLista->commitDado();
                $obLista->addDado();
                $obLista->ultimoDado->setCampo( "[cgm_usuario] - [nom_cgm]" );
                $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
                $obLista->commitDado();
                $obLista->addDado();
                $obLista->ultimoDado->setCampo( "[cod_terminal]" );
                $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
                $obLista->commitDado();

                $obChkFechar = new CheckBox;
                $obChkFechar->setName ( "boFechar_[cod_boletim]_");
                $obChkFechar->setValue( "false" );

                $obLista->addDadoComponente( $obChkFechar );
                $obLista->ultimoDado->setCampo( '' );
                $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
                $obLista->commitDadoComponente();

                $obLista->montaHTML();
                $stHTMLLista = $obLista->getHTML();
                $stJs .= "f.Ok.disabled=false;";

           } // Fim Multiplos Boletins
           else {

                $obHdnNroBoletim = new Hidden;
                $obHdnNroBoletim->setName  ( "inCodBoletim"       );
                $obHdnNroBoletim->setValue ( $rsBoletimAberto->getCampo( "cod_boletim" ) );

                $obHdnExercicio = new Hidden;
                $obHdnExercicio->setName  ( "stExercicio"         );
                $obHdnExercicio->setValue ( Sessao::getExercicio()    );

                //Define Objeto Label para Data do Boletim
                $obTxtDataBoletim = new Label;
                $obTxtDataBoletim->setName      ( "stDtBoletim"       );
                $obTxtDataBoletim->setValue     ( $rsBoletimAberto->getCampo( "dt_boletim" ) );
                $obTxtDataBoletim->setRotulo    ( "Data do Boletim"   );

                //Define Objeto Label para Nr. do Boletim
                $obTxtNroBoletim = new Label;
                $obTxtNroBoletim->setName      ( "inCodBoletim"       );
                $obTxtNroBoletim->setValue     ( $rsBoletimAberto->getCampo( "cod_boletim" ) );
                $obTxtNroBoletim->setRotulo    ( "Número do Boletim"  );

                $obFormulario->addComponente( $obTxtDataBoletim  );
                $obFormulario->addComponente( $obTxtNroBoletim   );
                $obFormulario->addHidden    ( $obHdnNroBoletim   );
                $obFormulario->addHidden    ( $obHdnExercicio    );

                $obRTesourariaBoletim->setCodBoletim( $rsBoletimAberto->getCampo( "cod_boletim" ) );
                $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->addUsuarioTerminal();
                $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->listarSituacaoPorBoletim( $rsLista, $obRTesourariaBoletim, 'aberto' );

                if ( $rsLista->getNumLinhas() > 0 ) {

                    $obLista = new Lista;
                    $obLista->setTitulo("Terminais em Aberto");
                    $obLista->setRecordSet( $rsLista );
                    $obLista->addCabecalho();
                    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
                    $obLista->ultimoCabecalho->setWidth( 5 );
                    $obLista->commitCabecalho();
                    $obLista->addCabecalho();
                    $obLista->ultimoCabecalho->addConteudo("Código ");
                    $obLista->ultimoCabecalho->setWidth( 15 );
                    $obLista->commitCabecalho();
                    $obLista->addCabecalho();
                    $obLista->ultimoCabecalho->addConteudo( "Data Abertura" );
                    $obLista->ultimoCabecalho->setWidth( 20 );
                    $obLista->commitCabecalho();
                    $obLista->addCabecalho();
                    $obLista->ultimoCabecalho->addConteudo("Usuário");
                    $obLista->ultimoCabecalho->setWidth( 35 );
                    $obLista->commitCabecalho();

                    $obLista->addDado();
                    $obLista->ultimoDado->setCampo( "cod_terminal" );
                    $obLista->commitDado();
                    $obLista->addDado();
                    $obLista->ultimoDado->setCampo( "dt_abertura" );
                    $obLista->commitDado();
                    $obLista->addDado();
                    $obLista->ultimoDado->setCampo( "nom_cgm" );
                    $obLista->commitDado();
                    //$obLista->addDado();

                    $obLista->montaHTML();
                    $stHTMLLista = $obLista->getHTML();
                    $stJs .= "f.Ok.disabled=true;";

                } else {
                    $stJs .= "f.Ok.disabled=false;";
                }
            } // Fim Boletins ùnicos

        } else {
            $obFormulario->addTitulo( "Não há Boletins para serem Fechados" );
            $stJs .= "f.Ok.disabled=true;";
        }

        $obFormulario->montaInnerHtml();
        $stHTML = $obFormulario->getHTML();
        $stHTML .= $stHTMLLista;
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

    } else {
        $stHTML = "";
    }

    $stJs .= "d.getElementById('spnBoletim').innerHTML = '".$stHTML."';".$stJsErro;
    SistemaLegado::executaFrameOculto( $stJs );
    break;

    case 'buscaBoletimFechamento':
        if ($_REQUEST['inCodEntidade']) {
            $obRTesourariaBoletim = new RTesourariaBoletim();
            $obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
            $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );

            $obErro = $obRTesourariaBoletim->listarBoletimAberto( $rsBoletimAberto , ' tb.cod_boletim ' );

            $obFormulario = new Formulario;

            if ( !$rsBoletimAberto->eof() ) {
                //Define Objeto Label para Data do Boletim
                $obTxtDataBoletim = new Label;
                $obTxtDataBoletim->setName      ( "stDtBoletim"       );
                $obTxtDataBoletim->setValue     ( $rsBoletimAberto->getCampo( "dt_boletim" ) );
                $obTxtDataBoletim->setRotulo    ( "Data do Boletim"   );

                //Define Objeto Label para Nr. do Boletim
                $obTxtNroBoletim = new Label;
                $obTxtNroBoletim->setName      ( "inCodBoletim"       );
                $obTxtNroBoletim->setValue     ( $rsBoletimAberto->getCampo( "cod_boletim" ) );
                $obTxtNroBoletim->setRotulo    ( "Número do Boletim"  );

                $obFormulario->addTitulo    ( "Este Boletim está aberto" );
                $obFormulario->addComponente( $obTxtDataBoletim          );
                $obFormulario->addComponente( $obTxtNroBoletim           );

                $stJs = 'f.Ok.disabled=true;';

            } else {
                $obHdnCgmUsuario = new Hidden;
                $obHdnCgmUsuario->setName( "cgmUsuario" );
                $obHdnCgmUsuario->setValue( Sessao::read('numCgm') );

                //Define Objeto Text para Data do Boletim
                $obTxtDataBoletim = new Data;
                $obTxtDataBoletim->setName      ( "stDtBoletim"     );
                $obTxtDataBoletim->setValue     ( $stDtBoletim      );
                $obTxtDataBoletim->setRotulo    ( "Data do Boletim" );
                $obTxtDataBoletim->setNull      ( true              );

                //Define Objeto Text para Codigo do Boletim
                $obTxtCodBoletim = new TextBox;
                $obTxtCodBoletim->setName      ( "inCodBoletim"                                );
                $obTxtCodBoletim->setValue     ( $inCodBoletim                                 );
                $obTxtCodBoletim->setRotulo    ( "Número do Boletim"                           );
                $obTxtCodBoletim->setTitle     ( "Informe o número do Boletim a ser reaberto"  );
                $obTxtCodBoletim->setNull      ( true                                          );
                $obTxtCodBoletim->setMaxLength ( 3                                             );
                $obTxtCodBoletim->setSize      ( 4                                             );

                //DEFINICAO DO FORMULARIO
                $obFormulario = new Formulario;
                $obFormulario->addTitulo    ( "Dados do Boletim" );
                $obFormulario->addComponente( $obTxtDataBoletim  );
                $obFormulario->addComponente( $obTxtCodBoletim   );

                $stJs = 'f.Ok.disabled=false;';
            }

            $obFormulario->montaInnerHtml();
            $stHTML = $obFormulario->getHTML();
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\'","\\'",$stHTML );

        } else {
            $stHTML = "";
        }

        $stJs .= "d.getElementById('spnBoletim').innerHTML = '".$stHTML."';".$stJsErro;
        SistemaLegado::executaFrameOculto( $stJs );
    break;
}
?>
