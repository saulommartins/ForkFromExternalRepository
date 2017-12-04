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

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
    * $Id:$  
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TComprasCompraDiretaHomologacao extends Persistente
{
    public function TComprasCompraDiretaHomologacao()
    {
        parent::Persistente();
        $this->setTabela("compras.homologacao");

        $this->setCampoCod('num_homologacao');
        $this->setComplementoChave('cod_compra_direta, cod_modalidade, cod_entidade, exercicio_compra_direta, lote, cod_cotacao, cod_item, exercicio_cotacao, cgm_fornecedor');

        $this->AddCampo( 'exercicio'	           ,'char'       , true	, '4'	,true	,true  );
        $this->AddCampo( 'num_homologacao'	   ,'integer'    , true	, ''	,true	,true  );
        $this->AddCampo( 'exercicio_compra_direta' ,'char'       , true	, '4' 	,true  	,true  );
        $this->AddCampo( 'cod_compra_direta'       ,'integer'    , true	, '' 	,true  	,true  );
        $this->AddCampo( 'cod_modalidade'      	   ,'integer'    , true	, '' 	,true  	,true  );
        $this->AddCampo( 'cod_entidade'  	   ,'integer'    , true	, ''	,false 	,false );
        $this->AddCampo( 'lote'  		   ,'integer'    , true	, ''	,false 	,false );
        $this->AddCampo( 'cod_cotacao'  	   ,'integer'    , true	, ''	,false 	,false );
        $this->AddCampo( 'cod_item'  		   ,'char'       , true	, '4'	,false 	,false );
        $this->AddCampo( 'exercicio_cotacao'  	   ,'char'       , true	, ''	,false 	,false );
        $this->AddCampo( 'cgm_fornecedor'  	   ,'integer'    , true	, ''	,false 	,false );
        $this->AddCampo( 'cod_tipo_documento'  	   ,'integer'    , true	, ''	,false 	,false );
        $this->AddCampo( 'cod_documento'  	   ,'integer'    , true	, ''	,false 	,false );
        $this->AddCampo( 'homologado'              ,'boolean'    , false , ''   ,false  ,false );
        $this->AddCampo( 'timestamp'          	   ,'timestamo'  , false , '' 	,false  ,false );

    }

    public function recuperaItensComStatus(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
    {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if( $this->getDado( "exercicio" ) != "" )
    $stFiltro .= "and compra_direta.exercicio_entidade = '".$this->getDado( "exercicio" )."' \n";
    if( $this->getDado( "cod_entidade" ) != "" )
    $stFiltro .= "and compra_direta.cod_entidade   = ".$this->getDado( "cod_entidade" )." \n";
    if( $this->getDado( "cod_modalidade" ) != "" )
    $stFiltro .= "and compra_direta.cod_modalidade = ".$this->getDado( "cod_modalidade" )." \n";
    if( $this->getDado( "cod_compra_direta" ) != "" )
    $stFiltro .= "and compra_direta.cod_compra_direta  = ".$this->getDado( "cod_compra_direta" )." \n";

    if( $stFiltro )
    $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro)-4);

        $stFiltro .= "
            group by
               homologacao.num_homologacao
             , homologacao.cod_tipo_documento
             , homologacao.timestamp
             , homologacao.cod_tipo_documento
             , homologacao.cod_documento
             , compra_direta.cod_compra_direta
             , compra_direta.cod_modalidade
             , compra_direta.cod_entidade
             , compra_direta.exercicio_entidade
             , cotacao_item.exercicio
             , julgamento_item.exercicio
             , homologacao.lote
             , homologacao.cod_cotacao
             , homologacao.cgm_fornecedor
             , homologacao.cod_item
             , homologacao.homologado
             , sw_cgm.nom_cgm
             , cotacao_item.quantidade
             , cotacao_fornecedor_item.vl_cotacao
             , catalogo_item.descricao_resumida
             , catalogo_item.descricao
             , homologacao.homologado
             , julgamento_item.exercicio
             , julgamento_item.cod_cotacao
             , julgamento_item.cod_item
             , julgamento_item.lote
             , julgamento_item.cgm_fornecedor
             , mapa_item.exercicio
             , mapa_item.cod_mapa
             , autorizacao_empenho.cod_autorizacao
             , autorizacao_empenho.exercicio
            ";

    $stOrdem = "order by julgamento_item.cod_item";

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
            , homologacao.timestamp
            , homologacao.cod_tipo_documento
            , homologacao.cod_documento
            , compra_direta.cod_compra_direta
            , compra_direta.cod_modalidade
            , compra_direta.cod_entidade
            , compra_direta.exercicio_entidade
            , cotacao_item.exercicio as exercicio_cotacao
            , julgamento_item.exercicio as julgamento_item_exercicio
            , homologacao.lote
            , homologacao.cod_cotacao
            , homologacao.cgm_fornecedor
            , homologacao.cod_item
            , homologacao.homologado
            , sw_cgm.nom_cgm
            , cotacao_item.quantidade
            , cotacao_fornecedor_item.vl_cotacao
            , catalogo_item.descricao_resumida
            , catalogo_item.descricao
            , homologacao.homologado
            , julgamento_item.exercicio
            , julgamento_item.cod_cotacao
            , julgamento_item.cod_item
            , julgamento_item.lote
            , julgamento_item.cgm_fornecedor
            , mapa_item.exercicio
            , mapa_item.cod_mapa
            ,  case when ( not homologacao.homologado or homologacao.homologado is null )
                 then 'A Homologar'
               else  case when not exists ( select 1
                               from empenho.item_pre_empenho_julgamento
                              where item_pre_empenho_julgamento.exercicio   = julgamento_item.exercicio
                            and item_pre_empenho_julgamento.cod_cotacao     = julgamento_item.cod_cotacao
                            and item_pre_empenho_julgamento.cod_item        = julgamento_item.cod_item
                            and item_pre_empenho_julgamento.lote            = julgamento_item.lote
                            and item_pre_empenho_julgamento.cgm_fornecedor  = julgamento_item.cgm_fornecedor )
                            then 'Homologado'
                            else 'Homologado e Autorizado ' || autorizacao_empenho.cod_autorizacao||'/'||autorizacao_empenho.exercicio
               end
               end as status

             --from compras.compra_direta

             from compras.julgamento_item

           inner join compras.cotacao_item
               on cotacao_item.exercicio   = julgamento_item.exercicio
              and cotacao_item.cod_cotacao = julgamento_item.cod_cotacao
              and cotacao_item.lote        = julgamento_item.lote
              and cotacao_item.cod_item    = julgamento_item.cod_item

           inner join compras.cotacao
               on cotacao.cod_cotacao = cotacao_item.cod_cotacao
              and cotacao.exercicio   = cotacao_item.exercicio

           inner join compras.cotacao_fornecedor_item
               on cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
              and cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
              and cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
              and cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
              and cotacao_fornecedor_item.lote           = julgamento_item.lote

           inner join compras.mapa_cotacao
               on mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao
              and mapa_cotacao.exercicio_cotacao = cotacao.exercicio

           inner join compras.mapa
               on mapa.cod_mapa  = mapa_cotacao.cod_mapa
              and mapa.exercicio = mapa_cotacao.exercicio_mapa

           inner join compras.mapa_item
               on mapa_item.cod_mapa  = mapa.cod_mapa
              and mapa_item.exercicio = mapa.exercicio
              and mapa_item.cod_item  = cotacao_fornecedor_item.cod_item
              and mapa_item.lote      = cotacao_fornecedor_item.lote

           inner join compras.compra_direta
               on compra_direta.cod_mapa       = mapa.cod_mapa
              and compra_direta.exercicio_mapa = mapa.exercicio

        left join compras.mapa_item_anulacao
               on mapa_item.exercicio             = mapa_item_anulacao.exercicio
              and mapa_item.exercicio_solicitacao = mapa_item_anulacao.exercicio_solicitacao
              and mapa_item.cod_mapa              = mapa_item_anulacao.cod_mapa
              and mapa_item.cod_entidade          = mapa_item_anulacao.cod_entidade
              and mapa_item.cod_solicitacao       = mapa_item_anulacao.cod_solicitacao
              and mapa_item.cod_centro  	  = mapa_item_anulacao.cod_centro
              and mapa_item.lote        	  = mapa_item_anulacao.lote
              and mapa_item.cod_item   	          = mapa_item_anulacao.cod_item

           inner join almoxarifado.catalogo_item
               on catalogo_item.cod_item = julgamento_item.cod_item

           inner join sw_cgm
               on sw_cgm.numcgm = julgamento_item.cgm_fornecedor

        left join compras.homologacao
               on homologacao.exercicio   = julgamento_item.exercicio
              and homologacao.cod_cotacao = julgamento_item.cod_cotacao
              and homologacao.lote        = julgamento_item.lote
              and homologacao.cod_item    = julgamento_item.cod_item
              and homologacao.cgm_fornecedor = julgamento_item.cgm_fornecedor
        
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
              AND autorizacao_empenho.exercicio = pre_empenho.exercicio";
        return $stSql;

    }

