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
    * Data de Criação   : 16/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: OCManterOrdemPagamento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.03.05
                    uc-02.03.20
                    uc-02.03.28
                    uc-02.03.31
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoOrdemPagamento.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoNotaLiquidacao.class.php";

$stPrograma      = "ManterOrdemPagamento";
$pgJs            = "JS".$stPrograma.".js";

if(empty($js))
    $js = "";

if (Sessao::read('stEmitirCarneOp') == '') {
    $stEmitirCarneOp = SistemaLegado::pegaDado('valor', 'administracao.configuracao', "WHERE exercicio='".Sessao::getExercicio()."' AND cod_modulo=".Sessao::getModulo()." AND parametro='emitir_carne_op'");
    Sessao::write('stEmitirCarneOp', $stEmitirCarneOp);
}

if ($_REQUEST['stCtrl'] != "buscaReceitas") {
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once $pgJs;
}

function montaRetencoes($boRetorna = false)
{
        $obFormulario = new Formulario;

        $obNao = new Radio;
        $obNao->setName   ("rdRetencao");
        $obNao->setId     ("boRetencaoN");
        $obNao->setTitle  ( 'Selecione a Inclusão de Retenções');
        $obNao->setRotulo ("Retenções");
        $obNao->setLabel  ("Não");
        $obNao->setChecked( true );
        $obNao->setValue  ( "N" );
        $obNao->obEvento->setOnClick("buscaDado('limpaContaValorRetencao');");

        $obRetOrcamentarias = new Radio;
        $obRetOrcamentarias->setName   ("rdRetencao");
        $obRetOrcamentarias->setId     ("boRetencaoO");
        $obRetOrcamentarias->setTitle  ( 'Selecione a Inclusão de Retenções');
        $obRetOrcamentarias->setRotulo ("Retenções");
        $obRetOrcamentarias->setLabel  ("Orçamentárias");
        $obRetOrcamentarias->setValue  ("O");
        $obRetOrcamentarias->setChecked( false );
        $obRetOrcamentarias->obEvento->setOnClick("buscaDado('montaContaValorRetencao');");

        $obRetExtraOrc = new Radio;
        $obRetExtraOrc->setName   ("rdRetencao");
        $obRetExtraOrc->setId     ("boRetencaoE");
        $obRetExtraOrc->setTitle  ( 'Selecione a Inclusão de Retenções');
        $obRetExtraOrc->setRotulo ("Retenções");
        $obRetExtraOrc->setLabel  ("Extra-Orçamentárias");
        $obRetExtraOrc->setValue  ("E");
        $obRetExtraOrc->setChecked( false );
        $obRetExtraOrc->obEvento->setOnClick("buscaDado('montaContaValorRetencao');");

        $obSpnContaValor = new Span;
        $obSpnContaValor->setId ('spnContaValorRetencao');

        $obSpnListaRetencao = new Span;
        $obSpnListaRetencao->setId ('spnListaRetencao');

        $obFormulario->agrupaComponentes ( array( $obNao, $obRetOrcamentarias, $obRetExtraOrc) );
        $obFormulario->addSpan       ( $obSpnContaValor  );
        $obFormulario->addSpan       ( $obSpnListaRetencao );

        $obFormulario->montaInnerHTML();
        $js = "d.getElementById('spnRetencoes').innerHTML = '".$obFormulario->getHTML()."';";

    if ($boRetorna) {
        return $js;
    } else {
        SistemaLegado::executaiFrameOculto($js);
    }

}

function montaListaRetencao($arListaRetencao)
{
        $inCountExt = 0;
        $inCountOrc = 0;
        $stHtmlListaOrc = '';
        $stHtmlListaExt = '';
        foreach ($arListaRetencao as $item) {
            if ($item['stTipo'] == 'O') {
                $arTmpRetOrc[$inCountOrc] = $item;
                $inCountOrc++;
            }
            if ($item['stTipo'] == 'E') {
                $arTmpRetExt[$inCountExt] = $item;
                $inCountExt++;
            }
        }
        if ($arTmpRetOrc) {
            $rsListaRetencaoOrc = new RecordSet;
            $rsListaRetencaoOrc->preenche( $arTmpRetOrc );

            $obLista = new Lista;
            $obLista->setRecordSet                 ( $rsListaRetencaoOrc  );
            $obLista->setTitulo                    ( "Retenções Orçamentárias" );
            $obLista->setMostraPaginacao           ( false                );
            $obLista->addCabecalho                 (                      );
            $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"             );
            $obLista->ultimoCabecalho->setWidth    ( 5                    );
            $obLista->commitCabecalho              (                      );
            $obLista->addCabecalho                 (                      );
            $obLista->ultimoCabecalho->addConteudo ( "Cód. Reduzido"      );
            $obLista->ultimoCabecalho->setWidth    ( 5                    );
            $obLista->commitCabecalho              (                      );
            $obLista->addCabecalho                 (                      );
            $obLista->ultimoCabecalho->addConteudo ( "Descrição"          );
            $obLista->ultimoCabecalho->setWidth    ( 59                   );
            $obLista->commitCabecalho              (                      );
            $obLista->addCabecalho                 (                      );
            if (Sessao::read('stEmitirCarneOp') == 'true') {
                $obLista->ultimoCabecalho->addConteudo ( "Crédito"            );
                $obLista->ultimoCabecalho->setWidth    ( 59                   );
                $obLista->commitCabecalho              (                      );
                $obLista->addCabecalho                 (                      );
            }
            $obLista->ultimoCabecalho->addConteudo ( "Valor Retenção"     );
            $obLista->ultimoCabecalho->setWidth    ( 14                   );
            $obLista->commitCabecalho              (                      );
            $obLista->addCabecalho                 (                      );
            $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"         );
            $obLista->ultimoCabecalho->setWidth    ( 5                );
            $obLista->commitCabecalho              (                  );
            $obLista->addDado                      (                      );
            $obLista->ultimoDado->setCampo         ( "[cod_reduzido]" );
            $obLista->ultimoDado->setAlinhamento   ( "DIREITA"            );
            $obLista->commitDado                   (                      );
            $obLista->addDado                      (                      );
            $obLista->ultimoDado->setCampo         ( "nom_conta"          );
            $obLista->ultimoDado->setAlinhamento   ( "ESQUERDA"           );
            $obLista->commitDado                   (                      );
            $obLista->addDado                      (                      );
            if (Sessao::read('stEmitirCarneOp') == 'true') {
                $obLista->ultimoDado->setCampo         ( "creditoDesc"        );
                $obLista->ultimoDado->setAlinhamento   ( "ESQUERDA"           );
                $obLista->commitDado                   (                      );
                $obLista->addDado                      (                      );
            }
            $obLista->ultimoDado->setCampo         ( "nuValor"            );
            $obLista->ultimoDado->setAlinhamento   ( "DIREITA"            );
            $obLista->commitDado                   (                      );
            $obLista->addAcao                      (                      );
            $obLista->ultimaAcao->setAcao          ( "EXCLUIR"            );
            $obLista->ultimaAcao->setFuncao        ( true                 );
            $obLista->ultimaAcao->setLink          ( "JavaScript:excluirItemRetencao();" );
            $obLista->ultimaAcao->addCampo         ( "inId","inId" );
            $obLista->commitAcao                   (                      );
            $obLista->montaInnerHTML();
            $stHtmlListaOrc = $obLista->getHTML();
        }

        if ($arTmpRetExt) {
            $rsListaRetencaoExt = new RecordSet;
            $rsListaRetencaoExt->preenche( $arTmpRetExt );

            $obLista = new Lista;
            $obLista->setRecordSet                 ( $rsListaRetencaoExt  );
            $obLista->setTitulo                    ( "Retenções Extra-Orçamentárias" );
            $obLista->setMostraPaginacao           ( false                );
            $obLista->addCabecalho                 (                      );
            $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"             );
            $obLista->ultimoCabecalho->setWidth    ( 5                    );
            $obLista->commitCabecalho              (                      );
            $obLista->addCabecalho                 (                      );
            $obLista->ultimoCabecalho->addConteudo ( "Cód. Reduzido"      );
            $obLista->ultimoCabecalho->setWidth    ( 15                   );
            $obLista->commitCabecalho              (                      );
            $obLista->addCabecalho                 (                      );
            $obLista->ultimoCabecalho->addConteudo ( "Descrição"          );
            $obLista->ultimoCabecalho->setWidth    ( 59                   );
            $obLista->commitCabecalho              (                      );
            $obLista->addCabecalho                 (                      );
            $obLista->ultimoCabecalho->addConteudo ( "Valor Retenção"     );
            $obLista->ultimoCabecalho->setWidth    ( 14                   );
            $obLista->commitCabecalho              (                      );
            $obLista->addCabecalho                 (                      );
            $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"         );
            $obLista->ultimoCabecalho->setWidth    ( 5                );
            $obLista->commitCabecalho              (                  );
            $obLista->addDado                      (                      );
            $obLista->ultimoDado->setCampo         ( "[cod_reduzido]" );
            $obLista->ultimoDado->setAlinhamento   ( "DIREITA"            );
            $obLista->commitDado                   (                      );
            $obLista->addDado                      (                      );
            $obLista->ultimoDado->setCampo         ( "nom_conta"          );
            $obLista->ultimoDado->setAlinhamento   ( "ESQUERDA"           );
            $obLista->commitDado                   (                      );
            $obLista->addDado                      (                      );
            $obLista->ultimoDado->setCampo         ( "nuValor"            );
            $obLista->ultimoDado->setAlinhamento   ( "DIREITA"            );
            $obLista->commitDado                   (                      );
            $obLista->addAcao                      (                      );
            $obLista->ultimaAcao->setAcao          ( "EXCLUIR"            );
            $obLista->ultimaAcao->setFuncao        ( true                 );
            $obLista->ultimaAcao->setLink          ( "JavaScript:excluirItemRetencao();" );
            $obLista->ultimaAcao->addCampo         ( "inId","inId" );
            $obLista->commitAcao                   (                      );
            $obLista->montaInnerHTML();
            $stHtmlListaExt = $obLista->getHTML();
        }

        $obFormulario = New Formulario;

        $arTlValorRetencao = Sessao::read('nuTlValorRetencao');
        $obLblValor = new Label;
        $obLblValor->setRotulo   ( "Total Retenções" );
        $obLblValor->setId       ( 'lblTlValorRetencao' );
        $obLblValor->setValue    ( number_format($arTlValorRetencao,2,',','.') );

        $obHdnValor = new Hidden;
        $obHdnValor->setName     ( "nuTlValorRetencao" );
        $obHdnValor->setId       ( "nuTlValorRetencao" );
        $obHdnValor->setValue    ( number_format($arTlValorRetencao,2,',','.') );

        $obFormulario->addHidden    ( $obHdnValor );
        $obFormulario->addComponente( $obLblValor );
        $obFormulario->montaInnerHTML();
        $stJs = "d.getElementById('spnListaRetencao').innerHTML = '".$stHtmlListaOrc."".$stHtmlListaExt."".$obFormulario->getHTML()."'; ";

        return $stJs;
}

