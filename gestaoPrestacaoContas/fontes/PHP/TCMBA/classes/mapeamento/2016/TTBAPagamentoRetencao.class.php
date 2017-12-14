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
/*
 * Extensão da Classe de mapeamento Arquivo: PagRetencao.txt
 *
 * @package URBEM
 * @subpackage Mapeamento
 * @version $Id: TTBAPagamentoRetencao.class.php 63992 2015-11-16 16:49:19Z lisiane $
 * @author Michel Teixeira
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTBAPagamentoRetencao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function __construct()
    {
        $this->setEstrutura( array() );
        $this->setEstruturaAuxiliar( array() );
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

function montaRecuperaDadosTribunal()
    {
        $stSql = " SELECT 1 AS tipo_registro
                        , empenho.exercicio  
                        , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                        , despesa.num_orgao AS cod_orgao
                        , despesa.num_unidade AS unidade_orcamentaria   
                        , empenho.cod_empenho AS num_empenho
                        , empenho.cod_empenho AS num_sub_empenho 
                        , REPLACE(pc.cod_estrutural,'.','') AS conta_contabil                                                 
                        , opr.cod_ordem   
                        , TO_CHAR(nlp.timestamp,'yyyymm') AS competencia                                                       
                        , opr.vl_retencao as vl_total_retencao   
                        , TO_CHAR(nlp.timestamp,'dd/mm/yyyy') AS dt_pagamento  
                        , REPLACE(conta.cod_estrutural,'.','') AS conta_pagadora        
                        , pagamento_tipo_documento_pagamento.cod_tipo AS tipo_pagamento
                        , pagamento_tipo_documento_pagamento.num_documento AS detalhe_tipo_pagamento
                        ,(coalesce(sum(nlp.vl_pago),0.00) - coalesce(sum(nlpa.vl_anulado),0.00)) as vl_pagamento_retencao  
                     FROM empenho.ordem_pagamento_retencao as OPR  
                     JOIN contabilidade.plano_analitica as PA                             
                       ON pa.cod_plano = opr.cod_plano                                
                      AND pa.exercicio = opr.exercicio                               
                     JOIN contabilidade.plano_conta as PC                                 
                       ON pa.cod_conta = pc.cod_conta                                 
                      AND pa.exercicio = pc.exercicio
               INNER JOIN empenho.pagamento_liquidacao as pl 
                       ON OPR.exercicio    = pl.exercicio_liquidacao                                         
                      AND OPR.cod_entidade = pl.cod_entidade                                                 
                      AND OPR.cod_ordem     = pl.cod_ordem 
               INNER JOIN empenho.nota_liquidacao as nl                                                             
                       ON nl.exercicio    = pl.exercicio_liquidacao                                         
                      AND nl.cod_entidade = pl.cod_entidade                                                 
                      AND nl.cod_nota     = pl.cod_nota 
               INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga as plnlp
                       ON plnlp.exercicio            = pl.exercicio                                          
                      AND plnlp.cod_entidade         = pl.cod_entidade                                       
                      AND plnlp.cod_ordem            = pl.cod_ordem                                          
                      AND plnlp.exercicio_liquidacao = pl.exercicio_liquidacao                               
                      AND plnlp.cod_nota             = pl.cod_nota  
                      AND plnlp.timestamp = (SELECT max(timestamp) FROM empenho.pagamento_liquidacao_nota_liquidacao_paga
                                                                  WHERE pagamento_liquidacao_nota_liquidacao_paga.exercicio = plnlp.exercicio                                              
                                                                    AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade         = plnlp.cod_entidade                                       
                                                                    AND pagamento_liquidacao_nota_liquidacao_paga.cod_ordem            = plnlp.cod_ordem                                          
                                                                    AND pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao = plnlp.exercicio_liquidacao                               
                                                                    AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota             = plnlp.cod_nota     )                                         
               INNER JOIN empenho.nota_liquidacao_paga as nlp
                       ON nlp.exercicio    = plnlp.exercicio_liquidacao                                      
                      AND nlp.cod_entidade = plnlp.cod_entidade                                              
                      AND nlp.cod_nota     = plnlp.cod_nota                                                  
                      AND nlp.timestamp    = plnlp.timestamp                                                 
                LEFT JOIN ( SELECT exercicio
                                 , cod_entidade
                                 , cod_nota
                                 , timestamp                               
                                 , coalesce(sum(vl_anulado),0.00) as vl_anulado                                
                              FROM empenho.nota_liquidacao_paga_anulada                                        
                          GROUP BY exercicio, cod_entidade, cod_nota, timestamp                                      
                        ) AS nlpa
                       ON nlpa.exercicio    = nlp.exercicio                                                 
                      AND nlpa.cod_entidade = nlp.cod_entidade                                              
                      AND nlpa.cod_nota     = nlp.cod_nota                                                   
                      AND nlpa.timestamp    = nlp.timestamp                                                  
               INNER JOIN empenho.nota_liquidacao_conta_pagadora AS nlcp
                       ON nlcp.exercicio_liquidacao = nlp.exercicio                                          
                      AND nlcp.cod_entidade         = nlp.cod_entidade                                       
                      AND nlcp.cod_nota             = nlp.cod_nota                                           
                      AND nlcp.timestamp            = nlp.timestamp                                          
               INNER JOIN ( SELECT pa.exercicio                                                               
                                 , pa.cod_plano                                                               
                                 , pc.nom_conta                                                               
                                 , rec.cod_recurso                                                             
                                 , rec.nom_recurso  
                                 , pc.cod_estrutural                                                          
                              FROM contabilidade.plano_analitica as pa                                        
                                 , contabilidade.plano_conta as pc                                            
                                 , contabilidade.plano_recurso as pr                                          
                                 , orcamento.recurso as rec                                                   
                             WHERE pa.cod_conta = pc.cod_conta                                                
                               AND pa.exercicio = pc.exercicio                                                
                               AND pa.cod_plano = pr.cod_plano                                                
                               AND pa.exercicio = pr.exercicio                                                
                               AND pr.cod_recurso = rec.cod_recurso                                           
                               AND pr.exercicio   = rec.exercicio                                             
                        ) AS conta
                        ON conta.cod_plano = nlcp.cod_plano                                                   
                       AND conta.exercicio = nlcp.exercicio                                                   
               INNER JOIN empenho.empenho
                       ON empenho.exercicio    = nl.exercicio_empenho
                      AND empenho.cod_entidade = nl.cod_entidade
                      AND empenho.cod_empenho  = nl.cod_empenho
                LEFT JOIN tcmba.pagamento_tipo_documento_pagamento
                       ON pagamento_tipo_documento_pagamento.cod_entidade = nlp.cod_entidade
                      AND pagamento_tipo_documento_pagamento.exercicio    = nlp.exercicio
                      AND pagamento_tipo_documento_pagamento.timestamp    = nlp.timestamp
                      AND pagamento_tipo_documento_pagamento.cod_nota     = nlp.cod_nota
               INNER JOIN empenho.pre_empenho
                       ON empenho.exercicio       = pre_empenho.exercicio
                      AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho             
               INNER JOIN empenho.pre_empenho_despesa
                       ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                      AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
               INNER JOIN orcamento.despesa
                       ON despesa.exercicio    = pre_empenho_despesa.exercicio
                      AND despesa.cod_despesa  = pre_empenho_despesa.cod_despesa 
                    WHERE opr.exercicio         = '".$this->getDado('exercicio')."'             
                      AND opr.cod_entidade      IN (".$this->getDado('entidades').")
                      AND to_date(to_char(nlp.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('data_inicial')."', 'dd/mm/yyyy')
                									 AND TO_DATE('".$this->getDado('data_final')."', 'dd/mm/yyyy')
                 GROUP BY empenho.exercicio  
                        , despesa.num_orgao   
                        , despesa.num_unidade   
                        , empenho.cod_empenho 
                        , empenho.cod_empenho 
                        , pc.cod_estrutural                                                 
                        , opr.cod_ordem
                        , nlp.timestamp
                        , opr.vl_retencao 
                        , conta.cod_estrutural 
                        , pagamento_tipo_documento_pagamento.cod_tipo 
                        , pagamento_tipo_documento_pagamento.num_documento 
        ";

      return $stSql;
    }

    function recuperaLogErro(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaLogErro().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    function montaRecuperaLogErro()
    {
        $stSql = " SELECT count(to_char(nlp.timestamp,'yyyy')) registros
                        , count(ptdp.exercicio) as obrigatorio
                     FROM empenho.nota_liquidacao_paga      AS nlp
               INNER JOIN tesouraria.pagamento
                       ON pagamento.exercicio       = nlp.exercicio
                      AND pagamento.cod_nota        = nlp.cod_nota
                      AND pagamento.cod_entidade    = nlp.cod_entidade
                      AND pagamento.timestamp       = nlp.timestamp
                LEFT JOIN tcmba.pagamento_tipo_documento_pagamento AS ptdp
                       ON ptdp.cod_entidade = pagamento.cod_entidade
                      AND ptdp.exercicio    = pagamento.exercicio
                      AND ptdp.timestamp    = pagamento.timestamp
                      AND ptdp.cod_nota     = pagamento.cod_nota
                    WHERE to_char(nlp.timestamp,'yyyy')  = '".$this->getDado('exercicio')."'
                      AND to_date(to_char(nlp.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                      AND nlp.cod_entidade IN (".$this->getDado('stEntidades').")
                      ";
        return $stSql;
    }

    public function __destruct() {}
}
