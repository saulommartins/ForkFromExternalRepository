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
    * Pagina executada no frame oculto para retornar valores para o principal
    * Data de Criação   : 24/03/2005

    * @author Analista: Diego B. Victória
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: luciano $
    $Date: 2007-08-27 17:19:21 -0300 (Seg, 27 Ago 2007) $

    * Casos de uso: uc-02.03.23,uc-02.03.28
*/

/*
$Log$
Revision 1.16  2007/08/27 20:19:21  luciano
Bug#9663#

Revision 1.15  2007/08/16 15:49:59  luciano
Bug#9921#

Revision 1.14  2007/08/14 14:27:51  luciano
Bug#9663#

Revision 1.13  2007/06/28 15:31:47  luciano
Bug#9108#

Revision 1.12  2007/04/30 19:20:46  cako
implementação uc-02.03.28

Revision 1.11  2006/09/28 09:51:34  eduardo
Bug #7060#

Revision 1.10  2006/07/26 19:49:05  jose.eduardo
Bug #6666#

Revision 1.9  2006/07/05 20:48:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoPagamentoLiquidacao.class.php" );

$boAdiantamento= false;

$obREmpenhoPagamentoLiquidacao = new REmpenhoPagamentoLiquidacao;

$rsListaPagamento         = new RecordSet;
$obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade    ( $_REQUEST["inCodigoEntidade"]       );
$obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setCodigoOrdem       ( $_REQUEST["inCodigoOrdem"]    );
$obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setExercicio         ( $_REQUEST["stExercicio"]      );
$obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->consultar();
if ($obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getAdiantamento()) {
    $boAdiantamento = true;
}

if ($obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getRetencao()) {
    $boRetencao = true;
}
$obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->listarItensPagamento ( $rsListaPagamento             );
$rsListaPagamento->addFormatacao("vl_pagamento", "NUMERIC_BR");
switch ($_REQUEST["stCtrl"]) {

    case "montaLista":
        montaListaPagamento($rsListaPagamento);
    break;

    case "validaData":
        $stDataValida   = mktime (0,0,0,substr($_REQUEST['stDataValida'],3,2),substr($_REQUEST['stDataValida'],0,2),substr($_REQUEST['stDataValida'],6,4));
        $stDataAtual    = mktime (0,0,0,date("m"),date("d"),date("Y"));

        if ($stDataAtual < $stDataValida) {
           $js .= 'f.stDtPagamento.value = "";';
           $js .= "alertaAviso('Data de Pagamento é maior que a data atual!','unica','erro','".Sessao::getId()."');";
           SistemaLegado::executaFrameOculto($js);
        }
    break;

    case "buscaContaBanco":
    include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php" );
    if ($_POST['inCodContaBanco'] != "") {
        $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco();
        $obRContabilidadePlanoBanco->setCodPlano( $_POST['inCodContaBanco'] );
        $obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
        $obRContabilidadePlanoBanco->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodigoEntidade'] );
        $obRContabilidadePlanoBanco->listarPlanoContaPagamento($rsRecordSet);
        $obRContabilidadePlanoBanco->consultarRecurso();
        $stNomContaBanco = $rsRecordSet->getCampo("nom_conta");
        $inCodRecurso    = $obRContabilidadePlanoBanco->obROrcamentoRecurso->getCodRecurso();
            if (!$stNomContaBanco) {
                $js .= 'f.inCodContaBanco.value = "";';
                $js .= 'f.inCodContaBanco.focus();';
                $js .= 'd.getElementById("stContaBanco").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodContaBanco"].")','form','erro','".Sessao::getId()."');";
            } else {
                if ($rsRecordSet->getCampo("cod_banco")) {
                    if ( strlen( trim($inCodRecurso) ) > 0 ) {
                        $js .= 'd.getElementById("stContaBanco").innerHTML = "'.$stNomContaBanco.'";';
                    } else {
                        $js  = "alertaAviso('Não existe Recurso Vinculado a esta conta.','unica','erro','".Sessao::getId()."');";
                        $js .= "f.inCodContaBanco.value='';";
                        $js .= 'd.getElementById("stContaBanco").innerHTML = "&nbsp;";';
                        $js .= "f.inCodContaBanco.focus();";
                    }
                } else {
                    $js .= 'd.getElementById("stContaBanco").innerHTML = "'.$stNomContaBanco.'";';
                }
            }
     } else $js .= 'd.getElementById("stContaBanco").innerHTML = "&nbsp;";';
     SistemaLegado::executaFrameOculto($js);
    break;

    case 'buscaFornecedorDiverso':
        if ($_POST["inCodFornecedor"] != "") {
            $obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obRCGM->setNumCGM( $_POST["inCodFornecedor"] );
            $obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obRCGM->listar( $rsCGM );
            $stNomFornecedor = $rsCGM->getCampo( "nom_cgm" );
            if (!$stNomFornecedor) {
                $js .= 'f.inCodFornecedor.value = "";';
                $js .= 'f.inCodFornecedor.focus();';
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodFornecedor"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "'.$stNomFornecedor.'";';
            }
        } else $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'verificaFornecedor':

        if ($boAdiantamento) {
            include_once( TEMP."TEmpenhoResponsavelAdiantamento.class.php");
            $obTEmpenhoResponsavelAdiantamento = new TEmpenhoResponsavelAdiantamento();
            $obTEmpenhoResponsavelAdiantamento->setDado('exercicio',Sessao::getExercicio());
            $obTEmpenhoResponsavelAdiantamento->setDado('numcgm',$_REQUEST['inCodFornecedor']);
            $obTEmpenhoResponsavelAdiantamento->setDado('conta_contrapartida',$rsListaPagamento->getCampo('conta_contrapartida'));
            $obTEmpenhoResponsavelAdiantamento->consultaEmpenhosFornecedor($rsVerificaEmpenho);

            if ($rsVerificaEmpenho->getNumLinhas() > 0) {
                while (!$rsVerificaEmpenho->eof()) {
                    if (SistemaLegado::comparaDatas($_REQUEST['stDtPagamento'],$rsVerificaEmpenho->getCampo('dt_prazo_prestacao'))) {
                           $boPendente++;
                    }
                    $rsVerificaEmpenho->Proximo();
                }
                if ($boPendente) {
                    echo " alertaAviso('@O responsável por adiantamento possui prestação de contas pendentes.','form','erro','".Sessao::getId()."'); ";
                } else {
                    echo " alertaAviso('','','','".Sessao::getId()."'); ";
                }
            }
        }

    break;

    case 'verificaDataOP':
    if ($_REQUEST['stDtPagamento'] != "" and $_REQUEST['inCodigoEntidade'] != "") {
        $obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodigoEntidade']);
        $obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setDataEmissao($_REQUEST['stDataOrdem']);
        $obREmpenhoPagamentoLiquidacao->setExercicio( Sessao::getExercicio() );
        $obREmpenhoPagamentoLiquidacao->listarMaiorData( $rsMaiorData );

        $stMaiorData = $rsMaiorData->getCampo( "data_op" );

        $stDataAtual = date("d") . "/" . date("m") . "/" . date("Y");
        if (SistemaLegado::comparaDatas($rsMaiorData->getCampo( "data_op" ),$_POST["stDtPagamento"])) {
            $js .= "f.stDtPagamento.value = '".$rsMaiorData->getCampo( "data_op" )."';";
            $js .= 'window.parent.frames["telaPrincipal"].document.frm.stDtPagamento.focus();';
            $js .= "alertaAviso('@Data de Pagamento de OP deve ser maior ou igual a ".$rsMaiorData->getCampo( "data_op" )." !','form','erro','".Sessao::getId()."');";
        }
    }
    SistemaLegado::executaFrameOculto($js);
    break;

}

function montaListaPagamento($rsListaPagamento , $boRetorna = false)
{
    global $boRetencao;
    global $boAdiantamento;
    if ( $rsListaPagamento->getNumLinhas() != 0 ) {
        $obLista3 = new Lista;
        $obLista3->setRecordSet                 ( $rsListaPagamento    );
        $obLista3->setTitulo                    ( "Registros"          );
        $obLista3->setMostraPaginacao           ( false                );
        $obLista3->addCabecalho                 (                      );
        $obLista3->ultimoCabecalho->addConteudo ( "&nbsp;"             );
        $obLista3->ultimoCabecalho->setWidth    ( 5                    );
        $obLista3->commitCabecalho              (                      );
        $obLista3->addCabecalho                 (                      );
        $obLista3->ultimoCabecalho->addConteudo ( "Número Empenho"     );
        $obLista3->ultimoCabecalho->setWidth    ( 15                   );
        $obLista3->commitCabecalho              (                      );
        $obLista3->addCabecalho                 (                      );
        $obLista3->ultimoCabecalho->addConteudo ( "Data Empenho"       );
        $obLista3->ultimoCabecalho->setWidth    ( 15                   );
        $obLista3->commitCabecalho              (                      );
        $obLista3->addCabecalho                 (                      );
        $obLista3->ultimoCabecalho->addConteudo ( "Número Liquidação"  );
        $obLista3->ultimoCabecalho->setWidth    ( 15                   );
        $obLista3->commitCabecalho              (                      );
        $obLista3->addCabecalho                 (                      );
        $obLista3->ultimoCabecalho->addConteudo ( "Data Liquidação"    );
        $obLista3->ultimoCabecalho->setWidth    ( 15                   );
        $obLista3->commitCabecalho              (                      );
        $obLista3->addCabecalho                 (                      );
        $obLista3->ultimoCabecalho->addConteudo ( "Valor a Pagar"      );
        $obLista3->ultimoCabecalho->setWidth    ( 30                   );
        $obLista3->commitCabecalho              (                      );

        $obLista3->addDado                      (                               );
        $obLista3->ultimoDado->setCampo         ( "[cod_empenho]/[ex_empenho]"  );
        $obLista3->ultimoDado->setAlinhamento   ( "DIREITA"                     );
        $obLista3->commitDado                   (                               );
        $obLista3->addDado                      (                               );
        $obLista3->ultimoDado->setCampo         ( "dt_empenho"                  );
        $obLista3->ultimoDado->setAlinhamento   ( "CENTRO"                      );
        $obLista3->commitDado                   (                               );
        $obLista3->addDado                      (                               );
        $obLista3->ultimoDado->setCampo         ( "[cod_nota]/[ex_nota]"        );
        $obLista3->ultimoDado->setAlinhamento   ( "DIREITA"                     );
        $obLista3->commitDado                   (                               );
        $obLista3->addDado                      (                               );
        $obLista3->ultimoDado->setCampo         ( "dt_nota"                     );
        $obLista3->ultimoDado->setAlinhamento   ( "CENTRO"                      );
        $obLista3->commitDado                   (                               );

        $obTxtVlPagamento = new Moeda();
        $obTxtVlPagamento->setName   ( 'nuVlPagamento_[cod_nota]-[ex_nota]_' );
        $obTxtVlPagamento->setSize   ( 20               );
        $obTxtVlPagamento->setValue  ( '[vl_pagamento]' );
        if($boRetencao || $boAdiantamento)
           $obTxtVlPagamento->setReadOnly ( true );

        $obLista3->addDadoComponente( $obTxtVlPagamento );
        $obLista3->ultimoDado->setAlinhamento( 'CSS' );
        $obLista3->ultimoDado->setClass( 'show_dados_center' );
        $obLista3->commitDadoComponente();

        $obLista3->montaHTML                    (                      );
        $stHTML =  $obLista3->getHtml           (                      );
        $stHTML = str_replace                   ( "\n","",$stHTML      );
        $stHTML = str_replace                   ( chr(13),"<br>",$stHTML      );
        $stHTML = str_replace                   ( "  ","",$stHTML      );
        $stHTML = str_replace                   ( "'","\\'",$stHTML    );
    } else {
        $stHTML = "&nbsp";
    }
    $js .= "d.getElementById('spnListaItem').innerHTML = '".$stHTML."';\n";
    $inCodRecurso = $rsListaPagamento->getCampo("cod_recurso");
    $js .= "f.inCodigoRecurso.value='".$inCodRecurso."';";
    if ($boRetorna) {
        return $js;
    } else {
        SistemaLegado::executaFrameOculto($js);
    }
}

?>
