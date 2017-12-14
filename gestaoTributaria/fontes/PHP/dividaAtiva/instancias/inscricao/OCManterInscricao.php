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
    * Página de Frame Oculto de Inscricao em divida ativa
    * Data de Criação   : 29/09/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCManterInscricao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.04.02
*/

/*
$Log$
Revision 1.5  2007/08/02 13:39:43  cercato
alterando componente da inscricao para funcionar sem zero a esquerda.

Revision 1.4  2007/08/01 21:28:20  cercato
alterando componente da inscricao para funcionar sem zero a esquerda.

Revision 1.3  2007/07/17 14:37:59  cercato
correcao para rotina de cancelamento de divida.

Revision 1.2  2006/10/05 15:34:39  dibueno
Alterações para exibir erro no componente

Revision 1.1  2006/09/29 11:55:42  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaCancelada.class.php" );

switch ($_REQUEST['stCtrl']) {
    case "atualizarInscricao":
        $stJs = "f.submit();";
        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaProcesso":
        include_once ( CAM_GA_PROT_NEGOCIO."RProcesso.class.php" );
        $obRProcesso  = new RProcesso;
        if ($_POST['inProcesso'] != '') {
            list($inProcesso,$inExercicio) = explode("/",$_POST['inProcesso']);
            $obRProcesso->setCodigoProcesso( $inProcesso  );
            $obRProcesso->setExercicio     ( $inExercicio );
            $obErro = $obRProcesso->validarProcesso();
            if ( $obErro->ocorreu() ) {
                $stJs .= 'f.inProcesso.value = "";';
                $stJs .= 'f.inProcesso.focus();';
                $stJs .= "alertaAviso('@Processo não encontrado. (".$_POST["inProcesso"].")','form','erro','".Sessao::getId()."');";
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaLivro":
        $stNomCampo = $_GET["stNomCampoCod"];
        $stJs = "";
        if ( strlen($_GET[$stNomCampo]) ) {
            $obTDATDividaAtiva = new TDATDividaAtiva;
            $obTDATDividaAtiva->recuperaLivroMax( $rsLivro );
            if ( $rsLivro->Eof() )
                $inTamanho = 1;
            else
                $inTamanho = strlen( $rsLivro->getCampo( "max_livro" ) );

            $stDados = str_replace( "/", "", $_GET[$stNomCampo] );
            $inTamanhoOrigem = strlen( $stDados );

            if ($inTamanhoOrigem >= 5) {
                $stAno = substr( $stDados, $inTamanhoOrigem-4, 4 );
                $stCodigo = substr( $stDados, 0, $inTamanhoOrigem-4 );
                $stFiltro = " WHERE num_livro = ".$stCodigo." AND exercicio_livro = '".$stAno."' LIMIT 1";
                $obTDATDividaAtiva->recuperaTodos( $rsInscricao, $stFiltro );

                if ( $rsInscricao->getNumLinhas() < 1 )
                    $boErro= true;
                else {
                    $stValor = "";
                    $inTamanhoOrigem -= 4;
                    for ($inX=0; $inX<$inTamanho-$inTamanhoOrigem; $inX++) {
                        $stValor .= "0";
                    }

                    $stValor .= $stCodigo;
                    $stJs = "jQuery('#".$_GET["stNomCampoCod"]."').val('".$stValor.'/'.$stAno."');";
                    echo $stJs;
                }
            }else
                $boErro= true;

        }

        if ($boErro) {
            $stJs = 'f.'.$_GET["stNomCampoCod"].'.value = "";';
            $stJs .= 'f.'.$_GET["stNomCampoCod"].'.focus();';
            $stJs .= "alertaAviso('@Código Livro inválido (".$_GET[$stNomCampo].").','form','erro','".Sessao::getId()."');";
            echo $stJs;
        }
        break;

    case "buscaInscricao":
        $stDescricao = '&nbsp;';
        $stNomCampo = $_GET["stNomCampoCod"];
        if ( strlen($_GET[$stNomCampo]) ) {
            $obTDATDividaAtiva = new TDATDividaAtiva;
            $obTDATDividaAtiva->recuperaCodigoInscricaoComponenteMax( $rsInscricao );
            if ( $rsInscricao->Eof() )
                $inTamanho = 1;
            else
                $inTamanho = strlen( $rsInscricao->getCampo( "max_inscricao" ) );

            $obTDATDividaAtiva = new TDATDividaAtiva;

            $stDados = str_replace( "/", "", $_GET[$stNomCampo] );

            $inTamanhoOrigem = strlen( $stDados );
            if ($inTamanhoOrigem >= 5) {
                $stAno = substr( $stDados, $inTamanhoOrigem-4, 4 );
                $stCodigo = substr( $stDados, 0, $inTamanhoOrigem-4 );

                if (isset($_REQUEST['inCodInscricaoInicial'])) {
                    $_SESSION['inCodInscricaoInicial'] = $_REQUEST['inCodInscricaoInicial'];
                }

                if (isset($_REQUEST['inCodInscricaoFinal'])) {
                    $_SESSION['inCodInscricaoFinal'] = $_REQUEST['inCodInscricaoFinal'];
                }
                if (isset($_SESSION['inCodInscricaoInicial']) && isset($_SESSION['inCodInscricaoFinal'])) {
                    if (($_SESSION['inCodInscricaoInicial'] == $_SESSION['inCodInscricaoFinal'])&&($_SESSION['inCodInscricaoInicial'] != '')&&($_SESSION['inCodInscricaoFinal'] != '')) {
                        $rsDividaCancelada     = new RecordSet;
                        $obTDATDividaCancelada = new TDATDividaCancelada;
                        $obTDATDividaCancelada->setDado('exercicio', $stAno);
                        $obTDATDividaCancelada->setDado('cod_inscricao', $stCodigo);
                        $obTDATDividaCancelada->recuperaDividaCancelada($rsDividaCancelada);
                        if ($rsDividaCancelada->inNumLinhas > 0) {
                            $stJs .= "alertaAviso('@Este código de dívida está cancelado (".$_GET[$stNomCampo].").','form','erro','".Sessao::getId()."');";

                            echo $stJs;
                        }
                    }
                }
                $obTDATDividaAtiva->setDado('cod_inscricao', $stCodigo );
                $obTDATDividaAtiva->setDado('exercicio', $stAno );
                $obTDATDividaAtiva->recuperaPorChave( $rsInscricao );
                if ( $rsInscricao->getNumLinhas() < 1 )
                    $boErro= true;
                else {
                    $stValor = "";
                    $inTamanhoOrigem -= 4;
                    for ($inX=0; $inX<$inTamanho-$inTamanhoOrigem; $inX++) {
                        $stValor .= "0";
                    }

                    $stValor .= $stCodigo;
                    $stJs = 'f.'.$_GET["stNomCampoCod"].'.value = "'.$stValor.'/'.$stAno.'";';
                    echo $stJs;
                }
            }else
                $boErro= true;
        }

        if ( isset($boErro) ) {
            $stJs = 'f.'.$_GET["stNomCampoCod"].'.value = "";';
            $stJs .= 'f.'.$_GET["stNomCampoCod"].'.focus();';
            $stJs .= "alertaAviso('@Código dívida ativa inválido (".$_GET[$stNomCampo].").','form','erro','".Sessao::getId()."');";
            echo $stJs;
        }
        break;
}
