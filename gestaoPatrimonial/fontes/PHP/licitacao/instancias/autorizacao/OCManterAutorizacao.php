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
    * Página de Formulario de Manter Homologacao
    * Data de Criação: 23/10/2006

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    * Casos de uso: uc-03.05.21

    $Id: OCManterAutorizacao.php 65513 2016-05-30 12:47:42Z evandro $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoHomologacao.class.php" );
include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacao.class.php" );

function montaSpanItens($rsRecordSet)
{
    $stJs = "";

    $rsRecordSet->addFormatacao ( 'vl_cotacao', 'NUMERIC_BR' );
    $rsRecordSet->addFormatacao ( 'quantidade', 'NUMERIC_BR' );

    $obLista = new Lista;
    $obLista->setTitulo( "Itens" );
    $obLista->setMostraPaginacao( false );

    if ( $rsRecordSet->getNumLinhas() >= 5 ) {
        $obLista->setMostraScroll( 150 );
    }

    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Seq" );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Item" );
    $obLista->ultimoCabecalho->setWidth( 27 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Qtde." );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Valor" );
    $obLista->ultimoCabecalho->setWidth( 13 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Fornecedor" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_item] - [item]" );
    $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "quantidade" );
    $obLista->ultimoDado->setAlinhamento( "DIREITA" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "vl_cotacao" );
    $obLista->ultimoDado->setAlinhamento( "DIREITA" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "fornecedor" );
    $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
    $obLista->commitDado();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace( "\n", "", $stHtml);
    $stHtml = str_replace( "  ", "", $stHtml);
    $stHtml = str_replace( "'" , "\\'", $stHtml);

    $stJs = "document.getElementById('spnItens').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function montaListaItensDetalhe($rsRegistros)
{
    $rsRegistros->setPrimeiroElemento();

    $rsRegistros->addFormatacao ( 'vl_unitario' , 'NUMERIC_BR' );
    $rsRegistros->addFormatacao ( 'quantidade' , 'NUMERIC_BR_4' );
    $rsRegistros->addFormatacao ( 'vl_cotacao' , 'NUMERIC_BR' );
    $rsRegistros->addFormatacao ( 'vl_total'   , 'NUMERIC_BR' );

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
    $table->Body->addCampo( 'vl_total' , 'D');

    $table->montaHTML();
    $stHTML = $table->getHtml();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    return $stHTML;
}

