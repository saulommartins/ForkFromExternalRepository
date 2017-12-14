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
    * Classe de mapeamento para geração do arquivo TRB.txt

    $Id: TTGOTRB.class.php 65190 2016-04-29 19:36:51Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTGOTRB extends PERSISTENTE
{
    public function recuperaTransferenciasDebito(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaTransferenciasDebito",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function recuperaTransferenciasCredito(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaTransferenciasCredito",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaTransferenciasCredito()
    {
        $stSql .= " SELECT  '10' as tipo_registro                                                                      \n";
        $stSql .= "        ,num_orgao                                                                                  \n";
        $stSql .= "        , '01' as num_unidade                                                                       \n";

        $stSql .= "        ,CASE WHEN (SUBSTR(plano_conta_credito.cod_estrutural,1,12) = '1.1.1.1.1.01')               \n";
        $stSql .= "              THEN '999999999999'                                                                   \n";
        $stSql .= "              ELSE LTRIM(split_part(num_conta_corrente,'-',1),'0')                                  \n";
        $stSql .= "         END AS num_conta_corrente                                                                  \n";
        $stSql .= "        ,CASE WHEN (SUBSTR(plano_conta_credito.cod_estrutural,1,12) = '1.1.1.1.1.01')               \n";
        $stSql .= "              THEN '999'                                                                            \n";
        $stSql .= "              ELSE num_banco                                                                        \n";
        $stSql .= "         END                                                                                        \n";
        $stSql .= "        ,CASE WHEN (SUBSTR(plano_conta_credito.cod_estrutural,1,12) = '1.1.1.1.1.01')               \n";
        $stSql .= "              THEN '999999'                                                                         \n";
        $stSql .= "              ELSE LTRIM(REPLACE(num_agencia,'-',''),'0')                                           \n";
        $stSql .= "         END AS num_agencia                                                                         \n";
        $stSql .= "        ,CASE WHEN (SUBSTR(plano_conta_credito.cod_estrutural,1,12) = '1.1.1.1.1.01')               \n";
        $stSql .= "              THEN '03'                                                                             \n";
        $stSql .= "              WHEN (SUBSTR(plano_conta_credito.cod_estrutural,1,5) = '1.1.4')                       \n";
        $stSql .= "              THEN '02'                                                                             \n";
        $stSql .= "              ELSE '01'                                                                             \n";
        $stSql .= "         END AS tipo_conta                                                                          \n";

        $stSql .= "        ,LTRIM(split_part(num_conta_corrente,'-',2),'0')  AS digito                                 \n";
        $stSql .= "        ,transferencia.exercicio                                                                    \n";
        $stSql .= "        ,cod_fonte                                                                                  \n";
        $stSql .= "        ,'0' AS sequencial                                                                          \n";
        $stSql .= "        ,sum(transferencia.valor) as valor                                                          \n";
        $stSql .= "   FROM tesouraria.transferencia                                                                    \n";
        $stSql .= "   JOIN contabilidade.plano_analitica as plano_analitica_credito                                    \n";
        $stSql .= "     ON plano_analitica_credito.cod_plano = transferencia.cod_plano_credito                         \n";
        $stSql .= "    AND plano_analitica_credito.exercicio = transferencia.exercicio                                 \n";
        $stSql .= "                                                                                                    \n";
        $stSql .= "   JOIN contabilidade.plano_conta as plano_conta_credito                                            \n";
        $stSql .= "     ON plano_conta_credito.cod_conta = plano_analitica_credito.cod_conta                           \n";
        $stSql .= "    AND plano_conta_credito.exercicio = plano_analitica_credito.exercicio                           \n";
        $stSql .= "                                                                                                    \n";
        $stSql .= "   JOIN ( SELECT  plano_banco.cod_banco                                                             \n";
        $stSql .= "                 ,plano_banco.exercicio                                                             \n";
        $stSql .= "                 ,plano_analitica.cod_plano                                                         \n";
        $stSql .= "                 ,conta_corrente.num_conta_corrente as num_conta_corrente_debito                    \n";
        $stSql .= "            FROM contabilidade.conta_debito                                                         \n";
        $stSql .= "            JOIN contabilidade.plano_analitica                                                      \n";
        $stSql .= "              ON conta_debito.cod_plano = plano_analitica.cod_plano                                 \n";
        $stSql .= "             AND conta_debito.exercicio = plano_analitica.exercicio                                 \n";
        $stSql .= "                                                                                                    \n";
        $stSql .= "            JOIN contabilidade.plano_banco                                                          \n";
        $stSql .= "              ON plano_banco.cod_plano = plano_analitica.cod_plano                                  \n";
        $stSql .= "             AND plano_banco.exercicio = plano_analitica.exercicio                                  \n";
        $stSql .= "                                                                                                    \n";
        $stSql .= "            JOIN monetario.conta_corrente                                                           \n";
        $stSql .= "              ON conta_corrente.cod_banco          = plano_banco.cod_banco                          \n";
        $stSql .= "             AND conta_corrente.cod_agencia        = plano_banco.cod_agencia                        \n";
        $stSql .= "             AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente                 \n";
        $stSql .= "             AND plano_banco.exercicio = '".$this->getDado('exercicio')."'                          \n";
        $stSql .= "        GROUP BY 1,2,3,4                                                                            \n";
        $stSql .= "        ) AS conta_corrente_debito                                                                  \n";
        $stSql .= "     ON transferencia.cod_plano_debito  = conta_corrente_debito.cod_plano                           \n";
        $stSql .= "    AND transferencia.exercicio         = conta_corrente_debito.exercicio                           \n";
        $stSql .= "                                                                                                    \n";
        $stSql .= "   JOIN contabilidade.plano_banco as plano_banco_credito                                            \n";
        $stSql .= "     ON plano_banco_credito.cod_plano = transferencia.cod_plano_credito                             \n";
        $stSql .= "    AND plano_banco_credito.exercicio = transferencia.exercicio                                     \n";
        $stSql .= "                                                                                                    \n";
        $stSql .= "   JOIN monetario.conta_corrente as conta_corrente_credito                                          \n";
        $stSql .= "     ON conta_corrente_credito.cod_banco          = plano_banco_credito.cod_banco                   \n";
        $stSql .= "    AND conta_corrente_credito.cod_agencia        = plano_banco_credito.cod_agencia                 \n";
        $stSql .= "    AND conta_corrente_credito.cod_conta_corrente = plano_banco_credito.cod_conta_corrente          \n";
        $stSql .= "                                                                                                    \n";
        $stSql .= "   JOIN monetario.agencia as agencia_credito                                                        \n";
        $stSql .= "     ON agencia_credito.cod_banco = conta_corrente_credito.cod_banco                                \n";
        $stSql .= "    AND agencia_credito.cod_agencia = conta_corrente_credito.cod_agencia                            \n";
        $stSql .= "                                                                                                    \n";
        $stSql .= "   JOIN monetario.banco as banco_credito                                                            \n";
        $stSql .= "     ON banco_credito.cod_banco = agencia_credito.cod_banco                                         \n";
        $stSql .= "                                                                                                    \n";
        $stSql .= "   JOIN contabilidade.plano_recurso as plano_recurso_credito                                        \n";
        $stSql .= "     ON plano_recurso_credito.cod_plano = plano_analitica_credito.cod_plano                         \n";
        $stSql .= "    AND plano_recurso_credito.exercicio = plano_analitica_credito.exercicio                         \n";
        $stSql .= "                                                                                                    \n";
        $stSql .= "   JOIN orcamento.recurso as recurso_credito                                                        \n";
        $stSql .= "     ON recurso_credito.cod_recurso = plano_recurso_credito.cod_recurso                             \n";
        $stSql .= "    AND recurso_credito.exercicio = plano_recurso_credito.exercicio                                 \n";
        $stSql .= "                                                                                                    \n";
        $stSql .= "   JOIN tcmgo.orgao_plano_banco as orgao_plano_banco_credito                                        \n";
        $stSql .= "     ON orgao_plano_banco_credito.cod_plano = plano_banco_credito.cod_plano                         \n";
        $stSql .= "    AND orgao_plano_banco_credito.exercicio = plano_banco_credito.exercicio                         \n";
        $stSql .= "                                                                                                    \n";
        $stSql .= "  WHERE transferencia.cod_tipo in (3,4,5)                                                           \n";
        $stSql .= "    AND plano_banco_credito.exercicio = '".$this->getDado('exercicio')."'                           \n";
        return $stSql;
    }

    public function montaRecuperaTransferenciasDebito()
    {
        $stSql .= " SELECT  '11' as tipo_registro                                                                    \n";
        $stSql .= "        , '01' as num_unidade_origem                                                              \n";
        $stSql .= "        ,CASE WHEN (SUBSTR(plano_conta_credito.cod_estrutural,1,12) = '1.1.1.1.1.01')             \n";
        $stSql .= "              THEN '999999999999'                                                                 \n";
        $stSql .= "              ELSE LTRIM(split_part(conta_corrente_credito_1.num_conta_corrente,'-',1),'0')       \n";
        $stSql .= "         END AS num_conta_corrente_origem                                                         \n";
        $stSql .= "        ,CASE WHEN (SUBSTR(plano_conta_credito.cod_estrutural,1,12) = '1.1.1.1.1.01')             \n";
        $stSql .= "              THEN '999'                                                                          \n";
        $stSql .= "              ELSE banco_credito.num_banco                                                        \n";
        $stSql .= "         END AS num_banco_origem                                                                  \n";
        $stSql .= "        ,CASE WHEN (SUBSTR(plano_conta_credito.cod_estrutural,1,12) = '1.1.1.1.1.01')             \n";
        $stSql .= "              THEN '999999'                                                                       \n";
        $stSql .= "              ELSE LTRIM(REPLACE(agencia_credito.num_agencia,'-',''),'0')                         \n";
        $stSql .= "         END AS num_agencia_origem                                                                \n";
        $stSql .= "        ,CASE WHEN (SUBSTR(plano_conta_credito.cod_estrutural,1,12) = '1.1.1.1.1.01')             \n";
        $stSql .= "              THEN '03'                                                                           \n";
        $stSql .= "              WHEN (SUBSTR(plano_conta_credito.cod_estrutural,1,5) = '1.1.4')                     \n";
        $stSql .= "              THEN '02'                                                                           \n";
        $stSql .= "              ELSE '01'                                                                           \n";
        $stSql .= "         END AS tipo_conta_origem                                                                 \n";
        $stSql .= "        ,LTRIM(split_part(conta_corrente_credito_1.num_conta_corrente,'-',2),'0')  AS digito_origem \n";

        $stSql .= "        ,orgao_plano_banco_debito.num_orgao                                                           \n";
        $stSql .= "        , '01' as num_unidade                                                                         \n";
        $stSql .= "        ,CASE WHEN (SUBSTR(plano_conta_debito.cod_estrutural,1,12) = '1.1.1.1.1.01')                  \n";
        $stSql .= "              THEN '999999999999'                                                                     \n";
        $stSql .= "              ELSE LTRIM(split_part(conta_corrente_debito.num_conta_corrente,'-',1),'0')              \n";
        $stSql .= "         END AS num_conta_corrente                                                                    \n";
        $stSql .= "        ,CASE WHEN (SUBSTR(plano_conta_debito.cod_estrutural,1,12) = '1.1.1.1.1.01')                  \n";
        $stSql .= "              THEN '999'                                                                              \n";
        $stSql .= "              ELSE banco_debito.num_banco                                                             \n";
        $stSql .= "         END AS num_banco                                                                             \n";
        $stSql .= "        ,CASE WHEN (SUBSTR(plano_conta_debito.cod_estrutural,1,12) = '1.1.1.1.1.01')                  \n";
        $stSql .= "              THEN '999999'                                                                           \n";
        $stSql .= "              ELSE LTRIM(REPLACE(agencia_debito.num_agencia,'-',''),'0')                              \n";
        $stSql .= "         END AS num_agencia                                                                           \n";
        $stSql .= "        ,CASE WHEN (SUBSTR(plano_conta_debito.cod_estrutural,1,12) = '1.1.1.1.1.01')                  \n";
        $stSql .= "              THEN '03'                                                                               \n";
        $stSql .= "              WHEN (SUBSTR(plano_conta_debito.cod_estrutural,1,5) = '1.1.4')                          \n";
        $stSql .= "              THEN '02'                                                                               \n";
        $stSql .= "              ELSE '01'                                                                               \n";
        $stSql .= "         END AS tipo_conta                                                                            \n";

        $stSql .= "        ,LTRIM(split_part(conta_corrente_debito.num_conta_corrente,'-',2),'0')  AS digito             \n";
        $stSql .= "        ,transferencia.exercicio                                                                      \n";
        $stSql .= "        ,recurso_debito.cod_fonte                                                                     \n";
        $stSql .= "        ,'0' AS sequencial                                                                            \n";
        $stSql .= "        ,sum(transferencia.valor) as valor                                                            \n";
        $stSql .= "   FROM tesouraria.transferencia                                                                      \n";

        $stSql .= "   JOIN contabilidade.plano_analitica as plano_analitica_credito                                  \n";
        $stSql .= "     ON plano_analitica_credito.cod_plano = transferencia.cod_plano_credito                       \n";
        $stSql .= "    AND plano_analitica_credito.exercicio = transferencia.exercicio                               \n";
        $stSql .= "                                                                                                  \n";
        $stSql .= "   JOIN contabilidade.plano_conta as plano_conta_credito                                          \n";
        $stSql .= "     ON plano_conta_credito.cod_conta = plano_analitica_credito.cod_conta                         \n";
        $stSql .= "    AND plano_conta_credito.exercicio = plano_analitica_credito.exercicio                         \n";
        $stSql .= "   JOIN contabilidade.plano_banco as plano_banco_credito                                          \n";
        $stSql .= "     ON plano_banco_credito.cod_plano = transferencia.cod_plano_credito                           \n";
        $stSql .= "    AND plano_banco_credito.exercicio = transferencia.exercicio                                   \n";
        $stSql .= "                                                                                                  \n";
        $stSql .= "   JOIN monetario.conta_corrente as conta_corrente_credito_1                                      \n";
        $stSql .= "     ON conta_corrente_credito_1.cod_banco          = plano_banco_credito.cod_banco               \n";
        $stSql .= "    AND conta_corrente_credito_1.cod_agencia        = plano_banco_credito.cod_agencia             \n";
        $stSql .= "    AND conta_corrente_credito_1.cod_conta_corrente = plano_banco_credito.cod_conta_corrente      \n";
        $stSql .= "                                                                                                  \n";
        $stSql .= "   JOIN monetario.agencia as agencia_credito                                                      \n";
        $stSql .= "     ON agencia_credito.cod_banco = conta_corrente_credito_1.cod_banco                            \n";
        $stSql .= "    AND agencia_credito.cod_agencia = conta_corrente_credito_1.cod_agencia                        \n";
        $stSql .= "                                                                                                  \n";
        $stSql .= "   JOIN monetario.banco as banco_credito                                                          \n";
        $stSql .= "     ON banco_credito.cod_banco = agencia_credito.cod_banco                                       \n";
        $stSql .= "                                                                                                  \n";
        $stSql .= "   JOIN contabilidade.plano_recurso as plano_recurso_credito                                      \n";
        $stSql .= "     ON plano_recurso_credito.cod_plano = plano_analitica_credito.cod_plano                       \n";
        $stSql .= "    AND plano_recurso_credito.exercicio = plano_analitica_credito.exercicio                       \n";
        $stSql .= "                                                                                                  \n";
        $stSql .= "   JOIN orcamento.recurso as recurso_credito                                                      \n";
        $stSql .= "     ON recurso_credito.cod_recurso = plano_recurso_credito.cod_recurso                           \n";
        $stSql .= "    AND recurso_credito.exercicio = plano_recurso_credito.exercicio                               \n";
        $stSql .= "                                                                                                  \n";
        $stSql .= "   JOIN tcmgo.orgao_plano_banco as orgao_plano_banco_credito                                      \n";
        $stSql .= "     ON orgao_plano_banco_credito.cod_plano = plano_banco_credito.cod_plano                       \n";
        $stSql .= "    AND orgao_plano_banco_credito.exercicio = plano_banco_credito.exercicio                       \n";

        $stSql .= "   JOIN contabilidade.plano_analitica as plano_analitica_debito                                       \n";
        $stSql .= "     ON plano_analitica_debito.cod_plano = transferencia.cod_plano_debito                             \n";
        $stSql .= "    AND plano_analitica_debito.exercicio = transferencia.exercicio                                    \n";
        $stSql .= "                                                                                                      \n";
        $stSql .= "   JOIN contabilidade.plano_conta as plano_conta_debito                                               \n";
        $stSql .= "     ON plano_conta_debito.cod_conta = plano_analitica_debito.cod_conta                               \n";
        $stSql .= "    AND plano_conta_debito.exercicio = plano_analitica_debito.exercicio                               \n";
        $stSql .= "                                                                                                      \n";
        $stSql .= "   JOIN ( SELECT  plano_banco.cod_banco                                                               \n";
        $stSql .= "                 ,plano_banco.exercicio                                                               \n";
        $stSql .= "                 ,plano_analitica.cod_plano                                                           \n";
        $stSql .= "                 ,conta_corrente.num_conta_corrente as num_conta_corrente_credito                     \n";
        $stSql .= "            FROM contabilidade.conta_credito                                                          \n";
        $stSql .= "            JOIN contabilidade.plano_analitica                                                        \n";
        $stSql .= "              ON conta_credito.cod_plano = plano_analitica.cod_plano                                  \n";
        $stSql .= "             AND conta_credito.exercicio = plano_analitica.exercicio                                  \n";
        $stSql .= "                                                                                                      \n";
        $stSql .= "            JOIN contabilidade.plano_banco                                                            \n";
        $stSql .= "              ON plano_banco.cod_plano = plano_analitica.cod_plano                                    \n";
        $stSql .= "             AND plano_banco.exercicio = plano_analitica.exercicio                                    \n";
        $stSql .= "                                                                                                      \n";
        $stSql .= "            JOIN monetario.conta_corrente                                                             \n";
        $stSql .= "              ON conta_corrente.cod_banco          = plano_banco.cod_banco                            \n";
        $stSql .= "             AND conta_corrente.cod_agencia        = plano_banco.cod_agencia                          \n";
        $stSql .= "             AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente                   \n";
        $stSql .= "             AND plano_banco.exercicio = '".$this->getDado('exercicio')."'                            \n";
        $stSql .= "        GROUP BY 1,2,3,4                                                                              \n";
        $stSql .= "        ) AS conta_corrente_credito                                                                   \n";
        $stSql .= "     ON transferencia.cod_plano_credito = conta_corrente_credito.cod_plano                            \n";
        $stSql .= "    AND transferencia.exercicio         = conta_corrente_credito.exercicio                            \n";
        $stSql .= "                                                                                                      \n";
        $stSql .= "   JOIN contabilidade.plano_banco as plano_banco_debito                                               \n";
        $stSql .= "     ON plano_banco_debito.cod_plano = transferencia.cod_plano_debito                                 \n";
        $stSql .= "    AND plano_banco_debito.exercicio = transferencia.exercicio                                        \n";
        $stSql .= "                                                                                                      \n";
        $stSql .= "   JOIN monetario.conta_corrente as conta_corrente_debito                                             \n";
        $stSql .= "     ON conta_corrente_debito.cod_banco          = plano_banco_debito.cod_banco                       \n";
        $stSql .= "    AND conta_corrente_debito.cod_agencia        = plano_banco_debito.cod_agencia                     \n";
        $stSql .= "    AND conta_corrente_debito.cod_conta_corrente = plano_banco_debito.cod_conta_corrente              \n";
        $stSql .= "                                                                                                      \n";
        $stSql .= "   JOIN monetario.agencia as agencia_debito                                                           \n";
        $stSql .= "     ON agencia_debito.cod_banco = conta_corrente_debito.cod_banco                                    \n";
        $stSql .= "    AND agencia_debito.cod_agencia = conta_corrente_debito.cod_agencia                                \n";
        $stSql .= "                                                                                                      \n";
        $stSql .= "   JOIN monetario.banco as banco_debito                                                               \n";
        $stSql .= "     ON banco_debito.cod_banco = agencia_debito.cod_banco                                             \n";
        $stSql .= "                                                                                                      \n";
        $stSql .= "   JOIN contabilidade.plano_recurso as plano_recurso_debito                                           \n";
        $stSql .= "     ON plano_recurso_debito.cod_plano = plano_analitica_debito.cod_plano                             \n";
        $stSql .= "    AND plano_recurso_debito.exercicio = plano_analitica_debito.exercicio                             \n";
        $stSql .= "                                                                                                      \n";
        $stSql .= "   JOIN orcamento.recurso as recurso_debito                                                           \n";
        $stSql .= "     ON recurso_debito.cod_recurso = plano_recurso_debito.cod_recurso                                 \n";
        $stSql .= "    AND recurso_debito.exercicio = plano_recurso_debito.exercicio                                     \n";
        $stSql .= "                                                                                                      \n";
        $stSql .= "   JOIN tcmgo.orgao_plano_banco as orgao_plano_banco_debito                                           \n";
        $stSql .= "     ON orgao_plano_banco_debito.cod_plano = plano_banco_debito.cod_plano                             \n";
        $stSql .= "    AND orgao_plano_banco_debito.exercicio = plano_banco_debito.exercicio                             \n";
        $stSql .= "                                                                                                      \n";
        $stSql .= "  WHERE transferencia.cod_tipo in (3,4,5)                                                             \n";
        $stSql .= "    AND plano_banco_debito.exercicio = '".$this->getDado('exercicio')."'                              \n";

        return $stSql;
    }
}
