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

    $Revision: 59772 $
    $Name$
    $Author: michel $
    $Date: 2014-09-10 13:02:11 -0300 (Wed, 10 Sep 2014) $
    
    $Id: TTPBDotacao.class.php 59772 2014-09-10 16:02:11Z michel $

    * Casos de uso: uc-06.03.00
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTPBDotacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBDotacao()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaTodos()
{
    
    $stSQL  = "
                SELECT desp.exercicio   
                     , lpad(desp.num_orgao::varchar, 2, '0')||lpad(desp.num_unidade::varchar, 2, '0') as unidade   
                     , desp.cod_funcao   
                     , desp.cod_subfuncao   
                     , programa.num_programa AS cod_programa
                     , acao.num_acao  
                     , substr(replace(cod_estrutural,'.',''),1,1) as categoria_economica   
                     , substr(replace(cod_estrutural,'.',''),2,1) as natureza   
                     , COALESCE(orcamento_modalidade_despesa.cod_modalidade,0) as modalidade   
                     , substr(replace(cod_estrutural,'.',''),5,2) as elemento   
                     , replace(SUM(vl_original)::varchar,'.',',') AS vl_original  
                  
                  FROM orcamento.despesa AS desp 
            
            INNER JOIN orcamento.conta_despesa AS cont
                    ON desp.exercicio = cont.exercicio
                   AND desp.cod_conta = cont.cod_conta
            
             LEFT JOIN tcepb.orcamento_modalidade_despesa
                    ON orcamento_modalidade_despesa.cod_despesa = desp.cod_despesa
                   AND orcamento_modalidade_despesa.exercicio   = desp.exercicio
                    
                  JOIN orcamento.despesa_acao
                    ON despesa_acao.exercicio_despesa = desp.exercicio
                   AND despesa_acao.cod_despesa       = desp.cod_despesa

                  JOIN ppa.acao
                    ON acao.cod_acao            = despesa_acao.cod_acao

                  JOIN ppa.programa
                    ON programa.cod_programa    = acao.cod_programa
                   
                 WHERE 1=1 ";
                 
    if ( $this->getDado('exercicio') ) {
        $stSQL .= "AND desp.exercicio = '".$this->getDado('exercicio')."' \n";
    }
    if ( $this->getDado('stEntidades') ) {
        $stSQL .= "AND desp.cod_entidade in (".$this->getDado('stEntidades').")  \n";
    }
                 
    $stSQL .=  "
             GROUP BY desp.exercicio
                  , desp.num_orgao
                  , desp.num_unidade
                  , desp.cod_funcao
                  , desp.cod_subfuncao
                  , programa.num_programa
                  , acao.num_acao
                  , substr(replace(cod_estrutural,'.',''),1,1)
                  , substr(replace(cod_estrutural,'.',''),2,1)
                  , orcamento_modalidade_despesa.cod_modalidade
                  , substr(replace(cod_estrutural,'.',''),5,2)   
                  
             ORDER BY   desp.exercicio
                  , desp.num_orgao
                  , desp.num_unidade
                  , desp.cod_funcao
                  , desp.cod_subfuncao
                  , programa.num_programa
                  , acao.num_acao
                  , substr(replace(cod_estrutural,'.',''),1,1)
                  , substr(replace(cod_estrutural,'.',''),2,1)
                  , orcamento_modalidade_despesa.cod_modalidade
                  , substr(replace(cod_estrutural,'.',''),5,2)";
                  
    return $stSQL;
}

