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
     * Classe de mapeamento para a tabela IMOBILIARIO.LOTE_URBANO
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMLoteUrbano.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.08
*/

/*
$Log$
Revision 1.8  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.LOTE_URBANO
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMLoteUrbano extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMLoteUrbano()
{
    parent::Persistente();
    $this->setTabela('imobiliario.lote_urbano');

    $this->setCampoCod('cod_lote');
    $this->setComplementoChave('');

    $this->AddCampo('cod_lote','integer',true,'',true,true);

}

function montaRecuperaRelacionamento()
{
    $stSQL  = " SELECT                                                      \n";
    $stSQL .= "    L.COD_LOTE,                                              \n";
    $stSQL .= "    L.TIMESTAMP,                                             \n";
    $stSQL .= "    TO_CHAR(L.DT_INSCRICAO,'dd/mm/yyyy') AS DT_INSCRICAO,    \n";
    $stSQL .= "    AL.COD_GRANDEZA,                                         \n";
    $stSQL .= "    AL.COD_UNIDADE,                                          \n";
    $stSQL .= "    AL.AREA_REAL,                                            \n";
    $stSQL .= "    PM.VL_PROFUNDIDADE_MEDIA,                                \n";
    $stSQL .= "    LL.COD_LOCALIZACAO,                                      \n";
    $stSQL .= "    LL.VALOR,                                                \n";
    $stSQL .= "    LB.COD_BAIRRO,                                           \n";
    $stSQL .= "    LB.COD_UF,                                               \n";
    $stSQL .= "    LB.COD_MUNICIPIO,                                        \n";
    $stSQL .= "    LPA.validado,                                            \n";
    $stSQL .= "    LA.valor_composto,                                       \n";
    $stSQL .= "    LA.nom_localizacao                                       \n";

    $stSQL .= " FROM                                                        \n";
    $stSQL .= "    imobiliario.vw_lote_ativo                AS L            \n";
    $stSQL .= " LEFT JOIN (                                                 \n";
    $stSQL .= "      SELECT                                                 \n";
    $stSQL .= "         lote_parcelado.cod_lote,                            \n";
    $stSQL .= "         lote_parcelado.validado                             \n";
    $stSQL .= "      FROM                                                   \n";
    $stSQL .= "         imobiliario.lote_parcelado,                         \n";
    $stSQL .= "         (SELECT                                             \n";
    $stSQL .= "             cod_lote,                                       \n";
    $stSQL .= "             Max(cod_parcelamento) as cod_ult_parcelamento   \n";
    $stSQL .= "          FROM                                               \n";
    $stSQL .= "             imobiliario.lote_parcelado                      \n";
    $stSQL .= "          GROUP BY cod_lote ) as ultimo_parcelamento         \n";
    $stSQL .= "      WHERE                                                  \n";
    $stSQL .= "         lote_parcelado.cod_lote = ultimo_parcelamento.cod_lote                         \n";
    $stSQL .= "         AND lote_parcelado.cod_parcelamento = ultimo_parcelamento.cod_ult_parcelamento \n";
    $stSQL .= "    )     AS LPA ON                                             \n";
    $stSQL .= "    L.cod_lote = LPA.cod_lote,                                  \n";
    $stSQL .= "    imobiliario.lote_urbano                 AS LU,              \n";
    $stSQL .= "    imobiliario.vw_area_lote_atual          AS AL,              \n";
    $stSQL .= "    imobiliario.profundidade_media          AS PM,              \n";
    $stSQL .= "    imobiliario.lote_localizacao            AS LL,              \n";
    $stSQL .= "    imobiliario.lote_bairro                 AS LB,              \n";
    $stSQL .= "    (                                                           \n";
    $stSQL .= "       SELECT                                                   \n";
    $stSQL .= "           loc.nom_localizacao,                                 \n";
    $stSQL .= "           loc.codigo_composto AS valor_composto,               \n";
    $stSQL .= "           loc.cod_localizacao                                  \n";
    $stSQL .= "       FROM                                                     \n";
    $stSQL .= "           imobiliario.localizacao loc                          \n";

    $stSQL .= "           LEFT JOIN (                                          \n";
    $stSQL .= "                    SELECT                                      \n";
    $stSQL .= "                        BAL.*                                   \n";
    $stSQL .= "                    FROM                                        \n";
    $stSQL .= "                        imobiliario.baixa_localizacao AS BAL,   \n";
    $stSQL .= "                        (                                       \n";
    $stSQL .= "                            SELECT                              \n";
    $stSQL .= "                                MAX (TIMESTAMP) AS TIMESTAMP,   \n";
    $stSQL .= "                                cod_localizacao                 \n";
    $stSQL .= "                            FROM                                \n";
    $stSQL .= "                                imobiliario.baixa_localizacao   \n";
    $stSQL .= "                            GROUP BY                            \n";
    $stSQL .= "                                cod_localizacao                 \n";
    $stSQL .= "                        ) AS BLL                                \n";
    $stSQL .= "                    WHERE                                       \n";
    $stSQL .= "                        BAL.cod_localizacao = BLL.cod_localizacao AND   \n";
    $stSQL .= "                        BAL.timestamp = BLL.timestamp                   \n";
    $stSQL .= "               ) bl                                             \n";
    $stSQL .= "               ON                                               \n";
    $stSQL .= "                    loc.cod_localizacao = bl.cod_localizacao    \n";
    $stSQL .= "           WHERE                                                \n";
    $stSQL .= "              ((bl.dt_inicio IS NULL) OR (bl.dt_inicio IS NOT NULL AND bl.dt_termino IS NOT NULL)                             AND bl.cod_localizacao = loc.cod_localizacao)     \n";

    $stSQL .= "                                                                \n";
    $stSQL .= "     ) AS LA                                                    \n";
    $stSQL .= " WHERE                                                          \n";
    $stSQL .= "    LU.COD_LOTE = L.COD_LOTE AND                                \n";
    $stSQL .= "    AL.COD_LOTE = L.COD_LOTE AND                                \n";
    $stSQL .= "    PM.COD_LOTE = L.COD_LOTE AND                                \n";
    $stSQL .= "    LL.COD_LOTE = L.COD_LOTE AND                                \n";
    $stSQL .= "    LB.COD_LOTE = L.COD_LOTE AND                                \n";
    $stSQL .= "    LA.COD_LOCALIZACAO  = LL.COD_LOCALIZACAO AND                \n";
    $stSQL .= "    ( LPA.validado IS NULL OR LPA.validado = true )             \n";

    return $stSQL;
}

function recuperaRelacionamentoLoteValidado(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoLoteValidado().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoLoteValidado()
{
    $stSQL  = " SELECT L.COD_LOTE
                     , TO_CHAR(L.DT_INSCRICAO,'dd/mm/yyyy') AS DT_INSCRICAO
                     , AL.COD_GRANDEZA
                     , AL.COD_UNIDADE
                     , AL.AREA_REAL
                     , PM.VL_PROFUNDIDADE_MEDIA
                     , LL.COD_LOCALIZACAO
                     , LL.VALOR
                     , LB.COD_BAIRRO
                     , LB.COD_UF
                     , LB.COD_MUNICIPIO
                     , LA.VALOR_COMPOSTO
                     , LP.COD_PROCESSO
                     , LP.ANO_EXERCICIO
                     , PS.COD_PARCELAMENTO
                  FROM imobiliario.vw_lote_ativo AS L
             LEFT JOIN imobiliario.lote_processo AS LP
                    ON L.COD_LOTE = LP.COD_LOTE
                     , imobiliario.lote_urbano AS LU
                     , imobiliario.vw_area_lote_atual AS AL
                     , imobiliario.profundidade_media AS PM
                     , imobiliario.lote_localizacao AS LL
                     , imobiliario.lote_bairro AS LB
                     , imobiliario.vw_localizacao_ativa AS LA
                     , imobiliario.lote_parcelado AS LPA
                     , imobiliario.parcelamento_solo AS PS
                 WHERE L.COD_LOTE = LU.COD_LOTE
                   AND L.COD_LOTE = AL.COD_LOTE
                   AND L.COD_LOTE = PM.COD_LOTE
                   AND L.COD_LOTE = LL.COD_LOTE
                   AND L.COD_LOTE = LB.COD_LOTE
                   AND LL.COD_LOCALIZACAO = LA.COD_LOCALIZACAO
                   AND L.COD_LOTE = LPA.COD_LOTE
                   AND LPA.VALIDADO = FALSE
                   AND LPA.COD_PARCELAMENTO = PS.COD_PARCELAMENTO
                   AND PS.COD_TIPO = 2
    ";

    return $stSQL;
}

function recuperaLoteAglutinar(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLoteAglutinar().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaLoteAglutinar()
{
    $stSql  = " SELECT                                                    \n";
    $stSql .= "     L.COD_LOTE,                                           \n";
    $stSql .= "     UA.COD_CONSTRUCAO,                                    \n";
    $stSql .= "     ED.NOM_TIPO,                                          \n";
    $stSql .= "     IM.INSCRICAO_MUNICIPAL,                               \n";
    $stSql .= "     LL.VALOR                                              \n";
    $stSql .= " FROM                                                      \n";
    $stSql .= "     imobiliario.lote             AS L,                        \n";
    $stSql .= "     imobiliario.imovel           AS IM,                       \n";
    $stSql .= "     imobiliario.unidade_autonoma AS UA,                       \n";
    $stSql .= "     imobiliario.tipo_edificacao  AS ED,                       \n";
    $stSql .= "     imobiliario.lote_localizacao AS LL                        \n";
    $stSql .= " WHERE                                                     \n";
    $stSql .= "     L.COD_LOTE = IM.COD_LOTE AND                          \n";
    $stSql .= "     L.COD_LOTE = LL.COD_LOTE AND                          \n";
    $stSql .= "     IM.INSCRICAO_MUNICIPAL = UA.INSCRICAO_MUNICIPAL AND   \n";
    $stSql .= "     UA.COD_TIPO = ED.COD_TIPO                             \n";

    return $stSql;
}

function recuperaRelacionamentoLoteBaixado(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoLoteBaixado().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $stOrdem, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoLoteBaixado()
{
    $stSQL  = "     SELECT                                                      \n";
    $stSQL .= "        L.COD_LOTE,                                              \n";
    $stSQL .= "        L.TIMESTAMP,                                             \n";
    $stSQL .= "        TO_CHAR(L.DT_INSCRICAO,'dd/mm/yyyy') AS DT_INSCRICAO,    \n";
    $stSQL .= "        bl.TIMESTAMP AS timestamp_baixa,                         \n";
    $stSQL .= "        TO_CHAR(bl.dt_inicio,'dd/mm/yyyy') AS dt_inicio,         \n";
    $stSQL .= "        bl.justificativa,                                        \n";
    $stSQL .= "        AL.COD_GRANDEZA,                                         \n";
    $stSQL .= "        AL.COD_UNIDADE,                                          \n";
    $stSQL .= "        AL.AREA_REAL,                                            \n";
    $stSQL .= "        PM.VL_PROFUNDIDADE_MEDIA,                                \n";
    $stSQL .= "        LL.COD_LOCALIZACAO,                                      \n";
    $stSQL .= "        LL.VALOR,                                                \n";
    $stSQL .= "        LB.COD_BAIRRO,                                           \n";
    $stSQL .= "        LB.COD_UF,                                               \n";
    $stSQL .= "        LB.COD_MUNICIPIO,                                        \n";
    $stSQL .= "        LPA.validado,                                            \n";
    $stSQL .= "        LA.valor_composto,                                       \n";
    $stSQL .= "        LA.nom_localizacao                                       \n";

    $stSQL .= "     FROM                                                        \n";

    $stSQL .= "        imobiliario.lote AS L                                    \n";

    $stSQL .= "        INNER JOIN (                                             \n";
    $stSQL .= "             SELECT                                              \n";
    $stSQL .= "                 BAL.*                                           \n";
    $stSQL .= "             FROM                                                \n";
    $stSQL .= "                 imobiliario.baixa_lote AS BAL,                  \n";
    $stSQL .= "                 (                                               \n";
    $stSQL .= "                 SELECT                                          \n";
    $stSQL .= "                     MAX (TIMESTAMP) AS TIMESTAMP,               \n";
    $stSQL .= "                     cod_lote                                    \n";
    $stSQL .= "                 FROM                                            \n";
    $stSQL .= "                     imobiliario.baixa_lote                      \n";
    $stSQL .= "                 GROUP BY                                        \n";
    $stSQL .= "                     cod_lote                                    \n";
    $stSQL .= "                 ) AS BT                                         \n";
    $stSQL .= "             WHERE                                               \n";
    $stSQL .= "                 BAL.cod_lote = BT.cod_lote AND                  \n";
    $stSQL .= "                 BAL.timestamp = BT.timestamp                    \n";
    $stSQL .= "         ) bl                                                    \n";
    $stSQL .= "         ON                                                      \n";
    $stSQL .= "             L.cod_lote = bl.cod_lote AND                        \n";
    $stSQL .= "             bl.dt_termino IS NULL                               \n";

    $stSQL .= "     LEFT JOIN (                                                 \n";
    $stSQL .= "          SELECT                                                 \n";
    $stSQL .= "             lote_parcelado.cod_lote,                            \n";
    $stSQL .= "             lote_parcelado.validado                             \n";
    $stSQL .= "          FROM                                                   \n";
    $stSQL .= "             imobiliario.lote_parcelado,                         \n";
    $stSQL .= "             (SELECT                                             \n";
    $stSQL .= "                 cod_lote,                                       \n";
    $stSQL .= "                 Max(cod_parcelamento) as cod_ult_parcelamento   \n";
    $stSQL .= "              FROM                                               \n";
    $stSQL .= "                 imobiliario.lote_parcelado                      \n";
    $stSQL .= "              GROUP BY cod_lote ) as ultimo_parcelamento         \n";
    $stSQL .= "          WHERE                                                  \n";
    $stSQL .= "             lote_parcelado.cod_lote = ultimo_parcelamento.cod_lote                         \n";
    $stSQL .= "             AND lote_parcelado.cod_parcelamento = ultimo_parcelamento.cod_ult_parcelamento \n";
    $stSQL .= "        )     AS LPA ON                                             \n";
    $stSQL .= "        L.cod_lote = LPA.cod_lote,                                  \n";
    $stSQL .= "        imobiliario.lote_urbano                 AS LU,              \n";
    $stSQL .= "        imobiliario.vw_area_lote_atual          AS AL,              \n";
    $stSQL .= "        imobiliario.profundidade_media          AS PM,              \n";
    $stSQL .= "        imobiliario.lote_localizacao            AS LL,              \n";
    $stSQL .= "        imobiliario.lote_bairro                 AS LB,              \n";
    $stSQL .= "        (                                                           \n";
    $stSQL .= "           SELECT                                                   \n";
    $stSQL .= "               loc.nom_localizacao,                                 \n";
    $stSQL .= "               loc.codigo_composto AS valor_composto,               \n";
    $stSQL .= "               loc.cod_localizacao                                  \n";
    $stSQL .= "           FROM                                                     \n";
    $stSQL .= "               imobiliario.localizacao loc                          \n";

    $stSQL .= "               LEFT JOIN (                                          \n";
    $stSQL .= "                        SELECT                                      \n";
    $stSQL .= "                            BAL.*                                   \n";
    $stSQL .= "                        FROM                                        \n";
    $stSQL .= "                            imobiliario.baixa_localizacao AS BAL,   \n";
    $stSQL .= "                            (                                       \n";
    $stSQL .= "                                SELECT                              \n";
    $stSQL .= "                                    MAX (TIMESTAMP) AS TIMESTAMP,   \n";
    $stSQL .= "                                    cod_localizacao                 \n";
    $stSQL .= "                                FROM                                \n";
    $stSQL .= "                                    imobiliario.baixa_localizacao   \n";
    $stSQL .= "                                GROUP BY                            \n";
    $stSQL .= "                                    cod_localizacao                 \n";
    $stSQL .= "                            ) AS BLL                                \n";
    $stSQL .= "                        WHERE                                       \n";
    $stSQL .= "                            BAL.cod_localizacao = BLL.cod_localizacao AND   \n";
    $stSQL .= "                            BAL.timestamp = BLL.timestamp                   \n";
    $stSQL .= "                   ) bl                                             \n";
    $stSQL .= "                   ON                                               \n";
    $stSQL .= "                        loc.cod_localizacao = bl.cod_localizacao    \n";
    $stSQL .= "               WHERE                                                \n";
    $stSQL .= "                  ((bl.dt_inicio IS NULL) OR (bl.dt_inicio IS NOT NULL AND bl.dt_termino IS NOT NULL)                             AND bl.cod_localizacao = loc.cod_localizacao)     \n";

    $stSQL .= "         ) AS LA                                                    \n";
    $stSQL .= "     WHERE                                                          \n";
    $stSQL .= "        LU.COD_LOTE = L.COD_LOTE AND                                \n";
    $stSQL .= "        AL.COD_LOTE = L.COD_LOTE AND                                \n";
    $stSQL .= "        PM.COD_LOTE = L.COD_LOTE AND                                \n";
    $stSQL .= "        LL.COD_LOTE = L.COD_LOTE AND                                \n";
    $stSQL .= "        LB.COD_LOTE = L.COD_LOTE AND                                \n";
    $stSQL .= "        LA.COD_LOCALIZACAO  = LL.COD_LOCALIZACAO AND                \n";
    $stSQL .= "        ( LPA.validado IS NULL OR LPA.validado = true )             \n";

    return $stSQL;
}

}
