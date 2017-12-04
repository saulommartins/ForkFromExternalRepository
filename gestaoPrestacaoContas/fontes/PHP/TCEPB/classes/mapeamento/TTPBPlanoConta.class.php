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
    * Data de Criação: 05/02/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.7  2007/05/11 21:34:08  hboaventura
Arquivos para geração do TCEPB

Revision 1.6  2007/05/11 20:23:14  hboaventura
Arquivos para geração do TCEPB

Revision 1.5  2007/05/10 21:39:47  hboaventura
Arquivos para geração do TCEPB

Revision 1.4  2007/04/28 01:59:05  diego
correções de sql

Revision 1.3  2007/04/23 15:28:35  rodrigo_sr
uc-06.03.00

Revision 1.2  2007/04/11 00:09:20  diego
Correções da última modificação de formatação dos arquivos.

Revision 1.1  2007/02/15 21:51:53  diego
Primeira versão...

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php" );

/**
  *
  * Data de Criação: 05/02/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTPBPlanoConta extends TContabilidadePlanoConta
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBPlanoConta()
{
    parent::TContabilidadePlanoConta();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaTodos()
{
    $stSql = "SELECT *
                FROM(SELECT  pc.exercicio
                          ,  SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,15) as estrutural
                          ,  RPAD(SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,9),9,'0') as cod_conta_reduzido
                          ,  pc.nom_conta
                          ,  pca.cod_plano
                            /*,contabilidade.fn_tipo_conta_plano(exercicio,cod_estrutural) as tipo*/
                          ,  CASE WHEN ( EXISTS ( SELECT  1
                                                    FROM  contabilidade.plano_analitica
                                                   WHERE  plano_analitica.exercicio = pb.exercicio
                                                     AND  plano_analitica.cod_plano = pb.cod_plano
                                                 )
                                         ) THEN 1
                                  ELSE
                                   CASE WHEN SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,14) like '4%'      then 2 /*Receita*/
                                        WHEN SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,14) like '3%'      then 3 /*Despesa*/
                                        WHEN SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,14) like '9%'      then 4
                                        WHEN SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,14) like '112%'    then 5
                                        WHEN SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,14) like '211%'    then 6 /*Despesa-Extra*/
                                        WHEN SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,14) like '212%'    then 6 /*Despesa-Extra*/
                                        WHEN SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,14) like '512%'    then 7
                                        WHEN SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,14) like '612%'    then 7
                                   ELSE NULL
                                   END
                              END as tipo_conta
                           ,  CASE WHEN pc.cod_sistema = 1 then 2
                                   WHEN pc.cod_sistema = 2 then 3
                                   WHEN pc.cod_sistema = 3 then 1
                                   WHEN pc.cod_sistema = 4 then 4
                                   ELSE 1
                              END AS cod_sistema
                        FROM  contabilidade.plano_conta pc
                  INNER JOIN  contabilidade.plano_analitica AS pca
                          ON  (pca.exercicio = pc.exercicio
                         and  pca.cod_conta = pc.cod_conta)
                   LEFT JOIN  contabilidade.plano_banco AS pb
                          ON  (pca.exercicio = pb.exercicio
                         AND  pca.cod_plano = pb.cod_plano)

                        WHERE    pc.exercicio = '".$this->getDado('exercicio')."'
                        ORDER BY pc.exercicio, SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,15),cod_sistema
                    )  AS tabelaElencoContabil
               WHERE  tipo_conta IS NOT NULL
            GROUP BY  exercicio
                   ,  estrutural
                   ,  cod_plano
                   ,  cod_conta_reduzido
                   ,  nom_conta
                   ,  tipo_conta
                   ,  cod_sistema
            ORDER BY  estrutural
  ";

    return $stSql;
}

