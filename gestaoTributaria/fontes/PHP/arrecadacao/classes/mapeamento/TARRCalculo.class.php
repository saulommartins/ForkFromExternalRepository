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
    * Classe de mapeamento da tabela ARRECADACAO.CALCULO
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRCalculo.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ARRECADACAO.CALCULO
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRCalculo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRCalculo()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.calculo');

    $this->setCampoCod('cod_calculo');
    $this->setComplementoChave('');

    $this->AddCampo('cod_calculo','integer',true,'',true,false);
    $this->AddCampo('cod_credito','integer',true,'',false,true);
    $this->AddCampo('cod_natureza','integer',true,'',false,true);
    $this->AddCampo('cod_genero','integer',true,'',false,true);
    $this->AddCampo('cod_especie','integer',true,'',false,true);
    $this->AddCampo('exercicio','char',true,'4',false,false);
    $this->AddCampo('valor','numeric',true,'14,2',false,false);
    $this->AddCampo('nro_parcelas','integer',true,'',false,false);
    $this->AddCampo('ativo','boolean',true,'',false,false);
    $this->AddCampo('simulado','boolean',false,'',false,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);
    $this->AddCampo('calculado','boolean',true,'',false,false);
}

function montaRecuperaRelacionamento()
{
    $stSql = "   SELECT                                             \n";
    $stSql .= "       C.*,                                           \n";
    $stSql .= "       IC.inscricao_municipal                         \n";
    $stSql .= "   FROM                                               \n";
    $stSql .= "       arrecadacao.calculo     AS C                       \n";
    $stSql .= "   INNER JOIN                                         \n";
    $stSql .= "   (                                                  \n";
    $stSql .= "    SELECT                                            \n";
    $stSql .= "        C.*                                           \n";
    $stSql .= "    FROM                                              \n";
    $stSql .= "        arrecadacao.imovel_calculo AS C,              \n";
    $stSql .= "        (                                             \n";
    $stSql .= "        SELECT                                        \n";
    $stSql .= "            MAX (TIMESTAMP) AS TIMESTAMP,             \n";
    $stSql .= "            INSCRICAO_MUNICIPAL                       \n";
    $stSql .= "        FROM                                          \n";
    $stSql .= "            arrecadacao.imovel_calculo                \n";
    $stSql .= "        GROUP BY                                      \n";
    $stSql .= "            INSCRICAO_MUNICIPAL                       \n";
    $stSql .= "        ) AS IC                                       \n";
    $stSql .= "    WHERE                                             \n";
    $stSql .= "        C.inscricao_municipal = IC.inscricao_municipal\n";
    $stSql .= "    AND C.TIMESTAMP = IC.TIMESTAMP                    \n";
    $stSql .= "    ) AS IC                                           \n";
    $stSql .= "   ON                                                 \n";
    $stSql .= "       C.COD_CALCULO = IC.COD_CALCULO                 \n";

    return $stSql;
}

function recuperaCalculosLancamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCalculosLancamento().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCalculosLancamento()
{
    $stSql = "   SELECT                                      \n";
    $stSql .= "       C.*,                                    \n";
    $stSql .= "       IC.inscricao_municipal,                 \n";
    $stSql .= "       CG.cod_grupo,                           \n";
    $stSql .= "       CG.ano_exercicio                        \n";
    $stSql .= "   FROM                                        \n";
    $stSql .= "       arrecadacao.calculo     AS C            \n";
    $stSql .= "   INNER JOIN                                   \n";
    $stSql .= "       arrecadacao.imovel_calculo AS IC        \n";
    $stSql .= "   ON                                          \n";
    $stSql .= "       C.COD_CALCULO = IC.COD_CALCULO          \n";
    $stSql .= "   LEFT JOIN                                   \n";
    $stSql .= "       arrecadacao.credito_grupo AS CG         \n";
    $stSql .= "   ON                                          \n";
    $stSql .= "       CG.ano_exercicio = C.exercicio  AND     \n";
    $stSql .= "       CG.COD_CREDITO = C.COD_CREDITO AND      \n";
    $stSql .= "       CG.COD_ESPECIE = C.COD_ESPECIE AND      \n";
    $stSql .= "       CG.COD_GENERO = C.COD_GENERO AND        \n";
    $stSql .= "       CG.COD_NATUREZA = C.COD_NATUREZA        \n";

    return $stSql;
}

