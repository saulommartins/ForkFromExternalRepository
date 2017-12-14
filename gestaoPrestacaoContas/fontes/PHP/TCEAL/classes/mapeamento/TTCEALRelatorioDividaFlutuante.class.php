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
  * Página de Mapemanto Relatorio Divida Flutuante
  * Data de Criação: 17/03/2016
  * @author Desenvolvedor: Michel Teixeira
  * $Id: TTCEALRelatorioDividaFlutuante.class.php 64655 2016-03-18 16:50:13Z michel $
  *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php";

class TTCEALRelatorioDividaFlutuante extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    public function recuperaDepositosDividaFlutuante(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;    
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDepositosDividaFlutuante();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaDepositosDividaFlutuante()
    {
        $stSql = " 
                    SELECT cod_plano::VARCHAR || ' - ' || nom_conta AS nom_conta
                         , (SUM(vl_saldo_anterior)) AS vl_saldo_anterior
                         , (SUM(vl_saldo_creditos)) AS inscricao
                         , (SUM(vl_saldo_debitos))  AS baixa
                         , (SUM(vl_saldo_atual))    AS vl_saldo_atual
                      FROM tceal.relatorio_divida_flutuante_depositos( '".$this->getDado('exercicio')."'
                                                                     , 'AND cod_entidade IN (".$this->getDado('cod_entidade').")'
                                                                     , '".$this->getDado('data_inicial')."'
                                                                     , '".$this->getDado('data_final')."'
                                                                     , 'A'
                                                                     )
                                                                   AS( cod_estrutural     VARCHAR
                                                                     , cod_plano          INTEGER
                                                                     , nom_conta          VARCHAR
                                                                     , exercicio          CHAR(4)
                                                                     , nom_entidade       VARCHAR
                                                                     , cod_entidade       INTEGER
                                                                     , vl_saldo_anterior  NUMERIC
                                                                     , vl_saldo_debitos   NUMERIC
                                                                     , vl_saldo_creditos  NUMERIC
                                                                     , vl_saldo_atual     NUMERIC             
                                                                     )
                  GROUP BY nom_conta
                         , cod_plano
                ";
        return $stSql;
    }

    public function recuperaTotaisOrgao(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;    
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaTotaisOrgao();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaTotaisOrgao()
    {
        $stSql = "
                    SELECT cod_entidade                            
                         , nom_entidade
                         , SUM(saldo_anterior)        AS saldo_anterior
                         , SUM(inscricao)             AS inscricao
                         , SUM(restabelecimento)      AS restabelecimento
                         , SUM(baixa)                 AS baixa 
                         , SUM(cancelamentos)         AS cancelamentos 
                         , ABS(SUM(saldo_atual))      AS saldo_atual
                      FROM (
                             SELECT cod_entidade
                                  , nom_entidade
                                  , ABS(SUM(vl_saldo_anterior)) as saldo_anterior
                                  , ABS(SUM(vl_saldo_creditos)) as inscricao
                                  , SUM(0.00)                   as restabelecimento
                                  , ABS(SUM(vl_saldo_debitos))  as baixa
                                  , SUM(0.00)                   as cancelamentos
                                  , ABS(SUM(vl_saldo_atual))    as saldo_atual
                               FROM tceal.relatorio_divida_flutuante_depositos( '".$this->getDado('exercicio')."'
                                                                              ,'AND cod_entidade IN (".$this->getDado('cod_entidade').")'
                                                                              ,'".$this->getDado('data_inicial')."'
                                                                              ,'".$this->getDado('data_final')."'
                                                                              ,'A'
                                                                              )
                                                                           AS ( cod_estrutural     VARCHAR
                                                                              , cod_plano          INTEGER
                                                                              , nom_conta          VARCHAR
                                                                              , exercicio          CHAR(4)
                                                                              , nom_entidade       VARCHAR
                                                                              , cod_entidade       INTEGER
                                                                              , vl_saldo_anterior  NUMERIC
                                                                              , vl_saldo_debitos   NUMERIC
                                                                              , vl_saldo_creditos  NUMERIC
                                                                              , vl_saldo_atual     NUMERIC             
                                                                              )		    
                           GROUP BY cod_entidade
                                  , nom_entidade

                              UNION

                             SELECT rp.cod_entidade
                                  , sw_cgm.nom_cgm as nom_entidade
                                  , ABS(SUM(restos_processados)+SUM(restos_nao_processados))                                                                      as saldo_anterior
                                  , ABS(SUM(liquidado_nao_processados)+SUM(restos_processados_anulado)+SUM(liquidado_nao_processados_anulado))                    as inscricao
                                  , SUM(0.00)                                                                                                                     as restabelecimento
                                  , ABS((SUM(restos_processados_anulado) + SUM(pagamento))+SUM(liquidado_nao_processados)+SUM(liquidado_nao_processados_anulado)) as baixa
                                  , SUM(empenhado_anulado)                                                                                                        as cancelamentos
                                  , ABS( ( ( (SUM(restos_processados)+SUM(restos_nao_processados))
                                          + (SUM(liquidado_nao_processados)+SUM(restos_processados_anulado)+SUM(liquidado_nao_processados_anulado)))
                                          - ((SUM(restos_processados_anulado) + SUM(pagamento))+SUM(liquidado_nao_processados)+SUM(liquidado_nao_processados_anulado))
                                         )
                                         - (SUM(empenhado_anulado))
                                       )
                                    as saldo_atual
                               FROM tceal.relatorio_divida_flutuante_restos_pagar( '".$this->getDado('exercicio')."'
                                                                                 , '".$this->getDado('cod_entidade')."'
                                                                                 , '".$this->getDado('data_inicial')."'
                                                                                 , '".$this->getDado('data_final')."'
                                                                                 ) AS rp
                                                                                 ( cod_empenho                       INTEGER
                                                                                 , cod_entidade                      INTEGER
                                                                                 , exercicio                         VARCHAR
                                                                                 , empenhado                         NUMERIC(14,2)
                                                                                 , liquidado                         NUMERIC(14,2)
                                                                                 , restos_nao_processados            NUMERIC(14,2)
                                                                                 , restos_processados                NUMERIC(14,2)
                                                                                 , restos_processados_anulado        NUMERIC(14,2)
                                                                                 , liquidado_nao_processados         NUMERIC(14,2)
                                                                                 , empenhado_anulado                 NUMERIC(14,2)
                                                                                 , pagamento                         NUMERIC(14,2)
                                                                                 , liquidado_nao_processados_anulado NUMERIC(14,2)
                                                                                 )
                         INNER JOIN orcamento.entidade 
                                 ON entidade.cod_entidade = rp.cod_entidade
                                AND entidade.exercicio = rp.exercicio
                         INNER JOIN sw_cgm 
                                 ON sw_cgm.numcgm = entidade.numcgm

                           GROUP BY rp.cod_entidade
                                  , sw_cgm.nom_cgm

                              UNION

                             SELECT retorno.cod_entidade
                                  , sw_cgm.nom_cgm                           as nom_entidade
                                  , SUM(0.00)                                as saldo_anterior
                                  , ABS(SUM(aliquidar)+SUM(liquidadoapagar)) as inscricao
                                  , SUM(0.00)                                as restabelecimento
                                  , SUM(0.00)                                as baixa
                                  , SUM(0.00)                                as cancelamentos
                                  , ABS(SUM(aliquidar)+SUM(liquidadoapagar)) as saldo_atual
                               FROM empenho.fn_situacao_empenho('".$this->getDado('cod_entidade')."'
                                                               ,'".$this->getDado('exercicio')."'
                                                               ,'".$this->getDado('data_inicial')."'
                                                               ,'".$this->getDado('data_final')."'
                                                               ,'".$this->getDado('data_inicial')."'
                                                               ,'".$this->getDado('data_final')."'
                                                               ,'".$this->getDado('data_inicial')."'
                                                               ,'".$this->getDado('data_final')."'
                                                               ,'".$this->getDado('data_inicial')."'
                                                               ,'".$this->getDado('data_final')."'
                                                               ,'".$this->getDado('data_inicial')."'
                                                               ,'".$this->getDado('data_final')."'
                                                               ,'".$this->getDado('data_inicial')."'
                                                               ,'".$this->getDado('data_final')."'
                                                               ,'','','','','','','','','','','','',''
                                                               ) as retorno
                                                               (cod_empenho         integer,
                                                                cod_entidade        integer,
                                                                exercicio           char(4),
                                                                emissao             text,
                                                                credor              varchar,
                                                                empenhado           numeric,
                                                                anulado             numeric,
                                                                saldoempenhado      numeric,
                                                                liquidado           numeric,
                                                                pago                numeric,
                                                                aliquidar           numeric,
                                                                empenhadoapagar     numeric,
                                                                liquidadoapagar     numeric,
                                                                cod_recurso         integer
                                                               )
                         INNER JOIN orcamento.entidade 
                                 ON entidade.cod_entidade = retorno.cod_entidade
                                AND entidade.exercicio =retorno.exercicio
                         INNER JOIN sw_cgm 
                                 ON sw_cgm.numcgm = entidade.numcgm
                           GROUP BY retorno.cod_entidade
                                  , sw_cgm.nom_cgm 
                           ) AS total_orgao
                  GROUP BY cod_entidade
                         , nom_entidade
                  ORDER BY cod_entidade
            ";
        return $stSql;
    }
    
     public function recuperaRestosPagar(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;    
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRestosPagar();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaRestosPagar()
    {
        $stSql = "
                    SELECT *
                         , ( ( ( saldo_anterior_p + inscricao_p + restabelicimento_p ) - ( baixa_p ) ) - cancelamento_p )      AS saldo_atual_p
                         , ( ( ( saldo_anterior_np + inscricao_np + restabelicimento_np ) - ( baixa_np ) ) - cancelamento_np ) AS saldo_atual_np
                      FROM (
                             SELECT 'Restos a Pagar ' || rp.exercicio as titulo
                                  , sw_cgm.nom_cgm as entidade
                                  , rp.cod_entidade
                                  , rp.exercicio
                                  , SUM(0.00) as restabelicimento_p
                                  , SUM(0.00) as restabelicimento_np
                                  , SUM(restos_processados) as saldo_anterior_p
                                  , SUM(liquidado_nao_processados) as inscricao_p
                                  , SUM(0.00) as cancelamento_p
                                  , SUM(restos_processados_anulado) + SUM(pagamento) + SUM(liquidado_nao_processados_anulado) as baixa_p
                                  , SUM(restos_nao_processados) as saldo_anterior_np
                                  , SUM(restos_processados_anulado) + SUM(liquidado_nao_processados_anulado) as inscricao_np
                                  , SUM(empenhado_anulado) as cancelamento_np
                                  , SUM(liquidado_nao_processados) as baixa_np 
                               FROM tceal.relatorio_divida_flutuante_restos_pagar( '".$this->getDado('exercicio')."'
                                                                                 , '".$this->getDado('cod_entidade')."'
                                                                                 , '".$this->getDado('data_inicial')."'
                                                                                 , '".$this->getDado('data_final')."'
                                                                                 ) AS rp
                                                                                 ( cod_empenho                       INTEGER
                                                                                 , cod_entidade                      INTEGER
                                                                                 , exercicio                         VARCHAR
                                                                                 , empenhado                         NUMERIC(14,2)
                                                                                 , liquidado                         NUMERIC(14,2)
                                                                                 , restos_nao_processados            NUMERIC(14,2)
                                                                                 , restos_processados                NUMERIC(14,2)
                                                                                 , restos_processados_anulado        NUMERIC(14,2)
                                                                                 , liquidado_nao_processados         NUMERIC(14,2)
                                                                                 , empenhado_anulado                 NUMERIC(14,2)
                                                                                 , pagamento                         NUMERIC(14,2)
                                                                                 , liquidado_nao_processados_anulado NUMERIC(14,2)
                                                                                 )
                         INNER JOIN orcamento.entidade 
                                 ON entidade.cod_entidade = rp.cod_entidade
                                AND entidade.exercicio = rp.exercicio
                         INNER JOIN sw_cgm 
                                 ON sw_cgm.numcgm = entidade.numcgm

		                   GROUP BY rp.cod_entidade
                                  , rp.exercicio
                                  , titulo
                                  , sw_cgm.nom_cgm

		                   ORDER BY rp.cod_entidade
                                  , rp.exercicio
                           ) AS restos
                   ";
                        
        return $stSql;
    }
    public function recuperaBalanceteVerificacao(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;    
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaBalanceteVerificacao();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaBalanceteVerificacao()
    {
        $stSql = "
                    SELECT CASE WHEN cc.nom_cgm IS NULL THEN 
                                    cd.nom_cgm
                                ELSE 
                                    cc.nom_cgm 
                           END as nome_entidade
                         , retorno.cod_estrutural                                                       
                         , retorno.nivel                                                                
                         , retorno.nom_conta                  
                         , retorno.cod_sistema                                                        
                         , retorno.indicador_superavit                                              
                         , retorno.vl_saldo_anterior                                                   
                         , (retorno.vl_saldo_debitos)  * -1 as baixa
                         , (retorno.vl_saldo_creditos) * -1 as inscricao
                         , (retorno.vl_saldo_atual)    * -1  as vl_saldo_atual
                      FROM tceal.fn_balancete_verificacao( '".$this->getDado('exercicio')."'
                                                         , 'cod_entidade IN (".$this->getDado('cod_entidade').") and substr(cod_estrutural,1,5)=''2.1.8'' '
                                                         ,'".$this->getDado('data_inicial')."'
                                                         ,'".$this->getDado('data_final')."'
                                                         ,''
                                                         ) AS retorno
                                                         ( cod_estrutural varchar                                                      
                                                         ,nivel integer                                                               
                                                         ,nom_conta varchar                  
                                                         ,cod_sistema integer                                                         
                                                         ,indicador_superavit char(12)                                                
                                                         ,vl_saldo_anterior numeric                                                   
                                                         ,vl_saldo_debitos  numeric                                                   
                                                         ,vl_saldo_creditos numeric                                                   
                                                         ,vl_saldo_atual    numeric                                                   
                                                         )

                          LEFT JOIN ( SELECT plano_conta.cod_estrutural 
                                           , sw_cgm.nom_cgm
                                        FROM contabilidade.plano_conta
                                  INNER JOIN contabilidade.plano_analitica
                                          ON plano_analitica.exercicio = plano_conta.exercicio
                                         AND plano_analitica.cod_conta = plano_conta.cod_conta
                                  INNER JOIN contabilidade.conta_credito
                                          ON conta_credito.cod_plano = plano_analitica.cod_plano
                                         AND conta_credito.exercicio = plano_analitica.exercicio
                                  INNER JOIN orcamento.entidade 
                                          ON entidade.cod_entidade = conta_credito.cod_entidade
                                         AND entidade.exercicio = conta_credito.exercicio
                                  INNER JOIN sw_cgm 
                                          ON sw_cgm.numcgm = entidade.numcgm 
                                    GROUP BY plano_conta.cod_estrutural 
                                           , sw_cgm.nom_cgm
                                    ) as cc
                                 ON cc.cod_estrutural = retorno.cod_estrutural
                        
                          LEFT JOIN ( SELECT plano_conta.cod_estrutural 
                                           , sw_cgm.nom_cgm
                                        FROM contabilidade.plano_conta
                                  INNER JOIN contabilidade.plano_analitica
                                          ON plano_analitica.exercicio = plano_conta.exercicio
                                         AND plano_analitica.cod_conta = plano_conta.cod_conta
                                  INNER JOIN contabilidade.conta_debito
                                          ON conta_debito.cod_plano = plano_analitica.cod_plano
                                         AND conta_debito.exercicio = plano_analitica.exercicio
                                  INNER JOIN orcamento.entidade 
                                          ON entidade.cod_entidade = conta_debito.cod_entidade
                                         AND entidade.exercicio = conta_debito.exercicio
                                  INNER JOIN sw_cgm 
                                          ON sw_cgm.numcgm = entidade.numcgm 
                                    GROUP BY plano_conta.cod_estrutural 
                                           , sw_cgm.nom_cgm
                                    ) as cd
                                 ON cd.cod_estrutural = retorno.cod_estrutural

                              WHERE substr(retorno.cod_estrutural,1,9) >= '2.1.8' 
                                AND substr(retorno.cod_estrutural,1,9) <= '2.1.8.9.4'
            ";
        return $stSql;
    }
    
    public function __destruct(){}

}   
?>
