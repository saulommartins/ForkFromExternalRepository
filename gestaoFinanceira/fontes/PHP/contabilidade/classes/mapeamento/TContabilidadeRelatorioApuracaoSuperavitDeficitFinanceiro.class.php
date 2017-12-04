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
 * Classe de Mapeamento do Relatório de Apuração de Superavit/Deficit Financeiro
 * Data de Criação: 11/12/2015
 
 * @author Analista:      Valtair
 * @author Desenvolvedor: Franver Sarmento de Moraes
 
 * @ignore

 * $Id: TContabilidadeRelatorioApuracaoSuperavitDeficitFinanceiro.class.php 66680 2016-11-09 13:01:02Z franver $
 * $Date: 2016-11-09 11:01:02 -0200 (Wed, 09 Nov 2016) $
 * $Author: franver $
 * $Rev: 66680 $
*/

class TContabilidadeRelatorioApuracaoSuperavitDeficitFinanceiro extends Persistente
{
    
    public function __construct()
    {
        parent::Persistente();
    }
    
    public function recuperaApuracaoContabilidade(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
	    $obConexao   = new Conexao;
	    $rsRecordSet = new RecordSet;
	    $stSql = $this->montaRecuperaApuracaoContabilidade().$stFiltro.$stOrdem;
	    $this->stDebug = $stSql;
	    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }
    
    public function montaRecuperaApuracaoContabilidade()
    {
        $stSql = "
          -- CONSULTA PARA CONTABILIDADE ATIVO E PASSIVO
          SELECT valor_ativo AS valor_ativo
               , valor_passivo AS valor_passivo
               , CASE WHEN valor_ativo > valor_passivo
                      THEN valor_ativo - valor_passivo
                      ELSE 0.00
                  END AS superavit
               , CASE WHEN valor_passivo > valor_ativo
                      THEN valor_passivo - valor_ativo
                      ELSE 0.00
                  END AS deficit
            FROM (SELECT SUM(vl_saldo_atual) AS valor_ativo
                   FROM contabilidade.fn_rl_balancete_verificacao('".($this->getDado('exercicio') - 1)."',' cod_entidade IN  (".$this->getDado('cod_entidades').") AND cod_estrutural ILIKE ''1.%''  AND indicador_superavit = ''financeiro'' ','01/01/".($this->getDado('exercicio') - 1)."','31/12/".($this->getDado('exercicio') - 1)."','A'::CHAR)
                     as retorno( cod_estrutural      varchar
                               , nivel               integer
                               , nom_conta           varchar
                               , cod_sistema         integer
                               , indicador_superavit char(12)
                               , vl_saldo_anterior   numeric
                               , vl_saldo_debitos    numeric
                               , vl_saldo_creditos   numeric
                               , vl_saldo_atual      numeric
                               )
                               WHERE indicador_superavit = 'financeiro') AS ativo
                 
               , (SELECT ABS(SUM(vl_saldo_atual)) AS valor_passivo
                   FROM contabilidade.fn_rl_balancete_verificacao('".($this->getDado('exercicio') - 1)."',' cod_entidade IN  (".$this->getDado('cod_entidades').") AND cod_estrutural ILIKE ''2.%''  AND indicador_superavit = ''financeiro'' ','01/01/".($this->getDado('exercicio') - 1)."','31/12/".($this->getDado('exercicio') - 1)."','A'::CHAR)
                     as retorno( cod_estrutural      varchar
                               , nivel               integer
                               , nom_conta           varchar
                               , cod_sistema         integer
                               , indicador_superavit char(12)
                               , vl_saldo_anterior   numeric
                               , vl_saldo_debitos    numeric
                               , vl_saldo_creditos   numeric
                               , vl_saldo_atual      numeric
                               )
                               WHERE indicador_superavit = 'financeiro'
                       )  AS passivo
        ";
        
        return $stSql;
    }
    
