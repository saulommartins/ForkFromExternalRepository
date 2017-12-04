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
    * Extensão da Classe de Mapeamento TTCEAMCotacao
    *
    * Data de Criação: 28/04/2014
    *
    * @author: Michel Teixeira
    *
    $Id: TTCEAMCotacao.class.php 59612 2014-09-02 12:00:51Z gelson $
    *
    * @ignore
    *
*/
class TTCEAMCotacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEAMCotacao()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaCotacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaCotacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaCotacao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaCotacao()
    {
        $stSql  = "
                    SELECT  Tipo
                    , tipo_valor
                    , processo_licitatorio
                    , tipo_pessoa
                    , cic_participante
                    , vl_cotacao
                    , situacao
                    , quantidade
                    , cod_item
               FROM (
                       SELECT 'LT' AS Tipo,
                               'E' AS tipo_valor,
                               CASE WHEN licitacao.cod_modalidade = 1  THEN 'CC'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                    WHEN licitacao.cod_modalidade = 2  THEN 'TP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                    WHEN licitacao.cod_modalidade = 3  THEN 'CO'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                    WHEN licitacao.cod_modalidade = 4  THEN 'LE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                    WHEN licitacao.cod_modalidade = 5  THEN 'CP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                    WHEN licitacao.cod_modalidade = 6  THEN 'PR'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                    WHEN licitacao.cod_modalidade = 7  THEN 'PE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                    WHEN licitacao.cod_modalidade = 8  THEN 'DL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                    WHEN licitacao.cod_modalidade = 9  THEN 'IL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                    WHEN licitacao.cod_modalidade = 10 THEN 'OT'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                    WHEN licitacao.cod_modalidade = 11 THEN 'RP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                              END AS processo_licitatorio,
                             CASE WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL THEN 2
                                  WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN 1
                                  ELSE 3
                             END AS tipo_pessoa,
                             CASE WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL THEN sw_cgm_pessoa_juridica.cnpj
                                  WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN sw_cgm_pessoa_fisica.cpf
                                  ELSE ''
                             END AS cic_participante,
                             REPLACE(cotacao_fornecedor_item.vl_cotacao::text, '.', ',') AS vl_cotacao,
                             CASE WHEN julgamento_item.ordem=1 THEN 'V'
                                  ELSE 'P'
                             END AS situacao,
                             REPLACE((cotacao_item.quantidade::numeric(14,2))::text, '.', ',') AS quantidade,
                             LPAD(cotacao_item.cod_item::varchar,8,'0')||LPAD(cotacao_item.lote::varchar,2,'0') AS cod_item
                
                        FROM licitacao.licitacao AS licitacao
                
                   LEFT JOIN licitacao.licitacao_anulada
                          ON licitacao_anulada.exercicio      = licitacao.exercicio
                         AND licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                         AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                         AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                  
                  INNER JOIN licitacao.cotacao_licitacao
                          ON cotacao_licitacao.cod_licitacao       = licitacao.cod_licitacao
                         AND cotacao_licitacao.cod_modalidade      = licitacao.cod_modalidade
                         AND cotacao_licitacao.cod_entidade        = licitacao.cod_entidade
                         AND cotacao_licitacao.exercicio_licitacao = licitacao.exercicio
                   
                  INNER JOIN compras.cotacao_fornecedor_item
                          ON cotacao_fornecedor_item.cod_item       = cotacao_licitacao.cod_item
                         AND cotacao_fornecedor_item.cgm_fornecedor = cotacao_licitacao.cgm_fornecedor
                         AND cotacao_fornecedor_item.cod_cotacao    = cotacao_licitacao.cod_cotacao
                         AND cotacao_fornecedor_item.exercicio      = cotacao_licitacao.exercicio_cotacao
                         AND cotacao_fornecedor_item.lote           = cotacao_licitacao.lote
                  
                   LEFT JOIN compras.cotacao_anulada
                          ON cotacao_anulada.exercicio   = cotacao_fornecedor_item.exercicio
                         AND cotacao_anulada.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                  
                  INNER JOIN compras.cotacao_item
                          ON cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                         AND cotacao_item.exercicio   = cotacao_fornecedor_item.exercicio
                         AND cotacao_item.cod_item    = cotacao_fornecedor_item.cod_item
                         AND cotacao_item.lote        = cotacao_fornecedor_item.lote
                         
                  INNER JOIN compras.julgamento_item
                          ON julgamento_item.cod_cotacao    = cotacao_fornecedor_item.cod_cotacao
                         AND julgamento_item.exercicio      = cotacao_fornecedor_item.exercicio
                         AND julgamento_item.cod_item       = cotacao_fornecedor_item.cod_item
                         AND julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                         AND julgamento_item.lote           = cotacao_fornecedor_item.lote
                  
                  INNER JOIN compras.julgamento
                          ON julgamento_item.cod_cotacao    = julgamento.cod_cotacao
                         AND julgamento_item.exercicio      = julgamento.exercicio
                         
                  LEFT JOIN sw_cgm_pessoa_juridica
                         ON sw_cgm_pessoa_juridica.numcgm = cotacao_fornecedor_item.cgm_fornecedor
                  
                  LEFT JOIN sw_cgm_pessoa_fisica
                         ON sw_cgm_pessoa_fisica.numcgm = cotacao_fornecedor_item.cgm_fornecedor
                  
                      WHERE licitacao.exercicio = '".$this->getDado('exercicio')."'
                        AND to_char(julgamento.timestamp,'MM') = '".$this->getDado('mes')."'
                        AND licitacao.cod_entidade IN (".$this->getDado('cod_entidade').")
                        
                   GROUP BY licitacao.cod_licitacao
                          , licitacao.cod_modalidade
                          , licitacao.cod_entidade
                          , licitacao.exercicio
                          , sw_cgm_pessoa_juridica.cnpj
                          , sw_cgm_pessoa_fisica.cpf
                          , cotacao_fornecedor_item.vl_cotacao
                          , cotacao_item.quantidade
                          , cotacao_item.cod_item
                          , julgamento_item.ordem
                          , cotacao_item.lote
                       ) AS tabela
                ORDER BY tabela.processo_licitatorio
                       , tabela.cod_item ";
        return $stSql;
    }
}
?>
