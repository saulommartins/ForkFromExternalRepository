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
    $Date: 2007-08-22 16:00:52 -0300 (Qua, 22 Ago 2007) $

    * Casos de uso: uc-02.04.25

*/

/*
$Log$
Revision 1.5  2007/08/22 19:00:52  cako
Bug#9858#

Revision 1.4  2006/10/23 17:38:14  domluc
Adicionada verificação a configuração

Revision 1.3  2006/10/23 16:33:08  domluc
Add opção para multiplos boletins

Revision 1.2  2006/07/05 20:39:03  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );
//include_once( CAM_GF_TES_NEGOCIO."RTesourariaTerminal.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "AbrirBoletim";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

sistemalegado::BloqueiaFrames();
switch ($_REQUEST["stCtrl"]) {

    case 'buscaNovoBoletim':
        if ($_REQUEST['inCodEntidade']) {
            $obRTesourariaBoletim = new RTesourariaBoletim();

            $boMultiploBoletim = $obRTesourariaBoletim->multiploBoletim();

            $obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
            $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );

            $obErro = $obRTesourariaBoletim->listarBoletimAberto( $rsBoletimAberto );

            $obRTesourariaBoletim->listar( $rsBoletim, "", " dt_boletim DESC");
            list($ano3,$mes3,$dia3) = preg_split("/-/",$rsBoletim->getCampo("dt_boletim"));
            $stUltimaData = "".$dia3."/".$mes3."/".$ano3."";

            $obFormulario = new Formulario;

            if ( !$boMultiploBoletim && $rsBoletimAberto->getNumLinhas() > 0 ) {
                $obLblAviso = new Label;
                $obLblAviso->setRotulo ( 'Boletim' );
                $obLblAviso->setValue  ( 'Entidade possui Boletim Aberto, na configuração atual não é possivel a abertura de multiplos Boletins' );

                $obFormulario->addComponente ( $obLblAviso );
                $stJs = 'f.Ok.disabled=true;';
            } else {
                $obErro = $obRTesourariaBoletim->buscaProximoCodigo( $boTransacao );

                $obHdnCgmUsuario = new Hidden;
                $obHdnCgmUsuario->setName( "cgmUsuario" );
                $obHdnCgmUsuario->setValue( Sessao::read('numCgm') );

                $obHdnNroBoletim = new Hidden;
                $obHdnNroBoletim->setName( "inCodBoletim" );
                $obHdnNroBoletim->setValue( $obRTesourariaBoletim->getCodBoletim() );

                //Define Objeto Label para Nr. do Boletim
                $obTxtNroBoletim = new Label;
                $obTxtNroBoletim->setName      ( "inCodBoletim"       );
                $obTxtNroBoletim->setValue     ( $obRTesourariaBoletim->getCodBoletim() );
                $obTxtNroBoletim->setRotulo    ( "Número do Boletim"  );

                 //Define Objeto Label para Data do último Boletim Aberto
                $obTxtDtUltBoletim = new Label;
                $obTxtDtUltBoletim->setName      ( "inDtUltBoletim"       );
                $obTxtDtUltBoletim->setValue     ( $stUltimaData );
                $obTxtDtUltBoletim->setRotulo    ( "Data do Último Boletim"  );

                //Define Objeto Text para Data do Boletim
                $obTxtDataBoletim = new Data;
                $obTxtDataBoletim->setName      ( "stDtBoletim"     );
                $obTxtDataBoletim->setValue     ( ""                );
                $obTxtDataBoletim->setRotulo    ( "Data do Boletim" );
                $obTxtDataBoletim->setTitle     ( "Informe a Data do Boletim." );
                $obTxtDataBoletim->setNull      ( false             );

                //DEFINICAO DO FORMULARIO
                $obFormulario = new Formulario;
                $obFormulario->addHidden    ( $obHdnNroBoletim   );
                if ($rsBoletim->getCampo("dt_boletim")!="") $obFormulario->addComponente($obTxtDtUltBoletim  );
                $obFormulario->addComponente( $obTxtNroBoletim   );
                $obFormulario->addComponente( $obTxtDataBoletim  );

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
            $stJs = 'f.Ok.disabled=false;';
        }

        $stJs .= "d.getElementById('spnBoletim').innerHTML = '".$stHTML."';".$stJsErro;
        SistemaLegado::executaFrameOculto( $stJs );
        SistemaLegado::LiberaFrames();
    break;
}
?>