function recuperaContaBancaria(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaContaBancaria().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContaBancaria()
{
    $stSql  = "      SELECT trim(upper(replace(conta_corrente.num_conta_corrente,'-',''))) as conta_corrente \n";
    $stSql .= "           , CASE WHEN (lancamento.cod_plano IS NOT NULL) THEN   \n";
    $stSql .= "                    1                                            \n";
    $stSql .= "                  ELSE                                           \n";
    $stSql .= "                    0                                            \n";
    $stSql .= "             END AS situacao                                     \n";
    $stSql .= "           , substr(banco.num_banco,1,3) as banco                \n";
    $stSql .= "           , lpad(trim(replace(agencia.num_agencia,'-','')),6,'0') as cod_agencia \n";
    $stSql .= "           , plano_conta.nom_conta                               \n";
    $stSql .= "           , rpad(replace(plano_conta.cod_estrutural,'.',''),15,'0') as estrutural \n";
    $stSql .= "        FROM contabilidade.plano_conta                           \n";
    $stSql .= "        JOIN contabilidade.plano_analitica                       \n";
    $stSql .= "          ON plano_analitica.exercicio = plano_conta.exercicio   \n";
    $stSql .= "         AND plano_analitica.cod_conta = plano_conta.cod_conta   \n";
    $stSql .= "        JOIN contabilidade.plano_banco                           \n";
    $stSql .= "          ON plano_banco.exercicio = plano_analitica.exercicio   \n";
    $stSql .= "         AND plano_banco.cod_plano = plano_analitica.cod_plano   \n";
    $stSql .= "        JOIN monetario.conta_corrente                            \n";
    $stSql .= "          ON plano_banco.cod_banco          = conta_corrente.cod_banco \n";
    $stSql .= "         AND plano_banco.cod_agencia        = conta_corrente.cod_agencia \n";
    $stSql .= "         AND plano_banco.cod_conta_corrente = conta_corrente.cod_conta_corrente \n";
    $stSql .= "        JOIN monetario.agencia                                   \n";
    $stSql .= "          ON conta_corrente.cod_banco   = agencia.cod_banco      \n";
    $stSql .= "         AND conta_corrente.cod_agencia = agencia.cod_agencia    \n";
    $stSql .= "        JOIN monetario.banco                                     \n";
    $stSql .= "          ON agencia.cod_banco = banco.cod_banco                 \n";
    $stSql .= "   LEFT JOIN (                                                   \n";
    $stSql .= "          SELECT conta_debito.cod_plano                          \n";
    $stSql .= "               , conta_debito.exercicio                          \n";
    $stSql .= "            FROM contabilidade.valor_lancamento                  \n";
    $stSql .= "            JOIN contabilidade.conta_debito                      \n";
    $stSql .= "              ON conta_debito.exercicio = valor_lancamento.exercicio \n";
    $stSql .= "             AND conta_debito.cod_entidade = valor_lancamento.cod_entidade \n";
    $stSql .= "             AND conta_debito.tipo = valor_lancamento.tipo       \n";
    $stSql .= "             AND conta_debito.cod_lote = valor_lancamento.cod_lote \n";
    $stSql .= "             AND conta_debito.sequencia = valor_lancamento.sequencia \n";
    $stSql .= "             AND conta_debito.tipo_valor = valor_lancamento.tipo_valor \n";
    $stSql .= "           WHERE valor_lancamento.tipo <> 'I'                   \n";
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND     valor_lancamento.cod_entidade IN (".$this->getDado('stEntidades').") \n";
    }
    if (trim($this->getDado('stExercicio'))) {
        $stSql .= " AND     valor_lancamento.exercicio = '".$this->getDado('stExercicio')."' \n";
    }
    $stSql .= "        GROUP BY conta_debito.cod_plano                          \n";
    $stSql .= "               , conta_debito.exercicio                          \n";
    $stSql .= "        ORDER BY conta_debito.cod_plano                          \n";
    $stSql .= "           ) AS lancamento                                       \n";
    $stSql .= "          ON lancamento.cod_plano = plano_analitica.cod_plano    \n";
    $stSql .= "         AND lancamento.exercicio = plano_analitica.exercicio    \n";
    # temporario, para nao pegar bancos internos
    $stSql .= "       WHERE banco.num_banco <>  '999'                           \n";
    $stSql .= "         AND banco.num_banco <>  '000'                           \n";
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND     plano_banco.cod_entidade IN (".$this->getDado('stEntidades').") \n";
    }
    if (trim($this->getDado('stExercicio'))) {
        $stSql .= " AND     plano_conta.exercicio = '".$this->getDado('stExercicio')."' \n";
    }
    $stSql .= "    ORDER BY plano_conta.cod_estrutural                          \n";

    return $stSql;
}

