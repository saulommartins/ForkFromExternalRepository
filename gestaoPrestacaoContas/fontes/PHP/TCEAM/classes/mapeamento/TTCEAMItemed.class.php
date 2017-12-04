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
    * Extensão da Classe de Mapeamento
    * Data de Criação: 31/03/2011
    *
    *
    * @author: Eduardo Paculski Schitz
    *
    * @package URBEM
    *
*/

class TTCEAMItemed extends Persistente
{
    /**
        * M�todo Construtor
        * @access Private
    */
    public function TTCEAMItemed()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }

    public function montaRecuperaTodos()
    {
        $stSql  = " SELECT 0 AS reservado_tc
                 , 'E' AS tipo_valor
                 , processo_licitatorio
                 , tipo_pessoa
                 , cpf_cnpj
                 , total_cotado_item
                 , vencedor_perdedor
                 , quantidade_item
                 , controle_item
                 , cod_item
              FROM (
                SELECT CASE WHEN licitacao.cod_modalidade = 1  AND licitacao_anulada.cod_licitacao IS NULL THEN 'CC'||licitacao.cod_licitacao::varchar||'-'||licitacao.exercicio::varchar
                            WHEN licitacao.cod_modalidade = 2  AND licitacao_anulada.cod_licitacao IS NULL THEN 'TP'||licitacao.cod_licitacao::varchar||'-'||licitacao.exercicio::varchar
                            WHEN licitacao.cod_modalidade = 3  AND licitacao_anulada.cod_licitacao IS NULL THEN 'CO'||licitacao.cod_licitacao::varchar||'-'||licitacao.exercicio::varchar
                            WHEN licitacao.cod_modalidade = 4  AND licitacao_anulada.cod_licitacao IS NULL THEN 'LE'||licitacao.cod_licitacao::varchar||'-'||licitacao.exercicio::varchar
                            WHEN licitacao.cod_modalidade = 5  AND licitacao_anulada.cod_licitacao IS NULL THEN 'CP'||licitacao.cod_licitacao::varchar||'-'||licitacao.exercicio::varchar
                            WHEN licitacao.cod_modalidade = 6  AND licitacao_anulada.cod_licitacao IS NULL THEN 'PR'||licitacao.cod_licitacao::varchar||'-'||licitacao.exercicio::varchar
                            WHEN licitacao.cod_modalidade = 7  AND licitacao_anulada.cod_licitacao IS NULL THEN 'PE'||licitacao.cod_licitacao::varchar||'-'||licitacao.exercicio::varchar
                            WHEN licitacao.cod_modalidade = 8  AND licitacao_anulada.cod_licitacao IS NULL THEN 'DL'||licitacao.cod_licitacao::varchar||'-'||licitacao.exercicio::varchar
                            WHEN licitacao.cod_modalidade = 9  AND licitacao_anulada.cod_licitacao IS NULL THEN 'IL'||licitacao.cod_licitacao::varchar||'-'||licitacao.exercicio::varchar
                            WHEN licitacao.cod_modalidade = 10 AND licitacao_anulada.cod_licitacao IS NULL THEN 'OT'||licitacao.cod_licitacao::varchar||'-'||licitacao.exercicio::varchar
                            WHEN licitacao.cod_modalidade = 11 AND licitacao_anulada.cod_licitacao IS NULL THEN 'RP'||licitacao.cod_licitacao::varchar||'-'||licitacao.exercicio::varchar
                            WHEN licitacao_anulada.cod_licitacao IS NOT NULL THEN 'LA'||licitacao.cod_licitacao::varchar||'-'||licitacao.exercicio::varchar
                       END AS processo_licitatorio
                     , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                                1
                            ELSE
                                2
                       END AS tipo_pessoa
                     , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                                sw_cgm_pessoa_fisica.cpf
                            ELSE
                                sw_cgm_pessoa_juridica.cnpj
                       END AS cpf_cnpj
                     , CASE WHEN licitacao_anulada.cod_licitacao IS NOT NULL THEN
                            0.00
                            ELSE
                            cotacao_fornecedor_item.vl_cotacao
                       END AS total_cotado_item
                     , CASE WHEN julgamento_item.ordem = 1 AND licitacao_anulada.cod_licitacao IS NULL THEN
                                'V'
                            ELSE
                                'P'
                       END AS vencedor_perdedor
                       , to_number(to_char(sum(cotacao_item.quantidade),'9999999999.99999'),'9999999999.99999') AS quantidade_item
                       , LPAD(mapa_item.cod_item::varchar,8,'0')||LPAD(mapa_item.lote::varchar,2,'0') AS controle_item
                       , catalogo_item.descricao
                       , mapa_item.cod_item
                  FROM compras.mapa_item
                  JOIN almoxarifado.catalogo_item
                    ON catalogo_item.cod_item = mapa_item.cod_item
             LEFT JOIN compras.mapa_cotacao
                    ON mapa_cotacao.cod_mapa       = mapa_item.cod_mapa
                   AND mapa_cotacao.exercicio_mapa = mapa_item.exercicio
             LEFT JOIN compras.cotacao
                    ON cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
                   AND cotacao.exercicio   = mapa_cotacao.exercicio_cotacao
             LEFT JOIN compras.cotacao_item
                    ON cotacao_item.cod_cotacao = cotacao.cod_cotacao
                   AND cotacao_item.exercicio   = cotacao.exercicio
                   AND cotacao_item.cod_item    = mapa_item.cod_item
             LEFT JOIN compras.cotacao_fornecedor_item
                    ON cotacao_fornecedor_item.cod_cotacao    = cotacao_item.cod_cotacao
                   AND cotacao_fornecedor_item.exercicio      = cotacao_item.exercicio
                   AND cotacao_fornecedor_item.cod_item       = cotacao_item.cod_item
                   AND cotacao_fornecedor_item.lote           = cotacao_item.lote
             LEFT JOIN compras.julgamento_item
                    ON julgamento_item.exercicio = cotacao_fornecedor_item.exercicio
                   AND julgamento_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                   AND julgamento_item.cod_item = cotacao_fornecedor_item.cod_item
                   AND julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                   AND julgamento_item.lote = cotacao_fornecedor_item.lote
                  JOIN licitacao.licitacao
                    ON licitacao.cod_mapa       = mapa_item.cod_mapa
                   AND licitacao.exercicio_mapa = mapa_item.exercicio
             LEFT JOIN licitacao.licitacao_anulada
                    ON licitacao.cod_licitacao  = licitacao_anulada.cod_licitacao
                   AND licitacao.cod_modalidade = licitacao_anulada.cod_modalidade
                   AND licitacao.cod_entidade   = licitacao_anulada.cod_entidade
                   AND licitacao.exercicio      = licitacao_anulada.exercicio
             LEFT JOIN licitacao.cotacao_licitacao
                    ON cotacao_licitacao.cod_item = cotacao_fornecedor_item.cod_item
                   AND cotacao_licitacao.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                   AND cotacao_licitacao.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                   AND cotacao_licitacao.exercicio_cotacao = cotacao_fornecedor_item.exercicio
                   AND cotacao_licitacao.lote = cotacao_fornecedor_item.lote
             LEFT JOIN licitacao.adjudicacao
                    ON adjudicacao.cod_licitacao = cotacao_licitacao.cod_licitacao
                   AND adjudicacao.cod_modalidade = cotacao_licitacao.cod_modalidade
                   AND adjudicacao.cod_entidade = cotacao_licitacao.cod_entidade
                   AND adjudicacao.exercicio_licitacao = cotacao_licitacao.exercicio_licitacao
                   AND adjudicacao.lote = cotacao_licitacao.lote
                   AND adjudicacao.cod_cotacao = cotacao_licitacao.cod_cotacao
                   AND adjudicacao.cod_item = cotacao_licitacao.cod_item
                   AND adjudicacao.exercicio_cotacao = cotacao_licitacao.exercicio_cotacao
                   AND adjudicacao.cgm_fornecedor = cotacao_licitacao.cgm_fornecedor
             LEFT JOIN licitacao.homologacao
                    ON homologacao.num_adjudicacao = adjudicacao.num_adjudicacao
                   AND homologacao.cod_entidade = adjudicacao.cod_entidade
                   AND homologacao.cod_modalidade = adjudicacao.cod_modalidade
                   AND homologacao.cod_licitacao = adjudicacao.cod_licitacao
                   AND homologacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                   AND homologacao.cod_item = adjudicacao.cod_item
                   AND homologacao.cod_cotacao = adjudicacao.cod_cotacao
                   AND homologacao.lote = adjudicacao.lote
                   AND homologacao.exercicio_cotacao = adjudicacao.exercicio_cotacao
                   AND homologacao.cgm_fornecedor = adjudicacao.cgm_fornecedor
             LEFT JOIN sw_cgm_pessoa_fisica
                    ON sw_cgm_pessoa_fisica.numcgm = julgamento_item.cgm_fornecedor
             LEFT JOIN sw_cgm_pessoa_juridica
                    ON sw_cgm_pessoa_juridica.numcgm = julgamento_item.cgm_fornecedor
                 WHERE mapa_item.exercicio = '".$this->getDado('exercicio')."'
                   AND to_char(licitacao.timestamp,'mm') = '".$this->getDado('mes')."'
                   AND licitacao.cod_entidade IN (".$this->getDado('cod_entidade').")
              GROUP BY sw_cgm_pessoa_fisica.numcgm
                     , sw_cgm_pessoa_fisica.cpf
                     , sw_cgm_pessoa_juridica.cnpj
                     , cotacao_fornecedor_item.vl_cotacao
                     , julgamento_item.ordem
                     , licitacao.cod_modalidade
                     , licitacao.cod_licitacao
                     , licitacao.exercicio
                     , licitacao_anulada.cod_licitacao
                     , mapa_item.cod_item
                     , mapa_item.lote
                     , licitacao.cod_entidade
                     , catalogo_item.descricao
                     , cotacao.cod_cotacao

             UNION ALL

                SELECT CASE WHEN compra_direta.cod_modalidade = 1 THEN 'CC'||compra_direta.cod_compra_direta::varchar||'-'||compra_direta.exercicio_entidade
                            WHEN compra_direta.cod_modalidade = 2 THEN 'TP'||compra_direta.cod_compra_direta::varchar||'-'||compra_direta.exercicio_entidade
                            WHEN compra_direta.cod_modalidade = 3 THEN 'CO'||compra_direta.cod_compra_direta::varchar||'-'||compra_direta.exercicio_entidade
                            WHEN compra_direta.cod_modalidade = 4 THEN 'LE'||compra_direta.cod_compra_direta::varchar||'-'||compra_direta.exercicio_entidade
                            WHEN compra_direta.cod_modalidade = 5 THEN 'CP'||compra_direta.cod_compra_direta::varchar||'-'||compra_direta.exercicio_entidade
                            WHEN compra_direta.cod_modalidade = 6 THEN 'PR'||compra_direta.cod_compra_direta::varchar||'-'||compra_direta.exercicio_entidade
                            WHEN compra_direta.cod_modalidade = 7 THEN 'PE'||compra_direta.cod_compra_direta::varchar||'-'||compra_direta.exercicio_entidade
                            WHEN compra_direta.cod_modalidade = 8 THEN 'DL'||compra_direta.cod_compra_direta::varchar||'-'||compra_direta.exercicio_entidade
                            WHEN compra_direta.cod_modalidade = 9 THEN 'IL'||compra_direta.cod_compra_direta::varchar||'-'||compra_direta.exercicio_entidade
                       END AS processo_licitatorio
                     , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                                1
                            ELSE
                                2
                       END AS tipo_pessoa
                     , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                                sw_cgm_pessoa_fisica.cpf
                            ELSE
                                sw_cgm_pessoa_juridica.cnpj
                       END AS cpf_cnpj
                     , cotacao_fornecedor_item.vl_cotacao AS total_cotado_item
                     , CASE WHEN julgamento_item.ordem = 1 THEN
                                'V'
                            ELSE
                                'P'
                       END AS vencedor_perdedor
                     , to_number(to_char(sum(cotacao_item.quantidade),'9999999999.99999'),'9999999999.99999') AS quantidade_item
                     , LPAD(mapa_item.cod_item::varchar,8,'0')||LPAD(mapa_item.lote::varchar,2,'0') AS controle_item
                     , catalogo_item.descricao
                     , mapa_item.cod_item
                  FROM compras.mapa_item
                  JOIN almoxarifado.catalogo_item
                    ON catalogo_item.cod_item = mapa_item.cod_item
             LEFT JOIN compras.mapa_cotacao
                    ON mapa_cotacao.cod_mapa       = mapa_item.cod_mapa
                   AND mapa_cotacao.exercicio_mapa = mapa_item.exercicio
             LEFT JOIN compras.cotacao
                    ON cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
                   AND cotacao.exercicio   = mapa_cotacao.exercicio_cotacao
             LEFT JOIN compras.cotacao_item
                    ON cotacao_item.cod_cotacao = cotacao.cod_cotacao
                   AND cotacao_item.exercicio   = cotacao.exercicio
                   AND cotacao_item.cod_item    = mapa_item.cod_item
             LEFT JOIN compras.cotacao_fornecedor_item
                    ON cotacao_fornecedor_item.cod_cotacao    = cotacao_item.cod_cotacao
                   AND cotacao_fornecedor_item.exercicio      = cotacao_item.exercicio
                   AND cotacao_fornecedor_item.cod_item       = cotacao_item.cod_item
                   AND cotacao_fornecedor_item.lote           = cotacao_item.lote
             LEFT JOIN compras.julgamento_item
                    ON julgamento_item.exercicio = cotacao_fornecedor_item.exercicio
                   AND julgamento_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                   AND julgamento_item.cod_item = cotacao_fornecedor_item.cod_item
                   AND julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                   AND julgamento_item.lote = cotacao_fornecedor_item.lote
                  JOIN compras.compra_direta
                    ON compra_direta.cod_mapa       = mapa_item.cod_mapa
                   AND compra_direta.exercicio_mapa = mapa_item.exercicio
             LEFT JOIN compras.compra_direta_anulacao
                    ON compra_direta.cod_compra_direta  = compra_direta_anulacao.cod_compra_direta
                   AND compra_direta.cod_modalidade     = compra_direta_anulacao.cod_modalidade
                   AND compra_direta.cod_entidade       = compra_direta_anulacao.cod_entidade
                   AND compra_direta.exercicio_entidade = compra_direta_anulacao.exercicio_entidade
             LEFT JOIN empenho.item_pre_empenho_julgamento
                    ON item_pre_empenho_julgamento.exercicio_julgamento = julgamento_item.exercicio
                   AND item_pre_empenho_julgamento.cod_cotacao = julgamento_item.cod_cotacao
                   AND item_pre_empenho_julgamento.cod_item = julgamento_item.cod_item
                   AND item_pre_empenho_julgamento.lote = julgamento_item.lote
                   AND item_pre_empenho_julgamento.cgm_fornecedor = julgamento_item.cgm_fornecedor
             LEFT JOIN sw_cgm_pessoa_fisica
                    ON sw_cgm_pessoa_fisica.numcgm = julgamento_item.cgm_fornecedor
             LEFT JOIN sw_cgm_pessoa_juridica
                    ON sw_cgm_pessoa_juridica.numcgm = julgamento_item.cgm_fornecedor
                 WHERE mapa_item.exercicio = '".$this->getDado('exercicio')."'
                   AND to_char(compra_direta.timestamp,'mm') = '".$this->getDado('mes')."'
                   AND compra_direta.cod_entidade IN (".$this->getDado('cod_entidade').")
              GROUP BY sw_cgm_pessoa_fisica.numcgm
                     , sw_cgm_pessoa_fisica.cpf
                     , sw_cgm_pessoa_juridica.cnpj
                     , cotacao_fornecedor_item.vl_cotacao
                     , julgamento_item.ordem
                     , compra_direta.cod_modalidade
                     , compra_direta.cod_compra_direta
                     , compra_direta.exercicio_entidade
                     , compra_direta_anulacao.cod_compra_direta
                     , mapa_item.cod_item
                     , mapa_item.lote
                     , compra_direta.cod_entidade
                     , catalogo_item.descricao
                     , cotacao.cod_cotacao
                ) AS registros
         ORDER BY registros.processo_licitatorio
                , cod_item

        ";

        return $stSql;
    }
}
?>
