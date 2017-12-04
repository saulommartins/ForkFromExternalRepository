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
     * Classe de mapeamento para a tabela IMOBILIARIO.CONSTRUCAO_EDIFICACAO
     * Data de Criação: 24/11/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Fábio Bertoldi Rodrigues

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: VCIMConstrucaoEdificacao.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  IMOBILIARIO.CONSTRUCAO_EDIFICACAO
  * Data de Criação: 24/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Fábio Bertoldi Rodrigues

  * @package URBEM
  * @subpackage Mapeamento
*/
class VCIMConstrucaoEdificacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function VCIMConstrucaoEdificacao()
{
    parent::Persistente();
    $this->setTabela('imobiliario.vw_edificacao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_construcao,cod_tipo');

    $this->AddCampo('cod_construcao'            ,'integer'  );
    $this->AddCampo('cod_construcao_autonoma'   ,'integer'  );
    $this->AddCampo('cod_tipo'                  ,'integer'  );
    $this->AddCampo('nom_tipo'                  ,'varchar'  );
    $this->AddCampo('area_real'                 ,'numeric'  );
    $this->AddCampo('cod_processo'              ,'integer'  );
    $this->AddCampo('exercicio'                 ,'character');
    $this->AddCampo('imovel_cond'               ,'varchar'  );
    $this->AddCampo('nom_condominio'            ,'varchar'  );
 //   $this->AddCampo('numero'                    ,'varchar'  );
//    $this->AddCampo('complemento'               ,'varchar'  );
    $this->AddCampo('area_unidade'              ,'numeric'  );
    $this->AddCampo('tipo_vinculo'              ,'varchar'  );
    $this->AddCampo('data_construcao'           ,'date'     );
    $this->AddCampo('data_baixa'                ,'date'     );
    $this->AddCampo('data_reativacao'           ,'date'     );

    $this->AddCampo('justificativa'             ,'varchar'  );
    //$this->AddCampo('timestamp'                 ,'timestamp');
    $this->AddCampo('timestamp_construcao'      ,'timestamp');
    $this->AddCampo('timestamp_baixa'      ,'timestamp');
}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT                                                \n";
    $stSql .= "     ve.cod_construcao,                                \n";
    $stSql .= "     ve.cod_construcao_autonoma,                       \n";
    $stSql .= " to_char(ve.data_construcao,'dd/mm/yyyy'    )    as data_construcao,  \n";
    $stSql .= "     ve.data_baixa,                                    \n";
    $stSql .= "     ve.justificativa,                                             \n";
    $stSql .= "     ve.cod_tipo,                                      \n";
    $stSql .= "     ve.nom_tipo,                                      \n";
    $stSql .= "     ve.area_real,                                     \n";
    $stSql .= "     ve.cod_processo,                                  \n";
    $stSql .= "     ve.exercicio,                                     \n";
    $stSql .= "     ve.nom_condominio,                                \n";
    $stSql .= "     ve.area_unidade,                                  \n";
    $stSql .= "     ve.tipo_vinculo,                                  \n";
    $stSql .= "     ve.timestamp_construcao,                          \n";
    $stSql .= "     ve.imovel_cond,                                   \n";
    $stSql .= "     via.inscricao_municipal,                          \n";
    $stSql .= "     via.cod_lote,                                     \n";
    $stSql .= "     via.cod_sublote,                                  \n";
    $stSql .= "     via.timestamp,                                    \n";
    $stSql .= "     via.dt_cadastro,                                  \n";
    $stSql .= "     vl.dt_inscricao,                                  \n";
    $stSql .= "     ll.valor as numero_lote,                          \n";
    $stSql .= "     vla.cod_nivel,                                    \n";
    $stSql .= "     vla.cod_vigencia,                                 \n";
    $stSql .= "     vla.cod_localizacao,                              \n";
    $stSql .= "     vla.valor_composto,                               \n";
    $stSql .= "     vla.valor_reduzido,                               \n";
    $stSql .= "     vla.valor,                                        \n";
    $stSql .= "     vla.nom_localizacao,                              \n";
    $stSql .= "     vla.mascara,                                      \n";
    $stSql .= "     vla.nom_nivel,                                     \n";
    $stSql .= "     imobiliario.fn_calcula_area_imovel( via.inscricao_municipal ) as area_total \n";
    $stSql .= " FROM                                                  \n";
    $stSql .= "     imobiliario.vw_edificacao AS ve,                          \n";
    $stSql .= "     imobiliario.vw_imovel_ativo AS via,                   \n";
    $stSql .= "     imobiliario.vw_lote_ativo AS vl,                      \n";
    $stSql .= "     imobiliario.lote_localizacao as ll,                   \n";
    $stSql .= "     imobiliario.vw_localizacao_ativa AS vla               \n";
    $stSql .= " WHERE                                                 \n";
    $stSql .= "     ve.imovel_cond     = via.inscricao_municipal AND  \n";
    $stSql .= "     via.cod_lote       = vl.cod_lote             AND  \n";
    $stSql .= "     vl.cod_lote        = ll.cod_lote             AND  \n";
    $stSql .= "     ll.cod_localizacao = vla.cod_localizacao          \n";

    return $stSql;
}

