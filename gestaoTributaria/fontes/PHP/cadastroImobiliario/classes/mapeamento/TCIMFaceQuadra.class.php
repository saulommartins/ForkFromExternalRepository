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
     * Classe de mapeamento para a tabela IMOBILIARIO.FACE_QUADRA
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMFaceQuadra.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.07
*/

/*
$Log$
Revision 1.7  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.FACE_QUADRA
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMFaceQuadra extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMFaceQuadra()
{
    parent::Persistente();
    $this->setTabela('imobiliario.face_quadra');

    $this->setCampoCod('cod_face');
    $this->setComplementoChave('cod_localizacao');

    $this->AddCampo('cod_face','integer',true,'',true,false);
    $this->AddCampo('cod_localizacao','integer',true,'',true,true);

}

function recuperaFaceQuadra(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaFaceQuadra().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function recuperaFaceQuadraTrecho(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaFaceQuadraTrecho().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaFaceQuadra()
{
$stSql .= "          SELECT                                                                                              \n";
$stSql .= "              RET.cod_face,                                                                                 \n";
$stSql .= "              RET.cod_localizacao,                                                                          \n";
$stSql .= "              RET.valor_composto,                                                                           \n";
$stSql .= "              RET.nom_localizacao,                                                                          \n";
$stSql .= "              TL.nom_tipo||' '||NL.nom_logradouro AS tipo_nom_logradouro                                    \n";
$stSql .= "          FROM (                                                                                            \n";
$stSql .= "          SELECT                                                                                            \n";
$stSql .= "               FQ.cod_face,                                                                                 \n";
$stSql .= "               LO.cod_localizacao,                                                                           \n";
$stSql .= "               imobiliario.fn_consulta_localizacao(LN.cod_vigencia,LO.cod_localizacao) AS valor_composto, \n";
$stSql .= "               LO.nom_localizacao,                                                                          \n";
$stSql .= "               MAX(FT.cod_logradouro) AS cod_logradouro,                                                    \n";
$stSql .= "               MAX(NL.timestamp) AS ultimo_cadastrado                                                       \n";
$stSql .= "          FROM                                                                                              \n";
$stSql .= "               imobiliario.localizacao AS LO
  \n";
$stSql .= "          LEFT OUTER JOIN (   \n";
$stSql .= "              SELECT                                             \n";
$stSql .= "                  BAL.*                                          \n";
$stSql .= "              FROM                                               \n";
$stSql .= "                  imobiliario.baixa_localizacao AS BAL,          \n";
$stSql .= "                  (                                              \n";
$stSql .= "                  SELECT                                         \n";
$stSql .= "                      MAX (TIMESTAMP) AS TIMESTAMP,              \n";
$stSql .= "                      cod_localizacao                            \n";
$stSql .= "                  FROM                                           \n";
$stSql .= "                      imobiliario.baixa_localizacao              \n";
$stSql .= "                  GROUP BY                                       \n";
$stSql .= "                      cod_localizacao                            \n";
$stSql .= "                  ) AS BLL                                        \n";
$stSql .= "              WHERE                                              \n";
$stSql .= "                  BAL.cod_localizacao = BLL.cod_localizacao AND   \n";
$stSql .= "                  BAL.timestamp = BLL.timestamp                   \n";
$stSql .= "       ) BL                                                  \n";
$stSql .= "       ON \n";
$stSql .= "          LO.cod_localizacao = BL.cod_localizacao           \n";

$stSql .= "               INNER JOIN imobiliario.face_quadra AS FQ                           \n";
$stSql .= "               ON LO.cod_localizacao = FQ.cod_localizacao                                               \n";
$stSql .= "       LEFT JOIN (                                                                          \n";
$stSql .= "                    SELECT                                                                   \n";
$stSql .= "                        BFQ.*                                                                \n";
$stSql .= "                    FROM                                                                     \n";
$stSql .= "                        imobiliario.baixa_face_quadra AS BFQ,                                \n";
$stSql .= "                        (                                                                    \n";
$stSql .= "                        SELECT                                                               \n";
$stSql .= "                            MAX (TIMESTAMP) AS TIMESTAMP,                                    \n";
$stSql .= "                            cod_face,                                                        \n";
$stSql .= "                            cod_localizacao                                                  \n";
$stSql .= "                        FROM                                                                 \n";
$stSql .= "                            imobiliario.baixa_face_quadra                                    \n";
$stSql .= "                        GROUP BY                                                             \n";
$stSql .= "                            cod_face,                                                        \n";
$stSql .= "                            cod_localizacao                                                  \n";
$stSql .= "                        ) AS FQ                                                              \n";
$stSql .= "                    WHERE                                                                    \n";
$stSql .= "                        BFQ.cod_face = FQ.cod_face AND                                       \n";
$stSql .= "                        BFQ.cod_localizacao = FQ.cod_localizacao AND                         \n";
$stSql .= "                        BFQ.timestamp = FQ.timestamp                                         \n";
$stSql .= "                ) BF                                                                         \n";
$stSql .= "         ON FQ.cod_face = BF.cod_face AND                                                    \n";
$stSql .= "            FQ.cod_localizacao = BF.cod_localizacao                                          \n";

$stSql .= "        INNER JOIN imobiliario.localizacao_nivel AS LN                     \n";
$stSql .= "        ON LO.cod_localizacao  = LN.cod_localizacao                                               \n";
$stSql .= "       INNER JOIN imobiliario.face_quadra_trecho AS FT                        \n";
$stSql .= "       ON FQ.cod_face = FT.cod_face AND                                                             \n";
$stSql .= "          FQ.cod_localizacao = FT.cod_localizacao\n";

$stSql .= "       LEFT JOIN (                                                     \n";
$stSql .= "                  SELECT                                                       \n";
$stSql .= "                      BAT.*                                                    \n";
$stSql .= "                  FROM                                                         \n";
$stSql .= "                      imobiliario.baixa_trecho AS BAT,                         \n";
$stSql .= "                      (                                                        \n";
$stSql .= "                      SELECT                                                   \n";
$stSql .= "                          MAX (TIMESTAMP) AS TIMESTAMP,                        \n";
$stSql .= "                          cod_logradouro,                                      \n";
$stSql .= "                          cod_trecho                                           \n";
$stSql .= "                      FROM                                                     \n";
$stSql .= "                          imobiliario.baixa_trecho                             \n";
$stSql .= "                      GROUP BY                                                 \n";
$stSql .= "                          cod_logradouro,                                      \n";
$stSql .= "                          cod_trecho                                           \n";
$stSql .= "                      ) AS BTC                                                 \n";
$stSql .= "                  WHERE                                                        \n";
$stSql .= "                      BAT.cod_trecho = BTC.cod_trecho AND                      \n";
$stSql .= "                      BAT.cod_logradouro = BTC.cod_logradouro AND              \n";
$stSql .= "                      BAT.timestamp = BTC.timestamp                            \n";
$stSql .= "    ) BT                                                            \n";
$stSql .= "   ON \n";
$stSql .= "     FT.cod_trecho = BT.cod_trecho AND                            \n";
$stSql .= "     FT.cod_logradouro = BT.cod_logradouro \n";

$stSql .= "  INNER JOIN sw_nome_logradouro AS NL                           \n";
$stSql .= "  ON FT.cod_logradouro = NL.cod_logradouro                                                     \n";
$stSql .= "          WHERE \n";
$stSql .= "                ((BL.dt_inicio IS NULL) OR (BL.dt_inicio IS NOT NULL AND BL.dt_termino IS NOT NULL) AND\n";
$stSql .= "                  BL.cod_localizacao = LO.cod_localizacao) AND\n";
$stSql .= "                ((BF.dt_inicio IS NULL) OR (BF.dt_inicio IS NOT NULL AND BF.dt_termino IS NOT NULL) AND\n";
$stSql .= "                 BF.cod_face = FQ.cod_face) AND   \n";
$stSql .= "                ((BT.dt_inicio IS NULL) OR (BT.dt_inicio IS NOT NULL AND BT.dt_termino IS NOT NULL) AND\n";
$stSql .= "                 BT.cod_trecho = FT.cod_trecho)\n";
$stSql .= "         GROUP BY \n";
$stSql .= "              FQ.cod_face,                                                       \n";
$stSql .= "              LO.cod_localizacao,                                                \n";                   $stSql .= "              valor_composto,                                                    \n";
$stSql .= "              LO.nom_localizacao                                                 \n";
$stSql .= "          ) AS RET INNER JOIN sw_nome_logradouro AS NL    \n";
$stSql .= "               ON RET.cod_logradouro = NL.cod_logradouro AND  \n";                                       $stSql .= "               RET.ultimo_cadastrado = NL.timestamp           \n";
$stSql .= "               INNER JOIN sw_tipo_logradouro AS TL                    \n";
$stSql .= "               ON NL.cod_tipo = TL.cod_tipo          \n";
$stSql .= "          WHERE                                  \n";
$stSql .= "              RET.valor_composto is not null \n";

/*
    $stSql .= "  SELECT                                                                                              \n";
    $stSql .= "        RET.cod_face,                                                                                 \n";
    $stSql .= "        RET.cod_localizacao,                                                                          \n";
    $stSql .= "        RET.valor_composto,                                                                           \n";
    $stSql .= "        RET.nom_localizacao,                                                                          \n";
    $stSql .= "        TL.nom_tipo||' '||NL.nom_logradouro AS tipo_nom_logradouro                                    \n";
    $stSql .= "    FROM (                                                                                            \n";
    $stSql .= "    SELECT                                                                                            \n";
    $stSql .= "         FQ.cod_face,                                                                                 \n";
    $stSql .= "         LO.cod_localizacao,                                                                          \n";
    $stSql .= "         imobiliario.fn_consulta_localizacao(LN.cod_vigencia,LO.cod_localizacao) AS valor_composto, \n";
    $stSql .= "         LO.nom_localizacao,                                                                          \n";
    $stSql .= "         MAX(FT.cod_logradouro) AS cod_logradouro,                                                    \n";
    $stSql .= "         MAX(NL.timestamp) AS ultimo_cadastrado                                                       \n";
    $stSql .= "    FROM                                                                                              \n";
    $stSql .= "         imobiliario.localizacao AS LO LEFT OUTER JOIN imobiliario.baixa_localizacao AS BL                    \n";
    $stSql .= "         ON LO.cod_localizacao = BL.cod_localizacao                                                   \n";
    $stSql .= "                                       INNER JOIN imobiliario.face_quadra AS FQ                           \n";
    $stSql .= "         ON LO.cod_localizacao = FQ.cod_localizacao                                                   \n";
    $stSql .= "                                       LEFT  JOIN imobiliario.baixa_face_quadra AS BF                     \n";
    $stSql .= "         ON FQ.cod_face = BF.cod_face AND                                                             \n";
    $stSql .= "            FQ.cod_localizacao = BF.cod_localizacao                                                   \n";
    $stSql .= "                                       INNER JOIN imobiliario.localizacao_nivel AS LN                     \n";
    $stSql .= "         ON LO.cod_localizacao  = LN.cod_localizacao                                                  \n";
    $stSql .= "                                       INNER JOIN imobiliario.face_quadra_trecho AS FT                    \n";
    $stSql .= "         ON FQ.cod_face = FT.cod_face AND                                                             \n";
    $stSql .= "            FQ.cod_localizacao = FT.cod_localizacao                                                   \n";
    $stSql .= "                                       LEFT JOIN imobiliario.baixa_trecho AS BT                           \n";
    $stSql .= "         ON FT.cod_trecho = BT.cod_trecho AND                                                         \n";
    $stSql .= "            FT.cod_logradouro = BT.cod_logradouro                                                     \n";
    $stSql .= "                                       INNER JOIN sw_nome_logradouro AS NL                           \n";
    $stSql .= "         ON FT.cod_logradouro = NL.cod_logradouro                                                     \n";
    $stSql .= "    WHERE BL.cod_localizacao IS NULL AND                                                              \n";
    $stSql .= "          BF.cod_face IS NULL AND                                                                     \n";
    $stSql .= "          BT.cod_trecho IS NULL                                                                       \n";
    $stSql .= "    GROUP BY                                                                                          \n";
    $stSql .= "        FQ.cod_face,                                                                                  \n";
    $stSql .= "        LO.cod_localizacao,                                                                           \n";
    $stSql .= "        valor_composto,                                                                               \n";
    $stSql .= "        LO.nom_localizacao                                                                            \n";
    $stSql .= "    ) AS RET INNER JOIN sw_nome_logradouro AS NL                                                     \n";
    $stSql .= "         ON RET.cod_logradouro = NL.cod_logradouro AND                                                \n";
    $stSql .= "         RET.ultimo_cadastrado = NL.timestamp                                                         \n";
    $stSql .= "         INNER JOIN sw_tipo_logradouro AS TL                                                         \n";
    $stSql .= "         ON NL.cod_tipo = TL.cod_tipo                                                                 \n";
    $stSql .= "    WHERE                                                                                             \n";
    $stSql .= "        RET.valor_composto is not null                                                                \n";
*/

    return $stSql;
}

