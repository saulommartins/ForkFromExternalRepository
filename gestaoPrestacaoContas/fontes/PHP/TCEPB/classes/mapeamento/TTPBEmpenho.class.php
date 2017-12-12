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
    * Extensão da Classe de mapeamento
    * Data de Criação: 30/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTPBEmpenho.class.php 59662 2014-09-04 13:27:32Z lisiane $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 22/01/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTPBEmpenho extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBEmpenho()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

function recuperaEmpenhos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaEmpenhos().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEmpenhos()
{
    $stSql = "
SELECT
     exercicio
       , unidade
       , cod_funcao
       , cod_subfuncao
       , cod_programa
       , num_pao
       , categoria_economica
       , natureza
       , cod_modalidade
       , modalidade
       , elemento
       , subelemento
       , cod_empenho
       , cod_tipo
       , tipo_empenho
       , dt_empenho
       , valor_empenhado
       , valor_empenhado_nao_formato
       , historico
       , complemento_historico
       , tipo_meta
       , cpf_cnpj
       , obra
       , cod_licitacao
 FROM(
         SELECT  despesa.exercicio
              ,  LPAD(despesa.num_orgao::VARCHAR,2,'0') || LPAD(despesa.num_unidade::VARCHAR,2,'0') as unidade
              ,  despesa.cod_funcao
              ,  despesa.cod_subfuncao
              ,  programa.num_programa AS cod_programa
              ,  acao.num_acao AS num_pao
              ,  SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),1,1) as categoria_economica
              ,  SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),2,1) as natureza
              ,  CASE WHEN atributo_empenho_valor.valor = '1'  THEN 4
                      WHEN atributo_empenho_valor.valor = '2'  THEN 3
                      WHEN atributo_empenho_valor.valor = '3'  THEN 2
                      WHEN atributo_empenho_valor.valor = '4'  THEN 1
                      WHEN atributo_empenho_valor.valor = '5'  THEN 6
                      WHEN atributo_empenho_valor.valor = '6'  THEN 8
                      WHEN atributo_empenho_valor.valor = '7'  THEN 6
                      WHEN atributo_empenho_valor.valor = '8'  THEN 6
                      WHEN atributo_empenho_valor.valor = '9'  THEN 6
                      WHEN atributo_empenho_valor.valor = '10' THEN 0
                 END as cod_modalidade
              ,  SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),3,2) as modalidade
              ,  SUBSTR(replace(estrutural_de_para.estrutural,'.',''),1,2) as elemento
              ,  SUBSTR(replace(estrutural_de_para.estrutural,'.',''),3,2) as subelemento
              ,  empenho.cod_empenho
              ,  pre_empenho.cod_tipo
              ,  CASE WHEN pre_empenho.cod_tipo = 2 THEN 3
                      WHEN pre_empenho.cod_tipo = 3 THEN 2
                      WHEN pre_empenho.cod_tipo = 1 THEN 1
                 END as tipo_empenho
              ,  TO_CHAR(empenho.dt_empenho,'dd/mm/yyyy') as dt_empenho
              ,  LPAD(TRIM(REPLACE(item_pre_empenho.valor_empenhado::VARCHAR,'.', ',')),16,'0') as valor_empenhado
              ,  item_pre_empenho.valor_empenhado as valor_empenhado_nao_formato
              ,  CASE WHEN length(pre_empenho.descricao)<60 THEN RPAD(pre_empenho.descricao,60,'x') else pre_empenho.descricao end as historico
              ,  'sem complemento'::varchar as complemento_historico
              , CASE WHEN SUBSTR(replace(estrutural_de_para.estrutural,'.',''),1,2) = '36' THEN '1'
                     WHEN SUBSTR(replace(estrutural_de_para.estrutural,'.',''),1,2) = '39' AND
                          SUBSTR(replace(estrutural_de_para.estrutural,'.',''),3,2) = '44' THEN '4'
                     WHEN SUBSTR(replace(estrutural_de_para.estrutural,'.',''),1,2) = '39' AND
                          SUBSTR(replace(estrutural_de_para.estrutural,'.',''),3,2) = '47' THEN '4'
                     WHEN SUBSTR(replace(estrutural_de_para.estrutural,'.',''),1,2) = '39' THEN '1'
                     WHEN SUBSTR(replace(estrutural_de_para.estrutural,'.',''),1,2) = '52' THEN '3'
                     WHEN SUBSTR(replace(estrutural_de_para.estrutural,'.',''),1,2) = '51' THEN '4'
                     WHEN SUBSTR(replace(estrutural_de_para.estrutural,'.',''),1,2) = '30' AND
                          SUBSTR(replace(estrutural_de_para.estrutural,'.',''),3,2) = '01' THEN '5'
                     WHEN SUBSTR(replace(estrutural_de_para.estrutural,'.',''),1,2) = '11' THEN '6'
                     ELSE '7'
                  END as tipo_meta

              ,  CASE WHEN  sw_cgm_documento.documento IS NOT NULL THEN LPAD(sw_cgm_documento.documento,14,'0')
                      ELSE  LPAD(entidade_documento.documento,14,'0')
                 END as cpf_cnpj
              , empenho_obras.num_obra||empenho_obras.exercicio_obras as obra
              , lcl.cod_licitacao||lcl.exercicio_licitacao as cod_licitacao

           FROM  empenho.empenho
      LEFT JOIN  tcepb.empenho_obras
             ON  empenho_obras.exercicio_empenho = empenho.exercicio
            AND empenho_obras.cod_entidade      = empenho.cod_entidade
            AND empenho_obras.cod_empenho       = empenho.cod_empenho
     
     INNER JOIN  empenho.pre_empenho
             ON  pre_empenho.exercicio = empenho.exercicio
            AND  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

      INNER JOIN (SELECT atributo_empenho_valor.*
                    FROM empenho.atributo_empenho_valor
                   INNER JOIN (SELECT cod_pre_empenho, exercicio, max(timestamp) as timestamp, cod_atributo, cod_modulo, cod_cadastro
                         FROM empenho.atributo_empenho_valor
                        WHERE cod_atributo = 101
                          AND cod_modulo = 10
                          AND cod_cadastro = 1
                        GROUP BY cod_pre_empenho, exercicio, cod_atributo, cod_modulo, cod_cadastro) as max_empenho
                      ON max_empenho.cod_pre_empenho = atributo_empenho_valor.cod_pre_empenho
                     AND max_empenho.exercicio     = atributo_empenho_valor.exercicio
                     AND max_empenho.timestamp       = atributo_empenho_valor.timestamp
                     AND max_empenho.cod_atributo    = atributo_empenho_valor.cod_atributo
                     AND max_empenho.cod_modulo      = atributo_empenho_valor.cod_modulo
                     AND max_empenho.cod_cadastro    = atributo_empenho_valor.cod_cadastro
                ) as atributo_empenho_valor
             ON atributo_empenho_valor.exercicio = pre_empenho.exercicio
            AND atributo_empenho_valor.cod_pre_empenho = pre_empenho.cod_pre_empenho

      LEFT JOIN ( SELECT  CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                               THEN sw_cgm_pessoa_fisica.cpf
                               WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                               THEN sw_cgm_pessoa_juridica.cnpj
                               ELSE NULL
                            END AS documento
                         ,  sw_cgm.numcgm
                      FROM  sw_cgm
                 LEFT JOIN  sw_cgm_pessoa_fisica
                        ON  sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                 LEFT JOIN  sw_cgm_pessoa_juridica
                        ON  sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                 ) AS sw_cgm_documento
                ON  sw_cgm_documento.numcgm = pre_empenho.cgm_beneficiario
                
    LEFT JOIN  ( SELECT  entidade.exercicio
                        ,  entidade.cod_entidade
                        ,  CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                THEN sw_cgm_pessoa_fisica.cpf
                                WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                                THEN sw_cgm_pessoa_juridica.cnpj
                                ELSE NULL
                           END AS documento
                     FROM  orcamento.entidade
                LEFT JOIN  sw_cgm_pessoa_fisica
                       ON  sw_cgm_pessoa_fisica.numcgm = entidade.numcgm
                LEFT JOIN  sw_cgm_pessoa_juridica
                       ON  sw_cgm_pessoa_juridica.numcgm = entidade.numcgm
                ) AS entidade_documento
               ON  entidade_documento.exercicio = empenho.exercicio
              AND  entidade_documento.cod_entidade = empenho.cod_entidade

     INNER JOIN  ( SELECT  exercicio
                        ,  cod_pre_empenho
                        ,  SUM(vl_total) as valor_empenhado
                     FROM  empenho.item_pre_empenho
                 GROUP BY  exercicio, cod_pre_empenho
                 ) AS item_pre_empenho
             ON  item_pre_empenho.exercicio = pre_empenho.exercicio
            AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

      LEFT JOIN  empenho.item_pre_empenho_julgamento as ipej
             ON  item_pre_empenho.exercicio       = ipej.exercicio
            AND  item_pre_empenho.cod_pre_empenho = ipej.cod_pre_empenho

      LEFT JOIN COMPRAS.cotacao_fornecedor_item as cfi
             ON ipej.exercicio      = cfi.exercicio
            AND ipej.cod_cotacao    = cfi.cod_cotacao
            AND ipej.lote           = cfi.lote
            AND ipej.cgm_fornecedor = cfi.cgm_fornecedor

    LEFT JOIN licitacao.cotacao_licitacao as lcl
           ON cfi.cgm_fornecedor = lcl.cgm_fornecedor
          AND cfi.cod_cotacao = lcl.cgm_fornecedor
          AND cfi.exercicio = lcl.exercicio_licitacao
          AND cfi.lote = lcl.lote

    LEFT JOIN licitacao.licitacao as ll
           ON lcl.cod_licitacao       = ll.cod_licitacao
          AND lcl.cod_modalidade      = ll.cod_modalidade
          AND lcl.cod_entidade        = ll.cod_entidade
          AND lcl.exercicio_licitacao = ll.exercicio

   INNER JOIN  empenho.pre_empenho_despesa
           ON  pre_empenho_despesa.exercicio = pre_empenho.exercicio
          AND  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
          
   INNER JOIN  orcamento.despesa
           ON  despesa.exercicio = pre_empenho_despesa.exercicio
          AND  despesa.cod_despesa = pre_empenho_despesa.cod_despesa
          
   INNER JOIN  orcamento.conta_despesa
           ON  conta_despesa.exercicio = despesa.exercicio
          AND  conta_despesa.cod_conta = despesa.cod_conta
   
    LEFT JOIN  tcepb.elemento_de_para AS estrutural_de_para
           ON  estrutural_de_para.exercicio = pre_empenho_despesa.exercicio
          AND  estrutural_de_para.cod_conta = pre_empenho_despesa.cod_conta
        
   INNER JOIN orcamento.programa_ppa_programa
           ON programa_ppa_programa.cod_programa = despesa.cod_programa
          AND programa_ppa_programa.exercicio    = despesa.exercicio

   INNER JOIN orcamento.despesa_acao
           ON despesa_acao.exercicio_despesa = despesa.exercicio
          AND despesa_acao.cod_despesa  	  = despesa.cod_despesa      
         
         JOIN  ppa.programa
           ON  programa.cod_programa = programa_ppa_programa.cod_programa_ppa
           
         JOIN  ppa.acao
           ON  acao.cod_programa = programa_ppa_programa.cod_programa_ppa
          AND  acao.cod_acao = despesa_acao.cod_acao

        WHERE  despesa.exercicio = '".$this->getDado('exercicio')."'
          AND  TO_CHAR(empenho.dt_empenho,'mm') = '".$this->getDado('inMes')."' ";
    
    if ( $this->getDado('stEntidades') ) {
        $stSql .= " AND despesa.cod_entidade in (".$this->getDado('stEntidades').") ";
    }
    
    $stSql .= " ORDER BY conta_despesa.exercicio
                       , empenho.cod_empenho

    ) as tbl
    