function montaListaItens($rsItens , $inCodLicitacao = null, $stExercicio=null , $inCodEntidade , $inCodModalidade, $codMapa)
{
    $rsItens->setPrimeiroElemento();
    $rsItens->addFormatacao ( 'quantidade' , 'NUMERIC_BR_4' );
    $rsItens->addFormatacao ( 'vl_cotacao' , 'NUMERIC_BR' );

    $table = new TableTree();

    $table->setRecordset( $rsItens );
    $table->setSummary('Itens');

    $table->setArquivo( CAM_GP_LIC_INSTANCIAS . 'autorizacao/OCManterAutorizacao.php');
    // parametros do recordSet
    $table->setParametros( array( "cod_item") );
    // 	parametros adicionais
    $stParamAdicionais  = "stCtrl=listarDetalheItem&inCodLicitacao=" . $inCodLicitacao;
    $stParamAdicionais .= "&stExercicio=" . $stExercicio;
    $stParamAdicionais .= "&inCodEntidade=" . $inCodEntidade;
    $stParamAdicionais .= "&inCodModalidade=" . $inCodModalidade;
    $stParamAdicionais .= "&inCodMapa=" . $codMapa;

    $table->setComplementoParametros( $stParamAdicionais );

    $table->Head->addCabecalho( 'Item' , 60  );
    $table->Head->addCabecalho( 'Quantidade' , 15  );
    $table->Head->addCabecalho( 'Valor Total' , 15  );

    $table->Body->addCampo( '[cod_item] - [descricao_completa]  [nome_marca]<br>[complemento]' , 'E');
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

switch ($_REQUEST['stCtrl']) {

case 'buscaInfoLicitacao':
    $obTLicitacaoHomolgacao = new TLicitacaoHomologacao;

    $stFiltro = "where homologacao.homologado       ".
                " and homologacao.cod_cotacao = " . $_REQUEST['inCodCotacao'] .
                " and  homologacao.exercicio_cotacao = '" . Sessao::getExercicio() . "'" .
                "  and not exists( select 1
                                         from licitacao.homologacao_anulada
                                        where homologacao_anulada.num_homologacao     = homologacao.num_homologacao
                                          and homologacao_anulada.cod_licitacao       = homologacao.cod_licitacao
                                          and homologacao_anulada.cod_modalidade      = homologacao.cod_modalidade
                                          and homologacao_anulada.cod_entidade        = homologacao.cod_entidade
                                          and homologacao_anulada.num_adjudicacao     = homologacao.num_adjudicacao
                                          and homologacao_anulada.exercicio_licitacao = homologacao.exercicio_licitacao
                                          and homologacao_anulada.lote                = homologacao.lote
                                          and homologacao_anulada.cod_cotacao         = homologacao.cod_cotacao
                                          and homologacao_anulada.cod_item            = homologacao.cod_item
                                          and homologacao_anulada.exercicio_cotacao   = homologacao.exercicio_cotacao
                                          and homologacao_anulada.cgm_fornecedor      = homologacao.cgm_fornecedor
                                )
                                          and not exists ( select 1
                                                             from empenho.item_pre_empenho_julgamento
                                                            where item_pre_empenho_julgamento.exercicio_julgamento = cotacao_fornecedor_item.exercicio
                                                              and item_pre_empenho_julgamento.cod_cotacao          = cotacao_fornecedor_item.cod_cotacao
                                                              and item_pre_empenho_julgamento.cod_item             = cotacao_fornecedor_item.cod_item
                                                              and item_pre_empenho_julgamento.lote                 = cotacao_fornecedor_item.lote
                                                              and item_pre_empenho_julgamento.cgm_fornecedor       = cotacao_fornecedor_item.cgm_fornecedor
                                                              and not exists( select  1
                                                                               from  empenho.autorizacao_empenho
                                                                                join empenho.autorizacao_anulada
                                                                                  on ( autorizacao_anulada.exercicio          = autorizacao_empenho.exercicio
                                                                                       and autorizacao_anulada.cod_entidade       = autorizacao_empenho.cod_entidade
                                                                                       and autorizacao_anulada.cod_autorizacao    = autorizacao_empenho.cod_autorizacao )
                                                                                     where item_pre_empenho_julgamento.exercicio       = autorizacao_empenho.exercicio
                                                                                       and item_pre_empenho_julgamento.cod_pre_empenho = autorizacao_empenho.cod_pre_empenho )
                                                        )";
    $obTLicitacaoHomolgacao->recuperaItensHomologacao ( $rsItens, $stFiltro );

    if ( $rsItens->getNumLinhas() > 0 ) {

        $arMapa = explode('/',$_REQUEST['inCodMapa']);

        $inCodMapa =  $arMapa[0];
        $stExercicioMapa = $arMapa[1];

        // busca itens do mapa, agrupados
        include_once ( CAM_GP_COM_MAPEAMENTO . 'TComprasCotacaoFornecedorItem.class.php' );
        $obTComprasCotacaoFornecedorItem = new TComprasCotacaoFornecedorItem ();

        $stFiltro = " WHERE mapa_cotacao.exercicio_mapa = '".$stExercicioMapa."'
                        AND mapa_cotacao.cod_mapa       = ".$inCodMapa."
                        AND julgamento_item.ordem       = 1 \n";
        $obTComprasCotacaoFornecedorItem->recuperaItensCotacaoJulgados ( $rsMapaItens, $stFiltro );

        // somar total do mapa
        $nuTotal = 0.00;
        while ( !$rsMapaItens->eof() ) {
            $nuTotal += $rsMapaItens->getCampo('vl_cotacao');
            $rsMapaItens->proximo();
        }
        $nuTotal = number_format($nuTotal,2,',','.');

        $rsMapaItens->setPrimeiroElemento();

        $stJs .= montaListaItens( $rsMapaItens , $_REQUEST["inCodLicitacao"] , Sessao::getExercicio() , $_REQUEST["inCodEntidade"] , $_REQUEST["inCodModalidade"],$inCodMapa  ) ;

        $stJs .= "d.getElementById('spnLabels').innerHTML = '" . $stHTML . "';";
        $stJs .= "$('stTotalMapa').innerHTML = '" . $nuTotal . "';\n";
    }
    break;

case 'listarDetalheItem' :

    $stFiltro  = " and licitacao.cod_licitacao = " . $_REQUEST["inCodLicitacao"];
    $stFiltro .= " and licitacao.cod_modalidade= " . $_REQUEST["inCodModalidade"];
    $stFiltro .= " and licitacao.cod_entidade= " . $_REQUEST["inCodEntidade"];
    $stFiltro .= " and mapa_item.cod_item = " . $_REQUEST["cod_item"];

    $obTLicitacao = new TLicitacaoLicitacao();
    $obTLicitacao->recuperaItensDetalhesAutorizacaoEmpenhoLicitacao( $rsDetalheItens , $stFiltro);

    $stJs = montaListaItensDetalhe( $rsDetalheItens );
    break;
}

echo $stJs;
?>
