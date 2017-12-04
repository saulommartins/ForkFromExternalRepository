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
    * Pagina Oculta para Reabrir Multiplos Boletins
    * Data de Criação   : 13/10/2006

    * @author Analista: Lucas Teixeira Stephanou
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-08-30 13:05:10 -0300 (Qui, 30 Ago 2007) $

    * Casos de uso: uc-02.04.06 , uc-02.04.25

*/

/*
$Log$
Revision 1.7  2007/08/30 16:05:10  cako
Bug#9982#

Revision 1.6  2007/08/24 20:24:51  cako
Bug#9982#

Revision 1.5  2007/08/23 21:14:08  cako
Bug#9982#

Revision 1.4  2007/07/27 13:55:44  cako
Bug#9771#

Revision 1.3  2007/04/30 20:22:51  cako
Bug #9091#

Revision 1.2  2006/10/23 18:34:58  domluc
Add Caso de Uso Boletim

Revision 1.1  2006/10/23 16:33:08  domluc
Add opção para multiplos boletins

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterBoletim";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PRReabrirMultiploBoletim.php";
$pgOcul = "OCReabrirMultiploBoletim.php";
$pgPror = "PO".$stPrograma.".php";

switch ($_REQUEST["stCtrl"]) {

    case 'montaListaReabertura':
        if ($_REQUEST['inCodEntidade']) {
            $obRTesourariaBoletim = new RTesourariaBoletim();
            $obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
            $obRTesourariaBoletim->setCodBoletimInicial($_REQUEST['inCodBoletim'] );
            $obRTesourariaBoletim->setCodBoletimFinal($_REQUEST['inCodBoletimFinal'] );
            $obRTesourariaBoletim->setDataBoletimInicial($_REQUEST['stDtBoletim'] );
            $obRTesourariaBoletim->setDataBoletimFinal($_REQUEST['stDtBoletimFinal'] );
            $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );

            $obErro = $obRTesourariaBoletim->listarBoletimFechado( $rsBoletim , ' tb.cod_boletim ' );
            // listar boletins abertos para a entidade
            if (!$obErro->ocorreu()) {
                $obLista = new Lista;
                $obLista->setTitulo     ( 'Selecione os boletins que deseja reabrir' );
                $obLista->setMostraPaginacao( false );
                $obLista->setMostraSelecionaTodos( true );
                $obLista->setRecordSet  ( $rsBoletim );
                $obLista->addCabecalho();
                $obLista->ultimoCabecalho->addConteudo("&nbsp;");
                $obLista->ultimoCabecalho->setWidth( 5 );
                $obLista->commitCabecalho();
                $obLista->ultimoCabecalho->addConteudo("Boletim");
                $obLista->ultimoCabecalho->setWidth( 5 );
                $obLista->commitCabecalho();
                $obLista->addCabecalho();
                $obLista->ultimoCabecalho->addConteudo("Data do Boletim");
                $obLista->ultimoCabecalho->setWidth( 10 );
                $obLista->commitCabecalho();
                $obLista->addCabecalho();
                $obLista->ultimoCabecalho->addConteudo("Usuário");
                $obLista->ultimoCabecalho->setWidth( 25 );
                $obLista->commitCabecalho();

                $obLista->addCabecalho();
                $obLista->ultimoCabecalho->addConteudo("Reabrir");
                $obLista->ultimoCabecalho->setWidth( 8 );
                $obLista->commitCabecalho();

                $obLista->addDado();
                $obLista->ultimoDado->setCampo( "cod_boletim" );
                $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
                $obLista->commitDado();
                $obLista->addDado();
                $obLista->ultimoDado->setCampo( "data_boletim" );
                $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
                $obLista->commitDado();

                $obLista->addDado();
                $obLista->ultimoDado->setCampo( "[cgm_usuario] - [nom_cgm]" );
                $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
                $obLista->commitDado();

                $obChkReabrir = new CheckBox;
                $obChkReabrir->setName ( "boReabrir_[cod_boletim]_");
                $obChkReabrir->setValue( "false" );

                $obLista->addDadoComponente( $obChkReabrir );
                $obLista->ultimoDado->setCampo( '' );
                $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
                $obLista->commitDadoComponente();

                $obLista->montaHTML();
                $stHtmlLista = $obLista->getHTML();
                $stHtmlLista = str_replace( "\n" ,"" ,$stHtmlLista );
                $stHtmlLista = str_replace( "  " ,"" ,$stHtmlLista );
                $stHtmlLista = str_replace( "'","\\'",$stHtmlLista );

                $stJs  = "d.getElementById('spnBoletins').innerHTML = '".$stHtmlLista."';";
                $stJs .= "d.getElementById('Ok').disabled = false;";
            } else {
                $stJs .= "alertaAviso('".$obErro->getDescricao().".','','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "alertaAviso('Selecione uma entidade.','','alerta','".Sessao::getId()."');";
        }
        echo $stJs;

    break;

    case 'Limpar':
        $stJs  = "d.getElementById('spnBoletins').innerHTML = '';";
        $stJs .= "d.getElementById('Ok').disabled = true;";
        echo $stJs;
    break;

}
?>
