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
  * Classe de mapeamento da tabela PESSOAL.CONTRATO_SERVIDOR
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CONTRATO_SERVIDOR
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalContratoServidor extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalContratoServidor()
{
    parent::Persistente();
    $this->setTabela('pessoal.contrato_servidor');

    $this->setCampoCod('cod_contrato');
    $this->setComplementoChave('');

    $this->AddCampo('cod_contrato'          ,'integer'       ,true,''    ,true,false);
    $this->AddCampo('cod_norma'             ,'integer'       ,true,''    ,false,false);
    $this->AddCampo('cod_tipo_pagamento'    ,'integer'       ,true,''    ,false,false);
    $this->AddCampo('cod_tipo_salario'      ,'integer'       ,true,''    ,false,false);
    $this->AddCampo('cod_tipo_admissao'     ,'integer'       ,true,''    ,false,false);
    $this->AddCampo('cod_vinculo'           ,'integer'       ,true,''    ,false,false);
    $this->AddCampo('cod_cargo'             ,'integer'       ,true,''    ,false,false);
    $this->AddCampo('cod_regime'            ,'integer'       ,true,''    ,false,false);
    $this->AddCampo('nr_cartao_ponto'       ,'char'       ,false,'10'    ,false,false);
    $this->AddCampo('dt_opcao_fgts'         ,'date'          ,true,''   ,false,false);
    $this->AddCampo('adiantamento'          ,'boolean'       ,true,''    ,false,false);
    $this->AddCampo('cod_categoria'         ,'integer'       ,true,''    ,false,false);
    $this->AddCampo('cod_sub_divisao'       ,'integer'       ,true,''    ,false,false);
    $this->AddCampo('ativo'                 ,'boolean'       ,true,''    ,false,false);
    $this->AddCampo('cod_grade'             ,'integer'       ,true,''    ,false,true);
}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT servidor.*                                                       \n";
    $stSql .= "     , servidor_contrato_servidor.cod_contrato                          \n";
    $stSql .= "  FROM pessoal.servidor_contrato_servidor                               \n";
    $stSql .= "     , pessoal.servidor                                                 \n";
    $stSql .= " WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor  \n";

    return $stSql;
}

