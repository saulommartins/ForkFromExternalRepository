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
  * Classe de mapeamento da tabela ECONOMICO.ATIVIDADE_MODALIDADE_LANCAMENTO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMAtividadeModalidadeLancamento.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.13
*/

/*
$Log$
Revision 1.6  2006/11/08 10:34:36  fabio
alteração do uc_05.02.13

Revision 1.5  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.ATIVIDADE_MODALIDADE_LANCAMENTO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMAtividadeModalidadeLancamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMAtividadeModalidadeLancamento()
{
    parent::Persistente();
    $this->setTabela('economico.atividade_modalidade_lancamento');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_atividade,cod_modalidade,dt_inicio');

    $this->AddCampo('cod_atividade' ,'integer',true ,''    ,true ,true );
    $this->AddCampo('cod_modalidade','integer',true ,''    ,true ,true );
    $this->AddCampo('dt_inicio'     ,'date'   ,true ,''    ,true ,false);
    $this->AddCampo('dt_baixa'      ,'date'   ,false,''    ,false,false);
    $this->AddCampo('motivo_baixa'  ,'varchar',false,''    ,false,false);
    $this->AddCampo('valor'         ,'numeric',false,'14,2',false,false);
    $this->AddCampo('percentual'    ,'boolean',false,''    ,false,false);
}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT                                                                  \n";
    $stSql .= "     AML.COD_ATIVIDADE                                                  \n";
    $stSql .= "   , AT.NOM_ATIVIDADE                                                   \n";
    $stSql .= "   , LN.VALOR_COMPOSTO                                                  \n";
    $stSql .= "   , AML.COD_MODALIDADE                                                 \n";
    $stSql .= "   , EML.NOM_MODALIDADE                                                 \n";
    $stSql .= "   , TO_CHAR ( AML.DT_INICIO,'dd/mm/yyyy' ) AS DT_VIGENCIA_MODALIDADE   \n";
    $stSql .= "   , TO_CHAR ( AML.DT_BAIXA ,'dd/mm/yyyy' ) AS DT_BAIXA_MODALIDADE      \n";
    $stSql .= "   , AML.MOTIVO_BAIXA AS MOTIVO_BAIXA_MODALIDADE                        \n";
    $stSql .= "   , AML.VALOR                                                          \n";
    $stSql .= "   , AML.PERCENTUAL                                                     \n";
    $stSql .= "   , CASE                                                               \n";
    $stSql .= "         WHEN AML.PERCENTUAL IS FALSE                                   \n";
    $stSql .= "         THEN                                                           \n";
    $stSql .= "         CASE                                                           \n";
    $stSql .= "             WHEN AMLI.cod_indicador IS NOT NULL                        \n";
    $stSql .= "             THEN 'Indicador'                                           \n";
    $stSql .= "             ELSE 'Moeda'                                               \n";
    $stSql .= "         END                                                            \n";
    $stSql .= "     END                                    AS MOEDA_INDICADOR          \n";
    $stSql .= "   , CASE                                                               \n";
    $stSql .= "         WHEN AML.PERCENTUAL IS FALSE                                   \n";
    $stSql .= "         THEN                                                           \n";
    $stSql .= "         CASE                                                           \n";
    $stSql .= "             WHEN AMLI.cod_indicador IS NOT NULL                        \n";
    $stSql .= "             THEN AMLI.cod_indicador                                    \n";
    $stSql .= "             ELSE AMLM.cod_moeda                                        \n";
    $stSql .= "         END                                                            \n";
    $stSql .= "     END                                    AS COD_MOEDA_INDICADOR      \n";
    $stSql .= "FROM                                                                    \n";
    $stSql .= "  economico.atividade_modalidade_lancamento AS AML                      \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "  economico.atividade AS AT                                             \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "    AT.COD_ATIVIDADE = AML.COD_ATIVIDADE                                \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "    (                                                                   \n";
    $stSql .= "     SELECT                                                             \n";
    $stSql .= "         LN.*,                                                          \n";
    $stSql .= "         LN2.valor                                                      \n";
    $stSql .= "     FROM (                                                             \n";
    $stSql .= "           SELECT                                                       \n";
    $stSql .= "               MAX(LN.cod_nivel) AS cod_nivel,                          \n";
    $stSql .= "               LN.cod_vigencia ,LN.cod_atividade,                       \n";
    $stSql .= "               economico.fn_consulta_atividade                          \n";
    $stSql .= "                 (LN.cod_vigencia,LN.cod_atividade) AS valor_composto,  \n";
    $stSql .= "               publico.fn_mascarareduzida                               \n";
    $stSql .= "                 (economico.fn_consulta_atividade                       \n";
    $stSql .= "                 (LN.cod_vigencia,LN.cod_atividade) ) AS valor_reduzido \n";
    $stSql .= "           FROM                                                         \n";
    $stSql .= "               economico.nivel_atividade_valor AS LN                    \n";
    $stSql .= "           WHERE                                                        \n";
    $stSql .= "               LN.valor <> '0'                                          \n";
    $stSql .= "           GROUP BY                                                     \n";
    $stSql .= "               LN.cod_vigencia,                                         \n";
    $stSql .= "               LN.cod_atividade) AS LN,                                 \n";
    $stSql .= "      economico.nivel_atividade_valor AS LN2                            \n";
    $stSql .= "     WHERE                                                              \n";
    $stSql .= "         LN.cod_nivel       = LN2.cod_nivel AND                         \n";
    $stSql .= "         LN.cod_atividade   = LN2.cod_atividade AND                     \n";
    $stSql .= "         LN.cod_vigencia    = LN2.cod_vigencia                          \n";
    $stSql .= "    ) AS LN                                                             \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "    LN.COD_ATIVIDADE = AML.COD_ATIVIDADE                                \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "  economico.modalidade_lancamento AS EML                                \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "    EML.COD_MODALIDADE = AML.COD_MODALIDADE                             \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "  economico.atividade_modalidade_indicador AS AMLI                      \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "          AMLI.COD_ATIVIDADE  = AML.COD_ATIVIDADE                       \n";
    $stSql .= "   AND    AMLI.COD_MODALIDADE = AML.COD_MODALIDADE                      \n";
    $stSql .= "   AND    AMLI.DT_INICIO      = AML.DT_INICIO                           \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "  economico.atividade_modalidade_moeda     AS AMLM                      \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "          AMLM.COD_ATIVIDADE  = AML.COD_ATIVIDADE                       \n";
    $stSql .= "   AND    AMLM.COD_MODALIDADE = AML.COD_MODALIDADE                      \n";
    $stSql .= "   AND    AMLM.DT_INICIO      = AML.DT_INICIO                           \n";

    return $stSql;
}

function recuperaModalidadeAtividade(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaModalidadeAtividade().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaModalidadeAtividade()
{
    $stSql  = "SELECT                                                                  \n";
    $stSql .= "    AML.COD_ATIVIDADE,                                                  \n";
    $stSql .= "    AML.COD_MODALIDADE,                                                 \n";
    $stSql .= "    EML.NOM_MODALIDADE                                                  \n";
    $stSql .= "FROM                                                                    \n";
    $stSql .= "  economico.atividade_modalidade_lancamento AS AML                        \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "  economico.modalidade_lancamento AS EML                                  \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "    EML.COD_MODALIDADE = AML.COD_MODALIDADE                             \n";
    $stSql .= "WHERE                                                                   \n";
    $stSql .= "    AML.DT_BAIXA     IS NULL  AND                                       \n";
    $stSql .= "    AML.MOTIVO_BAIXA IS NULL                                            \n";

    return $stSql;
}

function recuperaBaixados(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaBaixados().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaBaixados()
{
    $stSql  = "SELECT                                           \n";
    $stSql .= "    AML.DT_BAIXA,                                \n";
    $stSql .= "    AML.MOTIVO_BAIXA                             \n";
    $stSql .= "FROM                                             \n";
    $stSql .= "  economico.atividade_modalidade_lancamento AS AML \n";

    return $stSql;
}

function listaAtividadeModalidadeLancamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaListaAtividadeModalidadeLancamento().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaListaAtividadeModalidadeLancamento()
{
    $stSql  = "
        SELECT
            atividade_modalidade_lancamento.cod_modalidade,
            atividade_modalidade_lancamento.cod_atividade,
            atividade_cadastro_economico.inscricao_economica,
            modalidade_lancamento.nom_modalidade

        FROM
            economico.atividade

        INNER JOIN
            (
                SELECT
                    tmp.*

                FROM
                    economico.atividade_cadastro_economico AS tmp

                INNER JOIN
                    (
                        SELECT
                            max(atividade_cadastro_economico.ocorrencia_atividade) AS ocorrencia_atividade,
                            atividade_cadastro_economico.inscricao_economica

                        FROM
                            economico.atividade_cadastro_economico

                        GROUP BY
                            atividade_cadastro_economico.inscricao_economica
                    )AS tmp2
                ON
                    tmp.ocorrencia_atividade = tmp2.ocorrencia_atividade
                    AND tmp.inscricao_economica = tmp2.inscricao_economica

                WHERE
                    tmp.principal = true
                    AND tmp.dt_inicio <= now()::date
                    AND CASE WHEN tmp.dt_termino IS NOT NULL THEN
                            CASE WHEN tmp.dt_termino >= now()::date THEN
                                true
                            ELSE
                                false
                            END
                        ELSE
                            true
                        END
            )AS atividade_cadastro_economico
        ON
            atividade_cadastro_economico.cod_atividade = atividade.cod_atividade

        INNER JOIN
            (
                SELECT
                    tmp.*

                FROM
                    economico.atividade_modalidade_lancamento AS tmp

                INNER JOIN
                    (
                        SELECT
                            max(dt_inicio) AS dt_inicio,
                            cod_atividade

                        FROM
                            economico.atividade_modalidade_lancamento

                        GROUP BY
                            cod_atividade
                    )AS tmp2
                ON
                    tmp2.cod_atividade = tmp.cod_atividade
                    AND tmp2.dt_inicio = tmp.dt_inicio

                WHERE
                    tmp.dt_inicio <= now()::date
                    AND CASE WHEN tmp.dt_baixa IS NOT NULL THEN
                            CASE WHEN tmp.dt_baixa > now()::date THEN
                                true
                            ELSE
                                false
                            END
                        ELSE
                            true
                        END
            )AS atividade_modalidade_lancamento
        ON
            atividade_modalidade_lancamento.cod_atividade = atividade.cod_atividade

        INNER JOIN
            economico.modalidade_lancamento
        ON
            modalidade_lancamento.cod_modalidade = atividade_modalidade_lancamento.cod_modalidade
    ";

    return $stSql;
}

}
