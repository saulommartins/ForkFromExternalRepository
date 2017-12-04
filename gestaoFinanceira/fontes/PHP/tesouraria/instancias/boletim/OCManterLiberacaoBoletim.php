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
    * Paginae Oculta para funcionalidade Liberação de Boletim
    * Data de Criação   : 14/12/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-09-10 14:49:05 -0300 (Seg, 10 Set 2007) $

    * Casos de uso: uc-02.04.08 , uc-02.04.25

*/

/*
$Log$
Revision 1.8  2007/09/10 17:49:05  rodrigo_sr
Ticket#10008#

Revision 1.7  2007/08/30 15:57:58  cako
Bug#10008#

Revision 1.6  2006/10/23 18:34:58  domluc
Add Caso de Uso Boletim

Revision 1.5  2006/07/05 20:39:03  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "LiberacaoBoletim";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$obRTesourariaBoletim = new RTesourariaBoletim();

$stAcao = $request->get('stAcao');

switch ($_REQUEST["stCtrl"]) {

    case 'montaLista':
        if ($_REQUEST['inCodEntidade']) {
            if ($_REQUEST['stDataInicial']) {
                $obRTesourariaBoletim = new RTesourariaBoletim();
                $obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
                $obRTesourariaBoletim->setDataBoletimInicial($_REQUEST['stDataInicial'] );
                $obRTesourariaBoletim->setDataBoletimFinal($_REQUEST['stDataFinal'] );
                $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );

                if($stAcao == 'incluir')
                    $obErro = $obRTesourariaBoletim->listarBoletimFechado( $rsBoletim , ' tb.cod_boletim ' );
                elseif( $stAcao == 'excluir' )
                    $obErro = $obRTesourariaBoletim->listarBoletimLiberado( $rsBoletim , ' tb.cod_boletim ' );

                if (!$obErro->ocorreu()) {
                    $obLista = new Lista;
                    $obLista->setTitulo     ( "Boletins ".($stAcao == "incluir" ? "fechados":"liberados" )." para a entidade" );
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
                    $obLista->ultimoCabecalho->addConteudo(($stAcao == 'incluir' ? "Liberar" : "Cancelar" ));
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

                    $obChkLiberar = new CheckBox;
                    $obChkLiberar->setName ( "boLiberar_[cod_boletim]_");
                    $obChkLiberar->setValue( "false" );

                    $obLista->addDadoComponente( $obChkLiberar );
                    $obLista->ultimoDado->setCampo( '' );
                    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
                    $obLista->commitDadoComponente();

                    $obLista->montaInnerHTML();

                    $stHTML = $obLista->getHTML();
                    $stHTML = str_replace( "\n" ,"" ,$stHTML );
                    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
                    $stHTML = str_replace( "  " ,"" ,$stHTML );
                    $stHTML = str_replace( "'","\\'",$stHTML );
                    $stHTML = str_replace( "\\\'","\\'",$stHTML );

                    $stJs  = "document.getElementById('spnBoletins').innerHTML = '".$stHTML."';";
                    $stJs .= "document.getElementById('Ok').disabled = false;";
                }

            } else $stJs .= "alertaAviso('Selecione um mês.','','alerta','".Sessao::getId()."');";
        } else $stJs .= "alertaAviso('Selecione uma entidade.','','alerta','".Sessao::getId()."');";

        echo $stJs;
    break;

    case 'Limpar':
        $stJs  = "d.getElementById('spnBoletins').innerHTML = '';";
        $stJs .= "d.getElementById('Ok').disabled = true;";
        $stJs .= "document.frm.inMes.value = '';";
        echo $stJs;
    break;

}

?>
