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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 04/07/2005

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30762 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.29
*/

/*
$Log$
Revision 1.6  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioExtratoRecurso.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"                     );

$obRegra             = new ROrcamentoRelatorioExtratoRecurso;
$obROrcamentoRecurso = new ROrcamentoRecurso;

//seta elementos do filtro para ENTIDADE
$arFiltro = Sessao::read('filtroRelatorio');
if ($arFiltro['inCodEntidade'] != "") {
    $inCount = 0;
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stEntidade .= $valor.",";
        $inCount++;
    }
    $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );
} else {
    $stEntidade .= $arFiltro['stTodasEntidades'];
}

$arFiltro['inCodEntidade'] = $stEntidade;

switch ($_REQUEST['stCtrl']) {
    case 'buscaRecurso':

        $obROrcamentoRecurso->setCodRecurso( $_POST['inCodRecurso'] );
        $obROrcamentoRecurso->listar( $rsRecurso );
        $obROrcamentoRecurso->recuperaMascaraRecurso( $stMascaraRecurso );
        $arMascara = Mascara::validaMascaraDinamica($stMascaraRecurso,$_POST['inCodRecurso']);
        if ( $rsRecurso->getNumLinhas() > -1 and $_POST['inCodRecurso'] ) {
            $js .= 'f.inCodRecurso.value = "'.$arMascara[1].'";';
            $js .= 'd.getElementById("stDescricaoRecurso").innerHTML = "'.$rsRecurso->getCampo("nom_recurso").'";';
        } else {
            $null = "&nbsp;";
            $js .= 'f.inCodRecurso.value = "";';
            $js .= 'f.inCodRecurso.focus();';
            $js .= 'd.getElementById("stDescricaoRecurso").innerHTML = "'.$null.'";';
            if( $_POST['inCodRecurso'] )
                $js .= "alertaAviso('@Valor inválido. (".$_POST['inCodRecurso'].")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto( $js );

    break;

    default:
        $obRegra->setExercicio    ( $arFiltro['stExercicio']          );
        $obRegra->setCodEntidade  ( $stEntidade                             );
        $obRegra->setCodRecurso   ( $arFiltro['inCodRecurso']         );
        $obRegra->setDtInicial    ( '01/01/'.$arFiltro['stExercicio'] );
        $obRegra->setDtFinal      ( $arFiltro['stDataFinal']          );
        $obRegra->geraRecordSet( $rsRecordSet );
        Sessao::write('rsRecordSet',$rsRecordSet);
        //sessao->transf5 = $rsRecordSet;
        $obRegra->obRRelatorio->executaFrameOculto( "OCGeraRelatorioExtratoRecurso.php" );
    break;
}
?>
