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
    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação: 30/01/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62622 $
    $Name$
    $Author: carlos.silva $
    $Date: 2015-05-25 16:52:18 -0300 (Mon, 25 May 2015) $

    * Casos de uso: uc-06.04.00
*/

/*
$Log$
Revision 1.4  2007/06/12 20:44:11  hboaventura
inclusão dos casos de uso uc-06.04.00

Revision 1.3  2007/06/12 18:34:05  hboaventura
inclusão dos casos de uso uc-06.04.00

Revision 1.2  2007/05/18 14:54:16  bruce
*** empty log message ***

Revision 1.1  2007/05/08 14:48:57  bruce
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGOAFD extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/
    
    function recuperaContasBancarias(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaContasBancarias().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "" );
    }

    public function montaRecuperaContasBancarias()
    {
            $stSql = " 
               SELECT '10' AS tipo_registro
                    , ( SELECT SUBSTR(valor,1,1)
                                FROM administracao.configuracao_entidade 
                                WHERE configuracao_entidade.exercicio = plano_banco.exercicio
                                AND configuracao_entidade.cod_entidade = plano_banco.cod_entidade
                                AND  parametro = 'tc_ug_orgaounidade'
                      ) as cod_orgao
                    , banco.num_banco AS banco
                    , REPLACE(agencia.num_agencia,'-','') AS agencia
                    , REPLACE(ltrim(split_part(num_conta_corrente,'-',1),'0'),'.','') AS conta_corrente
                    , ltrim(split_part(num_conta_corrente,'-',2),'0') AS conta_corrente_dv
                    , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') 
                           THEN '03'
                           WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') 
                           THEN '02'
                           ELSE '01'
                      END as tipo_conta
                    
                    , ABS(pl_balancete_verificacao.vl_saldo_anterior) AS saldo_inicial
                    , ABS(pl_balancete_verificacao.vl_saldo_creditos) AS vl_saidas
                    , ABS(pl_balancete_verificacao.vl_saldo_debitos)  AS vl_entradas
                    , ABS(pl_balancete_verificacao.vl_saldo_atual)    AS saldo_final
                       
                 FROM  tcmgo.orgao_plano_banco
           
           INNER JOIN  contabilidade.plano_banco
                   ON  plano_banco.cod_plano = orgao_plano_banco.cod_plano
                  AND  plano_banco.exercicio = orgao_plano_banco.exercicio
                  
           INNER JOIN  contabilidade.plano_analitica
                   ON  plano_analitica.cod_plano = plano_banco.cod_plano
                  AND  plano_analitica.exercicio = plano_banco.exercicio
                  
           INNER JOIN  contabilidade.plano_conta
                   ON  plano_conta.cod_conta = plano_analitica.cod_conta
                  AND  plano_conta.exercicio = plano_analitica.exercicio
                  
          INNER JOIN (
                        SELECT cod_estrutural  
                              , vl_saldo_anterior
                              , vl_saldo_debitos 
                              , vl_saldo_creditos
                              , vl_saldo_atual   
                              , '".$this->getDado('exercicio')."'::varchar AS exercicio
                          
                         FROM contabilidade.fn_rl_balancete_verificacao('".$this->getDado('exercicio')."','cod_entidade IN (".$this->getDado('cod_entidade') .") ','01/01/".$this->getDado('exercicio')."','31/12/".$this->getDado('exercicio')."','A'::CHAR)
                           AS retorno( cod_estrutural        VARCHAR
                                ,nivel               INTEGER                                                        
                                ,nom_conta           VARCHAR
                                ,cod_sistema  	     INTEGER
                                ,indicador_superavit CHAR(12)                                                
                                ,vl_saldo_anterior   NUMERIC
                                ,vl_saldo_debitos    NUMERIC
                                ,vl_saldo_creditos   NUMERIC
                                ,vl_saldo_atual      NUMERIC
                            )  
                      ) AS pl_balancete_verificacao
                   ON pl_balancete_verificacao.cod_estrutural = plano_conta.cod_estrutural
                  AND pl_balancete_verificacao.exercicio      = plano_conta.exercicio  
                  
           INNER JOIN  monetario.agencia
                   ON  agencia.cod_banco   = plano_banco.cod_banco
                  AND  agencia.cod_agencia = plano_banco.cod_agencia
            
            LEFT JOIN  monetario.conta_corrente
                   ON  conta_corrente.cod_banco          = plano_banco.cod_banco
                  AND  conta_corrente.cod_agencia        = plano_banco.cod_agencia
                  AND  conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                                              
           INNER JOIN  monetario.banco
                   ON  banco.cod_banco = plano_banco.cod_banco
                
                WHERE  plano_banco.exercicio = '".$this->getDado('exercicio')."'
                  AND  plano_banco.cod_entidade IN (".$this->getDado('cod_entidade') .") ";
                 
        return $stSql;
    }
        
    function recuperaContasBancariasFonteRecurso(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaContasBancariasFonteRecurso().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "" );
    }
        
    public function montaRecuperaContasBancariasFonteRecurso()
    {  
       $stSql = "
               SELECT '11' AS tipo_registro
                    , ( SELECT SUBSTR(valor,1,1)
                                FROM administracao.configuracao_entidade
                                WHERE configuracao_entidade.exercicio = plano_banco.exercicio
                                AND configuracao_entidade.cod_entidade = plano_banco.cod_entidade
                                AND  parametro = 'tc_ug_orgaounidade'
                      ) as cod_orgao
                    , banco.num_banco AS banco
                    , REPLACE(agencia.num_agencia,'-','') AS agencia
                    , REPLACE(ltrim(split_part(num_conta_corrente,'-',1),'0'),'.','') AS conta_corrente
                    , ltrim(split_part(num_conta_corrente,'-',2),'0') AS conta_corrente_dv
                    , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') 
                           THEN '03'
                           WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') 
                           THEN '02'
                           ELSE '01'
                      END as tipo_conta
                    , recurso.cod_recurso AS cod_fonte_recurso

                    , ABS(pl_balancete_verificacao.vl_saldo_anterior) AS saldo_inicial
                    , ABS(pl_balancete_verificacao.vl_saldo_creditos) AS vl_saidas
                    , ABS(pl_balancete_verificacao.vl_saldo_debitos)  AS vl_entradas
                    , ABS(pl_balancete_verificacao.vl_saldo_atual)    AS saldo_final
                       
                 FROM  tcmgo.orgao_plano_banco
           
           INNER JOIN  contabilidade.plano_banco
                   ON  plano_banco.cod_plano = orgao_plano_banco.cod_plano
                  AND  plano_banco.exercicio = orgao_plano_banco.exercicio
                  
           INNER JOIN  contabilidade.plano_analitica
                   ON  plano_analitica.cod_plano = plano_banco.cod_plano
                  AND  plano_analitica.exercicio = plano_banco.exercicio
                  
           INNER JOIN  contabilidade.plano_conta
                   ON  plano_conta.cod_conta = plano_analitica.cod_conta
                  AND  plano_conta.exercicio = plano_analitica.exercicio
                  
          INNER JOIN (
                        SELECT cod_estrutural  
                              , vl_saldo_anterior
                              , vl_saldo_debitos 
                              , vl_saldo_creditos
                              , vl_saldo_atual   
                              , '".$this->getDado('exercicio')."'::varchar AS exercicio
                          
                         FROM contabilidade.fn_rl_balancete_verificacao('".$this->getDado('exercicio')."','cod_entidade IN (".$this->getDado('cod_entidade') .") ','01/01/".$this->getDado('exercicio')."','31/12/".$this->getDado('exercicio')."','A'::CHAR)
                           AS retorno( cod_estrutural        VARCHAR
                                ,nivel               INTEGER                                                        
                                ,nom_conta           VARCHAR
                                ,cod_sistema  	     INTEGER
                                ,indicador_superavit CHAR(12)                                                
                                ,vl_saldo_anterior   NUMERIC
                                ,vl_saldo_debitos    NUMERIC
                                ,vl_saldo_creditos   NUMERIC
                                ,vl_saldo_atual      NUMERIC
                            )  
                      ) AS pl_balancete_verificacao
                   ON pl_balancete_verificacao.cod_estrutural = plano_conta.cod_estrutural
                  AND pl_balancete_verificacao.exercicio      = plano_conta.exercicio                  
                  
           INNER JOIN contabilidade.plano_recurso
                   ON plano_recurso.cod_plano = plano_analitica.cod_plano
                  AND plano_recurso.exercicio = plano_analitica.exercicio
        
           INNER JOIN orcamento.recurso
                   ON recurso.cod_recurso = plano_recurso.cod_recurso
                  AND recurso.exercicio   = plano_recurso.exercicio
                  
           INNER JOIN  monetario.agencia
                   ON  agencia.cod_banco   = plano_banco.cod_banco
                  AND  agencia.cod_agencia = plano_banco.cod_agencia
            
            LEFT JOIN  monetario.conta_corrente
                   ON  conta_corrente.cod_banco          = plano_banco.cod_banco
                  AND  conta_corrente.cod_agencia        = plano_banco.cod_agencia
                  AND  conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                                              
           INNER JOIN  monetario.banco
                   ON  banco.cod_banco = plano_banco.cod_banco
                
                WHERE  plano_banco.exercicio    = '".$this->getDado('exercicio')."'
                  AND  plano_banco.cod_entidade IN (".$this->getDado('cod_entidade') .") ";
                  
        return $stSql;
    }
}

?>