function recuperaContaBancariaFontePagadora(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaContaBancariaFontePagadora().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContaBancariaFontePagadora()
{
    $stSql  = "      SELECT conta_corrente.num_conta_corrente                   \n";
    $stSql .= "           , conta_corrente.cod_conta_corrente                   \n";
    $stSql .= "           , banco.num_banco                                     \n";
    $stSql .= "           , banco.cod_banco                                     \n";
    $stSql .= "           , agencia.num_agencia                                 \n";
    $stSql .= "           , agencia.cod_agencia                                 \n";
    $stSql .= "           , plano_conta.nom_conta                               \n";
    $stSql .= "           , rpad(replace(plano_conta.cod_estrutural,'.',''),15,'0') as estrutural \n";
    $stSql .= "           , relacao_conta_corrente_fonte_pagadora.cod_tipo      \n";
    $stSql .= "           , relacao_conta_corrente_fonte_pagadora.exercicio     \n";
    $stSql .= "        FROM contabilidade.plano_conta                           \n";
    $stSql .= "        JOIN contabilidade.plano_analitica                       \n";
    $stSql .= "          ON plano_analitica.exercicio = plano_conta.exercicio   \n";
    $stSql .= "         AND plano_analitica.cod_conta = plano_conta.cod_conta   \n";
    $stSql .= "        JOIN contabilidade.plano_banco                           \n";
    $stSql .= "          ON plano_banco.exercicio = plano_analitica.exercicio   \n";
    $stSql .= "         AND plano_banco.cod_plano = plano_analitica.cod_plano   \n";
    $stSql .= "        JOIN monetario.conta_corrente                            \n";
    $stSql .= "          ON plano_banco.cod_banco          = conta_corrente.cod_banco \n";
    $stSql .= "         AND plano_banco.cod_agencia        = conta_corrente.cod_agencia \n";
    $stSql .= "         AND plano_banco.cod_conta_corrente = conta_corrente.cod_conta_corrente \n";
    $stSql .= "        JOIN monetario.agencia                                   \n";
    $stSql .= "          ON conta_corrente.cod_banco   = agencia.cod_banco      \n";
    $stSql .= "         AND conta_corrente.cod_agencia = agencia.cod_agencia    \n";
    $stSql .= "        JOIN monetario.banco                                     \n";
    $stSql .= "          ON agencia.cod_banco = banco.cod_banco                 \n";
    $stSql .= "   LEFT JOIN (                                                   \n";
    $stSql .= "          SELECT conta_debito.cod_plano                          \n";
    $stSql .= "               , conta_debito.exercicio                          \n";
    $stSql .= "            FROM contabilidade.valor_lancamento                  \n";
    $stSql .= "            JOIN contabilidade.conta_debito                      \n";
    $stSql .= "              ON conta_debito.exercicio = valor_lancamento.exercicio \n";
    $stSql .= "             AND conta_debito.cod_entidade = valor_lancamento.cod_entidade \n";
    $stSql .= "             AND conta_debito.tipo = valor_lancamento.tipo       \n";
    $stSql .= "             AND conta_debito.cod_lote = valor_lancamento.cod_lote \n";
    $stSql .= "             AND conta_debito.sequencia = valor_lancamento.sequencia \n";
    $stSql .= "             AND conta_debito.tipo_valor = valor_lancamento.tipo_valor \n";
    $stSql .= "           WHERE valor_lancamento.tipo <> 'I'                   \n";
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND     valor_lancamento.cod_entidade IN (".$this->getDado('stEntidades').") \n";
    }
    if (trim($this->getDado('stExercicio'))) {
        $stSql .= " AND     valor_lancamento.exercicio = '".$this->getDado('stExercicio')."' \n";
    }
    $stSql .= "        GROUP BY conta_debito.cod_plano                          \n";
    $stSql .= "               , conta_debito.exercicio                          \n";
    $stSql .= "        ORDER BY conta_debito.cod_plano                          \n";
    $stSql .= "           ) AS lancamento                                       \n";
    $stSql .= "          ON lancamento.cod_plano = plano_analitica.cod_plano    \n";
    $stSql .= "         AND lancamento.exercicio = plano_analitica.exercicio    \n";
    $stSql .= "   LEFT JOIN tcepb.relacao_conta_corrente_fonte_pagadora         \n";
    $stSql .= "          ON relacao_conta_corrente_fonte_pagadora.cod_banco = conta_corrente.cod_banco \n";
    $stSql .= "         AND relacao_conta_corrente_fonte_pagadora.cod_agencia = conta_corrente.cod_agencia \n";
    $stSql .= "         AND relacao_conta_corrente_fonte_pagadora.cod_conta_corrente = conta_corrente.cod_conta_corrente \n";
    if ($this->getDado('arCodTipo') != '') {
        $stSql .=  "    AND relacao_conta_corrente_fonte_pagadora.cod_tipo= ".$this->getDado('arCodTipo')."    \n";
        $stSql .=  "    AND relacao_conta_corrente_fonte_pagadora.exercicio= '".$this->getDado('stExercicio')."'    \n";
    }
    # temporario, para nao pegar bancos internos
    $stSql .= "       WHERE banco.num_banco <>  '999'                           \n";
    $stSql .= "         AND banco.num_banco <>  '000'                           \n";
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND     plano_banco.cod_entidade IN (".$this->getDado('stEntidades').") \n";
    }
    if (trim($this->getDado('stExercicio'))) {
        $stSql .= " AND     plano_conta.exercicio = '".$this->getDado('stExercicio')."' \n";
    }
    if (trim($this->getDado('arContasCorrente'))) {
        $stSql .= " AND    conta_corrente.cod_conta_corrente IN (".$this->getDado('arContasCorrente').") \n";
    }
    $stSql.= "   GROUP BY conta_corrente.cod_conta_corrente                                                \n";
    $stSql.= "                  ,conta_corrente.num_conta_corrente                                         \n";
    $stSql.= "                  ,banco.num_banco                                                           \n";
    $stSql.= "                  ,banco.cod_banco                                                           \n";
    $stSql.= "                  ,agencia.num_agencia                                                       \n";
    $stSql.= "                  ,agencia.cod_agencia                                                       \n";
    $stSql.= "                  ,plano_conta.nom_conta                                                     \n";
    $stSql.= "                  ,plano_conta.cod_estrutural                                                \n";
    $stSql.= "                  ,relacao_conta_corrente_fonte_pagadora.exercicio                           \n";
    $stSql.= "                  ,relacao_conta_corrente_fonte_pagadora.cod_tipo                            \n";
    $stSql.= "    ORDER BY plano_conta.cod_estrutural                                                      \n";

    return $stSql;
}