function recuperaRelacionamentoConsulta(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql .= $this->montaRecuperaRelacionamentoConsulta().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoConsulta()
{
    $stSql  = " SELECT                                                            \n";
    $stSql .= "     ve.cod_construcao,                                            \n";
    $stSql .= "     ve.cod_construcao_autonoma,                                   \n";
    $stSql .= "     to_char(ve.data_construcao,'dd/mm/yyyy' ) as data_construcao, \n";
    $stSql .= "     ve.data_baixa,           \n";
    $stSql .= "     ve.justificativa,                                             \n";
    $stSql .= "     ve.cod_tipo,                                                  \n";
    $stSql .= "     ve.cod_tipo_autonoma,                                         \n";
    $stSql .= "     ve.nom_tipo,                                                  \n";
    $stSql .= "     ve.area_real,                                                 \n";
    $stSql .= "     ve.cod_processo,                                              \n";
    $stSql .= "     ve.exercicio,                                                 \n";
    $stSql .= "     ve.nom_condominio,                                            \n";
    $stSql .= "     ve.tipo_vinculo,                                              \n";
    $stSql .= "     ve.timestamp_construcao,                                      \n";
    $stSql .= "     ve.imovel_cond,                                               \n";
    $stSql .= "     ve.area_unidade,                                              \n";
    $stSql .= "     ve.data_reativacao,                                           \n";
    $stSql .= "     ve.timestamp_baixa,                                           \n";

    $stSql .= "     via.inscricao_municipal,                                      \n";
    $stSql .= "     imobiliario.fn_calcula_area_imovel( via.inscricao_municipal ) AS area_imovel,                        \n";
    $stSql .= "     imobiliario.fn_calcula_area_imovel_lote( via.inscricao_municipal ) AS area_imovel_lote,              \n";
    $stSql .= "     imobiliario.fn_calcula_area_imovel_construcao( via.inscricao_municipal ) AS area_imovel_construcao,  \n";
    $stSql .= "     iil.cod_lote,                                                 \n";
    $stSql .= "     via.cod_sublote,                                              \n";
    $stSql .= "     via.timestamp,                                                \n";
    $stSql .= "     via.dt_cadastro,                                              \n";
    $stSql .= "     vl.dt_inscricao,                                              \n";
    $stSql .= "     ll.valor as numero_lote,                                      \n";
    $stSql .= "     vla.cod_nivel,                                                \n";
    $stSql .= "     vla.cod_vigencia,                                             \n";
    $stSql .= "     vla.cod_localizacao,                                          \n";
    $stSql .= "     vla.valor_composto,                                           \n";
    $stSql .= "     vla.valor_reduzido,                                           \n";
    $stSql .= "     vla.valor,                                                    \n";
    $stSql .= "     vla.nom_localizacao,                                          \n";
    $stSql .= "     vla.mascara,                                                  \n";
    $stSql .= "     vla.nom_nivel                                                 \n";
    $stSql .= " FROM                                                              \n";
    $stSql .= "     imobiliario.vw_edificacao AS ve,                                      \n";
    $stSql .= "     imobiliario.imovel AS via,                                        \n";
    $stSql .= "    (                                                              \n";
    $stSql .= "        SELECT                                                     \n";
    $stSql .= "            IIL.*                                                  \n";
    $stSql .= "        FROM                                                       \n";
    $stSql .= "            imobiliario.imovel_lote IIL,                           \n";
    $stSql .= "            (SELECT                                                \n";
    $stSql .= "                MAX (TIMESTAMP) AS TIMESTAMP,                      \n";
    $stSql .= "                INSCRICAO_MUNICIPAL                                \n";
    $stSql .= "            FROM                                                   \n";
    $stSql .= "                imobiliario.imovel_lote                            \n";
    $stSql .= "            GROUP BY                                               \n";
    $stSql .= "                INSCRICAO_MUNICIPAL                                \n";
    $stSql .= "            ) AS IL                                                \n";
    $stSql .= "        WHERE                                                      \n";
    $stSql .= "                IIL.INSCRICAO_MUNICIPAL = IL.INSCRICAO_MUNICIPAL   \n";
    $stSql .= "            AND IIL.TIMESTAMP = IL.TIMESTAMP                       \n";
    $stSql .= "    ) AS IIL,                                                      \n";
    $stSql .= "     imobiliario.lote AS vl,                                           \n";
    $stSql .= "     imobiliario.lote_localizacao as ll,                               \n";
    $stSql .= "     imobiliario.vw_localizacao_ativa AS vla                           \n";
    $stSql .= " WHERE                                                             \n";
    $stSql .= "     ve.imovel_cond     = via.inscricao_municipal AND              \n";
    $stSql .= "     via.inscricao_municipal = iil.inscricao_municipal AND         \n";
    $stSql .= "     iil.cod_lote       = vl.cod_lote             AND              \n";
    $stSql .= "     vl.cod_lote        = ll.cod_lote             AND              \n";
    $stSql .= "     ll.cod_localizacao = vla.cod_localizacao                      \n";

    return $stSql;
}

function recuperaRelacionamentoAlteracao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql .= $this->montaRecuperaRelacionamentoAlteracao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoAlteracao()
{
    $stSql  = " SELECT                                                            \n";
    $stSql .= "     ve.cod_construcao,                                            \n";
    $stSql .= "     ve.cod_construcao_autonoma,                                   \n";
    $stSql .= "     to_char(ve.data_construcao,'dd/mm/yyyy' ) as data_construcao, \n";
    $stSql .= "     ve.cod_tipo,                                                  \n";
    $stSql .= "     ve.cod_tipo_autonoma,                                         \n";
    $stSql .= "     ve.nom_tipo,                                                  \n";
    $stSql .= "     ve.area_real,                                                 \n";
    $stSql .= "     ve.cod_processo,                                              \n";
    $stSql .= "     ve.exercicio,                                                 \n";
    $stSql .= "     ve.tipo_vinculo,                                              \n";
    $stSql .= "     ve.timestamp_construcao,                                      \n";
    $stSql .= "     ve.imovel_cond,                                               \n";
    $stSql .= "     ve.area_unidade,                                              \n";
    $stSql .= "     ve.justificativa,                                             \n";
    $stSql .= "     ve.timestamp_baixa,                                           \n";
    $stSql .= "     ve.data_baixa,                                                \n";
    $stSql .= "     ve.data_reativacao,                                           \n";
    $stSql .= "     imobiliario.fn_calcula_area_imovel_construcao( ve.imovel_cond ) AS area_imovel_construcao,  \n";
    $stSql .= "     iil.cod_lote,                                                 \n";
    $stSql .= "     ll.valor as numero_lote,                                      \n";
    $stSql .= "     vla.cod_localizacao,                                          \n";
    $stSql .= "     vla.valor_composto                                            \n";
    $stSql .= " FROM                                                              \n";

//inicio da parte q substitui a view
    $stSql .= "( \n";
    $stSql .= "SELECT \n";
    $stSql .= "        ed.cod_construcao, \n";
    $stSql .= "        cc.timestamp AS timestamp_construcao, \n";
    $stSql .= "        coalesce(ud.cod_construcao, ua.cod_construcao) AS cod_construcao_autonoma, \n";
    $stSql .= "        ed.cod_tipo, \n";
    $stSql .= "        coalesce(ud.cod_tipo,ed.cod_tipo) AS cod_tipo_autonoma, \n";
    $stSql .= "        ac.area_real, \n";
    $stSql .= "        te.nom_tipo, \n";
    $stSql .= "        cp.cod_processo, \n";
    $stSql .= "        cp.exercicio, \n";
    $stSql .= "        to_char(bc.dt_inicio,'dd/mm/yyyy' ) AS data_baixa, \n";
    $stSql .= "        to_char(bc.dt_termino,'dd/mm/yyyy' ) AS data_reativacao, \n";
    $stSql .= "        bc.timestamp AS timestamp_baixa,\n";
    $stSql .= "        bc.justificativa, \n";
    $stSql .= "        dc.data_construcao, \n";
    $stSql .= "        bc.sistema,\n";
    $stSql .= "        CASE\n";
    $stSql .= "            WHEN bc.dt_inicio IS NOT NULL AND bc.dt_termino IS NULL THEN 'baixado'::text\n";
    $stSql .= "            ELSE 'ativo'::text\n";
    $stSql .= "        END AS situacao,\n";
    $stSql .= "        CASE\n";
    $stSql .= "            WHEN ud.inscricao_municipal IS NOT NULL THEN ud.inscricao_municipal\n";
    $stSql .= "            WHEN ua.inscricao_municipal IS NOT NULL THEN ua.inscricao_municipal\n";
    $stSql .= "            ELSE cd.cod_condominio\n";
    $stSql .= "        END AS imovel_cond, \n";
    $stSql .= "        cd.nom_condominio,\n";
    $stSql .= "        CASE\n";
    $stSql .= "            WHEN ud.inscricao_municipal::character varying IS NOT NULL THEN imobiliario.fn_area_unidade_dependente(ed.cod_construcao, ud.inscricao_municipal)::character varying\n";
    $stSql .= "            WHEN ua.inscricao_municipal::character varying IS NOT NULL THEN imobiliario.fn_area_unidade_autonoma(ed.cod_construcao, ua.inscricao_municipal)::character varying\n";
    $stSql .= "            ELSE NULL::character varying\n";
    $stSql .= "        END AS area_unidade,\n";
    $stSql .= "        CASE\n";
    $stSql .= "            WHEN ud.inscricao_municipal::character varying IS NOT NULL THEN 'Dependente'::text\n";
    $stSql .= "            WHEN ua.inscricao_municipal::character varying IS NOT NULL THEN 'Autônoma'::text\n";
    $stSql .= "            ELSE 'Condomínio'::text\n";
    $stSql .= "        END AS tipo_vinculo\n";
    $stSql .= "    FROM \n";
    $stSql .= "        imobiliario.construcao_edificacao ed\n";
    $stSql .= "    LEFT JOIN imobiliario.unidade_dependente ud ON ud.cod_construcao_dependente = ed.cod_construcao\n";
    $stSql .= "    LEFT JOIN imobiliario.unidade_autonoma ua ON ua.cod_construcao = ed.cod_construcao\n";
    $stSql .= "    LEFT JOIN \n";
    $stSql .= "        imobiliario.construcao cc ON cc.cod_construcao = COALESCE(ud.cod_construcao_dependente, ua.cod_construcao)\n";
    $stSql .= "   LEFT JOIN imobiliario.data_construcao dc ON dc.cod_construcao = COALESCE(ud.cod_construcao_dependente, ua.cod_construcao)\n";
    $stSql .= "   LEFT JOIN ( SELECT ac.cod_construcao, ac.timestamp, ac.area_real\n";
    $stSql .= "   FROM imobiliario.area_construcao ac, ( SELECT max(area_construcao.timestamp) AS timestamp, area_construcao.cod_construcao\n";
    $stSql .= "           FROM imobiliario.area_construcao\n";
    $stSql .= "          GROUP BY area_construcao.cod_construcao) mac\n";
    $stSql .= "  WHERE ac.cod_construcao = mac.cod_construcao AND ac.timestamp = mac.timestamp) ac ON ac.cod_construcao = COALESCE(ud.cod_construcao_dependente, ua.cod_construcao)\n";
    $stSql .= "   LEFT JOIN imobiliario.tipo_edificacao te ON te.cod_tipo = ed.cod_tipo\n";
    $stSql .= "   LEFT JOIN ( SELECT cp.cod_construcao, cp.cod_processo, cp.exercicio, cp.timestamp\n";
    $stSql .= "   FROM imobiliario.construcao_processo cp, ( SELECT max(construcao_processo.timestamp) AS timestamp, construcao_processo.cod_construcao\n";
    $stSql .= "           FROM imobiliario.construcao_processo\n";
    $stSql .= "          GROUP BY construcao_processo.cod_construcao) mcp\n";
    $stSql .= "  WHERE cp.cod_construcao = mcp.cod_construcao AND cp.timestamp = mcp.timestamp) cp ON ed.cod_construcao = cp.cod_construcao\n";

    $stSql .= "   LEFT JOIN ( \n";
    $stSql .= "        SELECT \n";
    $stSql .= "            cc.cod_construcao, \n";
    $stSql .= "            cd.cod_condominio, \n";
    $stSql .= "            cd.cod_tipo, \n";
    $stSql .= "            cd.nom_condominio, \n";
    $stSql .= "            cd.timestamp\n";
    $stSql .= "        FROM \n";
    $stSql .= "            imobiliario.construcao_condominio cc, \n";
    $stSql .= "            imobiliario.condominio cd\n";
    $stSql .= "        WHERE \n";
    $stSql .= "            cd.cod_condominio = cc.cod_condominio\n";
    $stSql .= "    ) cd ON cd.cod_construcao = ed.cod_construcao\n";

    $stSql .= "    LEFT JOIN (\n";
    $stSql .= "        SELECT\n";
    $stSql .= "            BAL.*\n";
    $stSql .= "        FROM\n";
    $stSql .= "            imobiliario.baixa_construcao AS BAL,\n";
    $stSql .= "            (\n";
    $stSql .= "            SELECT\n";
    $stSql .= "                MAX (TIMESTAMP) AS TIMESTAMP,\n";
    $stSql .= "                cod_construcao\n";
    $stSql .= "            FROM\n";
    $stSql .= "                imobiliario.baixa_construcao\n";
    $stSql .= "            GROUP BY\n";
    $stSql .= "                cod_construcao\n";
    $stSql .= "            ) AS BT\n";
    $stSql .= "        WHERE\n";
    $stSql .= "            BAL.cod_construcao = BT.cod_construcao AND\n";
    $stSql .= "            BAL.timestamp = BT.timestamp \n";
    $stSql .= "    ) bc\n";
    $stSql .= "    ON ed.cod_construcao = bc.cod_construcao\n";
    $stSql .= ")as ve,\n";
//fim da parte q substitui a view

//    $stSql .= "     imobiliario.vw_edificacao AS ve,                              \n";
    $stSql .= "    (                                                              \n";
    $stSql .= "        SELECT                                                     \n";
    $stSql .= "            IIL.*                                                  \n";
    $stSql .= "        FROM                                                       \n";
    $stSql .= "            imobiliario.imovel_lote IIL,                           \n";
    $stSql .= "            (SELECT                                                \n";
    $stSql .= "                MAX (TIMESTAMP) AS TIMESTAMP,                      \n";
    $stSql .= "                INSCRICAO_MUNICIPAL                                \n";
    $stSql .= "            FROM                                                   \n";
    $stSql .= "                imobiliario.imovel_lote                            \n";
    $stSql .= "            GROUP BY                                               \n";
    $stSql .= "                INSCRICAO_MUNICIPAL                                \n";
    $stSql .= "            ) AS IL                                                \n";
    $stSql .= "        WHERE                                                      \n";
    $stSql .= "                IIL.INSCRICAO_MUNICIPAL = IL.INSCRICAO_MUNICIPAL   \n";
    $stSql .= "            AND IIL.TIMESTAMP = IL.TIMESTAMP                       \n";
    $stSql .= "    ) AS IIL,                                                      \n";
    $stSql .= "     imobiliario.lote_localizacao as ll,                           \n";

    $stSql .= "    (                                                              \n";
    $stSql .= "        SELECT                                                     \n";
    $stSql .= "            loc.codigo_composto AS valor_composto,                 \n";
    $stSql .= "            loc.cod_localizacao                                    \n";
    $stSql .= "        FROM                                                       \n";
    $stSql .= "            imobiliario.localizacao loc                            \n";

    $stSql .= "       LEFT JOIN (                                                 \n";
    $stSql .= "                SELECT                                             \n";
    $stSql .= "                    BAL.*                                          \n";
    $stSql .= "                FROM                                               \n";
    $stSql .= "                    imobiliario.baixa_localizacao AS BAL,            \n";
    $stSql .= "                    (                                                \n";
    $stSql .= "                    SELECT                                           \n";
    $stSql .= "                        MAX (TIMESTAMP) AS TIMESTAMP,                \n";
    $stSql .= "                        cod_localizacao                              \n";
    $stSql .= "                    FROM                                             \n";
    $stSql .= "                        imobiliario.baixa_localizacao                \n";
    $stSql .= "                    GROUP BY                                         \n";
    $stSql .= "                        cod_localizacao                              \n";
    $stSql .= "                    ) AS BT                                          \n";
    $stSql .= "                WHERE                                                \n";
    $stSql .= "                    BAL.cod_localizacao = BT.cod_localizacao AND     \n";
    $stSql .= "                    BAL.timestamp = BT.timestamp                     \n";
    $stSql .= "            ) bl                                                     \n";
    $stSql .= "            ON                                                       \n";
    $stSql .= "                bl.cod_localizacao = loc.cod_localizacao             \n";
    $stSql .= "            WHERE                                                    \n";
    $stSql .= "                ((bl.dt_inicio IS NULL) OR (bl.dt_inicio IS NOT NULL AND bl.dt_termino IS NOT NULL) AND bl.cod_localizacao=loc.cod_localizacao)\n"; //pegando somente ativos (caso necessario remover aqui

    $stSql .= "                                                                   \n";
    $stSql .= "    ) AS vla                                                       \n";
    $stSql .= " WHERE                                                             \n";
    $stSql .= "     iil.inscricao_municipal = ve.imovel_cond     AND              \n";
    $stSql .= "     ll.cod_lote       = iil.cod_lote             AND              \n";
    $stSql .= "     ll.cod_localizacao = vla.cod_localizacao                      \n";

    return $stSql;
}

//Otimização da consulta de cadastro imobiliário - GRIS - 04/01/2006
function recuperaRelacionamentoListarConsulta(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoListarConsulta().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoListarConsulta()
{
    $stSql  = " SELECT imovel.inscricao_municipal                                                           \n";
    $stSql .= "      , unidade.tipo_vinculo                                                                 \n";
    $stSql .= "      , COALESCE(unidade.cod_construcao_dependente,unidade.cod_construcao) AS cod_construcao \n";
    $stSql .= "      , tipo_edificacao.nom_tipo                                                             \n";
    $stSql .= "   FROM imobiliario.imovel                                                                   \n";
    $stSql .= "        LEFT JOIN ( SELECT unidade_autonoma.inscricao_municipal         AS imovel_cond       \n";
    $stSql .= "                         , unidade_autonoma.cod_construcao                                   \n";
    $stSql .= "                         , unidade_autonoma.cod_tipo                                         \n";
    $stSql .= "                         , NULL                        AS cod_construcao_dependente          \n";
    $stSql .= "                         , 'Autônoma'                  AS tipo_vinculo                       \n";
    $stSql .= "                      FROM imobiliario.unidade_autonoma                                      \n";
    $stSql .= "                   UNION                                                                     \n";
    $stSql .= "                    SELECT unidade_dependente.inscricao_municipal         AS imovel_cond     \n";
    $stSql .= "                         , unidade_dependente.cod_construcao                                 \n";
    $stSql .= "                         , unidade_dependente.cod_tipo                                       \n";
    $stSql .= "                         , unidade_dependente.cod_construcao_dependente                      \n";
    $stSql .= "                         , 'Dependente'                                   AS tipo_vinculo    \n";
    $stSql .= "                      FROM imobiliario.unidade_dependente                                    \n";
    $stSql .= "                  )  AS unidade On imovel.inscricao_municipal = unidade.imovel_cond          \n";
    $stSql .= "         , imobiliario.construcao                                                            \n";

    $stSql .= "           LEFT JOIN ( \n";
    $stSql .= "        SELECT\n";
    $stSql .= "            IBC.*\n";
    $stSql .= "        FROM\n";
    $stSql .= "            imobiliario.baixa_construcao  AS IBC,\n";
    $stSql .= "            (\n";
    $stSql .= "            SELECT\n";
    $stSql .= "                MAX (TIMESTAMP) AS TIMESTAMP,\n";
    $stSql .= "                cod_construcao\n";
    $stSql .= "            FROM\n";
    $stSql .= "                imobiliario.baixa_construcao\n";
    $stSql .= "            GROUP BY\n";
    $stSql .= "                cod_construcao\n";
    $stSql .= "            ) AS BC\n";
    $stSql .= "        WHERE\n";
    $stSql .= "            IBC.cod_construcao = BC.cod_construcao AND\n";
    $stSql .= "            IBC.timestamp = BC.timestamp \n";
    $stSql .= "    ) ibc\n";
    $stSql .= "ON\n";
    $stSql .= "    construcao.cod_construcao = ibc.cod_construcao\n";

    $stSql .= "         , imobiliario.construcao_edificacao                                                 \n";
    $stSql .= "         , imobiliario.tipo_edificacao                                                       \n";
    $stSql .= "        , ( SELECT imovel_lote.inscricao_municipal                                           \n";
    $stSql .= "                 , imovel_lote.cod_lote                                                      \n";
    $stSql .= "              FROM imobiliario.imovel_lote                                                   \n";
    $stSql .= "                 , (SELECT MAX (timestamp)  AS timestamp                                     \n";
    $stSql .= "                         , inscricao_municipal                                               \n";
    $stSql .= "                      FROM imobiliario.imovel_lote                                           \n";
    $stSql .= "                     GROUP BY inscricao_municipal                                            \n";
    $stSql .= "                    ) AS imovel_lote_ultimo                                                  \n";
    $stSql .= "              WHERE imovel_lote.inscricao_municipal = imovel_lote_ultimo.inscricao_municipal \n";
    $stSql .= "                AND imovel_lote.timestamp           = imovel_lote_ultimo.timestamp           \n";
    $stSql .= "           ) AS imovel_lote                                                                  \n";
    $stSql .= "        , imobiliario.lote_localizacao                                                       \n";
    $stSql .= "   WHERE COALESCE(unidade.cod_construcao_dependente,unidade.cod_construcao) =  construcao.cod_construcao                 \n";
    $stSql .= "     AND construcao.cod_construcao  =  construcao_edificacao.cod_construcao                  \n";
    $stSql .= "     AND construcao_edificacao.cod_tipo  =  tipo_edificacao.cod_tipo                         \n";
    $stSql .= "     AND imovel.inscricao_municipal = imovel_lote.inscricao_municipal                        \n";
    $stSql .= "     AND imovel_lote.cod_lote = lote_localizacao.cod_lote                                    \n";

    return $stSql;
}

function recuperaEdificacoes( &$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = ""
) {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

    $stSql .= $this->montaRecuperaEdificacoes().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEdificacoes()
{
    $stSql  = "SELECT \n";
    $stSql .= "    cod_construcao ,\n";
    $stSql .= "    cod_construcao_autonoma ,\n";
    $stSql .= "    cod_tipo ,\n";
    $stSql .= "    nom_tipo ,\n";
    $stSql .= "    area_real ,\n";
    $stSql .= "    cod_processo ,\n";
    $stSql .= "    exercicio ,\n";
    $stSql .= "    imovel_cond ,\n";
    $stSql .= "    nom_condominio ,\n";
    $stSql .= "    area_unidade ,\n";
    $stSql .= "    tipo_vinculo ,\n";
    $stSql .= "    TO_CHAR(data_construcao::date,'dd/mm/yyyy') AS data_construcao ,\n";
    $stSql .= "    data_baixa ,\n";
    $stSql .= "    TO_CHAR(data_reativacao::date,'dd/mm/yyyy') AS data_reativacao ,\n";
    $stSql .= "    justificativa ,\n";
    $stSql .= "    TO_CHAR(timestamp_construcao::timestamp,'yyyy-mm-dd hh24:mi:ss.us') AS timestamp_construcao ,\n";
    $stSql .= "    TO_CHAR(timestamp_baixa::timestamp,'yyyy-mm-dd hh24:mi:ss.us') AS timestamp_baixa \n";
    $stSql .= "FROM \n";
    $stSql .= "    imobiliario.vw_edificacao \n";

    return $stSql;
}

//Otimização da consulta de cadastro imobiliário - GRIS - 04/01/2006
function recuperaRelacionamentoConsultaEdificacao( &$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = ""
) {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql .= $this->montaRecuperaRelacionamentoConsultaEdificacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoConsultaEdificacao()
{
    $stSql  = "SELECT imovel.inscricao_municipal                                                       \n";
    $stSql .= "     , unidade.tipo_vinculo                                                             \n";
    $stSql .= "     , unidade.cod_construcao                                                           \n";
    $stSql .= "     , to_char(data_construcao.data_construcao,'dd/mm/yyyy' ) AS data_construcao        \n";
    $stSql .= "     , to_char(BC.dt_inicio,'dd/mm/yyyy' ) AS data_baixa                                \n";
    $stSql .= "     , to_char(BC.dt_termino,'dd/mm/yyyy' ) AS data_termino                             \n";
    $stSql .= "     , BC.justificativa                                                                 \n";
    $stSql .= "     , construcao_edificacao.cod_tipo                                                   \n";
    $stSql .= "     , tipo_edificacao.nom_tipo                                                         \n";
    $stSql .= "     , construcao_processo.cod_processo                                                 \n";
    $stSql .= "     , construcao_processo.exercicio                                                    \n";
    $stSql .= "     , unidade.area                                                                     \n";
    $stSql .= "  FROM imobiliario.imovel                                                               \n";
    $stSql .= "       LEFT JOIN ( SELECT unidade_autonoma.inscricao_municipal AS imovel_cond           \n";
    $stSql .= "                        , unidade_autonoma.cod_construcao                               \n";
    $stSql .= "                        , unidade_autonoma.cod_tipo                                     \n";
    $stSql .= "                        , 'Autônoma'                  AS tipo_vinculo                   \n";
    $stSql .= "                        , area_unidade_autonoma.area                                    \n";
    $stSql .= "                     FROM imobiliario.unidade_autonoma                                  \n";
    $stSql .= "                        , ( SELECT area_unidade_autonoma.inscricao_municipal            \n";
    $stSql .= "                                 , area_unidade_autonoma.cod_construcao                 \n";
    $stSql .= "                                 , area_unidade_autonoma.cod_tipo                       \n";
    $stSql .= "                                 , area_unidade_autonoma.timestamp                      \n";
    $stSql .= "                                 , area_unidade_autonoma.area                           \n";
    $stSql .= "                              FROM imobiliario.area_unidade_autonoma                    \n";
    $stSql .= "                                 , ( SELECT inscricao_municipal                         \n";
    $stSql .= "                                          , cod_tipo                                    \n";
    $stSql .= "                                          , cod_construcao                              \n";
    $stSql .= "                                          , Max(timestamp) AS timestamp                 \n";
    $stSql .= "                                       FROM imobiliario.area_unidade_autonoma           \n";
    $stSql .= "                                     GROUP BY inscricao_municipal, cod_tipo, cod_construcao    \n";
    $stSql .= "                                   ) AS area_unidade_autonoma_max                       \n";
    $stSql .= "                            WHERE area_unidade_autonoma.inscricao_municipal = area_unidade_autonoma_max.inscricao_municipal                                                      \n";
    $stSql .= "                              AND area_unidade_autonoma.cod_construcao      = area_unidade_autonoma_max.cod_construcao                                                           \n";
    $stSql .= "                              AND area_unidade_autonoma.cod_tipo            = area_unidade_autonoma_max.cod_tipo                                                                 \n";
    $stSql .= "                              AND area_unidade_autonoma.timestamp           = area_unidade_autonoma_max.timestamp                                                                \n";
    $stSql .= "                          ) AS area_unidade_autonoma                                    \n";
    $stSql .= "                    WHERE imobiliario.unidade_autonoma.inscricao_municipal = area_unidade_autonoma.inscricao_municipal                                                          \n";
    $stSql .= "                       AND imobiliario.unidade_autonoma.cod_tipo           = area_unidade_autonoma.cod_tipo                                                                     \n";
    $stSql .= "                  UNION                                                                 \n";
    $stSql .= "                   SELECT unidade_dependente.inscricao_municipal AS imovel_cond         \n";
    $stSql .= "                        , unidade_dependente.cod_construcao_dependente As cod_construcao\n";
    $stSql .= "                        , unidade_dependente.cod_tipo                                   \n";
    $stSql .= "                        , 'Dependente' AS tipo_vinculo                                  \n";
    $stSql .= "                        , area_unidade_dependente.area                                  \n";
    $stSql .= "                     FROM imobiliario.unidade_dependente                                \n";
    $stSql .= "                        , ( SELECT area_unidade_dependente.inscricao_municipal          \n";
    $stSql .= "                                 , area_unidade_dependente.cod_tipo                     \n";
    $stSql .= "                                 , area_unidade_dependente.cod_construcao               \n";
    $stSql .= "                                 , area_unidade_dependente.cod_construcao_dependente    \n";
    $stSql .= "                                 , area_unidade_dependente.timestamp                    \n";
    $stSql .= "                                 , area_unidade_dependente.area                         \n";
    $stSql .= "                              FROM imobiliario.area_unidade_dependente                  \n";
    $stSql .= "                                 , ( SELECT inscricao_municipal                         \n";
    $stSql .= "                                          , cod_tipo                                    \n";
    $stSql .= "                                          , cod_construcao                              \n";
    $stSql .= "                                          , cod_construcao_dependente                   \n";
    $stSql .= "                                          , Max(timestamp) AS timestamp                 \n";
    $stSql .= "                                       FROM imobiliario.area_unidade_dependente         \n";
    $stSql .= "                                     GROUP BY inscricao_municipal, cod_tipo, cod_construcao, cod_construcao_dependente                           \n";
    $stSql .= "                                   ) AS area_unidade_dependente_max                     \n";
    $stSql .= "                            WHERE area_unidade_dependente.cod_tipo                  = area_unidade_dependente_max.cod_tipo                       \n";
    $stSql .= "                              AND area_unidade_dependente.cod_construcao            = area_unidade_dependente_max.cod_construcao                 \n";
    $stSql .= "                              AND area_unidade_dependente.cod_construcao_dependente = area_unidade_dependente_max.cod_construcao_dependente      \n";
    $stSql .= "                              AND area_unidade_dependente.timestamp                 = area_unidade_dependente_max.timestamp                      \n";
    $stSql .= "                          ) AS area_unidade_dependente                                                                                           \n";
    $stSql .= "                     WHERE imobiliario.unidade_dependente.inscricao_municipal       = area_unidade_dependente.inscricao_municipal                \n";
    $stSql .= "                       AND imobiliario.unidade_dependente.cod_tipo                  = area_unidade_dependente.cod_tipo                           \n";
    $stSql .= "                       AND imobiliario.unidade_dependente.cod_construcao            = area_unidade_dependente.cod_construcao                     \n";
    $stSql .= "                       AND imobiliario.unidade_dependente.cod_construcao_dependente = area_unidade_dependente.cod_construcao_dependente          \n";
    $stSql .= "                 )  AS unidade On imovel.inscricao_municipal = unidade.imovel_cond                                                               \n";
    $stSql .= "        , imobiliario.construcao                                                                                                                 \n";
    $stSql .= "          LEFT JOIN imobiliario.data_construcao       ON construcao.cod_construcao = data_construcao.cod_construcao                              \n";

    $stSql .= "          LEFT JOIN (                                                                    \n";
    $stSql .= "                SELECT                                                                   \n";
    $stSql .= "                    BCI.*                                                                \n";
    $stSql .= "                FROM                                                                     \n";
    $stSql .= "                    imobiliario.baixa_construcao AS BCI,                                 \n";
    $stSql .= "                    (                                                                    \n";
    $stSql .= "                    SELECT                                                               \n";
    $stSql .= "                        MAX (TIMESTAMP) AS TIMESTAMP,                                    \n";
    $stSql .= "                        cod_construcao                                                   \n";
    $stSql .= "                    FROM                                                                 \n";
    $stSql .= "                        imobiliario.baixa_construcao                                     \n";
    $stSql .= "                    GROUP BY                                                             \n";
    $stSql .= "                        cod_construcao                                                   \n";
    $stSql .= "                    ) AS BCC                                                             \n";
    $stSql .= "                WHERE                                                                    \n";
    $stSql .= "                    BCI.cod_construcao = BCC.cod_construcao AND                          \n";
    $stSql .= "                    BCI.timestamp = BCC.timestamp                                        \n";
    $stSql .= "          ) BC                                                                           \n";
    $stSql .= "          ON                                                                             \n";
    $stSql .= "                BC.cod_construcao = construcao.cod_construcao                            \n";

    $stSql .= "          LEFT JOIN ( SELECT construcao_processo.cod_construcao                                                                                  \n";
    $stSql .= "                           , construcao_processo.cod_processo                                                                                    \n";
    $stSql .= "                           , construcao_processo.exercicio                                                                                       \n";
    $stSql .= "                           , construcao_processo.timestamp                                                                                       \n";
    $stSql .= "                        FROM imobiliario.construcao_processo                                                                                     \n";
    $stSql .= "                           , ( SELECT max(construcao_processo.timestamp) AS timestamp                                                            \n";
    $stSql .= "                                    , construcao_processo.cod_construcao                                                                         \n";
    $stSql .= "                                 FROM imobiliario.construcao_processo                                                                            \n";
    $stSql .= "                             GROUP BY construcao_processo.cod_construcao) AS construcao_ultimo_processo                                          \n";
    $stSql .= "                                WHERE construcao_processo.cod_construcao = construcao_ultimo_processo.cod_construcao                             \n";
    $stSql .= "                                  AND construcao_processo.timestamp      = construcao_ultimo_processo.timestamp                                  \n";
    $stSql .= "                    ) AS construcao_processo  ON construcao.cod_construcao = construcao_processo.cod_construcao                                  \n";
    $stSql .= "        , imobiliario.construcao_edificacao                                                                                                      \n";
    $stSql .= "        , imobiliario.tipo_edificacao                                                                                                            \n";
    $stSql .= "  WHERE unidade.cod_construcao         = construcao.cod_construcao                                                                               \n";
    $stSql .= "    AND construcao.cod_construcao      = construcao_edificacao.cod_construcao                                                                    \n";
    $stSql .= "    AND construcao_edificacao.cod_tipo = tipo_edificacao.cod_tipo                                                                                \n";

    return $stSql;
}

function recuperaUnidadeAutonoma(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaUnidadeAutonoma().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaUnidadeAutonoma()
{
    $stSql  = "SELECT                                                                         \n";
    $stSql .= "    DISTINCT ON( UA.COD_construcao )                                           \n";
    $stSql .= "    UA.cod_construcao,                                                         \n";
    $stSql .= "    TE.nom_tipo,                                                               \n";
    $stSql .= "    IIL.cod_lote,                                                              \n";
    $stSql .= "    LL.valor as numero_lote,                                                   \n";
    $stSql .= "    VLA.valor_composto,                                                        \n";
    $stSql .= "    imobiliario.fn_calcula_area_construcao( UA.cod_construcao ) as area_total  \n";
    $stSql .= "FROM                                                                           \n";
    $stSql .= "    imobiliario.unidade_autonoma AS UA,                                        \n";
    $stSql .= "    imobiliario.tipo_edificacao  AS TE,                                        \n";
    $stSql .= "    imobiliario.imovel           AS I,                                         \n";
    $stSql .= "    (                                                                          \n";
    $stSql .= "        SELECT                                                                 \n";
    $stSql .= "            IIL.*                                                              \n";
    $stSql .= "        FROM                                                                   \n";
    $stSql .= "            imobiliario.imovel_lote IIL,                                       \n";
    $stSql .= "            (SELECT                                                            \n";
    $stSql .= "                MAX (TIMESTAMP) AS TIMESTAMP,                                  \n";
    $stSql .= "                INSCRICAO_MUNICIPAL                                            \n";
    $stSql .= "            FROM                                                               \n";
    $stSql .= "                imobiliario.imovel_lote                                        \n";
    $stSql .= "            GROUP BY                                                           \n";
    $stSql .= "                INSCRICAO_MUNICIPAL                                            \n";
    $stSql .= "            ) AS IL                                                            \n";
    $stSql .= "        WHERE                                                                  \n";
    $stSql .= "                IIL.INSCRICAO_MUNICIPAL = IL.INSCRICAO_MUNICIPAL               \n";
    $stSql .= "            AND IIL.TIMESTAMP = IL.TIMESTAMP                                   \n";
    $stSql .= "    ) AS IIL,                                                                  \n";
    $stSql .= "    imobiliario.lote                 AS L,                                     \n";
    $stSql .= "    imobiliario.lote_localizacao     AS LL,                                    \n";
    $stSql .= "    imobiliario.vw_localizacao_ativa AS VLA                                    \n";
    $stSql .= "WHERE                                                                          \n";
    $stSql .= "    UA.cod_tipo = TE.cod_tipo AND                                              \n";
    $stSql .= "    UA.inscricao_municipal = I.inscricao_municipal AND                         \n";
    $stSql .= "    I.inscricao_municipal = IIL.inscricao_municipal AND                        \n";
    $stSql .= "    IIL.cod_lote       = L.cod_lote                 AND                        \n";
    $stSql .= "    L.cod_lote         = LL.cod_lote                AND                        \n";
    $stSql .= "    LL.cod_localizacao = VLA.cod_localizacao                                   \n";

    return $stSql;
}

function recuperaRelacionamentoBaixa(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

    $stSql .= $this->montaRecuperaRelacionamentoBaixa().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoBaixa()
{
    $stSql  = " SELECT                                                            \n";
    $stSql .= "    dados.*              \n";
    $stSql .= "FROM (\n";
    $stSql .= "    SELECT\n";
    $stSql .= "        ii.inscricao_municipal AS imovel_cond,\n";
    $stSql .= "        ibc.timestamp AS timestamp_baixa,\n";
    $stSql .= "        CASE\n";
    $stSql .= "            WHEN ibc.dt_inicio IS NOT NULL AND ibc.dt_termino IS NULL THEN 'Baixado'\n";
    $stSql .= "            ELSE 'Ativo'\n";
    $stSql .= "        END AS situacao_construcao,\n";
    $stSql .= "        to_char(ibc.dt_inicio,'dd/mm/yyyy'    )    as dt_inicio_construcao,  \n";
    $stSql .= "        to_char(ibc.dt_termino,'dd/mm/yyyy'    )    as dt_termino_construcao,  \n";
    $stSql .= "        ibc.justificativa AS justificativa_construcao,\n";
    $stSql .= "        ibc.justificativa_termino AS justificativa_termino_construcao,\n";

    $stSql .= "        unidade.cod_tipo AS cod_tipo_autonoma,\n";

    $stSql .= "        unidade.cod_construcao AS cod_construcao_autonoma,\n";
    $stSql .= "        unidade.cod_construcao_dependente AS cod_construcao,\n";
    $stSql .= "        COALESCE (unidade.cod_construcao_dependente, unidade.cod_construcao) AS cod_construcao_dep_aut,\n";
    $stSql .= "        CASE\n";
    $stSql .= "            WHEN \n";
    $stSql .= "                ibc.dt_inicio IS NOT NULL AND \n";
    $stSql .= "                ibc.dt_termino IS NULL AND \n";
    $stSql .= "                unidade.dt_inicio IS NOT NULL AND \n";
    $stSql .= "                unidade.dt_termino IS NULL THEN 'Baixado'\n";
    $stSql .= "            ELSE 'Ativo'\n";
    $stSql .= "        END AS situacao_unidade,\n";
    $stSql .= "        to_char(unidade.dt_inicio,'dd/mm/yyyy'    )    as dt_inicio_unidade,  \n";
    $stSql .= "        to_char(unidade.dt_termino,'dd/mm/yyyy'    )    as dt_termino_unidade,  \n";
    $stSql .= "        unidade.justificativa AS justificativa_unidade,\n";
    $stSql .= "        unidade.justificativa_termino AS justificativa_termino_unidade,\n";
    $stSql .= "        unidade.tipo_vinculo,\n";
    $stSql .= "        ite.nom_tipo,\n";
    $stSql .= "        ice.cod_tipo,\n";
    $stSql .= "        ac.area_real,\n";
    $stSql .= "        CASE\n";
    $stSql .= "            WHEN unidade.tipo_vinculo = 'Dependente' THEN imobiliario.fn_area_unidade_dependente(ice.cod_construcao, ii.inscricao_municipal)::character varying\n";
    $stSql .= "            WHEN unidade.tipo_vinculo = 'Autônoma' THEN imobiliario.fn_area_unidade_autonoma(ice.cod_construcao, ii.inscricao_municipal)::character varying\n";
    $stSql .= "            ELSE NULL::character varying\n";
    $stSql .= "        END AS area_unidade,\n";
    $stSql .= "        to_char(dc.data_construcao,'dd/mm/yyyy' ) AS data_construcao,\n";
    $stSql .= "        iil.cod_lote,        \n";
    $stSql .= "        ll.valor as numero_lote,\n";
    $stSql .= "        vla.cod_localizacao,\n";
    $stSql .= "        vla.valor_composto\n";
    $stSql .= "    FROM\n";
    $stSql .= "        imobiliario.imovel AS ii\n";
    $stSql .= "    INNER JOIN ( \n";
    $stSql .= "            SELECT \n";
    $stSql .= "                ibua.dt_inicio,\n";
    $stSql .= "                ibua.dt_termino,\n";
    $stSql .= "                ibua.justificativa,\n";
    $stSql .= "                ibua.justificativa_termino,\n";
    $stSql .= "                unidade_autonoma.inscricao_municipal, \n";
    $stSql .= "                unidade_autonoma.cod_construcao, \n";
    $stSql .= "                unidade_autonoma.cod_tipo, \n";
    $stSql .= "                NULL  AS cod_construcao_dependente, \n";
    $stSql .= "                'Autônoma'  AS tipo_vinculo                       \n";
    $stSql .= "            FROM \n";
    $stSql .= "                imobiliario.unidade_autonoma\n";
    $stSql .= "            LEFT JOIN\n";
    $stSql .= "                (\n";
    $stSql .= "                SELECT                                             \n";
    $stSql .= "                    BAL.*                                          \n";
    $stSql .= "                FROM                                               \n";
    $stSql .= "                    imobiliario.baixa_unidade_autonoma AS BAL,            \n";
    $stSql .= "                    (                                                \n";
    $stSql .= "                    SELECT                                           \n";
    $stSql .= "                        MAX (TIMESTAMP) AS TIMESTAMP,                \n";
    $stSql .= "                        cod_construcao\n";
    $stSql .= "                    FROM                                             \n";
    $stSql .= "                        imobiliario.baixa_unidade_autonoma\n";
    $stSql .= "                    GROUP BY                                         \n";
    $stSql .= "                        cod_construcao\n";
    $stSql .= "                    ) AS BT                                          \n";
    $stSql .= "                WHERE                                                \n";
    $stSql .= "                    BAL.cod_construcao = BT.cod_construcao AND     \n";
    $stSql .= "                    BAL.timestamp = BT.timestamp           \n";
    $stSql .= "                ) AS ibua\n";
    $stSql .= "            ON\n";
    $stSql .= "                ibua.cod_construcao = imobiliario.unidade_autonoma.cod_construcao\n";
    $stSql .= "        UNION\n";
    $stSql .= "            SELECT \n";
    $stSql .= "                ibud.dt_inicio,\n";
    $stSql .= "                ibud.dt_termino,\n";
    $stSql .= "                ibud.justificativa,\n";
    $stSql .= "                ibud.justificativa_termino,\n";
    $stSql .= "                unidade_dependente.inscricao_municipal,\n";
    $stSql .= "                unidade_dependente.cod_construcao, \n";
    $stSql .= "                unidade_dependente.cod_tipo, \n";
    $stSql .= "                unidade_dependente.cod_construcao_dependente, \n";
    $stSql .= "                'Dependente' AS tipo_vinculo    \n";
    $stSql .= "            FROM \n";
    $stSql .= "                imobiliario.unidade_dependente\n";
    $stSql .= "            LEFT JOIN\n";

    $stSql .= "                (\n";
    $stSql .= "                SELECT                                             \n";
    $stSql .= "                    BAL.*                                          \n";
    $stSql .= "                FROM                                               \n";
    $stSql .= "                    imobiliario.baixa_unidade_dependente AS BAL,            \n";
    $stSql .= "                    (                                                \n";
    $stSql .= "                    SELECT                                           \n";
    $stSql .= "                        MAX (TIMESTAMP) AS TIMESTAMP,                \n";
    $stSql .= "                        cod_construcao,\n";
    $stSql .= "                        cod_construcao_dependente\n";
    $stSql .= "                    FROM                                             \n";
    $stSql .= "                        imobiliario.baixa_unidade_dependente\n";
    $stSql .= "                    GROUP BY                                         \n";
    $stSql .= "                        cod_construcao,\n";
    $stSql .= "                        cod_construcao_dependente\n";
    $stSql .= "                    ) AS BT                                          \n";
    $stSql .= "                WHERE                                                \n";
    $stSql .= "                    BAL.cod_construcao = BT.cod_construcao AND     \n";
    $stSql .= "                    BAL.cod_construcao_dependente = BT.cod_construcao_dependente AND     \n";
    $stSql .= "                    BAL.timestamp = BT.timestamp           \n";
    $stSql .= "                ) AS ibud\n";

    $stSql .= "            ON\n";
    $stSql .= "                ibud.cod_construcao_dependente = unidade_dependente.cod_construcao_dependente AND\n";
    $stSql .= "                ibud.cod_construcao = unidade_dependente.cod_construcao\n";
    $stSql .= "    )AS unidade \n";
    $stSql .= "    On \n";
    $stSql .= "        ii.inscricao_municipal = unidade.inscricao_municipal\n";
    $stSql .= "    INNER JOIN\n";
    $stSql .= "        imobiliario.construcao AS ic\n";
    $stSql .= "    ON\n";
    $stSql .= "        ic.cod_construcao = COALESCE(unidade.cod_construcao_dependente, unidade.cod_construcao)\n";
    $stSql .= "    LEFT JOIN\n";

    $stSql .= "        (\n";
    $stSql .= "            SELECT                                             \n";
    $stSql .= "                BAL.*                                          \n";
    $stSql .= "            FROM                                               \n";
    $stSql .= "                imobiliario.baixa_construcao AS BAL,            \n";
    $stSql .= "                (                                                \n";
    $stSql .= "                SELECT                                           \n";
    $stSql .= "                    MAX (TIMESTAMP) AS TIMESTAMP,                \n";
    $stSql .= "                    cod_construcao\n";
    $stSql .= "                FROM                                             \n";
    $stSql .= "                    imobiliario.baixa_construcao\n";
    $stSql .= "                GROUP BY                                         \n";
    $stSql .= "                    cod_construcao\n";
    $stSql .= "                ) AS BT                                          \n";
    $stSql .= "            WHERE                                                \n";
    $stSql .= "                BAL.cod_construcao = BT.cod_construcao AND     \n";
    $stSql .= "                BAL.timestamp = BT.timestamp           \n";
    $stSql .= "        ) AS ibc\n";

    $stSql .= "    ON\n";
    $stSql .= "        ibc.cod_construcao = COALESCE(unidade.cod_construcao_dependente, unidade.cod_construcao)\n";
    $stSql .= "    INNER JOIN\n";
    $stSql .= "        imobiliario.construcao_edificacao AS ice\n";
    $stSql .= "    ON\n";
    $stSql .= "        ice.cod_construcao = COALESCE(unidade.cod_construcao_dependente, unidade.cod_construcao)\n";
    $stSql .= "    INNER JOIN \n";
    $stSql .= "        imobiliario.tipo_edificacao AS ite \n";
    $stSql .= "    ON \n";
    $stSql .= "        ite.cod_tipo = ice.cod_tipo\n";
    $stSql .= "    LEFT JOIN ( \n";
    $stSql .= "        SELECT \n";
    $stSql .= "            ac.cod_construcao,\n";
    $stSql .= "            ac.timestamp,\n";
    $stSql .= "            ac.area_real\n";
    $stSql .= "        FROM \n";
    $stSql .= "            imobiliario.area_construcao ac, \n";
    $stSql .= "            ( \n";
    $stSql .= "                SELECT \n";
    $stSql .= "                    max(area_construcao.timestamp) AS timestamp, \n";
    $stSql .= "                    area_construcao.cod_construcao\n";
    $stSql .= "                FROM \n";
    $stSql .= "                    imobiliario.area_construcao\n";
    $stSql .= "                GROUP BY area_construcao.cod_construcao\n";
    $stSql .= "            ) mac\n";
    $stSql .= "        WHERE \n";
    $stSql .= "            ac.cod_construcao = mac.cod_construcao AND \n";
    $stSql .= "            ac.timestamp = mac.timestamp\n";
    $stSql .= "    ) ac \n";
    $stSql .= "    ON \n";
    $stSql .= "        ac.cod_construcao = COALESCE(unidade.cod_construcao_dependente, unidade.cod_construcao)\n";
    $stSql .= "    LEFT JOIN \n";
    $stSql .= "        imobiliario.data_construcao AS dc\n";
    $stSql .= "    ON \n";
    $stSql .= "        dc.cod_construcao = COALESCE(unidade.cod_construcao_dependente, unidade.cod_construcao)\n";

    $stSql .= "    INNER JOIN\n";
    $stSql .= "        (                                                              \n";
    $stSql .= "            SELECT                                                     \n";
    $stSql .= "                IIL.*                                                  \n";
    $stSql .= "            FROM                                                       \n";
    $stSql .= "                imobiliario.imovel_lote IIL,                           \n";
    $stSql .= "                (SELECT                                                \n";
    $stSql .= "                    MAX (TIMESTAMP) AS TIMESTAMP,                      \n";
    $stSql .= "                    INSCRICAO_MUNICIPAL                                \n";
    $stSql .= "                FROM                                                   \n";
    $stSql .= "                    imobiliario.imovel_lote                            \n";
    $stSql .= "                GROUP BY                                               \n";
    $stSql .= "                    INSCRICAO_MUNICIPAL                                \n";
    $stSql .= "                ) AS IL                                                \n";
    $stSql .= "            WHERE                                                      \n";
    $stSql .= "                    IIL.INSCRICAO_MUNICIPAL = IL.INSCRICAO_MUNICIPAL   \n";
    $stSql .= "                AND IIL.TIMESTAMP = IL.TIMESTAMP                       \n";
    $stSql .= "        ) AS iil\n";
    $stSql .= "    ON\n";
    $stSql .= "        iil.inscricao_municipal = ii.inscricao_municipal\n";

    $stSql .= "    INNER JOIN\n";
    $stSql .= "        imobiliario.lote_localizacao as ll\n";
    $stSql .= "    ON\n";
    $stSql .= "        ll.cod_lote = iil.cod_lote\n";

    $stSql .= "    INNER JOIN\n";
    $stSql .= "        (                                                              \n";
    $stSql .= "            SELECT                                                     \n";
    $stSql .= "                loc.codigo_composto AS valor_composto,                 \n";
    $stSql .= "                loc.cod_localizacao                                    \n";
    $stSql .= "            FROM                                                       \n";
    $stSql .= "                imobiliario.localizacao loc                            \n";

    $stSql .= "            LEFT JOIN (                                                 \n";
    $stSql .= "                        SELECT                                             \n";
    $stSql .= "                            BAL.*                                          \n";
    $stSql .= "                        FROM                                               \n";
    $stSql .= "                            imobiliario.baixa_localizacao AS BAL,            \n";
    $stSql .= "                            (                                                \n";
    $stSql .= "                            SELECT                                           \n";
    $stSql .= "                                MAX (TIMESTAMP) AS TIMESTAMP,                \n";
    $stSql .= "                                cod_localizacao                              \n";
    $stSql .= "                            FROM                                             \n";
    $stSql .= "                                imobiliario.baixa_localizacao                \n";
    $stSql .= "                            GROUP BY                                         \n";
    $stSql .= "                                cod_localizacao                              \n";
    $stSql .= "                            ) AS BT                                          \n";
    $stSql .= "                        WHERE                                                \n";
    $stSql .= "                            BAL.cod_localizacao = BT.cod_localizacao AND     \n";
    $stSql .= "                            BAL.timestamp = BT.timestamp                     \n";
    $stSql .= "                    ) bl                                                     \n";
    $stSql .= "            ON                                                       \n";
    $stSql .= "                bl.cod_localizacao = loc.cod_localizacao             \n";
    $stSql .= "            WHERE                                                    \n";
    $stSql .= "                ((bl.dt_inicio IS NULL) OR (bl.dt_inicio IS NOT NULL AND bl.dt_termino IS NOT NULL) AND bl.cod_localizacao=loc.cod_localizacao)\n";
    $stSql .= "        ) AS vla                                                       \n";
    $stSql .= "    ON\n";
    $stSql .= "        ll.cod_localizacao = vla.cod_localizacao\n";
    $stSql .= "    )AS dados\n";

    return $stSql;
}
}