function montaListaLiquidacao($rsListaLiquidacao , $newValorTotal , $newValorAnulado , $valorParaAnular , $cgmFornecedor , $boRetorna = false)
{
    global $boRetencao;
    if ( $rsListaLiquidacao->getNumLinhas() != 0 ) {

        $obLista3 = new Lista;
        $obLista3->setRecordSet                 ( $rsListaLiquidacao   );
        $obLista3->setTitulo                    ( "Registros"          );
        $obLista3->setMostraPaginacao           ( false                );
        $obLista3->addCabecalho                 (                      );
        $obLista3->ultimoCabecalho->addConteudo ( "&nbsp;"             );
        $obLista3->ultimoCabecalho->setWidth    ( 5                    );
        $obLista3->commitCabecalho              (                      );
        $obLista3->addCabecalho                 (                      );
        $obLista3->ultimoCabecalho->addConteudo ( "Empenho"            );
        $obLista3->ultimoCabecalho->setWidth    ( 15                   );
        $obLista3->commitCabecalho              (                      );
        $obLista3->addCabecalho                 (                      );
        $obLista3->ultimoCabecalho->addConteudo ( "Data do Empenho"    );
        $obLista3->ultimoCabecalho->setWidth    ( 15                   );
        $obLista3->commitCabecalho              (                      );
        $obLista3->addCabecalho                 (                      );
        $obLista3->ultimoCabecalho->addConteudo ( "Liquidação"         );
        $obLista3->ultimoCabecalho->setWidth    ( 15                   );
        $obLista3->commitCabecalho              (                      );
        $obLista3->addCabecalho                 (                      );
        $obLista3->ultimoCabecalho->addConteudo ( "Data da Liquidação" );
        $obLista3->ultimoCabecalho->setWidth    ( 15                   );
        $obLista3->commitCabecalho              (                      );
        if ($_REQUEST["stAcao"] == "anular") {
            $obLista3->addCabecalho                 (                      );
            $obLista3->ultimoCabecalho->addConteudo ( "Valor da O.P."      );
            $obLista3->ultimoCabecalho->setWidth    ( 20                   );
            $obLista3->commitCabecalho              (                      );

            $obLista3->addCabecalho                 (                      );
            $obLista3->ultimoCabecalho->addConteudo ( "Valor a Anular"      );
            $obLista3->ultimoCabecalho->setWidth    ( 20                   );
            $obLista3->commitCabecalho              (                      );

        } else {
            $obLista3->addCabecalho                 (                      );
            $obLista3->ultimoCabecalho->addConteudo ( "Valor a Pagar"      );
            $obLista3->ultimoCabecalho->setWidth    ( 30                   );
            $obLista3->commitCabecalho              (                      );
        }
        if ($_REQUEST["stAcao"] == "incluir") {
            $obLista3->addCabecalho                 (                  );
            $obLista3->ultimoCabecalho->addConteudo ( "&nbsp;"         );
            $obLista3->ultimoCabecalho->setWidth    ( 5                );
            $obLista3->commitCabecalho              (                  );
        }

        $obLista3->addDado                      (                      );
        $obLista3->ultimoDado->setCampo         ( "[cod_empenho]/[ex_empenho]" );
        $obLista3->ultimoDado->setAlinhamento   ( "DIREITA"            );
        $obLista3->commitDado                   (                      );
        $obLista3->addDado                      (                      );
        $obLista3->ultimoDado->setCampo         ( "dt_empenho"         );
        $obLista3->ultimoDado->setAlinhamento   ( "CENTRO"             );
        $obLista3->commitDado                   (                      );
        $obLista3->addDado                      (                      );
        $obLista3->ultimoDado->setCampo         ( "[cod_nota]/[ex_nota]" );
        $obLista3->ultimoDado->setAlinhamento   ( "DIREITA"            );
        $obLista3->commitDado                   (                      );
        $obLista3->addDado                      (                      );
        $obLista3->ultimoDado->setCampo         ( "dt_nota"            );
        $obLista3->ultimoDado->setAlinhamento   ( "CENTRO"             );
        $obLista3->commitDado                   (                      );

        if ($_REQUEST['stAcao'] == 'anular') {

            $obLista3->addDado                      (                      );
            $obLista3->ultimoDado->setCampo         ( "valor_pagar"        );
            $obLista3->ultimoDado->setAlinhamento   ( "DIREITA"            );
            $obLista3->commitDado                   (                      );

            // Define Objeto Numerico para Valor
            $obTxtValor = new Numerico;
            $obTxtValor->setName     ( "nuValor_[cod_nota]_" );
            $obTxtValor->setId       ( "nuValor" );
            $obTxtValor->setAlign    ( 'RIGHT');
            $obTxtValor->setTitle    ( "" );
            $obTxtValor->setMaxLength( 19 );
            $obTxtValor->setSize     ( 21 );
            $obTxtValor->setValue    ( "valor_pagar" );
            if($boRetencao)
              $obTxtValor->setReadOnly ( true );

            $obLista3->addDadoComponente( $obTxtValor );
            $obLista3->ultimoDado->setAlinhamento( 'CSS' );
            $obLista3->ultimoDado->setClass( 'show_dados_center' );
            $obLista3->commitDadoComponente();

        } else {
            $obLista3->addDado                      (                      );
            $obLista3->ultimoDado->setCampo         ( "valor_pagar"        );
            $obLista3->ultimoDado->setAlinhamento   ( "DIREITA"            );
            $obLista3->commitDado                   (                      );
        }

        if ($_REQUEST["stAcao"] == "incluir") {
            $obLista3->addAcao                      (                      );
            $obLista3->ultimaAcao->setAcao          ( "EXCLUIR"            );
            $obLista3->ultimaAcao->setFuncao        ( true                 );
            $obLista3->ultimaAcao->setLink   ( "JavaScript:excluirItem();" );
            $obLista3->ultimaAcao->addCampo        ( "inIndice","cod_nota" );
            $obLista3->commitAcao                   (                      );
        }

        $obLista3->montaHTML                     (                      );
        $stHTML =  $obLista3->getHtml            (                      );
        $stHTML = str_replace                   ( "\n","",$stHTML      );
        $stHTML = str_replace                   ( chr(13),"<br>",$stHTML      );
        $stHTML = str_replace                   ( "  ","",$stHTML      );
        $stHTML = str_replace                   ( "'","\\'",$stHTML    );
    } else {
        $stHTML = "&nbsp";
    }

    while ( !$rsListaLiquidacao->eof() ) {
        $data = $rsListaLiquidacao->getCampo("dt_nota");
        if (SistemaLegado::comparaDatas($rsListaLiquidacao->getCampo("dt_nota"),$stDtUltimaLiquidacao)) {
            $stDtUltimaLiquidacao = $rsListaLiquidacao->getCampo("dt_nota");
        }
        $rsListaLiquidacao->proximo();
    }

    Sessao::write('dtUltimaLiquidacao', $stDtUltimaLiquidacao);

    $js .= "d.getElementById('spnListaItem').innerHTML = '".$stHTML."';\n";
    $js .= "if( d.frm.flValorTotal ) d.frm.flValorTotal.value = '$newValorTotal';";
    if ($_REQUEST["stAcao"] == "incluir") {
        $js .= "d.frm.stFornecedor.value = '".$cgmFornecedor."';";
    } else {
        $js .= "if( d.frm.flValorAnulado ) d.frm.flValorAnulado.value = '$newValorAnulado';";
    }

    if ($boRetencao) {
        if($_REQUEST['stAcao'] == 'anular') SistemaLegado::exibeAvisoTelaPrincipal('Esta OP possui retenções: A Anulação não poderá ser parcial.','','');
    }

    if ($boRetorna) {
        return $js;
    } else {
        SistemaLegado::executaiFrameOculto($js);
    }
}