function recuperaAtualizacaoOrcamentaria(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAtualizacaoOrcamentaria().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaAtualizacaoOrcamentaria()
{
                  
    $stSql = "               
              /* suplementacoes */

              SELECT  despesa.exercicio
                   ,  LPAD(despesa.num_orgao::varchar, 2, '0') || LPAD(despesa.num_unidade::varchar, 2, '0') AS unidade
                   ,  despesa.cod_funcao
                   ,  despesa.cod_subfuncao
                   ,  ppa_programa.num_programa
                   ,  acao.num_acao
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),1,1) AS categoria_economica
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),2,1) AS natureza
                   ,  orcamento_modalidade_despesa.cod_modalidade AS modalidade
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),5,2) AS elemento
                   ,  CASE WHEN suplementacao.cod_tipo = 1  THEN 4
                           WHEN suplementacao.cod_tipo = 2  THEN 1
                           WHEN suplementacao.cod_tipo = 3  THEN 1
                           WHEN suplementacao.cod_tipo = 4  THEN 3
                           WHEN suplementacao.cod_tipo = 5  THEN 2
                           WHEN suplementacao.cod_tipo = 6  THEN 8
                           WHEN suplementacao.cod_tipo = 7  THEN 6
                           WHEN suplementacao.cod_tipo = 8  THEN 6
                           WHEN suplementacao.cod_tipo = 9  THEN 9
                           WHEN suplementacao.cod_tipo = 10 THEN 7
                           WHEN suplementacao.cod_tipo = 11 THEN 10
                           WHEN suplementacao.cod_tipo = 12 THEN 13
                           WHEN suplementacao.cod_tipo = 13 THEN 13
                           WHEN suplementacao.cod_tipo = 14 THEN 13
                      END AS tipo_suplementacao
                   ,  REPLACE(SUM(suplementacao_suplementada.valor)::varchar,'.',',') as valor
                   ,  ( LPAD(norma.num_norma,4,'0') || TO_CHAR(norma.dt_assinatura,'yyyy') ) AS num_norma
                FROM  orcamento.despesa
                
          INNER JOIN  orcamento.conta_despesa
                  ON  conta_despesa.exercicio = despesa.exercicio
                 AND  conta_despesa.cod_conta = despesa.cod_conta
                 
          INNER JOIN  orcamento.suplementacao_suplementada
                  ON  suplementacao_suplementada.exercicio = despesa.exercicio
                 AND  suplementacao_suplementada.cod_despesa = despesa.cod_despesa
                 
          INNER JOIN  orcamento.suplementacao
                  ON  suplementacao.exercicio = suplementacao_suplementada.exercicio
                 AND  suplementacao.cod_suplementacao = suplementacao_suplementada.cod_suplementacao
                 
          INNER JOIN  normas.norma
                 ON   suplementacao.cod_norma = norma.cod_norma
                 
          INNER JOIN orcamento.programa AS orcamento_programa
                  ON orcamento_programa.exercicio = despesa.exercicio
                 AND orcamento_programa.cod_programa = despesa.cod_programa
        
          INNER JOIN orcamento.programa_ppa_programa
                  ON programa_ppa_programa.exercicio = orcamento_programa.exercicio
                 AND programa_ppa_programa.cod_programa = orcamento_programa.cod_programa
          
          INNER JOIN ppa.programa AS ppa_programa
                  ON ppa_programa.cod_programa = programa_ppa_programa.cod_programa
                  
          INNER JOIN orcamento.despesa_acao
                  ON despesa_acao.exercicio_despesa = despesa.exercicio
                 AND despesa_acao.cod_despesa = despesa.cod_despesa 

          INNER JOIN ppa.acao
                  ON acao.cod_acao = despesa_acao.cod_acao
                  
	      INNER JOIN tcepb.orcamento_modalidade_despesa
	              ON orcamento_modalidade_despesa.cod_despesa = despesa.cod_despesa
	             AND orcamento_modalidade_despesa.exercicio   = despesa.exercicio
                 
               WHERE  NOT EXISTS ( SELECT  1
                                     FROM  orcamento.suplementacao_anulada
                                    WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio
                                      AND  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                 )
                 AND  NOT EXISTS ( SELECT  1
                                     FROM  orcamento.suplementacao_anulada
                                    WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio
                                      AND  suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                 )
                 AND  despesa.exercicio = '".$this->getDado('exercicio')."'
                 AND  despesa.cod_entidade in (".$this->getDado('stEntidades').")
                 AND  TO_CHAR(suplementacao.dt_suplementacao, 'mm') = '".$this->getDado('inMes')."'
            
            GROUP BY  despesa.exercicio
                   ,  despesa.num_orgao
                   ,  despesa.num_unidade
                   ,  despesa.cod_funcao
                   ,  despesa.cod_subfuncao
                   ,  ppa_programa.num_programa
                   ,  acao.num_acao
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),1,1)
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),2,1)
                   ,  orcamento_modalidade_despesa.cod_modalidade
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),5,2)
                   ,  suplementacao.cod_tipo
                   ,  norma.num_norma
                   ,  TO_CHAR(norma.dt_assinatura,'yyyy')

              UNION ALL

          /* reducoes */

              SELECT  despesa.exercicio
                   ,  LPAD(despesa.num_orgao::varchar, 2, '0') || LPAD(despesa.num_unidade::varchar, 2, '0') AS unidade
                   ,  despesa.cod_funcao
                   ,  despesa.cod_subfuncao
                   ,  ppa_programa.num_programa
                   ,  acao.num_acao
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),1,1)  AS categoria_economica
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),2,1)  AS natureza
                   ,  orcamento_modalidade_despesa.cod_modalidade AS modalidade
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),5,2)  AS elemento
                   ,  CASE WHEN suplementacao.cod_tipo = 1  THEN 11
                           WHEN suplementacao.cod_tipo = 2  THEN 1
                           WHEN suplementacao.cod_tipo = 3  THEN 1
                           WHEN suplementacao.cod_tipo = 4  THEN 3
                           WHEN suplementacao.cod_tipo = 5  THEN 2
                           WHEN suplementacao.cod_tipo = 6  THEN 8
                           WHEN suplementacao.cod_tipo = 7  THEN 6
                           WHEN suplementacao.cod_tipo = 8  THEN 6
                           WHEN suplementacao.cod_tipo = 9  THEN 9
                           WHEN suplementacao.cod_tipo = 10 THEN 7
                           WHEN suplementacao.cod_tipo = 11 THEN 10
                           WHEN suplementacao.cod_tipo = 12 THEN 12
                           WHEN suplementacao.cod_tipo = 13 THEN 12
                           WHEN suplementacao.cod_tipo = 14 THEN 12
                      END AS tipo_suplementacao
                   ,  REPLACE(SUM(suplementacao_reducao.valor)::varchar,'.',',') AS valor
                   ,  ( LPAD(norma.num_norma,4,'0') || TO_CHAR(norma.dt_assinatura,'yyyy') ) AS num_norma
                FROM  orcamento.despesa
                
          INNER JOIN  orcamento.conta_despesa
                  ON  conta_despesa.exercicio = despesa.exercicio
                 AND  conta_despesa.cod_conta = despesa.cod_conta
                 
          INNER JOIN  orcamento.suplementacao_reducao
                  ON  suplementacao_reducao.exercicio = despesa.exercicio
                 AND  suplementacao_reducao.cod_despesa = despesa.cod_despesa
                 
          INNER JOIN  orcamento.suplementacao
                  ON  suplementacao.exercicio = suplementacao_reducao.exercicio
                 AND  suplementacao.cod_suplementacao = suplementacao_reducao.cod_suplementacao
                 
          INNER JOIN  normas.norma
                 ON   suplementacao.cod_norma = norma.cod_norma
                 
          INNER JOIN orcamento.programa AS orcamento_programa
                  ON orcamento_programa.exercicio = despesa.exercicio
                 AND orcamento_programa.cod_programa = despesa.cod_programa
        
          INNER JOIN orcamento.programa_ppa_programa
                  ON programa_ppa_programa.exercicio = orcamento_programa.exercicio
                 AND programa_ppa_programa.cod_programa = orcamento_programa.cod_programa
          
          INNER JOIN ppa.programa AS ppa_programa
                  ON ppa_programa.cod_programa = programa_ppa_programa.cod_programa
                  
          INNER JOIN orcamento.despesa_acao
                  ON despesa_acao.exercicio_despesa = despesa.exercicio
                 AND despesa_acao.cod_despesa = despesa.cod_despesa 

          INNER JOIN ppa.acao
                  ON acao.cod_acao = despesa_acao.cod_acao
                                  
	      INNER JOIN tcepb.orcamento_modalidade_despesa
	              ON orcamento_modalidade_despesa.cod_despesa = despesa.cod_despesa
	             AND orcamento_modalidade_despesa.exercicio   = despesa.exercicio
                                  
               WHERE  NOT EXISTS ( SELECT  1
                                     FROM  orcamento.suplementacao_anulada
                                    WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio
                                      AND  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                 )
                 AND  NOT EXISTS ( SELECT  1
                                     FROM  orcamento.suplementacao_anulada
                                    WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio
                                      AND  suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                 )
                 AND  despesa.exercicio = '".$this->getDado('exercicio')."'
                 AND  despesa.cod_entidade in (".$this->getDado('stEntidades').")
                 AND  TO_CHAR(suplementacao.dt_suplementacao, 'mm') = '".$this->getDado('inMes')."'
                 
            GROUP BY  despesa.exercicio
                   ,  despesa.num_orgao
                   ,  despesa.num_unidade
                   ,  despesa.cod_funcao
                   ,  despesa.cod_subfuncao
                   ,  ppa_programa.num_programa
                   ,  acao.num_acao
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),1,1)
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),2,1)
                   ,  orcamento_modalidade_despesa.cod_modalidade
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),5,2)
                   ,  suplementacao.cod_tipo
                   ,  norma.num_norma
                   ,  TO_CHAR(norma.dt_assinatura,'yyyy') ";

    return $stSql;
}

}

?>