/*    function recuperaItensHomologacao(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "") {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql  = $this->montaRecuperaItensHomologacao  ().$stFiltro.$stOrdem;
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

    }*/

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

            from compras.homologacao

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
             on mapa_item.cod_mapa  = mapa_cotacao.cod_mapa
            and mapa_item.exercicio = mapa_cotacao.exercicio_mapa
            and mapa_item.cod_item  = cotacao_fornecedor_item.cod_item
            and mapa_item.lote      = cotacao_fornecedor_item.lote

          left join compras.mapa_item_anulacao
             on mapa_item.exercicio             = mapa_item_anulacao.exercicio
            and mapa_item.exercicio_solicitacao = mapa_item_anulacao.exercicio_solicitacao
            and mapa_item.cod_mapa   	    = mapa_item_anulacao.cod_mapa
            and mapa_item.cod_entidade   	    = mapa_item_anulacao.cod_entidade
            and mapa_item.cod_solicitacao       = mapa_item_anulacao.cod_solicitacao
            and mapa_item.cod_centro   	    = mapa_item_anulacao.cod_centro
            and mapa_item.lote        	    = mapa_item_anulacao.lote
            and mapa_item.cod_item 		    = mapa_item_anulacao.cod_item

         inner join sw_cgm
             on sw_cgm.numcgm = homologacao.cgm_fornecedor

         inner join almoxarifado.catalogo_item
             on catalogo_item.cod_item = homologacao.cod_item

         inner join administracao.unidade_medida
             on catalogo_item.cod_grandeza = unidade_medida.cod_grandeza
            and catalogo_item.cod_unidade  = unidade_medida.cod_unidade

         INNER JOIN (
                 select julgamento_item.exercicio
                  , julgamento_item.cod_cotacao
                  , julgamento_item.cod_item
                  , julgamento_item.lote
                  , julgamento_item.cgm_fornecedor
                  , cotacao_fornecedor_item.vl_cotacao

                   from compras.julgamento_item

              inner join compras.cotacao_fornecedor_item
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

    where homologacao.cod_entidade            = ".$this->getDado('cod_entidade')."
      and homologacao.cod_modalidade          = ".$this->getDado('cod_modalidade')."
      and homologacao.cod_compra_direta       = ".$this->getDado('cod_compra_direta')."
      and homologacao.exercicio_compra_direta = '".$this->getDado('exercicio')."'
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
        , unidade_medida.cod_grANDeza
        , unidade_medida.nom_unidade
        , unidade_medida.simbolo
        , mapa.cod_mapa
        , mapa.exercicio as exercicio_mapa
        , mapa_item_reserva.cod_reserva
            , case when ((solicitacao_item.quantidade - coalesce(solicitacao_item_anulacao.quantidade,0.00) - (
                   select sum(quantidade)
                     from compras.mapa_item
                     where exercicio = solicitacao_item.exercicio
                       AND cod_entidade  = solicitacao_item.cod_entidade
                       AND cod_solicitacao = solicitacao_item.cod_solicitacao
                       AND cod_centro = solicitacao_item.cod_centro
                       AND cod_item = solicitacao_item.cod_item
                  group by cod_solicitacao , cod_entidade, exercicio
                )) = 0) then
            0::numeric(14,2)
        else (( solicitacao_item.quantidade - coalesce(solicitacao_item_anulacao.quantidade,0.00) - (
                   select coalesce(sum(quantidade),0.00)
                     from compras.mapa_item
                     where exercicio = solicitacao_item.exercicio
                       AND cod_entidade  = solicitacao_item.cod_entidade
                       AND cod_solicitacao = solicitacao_item.cod_solicitacao
                       AND cod_centro = solicitacao_item.cod_centro
                       AND cod_item = solicitacao_item.cod_item
                  group by cod_solicitacao , cod_entidade, exercicio
                )
                )::numeric(14,2) * ( cotacao_fornecedor_item.vl_cotacao / mapa_item.quantidade)::numeric(14,2))::numeric(14,2)
          end as nova_reserva_solicitacao

                FROM compras.julgamento_item

              INNER JOIN compras.homologacao
                      ON homologacao.exercicio   = julgamento_item.exercicio
                     AND homologacao.cod_cotacao = julgamento_item.cod_cotacao
                 AND homologacao.lote        = julgamento_item.lote
                 AND homologacao.cod_item    = julgamento_item.cod_item

              INNER JOIN compras.cotacao_fornecedor_item
                  ON cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
                 AND cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
                 AND cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
                 AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                 AND cotacao_fornecedor_item.lote           = julgamento_item.lote

              INNER JOIN compras.cotacao_item
                  ON cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                 AND cotacao_item.exercicio   = cotacao_fornecedor_item.exercicio
                 AND cotacao_item.lote        = cotacao_fornecedor_item.lote
                 AND cotacao_item.cod_item    = cotacao_fornecedor_item.cod_item

              INNER JOIN compras.cotacao
                  ON cotacao.cod_cotacao = cotacao_item.cod_cotacao
                 AND cotacao.exercicio   = cotacao_item.exercicio

              INNER JOIN compras.mapa_cotacao
                  ON cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
                 AND cotacao.exercicio   = mapa_cotacao.exercicio_cotacao

              INNER JOIN compras.mapa_item
                  ON mapa_cotacao.cod_mapa       = mapa_item.cod_mapa
                 AND mapa_cotacao.exercicio_mapa = mapa_item.exercicio
                 AND mapa_item.cod_item          = cotacao_fornecedor_item.cod_item
                 AND mapa_item.lote              = cotacao_fornecedor_item.lote

              INNER JOIN compras.mapa
                  ON mapa.cod_mapa  = mapa_cotacao.cod_mapa
                 AND mapa.exercicio = mapa_cotacao.exercicio_mapa


              INNER JOIN compras.compra_direta
                  ON compra_direta.cod_mapa       = mapa.cod_mapa
                 AND compra_direta.exercicio_mapa = mapa.exercicio

              INNER JOIN compras.mapa_solicitacao
                  ON mapa_solicitacao.exercicio             = mapa_item.exercicio
                 AND mapa_solicitacao.cod_entidade          = mapa_item.cod_entidade
                 AND mapa_solicitacao.cod_solicitacao       = mapa_item.cod_solicitacao
                 AND mapa_solicitacao.cod_mapa              = mapa_item.cod_mapa
                 AND mapa_solicitacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao

              INNER JOIN compras.solicitacao_homologada
                  ON solicitacao_homologada.exercicio       = mapa_solicitacao.exercicio_solicitacao
                 AND solicitacao_homologada.cod_entidade    = mapa_solicitacao.cod_entidade
                 AND solicitacao_homologada.cod_solicitacao = mapa_solicitacao.cod_solicitacao

              INNER JOIN compras.solicitacao
                  ON solicitacao.exercicio       = solicitacao_homologada.exercicio
                 AND solicitacao.cod_entidade    = solicitacao_homologada.cod_entidade
                 AND solicitacao.cod_solicitacao = solicitacao_homologada.cod_solicitacao

              INNER JOIN compras.solicitacao_item
                  ON solicitacao_item.exercicio        = mapa_item.exercicio
                 AND solicitacao_item.cod_entidade     = mapa_item.cod_entidade
                 AND solicitacao_item.cod_solicitacao  = mapa_item.cod_solicitacao
                 AND solicitacao_item.cod_centro       = mapa_item.cod_centro
                 AND solicitacao_item.cod_item         = mapa_item.cod_item
                 AND solicitacao_item.exercicio        = solicitacao.exercicio
                 AND solicitacao_item.cod_entidade     = solicitacao.cod_entidade
                 AND solicitacao_item.cod_solicitacao  = solicitacao.cod_solicitacao

               LEFT JOIN compras.solicitacao_item_anulacao
                  on solicitacao_item_anulacao.exercicio 	   = solicitacao_item.exercicio
                 AND solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade
                 AND solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                 AND solicitacao_item_anulacao.cod_centro 	   = solicitacao_item.cod_centro
                 AND solicitacao_item_anulacao.cod_item 	   = solicitacao_item.cod_item

              INNER JOIN almoxarifado.catalogo_item
                  on catalogo_item.cod_item = solicitacao_item.cod_item

              INNER JOIN administracao.unidade_medida
                  on unidade_medida.cod_grandeza = catalogo_item.cod_grandeza
                 AND unidade_medida.cod_unidade  = catalogo_item.cod_unidade

              INNER JOIN compras.solicitacao_item_dotacao
                  on solicitacao_item.exercicio        = solicitacao_item_dotacao.exercicio
                 AND solicitacao_item.cod_entidade     = solicitacao_item_dotacao.cod_entidade
                 AND solicitacao_item.cod_solicitacao  = solicitacao_item_dotacao.cod_solicitacao
                 AND solicitacao_item.cod_centro       = solicitacao_item_dotacao.cod_centro
                 AND solicitacao_item.cod_item         = solicitacao_item_dotacao.cod_item

              INNER JOIN compras.mapa_item_reserva
                  on mapa_item_reserva.exercicio_mapa        = mapa_item.exercicio
                 AND mapa_item_reserva.cod_mapa              = mapa_item.cod_mapa
                     AND mapa_item_reserva.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                 AND mapa_item_reserva.cod_entidade          = mapa_item.cod_entidade
                 AND mapa_item_reserva.cod_solicitacao       = mapa_item.cod_solicitacao
                 AND mapa_item_reserva.cod_centro            = mapa_item.cod_centro
                 AND mapa_item_reserva.cod_item              = mapa_item.cod_item
                 AND mapa_item_reserva.lote                  = mapa_item.lote

              -- WHERE compra_direta.exercicio_entidade = 2011
              --  AND compra_direta.cod_entidade   = 2
              --  AND compra_direta.cod_modalidade = 9
        ";

        return $stSql;
    }

    public function recuperaGrupoAutEmpenho(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stGrupo = "        GROUP BY cotacao_fornecedor_item.cgm_fornecedor
                    , solicitacao_item_dotacao.cod_item
                    , vw_classificacao_despesa.mascara_classificacao
                    , solicitacao_item_dotacao.cod_despesa
                    , solicitacao_item_dotacao.cod_conta
                    , solicitacao_item_dotacao.cod_entidade
                    , compra_direta.cod_modalidade
                    , despesa.num_orgao
                    , despesa.num_unidade
                    , objeto.cod_objeto
                    , objeto.descricao
                ) AS teste

            GROUP BY cod_despesa
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

    FROM ( select cotacao_fornecedor_item.cgm_fornecedor as fornecedor
        , solicitacao_item_dotacao.cod_item
        , solicitacao_item_dotacao.cod_despesa
        , solicitacao_item_dotacao.cod_conta
        , solicitacao_item_dotacao.cod_entidade
            , vw_classificacao_despesa.mascara_classificacao
            , compra_direta.cod_modalidade
            , despesa.num_orgao
        , despesa.num_unidade
            , objeto.cod_objeto
            , objeto.descricao as desc_objeto
        , 0 as historico
        , 0 as cod_tipo
        , false as implantado
        , (( sum(cotacao_fornecedor_item.vl_cotacao) / sum(cotacao_item.quantidade) ) * sum(mapa_item_dotacao.quantidade))::numeric(14,2) as reserva

        FROM compras.julgamento_item

      INNER JOIN compras.homologacao
              ON homologacao.exercicio   = julgamento_item.exercicio
         AND homologacao.cod_cotacao = julgamento_item.cod_cotacao
         AND homologacao.lote        = julgamento_item.lote
         AND homologacao.cod_item    = julgamento_item.cod_item

      INNER JOIN compras.cotacao_fornecedor_item
          ON cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
         AND cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
         AND cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
         AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
         AND cotacao_fornecedor_item.lote           = julgamento_item.lote

      INNER JOIN compras.cotacao_item
          ON cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
         AND cotacao_item.exercicio   = cotacao_fornecedor_item.exercicio
         AND cotacao_item.lote        = cotacao_fornecedor_item.lote
         AND cotacao_item.cod_item    = cotacao_fornecedor_item.cod_item

      INNER JOIN compras.cotacao
          ON cotacao.cod_cotacao = cotacao_item.cod_cotacao
         AND cotacao.exercicio   = cotacao_item.exercicio

      INNER JOIN compras.mapa_cotacao
          ON cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
         AND cotacao.exercicio   = mapa_cotacao.exercicio_cotacao

      INNER JOIN compras.mapa
          ON mapa.cod_mapa  = mapa_cotacao.cod_mapa
         AND mapa.exercicio = mapa_cotacao.exercicio_mapa

      INNER JOIN compras.compra_direta
          ON compra_direta.cod_mapa       = mapa.cod_mapa
         AND compra_direta.exercicio_mapa = mapa.exercicio

      INNER JOIN compras.objeto
          ON objeto.cod_objeto = compra_direta.cod_objeto

      INNER JOIN compras.mapa_item
          ON mapa_cotacao.cod_mapa       = mapa_item.cod_mapa
         AND mapa_cotacao.exercicio_mapa = mapa_item.exercicio
         AND mapa_item.cod_item          = cotacao_fornecedor_item.cod_item
         AND mapa_item.lote              = cotacao_fornecedor_item.lote

      INNER JOIN compras.mapa_item_dotacao
          ON mapa_item_dotacao.exercicio             = mapa_item.exercicio
         AND mapa_item_dotacao.cod_mapa              = mapa_item.cod_mapa
         AND mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
         AND mapa_item_dotacao.cod_entidade          = mapa_item.cod_entidade
         AND mapa_item_dotacao.cod_solicitacao       = mapa_item.cod_solicitacao
         AND mapa_item_dotacao.cod_centro            = mapa_item.cod_centro
         AND mapa_item_dotacao.cod_item              = mapa_item.cod_item
         AND mapa_item_dotacao.lote                  = mapa_item.lote

      INNER JOIN compras.mapa_solicitacao
          ON mapa_solicitacao.exercicio             = mapa_item.exercicio
         AND mapa_solicitacao.cod_entidade          = mapa_item.cod_entidade
         AND mapa_solicitacao.cod_solicitacao       = mapa_item.cod_solicitacao
         AND mapa_solicitacao.cod_mapa              = mapa_item.cod_mapa
         AND mapa_solicitacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao

      INNER JOIN compras.solicitacao_homologada
          ON solicitacao_homologada.exercicio       = mapa_solicitacao.exercicio_solicitacao
         AND solicitacao_homologada.cod_entidade    = mapa_solicitacao.cod_entidade
         AND solicitacao_homologada.cod_solicitacao = mapa_solicitacao.cod_solicitacao

      INNER JOIN compras.solicitacao
          ON solicitacao.exercicio       = solicitacao_homologada.exercicio
         AND solicitacao.cod_entidade    = solicitacao_homologada.cod_entidade
         AND solicitacao.cod_solicitacao = solicitacao_homologada.cod_solicitacao

      INNER JOIN compras.solicitacao_item
          ON solicitacao_item.exercicio        = mapa_item.exercicio
         AND solicitacao_item.cod_entidade     = mapa_item.cod_entidade
         AND solicitacao_item.cod_solicitacao  = mapa_item.cod_solicitacao
         AND solicitacao_item.cod_centro       = mapa_item.cod_centro
         AND solicitacao_item.cod_item         = mapa_item.cod_item
         AND solicitacao_item.exercicio        = solicitacao.exercicio
         AND solicitacao_item.cod_entidade     = solicitacao.cod_entidade
         AND solicitacao_item.cod_solicitacao  = solicitacao.cod_solicitacao

      INNER JOIN compras.solicitacao_item_dotacao
          ON solicitacao_item.exercicio       = solicitacao_item_dotacao.exercicio
         AND solicitacao_item.cod_entidade    = solicitacao_item_dotacao.cod_entidade
         AND solicitacao_item.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
         AND solicitacao_item.cod_centro      = solicitacao_item_dotacao.cod_centro
         AND solicitacao_item.cod_item        = solicitacao_item_dotacao.cod_item
         AND mapa_item_dotacao.cod_despesa    = solicitacao_item_dotacao.cod_despesa

     INNER JOIN orcamento.despesa
         ON despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa
        AND despesa.exercicio   = solicitacao_item_dotacao.exercicio

     INNER JOIN orcamento.vw_classificacao_despesa
         ON solicitacao_item_dotacao.cod_conta = vw_classificacao_despesa.cod_conta
        AND solicitacao_item_dotacao.exercicio = vw_classificacao_despesa.exercicio
    ";

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
}
