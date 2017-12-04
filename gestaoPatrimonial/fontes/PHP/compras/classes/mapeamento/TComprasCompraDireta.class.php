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

    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação: 30/01/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-03.04.33
                    uc-03.04.32

    $Id: TComprasCompraDireta.class.php 65449 2016-05-23 18:17:48Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TComprasCompraDireta extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    **/
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela("compras.compra_direta");

        $this->setCampoCod('cod_compra_direta');
        $this->setComplementoChave('cod_entidade,exercicio_entidade,cod_modalidade');

        $this->AddCampo( 'cod_compra_direta'	,'sequence' ,true	, ''	,true	,false  );
        $this->AddCampo( 'cod_entidade'       	,'integer'  ,true	, '' 	,true  	,true	);
        $this->AddCampo( 'exercicio_entidade'   ,'char'     ,true	, '4' 	,true  	,true	);
        $this->AddCampo( 'cod_modalidade'      	,'integer'  ,true	, '' 	,true  	,true	);
        $this->AddCampo( 'cod_tipo_objeto'      ,'integer'  ,true	, '' 	,false 	,true 	);
        $this->AddCampo( 'cod_objeto'           ,'integer'  ,true	, '' 	,false 	,true 	);
        $this->AddCampo( 'exercicio_mapa'       ,'char'     ,true	, '4' 	,false 	,true	);
        $this->AddCampo( 'cod_mapa'             ,'integer'  ,true	, '' 	,false  ,true   );
        $this->AddCampo( 'dt_entrega_proposta'  ,'date'     ,true	, '' 	,false  ,false  );
        $this->AddCampo( 'dt_validade_proposta' ,'date'     ,true	, '' 	,false 	,false  );
        $this->AddCampo( 'condicoes_pagamento'  ,'varchar'  ,true	, '80' 	,false 	,false  );
        $this->AddCampo( 'prazo_entrega'        ,'integer'  ,true	, '3' 	,false 	,false  );
        $this->AddCampo( 'timestamp'            ,'timestamp',false 	, '' 	,false  ,false  );
    }

    public function recuperaMapaCompraDireta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaMapaCompraDireta().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaMapaCompraDireta()
    {
        $stSql = "SELECT compra_direta.cod_mapa
                       , compra_direta.exercicio_mapa
                       , objeto.descricao as objeto
                    FROM compras.compra_direta
              INNER JOIN compras.mapa
                      ON mapa.cod_mapa      = compra_direta.cod_mapa
                     AND mapa.exercicio     = compra_direta.exercicio_mapa
              INNER JOIN compras.objeto
                      ON objeto.cod_objeto  = mapa.cod_objeto
                    ";

        return $stSql;
    }

    public function recuperaMapaCompraDiretaJulgada(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaMapaCompraDiretaJulgada",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaMapaCompraDiretaJulgada()
    {
        $stSql = "
            SELECT  compra_direta.cod_mapa
                 ,  compra_direta.exercicio_mapa
                 ,  objeto.descricao AS objeto
              FROM  compras.compra_direta
        INNER JOIN  compras.objeto
                ON  objeto.cod_objeto = compra_direta.cod_objeto

        INNER JOIN  compras.mapa_cotacao
                ON  mapa_cotacao.cod_mapa = compra_direta.cod_mapa
               AND  mapa_cotacao.exercicio_mapa = compra_direta.exercicio_mapa

        INNER JOIN  compras.cotacao
                ON  mapa_cotacao.cod_cotacao = cotacao.cod_cotacao
               AND  mapa_cotacao.exercicio_cotacao = cotacao.exercicio

        INNER JOIN  compras.julgamento
                ON  cotacao.exercicio = julgamento.exercicio
               AND  cotacao.cod_cotacao = julgamento.cod_cotacao

             WHERE  NOT EXISTS (    SELECT  1
                                      FROM  compras.compra_direta_anulacao
                                     WHERE  compra_direta_anulacao.cod_modalidade = compra_direta.cod_modalidade
                                       AND  compra_direta_anulacao.exercicio_entidade = compra_direta.exercicio_entidade
                                       AND  compra_direta_anulacao.cod_entidade = compra_direta.cod_entidade
                                       AND  compra_direta_anulacao.cod_compra_direta = compra_direta.cod_compra_direta
                               )
                -- Não pode existir uma cotação anulada.
                AND NOT EXISTS (
                                    SELECT  1
                                      FROM  compras.cotacao_anulada
                                     WHERE  cotacao_anulada.cod_cotacao = cotacao.cod_cotacao
                                       AND  cotacao_anulada.exercicio   = cotacao.exercicio
                               )

        ";
        if ($this->getDado('cod_compra_direta')) {
            $stSql .= " AND compra_direta.cod_compra_direta = ".$this->getDado('cod_compra_direta')." ";
        }
        if ($this->getDado('cod_modalidade')) {
            $stSql .= " AND compra_direta.cod_modalidade = ".$this->getDado('cod_modalidade')." ";
        }
        if ($this->getDado('cod_entidade')) {
            $stSql .= " AND compra_direta.cod_entidade = ".$this->getDado('cod_entidade')." ";
        }
        if ($this->getDado('exercicio_entidade')) {
            $stSql .= " AND compra_direta.exercicio_entidade = '".$this->getDado('exercicio_entidade')."' ";
        }

        return $stSql;

    }

    public function recuperaCompraDiretaPorMapa(&$rsRecordSet,$stFiltro='',$stOrder='',$boTransacao='')
    {
        return $this->executaRecupera("montaRecuperaCompraDiretaPorMapa",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaCompraDiretaPorMapa()
    {
        $stSql = "
                    SELECT
                            compra_direta.cod_compra_direta
                         ,	compra_direta.cod_entidade
                         ,  sw_cgm.nom_cgm as entidade
                         ,	compra_direta.exercicio_entidade
                         ,	compra_direta.cod_modalidade
                         ,  modalidade.descricao as modalidade
                         ,  compra_direta.cod_mapa
                         ,  compra_direta.exercicio_mapa
                         ,  compra_direta.cod_objeto
                         ,  objeto.descricao as objeto
                         ,  mapa.cod_tipo_licitacao
                      FROM	compras.compra_direta

                INNER JOIN  compras.mapa
                        ON  mapa.cod_mapa = compra_direta.cod_mapa
                       AND  mapa.exercicio = compra_direta.exercicio_mapa

                INNER JOIN  orcamento.entidade
                        ON  entidade.cod_entidade = compra_direta.cod_entidade
            AND entidade.exercicio    = '".Sessao::getExercicio()."'

                INNER JOIN  sw_cgm
                        ON  sw_cgm.numcgm = entidade.numcgm

                INNER JOIN  compras.modalidade
                        ON  modalidade.cod_modalidade = compra_direta.cod_modalidade

                INNER JOIN  compras.objeto
                        ON  objeto.cod_objeto = compra_direta.cod_objeto
                     WHERE	compra_direta.cod_mapa = ".$this->getDado('cod_mapa')."
                       AND	compra_direta.exercicio_mapa = '".$this->getDado('exercicio_mapa')."'

                       -- NÃO PODE LISTAR COMPRAS DIRETAS ANULADAS.
                       AND  NOT EXISTS
                            (
                                SELECT  1
                                  FROM  compras.compra_direta_anulacao
                                 WHERE  compra_direta_anulacao.cod_modalidade     = compra_direta.cod_modalidade
                                   AND  compra_direta_anulacao.exercicio_entidade = compra_direta.exercicio_entidade
                                   AND  compra_direta_anulacao.cod_entidade       = compra_direta.cod_entidade
                                   AND  compra_direta_anulacao.cod_compra_direta  = compra_direta.cod_compra_direta
                            )
        ";

        return $stSql;
    }

        function recuperaItensAgrupadosAutorizacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
        {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stGroupBy = " group by cotacao_fornecedor_item.cgm_fornecedor
                            , vw_classificacao_despesa.mascara_classificacao
                            , solicitacao_item_dotacao.cod_despesa
                            , solicitacao_item_dotacao.cod_conta
                            , solicitacao_item_dotacao.cod_entidade
                            , nom_entidade
                            , compra_direta.cod_modalidade
                            , despesa.num_orgao
                            , despesa.num_unidade
                            , objeto.cod_objeto
                            , objeto.descricao";
        $stSql = $this->montaRecuperaItensAgrupadosAutorizacao().$stFiltro.$stGroupBy.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaItensAgrupadosAutorizacao()
    {
        $stSql = "
            select  cotacao_fornecedor_item.cgm_fornecedor as fornecedor
                  , solicitacao_item_dotacao.cod_despesa
                  , solicitacao_item_dotacao.cod_conta
                  , solicitacao_item_dotacao.cod_entidade
                  , sw_cgm.nom_cgm as nom_entidade
                  , vw_classificacao_despesa.mascara_classificacao
                  , compra_direta.cod_modalidade
                  , despesa.num_orgao
                  , despesa.num_unidade
                  , objeto.cod_objeto
                  , objeto.descricao as desc_objeto
                  , 0 as historico
                  , 0 as cod_tipo
                  , false as implantado
                  , sum(( cotacao_fornecedor_item.vl_cotacao / cotacao_item.quantidade ) * mapa_item_dotacao.quantidade)::numeric(14,2) as reserva
              from
                  compras.compra_direta
                  inner join compras.mapa_cotacao
                          on mapa_cotacao.cod_mapa = compra_direta.cod_mapa
                         and mapa_cotacao.exercicio_mapa = compra_direta.exercicio_mapa
                  inner join compras.objeto
                          on objeto.cod_objeto = compra_direta.cod_objeto
                  inner join compras.cotacao
                          on cotacao.cod_cotacao    = mapa_cotacao.cod_cotacao
                         and cotacao.exercicio      = mapa_cotacao.exercicio_cotacao
                  inner join compras.cotacao_item
                          on cotacao_item.cod_cotacao   = cotacao.cod_cotacao
                         and cotacao_item.exercicio     = cotacao.exercicio
                  inner join compras.cotacao_fornecedor_item
                          on cotacao_item.cod_cotacao          = cotacao_fornecedor_item.cod_cotacao
                         and cotacao_item.exercicio            = cotacao_fornecedor_item.exercicio
                         and cotacao_item.cod_item             = cotacao_fornecedor_item.cod_item
                         and cotacao_item.lote                 = cotacao_fornecedor_item.lote
                  inner join compras.julgamento_item
                          on julgamento_item.exercicio = cotacao_fornecedor_item.exercicio
                         and julgamento_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                         and julgamento_item.cod_item = cotacao_fornecedor_item.cod_item
                         and julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                         and julgamento_item.lote = cotacao_fornecedor_item.lote
                  inner join compras.mapa_item
                          on mapa_cotacao.cod_mapa      = mapa_item.cod_mapa
                         and mapa_cotacao.exercicio_mapa= mapa_item.exercicio
                         and mapa_item.cod_item      = cotacao_fornecedor_item.cod_item
                         and mapa_item.lote          = cotacao_fornecedor_item.lote
                  inner join compras.mapa_item_dotacao
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
                  inner join compras.solicitacao_item_dotacao
                          on solicitacao_item.exercicio        = solicitacao_item_dotacao.exercicio
                         and solicitacao_item.cod_entidade     = solicitacao_item_dotacao.cod_entidade
                         and solicitacao_item.cod_solicitacao  = solicitacao_item_dotacao.cod_solicitacao
                         and solicitacao_item.cod_centro       = solicitacao_item_dotacao.cod_centro
                         and solicitacao_item.cod_item         = solicitacao_item_dotacao.cod_item
                         and mapa_item_dotacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
                  inner join orcamento.despesa
                          on despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa
                         and despesa.exercicio = solicitacao_item_dotacao.exercicio
                  inner join orcamento.vw_classificacao_despesa
                          on solicitacao_item_dotacao.cod_conta =  vw_classificacao_despesa.cod_conta
                         and solicitacao_item_dotacao.exercicio =  vw_classificacao_despesa.exercicio
                  inner join orcamento.entidade
                          on entidade.cod_entidade = solicitacao_item_dotacao.cod_entidade
                         and entidade.exercicio = solicitacao_item_dotacao.exercicio
                  inner join sw_cgm
                          on sw_cgm.numcgm = entidade.numcgm
                       where compra_direta.cod_compra_direta is not null
                         and julgamento_item.ordem = 1

                ";

            return $stSql;
    }

    public function recuperaInfoItensAgrupadosSolicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stGroupBy = " group by cotacao_fornecedor_item.cgm_fornecedor
                           , vw_classificacao_despesa.mascara_classificacao
                           , cotacao_fornecedor_item.lote
                           , solicitacao_item_dotacao.cod_despesa
                           , solicitacao_item_dotacao.cod_conta
                           , solicitacao_item_dotacao.cod_entidade
                           , nom_entidade
                           , compra_direta.cod_modalidade
                           , despesa.num_orgao
                           , despesa.num_unidade
                           , objeto.cod_objeto
                           , objeto.descricao
                           , cotacao_item.cod_cotacao
                           , cotacao_item.exercicio
                           , cotacao_item.cod_item
                           , cotacao_fornecedor_item.cod_marca
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
                           , cotacao_item.quantidade
                           , solicitacao_item.complemento
                           , solicitacao_item.cod_centro
                           , solicitacao.cod_solicitacao";
        $stSql = $this->montaRecuperaInfoItensAgrupadosSolicitacao().$stFiltro.$stGroupBy.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaInfoItensAgrupadosSolicitacao()
    {
        $stSql = "select cotacao_item.cod_cotacao
                       , cotacao_item.exercicio
                       , cotacao_item.cod_item
                       , cotacao_fornecedor_item.cod_marca
                       , cotacao_item.lote
                       , cotacao_fornecedor_item.cgm_fornecedor as fornecedor
                       , cotacao_fornecedor_item.lote
                       , solicitacao_item_dotacao.cod_despesa
                       , solicitacao_item_dotacao.cod_conta
                       , solicitacao.cod_solicitacao
                       , solicitacao_item.exercicio as exercicio_solicitacao
                       , solicitacao_item_dotacao.cod_entidade
                       , sw_cgm.nom_cgm as nom_entidade
                       , vw_classificacao_despesa.mascara_classificacao
                       , compra_direta.cod_modalidade
                       , despesa.num_orgao
                       , despesa.num_unidade
                       , objeto.cod_objeto
                       , objeto.descricao as desc_objeto
                       , 0 as historico
                       , 0 as cod_tipo
                       , false as implantado
                       , (( sum(cotacao_fornecedor_item.vl_cotacao) / sum(cotacao_item.quantidade) ) * (sum(mapa_item_dotacao.quantidade) - coalesce (sum(mapa_item_anulacao.quantidade),0)))::numeric(14,2) as reserva
                       , (sum(mapa_item_dotacao.quantidade) - coalesce (sum(mapa_item_anulacao.quantidade),0))::numeric(14,2) as qtd_cotacao
                       , (( sum(cotacao_fornecedor_item.vl_cotacao) / sum(cotacao_item.quantidade) ) * (sum(mapa_item_dotacao.quantidade) - coalesce (sum(mapa_item_anulacao.quantidade),0)))::numeric(14,2) as vl_cotacao
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

                    from compras.compra_direta
                   inner join compras.mapa_cotacao
                           on mapa_cotacao.cod_mapa = compra_direta.cod_mapa
                          and mapa_cotacao.exercicio_mapa = compra_direta.exercicio_mapa

                   inner join compras.mapa
                           on mapa.cod_mapa = mapa_cotacao.cod_mapa
                          and mapa.exercicio = mapa_cotacao.exercicio_mapa

                   inner join compras.objeto
                           on objeto.cod_objeto = compra_direta.cod_objeto

                   inner join compras.cotacao
                           on cotacao.cod_cotacao    = mapa_cotacao.cod_cotacao
                          and cotacao.exercicio      = mapa_cotacao.exercicio_cotacao

                   inner join compras.cotacao_item
                           on cotacao_item.cod_cotacao   = cotacao.cod_cotacao
                          and cotacao_item.exercicio     = cotacao.exercicio

                   inner join compras.cotacao_fornecedor_item
                           on cotacao_item.cod_cotacao          = cotacao_fornecedor_item.cod_cotacao
                          and cotacao_item.exercicio            = cotacao_fornecedor_item.exercicio
                          and cotacao_item.cod_item             = cotacao_fornecedor_item.cod_item
                          and cotacao_item.lote                 = cotacao_fornecedor_item.lote

                   inner join compras.julgamento_item
                           on julgamento_item.exercicio = cotacao_fornecedor_item.exercicio
                          and julgamento_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                          and julgamento_item.cod_item = cotacao_fornecedor_item.cod_item
                          and julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                          and julgamento_item.lote = cotacao_fornecedor_item.lote

                   inner join compras.mapa_item
                           on mapa_cotacao.cod_mapa      = mapa_item.cod_mapa
                          and mapa_cotacao.exercicio_mapa= mapa_item.exercicio
                          and mapa_item.cod_item      = cotacao_fornecedor_item.cod_item
                          and mapa_item.lote          = cotacao_fornecedor_item.lote

                    inner join compras.mapa_item_dotacao
                           on mapa_item_dotacao.exercicio             = mapa_item.exercicio
                          and mapa_item_dotacao.cod_mapa              = mapa_item.cod_mapa
                          and mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                          and mapa_item_dotacao.cod_entidade          = mapa_item.cod_entidade
                          and mapa_item_dotacao.cod_solicitacao       = mapa_item.cod_solicitacao
                          and mapa_item_dotacao.cod_centro            = mapa_item.cod_centro
                          and mapa_item_dotacao.cod_item              = mapa_item.cod_item
                          and mapa_item_dotacao.lote                  = mapa_item.lote

                    LEFT JOIN compras.mapa_item_anulacao
                           ON mapa_item_anulacao.exercicio             = mapa_item_dotacao.exercicio
                          AND mapa_item_anulacao.cod_mapa              = mapa_item_dotacao.cod_mapa
                          AND mapa_item_anulacao.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
                          AND mapa_item_anulacao.cod_entidade          = mapa_item_dotacao.cod_entidade
                          AND mapa_item_anulacao.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
                          AND mapa_item_anulacao.cod_centro            = mapa_item_dotacao.cod_centro
                          AND mapa_item_anulacao.cod_item              = mapa_item_dotacao.cod_item
                          AND mapa_item_anulacao.lote                  = mapa_item_dotacao.lote
                          AND mapa_item_anulacao.cod_conta             = mapa_item_dotacao.cod_conta
                          AND mapa_item_anulacao.cod_despesa           = mapa_item_dotacao.cod_despesa

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

                   inner join orcamento.despesa
                           on despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa
                          and despesa.exercicio = solicitacao_item_dotacao.exercicio

                   inner join orcamento.vw_classificacao_despesa
                           on solicitacao_item_dotacao.cod_conta =  vw_classificacao_despesa.cod_conta
                          and solicitacao_item_dotacao.exercicio =  vw_classificacao_despesa.exercicio

                   inner join orcamento.entidade
                           on entidade.cod_entidade = solicitacao_item_dotacao.cod_entidade
                          and entidade.exercicio = solicitacao_item_dotacao.exercicio

                   inner join sw_cgm
                           on sw_cgm.numcgm = entidade.numcgm

                   where compra_direta.cod_compra_direta is not null
                     and julgamento_item.ordem = 1 ";

        return $stSql;
    }

    public function recuperaItensAutorizacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaItensAutorizacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaItensAutorizacao()
    {
        $stSql = "
           select
                  cotacao_item.cod_cotacao
                , cotacao_item.exercicio
                , cotacao_item.cod_item
                , cotacao_item.lote
                , solicitacao_item.exercicio as exercicio_solicitacao
                , cotacao_fornecedor_item.cgm_fornecedor as fornecedor
                , solicitacao_item_dotacao.quantidade as qtd_solicitada
                , solicitacao_item.cod_solicitacao
                , solicitacao_item_dotacao.cod_despesa
                , solicitacao_item_dotacao.cod_conta
                , solicitacao_item_dotacao.cod_centro
                , mapa_item_dotacao.quantidade as qtd_mapa
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
                                solicitacao_item_dotacao.quantidade
                                -
                                coalesce(solicitacao_item_anulacao.quantidade,0.00)
                                -
                                (
                                   select sum(quantidade)
                                     from compras.mapa_item_dotacao
                                     where exercicio       = solicitacao_item_dotacao.exercicio
                                       and cod_entidade    = solicitacao_item_dotacao.cod_entidade
                                       and cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                                       and cod_centro      = solicitacao_item_dotacao.cod_centro
                                       and cod_item        = solicitacao_item_dotacao.cod_item
                                       and cod_despesa     = solicitacao_item_dotacao.cod_despesa
                                       and cod_conta       = solicitacao_item_dotacao.cod_conta
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
                                solicitacao_item_dotacao.quantidade
                                -
                                coalesce(solicitacao_item_anulacao.quantidade,0.00)
                                -
                                (
                                   select coalesce(sum(quantidade),0.00)
                                     from compras.mapa_item_dotacao
                                     where exercicio       = solicitacao_item_dotacao.exercicio
                                       and cod_entidade    = solicitacao_item_dotacao.cod_entidade
                                       and cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                                       and cod_centro      = solicitacao_item_dotacao.cod_centro
                                       and cod_item        = solicitacao_item_dotacao.cod_item
                                       and cod_despesa     = solicitacao_item_dotacao.cod_despesa
                                       and cod_conta       = solicitacao_item_dotacao.cod_conta
                                  group by cod_solicitacao , cod_entidade, exercicio
                                )
                            )::numeric(14,2)
                            *
                            (
                                cotacao_fornecedor_item.vl_cotacao
                                /
                                mapa_item_dotacao.quantidade
                            )::numeric(14,2)
                        )::numeric(14,2)
              end as nova_reserva_solicitacao
              from
                  compras.compra_direta
                  inner join compras.mapa_cotacao
                          on mapa_cotacao.cod_mapa = compra_direta.cod_mapa
                         and mapa_cotacao.exercicio_mapa = compra_direta.exercicio_mapa
                  inner join compras.mapa
                          on mapa.cod_mapa = mapa_cotacao.cod_mapa
                         and mapa.exercicio = mapa_cotacao.exercicio_mapa
                  inner join compras.objeto
                          on objeto.cod_objeto = compra_direta.cod_objeto
                  inner join compras.cotacao
                          on cotacao.cod_cotacao    = mapa_cotacao.cod_cotacao
                         and cotacao.exercicio      = mapa_cotacao.exercicio_cotacao
                  inner join compras.cotacao_item
                          on cotacao_item.cod_cotacao   = cotacao.cod_cotacao
                         and cotacao_item.exercicio     = cotacao.exercicio
                  inner join compras.cotacao_fornecedor_item
                          on cotacao_item.cod_cotacao          = cotacao_fornecedor_item.cod_cotacao
                         and cotacao_item.exercicio            = cotacao_fornecedor_item.exercicio
                         and cotacao_item.cod_item             = cotacao_fornecedor_item.cod_item
                         and cotacao_item.lote                 = cotacao_fornecedor_item.lote
                  inner join compras.julgamento_item
                          on julgamento_item.exercicio = cotacao_fornecedor_item.exercicio
                         and julgamento_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                         and julgamento_item.cod_item = cotacao_fornecedor_item.cod_item
                         and julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                         and julgamento_item.lote = cotacao_fornecedor_item.lote
                  inner join compras.mapa_item
                          on mapa_cotacao.cod_mapa      = mapa_item.cod_mapa
                         and mapa_cotacao.exercicio_mapa= mapa_item.exercicio
                         and mapa_item.cod_item      = cotacao_fornecedor_item.cod_item
                         and mapa_item.lote          = cotacao_fornecedor_item.lote
                  inner join compras.mapa_item_dotacao
                          on mapa_item.exercicio              = mapa_item_dotacao.exercicio
                         and mapa_item.cod_mapa               = mapa_item_dotacao.cod_mapa
                         and mapa_item.exercicio_solicitacao  = mapa_item_dotacao.exercicio_solicitacao
                         and mapa_item.cod_entidade           = mapa_item_dotacao.cod_entidade
                         and mapa_item.cod_solicitacao        = mapa_item_dotacao.cod_solicitacao
                         and mapa_item.cod_centro             = mapa_item_dotacao.cod_centro
                         and mapa_item.cod_item               = mapa_item_dotacao.cod_item
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
                  inner join compras.solicitacao_item_dotacao
                          on solicitacao_item_dotacao.exercicio          = solicitacao_item.exercicio
                         and solicitacao_item_dotacao.cod_entidade       = solicitacao_item.cod_entidade
                         and solicitacao_item_dotacao.cod_solicitacao    = solicitacao_item.cod_solicitacao
                         and solicitacao_item_dotacao.cod_centro         = solicitacao_item.cod_centro
                         and solicitacao_item_dotacao.cod_item           = solicitacao_item.cod_item
                         and solicitacao_item_dotacao.cod_despesa        = mapa_item_dotacao.cod_despesa
                         and solicitacao_item_dotacao.cod_conta          = mapa_item_dotacao.cod_conta
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
                  inner join compras.mapa_item_reserva
                          on mapa_item_dotacao.exercicio             = mapa_item_reserva.exercicio_mapa
                         and mapa_item_dotacao.cod_mapa              = mapa_item_reserva.cod_mapa
                         and mapa_item_dotacao.exercicio_solicitacao = mapa_item_reserva.exercicio_solicitacao
                         and mapa_item_dotacao.cod_entidade          = mapa_item_reserva.cod_entidade
                         and mapa_item_dotacao.cod_solicitacao       = mapa_item_reserva.cod_solicitacao
                         and mapa_item_dotacao.cod_centro            = mapa_item_reserva.cod_centro
                         and mapa_item_dotacao.cod_item              = mapa_item_reserva.cod_item
                         and mapa_item_dotacao.lote                  = mapa_item_reserva.lote
                         and mapa_item_dotacao.cod_despesa           = mapa_item_reserva.cod_despesa
                         and mapa_item_dotacao.cod_conta             = mapa_item_reserva.cod_conta
                         and mapa_item.lote                  = mapa_item_reserva.lote
                       where compra_direta.cod_compra_direta is not null
                            and julgamento_item.ordem = 1
        ";

        return $stSql;
    }
    public function recuperaItensDetalhesAutorizacaoEmpenho(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaItensDetalhesAutorizacaoEmpenho().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaItensDetalhesAutorizacaoEmpenho()
    {
        $stSql = "
             SELECT mapa_item.cod_solicitacao
                  , mapa_item.cod_item
                  , mapa_item.cod_centro
                  , CASE
                        WHEN mapa_item.lote = 0 THEN
                            'Unico'::varchar
                        ELSE
                            mapa_item.lote::varchar
                    END AS lote
                  , (mapa_item_dotacao.quantidade - coalesce (mapa_item_anulacao.quantidade,0))::numeric(14,2) as quantidade
                  , (( cotacao_fornecedor_item.vl_cotacao / cotacao_item.quantidade ) * (mapa_item_dotacao.quantidade - coalesce (mapa_item_anulacao.quantidade,0)))::numeric(14,2) AS vl_cotacao
                  , ( cotacao_fornecedor_item.vl_cotacao / cotacao_item.quantidade )::numeric(14,2) AS vl_unitario
                  , conta_despesa.cod_estrutural
                  , julgamento_item.cgm_fornecedor
                  , ( SELECT nom_cgm FROM sw_cgm WHERE numcgm = julgamento_item.cgm_fornecedor ) AS fornecedor
               FROM compras.compra_direta
               JOIN compras.mapa
                 ON compra_direta.cod_mapa       = mapa.cod_mapa
                AND compra_direta.exercicio_mapa = mapa.exercicio
               JOIN compras.mapa_item
                 ON mapa_item.cod_mapa  = mapa.cod_mapa
                AND mapa_item.exercicio = mapa.exercicio
                JOIN compras.mapa_item_dotacao
                 ON mapa_item_dotacao.cod_mapa              = mapa_item.cod_mapa
                AND mapa_item_dotacao.exercicio             = mapa_item.exercicio
                AND mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                AND mapa_item_dotacao.cod_entidade          = mapa_item.cod_entidade
                AND mapa_item_dotacao.cod_solicitacao       = mapa_item.cod_solicitacao
                AND mapa_item_dotacao.cod_centro            = mapa_item.cod_centro
                AND mapa_item_dotacao.cod_item              = mapa_item.cod_item
                AND mapa_item_dotacao.lote                  = mapa_item.lote
          LEFT JOIN compras.mapa_item_anulacao
                 ON mapa_item_anulacao.exercicio             = mapa_item_dotacao.exercicio
                AND mapa_item_anulacao.cod_mapa              = mapa_item_dotacao.cod_mapa
                AND mapa_item_anulacao.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
                AND mapa_item_anulacao.cod_entidade          = mapa_item_dotacao.cod_entidade
                AND mapa_item_anulacao.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
                AND mapa_item_anulacao.cod_centro            = mapa_item_dotacao.cod_centro
                AND mapa_item_anulacao.cod_item              = mapa_item_dotacao.cod_item
                AND mapa_item_anulacao.lote                  = mapa_item_dotacao.lote
                AND mapa_item_anulacao.cod_conta             = mapa_item_dotacao.cod_conta
                AND mapa_item_anulacao.cod_despesa           = mapa_item_dotacao.cod_despesa
               JOIN compras.mapa_cotacao
                 ON mapa_cotacao.cod_mapa       = mapa.cod_mapa
                AND mapa_cotacao.exercicio_mapa = mapa.exercicio
               JOIN compras.cotacao
                 ON cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
                AND cotacao.exercicio   = mapa_cotacao.exercicio_cotacao
               JOIN compras.julgamento_item
                 ON julgamento_item.cod_cotacao = mapa_cotacao.cod_cotacao
                AND julgamento_item.exercicio   = mapa_cotacao.exercicio_cotacao
                AND julgamento_item.cod_item    = mapa_item.cod_item
                AND julgamento_item.lote        = mapa_item.lote
               JOIN compras.cotacao_fornecedor_item
                 ON cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
                AND cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
                AND cotacao_fornecedor_item.lote           = julgamento_item.lote
                AND cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
                AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
               JOIN compras.cotacao_item
                 ON cotacao_item.cod_item    = cotacao_fornecedor_item.cod_item
                AND cotacao_item.exercicio   = cotacao_fornecedor_item.exercicio
                AND cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                AND cotacao_item.lote        = cotacao_fornecedor_item.lote
                JOIN compras.solicitacao_item
                 ON solicitacao_item.cod_solicitacao = mapa_item.cod_solicitacao
                AND solicitacao_item.exercicio       = mapa_item.exercicio_solicitacao
                AND solicitacao_item.cod_entidade    = mapa_item.cod_entidade
                AND solicitacao_item.cod_centro      = mapa_item.cod_centro
                AND solicitacao_item.cod_item        = mapa_item.cod_item
               JOIN compras.solicitacao_item_dotacao
                 ON solicitacao_item_dotacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                AND solicitacao_item_dotacao.exercicio       = solicitacao_item.exercicio
                AND solicitacao_item_dotacao.cod_entidade    = solicitacao_item.cod_entidade
                AND solicitacao_item_dotacao.cod_centro      = solicitacao_item.cod_centro
                AND solicitacao_item_dotacao.cod_item        = solicitacao_item.cod_item
                AND solicitacao_item_dotacao.cod_despesa     = mapa_item_dotacao.cod_despesa
               JOIN orcamento.conta_despesa
                 ON conta_despesa.cod_conta  = solicitacao_item_dotacao.cod_conta
                AND conta_despesa.exercicio  = solicitacao_item_dotacao.exercicio
               JOIN orcamento.despesa
                 ON despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa
                AND despesa.exercicio   = solicitacao_item_dotacao.exercicio
              WHERE julgamento_item.ordem = 1
        ";

        return $stSql;
    }

    public function recuperaCompraDireta(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaCompraDireta",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaCompraDireta()
    {
        global $request;

    $stSql = "
            SELECT  compra_direta.cod_compra_direta
                 ,  compra_direta.cod_modalidade
                 ,  modalidade.descricao AS modalidade
                 ,  compra_direta.cod_entidade
                 ,  sw_cgm.nom_cgm AS entidade
                 ,  entidade.exercicio AS entidade_exercicio
                 ,  compra_direta.exercicio_entidade
                 ,  TO_CHAR(compra_direta.timestamp,'dd/mm/yyyy') as data
                 ,  TO_CHAR(compra_direta.timestamp,'HH24:MI') as hora
                 ,  compra_direta.cod_mapa
                 ,  compra_direta.exercicio_mapa
                 ,  mapa.cod_tipo_licitacao
                 ,  tipo_objeto.descricao as desc_tipo_objeto
                 ,  objeto.descricao as desc_objeto
                 ,  compra_direta.timestamp
        ";

        if ( $this->getDado('julgamento') || $request->get('stAcao') == 'reemitir') {
            $stSql .= "
                 ,  mapa_cot.exercicio_cotacao
                 ,  mapa_cot.cod_cotacao
        ";
        }
  
            $stSql .= "                     
                 , homologadas.homologado

              FROM  compras.compra_direta
        INNER JOIN  compras.mapa
                ON  mapa.cod_mapa = compra_direta.cod_mapa
               AND  mapa.exercicio = compra_direta.exercicio_mapa
        INNER JOIN  orcamento.entidade
                ON  entidade.cod_entidade = compra_direta.cod_entidade
               AND  entidade.exercicio = compra_direta.exercicio_entidade
        INNER JOIN  sw_cgm
                ON  sw_cgm.numcgm = entidade.numcgm
        INNER JOIN  compras.modalidade
                ON  modalidade.cod_modalidade = compra_direta.cod_modalidade
        INNER JOIN  compras.tipo_objeto
                ON  tipo_objeto.cod_tipo_objeto = compra_direta.cod_tipo_objeto
        INNER JOIN  compras.objeto
                ON  objeto.cod_objeto = compra_direta.cod_objeto
        ";
  if ( $this->getDado('julgamento') || $request->get('stAcao') == 'reemitir') {
            $stSql .= "
        INNER JOIN  compras.mapa_cotacao as mapa_cot
                ON  mapa.cod_mapa       = mapa_cot.cod_mapa
               AND  mapa.exercicio      = mapa_cot.exercicio_mapa ";
      }
      
    if ($request->get('inCGMFornecedor') != '') {
        $stSql .= " INNER JOIN (   SELECT exercicio
                                          , ordem
                                          , cod_cotacao
                                          , cgm_fornecedor
                                     FROM compras.julgamento_item
                                    WHERE julgamento_item.ordem = 1
                                 GROUP BY exercicio
                                          , ordem
                                          , cod_cotacao
                                          , cgm_fornecedor ) AS j
                           ON j.cod_cotacao = mapa_cot.cod_cotacao
                          AND j.exercicio   = mapa_cot.exercicio_cotacao
                          AND j.cgm_fornecedor = ".$request->get('inCGMFornecedor');
    }
    if ($request->get('stAcao') == 'publicar') {
        $stSql .= " LEFT JOIN compras.compra_direta_processo
                           ON compra_direta_processo.cod_compra_direta = compra_direta.cod_compra_direta
                          AND compra_direta_processo.cod_entidade = compra_direta.cod_entidade
                          AND compra_direta_processo.cod_modalidade = compra_direta.cod_modalidade
                          AND compra_direta_processo.exercicio_entidade = compra_direta.exercicio_entidade";
    }
        $stSql .= " 
            LEFT JOIN (SELECT  homologacao.cod_compra_direta 
                                       , homologacao.cod_entidade
                                       , homologacao.exercicio_compra_direta
                                       , homologacao.cod_modalidade
                                       , homologacao.homologado
                               FROM compras.homologacao
                        GROUP BY  homologacao.cod_compra_direta 
                                       , homologacao.cod_entidade
                                       , homologacao.exercicio_compra_direta
                                       , homologacao.cod_modalidade
                                       , homologacao.homologado
                            ) AS homologadas
                       ON homologadas.cod_compra_direta = compra_direta.cod_compra_direta
                      AND homologadas.cod_entidade = compra_direta.cod_entidade
                      AND homologadas.exercicio_compra_direta = compra_direta.exercicio_entidade
                      AND homologadas.cod_modalidade = compra_direta.cod_modalidade";
        return $stSql;       
    }

    public function recuperaCompraDiretaAutorizacaoEmpenho(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaCompraDiretaAutorizacaoEmpenho",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaCompraDiretaAutorizacaoEmpenho()
    {
        $stSql = "
            SELECT DISTINCT
                compra_direta.cod_compra_direta
                 ,  compra_direta.timestamp
                 ,  compra_direta.cod_modalidade
                 ,  modalidade.descricao AS modalidade
                 ,  compra_direta.cod_entidade
                 ,  sw_cgm.nom_cgm AS entidade
                 ,  TO_CHAR(compra_direta.timestamp,'dd/mm/yyyy') as data
                 ,  TO_CHAR(compra_direta.dt_entrega_proposta,'dd/mm/yyyy') as dt_entrega
                 ,  TO_CHAR(compra_direta.dt_validade_proposta,'dd/mm/yyyy') as dt_validade
                 ,  compra_direta.condicoes_pagamento
                 ,  compra_direta.prazo_entrega
                 ,  compra_direta.cod_mapa
                 ,  compra_direta.exercicio_mapa
                 ,  compra_direta.cod_tipo_objeto
                 ,  compra_direta.exercicio_entidade
                 ,  tipo_objeto.descricao as tipo_objeto
                 ,  compra_direta.cod_objeto
                 ,  objeto.descricao as objeto
              FROM  compras.compra_direta
        
        INNER JOIN  orcamento.entidade
                ON  entidade.cod_entidade = compra_direta.cod_entidade
               AND  entidade.exercicio    = compra_direta.exercicio_entidade
        
        INNER JOIN  sw_cgm
                ON  sw_cgm.numcgm = entidade.numcgm
        
        INNER JOIN  compras.modalidade
                ON  modalidade.cod_modalidade = compra_direta.cod_modalidade
        
        INNER JOIN  compras.tipo_objeto
                ON  compra_direta.cod_tipo_objeto = tipo_objeto.cod_tipo_objeto
        
        INNER JOIN  compras.objeto
                ON  compra_direta.cod_objeto = objeto.cod_objeto
        
        INNER JOIN  compras.mapa_cotacao
                ON  mapa_cotacao.cod_mapa       = compra_direta.cod_mapa
               AND  mapa_cotacao.exercicio_mapa = compra_direta.exercicio_mapa
        
        INNER JOIN  compras.cotacao
                ON  cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
               AND  cotacao.exercicio   = mapa_cotacao.exercicio_cotacao
               
        INNER JOIN compras.homologacao
                ON homologacao.cod_compra_direta = compra_direta.cod_compra_direta
               AND homologacao.cod_entidade      = compra_direta.cod_entidade
               AND homologacao.cod_modalidade    = compra_direta.cod_modalidade
               AND homologacao.exercicio         = '".$this->getDado('exercicio')."'
             
         WHERE  EXISTS  ( SELECT *
                           FROM compras.julgamento_item
                           
                           JOIN compras.julgamento
                             ON julgamento.exercicio   = julgamento_item.exercicio
                            AND julgamento.cod_cotacao = julgamento_item.cod_cotacao
                           
                           JOIN compras.cotacao
                             ON cotacao.exercicio   = julgamento.exercicio
                            AND cotacao.cod_cotacao = julgamento.cod_cotacao
                           
                           JOIN compras.mapa
                             ON mapa.exercicio = mapa_cotacao.exercicio_mapa
                            AND mapa.cod_mapa  = mapa_cotacao.cod_mapa
                      
                      LEFT JOIN (SELECT item_pre_empenho_julgamento.cod_cotacao
                                      , item_pre_empenho_julgamento.exercicio_julgamento
                                      , item_pre_empenho_julgamento.cod_item
                                      , item_pre_empenho_julgamento.lote
                                   FROM empenho.item_pre_empenho_julgamento
                                ) AS itens_julgamento
                             ON itens_julgamento.exercicio_julgamento = julgamento_item.exercicio
                            AND itens_julgamento.cod_cotacao          = julgamento_item.cod_cotacao
                            AND itens_julgamento.cod_item             = julgamento_item.cod_item
                            AND itens_julgamento.lote                 = julgamento_item.lote
                          
                          WHERE itens_julgamento.cod_cotacao IS NULL
                            AND julgamento_item.cod_cotacao = mapa_cotacao.cod_cotacao
                            AND julgamento_item.exercicio   = mapa_cotacao.exercicio_cotacao
                        )
                        
        AND NOT EXISTS (
                        SELECT  1
                            FROM  compras.compra_direta_anulacao
                            WHERE  compra_direta_anulacao.cod_compra_direta = compra_direta.cod_compra_direta
                            AND  compra_direta_anulacao.cod_entidade = compra_direta.cod_entidade
                            AND  compra_direta_anulacao.exercicio_entidade = compra_direta.exercicio_entidade
                            AND  compra_direta_anulacao.cod_modalidade = compra_direta.cod_modalidade
                   )

        -- Não pode existir uma cotação anulada.
        AND NOT EXISTS (
                        SELECT  1
                            FROM  compras.cotacao_anulada
                            WHERE  cotacao_anulada.cod_cotacao = cotacao.cod_cotacao
                            AND  cotacao_anulada.exercicio   = cotacao.exercicio
                   )
        ";
        if ($this->getDado('cod_compra_direta')) {
            $stSql .= " AND compra_direta.cod_compra_direta = ".$this->getDado('cod_compra_direta')." ";
        }
        if ($this->getDado('cod_entidade')) {
            $stSql .= " AND compra_direta.cod_entidade = ".$this->getDado('cod_entidade')." ";
        }
        if ($this->getDado('exercicio_entidade')) {
            $stSql .= " AND compra_direta.exercicio_entidade = '".$this->getDado('exercicio_entidade')."' ";
        }
        if ($this->getDado('cod_modalidade')) {
            $stSql .= " AND compra_direta.cod_modalidade = ".$this->getDado('cod_modalidade')." ";
        }
        if ($this->getDado('cod_mapa')) {
            $stSql .= " AND compra_direta.cod_mapa = ".$this->getDado('cod_mapa')." ";
        }

        return $stSql;
    }

    public function recuperaCompraDiretaContratoCombo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaCompraDiretaContratoCombo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaCompraDiretaContratoCombo()
    {
        $stSql = "
            SELECT  compra_direta.cod_compra_direta
                 ,  compra_direta.cod_modalidade
                 ,  modalidade.descricao AS modalidade
                 ,  compra_direta.cod_entidade
                 ,  sw_cgm.nom_cgm AS entidade
                 ,  TO_CHAR(compra_direta.timestamp,'dd/mm/yyyy') as data
                 ,  TO_CHAR(compra_direta.dt_entrega_proposta,'dd/mm/yyyy') as dt_entrega
                 ,  TO_CHAR(compra_direta.dt_validade_proposta,'dd/mm/yyyy') as dt_validade
                 ,  compra_direta.condicoes_pagamento
                 ,  compra_direta.prazo_entrega
                 ,  compra_direta.cod_mapa
                 ,  compra_direta.exercicio_mapa
                 ,  compra_direta.cod_tipo_objeto
                 ,  tipo_objeto.descricao as tipo_objeto
                 ,  compra_direta.cod_objeto
                 ,  objeto.descricao as objeto
              FROM  compras.compra_direta
        INNER JOIN  orcamento.entidade
                ON  entidade.cod_entidade = compra_direta.cod_entidade
               AND  entidade.exercicio = compra_direta.exercicio_entidade
        INNER JOIN  sw_cgm
                ON  sw_cgm.numcgm = entidade.numcgm
        INNER JOIN  compras.modalidade
                ON  modalidade.cod_modalidade = compra_direta.cod_modalidade
        INNER JOIN  compras.tipo_objeto
                ON  compra_direta.cod_tipo_objeto = tipo_objeto.cod_tipo_objeto
        INNER JOIN  compras.objeto
                ON  compra_direta.cod_objeto = objeto.cod_objeto
        INNER JOIN  compras.mapa_cotacao
                ON  mapa_cotacao.cod_mapa = compra_direta.cod_mapa
               AND  mapa_cotacao.exercicio_mapa = compra_direta.exercicio_mapa

        INNER JOIN  compras.cotacao
                ON  cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
               AND  cotacao.exercicio   = mapa_cotacao.exercicio_cotacao
       
         LEFT JOIN (SELECT MAX(num_contrato) AS num_contrato
                                      , cod_compra_direta
                                      , exercicio_compra_direta
                                      , exercicio
                                      , cod_entidade
                                      , cod_modalidade 
                                 FROM licitacao.contrato_compra_direta 
                             GROUP BY cod_compra_direta
                                      , exercicio_compra_direta
                                      , exercicio
                                      , cod_entidade
                                      , cod_modalidade) AS max_contrato_compra_direta
              
                          ON max_contrato_compra_direta.cod_compra_direta       = compra_direta.cod_compra_direta
                         AND max_contrato_compra_direta.exercicio_compra_direta = compra_direta.exercicio_entidade
                         AND max_contrato_compra_direta.cod_entidade            = compra_direta.cod_entidade
                         AND max_contrato_compra_direta.cod_modalidade          = compra_direta.cod_modalidade
              
                   LEFT JOIN licitacao.contrato
                          ON contrato.num_contrato = max_contrato_compra_direta.num_contrato
                         AND contrato.exercicio    = max_contrato_compra_direta.exercicio
                         AND contrato.cod_entidade = max_contrato_compra_direta.cod_entidade
              
                   LEFT JOIN licitacao.contrato_anulado
                          ON contrato_anulado.num_contrato = contrato.num_contrato
                         AND contrato_anulado.exercicio    = contrato.exercicio
                         AND contrato_anulado.cod_entidade = contrato.cod_entidade

             WHERE ((contrato.num_contrato IS NULL) OR (contrato.num_contrato IS NOT NULL AND contrato_anulado.num_contrato IS NOT NULL))

                AND EXISTS  (   SELECT  1
                                  FROM  compras.julgamento_item
                                 WHERE  julgamento_item.cod_cotacao = mapa_cotacao.cod_cotacao
                                   AND  julgamento_item.exercicio = mapa_cotacao.exercicio_cotacao
                                   AND  julgamento_item.ordem = 1
                            )

                AND NOT EXISTS (
                                    SELECT  1
                                      FROM  compras.compra_direta_anulacao
                                     WHERE  compra_direta_anulacao.cod_compra_direta = compra_direta.cod_compra_direta
                                       AND  compra_direta_anulacao.cod_entidade = compra_direta.cod_entidade
                                       AND  compra_direta_anulacao.exercicio_entidade = compra_direta.exercicio_entidade
                                       AND  compra_direta_anulacao.cod_modalidade = compra_direta.cod_modalidade
                               )

                -- Não pode existir uma cotação anulada.
                AND NOT EXISTS (
                                    SELECT  1
                                      FROM  compras.cotacao_anulada
                                     WHERE  cotacao_anulada.cod_cotacao = cotacao.cod_cotacao
                                       AND  cotacao_anulada.exercicio   = cotacao.exercicio
                               )
        ";

        if ($this->getDado('cod_compra_direta')) {
            $stSql .= " AND compra_direta.cod_compra_direta = ".$this->getDado('cod_compra_direta')." ";
        }
        if ($this->getDado('cod_entidade')) {
            $stSql .= " AND compra_direta.cod_entidade = ".$this->getDado('cod_entidade')." ";
        }
        if ($this->getDado('exercicio_entidade')) {
            $stSql .= " AND compra_direta.exercicio_entidade = '".$this->getDado('exercicio_entidade')."'";
        }
        if ($this->getDado('cod_modalidade')) {
            $stSql .= " AND compra_direta.cod_modalidade = ".$this->getDado('cod_modalidade')." ";
        }
        if ($this->getDado('cod_mapa')) {
            $stSql .= " AND compra_direta.cod_mapa = ".$this->getDado('cod_mapa')." ";
        }

        $stSql.= " ORDER BY compra_direta.cod_compra_direta ";
        
        return $stSql;
    }

    public function recuperaObjetoCompraDireta(&$rsRecordSet)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if( $this->getDado('cod_compra_direta') != "" )
            $stFiltro  = " AND compra_direta.cod_compra_direta = ".$this->getDado('cod_compra_direta')."   \n";
        if( $this->getDado('cod_modalidade') != "" )
            $stFiltro .= " AND compra_direta.cod_modalidade = ".$this->getDado('cod_modalidade')." \n";
        if( $this->getDado('cod_entidade') != "" )
            $stFiltro .= " AND compra_direta.cod_entidade = ".$this->getDado('cod_entidade')."     \n";
        if( $this->getDado('exercicio_entidade') != "" )
            $stFiltro .= " AND compra_direta.exercicio_entidade = '".$this->getDado('exercicio_entidade')."'  \n";

        $stFiltro = ($stFiltro!="")?" WHERE ".substr($stFiltro,4,strlen($stFiltro)):"";

        $stSql = $this->montaRecuperaObjetoCompraDireta().$stFiltro;

        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql);
    }

    public function montaRecuperaObjetoCompraDireta()
    {
        $stSql  = " select compra_direta.cod_compra_direta                \n";
        $stSql .= "      , compra_direta.cod_modalidade                   \n";
        $stSql .= "      , compra_direta.cod_entidade                     \n";
        $stSql .= "      , compra_direta.exercicio_entidade               \n";
        $stSql .= "      , objeto.cod_objeto                              \n";
        $stSql .= "      , objeto.descricao                               \n";
        $stSql .= "   from compras.compra_direta                          \n";
        $stSql .= "   inner join compras.objeto                           \n";
        $stSql .= "     on objeto.cod_objeto = compra_direta.cod_objeto   \n";

        return $stSql;
    }

    public function recuperaCompraDiretaFornecedores(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaCompraDiretaFornecedores",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaCompraDiretaFornecedores()
    {
        $stSql =" SELECT julgamento_item.cgm_fornecedor                                                                         \n";
        $stSql.="      , sw_cgm.nom_cgm                                                                                         \n";
        $stSql.="   FROM compras.compra_direta                                                                                  \n";

        $stSql.=" INNER JOIN compras.mapa_cotacao                                                                               \n";
        $stSql.="         ON compra_direta.cod_mapa = mapa_cotacao.cod_mapa                                                     \n";
        $stSql.="        AND compra_direta.exercicio_mapa = mapa_cotacao.exercicio_mapa                                         \n";

        $stSql.=" INNER JOIN compras.cotacao                                                                                    \n";
        $stSql.="         ON mapa_cotacao.cod_cotacao = cotacao.cod_cotacao                                                     \n";
        $stSql.="        AND mapa_cotacao.exercicio_cotacao = cotacao.exercicio                                                 \n";
        $stSql.="        AND NOT EXISTS( SELECT 1                                                                               \n";
        $stSql.="                          FROM compras.cotacao_anulada                                                         \n";
        $stSql.="                         WHERE cotacao.cod_cotacao = cotacao_anulada.cod_cotacao                               \n";
        $stSql.="                           AND cotacao.exercicio = cotacao_anulada.exercicio                                   \n";
        $stSql.="                       )                                                                                       \n";

        $stSql.=" INNER JOIN compras.julgamento                                                                                 \n";
        $stSql.="         ON cotacao.cod_cotacao = julgamento.cod_cotacao                                                       \n";
        $stSql.="        AND cotacao.exercicio = julgamento.exercicio                                                           \n";

        $stSql.=" INNER JOIN compras.julgamento_item                                                                            \n";
        $stSql.="         ON julgamento_item.exercicio = julgamento.exercicio                                                   \n";
        $stSql.="        AND julgamento_item.cod_cotacao = julgamento.cod_cotacao                                               \n";

        $stSql.=" INNER JOIN sw_cgm                                                                                             \n";
        $stSql.="         ON sw_cgm.numcgm = julgamento_item.cgm_fornecedor                                                     \n";

        $stSql.="   LEFT JOIN (SELECT MAX(num_contrato) AS num_contrato
                                      , cod_compra_direta
                                      , exercicio_compra_direta
                                      , exercicio
                                      , cod_entidade
                                      , cod_modalidade 
                                 FROM licitacao.contrato_compra_direta 
                             GROUP BY cod_compra_direta
                                      , exercicio_compra_direta
                                      , exercicio
                                      , cod_entidade
                                      , cod_modalidade) AS max_contrato_compra_direta
              
                          ON max_contrato_compra_direta.cod_compra_direta       = compra_direta.cod_compra_direta
                         AND max_contrato_compra_direta.exercicio_compra_direta = compra_direta.exercicio_entidade
                         AND max_contrato_compra_direta.cod_entidade            = compra_direta.cod_entidade
                         AND max_contrato_compra_direta.cod_modalidade          = compra_direta.cod_modalidade
              
                   LEFT JOIN licitacao.contrato
                          ON contrato.num_contrato = max_contrato_compra_direta.num_contrato
                         AND contrato.exercicio    = max_contrato_compra_direta.exercicio
                         AND contrato.cod_entidade = max_contrato_compra_direta.cod_entidade
              
                   LEFT JOIN licitacao.contrato_anulado
                          ON contrato_anulado.num_contrato = contrato.num_contrato
                         AND contrato_anulado.exercicio    = contrato.exercicio
                         AND contrato_anulado.cod_entidade = contrato.cod_entidade";
        
        $stSql.=" WHERE ((contrato.num_contrato IS NULL) OR (contrato.num_contrato IS NOT NULL AND contrato_anulado.num_contrato IS NOT NULL))
        
                    AND NOT EXISTS ( SELECT 1                                                                                   \n";
        $stSql.="                      FROM compras.compra_direta_anulacao                                                      \n";
        $stSql.="                     WHERE compra_direta.cod_compra_direta  = compra_direta_anulacao.cod_compra_direta         \n";
        $stSql.="                       AND compra_direta.cod_entidade       = compra_direta_anulacao.cod_entidade              \n";
        $stSql.="                       AND compra_direta.exercicio_entidade = compra_direta_anulacao.exercicio_entidade        \n";
        $stSql.="                       AND compra_direta.cod_modalidade     = compra_direta_anulacao.cod_modalidade            \n";
        $stSql.="                  )                                                                                            \n";

        if ( $this->getDado( 'cod_compra_direta' ) ) {
            $stSql.= ' AND compra_direta.cod_compra_direta = '.$this->getDado( 'cod_compra_direta' );
        }

        if ( $this->getDado( 'cgm_fornecedor' ) ) {
            $stSql.= ' AND julgamento_item.cgm_fornecedor = '.$this->getDado( 'cgm_fornecedor' );
        }

        if ( $this->getDado( 'cod_modalidade' ) ) {
            $stSql.= ' AND compra_direta.cod_modalidade = '.$this->getDado( 'cod_modalidade' );
        }
        if ( $this->getDado( 'cod_entidade' ) ) {
            $stSql.= ' AND compra_direta.cod_entidade = '.$this->getDado( 'cod_entidade' );
        }
        if ( $this->getDado( 'exercicio_entidade' ) ) {
            $stSql.= " AND compra_direta.exercicio_entidade = '".$this->getDado( 'exercicio_entidade' )."'";
        }
        $stSql.=" AND julgamento_item.ordem = 1";
        $stSql.=" GROUP BY julgamento_item.cgm_fornecedor,sw_cgm.nom_cgm ";

        return $stSql;
    }

    public function recuperaDotacaoOrcamentaria(&$rsRecordSet)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

    $stFiltro .= " WHERE compra_direta.cod_compra_direta  = ".$this->getDado('cod_compra_direta')."
             AND compra_direta.cod_modalidade     = ".$this->getDado('cod_modalidade')."
             AND compra_direta.cod_entidade       = ".$this->getDado('cod_entidade')."
             AND compra_direta.exercicio_entidade = '".$this->getDado('exercicio_entidade')."'";

    $stGrupo  .= " GROUP BY unidade.num_unidade,
                unidade.nom_unidade,
                programa.cod_programa,
                programa.descricao,
                conta_despesa.cod_estrutural,
                conta_despesa.descricao,
                recurso.cod_recurso,
                recurso.cod_fonte,
                recurso.nom_recurso";

        $stSql = $this->montaRecuperaDotacaoOrcamentaria().$stFiltro.$stGrupo;

        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaDotacaoOrcamentaria()
    {
    $stSql = "SELECT unidade.num_unidade,
            unidade.nom_unidade,
            programa.cod_programa,
            programa.descricao,
            conta_despesa.cod_estrutural,
            conta_despesa.descricao as descricao_estrutural,
            recurso.cod_recurso,
            recurso.cod_fonte,
            recurso.nom_recurso

           FROM compras.compra_direta

         INNER JOIN compras.mapa
             ON mapa.cod_mapa  = compra_direta.cod_mapa
            AND mapa.exercicio = compra_direta.exercicio_mapa

         INNER JOIN compras.mapa_item_dotacao
             ON mapa_item_dotacao.cod_mapa  = compra_direta.cod_mapa
            AND mapa_item_dotacao.exercicio = compra_direta.exercicio_mapa

         INNER JOIN orcamento.despesa
             ON despesa.cod_despesa = mapa_item_dotacao.cod_despesa
            AND despesa.exercicio   = mapa_item_dotacao.exercicio

         INNER JOIN orcamento.unidade
             ON unidade.num_unidade = despesa.num_unidade
            AND unidade.num_orgao   = despesa.num_orgao
            AND unidade.exercicio   = despesa.exercicio

         INNER JOIN orcamento.programa
             ON programa.cod_programa = despesa.cod_programa
            AND programa.exercicio    = despesa.exercicio

         INNER JOIN orcamento.conta_despesa
             ON conta_despesa.cod_conta = despesa.cod_conta
            AND conta_despesa.exercicio = despesa.exercicio

         INNER JOIN orcamento.recurso
             ON recurso.cod_recurso = despesa.cod_recurso
            AND recurso.exercicio   = despesa.exercicio
    ";

    return $stSql;
    }
     public function recuperaCompraDiretaAutorizacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaCompraDiretaAutorizacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaCompraDiretaAutorizacao()
    {
        $stSql = "
            SELECT DISTINCT
                        compra_direta.cod_compra_direta
                     ,  compra_direta.timestamp
                     ,  compra_direta.cod_modalidade
                     ,  modalidade.descricao AS modalidade
                     ,  compra_direta.cod_entidade
                      , compra_direta.cod_entidade::varchar||' - '|| entidade_nom_cgm.nom_cgm as entidade 
                     ,  TO_CHAR(compra_direta.timestamp,'dd/mm/yyyy') as data
                     ,  TO_CHAR(compra_direta.dt_entrega_proposta,'dd/mm/yyyy') as dt_entrega
                     ,  TO_CHAR(compra_direta.dt_validade_proposta,'dd/mm/yyyy') as dt_validade
                     ,  compra_direta.condicoes_pagamento
                     ,  compra_direta.prazo_entrega
                     ,  compra_direta.cod_mapa ||'/'|| compra_direta.exercicio_mapa as mapa
                     ,  compra_direta.exercicio_mapa
                     ,  compra_direta.cod_tipo_objeto
                     ,  compra_direta.exercicio_entidade
                     ,  tipo_objeto.descricao as tipo_objeto
                     ,  compra_direta.cod_objeto
                     ,  objeto.descricao as objeto
                     , item_pre_empenho.quantidade
                     , item_pre_empenho.vl_total
                     , item_pre_empenho.vl_total/item_pre_empenho.quantidade as vl_unitario
                     , catalogo_item.cod_item
                     , catalogo_item.descricao         
                     , autorizacao_empenho.cod_autorizacao||'/'|| compra_direta.exercicio_entidade as autorizacao
                     , item_pre_empenho_julgamento.cgm_fornecedor||' - '|| sw_cgm.nom_cgm as fornecedor
                     , autorizacao_empenho.cod_autorizacao
                     , to_char(homologacao.timestamp::date, 'dd/mm/yyyy') as dt_homologacao                      
                     , homologacao.homologado
              FROM  compras.compra_direta
        
      INNER JOIN  compras.modalidade
                  ON  modalidade.cod_modalidade = compra_direta.cod_modalidade
                  
     INNER JOIN compras.homologacao
                  ON homologacao.cod_compra_direta = compra_direta.cod_compra_direta
                AND homologacao.cod_entidade      = compra_direta.cod_entidade
                AND homologacao.cod_modalidade    = compra_direta.cod_modalidade             
      
     INNER JOIN  compras.tipo_objeto
                 ON  compra_direta.cod_tipo_objeto = tipo_objeto.cod_tipo_objeto
        
     INNER JOIN  compras.objeto
                 ON  compra_direta.cod_objeto = objeto.cod_objeto
        
     INNER JOIN  compras.mapa_cotacao
                 ON  mapa_cotacao.cod_mapa       = compra_direta.cod_mapa
               AND  mapa_cotacao.exercicio_mapa = compra_direta.exercicio_mapa
        
     INNER JOIN  compras.cotacao
                 ON  cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
               AND  cotacao.exercicio   = mapa_cotacao.exercicio_cotacao               

     INNER JOIN compras.julgamento
                 ON julgamento.exercicio = cotacao.exercicio
               AND julgamento.cod_cotacao = cotacao.cod_cotacao                  

     INNER JOIN compras.julgamento_item  
                 ON julgamento_item.cod_cotacao = julgamento.cod_cotacao 
               AND julgamento_item.exercicio = julgamento.exercicio 
                                

      LEFT JOIN empenho.item_pre_empenho_julgamento  
                  ON item_pre_empenho_julgamento.exercicio_julgamento = julgamento_item.exercicio  
                AND item_pre_empenho_julgamento.cod_cotacao = julgamento_item.cod_cotacao  
                AND item_pre_empenho_julgamento.cod_item = julgamento_item.cod_item  
                AND item_pre_empenho_julgamento.lote = julgamento_item.lote  
                AND item_pre_empenho_julgamento.cgm_fornecedor = julgamento_item.cgm_fornecedor
                
       LEFT JOIN sw_cgm
                   ON sw_cgm.numcgm = item_pre_empenho_julgamento.cgm_fornecedor  

      LEFT JOIN empenho.item_pre_empenho
                  ON item_pre_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho 
                AND item_pre_empenho.exercicio = item_pre_empenho_julgamento.exercicio
                AND item_pre_empenho.num_item = item_pre_empenho_julgamento.num_item
                          
      LEFT JOIN almoxarifado.catalogo_item
                  ON catalogo_item.cod_item = julgamento_item.cod_item

      LEFT JOIN empenho.pre_empenho
                  ON pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                AND pre_empenho.exercicio = item_pre_empenho.exercicio

      LEFT JOIN empenho.autorizacao_empenho  
                  ON autorizacao_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                AND autorizacao_empenho.exercicio = pre_empenho.exercicio
                                                 
      
        LEFT JOIN (
                                SELECT compra_direta.cod_compra_direta
                                          , compra_direta.cod_entidade
                                          , compra_direta.exercicio_entidade
                                          , compra_direta.cod_modalidade                                        
                                          , cgm.nom_cgm
                                  FROM  compras.compra_direta        
                          INNER JOIN  orcamento.entidade
                                      ON  entidade.cod_entidade = compra_direta.cod_entidade
                                    AND  entidade.exercicio    = compra_direta.exercicio_entidade
                          INNER JOIN sw_cgm as cgm
                                      ON cgm.numcgm = entidade.numcgm        
                          GROUP BY compra_direta.cod_compra_direta
                                          , compra_direta.cod_entidade
                                          , compra_direta.exercicio_entidade
                                          , compra_direta.cod_modalidade                                        
                                          , cgm.nom_cgm
                                 ) as entidade_nom_cgm
                             ON entidade_nom_cgm.cod_compra_direta = compra_direta.cod_compra_direta 
                           AND entidade_nom_cgm.cod_entidade = compra_direta.cod_entidade
                           AND entidade_nom_cgm.exercicio_entidade = compra_direta.exercicio_entidade
                           AND entidade_nom_cgm.cod_modalidade = compra_direta.cod_modalidade
                           
            WHERE                       
                    compra_direta.cod_compra_direta = ".$this->getDado('cod_compra_direta')." 
                    AND compra_direta.cod_entidade = ".$this->getDado('cod_entidade')." 
                    AND compra_direta.cod_modalidade = ".$this->getDado('cod_modalidade')." 
                    AND compra_direta.cod_mapa = ".$this->getDado('cod_mapa')." 
                    AND compra_direta.exercicio_mapa  = '".$this->getDado('exercicio_mapa')."'  ";
        

        return $stSql;
    }
    
    public function recuperaCompraDiretaAutorizacaoEmpenhoItens(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaCompraDiretaAutorizacaoEmpenhoItens",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaCompraDiretaAutorizacaoEmpenhoItens()
    {
        $stSql = "
            SELECT DISTINCT
                        compra_direta.cod_compra_direta
                     ,  compra_direta.timestamp
                     ,  compra_direta.cod_modalidade
                     ,  modalidade.descricao AS modalidade
                     ,  compra_direta.cod_entidade
                      , compra_direta.cod_entidade::varchar||' - '|| entidade_nom_cgm.nom_cgm as entidade 
                     ,  TO_CHAR(compra_direta.timestamp,'dd/mm/yyyy') as data
                     ,  TO_CHAR(compra_direta.dt_entrega_proposta,'dd/mm/yyyy') as dt_entrega
                     ,  TO_CHAR(compra_direta.dt_validade_proposta,'dd/mm/yyyy') as dt_validade
                     ,  compra_direta.condicoes_pagamento
                     ,  compra_direta.prazo_entrega
                     ,  compra_direta.cod_mapa ||'/'|| compra_direta.exercicio_mapa as mapa
                     ,  compra_direta.exercicio_mapa
                     ,  compra_direta.cod_tipo_objeto
                     ,  compra_direta.exercicio_entidade
                     ,  tipo_objeto.descricao as tipo_objeto
                     ,  compra_direta.cod_objeto
                     ,  objeto.descricao as objeto
                     , item_pre_empenho.quantidade
                     , item_pre_empenho.vl_total
                     , item_pre_empenho.vl_total/item_pre_empenho.quantidade as vl_unitario
                     , catalogo_item.cod_item
                     , catalogo_item.descricao         
                     , autorizacao_empenho.cod_autorizacao||'/'|| compra_direta.exercicio_entidade as autorizacao
                     , item_pre_empenho_julgamento.cgm_fornecedor||' - '|| sw_cgm.nom_cgm as fornecedor
                     , autorizacao_empenho.cod_autorizacao
                     , to_char(homologacao.timestamp::date, 'dd/mm/yyyy') as dt_homologacao 
                     , item_pre_empenho.num_item
              FROM  compras.compra_direta
        
      INNER JOIN  compras.modalidade
                  ON  modalidade.cod_modalidade = compra_direta.cod_modalidade

     INNER JOIN  compras.tipo_objeto
                 ON  compra_direta.cod_tipo_objeto = tipo_objeto.cod_tipo_objeto
        
     INNER JOIN  compras.objeto
                 ON  compra_direta.cod_objeto = objeto.cod_objeto
        
     INNER JOIN  compras.mapa_cotacao
                 ON  mapa_cotacao.cod_mapa       = compra_direta.cod_mapa
               AND  mapa_cotacao.exercicio_mapa = compra_direta.exercicio_mapa
        
     INNER JOIN  compras.cotacao
                 ON  cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
               AND  cotacao.exercicio   = mapa_cotacao.exercicio_cotacao               

     INNER JOIN compras.julgamento
                 ON julgamento.exercicio = cotacao.exercicio
               AND julgamento.cod_cotacao = cotacao.cod_cotacao                  

     INNER JOIN compras.julgamento_item  
                 ON julgamento_item.cod_cotacao = julgamento.cod_cotacao 
               AND julgamento_item.exercicio = julgamento.exercicio 
                                

      INNER JOIN empenho.item_pre_empenho_julgamento  
                  ON item_pre_empenho_julgamento.exercicio_julgamento = julgamento_item.exercicio  
                AND item_pre_empenho_julgamento.cod_cotacao = julgamento_item.cod_cotacao  
                AND item_pre_empenho_julgamento.cod_item = julgamento_item.cod_item  
                AND item_pre_empenho_julgamento.lote = julgamento_item.lote  
                AND item_pre_empenho_julgamento.cgm_fornecedor = julgamento_item.cgm_fornecedor
                
       INNER JOIN sw_cgm
                   ON sw_cgm.numcgm = item_pre_empenho_julgamento.cgm_fornecedor  

      INNER JOIN empenho.item_pre_empenho
                  ON item_pre_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho 
                AND item_pre_empenho.exercicio = item_pre_empenho_julgamento.exercicio
                AND item_pre_empenho.num_item = item_pre_empenho_julgamento.num_item
                          
      INNER JOIN almoxarifado.catalogo_item
                  ON catalogo_item.cod_item = julgamento_item.cod_item

      INNER JOIN empenho.pre_empenho
                  ON pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                AND pre_empenho.exercicio = item_pre_empenho.exercicio

      INNER JOIN empenho.autorizacao_empenho  
                  ON autorizacao_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                AND autorizacao_empenho.exercicio = pre_empenho.exercicio
                                                 
      INNER JOIN compras.homologacao
                  ON homologacao.cod_compra_direta = compra_direta.cod_compra_direta
                AND homologacao.cod_entidade      = compra_direta.cod_entidade
                AND homologacao.cod_modalidade    = compra_direta.cod_modalidade
                
      
        INNER JOIN (
                                SELECT compra_direta.cod_compra_direta
                                          , compra_direta.cod_entidade
                                          , compra_direta.exercicio_entidade
                                          , compra_direta.cod_modalidade                                        
                                          , cgm.nom_cgm
                                  FROM  compras.compra_direta        
                          INNER JOIN  orcamento.entidade
                                      ON  entidade.cod_entidade = compra_direta.cod_entidade
                                    AND  entidade.exercicio    = compra_direta.exercicio_entidade
                          INNER JOIN sw_cgm as cgm
                                      ON cgm.numcgm = entidade.numcgm        
                          GROUP BY compra_direta.cod_compra_direta
                                          , compra_direta.cod_entidade
                                          , compra_direta.exercicio_entidade
                                          , compra_direta.cod_modalidade                                        
                                          , cgm.nom_cgm
                                 ) as entidade_nom_cgm
                             ON entidade_nom_cgm.cod_compra_direta = compra_direta.cod_compra_direta 
                           AND entidade_nom_cgm.cod_entidade = compra_direta.cod_entidade
                           AND entidade_nom_cgm.exercicio_entidade = compra_direta.exercicio_entidade
                           AND entidade_nom_cgm.cod_modalidade = compra_direta.cod_modalidade
                           
            WHERE                       
                     NOT EXISTS (
                                       SELECT  1
                                          FROM  compras.compra_direta_anulacao
                                        WHERE  compra_direta_anulacao.cod_compra_direta = compra_direta.cod_compra_direta
                                            AND  compra_direta_anulacao.cod_entidade = compra_direta.cod_entidade
                                            AND  compra_direta_anulacao.exercicio_entidade = compra_direta.exercicio_entidade
                                            AND  compra_direta_anulacao.cod_modalidade = compra_direta.cod_modalidade
                                            )

                     -- Não pode existir uma cotação anulada.
                     AND NOT EXISTS (
                                      SELECT  1
                                         FROM  compras.cotacao_anulada
                                       WHERE  cotacao_anulada.cod_cotacao = cotacao.cod_cotacao
                                            AND  cotacao_anulada.exercicio   = cotacao.exercicio
                                )
       
                    AND compra_direta.cod_compra_direta = ".$this->getDado('cod_compra_direta')." 
                    AND compra_direta.cod_entidade = ".$this->getDado('cod_entidade')." 
                    AND compra_direta.cod_modalidade = ".$this->getDado('cod_modalidade')." 
                    AND compra_direta.cod_mapa = ".$this->getDado('cod_mapa')." 
                    AND compra_direta.exercicio_mapa  = '".$this->getDado('exercicio_mapa')."'
        ORDER BY autorizacao_empenho.cod_autorizacao, item_pre_empenho.num_item ";

        return $stSql;
    }
    
     public function recuperaStatusCompraDireta(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaStatusCompraDireta",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaStatusCompraDireta()
    {
        $stSql = "
            SELECT DISTINCT
                        compra_direta.cod_compra_direta
                     ,  compra_direta.timestamp
                     ,  compra_direta.cod_modalidade
                     ,  compra_direta.cod_entidade
                      , compra_direta.cod_entidade
                     ,  compra_direta.condicoes_pagamento
                     ,  compra_direta.prazo_entrega
                     ,  compra_direta.cod_mapa ||'/'|| compra_direta.exercicio_mapa as mapa
                     ,  compra_direta.exercicio_mapa
                     ,  compra_direta.cod_tipo_objeto
                     ,  compra_direta.exercicio_entidade
                     , CASE WHEN compra_direta_anulacao.cod_compra_direta IS NULL THEN
                        'Ativa'
                       ELSE
                        'Anulada'
                       END as status
              FROM  compras.compra_direta

        LEFT JOIN  compras.compra_direta_anulacao
                  ON  compra_direta_anulacao.cod_compra_direta = compra_direta.cod_compra_direta
                AND  compra_direta_anulacao.cod_entidade = compra_direta.cod_entidade
                AND  compra_direta_anulacao.exercicio_entidade = compra_direta.exercicio_entidade
                AND  compra_direta_anulacao.cod_modalidade = compra_direta.cod_modalidade

            WHERE compra_direta.cod_compra_direta = ".$this->getDado('cod_compra_direta')."
                AND compra_direta.cod_entidade = ".$this->getDado('cod_entidade')."
                AND compra_direta.cod_modalidade = ".$this->getDado('cod_modalidade')."
                AND compra_direta.cod_mapa = ".$this->getDado('cod_mapa')." 
                AND compra_direta.exercicio_mapa  = '".$this->getDado('exercicio_mapa')."'  ";

        return $stSql;
    }

    public function recuperaDataCompraDireta(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDataCompraDireta",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDataCompraDireta()
    {
        $stSql = "
                SELECT compra_direta.cod_compra_direta
                     , compra_direta.timestamp
                     , compra_direta.cod_modalidade
                     , compra_direta.cod_entidade
                     , compra_direta.cod_entidade
                     , compra_direta.condicoes_pagamento
                     , compra_direta.prazo_entrega
                     , compra_direta.cod_mapa ||'/'|| compra_direta.exercicio_mapa as mapa
                     , compra_direta.exercicio_mapa
                     , compra_direta.cod_tipo_objeto
                     , compra_direta.exercicio_entidade
                     , TO_CHAR(compra_direta.timestamp,'dd/mm/yyyy') AS data
                  FROM compras.compra_direta
                 WHERE compra_direta.exercicio_entidade = '".$this->getDado('exercicio_entidade')."' \n";

        if ( $this->getDado('cod_entidade') )
            $stSql.= " AND compra_direta.cod_entidade = ".$this->getDado('cod_entidade')." \n";

        if ( $this->getDado('cod_compra_direta') )
            $stSql.= " AND compra_direta.cod_compra_direta = ".$this->getDado('cod_compra_direta')." \n";

        if ( $this->getDado('cod_modalidade') )
            $stSql.= " AND compra_direta.cod_modalidade = ".$this->getDado('cod_modalidade')." \n";

        return $stSql;
    }
    
    public function __destruct() {}
}