GROUP BY exercicio
       , unidade
       , cod_funcao
       , cod_subfuncao
       , cod_programa
       , num_pao
       , categoria_economica
       , natureza
       , cod_modalidade
       , modalidade
       , elemento
       , subelemento
       , cod_empenho
       , cod_tipo
       , tipo_empenho
       , dt_empenho
       , valor_empenhado
       , valor_empenhado_nao_formato
       , historico
       , complemento_historico
       , tipo_meta
       , cpf_cnpj
       , obra
       , cod_licitacao ";

    return $stSql;
}

function recuperaEstornos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaEstornos().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEstornos()
{
    
    $stSQL = "
            SELECT TO_CHAR(empenho_anulado.timestamp,'yyyy') AS exercicio  
                 , LPAD(despesa.num_orgao::VARCHAR, 2, '0')||LPAD(despesa.num_unidade::VARCHAR, 2, '0') AS unidade  
                 , empenho.cod_empenho  
                 , substr(empenho_anulado.oid::VARCHAR,length(empenho_anulado.oid::VARCHAR)-6,7) AS numero_empenho_anulado  
                 , TO_CHAR(empenho_anulado.timestamp,'dd/mm/yyyy') AS data_anulacao  
                 , COALESCE(sume.valor_anulado, 0.00) AS valor_anulado  
                 , CASE WHEN liq.cod_entidade IS NOT NULL THEN 'S'
                        ELSE 'N'
                   END AS foi_liquidada   
                 , 'Anulação do Empenho Nro: '||empenho.cod_empenho||' de '||to_char(empenho.dt_empenho,'dd/mm/yyyy') AS motivo    
         
             FROM empenho.empenho
                             
        LEFT JOIN ( SELECT exercicio_empenho  
                        , cod_entidade  
                        , cod_empenho  
                     FROM empenho.nota_liquidacao as liq  
                    WHERE exercicio = '".$this->getDado('exercicio')."' \n";
                    
    if ( $this->getDado('stEntidades') ) {
        $stSQL .= "   AND cod_entidade in (".$this->getDado('stEntidades').") \n";
    }
                      
    $stSQL .= "  GROUP BY exercicio_empenho
                        , cod_entidade
                        , cod_empenho  
                  ) AS liq  
                 ON empenho.exercicio    = liq.exercicio_empenho  
                AND empenho.cod_entidade = liq.cod_entidade  
                AND empenho.cod_empenho  = liq.cod_empenho  
                                      
         INNER JOIN empenho.pre_empenho
                 ON empenho.exercicio       = pre_empenho.exercicio  
                AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho  
         
         INNER JOIN empenho.empenho_anulado
                 ON empenho.exercicio    = empenho_anulado.exercicio  
                AND empenho.cod_entidade = empenho_anulado.cod_entidade  
                AND empenho.cod_empenho  = empenho_anulado.cod_empenho  
         
         INNER JOIN empenho.pre_empenho_despesa
                 ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio  
                AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
         
         INNER JOIN orcamento.despesa
                 ON pre_empenho_despesa.exercicio   = despesa.exercicio  
                AND pre_empenho_despesa.cod_despesa = despesa.cod_despesa  
         
         INNER JOIN orcamento.conta_despesa
                 ON despesa.exercicio = conta_despesa.exercicio  
                AND despesa.cod_conta = conta_despesa.cod_conta  
         
         INNER JOIN (  SELECT exercicio  
                          , cod_entidade  
                          , cod_empenho  
                          , timestamp  
                          , sum(vl_anulado) AS valor_anulado  
                       FROM empenho.empenho_anulado_item AS ipe  
                      WHERE exercicio = '".$this->getDado('exercicio')."' \n";
                      
    if ( $this->getDado('stEntidades') ) {
        $stSQL .= "    AND cod_entidade in (".$this->getDado('stEntidades').") \n";
    }
    
    $stSQL .= "    GROUP BY exercicio
                          , cod_entidade
                          , cod_empenho
                          , timestamp  
                     ) AS sume
                 ON empenho_anulado.exercicio       = sume.exercicio  
                AND empenho_anulado.cod_entidade    = sume.cod_entidade  
                AND empenho_anulado.cod_empenho     = sume.cod_empenho  
                AND empenho_anulado.timestamp       = sume.timestamp  
                
              WHERE TO_CHAR(empenho_anulado.timestamp,'yyyy') = '".$this->getDado('exercicio')."' \n";
              
    if ( $this->getDado('stEntidades') ) {
        $stSQL .= " AND empenho_anulado.cod_entidade in (".$this->getDado('stEntidades').")  \n";
    }
    
    $stSQL .= " AND TO_CHAR(empenho_anulado.timestamp,'mm') = '".$this->getDado('inMes')."'
         
           ORDER BY despesa.num_orgao
                  , despesa.num_unidade
                  , empenho.cod_empenho
                  , empenho_anulado.oid ";
                  
    return $stSQL;

}
}
