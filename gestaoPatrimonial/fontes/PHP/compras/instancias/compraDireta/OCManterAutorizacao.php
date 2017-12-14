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
 * Pagina de Oculto
 * Data de Criação   : 29/01/2007

 * @author Desenvolvedor: Lucas Teixeira Stephanou

 * @ignore

 $Id: OCManterAutorizacao.php 65503 2016-05-27 13:49:00Z evandro $

 * Casos de uso: uc-03.04.32

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutorizacao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;

unset($stJs);

$stMsgErro  = "Compra Direta não encontrada";

function montaListaItens($rsItens , $inCodCompraDireta = null, $stExercicio=null , $inCodEntidade , $inCodModalidade)
{
    // formata recordset
    $rsItens->setPrimeiroElemento();

    $rsItens->addFormatacao ( 'valor_unitario' , 'NUMERIC_BR' );
    $rsItens->addFormatacao ( 'quantidade' , 'NUMERIC_BR' );
    $rsItens->addFormatacao ( 'vl_cotacao' , 'NUMERIC_BR' );

    $table = new TableTree();

    $table->setRecordset( $rsItens );
    $table->setSummary('Itens');

    $table->setArquivo( CAM_GP_COM_INSTANCIAS . 'compraDireta/OCManterAutorizacao.php');
    // parametros do recordSet
    $table->setParametros( array( "cod_item") );
    // 	parametros adicionais
    $stParamAdicionais  = "stCtrl=listarDetalheItem&inCodCompraDireta=" . $inCodCompraDireta;
    $stParamAdicionais .= "&stExercicio=" . $stExercicio;
    $stParamAdicionais .= "&inCodEntidade=" . $inCodEntidade;
    $stParamAdicionais .= "&inCodModalidade=" . $inCodModalidade;
    $table->setComplementoParametros( $stParamAdicionais );

    $table->Head->addCabecalho( 'Item' , 60  );
    $table->Head->addCabecalho( 'Quantidade' , 15  );
    $table->Head->addCabecalho( 'Valor Total' , 15  );

    $table->Body->addCampo( '[cod_item] - [descricao_completa] [nome_marca]<br>[complemento]' , 'E');
    $table->Body->addCampo( 'quantidade' , 'C');
    $table->Body->addCampo( 'vl_cotacao' , 'D');

    $table->Foot->addSoma( 'vl_cotacao', 'D' );

    $table->montaHTML();
    $stHTML = $table->getHtml();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

        $stJs = "d.getElementById('spnItens').innerHTML = '" . $stHTML . "';";

        return $stJs;
}

function montaListaItensDetalhe($rsRegistros)
{
    $rsRegistros->setPrimeiroElemento();

    $rsRegistros->addFormatacao ( 'vl_unitario' , 'NUMERIC_BR' );
    $rsRegistros->addFormatacao ( 'quantidade' , 'NUMERIC_BR' );
    $rsRegistros->addFormatacao ( 'vl_cotacao' , 'NUMERIC_BR' );

    $table = new Table();

    $table->setRecordset( $rsRegistros );
    $table->setSummary('Itens');

    $table->Head->addCabecalho( 'Fornecedor' , 30  );
    $table->Head->addCabecalho( 'Solicitação' , 8  );
    $table->Head->addCabecalho( 'Lote' , 5  );
    $table->Head->addCabecalho( 'Centro de Custo' , 10  );
    $table->Head->addCabecalho( 'Dotação Orçamentária' , 14  );
    $table->Head->addCabecalho( 'Valor Unitário' , 10  );
    $table->Head->addCabecalho( 'Quantidade' , 10  );
    $table->Head->addCabecalho( 'Valor Total' , 14  );

    $table->Body->addCampo( '[cgm_fornecedor] - [fornecedor]' , 'C');
    $table->Body->addCampo( 'cod_solicitacao' , 'C');
    $table->Body->addCampo( 'lote' , 'C');
    $table->Body->addCampo( 'cod_centro' , 'C');
    $table->Body->addCampo( 'cod_estrutural' , 'C');
    $table->Body->addCampo( 'vl_unitario' , 'D');
    $table->Body->addCampo( 'quantidade' , 'D');
    $table->Body->addCampo( 'vl_cotacao' , 'D');

    $table->montaHTML();
    $stHTML = $table->getHtml();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    return $stHTML;
}