function montaRecuperaFaceQuadraTrecho()
{
    $stSql .= "     SELECT                                                            \n";
    $stSql .= "        FQ.cod_face,                                                   \n";
    $stSql .= "        FQ.cod_localizacao,                                            \n";
    $stSql .= "        FT.cod_logradouro,                                             \n";
    $stSql .= "        TR.*,                                                          \n";
    $stSql .= "        TL.nom_tipo||' '||MNL.nom_logradouro AS tipo_nom_logradouro    \n";
    $stSql .= "    FROM                                                               \n";
    $stSql .= "        (                                                              \n";
    $stSql .= "            SELECT                                                     \n";
    $stSql .= "                FQ.*                                                   \n";
    $stSql .= "            FROM                                                       \n";
    $stSql .= "                imobiliario.face_quadra AS FQ                          \n";

    $stSql .= "           LEFT JOIN (                                                 \n";
    $stSql .= "                SELECT                                                 \n";
    $stSql .= "                    BAL.*                                              \n";
    $stSql .= "                FROM                                                   \n";
    $stSql .= "                    imobiliario.baixa_face_quadra AS BAL,              \n";
    $stSql .= "                    (                                                  \n";
    $stSql .= "                    SELECT                                             \n";
    $stSql .= "                        MAX (TIMESTAMP) AS TIMESTAMP,                  \n";
    $stSql .= "                        cod_face                                       \n";
    $stSql .= "                    FROM                                               \n";
    $stSql .= "                        imobiliario.baixa_face_quadra                  \n";
    $stSql .= "                    GROUP BY                                           \n";
    $stSql .= "                        cod_face                                       \n";
    $stSql .= "                    ) AS BT                                            \n";
    $stSql .= "                WHERE                                                  \n";
    $stSql .= "                    BAL.cod_face = BT.cod_face AND                     \n";
    $stSql .= "                    BAL.timestamp = BT.timestamp                       \n";
    $stSql .= "            ) BF                                                       \n";
    $stSql .= "            ON                                                         \n";
    $stSql .= "                FQ.cod_face = BF.cod_face                              \n";
    $stSql .= "            WHERE                                                      \n";
    $stSql .= "                ((BF.dt_inicio IS NULL) OR (BF.dt_inicio IS NOT NULL AND BF.dt_termino IS NOT NULL) AND FQ.cod_face = BF.cod_face)                                                    \n";

    $stSql .= "        ) AS FQ,                                                       \n";
    $stSql .= "        (                                                              \n";
    $stSql .= "            SELECT                                                     \n";
    $stSql .= "                NL.*                                                   \n";
    $stSql .= "            FROM                                                       \n";
    $stSql .= "                sw_nome_logradouro AS NL,                             \n";
    $stSql .= "                (                                                      \n";
    $stSql .= "            SELECT                                                     \n";
    $stSql .= "                MAX( TIMESTAMP ) AS TIMESTAMP,                         \n";
    $stSql .= "                COD_LOGRADOURO                                         \n";
//    $stSql .= "                COD_LOGRADOURO,                                        \n";
//    $stSql .= "                COD_TIPO                                               \n";
    $stSql .= "            FROM                                                       \n";
    $stSql .= "                sw_nome_logradouro                                    \n";
    $stSql .= "            GROUP BY                                                   \n";
    $stSql .= "                COD_LOGRADOURO  ) AS MNL                               \n";
//    $stSql .= "                COD_LOGRADOURO,                                        \n";
//    $stSql .= "                COD_TIPO ) AS MNL                                      \n";
    $stSql .= "            WHERE                                                      \n";
    $stSql .= "                NL.COD_LOGRADOURO = MNL.COD_LOGRADOURO AND             \n";
    $stSql .= "                NL.TIMESTAMP      = MNL.TIMESTAMP                      \n";
//    $stSql .= "                NL.TIMESTAMP      = MNL.TIMESTAMP AND                  \n";
//    $stSql .= "                NL.COD_TIPO       = MNL.COD_TIPO                       \n";
    $stSql .= "        ) AS MNL,                                                      \n";
    $stSql .= "        imobiliario.face_quadra_trecho as FT,                              \n";
    $stSql .= "        sw_tipo_logradouro as TL,                                     \n";
    $stSql .= "        imobiliario.trecho as TR                                           \n";
    $stSql .= "    WHERE                                                              \n";
    $stSql .= "        FQ.cod_face = FT.cod_face AND                                  \n";
    $stSql .= "        FQ.cod_localizacao = FT.cod_localizacao AND                    \n";
    $stSql .= "        FT.cod_logradouro = MNL.cod_logradouro AND                     \n";
    $stSql .= "        MNL.cod_tipo = TL.cod_tipo AND                                 \n";
    $stSql .= "        FT.cod_trecho = TR.cod_trecho AND                              \n";
    $stSql .= "        FT.cod_logradouro = TR.cod_logradouro AND                      \n";
    $stSql .= "        TR.cod_logradouro = MNL.cod_logradouro                         \n";

    return $stSql;
}