function montaRecuperaRelacionamentoCargo()
{
$stSql  = "   select                                                                                                            \n";
$stSql .= "    MAX( cs.cod_cargo) as cod_cargo,                                                                                 \n";
$stSql .= "    MAX( cs.cod_sub_divisao) as cod_sub_divisao,                                                                     \n";
$stSql .= "    MAX( cs.cod_norma) as cod_norma,                                                                                 \n";
$stSql .= "    MAX( cs.cod_norma) as norma_maxima,                                                                              \n";
$stSql .= "    MAX( scs.cod_servidor) as cod_servidor,                                                                          \n";
$stSql .= "    MAX( csf.cod_cargo) as cod_funcao,                                                                               \n";
$stSql .= "    MAX( csf.vigencia) as vigencia,                                                                                  \n";
$stSql .= "    MAX( csef.cod_especialidade) as cod_especialidade_funcao,                                                        \n";
$stSql .= "    MAX( csec.cod_especialidade) as cod_especialidade_cargo,                                                         \n";
$stSql .= "    MAX( cssdf.cod_sub_divisao) as cod_sub_divisao_funcao                                                            \n";
$stSql .= "   FROM                                                                                                              \n";
$stSql .= "       pessoal.servidor_contrato_servidor as scs,                                                                    \n";
$stSql .= "       pessoal.contrato_servidor_orgao as cso,                                                                       \n";
$stSql .= "        ( Select * FROM pessoal.contrato_servidor_funcao WHERE                                                       \n";
$stSql .= "            timestamp::varchar||cod_contrato::varchar IN (                                                                             \n";
$stSql .= "                Select max(timestamp)::varchar||cof.cod_contrato::varchar FROM pessoal.contrato_servidor_funcao as cof,                \n";
$stSql .= "                       pessoal.contrato_servidor as cs                                                               \n";
$stSql .= "                WHERE cs.cod_contrato = cof.cod_contrato                                                             \n";
$stSql .= "                GROUP BY                                                                                             \n";
$stSql .= "                    cof.cod_contrato                                                                                 \n";
$stSql .= "                                     ) ) as csf,                                                                     \n";
$stSql .= "       organograma.orgao as o,                                                                                       \n";
$stSql .= "       pessoal.servidor as s,                                                                                        \n";
$stSql .= "       pessoal.contrato_servidor as cs                                                                               \n";
$stSql .= "   LEFT OUTER JOIN                                                                                                   \n";
$stSql .= "       pessoal.contrato_servidor_especialidade_cargo as csec                                                         \n";
$stSql .= "   ON                                                                                                                \n";
$stSql .= "       ( cs.cod_contrato = csec.cod_contrato )                                                                       \n";
$stSql .= "   LEFT OUTER JOIN                                                                                                   \n";
$stSql .= "      (SELECT especialidade_funcao.*                                                                                 \n";
$stSql .= "        FROM pessoal.contrato_servidor_especialidade_funcao as especialidade_funcao                                  \n";
$stSql .= "           , (  SELECT cod_contrato                                                                                  \n";
$stSql .= "                     , max(timestamp) as timestamp                                                                   \n";
$stSql .= "                  FROM pessoal.contrato_servidor_especialidade_funcao                                                \n";
$stSql .= "              GROUP BY cod_contrato) as max_especialidade_funcao                                                     \n";
$stSql .= "           , pessoal.especialidade                                                                                   \n";
$stSql .= "           , pessoal.cargo                                                                                           \n";
$stSql .= "           , pessoal.contrato_servidor_funcao                                                                        \n";
$stSql .= "           , (  SELECT cod_contrato                                                                                  \n";
$stSql .= "                     , max(timestamp) as timestamp                                                                   \n";
$stSql .= "                  FROM pessoal.contrato_servidor_funcao                                                              \n";
$stSql .= "              GROUP BY cod_contrato) as max_contrato_servidor_funcao                                                 \n";
$stSql .= "       WHERE especialidade_funcao.cod_contrato = max_especialidade_funcao.cod_contrato                               \n";
$stSql .= "         AND especialidade_funcao.timestamp    = max_especialidade_funcao.timestamp                                  \n";
$stSql .= "         AND especialidade_funcao.cod_especialidade = especialidade.cod_especialidade                                \n";
$stSql .= "         AND especialidade.cod_cargo = cargo.cod_cargo                                                               \n";
$stSql .= "         AND cargo.cod_cargo = contrato_servidor_funcao.cod_cargo                                                    \n";
$stSql .= "         AND contrato_servidor_funcao.timestamp = max_especialidade_funcao.timestamp                                 \n";
$stSql .= "         AND contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                       \n";
$stSql .= "         AND contrato_servidor_funcao.timestamp    = max_contrato_servidor_funcao.timestamp ) as csef                \n";
//$stSql .= "        ( Select contrato_servidor_especialidade_funcao.*                                                            \n";
//$stSql .= "            FROM pessoal.contrato_servidor_especialidade_funcao                                                      \n";
//$stSql .= "               , pessoal.especialidade                                                             \n";
//$stSql .= "               , pessoal.cargo                                                                                       \n";
//$stSql .= "               , pessoal.contrato_servidor_funcao                                                                    \n";
//$stSql .= "           WHERE contrato_servidor_especialidade_funcao.cod_especialidade = especialidade.cod_especialidade \n";
//$stSql .= "             AND especialidade.cod_cargo = cargo.cod_cargo                                         \n";
//$stSql .= "             AND cargo.cod_cargo = contrato_servidor_funcao.cod_cargo                                                \n";
//$stSql .= "             AND contrato_servidor_funcao.timestamp = contrato_servidor_especialidade_funcao.timestamp               \n";
//$stSql .= "             AND contrato_servidor_especialidade_funcao.timestamp||contrato_servidor_especialidade_funcao.cod_contrato IN (\n";
//$stSql .= "                Select max(timestamp)||ceof.cod_contrato FROM pessoal.contrato_servidor_especialidade_funcao as ceof,\n";
//$stSql .= "                       pessoal.contrato_servidor as cs                                                               \n";
//$stSql .= "                WHERE cs.cod_contrato = ceof.cod_contrato GROUP BY ceof.cod_contrato  ) ) as csef                    \n";
$stSql .= "   ON                                                                                                                \n";
$stSql .= "       ( cs.cod_contrato = csef.cod_contrato )                                                                       \n";
$stSql .= "   LEFT OUTER JOIN                                                                                                   \n";
$stSql .= "       (select * from  pessoal.contrato_servidor_sub_divisao_funcao WHERE                                            \n";
$stSql .= "            timestamp::varchar||cod_contrato::varchar IN (                                                           \n";
$stSql .= "                select max(timestamp)::varchar||sub.cod_contrato::varchar from pessoal.contrato_servidor_sub_divisao_funcao as sub,    \n";
$stSql .= "                        pessoal.contrato_servidor as cs                                                              \n";
$stSql .= "                WHERE cs.cod_contrato = sub.cod_contrato GROUP BY sub.cod_contrato )                                 \n";
$stSql .= "             ) as cssdf                                                                                              \n";
$stSql .= "   ON ( cs.cod_contrato = cssdf.cod_contrato )                                                                       \n";
$stSql .= "   WHERE                                                                                                             \n";
$stSql .= "       s.cod_servidor  = scs.cod_servidor                                                                            \n";
$stSql .= "       and cs.cod_contrato = cso.cod_contrato                                                                        \n";
$stSql .= "       and cs.cod_contrato = scs.cod_contrato                                                                        \n";
$stSql .= "       and cs.cod_contrato = csf.cod_contrato                                                                        \n";
return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaRelacionamentoCargo.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamentoCargo(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    //$stOrdem = ' order by ' . $stOrdem;

    $stSql = $this->montaRecuperaRelacionamentoCargo().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoContratoRescisao()
{
 $stSql  = "   SELECT                                                                                         \n";
 $stSql .= "        s.cod_servidor,                                                                           \n";
 $stSql .= "        s.numcgm,                                                                                 \n";
 $stSql .= "        c.registro as registro,                                                                   \n";
 $stSql .= "        to_char(cs.dt_demissao, 'dd/mm/yyyy') as dt_demissao,                                     \n";
 $stSql .= "        cs.cod_contrato,                                                                          \n";
 $stSql .= "        o.descricao as lotacao,                                                                   \n";
 $stSql .= "        o.cod_orgao as cod_orgao                                                                  \n";
 $stSql .= "   FROM                                                                                           \n";
 $stSql .= "        pessoal.servidor as s,                                                                    \n";
 $stSql .= "        pessoal.servidor_contrato_servidor as scs,                                                \n";
 $stSql .= "        pessoal.contrato_servidor as cs,                                                          \n";
 $stSql .= "        pessoal.contrato as c,                                                                    \n";
 $stSql .= "        pessoal.contrato_servidor_caso_causa  as cscr,                                        \n";
 $stSql .= " ( Select contrato_servidor_orgao.cod_contrato as cod_contrato1,                                  \n";
 $stSql .= "                 contrato_servidor_orgao.cod_orgao,                                               \n";
 $stSql .= "            from pessoal.contrato_servidor_orgao                                                  \n";
 $stSql .= "               , ( Select cod_contrato as c2, cod_orgao, Max(timestamp) as data  \n";
 $stSql .= "                   From pessoal.contrato_servidor_orgao                                           \n";
 $stSql .= "               Group By cod_contrato, cod_orgao ) as ult_orgao                   \n";
 $stSql .= "           Where contrato_servidor_orgao.cod_contrato    = ult_orgao.c2                           \n";
 $stSql .= "             And contrato_servidor_orgao.cod_orgao       = ult_orgao.cod_orgao                    \n";
 $stSql .= "             And contrato_servidor_orgao.timestamp       = ult_orgao.data ) as cso,               \n";
 $stSql .= "        ( Select * FROM pessoal.contrato_servidor_funcao WHERE                                    \n";
 $stSql .= "            timestamp IN (                                                                         \n";
 $stSql .= "                Select max(timestamp) FROM pessoal.contrato_servidor_funcao as cof,                \n";
 $stSql .= "                       pessoal.contrato_servidor as cs                                            \n";
 $stSql .= "                WHERE cs.cod_contrato = cof.cod_contrato  ) ) as csf,                             \n";
 $stSql .= "        organograma.orgao as o                                                                    \n";
 $stSql .= "   WHERE                                                                                          \n";
 $stSql .= "        s.cod_servidor = scs.cod_servidor and                                                     \n";
 $stSql .= "        scs.cod_contrato = cs.cod_contrato and                                                    \n";
 $stSql .= "        c.cod_contrato = cs.cod_contrato and                                                      \n";
 $stSql .= "        cs.cod_contrato = cso.cod_contrato1                                                       \n";
 $stSql .= "        and cso.cod_orgao = o.cod_orgao                                                           \n";

return $stSql;

}

function RecuperaRelacionamentoContratoRescisao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    //$stOrdem = ' order by ' . $stOrdem;
    $stFiltro .= " GROUP BY s.cod_servidor,s.numcgm,c.registro,cs.dt_demissao,cs.cod_contrato,o.descricao,o.cod_orgao ";
    $stSql = $this->montaRecuperaRelacionamentoContratoRescisao().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaDadosAbaContratoServidor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : "";
    $stSql = $this->montaRecuperaDadosAbaContratoServidor().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosAbaContratoServidor()
{
    $stSql  = "   SELECT cs.*                                                                           \n";
    $stSql .= "        , to_char(cs.dt_opcao_fgts,'dd/mm/yyyy')        as dtOpcaoFgts                   \n";
    $stSql .= "        , ced.cod_tipo                                                                   \n";
    $stSql .= "        , to_char(posse.dt_nomeacao,'dd/mm/yyyy')       as dt_nomeacao                   \n";
    $stSql .= "        , to_char(posse.dt_posse,'dd/mm/yyyy')          as dt_posse                      \n";
    $stSql .= "        , to_char(posse.dt_admissao,'dd/mm/yyyy')       as dt_admissao                   \n";
    $stSql .= "        , to_char(exame.dt_validade_exame,'dd/mm/yyyy') as dt_validade_exame             \n";
    $stSql .= "        , esp_cargo.cod_especialidade                   as cod_especialidade_cargo       \n";
    $stSql .= "        , funcao.cod_cargo                              as cod_funcao                    \n";
    $stSql .= "        , to_char(funcao.vigencia,'dd/mm/yyyy')         as ultima_vigencia               \n";
    $stSql .= "        , esp_funcao.cod_especialidade                  as cod_especialidade_funcao      \n";
    $stSql .= "        , orgao.cod_orgao                                                                \n";
    $stSql .= "        , local.cod_local                                                                \n";
    $stSql .= "        , fgts.cod_banco                                as cod_banco_fgts                \n";
    $stSql .= "        , fgts.num_banco                                as num_banco_fgts                \n";
    $stSql .= "        , fgts.cod_agencia                              as cod_agencia_fgts              \n";
    $stSql .= "        , fgts.nr_conta                                 as conta_fgts                    \n";
    $stSql .= "        , salario.horas_mensais                                                          \n";
    $stSql .= "        , salario.horas_semanais                                                         \n";
    $stSql .= "        , salario.salario                                                                \n";
    $stSql .= "        , to_char(salario.vigencia,'dd/mm/yyyy')        as vigencia                      \n";
    $stSql .= "        , to_char(progressao.dt_inicio_progressao,'dd/mm/yyyy') as dt_inicio_progressao  \n";
    $stSql .= "        , nivel_padrao.cod_nivel_padrao                                                  \n";
    $stSql .= "        , padrao.cod_padrao                                                              \n";
    $stSql .= "        , conta_salario.cod_banco                       as cod_banco_salario             \n";
    $stSql .= "        , conta_salario.num_banco                       as num_banco_salario             \n";
    $stSql .= "        , conta_salario.cod_agencia                     as cod_agencia_salario           \n";
    $stSql .= "        , conta_salario.nr_conta                        as conta_salario                 \n";
    $stSql .= "        , contrato.registro                                                              \n";
    $stSql .= "        , ocorrencia.cod_ocorrencia                                                      \n";
    $stSql .= "        , contrato_servidor_sindicato.numcgm_sindicato                                   \n";
    $stSql .= "        , recuperarSituacaoDoContratoLiteral(contrato.cod_contrato,0,'".Sessao::getEntidade()."') as situacao \n";
    $stSql .= "        , contrato_servidor_forma_pagamento.cod_forma_pagamento                          \n";
    $stSql .= "     FROM pessoal.contrato_servidor_nomeacao_posse as posse                              \n";
    $stSql .= "        , (  SELECT cod_contrato                                                         \n";
    $stSql .= "                  , max(timestamp) as timestamp                                          \n";
    $stSql .= "               FROM pessoal.contrato_servidor_nomeacao_posse                             \n";
    $stSql .= "           GROUP BY cod_contrato)                  as max_posse                          \n";
    $stSql .= "        , pessoal.contrato_servidor_funcao         as funcao                             \n";
    $stSql .= "        , (  SELECT cod_contrato                                                         \n";
    $stSql .= "                  , max(timestamp) as timestamp                                          \n";
    $stSql .= "               FROM pessoal.contrato_servidor_funcao                                     \n";
    $stSql .= "           GROUP BY cod_contrato) as max_funcao                                          \n";
    $stSql .= "        , pessoal.contrato_servidor_orgao          as orgao                              \n";
    $stSql .= "        , (  SELECT cod_contrato                                                         \n";
    $stSql .= "                  , max(timestamp) as timestamp                                          \n";
    $stSql .= "               FROM pessoal.contrato_servidor_orgao                                      \n";
    $stSql .= "           GROUP BY cod_contrato) as max_orgao                                           \n";
    $stSql .= "        , pessoal.contrato_servidor_salario        as salario                            \n";
    $stSql .= "        , (  SELECT cod_contrato                                                         \n";
    $stSql .= "                  , max(timestamp) as timestamp                                          \n";
    $stSql .= "               FROM pessoal.contrato_servidor_salario                                    \n";
    $stSql .= "           GROUP BY cod_contrato) as max_salario                                         \n";
    $stSql .= "        , pessoal.contrato                                                               \n";

    $stSql .= "INNER JOIN pessoal.contrato_servidor_forma_pagamento                                                                 \n";
    $stSql .= "        ON contrato_servidor_forma_pagamento.cod_contrato = contrato.cod_contrato                                    \n";
    $stSql .= "INNER JOIN (  SELECT contrato_servidor_forma_pagamento.cod_contrato                                                  \n";
    $stSql .= "                   , max(timestamp) as timestamp                                                                     \n";
    $stSql .= "                FROM pessoal.contrato_servidor_forma_pagamento                                                       \n";
    $stSql .= "            GROUP BY contrato_servidor_forma_pagamento.cod_contrato                                                  \n";
    $stSql .= "           ) as max_contrato_servidor_forma_pagamento                                                                \n";
    $stSql .= "        ON contrato_servidor_forma_pagamento.cod_contrato = max_contrato_servidor_forma_pagamento.cod_contrato       \n";
    $stSql .= "       AND contrato_servidor_forma_pagamento.timestamp = max_contrato_servidor_forma_pagamento.timestamp             \n";

    $stSql .= "        , pessoal.contrato_servidor                as cs                                 \n";

    $stSql .= "LEFT JOIN pessoal.contrato_servidor_sindicato                                                                                                                 \n";
    $stSql .= "       ON contrato_servidor_sindicato.cod_contrato = cs.cod_contrato                                                                                                     \n";

    $stSql .= "LEFT JOIN (SELECT conta_salario.*                                                        \n";
    $stSql .= "                , banco.num_banco                                                        \n";
    $stSql .= "             FROM pessoal.contrato_servidor_conta_salario as conta_salario               \n";
    $stSql .= "                , monetario.agencia                    as agencia                        \n";
    $stSql .= "                , monetario.banco                      as banco                          \n";
    $stSql .= "            WHERE conta_salario.cod_agencia   = agencia.cod_agencia                      \n";
    $stSql .= "              AND conta_salario.cod_banco     = agencia.cod_banco                        \n";
    $stSql .= "              AND agencia.cod_banco  = banco.cod_banco ) as conta_salario                \n";
    $stSql .= "       ON cs.cod_contrato  = conta_salario.cod_contrato                                  \n";
    $stSql .= "LEFT JOIN (SELECT padrao.*                                                               \n";
    $stSql .= "             FROM pessoal.contrato_servidor_padrao as padrao                             \n";
    $stSql .= "                , (  SELECT cod_contrato                                                 \n";
    $stSql .= "                          , max(timestamp) as timestamp                                  \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_padrao                             \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_padrao                                  \n";
    $stSql .= "            WHERE padrao.cod_contrato = max_padrao.cod_contrato                          \n";
    $stSql .= "              AND padrao.timestamp    = max_padrao.timestamp) as padrao                  \n";
    $stSql .= "       ON cs.cod_contrato        = padrao.cod_contrato                                   \n";
    $stSql .= "LEFT JOIN (SELECT nivel_padrao.*                                                         \n";
    $stSql .= "             FROM pessoal.contrato_servidor_nivel_padrao as nivel_padrao                 \n";
    $stSql .= "                , (  SELECT cod_contrato                                                 \n";
    $stSql .= "                          , max(timestamp) as timestamp                                  \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_nivel_padrao                       \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_nivel_padrao                            \n";
    $stSql .= "            WHERE nivel_padrao.cod_contrato = max_nivel_padrao.cod_contrato              \n";
    $stSql .= "              AND nivel_padrao.timestamp    = max_nivel_padrao.timestamp) as nivel_padrao\n";
    $stSql .= "       ON cs.cod_contrato        = nivel_padrao.cod_contrato                             \n";
    $stSql .= "LEFT JOIN (SELECT progressao.*                                                           \n";
    $stSql .= "             FROM pessoal.contrato_servidor_inicio_progressao as progressao              \n";
    $stSql .= "                , (  SELECT cod_contrato                                                 \n";
    $stSql .= "                          , max(timestamp) as timestamp                                  \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_inicio_progressao                  \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_progressao                              \n";
    $stSql .= "            WHERE progressao.cod_contrato = max_progressao.cod_contrato                  \n";
    $stSql .= "              AND progressao.timestamp    = max_progressao.timestamp) as progressao      \n";
    $stSql .= "       ON cs.cod_contrato        = progressao.cod_contrato                               \n";
    $stSql .= "LEFT JOIN (SELECT fgts.*                                                                 \n";
    $stSql .= "                , banco.num_banco                                                        \n";
    $stSql .= "             FROM pessoal.contrato_servidor_conta_fgts as fgts                           \n";
    $stSql .= "                , monetario.agencia                    as agencia                        \n";
    $stSql .= "                , monetario.banco                      as banco                          \n";
    $stSql .= "            WHERE fgts.cod_agencia   = agencia.cod_agencia                               \n";
    $stSql .= "              AND fgts.cod_banco     = agencia.cod_banco                                 \n";
    $stSql .= "              AND agencia.cod_banco  = banco.cod_banco ) as fgts                         \n";
    $stSql .= "       ON cs.cod_contrato        = fgts.cod_contrato                                     \n";
    $stSql .= "LEFT JOIN (SELECT local.*                                                                \n";
    $stSql .= "             FROM pessoal.contrato_servidor_local as local                               \n";
    $stSql .= "                , (  SELECT cod_contrato                                                 \n";
    $stSql .= "                          , max(timestamp) as timestamp                                  \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_local                              \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_local                                   \n";
//     $stSql .= "                , pessoal.contrato_servidor_salario as salario \n";
//     $stSql .= "                , (  SELECT cod_contrato                                                 \n";
//     $stSql .= "                          , max(timestamp) as timestamp                                  \n";
//     $stSql .= "                       FROM pessoal.contrato_servidor_salario  \n";
//     $stSql .= "                      GROUP BY cod_contrato) as max_salario                              \n";
    $stSql .= "            WHERE local.cod_contrato = max_local.cod_contrato                            \n";
    $stSql .= "              AND local.timestamp    = max_local.timestamp) as local                     \n";
//     $stSql .= "              AND salario.cod_contrato = max_salario.cod_contrato                        \n";
//     $stSql .= "              AND salario.timestamp    = max_salario.timestamp                           \n";
//     $stSql .= "              AND max_local.timestamp  = max_salario.timestamp) as local                 \n";
    $stSql .= "       ON cs.cod_contrato        = local.cod_contrato                                    \n";
    $stSql .= "LEFT JOIN (SELECT esp_funcao.*                                                           \n";
    $stSql .= "             FROM pessoal.contrato_servidor_especialidade_funcao as esp_funcao           \n";
    $stSql .= "                , (  SELECT cod_contrato                                                 \n";
    $stSql .= "                          , max(timestamp) as timestamp                                  \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_especialidade_funcao               \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_esp_funcao                              \n";
    $stSql .= "            WHERE esp_funcao.cod_contrato = max_esp_funcao.cod_contrato                  \n";
    $stSql .= "              AND esp_funcao.timestamp    = max_esp_funcao.timestamp) as esp_funcao      \n";
    $stSql .= "       ON cs.cod_contrato        = esp_funcao.cod_contrato                               \n";
    $stSql .= "LEFT JOIN pessoal.contrato_servidor_especialidade_cargo as esp_cargo                     \n";
    $stSql .= "       ON cs.cod_contrato        = esp_cargo.cod_contrato                                \n";
    $stSql .= "LEFT JOIN pessoal.contrato_servidor_cedencia as ced                                      \n";
    $stSql .= "       ON cs.cod_contrato        = ced.cod_contrato                                      \n";
    $stSql .= "LEFT JOIN (SELECT exame.*                                                                \n";
    $stSql .= "             FROM pessoal.contrato_servidor_exame_medico AS exame                        \n";
    $stSql .= "                , (  SELECT cod_contrato                                                 \n";
    $stSql .= "                          , max(timestamp) as timestamp                                  \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_exame_medico                       \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_exame                                   \n";
    $stSql .= "            WHERE exame.cod_contrato = max_exame.cod_contrato                            \n";
    $stSql .= "              AND exame.timestamp    = max_exame.timestamp) as exame                     \n";
    $stSql .= "       ON cs.cod_contrato        = exame.cod_contrato                                    \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_ocorrencia.*                                         \n";
    $stSql .= "             FROM pessoal.contrato_servidor_ocorrencia                                   \n";
    $stSql .= "                , (  SELECT cod_contrato                                                 \n";
    $stSql .= "                          , max(timestamp) as timestamp                                  \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_ocorrencia                         \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_ocorrencia            \n";
    $stSql .= "            WHERE contrato_servidor_ocorrencia.cod_contrato = max_contrato_servidor_ocorrencia.cod_contrato                           \n";
    $stSql .= "              AND contrato_servidor_ocorrencia.timestamp    = max_contrato_servidor_ocorrencia.timestamp) as ocorrencia               \n";
    $stSql .= "       ON cs.cod_contrato        = ocorrencia.cod_contrato                               \n";
    $stSql .= "	  LEFT JOIN ( SELECT A.cod_contrato FROM pessoal.aposentadoria A,  pessoal.classificacao_enquadramento CE, ( SELECT cod_contrato, max(timestamp) AS timestamp FROM pessoal.aposentadoria WHERE aposentadoria.cod_contrato = ".$this->getDado("cod_contrato")." GROUP BY cod_contrato ) AS max_aposentadoria     \n";
    $stSql .= "	  WHERE 																															      \n";
    $stSql .= "   	 A.cod_classificacao = CE.cod_classificacao AND A.cod_enquadramento = CE.cod_enquadramento 							          	  	  \n";
    $stSql .= "   	 AND A.cod_contrato = max_aposentadoria.cod_contrato AND A.timestamp = max_aposentadoria.timestamp 									  \n";
    $stSql .= "   	 AND A.cod_contrato = ".$this->getDado("cod_contrato")."																			  \n";
    $stSql .= "      AND NOT EXISTS ( 																													  \n";
    $stSql .= "							SELECT max_aposentadoria.* FROM ( SELECT cod_contrato, max(timestamp) AS timestamp FROM pessoal.aposentadoria WHERE aposentadoria.cod_contrato = ".$this->getDado("cod_contrato")." GROUP BY cod_contrato ) max_aposentadoria, ( SELECT cod_contrato, max(timestamp_aposentadoria) AS timestamp_aposentadoria FROM pessoal.aposentadoria_excluida WHERE aposentadoria_excluida.cod_contrato = ".$this->getDado("cod_contrato")." GROUP BY cod_contrato ) max_aposentadoria_excluida \n";
    $stSql .= "   				   			WHERE 																														  												\n";
    $stSql .= " 						    max_aposentadoria.cod_contrato = max_aposentadoria_excluida.cod_contrato AND max_aposentadoria.timestamp = max_aposentadoria_excluida.timestamp_aposentadoria         		\n";
    $stSql .= "                   )																																												    	\n";
    $stSql .= "  ) AS aposentado			\n";
    $stSql .= "  ON cs.cod_contrato = aposentado.cod_contrato \n";
    $stSql .= "    WHERE cs.cod_contrato            = posse.cod_contrato                                \n";
    $stSql .= "      AND max_posse.cod_contrato     = posse.cod_contrato                                \n";
    $stSql .= "      AND max_posse.timestamp        = posse.timestamp                                   \n";
    $stSql .= "      AND max_funcao.cod_contrato    = funcao.cod_contrato                               \n";
    $stSql .= "      AND max_funcao.timestamp       = funcao.timestamp                                  \n";
    $stSql .= "      AND funcao.cod_contrato        = cs.cod_contrato                                   \n";
    $stSql .= "      AND max_orgao.cod_contrato     = orgao.cod_contrato                                \n";
    $stSql .= "      AND max_orgao.timestamp        = orgao.timestamp                                   \n";
    $stSql .= "      AND orgao.cod_contrato         = cs.cod_contrato                                   \n";
    $stSql .= "      AND max_salario.cod_contrato   = salario.cod_contrato                              \n";
    $stSql .= "      AND max_salario.timestamp      = salario.timestamp                                 \n";
    $stSql .= "      AND salario.cod_contrato       = cs.cod_contrato                                   \n";
    $stSql .= "      AND contrato.cod_contrato      = cs.cod_contrato                                   \n";

    return $stSql;
}

function recuperaContratosServidorResumido(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : "";
    $stSql = $this->montaRecuperaContratosServidorResumido().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosServidorResumido()
{
    $stSql  = "SELECT\n";
    $stSql .= "     pcs.*,\n";
    $stSql .= "     ps.cod_servidor,\n";
    $stSql .= "     pc.registro,\n";
    $stSql .= "     cgm.nom_cgm as servidor,\n";
    $stSql .= "     cgm.numcgm,\n";
    $stSql .= "     orgao.cod_orgao,\n";
    $stSql .= "     recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as lotacao,\n";
    $stSql .= "     vw_orgao_nivel.orgao,\n";
    $stSql .= "     cargo.cod_cargo,\n";
    $stSql .= "     cargo.descricao as cargo,\n";
    $stSql .= "     esp.descricao as esp_cargo,\n";
    $stSql .= "     cargo_funcao.cod_cargo as cod_funcao,\n";
    $stSql .= "     cargo_funcao.descricao as funcao,\n";
    $stSql .= "     to_char(funcao.vigencia,'dd/mm/yyyy') as dt_alteracao_funcao,\n";
    $stSql .= "     espf.descricao as esp_funcao,\n";
    $stSql .= "     local.descricao as local,\n";
    $stSql .= "     pcsl.cod_local,\n";
    $stSql .= "     to_char(posse.dt_posse,'dd/mm/yyyy') as dt_posse,\n";
    $stSql .= "     to_char(posse.dt_nomeacao,'dd/mm/yyyy') as dt_nomeacao,\n";
    $stSql .= "     to_char(posse.dt_admissao,'dd/mm/yyyy') as dt_admissao,\n";
    $stSql .= "     pcsp.cod_padrao,\n";
    $stSql .= "     recuperarSituacaoDoContratoLiteral(pc.cod_contrato,0,'".Sessao::getEntidade()."') as situacao\n";
    $stSql .= "	    , forma_pagamento.cod_forma_pagamento\n";
    $stSql .= "FROM\n";
    $stSql .= "     pessoal.servidor_contrato_servidor as psc\n";
    $stSql .= "INNER JOIN pessoal.servidor as ps\n";
    $stSql .= "        on psc.cod_servidor = ps.cod_servidor\n";
    $stSql .= "INNER JOIN pessoal.contrato_servidor as pcs\n";
    $stSql .= "        on pcs.cod_contrato = psc.cod_contrato\n";
    $stSql .= "	INNER JOIN pessoal.contrato_servidor_forma_pagamento\n";
    $stSql .= " 	ON contrato_servidor_forma_pagamento.cod_contrato = pcs.cod_contrato\n";
    $stSql .= " 	AND contrato_servidor_forma_pagamento.timestamp = ( SELECT MAX(timestamp)\n";
    $stSql .= "								    FROM pessoal.contrato_servidor_forma_pagamento\n";
    $stSql .= "								    WHERE cod_contrato = pcs.cod_contrato )\n";
    $stSql .= " INNER JOIN pessoal.forma_pagamento\n";
    $stSql .= "    	ON forma_pagamento.cod_forma_pagamento = contrato_servidor_forma_pagamento.cod_forma_pagamento\n";
    $stSql .= "INNER JOIN pessoal.contrato as pc\n";
    $stSql .= "        on pcs.cod_contrato = pc.cod_contrato\n";
    $stSql .= "INNER JOIN sw_cgm_pessoa_fisica as pf\n";
    $stSql .= "        ON ps.numcgm = pf.numcgm\n";
    $stSql .= "INNER JOIN sw_cgm as cgm\n";
    $stSql .= "        ON pf.numcgm = cgm.numcgm\n";
    $stSql .= "INNER JOIN pessoal.contrato_servidor_nomeacao_posse as posse\n";
    $stSql .= "        ON pcs.cod_contrato = posse.cod_contrato\n";
    $stSql .= "       AND posse.timestamp = (select timestamp\n";
    $stSql .= "                                from pessoal.contrato_servidor_nomeacao_posse\n";
    $stSql .= "                               where cod_contrato = pcs.cod_contrato\n";
    $stSql .= "                            order by timestamp desc\n";
    $stSql .= "                               limit 1)\n";
    $stSql .= "INNER JOIN pessoal.contrato_servidor_funcao as funcao\n";
    $stSql .= "        ON pcs.cod_contrato = funcao.cod_contrato\n";
    $stSql .= "       AND funcao.timestamp = (select timestamp\n";
    $stSql .= "                                 from pessoal.contrato_servidor_funcao\n";
    $stSql .= "                                where cod_contrato = pcs.cod_contrato\n";
    $stSql .= "                             order by timestamp desc\n";
    $stSql .= "                                limit 1)\n";
    $stSql .= "INNER JOIN pessoal.cargo as cargo_funcao\n";
    $stSql .= "        ON funcao.cod_cargo = cargo_funcao.cod_cargo\n";
    $stSql .= "LEFT JOIN pessoal.contrato_servidor_especialidade_cargo as esp_cargo\n";
    $stSql .= "        ON pcs.cod_contrato = esp_cargo.cod_contrato\n";
    $stSql .= " LEFT JOIN pessoal.especialidade as esp\n";
    $stSql .= "        ON esp_cargo.cod_especialidade = esp.cod_especialidade\n";
    $stSql .= " LEFT JOIN pessoal.cargo as cargo\n";
    $stSql .= "        ON pcs.cod_cargo = cargo.cod_cargo\n";
    $stSql .= " LEFT JOIN pessoal.contrato_servidor_local as pcsl\n";
    $stSql .= "        ON pcs.cod_contrato = pcsl.cod_contrato\n";
    $stSql .= "       AND pcsl.timestamp = (select timestamp\n";
    $stSql .= "                               from pessoal.contrato_servidor_local\n";
    $stSql .= "                              where cod_contrato = pcs.cod_contrato\n";
    $stSql .= "                           order by timestamp desc\n";
    $stSql .= "                              limit 1)\n";
    $stSql .= " LEFT JOIN organograma.local as local\n";
    $stSql .= "        ON pcsl.cod_local = local.cod_local\n";
    $stSql .= " LEFT JOIN pessoal.contrato_servidor_padrao as pcsp\n";
    $stSql .= "        ON pcs.cod_contrato = pcsp.cod_contrato\n";
    $stSql .= "       AND pcsp.timestamp = (select timestamp\n";
    $stSql .= "                               from pessoal.contrato_servidor_padrao\n";
    $stSql .= "                              where cod_contrato = pcs.cod_contrato\n";
    $stSql .= "                           order by timestamp desc\n";
    $stSql .= "                              limit 1)\n";
    $stSql .= "LEFT JOIN pessoal.contrato_servidor_especialidade_funcao as esp_funcao\n";
    $stSql .= "       ON pcs.cod_contrato = esp_funcao.cod_contrato\n";
    $stSql .= "      AND esp_funcao.timestamp = (select timestamp\n";
    $stSql .= "                                    from pessoal.contrato_servidor_especialidade_funcao\n";
    $stSql .= "                                   where cod_contrato = pcs.cod_contrato\n";
    $stSql .= "                                order by timestamp desc\n";
    $stSql .= "                                   limit 1)\n";
    $stSql .= " LEFT JOIN pessoal.especialidade as espf\n";
    $stSql .= "        ON esp_funcao.cod_especialidade = espf.cod_especialidade\n";
    $stSql .= "INNER JOIN pessoal.contrato_servidor_orgao as pcso\n";
    $stSql .= "        ON pcs.cod_contrato = pcso.cod_contrato\n";
    $stSql .= "       AND pcso.timestamp = (select timestamp\n";
    $stSql .= "                               from pessoal.contrato_servidor_orgao\n";
    $stSql .= "                              where cod_contrato = pcs.cod_contrato\n";
    $stSql .= "                           order by timestamp desc\n";
    $stSql .= "                              limit 1)\n";
    $stSql .= "INNER JOIN organograma.orgao\n";
    $stSql .= "        ON pcso.cod_orgao = orgao.cod_orgao\n";
    $stSql .= "INNER JOIN organograma.orgao_nivel\n";
    $stSql .= "        ON orgao.cod_orgao = orgao_nivel.cod_orgao\n";
    $stSql .= "INNER JOIN organograma.nivel\n";
    $stSql .= "        ON orgao_nivel.cod_nivel       = nivel.cod_nivel\n";
    $stSql .= "       AND orgao_nivel.cod_organograma = nivel.cod_organograma\n";
    $stSql .= "INNER JOIN organograma.organograma\n";
    $stSql .= "        ON nivel.cod_organograma = organograma.cod_organograma\n";
    $stSql .= "INNER JOIN organograma.vw_orgao_nivel\n";
    $stSql .= "        ON orgao.cod_orgao             = vw_orgao_nivel.cod_orgao\n";
    $stSql .= "       AND organograma.cod_organograma = vw_orgao_nivel.cod_organograma\n";
    $stSql .= "       AND nivel.cod_nivel             = vw_orgao_nivel.nivel\n";
    $stSql .= "     WHERE pc.cod_contrato NOT IN (\n";
    $stSql .= "                                    SELECT cod_contrato\n";
    $stSql .= "                                      FROM pessoal.contrato_servidor_caso_causa\n";
    $stSql .= "                                  )\n";

    return $stSql;

}

function recuperaContratosServidorRelatorio(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : "";
    $stSql = $this->montaRecuperaContratosServidorRelatorio().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosServidorRelatorio()
{
    $stSql  = "SELECT
         pcs.*,
         ps.cod_servidor,
         pc.registro,
         cgm.nom_cgm as servidor,
         cgm.numcgm,
         orgao.cod_orgao,
         recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as lotacao,
         vw_orgao_nivel.orgao,
         cargo.cod_cargo,
         cargo.descricao as cargo,
         esp.descricao as esp_cargo,
         cargo_funcao.cod_cargo as cod_funcao,
         cargo_funcao.descricao as funcao,
         to_char(funcao.vigencia,'dd/mm/yyyy') as dt_alteracao_funcao,
         espf.descricao as esp_funcao,
         local.cod_local||' - '||local.descricao as local,
         pcsl.cod_local,
         to_char(posse.dt_posse,'dd/mm/yyyy') as dt_posse,
         to_char(posse.dt_nomeacao,'dd/mm/yyyy') as dt_nomeacao,
         to_char(posse.dt_admissao,'dd/mm/yyyy') as dt_admissao,
         pcsp.cod_padrao,
         recuperarSituacaoDoContratoLiteral(pc.cod_contrato,0,'".Sessao::getEntidade()."') as situacao
         , forma_pagamento.cod_forma_pagamento
    FROM
         pessoal.servidor_contrato_servidor as psc
    INNER JOIN pessoal.servidor as ps
            on psc.cod_servidor = ps.cod_servidor
    INNER JOIN pessoal.contrato_servidor as pcs
            on pcs.cod_contrato = psc.cod_contrato
     INNER JOIN pessoal.contrato_servidor_forma_pagamento
         ON contrato_servidor_forma_pagamento.cod_contrato = pcs.cod_contrato
         AND contrato_servidor_forma_pagamento.timestamp = ( SELECT MAX(timestamp)
                                     FROM pessoal.contrato_servidor_forma_pagamento
                                     WHERE cod_contrato = pcs.cod_contrato )
     INNER JOIN pessoal.forma_pagamento
         ON forma_pagamento.cod_forma_pagamento = contrato_servidor_forma_pagamento.cod_forma_pagamento
    INNER JOIN pessoal.contrato as pc
            on pcs.cod_contrato = pc.cod_contrato
    INNER JOIN sw_cgm_pessoa_fisica as pf
            ON ps.numcgm = pf.numcgm
    INNER JOIN sw_cgm as cgm
            ON pf.numcgm = cgm.numcgm
    INNER JOIN pessoal.contrato_servidor_nomeacao_posse as posse
            ON pcs.cod_contrato = posse.cod_contrato
           AND posse.timestamp = (select timestamp
                                    from pessoal.contrato_servidor_nomeacao_posse
                                   where cod_contrato = pcs.cod_contrato
                                order by timestamp desc
                                   limit 1)
    INNER JOIN pessoal.contrato_servidor_funcao as funcao
            ON pcs.cod_contrato = funcao.cod_contrato
           AND funcao.timestamp = (select timestamp
                                     from pessoal.contrato_servidor_funcao
                                    where cod_contrato = pcs.cod_contrato
                                 order by timestamp desc
                                    limit 1)
    INNER JOIN pessoal.cargo as cargo_funcao
            ON funcao.cod_cargo = cargo_funcao.cod_cargo
    LEFT JOIN pessoal.contrato_servidor_especialidade_cargo as esp_cargo
            ON pcs.cod_contrato = esp_cargo.cod_contrato
     LEFT JOIN pessoal.especialidade as esp
            ON esp_cargo.cod_especialidade = esp.cod_especialidade
     LEFT JOIN pessoal.cargo as cargo
            ON pcs.cod_cargo = cargo.cod_cargo
     LEFT JOIN pessoal.contrato_servidor_local as pcsl
            ON pcs.cod_contrato = pcsl.cod_contrato
           AND pcsl.timestamp = (select timestamp
                                   from pessoal.contrato_servidor_local
                                  where cod_contrato = pcs.cod_contrato
                               order by timestamp desc
                                  limit 1)
     LEFT JOIN organograma.local as local
            ON pcsl.cod_local = local.cod_local
     LEFT JOIN pessoal.contrato_servidor_padrao as pcsp
            ON pcs.cod_contrato = pcsp.cod_contrato
           AND pcsp.timestamp = (select timestamp
                                   from pessoal.contrato_servidor_padrao
                                  where cod_contrato = pcs.cod_contrato
                               order by timestamp desc
                                  limit 1)
    LEFT JOIN pessoal.contrato_servidor_especialidade_funcao as esp_funcao
           ON pcs.cod_contrato = esp_funcao.cod_contrato
          AND esp_funcao.timestamp = (select timestamp
                                        from pessoal.contrato_servidor_especialidade_funcao
                                       where cod_contrato = pcs.cod_contrato
                                    order by timestamp desc
                                       limit 1)
     LEFT JOIN pessoal.especialidade as espf
            ON esp_funcao.cod_especialidade = espf.cod_especialidade
    INNER JOIN pessoal.contrato_servidor_orgao as pcso
            ON pcs.cod_contrato = pcso.cod_contrato
           AND pcso.timestamp = (select timestamp
                                   from pessoal.contrato_servidor_orgao
                                  where cod_contrato = pcs.cod_contrato
                               order by timestamp desc
                                  limit 1)
    INNER JOIN organograma.orgao
            ON pcso.cod_orgao = orgao.cod_orgao
    INNER JOIN organograma.orgao_nivel
            ON orgao.cod_orgao = orgao_nivel.cod_orgao
    INNER JOIN organograma.nivel
            ON orgao_nivel.cod_nivel       = nivel.cod_nivel
           AND orgao_nivel.cod_organograma = nivel.cod_organograma
    INNER JOIN organograma.organograma
            ON nivel.cod_organograma = organograma.cod_organograma
    INNER JOIN organograma.vw_orgao_nivel
            ON orgao.cod_orgao             = vw_orgao_nivel.cod_orgao
           AND organograma.cod_organograma = vw_orgao_nivel.cod_organograma
           AND nivel.cod_nivel             = vw_orgao_nivel.nivel
         WHERE 1=1
         --WHERE pc.cod_contrato NOT IN (
         --                               SELECT cod_contrato
         --                                 FROM pessoal.contrato_servidor_caso_causa
         --                             )
    ";
    return $stSql;

}

/*
  * Lista os contratos por cargo exercido
*/
function recuperaContratosCargoExercido(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : "";
    $stSql = $this->montaRecuperaContratosCargoExercido().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/*
  * Monta SQL para listar os contratos por cargo exercido
*/
function montaRecuperaContratosCargoExercido()
{
    $stSql  = "    SELECT pcs.cod_contrato                                    \n";
    $stSql .= "      FROM pessoal.contrato_servidor pcs                       \n";
    $stSql .= " LEFT JOIN pessoal.contrato_servidor_especialidade_cargo pcsec \n";
    $stSql .= "        ON pcsec.cod_contrato = pcs.cod_contrato               \n";

    return $stSql;
}

/*
  * Lista os contratos por cargo exercido
*/
function recuperaContratosCargoExercidoComSubDivisaoAssentamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : "";
    $stSql = $this->montaRecuperaContratosCargoExercidoComSubDivisaoAssentamento().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/*
  * Monta SQL para listar os contratos por cargo exercido
*/
function montaRecuperaContratosCargoExercidoComSubDivisaoAssentamento()
{
    $stSql  = "    SELECT pcs.cod_contrato                                   
                     FROM pessoal.contrato_servidor pcs                      
                LEFT JOIN pessoal.contrato_servidor_especialidade_cargo pcsec
                       ON pcsec.cod_contrato = pcs.cod_contrato              
               INNER JOIN pessoal.assentamento_sub_divisao                   
                       ON assentamento_sub_divisao.cod_sub_divisao = pcs.cod_sub_divisao
               INNER JOIN pessoal.contrato_servidor_situacao
                       ON contrato_servidor_situacao.cod_contrato = pcs.cod_contrato
            ";


    return $stSql;
}

/*
  * Lista os contratos pelo cargo da funcão exercida
*/
function recuperaContratosFuncaoExercida(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : "";
    $stSql = $this->montaRecuperaContratosFuncaoExercida().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/*
  * Monta SQL para listar os contratos pelo cargo da funcão exercido
*/
function montaRecuperaContratosFuncaoExercida()
{
    $stSql  = "    SELECT pcs.cod_contrato                                                         \n";
    $stSql .= "         , pcsf.cod_cargo                                                           \n";
    $stSql .= "         , pcsef.cod_especialidade                                                  \n";
    $stSql .= "      FROM pessoal.contrato_servidor pcs                                            \n";
    $stSql .= "      JOIN ( SELECT pcsf.cod_contrato                                               \n";
    $stSql .= "                  , pcsf.cod_cargo                                                  \n";
    $stSql .= "               FROM pessoal.contrato_servidor_funcao pcsf                           \n";
    $stSql .= "               JOIN ( SELECT pcsf.cod_contrato                                      \n";
    $stSql .= "                                     , MAX( pcsf.timestamp ) as timestamp           \n";
    $stSql .= "                                  FROM pessoal.contrato_servidor_funcao pcsf        \n";
    $stSql .= "                              GROUP BY pcsf.cod_contrato                            \n";
    $stSql .= "                    ) as max_pcsf                                                   \n";
    $stSql .= "                 ON max_pcsf.cod_contrato = pcsf.cod_contrato                       \n";
    $stSql .= "                AND max_pcsf.timestamp    = pcsf.timestamp                          \n";
    $stSql .= "           ) as pcsf                                                                \n";
    $stSql .= "        ON pcsf.cod_contrato = pcs.cod_contrato                                     \n";
    $stSql .= " LEFT JOIN ( SELECT pcsef.cod_contrato                                              \n";
    $stSql .= "                  , pcsef.cod_especialidade                                         \n";
    $stSql .= "               FROM pessoal.contrato_servidor_especialidade_funcao pcsef            \n";
    $stSql .= "               JOIN (   SELECT pcsef.cod_contrato                                   \n";
    $stSql .= "                             , MAX( pcsef.timestamp ) as timestamp                  \n";
    $stSql .= "                          FROM pessoal.contrato_servidor_especialidade_funcao pcsef \n";
    $stSql .= "                      GROUP BY pcsef.cod_contrato                                   \n";
    $stSql .= "                    ) as max_pcsef                                                  \n";
    $stSql .= "                 ON max_pcsef.cod_contrato = pcsef.cod_contrato                     \n";
    $stSql .= "                AND max_pcsef.timestamp    = pcsef.timestamp                        \n";
    $stSql .= "           ) as pcsef                                                               \n";
    $stSql .= "        ON pcsef.cod_contrato = pcs.cod_contrato                                    \n";

    return $stSql;
}

/*
  * Lista os contratos pelo cargo da funcão exercida
*/
function recuperaContratosFuncaoExercidaComSubDivisaoAssentamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stGroup = " GROUP BY pcs.cod_contrato
                        , pcsf.cod_cargo
                        , pcsef.cod_especialidade ";
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : "";
    $stSql = $this->montaRecuperaContratosFuncaoExercidaComSubDivisaoAssentamento().$stFiltro.$stGroup.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/*
  * Monta SQL para listar os contratos pelo cargo da funcão exercido
*/
function montaRecuperaContratosFuncaoExercidaComSubDivisaoAssentamento()
{
    $stSql  = "    SELECT pcs.cod_contrato                                                        
                        , pcsf.cod_cargo                                                          
                        , pcsef.cod_especialidade                                                 
                     FROM pessoal.contrato_servidor pcs                                           
                     JOIN ( SELECT pcsf.cod_contrato                                              
                                 , pcsf.cod_cargo                                                 
                              FROM pessoal.contrato_servidor_funcao pcsf                          
                              JOIN ( SELECT pcsf.cod_contrato                                     
                                                    , MAX( pcsf.timestamp ) as timestamp          
                                                 FROM pessoal.contrato_servidor_funcao pcsf       
                                             GROUP BY pcsf.cod_contrato                           
                                   ) as max_pcsf                                                  
                                ON max_pcsf.cod_contrato = pcsf.cod_contrato                      
                               AND max_pcsf.timestamp    = pcsf.timestamp                         
                          ) as pcsf                                                               
                       ON pcsf.cod_contrato = pcs.cod_contrato                                    
               INNER JOIN pessoal.contrato_servidor_sub_divisao_funcao                            
                       ON contrato_servidor_sub_divisao_funcao.cod_contrato = pcs.cod_contrato    
               INNER JOIN pessoal.assentamento_sub_divisao                                        
                       ON assentamento_sub_divisao.cod_sub_divisao = contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                LEFT JOIN ( SELECT pcsef.cod_contrato                                              
                                 , pcsef.cod_especialidade                                         
                              FROM pessoal.contrato_servidor_especialidade_funcao pcsef            
                              JOIN (   SELECT pcsef.cod_contrato                                   
                                            , MAX( pcsef.timestamp ) as timestamp                  
                                         FROM pessoal.contrato_servidor_especialidade_funcao pcsef 
                                     GROUP BY pcsef.cod_contrato                                   
                                   ) as max_pcsef                                                  
                                ON max_pcsef.cod_contrato = pcsef.cod_contrato                     
                               AND max_pcsef.timestamp    = pcsef.timestamp                        
                          ) as pcsef                                                               
                       ON pcsef.cod_contrato = pcs.cod_contrato                                    
               INNER JOIN pessoal.contrato_servidor_situacao
                       ON contrato_servidor_situacao.cod_contrato = pcs.cod_contrato
            ";
    return $stSql;
}

/*
  * Lista os contratos por lotacão
*/
function recuperaContratosLotacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : "";
    $stSql = $this->montaRecuperaContratosLotacao().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/*
  * Monta SQL para listar os contratos por lotacão
*/
function montaRecuperaContratosLotacao()
{
    $stSql  = " SELECT pcs.cod_contrato                                         \n";
    $stSql .= "      , pcso.cod_orgao                                           \n";
    $stSql .= "      , oon.orgao                                                \n";
    $stSql .= "   FROM pessoal.contrato_servidor pcs                            \n";
    $stSql .= "   JOIN ( SELECT pcso.cod_contrato                               \n";
    $stSql .= "               , pcso.cod_orgao                                  \n";
    $stSql .= "            FROM pessoal.contrato_servidor_orgao pcso            \n";
    $stSql .= "            JOIN (   SELECT cod_contrato                         \n";
    $stSql .= "                          , MAX (timestamp) as timestamp         \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_orgao pcso \n";
    $stSql .= "                   GROUP BY cod_contrato                         \n";
    $stSql .= "                 ) as max_pcso                                   \n";
    $stSql .= "              ON max_pcso.cod_contrato = pcso.cod_contrato       \n";
    $stSql .= "             AND max_pcso.timestamp    = pcso.timestamp          \n";
    $stSql .= "        ) as pcso                                                \n";
    $stSql .= "     ON pcso.cod_contrato = pcs.cod_contrato                     \n";
    $stSql .= "   JOIN organograma.vw_orgao_nivel oon                           \n";
    $stSql .= "     ON oon.cod_orgao       = pcso.cod_orgao                     \n";

    return $stSql;
}

/*
 * Lista os contratos para ser usado em listas
 */
function recuperaRelacionamentoListarContratos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : "";
    $stSql = $this->montaRecuperaRelacionamentoListarContratos().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoListarContratos()
{
    $stSql  = "SELECT contrato_servidor.cod_contrato 															\n";
    $stSql .= "		, contrato.registro																					\n";
    $stSql .= "		, servidor_contrato_servidor.cod_servidor 															\n";
    $stSql .= "		, servidor.numcgm 																					\n";
    $stSql .= "		, sw_cgm.nom_cgm																					\n";
    $stSql .= "		, to_char(contrato_servidor_nomeacao_posse.dt_posse,'dd/mm/yyyy') as dt_posse 						\n";
    $stSql .= "		, to_char(contrato_servidor_nomeacao_posse.dt_nomeacao,'dd/mm/yyyy') as dt_nomeacao 				\n";
    $stSql .= "		, to_char(contrato_servidor_nomeacao_posse.dt_admissao,'dd/mm/yyyy') as dt_admissao 				\n";
    $stSql .= "		, contrato_servidor_orgao.cod_orgao 																\n";
    $stSql .= "		, recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as lotacao  																		\n";
    $stSql .= "     , recuperarSituacaoDoContratoLiteral(contrato.cod_contrato,0,'".Sessao::getEntidade()."') as situacao \n";
    $stSql .= "FROM																										\n";
    $stSql .= "    pessoal.servidor 																					\n";
    $stSql .= "JOIN 																									\n";
    $stSql .= "    pessoal.servidor_contrato_servidor 																	\n";
    $stSql .= "ON 																										\n";
    $stSql .= "	   servidor.cod_servidor = servidor_contrato_servidor.cod_servidor 										\n";
    $stSql .= "JOIN 																									\n";
    $stSql .= "    pessoal.contrato 																					\n";
    $stSql .= "ON																										\n";
    $stSql .= "    servidor_contrato_servidor.cod_contrato = contrato.cod_contrato 										\n";
    $stSql .= "JOIN 																									\n";
    $stSql .= "    pessoal.contrato_servidor 																			\n";
    $stSql .= "ON 																										\n";
    $stSql .= "	   contrato_servidor.cod_contrato = contrato.cod_contrato 												\n";

    if ( $this->getDado("registro") != '' ) {
        $stSql .= " AND contrato.registro = ". $this->getDado("registro")."												\n";
    }

    if ( $this->getDado("numcgm") != '' ) {
        $stSql .= " AND servidor.numcgm = ". $this->getDado("numcgm")."	    										\n";
    }

    $stSql .= "JOIN 																									\n";
    $stSql .= "	   pessoal.contrato_servidor_nomeacao_posse 															\n";
    $stSql .= "ON 																										\n";
    $stSql .= "	   contrato_servidor.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato                       \n";
    $stSql .= "JOIN 																									\n";
    $stSql .= "		( SELECT cod_contrato, max(timestamp) as timestamp FROM pessoal.contrato_servidor_nomeacao_posse GROUP BY cod_contrato ) \n";
    $stSql .= "	max_contrato_servidor_nomeacao_posse  																	\n";
    $stSql .= "ON 																										\n";
    $stSql .= "	   contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp \n";
    $stSql .= "JOIN 																									\n";
    $stSql .= "pessoal.contrato_servidor_orgao 																			\n";
    $stSql .= "ON 																										\n";
    $stSql .= "	   contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato 								\n";
    $stSql .= "JOIN 																									\n";
    $stSql .= "	(  SELECT cod_contrato, max(timestamp) as timestamp FROM pessoal.contrato_servidor_orgao GROUP BY cod_contrato) max_contrato_servidor_orgao \n";
    $stSql .= "ON 																										\n";
    $stSql .= "	   contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp      \n";
    $stSql .= "JOIN  																									\n";
    $stSql .= "	   organograma.orgao 																					\n";
    $stSql .= "ON contrato_servidor_orgao.cod_orgao = orgao.cod_orgao 													\n";
    $stSql .= "JOIN 																									\n";
    $stSql .= "	   sw_cgm 																								\n";
    $stSql .= "ON 																										\n";
    $stSql .= "	   contrato_servidor.cod_contrato = contrato.cod_contrato AND servidor.numcgm = sw_cgm.numcgm 			\n";
    $stSql .= "LEFT JOIN 																								\n";
    $stSql .= "	   ( SELECT cod_contrato, max(timestamp) AS timestamp FROM pessoal.aposentadoria GROUP BY cod_contrato ) max_aposentadoria 				  \n";
    $stSql .= "ON  																										\n";
    $stSql .= "    contrato_servidor.cod_contrato = max_aposentadoria.cod_contrato 									    \n";

    if ($this->getDado("registro") == '' AND $this->getDado("numcgm") == '' ) {
        $stSql .= "AND NOT EXISTS ( SELECT max_aposentadoria_excluida.cod_contrato FROM																																			\n";
        $stSql .= "		( SELECT cod_contrato, max(timestamp_aposentadoria) AS timestamp_aposentadoria FROM pessoal.aposentadoria_excluida GROUP BY cod_contrato ) max_aposentadoria_excluida 		\n";
        $stSql .= "WHERE max_aposentadoria.cod_contrato = max_aposentadoria_excluida.cod_contrato AND max_aposentadoria.timestamp = max_aposentadoria_excluida.timestamp_aposentadoria          								\n";
        $stSql .= ") 																																																			\n";
    }

    $stSql .= "LEFT JOIN  																								\n";
    $stSql .= "    pessoal.aposentadoria 																				\n";
    $stSql .= "ON  																										\n";
    $stSql .= "    max_aposentadoria.cod_contrato = aposentadoria.cod_contrato AND max_aposentadoria.timestamp = aposentadoria.timestamp 											  	\n";

    if ( $this->getDado("registro") != '' ) {

        $stSql .= " AND NOT EXISTS ( SELECT max_aposentadoria.cod_contrato FROM ( SELECT cod_contrato, max(timestamp) AS timestamp FROM pessoal.aposentadoria GROUP BY cod_contrato ) max_aposentadoria 					\n";
        $stSql .= "                 	JOIN ( SELECT cod_contrato, max(timestamp_aposentadoria) AS timestamp_aposentadoria FROM pessoal.aposentadoria_excluida GROUP BY cod_contrato ) max_aposentadoria_excluida  		\n";
        $stSql .= "                     ON max_aposentadoria.cod_contrato = max_aposentadoria_excluida.cod_contrato AND max_aposentadoria.timestamp = max_aposentadoria_excluida.timestamp_aposentadoria 											\n";
        $stSql .= "                     JOIN ( SELECT cod_contrato FROM pessoal.contrato WHERE registro = ".$this->getDado("registro").") contrato 																		\n";
        $stSql .= "                     ON max_aposentadoria.cod_contrato = contrato.cod_contrato 																																					\n";
        $stSql .= ") 																																																								\n";
    }
    if ( $this->getDado("numcgm") != '' ) {

        $stSql .= " AND NOT EXISTS ( SELECT max_aposentadoria.cod_contrato FROM ( SELECT cod_contrato, max(timestamp) AS timestamp FROM pessoal.aposentadoria GROUP BY cod_contrato ) max_aposentadoria 					\n";
        $stSql .= "                 	JOIN ( SELECT cod_contrato, max(timestamp_aposentadoria) AS timestamp_aposentadoria FROM pessoal.aposentadoria_excluida GROUP BY cod_contrato ) max_aposentadoria_excluida  		\n";
        $stSql .= "                     ON max_aposentadoria.cod_contrato = max_aposentadoria_excluida.cod_contrato AND max_aposentadoria.timestamp = max_aposentadoria_excluida.timestamp_aposentadoria 											\n";
        $stSql .= "                     JOIN ( SELECT servidor_contrato_servidor.cod_contrato FROM pessoal.servidor   																									\n";
        $stSql .= "    						   		JOIN pessoal.servidor_contrato_servidor 																																\n";
        $stSql .= "    						   		ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor AND servidor.numcgm = ".$this->getDado("numcgm")." ) contrato																\n";
        $stSql .= "                     ON max_aposentadoria.cod_contrato = contrato.cod_contrato 																																					\n";
        $stSql .= ") 																																																								\n";
    }

    return $stSql;
}

function recuperaContratosParaRegistroEvento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : "";
    $stSql = $this->montaRecuperaContratosParaRegistroEvento().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosParaRegistroEvento()
{
    $stSql  = "SELECT * FROM (     \n";
    $stSql .= "select servidor.cod_contrato\n";
    $stSql .= "     , servidor.registro\n";
    $stSql .= "     , servidor.numcgm\n";
    $stSql .= "     , servidor.nom_cgm\n";
    $stSql .= "     , servidor.cod_orgao\n";
    $stSql .= "     , servidor.desc_orgao as descricao_lotacao\n";
    $stSql .= "     , servidor.orgao as cod_estrutural\n";
    $stSql .= "     , servidor.cod_especialidade_cargo\n";
    $stSql .= "     , servidor.cod_cargo \n";
    $stSql .= "     , recuperarSituacaoDoContratoLiteral(servidor.cod_contrato,0,'".Sessao::getEntidade()."') as situacao\n";
    $stSql .= "     , servidor.cod_especialidade_funcao     \n";
    $stSql .= "     , servidor.cod_funcao\n";
    $stSql .= "     , servidor.cod_padrao\n";
    $stSql .= "     , servidor.cod_local\n";
    $stSql .= "  from recuperarContratoServidor('cgm,oo','".Sessao::getEntidade()."',0,'".$this->getDado("stTipoFiltro")."','".$this->getDado("stValoresFiltro")."','".Sessao::getExercicio()."') as servidor\n";
    $stSql .= "UNION\n";
    $stSql .= "select pensionista.cod_contrato\n";
    $stSql .= "     , pensionista.registro\n";
    $stSql .= "     , pensionista.numcgm\n";
    $stSql .= "     , pensionista.nom_cgm\n";
    $stSql .= "     , pensionista.cod_orgao\n";
    $stSql .= "     , pensionista.desc_orgao as descricao_lotacao\n";
    $stSql .= "     , pensionista.orgao as cod_estrutural\n";
    $stSql .= "     , 0 as cod_especialidade                 \n";
    $stSql .= "     , 0 as cod_cargo                             \n";
    $stSql .= "     , recuperarSituacaoDoContratoLiteral(pensionista.cod_contrato,0,'".Sessao::getEntidade()."') as situacao \n";
    $stSql .= "     , 0 as cod_especialidade_funcao                                                                              \n";
    $stSql .= "     , 0 as cod_funcao                                                                                                \n";
    $stSql .= "     , 0 as cod_padrao                                                                                                    \n";
    $stSql .= "     , 0 as cod_local\n";
    $stSql .= "  from recuperarContratoPensionista('cgm,oo','".Sessao::getEntidade()."',0,'".$this->getDado("stTipoFiltro")."','".$this->getDado("stValoresFiltro")."','".Sessao::getExercicio()."') as pensionista\n";
    $stSql .= ") as contrato \n";
    $stSql .= " WHERE recuperarSituacaoDoContrato(contrato.cod_contrato,0,'".Sessao::getEntidade()."') IN (".$this->getDado("situacao").")       \n";

    return $stSql;
}

function recuperaRegistroTrabalhadoresSEFIP(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : " ORDER BY nom_cgm";
    $stSql = $this->montaRecuperaRegistroTrabalhadoresSEFIP().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRegistroTrabalhadoresSEFIP()
{
    $stSql  = "SELECT servidor.numcgm                                                                                  \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                                   \n";
    $stSql .= "     , replace(replace(trim(sw_cgm_pessoa_fisica.servidor_pis_pasep),'.',''),'-','') as servidor_pis_pasep  \n";
    $stSql .= "     , to_char(contrato_servidor_nomeacao_posse.dt_admissao,'ddmmyyyy') as dt_admissao                  \n";
    $stSql .= "     , contrato_servidor_nomeacao_posse.dt_admissao as dt_admissao_n_formatado                          \n";
    $stSql .= "     , cod_categoria                                                                                    \n";
    $stSql .= "     , registro                                                                                         \n";
    $stSql .= "     , contrato.cod_contrato                                                                            \n";
    $stSql .= "     , ctps.numero                                                                                      \n";
    $stSql .= "     , ctps.serie                                                                                       \n";
    $stSql .= "     , to_char(contrato_servidor.dt_opcao_fgts,'ddmmyyyy') as dt_opcao_fgts                             \n";
    $stSql .= "     , to_char(sw_cgm_pessoa_fisica.dt_nascimento,'ddmmyyyy') as dt_nascimento                          \n";
    $stSql .= "     , (SELECT codigo FROM pessoal.cbo WHERE cbo_cargo.cod_cbo = cod_cbo) as cbo                        \n";
    $stSql .= "     , ocorrencia.num_ocorrencia                                                                        \n";
    $stSql .= "  FROM pessoal.servidor                                                                                 \n";
    $stSql .= "LEFT JOIN (SELECT ctps.*                                                                                \n";
    $stSql .= "                , servidor_ctps.cod_servidor                                                            \n";
    $stSql .= "             FROM pessoal.servidor_ctps                                                                 \n";
    $stSql .= "                , pessoal.ctps                                                                          \n";
    $stSql .= "                , (SELECT cod_servidor                                                                  \n";
    $stSql .= "                        , max(cod_ctps) as cod_ctps                                                     \n";
    $stSql .= "                     FROM pessoal.servidor_ctps                                                         \n";
    $stSql .= "                   GROUP BY cod_servidor) as max_servidor_ctps                                          \n";
    $stSql .= "            WHERE servidor_ctps.cod_ctps = ctps.cod_ctps                                                \n";
    $stSql .= "              AND servidor_ctps.cod_ctps = max_servidor_ctps.cod_ctps                                   \n";
    $stSql .= "              AND servidor_ctps.cod_servidor = max_servidor_ctps.cod_servidor) as ctps                  \n";
    $stSql .= "    ON servidor.cod_servidor = ctps.cod_servidor                                                        \n";
    $stSql .= "     , pessoal.servidor_pis_pasep                                                                       \n";
    $stSql .= "     , (SELECT cod_servidor                                                                             \n";
    $stSql .= "             , max(timestamp) as timestamp                                                              \n";
    $stSql .= "          FROM pessoal.servidor_pis_pasep                                                               \n";
    $stSql .= "        WHERE timestamp <= (ultimotimestampperiodomovimentacao(". $this->getDado("cod_periodo_movimentacao") .",'". $this->getDado("entidade") ."')::timestamp)     \n";
    $stSql .= "        GROUP BY cod_servidor) as max_servidor_pis_pasep                                                \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                               \n";
    $stSql .= "     , pessoal.contrato_servidor_orgao                                                                  \n";
    $stSql .= "     , (SELECT cod_contrato                                                                             \n";
    $stSql .= "             , max(timestamp) as timestamp                                                              \n";
    $stSql .= "          FROM pessoal.contrato_servidor_orgao                                                          \n";
    $stSql .= "        WHERE timestamp <= (ultimotimestampperiodomovimentacao(". $this->getDado("cod_periodo_movimentacao") .",'". $this->getDado("entidade") ."')::timestamp)     \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_orgao                                           \n";
    $stSql .= "     , pessoal.contrato_servidor_nomeacao_posse                                                         \n";
    $stSql .= "     , (SELECT cod_contrato                                                                             \n";
    $stSql .= "             , max(timestamp) as timestamp                                                              \n";
    $stSql .= "          FROM pessoal.contrato_servidor_nomeacao_posse                                                 \n";
    $stSql .= "        WHERE timestamp <= (ultimotimestampperiodomovimentacao(". $this->getDado("cod_periodo_movimentacao") .",'". $this->getDado("entidade") ."')::timestamp)     \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse                                  \n";
    $stSql .= "     , pessoal.contrato_servidor                                                                        \n";
    $stSql .= "     , pessoal.contrato                                                                                 \n";
    $stSql .=  ( $this->getDado("stJoin") != "" ) ? $this->getDado("stJoin") : "";
    $stSql .= "     , sw_cgm                                                                                           \n";
    $stSql .= "     , sw_cgm_pessoa_fisica                                                                             \n";
    $stSql .= "     , pessoal.cargo                                                                                    \n";

    $stSql .= "     , pessoal.cbo_cargo                                                                                \n";
    $stSql .= "     , (SELECT cod_cargo                                                                                \n";
    $stSql .= "             , max(timestamp) as timestamp                                                              \n";
    $stSql .= "          FROM pessoal.cbo_cargo                                                                         \n";
    $stSql .= "        WHERE timestamp <= (ultimotimestampperiodomovimentacao(". $this->getDado("cod_periodo_movimentacao") .",'". $this->getDado("entidade") ."')::timestamp)     \n";
    $stSql .= "        GROUP BY cod_cargo) as max_cbo_cargo                                                             \n";

    $stSql .= "     , pessoal.contrato_servidor_ocorrencia                                                             \n";
    $stSql .= "     , (SELECT cod_contrato                                                                             \n";
    $stSql .= "             , max(timestamp) as timestamp                                                              \n";
    $stSql .= "          FROM pessoal.contrato_servidor_ocorrencia                                                     \n";
    $stSql .= "        WHERE timestamp <= (ultimotimestampperiodomovimentacao(". $this->getDado("cod_periodo_movimentacao") .",'". $this->getDado("entidade") ."')::timestamp)     \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_ocorrencia                                      \n";
    $stSql .= "     , pessoal.ocorrencia                                                                               \n";
    $stSql .= " WHERE servidor.cod_servidor = servidor_pis_pasep.cod_servidor                                          \n";
    $stSql .= "   AND servidor_pis_pasep.cod_servidor = max_servidor_pis_pasep.cod_servidor                            \n";
    $stSql .= "   AND servidor_pis_pasep.timestamp = max_servidor_pis_pasep.timestamp                                  \n";
    $stSql .= "   AND servidor.cod_servidor = servidor_contrato_servidor.cod_servidor                                  \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato          \n";
    $stSql .= "   AND contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato\n";
    $stSql .= "   AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp      \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato                         \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_contrato = contrato.cod_contrato                                  \n";
    $stSql .= "   AND contrato_servidor.cod_cargo = cargo.cod_cargo                                                    \n";
    $stSql .= "   AND servidor.numcgm = sw_cgm.numcgm                                                                  \n";
    $stSql .= "   AND servidor.numcgm = sw_cgm_pessoa_fisica.numcgm                                                    \n";
    $stSql .= "   AND contrato_servidor.cod_contrato = contrato_servidor_ocorrencia.cod_contrato                       \n";
    $stSql .= "   AND contrato_servidor_ocorrencia.cod_contrato = max_contrato_servidor_ocorrencia.cod_contrato        \n";
    $stSql .= "   AND contrato_servidor_ocorrencia.timestamp = max_contrato_servidor_ocorrencia.timestamp              \n";
    $stSql .= "   AND contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato                            \n";
    $stSql .= "   AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                  \n";
    $stSql .= "   AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp                        \n";
    $stSql .= "   AND contrato_servidor_ocorrencia.cod_ocorrencia = ocorrencia.cod_ocorrencia                          \n";
    $stSql .= "   AND cargo.cod_cargo = cbo_cargo.cod_cargo                          \n";
    $stSql .= "   AND cbo_cargo.cod_cargo = max_cbo_cargo.cod_cargo                          \n";
    $stSql .= "   AND cbo_cargo.timestamp = max_cbo_cargo.timestamp                          \n";

$stSql .= "AND EXISTS (SELECT contrato_servidor_previdencia.cod_contrato                                                \n";
$stSql .= "  FROM pessoal.contrato_servidor_previdencia                                                                 \n";
$stSql .= "     , (SELECT cod_contrato                                                                                  \n";
$stSql .= "             , cod_previdencia                                                                               \n";
$stSql .= "             , max(timestamp) as timestamp                                                                   \n";
$stSql .= "          FROM pessoal.contrato_servidor_previdencia                                                         \n";
$stSql .= "        WHERE timestamp <= (ultimotimestampperiodomovimentacao(". $this->getDado("cod_periodo_movimentacao") .",'". $this->getDado("entidade") ."')::timestamp)     \n";
$stSql .= "         GROUP BY cod_contrato                                                                               \n";
$stSql .= "                , cod_previdencia) as max_contrato_servidor_previdencia                                      \n";
$stSql .= "      , folhapagamento.previdencia                                                                           \n";
$stSql .= "  WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato          \n";
$stSql .= "    AND contrato_servidor_previdencia.cod_previdencia = max_contrato_servidor_previdencia.cod_previdencia    \n";
$stSql .= "    AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp                \n";
$stSql .= "    AND contrato_servidor_previdencia.bo_excluido IS FALSE                                                   \n";
$stSql .= "    AND contrato_servidor_previdencia.cod_previdencia = previdencia.cod_previdencia                          \n";
$stSql .= "    AND previdencia.cod_regime_previdencia = 1                                                               \n";
$stSql .= "    AND contrato_servidor.cod_contrato = contrato_servidor_previdencia.cod_contrato)                         \n";

    return $stSql;
}

function recuperaContratosSEFIP(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaContratosSEFIP().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosSEFIP()
{
    $stSql  = "SELECT contrato.*                                                                                       \n";
    $stSql .= "  FROM pessoal.servidor                                                                                 \n";
    $stSql .= "LEFT JOIN (SELECT ctps.*                                                                                \n";
    $stSql .= "                , servidor_ctps.cod_servidor                                                            \n";
    $stSql .= "             FROM pessoal.servidor_ctps                                                                 \n";
    $stSql .= "                , pessoal.ctps                                                                          \n";
    $stSql .= "                , (SELECT cod_servidor                                                                  \n";
    $stSql .= "                        , max(cod_ctps) as cod_ctps                                                     \n";
    $stSql .= "                     FROM pessoal.servidor_ctps                                                         \n";
    $stSql .= "                   GROUP BY cod_servidor) as max_servidor_ctps                                          \n";
    $stSql .= "            WHERE servidor_ctps.cod_ctps = ctps.cod_ctps                                                \n";
    $stSql .= "              AND servidor_ctps.cod_ctps = max_servidor_ctps.cod_ctps                                   \n";
    $stSql .= "              AND servidor_ctps.cod_servidor = max_servidor_ctps.cod_servidor) as ctps                  \n";
    $stSql .= "    ON servidor.cod_servidor = ctps.cod_servidor                                                        \n";
    $stSql .= "     , pessoal.servidor_pis_pasep                                                                       \n";
    $stSql .= "     , (SELECT cod_servidor                                                                             \n";
    $stSql .= "             , max(timestamp) as timestamp                                                              \n";
    $stSql .= "          FROM pessoal.servidor_pis_pasep                                                               \n";
    $stSql .= "        GROUP BY cod_servidor) as max_servidor_pis_pasep                                                \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                               \n";
    $stSql .= "     , pessoal.contrato_servidor_orgao                                                                  \n";
    $stSql .= "     , (SELECT cod_contrato                                                                             \n";
    $stSql .= "             , max(timestamp) as timestamp                                                              \n";
    $stSql .= "          FROM pessoal.contrato_servidor_orgao                                                          \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_orgao                                           \n";
    $stSql .= "     , pessoal.contrato_servidor_nomeacao_posse                                                         \n";
    $stSql .= "     , (SELECT cod_contrato                                                                             \n";
    $stSql .= "             , max(timestamp) as timestamp                                                              \n";
    $stSql .= "          FROM pessoal.contrato_servidor_nomeacao_posse                                                 \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse                                  \n";
    $stSql .= "     , pessoal.contrato_servidor                                                                        \n";
    $stSql .= "     , pessoal.contrato                                                                                 \n";
    $stSql .= ( $this->getDado("stJoin") != "" ) ? $this->getDado("stJoin") : "";
    $stSql .= "     , sw_cgm                                                                                           \n";
    $stSql .= "     , sw_cgm_pessoa_fisica                                                                             \n";
    $stSql .= "     , pessoal.cargo                                                                                    \n";
    $stSql .= "     , pessoal.contrato_servidor_ocorrencia                                                             \n";
    $stSql .= "     , (SELECT cod_contrato                                                                             \n";
    $stSql .= "             , max(timestamp) as timestamp                                                              \n";
    $stSql .= "          FROM pessoal.contrato_servidor_ocorrencia                                                     \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_ocorrencia                                      \n";
    $stSql .= "     , pessoal.ocorrencia                                                                               \n";
    $stSql .= " WHERE servidor.cod_servidor = servidor_pis_pasep.cod_servidor                                          \n";
    $stSql .= "   AND servidor_pis_pasep.cod_servidor = max_servidor_pis_pasep.cod_servidor                            \n";
    $stSql .= "   AND servidor_pis_pasep.timestamp = max_servidor_pis_pasep.timestamp                                  \n";
    $stSql .= "   AND servidor.cod_servidor = servidor_contrato_servidor.cod_servidor                                  \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato          \n";
    $stSql .= "   AND contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato\n";
    $stSql .= "   AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp      \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato                         \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_contrato = contrato.cod_contrato                                  \n";
    $stSql .= "   AND contrato_servidor.cod_cargo = cargo.cod_cargo                                                    \n";
    $stSql .= "   AND servidor.numcgm = sw_cgm.numcgm                                                                  \n";
    $stSql .= "   AND servidor.numcgm = sw_cgm_pessoa_fisica.numcgm                                                    \n";
    $stSql .= "   AND contrato_servidor.cod_contrato = contrato_servidor_ocorrencia.cod_contrato                       \n";
    $stSql .= "   AND contrato_servidor_ocorrencia.cod_contrato = max_contrato_servidor_ocorrencia.cod_contrato        \n";
    $stSql .= "   AND contrato_servidor_ocorrencia.timestamp = max_contrato_servidor_ocorrencia.timestamp              \n";
    $stSql .= "   AND contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato                            \n";
    $stSql .= "   AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                  \n";
    $stSql .= "   AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp                        \n";
    $stSql .= "   AND contrato_servidor_ocorrencia.cod_ocorrencia = ocorrencia.cod_ocorrencia                          \n";

    return $stSql;
}

function recuperaInformacoesMensais(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : " ORDER BY nom_cgm";
    $stSql  = $this->montaRecuperaInformacoesMensais().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaInformacoesMensais()
{
    $stSql  ="   SELECT contrato.*                                                                                             \n";
    $stSql .="        , initcap(sw_cgm.nom_cgm) as nom_cgm                                                                     \n";
    $stSql .="        , cargo.descricao                                                                                        \n";
    $stSql .="        , orgao.cod_orgao                                                                                        \n";
    $stSql .="        , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as desc_orgao              \n";
    $stSql .="        , cod_local                                                                                              \n";
    $stSql .="        , desc_local                                                                                             \n";
    $stSql .= $this->getDado("campo_join");
    $stSql .="     FROM pessoal.servidor_contrato_servidor                                                                     \n";
    $stSql .= $this->getDado("join");
    $stSql .="LEFT JOIN (SELECT contrato_servidor_local.cod_local                                                              \n";
    $stSql .="                , contrato_servidor_local.cod_contrato                                                           \n";
    $stSql .="                , local.descricao as desc_local                                                                  \n";
    $stSql .="             FROM pessoal.contrato_servidor_local                                                                \n";
    $stSql .="                , (SELECT cod_contrato                                                                           \n";
    $stSql .="                        , max(timestamp) as timestamp                                                            \n";
    $stSql .="                     FROM pessoal.contrato_servidor_local                                                        \n";
    $stSql .="                   GROUP BY cod_contrato) as max_contrato_servidor_local                                         \n";
    $stSql .="                , organograma.local                                                                              \n";
    $stSql .="            WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                \n";
    $stSql .="              AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp                      \n";
    $stSql .="              AND contrato_servidor_local.cod_local = local.cod_local) as contrato_servidor_local                \n";
    $stSql .="       ON contrato_servidor_local.cod_contrato = servidor_contrato_servidor.cod_contrato                         \n";
    $stSql .="        , pessoal.contrato                                                                                       \n";
    $stSql .="        , pessoal.servidor                                                                                       \n";
    $stSql .="        , pessoal.cargo                                                                                          \n";
    $stSql .="        , pessoal.contrato_servidor_funcao                                                                       \n";
    $stSql .="        , (SELECT cod_contrato                                                                                   \n";
    $stSql .="                , max(timestamp) as timestamp                                                                    \n";
    $stSql .="             FROM pessoal.contrato_servidor_funcao                                                               \n";
    $stSql .="           GROUP BY cod_contrato) as max_contrato_servidor_funcao                                                \n";
    $stSql .="        , pessoal.contrato_servidor_orgao                                                                        \n";
    $stSql .="        , (SELECT cod_contrato                                                                                   \n";
    $stSql .="                , max(timestamp) as timestamp                                                                    \n";
    $stSql .="             FROM pessoal.contrato_servidor_orgao                                                                \n";
    $stSql .="           GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                 \n";
    $stSql .="        , organograma.orgao                                                                                      \n";
    $stSql .="        , sw_cgm                                                                                                 \n";
    $stSql .="    WHERE servidor_contrato_servidor.cod_contrato = contrato.cod_contrato                                        \n";
    $stSql .="      AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                        \n";
    $stSql .="      AND servidor_contrato_servidor.cod_contrato = contrato_servidor_funcao.cod_contrato                        \n";
    $stSql .="      AND contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                      \n";
    $stSql .="      AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp                            \n";
    $stSql .="      AND contrato_servidor_funcao.cod_cargo = cargo.cod_cargo                                                   \n";
    $stSql .="      AND servidor_contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato                         \n";
    $stSql .="      AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                        \n";
    $stSql .="      AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp                              \n";
    $stSql .="      AND contrato_servidor_orgao.cod_orgao = orgao.cod_orgao                                                    \n";
    $stSql .="      AND servidor.numcgm = sw_cgm.numcgm                                                                        \n";

    return $stSql;
}

function recuperaContagemSindicatos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContagemSindicatos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaContagemSindicatos()
{
    $stSQL = "select numcgm_sindicato as numcgm, (select cnpj from sw_cgm_pessoa_juridica where numcgm = numcgm_sindicato) as cnpj, count(1) as contador from pessoal.contrato_servidor_sindicato group by numcgm_sindicato";

    return $stSQL;
}

function recuperaCAGED(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaCAGED",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaCAGED()
{
    $stSQL  = "select numcgm_sindicato as numcgm, (select cnpj from sw_cgm_pessoa_juridica where numcgm = numcgm_sindicato) as cnpj, count(1) as contador from pessoal.contrato_servidor_sindicato group by numcgm_sindicato";

    return $stSQL;
}

}