$stCtrl = $_REQUEST['stCtrl'];

switch ($_REQUEST ["stCtrl"]) {

    case "buscaLiquidacoes":
        $obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
        $js .= "f = parent.frames['telaPrincipal'].document.frm;\n";
        $js .= "d = parent.frames['telaPrincipal'].document;\n";
        $js .= "limpaSelect(f.cmbLiquidacao,0); \n";
        $js .= "f.cmbLiquidacao[0] = new Option('Selecione','', 'selected');\n";
        if ($_REQUEST["inCodigoEmpenho"] && $_REQUEST["inCodEntidade"]) {
            $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setCodEmpenho( $_REQUEST["inCodigoEmpenho"]);
        $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodEntidade"]);
            $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setExercicio( $_REQUEST['stExercicioEmpenho'] );
            $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->checarImplantado( $boImplantado );
            $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->retornaCategoria( $inCodCategoria );
            if ($boImplantado) {
                $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->listarNotasAPagarDisponiveisImplantadas( $rsLiquidacoes );
            } else {
                $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->listarNotasDisponiveis( $rsLiquidacoes );
            }

            $inContador = 1;
            while ( !$rsLiquidacoes->eof() ) {
                if ( $rsLiquidacoes->getCampo("cod_empenho") == $_REQUEST["inCodigoEmpenho"] ) {

                    $flValorNota        = $rsLiquidacoes->getCampo( "vl_nota" );
                    $flValorNotaTMP     = str_replace( '.','',$flValorNota );
                    $flValorNotaTMP     = str_replace( ',','.',$flValorNotaTMP );
                    if ($flValorNotaTMP > 0) {
                        $inCodigoLiquidacao = $rsLiquidacoes->getCampo( "cod_nota"          );
                        $inCodRecurso       = $rsLiquidacoes->getCampo( "cod_recurso"       );
                        $exercicioNota      = $rsLiquidacoes->getCampo( "exercicio_nota"    );
                        $dtDataLiquidacao   = $rsLiquidacoes->getCampo( "dt_liquidacao"     );
                        $inCodigoEmpenho    = $rsLiquidacoes->getCampo( "cod_empenho"       );
                        $stDescricao        = $rsLiquidacoes->getCampo( "descricao"         );
                        $dtDataEmpenho      = $rsLiquidacoes->getCampo( "dt_empenho"        );
                        $exercicioEmpenho   = $rsLiquidacoes->getCampo( "exercicio_empenho" );
                        $numCGM             = $rsLiquidacoes->getCampo( "cgm_beneficiario"  );
                        $nomeCGM            = str_replace("'","\'",$rsLiquidacoes->getCampo( "beneficiario"));
                        $boImplantado       = $rsLiquidacoes->getCampo( "implantado"        );
                        $nuVlOrdemPago      = $rsLiquidacoes->getCampo("vl_ordem") - $rsLiquidacoes->getCampo("vl_ordem_anulada");
                        $nuVlLiquidado      = $rsLiquidacoes->getCampo( "vl_itens" ) - $rsLiquidacoes->getCampo( "vl_itens_anulados" );
                        $nuVlPago           = $rsLiquidacoes->getCampo( "vl_pago" ) - $rsLiquidacoes->getCampo( "vl_pago_anulado" );

                        $stDescricao = str_replace (array("\n","\r","\t"),array(" ","",""),$stDescricao);
                        $stDescricao = str_replace("'", "\'", $stDescricao);

                        Sessao::write('stDescricaoLiquidacao', $stDescricao);
                        $nuVlAPagar = $nuVlLiquidado - $nuVlOrdemPago;
                        $nuVlAPagar         = number_format( $nuVlAPagar, 2, ',','.' );
                        $mixCombo = $inCodigoLiquidacao." - ".$dtDataLiquidacao;
                        $mixComboValor = $inCodigoLiquidacao."||".$dtDataLiquidacao."||".$nuVlAPagar."||".$inCodigoEmpenho."||".$dtDataEmpenho."||".$exercicioEmpenho."||".$numCGM."||".$nomeCGM."||".$exercicioNota."||".$boImplantado."||".$inCodRecurso."||".$inCodCategoria;
                        $js .= "f.cmbLiquidacao.options[$inContador] = new Option('".$mixCombo."','".$mixComboValor."'); \n";
                        $inContador++;
                    }
                }
                $rsLiquidacoes->proximo();
            }

            if ($rsLiquidacoes->inNumLinhas < 1) {
                    $js .= "alertaAvisoTelaPrincipal('Número do Empenho é inválido (" . $_REQUEST["inCodigoEmpenho"] . ").','form','erro','" . Sessao::getId() . "', '../');";
                    $js .= "f.inCodigoEmpenho.value='';";
                    $js .= "f.flValorPagar.value='';";
                    $js .= "d.getElementById('stDescEmpenho').innerHTML='&nbsp;';";

            }

            $rsLiquidacoes->anterior();
            $stFornecedor = ( $rsLiquidacoes->getCampo('beneficiario') ) ? str_replace("'","\'",$rsLiquidacoes->getCampo( "beneficiario"      )): '&nbsp;';
            $js .= "d.getElementById('stDescEmpenho').innerHTML='".$stFornecedor."';";
        } else {
            $js .= "f.inCodigoEmpenho.value='';";
            $js .= "d.getElementById('stDescEmpenho').innerHTML='&nbsp;';";
            $js .= "f.flValorPagar.value='';";
            if ($_REQUEST["inCodEntidade"] == "") {
                $stMensagem = "Digite um Número do Empenho para a Entidade Selecionada.";
                $js .= "alertaAvisoTelaPrincipal('".$stMensagem."','form','erro','" . Sessao::getId() . "', '../');";
            }
        }

        SistemaLegado::executaiFrameOculto($js);
    break;

    case "incluirItem":
        $mixLiquidacao = explode("||", $_REQUEST["cmbLiquidacao"]);
        $inCodigoLiquidacao = $mixLiquidacao[0];
        $dtDataLiquidacao   = $mixLiquidacao[1];
        $flValorNota        = $mixLiquidacao[2];
        $inCodigoEmpenho    = $mixLiquidacao[3];
        $dtDataEmpenho      = $mixLiquidacao[4];
        $exercicioEmpenho   = $mixLiquidacao[5];
        $inNumCGM           = $mixLiquidacao[6];
        $stNomeCGM          = $mixLiquidacao[7];
        $exercicioNota      = $mixLiquidacao[8];
        $boImplantado       = $mixLiquidacao[9];
        $inCodRecurso       = $mixLiquidacao[10];
        $inCodCategoria     = $mixLiquidacao[11];
        $stDescricao        = Sessao::read('stDescricaoLiquidacao');
        $stInsere = false;

        $arItens = Sessao::read('itemOrdem');

        if ($inCodCategoria == 2 || $inCodCategoria == 3) {
            $boAdiantamento = 't';
        }
        if ($arItens) {
            $inCountSessao = count ($arItens);
        } else {
            $inCountSessao = 0;
            $stInsere = true;
        }

        for ($iCount = 0; $iCount < $inCountSessao; $iCount++) {

            $stInsere = true;
            if ($arItens[$iCount]["num_cgm"] != $inNumCGM) {
                $obErro = new Erro;
                $obErro->setDescricao( "As notas de liquidação informadas devem ser do mesmo fornecedor!" );
                $stInsere = false;

            } elseif ($arItens[$iCount]["cod_nota"]    == $inCodigoLiquidacao) {
                $obErro = new Erro;
                $obErro->setDescricao( "Nota de liquidação já informada!" );
                $stInsere = false;

            } elseif ($arItens[$iCount]["cod_recurso"] != $inCodRecurso) {
                $obErro = new Erro;
                $obErro->setDescricao( "As notas de liquidação informadas devem ser do mesmo recurso!" );
                $stInsere = false;
            }

            if ($stInsere == false) {
                $iCount = $inCountSessao;
                $stJs .= "alertaAvisoTelaPrincipal('".urlencode($obErro->getDescricao())."','n_incluir','erro','" . Sessao::getId() . "', '../');";
            }

            if ( isset($arItens) && $arItens[$iCount]["cod_empenho"] != $inCodigoEmpenho && $stInsere ) {
                echo "<script> avisoRetencao('Para Retenções, inclua apenas uma Liquidação de um Empenho' ); </script>";
                $stJs .= "d.getElementById('boRetencaoN').checked = true;\n";
                $stJs .= "d.getElementById('boRetencaoN').disabled = true;\n";
                $stJs .= "d.getElementById('boRetencaoO').disabled = true;\n";
                $stJs .= "d.getElementById('boRetencaoE').disabled = true;\n";
                $stJs .= "d.getElementById('spnContaValorRetencao').innerHTML = '';";
                $stJs .= "d.getElementById('spnListaRetencao').innerHTML = '';";
                Sessao::remove('itemRetencao');
                Sessao::remove('nuTlValorRetencao');
                $boRetencao = false;
            }   else $boRetencao = true;

            if ( isset($arItens) && $stInsere) {
                echo "<script> avisoRetencao('Para Retenções, inclua apenas uma Liquidação de um Empenho' ); </script>";
                $stJs .= "d.getElementById('boRetencaoN').checked = true;\n";
                $stJs .= "d.getElementById('boRetencaoN').disabled = true;\n";
                $stJs .= "d.getElementById('boRetencaoO').disabled = true;\n";
                $stJs .= "d.getElementById('boRetencaoE').disabled = true;\n";
                $stJs .= "d.getElementById('spnContaValorRetencao').innerHTML = '';";
                $stJs .= "d.getElementById('spnListaRetencao').innerHTML = '';";
                Sessao::remove('itemRetencao');
                Sessao::remove('nuTlValorRetencao');
                $boRetencao = false;
            }   else $boRetencao = true;

        }
        if ( str_replace('.','',str_replace(',','.',$flValorNota)) < str_replace('.','',str_replace(',','.',$_REQUEST['flValorPagar']))) {
            $obErro = new Erro;
            $obErro->setDescricao( "O Valor a Pagar não pode ser maior que o Valor da Nota!" );
            SistemaLegado::exibeAvisoTelaPrincipal(urlencode($obErro->getDescricao()),"n_incluir","erro");
            $stInsere = false;
        }

        $arAdiantamento = Sessao::read('boAdiantamento');
        $arImplantado = Sessao::read('bo_implantado');
        $arValorTotalOrdem = Sessao::read('valorTotalOrdem');
        $arCgmFornecedor = Sessao::read('cgmFornecedor');

        if ( ( $boAdiantamento == 't' || $arAdiantamento == 't' ) && ( $inCountSessao > 0 ) ) {
            $obErro = new Erro;
            $obErro->setDescricao( "Não é possível informar mais de um empenho na ordem quando a categoria for Adiantamentos/Subvenções!" );
            SistemaLegado::exibeAvisoTelaPrincipal(urlencode($obErro->getDescricao()),"n_incluir","erro");
            $stInsere = false;
        }

        if ($stInsere) {
            if ($arItens) {
               $inLast = count ($arItens);
            } else {
                $inLast = 0;
                $arItens = array ();
                $arValorTotalOrdem = 0;
                $arCgmFornecedor = "";
            }
            $arItens[$inLast]["cod_empenho"    ] = $inCodigoEmpenho;
            $arItens[$inLast]["dt_empenho"     ] = $dtDataEmpenho;
            $arItens[$inLast]["ex_empenho"     ] = $exercicioEmpenho;
            $arItens[$inLast]["cod_nota"       ] = $inCodigoLiquidacao;
            $arItens[$inLast]["ex_nota"        ] = $exercicioNota;
            $arItens[$inLast]["dt_nota"        ] = $dtDataLiquidacao;
            $arItens[$inLast]["valor_pagar"    ] = $_REQUEST['flValorPagar'];
            $arItens[$inLast]["max_valor_pagar"] = $flValorNota;
            $arItens[$inLast]["num_cgm"        ] = $inNumCGM;
            $arItens[$inLast]["nom_cgm"        ] = $stNomeCGM;
            $arItens[$inLast]["cod_recurso"    ] = $inCodRecurso;
            $arItens[$inLast]["cod_categoria"  ] = $inCodCategoria;
            $arItens[$inLast]["descricao"      ] = $stDescricao;
            $arImplantado = $boImplantado;

            if ($boAdiantamento == 't') {
                $arAdiantamento = $boAdiantamento;
            }

            $somaTemp = str_replace(".","",$_REQUEST['flValorPagar']);
            $somatorio = str_replace(",",".",$somaTemp);
            $arValorTotalOrdem += $somatorio;
            $newValorTotal = number_format($arValorTotalOrdem, 2, ',', '.');

            Sessao::write('itemOrdem', $arItens);
            $arCgmFornecedor = $inNumCGM." - ".$stNomeCGM;
        } else {
            $newValorTotal = number_format($arValorTotalOrdem, 2, ',', '.');
        }
        $rsListaItemOrdem = new RecordSet;
        $rsListaItemOrdem->preenche ( $arItens );
        $rsListaItemOrdem->ordena("cod_nota");
        $stJs .= montaListaLiquidacao( $rsListaItemOrdem , $newValorTotal , "" , "" , $arCgmFornecedor,true );

        if (($rsListaItemOrdem->getNumLinhas())=='1') {
            if ($stInsere)
                $stJs .= "f.stDescricaoOrdem.value = '".$stDescricao."';";
        } else {
            if (($arItens[$inLast]["cod_empenho"] != $arItens[$inLast-1]["cod_empenho"])
            and ($arItens[$inLast]["cod_empenho"] != $arItens[0]["cod_empenho"])) {
                $stJs .= "f.stDescricaoOrdem.value = 'Referente pagamento de liquidações de diversos Empenhos, conforme relacionados acima.';";
            }
        }

        $arItemRetencao = Sessao::read('itemRetencao');
        if(!isset($arItemRetencao) && ($boRetencao || $inLast == 0))
            $stJs .= montaRetencoes (true);

        if ( count($arItens) == 1 ) {
            $stJs .= "f.inCodEntidade.readOnly = true; \n";
            $stJs .= "var combo = d.getElementById('stNomeEntidade'); \n";
            $stJs .= "var stCodigo  = \"window.parent.frames['telaPrincipal'].document.getElementById('inCodEntidade').focus(); \";\n";
            $stJs .= "var stCodigo2 = \"alertaAvisoTelaPrincipal('A Entidade não pode ser alterada para os Itens cadastrados!()','form','erro','".Sessao::getId()."', '../'); \";\n";
            $stJs .= "var stCodigo3 = \"window.parent.frames['telaPrincipal'].document.frm.stNomeEntidade.value='".$_REQUEST['inCodEntidade']."';\";\n";
            $stJs .= "combo.setAttribute('onchange',stCodigo); \n";
            $stJs .= "combo.setAttribute('onclick', stCodigo + stCodigo2); \n";
            $stJs .= "combo.setAttribute('onblur', stCodigo3); \n";
        }
        Sessao::write('boAdiantamento', $arAdiantamento);
        Sessao::write('bo_implantado', $arImplantado);
        Sessao::write('valorTotalOrdem', $arValorTotalOrdem);
        Sessao::write('cgmFornecedor', $arCgmFornecedor);

        $stJs .= "d.frm.Ok.disabled = false; \n"; // Habilita novamente o botão Ok, já com a lista pronta.

    case "limparItem":
        $stJs .= "limpaSelect(f.cmbLiquidacao,0); \n";
        $stJs .= "d.frm.flValorPagar.value = '';";
        $stJs .= "d.frm.inCodigoEmpenho.value = '';";
        $stJs .= "d.getElementById('stDescEmpenho').innerHTML = '&nbsp;';";
        SistemaLegado::executaiFrameOculto($stJs);
    break;

    case "limparRetencoes":
        $stJs .= "d.frm.inCodPlanoRetencao.value = '';";
        $stJs .= "d.getElementById('stNomContaRetencao').innerHTML = '&nbsp;';";
        $stJs .= "d.getElementById('nuValorRetencao').value = '';";
        SistemaLegado::executaiFrameOculto($stJs);
    break;

    case "recuperaItem":
        include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamentoLiquidacaoAnulada.class.php" );
        $obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
        $obREmpenhoOrdemPagamento->setCodigoOrdem($_REQUEST["hdnCodigoOrdem"]);
        $obREmpenhoOrdemPagamento->setExercicio($_REQUEST["hdnExercicioOrdem"]);
        $obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade($_REQUEST["hdnCodigoEntidade"]);
        $obREmpenhoOrdemPagamento->consultar();
        $boRetencao = $obREmpenhoOrdemPagamento->getRetencao();
        $obREmpenhoOrdemPagamento->listarItem($rsItens);

        $stAcao = $request->get('stAcao');

        if ($stAcao == 'anular') {
            $obTEmpenhoOrdemPagamentoLiquidacaoAnulada = new TEmpenhoOrdemPagamentoLiquidacaoAnulada();
            $obTEmpenhoOrdemPagamentoLiquidacaoAnulada->setDado( 'cod_ordem'   , $obREmpenhoOrdemPagamento->getCodigoOrdem() );
            $obTEmpenhoOrdemPagamentoLiquidacaoAnulada->setDado( 'cod_entidade', $obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade() );
            $obTEmpenhoOrdemPagamentoLiquidacaoAnulada->setDado( 'exercicio'   , $obREmpenhoOrdemPagamento->getExercicio() );
            $obTEmpenhoOrdemPagamentoLiquidacaoAnulada->recuperaValorAnular( $rsValores );
        }

        $inCountItem = 0;
        $newValorTotal = 0;

        $arItens = array ();
        while ( !$rsItens->eof() ) {
            /* Caso a ação for anular, somente lista as notas que possuem valor a pagar maior que zero */
            if (!($stAcao == 'anular' && $rsValores->getCampo( 'vl_a_anular' ) == 0.00 )) {
                $arItens[$inCountItem]["cod_empenho" ] = $rsItens->getCampo("cod_empenho"         );
                $arItens[$inCountItem]["ex_empenho" ]  = $rsItens->getCampo("exercicio_empenho"   );
                $arItens[$inCountItem]["dt_empenho" ]  = $rsItens->getCampo("dt_empenho"          );
                $arItens[$inCountItem]["cod_nota" ]    = $rsItens->getCampo("cod_nota"            );
                $arItens[$inCountItem]["ex_nota" ]     = $rsItens->getCampo("exercicio_liquidacao");
                $arItens[$inCountItem]["dt_nota"]      = $rsItens->getCampo("dt_liquidacao"       );

                if ($stAcao == 'anular') {
                    $arItens[$inCountItem]["valor_pagar"] = number_format($rsValores->getCampo( 'vl_a_anular' ) ,2 ,',','.') ;
                } else {
                    $arItens[$inCountItem]["valor_pagar"] = $rsItens->getCampo("vl_pagamento");
                }

                $arItens[$inCountItem]["num_cgm" ]     = $rsItens->getCampo("cgm_beneficiario"    );
                $arItens[$inCountItem]["nom_cgm" ]     = $rsItens->getCampo("beneficiario"        );
                $inCountItem++;
            }
            $rsValores->proximo();
            $rsItens->proximo();
        }
        if ($stAcao != "anular") {
            $newValorTotal   = number_format($_REQUEST["hdnValorTotal"], 2, ',', '.');
            $newValorAnulado = number_format($_REQUEST["hdnValorAnulado"], 2, ',', '.');

            $valorAnularTemp = $_REQUEST["hdnValorTotal"] - $_REQUEST["hdnValorAnulado"];
            $ValorParaAnular = number_format($valorAnularTemp, 2, ',', '.');
        } else {
            $ValorParaAnular = number_format($_REQUEST["hdnValorAnular"], 2, ',', '.');
        }
        Sessao::write('itemOrdem', $arItens);

        $rsListaItemOrdem = new RecordSet;
        $rsListaItemOrdem->preenche ( $arItens );
        $rsListaItemOrdem->ordena("cod_nota");
        montaListaLiquidacao( $rsListaItemOrdem , $newValorTotal , $newValorAnulado , $ValorParaAnular , "" );
        exit (0);
    break;
    case "excluirItem":
        $arItens = Sessao::read('itemOrdem');
        $arTmpItem = array ();
        $inCountSessao = count ($arItens);
        $inCountArray = 0;
        $newValorTotal = 0;
        $js = "";
        $dtUltima = "01/01/".Sessao::getExercicio();
        Sessao::write('valorTotalOrdem', 0);
        $stDtLiquidacao = 0;

        for ($inCount = 0; $inCount < $inCountSessao; $inCount++) {
            if ($arItens[$inCount][ "cod_nota" ] != $_REQUEST[ "inIndice" ]) {

                list( $inDia, $inMes, $inAno ) = explode( '/', $arItens[$inCount]["dt_nota"] );
                if( intval($inAno.$inMes.$inDia) > $stDtLiquidacao )
                    $stDtLiquidacao = $inAno.$inMes.$inDia;

                if ($inCodEmpenhoUltimo) {
                    if ($inCodEmpenhoUltimo == $arItens[$inCount]['cod_empenho']) {
                        $boLibera = true;
                    }
                }

                $inCodEmpenhoUltimo = $arItens[$inCount]["cod_empenho"];
                $stDescricaoEmpenhoUltimo = $arItens[$inCount]["descricao"];

                $arTmpItem[$inCountArray] = $arItens[$inCount];

                $somaTemp = str_replace(".","",$arItens[$inCount][ "valor_pagar" ]);
                $somatorio = str_replace(",",".",$somaTemp);
                Sessao::read('valorTotalOrdem');
                $arValorTotalOrdem += $somatorio;
                Sessao::write('valorTotalOrdem', $arValorTotalOrdem);
                $newValorTotal = number_format($arValorTotalOrdem, 2, ',', '.');

                $inCountArray++;

            } else {
                $js .= "d.frm.stDataLiquidacao.value = '01/01/".Sessao::getExercicio()."';";

                // se empenho for de adiantamento libera para poder inserir outros
                if ($arItens[$inCount][ "cod_categoria" ] == 2 || $arItens[$inCount][ "cod_categoria" ] == 3) {
                    Sessao::write('boAdiantamento', 'f');
                }
            }
        }

        for ( $inCount = 0; $inCount < count( $arTmpItem ); $inCount++ ) {
            $arTmpItem[$inCount]["max_valor_pagar"] = $newValorTotal;
        }

        $stDtLiquidacao = substr($stDtLiquidacao,6,2).'/'.substr($stDtLiquidacao,4,2).'/'.substr($stDtLiquidacao,0,4);

        $arItens = array();
        if ( count($arTmpItem) > 0 ) {

            $arItens = $arTmpItem;

            $rsListaItemOrdem = new RecordSet;
            $rsListaItemOrdem->preenche ($arItens );
            $rsListaItemOrdem->ordena("cod_nota");
            $js .= montaListaLiquidacao( $rsListaItemOrdem , $newValorTotal , "" , "" , Sessao::read('cgmFornecedor'), true );
            if(count($arTmpItem) == 1)
                $boLibera = true;
        if ($inCountSessao==2)
                $js .= "f.stDescricaoOrdem.value = '".$stDescricaoEmpenhoUltimo."';";
            else {
                if (($arItens[$inCountSessao-2][ "cod_empenho" ]==$arItens[0][ "cod_empenho" ] ) && ($arItens[$inCountSessao-2][ "cod_empenho" ]==$arItens[$inCountSessao-3][ "cod_empenho" ] ))
                    $js .= "f.stDescricaoOrdem.value = '".$stDescricaoEmpenhoUltimo."';";
                else
                    $js .= "f.stDescricaoOrdem.value = 'Referente pagamento de liquidações de diversos Empenhos, conforme relacionados acima.';";
            }
        } else {
            $obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
            $obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade($_REQUEST["inCodEntidade"]);
            $obREmpenhoOrdemPagamento->setExercicio(Sessao::getExercicio());
            $obErro = $obREmpenhoOrdemPagamento->listarMaiorData( $rsMaiorData );
            $js .= "d.frm.flValorTotal.value = '';";
            $js .= "d.frm.stFornecedor.value = '';";
            $js .= "f.stDescricaoOrdem.value = '';";
            Sessao::remove('itemRetencao');
            Sessao::remove('nuTlValorRetencao');
            $js .= "d.getElementById('spnListaItem').innerHTML = '';";
            $js .= "d.getElementById('spnRetencoes').innerHTML = '';";
        }
        Sessao::write('itemOrdem', $arItens);
        if ($boLibera) {
            $js .= "d.getElementById('boRetencaoO').disabled = false;\n";
            $js .= "d.getElementById('boRetencaoE').disabled = false;\n";
            $js .= "d.getElementById('boRetencaoN').disabled = false;\n";
        }

        SistemaLegado::executaiFrameOculto( $js."\n
                                            f.inCodEntidade.readOnly = false; \n
                                            var combo = f.stNomeEntidade; \n
                                            var stCodigo = 'JavaScript:preencheCampo( this, document.frm.inCodEntidade, \'".Sessao::getId()."\' );';
                                            combo.setAttribute('onchange',stCodigo); \n
                                            combo.setAttribute('onclick', ''); \n
                                            combo.setAttribute('onblur','JavaScript:buscaDado(\'buscaDtOrdem\');');
                                          ");
    break;
    case "limparOrdem":
        Sessao::remove('itemOrdem');
        Sessao::remove('itemRetencao');
        Sessao::remove('nuTlValorRetencao');
        $stJs .= "d.frm.reset();";
        $stJs .= "var combo = d.getElementById('stNomeEntidade'); \n";
        $stJs .= "combo.setAttribute('onchange','preencheCampo( this, document.frm.inCodEntidade, \'".Sessao::getId()."\' );buscaDado(\'buscaDtOrdem\');'); \n";
        $stJs .= "combo.setAttribute('onclick',''); \n";
        $stJs .= "combo.setAttribute('onblur',''); \n";
        $stJs .= "d.getElementById('spnListaItem').innerHTML = '';";
        $stJs .= "if (d.frm.Ok.disabled) d.frm.Ok.disabled = false; ";
        SistemaLegado::executaiFrameOculto($stJs);
    break;

    case 'buscaDtOrdem':

        if ($_REQUEST["inCodEntidade"] !="") {
            $obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
            $obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade($_REQUEST["inCodEntidade"]);
            $obREmpenhoOrdemPagamento->setExercicio(Sessao::getExercicio());
            $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->setDtLiquidacao($_REQUEST['stDataLiquidacao']);
            $obErro = $obREmpenhoOrdemPagamento->listarMaiorData( $rsMaiorData );

            if (!$obErro->ocorreu()) {
                $stDtOrdem      = $rsMaiorData->getCampo( "data_ordem" );
                if ($stDtOrdem == "") {
                $stDtOrdem = "01/01/".Sessao::getExercicio();
                }

                $stDtLiquidacao = Sessao::read('dtUltimaLiquidacao');

                if ($stDtLiquidacao) {
                     $stDtOrdem = $stDtLiquidacao;
                }
                $stDtOrdemOld = '31/12/'.(Sessao::getExercicio() -1).'';
                if (SistemaLegado::comparaDatas($stDtOrdem,$stDtOrdemOld)) {
                    $js .= 'f.stDtOrdem.value = "'.$stDtOrdem.'";';
                    $js .= 'f.inCodigoEmpenho.focus();';
                }
            }
        } else $js .= 'f.stDtOrdem.value= "";';

        SistemaLegado::executaiFrameOculto($js);

    break;

    case "preencheListaLiquidacoes":
        if ($_REQUEST['inCodEmpenhoLiquidacao'] && $_REQUEST['inCodNotaLiquidacao']) {

            $obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
            $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setCodEmpenho( $_REQUEST["inCodEmpenhoLiquidacao"]);
            $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodEntidadeLiquidacao"]);
            $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setExercicio( $_REQUEST['tExercicioEmpenhoLiquidacao'] );
            $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->setCodNota( $_REQUEST['inCodNotaLiquidacao'] );
            $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->setExercicio( $_REQUEST['stExercicioNotaLiquidacao']);
            $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->checarImplantado( $boImplantado );
            if ($_REQUEST['boImplantado']) {
                $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->listarNotasAPagarDisponiveisImplantadas( $rsLiquidacoes );
            } else {
                $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->listarNotasDisponiveis( $rsLiquidacoes );
            }

            $flValorNota        = $rsLiquidacoes->getCampo( "vl_nota" );
            $flValorNotaTMP     = str_replace( '.','',$flValorNota );
            $flValorNotaTMP     = str_replace( ',','.',$flValorNotaTMP );

            if ($flValorNotaTMP > 0) {
                $inCodigoLiquidacao = $rsLiquidacoes->getCampo( "cod_nota"          );
                $inCodRecurso       = $rsLiquidacoes->getCampo( "cod_recurso"       );
                $exercicioNota      = $rsLiquidacoes->getCampo( "exercicio_nota"    );
                $dtDataLiquidacao   = $rsLiquidacoes->getCampo( "dt_liquidacao"     );
                $inCodigoEmpenho    = $rsLiquidacoes->getCampo( "cod_empenho"       );
                $stDescricao        = $rsLiquidacoes->getCampo( "descricao"       );
                $dtDataEmpenho      = $rsLiquidacoes->getCampo( "dt_empenho"        );
                $exercicioEmpenho   = $rsLiquidacoes->getCampo( "exercicio_empenho" );
                $numCGM             = $rsLiquidacoes->getCampo( "cgm_beneficiario"  );
                $nomeCGM            = str_replace("'","\'",$rsLiquidacoes->getCampo( "beneficiario"));
                $boImplantado       = $rsLiquidacoes->getCampo( "implantado"        );
                $nuVlOrdemPago      = $rsLiquidacoes->getCampo("vl_ordem") - $rsLiquidacoes->getCampo("vl_ordem_anulada");
                $nuVlLiquidado      = $rsLiquidacoes->getCampo( "vl_itens" ) - $rsLiquidacoes->getCampo( "vl_itens_anulados" );
                $nuVlPago           = $rsLiquidacoes->getCampo( "vl_pago" ) - $rsLiquidacoes->getCampo( "vl_pago_anulado" );
                $nuVlAPagar         = number_format( $flValorNotaTMP, 2, ',','.' );
                $mixCombo = $inCodigoLiquidacao." - ".$dtDataLiquidacao;
                $mixComboValor = $inCodigoLiquidacao."||".$dtDataLiquidacao."||".$nuVlAPagar."||".$inCodigoEmpenho."||".$dtDataEmpenho."||".$exercicioEmpenho."||".$numCGM."||".$nomeCGM."||".$exercicioNota."||".$boImplantado."||".$inCodRecurso;

            }
            $stFornecedor = ( $rsLiquidacoes->getCampo('beneficiario') ) ? str_replace("'","\'",$rsLiquidacoes->getCampo( "beneficiario"      )): '&nbsp;';
            $js .= "d.getElementById('stDescEmpenho').innerHTML='".$stFornecedor."';";

            $mixLiquidacao = explode("||", $mixComboValor);
            $inCodigoLiquidacao = $mixLiquidacao[0];
            $dtDataLiquidacao   = $mixLiquidacao[1];
            $flValorNota        = $mixLiquidacao[2];
            $inCodigoEmpenho    = $mixLiquidacao[3];
            $dtDataEmpenho      = $mixLiquidacao[4];
            $exercicioEmpenho   = $mixLiquidacao[5];
            $inNumCGM           = $mixLiquidacao[6];
            $stNomeCGM          = $mixLiquidacao[7];
            $exercicioNota      = $mixLiquidacao[8];
            $boImplantado       = $mixLiquidacao[9];
            $inCodRecurso       = $mixLiquidacao[10];

            $stInsere = false;

            $arItens = Sessao::read('itemOrdem');
            if ($arItens) {
                $inCountSessao = count ($arItens);
            } else {
                $inCountSessao = 0;
                $stInsere = true;
            }
            if (!$_REQUEST['stLiq']) {
                for ($iCount = 0; $iCount < $inCountSessao; $iCount++) {
                    if ($arItens[$iCount]["num_cgm"] != $inNumCGM) {
                        $obErro = new Erro;
                        $obErro->setDescricao( "As notas de liquidação informadas devem ser do mesmo fornecedor!" );
                        SistemaLegado::exibeAvisoTelaPrincipal(urlencode($obErro->getDescricao()),"n_incluir","erro");
                        $stInsere = false;
                        $iCount = $inCountSessao;
                    } elseif ($arItens[$iCount]["cod_nota"]    == $inCodigoLiquidacao) {
                        $obErro = new Erro;
                        $obErro->setDescricao( "Nota de liquidação já informada!" );
                        SistemaLegado::exibeAvisoTelaPrincipal(urlencode($obErro->getDescricao()),"n_incluir","erro");
                        $stInsere = false;
                        $iCount = $inCountSessao;
                    } elseif ($arItens[$iCount]["cod_recurso"] != $inCodRecurso) {
                        $obErro = new Erro;
                        $obErro->setDescricao( "As notas de liquidação informadas devem ser do mesmo recurso!" );
                        SistemaLegado::exibeAvisoTelaPrincipal(urlencode($obErro->getDescricao()),"n_incluir","erro");
                        $stInsere = false;
                        $iCount = $inCountSessao;
                    } else {
                        $stInsere = true;
                    }
                }
            }
            if ($stInsere) {
                if ($arItens && !$_REQUEST['stLiq']) {
                   $inLast = count ($arItens);
                } else {
                    $inLast = 0;
                    Sessao::remove('itemOrdem');
                    Sessao::remove('valorTotalOrdem');
                    Sessao::remove('cgmFornecedor');
                }

                $arItens[$inLast]["cod_empenho"    ] = $inCodigoEmpenho;
        $arItens[$inLast]["nom_conta  "    ] = $stDescricao;
                $arItens[$inLast]["dt_empenho"     ] = $dtDataEmpenho;
                $arItens[$inLast]["ex_empenho"     ] = $exercicioEmpenho;
                $arItens[$inLast]["cod_nota"       ] = $inCodigoLiquidacao;
                $arItens[$inLast]["ex_nota"        ] = $exercicioNota;
                $arItens[$inLast]["dt_nota"        ] = $dtDataLiquidacao;
                $arItens[$inLast]["valor_pagar"    ] = $flValorNota;
                $arItens[$inLast]["max_valor_pagar"] = $flValorNota;
                $arItens[$inLast]["num_cgm"        ] = $inNumCGM;
                $arItens[$inLast]["nom_cgm"        ] = $stNomeCGM;
                $arItens[$inLast]["cod_recurso"    ] = $inCodRecurso;
                Sessao::write('bo_implantado', $boImplantado);
                Sessao::write('itemOrdem', $arItens);

                $somaTemp = str_replace(".","", $flValorNota);
                $somatorio = str_replace(",",".",$somaTemp);
                $arValorTotalOrdem = Sessao::read('valorTotalOrdem');
                $arValorTotalOrdem += $somatorio;
                Sessao::write('valorTotalOrdem', $arValorTotalOrdem);
                $newValorTotal = number_format($arValorTotalOrdem, 2, ',', '.');

                Sessao::write('cgmFornecedor', $inNumCGM." - ".$stNomeCGM);
            } else {
                $newValorTotal = number_format(Sessao::read('valorTotalOrdem'), 2, ',', '.');
            }

            $rsListaItemOrdem = new RecordSet;
            $rsListaItemOrdem->preenche ( $arItens );
            $rsListaItemOrdem->ordena("cod_nota");
            $stJs  = $js;
            $stJs .= montaListaLiquidacao( $rsListaItemOrdem , $newValorTotal , "" , "" , Sessao::read('cgmFornecedor'),true );
            if (($rsListaItemOrdem->getNumLinhas())=='1') {
                $stDescricao = str_replace("\n", "\\n", $stDescricao);
                $stJs .= "f.stDescricaoOrdem.value = '".$stDescricao."';";
            } else {
                if (($arItens[$inLast]["cod_empenho"] != $arItens[$inLast-1]["cod_empenho"])
                and ($arItens[$inLast]["cod_empenho"] != $arItens[0]["cod_empenho"])) {
                    $stJs .= "f.stDescricaoOrdem.value = 'Referente pagamento de liquidações de diversos Empenhos, conforme relacionados acima.';";
                }
            }

            if ($_REQUEST['stLiq'] == 1) { // Recurso de 'emitir OP' vindo da tela de liquidação.
                $stJs .= montaRetencoes( true );
            }
            $stJs .= "f.inCodEntidade.readOnly = true; \n";
            $stJs .= "var combo = d.getElementById('stNomeEntidade'); \n";
            $stJs .= "var stCodigo  = \"window.parent.frames['telaPrincipal'].document.getElementById('inCodEntidade').focus(); \"; \n";
            $stJs .= "var stCodigo2 = \"alertaAvisoTelaPrincipal('A Entidade não pode ser alterada para os Itens cadastrados!()','form','erro','".Sessao::getId()."', '../'); \"; \n";
            $stJs .= "var stCodigo3 = \"window.parent.frames['telaPrincipal'].document.frm.stNomeEntidade.value='".$_REQUEST['inCodEntidadeLiquidacao']."';\"; \n";
            $stJs .= "combo.setAttribute('onchange',stCodigo); \n";
            $stJs .= "combo.setAttribute('onclick', stCodigo + stCodigo2); \n";
            $stJs .= "combo.setAttribute('onblur', stCodigo3); \n";
        }
        $stJs .= "limpaSelect(f.cmbLiquidacao,0); \n";
        $stJs .= "d.frm.flValorPagar.value = '';";
        $stJs .= "d.frm.inCodigoEmpenho.value = '';";
        $stJs .= "d.getElementById('stDescEmpenho').innerHTML = '&nbsp;';";
        $stJs .= "d.frm.inCodEmpenhoLiquidacao.value = '';";
        $stJs .= "d.frm.inCodNotaLiquidacao.value = '';";

        # Busca Ordem, fonte replicado do buscaDtOrdem

        if ($_REQUEST["inCodEntidade"] !="") {
            $obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
            $obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade($_REQUEST["inCodEntidade"]);
            $obREmpenhoOrdemPagamento->setExercicio(Sessao::getExercicio());
            $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->setDtLiquidacao($_REQUEST['stDataLiquidacao']);
            $obErro = $obREmpenhoOrdemPagamento->listarMaiorData( $rsMaiorData );

            if (!$obErro->ocorreu()) {
                $stDtOrdem      = $rsMaiorData->getCampo( "data_ordem" );
                if ($stDtOrdem == "") {
                $stDtOrdem = "01/01/".Sessao::getExercicio();
                }

                $stDtLiquidacao = Sessao::read('dtUltimaLiquidacao');

                if ($stDtLiquidacao) {
                     $stDtOrdem = $stDtLiquidacao;
                }
                $stDtOrdemOld = '31/12/'.(Sessao::getExercicio() -1).'';
                if (SistemaLegado::comparaDatas($stDtOrdem,$stDtOrdemOld)) {
                    $stJs .= 'f.stDtOrdem.value = "'.$stDtOrdem.'";';
                    $stJs .= 'f.inCodigoEmpenho.focus();';
                }
            }
        } else {
            $stJs .= 'f.stDtOrdem.value= "";';
        }

        SistemaLegado::executaiFrameOculto($stJs);
    break;

    case 'buscaFornecedorDiverso':

        if ($_POST["inCodFornecedor"] != "") {
        include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoPagamentoLiquidacao.class.php" );
            $obREmpenhoPagamentoLiquidacao = new REmpenhoPagamentoLiquidacao;
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

    case 'montaContaValorRetencao':
        include_once(CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracaoEntidade.class.php');
        $obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade;

        $stFiltroConta = "  WHERE parametro = 'conta_caixa' AND cod_entidade = ".$_REQUEST['inCodEntidade']." AND exercicio = '".Sessao::getExercicio()."' ";
        $obErro = $obTAdministracaoConfiguracaoEntidade->recuperaTodos($rsContas, $stFiltroConta, '', $boTransacao);

        if (!$obErro->ocorreu() && !$rsContas->eof() && $rsContas->getNumLinhas() == 1) {
            $obFormulario = new Formulario;
            switch ($_REQUEST['rdRetencao']) {
                case 'E':
                    include_once( CAM_GF_CONT_COMPONENTES."IPopUpContaAnalitica.class.php");
                    $obBscContaRetencao = new IPopUpContaAnalitica(Sessao::read('componentes'));
                    $obBscContaRetencao->setRotulo("Conta Retenção");
                    $obBscContaRetencao->setTitle("Informe a Conta de Retenção");
                    $obBscContaRetencao->setId("stNomContaRetencao");
                    $obBscContaRetencao->setName("stNomContaRetencao");
                    $obBscContaRetencao->obCampoCod->setName("inCodPlanoRetencao");
                    $obBscContaRetencao->obCampoCod->setId("inCodPlanoRetencao");
                    $obBscContaRetencao->setNull(false);
                    $obBscContaRetencao->setTipoBusca("emp_retencao_op_extra");
                break;
                case 'O':
                    include_once CAM_GF_ORC_COMPONENTES."IPopUpReceita.class.php";
                    $obBscContaRetencao = new IPopUpReceita(Sessao::read('componentes'));
                    $obBscContaRetencao->setId("stNomContaRetencao");
                    $obBscContaRetencao->setName("stNomContaRetencao");
                    $obBscContaRetencao->setNull(false);
                    $obBscContaRetencao->setTitle("Informe a Conta de Retenção");
                    $obBscContaRetencao->setTipoBusca('retencoes');
                    $obBscContaRetencao->obCampoCod->setName("inCodPlanoRetencao");
                    $obBscContaRetencao->obCampoCod->setId("inCodPlanoRetencao");
                    $obBscContaRetencao->obCampoCod->obEvento->setOnChange('buscaReceitas();');
                    $obBscContaRetencao->setUsaFiltro(true);

                    if (Sessao::read('stEmitirCarneOp') == 'true') {
                        $obCmbCodCredito = new Select;
                        $obCmbCodCredito->setRotulo("*Crédito da Receita");
                        $obCmbCodCredito->setTitle("Informe o Crédito da Receita.");
                        $obCmbCodCredito->setName("inCodCredito");
                        $obCmbCodCredito->setId("inCodCredito");
                        $obCmbCodCredito->addOption("", "Selecione");
                        $obCmbCodCredito->obEvento->setOnChange("jq('#creditoDesc').val(this[this.selectedIndex].text);");

                        $obHdnCreditoDesc = new Hidden;
                        $obHdnCreditoDesc->setName("creditoDesc");
                        $obHdnCreditoDesc->setId("creditoDesc");
                    }

                break;
            }

            $obTxtValorRetencao = new Moeda;
            $obTxtValorRetencao->setName     ( "nuValorRetencao" );
            $obTxtValorRetencao->setId       ( "nuValorRetencao" );
            $obTxtValorRetencao->setRotulo   ( "Valor Retenção"  );
            $obTxtValorRetencao->setTitle    ( "Informe o Valor da Retenção" );
            $obTxtValorRetencao->setNull     ( false     );
            $obTxtValorRetencao->setMaxLength( 12        );

            $obBtnIncluirItem = new Button;
            $obBtnIncluirItem->setName              ( "btnIncluirRetencao" );
            $obBtnIncluirItem->setValue             ( "Incluir"        );
            $obBtnIncluirItem->setTipo              ( "button"         );
            $obBtnIncluirItem->obEvento->setOnClick ( "incluirRetencao();" );

            $obBtnLimparItem = new Button;
            $obBtnLimparItem->setName               ( "btnLimparRetencoes"  );
            $obBtnLimparItem->setValue              ( "Limpar"         );
            $obBtnLimparItem->setTipo               ( "button"         );
            $obBtnLimparItem->obEvento->setOnClick  ( "limparRetencoes();"  );

            $obFormulario->addComponente($obBscContaRetencao);
            if ($_REQUEST['rdRetencao'] == 'O' && (Sessao::read('stEmitirCarneOp') == 'true')) {
                $obFormulario->addHidden($obHdnCreditoDesc);
                $obFormulario->addComponente($obCmbCodCredito);
            }
            $obFormulario->addComponente($obTxtValorRetencao);
            $obFormulario->defineBarra(array($obBtnIncluirItem, $obBtnLimparItem));
            $obFormulario->montaInnerHTML();

            $js = "d.getElementById('spnContaValorRetencao').innerHTML = '".$obFormulario->getHTML()."';";
        } else {
            $js .= "d.getElementById('boRetencaoN').checked = true;\n ";
            $js .= "alertaAvisoTelaPrincipal('Conta Caixa não cadastrada para essa entidade.','form','erro','".Sessao::getId()."', '../');\n";
        }

        SistemaLegado::executaiFrameOculto($js);

    break;

    case 'buscaReceitas':
        include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceitaCredito.class.php";
        $obTOrcamentoReceitaCredito = new TOrcamentoReceitaCredito;
        $obTOrcamentoReceitaCredito->setDado('cod_receita', $_REQUEST['inCodPlanoRetencao']);
        $obTOrcamentoReceitaCredito->setDado('exercicio', Sessao::getExercicio());
        $obTOrcamentoReceitaCredito->recuperaDadosCredito($rsReceitaCredito);

        echo json_encode($rsReceitaCredito->getElementos());
        break;

    case 'incluirRetencao':
        $nuValorRetencao = str_replace(',','.',str_replace('.','',$_REQUEST['nuValorRetencao']));
        $arTlValorRetencao = Sessao::read('nuTlValorRetencao');
        if ((int) ($arTlValorRetencao + $nuValorRetencao) > (int) Sessao::read('valorTotalOrdem')) {
            echo "<script> avisoRetencao('O Valor das Retenções não pode ser maior que o valor da OP.'); </script>";
            $stJs .= "d.frm.Ok.disabled = false;";
        } else {
            $arItemRetencao = Sessao::read('itemRetencao');
            if (!isset($arItemRetencao)) {
                $arItemRetencao[0]['inId']         = 1;
                $arItemRetencao[0]['cod_reduzido'] = $_REQUEST['inCodPlanoRetencao'];
                $arItemRetencao[0]['nom_conta']    = $_REQUEST['stNomContaRetencao'];
                $arItemRetencao[0]['nuValor']      = $_REQUEST['nuValorRetencao'];
                $arItemRetencao[0]['stTipo']       = $_REQUEST['rdRetencao'];
                $arItemRetencao[0]['inCodCredito'] = $_REQUEST['inCodCredito'];
                $arItemRetencao[0]['creditoDesc']  = $_REQUEST['creditoDesc'];
                $arTlValorRetencao += $nuValorRetencao;
                Sessao::write('nuTlValorRetencao', $arTlValorRetencao);
            } else {
                $boIncluir = true;
                foreach ($arItemRetencao as $itens) {
                    if ( ($_REQUEST['inCodPlanoRetencao'] == $itens['cod_reduzido']) && ($_REQUEST['rdRetencao'] == $itens['stTipo']) ) {
                        echo "<script> avisoRetencao('A conta ".$_REQUEST['inCodPlanoRetencao']." já se encontra na lista de Retenções.'); </script>";
                        $boIncluir = false;
                    }
                    $inCount = count($arItemRetencao);
                    $inLast = $inCount == $itens['inId'] ? $inCount+1 : $inLast+1 ;
                }

                if ($boIncluir) {
                    $arItemRetencao[$inCount]['inId']         = $inLast;
                    $arItemRetencao[$inCount]['cod_reduzido'] = $_REQUEST['inCodPlanoRetencao'];
                    $arItemRetencao[$inCount]['nom_conta']    = $_REQUEST['stNomContaRetencao'];
                    $arItemRetencao[$inCount]['nuValor']      = $_REQUEST['nuValorRetencao'];
                    $arItemRetencao[$inCount]['stTipo']       = $_REQUEST['rdRetencao'];
                    $arItemRetencao[$inCount]['inCodCredito'] = $_REQUEST['inCodCredito'];
                    $arItemRetencao[$inCount]['creditoDesc']  = $_REQUEST['creditoDesc'];
                }
            }
            Sessao::write('itemRetencao', $arItemRetencao);

            if ($boIncluir) {
                $arTlValorRetencao += $nuValorRetencao;
                Sessao::write('nuTlValorRetencao', $arTlValorRetencao);
            }

            $stJs .= montaListaRetencao( $arItemRetencao );
            $stJs .= "d.getElementById('inCodPlanoRetencao').value = '';";
            $stJs .= "d.getElementById('stNomContaRetencao').innerHTML = '&nbsp;';";
            $stJs .= "d.getElementById('nuValorRetencao').value = '';";
            if (Sessao::read('stEmitirCarneOp') == 'true') {
                $stJs .= "limpaSelect(f.inCodCredito, 1);";
            }
            $stJs .= "d.frm.Ok.disabled = false;";
        }
        SistemaLegado::executaiFrameOculto( $stJs );

    break;

    case 'excluirItemRetencao':
        $arItemRetencao = Sessao::read('itemRetencao');
        $arTlValorRetencao = Sessao::read('nuTlValorRetencao');
        if (is_array($arItemRetencao)) {
            $arRetencao = Array();
            $inCount = 0;
            foreach ($arItemRetencao as $itens) {
                if ((int) $itens['inId'] != (int) $_REQUEST['inId']) {
                    $arRetencao[$inCount] = $itens;
                    $inCount++;
                } else {
                    $arTlValorRetencao -= str_replace(',','.',str_replace('.','',$itens['nuValor']));
                    Sessao::write('nuTlValorRetencao', $arTlValorRetencao);
                }
            }
            $arItemRetencao = $arRetencao;
        }
        if (count($arItemRetencao) == 0) {
            $stJs = "d.getElementById('spnListaRetencao').innerHTML = '&nbsp;';";
            Sessao::remove('nuTlValorRetencao');
            Sessao::remove('itemRetencao');
        } else {
            Sessao::write('itemRetencao', $arItemRetencao);
            $stJs = montaListaRetencao( $arItemRetencao );
        }

        SistemaLegado::executaiFrameOculto($stJs);

    break;

    case 'limpaContaValorRetencao':
        Sessao::remove('itemRetencao');
        Sessao::remove('nuTlValorRetencao');
        $js  = "d.getElementById('spnContaValorRetencao').innerHTML = '';";
        $js .= "d.getElementById('spnListaRetencao').innerHTML = '';";
        SistemaLegado::executaiFrameOculto($js);

    break;
}
?>
