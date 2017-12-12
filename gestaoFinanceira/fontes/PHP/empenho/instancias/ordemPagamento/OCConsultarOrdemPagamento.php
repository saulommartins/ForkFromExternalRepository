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
    * Página de processamento Oculto para Consulta de Ordens de Pagamento
    * Data de Criação: 31/05/2005

    * @author Analista: Dieine
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: cako $
    $Date: 2006-11-22 18:01:19 -0200 (Qua, 22 Nov 2006) $

    * Casos de uso: uc-02.03.05
*/

/*
$Log$
Revision 1.8  2006/11/22 20:01:19  cako
Bug #7244#

Revision 1.7  2006/09/28 09:51:34  eduardo
Bug #7060#

Revision 1.6  2006/07/05 20:48:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoOrdemPagamento.class.php" );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoNotaLiquidacao.class.php" );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"               );

function montaListaContas()
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamento.class.php" );
    $obTEmpenhoOrdemPagamento = new TEmpenhoOrdemPagamento;
    $stNotas = str_replace('-',',',$_GET['stNotas']);
    $stNotas = substr($stNotas,0,strlen($stNotas)-1);
    $obTEmpenhoOrdemPagamento->setDado('cod_ordem',         $_GET['inCodOrdem']);
    $obTEmpenhoOrdemPagamento->setDado('cod_nota',          $stNotas);
    $obTEmpenhoOrdemPagamento->setDado('exercicio',         $_GET['stExercicio']);
    $obTEmpenhoOrdemPagamento->setDado('exercicio_empenho', $_GET['stExercicioEmpenho']);
    $obTEmpenhoOrdemPagamento->setDado('cod_entidade',      $_GET['inCodEntidade']);
    $obTEmpenhoOrdemPagamento->recuperaContasPagadoras( $rsRecordSet );

    $rsRecordSet->addFormatacao("vl_pago", "NUMERIC_BR" );

    $obLista = new Lista;
    $obLista->setMostraPaginacao ( false );
    $obLista->setRecordSet ( $rsRecordSet );
    $obLista->setTitulo ( 'Contas Pagadoras' );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Conta");
    $obLista->ultimoCabecalho->setWidth( 50 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Recurso");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor Pago");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_plano] - [nom_conta]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_recurso] - [nom_recurso]" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "vl_pago" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs .= "d.getElementById('spnListaContas').innerHTML = '".$stHTML."';";

    return $stJs;
}

function montaLista($arRecordSet, $boExecuta = true)
{
    $rsLista = new RecordSet;
    $rsLista->preenche( $arRecordSet );
    $rsLista->addFormatacao( "valor", "NUMERIC_BR" );

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsLista );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->ultimoCabecalho->setRowSpan( 2 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Empenho");
    $obLista->ultimoCabecalho->setWidth( 38 );
    $obLista->ultimoCabecalho->setColSpan( 2 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Liquidação");
    $obLista->ultimoCabecalho->setWidth( 38 );
    $obLista->ultimoCabecalho->setColSpan( 2 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor a Pagar");
    $obLista->ultimoCabecalho->setWidth( 19 );
    $obLista->ultimoCabecalho->setRowSpan( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho( true );
    $obLista->ultimoCabecalho->addConteudo("Número");
    $obLista->ultimoCabecalho->setWidth( 18 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Data");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Número");
    $obLista->ultimoCabecalho->setWidth( 18 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Data");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nr_empenho" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_empenho" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nr_nota" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_nota" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "valor" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto("d.getElementById('spnListaRegistros').innerHTML = '".$stHTML."';");
    } else {
        return $stHTML;
    }
}

switch ($_REQUEST ["stCtrl"]) {
    case 'montaListaLiquidacoes':
        $stHTML =  montaLista( Sessao::read('arNota'), true );
    break;

    case 'montaListaContas':
        $stJs = montaListaContas();
        echo $stJs;
    break;

    case "buscaCredor":
        if ($_POST["inCodCredor"] != "") {
            $obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
            $obREmpenhoOrdemPagamento->obROrcamentoEntidade->obRCGM->setNumCGM( $_POST['inCodCredor'] );
            $obREmpenhoOrdemPagamento->obROrcamentoEntidade->obRCGM->listar( $rsCGM );
            $stNomCredor = $rsCGM->getCampo( "nom_cgm" );
            if (!$stNomCredor) {
                $js .= 'f.inCodCredor.value = "";';
                $js .= 'f.inCodCredor.focus();';
                $js .= 'd.getElementById("stNomCredor").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodCredor"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomCredor").innerHTML = "'.$stNomCredor.'";';
            }
        } else {
            $js .= 'd.getElementById("stNomCredor").innerHTML = "&nbsp;";';
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

    case "buscaRecurso":
        if ($_POST["inCodRecurso"] != "") {
            $obROrcamentoRecurso = new ROrcamentoRecurso;
            $obROrcamentoRecurso->setExercicio ( Sessao::getExercicio() );
            $obROrcamentoRecurso->setCodRecurso( $_POST['inCodRecurso'] );
            $obROrcamentoRecurso->listar( $rsRecurso );
            $stNomRecurso = $rsRecurso->getCampo( 'nom_recurso' );
            if (!$stNomRecurso) {
                $js .= 'f.inCodRecurso.value = "";';
                $js .= 'f.inCodRecurso.focus();';
                $js .= 'd.getElementById("stNomRecurso").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodRecurso"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomRecurso").innerHTML = "'.$stNomRecurso.'";';
            }
        } else {
            $js .= 'd.getElementById("stNomRecurso").innerHTML = "&nbsp;";';
        }
        SistemaLegado::executaFrameOculto($js);
    break;
}
?>