function recuperaFaceQuadraBaixa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaFaceQuadraBaixa().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaFaceQuadraBaixa()
{
    $stSql .= "  SELECT                                                                                     \n";
    $stSql .= "        RET.cod_face,                                                                        \n";
    $stSql .= "        RET.cod_localizacao,                                                                 \n";
    $stSql .= "        RET.valor_composto,                                                                  \n";
    $stSql .= "        RET.nom_localizacao,                                                                 \n";
    $stSql .= "        RET.dt_inicio,                                                                       \n";
    $stSql .= "        RET.timestamp,                                                                       \n";
    $stSql .= "        RET.justificativa,                                                                   \n";
    $stSql .= "        TL.nom_tipo||' '||NL.nom_logradouro AS tipo_nom_logradouro                           \n";
    $stSql .= "    FROM (                                                                                   \n";
    $stSql .= "          SELECT                                                                             \n";
    $stSql .= "             to_char(BF.dt_inicio,'dd/mm/yyyy') as dt_inicio,                                \n";
    $stSql .= "             BF.timestamp as timestamp,                                                      \n";
    $stSql .= "             BF.justificativa,                                                               \n";
    $stSql .= "             FQ.cod_face,                                                                    \n";
    $stSql .= "             LO.cod_localizacao,                                                             \n";
    $stSql .= "             imobiliario.fn_consulta_localizacao(LN.cod_vigencia,LO.cod_localizacao) AS       valor_composto,                                                                                         \n";
    $stSql .= "             LO.nom_localizacao,                                                             \n";
    $stSql .= "             MAX(FT.cod_logradouro) AS cod_logradouro,                                       \n";
    $stSql .= "             MAX(NL.timestamp) AS ultimo_cadastrado                                          \n";
    $stSql .= "         FROM                                                                                \n";
    $stSql .= "             imobiliario.localizacao AS LO                                                   \n";

    $stSql .= "            LEFT OUTER JOIN (                                                                \n";
    $stSql .= "                SELECT                                                                       \n";
    $stSql .= "                    BFQ.*                                                                    \n";
    $stSql .= "                FROM                                                                         \n";
    $stSql .= "                    imobiliario.baixa_localizacao AS BFQ,                                    \n";
    $stSql .= "                    (                                                                        \n";
    $stSql .= "                    SELECT                                                                   \n";
    $stSql .= "                        MAX (TIMESTAMP) AS TIMESTAMP,                                        \n";
    $stSql .= "                        cod_localizacao                                                      \n";
    $stSql .= "                    FROM                                                                     \n";
    $stSql .= "                        imobiliario.baixa_localizacao                                        \n";
    $stSql .= "                    GROUP BY                                                                 \n";
    $stSql .= "                        cod_localizacao                                                      \n";
    $stSql .= "                    ) AS FQ                                                                  \n";
    $stSql .= "                WHERE                                                                        \n";
    $stSql .= "                    BFQ.cod_localizacao = FQ.cod_localizacao AND                             \n";
    $stSql .= "                    BFQ.timestamp = FQ.timestamp                                             \n";
    $stSql .= "            ) BL                                                                             \n";
    $stSql .= "            ON                                                                               \n";
    $stSql .= "                LO.cod_localizacao = BL.cod_localizacao                                      \n";

    $stSql .= "         INNER JOIN imobiliario.face_quadra AS FQ                                            \n";
    $stSql .= "       ON LO.cod_localizacao = FQ.cod_localizacao                                            \n";
    $stSql .= "       INNER JOIN (                                                                          \n";
    $stSql .= "                    SELECT                                                                   \n";
    $stSql .= "                        BFQ.*                                                                \n";
    $stSql .= "                    FROM                                                                     \n";
    $stSql .= "                        imobiliario.baixa_face_quadra AS BFQ,                                \n";
    $stSql .= "                        (                                                                    \n";
    $stSql .= "                        SELECT                                                               \n";
    $stSql .= "                            MAX (TIMESTAMP) AS TIMESTAMP,                                    \n";
    $stSql .= "                            cod_face,                                                        \n";
    $stSql .= "                            cod_localizacao                                                  \n";
    $stSql .= "                        FROM                                                                 \n";
    $stSql .= "                            imobiliario.baixa_face_quadra                                    \n";
    $stSql .= "                        GROUP BY                                                             \n";
    $stSql .= "                            cod_face,                                                        \n";
    $stSql .= "                            cod_localizacao                                                  \n";
    $stSql .= "                        ) AS FQ                                                              \n";
    $stSql .= "                    WHERE                                                                    \n";
    $stSql .= "                        BFQ.cod_face = FQ.cod_face AND                                       \n";
    $stSql .= "                        BFQ.cod_localizacao = FQ.cod_localizacao AND                         \n";
    $stSql .= "                        BFQ.timestamp = FQ.timestamp                                         \n";
    $stSql .= "                ) BF                                                                         \n";
    $stSql .= "     ON                                                                                      \n";
    $stSql .= "        BF.cod_face = FQ.cod_face AND                                                        \n";
    $stSql .= "        BF.cod_localizacao = FQ.cod_localizacao AND                                          \n";
    $stSql .= "        BF.dt_termino IS NULL                                                                \n";
    $stSql .= "     INNER JOIN imobiliario.localizacao_nivel AS LN                                          \n";
    $stSql .= "       ON LO.cod_localizacao  = LN.cod_localizacao                                           \n";
    $stSql .= "     INNER JOIN imobiliario.face_quadra_trecho AS FT                                         \n";
    $stSql .= "       ON FQ.cod_face = FT.cod_face AND                                                      \n";
    $stSql .= "          FQ.cod_localizacao = FT.cod_localizacao                                            \n";

    $stSql .= "           LEFT JOIN (                                                                       \n";
    $stSql .= "            SELECT                                                                           \n";
    $stSql .= "                BFQ.*                                                                        \n";
    $stSql .= "            FROM                                                                             \n";
    $stSql .= "                imobiliario.baixa_trecho AS BFQ,                                             \n";
    $stSql .= "                (                                                                            \n";
    $stSql .= "                SELECT                                                                       \n";
    $stSql .= "                    MAX (TIMESTAMP) AS TIMESTAMP,                                            \n";
    $stSql .= "                    cod_trecho,                                                              \n";
    $stSql .= "                    cod_logradouro                                                           \n";
    $stSql .= "                FROM                                                                         \n";
    $stSql .= "                    imobiliario.baixa_trecho                                                 \n";
    $stSql .= "                GROUP BY                                                                     \n";
    $stSql .= "                    cod_trecho,                                                              \n";
    $stSql .= "                    cod_logradouro                                                           \n";
    $stSql .= "                ) AS FQ                                                                      \n";
    $stSql .= "            WHERE                                                                            \n";
    $stSql .= "                BFQ.cod_trecho = FQ.cod_trecho AND                                           \n";
    $stSql .= "                BFQ.cod_logradouro = FQ.cod_logradouro AND                                   \n";
    $stSql .= "                BFQ.timestamp = FQ.timestamp                                                 \n";
    $stSql .= "        ) BT                                                                                 \n";
    $stSql .= "        ON                                                                                   \n";
    $stSql .= "            FT.cod_trecho = BT.cod_trecho AND                                                \n";
    $stSql .= "            FT.cod_logradouro = BT.cod_logradouro                                            \n";

    $stSql .= "                                     INNER JOIN sw_nome_logradouro AS NL                     \n";
    $stSql .= "       ON FT.cod_logradouro = NL.cod_logradouro                                              \n";

    $stSql .= "  WHERE                                                                                      \n";
    $stSql .= "    ((BL.dt_inicio IS NULL) OR (BL.dt_inicio IS NOT NULL AND BL.dt_termino IS NOT NULL) AND BL.cod_localizacao = LO.cod_localizacao) AND\n";
    $stSql .= "    ((BT.dt_inicio IS NULL) OR (BT.dt_inicio IS NOT NULL AND BT.dt_termino IS NOT NULL) AND BT.cod_trecho=FT.cod_trecho AND BT.cod_logradouro = FT.cod_logradouro)\n";

    $stSql .= "  GROUP BY                                                                                   \n";
    $stSql .= "      BF.dt_inicio,                                                                          \n";
    $stSql .= "      BF.timestamp,                                                                          \n";
    $stSql .= "      BF.justificativa,                                                                      \n";
    $stSql .= "      FQ.cod_face,                                                                           \n";
    $stSql .= "      LO.cod_localizacao,                                                                    \n";
    $stSql .= "      valor_composto,                                                                        \n";
    $stSql .= "      LO.nom_localizacao                                                                     \n";
    $stSql .= "  ) AS RET INNER JOIN sw_nome_logradouro AS NL                                               \n";
    $stSql .= "       ON RET.cod_logradouro = NL.cod_logradouro AND                                         \n";
    $stSql .= "       RET.ultimo_cadastrado = NL.timestamp                                                  \n";
    $stSql .= "       INNER JOIN sw_tipo_logradouro AS TL                                                   \n";
    $stSql .= "       ON NL.cod_tipo = TL.cod_tipo                                                          \n";
    $stSql .= "  WHERE                                                                                      \n";
    $stSql .= "      RET.valor_composto is not null                                                         \n";

    return $stSql;
}

}
