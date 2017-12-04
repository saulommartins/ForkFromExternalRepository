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
    * Extensão da Classe de Mapeamento TTCEALBalanceteVerificacao
    *
    * Data de Criação: 27/05/2014
    *
    * @author: Franver Sarmento de Moraes
    *
    * $Id: TTCEALPagamentoFinanceiro.class.php 65563 2016-05-31 20:36:59Z michel $
    *
    * @ignore
    *
*/

class TTCEALPagamentoFinanceiro extends Persistente {
    /**
        * Método Construtor
        * @access Public
    */
    public function TTCEALPagamentoFinanceiro()
    {
        parent::Persistente();
    }
    /**
     * Método para trazer todos os registros de Projeto Atividade, para o TCEAL
     * @access Public
     * @param  Object  $rsRecordSet Objeto RecordSet
     * @param  String  $stCondicao  String de condição do SQL (WHERE)
     * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
     * @param  Boolean $boTransacao
     * @return Object  Objeto Erro
    */
    public function recuperaPagamentoFinanceiro(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaPagamentoFinanceiro().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaPagamentoFinanceiro()
    {
        $stSql = "
                SELECT * FROM (
                 SELECT (SELECT PJ.cnpj
                          FROM orcamento.entidade
                          JOIN sw_cgm
                            ON sw_cgm.numcgm=entidade.numcgm
                          JOIN sw_cgm_pessoa_juridica AS PJ
                            ON sw_cgm.numcgm=PJ.numcgm
                         WHERE entidade.exercicio='".$this->getDado('exercicio')."'
                           AND entidade.cod_entidade=entidade
                       ) AS cod_und_gestora
                     , (SELECT   CASE valor WHEN '' THEN '0000'
                                          ELSE valor
                                END AS valor
                          FROM administracao.configuracao_entidade
                         WHERE configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                           AND configuracao_entidade.cod_entidade = entidade
                           AND configuracao_entidade.cod_modulo = 62
                           AND configuracao_entidade.parametro = 'tceal_configuracao_unidade_autonoma'
                       ) AS codigo_ua
                     , exercicio || TO_CHAR(dt_empenho,'mm') || LPAD(empenho::VARCHAR , 7, '0')AS num_empenho
                     , exercicio_liquidacao || TO_CHAR(dt_liquidacao,'mm') || LPAD(cod_nota::VARCHAR , 7, '0') AS num_liquidacao
                     , LPAD(ordem::VARCHAR,13,'0') AS num_pagamento
                     , CASE WHEN sinal = '-'THEN 
                            valor_estornado
                        ELSE
                            valor
                        END as valor
                     , sinal
                     , tipo_pagamento
                     , num_documento
                     , RPAD(REPLACE(despesa,'.',''),16,'0') as cod_conta_balancete
                     , cod_banco AS cod_banco
                     , cod_agencia AS cod_agencia_banco
                     , conta_corrente AS num_conta_bancaria
                     , CASE WHEN SUBSTR(REPLACE(despesa, '.',''), 0, 7) ILIKE '1111150'
                            THEN 2
                            ELSE 1
                        END AS tipo_conta
                  FROM tceal.empenho_pago_estornado('".$this->getDado('exercicio')."','".$this->getDado('dtInicial')."','".$this->getDado('dtFinal')."','".$this->getDado('cod_entidade')."','data')
                    as retorno(
                       entidade            integer,
                       descricao_categoria varchar,
                       nom_tipo            varchar,
                       empenho             integer,
                       exercicio           char(4),
                       cgm                 integer,
                       razao_social        varchar,
                       cod_nota            integer,
                       exercicio_liquidacao char(4),
                       dt_liquidacao       date,
                       data                text,
                       ordem               integer,
                       conta               integer,
                       nome_conta          varchar,
                       valor               numeric,
                       valor_estornado     numeric,
                       valor_liquido       numeric,
                       descricao           varchar,
                       recurso             varchar,
                       despesa             varchar(150),
                       cod_banco           varchar,
                       cod_agencia         varchar,
                       conta_corrente      varchar(30),
                       sinal               varchar,
                       dt_empenho          date,
                       tipo_pagamento      integer,
                       num_documento       varchar
                       )
                  UNION
                 SELECT (SELECT PJ.cnpj
                           FROM orcamento.entidade
                           JOIN sw_cgm
                             ON sw_cgm.numcgm=entidade.numcgm
                           JOIN sw_cgm_pessoa_juridica AS PJ
                             ON sw_cgm.numcgm=PJ.numcgm
                          WHERE entidade.exercicio='".$this->getDado('exercicio')."'
                            AND entidade.cod_entidade=entidade
                        ) AS cod_und_gestora
                     , (SELECT   CASE valor WHEN '' THEN '0000'
                                          ELSE valor
                                END AS valor
                           FROM administracao.configuracao_entidade
                          WHERE configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                            AND configuracao_entidade.cod_entidade = entidade
                            AND configuracao_entidade.cod_modulo = 62
                            AND configuracao_entidade.parametro = 'tceal_configuracao_unidade_autonoma'
                        ) AS codigo_ua
                      , exercicio || TO_CHAR(dt_empenho,'mm') || LPAD(empenho::VARCHAR , 7, '0')AS num_empenho
                      , exercicio_liquidacao || TO_CHAR(dt_liquidacao,'mm') || LPAD(cod_nota::VARCHAR , 7, '0') AS num_liquidacao
                      , LPAD(ordem::VARCHAR,13,'0') AS num_pagamento
                      , valor
                      , sinal
                      , tipo_pagamento
                      , num_documento
                      , RPAD(REPLACE(cod_estrutural,'.',''),16,'0') as cod_conta_balancete
                      , cod_banco AS cod_banco
                      , cod_agencia AS cod_agencia_banco
                      , conta_corrente AS num_conta_bancaria
                      , CASE WHEN SUBSTR(cod_estrutural, 0, 7) ILIKE '1111150'
                             THEN 2
                             ELSE 1
                         END AS tipo_conta
                  FROM tceal.empenho_pago_estornado_restos( '".$this->getDado('dtInicial')."', '".$this->getDado('dtFinal')."', '".$this->getDado('cod_entidade')."', '1')
                    as retorno1( 
                       entidade            integer,                             
                       empenho             integer,                             
                       exercicio           char(4),                             
                       credor              varchar,                             
                       cod_estrutural      varchar,                             
                       cod_nota            integer,                             
                       exercicio_liquidacao char(4),
                       dt_liquidacao       date,
                       data                text,                                
                       conta               integer,                             
                       banco               varchar,                             
                       valor               numeric,
                       cod_banco           varchar,
                       cod_agencia         varchar,
                       conta_corrente      varchar,
                       sinal               varchar,
                       dt_empenho          date,
                       ordem               integer,
                       tipo_pagamento      INTEGER,
                       num_documento       VARCHAR
                       ) 
                  UNION     
                 SELECT (SELECT PJ.cnpj
                           FROM orcamento.entidade
                           JOIN sw_cgm
                             ON sw_cgm.numcgm=entidade.numcgm
                           JOIN sw_cgm_pessoa_juridica AS PJ
                             ON sw_cgm.numcgm=PJ.numcgm
                          WHERE entidade.exercicio='".$this->getDado('exercicio')."'
                            AND entidade.cod_entidade=entidade
                        ) AS cod_und_gestora
                     , (SELECT   CASE valor WHEN '' THEN '0000'
                                          ELSE valor
                                END AS valor
                           FROM administracao.configuracao_entidade
                          WHERE configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                            AND configuracao_entidade.cod_entidade = entidade
                            AND configuracao_entidade.cod_modulo = 62
                            AND configuracao_entidade.parametro = 'tceal_configuracao_unidade_autonoma'
                        ) AS codigo_ua
                      , exercicio || TO_CHAR(dt_empenho,'mm') || LPAD(empenho::VARCHAR , 7, '0')AS num_empenho
                      , exercicio_liquidacao || TO_CHAR(dt_liquidacao,'mm') || LPAD(cod_nota::VARCHAR , 7, '0') AS num_liquidacao
                      , LPAD(ordem::VARCHAR,13,'0') AS num_pagamento
                      , valor
                      , sinal
                      , tipo_pagamento
                      , num_documento
                      , RPAD(REPLACE(cod_estrutural,'.',''),16,'0') as cod_conta_balancete
                      , cod_banco AS cod_banco
                      , cod_agencia AS cod_agencia_banco
                      , conta_corrente AS num_conta_bancaria
                      , CASE WHEN SUBSTR(cod_estrutural, 0, 7) ILIKE '1111150'
                             THEN 2
                             ELSE 1
                         END AS tipo_conta
                  FROM tceal.empenho_pago_estornado_restos( '".$this->getDado('dtInicial')."', '".$this->getDado('dtFinal')."', '".$this->getDado('cod_entidade')."', '2')
                    as retorno2( 
                       entidade            integer,                             
                       empenho             integer,                             
                       exercicio           char(4),                             
                       credor              varchar,                             
                       cod_estrutural      varchar,                             
                       cod_nota            integer,                             
                       exercicio_liquidacao char(4),
                       dt_liquidacao       date,
                       data                text,                                
                       conta               integer,                             
                       banco               varchar,                             
                       valor               numeric,
                       cod_banco           varchar,
                       cod_agencia         varchar,
                       conta_corrente      varchar,
                       sinal               varchar,
                       dt_empenho          date,
                       ordem               integer,
                       tipo_pagamento      INTEGER,
                       num_documento       VARCHAR
                       )
                  ) AS tabela ORDER BY tabela.num_empenho ASC
                  ";
                  
        return $stSql;
    }
}
?>