function recuperaContaBancariaFontePagadoraContas(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaContaBancariaFontePagadoraContas().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContaBancariaFontePagadoraContas()
{
    $stSql  = "      SELECT conta_corrente.num_conta_corrente                   \n";
    $stSql .= "           , conta_corrente.cod_conta_corrente                   \n";
    $stSql .= "           , banco.num_banco                                     \n";
    $stSql .= "           , banco.cod_banco                                     \n";
    $stSql .= "           , agencia.num_agencia                                 \n";
    $stSql .= "           , agencia.cod_agencia                                 \n";
    $stSql .= "           , plano_conta.nom_conta                               \n";
    $stSql .= "           , rpad(replace(plano_conta.cod_estrutural,'.',''),15,'0') as estrutural \n";
    $stSql .= "        FROM contabilidade.plano_conta                           \n";
    $stSql .= "        JOIN contabilidade.plano_analitica                       \n";
    $stSql .= "          ON plano_analitica.exercicio = plano_conta.exercicio   \n";
    $stSql .= "         AND plano_analitica.cod_conta = plano_conta.cod_conta   \n";
    $stSql .= "        JOIN contabilidade.plano_banco                           \n";
    $stSql .= "          ON plano_banco.exercicio = plano_analitica.exercicio   \n";
    $stSql .= "         AND plano_banco.cod_plano = plano_analitica.cod_plano   \n";
    $stSql .= "        JOIN monetario.conta_corrente                            \n";
    $stSql .= "          ON plano_banco.cod_banco          = conta_corrente.cod_banco \n";
    $stSql .= "         AND plano_banco.cod_agencia        = conta_corrente.cod_agencia \n";
    $stSql .= "         AND plano_banco.cod_conta_corrente = conta_corrente.cod_conta_corrente \n";
    $stSql .= "        JOIN monetario.agencia                                   \n";
    $stSql .= "          ON conta_corrente.cod_banco   = agencia.cod_banco      \n";
    $stSql .= "         AND conta_corrente.cod_agencia = agencia.cod_agencia    \n";
    $stSql .= "        JOIN monetario.banco                                     \n";
    $stSql .= "          ON agencia.cod_banco = banco.cod_banco                 \n";
    $stSql .= "   LEFT JOIN (                                                   \n";
    $stSql .= "          SELECT conta_debito.cod_plano                          \n";
    $stSql .= "               , conta_debito.exercicio                          \n";
    $stSql .= "            FROM contabilidade.valor_lancamento                  \n";
    $stSql .= "            JOIN contabilidade.conta_debito                      \n";
    $stSql .= "              ON conta_debito.exercicio = valor_lancamento.exercicio \n";
    $stSql .= "             AND conta_debito.cod_entidade = valor_lancamento.cod_entidade \n";
    $stSql .= "             AND conta_debito.tipo = valor_lancamento.tipo       \n";
    $stSql .= "             AND conta_debito.cod_lote = valor_lancamento.cod_lote \n";
    $stSql .= "             AND conta_debito.sequencia = valor_lancamento.sequencia \n";
    $stSql .= "             AND conta_debito.tipo_valor = valor_lancamento.tipo_valor \n";
    $stSql .= "           WHERE valor_lancamento.tipo <> 'I'                   \n";
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND     valor_lancamento.cod_entidade IN (".$this->getDado('stEntidades').") \n";
    }
    if (trim($this->getDado('stExercicio'))) {
        $stSql .= " AND     valor_lancamento.exercicio = '".$this->getDado('stExercicio')."' \n";
    }
    $stSql .= "        GROUP BY conta_debito.cod_plano                          \n";
    $stSql .= "               , conta_debito.exercicio                          \n";
    $stSql .= "        ORDER BY conta_debito.cod_plano                          \n";
    $stSql .= "           ) AS lancamento                                       \n";
    $stSql .= "          ON lancamento.cod_plano = plano_analitica.cod_plano    \n";
    $stSql .= "         AND lancamento.exercicio = plano_analitica.exercicio    \n";
    # temporario, para nao pegar bancos internos
    $stSql .= "       WHERE banco.num_banco <>  '999'                           \n";
    $stSql .= "         AND banco.num_banco <>  '000'                           \n";
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND     plano_banco.cod_entidade IN (".$this->getDado('stEntidades').") \n";
    }
    if (trim($this->getDado('stExercicio'))) {
        $stSql .= " AND     plano_conta.exercicio = '".$this->getDado('stExercicio')."' \n";
    }
    if (trim($this->getDado('arContasCorrente'))) {
        $stSql .= " AND    conta_corrente.cod_conta_corrente IN (".$this->getDado('arContasCorrente').") \n";
    }

    $stSql.= "   GROUP BY conta_corrente.cod_conta_corrente                                                \n";
    $stSql.= "                  ,conta_corrente.num_conta_corrente                                         \n";
    $stSql.= "                  ,banco.num_banco                                                           \n";
    $stSql.= "                  ,banco.cod_banco                                                           \n";
    $stSql.= "                  ,agencia.num_agencia                                                       \n";
    $stSql.= "                  ,agencia.cod_agencia                                                       \n";
    $stSql.= "                  ,plano_conta.nom_conta                                                     \n";
    $stSql.= "                  ,plano_conta.cod_estrutural                                                \n";

    $stSql .= "    ORDER BY plano_conta.cod_estrutural                          \n";

    return $stSql;
}

