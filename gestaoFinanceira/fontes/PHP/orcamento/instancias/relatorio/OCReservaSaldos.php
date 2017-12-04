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
    * Página de Formulrio de Seleção de Impressora para Relatorio
    * Data de Criação   : 09/05/2005

    * @author Diego B. Victória
    * @author Cleisson Barboza

    * @ignore

    $Revision: 30762 $
    $Name$
    $Autor:$
    $Date: 2006-10-25 09:19:07 -0300 (Qua, 25 Out 2006) $

    * Casos de uso: uc-02.01.08
                    uc-02.01.28
*/

/*
$Log$
Revision 1.8  2006/10/25 12:19:07  larocca
Bug #7283#

Revision 1.7  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"           );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioReservaSaldos.class.php"  );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php" );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obROrcamentoDespesa = new ROrcamentoDespesa;
$obROrcamentoDespesa->setExercicio(Sessao::getExercicio() );
switch ($stCtrl) {

    case 'buscaDespesa':

    if ($inCodDespesa != "") {
        $obROrcamentoDespesa->setCodDespesa( $inCodDespesa );
        $obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
        $obROrcamentoDespesa->listarDespesaUsuario( $rsDespesa );
        $stNomDespesa = $rsDespesa->getCampo( "descricao" );
        if (!$stNomDespesa) {
            $js .= 'f.inCodDespesa.value = "";';
            $js .= 'window.parent.frames["telaPrincipal"].document.frm.inCodDespesa.focus();';
            $js .= 'd.getElementById("stNomDespesa").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodDespesa"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("stNomDespesa").innerHTML = "'.$stNomDespesa.'";';
        }
    } else $js .= 'd.getElementById("stNomDespesa").innerHTML = "&nbsp;";';
    SistemaLegado::executaFrameOculto($js);
    break;

    case 'montaUnidade':
        $stJs  = "limpaSelect( f.inNumUnidade,0 );";
        $stJs .= "f.inNumUnidadeTxt.value = '';";
        $stJs .= "f.inNumUnidade.options[0] = new Option('Selecione',  '', '');";
        if ($_REQUEST['inNumOrgao'] != "") {
            $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $_REQUEST['inNumOrgao'] );
            $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setExercicio( Sessao::getExercicio() );
            $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->consultar( $rsUnidade );

            $inCount = 0;
            while (!$rsUnidade->eof()) {
                $inCount++;
                $inId   = $rsUnidade->getCampo("num_unidade");
                $stDesc = $rsUnidade->getCampo("nom_unidade");
                if( $stSelecionado == $inId )
                    $stSelected = 'selected';
                else
                    $stSelected = '';
                $stJs .= "f.inNumUnidade.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                $unidade[$rsUnidade->getCampo('num_unidade')] = ' - '.$rsUnidade->getCampo('nom_unidade');
                $arNomFiltro = Sessao::read('filtroNomRelatorio');
                $arNomFiltro['unidade'] = $unidade;
                Sessao::write('nomFiltro',$arNomFiltro);
                $rsUnidade->proximo();
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;

    default:
        $obRRelatorio      = new RRelatorio;
        $obROrcamentoReservaSaldos  = new ROrcamentoRelatorioReservaSaldos;
        $arFiltro = Sessao::read('filtroRelatorio');

        if ( is_array($arFiltro['inCodEntidade']) ) {
            foreach ($arFiltro['inCodEntidade'] as $inCodEntidadeForm) {
                $stCodigoEntidade .= $inCodEntidadeForm.',';
            }
        }
        $stCodigoEntidade = ( $stCodigoEntidade ) ? substr($stCodigoEntidade,0,strlen($stCodigoEntidade)-1) : '';
        $obROrcamentoReservaSaldos->setExercicio                        ( Sessao::getExercicio()              );
        $obROrcamentoReservaSaldos->obROrcamentoDespesa->setCodDespesa  ( $arFiltro['inCodDespesa'] );
        $obROrcamentoReservaSaldos->setCodReserva                       ( $arFiltro['inCodReserva'] );
        $obROrcamentoReservaSaldos->setDtInicial                        ( $arFiltro['stDtInicial']  );
        $obROrcamentoReservaSaldos->setDtFinal                          ( $arFiltro['stDtFinal']    );
//        $obROrcamentoReservaSaldos->setTipo                             ( "M"                             );
        $obROrcamentoReservaSaldos->setNumOrgao                         ( $arFiltro['inNumOrgao']   );
        $obROrcamentoReservaSaldos->setNumUnidade                       ( $arFiltro['inNumUnidade'] );
        $obROrcamentoReservaSaldos->setSituacao                         ( $arFiltro['stSituacao']   );
        $obROrcamentoReservaSaldos->setListarReservas                   ( $arFiltro['stReservas']   );
        $obROrcamentoReservaSaldos->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $stCodigoEntidade );
        $obROrcamentoReservaSaldos->obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso( $arFiltro['inCodRecurso'] );
        $obROrcamentoReservaSaldos->listarReservaSaldos( $rsLista );

        Sessao::write('rsLista',$rsLista);
        //sessao->transf5 = $rsLista;

        $obRRelatorio->executaFrameOculto( "OCGeraRelatorioReservaSaldos.php" );
    break;
}

?>