function montaFormDatasAutorizacao($inCodMapaCompras, $stExercicioMapaCompras)
{
    $obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;
    $obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
    $obFormulario = new Formulario();

    include_once(CAM_GP_COM_MAPEAMENTO."TComprasMapaSolicitacao.class.php");
    $obTComprasMapaSolicitacao = new TComprasMapaSolicitacao;
    $obTComprasMapaSolicitacao->setDado('cod_mapa', $inCodMapaCompras);
    $obTComprasMapaSolicitacao->setDado('exercicio', $stExercicioMapaCompras);
    $obTComprasMapaSolicitacao->recuperaSolicitacaoEntidade( $rsAutorizacoes );

    $inTotalEntidades = $rsAutorizacoes->getNumLinhas();
    $stHTMLAutorizacoes = "";

    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
    $obTEmpenhoEmpenho = new TEmpenhoEmpenho;

    $stFiltro = "  AND empenho.cod_entidade = ".$rsAutorizacoes->getCampo('cod_entidade')." \n";
    $stFiltro.= " AND empenho.exercicio = '".Sessao::getExercicio()."'                      \n";
    $stOrdem  = " ORDER BY empenho.dt_empenho DESC LIMIT 1                                  \n";

    $obTEmpenhoEmpenho->recuperaUltimaDataEmpenho( $rsRecordSet,$stFiltro,$stOrdem );

    if ($dataUltimoEmpenho !="") {
        $dataUltimoEmpenho = SistemaLegado::dataToBr($rsRecordSet->getCampo('dt_empenho'));
    }

    while ( !$rsAutorizacoes->eof() ) {
        $obFormulario = new Formulario();

        $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $rsAutorizacoes->getCampo('cod_entidade'));
        $obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
        $obErro = $obREmpenhoAutorizacaoEmpenho->listarMaiorData( $rsMaiorData );

        if ( ($rsMaiorData->getCampo( "data_autorizacao" ) !="") ) {
            $stDtAutorizacao = $rsMaiorData->getCampo( "data_autorizacao" );
            $stExercicioDtAutorizacao = substr($stDtAutorizacao, 6, 4);
        } elseif ( ( $dataUltimoEmpenho !="") ) {
            $stDtAutorizacao = $dataUltimoEmpenho;
            $stExercicioDtAutorizacao = substr($dataUltimoEmpenho, 6, 4);
        } else {
            $stDtAutorizacao = "01/01/".Sessao::getExercicio();
            $stExercicioDtAutorizacao = Sessao::getExercicio();
        }

        $obFormulario->addTitulo ( $rsAutorizacoes->getCampo('cod_entidade').' - '.$rsAutorizacoes->getCampo('nom_cgm') );

        $obTxtDtAutorizacao = new Data();
        $obTxtDtAutorizacao->setRotulo( "Data da Autorização");
        $obTxtDtAutorizacao->setValue ( $stDtAutorizacao );
        $obTxtDtAutorizacao->setName  ( "dtAutorizacao_".$rsAutorizacoes->getCampo('cod_entidade') );
        $obTxtDtAutorizacao->setId    ( "dtAutorizacao_".$rsAutorizacoes->getCampo('cod_entidade') );

        $obFormulario->addComponente( $obTxtDtAutorizacao );

        $obFormulario->montaInnerHTML();
        $stHTMLAutorizacoes .= $obFormulario->getHTML();

        $rsAutorizacoes->proximo();

    }

    return $stHTMLAutorizacoes;
}

