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
    * Extensão da Classe de Mapeamento TTCETOPagamentoFinanceiro
    *
    * Data de Criação: 18/11/2014
    *
    * @author: Evandro Melos
    *
    * $Id: TTCETOPagamentoFinanceiro.class.php 60986 2014-11-27 13:03:59Z carlos.silva $
    *
    * @ignore
    *
*/

class TTCETOPagamentoFinanceiro extends Persistente {
    /**
        * Método Construtor
        * @access Public
    */
    public function TTCETOPagamentoFinanceiro()
    {
        parent::Persistente();
    }
    /**
     * Método para trazer todos os registros de Projeto Atividade, para o TCETO
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
        $stSql = "  SELECT * 
                    FROM (
                            SELECT (SELECT PJ.cnpj
                                        FROM orcamento.entidade
                                        JOIN sw_cgm
                                            ON sw_cgm.numcgm=entidade.numcgm
                                        JOIN sw_cgm_pessoa_juridica AS PJ
                                            ON sw_cgm.numcgm=PJ.numcgm
                                        WHERE entidade.exercicio='".$this->getDado('exercicio')."'
                                        AND entidade.cod_entidade=entidade
                                    ) AS cod_und_gestora
                                    , recurso_vinculado::varchar
                                    , exercicio || LPAD(empenho::VARCHAR ,9,'0') AS num_empenho
                                    , exercicio || LPAD(ordem::VARCHAR,9,'0') AS num_pagamento
                                    , exercicio || LPAD(ordem::VARCHAR,9,'0') AS num_registro
                                    , to_date(data,'dd/mm/yyyy') as data
                                    , REPLACE(despesa, '.','') AS cod_conta_balancete
                                    , CASE WHEN sinal = '-'THEN 
                                           valor_estornado
                                       ELSE
                                           valor
                                       END as valor
                                    , sinal
                                    , tipo_pagamento
                                    , num_documento
                                    
                            FROM tceto.empenho_pago_estornado('".$this->getDado('exercicio')."','".$this->getDado('dtInicial')."','".$this->getDado('dtFinal')."','".$this->getDado('cod_entidade')."','data')
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
                                        num_documento       varchar,
                                        tipo_pagamento      integer,                                        
                                        recurso_vinculado   integer
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
                                    , recurso_vinculado::varchar
                                    , exercicio || LPAD(empenho::VARCHAR ,9,'0') AS num_empenho
                                    , exercicio || LPAD(ordem::VARCHAR,9,'0') AS num_pagamento
                                    , exercicio || LPAD(ordem::VARCHAR,9,'0') AS num_registro
                                    , to_date(data,'dd/mm/yyyy') as data
                                    , REPLACE(cod_estrutural,'.','') AS conta_contabil
                                    , valor
                                    , sinal
                                    , tipo_pagamento
                                    , num_documento
                                    
                            FROM tceto.empenho_pago_estornado_restos( '".$this->getDado('dtInicial')."', '".$this->getDado('dtFinal')."', '".$this->getDado('cod_entidade')."', '1')
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
                                        num_documento       varchar,
                                        tipo_pagamento      integer,
                                        recurso_vinculado   integer
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
                                    , recurso_vinculado::varchar
                                    , exercicio || LPAD(empenho::VARCHAR ,9,'0') AS num_empenho
                                    , exercicio || LPAD(ordem::VARCHAR,9,'0') AS num_pagamento
                                    , exercicio || LPAD(ordem::VARCHAR,9,'0') AS num_registro
                                    , to_date(data,'dd/mm/yyyy') as data
                                    , REPLACE(cod_estrutural,'.','') AS conta_contabil
                                    , valor
                                    , sinal
                                    , tipo_pagamento
                                    , num_documento
                                    
                            FROM tceto.empenho_pago_estornado_restos( '".$this->getDado('dtInicial')."', '".$this->getDado('dtFinal')."', '".$this->getDado('cod_entidade')."', '2')
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
                                        num_documento       varchar,
                                        tipo_pagamento      integer,
                                        recurso_vinculado   integer
                            )
                    ) AS tabela 
                    
                    ORDER BY tabela.num_empenho ASC
                  ";
                  
        return $stSql;
    }
}
?>