function recuperaCalculosLancamentoEconomico(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCalculosLancamentoEconomico().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCalculosLancamentoEconomico()
{
    $stSql = "   SELECT                                      \n";
    $stSql .= "       C.*,                                    \n";
    $stSql .= "       CEC.inscricao_economica,                 \n";
    $stSql .= "       CG.cod_grupo,                            \n";
    $stSql .= "       CG.ano_exercicio                         \n";
    $stSql .= "   FROM                                        \n";
    $stSql .= "       arrecadacao.calculo     AS C            \n";
    $stSql .= "   INNER JOIN                                   \n";
    $stSql .= "       arrecadacao.cadastro_economico_calculo AS CEC        \n";
    $stSql .= "   ON                                          \n";
    $stSql .= "       C.COD_CALCULO = CEC.COD_CALCULO          \n";
    $stSql .= "   LEFT JOIN                                   \n";
    $stSql .= "       arrecadacao.credito_grupo AS CG         \n";
    $stSql .= "   ON                                          \n";
    $stSql .= "       CG.ano_exercicio = C.exercicio  AND \n";
    $stSql .= "       CG.COD_CREDITO = C.COD_CREDITO AND      \n";
    $stSql .= "       CG.COD_ESPECIE = C.COD_ESPECIE AND      \n";
    $stSql .= "       CG.COD_GENERO = C.COD_GENERO AND        \n";
    $stSql .= "       CG.COD_NATUREZA = C.COD_NATUREZA        \n";

    return $stSql;
}

function recuperaValorCalculo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaValorCalculo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaValorCalculo()
{
    $stSql = " SELECT                                                               \n";
    $stSql .= "     IC.inscricao_municipal,                                    \n";
    $stSql .= "     C.valor,                                                            \n";
    $stSql .= "     C.cod_calculo,                                                      \n";
    $stSql .= "     CG.cod_credito,                                               \n";
    $stSql .= "     CG.desconto                                                   \n";
    $stSql .= " FROM                                                                   \n";
    $stSql .= "     arrecadacao.calculo AS C                               \n";
    $stSql .= " INNER JOIN                                                           \n";
    $stSql .= "     arrecadacao.calculo_grupo_credito ACGC       \n";
    $stSql .= " ON                                                                          \n";
    $stSql .= "     ACGC.cod_calculo = C.cod_calculo                  \n";
    $stSql .= " INNER JOIN                                                           \n";
    $stSql .= "     arrecadacao.credito_grupo AS CG                  \n";
    $stSql .= " ON                                                                       \n";
    $stSql .= "     c.cod_credito = cg.cod_credito  and               \n";
    $stSql .= "     c.cod_especie = cg.cod_especie  and            \n";
    $stSql .= "     c.cod_genero  = cg.cod_genero   and            \n";
    $stSql .= "     c.cod_natureza= cg.cod_natureza and           \n";
    $stSql .= "     cg.cod_grupo  = ACGC.cod_grupo  and             \n";
    $stSql .= "     cg.ano_exercicio = ACGC.ano_exercicio \n";
    $stSql .= " LEFT JOIN                                                             \n";
    $stSql .= "   (                                                                        \n";
    $stSql .= "    SELECT                                                             \n";
    $stSql .= "        C.*                                                                \n";
    $stSql .= "    FROM                                                                \n";
    $stSql .= "        arrecadacao.imovel_calculo AS C,                \n";
    $stSql .= "        (                                                                   \n";
    $stSql .= "        SELECT                                                         \n";
    $stSql .= "            MAX (TIMESTAMP) AS TIMESTAMP,             \n";
    $stSql .= "            INSCRICAO_MUNICIPAL                               \n";
    $stSql .= "        FROM                                                            \n";
    $stSql .= "            arrecadacao.imovel_calculo                     \n";
    $stSql .= "        GROUP BY                                                     \n";
    $stSql .= "            INSCRICAO_MUNICIPAL                               \n";
    $stSql .= "        ) AS IC                                                          \n";
    $stSql .= "    WHERE                                                             \n";
    $stSql .= "        C.inscricao_municipal = IC.inscricao_municipal\n";
    $stSql .= "    AND C.TIMESTAMP = IC.TIMESTAMP                   \n";
    $stSql .= "    ) AS IC                                                              \n";
    $stSql .= "   ON                                                                     \n";
    $stSql .= "       C.COD_CALCULO = IC.COD_CALCULO               \n";

    return $stSql;
}

function recuperaValorCalculoCredito(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaValorCalculoCredito().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaValorCalculoCredito()
{
    $stSql = " SELECT                                                               \n";
    $stSql .= "     IC.inscricao_municipal,                                    \n";
    $stSql .= "     C.valor,                                                            \n";
    $stSql .= "     C.cod_credito                                               \n";
    $stSql .= " FROM                                                                   \n";
    $stSql .= "     arrecadacao.calculo AS C                               \n";
    $stSql .= " LEFT JOIN                                                             \n";
    $stSql .= "   (                                                                        \n";
    $stSql .= "    SELECT                                                             \n";
    $stSql .= "        C.*                                                                \n";
    $stSql .= "    FROM                                                                \n";
    $stSql .= "        arrecadacao.imovel_calculo AS C,                \n";
    $stSql .= "        (                                                                   \n";
    $stSql .= "        SELECT                                                         \n";
    $stSql .= "            MAX (TIMESTAMP) AS TIMESTAMP,             \n";
    $stSql .= "            INSCRICAO_MUNICIPAL                               \n";
    $stSql .= "        FROM                                                            \n";
    $stSql .= "            arrecadacao.imovel_calculo                     \n";
    $stSql .= "        GROUP BY                                                     \n";
    $stSql .= "            INSCRICAO_MUNICIPAL                               \n";
    $stSql .= "        ) AS IC                                                          \n";
    $stSql .= "    WHERE                                                             \n";
    $stSql .= "        C.inscricao_municipal = IC.inscricao_municipal\n";
    $stSql .= "    AND C.TIMESTAMP = IC.TIMESTAMP                   \n";
    $stSql .= "    ) AS IC                                                              \n";
    $stSql .= "   ON                                                                     \n";
    $stSql .= "       C.COD_CALCULO = IC.COD_CALCULO               \n";

    return $stSql;
}

function recuperaValorCalculoCreditoEconomico(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaValorCalculoCreditoEconomico().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaValorCalculoCreditoEconomico()
{
    $stSql = " SELECT                                                               \n";
    $stSql .= "     CEC.inscricao_economica,                                    \n";
    $stSql .= "     C.valor,                                                            \n";
    $stSql .= "     C.cod_credito,                                               \n";
    $stSql .= "     C.cod_calculo                                               \n";
    $stSql .= " FROM                                                                   \n";
    $stSql .= "     arrecadacao.calculo AS C                               \n";
    $stSql .= " LEFT JOIN                                                             \n";
    $stSql .= "   (                                                                        \n";
    $stSql .= "    SELECT                                                             \n";
    $stSql .= "        CEC.*                                                                \n";
    $stSql .= "    FROM                                                                \n";
    $stSql .= "        arrecadacao.cadastro_economico_calculo AS CEC,                \n";
    $stSql .= "        (                                                                   \n";
    $stSql .= "        SELECT                                                         \n";
    $stSql .= "            MAX (TIMESTAMP) AS TIMESTAMP,             \n";
    $stSql .= "            INSCRICAO_ECONOMICA                               \n";
    $stSql .= "        FROM                                                            \n";
    $stSql .= "            arrecadacao.cadastro_economico_calculo                     \n";
    $stSql .= "        GROUP BY                                                     \n";
    $stSql .= "            INSCRICAO_ECONOMICA                               \n";
    $stSql .= "        ) AS CECC                                                          \n";
    $stSql .= "    WHERE                                                             \n";
    $stSql .= "        CEC.inscricao_economica = CECC.inscricao_economica	\n";
    $stSql .= "    AND CEC.TIMESTAMP = CECC.TIMESTAMP                   \n";
    $stSql .= "    ) AS CEC                                                              \n";
    $stSql .= "   ON                                                                     \n";
    $stSql .= "       C.COD_CALCULO = CEC.COD_CALCULO               \n";

    return $stSql;
}

function recuperaLogCalculo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLogCalculo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLogCalculo()
{
    $stSql ="    SELECT ac.cod_calculo                     \n";
    $stSql .="         , ac.valor                           \n";
    $stSql .="         , ac.cod_credito                     \n";
    $stSql .="         , alc.valor                          \n";
    $stSql .="      FROM arrecadacao.calculo ac             \n";
    $stSql .="INNER JOIN arrecadacao.log_calculo alc        \n";
    $stSql .="        ON alc.cod_calculo = ac.cod_calculo   \n";

    return $stSql;
}

function recuperaListaRelatorioExecucao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaRelatorioExecucao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaRelatorioExecucao()
{
    $stSql = "    SELECT ac.cod_calculo                                                            \n";
    $stSql .= "         , ac.valor                                                                  \n";
    $stSql .= "         , mc.cod_credito                                                            \n";
    $stSql .= "         , mc.cod_especie                                                            \n";
    $stSql .= "         , mc.cod_genero                                                             \n";
    $stSql .= "         , mc.cod_natureza                                                           \n";
    $stSql .= "         , mc.descricao_credito                                                      \n";
    $stSql .= "         , CAST((SELECT array_to_string( ARRAY( select numcgm from sw_cgm where numcgm IN ( SELECT numcgm FROM arrecadacao.calculo_cgm WHERE cod_calculo = ac.cod_calculo)), '/' ) ) AS VARCHAR) AS numcgm                                                                          \n";
    $stSql .= "         , CAST((SELECT array_to_string( ARRAY( select nom_cgm from sw_cgm where numcgm IN ( SELECT numcgm FROM arrecadacao.calculo_cgm WHERE cod_calculo = ac.cod_calculo)), '/' ) ) AS VARCHAR) AS nom_cgm                                                                        \n";
    $stSql .= "         , case                                                                      \n";
    $stSql .= "             when ic.cod_calculo  is not null then ic.inscricao_municipal            \n";
    $stSql .= "             when cec.cod_calculo is not null then cec.inscricao_economica           \n";
    $stSql .= "           else 0                                                                    \n";
    $stSql .= "           end as inscricao                                                          \n";
    $stSql .= "         , case                                                                      \n";
    $stSql .= "             when position('Erro' in alc.valor) > 0 then 'Erro'::varchar             \n";
    $stSql .= "             else 'OK'::varchar                                                      \n";
    $stSql .= "           end as status                                                             \n";
    $stSql .= "         , alc.valor as log_valor                                                    \n";
    $stSql .= "      FROM arrecadacao.calculo ac                                                    \n";
    $stSql .= "INNER JOIN monetario.credito mc                                                      \n";
    $stSql .= "        ON mc.cod_credito = ac.cod_credito                                           \n";
    $stSql .= "       AND mc.cod_especie = ac.cod_especie                                           \n";
    $stSql .= "       AND mc.cod_genero = ac.cod_genero                                             \n";
    $stSql .= "       AND mc.cod_natureza = ac.cod_natureza                                         \n";
    $stSql .= " left JOIN arrecadacao.log_calculo alc                                               \n";
    $stSql .= "        ON alc.cod_calculo = ac.cod_calculo                                          \n";
    $stSql .= " LEFT JOIN arrecadacao.imovel_calculo ic                                             \n";
    $stSql .= "        ON ic.cod_calculo   = ac.cod_calculo                                         \n";
    $stSql .= " LEFT JOIN arrecadacao.cadastro_economico_calculo cec                                \n";
    $stSql .= "        ON cec.cod_calculo  = ac.cod_calculo                                         \n";

    return $stSql;
}

function recuperaListaConsulta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = " \n\t order by ac.timestamp desc";
    $stSql = $this->montaRecuperaListaConsulta().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaConsulta()
{
    $stSql  = "       select to_char(ac.timestamp,'dd/mm/YYYY HH24:MI')  as data   \n";
    $stSql .= "            , aic.inscricao_municipal                               \n";
    $stSql .= "            , ac.cod_calculo                                        \n";
    $stSql .= "            , ac.valor as vlr                                       \n";
    $stSql .= "            , ac.exercicio                                          \n";
    $stSql .= "            , (mc.cod_credito||'.'||                                 \n";
    $stSql .= "             mc.cod_especie ||'.'||                                 \n";
    $stSql .= "             mc.cod_genero  ||'.'||                                 \n";
    $stSql .= "             mc.cod_natureza||' '||                                 \n";
    $stSql .= "             mc.descricao_credito) as credito                       \n";
    $stSql .= "         from arrecadacao.imovel_calculo aic                        \n";
    $stSql .= "   inner join arrecadacao.calculo ac                                \n";
    $stSql .= "           on aic.cod_calculo = ac.cod_calculo                      \n";
    $stSql .= "   inner join monetario.credito mc                                  \n";
    $stSql .= "           on mc.cod_credito = ac.cod_credito                       \n";
    $stSql .= "          and mc.cod_especie = ac.cod_especie                       \n";
    $stSql .= "          and mc.cod_genero  = ac.cod_genero                        \n";
    $stSql .= "          and mc.cod_natureza= ac.cod_natureza                      \n";
    $stSql .= "    left join arrecadacao.lancamento_calculo alc                    \n";
    $stSql .= "           on alc.cod_calculo = ac.cod_calculo                      \n";
    $stSql .= "        where alc.cod_calculo is null                               \n";

    return $stSql;
}

function recuperaCalculosGrupo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = "";
    $stSql = $this->montaRecuperaCalculosGrupo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCalculosGrupo()
{
    $stSql = "   SELECT                                             	\n";
    $stSql .= "       C.*,                                           	\n";
    $stSql .= "       IC.inscricao_municipal                         	\n";
    $stSql .= "   FROM                                               	\n";
    $stSql .= "       arrecadacao.calculo     AS C                   	\n";
    $stSql .= "   INNER JOIN                                         	\n";
    $stSql .= "   (                                                  	\n";
    $stSql .= "    SELECT                                            	\n";
    $stSql .= "        C.*                                           	\n";
    $stSql .= "    FROM                                              	\n";
    $stSql .= "        arrecadacao.imovel_calculo AS C,              	\n";
    $stSql .= "        (                                             	\n";
    $stSql .= "        SELECT                                        	\n";
    $stSql .= "            MAX (TIMESTAMP) AS TIMESTAMP,             	\n";
    $stSql .= "            INSCRICAO_MUNICIPAL                       	\n";
    $stSql .= "        FROM                                          	\n";
    $stSql .= "            arrecadacao.imovel_calculo                	\n";
    $stSql .= "        GROUP BY                                      	\n";
    $stSql .= "            INSCRICAO_MUNICIPAL                       	\n";
    $stSql .= "        ) AS IC                                       	\n";
    $stSql .= "    WHERE                                             	\n";
    $stSql .= "        C.inscricao_municipal = IC.inscricao_municipal	\n";
    $stSql .= "    AND C.TIMESTAMP = IC.TIMESTAMP                    	\n";
    $stSql .= "    ) AS IC                                           	\n";
    $stSql .= "   ON                                                 	\n";
    $stSql .= "       C.COD_CALCULO = IC.COD_CALCULO                 	\n";
    $stSql .= "   INNER JOIN arrecadacao.calculo_grupo_credito       	\n";
    $stSql .= "   on calculo_grupo_credito.COD_CALCULO  = C.COD_CALCULO \n";

    return $stSql;

}
function recuperaSomaCalculos(&$rsRecordSet, $stFiltro = "", $stFiltro2 = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = "";
    $stSql = $this->montaRecuperaSomaCalculos( $stFiltro2, $stFiltro ).$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSomaCalculos($stFiltro2, $stFiltro)
{
    if (!$stFiltro2) {

        $stSql = "	SELECT																\n";
        $stSql .= "		soma_calculo.valor as soma										\n";
        $stSql .= "	FROM																\n";
        $stSql .= "		arrecadacao.calculo_grupo_credito as acgc						\n";
        $stSql .= $stFiltro;

    } else {

        $stSql = " select														     \n";
        $stSql .= "		sum(ac.valor) as valor										 \n";
        $stSql .= "	  from															 \n";
        $stSql .= "		arrecadacao.calculo as ac									 \n";
        $stSql .= "	  where timestamp = (											 \n";
        $stSql .= "			select timestamp from arrecadacao.calculo				 \n";
        $stSql .= "			where cod_calculo = ". $stFiltro2 ."					 \n";
        $stSql .= "			)														 \n";

    }

    return $stSql;
}

function listaCalculosSemLancamentoPorGrupo(&$rsRecordSet, $inCodGrupo, $inExercicio, $stFiltro, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = "";
    $stSql = $this->montaListaCalculosSemLancamentoPorGrupo( $inCodGrupo, $inExercicio ).$stFiltro;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaListaCalculosSemLancamentoPorGrupo($inCodGrupo, $inExercicio)
{
    $stSql = " SELECT
                    ac.*
                FROM
                    arrecadacao.calculo_grupo_credito AS acgr

                INNER JOIN
                    arrecadacao.calculo AS ac
                ON
                    ac.cod_calculo = acgr.cod_calculo
                    AND ac.ativo = TRUE

                LEFT JOIN
                    arrecadacao.lancamento_calculo AS alc
                ON
                    alc.cod_calculo = acgr.cod_calculo

                LEFT JOIN
                    arrecadacao.imovel_calculo AS aic
                ON
                    aic.cod_calculo = ac.cod_calculo

                LEFT JOIN
                    arrecadacao.cadastro_economico_calculo AS acec
                ON
                    acec.cod_calculo = ac.cod_calculo

                WHERE
                    cod_grupo = ".$inCodGrupo."
                    AND acgr.ano_exercicio = '".$inExercicio."'
                    AND alc.cod_calculo IS NULL ";

    return $stSql;
}

}// end of class
?>