switch ($_REQUEST['stCtrl']) {
    case 'listarDetalheItem' :

            $stFiltro  = " and compra_direta.cod_compra_direta = " . $_REQUEST["inCodCompraDireta"];
            $stFiltro .= " and compra_direta.cod_modalidade= " . $_REQUEST["inCodModalidade"];
            $stFiltro .= " and compra_direta.cod_entidade= " . $_REQUEST["inCodEntidade"];
            $stFiltro .= " and compra_direta.exercicio_entidade= " . Sessao::getExercicio()."::VARCHAR";
            $stFiltro .= " and mapa_item.cod_item = " . $_REQUEST["cod_item"];

            $stFiltro .= "
                -- Não pode existir uma cotação anulada.
                AND NOT EXISTS (
                                    SELECT  1
                                      FROM  compras.cotacao_anulada
                                     WHERE  cotacao_anulada.cod_cotacao = cotacao.cod_cotacao
                                       AND  cotacao_anulada.exercicio   = cotacao.exercicio
                               ) ";

            require_once ( CAM_GP_COM_MAPEAMENTO . "TComprasCompraDireta.class.php");
            $obTCompraDireta = new TComprasCompraDireta();
            $obTCompraDireta->recuperaItensDetalhesAutorizacaoEmpenho( $rsDetalheItens , $stFiltro);

            $stJs = montaListaItensDetalhe( $rsDetalheItens );

        break;

    case 'buscaInfoCompraDireta':

        # Validação necessária
        if ($_REQUEST["inCodEntidade"] && $_REQUEST["inCodModalidade"] && $_REQUEST["inCodCompraDireta"] && $_REQUEST["stExercicioEntidade"]) {
            require_once CAM_GP_COM_MAPEAMENTO."TComprasCompraDireta.class.php";
            $obTCompraDireta = new TComprasCompraDireta();

            $obTCompraDireta->setDado ('cod_compra_direta'   , $_REQUEST['inCodCompraDireta']   );
            $obTCompraDireta->setDado ('cod_modalidade'      , $_REQUEST['inCodModalidade']     );
            $obTCompraDireta->setDado ('cod_entidade'        , $_REQUEST['inCodEntidade']       );
            $obTCompraDireta->setDado ('exercicio_entidade'  , $_REQUEST["stExercicioEntidade"] );
            $obTCompraDireta->recuperaMapaCompraDiretaJulgada ($rsCompraDiretaMapa , $stFiltroMapa);

            if ($rsCompraDiretaMapa->getNumLinhas() > 0) {
                $inCodMapa       =  $rsCompraDiretaMapa->getCampo("cod_mapa");
                $stExercicioMapa = $rsCompraDiretaMapa->getCampo("exercicio_mapa");
                $stObjeto        = $rsCompraDiretaMapa->getCampo("objeto");

                // busca itens do mapa, agrupados
                include_once CAM_GP_COM_MAPEAMENTO.'TComprasCotacaoFornecedorItem.class.php';
                $obTComprasCotacaoFornecedorItem = new TComprasCotacaoFornecedorItem;

                $stFiltro = "
                        WHERE  mapa_cotacao.exercicio_mapa = '$stExercicioMapa'
                          AND  mapa_cotacao.cod_mapa = $inCodMapa
                          AND  julgamento_item.ordem = 1
                          --   Não pode existir uma cotação anulada.
                          AND  NOT EXISTS (
                                            SELECT  1
                                              FROM  compras.cotacao_anulada
                                             WHERE  cotacao_anulada.cod_cotacao = cotacao_item.cod_cotacao
                                               AND  cotacao_anulada.exercicio   = cotacao_item.exercicio
                                         ) ";

                $obTComprasCotacaoFornecedorItem->recuperaItensCotacaoJulgadosCompraDireta ( $rsMapaItens, $stFiltro );

                // somar total do mapa
                $nuTotal = 0.00;
                while (!$rsMapaItens->eof()) {
                    $itemComplemento = "";
                    include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapaItem.class.php';
                    $obTComprasMapaItem = new TComprasMapaItem();
                    $obTComprasMapaItem->setDado('cod_mapa'  , $rsMapaItens->getCampo('cod_mapa'));
                    $obTComprasMapaItem->setDado('cod_item'  , $rsMapaItens->getCampo('cod_item'));
                    $obTComprasMapaItem->setDado('exercicio' , $rsMapaItens->getCampo('exercicio'));
                    $obTComprasMapaItem->recuperaComplementoItemMapa( $rsItemComplemento );

                    $rsItemComplemento->setPrimeiroElemento();
                    while (!$rsItemComplemento->eof()) {
                        if ($itemComplemento == "") {
                            $itemComplemento = $rsItemComplemento->getCampo('complemento');
                        } else {
                            $itemComplemento = $itemComplemento ." <br>".$rsItemComplemento->getCampo('complemento');
                        }
                        $rsItemComplemento->proximo();
                    }
                    $rsMapaItens->setCampo('complemento', $itemComplemento);

                    $nuTotal += $rsMapaItens->getCampo('vl_cotacao');
                    $rsMapaItens->proximo();
                }
                $nuTotal = number_format($nuTotal,2,',','.');

                $rsMapaItens->setPrimeiroElemento();

                $stHTMLAutorizacao = montaFormDatasAutorizacao($_REQUEST['inCodMapaCompras'], $_REQUEST['stExercicioMapaCompras']);
                $stJs .= "d.getElementById('spnAutorizacoes').innerHTML = '" . $stHTMLAutorizacao . "';";
                $stJs .= montaListaItens( $rsMapaItens , $_REQUEST["inCodCompraDireta"] , Sessao::getExercicio() , $_REQUEST["inCodEntidade"] , $_REQUEST["inCodModalidade"]  ) ;

                $stJs .= "d.getElementById('spnLabels').innerHTML = '" . $stHTML . "';";
                $stJs .= "$('stTotalMapa').innerHTML = '" . $nuTotal . "';\n";
            }
        }
        break;
}

echo $stJs;
?>
