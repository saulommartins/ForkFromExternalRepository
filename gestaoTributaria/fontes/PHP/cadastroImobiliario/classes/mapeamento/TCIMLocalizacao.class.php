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
     * Classe de mapeamento para a tabela IMOBILIARIO.LOCALIZACAO
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMLocalizacao.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.03
*/

/*
$Log$
Revision 1.10  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.LOCALIZACAO
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMLocalizacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMLocalizacao()
{
    parent::Persistente();
    $this->setTabela('imobiliario.localizacao');

    $this->setCampoCod('cod_localizacao');
    $this->setComplementoChave('');

    $this->AddCampo('cod_localizacao','integer',true,'',true,false);
    $this->AddCampo('nom_localizacao','varchar',true,'80',false,false);
    $this->AddCampo('codigo_composto','varchar',true,'100',false,false);
}

function recuperaLocalizacaoAtiva(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLocalizacaoAtiva().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLocalizacaoAtiva()
{
    $stSQL .=" SELECT                                                    \n";
    $stSQL .="     LN.*,                                                 \n";
    $stSQL .="     LO.nom_localizacao,                                   \n";
    $stSQL .="     NI.mascara,                                           \n";
    $stSQL .="     NI.nom_nivel                                          \n";
    $stSQL .=" FROM                                                      \n";
    $stSQL .="     (                                                     \n";
    $stSQL .="      SELECT                                               \n";
    $stSQL .="          LN.*,                                            \n";
    $stSQL .="          LN2.valor                                        \n";
    $stSQL .="      FROM (                                               \n";
    $stSQL .="            SELECT                                         \n";
    $stSQL .="                MAX(LN.cod_nivel) AS cod_nivel,            \n";
    $stSQL .="                LN.cod_vigencia ,LN.cod_localizacao,       \n";
    $stSQL .="                imobiliario.fn_consulta_localizacao        \n";
    $stSQL .="(LN.cod_vigencia,LN.cod_localizacao) AS valor_composto,    \n";
    $stSQL .="                publico.fn_mascarareduzida                 \n";
    $stSQL .="( imobiliario.fn_consulta_localizacao                      \n";
    $stSQL .="(LN.cod_vigencia,LN.cod_localizacao) ) AS valor_reduzido   \n";
    $stSQL .="            FROM                                           \n";
    $stSQL .="                imobiliario.localizacao_nivel AS LN        \n";
    $stSQL .="            WHERE                                          \n";
    $stSQL .="                LN.valor <> 0                              \n";
    $stSQL .="            GROUP BY                                       \n";
    $stSQL .="                LN.cod_vigencia,                           \n";
    $stSQL .="                LN.cod_localizacao) AS LN,                 \n";
    $stSQL .="       imobiliario.localizacao_nivel AS LN2                \n";
    $stSQL .="      WHERE                                                \n";
    $stSQL .="          LN.cod_nivel       = LN2.cod_nivel AND           \n";
    $stSQL .="          LN.cod_localizacao = LN2.cod_localizacao AND     \n";
    $stSQL .="          LN.cod_vigencia    = LN2.cod_vigencia            \n";
    $stSQL .="     ) AS LN,                                              \n";
    $stSQL .="     imobiliario.nivel       AS NI,                        \n";
    $stSQL .="     (                                                     \n";
    $stSQL .="      SELECT                                               \n";
    $stSQL .="          LOC.*                                            \n";
    $stSQL .="      FROM                                                 \n";
    $stSQL .="          imobiliario.localizacao AS LOC                   \n";

    $stSQL .="      LEFT JOIN (                                          \n";
    $stSQL .="        SELECT                                             \n";
    $stSQL .="            BAL.*                                          \n";
    $stSQL .="        FROM                                               \n";
    $stSQL .="            imobiliario.baixa_localizacao AS BAL,          \n";
    $stSQL .="            (                                              \n";
    $stSQL .="            SELECT                                         \n";
    $stSQL .="                MAX (TIMESTAMP) AS TIMESTAMP,              \n";
    $stSQL .="                cod_localizacao                            \n";
    $stSQL .="            FROM                                           \n";
    $stSQL .="                imobiliario.baixa_localizacao              \n";
    $stSQL .="            GROUP BY                                       \n";
    $stSQL .="                cod_localizacao                            \n";
    $stSQL .="            ) AS BL                                        \n";
    $stSQL .="        WHERE                                              \n";
    $stSQL .="            BAL.cod_localizacao = BL.cod_localizacao AND   \n";
    $stSQL .="            BAL.timestamp = BL.timestamp                   \n";
    $stSQL .="     ) BLOC                                                \n";
    $stSQL .="     ON                                                    \n";
    $stSQL .="        LOC.COD_LOCALIZACAO = BLOC.COD_LOCALIZACAO         \n";

    $stSQL .="      WHERE                                                \n";
    $stSQL .="          ((BLOC.dt_inicio IS NULL) OR (BLOC.dt_inicio IS NOT NULL AND BLOC.dt_termino IS NOT NULL) AND BLOC.cod_localizacao = LOC.cod_localizacao)) AS LO               \n";

    $stSQL .=" WHERE                                                     \n";
    $stSQL .="     LN.cod_nivel       = NI.cod_nivel       AND           \n";
    $stSQL .="     LN.cod_vigencia    = NI.cod_vigencia    AND           \n";
    $stSQL .="     LN.cod_localizacao = LO.cod_localizacao               \n";

    return $stSQL;
}

function validaCodigoLocalizacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaValidaCodigoLocalizacao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaValidaCodigoLocalizacao()
{
    $stSQL .= "SELECT                                                                       \n";
    $stSQL .= "     L.cod_localizacao                                                       \n";
    $stSQL .= " FROM                                                                        \n";
    $stSQL .= "     imobiliario.localizacao L                                               \n";
    $stSQL .= " LEFT JOIN (                                                                 \n";
    $stSQL .= "    SELECT                                                                   \n";
    $stSQL .= "        BAL.*                                                                \n";
    $stSQL .= "    FROM                                                                     \n";
    $stSQL .= "        imobiliario.baixa_localizacao AS BAL,                                \n";
    $stSQL .= "        (                                                                    \n";
    $stSQL .= "        SELECT                                                               \n";
    $stSQL .= "            MAX (TIMESTAMP) AS TIMESTAMP,                                    \n";
    $stSQL .= "            cod_localizacao                                                  \n";
    $stSQL .= "        FROM                                                                 \n";
    $stSQL .= "            imobiliario.baixa_localizacao                                    \n";
    $stSQL .= "        GROUP BY                                                             \n";
    $stSQL .= "            cod_localizacao                                                  \n";
    $stSQL .= "        ) AS BT                                                              \n";
    $stSQL .= "    WHERE                                                                    \n";
    $stSQL .= "        BAL.cod_localizacao = BT.cod_localizacao AND                         \n";
    $stSQL .= "        BAL.timestamp = BT.timestamp                                         \n";
    $stSQL .= " ) BL                                                                        \n";
    $stSQL .= " ON                                                                          \n";
    $stSQL .= "   L.cod_localizacao = BL.cod_localizacao,                                   \n";
    $stSQL .= "   imobiliario.localizacao_nivel LN                                          \n";
    $stSQL .= " WHERE                                                                       \n";
    $stSQL .= "     ((BL.dt_inicio IS NULL) OR (BL.dt_inicio IS NOT NULL AND BL.dt_termino IS NOT NULL) AND BL.cod_localizacao=L.cod_localizacao) AND LN.cod_localizacao = L.cod_localizacao        \n";

    return $stSQL;
}

function AtualizaLocalizacao($boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaAtualizaLocalizacao();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaDML( $stSql, $boTransacao );

    return $obErro;
}

function montaAtualizaLocalizacao()
{
    $stSql  = "  UPDATE  imobiliario.localizacao \n";
    $stSql .= "  SET     codigo_composto = codigo_composto \n";
    $stSql .= "  WHERE   codigo_composto like '".$this->getDado( "valor" )."%'\n";

    return $stSql;
}

function recuperaLocalizacaoPrimeiroNivel(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLocalizacaoPrimeiroNivel($stFiltro).$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLocalizacaoPrimeiroNivel($stFiltro="1,1")
{
        $stSql = "select *                                             \n";
        $stSql .= "from imobiliario.fn_lista_localizacoes(".$stFiltro.")\n";
        $stSql .= "as tabela   (   cod_nivel       integer,             \n";
        $stSql .= "                cod_vigencia    integer,             \n";
        $stSql .= "                valor           integer,             \n";
        $stSql .= "                cod_localizacao integer,             \n";
        $stSql .= "                nom_localizacao varchar,             \n";
        $stSql .= "                valor_composto  varchar,             \n";
        $stSql .= "                valor_reduzido  varchar              \n";
        $stSql .= "            )                                        \n";

    return $stSql;
}

function recuperaNomLocalizacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaNomLocalizacao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaNomLocalizacao()
{
    $stSql  = "SELECT l.codigo_composto, l.cod_localizacao, l.nom_localizacao, ln.cod_nivel                 \n";
    $stSql .= "FROM (                                                                                       \n";
    $stSql .= "    SELECT                                                                                   \n";
    $stSql .= "            *                                                                                \n";
    $stSql .= "    FROM                                                                                     \n";
    $stSql .= "        imobiliario.localizacao loc                                                          \n";
    $stSql .= "    WHERE NOT (                                                                              \n";
    $stSql .= "        EXISTS (                                                                             \n";
    $stSql .= "            SELECT                                                                           \n";
    $stSql .= "                BAL.cod_localizacao,                                                         \n";
    $stSql .= "                BAL.timestamp,                                                               \n";
    $stSql .= "                BAL.justificativa                                                            \n";
    $stSql .= "            FROM                                                                             \n";
    $stSql .= "                imobiliario.baixa_localizacao AS BAL,                                        \n";
    $stSql .= "                (                                                                            \n";
    $stSql .= "                    SELECT                                                                   \n";
    $stSql .= "                        MAX (TIMESTAMP) AS TIMESTAMP,                                        \n";
    $stSql .= "                        cod_localizacao                                                      \n";
    $stSql .= "                    FROM                                                                     \n";
    $stSql .= "                        imobiliario.baixa_localizacao                                        \n";
    $stSql .= "                    GROUP BY                                                                 \n";
    $stSql .= "                        cod_localizacao                                                      \n";
    $stSql .= "                ) AS BL                                                                      \n";
    $stSql .= "            WHERE                                                                            \n";
    $stSql .= "                BAL.cod_localizacao = BL.cod_localizacao AND                                 \n";
    $stSql .= "                BAL.timestamp = BL.timestamp AND                                             \n";
    $stSql .= "                BL.cod_localizacao = loc.cod_localizacao AND                                 \n";
    $stSql .= "                BAL.dt_inicio IS NOT NULL AND                                                \n";
    $stSql .= "                BAL.dt_termino IS NULL                                                       \n";
    $stSql .= "       )                                                                                     \n";
    $stSql .= "   )                                                                                         \n";
    $stSql .= ") as l                                                                                       \n";
    $stSql .= "INNER JOIN imobiliario.localizacao_nivel ln on ln.cod_localizacao = l.cod_localizacao        \n";

    return $stSql;
}

function verificaBaixaLocalizacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaVerificaBaixaLocalizacao().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaVerificaBaixaLocalizacao()
{
    $stSql   = "  SELECT                                                                 \n";
    $stSql  .= "      niv.cod_nivel,                                                     \n";
    $stSql  .= "      niv.cod_vigencia,                                                  \n";
    $stSql  .= "      niv.mascara,                                                       \n";
    $stSql  .= "      niv.nom_nivel,                                                     \n";
    $stSql  .= "      l.cod_localizacao,                                                 \n";
    $stSql  .= "      l.nom_localizacao,                                                 \n";
    $stSql  .= "      l.codigo_composto AS valor_composto,                               \n";
    $stSql  .= "      BL.justificativa,                                                  \n";
    $stSql  .= "      BL.timestamp,                                                      \n";
    $stSql  .= "      to_char(BL.dt_inicio,'dd/mm/yyyy') as dt_inicio,                   \n";
    $stSql  .= "      publico.fn_mascarareduzida(l.codigo_composto) AS valor_reduzido,   \n";
    $stSql  .= "      locn.valor                                                         \n";
    $stSql  .= "  FROM                                                                   \n";
    $stSql  .= "      imobiliario.localizacao AS l                                       \n";
    $stSql  .= "  INNER JOIN (                                                           \n";
    $stSql  .= "      SELECT                                                             \n";
    $stSql  .= "          BAT.*                                                          \n";
    $stSql  .= "      FROM                                                               \n";
    $stSql  .= "          imobiliario.baixa_localizacao AS BAT,                          \n";
    $stSql  .= "          (                                                              \n";
    $stSql  .= "          SELECT                                                         \n";
    $stSql  .= "              MAX (TIMESTAMP) AS TIMESTAMP,                              \n";
    $stSql  .= "              cod_localizacao                                            \n";
    $stSql  .= "          FROM                                                           \n";
    $stSql  .= "              imobiliario.baixa_localizacao                              \n";
    $stSql  .= "          GROUP BY                                                       \n";
    $stSql  .= "              cod_localizacao                                            \n";
    $stSql  .= "          ) AS BT                                                        \n";
    $stSql  .= "      WHERE                                                              \n";
    $stSql  .= "          BAT.cod_localizacao = BT.cod_localizacao AND                   \n";
    $stSql  .= "          BAT.timestamp = BT.timestamp                                   \n";
    $stSql  .= "  ) BL                                                                   \n";
    $stSql  .= "  ON                                                                     \n";
    $stSql  .= "    l.cod_localizacao = BL.cod_localizacao AND                           \n";
    $stSql  .= "    BL.dt_termino IS NULL,                                               \n";
    $stSql  .= "    imobiliario.localizacao_nivel locn,                                  \n";
    $stSql  .= "      ( SELECT                                                           \n";
    $stSql  .= "          max(localizacao_nivel.cod_nivel) AS cod_nivel,                 \n";
    $stSql  .= "          localizacao_nivel.cod_vigencia,                                \n";
    $stSql  .= "  localizacao_nivel.cod_localizacao                                      \n";
    $stSql  .= "        FROM                                                             \n";
    $stSql  .= "          imobiliario.localizacao_nivel                                  \n";
    $stSql  .= "        WHERE                                                            \n";
    $stSql  .= "          localizacao_nivel.valor::text <> 0::text                       \n";
    $stSql  .= "        GROUP BY                                                         \n";
    $stSql  .= "          localizacao_nivel.cod_vigencia,                                \n";
    $stSql  .= "          localizacao_nivel.cod_localizacao                              \n";
    $stSql  .= "      ) mniv,                                                            \n";
    $stSql  .= "  imobiliario.nivel niv                                                  \n";
    $stSql  .= "  WHERE                                                                  \n";
    $stSql  .= "      l.cod_localizacao = mniv.cod_localizacao AND                       \n";
    $stSql  .= "      l.cod_localizacao = locn.cod_localizacao AND                       \n";
    $stSql  .= "      mniv.cod_vigencia = locn.cod_vigencia AND                          \n";
    $stSql  .= "      mniv.cod_nivel = locn.cod_nivel AND                                \n";
    $stSql  .= "      mniv.cod_vigencia = niv.cod_vigencia AND                           \n";
    $stSql  .= "      mniv.cod_nivel = niv.cod_nivel                                     \n";

    return $stSql;
}

}
