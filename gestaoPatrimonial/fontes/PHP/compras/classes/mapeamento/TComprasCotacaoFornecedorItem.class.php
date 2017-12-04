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
    * Classe de mapeamento da tabela compras.cotacao_fornecedor_item
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-03.05.25
                    uc-03.05.26

    $Id: TComprasCotacaoFornecedorItem.class.php 66191 2016-07-28 14:03:35Z carlos.silva $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TComprasCotacaoFornecedorItem extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function __construct()
    {
        parent::Persistente();
        $this->setTabela("compras.cotacao_fornecedor_item");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_cotacao,cod_item,cgm_fornecedor');

        $this->AddCampo('exercicio'     ,'CHAR'     ,true , '4'     ,true ,'TComprasCotacaoItem');
        $this->AddCampo('cod_cotacao'   ,'INTEGER'  ,true , ''      ,true ,'TComprasCotacaoItem');
        $this->AddCampo('cod_item'      ,'INTEGER'  ,true , ''      ,true ,'TComprasCotacaoItem');
        $this->AddCampo('lote'          ,'INTEGER'  ,true , ''      ,true ,'TComprasCotacaoItem');
        $this->AddCampo('cgm_fornecedor','INTEGER'  ,true , ''      ,true ,'TComprasFornecedor' );
        $this->AddCampo('cod_marca'     ,'INTEGER'  ,true , ''      ,false,false                );
        $this->AddCampo('timestamp'     ,'timestamp',false, ''      ,false,false                );
        $this->AddCampo('dt_validade'   ,'DATE'     ,true , ''      ,false,false                );
        $this->AddCampo('vl_cotacao'    ,'NUMERIC'  ,true , '14.2'  ,false,false                );
    }

    function montaRecuperaRelacionamento()
    {
        $stSql = "
    select sw_cgm.nom_cgm
         , marca.descricao
         , fornecedor.tipo
         , cotacao_fornecedor_item.lote
         , cotacao_fornecedor_item.vl_cotacao as vl_total
         , cotacao_fornecedor_item.cgm_fornecedor
         , cotacao_fornecedor_item.lote
         , cotacao_item.quantidade
         , CASE WHEN cotacao_fornecedor_item_desclassificacao.cod_item IS NULL THEN
             julgamento_item.justificativa
           ELSE
             cotacao_fornecedor_item_desclassificacao.justificativa
           END as justificativa
         , cast (   ( cotacao_fornecedor_item.vl_cotacao / cotacao_item.quantidade ) as numeric(14,2)) as vl_unitario
         , catalogo_item.descricao_resumida as item
         , CASE WHEN  ( exists ( select  cotacao_fornecedor_item_desclassificacao.cgm_fornecedor
                                   from  compras.cotacao_fornecedor_item_desclassificacao
                                  where cotacao_fornecedor_item.cgm_fornecedor =  cotacao_fornecedor_item_desclassificacao.cgm_fornecedor
                                    and cotacao_fornecedor_item.cod_item       =  cotacao_fornecedor_item_desclassificacao.cod_item
                                    and cotacao_fornecedor_item.cod_cotacao    =  cotacao_fornecedor_item_desclassificacao.cod_cotacao
                                    and cotacao_fornecedor_item.exercicio      =  cotacao_fornecedor_item_desclassificacao.exercicio
                                    and cotacao_fornecedor_item.lote           =  cotacao_fornecedor_item_desclassificacao.lote )
                        or
                        exists ( select 1
                                    from compras.fornecedor_inativacao
                                   where fornecedor_inativacao.timestamp_fim is null
                                     and fornecedor_inativacao.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor )
                    )
              THEN 'desclassificado'
              ELSE 'classificado'
             END AS status
    
        , CASE WHEN (julgamento.timestamp IS NOT NULL) THEN
            'true'
          ELSE
            'false'
          END as julgado
    
        from compras.cotacao
    
        join compras.cotacao_item
          on ( cotacao.exercicio   = cotacao_item.exercicio
         and   cotacao.cod_cotacao = cotacao_item.cod_cotacao )
    
        join almoxarifado.catalogo_item
          on ( cotacao_item.cod_item = catalogo_item.cod_item )
    
        join compras.cotacao_fornecedor_item
          on ( cotacao_item.exercicio   = cotacao_fornecedor_item.exercicio
         and   cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
         and   cotacao_item.cod_item    = cotacao_fornecedor_item.cod_item
         and   cotacao_item.lote        = cotacao_fornecedor_item.lote )
    
    LEFT JOIN  compras.julgamento
           ON  cotacao_fornecedor_item.exercicio      = julgamento.exercicio
          AND  cotacao_fornecedor_item.cod_cotacao    = julgamento.cod_cotacao
    
    LEFT join compras.julgamento_item
            on ( cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
           and   cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
           and   cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
           and   cotacao_fornecedor_item.lote           = julgamento_item.lote
           and   cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor )
    
        left join compras.cotacao_fornecedor_item_desclassificacao
            on cotacao_fornecedor_item.cgm_fornecedor =  cotacao_fornecedor_item_desclassificacao.cgm_fornecedor
            and cotacao_fornecedor_item.cod_item       =  cotacao_fornecedor_item_desclassificacao.cod_item
            and cotacao_fornecedor_item.cod_cotacao    =  cotacao_fornecedor_item_desclassificacao.cod_cotacao
            and cotacao_fornecedor_item.exercicio      =  cotacao_fornecedor_item_desclassificacao.exercicio
            and cotacao_fornecedor_item.lote           =  cotacao_fornecedor_item_desclassificacao.lote
    
        join  almoxarifado.catalogo_item_marca
          on (catalogo_item_marca.cod_item  = cotacao_fornecedor_item.cod_item
         and  catalogo_item_marca.cod_marca = cotacao_fornecedor_item.cod_marca )
    
        join almoxarifado.marca
          on ( marca.cod_marca = catalogo_item_marca.cod_marca )
    
        join compras.fornecedor
          on ( cotacao_fornecedor_item.cgm_fornecedor = fornecedor.cgm_fornecedor )
    
        join sw_cgm
          on ( sw_cgm.numcgm = fornecedor.cgm_fornecedor )
        join compras.mapa_cotacao
          on ( mapa_cotacao.exercicio_cotacao = cotacao.exercicio
         and   mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao )
    
        ";
    
        return $stSql;
    
    }

    function montaRecuperaValorTotalItensLicitacao()
    {
       $stSql = " select sum(c.vl_cotacao*ci.quantidade) as valor_total             ";
       $stSql.= " from compras.cotacao as c                                         ";
       $stSql.= "     ,compras.cotacao_item as ci                                   ";
       $stSql.= "     ,compras.cotacao_fornecedor_item as cfi                       ";
       $stSql.= "     ,compras.mapa_cotacao as mc                                   ";
       $stSql.= "     ,compras.licitacao as l                                       ";
       $stSql.= " where                                                             ";
       $stSql.= "      c.exercicio = ci.exercicio                                   ";
       $stSql.= "  and c.cod_cotacacao = ci.cod_cotacao                             ";
       $stSql.= "  and ci.exercicio = cfi.exercicio                                 ";
       $stSql.= "  and ci.cod_cotacao = cfi.cod_cotacao                             ";
       $stSql.= "  and ci.cod_item = cfi.cod_item                                   ";
       $stSql.= "  and l.cod_mapa = mc.cod_mapa                                     ";
       $stSql.= "  and l.exericicio = mc.exercicio_mapa                             ";
       $stSql.= "  and cfi.cgm_fornecedor = ".$this->getDado("cgm_fornecedor")."    ";
       $stSql.= "  and l.cod_licitacao = ".$this->getDado("cod_licitacao")."        ";
       $stSql.= "  and l.exercicio = ".$this->getDado("exercicio")."                ";
       $stSql.= "  and l.cod_entidade = ".$this->getDado("cod_entidade")."          ";
       $stSql.= "  and l.cod_modalidade = ".$this->getDado("cod_modalidade")."      ";
    
       return $stSql;
    }

    function recuperaItensFornecedor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaItensFornecedor().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    function montaRecuperaItensFornecedor()
    {
        $stSql  = "
            SELECT cotacao_fornecedor_item.*
                 , marca.descricao
              FROM compras.cotacao_fornecedor_item
        INNER JOIN almoxarifado.marca
                ON cotacao_fornecedor_item.cod_marca = marca.cod_marca
             WHERE cod_cotacao = ".$this->getDado('cod_cotacao')."
               AND exercicio = '".$this->getDado('exercicio')."'
               AND cod_item = ".$this->getDado('cod_item')."
               AND cgm_fornecedor = ".$this->getDado('cgm_fornecedor')."
               AND lote = ".$this->getDado('lote')."
        ";
        return $stSql;
    }

    function recuperaFornecedoresCotacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaFornecedoresCotacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    function montaRecuperaFornecedoresCotacao()
    {
        $stSql  = " select distinct cotacao_fornecedor_item.cgm_fornecedor            \n";
        $stSql .= "      , sw_cgm.nom_cgm as fornecedor                               \n";
        $stSql .= "   from compras.cotacao_fornecedor_item                            \n";
        $stSql .= "   join sw_cgm                                                     \n";
        $stSql .= "     on sw_cgm.numcgm = cotacao_fornecedor_item.cgm_fornecedor     \n";
        $stSql .= "  where cod_cotacao = " . $this->getDado( 'cod_cotacao' ) . " and exercicio = '" . $this->getDado( 'exercicio_cotacao' ) . "' \n";

        return $stSql;
    }

    function recuperaItensFornecedorLote(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaItensFornecedorLote ().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    function montaRecuperaItensFornecedorLote()
    {
        $stSql = "
                  select cotacao_fornecedor_item.exercicio
                       , cotacao_fornecedor_item.cod_cotacao
                       , cotacao_fornecedor_item.cod_item
                       , cotacao_fornecedor_item.cgm_fornecedor
                       , cotacao_fornecedor_item.lote
                       , cotacao_fornecedor_item.cod_marca
                       , cotacao_fornecedor_item.timestamp
                       , cotacao_fornecedor_item.dt_validade
                       , cotacao_fornecedor_item.vl_cotacao
                       , CASE WHEN  exists ( select  cotacao_fornecedor_item_desclassificacao.cgm_fornecedor
                                           from  compras.cotacao_fornecedor_item_desclassificacao
                                          where cotacao_fornecedor_item.cgm_fornecedor =  cotacao_fornecedor_item_desclassificacao.cgm_fornecedor
                                            and cotacao_fornecedor_item.cod_item       =  cotacao_fornecedor_item_desclassificacao.cod_item
                                            and cotacao_fornecedor_item.cod_cotacao    =  cotacao_fornecedor_item_desclassificacao.cod_cotacao
                                            and cotacao_fornecedor_item.exercicio      =  cotacao_fornecedor_item_desclassificacao.exercicio
                                            and cotacao_fornecedor_item.lote           =  cotacao_fornecedor_item_desclassificacao.lote )
                           THEN 'desclassificado'
                           ELSE 'classificado'
                          END AS status
                    from compras.cotacao_fornecedor_item "
                  . "Where compras.cotacao_fornecedor_item.exercicio      = '" . $this->getDado('exercicio'      ) . "' \n"
                  . "  and compras.cotacao_fornecedor_item.cod_cotacao    = "  . $this->getDado('cod_cotacao'    ) . "  \n"
                  . "  and compras.cotacao_fornecedor_item.cgm_fornecedor = "  . $this->getDado('cgm_fornecedor' ) . "  \n"
                  . "  and compras.cotacao_fornecedor_item.lote           = "  . $this->getDado('lote'           ) . "  \n";
    
        return $stSql;
    
    }

    public function recuperaCotacaoEsfinge(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera( "montaRecuperaCotacaoEsfinge", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    }

    public function montaRecuperaCotacaoEsfinge()
    {
        $stSql = "
                select case when sw_cgm_pessoa_fisica.numcgm is not     null then '01'
                            when sw_cgm_pessoa_juridica.numcgm is not null then '02'
                            else '00'
                       end as tipo_pessoa
                     , case when sw_cgm_pessoa_fisica.numcgm is not null then sw_cgm_pessoa_fisica.cpf
                            when sw_cgm_pessoa_juridica.numcgm is not null then sw_cgm_pessoa_juridica.cnpj
                            else ''
                       end as cic_participante
                     , licitacao.cod_licitacao
                     , mapa_item.cod_item
                     , mapa_item.quantidade
                     , cotacao_fornecedor_item.vl_cotacao
                     , julgamento_item.ordem
                from licitacao.licitacao

                join compras.mapa
                on mapa.exercicio = licitacao.exercicio_mapa
                and mapa.cod_mapa = licitacao.cod_mapa

                join compras.mapa_solicitacao
                on mapa_solicitacao.exercicio = mapa.exercicio
                and mapa_solicitacao.cod_mapa = mapa.cod_mapa

                join compras.mapa_item
                on mapa_item.exercicio              = mapa_solicitacao.exercicio
                and mapa_item.cod_entidade          = mapa_solicitacao.cod_entidade
                and mapa_item.cod_solicitacao       = mapa_solicitacao.cod_solicitacao
                and mapa_item.cod_mapa              = mapa_solicitacao.cod_mapa
                and mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao

                join compras.mapa_cotacao
                on mapa_cotacao.exercicio_mapa = mapa.exercicio
                and mapa_cotacao.cod_mapa      = mapa.cod_mapa

                join compras.cotacao
                on cotacao.exercicio = mapa_cotacao.exercicio_cotacao
                and cotacao.cod_cotacao = mapa_cotacao.cod_cotacao

                join compras.cotacao_item
                on cotacao_item.exercicio = cotacao.exercicio
                and cotacao_item.cod_cotacao = cotacao.cod_cotacao
                and cotacao_item.lote = mapa_item.lote
                and cotacao_item.cod_item = mapa_item.cod_item

                join compras.cotacao_fornecedor_item
                on cotacao_fornecedor_item.exercicio    = cotacao_item.exercicio
                and cotacao_fornecedor_item.cod_cotacao = cotacao_item.cod_cotacao
                and cotacao_fornecedor_item.cod_item    = cotacao_item.cod_item
                and cotacao_fornecedor_item.lote        = cotacao_item.lote

                join sw_cgm
                on sw_cgm.numcgm = cotacao_fornecedor_item.cgm_fornecedor

                left join sw_cgm_pessoa_fisica
                on sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                left join sw_cgm_pessoa_juridica
                on sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm


                join compras.julgamento_item
                on julgamento_item.exercicio = cotacao_fornecedor_item.exercicio
                and julgamento_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                and julgamento_item.cod_item = cotacao_fornecedor_item.cod_item
                and julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                and julgamento_item.lote = cotacao_fornecedor_item.lote

                where licitacao.exercicio = '".$this->getDado( 'exercicio' )."'
                and licitacao.cod_entidade in ( ".$this->getDado( 'cod_entidade' )." )
                and licitacao.timestamp >= to_date( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' )
                and licitacao.timestamp <= to_date( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )
        ";

    return $stSql;

}

    function recuperaItensCotacaoJulgados(&$rsRecordSet,$stFiltro='',$stOrder='',$boTransacao='')
    {
        $stOrder = " GROUP BY catalogo_item.descricao_resumida
                            , catalogo_item.descricao
                            , marca.cod_marca
                            , marca.descricao 
                            , cotacao_item.cod_item
                            , cotacao_item.quantidade
                            , cotacao_item.lote
                            , cotacao_item.cod_cotacao
                            , mapa_cotacao.cod_mapa
                            , mapa_cotacao.exercicio_mapa
                            , cotacao_fornecedor_item.vl_cotacao
                    
                     ORDER BY cotacao_item.cod_item ";

        return $this->executaRecupera("montaRecuperaItensCotacaoJulgados",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    function montaRecuperaItensCotacaoJulgados()
    {
        $stSql = "
                    SELECT catalogo_item.descricao_resumida
                         , catalogo_item.descricao as descricao_completa
                         , CASE WHEN marca.cod_marca IS NOT NULL
                                THEN '  - Marca:( '||marca.cod_marca||' - '||marca.descricao||' )' 
                                ELSE ''
                         END AS nome_marca
                         , cotacao_item.cod_item
                         , cotacao_item.quantidade
                         , cotacao_item.lote
                         , cotacao_item.cod_cotacao
                         , mapa_cotacao.cod_mapa
                         , mapa_cotacao.exercicio_mapa as exercicio
                         , cotacao_fornecedor_item.vl_cotacao

                      FROM compras.cotacao_item

                INNER JOIN (
                                SELECT adjudicacao.num_adjudicacao
                                    , adjudicacao.timestamp
                                    , adjudicacao.cod_licitacao
                                    , adjudicacao.cod_modalidade
                                    , adjudicacao.cod_entidade
                                    , adjudicacao.exercicio_licitacao
                                    , adjudicacao.lote
                                    , adjudicacao.cod_cotacao
                                    , adjudicacao.cgm_fornecedor
                                    , adjudicacao.cod_item
                                    , adjudicacao.exercicio_cotacao
                                    , adjudicacao.cod_documento
                                    , adjudicacao.cod_tipo_documento
                                    , adjudicacao.adjudicado
                                FROM licitacao.adjudicacao

                           LEFT JOIN licitacao.adjudicacao_anulada
                                  ON adjudicacao_anulada.num_adjudicacao     = adjudicacao.num_adjudicacao
                                 AND adjudicacao_anulada.cod_entidade        = adjudicacao.cod_entidade
                                 AND adjudicacao_anulada.cod_modalidade      = adjudicacao.cod_modalidade
                                 AND adjudicacao_anulada.cod_licitacao       = adjudicacao.cod_licitacao
                                 AND adjudicacao_anulada.exercicio_licitacao = adjudicacao.exercicio_licitacao
                                 AND adjudicacao_anulada.cod_item            = adjudicacao.cod_item
                                 AND adjudicacao_anulada.cgm_fornecedor      = adjudicacao.cgm_fornecedor
                                 AND adjudicacao_anulada.cod_cotacao         = adjudicacao.cod_cotacao
                                 AND adjudicacao_anulada.lote                = adjudicacao.lote
                                 AND adjudicacao_anulada.exercicio_cotacao   = adjudicacao.exercicio_cotacao

                               WHERE adjudicacao_anulada.num_adjudicacao IS NULL
                          ) AS adjudicacao
                         ON cotacao_item.exercicio   = adjudicacao.exercicio_cotacao
                        AND cotacao_item.cod_cotacao = adjudicacao.cod_cotacao
                        AND cotacao_item.lote        = adjudicacao.lote
                        AND cotacao_item.cod_item    = adjudicacao.cod_item

                      JOIN almoxarifado.catalogo_item
                        ON cotacao_item.cod_item = catalogo_item.cod_item
    
                      JOIN compras.cotacao_fornecedor_item
                        ON   cotacao_fornecedor_item.exercicio   = cotacao_item.exercicio
                       AND   cotacao_fornecedor_item.cod_cotacao = cotacao_item.cod_cotacao
                       AND   cotacao_fornecedor_item.cod_item    = cotacao_item.cod_item
                       AND   cotacao_fornecedor_item.lote        = cotacao_item.lote

                      JOIN compras.mapa_cotacao
                        ON   cotacao_item.cod_cotacao = mapa_cotacao.cod_cotacao
                       AND   cotacao_item.exercicio   = mapa_cotacao .exercicio_cotacao

                      JOIN compras.julgamento_item
                        ON   cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
                       AND   cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
                       AND   cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
                       AND   cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                       AND   cotacao_fornecedor_item.lote           = julgamento_item.lote

                      JOIN  compras.mapa
                        ON  mapa_cotacao.cod_mapa       = mapa.cod_mapa
                       AND  mapa_cotacao.exercicio_mapa = mapa.exercicio

                      JOIN  compras.mapa_item
                        ON  mapa_item.exercicio = mapa.exercicio
                       AND  mapa_item.cod_mapa  = mapa.cod_mapa
                       AND  mapa_item.cod_item  = cotacao_fornecedor_item.cod_item
                       AND  mapa_item.lote      = cotacao_fornecedor_item.lote

                      JOIN  compras.mapa_solicitacao
                        ON  mapa_solicitacao.exercicio             = mapa_item.exercicio
                       AND  mapa_solicitacao.cod_entidade          = mapa_item.cod_entidade
                       AND  mapa_solicitacao.cod_solicitacao       = mapa_item.cod_solicitacao
                       AND  mapa_solicitacao.cod_mapa              = mapa_item.cod_mapa
                       AND  mapa_solicitacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                      
                      LEFT JOIN almoxarifado.catalogo_item_marca
                         ON catalogo_item_marca.cod_item         = cotacao_fornecedor_item.cod_item
                        AND catalogo_item_marca.cod_marca        = cotacao_fornecedor_item.cod_marca
                      
                      LEFT JOIN almoxarifado.marca
                         ON marca.cod_marca = catalogo_item_marca.cod_marca
                 ";
    
        return $stSql;
    }

    function recuperaItensCotacaoJulgadosAutorizacaoParcial(&$rsRecordSet,$stFiltro='',$stOrder='',$boTransacao='')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        
        $stGroupBy = " GROUP BY catalogo_item.descricao_resumida
                              , catalogo_item.descricao
                              , cotacao_item.cod_item
                              , cotacao_item.quantidade
                              , cotacao_item.lote
                              , cotacao_item.cod_cotacao
                              , mapa_cotacao.cod_mapa
                              , mapa_cotacao.exercicio_mapa
                              , cotacao_fornecedor_item.vl_cotacao
                              , julgamento_item.cgm_fornecedor
                              , sw_cgm.nom_cgm
                              , julgamento_item.ordem ";
        if($stOrdem=='')
            $stOrder = " ORDER BY cotacao_item.cod_item ";

        $stSql  = $this->montaRecuperaItensCotacaoJulgadosAutorizacaoParcial().$stFiltro.$stGroupBy.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    function montaRecuperaItensCotacaoJulgadosAutorizacaoParcial()
    {
        $stSql = "
                    SELECT catalogo_item.descricao_resumida
                         , catalogo_item.descricao as descricao_completa
                         , cotacao_item.cod_item
                         , cotacao_item.quantidade
                         , cotacao_item.lote
                         , cotacao_item.cod_cotacao
                         , mapa_cotacao.cod_mapa
                         , mapa_cotacao.exercicio_mapa as exercicio
                         , cotacao_fornecedor_item.vl_cotacao
                         , ((coalesce(cotacao_item.quantidade, 0.00) - sum(coalesce(item_pre_empenho.quantidade, 0.00)))
                           *
                           (cotacao_fornecedor_item.vl_cotacao / coalesce(cotacao_item.quantidade, 0.00)))::numeric(14,2) as vl_cotacao_saldo
                         , coalesce(cotacao_item.quantidade, 0.00) - sum(coalesce(item_pre_empenho.quantidade, 0.00)) as quantidade_saldo
                         , julgamento_item.cgm_fornecedor
                         , sw_cgm.nom_cgm AS fornecedor
                         , julgamento_item.ordem

                      FROM compras.cotacao_item

                INNER JOIN (
                               SELECT adjudicacao.num_adjudicacao
                                    , adjudicacao.timestamp
                                    , adjudicacao.cod_licitacao
                                    , adjudicacao.cod_modalidade
                                    , adjudicacao.cod_entidade
                                    , adjudicacao.exercicio_licitacao
                                    , adjudicacao.lote
                                    , adjudicacao.cod_cotacao
                                    , adjudicacao.cgm_fornecedor
                                    , adjudicacao.cod_item
                                    , adjudicacao.exercicio_cotacao
                                    , adjudicacao.cod_documento
                                    , adjudicacao.cod_tipo_documento
                                    , adjudicacao.adjudicado
                                FROM licitacao.adjudicacao
                           LEFT JOIN licitacao.adjudicacao_anulada
                                  ON adjudicacao_anulada.num_adjudicacao     = adjudicacao.num_adjudicacao
                                 AND adjudicacao_anulada.cod_entidade        = adjudicacao.cod_entidade
                                 AND adjudicacao_anulada.cod_modalidade      = adjudicacao.cod_modalidade
                                 AND adjudicacao_anulada.cod_licitacao       = adjudicacao.cod_licitacao
                                 AND adjudicacao_anulada.exercicio_licitacao = adjudicacao.exercicio_licitacao
                                 AND adjudicacao_anulada.cod_item            = adjudicacao.cod_item
                                 AND adjudicacao_anulada.cgm_fornecedor      = adjudicacao.cgm_fornecedor
                                 AND adjudicacao_anulada.cod_cotacao         = adjudicacao.cod_cotacao
                                 AND adjudicacao_anulada.lote                = adjudicacao.lote
                                 AND adjudicacao_anulada.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                          INNER JOIN licitacao.homologacao
                                  ON adjudicacao.num_adjudicacao     = homologacao.num_adjudicacao
                                 AND adjudicacao.cod_entidade        = homologacao.cod_entidade
                                 AND adjudicacao.cod_modalidade      = homologacao.cod_modalidade
                                 AND adjudicacao.cod_licitacao       = homologacao.cod_licitacao
                                 AND adjudicacao.exercicio_licitacao = homologacao.exercicio_licitacao
                                 AND adjudicacao.cod_item            = homologacao.cod_item
                                 AND adjudicacao.cod_cotacao         = homologacao.cod_cotacao
                                 AND adjudicacao.lote                = homologacao.lote
                                 AND adjudicacao.exercicio_cotacao   = homologacao.exercicio_cotacao
                                 AND adjudicacao.cgm_fornecedor      = homologacao.cgm_fornecedor
                           LEFT JOIN licitacao.homologacao_anulada
                                  ON homologacao_anulada.num_homologacao     = homologacao.num_homologacao
                                 AND homologacao_anulada.cod_licitacao       = homologacao.cod_licitacao
                                 AND homologacao_anulada.cod_modalidade      = homologacao.cod_modalidade
                                 AND homologacao_anulada.cod_entidade        = homologacao.cod_entidade
                                 AND homologacao_anulada.num_adjudicacao     = homologacao.num_adjudicacao
                                 AND homologacao_anulada.exercicio_licitacao = homologacao.exercicio_licitacao
                                 AND homologacao_anulada.lote                = homologacao.lote
                                 AND homologacao_anulada.cod_cotacao         = homologacao.cod_cotacao
                                 AND homologacao_anulada.cod_item            = homologacao.cod_item
                                 AND homologacao_anulada.exercicio_cotacao   = homologacao.exercicio_cotacao
                                 AND homologacao_anulada.cgm_fornecedor      = homologacao.cgm_fornecedor
                               WHERE adjudicacao_anulada.num_adjudicacao IS NULL
                                 AND homologacao_anulada.num_homologacao IS NULL
                           ) AS adjudicacao
                        ON cotacao_item.exercicio   = adjudicacao.exercicio_cotacao
                       AND cotacao_item.cod_cotacao = adjudicacao.cod_cotacao
                       AND cotacao_item.lote        = adjudicacao.lote
                       AND cotacao_item.cod_item    = adjudicacao.cod_item

                INNER JOIN almoxarifado.catalogo_item
                        ON cotacao_item.cod_item = catalogo_item.cod_item

                INNER JOIN compras.cotacao_fornecedor_item
                        ON cotacao_fornecedor_item.exercicio        = cotacao_item.exercicio
                       AND cotacao_fornecedor_item.cod_cotacao      = cotacao_item.cod_cotacao
                       AND cotacao_fornecedor_item.cod_item         = cotacao_item.cod_item
                       AND cotacao_fornecedor_item.lote             = cotacao_item.lote
                       AND cotacao_fornecedor_item.cgm_fornecedor   = adjudicacao.cgm_fornecedor

                INNER JOIN compras.mapa_cotacao
                        ON cotacao_item.cod_cotacao = mapa_cotacao.cod_cotacao
                       AND cotacao_item.exercicio   = mapa_cotacao .exercicio_cotacao

                INNER JOIN compras.julgamento_item
                        ON cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
                       AND cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
                       AND cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
                       AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                       AND cotacao_fornecedor_item.lote           = julgamento_item.lote

                INNER JOIN compras.mapa
                        ON mapa_cotacao.cod_mapa       = mapa.cod_mapa
                       AND mapa_cotacao.exercicio_mapa = mapa.exercicio

                INNER JOIN compras.mapa_item
                        ON mapa_item.exercicio = mapa.exercicio
                       AND mapa_item.cod_mapa  = mapa.cod_mapa
                       AND mapa_item.cod_item  = cotacao_fornecedor_item.cod_item
                       AND mapa_item.lote      = cotacao_fornecedor_item.lote

                INNER JOIN compras.mapa_solicitacao
                        ON mapa_solicitacao.exercicio             = mapa_item.exercicio
                       AND mapa_solicitacao.cod_entidade          = mapa_item.cod_entidade
                       AND mapa_solicitacao.cod_solicitacao       = mapa_item.cod_solicitacao
                       AND mapa_solicitacao.cod_mapa              = mapa_item.cod_mapa
                       AND mapa_solicitacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao

                 LEFT JOIN compras.mapa_item_dotacao
                        ON mapa_item.exercicio             = mapa_item_dotacao.exercicio
                       AND mapa_item.cod_mapa              = mapa_item_dotacao.cod_mapa
                       AND mapa_item.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
                       AND mapa_item.cod_entidade          = mapa_item_dotacao.cod_entidade
                       AND mapa_item.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
                       AND mapa_item.cod_centro            = mapa_item_dotacao.cod_centro
                       AND mapa_item.cod_item              = mapa_item_dotacao.cod_item
                       AND mapa_item.lote                  = mapa_item_dotacao.lote  

                 LEFT JOIN empenho.item_pre_empenho_julgamento
                        ON item_pre_empenho_julgamento.exercicio_julgamento = adjudicacao.exercicio_cotacao
                       AND item_pre_empenho_julgamento.cod_cotacao          = adjudicacao.cod_cotacao
                       AND item_pre_empenho_julgamento.cod_item             = adjudicacao.cod_item
                       AND item_pre_empenho_julgamento.lote                 = adjudicacao.lote
                       AND item_pre_empenho_julgamento.cgm_fornecedor       = adjudicacao.cgm_fornecedor

                 LEFT JOIN ( SELECT item_pre_empenho.*
                                  , pre_empenho_despesa.cod_despesa
                               FROM empenho.item_pre_empenho
                         INNER JOIN empenho.pre_empenho_despesa
                                 ON pre_empenho_despesa.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                                AND pre_empenho_despesa.exercicio       = item_pre_empenho.exercicio
                           ) AS item_pre_empenho
                        ON item_pre_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho
                       AND item_pre_empenho.exercicio       = item_pre_empenho_julgamento.exercicio
                       AND item_pre_empenho.num_item        = item_pre_empenho_julgamento.num_item
                       AND (     mapa_item_dotacao.cod_despesa IS NULL
                             OR  item_pre_empenho.cod_despesa  = mapa_item_dotacao.cod_despesa
                           )

                INNER JOIN sw_cgm
                        ON sw_cgm.numcgm = julgamento_item.cgm_fornecedor
                 ";

        return $stSql;
    }

    function recuperaItensCotacaoJulgadosCompraDireta(&$rsRecordSet,$stFiltro='',$stOrder='',$boTransacao='')
    {
        $stOrder = " GROUP BY catalogo_item.descricao_resumida
                            , catalogo_item.descricao
                            , marca.cod_marca
                            , marca.descricao 
                            , cotacao_item.cod_item
                            , cotacao_item.quantidade
                            , cotacao_item.lote
                            , cotacao_item.cod_cotacao
                            , mapa_cotacao.cod_mapa
                            , mapa_cotacao.exercicio_mapa
                            , cotacao_fornecedor_item.vl_cotacao
                    
                     ORDER BY cotacao_item.cod_item ";

        return $this->executaRecupera("montaRecuperaItensCotacaoJulgadosCompraDireta",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    function montaRecuperaItensCotacaoJulgadosCompraDireta()
    {
        $stSql = "
                     SELECT catalogo_item.descricao_resumida
                          , catalogo_item.descricao as descricao_completa
                          , CASE WHEN marca.cod_marca IS NOT NULL
                                THEN '  - Marca:( '||marca.cod_marca||' - '||marca.descricao||' )' 
                                ELSE ''
                            END AS nome_marca
                          , cotacao_item.cod_item
                          , cotacao_item.quantidade
                          , cotacao_item.lote
                          , cotacao_item.cod_cotacao
                          , mapa_cotacao.cod_mapa
                          , mapa_cotacao.exercicio_mapa as exercicio
                          , cotacao_fornecedor_item.vl_cotacao

                       FROM compras.cotacao_item

                 INNER JOIN almoxarifado.catalogo_item
                         ON cotacao_item.cod_item = catalogo_item.cod_item

                 INNER JOIN compras.cotacao_fornecedor_item
                         ON cotacao_fornecedor_item.exercicio   = cotacao_item.exercicio
                        AND cotacao_fornecedor_item.cod_cotacao = cotacao_item.cod_cotacao
                        AND cotacao_fornecedor_item.cod_item    = cotacao_item.cod_item
                        AND cotacao_fornecedor_item.lote        = cotacao_item.lote

                 INNER JOIN compras.mapa_cotacao
                         ON cotacao_item.cod_cotacao = mapa_cotacao.cod_cotacao
                        AND cotacao_item.exercicio   = mapa_cotacao .exercicio_cotacao

                 INNER JOIN compras.julgamento_item
                         ON cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
                        AND cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
                        AND cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
                        AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                        AND cotacao_fornecedor_item.lote           = julgamento_item.lote

                 INNER JOIN compras.mapa
                         ON mapa_cotacao.cod_mapa       = mapa.cod_mapa
                        AND mapa_cotacao.exercicio_mapa = mapa.exercicio

                 INNER JOIN compras.mapa_item
                         ON mapa_item.exercicio = mapa.exercicio
                        AND mapa_item.cod_mapa  = mapa.cod_mapa
                        AND mapa_item.cod_item  = cotacao_fornecedor_item.cod_item
                        AND mapa_item.lote      = cotacao_fornecedor_item.lote

                 INNER JOIN compras.mapa_solicitacao
                         ON mapa_solicitacao.exercicio             = mapa_item.exercicio
                        AND mapa_solicitacao.cod_entidade          = mapa_item.cod_entidade
                        AND mapa_solicitacao.cod_solicitacao       = mapa_item.cod_solicitacao
                        AND mapa_solicitacao.cod_mapa              = mapa_item.cod_mapa
                        AND mapa_solicitacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                
                 LEFT JOIN almoxarifado.catalogo_item_marca
                         ON catalogo_item_marca.cod_item         = cotacao_fornecedor_item.cod_item
                        AND catalogo_item_marca.cod_marca       = cotacao_fornecedor_item.cod_marca

                 LEFT JOIN almoxarifado.marca
                        ON marca.cod_marca = catalogo_item_marca.cod_marca

                 ";
    
        return $stSql;
    }

    function recuperaTotaisLoteFornecedor(&$rsRecordSet,$stFiltro='',$stOrder='',$boTransacao='')
    {
        $stOrder = "group by cotacao_fornecedor_item.exercicio
                         ,   cotacao_fornecedor_item.cod_cotacao
                         ,   cotacao_fornecedor_item.lote
                         , cotacao_fornecedor_item.cgm_fornecedor
                    order by vl_total";

        return $this->executaRecupera( "montaRecuperaTotaisLoteFornecedor",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    function montaRecuperaTotaisLoteFornecedor()
    {
        $stSql = "select cotacao_fornecedor_item.exercicio
                       , cotacao_fornecedor_item.cod_cotacao
                       , cotacao_fornecedor_item.lote
                       , cotacao_fornecedor_item.cgm_fornecedor
                       , sum ( cotacao_fornecedor_item.vl_cotacao ) as vl_total
                    from compras.cotacao_fornecedor_item ";
    
        return $stSql;
    }

    function recuperaUltimosItens(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaUltimosItens().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    function montaRecuperaUltimosItens()
    {
        $stSql = "SELECT * FROM compras.cotacao_fornecedor_item
                  WHERE 1=1";

        return $stSql;
    }

    function recuperaCotacaoFornecedorItem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaCotacaoFornecedorItem().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    function montaRecuperaCotacaoFornecedorItem()
    {
        $stSql = '    SELECT *
          FROM almoxarifado.catalogo_item_marca
    INNER JOIN compras.cotacao_fornecedor_item
            ON catalogo_item_marca.cod_item = cotacao_fornecedor_item.cod_item
           AND catalogo_item_marca.cod_marca = cotacao_fornecedor_item.cod_marca
         WHERE catalogo_item_marca.cod_item = '.$this->getDado('cod_item');

         return $stSql;
    }

    //inclusão para ser usada no OC onde não deve demonstrar debug
    function inclusaoNoDebug($boTransacao = '')
    {
      $obErro     = new Erro;
        $obConexao  = new Transacao;//Conexao;

        if ( !$obErro->ocorreu() ) {
            $stSql = $this->montaInclusao( $boTransacao, $arBlob, $obConexao );
            if ($arBlob["qtd_blob"]) {
                $boTranFalse = false;
                if ( !Sessao::getTrataExcecao() && !$boTransacao) {
                    $boTransacao = true;
                    $boTranFalse = true;
                }

                $obErro = $obConexao->__executaDML( $stSql, $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    for ($inX=0; $inX<$arBlob["qtd_blob"]; $inX++) {
                        $obConexao->gravaBlob( $arBlob["blob_oid"][$inX], $arBlob["blob"][$inX] );
                    }

                    if ($boTranFalse) {
                        $obConexao->fechaTransacao( $boTranFalse, $boTransacao, $obErro );
                    }
                }
            }else
                $obErro = $obConexao->__executaDML( $stSql, $boTransacao );
        }

        return $obErro;
    }
}
