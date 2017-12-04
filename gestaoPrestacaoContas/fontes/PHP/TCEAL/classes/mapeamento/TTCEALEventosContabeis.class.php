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

    * Extensão da Classe de Mapeamento TTCEALEventosContabeis
    *
    * Data de Criação: 31/03/2016
    *
    * @author: Arthur Cruz
    *
    * $Id: TTCEALEventosContabeis.class.php 65563 2016-05-31 20:36:59Z michel $
    *
    * @ignore
    *
*/
class TTCEALEventosContabeis extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALEventosContabeis()
    {
        parent::Persistente();
    }
     
    function recuperaEventosContabeis(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = ""){
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montarecuperaEventosContabeis().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        return $obErro;
    }
    
    function montarecuperaEventosContabeis(){
        $stSql = " SELECT cod_und_gestora
                        , codigo_ua
                        , exercicio
                        , bimestre
                        , cod_evento
                        , historico
                        , TO_CHAR(dt_lancamento, 'DD/MM/YYYY') AS dt_lancamento
                        , id_debcred
                        , RPAD(REPLACE(cod_conta_contabil,'.',''),17,'0') AS cod_conta_contabil
                        , vl_lancamento
                     FROM ( SELECT ( SELECT PJ.cnpj 
                                       FROM orcamento.entidade 
                                 INNER JOIN sw_cgm 
                                         ON sw_cgm.numcgm = entidade.numcgm
                                 INNER JOIN sw_cgm_pessoa_juridica AS PJ 
                                         ON PJ.numcgm = sw_cgm.numcgm
                                      WHERE entidade.exercicio = '".$this->getDado('stExercicio')."' 
                                        AND entidade.cod_entidade = ".$this->getDado('inCodEntidade')."
                        ) AS cod_und_gestora
                        , (SELECT LPAD(valor,4,'0') 
                             FROM administracao.configuracao_entidade 
                            WHERE exercicio = '".$this->getDado('stExercicio')."'
                              AND cod_entidade = ".$this->getDado('inCodEntidade')." 
                              AND cod_modulo = 62 
                              AND parametro = 'tceal_configuracao_unidade_autonoma'
                        ) AS codigo_ua
                        , '".$this->getDado('stExercicio')."' AS exercicio
                        , ".$this->getDado('bimestre')." AS bimestre
                        , retorno_evento.cod_evento
                        , historico_contabil.nom_historico AS historico
                        , TO_DATE(TO_CHAR(lote.dt_lote, 'DD/MM/YYYY'), 'DD-MM-YYYY') AS dt_lancamento
                        , CASE WHEN retorno_evento.tipo_conta = 'credito' THEN 2
                               WHEN retorno_evento.tipo_conta = 'debito' THEN 1
                          END AS id_debcred
                        , REPLACE(retorno_evento.cod_estrutural, '.', '') AS cod_conta_contabil
                        , SUM(valor_lancamento.vl_lancamento) AS vl_lancamento
                     FROM contabilidade.lancamento
               INNER JOIN contabilidade.historico_contabil
                       ON historico_contabil.cod_historico = lancamento.cod_historico
                      AND historico_contabil.exercicio     = lancamento.exercicio
               INNER JOIN contabilidade.valor_lancamento
                       ON valor_lancamento.cod_lote      = lancamento.cod_lote       
                      AND valor_lancamento.tipo          = lancamento.tipo            
                      AND valor_lancamento.sequencia     = lancamento.sequencia       
                      AND valor_lancamento.exercicio     = lancamento.exercicio       
                      AND valor_lancamento.cod_entidade  = lancamento.cod_entidade
               INNER JOIN contabilidade.lote
                       ON lote.exercicio    = lancamento.exercicio
                      AND lote.cod_lote     = lancamento.cod_lote
                      AND lote.tipo         = lancamento.tipo
                      AND lote.cod_entidade = lancamento.cod_entidade
               INNER JOIN ( SELECT cc.cod_lote
                                 , cc.tipo
                                 , cc.sequencia
                                 , cc.exercicio                                    
                                 , cc.tipo_valor                                   
                                 , cc.cod_entidade                                 
                                 , cc.cod_plano
                                 , pc.cod_conta                                    
                                 , pc.cod_estrutural
                                 , pc.nom_conta
                              FROM contabilidade.plano_analitica     AS pa          
                        INNER JOIN contabilidade.conta_credito       AS cc          
                                ON cc.cod_plano    = pa.cod_plano
                               AND cc.exercicio    = pa.exercicio                                                                                                    
                        INNER JOIN contabilidade.plano_conta         AS pc          
                                ON pc.cod_conta    = pa.cod_conta
                               AND pc.exercicio    = pa.exercicio                                                             
                             WHERE pa.exercicio = '".$this->getDado('stExercicio')."' 

                          UNION 

                            SELECT cd.cod_lote
                                 , cd.tipo                                      
                                 , cd.sequencia
                                 , cd.exercicio
                                 , cd.tipo_valor
                                 , cd.cod_entidade
                                 , cd.cod_plano
                                 , pc.cod_conta
                                 , pc.cod_estrutural
                                 , pc.nom_conta                                      
                              FROM contabilidade.plano_analitica     AS pa          
                        INNER JOIN contabilidade.conta_debito        AS cd          
                                ON cd.cod_plano    = pa.cod_plano    
                               AND cd.exercicio    = pa.exercicio                                                          
                        INNER JOIN contabilidade.plano_conta         AS pc          
                                ON pc.cod_conta    = pa.cod_conta   
                               AND pc.exercicio    = pa.exercicio                                                           
                             WHERE pa.exercicio = '".$this->getDado('stExercicio')."' 
                        ) AS  contas                                             
                       ON contas.cod_lote     = valor_lancamento.cod_lote
                      AND contas.tipo_valor   = valor_lancamento.tipo_valor
                      AND contas.tipo         = valor_lancamento.tipo
                      AND contas.sequencia    = valor_lancamento.sequencia
                      AND contas.exercicio    = valor_lancamento.exercicio
                      AND contas.cod_entidade = valor_lancamento.cod_entidade
               INNER JOIN ( SELECT *
                              FROM recupera_tipo_eventos_contabeis('".$this->getDado('stExercicio')."' )
                                AS retorno (  cod_conta integer
                                            , cod_estrutural varchar
                                            , tipo_conta text
                                            , cod_evento integer
                                            , nom_evento varchar
                                         )
                       ) AS retorno_evento
                      ON retorno_evento.cod_conta = contas.cod_conta
                   WHERE lancamento.exercicio = '".$this->getDado('stExercicio')."'
                     AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado("dt_inicial")."','DD/MM/YYYY')
                                         AND TO_DATE('".$this->getDado("dt_final")."','DD/MM/YYYY')
                     AND lancamento.cod_entidade IN ( ".$this->getDado('inCodEntidade')." )
                GROUP BY 1,2,3,4,5,6,7,8,9
	            ORDER BY retorno_evento.cod_evento, dt_lancamento) as tabela";
        return $stSql;        
    }
     
}

?>