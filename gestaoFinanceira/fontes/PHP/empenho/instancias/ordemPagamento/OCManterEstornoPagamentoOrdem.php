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
    * Data de Criação   : 29/03/2005

    * @author Analista: Diego B. Victória
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-09-03 12:45:47 -0300 (Seg, 03 Set 2007) $

    * Casos de uso: uc-02.03.23
                    uc-02.03.28
*/

/*
$Log$
Revision 1.14  2007/09/03 15:45:47  luciano
Bug#9663#

Revision 1.13  2007/08/16 15:56:31  luciano
Bug#9663#,Bug#9921#

Revision 1.12  2007/08/14 14:28:31  luciano
Bug#9663#

Revision 1.11  2007/08/10 21:23:11  luciano
Bug#9663#

Revision 1.10  2007/05/30 13:13:11  luciano
#9090#

Revision 1.9  2007/04/30 19:20:46  cako
implementação uc-02.03.28

Revision 1.8  2007/04/05 15:16:08  cako
Bug #8996#

Revision 1.7  2006/10/11 17:28:48  cako
Ajustes

Revision 1.6  2006/09/28 09:51:34  eduardo
Bug #7060#

Revision 1.5  2006/07/21 19:10:36  jose.eduardo
Bug #6616#

Revision 1.4  2006/07/05 20:48:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoPagamentoLiquidacao.class.php" );

$obREmpenhoPagamentoLiquidacao  = new REmpenhoPagamentoLiquidacao;
$rsListaEstorno               = new RecordSet;

$obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade   ( $_REQUEST["inCodigoEntidade"] );
$obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setCodigoOrdem                            ( $_REQUEST["inCodigoOrdem"]    );
$obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setExercicio                              ( $_REQUEST["stExercicio"]      );
$obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->consultar();
$boRetencao     = $obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getRetencao();
$boAdiantamento = $obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getAdiantamento();
$obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->listarItensEstorno                        ( $rsListaEstorno               );

$rsListaEstorno->addFormatacao("vl_pago"            , "NUMERIC_BR");
$rsListaEstorno->addFormatacao("vl_pagonaoprestado" , "NUMERIC_BR");
$rsListaEstorno->addFormatacao("vl_pagamento"       , "NUMERIC_BR");

switch ($_POST["stCtrl"]) {

    case "montaLista":
        montaListaPagamento($rsListaEstorno);
    break;

    case "buscaContaBanco":
    if ($_POST['inCodContaBanco'] != "") {
        $obREmpenhoPagamentoLiquidacao->obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST['inCodContaBanco'] );
        $obREmpenhoPagamentoLiquidacao->obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
        $obREmpenhoPagamentoLiquidacao->obRContabilidadePlanoContaAnalitica->consultar();
        $stNomContaBanco = $obREmpenhoPagamentoLiquidacao->obRContabilidadePlanoContaAnalitica->getNomConta();
        if (!$stNomContaBanco) {
            $js .= 'f.inCodContaBanco.value = "";';
            $js .= 'f.inCodContaBanco.focus();';
            $js .= 'd.getElementById("stContaBanco").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodContaBanco"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("stContaBanco").innerHTML = "'.$stNomContaBanco.'";';
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
}

function montaListaPagamento($rsListaEstorno , $boRetorna = false)
{
    global $boRetencao;
    global $boAdiantamento;
    if ( $rsListaEstorno->getNumLinhas() != 0 ) {
        $obLista3 = new Lista;
        $obLista3->setRecordSet                 ( $rsListaEstorno    );
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
        $obLista3->ultimoCabecalho->addConteudo ( "Data Pagamento"     );
        $obLista3->ultimoCabecalho->setWidth    ( 15                   );
        $obLista3->commitCabecalho              (                      );
        $obLista3->addCabecalho                 (                      );
        $obLista3->ultimoCabecalho->addConteudo ( "Conta Banco"        );
        $obLista3->ultimoCabecalho->setWidth    ( 15                   );
        $obLista3->commitCabecalho              (                      );
        $obLista3->addCabecalho                 (                      );
        $obLista3->ultimoCabecalho->addConteudo ( "Valor a estornar"   );
        $obLista3->ultimoCabecalho->setWidth    ( 20                   );
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
        $obLista3->addDado                      (                               );
        $obLista3->ultimoDado->setCampo         ( "dt_pagamento"                );
        $obLista3->ultimoDado->setAlinhamento   ( "CENTRO"                      );
        $obLista3->commitDado                   (                               );
        $obLista3->addDado                      (                               );
        $obLista3->ultimoDado->setCampo         ( "cod_plano"                   );
        $obLista3->ultimoDado->setAlinhamento   ( "CENTRO"                      );
        $obLista3->commitDado                   (                               );

        $obTxtVlPagamento = new Moeda();
        $obTxtVlPagamento->setName   ( 'nuVlPagamento_[cod_nota]_[ex_nota]_[dt_pagamento]_' );
        $obTxtVlPagamento->setSize   ( 20               );
        $obTxtVlPagamento->setValue  ( '[vl_pagonaoprestado]' );

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
    $inCodRecurso = $rsListaEstorno->getCampo("cod_recurso");

    $js .= "d.getElementById('spnListaItem').innerHTML = '".$stHTML."';\n";
    $js .= "f.inCodigoRecurso.value= '".$inCodRecurso."';";
    $js .= "f.nuValorPrestado.value= '".$rsListaEstorno->getCampo('vl_prestado')."';";

    if($boRetencao)
        SistemaLegado::exibeAviso('Esta OP possui retenções: O Estorno não poderá ser parcial.','','');
    if($boAdiantamento)
        SistemaLegado::exibeAviso('Esta OP é de adiantamentos/subvenções: O Estorno não poderá ser parcial.','','');
    if ($boRetorna) {
        return $js;
    } else {
        SistemaLegado::executaFrameOculto($js);
    }
}

?>