    public function recuperaApuracaoExecucao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
	    $obConexao   = new Conexao;
	    $rsRecordSet = new RecordSet;
	    $stSql = $this->montaRecuperaApuracaoExecucao().$stFiltro.$stOrdem;
	    $this->stDebug = $stSql;        
	    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }
    
    public function montaRecuperaApuracaoExecucao()
    {
        $stSql ="
              SELECT cod_recurso
                   , nom_recurso
                   , valor_ativo
                   , valor_passivo
                   , CASE WHEN valor_ativo > valor_passivo
                          THEN valor_ativo - valor_passivo
                          ELSE 0.00
                      END AS superavit
                   , CASE WHEN valor_passivo > valor_ativo
                          THEN valor_passivo - valor_ativo
                          ELSE 0.00
                      END AS deficit
                FROM (
                      SELECT recurso.cod_recurso
                           , recurso.nom_recurso
                           , COALESCE(valor_ativo,0.00) AS valor_ativo
                           , COALESCE(valor_passivo,0.00) AS valor_passivo
                        FROM orcamento.recurso('".$this->getDado('exercicio')."')
                   LEFT JOIN (
                              SELECT cod_recurso
                                   , COALESCE(SUM(valor),0.00) AS valor_ativo
                                FROM (
                                      SELECT cod_recurso
                                           , COALESCE(vl_saldo_atual,0.00) AS valor
                                        FROM contabilidade.fn_rl_apuracao_superavit_defict_conta_banco
                                           ( '".($this->getDado('exercicio') - 1)."'
                                           , ' cod_entidade IN  ( ".$this->getDado('cod_entidades')." ) AND cod_estrutural NOT ILIKE (''1.1.3.8%'')  AND indicador_superavit = ''financeiro'' '
                                           , '01/01/".($this->getDado('exercicio') - 1)."'
                                           , '31/12/".($this->getDado('exercicio') - 1)."'
                                           , 'A'::CHAR
                                           )
                                          AS retorno
                                           ( cod_recurso         integer
                                           , indicador_superavit char(12)
                                           , vl_saldo_anterior   numeric
                                           , vl_saldo_debitos    numeric
                                           , vl_saldo_creditos   numeric
                                           , vl_saldo_atual      numeric
                                           )
                                       WHERE indicador_superavit = 'financeiro'
                                       UNION
                                      SELECT cod_recurso
                                           , COALESCE(SUM(vl_saldo_atual),0.00) AS valor
                                        FROM contabilidade.fn_rl_apuracao_superavit_defict_recurso
                                           ( '".($this->getDado('exercicio') - 1)."'
                                           , ' cod_entidade IN  (".$this->getDado('cod_entidades').") AND cod_estrutural ILIKE (''1.1.3.8%'')  AND indicador_superavit = ''financeiro'' '
                                           , '01/01/".($this->getDado('exercicio') - 1)."'
                                           , '31/12/".($this->getDado('exercicio') - 1)."'
                                           , 'A'::CHAR
                                           )
                                          AS retorno
                                           ( cod_recurso         integer
                                           , indicador_superavit char(12)
                                           , vl_saldo_anterior   numeric
                                           , vl_saldo_debitos    numeric
                                           , vl_saldo_creditos   numeric
                                           , vl_saldo_atual      numeric
                                           )
                                       WHERE indicador_superavit = 'financeiro'
                                GROUP BY cod_recurso
                                ORDER BY cod_recurso
                               --, indicador_superavit
                                     ) AS execucao
                            GROUP BY execucao.cod_recurso
                            ORDER BY execucao.cod_recurso
                             ) AS ativo
                          ON ativo.cod_recurso = recurso.cod_recurso
                          
                   LEFT JOIN (
                              SELECT cod_recurso
                                   , COALESCE(SUM(COALESCE(valor,0.00)),0.00) AS valor_passivo
                                FROM (
                                      SELECT cod_recurso
                                           , COALESCE(SUM(COALESCE(valor,0.00)),0.00) AS valor
                                        FROM contabilidade.apuracao_empenho_superavit_deficit
                                           ( '".($this->getDado('exercicio') - 1)."'
                                           , '01/01/'
                                           , '31/12/".($this->getDado('exercicio') - 1)."'
                                           , '".$this->getDado('cod_entidades')."'
                                           )
                                           as retorno
                                           ( exercicio    CHAR(4)
                                           , cod_empenho     TEXT
                                           , cod_entidade INTEGER
                                           , cod_recurso  INTEGER
                                           , valor        NUMERIC
                                           )
                                    GROUP BY cod_recurso
                                       UNION 
                                      SELECT cod_recurso
                                           , CASE WHEN COALESCE(SUM(COALESCE(vl_saldo_atual,0.00)),0.00) > 0.00
                                                  THEN (COALESCE(SUM(COALESCE(vl_saldo_atual,0.00)),0.00)*-1)
                                                  ELSE ABS(COALESCE(SUM(COALESCE(vl_saldo_atual,0.00)),0.00))
                                              END AS valor
                                        FROM contabilidade.fn_rl_apuracao_superavit_defict_recurso
                                             ( '".($this->getDado('exercicio') - 1)."'
                                             , ' cod_entidade IN  (".$this->getDado('cod_entidades').") AND cod_estrutural ILIKE ''2.1.8%'' AND indicador_superavit = ''financeiro'' '
                                             , '01/01/".($this->getDado('exercicio') - 1)."'
                                             , '31/12/".($this->getDado('exercicio') - 1)."'
                                             , 'A'::CHAR
                                             )
                                             AS retorno
                                             ( cod_recurso      integer
                                             , indicador_superavit char(12)
                                             , vl_saldo_anterior   numeric
                                             , vl_saldo_debitos    numeric
                                             , vl_saldo_creditos   numeric
                                             , vl_saldo_atual      numeric
                                             )
                                       WHERE indicador_superavit = 'financeiro'
                                    GROUP BY cod_recurso
                                     ) AS execucao
                              GROUP BY execucao.cod_recurso
                              ORDER BY execucao.cod_recurso
                             ) AS passivo
                          ON passivo.cod_recurso = recurso.cod_recurso
                    ORDER BY recurso.cod_recurso ASC
                     ) AS execucao
               WHERE NOT (valor_ativo = 0.00 AND valor_passivo = 0.00)
        ";
        return $stSql;
    }
	
	public function __destruct(){}
}
?>