function recuperaContas(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaContas().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaContas()
{
    $stSql  = " SELECT   tcepb.relacao_conta_corrente_fonte_pagadora.cod_banco                                        \n";
    $stSql .= "         ,tcepb.relacao_conta_corrente_fonte_pagadora.cod_agencia                                      \n";
    $stSql .= "         ,tcepb.relacao_conta_corrente_fonte_pagadora.cod_conta_corrente                               \n";
    $stSql .= "         ,tcepb.relacao_conta_corrente_fonte_pagadora.cod_tipo                                         \n";
    $stSql .= "         ,monetario.conta_corrente.num_conta_corrente                                                  \n";
    $stSql .= "         ,contabilidade.plano_banco.cod_plano                                                          \n";
    $stSql .= "         ,contabilidade.plano_conta.nom_conta                                                          \n";
    $stSql .= "         ,tcepb.tipo_origem_recurso.descricao                                                          \n";
    $stSql .= "   FROM  tcepb.relacao_conta_corrente_fonte_pagadora                                                   \n";
    $stSql .= "   JOIN  tcepb.tipo_origem_recurso                                                                     \n";
    $stSql .= "     ON  tcepb.tipo_origem_recurso.cod_tipo = tcepb.relacao_conta_corrente_fonte_pagadora.cod_tipo     \n";
    $stSql .= "    AND  tcepb.tipo_origem_recurso.exercicio = tcepb.relacao_conta_corrente_fonte_pagadora.exercicio   \n";
    $stSql  .= "   JOIN  monetario.conta_corrente                                                                      \n";
    $stSql .= "     ON  monetario.conta_corrente.cod_conta_corrente = tcepb.relacao_conta_corrente_fonte_pagadora.cod_conta_corrente \n";
    $stSql  .= "   JOIN  contabilidade.plano_banco                                                                     \n";
    $stSql  .= "     ON  contabilidade.plano_banco.cod_conta_corrente = tcepb.relacao_conta_corrente_fonte_pagadora.cod_conta_corrente   \n";
    $stSql  .= "    AND  contabilidade.plano_banco.exercicio    =  tcepb.relacao_conta_corrente_fonte_pagadora.exercicio \n";
    $stSql  .= "   JOIN  contabilidade.plano_analitica                                                                   \n";
    $stSql  .= "     ON  contabilidade.plano_analitica.cod_plano = contabilidade.plano_banco.cod_plano                   \n";
    $stSql  .= "    AND  contabilidade.plano_analitica.exercicio  = contabilidade.plano_banco.exercicio                  \n";
    $stSql  .= "   JOIN  contabilidade.plano_conta                                                                       \n";
    $stSql  .= "     ON  contabilidade.plano_conta.cod_conta = contabilidade.plano_analitica.cod_conta                   \n";
    $stSql  .= "    AND  contabilidade.plano_conta.exercicio = contabilidade.plano_analitica.exercicio                   \n";
    $stSql  .= "  WHERE  tcepb.relacao_conta_corrente_fonte_pagadora.exercicio ='".Sessao::getExercicio()."'             \n";
    if ($this->getDado('arCodTipo') != '') {
        $stSql .=  "    AND tcepb.relacao_conta_corrente_fonte_pagadora.cod_tipo IN (".$this->getDado('arCodTipo').")    \n";
    }

    $stSql  .= "  ORDER BY tcepb.relacao_conta_corrente_fonte_pagadora.cod_tipo                                          \n";

    return $stSql;
}

function recuperaFontePagadora(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaFontePagadora().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaFontePagadora()
{
     $stSql  = "  SELECT tcepb.tipo_origem_recurso.descricao                                                         \n";
     $stSql .= "        ,tcepb.relacao_conta_corrente_fonte_pagadora.cod_tipo                                        \n";
     $stSql .= "    FROM tcepb.tipo_origem_recurso                                                                   \n";
     $stSql .= "    JOIN tcepb.relacao_conta_corrente_fonte_pagadora                                                 \n";
     $stSql .= "      ON tcepb.relacao_conta_corrente_fonte_pagadora.cod_tipo  = tcepb.tipo_origem_recurso.cod_tipo  \n";
     $stSql .= "     AND tcepb.relacao_conta_corrente_fonte_pagadora.exercicio = tcepb.tipo_origem_recurso.exercicio \n";
     $stSql .= "   WHERE tcepb.relacao_conta_corrente_fonte_pagadora.exercicio = '".Sessao::getExercicio()."'        \n";
     $stSql .= "GROUP BY tcepb.tipo_origem_recurso.descricao                                                         \n";
     $stSql .= "        ,tcepb.relacao_conta_corrente_fonte_pagadora.cod_tipo                                        \n";

    return $stSql;
}

function recuperaContasCodTipo(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if ($stOrdem=="") {
        $stOrdem="ORDER BY tcepb.relacao_conta_corrente_fonte_pagadora.cod_tipo                                       \n";
    }
    $stSql = $this->montaRecuperaContasCodTipo().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContasCodTipo()
{
    $stSql  = "SELECT *   \n";
    $stSql .= "  FROM  tcepb.relacao_conta_corrente_fonte_pagadora                                                   \n";
    $stSql .= "  JOIN  tcepb.tipo_origem_recurso                                                                     \n";
    $stSql .= "    ON  tcepb.relacao_conta_corrente_fonte_pagadora.cod_tipo =  tcepb.tipo_origem_recurso.cod_tipo    \n";
    $stSql .= "   AND  tcepb.relacao_conta_corrente_fonte_pagadora.exercicio =  tcepb.tipo_origem_recurso.exercicio  \n";

    return $stSql;
}

}
