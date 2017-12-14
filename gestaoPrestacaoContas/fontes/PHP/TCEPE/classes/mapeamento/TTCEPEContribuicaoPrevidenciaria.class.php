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
    * 
    * Data de Criação: 18/11/2014

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor:  Michel Teixeira
    $Id: TTCEPEContribuicaoPrevidenciaria.class.php 60836 2014-11-18 15:31:02Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEContribuicaoPrevidenciaria extends Persistente
{
    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEPEContribuicaoPrevidenciaria()
    {
        parent::Persistente();
    }

    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContribuicaoPrevidenciaria.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaContribuicaoPrevidenciaria(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContribuicaoPrevidenciaria().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaContribuicaoPrevidenciaria()
    {
        $stSql = "
                   SELECT servidor.mes
                        , servidor.cod_regime_previdencia
                        , servidor.tipo_contribuicao
                        , COALESCE(servidor.aliquota, 0.00) AS aliquota
                        , COALESCE(SUM(servidor.base), 0.00) AS base
                        , COALESCE(SUM(servidor.retido), 0.00) AS retido
                        , COALESCE(servidor.valor_contabilizado, 0.00) AS valor_contabilizado
                        , COALESCE(SUM(servidor.pago_direto), 0.00) AS pago_direto
                        , COALESCE(SUM(servidor.recolhido), 0.00) AS recolhido
                        , COALESCE(SUM(servidor.familia ), 0.00) AS familia
                        , servidor.dt_vencimento
                        , servidor.dt_repasse
                     FROM (
                               SELECT CASE WHEN CP.folha = 3 THEN '13'::VARCHAR
                                           ELSE '".$this->getDado('stMes')."'::VARCHAR
                                      END AS mes
                                    , contrato.registro AS matricula
                                    , sw_cgm.nom_cgm
                                    , previdencia.cod_regime_previdencia
                                    , CASE WHEN previdencia.cod_vinculo IN (1,2,3) THEN 1
                                      END AS tipo_contribuicao
                                    , previdencia_previdencia.aliquota
                                    , CP.base
                                    , CP.desconto AS retido
                                    , empenhos.valor_liquido AS valor_contabilizado
                                    , SUM(registro_evento_periodo.valor) AS valor_pago_direto
                                    , CASE WHEN previdencia.cod_vinculo IN (2,3) THEN
                                                    SUM(registro_evento_periodo.valor)
                                      END AS pago_direto
                                    , ((CP.base * previdencia_previdencia.aliquota / 100)::numeric(16,2) + CP.desconto)::numeric(16,2) AS recolhido
                                    , TO_CHAR(periodo_movimentacao.dt_final, 'ddmmyyyy') AS dt_vencimento
                                    , TO_CHAR(empenhos.data, 'ddmmyyyy') AS dt_repasse
                                    , contrato.cod_contrato
                                    , periodo_movimentacao.cod_periodo_movimentacao
                                    , CP.familia            

                                 FROM sw_cgm

                           INNER JOIN sw_cgm_pessoa_fisica
                                   ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm

                            LEFT JOIN pessoal".$this->getDado('stEntidades').".pensionista
                                   ON pensionista.numcgm = sw_cgm.numcgm

                            LEFT JOIN pessoal".$this->getDado('stEntidades').".contrato_pensionista
                                   ON contrato_pensionista.cod_pensionista=pensionista.cod_pensionista
                                  AND contrato_pensionista.cod_contrato_cedente=pensionista.cod_contrato_cedente

                            LEFT JOIN pessoal".$this->getDado('stEntidades').".servidor
                                   ON servidor.numcgm = sw_cgm.numcgm

                            LEFT JOIN pessoal".$this->getDado('stEntidades').".servidor_contrato_servidor 
                                   ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

                           INNER JOIN pessoal".$this->getDado('stEntidades').".contrato
                                   ON (   contrato.cod_contrato=contrato_pensionista.cod_contrato
                                       OR 
                                          contrato.cod_contrato=servidor_contrato_servidor.cod_contrato
                                      )

                           INNER JOIN folhapagamento".$this->getDado('stEntidades').".periodo_movimentacao
                                   ON TO_CHAR(periodo_movimentacao.dt_final, 'mmyyyy') = '".$this->getDado('stMesAno')."'

                           INNER JOIN (   SELECT previdencia_evento.cod_evento, contrato_servidor_previdencia.cod_contrato, evento.natureza
                                            FROM folhapagamento".$this->getDado('stEntidades').".previdencia_previdencia
                                      INNER JOIN folhapagamento".$this->getDado('stEntidades').".previdencia
                                              ON previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia
                                      INNER JOIN (
                                                    SELECT contrato_servidor_previdencia.cod_previdencia  
                                                         , contrato_servidor_previdencia.cod_contrato                                                       
                                                      FROM pessoal".$this->getDado('stEntidades').".contrato_servidor_previdencia                                                                              
                                                INNER JOIN ( SELECT cod_contrato                                                                                 
                                                                  , max(timestamp) as timestamp                                                                  
                                                              FROM pessoal".$this->getDado('stEntidades').".contrato_servidor_previdencia                                                        
                                                          GROUP BY cod_contrato
                                                           ) AS max_contrato_servidor_previdencia                                           
                                                        ON contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato           
                                                       AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                                                       AND contrato_servidor_previdencia.bo_excluido IS FALSE
                                                INNER JOIN pessoal".$this->getDado('stEntidades').".contrato_servidor                                                                             
                                                        ON contrato_servidor.cod_contrato = contrato_servidor_previdencia.cod_contrato                                                                        
                                                     UNION 
                                                    SELECT contrato_pensionista_previdencia.cod_previdencia     
                                                         , contrato_pensionista_previdencia.cod_contrato                                                 
                                                      FROM pessoal".$this->getDado('stEntidades').".contrato_pensionista_previdencia                                                              
                                                INNER JOIN ( SELECT cod_contrato                                                                                 
                                                                  , max(timestamp) as timestamp                                                                  
                                                               FROM pessoal".$this->getDado('stEntidades').".contrato_pensionista_previdencia                                                     
                                                           GROUP BY cod_contrato
                                                           ) AS max_contrato_pensionista_previdencia                                        
                                                        ON contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato     
                                                       AND contrato_pensionista_previdencia.timestamp = max_contrato_pensionista_previdencia.timestamp           
                                                INNER JOIN pessoal".$this->getDado('stEntidades').".contrato_pensionista                                                                          
                                                        ON contrato_pensionista.cod_contrato = contrato_pensionista_previdencia.cod_contrato                     
                                                 ) contrato_servidor_previdencia
                                              ON previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia
                                      INNER JOIN folhapagamento".$this->getDado('stEntidades').".previdencia_evento
                                              ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                             AND previdencia_previdencia.timestamp = previdencia_evento.timestamp
                                      INNER JOIN folhapagamento".$this->getDado('stEntidades').".evento
                                              ON evento.cod_evento = previdencia_evento.cod_evento

                                           WHERE previdencia.cod_previdencia = ".$this->getDado('inCodPrevidencia')."
                                             AND previdencia_evento.cod_tipo = 1

                                        GROUP BY previdencia_evento.cod_evento, contrato_servidor_previdencia.cod_contrato, evento.natureza
                                      ) AS evento
                                   ON evento.cod_contrato = contrato.cod_contrato

                           INNER JOIN (   SELECT registro_evento_periodo.cod_contrato
                                               , registro_evento_periodo.cod_periodo_movimentacao
                                               , CASE WHEN evento.natureza = 'P' THEN
                                                                SUM(evento_calculado.valor)
                                                      ELSE
                                                                SUM(evento_calculado.valor)*(-1)
                                                 END AS valor
                                            FROM folhapagamento".$this->getDado('stEntidades').".registro_evento_periodo
                                               , folhapagamento".$this->getDado('stEntidades').".evento_calculado
                                               , folhapagamento".$this->getDado('stEntidades').".evento                                              
                                           WHERE evento_calculado.cod_registro = registro_evento_periodo.cod_registro
                                             AND evento.cod_evento = evento_calculado.cod_evento
                                             AND evento.natureza IN ('P','D')
                                             AND registro_evento_periodo.cod_periodo_movimentacao = ".$this->getDado('inCodMovimentacao')."
                                        GROUP BY registro_evento_periodo.cod_contrato
                                               , registro_evento_periodo.cod_periodo_movimentacao
                                               , evento.natureza
                                      ) AS registro_evento_periodo
                                   ON registro_evento_periodo.cod_contrato = contrato.cod_contrato
                                  AND registro_evento_periodo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                  
                            LEFT JOIN ultimo_contrato_servidor_previdencia('".$this->getDado('stEntidades')."', ".$this->getDado('inCodMovimentacao').") AS contrato_servidor_previdencia
                                   ON contrato_servidor_previdencia.cod_contrato = contrato.cod_contrato

                            LEFT JOIN ultimo_contrato_pensionista_previdencia('".$this->getDado('stEntidades')."', ".$this->getDado('inCodMovimentacao').") AS contrato_pensionista_previdencia
                                   ON contrato_pensionista_previdencia.cod_contrato = contrato.cod_contrato

                           INNER JOIN folhapagamento".$this->getDado('stEntidades').".previdencia
                                   ON (   previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia
                                       OR 
                                          previdencia.cod_previdencia = contrato_pensionista_previdencia.cod_previdencia
                                      )

                           INNER JOIN folhapagamento".$this->getDado('stEntidades').".previdencia_previdencia
                                   ON previdencia_previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia
                                  AND previdencia_previdencia.timestamp = (  SELECT max(PP.timestamp) as timestamp                                                        
                                                                               FROM folhapagamento".$this->getDado('stEntidades').".previdencia_previdencia AS PP
                                                                              WHERE PP.cod_previdencia=contrato_servidor_previdencia.cod_previdencia                         
                                                                           GROUP BY cod_previdencia
                                                                          )

                           INNER JOIN (   SELECT *, 0 AS folha FROM recuperaContribuicaoPrevidenciaria('".$this->getDado('stEntidades')."', ".$this->getDado('inCodMovimentacao').", ".$this->getDado('inCodPrevidencia').", 0, 'todos', '', '', 'nao', 0)
                                          UNION ALL
                                          SELECT *, 1 AS folha FROM recuperaContribuicaoPrevidenciaria('".$this->getDado('stEntidades')."', ".$this->getDado('inCodMovimentacao').", ".$this->getDado('inCodPrevidencia').", 1, 'todos', '', '', 'nao', 0)
                                          UNION ALL
                                          SELECT *, 2 AS folha FROM recuperaContribuicaoPrevidenciaria('".$this->getDado('stEntidades')."', ".$this->getDado('inCodMovimentacao').", ".$this->getDado('inCodPrevidencia').", 2, 'todos', '', '', 'nao', 0)
                                          UNION ALL
                                          SELECT *, 3 AS folha FROM recuperaContribuicaoPrevidenciaria('".$this->getDado('stEntidades')."', ".$this->getDado('inCodMovimentacao').", ".$this->getDado('inCodPrevidencia').", 3, 'todos', '', '', 'nao', 0)
                                          UNION ALL
                                          SELECT *, 4 AS folha FROM recuperaContribuicaoPrevidenciaria('".$this->getDado('stEntidades')."', ".$this->getDado('inCodMovimentacao').", ".$this->getDado('inCodPrevidencia').", 4, 'todos', '', '', 'nao', 0)
                                        ORDER BY nom_cgm, registro
                                      ) AS CP
                                   ON CP.cod_contrato = contrato.cod_contrato

                            LEFT JOIN ( SELECT retorno.cgm
                                             , sum(retorno.valor_liquido) AS valor_liquido
                                             , TO_DATE(MAX(retorno.data), 'dd/mm/yyyy') AS data
                                             , CASE WHEN retorno.despesa LIKE ('3.1.9.0.13.02%') THEN 1
                                                    WHEN retorno.despesa LIKE ('3.1.9.1.13.14%') THEN 2
                                               END AS tipo_previdencia
                                          FROM empenho.fn_empenho_empenhado_pago_estornado ('".$this->getDado('stExercicio')."',                             
                                                                                            '',
                                                                                            '".$this->getDado('stDataInicial')."',
                                                                                            '".$this->getDado('stDataFinal')."',
                                                                                            '".$this->getDado('inCodEntidade')."',                                                                                
                                                                                            '',
                                                                                            '',
                                                                                            '',              
                                                                                            '',
                                                                                            '',                 
                                                                                            '',
                                                                                            '',                           
                                                                                            '',
                                                                                            '',                                  
                                                                                            '',
                                                                                            '',                                   
                                                                                            '',
                                                                                            '',
                                                                                            '',  
                                                                                            '',
                                                                                            ''
                                                                                           )
                                                                                AS retorno (                           
                                                                                            entidade            integer,                                                                                       
                                                                                            descricao_categoria varchar,                                                                                       
                                                                                            nom_tipo            varchar,                                                                                       
                                                                                            empenho             integer,                                                                                       
                                                                                            exercicio           char(4),                                                                                       
                                                                                            cgm                 integer,                                                                                       
                                                                                            razao_social        varchar,                                                                                       
                                                                                            cod_nota            integer,                                                                                       
                                                                                            data                text,                                                                                          
                                                                                            ordem               integer,                                                                                       
                                                                                            conta               integer,                                                                                       
                                                                                            nome_conta          varchar,                                                                                       
                                                                                            valor               numeric,                                                                                       
                                                                                            valor_estornado     numeric,                                                                                       
                                                                                            valor_liquido       numeric,                                                                                       
                                                                                            descricao           varchar,                                                                                       
                                                                                            recurso             varchar,                                                                                       
                                                                                            despesa             varchar(150)                                                                                   
                                                                                           )
      
                                         WHERE retorno.despesa LIKE ('3.1.9.0.13.02%')--RGPS 
                                            OR retorno.despesa LIKE ('3.1.9.1.13.14%')--RPPS 
                                      GROUP BY retorno.cgm, tipo_previdencia
                                    ) AS empenhos
                                   ON empenhos.tipo_previdencia = previdencia.cod_regime_previdencia

                             GROUP BY sw_cgm.nom_cgm
                                    , contrato.cod_contrato
                                    , previdencia.cod_regime_previdencia
                                    , previdencia.cod_vinculo
                                    , previdencia_previdencia.aliquota
                                    , periodo_movimentacao.dt_final
                                    , periodo_movimentacao.cod_periodo_movimentacao
                                    , CP.categoria
                                    , CP.maternidade
                                    , CP.desconto
                                    , CP.base
                                    , CP.familia
                                    , CP.folha
                                    , empenhos.valor_liquido
                                    , empenhos.data
                                    
                             ORDER BY sw_cgm.nom_cgm 
                                    , contrato.registro
                          ) AS servidor
                          
                 GROUP BY servidor.mes
                        , servidor.cod_regime_previdencia
                        , servidor.tipo_contribuicao
                        , servidor.aliquota
                        , servidor.dt_vencimento
                        , servidor.dt_repasse
                        , servidor.valor_contabilizado
                ";
        return $stSql;
    }

}
?>