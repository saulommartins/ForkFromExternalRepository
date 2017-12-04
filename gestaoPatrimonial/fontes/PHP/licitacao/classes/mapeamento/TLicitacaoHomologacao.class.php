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
    * Classe de mapeamento da tabela licitacao.homologacao
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-03.05.21

    $Id: TLicitacaoHomologacao.class.php 65449 2016-05-23 18:17:48Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TLicitacaoHomologacao extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    **/
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela("licitacao.homologacao");

        $this->setCampoCod('num_homologacao');
        $this->setComplementoChave(' cod_licitacao, cod_modalidade, cod_entidade, num_adjudicacao, exercicio_licitacao, lote, cod_cotacao, cod_item, exercicio_cotacao, cgm_fornecedor');

        $this->AddCampo( 'num_homologacao'      , 'integer'  , true , '' , true , false );
        $this->AddCampo( 'timestamp'            , 'char'     , false, '' , true , false );
        $this->AddCampo( 'cod_licitacao'        , 'integer'  , true , '' , true , 'TLicitacaoAdjudicacao' );
        $this->AddCampo( 'cod_modalidade'       , 'integer'  , true , '' , true , 'TLicitacaoAdjudicacao' );
        $this->AddCampo( 'cod_entidade'         , 'integer'  , true , '' , true , 'TLicitacaoAdjudicacao' );
        $this->AddCampo( 'num_adjudicacao'      , 'integer'  , true , '' , true , 'TLicitacaoAdjudicacao' );
        $this->AddCampo( 'exercicio_licitacao'  , 'char'     , true , '4', true , 'TLicitacaoAdjudicacao' );
        $this->AddCampo( 'lote'                 , 'integer'  , true , '' , true , 'TLicitacaoAdjudicacao' );
        $this->AddCampo( 'cod_cotacao'          , 'integer'  , true , '' , true , 'TLicitacaoAdjudicacao' );
        $this->AddCampo( 'cgm_fornecedor'       , 'integer'  , true , '' , true , 'TLicitacaoAdjudicacao' );
        $this->AddCampo( 'cod_item'             , 'integer'  , true , '' , true , 'TLicitacaoAdjudicacao' );
        $this->AddCampo( 'exercicio_cotacao'    , 'char'     , true , '4', true , 'TLicitacaoAdjudicacao' );
        $this->AddCampo( 'cod_tipo_documento'   , 'integer'  , true , '' , false, false );
        $this->AddCampo( 'cod_documento'        , 'integer'  , true , '' , false, false );
        $this->AddCampo( 'homologado'           , 'boolean'  , true , '' , false, false );

    }

    public function recuperaItensComStatus(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if( $this->obTLicitacaoAdjudicacao->obTLicitacaoCotacaoLicitacao->obTLicitacaoLicitacao->getDado( "exercicio" ) != "" )
            $stFiltro .= "and adjudicacao.exercicio_licitacao = '".$this->obTLicitacaoAdjudicacao->obTLicitacaoCotacaoLicitacao->obTLicitacaoLicitacao->getDado( "exercicio" )."' \n";
        if( $this->obTLicitacaoAdjudicacao->getDado( "cod_entidade" ) != "" )
            $stFiltro .= "and adjudicacao.cod_entidade   = ".$this->obTLicitacaoAdjudicacao->getDado( "cod_entidade" )." \n";
        if( $this->obTLicitacaoAdjudicacao->getDado( "cod_modalidade" ) != "" )
            $stFiltro .= "and adjudicacao.cod_modalidade = ".$this->obTLicitacaoAdjudicacao->getDado( "cod_modalidade" )." \n";
        if( $this->obTLicitacaoAdjudicacao->getDado( "cod_licitacao" ) != "" )
            $stFiltro .= "and adjudicacao.cod_licitacao  = ".$this->obTLicitacaoAdjudicacao->getDado( "cod_licitacao" )." \n";

        if( $stFiltro )
            $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro)-4);

        $stFiltro .= "
        group by
              homologacao.num_homologacao
            , adjudicacao.cod_item
            , homologacao.cod_tipo_documento
            , homologacao.cod_documento
            , adjudicacao.cod_licitacao
            , adjudicacao.cod_modalidade
            , adjudicacao.cod_entidade
            , adjudicacao.num_adjudicacao
            , adjudicacao.timestamp
            , adjudicacao.exercicio_licitacao
            , adjudicacao.lote
            , adjudicacao.cod_cotacao
            , adjudicacao.exercicio_cotacao
            , adjudicacao.cgm_fornecedor
            , sw_cgm.nom_cgm
            , cotacao_item.quantidade
            , cotacao_fornecedor_item.vl_cotacao
            , catalogo_item.descricao_resumida
            , catalogo_item.descricao
            , adjudicacao.adjudicado
            , homologacao.homologado
            , homologacao.motivo
            , homologacao.revogacao
            , homologacao.num_adjudicacao_anulada
            , julgamento_item.exercicio
            , julgamento_item.cod_cotacao
            , julgamento_item.cod_item
            , julgamento_item.lote
            , julgamento_item.cgm_fornecedor
            , mapa_item.exercicio
            , mapa_item.cod_mapa
            , homologacao.timestamp
            , autorizacao_empenho.cod_autorizacao
            , autorizacao_empenho.exercicio 
            ";

        $stOrdem = "order by adjudicacao.cod_item, catalogo_item.descricao";

        $stSql  = $this->montaRecuperaItensComStatus().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaItensComStatus()
    {
        $stSql = "
                select homologacao.num_homologacao
                     , homologacao.cod_tipo_documento
                     , homologacao.cod_documento
                     , adjudicacao.cod_licitacao
                     , adjudicacao.cod_modalidade
                     , adjudicacao.cod_entidade
                     , adjudicacao.num_adjudicacao
                     , adjudicacao.timestamp as timestamp_adjudicacao
                     , homologacao.timestamp as timestamp_homologacao
                     , adjudicacao.exercicio_licitacao
                     , adjudicacao.lote
                     , adjudicacao.cod_cotacao
                     , adjudicacao.exercicio_cotacao
                     , adjudicacao.cgm_fornecedor
                     , sw_cgm.nom_cgm
                     , mapa_item.exercicio as exercicio_mapa
                     , mapa_item.cod_mapa
                     , cotacao_item.quantidade
                     , cotacao_fornecedor_item.vl_cotacao
                     --, sum(mapa_item.quantidade) - coalesce(sum(mapa_item_anulacao.quantidade),0) as quantidade
                     , publico.fn_numeric_br(sum(mapa_item.vl_total) - coalesce(sum(mapa_item_anulacao.vl_total),0)) as vl_total

                     , ((sum(mapa_item.vl_total) - coalesce(sum(mapa_item_anulacao.vl_total),0)) / (sum(mapa_item.quantidade) - coalesce(sum(mapa_item_anulacao.quantidade),0)))::numeric(14,2) as vl_unitario_referencia

                     , adjudicacao.cod_item
                     , catalogo_item.descricao_resumida
                     , catalogo_item.descricao
                     , case when ( not homologacao.num_adjudicacao_anulada is null )
                            then 'Anulada'
                            else case when ( not homologacao.homologado or homologacao.homologado is null )
                                      then 'A Homologar'
                                      else  case when not exists ( select 1
                                                                  from empenho.item_pre_empenho_julgamento
                                                                 where item_pre_empenho_julgamento.exercicio       = julgamento_item.exercicio
                                                                   and item_pre_empenho_julgamento.cod_cotacao     = julgamento_item.cod_cotacao
                                                                   and item_pre_empenho_julgamento.cod_item        = julgamento_item.cod_item
                                                                   and item_pre_empenho_julgamento.lote            = julgamento_item.lote
                                                                   and item_pre_empenho_julgamento.cgm_fornecedor  = julgamento_item.cgm_fornecedor )
                                                 then 'Homologado'
                                                 else 'Homologado e Autorizado '|| autorizacao_empenho.cod_autorizacao||'/'||autorizacao_empenho.exercicio 
                                            end
                                 end
                       end as status
                     , adjudicacao.adjudicado
                     , homologacao.homologado
                     , homologacao.motivo as justificativa_anulacao
                     , homologacao.revogacao
                     , homologacao.num_adjudicacao_anulada
                  from (
                               select adjudicacao.num_adjudicacao
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
                                 from licitacao.adjudicacao
                            left join licitacao.adjudicacao_anulada
                                   on adjudicacao_anulada.num_adjudicacao       = adjudicacao.num_adjudicacao
                                  and adjudicacao_anulada.cod_entidade          = adjudicacao.cod_entidade
                                  and adjudicacao_anulada.cod_modalidade        = adjudicacao.cod_modalidade
                                  and adjudicacao_anulada.cod_licitacao         = adjudicacao.cod_licitacao
                                  and adjudicacao_anulada.exercicio_licitacao   = adjudicacao.exercicio_licitacao
                                  and adjudicacao_anulada.cod_item              = adjudicacao.cod_item
                                  and adjudicacao_anulada.cgm_fornecedor        = adjudicacao.cgm_fornecedor
                                  and adjudicacao_anulada.cod_cotacao           = adjudicacao.cod_cotacao
                                  and adjudicacao_anulada.lote                  = adjudicacao.lote
                                  and adjudicacao_anulada.exercicio_cotacao     = adjudicacao.exercicio_cotacao
                                where adjudicacao_anulada.num_adjudicacao is null
                       ) as adjudicacao

                  join compras.cotacao_item
                    on cotacao_item.exercicio   = adjudicacao.exercicio_cotacao
                   and cotacao_item.cod_cotacao = adjudicacao.cod_cotacao
                   and cotacao_item.lote        = adjudicacao.lote
                   and cotacao_item.cod_item    = adjudicacao.cod_item

                  join compras.cotacao
                    on cotacao.cod_cotacao = cotacao_item.cod_cotacao
                   and cotacao.exercicio = cotacao_item.exercicio

                  join compras.mapa_cotacao
                    on mapa_cotacao.cod_cotacao = cotacao.cod_cotacao
                   and mapa_cotacao.exercicio_cotacao = cotacao.exercicio

                  join compras.cotacao_fornecedor_item
                    on cotacao_fornecedor_item.exercicio      = adjudicacao.exercicio_cotacao
                   and cotacao_fornecedor_item.cod_cotacao    = adjudicacao.cod_cotacao
                   and cotacao_fornecedor_item.cod_item       = adjudicacao.cod_item
                   and cotacao_fornecedor_item.cgm_fornecedor = adjudicacao.cgm_fornecedor
                   and cotacao_fornecedor_item.lote           = adjudicacao.lote

                  join compras.mapa_item
                    on mapa_item.cod_mapa = mapa_cotacao.cod_mapa
                   and mapa_item.exercicio = mapa_cotacao.exercicio_mapa
                   and mapa_item.cod_item = cotacao_fornecedor_item.cod_item
                   and mapa_item.lote = cotacao_fornecedor_item.lote

                  left join compras.mapa_item_anulacao
                        on mapa_item.exercicio  = mapa_item_anulacao.exercicio
                       and mapa_item.exercicio_solicitacao  = mapa_item_anulacao.exercicio_solicitacao
                       and mapa_item.cod_mapa   = mapa_item_anulacao.cod_mapa
                       and mapa_item.cod_entidade   = mapa_item_anulacao.cod_entidade
                       and mapa_item.cod_solicitacao   = mapa_item_anulacao.cod_solicitacao
                       and mapa_item.cod_centro   = mapa_item_anulacao.cod_centro
                       and mapa_item.lote        = mapa_item_anulacao.lote
                       and mapa_item.cod_item   = mapa_item_anulacao.cod_item

                  join compras.julgamento_item
                    on ( cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
                   and   cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
                   and   cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
                   and   cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                   and   cotacao_fornecedor_item.lote           = julgamento_item.lote )

                  join almoxarifado.catalogo_item
                    on catalogo_item.cod_item = adjudicacao.cod_item

                  join sw_cgm
                    on sw_cgm.numcgm = adjudicacao.cgm_fornecedor

                 left join (
                            select MAX(homologacao.num_homologacao) as num_homologacao
                                 , homologacao.cod_tipo_documento
                                 , homologacao.cod_documento
                                 , homologacao.cod_entidade
                                 , homologacao.cod_modalidade
                                 , homologacao.cod_licitacao
                                 , homologacao.exercicio_licitacao
                                 , homologacao.cod_item
                                 , homologacao.cgm_fornecedor
                                 , homologacao.cod_cotacao
                                 , homologacao.lote
                                 , homologacao.exercicio_cotacao
                                 , homologacao.num_adjudicacao
                                 , homologacao.homologado
                                 , homologacao_anulada.num_adjudicacao as num_adjudicacao_anulada
                                 , homologacao_anulada.motivo
                                 , homologacao_anulada.revogacao
                                 , MAX(homologacao.timestamp) as timestamp
                              from licitacao.homologacao
                           left join licitacao.homologacao_anulada
                                  on homologacao_anulada.num_homologacao       = homologacao.num_homologacao
                                 and homologacao_anulada.cod_licitacao         = homologacao.cod_licitacao
                                 and homologacao_anulada.cod_modalidade        = homologacao.cod_modalidade
                                 and homologacao_anulada.cod_entidade          = homologacao.cod_entidade
                                 and homologacao_anulada.num_adjudicacao       = homologacao.num_adjudicacao
                                 and homologacao_anulada.exercicio_licitacao   = homologacao.exercicio_licitacao
                                 and homologacao_anulada.lote                  = homologacao.lote
                                 and homologacao_anulada.cod_cotacao           = homologacao.cod_cotacao
                                 and homologacao_anulada.cgm_fornecedor        = homologacao.cgm_fornecedor
                                 and homologacao_anulada.cod_item              = homologacao.cod_item
                                 and homologacao_anulada.exercicio_cotacao     = homologacao.exercicio_cotacao
                         group by homologacao.cod_tipo_documento
                                    , homologacao.cod_documento
                                    , homologacao.cod_entidade
                                    , homologacao.cod_modalidade
                                    , homologacao.cod_licitacao
                                    , homologacao.exercicio_licitacao
                                    , homologacao.cod_item
                                    , homologacao.cgm_fornecedor
                                    , homologacao.cod_cotacao
                                    , homologacao.lote
                                    , homologacao.exercicio_cotacao
                                    , homologacao.num_adjudicacao
                                    , homologacao.homologado
                                    , homologacao_anulada.num_adjudicacao 
                                    , homologacao_anulada.motivo
                                    , homologacao_anulada.revogacao
                       ) as homologacao
                    on homologacao.num_adjudicacao       = adjudicacao.num_adjudicacao
                   and homologacao.cod_entidade          = adjudicacao.cod_entidade
                   and homologacao.cod_modalidade        = adjudicacao.cod_modalidade
                   and homologacao.cod_licitacao         = adjudicacao.cod_licitacao
                   and homologacao.exercicio_licitacao   = adjudicacao.exercicio_licitacao
                   and homologacao.cod_item              = adjudicacao.cod_item
                   and homologacao.cgm_fornecedor        = adjudicacao.cgm_fornecedor
                   and homologacao.cod_cotacao           = adjudicacao.cod_cotacao
                   and homologacao.lote                  = adjudicacao.lote
                   and homologacao.exercicio_cotacao     = adjudicacao.exercicio_cotacao
                   
           LEFT JOIN empenho.item_pre_empenho_julgamento  
                     ON item_pre_empenho_julgamento.exercicio_julgamento = julgamento_item.exercicio  
                   AND item_pre_empenho_julgamento.cod_cotacao = julgamento_item.cod_cotacao  
                   AND item_pre_empenho_julgamento.cod_item = julgamento_item.cod_item  
                   AND item_pre_empenho_julgamento.lote = julgamento_item.lote  
                   AND item_pre_empenho_julgamento.cgm_fornecedor = julgamento_item.cgm_fornecedor

            LEFT JOIN empenho.item_pre_empenho
                      ON item_pre_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho 
                    AND item_pre_empenho.exercicio = item_pre_empenho_julgamento.exercicio
                    AND item_pre_empenho.num_item = item_pre_empenho_julgamento.num_item    
                    
             LEFT JOIN empenho.pre_empenho
                       ON pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                     AND pre_empenho.exercicio = item_pre_empenho.exercicio
                     
              LEFT JOIN empenho.autorizacao_empenho  
                        ON autorizacao_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                      AND autorizacao_empenho.exercicio = pre_empenho.exercicio    
             ";

        return $stSql;

    }

    public function recuperaItensHomologacao(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql  = $this->montaRecuperaItensHomologacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    public function montaRecuperaItensHomologacao()
    {
        $stSql = "
                    select catalogo_item.cod_item
                         , catalogo_item.descricao_resumida as item
                         , cotacao_item.quantidade
                         , cotacao_fornecedor_item.vl_cotacao
                         , sw_cgm.nom_cgm  as fornecedor
                         , to_char(homologacao.timestamp,'dd/mm/yyyy') as dt_homologacao
                      from licitacao.homologacao
                      join licitacao.adjudicacao
                        on ( adjudicacao.num_adjudicacao     = homologacao.num_adjudicacao
                       and   adjudicacao.cod_entidade        = homologacao.cod_entidade
                       and   adjudicacao.cod_modalidade      = homologacao.cod_modalidade
                       and   adjudicacao.cod_licitacao       = homologacao.cod_licitacao
                       and   adjudicacao.exercicio_licitacao = homologacao.exercicio_licitacao
                       and   adjudicacao.cod_item            = homologacao.cod_item
                       and   adjudicacao.cod_cotacao         = homologacao.cod_cotacao
                       and   adjudicacao.lote                = homologacao.lote
                       and   adjudicacao.exercicio_cotacao   = homologacao.exercicio_cotacao
                       and   adjudicacao.cgm_fornecedor      = homologacao.cgm_fornecedor )

                      join compras.cotacao_fornecedor_item
                        on ( adjudicacao.cod_cotacao       = cotacao_fornecedor_item.cod_cotacao
                       and   adjudicacao.exercicio_cotacao = cotacao_fornecedor_item.exercicio
                       and   adjudicacao.cod_item          = cotacao_fornecedor_item.cod_item
                       and   adjudicacao.lote              = cotacao_fornecedor_item.lote
                       and   adjudicacao.cgm_fornecedor    = cotacao_fornecedor_item.cgm_fornecedor )

                      join compras.cotacao_item
                        on ( cotacao_item.exercicio   = cotacao_fornecedor_item.exercicio
                       and   cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                       and   cotacao_item.cod_item    = cotacao_fornecedor_item.cod_item
                       and   cotacao_item.lote        = cotacao_fornecedor_item.lote  )

                      join almoxarifado.catalogo_item
                        on (catalogo_item.cod_item = cotacao_item.cod_item )
                      join sw_cgm
                        on ( sw_cgm.numcgm = cotacao_fornecedor_item.cgm_fornecedor  )
                 ";

        return  $stSql;

    }

    public function recuperaItensAutorizacaoParcial(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stGroupBy = " GROUP BY catalogo_item.cod_item
                              , catalogo_item.descricao_resumida
                              , cotacao_fornecedor_item.vl_cotacao
                              , sw_cgm.nom_cgm
                              , to_char(homologacao.timestamp,'dd/mm/yyyy')
                              , cotacao_item.quantidade
                        HAVING coalesce(cotacao_item.quantidade, 0.00) - sum(coalesce(item_pre_empenho.quantidade, 0.00)) > 0 ";

        $stSql  = $this->montaRecuperaItensAutorizacaoParcial().$stFiltro.$stGroupBy.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    public function montaRecuperaItensAutorizacaoParcial()
    {
        $stSql = "
                    SELECT catalogo_item.cod_item
                         , catalogo_item.descricao_resumida AS item
                         , cotacao_fornecedor_item.vl_cotacao
                         , sw_cgm.nom_cgm AS fornecedor
                         , to_char(homologacao.timestamp,'dd/mm/yyyy') AS dt_homologacao
                         , cotacao_item.quantidade
                         , SUM(COALESCE(item_pre_empenho.quantidade, 0.00)) AS quantidade_empenho
                         , COALESCE(cotacao_item.quantidade, 0.00) - SUM(COALESCE(item_pre_empenho.quantidade, 0.00)) AS quantidade_saldo
                      FROM licitacao.homologacao
                INNER JOIN licitacao.adjudicacao
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
                INNER JOIN compras.cotacao_fornecedor_item
                        ON adjudicacao.cod_cotacao       = cotacao_fornecedor_item.cod_cotacao
                       AND adjudicacao.exercicio_cotacao = cotacao_fornecedor_item.exercicio
                       AND adjudicacao.cod_item          = cotacao_fornecedor_item.cod_item
                       AND adjudicacao.lote              = cotacao_fornecedor_item.lote
                       AND adjudicacao.cgm_fornecedor    = cotacao_fornecedor_item.cgm_fornecedor
                INNER JOIN compras.cotacao_item
                        ON cotacao_item.exercicio   = cotacao_fornecedor_item.exercicio
                       AND cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                       AND cotacao_item.cod_item    = cotacao_fornecedor_item.cod_item
                       AND cotacao_item.lote        = cotacao_fornecedor_item.lote 
                INNER JOIN almoxarifado.catalogo_item
                        ON catalogo_item.cod_item = cotacao_item.cod_item
                INNER JOIN sw_cgm
                        ON sw_cgm.numcgm = cotacao_fornecedor_item.cgm_fornecedor
                 LEFT JOIN empenho.item_pre_empenho_julgamento
                        ON item_pre_empenho_julgamento.exercicio_julgamento = homologacao.exercicio_cotacao
                       AND item_pre_empenho_julgamento.cod_cotacao          = homologacao.cod_cotacao
                       AND item_pre_empenho_julgamento.cod_item             = homologacao.cod_item
                       AND item_pre_empenho_julgamento.lote                 = homologacao.lote
                       AND item_pre_empenho_julgamento.cgm_fornecedor       = homologacao.cgm_fornecedor
                 LEFT JOIN empenho.item_pre_empenho
                        ON item_pre_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho
                       AND item_pre_empenho.exercicio       = item_pre_empenho_julgamento.exercicio
                       AND item_pre_empenho.num_item        = item_pre_empenho_julgamento.num_item
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
                 ";

        return  $stSql;
    }

    public function recuperaItensRelatorio(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql  = $this->montaRecuperaItensRelatorio().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaItensRelatorio()
    {
        $stSql = "
select  sw_cgm.nom_cgm as nom_fornecedor
   , catalogo_item.descricao as desc_item
   , publico.fn_numeric_br(cotacao_item.quantidade) as qtd
   , publico.fn_numeric_br(cotacao_fornecedor_item.vl_cotacao) as valor
   , publico.fn_numeric_br(sum(mapa_item.vl_total) - coalesce(sum(mapa_item_anulacao.vl_total),0)) as vl_total
   , unidade_medida.nom_unidade
   , mapa_item.cod_mapa
   , mapa_item.cod_entidade
   , mapa_item.cod_item
   , mapa_item.exercicio as exercicio_mapa
   , (julgamento_vencedor.vl_cotacao / (sum(mapa_item.quantidade) - coalesce(sum(mapa_item_anulacao.quantidade),0))::numeric(14,2))::numeric(14,2) as vl_unitario
   , ((sum(mapa_item.vl_total) - coalesce(sum(mapa_item_anulacao.vl_total),0)) / (sum(mapa_item.quantidade) - coalesce(sum(mapa_item_anulacao.quantidade),0)))::numeric(14,2) as vl_unitario_referencia
   , homologacao.timestamp
from (
               select homologacao.num_homologacao
                    , homologacao.cod_tipo_documento
                    , homologacao.cod_documento
                    , homologacao.cod_entidade
                    , homologacao.cod_modalidade
                    , homologacao.cod_licitacao
                    , homologacao.exercicio_licitacao
                    , homologacao.cod_item
                    , homologacao.cgm_fornecedor
                    , homologacao.cod_cotacao
                    , homologacao.lote
                    , homologacao.exercicio_cotacao
                    , homologacao.num_adjudicacao
                    , homologacao.homologado
                    , homologacao_anulada.motivo
                    , homologacao.timestamp
                 from licitacao.homologacao
              left join licitacao.homologacao_anulada
                     on homologacao_anulada.num_homologacao       = homologacao.num_homologacao
                    and homologacao_anulada.cod_licitacao         = homologacao.cod_licitacao
                    and homologacao_anulada.cod_modalidade        = homologacao.cod_modalidade
                    and homologacao_anulada.cod_entidade          = homologacao.cod_entidade
                    and homologacao_anulada.num_adjudicacao       = homologacao.num_adjudicacao
                    and homologacao_anulada.exercicio_licitacao   = homologacao.exercicio_licitacao
                    and homologacao_anulada.lote                  = homologacao.lote
                    and homologacao_anulada.cod_cotacao           = homologacao.cod_cotacao
                    and homologacao_anulada.cgm_fornecedor        = homologacao.cgm_fornecedor
                    and homologacao_anulada.cod_item              = homologacao.cod_item
                    and homologacao_anulada.exercicio_cotacao     = homologacao.exercicio_cotacao
                    and homologacao_anulada.num_homologacao is null
          ) as homologacao
     inner join compras.cotacao_item
       on cotacao_item.exercicio   = homologacao.exercicio_cotacao
      and cotacao_item.cod_cotacao = homologacao.cod_cotacao
      and cotacao_item.lote        = homologacao.lote
      and cotacao_item.cod_item    = homologacao.cod_item

     inner join compras.cotacao_fornecedor_item
       on cotacao_fornecedor_item.exercicio      = homologacao.exercicio_cotacao
      and cotacao_fornecedor_item.cod_cotacao    = homologacao.cod_cotacao
      and cotacao_fornecedor_item.cod_item       = homologacao.cod_item
      and cotacao_fornecedor_item.cgm_fornecedor = homologacao.cgm_fornecedor
      and cotacao_fornecedor_item.lote           = homologacao.lote

     inner join compras.mapa_cotacao
       on mapa_cotacao.cod_cotacao       = cotacao_item.cod_cotacao
      and mapa_cotacao.exercicio_cotacao = cotacao_item.exercicio

     inner join compras.mapa_item
       on mapa_item.cod_mapa = mapa_cotacao.cod_mapa
      and mapa_item.exercicio = mapa_cotacao.exercicio_mapa
      and mapa_item.cod_item = cotacao_fornecedor_item.cod_item
      and mapa_item.lote = cotacao_fornecedor_item.lote

     left join compras.mapa_item_anulacao
           on mapa_item.exercicio  = mapa_item_anulacao.exercicio
          and mapa_item.exercicio_solicitacao  = mapa_item_anulacao.exercicio_solicitacao
          and mapa_item.cod_mapa   = mapa_item_anulacao.cod_mapa
          and mapa_item.cod_entidade   = mapa_item_anulacao.cod_entidade
          and mapa_item.cod_solicitacao   = mapa_item_anulacao.cod_solicitacao
          and mapa_item.cod_centro   = mapa_item_anulacao.cod_centro
          and mapa_item.lote        = mapa_item_anulacao.lote
          and mapa_item.cod_item   = mapa_item_anulacao.cod_item

     inner join sw_cgm
       on sw_cgm.numcgm = homologacao.cgm_fornecedor
     inner join almoxarifado.catalogo_item
       on catalogo_item.cod_item = homologacao.cod_item

     inner join administracao.unidade_medida
             on catalogo_item.cod_grandeza = unidade_medida.cod_grandeza
            and catalogo_item.cod_unidade = unidade_medida.cod_unidade

    INNER JOIN (
                 select julgamento_item.exercicio
                      , julgamento_item.cod_cotacao
                      , julgamento_item.cod_item
                      , julgamento_item.lote
                      , julgamento_item.cgm_fornecedor
                      , cotacao_fornecedor_item.vl_cotacao
                   from compras.julgamento_item
                     join compras.cotacao_fornecedor_item
                       ON cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
                      and cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
                      and cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
                      and cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                      and cotacao_fornecedor_item.lote           = julgamento_item.lote
                      AND julgamento_item.ordem = 1
                ) as julgamento_vencedor
           on julgamento_vencedor.exercicio   = cotacao_item.exercicio
          and julgamento_vencedor.cod_cotacao = cotacao_item.cod_cotacao
          and julgamento_vencedor.cod_item    = cotacao_item.cod_item
          and julgamento_vencedor.lote        = cotacao_item.lote

    where homologacao.cod_entidade = ".$this->getDado('cod_entidade')."
      and homologacao.cod_modalidade = ".$this->getDado('cod_modalidade')."
      and homologacao.cod_licitacao = ".$this->getDado('cod_licitacao')."
      and homologacao.exercicio_licitacao = '".$this->getDado('exercicio')."'
      and homologacao.homologado = true

    group by nom_fornecedor
           , desc_item
           , qtd
           , valor
           , unidade_medida.nom_unidade
           , mapa_item.cod_mapa
           , mapa_item.cod_entidade
           , mapa_item.cod_item
           , mapa_item.exercicio
           , julgamento_vencedor.vl_cotacao
           , homologacao.timestamp";

        return $stSql;
    }

    public function recuperaCotacoesParaEmpenho(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql  = $this->montaRecuperaCotacoesParaEmpenho().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaCotacoesParaEmpenho()
    {
        $stSql = "
                       select licitacao.cod_licitacao
                            , licitacao.exercicio
                            , licitacao.cod_mapa
                            , TO_CHAR( licitacao.timestamp,'dd/mm/yyyy') as data
                            , modalidade.cod_modalidade
                            , modalidade.descricao as modalidade
                            , sw_cgm.nom_cgm as entidade
                            , entidade.cod_entidade
                            , mapa_cotacao.cod_mapa
                            , mapa_cotacao.exercicio_mapa
                            , mapa_cotacao.cod_cotacao
                            , mapa_cotacao.exercicio_cotacao
                       from licitacao.licitacao
                       join compras.mapa_cotacao
                         on ( licitacao.cod_mapa       = mapa_cotacao.cod_mapa
                        and   licitacao.exercicio_mapa = mapa_cotacao.exercicio_mapa )
                       join compras.modalidade
                         on ( licitacao.cod_modalidade = modalidade.cod_modalidade)
                       join orcamento.entidade
                         on (licitacao.cod_entidade = entidade.cod_entidade
                        and  licitacao.exercicio    = entidade.exercicio   )
                       join sw_cgm
                         on ( entidade.numcgm = sw_cgm.numcgm )
                       where exists ( select 1
                                        from licitacao.homologacao
                                       where homologacao.homologado
                                         and homologacao.cod_cotacao       = mapa_cotacao.cod_cotacao
                                         and homologacao.exercicio_cotacao = mapa_cotacao.exercicio_cotacao
                                         and not exists ( select 1
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
                                                             and homologacao_anulada.cgm_fornecedor      = homologacao.cgm_fornecedor )
                                         ---- deve existir ao menos um item que esteja homologado, não anulado e sem registro na tabela empenho.item_pre_empenho_julgamento
                                         ---- ou que tenha registro anulado nesta tabela
                                          and not exists ( select 1
                                                             from empenho.item_pre_empenho_julgamento
                                                            where item_pre_empenho_julgamento.exercicio_julgamento = homologacao.exercicio_cotacao
                                                              and item_pre_empenho_julgamento.cod_cotacao          = homologacao.cod_cotacao
                                                              and item_pre_empenho_julgamento.cod_item             = homologacao.cod_item
                                                              and item_pre_empenho_julgamento.lote                 = homologacao.lote
                                                              and item_pre_empenho_julgamento.cgm_fornecedor       = homologacao.cgm_fornecedor
                                                         )
                                   )
                         ";

        return $stSql;
    }

    public function recuperaCotacoesParaEmpenhoParcial(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql  = $this->montaRecuperaCotacoesParaEmpenhoParcial().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaCotacoesParaEmpenhoParcial()
    {
        $stSql = "
                     SELECT licitacao.cod_licitacao
                          , licitacao.exercicio
                          , licitacao.cod_mapa
                          , TO_CHAR( licitacao.timestamp,'dd/mm/yyyy') AS data
                          , modalidade.cod_modalidade
                          , modalidade.descricao AS modalidade
                          , sw_cgm.nom_cgm AS entidade
                          , entidade.cod_entidade
                          , mapa_cotacao.cod_mapa
                          , mapa_cotacao.exercicio_mapa
                          , mapa_cotacao.cod_cotacao
                          , mapa_cotacao.exercicio_cotacao
                       FROM licitacao.licitacao
                  LEFT JOIN licitacao.licitacao_anulada
                         ON licitacao_anulada.cod_licitacao     = licitacao.cod_licitacao
                        AND licitacao_anulada.cod_modalidade    = licitacao.cod_modalidade
                        AND licitacao_anulada.cod_entidade      = licitacao.cod_entidade
                        AND licitacao_anulada.exercicio         = licitacao.exercicio
                 INNER JOIN compras.mapa_cotacao
                         ON licitacao.cod_mapa      = mapa_cotacao.cod_mapa
                        AND licitacao.exercicio_mapa= mapa_cotacao.exercicio_mapa
                 INNER JOIN compras.modalidade
                         ON licitacao.cod_modalidade= modalidade.cod_modalidade
                 INNER JOIN orcamento.entidade
                         ON licitacao.cod_entidade  = entidade.cod_entidade
                        AND licitacao.exercicio     = entidade.exercicio
                 INNER JOIN sw_cgm
                         ON entidade.numcgm = sw_cgm.numcgm
                 INNER JOIN ( select homologacao.cod_licitacao
                                   , homologacao.cod_modalidade
                                   , homologacao.cod_entidade
                                   , homologacao.exercicio_licitacao
                                   , homologacao.cod_cotacao
                                   , homologacao.exercicio_cotacao
                                from licitacao.adjudicacao
                           left join licitacao.adjudicacao_anulada
                                  on adjudicacao_anulada.num_adjudicacao      = adjudicacao.num_adjudicacao
                                 and adjudicacao_anulada.cod_entidade         = adjudicacao.cod_entidade
                                 and adjudicacao_anulada.cod_modalidade       = adjudicacao.cod_modalidade
                                 and adjudicacao_anulada.cod_licitacao        = adjudicacao.cod_licitacao
                                 and adjudicacao_anulada.exercicio_licitacao  = adjudicacao.exercicio_licitacao
                                 and adjudicacao_anulada.cod_item             = adjudicacao.cod_item
                                 and adjudicacao_anulada.cod_cotacao          = adjudicacao.cod_cotacao
                                 and adjudicacao_anulada.lote                 = adjudicacao.lote
                                 and adjudicacao_anulada.exercicio_cotacao    = adjudicacao.exercicio_cotacao
                                 and adjudicacao_anulada.cgm_fornecedor       = adjudicacao.cgm_fornecedor
                          inner join licitacao.homologacao
                                  on homologacao.num_adjudicacao        = adjudicacao.num_adjudicacao
                                 and homologacao.cod_entidade           = adjudicacao.cod_entidade
                                 and homologacao.cod_modalidade         = adjudicacao.cod_modalidade
                                 and homologacao.cod_licitacao          = adjudicacao.cod_licitacao
                                 and homologacao.exercicio_licitacao    = adjudicacao.exercicio_licitacao
                                 and homologacao.cod_item               = adjudicacao.cod_item
                                 and homologacao.cod_cotacao            = adjudicacao.cod_cotacao
                                 and homologacao.lote                   = adjudicacao.lote
                                 and homologacao.exercicio_cotacao      = adjudicacao.exercicio_cotacao
                                 and homologacao.cgm_fornecedor         = adjudicacao.cgm_fornecedor
                                 and homologacao.num_homologacao        = (   select max(h.num_homologacao)
                                                                                from licitacao.homologacao as h
                                                                               where h.num_adjudicacao      = adjudicacao.num_adjudicacao
                                                                                 and h.cod_entidade         = adjudicacao.cod_entidade
                                                                                 and h.cod_modalidade       = adjudicacao.cod_modalidade
                                                                                 and h.cod_licitacao        = adjudicacao.cod_licitacao
                                                                                 and h.exercicio_licitacao  = adjudicacao.exercicio_licitacao
                                                                                 and h.cod_item             = adjudicacao.cod_item
                                                                                 and h.cod_cotacao          = adjudicacao.cod_cotacao
                                                                                 and h.lote                 = adjudicacao.lote
                                                                                 and h.exercicio_cotacao    = adjudicacao.exercicio_cotacao
                                                                                 and h.cgm_fornecedor       = adjudicacao.cgm_fornecedor )
                           left join licitacao.homologacao_anulada
                                  on homologacao_anulada.num_homologacao        = homologacao.num_homologacao
                                 and homologacao_anulada.cod_licitacao          = homologacao.cod_licitacao
                                 and homologacao_anulada.cod_modalidade         = homologacao.cod_modalidade
                                 and homologacao_anulada.cod_entidade           = homologacao.cod_entidade
                                 and homologacao_anulada.num_adjudicacao        = homologacao.num_adjudicacao
                                 and homologacao_anulada.exercicio_licitacao    = homologacao.exercicio_licitacao
                                 and homologacao_anulada.lote                   = homologacao.lote
                                 and homologacao_anulada.cod_cotacao            = homologacao.cod_cotacao
                                 and homologacao_anulada.cod_item               = homologacao.cod_item
                                 and homologacao_anulada.exercicio_cotacao      = homologacao.exercicio_cotacao
                                 and homologacao_anulada.cgm_fornecedor         = homologacao.cgm_fornecedor
                          inner join licitacao.cotacao_licitacao
                                  on cotacao_licitacao.cod_licitacao        = adjudicacao.cod_licitacao
                                 and cotacao_licitacao.cod_modalidade       = adjudicacao.cod_modalidade
                                 and cotacao_licitacao.cod_entidade         = adjudicacao.cod_entidade
                                 and cotacao_licitacao.exercicio_licitacao  = adjudicacao.exercicio_licitacao
                                 and cotacao_licitacao.lote                 = adjudicacao.lote
                                 and cotacao_licitacao.cod_cotacao          = adjudicacao.cod_cotacao
                                 and cotacao_licitacao.cod_item             = adjudicacao.cod_item
                                 and cotacao_licitacao.exercicio_cotacao    = adjudicacao.exercicio_cotacao
                                 and cotacao_licitacao.cgm_fornecedor       = adjudicacao.cgm_fornecedor  
                          inner join compras.cotacao_fornecedor_item
                                  on cotacao_fornecedor_item.cod_item       = cotacao_licitacao.cod_item
                                 and cotacao_fornecedor_item.cgm_fornecedor = cotacao_licitacao.cgm_fornecedor
                                 and cotacao_fornecedor_item.cod_cotacao    = cotacao_licitacao.cod_cotacao
                                 and cotacao_fornecedor_item.exercicio      = cotacao_licitacao.exercicio_cotacao
                                 and cotacao_fornecedor_item.lote           = cotacao_licitacao.lote
                          inner join compras.cotacao_item
                                  on cotacao_item.exercicio	    = cotacao_fornecedor_item.exercicio
                                 and cotacao_item.cod_cotacao	= cotacao_fornecedor_item.cod_cotacao
                                 and cotacao_item.cod_item      = cotacao_fornecedor_item.cod_item
                                 and cotacao_item.lote		    = cotacao_fornecedor_item.lote
                           left join compras.cotacao_anulada
                                  on cotacao_anulada.cod_cotacao = cotacao_licitacao.cod_cotacao
                                 and cotacao_anulada.exercicio   = cotacao_licitacao.exercicio_cotacao
                           left join ( select item_pre_empenho_julgamento.exercicio_julgamento
                                            , item_pre_empenho_julgamento.cod_cotacao
                                            , item_pre_empenho_julgamento.cod_item
                                            , item_pre_empenho_julgamento.lote
                                            , item_pre_empenho_julgamento.cgm_fornecedor
                                            , sum(coalesce(item_pre_empenho.quantidade, 0.00)) as quantidade
                                         from empenho.item_pre_empenho_julgamento
                                   inner join empenho.item_pre_empenho
                                           on item_pre_empenho.cod_pre_empenho  = item_pre_empenho_julgamento.cod_pre_empenho
                                          and item_pre_empenho.exercicio        = item_pre_empenho_julgamento.exercicio
                                          and item_pre_empenho.num_item         = item_pre_empenho_julgamento.num_item
                                     group by item_pre_empenho_julgamento.exercicio_julgamento
                                            , item_pre_empenho_julgamento.cod_cotacao
                                            , item_pre_empenho_julgamento.cod_item
                                            , item_pre_empenho_julgamento.lote
                                            , item_pre_empenho_julgamento.cgm_fornecedor
                                     ) as item_pre_empenho_julgamento
                                  on item_pre_empenho_julgamento.exercicio_julgamento = homologacao.exercicio_cotacao
                                 and item_pre_empenho_julgamento.cod_cotacao          = homologacao.cod_cotacao
                                 and item_pre_empenho_julgamento.cod_item             = homologacao.cod_item
                                 and item_pre_empenho_julgamento.lote                 = homologacao.lote
                                 and item_pre_empenho_julgamento.cgm_fornecedor       = homologacao.cgm_fornecedor
                               where homologacao.homologado
                                 and homologacao_anulada.cod_item is null
                                 and adjudicacao.adjudicado
                                 and adjudicacao_anulada.cod_item is null
                                 and coalesce(cotacao_item.quantidade, 0.00) - coalesce(item_pre_empenho_julgamento.quantidade, 0.00) > 0
                                 and cotacao_anulada.cod_cotacao is null
                            group by homologacao.cod_licitacao
                                   , homologacao.cod_modalidade
                                   , homologacao.cod_entidade
                                   , homologacao.exercicio_licitacao
                                   , homologacao.cod_cotacao
                                   , homologacao.exercicio_cotacao
                            order by homologacao.exercicio_licitacao
                                   , homologacao.cod_licitacao
                            ) AS homologacao
                         ON homologacao.cod_licitacao          = licitacao.cod_licitacao
                        AND homologacao.cod_modalidade         = licitacao.cod_modalidade
                        AND homologacao.cod_entidade           = licitacao.cod_entidade
                        AND homologacao.exercicio_licitacao    = licitacao.exercicio
                        AND homologacao.cod_cotacao            = mapa_cotacao.cod_cotacao
                        AND homologacao.exercicio_cotacao      = mapa_cotacao.exercicio_cotacao
                  LEFT JOIN licitacao.edital
                         ON licitacao.cod_licitacao    = edital.cod_licitacao
                        AND licitacao.cod_modalidade   = edital.cod_modalidade
                        AND licitacao.cod_entidade     = edital.cod_entidade
                        AND licitacao.exercicio        = edital.exercicio
                  LEFT JOIN licitacao.edital_suspenso
                         ON edital_suspenso.num_edital = edital.num_edital
                        AND edital_suspenso.exercicio  = edital.exercicio
                      WHERE licitacao_anulada.cod_licitacao IS NULL
                        AND edital_suspenso.num_edital IS NULL
                        -- Para as modalidades 1,2,3,4,5,6,7,10,11 é obrigatório exister um edital
                        AND CASE WHEN licitacao.cod_modalidade in (1,2,3,4,5,6,7,10,11)
                                    THEN edital.num_edital IS NOT NULL
                                -- Para as modalidades 8,9 é facultativo possuir um edital
                                 WHEN licitacao.cod_modalidade in (8,9)
                                    THEN ( edital.num_edital IS NOT NULL OR edital.num_edital IS NULL )
                            END
                        AND EXISTS (   SELECT mp.exercicio
                                            , mp.cod_mapa
                                            , mp.cod_objeto
                                            , mp.timestamp
                                            , mp.cod_tipo_licitacao
                                            , solicitacao.registro_precos
                                         FROM compras.mapa AS mp
                                       INNER JOIN compras.mapa_solicitacao
                                           ON mapa_solicitacao.exercicio = mp.exercicio
                                          AND mapa_solicitacao.cod_mapa  = mp.cod_mapa
                                       INNER JOIN compras.solicitacao_homologada
                                           ON solicitacao_homologada.exercicio       = mapa_solicitacao.exercicio_solicitacao
                                          AND solicitacao_homologada.cod_entidade    = mapa_solicitacao.cod_entidade
                                          AND solicitacao_homologada.cod_solicitacao = mapa_solicitacao.cod_solicitacao
                                       INNER JOIN compras.solicitacao
                                           ON solicitacao.exercicio       = solicitacao_homologada.exercicio
                                          AND solicitacao.cod_entidade    = solicitacao_homologada.cod_entidade
                                          AND solicitacao.cod_solicitacao = solicitacao_homologada.cod_solicitacao
                                        WHERE mp.cod_mapa	= mapa_cotacao.cod_mapa
                                          AND mp.exercicio	= mapa_cotacao.exercicio_mapa
                                         GROUP BY mp.exercicio
                                            , mp.cod_mapa
                                            , mp.cod_objeto
                                            , mp.timestamp
                                            , mp.cod_tipo_licitacao
                                            , solicitacao.registro_precos )                                                                 \n";

        return $stSql;
    }

    public function recuperaItensAgrupadosSolicitacaoLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stGroupBy = " group by cotacao_fornecedor_item.cgm_fornecedor
                              , cotacao_fornecedor_item.lote
                              , solicitacao_item_dotacao.cod_despesa
                              , solicitacao_item_dotacao.cod_conta
                              , solicitacao_item_dotacao.cod_entidade
                              , cotacao_item.cod_cotacao
                              , cotacao_item.exercicio
                              , cotacao_item.cod_item
                              , cotacao_item.lote
                              , mapa_item.quantidade
                              , cotacao_fornecedor_item.vl_cotacao
                              , catalogo_item.descricao_resumida
                              , catalogo_item.descricao
                              , unidade_medida.cod_unidade
                              , unidade_medida.cod_grandeza
                              , unidade_medida.nom_unidade
                              , unidade_medida.simbolo
                              , mapa.cod_mapa
                              , mapa.exercicio
                              , solicitacao_item.exercicio
                              , cotacao_item.quantidade
                              , solicitacao_item.quantidade
                              , solicitacao_item.cod_solicitacao
                              , solicitacao_item_dotacao.cod_centro
                              , solicitacao_item_dotacao.vl_reserva
                              , mapa_item_reserva.cod_reserva
                              , solicitacao_item_anulacao.quantidade
                              , solicitacao_item.cod_entidade
                              , solicitacao_item.cod_centro
                              , solicitacao_item.cod_item";
        $stSql = $this->montaRecuperaItensAgrupadosSolicitacaoLicitacao().$stFiltro.$stGroupBy.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaItensAgrupadosSolicitacaoLicitacao()
    {
        $stSql = "select cotacao_item.cod_cotacao
                       , cotacao_item.exercicio
                       , cotacao_item.cod_item
                       , cotacao_item.lote
                       , solicitacao_item.exercicio as exercicio_solicitacao
                       , cotacao_fornecedor_item.cgm_fornecedor as fornecedor
                       , cotacao_fornecedor_item.lote
                       , solicitacao_item_dotacao.cod_despesa
                       , solicitacao_item_dotacao.cod_conta
                       , solicitacao_item.exercicio as exercicio_solicitacao
                       , solicitacao_item_dotacao.cod_entidade
                       , 0 as historico
                       , 0 as cod_tipo
                       , false as implantado
                       , (( sum(cotacao_fornecedor_item.vl_cotacao) / sum(cotacao_item.quantidade) ) * sum(mapa_item_dotacao.quantidade))::numeric(14,2) as reserva
                       , sum(mapa_item_dotacao.quantidade) as qtd_cotacao
                       , (( sum(cotacao_fornecedor_item.vl_cotacao) / sum(cotacao_item.quantidade) ) * sum(mapa_item_dotacao.quantidade))::numeric(14,2) as vl_cotacao
                       , catalogo_item.descricao_resumida
                       , catalogo_item.descricao as descricao_completa
                       , unidade_medida.cod_unidade
                       , unidade_medida.cod_grandeza
                       , unidade_medida.nom_unidade
                       , unidade_medida.simbolo
                       , mapa_item_reserva.cod_reserva
                       , mapa.cod_mapa
                       , mapa.exercicio as exercicio_mapa

                  from licitacao.homologacao
             left join licitacao.homologacao_anulada
                    on homologacao.num_homologacao     = homologacao_anulada.num_homologacao
                   and homologacao.cod_licitacao       = homologacao_anulada.cod_licitacao
                   and homologacao.cod_modalidade      = homologacao_anulada.cod_modalidade
                   and homologacao.cod_entidade        = homologacao_anulada.cod_entidade
                   and homologacao.num_adjudicacao     = homologacao_anulada.num_adjudicacao
                   and homologacao.exercicio_licitacao = homologacao_anulada.exercicio_licitacao
                   and homologacao.lote                = homologacao_anulada.lote
                   and homologacao.cod_cotacao         = homologacao_anulada.cod_cotacao
                   and homologacao.cod_item            = homologacao_anulada.cod_item
                   and homologacao.exercicio_cotacao   = homologacao_anulada.exercicio_cotacao
                   and homologacao.cgm_fornecedor      = homologacao_anulada.cgm_fornecedor
            inner join licitacao.adjudicacao
                    on homologacao.cod_licitacao       = adjudicacao.cod_licitacao
                   and homologacao.cod_modalidade      = adjudicacao.cod_modalidade
                   and homologacao.cod_entidade        = adjudicacao.cod_entidade
                   and homologacao.num_adjudicacao     = adjudicacao.num_adjudicacao
                   and homologacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                   and homologacao.lote                = adjudicacao.lote
                   and homologacao.cod_cotacao         = adjudicacao.cod_cotacao
                   and homologacao.cod_item            = adjudicacao.cod_item
                   and homologacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                   and homologacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor
             left join licitacao.adjudicacao_anulada
                    on adjudicacao.num_adjudicacao     = adjudicacao_anulada.num_adjudicacao
                   and adjudicacao.cod_licitacao       = adjudicacao_anulada.cod_licitacao
                   and adjudicacao.cod_modalidade      = adjudicacao_anulada.cod_modalidade
                   and adjudicacao.cod_entidade        = adjudicacao_anulada.cod_entidade
                   and adjudicacao.exercicio_licitacao = adjudicacao_anulada.exercicio_licitacao
                   and adjudicacao.lote                = adjudicacao_anulada.lote
                   and adjudicacao.cod_cotacao         = adjudicacao_anulada.cod_cotacao
                   and adjudicacao.cod_item            = adjudicacao_anulada.cod_item
                   and adjudicacao.exercicio_cotacao   = adjudicacao_anulada.exercicio_cotacao
                   and adjudicacao.cgm_fornecedor      = adjudicacao_anulada.cgm_fornecedor
            inner join licitacao.cotacao_licitacao
                    on cotacao_licitacao.cod_licitacao       = adjudicacao.cod_licitacao
                   and cotacao_licitacao.cod_modalidade      = adjudicacao.cod_modalidade
                   and cotacao_licitacao.cod_entidade        = adjudicacao.cod_entidade
                   and cotacao_licitacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                   and cotacao_licitacao.lote                = adjudicacao.lote
                   and cotacao_licitacao.cod_item            = adjudicacao.cod_item
                   and cotacao_licitacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                   and cotacao_licitacao.cod_cotacao         = adjudicacao.cod_cotacao
                   and cotacao_licitacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor
            inner join licitacao.licitacao
                    on licitacao.cod_licitacao       = cotacao_licitacao.cod_licitacao
                   and licitacao.cod_modalidade      = cotacao_licitacao.cod_modalidade
                   and licitacao.cod_entidade        = cotacao_licitacao.cod_entidade
                   and licitacao.exercicio           = cotacao_licitacao.exercicio_licitacao
            inner join compras.cotacao_fornecedor_item
                    on cotacao_licitacao.cod_cotacao          = cotacao_fornecedor_item.cod_cotacao
                   and cotacao_licitacao.exercicio_cotacao    = cotacao_fornecedor_item.exercicio
                   and cotacao_licitacao.cod_item             = cotacao_fornecedor_item.cod_item
                   and cotacao_licitacao.cgm_fornecedor       = cotacao_fornecedor_item.cgm_fornecedor
                   and cotacao_licitacao.lote                 = cotacao_fornecedor_item.lote
            inner join compras.cotacao_item
                    on cotacao_item.cod_cotacao   = cotacao_fornecedor_item.cod_cotacao
                   and cotacao_item.exercicio     = cotacao_fornecedor_item.exercicio
                   and cotacao_item.lote          = cotacao_fornecedor_item.lote
                   and cotacao_item.cod_item      = cotacao_fornecedor_item.cod_item
            inner join compras.cotacao
                    on cotacao.cod_cotacao    = cotacao_item.cod_cotacao
                   and cotacao.exercicio      = cotacao_item.exercicio
            inner join compras.mapa_cotacao
                    on cotacao.cod_cotacao    = mapa_cotacao.cod_cotacao
                   and cotacao.exercicio      = mapa_cotacao.exercicio_cotacao
            inner join compras.mapa_item
                    on mapa_cotacao.cod_mapa      = mapa_item.cod_mapa
                   and mapa_cotacao.exercicio_mapa= mapa_item.exercicio
                   and mapa_item.cod_item      = cotacao_licitacao.cod_item
                   and mapa_item.lote          = cotacao_licitacao.lote
            inner join compras.mapa_item_dotacao
                    on mapa_item_dotacao.exercicio             = mapa_item.exercicio
                   and mapa_item_dotacao.cod_mapa              = mapa_item.cod_mapa
                   and mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                   and mapa_item_dotacao.cod_entidade          = mapa_item.cod_entidade
                   and mapa_item_dotacao.cod_solicitacao       = mapa_item.cod_solicitacao
                   and mapa_item_dotacao.cod_centro            = mapa_item.cod_centro
                   and mapa_item_dotacao.cod_item              = mapa_item.cod_item
                   and mapa_item_dotacao.lote                  = mapa_item.lote
            inner join compras.mapa
                   on mapa.cod_mapa      = mapa_item.cod_mapa
                   and mapa.exercicio      = mapa_item.exercicio
            inner join compras.mapa_solicitacao
                    on mapa_solicitacao.exercicio             = mapa_item.exercicio
                   and mapa_solicitacao.cod_entidade          = mapa_item.cod_entidade
                   and mapa_solicitacao.cod_solicitacao       = mapa_item.cod_solicitacao
                   and mapa_solicitacao.cod_mapa              = mapa_item.cod_mapa
                   and mapa_solicitacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
            inner join compras.solicitacao_homologada
                    on solicitacao_homologada.exercicio       = mapa_solicitacao.exercicio_solicitacao
                   and solicitacao_homologada.cod_entidade    = mapa_solicitacao.cod_entidade
                   and solicitacao_homologada.cod_solicitacao = mapa_solicitacao.cod_solicitacao
            inner join compras.solicitacao
                    on solicitacao.exercicio       = solicitacao_homologada.exercicio
                   and solicitacao.cod_entidade    = solicitacao_homologada.cod_entidade
                   and solicitacao.cod_solicitacao = solicitacao_homologada.cod_solicitacao
            inner join compras.solicitacao_item
                    on solicitacao_item.exercicio          = mapa_item.exercicio
                   and solicitacao_item.cod_entidade       = mapa_item.cod_entidade
                   and solicitacao_item.cod_solicitacao    = mapa_item.cod_solicitacao
                   and solicitacao_item.cod_centro         = mapa_item.cod_centro
                   and solicitacao_item.cod_item           = mapa_item.cod_item
                   and solicitacao_item.exercicio          = solicitacao.exercicio
                   and solicitacao_item.cod_entidade       = solicitacao.cod_entidade
                   and solicitacao_item.cod_solicitacao    = solicitacao.cod_solicitacao
             left join compras.solicitacao_item_anulacao
                    on solicitacao_item_anulacao.exercicio = solicitacao_item.exercicio
                   and solicitacao_item_anulacao.cod_entidade  = solicitacao_item.cod_entidade
                   and solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                   and solicitacao_item_anulacao.cod_centro = solicitacao_item.cod_centro
                   and solicitacao_item_anulacao.cod_item = solicitacao_item.cod_item
            inner join almoxarifado.catalogo_item
                    on catalogo_item.cod_item = solicitacao_item.cod_item
            inner join administracao.unidade_medida
                    on unidade_medida.cod_grandeza = catalogo_item.cod_grandeza
                   and unidade_medida.cod_unidade = catalogo_item.cod_unidade
            inner join compras.solicitacao_item_dotacao
                    on solicitacao_item.exercicio        = solicitacao_item_dotacao.exercicio
                   and solicitacao_item.cod_entidade     = solicitacao_item_dotacao.cod_entidade
                   and solicitacao_item.cod_solicitacao  = solicitacao_item_dotacao.cod_solicitacao
                   and solicitacao_item.cod_centro       = solicitacao_item_dotacao.cod_centro
                   and solicitacao_item.cod_item         = solicitacao_item_dotacao.cod_item
                   and mapa_item_dotacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
            inner join compras.mapa_item_reserva
                    on mapa_item_reserva.exercicio_mapa        = mapa_item_dotacao.exercicio
                   and mapa_item_reserva.cod_mapa              = mapa_item_dotacao.cod_mapa
                   and mapa_item_reserva.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
                   and mapa_item_reserva.cod_entidade          = mapa_item_dotacao.cod_entidade
                   and mapa_item_reserva.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
                   and mapa_item_reserva.cod_centro            = mapa_item_dotacao.cod_centro
                   and mapa_item_reserva.cod_item              = mapa_item_dotacao.cod_item
                   and mapa_item_reserva.lote                  = mapa_item_dotacao.lote
                   and mapa_item_reserva.cod_despesa           = mapa_item_dotacao.cod_despesa
                   and mapa_item_reserva.cod_conta             = mapa_item_dotacao.cod_conta

                 where homologacao_anulada.cod_licitacao is null
                   and adjudicacao_anulada.cod_licitacao is null";

        return $stSql;
    }
    
    public function recuperaItensAgrupadosSolicitacaoLicitacaoMapa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stGroupBy = " group by cotacao_fornecedor_item.cgm_fornecedor
                              , cotacao_fornecedor_item.lote
                              , solicitacao_item_dotacao.cod_despesa
                              , solicitacao_item_dotacao.cod_conta
                              , solicitacao_item_dotacao.cod_entidade
                              , cotacao_item.cod_cotacao
                              , cotacao_item.exercicio
                              , cotacao_item.cod_item
                              , cotacao_item.lote
                              , mapa_item.quantidade
                              , cotacao_fornecedor_item.vl_cotacao
                              , catalogo_item.descricao_resumida
                              , catalogo_item.descricao
                              , unidade_medida.cod_unidade
                              , unidade_medida.cod_grandeza
                              , unidade_medida.nom_unidade
                              , unidade_medida.simbolo
                              , mapa.cod_mapa
                              , mapa.exercicio
                              , solicitacao_item.exercicio
                              , cotacao_item.quantidade
                              , solicitacao_item.quantidade
                              , solicitacao_item.cod_solicitacao
                              , solicitacao_item_dotacao.cod_centro
                              , solicitacao_item_dotacao.vl_reserva
                              , solicitacao_item_anulacao.quantidade
                              , solicitacao_item.cod_entidade
                              , solicitacao_item.cod_centro
                              , solicitacao_item.cod_item
                              , sw_cgm.nom_cgm";
        $stSql = $this->montaRecuperaItensAgrupadosSolicitacaoLicitacaoMapa().$stFiltro.$stGroupBy.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaItensAgrupadosSolicitacaoLicitacaoMapa()
    {
        $stSql = "select cotacao_item.cod_cotacao
                       , cotacao_item.exercicio
                       , cotacao_item.cod_item
                       , solicitacao_item.cod_centro
                       , cotacao_item.lote
                       , solicitacao_item.cod_solicitacao
                       , solicitacao_item.exercicio as exercicio_solicitacao
                       , cotacao_fornecedor_item.cgm_fornecedor as fornecedor
                       , cotacao_fornecedor_item.lote
                       , solicitacao_item_dotacao.cod_despesa
                       , solicitacao_item_dotacao.cod_conta
                       , solicitacao_item_dotacao.cod_entidade
                       , sw_cgm.nom_cgm as nom_entidade
                       , 0 as historico
                       , 0 as cod_tipo
                       , false as implantado
                       , (( sum(cotacao_fornecedor_item.vl_cotacao) / sum(cotacao_item.quantidade) ) * sum(mapa_item_dotacao.quantidade))::numeric(14,2) as reserva
                       , sum(mapa_item_dotacao.quantidade) as qtd_cotacao
                       , (( sum(cotacao_fornecedor_item.vl_cotacao) / sum(cotacao_item.quantidade) ) * sum(mapa_item_dotacao.quantidade))::numeric(14,2) as vl_cotacao
                       , catalogo_item.descricao_resumida
                       , catalogo_item.descricao as descricao_completa
                       , unidade_medida.cod_unidade
                       , unidade_medida.cod_grandeza
                       , unidade_medida.nom_unidade
                       , unidade_medida.simbolo
                       , mapa.cod_mapa
                       , mapa.exercicio as exercicio_mapa

                  from licitacao.homologacao
             left join licitacao.homologacao_anulada
                    on homologacao.num_homologacao     = homologacao_anulada.num_homologacao
                   and homologacao.cod_licitacao       = homologacao_anulada.cod_licitacao
                   and homologacao.cod_modalidade      = homologacao_anulada.cod_modalidade
                   and homologacao.cod_entidade        = homologacao_anulada.cod_entidade
                   and homologacao.num_adjudicacao     = homologacao_anulada.num_adjudicacao
                   and homologacao.exercicio_licitacao = homologacao_anulada.exercicio_licitacao
                   and homologacao.lote                = homologacao_anulada.lote
                   and homologacao.cod_cotacao         = homologacao_anulada.cod_cotacao
                   and homologacao.cod_item            = homologacao_anulada.cod_item
                   and homologacao.exercicio_cotacao   = homologacao_anulada.exercicio_cotacao
                   and homologacao.cgm_fornecedor      = homologacao_anulada.cgm_fornecedor
            inner join licitacao.adjudicacao
                    on homologacao.cod_licitacao       = adjudicacao.cod_licitacao
                   and homologacao.cod_modalidade      = adjudicacao.cod_modalidade
                   and homologacao.cod_entidade        = adjudicacao.cod_entidade
                   and homologacao.num_adjudicacao     = adjudicacao.num_adjudicacao
                   and homologacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                   and homologacao.lote                = adjudicacao.lote
                   and homologacao.cod_cotacao         = adjudicacao.cod_cotacao
                   and homologacao.cod_item            = adjudicacao.cod_item
                   and homologacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                   and homologacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor
             left join licitacao.adjudicacao_anulada
                    on adjudicacao.num_adjudicacao     = adjudicacao_anulada.num_adjudicacao
                   and adjudicacao.cod_licitacao       = adjudicacao_anulada.cod_licitacao
                   and adjudicacao.cod_modalidade      = adjudicacao_anulada.cod_modalidade
                   and adjudicacao.cod_entidade        = adjudicacao_anulada.cod_entidade
                   and adjudicacao.exercicio_licitacao = adjudicacao_anulada.exercicio_licitacao
                   and adjudicacao.lote                = adjudicacao_anulada.lote
                   and adjudicacao.cod_cotacao         = adjudicacao_anulada.cod_cotacao
                   and adjudicacao.cod_item            = adjudicacao_anulada.cod_item
                   and adjudicacao.exercicio_cotacao   = adjudicacao_anulada.exercicio_cotacao
                   and adjudicacao.cgm_fornecedor      = adjudicacao_anulada.cgm_fornecedor
            inner join licitacao.cotacao_licitacao
                    on cotacao_licitacao.cod_licitacao       = adjudicacao.cod_licitacao
                   and cotacao_licitacao.cod_modalidade      = adjudicacao.cod_modalidade
                   and cotacao_licitacao.cod_entidade        = adjudicacao.cod_entidade
                   and cotacao_licitacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                   and cotacao_licitacao.cod_cotacao         = adjudicacao.cod_cotacao
                   and cotacao_licitacao.lote                = adjudicacao.lote
                   and cotacao_licitacao.cod_item            = adjudicacao.cod_item
                   and cotacao_licitacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                   and cotacao_licitacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor
            inner join licitacao.licitacao
                    on licitacao.cod_licitacao       = cotacao_licitacao.cod_licitacao
                   and licitacao.cod_modalidade      = cotacao_licitacao.cod_modalidade
                   and licitacao.cod_entidade        = cotacao_licitacao.cod_entidade
                   and licitacao.exercicio           = cotacao_licitacao.exercicio_licitacao
            inner join compras.cotacao_fornecedor_item
                    on cotacao_licitacao.cod_cotacao          = cotacao_fornecedor_item.cod_cotacao
                   and cotacao_licitacao.exercicio_cotacao    = cotacao_fornecedor_item.exercicio
                   and cotacao_licitacao.cod_item             = cotacao_fornecedor_item.cod_item
                   and cotacao_licitacao.cgm_fornecedor       = cotacao_fornecedor_item.cgm_fornecedor
                   and cotacao_licitacao.lote                 = cotacao_fornecedor_item.lote
            inner join compras.cotacao_item
                    on cotacao_item.cod_cotacao   = cotacao_fornecedor_item.cod_cotacao
                   and cotacao_item.exercicio     = cotacao_fornecedor_item.exercicio
                   and cotacao_item.lote          = cotacao_fornecedor_item.lote
                   and cotacao_item.cod_item      = cotacao_fornecedor_item.cod_item
            inner join compras.cotacao
                    on cotacao.cod_cotacao    = cotacao_item.cod_cotacao
                   and cotacao.exercicio      = cotacao_item.exercicio
            inner join compras.mapa_cotacao
                    on cotacao.cod_cotacao    = mapa_cotacao.cod_cotacao
                   and cotacao.exercicio      = mapa_cotacao.exercicio_cotacao
            inner join compras.mapa_item
                    on mapa_cotacao.cod_mapa      = mapa_item.cod_mapa
                   and mapa_cotacao.exercicio_mapa= mapa_item.exercicio
                   and mapa_item.cod_item      = cotacao_licitacao.cod_item
                   and mapa_item.lote          = cotacao_licitacao.lote
            inner join compras.mapa_item_dotacao
                    on mapa_item_dotacao.exercicio             = mapa_item.exercicio
                   and mapa_item_dotacao.cod_mapa              = mapa_item.cod_mapa
                   and mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                   and mapa_item_dotacao.cod_entidade          = mapa_item.cod_entidade
                   and mapa_item_dotacao.cod_solicitacao       = mapa_item.cod_solicitacao
                   and mapa_item_dotacao.cod_centro            = mapa_item.cod_centro
                   and mapa_item_dotacao.cod_item              = mapa_item.cod_item
                   and mapa_item_dotacao.lote                  = mapa_item.lote
            inner join compras.mapa
                   on mapa.cod_mapa      = mapa_item.cod_mapa
                   and mapa.exercicio      = mapa_item.exercicio
            inner join compras.mapa_solicitacao
                    on mapa_solicitacao.exercicio             = mapa_item.exercicio
                   and mapa_solicitacao.cod_entidade          = mapa_item.cod_entidade
                   and mapa_solicitacao.cod_solicitacao       = mapa_item.cod_solicitacao
                   and mapa_solicitacao.cod_mapa              = mapa_item.cod_mapa
                   and mapa_solicitacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
            inner join compras.solicitacao_homologada
                    on solicitacao_homologada.exercicio       = mapa_solicitacao.exercicio_solicitacao
                   and solicitacao_homologada.cod_entidade    = mapa_solicitacao.cod_entidade
                   and solicitacao_homologada.cod_solicitacao = mapa_solicitacao.cod_solicitacao
            inner join compras.solicitacao
                    on solicitacao.exercicio       = solicitacao_homologada.exercicio
                   and solicitacao.cod_entidade    = solicitacao_homologada.cod_entidade
                   and solicitacao.cod_solicitacao = solicitacao_homologada.cod_solicitacao
            inner join compras.solicitacao_item
                    on solicitacao_item.exercicio          = mapa_item.exercicio
                   and solicitacao_item.cod_entidade       = mapa_item.cod_entidade
                   and solicitacao_item.cod_solicitacao    = mapa_item.cod_solicitacao
                   and solicitacao_item.cod_centro         = mapa_item.cod_centro
                   and solicitacao_item.cod_item           = mapa_item.cod_item
                   and solicitacao_item.exercicio          = solicitacao.exercicio
                   and solicitacao_item.cod_entidade       = solicitacao.cod_entidade
                   and solicitacao_item.cod_solicitacao    = solicitacao.cod_solicitacao
             left join compras.solicitacao_item_anulacao
                    on solicitacao_item_anulacao.exercicio = solicitacao_item.exercicio
                   and solicitacao_item_anulacao.cod_entidade  = solicitacao_item.cod_entidade
                   and solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                   and solicitacao_item_anulacao.cod_centro = solicitacao_item.cod_centro
                   and solicitacao_item_anulacao.cod_item = solicitacao_item.cod_item
            inner join almoxarifado.catalogo_item
                    on catalogo_item.cod_item = solicitacao_item.cod_item
            inner join administracao.unidade_medida
                    on unidade_medida.cod_grandeza = catalogo_item.cod_grandeza
                   and unidade_medida.cod_unidade = catalogo_item.cod_unidade
            inner join compras.solicitacao_item_dotacao
                    on solicitacao_item.exercicio        = solicitacao_item_dotacao.exercicio
                   and solicitacao_item.cod_entidade     = solicitacao_item_dotacao.cod_entidade
                   and solicitacao_item.cod_solicitacao  = solicitacao_item_dotacao.cod_solicitacao
                   and solicitacao_item.cod_centro       = solicitacao_item_dotacao.cod_centro
                   and solicitacao_item.cod_item         = solicitacao_item_dotacao.cod_item
                   and mapa_item_dotacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
            inner join orcamento.entidade
                    on entidade.cod_entidade = solicitacao_item_dotacao.cod_entidade
                   and entidade.exercicio = solicitacao_item_dotacao.exercicio
            inner join sw_cgm
                    on sw_cgm.numcgm = entidade.numcgm

                 where homologacao_anulada.cod_licitacao is null
                   and adjudicacao_anulada.cod_licitacao is null";

        return $stSql;
    }

    public function recuperaItensAgrupadosSolicitacaoLicitacaoImp(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stGroupBy = " group by cotacao_fornecedor_item.cgm_fornecedor
                              , cotacao_fornecedor_item.lote
                              , solicitacao_item_dotacao.cod_despesa
                              , solicitacao_item_dotacao.cod_conta
                              , solicitacao_item_dotacao.cod_entidade
                              , cotacao_item.cod_cotacao
                              , cotacao_item.exercicio
                              , cotacao_item.cod_item
                              , cotacao_item.lote
                              , catalogo_item.descricao_resumida
                              , catalogo_item.descricao
                              , unidade_medida.cod_unidade
                              , unidade_medida.cod_grandeza
                              , unidade_medida.nom_unidade
                              , unidade_medida.simbolo
                              , mapa.cod_mapa
                              , mapa.exercicio
                              , solicitacao_item.exercicio
                              , solicitacao_item.cod_solicitacao
                              , solicitacao_item.cod_entidade
                              , solicitacao_item.cod_item
                              , solicitacao_item.complemento
                              , solicitacao_item.cod_centro
                              , cotacao_fornecedor_item.cod_marca";
        $stSql = $this->montaRecuperaItensAgrupadosSolicitacaoLicitacaoImp().$stFiltro.$stGroupBy.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaItensAgrupadosSolicitacaoLicitacaoImp()
    {
        $stSql = "select cotacao_item.cod_cotacao
                       , cotacao_item.exercicio
                       , cotacao_item.cod_item
                       , cotacao_item.lote
                       , solicitacao_item.exercicio as exercicio_solicitacao
                       , cotacao_fornecedor_item.cgm_fornecedor as fornecedor
                       , cotacao_fornecedor_item.lote
                       , solicitacao_item_dotacao.cod_despesa
                       , solicitacao_item_dotacao.cod_conta
                       , solicitacao_item.exercicio as exercicio_solicitacao
                       , solicitacao_item.cod_entidade
                       , 0 as historico
                       , 0 as cod_tipo
                       , false as implantado
                       , CASE WHEN solicitacao_item_dotacao.cod_despesa IS NOT NULL
                              THEN (( sum(cotacao_fornecedor_item.vl_cotacao) / sum(cotacao_item.quantidade) ) * (sum(mapa_item_dotacao.quantidade) - coalesce (sum(mapa_item_anulacao.quantidade),0)) )::numeric(14,2)
                              ELSE (( sum(cotacao_fornecedor_item.vl_cotacao) / sum(cotacao_item.quantidade) ) * (sum(mapa_item.quantidade) - coalesce (sum(mapa_item_anulacao.quantidade),0)) )::numeric(14,2)
                         END AS reserva
                       , CASE WHEN solicitacao_item_dotacao.cod_despesa IS NOT NULL
                              THEN sum(mapa_item_dotacao.quantidade) - coalesce (sum(mapa_item_anulacao.quantidade),0)
                              ELSE sum(mapa_item.quantidade) - coalesce (sum(mapa_item_anulacao.quantidade),0)
                         END AS qtd_cotacao
                       , CASE WHEN solicitacao_item_dotacao.cod_despesa IS NOT NULL
                              THEN (( sum(cotacao_fornecedor_item.vl_cotacao) / sum(cotacao_item.quantidade) ) * (sum(mapa_item_dotacao.quantidade) - coalesce (sum(mapa_item_anulacao.quantidade),0)) )::numeric(14,2)
                              ELSE (( sum(cotacao_fornecedor_item.vl_cotacao) / sum(cotacao_item.quantidade) ) * (sum(mapa_item.quantidade) - coalesce (sum(mapa_item_anulacao.quantidade),0)) )::numeric(14,2)
                         END AS vl_cotacao
                       , catalogo_item.descricao_resumida
                       , catalogo_item.descricao as descricao_completa
                       , unidade_medida.cod_unidade
                       , unidade_medida.cod_grandeza
                       , unidade_medida.nom_unidade
                       , unidade_medida.simbolo
                       , mapa.cod_mapa
                       , mapa.exercicio as exercicio_mapa
                       , solicitacao_item.complemento
                       , solicitacao_item.cod_centro
                       , cotacao_fornecedor_item.cod_marca

                  from licitacao.homologacao
             left join licitacao.homologacao_anulada
                    on homologacao.num_homologacao     = homologacao_anulada.num_homologacao
                   and homologacao.cod_licitacao       = homologacao_anulada.cod_licitacao
                   and homologacao.cod_modalidade      = homologacao_anulada.cod_modalidade
                   and homologacao.cod_entidade        = homologacao_anulada.cod_entidade
                   and homologacao.num_adjudicacao     = homologacao_anulada.num_adjudicacao
                   and homologacao.exercicio_licitacao = homologacao_anulada.exercicio_licitacao
                   and homologacao.lote                = homologacao_anulada.lote
                   and homologacao.cod_cotacao         = homologacao_anulada.cod_cotacao
                   and homologacao.cod_item            = homologacao_anulada.cod_item
                   and homologacao.exercicio_cotacao   = homologacao_anulada.exercicio_cotacao
                   and homologacao.cgm_fornecedor      = homologacao_anulada.cgm_fornecedor
            inner join licitacao.adjudicacao
                    on homologacao.cod_licitacao       = adjudicacao.cod_licitacao
                   and homologacao.cod_modalidade      = adjudicacao.cod_modalidade
                   and homologacao.cod_entidade        = adjudicacao.cod_entidade
                   and homologacao.num_adjudicacao     = adjudicacao.num_adjudicacao
                   and homologacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                   and homologacao.lote                = adjudicacao.lote
                   and homologacao.cod_cotacao         = adjudicacao.cod_cotacao
                   and homologacao.cod_item            = adjudicacao.cod_item
                   and homologacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                   and homologacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor
             left join licitacao.adjudicacao_anulada
                    on adjudicacao.num_adjudicacao     = adjudicacao_anulada.num_adjudicacao
                   and adjudicacao.cod_licitacao       = adjudicacao_anulada.cod_licitacao
                   and adjudicacao.cod_modalidade      = adjudicacao_anulada.cod_modalidade
                   and adjudicacao.cod_entidade        = adjudicacao_anulada.cod_entidade
                   and adjudicacao.exercicio_licitacao = adjudicacao_anulada.exercicio_licitacao
                   and adjudicacao.lote                = adjudicacao_anulada.lote
                   and adjudicacao.cod_cotacao         = adjudicacao_anulada.cod_cotacao
                   and adjudicacao.cod_item            = adjudicacao_anulada.cod_item
                   and adjudicacao.exercicio_cotacao   = adjudicacao_anulada.exercicio_cotacao
                   and adjudicacao.cgm_fornecedor      = adjudicacao_anulada.cgm_fornecedor
            inner join licitacao.cotacao_licitacao
                    on cotacao_licitacao.cod_licitacao       = adjudicacao.cod_licitacao
                   and cotacao_licitacao.cod_modalidade      = adjudicacao.cod_modalidade
                   and cotacao_licitacao.cod_entidade        = adjudicacao.cod_entidade
                   and cotacao_licitacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                   and cotacao_licitacao.lote                = adjudicacao.lote
                   and cotacao_licitacao.cod_item            = adjudicacao.cod_item
                   and cotacao_licitacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                   and cotacao_licitacao.cod_cotacao         = adjudicacao.cod_cotacao
                   and cotacao_licitacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor
            inner join licitacao.licitacao
                    on licitacao.cod_licitacao       = cotacao_licitacao.cod_licitacao
                   and licitacao.cod_modalidade      = cotacao_licitacao.cod_modalidade
                   and licitacao.cod_entidade        = cotacao_licitacao.cod_entidade
                   and licitacao.exercicio           = cotacao_licitacao.exercicio_licitacao
            inner join compras.cotacao_fornecedor_item
                    on cotacao_licitacao.cod_cotacao          = cotacao_fornecedor_item.cod_cotacao
                   and cotacao_licitacao.exercicio_cotacao    = cotacao_fornecedor_item.exercicio
                   and cotacao_licitacao.cod_item             = cotacao_fornecedor_item.cod_item
                   and cotacao_licitacao.cgm_fornecedor       = cotacao_fornecedor_item.cgm_fornecedor
                   and cotacao_licitacao.lote                 = cotacao_fornecedor_item.lote
            inner join compras.cotacao_item
                    on cotacao_item.cod_cotacao   = cotacao_fornecedor_item.cod_cotacao
                   and cotacao_item.exercicio     = cotacao_fornecedor_item.exercicio
                   and cotacao_item.lote          = cotacao_fornecedor_item.lote
                   and cotacao_item.cod_item      = cotacao_fornecedor_item.cod_item
            inner join compras.cotacao
                    on cotacao.cod_cotacao    = cotacao_item.cod_cotacao
                   and cotacao.exercicio      = cotacao_item.exercicio
            inner join compras.mapa_cotacao
                    on cotacao.cod_cotacao    = mapa_cotacao.cod_cotacao
                   and cotacao.exercicio      = mapa_cotacao.exercicio_cotacao
            inner join compras.mapa_item
                    on mapa_cotacao.cod_mapa      = mapa_item.cod_mapa
                   and mapa_cotacao.exercicio_mapa= mapa_item.exercicio
                   and mapa_item.cod_item      = cotacao_licitacao.cod_item
                   and mapa_item.lote          = cotacao_licitacao.lote
             left join compras.mapa_item_dotacao
                    on mapa_item_dotacao.exercicio             = mapa_item.exercicio
                   and mapa_item_dotacao.cod_mapa              = mapa_item.cod_mapa
                   and mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                   and mapa_item_dotacao.cod_entidade          = mapa_item.cod_entidade
                   and mapa_item_dotacao.cod_solicitacao       = mapa_item.cod_solicitacao
                   and mapa_item_dotacao.cod_centro            = mapa_item.cod_centro
                   and mapa_item_dotacao.cod_item              = mapa_item.cod_item
                   and mapa_item_dotacao.lote                  = mapa_item.lote
             left join compras.mapa_item_anulacao
                    on mapa_item_anulacao.exercicio             = mapa_item_dotacao.exercicio
                   and mapa_item_anulacao.cod_mapa              = mapa_item_dotacao.cod_mapa
                   and mapa_item_anulacao.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
                   and mapa_item_anulacao.cod_entidade          = mapa_item_dotacao.cod_entidade
                   and mapa_item_anulacao.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
                   and mapa_item_anulacao.cod_centro            = mapa_item_dotacao.cod_centro
                   and mapa_item_anulacao.cod_item              = mapa_item_dotacao.cod_item
                   and mapa_item_anulacao.lote                  = mapa_item_dotacao.lote
                   and mapa_item_anulacao.cod_conta             = mapa_item_dotacao.cod_conta
                   and mapa_item_anulacao.cod_despesa           = mapa_item_dotacao.cod_despesa
            inner join compras.mapa
                   on mapa.cod_mapa      = mapa_item.cod_mapa
                   and mapa.exercicio      = mapa_item.exercicio
            inner join compras.mapa_solicitacao
                    on mapa_solicitacao.exercicio             = mapa_item.exercicio
                   and mapa_solicitacao.cod_entidade          = mapa_item.cod_entidade
                   and mapa_solicitacao.cod_solicitacao       = mapa_item.cod_solicitacao
                   and mapa_solicitacao.cod_mapa              = mapa_item.cod_mapa
                   and mapa_solicitacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
            inner join compras.solicitacao_homologada
                    on solicitacao_homologada.exercicio       = mapa_solicitacao.exercicio_solicitacao
                   and solicitacao_homologada.cod_entidade    = mapa_solicitacao.cod_entidade
                   and solicitacao_homologada.cod_solicitacao = mapa_solicitacao.cod_solicitacao
            inner join compras.solicitacao
                    on solicitacao.exercicio       = solicitacao_homologada.exercicio
                   and solicitacao.cod_entidade    = solicitacao_homologada.cod_entidade
                   and solicitacao.cod_solicitacao = solicitacao_homologada.cod_solicitacao
            inner join compras.solicitacao_item
                    on solicitacao_item.exercicio          = mapa_item.exercicio
                   and solicitacao_item.cod_entidade       = mapa_item.cod_entidade
                   and solicitacao_item.cod_solicitacao    = mapa_item.cod_solicitacao
                   and solicitacao_item.cod_centro         = mapa_item.cod_centro
                   and solicitacao_item.cod_item           = mapa_item.cod_item
                   and solicitacao_item.exercicio          = solicitacao.exercicio
                   and solicitacao_item.cod_entidade       = solicitacao.cod_entidade
                   and solicitacao_item.cod_solicitacao    = solicitacao.cod_solicitacao
             left join compras.solicitacao_item_anulacao
                    on solicitacao_item_anulacao.exercicio = solicitacao_item.exercicio
                   and solicitacao_item_anulacao.cod_entidade  = solicitacao_item.cod_entidade
                   and solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                   and solicitacao_item_anulacao.cod_centro = solicitacao_item.cod_centro
                   and solicitacao_item_anulacao.cod_item = solicitacao_item.cod_item
            inner join almoxarifado.catalogo_item
                    on catalogo_item.cod_item = solicitacao_item.cod_item
            inner join administracao.unidade_medida
                    on unidade_medida.cod_grandeza = catalogo_item.cod_grandeza
                   and unidade_medida.cod_unidade = catalogo_item.cod_unidade
             left join compras.solicitacao_item_dotacao
                    on solicitacao_item.exercicio        = solicitacao_item_dotacao.exercicio
                   and solicitacao_item.cod_entidade     = solicitacao_item_dotacao.cod_entidade
                   and solicitacao_item.cod_solicitacao  = solicitacao_item_dotacao.cod_solicitacao
                   and solicitacao_item.cod_centro       = solicitacao_item_dotacao.cod_centro
                   and solicitacao_item.cod_item         = solicitacao_item_dotacao.cod_item
                   and mapa_item_dotacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
             left join compras.mapa_item_reserva
                    on mapa_item_reserva.exercicio_mapa        = mapa_item_dotacao.exercicio
                   and mapa_item_reserva.cod_mapa              = mapa_item_dotacao.cod_mapa
                   and mapa_item_reserva.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
                   and mapa_item_reserva.cod_entidade          = mapa_item_dotacao.cod_entidade
                   and mapa_item_reserva.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
                   and mapa_item_reserva.cod_centro            = mapa_item_dotacao.cod_centro
                   and mapa_item_reserva.cod_item              = mapa_item_dotacao.cod_item
                   and mapa_item_reserva.lote                  = mapa_item_dotacao.lote
                   and mapa_item_reserva.cod_despesa           = mapa_item_dotacao.cod_despesa
                   and mapa_item_reserva.cod_conta             = mapa_item_dotacao.cod_conta

                 where homologacao_anulada.cod_licitacao is null
                   and adjudicacao_anulada.cod_licitacao is null";

        return $stSql;
    }

    public function recuperaItensHomologacaoAutEmpenho(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql  = $this->montaRecuperaItensHomologacaoAutEmpenho().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaItensHomologacaoAutEmpenho()
    {
        $stSql = "
           select
                  cotacao_item.cod_cotacao
                , cotacao_item.exercicio
                , cotacao_item.cod_item
                , cotacao_item.lote
                , solicitacao_item.exercicio as exercicio_solicitacao
                , cotacao_fornecedor_item.cgm_fornecedor as fornecedor
                , solicitacao_item.quantidade as qtd_solicitada
                , solicitacao_item.cod_solicitacao
                , solicitacao_item_dotacao.cod_despesa
                , solicitacao_item_dotacao.cod_conta
                , solicitacao_item_dotacao.cod_centro
                , mapa_item.quantidade as qtd_mapa
                , cotacao_item.quantidade as qtd_cotacao
                , solicitacao_item_dotacao.vl_reserva
                , cotacao_fornecedor_item.vl_cotacao
                , catalogo_item.descricao_resumida
                , catalogo_item.descricao as descricao_completa
                , unidade_medida.cod_unidade
                , unidade_medida.cod_grandeza
                , unidade_medida.nom_unidade
                , unidade_medida.simbolo
                , mapa.cod_mapa
                , mapa.exercicio as exercicio_mapa
                , mapa_item_reserva.cod_reserva
                , case
                when    (
                            (
                                solicitacao_item.quantidade
                                -
                                coalesce(solicitacao_item_anulacao.quantidade,0.00)
                                -
                                (
                                   select sum(quantidade)
                                     from compras.mapa_item
                                     where exercicio = solicitacao_item.exercicio
                                       and cod_entidade  = solicitacao_item.cod_entidade
                                       and cod_solicitacao = solicitacao_item.cod_solicitacao
                                       and cod_centro = solicitacao_item.cod_centro
                                       and cod_item = solicitacao_item.cod_item
                                  group by cod_solicitacao , cod_entidade, exercicio
                                )
                            )
                            =
                            0
                        ) then
                    0::numeric(14,2)
                else
                        (
                            (
                                solicitacao_item.quantidade
                                -
                                coalesce(solicitacao_item_anulacao.quantidade,0.00)
                                -
                                (
                                   select coalesce(sum(quantidade),0.00)
                                     from compras.mapa_item
                                     where exercicio = solicitacao_item.exercicio
                                       and cod_entidade  = solicitacao_item.cod_entidade
                                       and cod_solicitacao = solicitacao_item.cod_solicitacao
                                       and cod_centro = solicitacao_item.cod_centro
                                       and cod_item = solicitacao_item.cod_item
                                  group by cod_solicitacao , cod_entidade, exercicio
                                )
                            )::numeric(14,2)
                            *
                            (
                                cotacao_fornecedor_item.vl_cotacao
                                /
                                mapa_item.quantidade
                            )::numeric(14,2)
                        )::numeric(14,2)
              end as nova_reserva_solicitacao
              from
                   licitacao.homologacao
                   left join licitacao.homologacao_anulada
                          on homologacao.num_homologacao     = homologacao_anulada.num_homologacao
                         and homologacao.cod_licitacao       = homologacao_anulada.cod_licitacao
                         and homologacao.cod_modalidade      = homologacao_anulada.cod_modalidade
                         and homologacao.cod_entidade        = homologacao_anulada.cod_entidade
                         and homologacao.num_adjudicacao     = homologacao_anulada.num_adjudicacao
                         and homologacao.exercicio_licitacao = homologacao_anulada.exercicio_licitacao
                         and homologacao.lote                = homologacao_anulada.lote
                         and homologacao.cod_cotacao         = homologacao_anulada.cod_cotacao
                         and homologacao.cod_item            = homologacao_anulada.cod_item
                         and homologacao.exercicio_cotacao   = homologacao_anulada.exercicio_cotacao
                         and homologacao.cgm_fornecedor      = homologacao_anulada.cgm_fornecedor
                  inner join licitacao.adjudicacao
                          on homologacao.cod_licitacao       = adjudicacao.cod_licitacao
                         and homologacao.cod_modalidade      = adjudicacao.cod_modalidade
                         and homologacao.cod_entidade        = adjudicacao.cod_entidade
                         and homologacao.num_adjudicacao     = adjudicacao.num_adjudicacao
                         and homologacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                         and homologacao.lote                = adjudicacao.lote
                         and homologacao.cod_cotacao         = adjudicacao.cod_cotacao
                         and homologacao.cod_item            = adjudicacao.cod_item
                         and homologacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                         and homologacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor
                   left join licitacao.adjudicacao_anulada
                          on adjudicacao.num_adjudicacao     = adjudicacao_anulada.num_adjudicacao
                         and adjudicacao.cod_licitacao       = adjudicacao_anulada.cod_licitacao
                         and adjudicacao.cod_modalidade      = adjudicacao_anulada.cod_modalidade
                         and adjudicacao.cod_entidade        = adjudicacao_anulada.cod_entidade
                         and adjudicacao.exercicio_licitacao = adjudicacao_anulada.exercicio_licitacao
                         and adjudicacao.lote                = adjudicacao_anulada.lote
                         and adjudicacao.cod_cotacao         = adjudicacao_anulada.cod_cotacao
                         and adjudicacao.cod_item            = adjudicacao_anulada.cod_item
                         and adjudicacao.exercicio_cotacao   = adjudicacao_anulada.exercicio_cotacao
                         and adjudicacao.cgm_fornecedor      = adjudicacao_anulada.cgm_fornecedor
                  inner join licitacao.cotacao_licitacao
                          on cotacao_licitacao.cod_licitacao       = adjudicacao.cod_licitacao
                         and cotacao_licitacao.cod_modalidade      = adjudicacao.cod_modalidade
                         and cotacao_licitacao.cod_entidade        = adjudicacao.cod_entidade
                         and cotacao_licitacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                         and cotacao_licitacao.lote                = adjudicacao.lote
                         and cotacao_licitacao.cod_item            = adjudicacao.cod_item
                         and cotacao_licitacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                         and cotacao_licitacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor
                  inner join licitacao.licitacao
                          on licitacao.cod_licitacao       = cotacao_licitacao.cod_licitacao
                         and licitacao.cod_modalidade      = cotacao_licitacao.cod_modalidade
                         and licitacao.cod_entidade        = cotacao_licitacao.cod_entidade
                         and licitacao.exercicio           = cotacao_licitacao.exercicio_licitacao
                  inner join compras.cotacao_fornecedor_item
                          on cotacao_licitacao.cod_cotacao          = cotacao_fornecedor_item.cod_cotacao
                         and cotacao_licitacao.exercicio_cotacao    = cotacao_fornecedor_item.exercicio
                         and cotacao_licitacao.cod_item             = cotacao_fornecedor_item.cod_item
                         and cotacao_licitacao.cgm_fornecedor       = cotacao_fornecedor_item.cgm_fornecedor
                         and cotacao_licitacao.lote                 = cotacao_fornecedor_item.lote
                  inner join compras.cotacao_item
                          on cotacao_item.cod_cotacao   = cotacao_fornecedor_item.cod_cotacao
                         and cotacao_item.exercicio     = cotacao_fornecedor_item.exercicio
                         and cotacao_item.lote          = cotacao_fornecedor_item.lote
                         and cotacao_item.cod_item      = cotacao_fornecedor_item.cod_item
                  inner join compras.cotacao
                          on cotacao.cod_cotacao    = cotacao_item.cod_cotacao
                         and cotacao.exercicio      = cotacao_item.exercicio
                  inner join compras.mapa_cotacao
                          on cotacao.cod_cotacao    = mapa_cotacao.cod_cotacao
                         and cotacao.exercicio      = mapa_cotacao.exercicio_cotacao
                  inner join compras.mapa_item
                          on mapa_cotacao.cod_mapa      = mapa_item.cod_mapa
                         and mapa_cotacao.exercicio_mapa= mapa_item.exercicio
                         and mapa_item.cod_item      = cotacao_licitacao.cod_item
                         and mapa_item.lote          = cotacao_licitacao.lote
                  inner join compras.mapa
                          on mapa.cod_mapa      = mapa_item.cod_mapa
                         and mapa.exercicio  	= mapa_item.exercicio
                  inner join compras.mapa_solicitacao
                          on mapa_solicitacao.exercicio             = mapa_item.exercicio
                         and mapa_solicitacao.cod_entidade          = mapa_item.cod_entidade
                         and mapa_solicitacao.cod_solicitacao       = mapa_item.cod_solicitacao
                         and mapa_solicitacao.cod_mapa              = mapa_item.cod_mapa
                         and mapa_solicitacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                  inner join compras.solicitacao_homologada
                          on solicitacao_homologada.exercicio       = mapa_solicitacao.exercicio_solicitacao
                         and solicitacao_homologada.cod_entidade    = mapa_solicitacao.cod_entidade
                         and solicitacao_homologada.cod_solicitacao = mapa_solicitacao.cod_solicitacao
                  inner join compras.solicitacao
                          on solicitacao.exercicio       = solicitacao_homologada.exercicio
                         and solicitacao.cod_entidade    = solicitacao_homologada.cod_entidade
                         and solicitacao.cod_solicitacao = solicitacao_homologada.cod_solicitacao
                  inner join compras.solicitacao_item
                          on solicitacao_item.exercicio          = mapa_item.exercicio
                         and solicitacao_item.cod_entidade       = mapa_item.cod_entidade
                         and solicitacao_item.cod_solicitacao    = mapa_item.cod_solicitacao
                         and solicitacao_item.cod_centro         = mapa_item.cod_centro
                         and solicitacao_item.cod_item           = mapa_item.cod_item
                         and solicitacao_item.exercicio          = solicitacao.exercicio
                         and solicitacao_item.cod_entidade       = solicitacao.cod_entidade
                         and solicitacao_item.cod_solicitacao    = solicitacao.cod_solicitacao
                    left join compras.solicitacao_item_anulacao
                          on solicitacao_item_anulacao.exercicio = solicitacao_item.exercicio
                          and solicitacao_item_anulacao.cod_entidade  = solicitacao_item.cod_entidade
                          and solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                          and solicitacao_item_anulacao.cod_centro = solicitacao_item.cod_centro
                          and solicitacao_item_anulacao.cod_item = solicitacao_item.cod_item
                  inner join almoxarifado.catalogo_item
                            on catalogo_item.cod_item = solicitacao_item.cod_item
                    inner join administracao.unidade_medida
                          on unidade_medida.cod_grandeza = catalogo_item.cod_grandeza
                         and unidade_medida.cod_unidade = catalogo_item.cod_unidade
                  inner join compras.solicitacao_item_dotacao
                          on solicitacao_item.exercicio        = solicitacao_item_dotacao.exercicio
                         and solicitacao_item.cod_entidade     = solicitacao_item_dotacao.cod_entidade
                         and solicitacao_item.cod_solicitacao  = solicitacao_item_dotacao.cod_solicitacao
                         and solicitacao_item.cod_centro       = solicitacao_item_dotacao.cod_centro
                         and solicitacao_item.cod_item         = solicitacao_item_dotacao.cod_item
                  inner join compras.mapa_item_reserva
                          on mapa_item_reserva.exercicio_mapa        = mapa_item.exercicio
                         and mapa_item_reserva.cod_mapa              = mapa_item.cod_mapa
                         and mapa_item_reserva.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                         and mapa_item_reserva.cod_entidade          = mapa_item.cod_entidade
                         and mapa_item_reserva.cod_solicitacao       = mapa_item.cod_solicitacao
                         and mapa_item_reserva.cod_centro            = mapa_item.cod_centro
                         and mapa_item_reserva.cod_item              = mapa_item.cod_item
                         and mapa_item_reserva.lote                  = mapa_item.lote
                       where homologacao_anulada.cod_licitacao is null
                         and adjudicacao_anulada.cod_licitacao is null
        ";

        return $stSql;
    }

    public function recuperaGrupoAutEmpenho(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stGrupo = " group by cotacao_fornecedor_item.cgm_fornecedor
                            , homologacao.cod_item
                            , vw_classificacao_despesa.mascara_classificacao
                            , solicitacao_item_dotacao.cod_despesa
                            , solicitacao_item_dotacao.cod_conta
                            , homologacao.cod_entidade
                            , licitacao.cod_modalidade
                            , despesa.num_orgao
                            , despesa.num_unidade
                            , objeto.cod_objeto
                            , objeto.descricao
                    ) AS teste
                    group by cod_despesa
                         , fornecedor
                         , cod_conta
                         , cod_entidade
                         , mascara_classificacao
                         , cod_modalidade
                         , num_orgao
                         , num_unidade
                         , cod_objeto
                         , desc_objeto
                         , historico
                         , cod_tipo
                         , implantado ";
        $stSql  = $this->montaRecuperaGrupoAutEmpenho().$stFiltro.$stGrupo.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaGrupoAutEmpenho()
    {
        $stSql = "
            SELECT sum(reserva) AS reserva
                 , fornecedor
                 , cod_conta
                 , cod_entidade
                 , cod_despesa
                 , mascara_classificacao
                 , cod_modalidade
                 , num_orgao
                 , num_unidade
                 , cod_objeto
                 , desc_objeto
                 , historico
                 , cod_tipo
                 , implantado
                 , count(cod_item) as qtd_itens_homologados
              FROM (
            select  cotacao_fornecedor_item.cgm_fornecedor as fornecedor
                  , homologacao.cod_item
                  , solicitacao_item_dotacao.cod_despesa
                  , solicitacao_item_dotacao.cod_conta
                  , homologacao.cod_entidade
                  , vw_classificacao_despesa.mascara_classificacao
                  , licitacao.cod_modalidade
                  , despesa.num_orgao
                  , despesa.num_unidade
                  , objeto.cod_objeto
                  , objeto.descricao as desc_objeto
                  , 0 as historico
                  , 0 as cod_tipo
                  , false as implantado
                  , (( sum(cotacao_fornecedor_item.vl_cotacao) / sum(cotacao_item.quantidade) ) * sum(mapa_item_dotacao.quantidade))::numeric(14,2) as reserva
              from
                   licitacao.homologacao
                   left join licitacao.homologacao_anulada
                          on homologacao.num_homologacao     = homologacao_anulada.num_homologacao
                         and homologacao.cod_licitacao       = homologacao_anulada.cod_licitacao
                         and homologacao.cod_modalidade      = homologacao_anulada.cod_modalidade
                         and homologacao.cod_entidade        = homologacao_anulada.cod_entidade
                         and homologacao.num_adjudicacao     = homologacao_anulada.num_adjudicacao
                         and homologacao.exercicio_licitacao = homologacao_anulada.exercicio_licitacao
                         and homologacao.lote                = homologacao_anulada.lote
                         and homologacao.cod_cotacao         = homologacao_anulada.cod_cotacao
                         and homologacao.cod_item            = homologacao_anulada.cod_item
                         and homologacao.exercicio_cotacao   = homologacao_anulada.exercicio_cotacao
                         and homologacao.cgm_fornecedor      = homologacao_anulada.cgm_fornecedor
                  inner join licitacao.adjudicacao
                          on homologacao.cod_licitacao       = adjudicacao.cod_licitacao
                         and homologacao.cod_modalidade      = adjudicacao.cod_modalidade
                         and homologacao.cod_entidade        = adjudicacao.cod_entidade
                         and homologacao.num_adjudicacao     = adjudicacao.num_adjudicacao
                         and homologacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                         and homologacao.lote                = adjudicacao.lote
                         and homologacao.cod_cotacao         = adjudicacao.cod_cotacao
                         and homologacao.cod_item            = adjudicacao.cod_item
                         and homologacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                         and homologacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor
                   left join licitacao.adjudicacao_anulada
                          on adjudicacao.num_adjudicacao     = adjudicacao_anulada.num_adjudicacao
                         and adjudicacao.cod_licitacao       = adjudicacao_anulada.cod_licitacao
                         and adjudicacao.cod_modalidade      = adjudicacao_anulada.cod_modalidade
                         and adjudicacao.cod_entidade        = adjudicacao_anulada.cod_entidade
                         and adjudicacao.exercicio_licitacao = adjudicacao_anulada.exercicio_licitacao
                         and adjudicacao.lote                = adjudicacao_anulada.lote
                         and adjudicacao.cod_cotacao         = adjudicacao_anulada.cod_cotacao
                         and adjudicacao.cod_item            = adjudicacao_anulada.cod_item
                         and adjudicacao.exercicio_cotacao   = adjudicacao_anulada.exercicio_cotacao
                         and adjudicacao.cgm_fornecedor      = adjudicacao_anulada.cgm_fornecedor
                  inner join licitacao.cotacao_licitacao
                          on cotacao_licitacao.cod_licitacao       = adjudicacao.cod_licitacao
                         and cotacao_licitacao.cod_modalidade      = adjudicacao.cod_modalidade
                         and cotacao_licitacao.cod_entidade        = adjudicacao.cod_entidade
                         and cotacao_licitacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                         and cotacao_licitacao.lote                = adjudicacao.lote
                         and cotacao_licitacao.cod_item            = adjudicacao.cod_item
                         and cotacao_licitacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                         and cotacao_licitacao.cod_cotacao         = adjudicacao.cod_cotacao
                         and cotacao_licitacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor
                         and cotacao_licitacao.cod_cotacao         = adjudicacao.cod_cotacao
                  inner join licitacao.licitacao
                          on licitacao.cod_licitacao       = cotacao_licitacao.cod_licitacao
                         and licitacao.cod_modalidade      = cotacao_licitacao.cod_modalidade
                         and licitacao.cod_entidade        = cotacao_licitacao.cod_entidade
                         and licitacao.exercicio           = cotacao_licitacao.exercicio_licitacao
                  inner join compras.objeto
                          on objeto.cod_objeto = licitacao.cod_objeto
                  inner join compras.cotacao_fornecedor_item
                          on cotacao_licitacao.cod_cotacao          = cotacao_fornecedor_item.cod_cotacao
                         and cotacao_licitacao.exercicio_cotacao    = cotacao_fornecedor_item.exercicio
                         and cotacao_licitacao.cod_item             = cotacao_fornecedor_item.cod_item
                         and cotacao_licitacao.cgm_fornecedor       = cotacao_fornecedor_item.cgm_fornecedor
                         and cotacao_licitacao.lote                 = cotacao_fornecedor_item.lote
                  inner join compras.cotacao_item
                          on cotacao_item.cod_cotacao   = cotacao_fornecedor_item.cod_cotacao
                         and cotacao_item.exercicio     = cotacao_fornecedor_item.exercicio
                         and cotacao_item.lote          = cotacao_fornecedor_item.lote
                         and cotacao_item.cod_item      = cotacao_fornecedor_item.cod_item
                  inner join compras.cotacao
                          on cotacao.cod_cotacao    = cotacao_item.cod_cotacao
                         and cotacao.exercicio      = cotacao_item.exercicio
                  inner join compras.mapa_cotacao
                          on cotacao.cod_cotacao    = mapa_cotacao.cod_cotacao
                         and cotacao.exercicio      = mapa_cotacao.exercicio_cotacao
                  inner join compras.mapa_item
                          on mapa_cotacao.cod_mapa      = mapa_item.cod_mapa
                         and mapa_cotacao.exercicio_mapa= mapa_item.exercicio
                         and mapa_item.cod_item      = cotacao_licitacao.cod_item
                         and mapa_item.lote          = cotacao_licitacao.lote
                   left join compras.mapa_item_dotacao
                          on mapa_item_dotacao.exercicio             = mapa_item.exercicio
                         and mapa_item_dotacao.cod_mapa              = mapa_item.cod_mapa
                         and mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                         and mapa_item_dotacao.cod_entidade          = mapa_item.cod_entidade
                         and mapa_item_dotacao.cod_solicitacao       = mapa_item.cod_solicitacao
                         and mapa_item_dotacao.cod_centro            = mapa_item.cod_centro
                         and mapa_item_dotacao.cod_item              = mapa_item.cod_item
                         and mapa_item_dotacao.lote                  = mapa_item.lote
                  inner join compras.mapa_solicitacao
                          on mapa_solicitacao.exercicio             = mapa_item.exercicio
                         and mapa_solicitacao.cod_entidade          = mapa_item.cod_entidade
                         and mapa_solicitacao.cod_solicitacao       = mapa_item.cod_solicitacao
                         and mapa_solicitacao.cod_mapa              = mapa_item.cod_mapa
                         and mapa_solicitacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                  inner join compras.solicitacao_homologada
                          on solicitacao_homologada.exercicio       = mapa_solicitacao.exercicio_solicitacao
                         and solicitacao_homologada.cod_entidade    = mapa_solicitacao.cod_entidade
                         and solicitacao_homologada.cod_solicitacao = mapa_solicitacao.cod_solicitacao
                  inner join compras.solicitacao
                          on solicitacao.exercicio       = solicitacao_homologada.exercicio
                         and solicitacao.cod_entidade    = solicitacao_homologada.cod_entidade
                         and solicitacao.cod_solicitacao = solicitacao_homologada.cod_solicitacao
                  inner join compras.solicitacao_item
                          on solicitacao_item.exercicio          = mapa_item.exercicio
                         and solicitacao_item.cod_entidade       = mapa_item.cod_entidade
                         and solicitacao_item.cod_solicitacao    = mapa_item.cod_solicitacao
                         and solicitacao_item.cod_centro         = mapa_item.cod_centro
                         and solicitacao_item.cod_item           = mapa_item.cod_item
                         and solicitacao_item.exercicio          = solicitacao.exercicio
                         and solicitacao_item.cod_entidade       = solicitacao.cod_entidade
                         and solicitacao_item.cod_solicitacao    = solicitacao.cod_solicitacao
                   left join compras.solicitacao_item_dotacao
                          on solicitacao_item.exercicio        = solicitacao_item_dotacao.exercicio
                         and solicitacao_item.cod_entidade     = solicitacao_item_dotacao.cod_entidade
                         and solicitacao_item.cod_solicitacao  = solicitacao_item_dotacao.cod_solicitacao
                         and solicitacao_item.cod_centro       = solicitacao_item_dotacao.cod_centro
                         and solicitacao_item.cod_item         = solicitacao_item_dotacao.cod_item
                         and mapa_item_dotacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
                   left join orcamento.despesa
                          on despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa
                         and despesa.exercicio = solicitacao_item_dotacao.exercicio
                   left join orcamento.vw_classificacao_despesa
                          on solicitacao_item_dotacao.cod_conta =  vw_classificacao_despesa.cod_conta
                         and solicitacao_item_dotacao.exercicio =  vw_classificacao_despesa.exercicio
                       where homologacao_anulada.cod_licitacao is null
                         and adjudicacao_anulada.cod_licitacao is null
        ";

        return $stSql;
    }

function recuperaSolicitacaoLicitacaoNaoAnulada(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaSolicitacaoLicitacaoNaoAnulada().$stFiltro.$stOrdem.$stGroupBy;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaSolicitacaoLicitacaoNaoAnulada()
    {
        $stSql = "select solicitacao.cod_solicitacao
                       , solicitacao.observacao
                       , solicitacao.exercicio
                       , solicitacao.cod_almoxarifado
                       , solicitacao.cod_entidade
                       , solicitacao.cgm_solicitante
                       , solicitacao.cgm_requisitante
                       , solicitacao.cod_objeto
                       , solicitacao.prazo_entrega
                       , solicitacao.timestamp

                  from licitacao.homologacao
             left join licitacao.homologacao_anulada
                    on homologacao.num_homologacao     = homologacao_anulada.num_homologacao
                   and homologacao.cod_licitacao       = homologacao_anulada.cod_licitacao
                   and homologacao.cod_modalidade      = homologacao_anulada.cod_modalidade
                   and homologacao.cod_entidade        = homologacao_anulada.cod_entidade
                   and homologacao.num_adjudicacao     = homologacao_anulada.num_adjudicacao
                   and homologacao.exercicio_licitacao = homologacao_anulada.exercicio_licitacao
                   and homologacao.lote                = homologacao_anulada.lote
                   and homologacao.cod_cotacao         = homologacao_anulada.cod_cotacao
                   and homologacao.cod_item            = homologacao_anulada.cod_item
                   and homologacao.exercicio_cotacao   = homologacao_anulada.exercicio_cotacao
                   and homologacao.cgm_fornecedor      = homologacao_anulada.cgm_fornecedor
            inner join licitacao.adjudicacao
                    on homologacao.cod_licitacao       = adjudicacao.cod_licitacao
                   and homologacao.cod_modalidade      = adjudicacao.cod_modalidade
                   and homologacao.cod_entidade        = adjudicacao.cod_entidade
                   and homologacao.num_adjudicacao     = adjudicacao.num_adjudicacao
                   and homologacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                   and homologacao.lote                = adjudicacao.lote
                   and homologacao.cod_cotacao         = adjudicacao.cod_cotacao
                   and homologacao.cod_item            = adjudicacao.cod_item
                   and homologacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                   and homologacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor
             left join licitacao.adjudicacao_anulada
                    on adjudicacao.num_adjudicacao     = adjudicacao_anulada.num_adjudicacao
                   and adjudicacao.cod_licitacao       = adjudicacao_anulada.cod_licitacao
                   and adjudicacao.cod_modalidade      = adjudicacao_anulada.cod_modalidade
                   and adjudicacao.cod_entidade        = adjudicacao_anulada.cod_entidade
                   and adjudicacao.exercicio_licitacao = adjudicacao_anulada.exercicio_licitacao
                   and adjudicacao.lote                = adjudicacao_anulada.lote
                   and adjudicacao.cod_cotacao         = adjudicacao_anulada.cod_cotacao
                   and adjudicacao.cod_item            = adjudicacao_anulada.cod_item
                   and adjudicacao.exercicio_cotacao   = adjudicacao_anulada.exercicio_cotacao
                   and adjudicacao.cgm_fornecedor      = adjudicacao_anulada.cgm_fornecedor
            inner join licitacao.cotacao_licitacao
                    on cotacao_licitacao.cod_licitacao       = adjudicacao.cod_licitacao
                   and cotacao_licitacao.cod_modalidade      = adjudicacao.cod_modalidade
                   and cotacao_licitacao.cod_entidade        = adjudicacao.cod_entidade
                   and cotacao_licitacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                   and cotacao_licitacao.lote                = adjudicacao.lote
                   and cotacao_licitacao.cod_item            = adjudicacao.cod_item
                   and cotacao_licitacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                   and cotacao_licitacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor
            inner join licitacao.licitacao
                    on licitacao.cod_licitacao       = cotacao_licitacao.cod_licitacao
                   and licitacao.cod_modalidade      = cotacao_licitacao.cod_modalidade
                   and licitacao.cod_entidade        = cotacao_licitacao.cod_entidade
                   and licitacao.exercicio           = cotacao_licitacao.exercicio_licitacao
            inner join compras.cotacao_fornecedor_item
                    on cotacao_licitacao.cod_cotacao          = cotacao_fornecedor_item.cod_cotacao
                   and cotacao_licitacao.exercicio_cotacao    = cotacao_fornecedor_item.exercicio
                   and cotacao_licitacao.cod_item             = cotacao_fornecedor_item.cod_item
                   and cotacao_licitacao.cgm_fornecedor       = cotacao_fornecedor_item.cgm_fornecedor
                   and cotacao_licitacao.lote                 = cotacao_fornecedor_item.lote
            inner join compras.cotacao_item
                    on cotacao_item.cod_cotacao   = cotacao_fornecedor_item.cod_cotacao
                   and cotacao_item.exercicio     = cotacao_fornecedor_item.exercicio
                   and cotacao_item.lote          = cotacao_fornecedor_item.lote
                   and cotacao_item.cod_item      = cotacao_fornecedor_item.cod_item
            inner join compras.cotacao
                    on cotacao.cod_cotacao    = cotacao_item.cod_cotacao
                   and cotacao.exercicio      = cotacao_item.exercicio
            inner join compras.mapa_cotacao
                    on cotacao.cod_cotacao    = mapa_cotacao.cod_cotacao
                   and cotacao.exercicio      = mapa_cotacao.exercicio_cotacao
            inner join compras.mapa_item
                    on mapa_cotacao.cod_mapa      = mapa_item.cod_mapa
                   and mapa_cotacao.exercicio_mapa= mapa_item.exercicio
                   and mapa_item.cod_item      = cotacao_licitacao.cod_item
                   and mapa_item.lote          = cotacao_licitacao.lote
            inner join compras.mapa_item_dotacao
                    on mapa_item_dotacao.exercicio             = mapa_item.exercicio
                   and mapa_item_dotacao.cod_mapa              = mapa_item.cod_mapa
                   and mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                   and mapa_item_dotacao.cod_entidade          = mapa_item.cod_entidade
                   and mapa_item_dotacao.cod_solicitacao       = mapa_item.cod_solicitacao
                   and mapa_item_dotacao.cod_centro            = mapa_item.cod_centro
                   and mapa_item_dotacao.cod_item              = mapa_item.cod_item
                   and mapa_item_dotacao.lote                  = mapa_item.lote
             left join compras.mapa_item_anulacao
                    on mapa_item_anulacao.exercicio             = mapa_item_dotacao.exercicio
                   and mapa_item_anulacao.cod_mapa              = mapa_item_dotacao.cod_mapa
                   and mapa_item_anulacao.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
                   and mapa_item_anulacao.cod_entidade          = mapa_item_dotacao.cod_entidade
                   and mapa_item_anulacao.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
                   and mapa_item_anulacao.cod_centro            = mapa_item_dotacao.cod_centro
                   and mapa_item_anulacao.cod_item              = mapa_item_dotacao.cod_item
                   and mapa_item_anulacao.lote                  = mapa_item_dotacao.lote
                   and mapa_item_anulacao.cod_conta             = mapa_item_dotacao.cod_conta
                   and mapa_item_anulacao.cod_despesa           = mapa_item_dotacao.cod_despesa
            inner join compras.mapa
                   on mapa.cod_mapa      = mapa_item.cod_mapa
                   and mapa.exercicio      = mapa_item.exercicio
            inner join compras.mapa_solicitacao
                    on mapa_solicitacao.exercicio             = mapa_item.exercicio
                   and mapa_solicitacao.cod_entidade          = mapa_item.cod_entidade
                   and mapa_solicitacao.cod_solicitacao       = mapa_item.cod_solicitacao
                   and mapa_solicitacao.cod_mapa              = mapa_item.cod_mapa
                   and mapa_solicitacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
            inner join compras.solicitacao_homologada
                    on solicitacao_homologada.exercicio       = mapa_solicitacao.exercicio_solicitacao
                   and solicitacao_homologada.cod_entidade    = mapa_solicitacao.cod_entidade
                   and solicitacao_homologada.cod_solicitacao = mapa_solicitacao.cod_solicitacao
            inner join compras.solicitacao
                    on solicitacao.exercicio       = solicitacao_homologada.exercicio
                   and solicitacao.cod_entidade    = solicitacao_homologada.cod_entidade
                   and solicitacao.cod_solicitacao = solicitacao_homologada.cod_solicitacao
            inner join compras.solicitacao_item
                    on solicitacao_item.exercicio          = mapa_item.exercicio
                   and solicitacao_item.cod_entidade       = mapa_item.cod_entidade
                   and solicitacao_item.cod_solicitacao    = mapa_item.cod_solicitacao
                   and solicitacao_item.cod_centro         = mapa_item.cod_centro
                   and solicitacao_item.cod_item           = mapa_item.cod_item
                   and solicitacao_item.exercicio          = solicitacao.exercicio
                   and solicitacao_item.cod_entidade       = solicitacao.cod_entidade
                   and solicitacao_item.cod_solicitacao    = solicitacao.cod_solicitacao
             left join compras.solicitacao_item_anulacao
                    on solicitacao_item_anulacao.exercicio = solicitacao_item.exercicio
                   and solicitacao_item_anulacao.cod_entidade  = solicitacao_item.cod_entidade
                   and solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                   and solicitacao_item_anulacao.cod_centro = solicitacao_item.cod_centro
                   and solicitacao_item_anulacao.cod_item = solicitacao_item.cod_item
            inner join almoxarifado.catalogo_item
                    on catalogo_item.cod_item = solicitacao_item.cod_item
            inner join administracao.unidade_medida
                    on unidade_medida.cod_grandeza = catalogo_item.cod_grandeza
                   and unidade_medida.cod_unidade = catalogo_item.cod_unidade
            inner join compras.solicitacao_item_dotacao
                    on solicitacao_item.exercicio        = solicitacao_item_dotacao.exercicio
                   and solicitacao_item.cod_entidade     = solicitacao_item_dotacao.cod_entidade
                   and solicitacao_item.cod_solicitacao  = solicitacao_item_dotacao.cod_solicitacao
                   and solicitacao_item.cod_centro       = solicitacao_item_dotacao.cod_centro
                   and solicitacao_item.cod_item         = solicitacao_item_dotacao.cod_item
                   and mapa_item_dotacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
            inner join compras.mapa_item_reserva
                    on mapa_item_reserva.exercicio_mapa        = mapa_item_dotacao.exercicio
                   and mapa_item_reserva.cod_mapa              = mapa_item_dotacao.cod_mapa
                   and mapa_item_reserva.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
                   and mapa_item_reserva.cod_entidade          = mapa_item_dotacao.cod_entidade
                   and mapa_item_reserva.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
                   and mapa_item_reserva.cod_centro            = mapa_item_dotacao.cod_centro
                   and mapa_item_reserva.cod_item              = mapa_item_dotacao.cod_item
                   and mapa_item_reserva.lote                  = mapa_item_dotacao.lote
                   and mapa_item_reserva.cod_despesa           = mapa_item_dotacao.cod_despesa
                   and mapa_item_reserva.cod_conta             = mapa_item_dotacao.cod_conta

                 where homologacao_anulada.cod_licitacao is null
                   and adjudicacao_anulada.cod_licitacao is null";

        return $stSql;
    }
    function recuperaEmpenhoPreEmpenhoCotacao(&$rsRecordSet, $stFiltro="", $boTransacao="")
    {
        $stSql  =  "SELECT MAX(dt_empenho) as dt_empenho
                         FROM empenho.empenho 
                         WHERE  cod_pre_empenho IN (SELECT cod_pre_empenho 
                                                                           FROM empenho.item_pre_empenho_julgamento 
                                                                        WHERE cod_cotacao =  '".$this->getDado('cod_cotacao')."' )";



        return $this->executaRecuperaSql($stSql, $rsRecordSet, '', '', $boTransacao);
    }
    
    public function __destruct() {}   
}
