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
    * Data de Criação   : 02/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Michel Teixeira
    $Id: TTCEPEPlanoConta.class.php 60579 2014-10-31 12:56:40Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEPlanoConta extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEPEPlanoConta()
    {
        parent::Persistente();
    }


    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContaBancaria.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaContaBancaria(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContaBancaria().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaContaBancaria()
    {
        $stSql  = "      SELECT lpad(regexp_replace(conta_corrente.num_conta_corrente,'[.|-]','','gi'),12,'0') as num_conta \n";
        $stSql .= "           , substr(banco.num_banco,1,3) as cod_banco                                                    \n";
        $stSql .= "           , lpad(regexp_replace(agencia.num_agencia,'[.|-]','','gi'),6,'0') as cod_agencia              \n";
        $stSql .= "           , plano_conta.nom_conta as titulo_conta                                       \n";
        $stSql .= "           , rpad(replace(plano_conta.cod_estrutural,'.',''),15,'0') as conta_contabil   \n";
        $stSql .= "           , PBT.cod_tipo_conta_banco as tipo_conta                                      \n";
        $stSql .= "        FROM contabilidade.plano_conta                                                   \n";
        $stSql .= "        JOIN contabilidade.plano_analitica                                               \n";
        $stSql .= "          ON plano_analitica.exercicio = plano_conta.exercicio                           \n";
        $stSql .= "         AND plano_analitica.cod_conta = plano_conta.cod_conta                           \n";
        $stSql .= "        JOIN (	SELECT cod_lote, tipo, sequencia, exercicio,  cod_entidade, cod_plano   \n";
        $stSql .= "                   FROM (                                                                \n";
        $stSql .= "                          SELECT *, 'C' AS cd                                            \n";
        $stSql .= "                            FROM contabilidade.conta_credito                             \n";
        $stSql .= "                           WHERE exercicio='".$this->getDado('stExercicio')."'           \n";
        $stSql .= "                       UNION ALL                                                         \n";
        $stSql .= "                          SELECT *,'D' AS cd                                             \n";
        $stSql .= "                            FROM contabilidade.conta_debito                              \n";
        $stSql .= "                           WHERE exercicio='".$this->getDado('stExercicio')."'           \n";
        $stSql .= "                        ) AS result                                                      \n";
        $stSql .= "               GROUP BY cod_lote                                                         \n";
        $stSql .= "                      , tipo                                                             \n";
        $stSql .= "                      , sequencia                                                        \n";
        $stSql .= "                      , exercicio                                                        \n";
        $stSql .= "                      , cod_entidade                                                     \n";
        $stSql .= "                      , cod_plano                                                        \n";
        $stSql .= "             ) AS cc                                                                     \n";
        $stSql .= "          ON plano_analitica.cod_plano    = cc.cod_plano                                 \n";
        $stSql .= "         AND plano_analitica.exercicio    = cc.exercicio                                 \n";
        $stSql .= "        JOIN contabilidade.lancamento AS la                                              \n";
        $stSql .= "          ON cc.cod_lote     = la.cod_lote                                               \n";
        $stSql .= "         AND cc.tipo         = la.tipo                                                   \n";
        $stSql .= "         AND cc.sequencia    = la.sequencia                                              \n";
        $stSql .= "         AND cc.exercicio    = la.exercicio                                              \n";
        $stSql .= "         AND cc.cod_entidade = la.cod_entidade                                           \n";
        $stSql .= "        JOIN contabilidade.lote AS lo                                                    \n";
        $stSql .= "          ON la.cod_lote     = lo.cod_lote                                               \n";
        $stSql .= "         AND la.exercicio    = lo.exercicio                                              \n";
        $stSql .= "         AND la.tipo         = lo.tipo                                                   \n";
        $stSql .= "         AND la.cod_entidade = lo.cod_entidade                                           \n";
        $stSql .= "        JOIN contabilidade.plano_banco                                                   \n";
        $stSql .= "          ON plano_banco.exercicio = plano_analitica.exercicio                           \n";
        $stSql .= "         AND plano_banco.cod_plano = plano_analitica.cod_plano                           \n";
        $stSql .= "        JOIN monetario.conta_corrente                                                    \n";
        $stSql .= "          ON plano_banco.cod_banco          = conta_corrente.cod_banco                   \n";
        $stSql .= "         AND plano_banco.cod_agencia        = conta_corrente.cod_agencia                 \n";
        $stSql .= "         AND plano_banco.cod_conta_corrente = conta_corrente.cod_conta_corrente          \n";
        $stSql .= "        JOIN monetario.agencia                                                           \n";
        $stSql .= "          ON conta_corrente.cod_banco   = agencia.cod_banco                              \n";
        $stSql .= "         AND conta_corrente.cod_agencia = agencia.cod_agencia                            \n";
        $stSql .= "        JOIN monetario.banco                                                             \n";
        $stSql .= "          ON agencia.cod_banco = banco.cod_banco                                         \n";
        $stSql .= "   LEFT JOIN tcepe.plano_banco_tipo_conta_banco AS PBT                                   \n";
        $stSql .= "          ON PBT.cod_plano=plano_banco.cod_plano                                         \n";
        $stSql .= "         AND PBT.exercicio=plano_banco.exercicio                                         \n";
        $stSql .= "   LEFT JOIN (                                                                           \n";
        $stSql .= "          SELECT conta_debito.cod_plano                                                  \n";
        $stSql .= "               , conta_debito.exercicio                                                  \n";
        $stSql .= "            FROM contabilidade.valor_lancamento                                          \n";
        $stSql .= "            JOIN contabilidade.conta_debito                                              \n";
        $stSql .= "              ON conta_debito.exercicio = valor_lancamento.exercicio                     \n";
        $stSql .= "             AND conta_debito.cod_entidade = valor_lancamento.cod_entidade               \n";
        $stSql .= "             AND conta_debito.tipo = valor_lancamento.tipo                               \n";
        $stSql .= "             AND conta_debito.cod_lote = valor_lancamento.cod_lote                       \n";
        $stSql .= "             AND conta_debito.sequencia = valor_lancamento.sequencia                     \n";
        $stSql .= "             AND conta_debito.tipo_valor = valor_lancamento.tipo_valor                   \n";
        $stSql .= "           WHERE valor_lancamento.tipo <> 'I'                                            \n";
        if (trim($this->getDado('stEntidades'))) {
            $stSql .= " AND     valor_lancamento.cod_entidade IN (".$this->getDado('stEntidades').")        \n";
        }
        if (trim($this->getDado('stExercicio'))) {
            $stSql .= " AND     valor_lancamento.exercicio = '".$this->getDado('stExercicio')."'            \n";
        }
        $stSql .= "        GROUP BY conta_debito.cod_plano                                                  \n";
        $stSql .= "               , conta_debito.exercicio                                                  \n";
        $stSql .= "        ORDER BY conta_debito.cod_plano                                                  \n";
        $stSql .= "           ) AS lancamento                                                               \n";
        $stSql .= "          ON lancamento.cod_plano = plano_analitica.cod_plano                            \n";
        $stSql .= "         AND lancamento.exercicio = plano_analitica.exercicio                            \n";
        # Para nao pegar bancos internos
        $stSql .= "       WHERE banco.num_banco <>  '999'                                                   \n";
        $stSql .= "         AND banco.num_banco <>  '000'                                                   \n";
        if (trim($this->getDado('stEntidades'))) {
            $stSql .= " AND     plano_banco.cod_entidade IN (".$this->getDado('stEntidades').")             \n";
        }
        if (trim($this->getDado('stExercicio'))) {
            $stSql .= " AND     plano_conta.exercicio = '".$this->getDado('stExercicio')."'                 \n";
        }
        if ($this->getDado('dtInicial')&&$this->getDado('dtFinal')) {
            $stSql .= " AND lo.dt_lote BETWEEN to_date( '".$this->getDado('dtInicial')."' , 'dd/mm/yyyy' )  \n";
            $stSql .= "                    AND to_date( '".$this->getDado('dtFinal')."' , 'dd/mm/yyyy' )    \n";
        }

        $stSql .= "    GROUP BY conta_corrente.num_conta_corrente                                           \n";
        $stSql .= "           , banco.num_banco                                                             \n";                           
        $stSql .= "           , agencia.num_agencia                                                         \n";                       
        $stSql .= "           , plano_conta.nom_conta                                                       \n";                                
        $stSql .= "           , plano_conta.cod_estrutural                                                  \n";
        $stSql .= "           , PBT.cod_tipo_conta_banco                                                    \n";
        $stSql .= "    ORDER BY plano_conta.cod_estrutural                                                  \n";
    
        return $stSql;
    }

}
