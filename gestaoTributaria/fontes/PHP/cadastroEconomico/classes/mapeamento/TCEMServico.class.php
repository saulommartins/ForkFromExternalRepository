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
  * Classe de mapeamento da tabela ECONOMICO.SERVICO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMServico.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.03
*/

/*
$Log$
Revision 1.14  2007/06/07 18:59:12  cercato
Bug #9363#

Revision 1.13  2006/11/23 16:05:48  cercato
bug #7573#

Revision 1.12  2006/11/03 11:08:12  cercato
bug #7314#

Revision 1.11  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.SERVICO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMServico extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMServico()
{
    parent::Persistente();
    $this->setTabela('economico.servico');

    $this->setCampoCod('cod_servico');
    $this->setComplementoChave('');

    $this->AddCampo('cod_servico','integer',true,'',true,true);
    $this->AddCampo('nom_servico','varchar',true,'180',false,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);
    $this->AddCampo('cod_estrutural','varchar',true,'',false,false);

}

function recuperaServicoAtivo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "", $inCodigoAtividade = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaServicoAtivo( $inCodigoAtividade ).$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaServicoAliquota(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaServicoAliquota().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaServicoAtivo($inCodigoAtividade = "")
{
    $stSQL  ="
                SELECT DISTINCT
                    LN.*,
                    LO.nom_servico,
                    NI.mascara,
                    NI.nom_nivel,
                    LO.valor as valor_aliquota,
                    to_char(LO.dt_vigencia,'dd/mm/yyyy') as dt_vigencia
                FROM
                    (
                    SELECT
                        LN.*,
                        LN2.valor
                    FROM (
                            SELECT
                                MAX(LN.cod_nivel) AS cod_nivel,
                                LN.cod_vigencia ,LN.cod_servico,
                                economico.fn_consulta_servico
                (LN.cod_vigencia,LN.cod_servico) AS valor_composto,
                                publico.fn_mascarareduzida
                ( economico.fn_consulta_servico
                (LN.cod_vigencia,LN.cod_servico) ) AS valor_reduzido
                            FROM
                                economico.nivel_servico_valor AS LN
                            WHERE
                                LN.valor <> '0'
                            GROUP BY
                                LN.cod_vigencia,
                                LN.cod_servico) AS LN,
                    economico.nivel_servico_valor AS LN2
                    WHERE
                        LN.cod_nivel       = LN2.cod_nivel AND
                        LN.cod_servico     = LN2.cod_servico AND
                        LN.cod_vigencia    = LN2.cod_vigencia
                    ) AS LN ";

        if ($inCodigoAtividade) { //parte para filtrar servicos por filtro, deve funcionar apenas para o nivel 1
            $stSQL .="
                ,(
                    SELECT
                        es.cod_estrutural,
                        es.cod_servico,
                        esa.cod_atividade
                    FROM
                        economico.servico AS es
                    INNER JOIN
                        economico.servico_atividade AS esa
                    ON
                        esa.cod_servico = es.cod_servico
                )AS esso
                ";
        }

            $stSQL .=" ,economico.nivel_servico AS NI,
                    (
                    SELECT
                        LOC.*,
                        AL.valor,
                        AL.dt_vigencia
                    FROM
                        economico.servico AS LOC
                    LEFT JOIN
                        ( SELECT
                                ALS.*
                            FROM
                                economico.aliquota_servico ALS,
                                (SELECT
                                    MAX (TIMESTAMP) AS TIMESTAMP,
                                    cod_servico
                                FROM
                                    economico.aliquota_servico
                                GROUP BY
                                    cod_servico
                                ) AS ALSS
                            WHERE
                                ALS.cod_servico = ALSS.cod_servico
                                AND ALS.TIMESTAMP = ALSS.TIMESTAMP
                            ) AS AL
                    ON
                        LOC.cod_servico = AL.cod_servico
                    ) AS LO

                WHERE
                    LN.cod_nivel       = NI.cod_nivel       AND
                    LN.cod_vigencia    = NI.cod_vigencia    AND
                    LN.cod_servico     = LO.cod_servico                  \n";
                if ($inCodigoAtividade) { //parte para filtrar servicos por filtro, deve funcionar apenas para o nivel 1
                    $stSQL .=" AND esso.cod_estrutural like LN.valor_reduzido ||'%'
                    AND esso.cod_atividade = ".$inCodigoAtividade;
                }

    return $stSQL;
}

function montaRecuperaServicoAliquota()
{
    $stSQL .=" SELECT                                                    \n";
    $stSQL .="     LN.*,                                                 \n";
    $stSQL .="     LO.nom_servico,                                       \n";
    $stSQL .="     NI.mascara,                                           \n";
    $stSQL .="     NI.nom_nivel,                                         \n";
    $stSQL .="     LO.valor as valor_aliquota,                           \n";
    $stSQL .="     to_char(VI.dt_inicio,'dd/mm/yyyy') as dt_vigencia,    \n";
    $stSQL .="     to_char(LO.dt_vigencia,'dd/mm/yyyy') as dt_vigencia_aliquota     \n";
    $stSQL .=" FROM                                                      \n";
    $stSQL .="     (                                                     \n";
    $stSQL .="      SELECT                                               \n";
    $stSQL .="          LN.*,                                            \n";
    $stSQL .="          LN2.valor                                        \n";
    $stSQL .="      FROM (                                               \n";
    $stSQL .="            SELECT                                         \n";
    $stSQL .="                MAX(LN.cod_nivel) AS cod_nivel,            \n";
    $stSQL .="                LN.cod_vigencia ,LN.cod_servico,           \n";
    //$stSQL .="                economico.fn_consulta_atividade            \n";
    $stSQL .="                economico.fn_consulta_servico              \n";
    $stSQL .="(LN.cod_vigencia,LN.cod_servico) AS valor_composto,        \n";
    /*$stSQL .="                publico.fn_consulta_atividade              \n";
    $stSQL .="( economico.fn_consulta_atividade                          \n";*/
    $stSQL .="                publico.fn_mascarareduzida (               \n";
    $stSQL .="                economico.fn_consulta_servico              \n";
    $stSQL .="(LN.cod_vigencia,LN.cod_servico) )  AS valor_reduzido      \n";
    $stSQL .="            FROM                                           \n";
    $stSQL .="                economico.nivel_servico_valor AS LN        \n";
    $stSQL .="            WHERE                                          \n";
    $stSQL .="                LN.valor <> '0'                            \n";
    $stSQL .="            GROUP BY                                       \n";
    $stSQL .="                LN.cod_vigencia,                           \n";
    $stSQL .="                LN.cod_servico) AS LN,                     \n";
    $stSQL .="      economico.nivel_servico_valor AS LN2                 \n";
    $stSQL .="      WHERE                                                \n";
    $stSQL .="          LN.cod_nivel       = LN2.cod_nivel AND           \n";
    $stSQL .="          LN.cod_servico     = LN2.cod_servico AND         \n";
    $stSQL .="          LN.cod_vigencia    = LN2.cod_vigencia            \n";
    $stSQL .="     ) AS LN,                                              \n";
    $stSQL .="     (                                                     \n";
    $stSQL .="      SELECT                                               \n";
    $stSQL .="          MAX(COD_NIVEL) AS COD_NIVEL,                     \n";
    $stSQL .="          COD_VIGENCIA                                     \n";
    $stSQL .="      FROM                                                 \n";
    $stSQL .="          economico.nivel_servico                          \n";
    $stSQL .="      WHERE                                                \n";
    $stSQL .="          COD_VIGENCIA IN                                  \n";
    $stSQL .="          ( SELECT COD_VIGENCIA FROM economico.vigencia_servico ) \n";
    $stSQL .="      GROUP BY COD_VIGENCIA                                \n";
    $stSQL .="     ) AS NO,                                              \n";
    $stSQL .="     economico.nivel_servico AS NI,                        \n";
    $stSQL .="     (                                                     \n";
    $stSQL .="      SELECT                                               \n";
    $stSQL .="          LOC.*,                                           \n";
    $stSQL .="          AL.valor,                                        \n";
    $stSQL .="          AL.dt_vigencia                                   \n";
    $stSQL .="      FROM                                                 \n";
    $stSQL .="          economico.servico AS LOC                         \n";
    $stSQL .="      LEFT JOIN                                            \n";
    $stSQL .="         ( SELECT                                          \n";
    $stSQL .="                ALS.*                                      \n";
    $stSQL .="             FROM                                          \n";
    $stSQL .="                economico.aliquota_servico ALS,            \n";
    $stSQL .="                (SELECT                                    \n";
    $stSQL .="                    MAX (TIMESTAMP) AS TIMESTAMP,          \n";
    $stSQL .="                    cod_servico                            \n";
    $stSQL .="                 FROM                                      \n";
    $stSQL .="                    economico.aliquota_servico             \n";
    $stSQL .="                 GROUP BY                                  \n";
    $stSQL .="                    cod_servico                            \n";
    $stSQL .="                 ) AS ALSS                                 \n";
    $stSQL .="             WHERE                                         \n";
    $stSQL .="                ALS.cod_servico = ALSS.cod_servico         \n";
    $stSQL .="                AND ALS.TIMESTAMP = ALSS.TIMESTAMP         \n";
    $stSQL .="             ) AS AL                                       \n";
    $stSQL .="      ON                                                   \n";
    $stSQL .="          LOC.cod_servico = AL.cod_servico                 \n";
    $stSQL .="     ) AS LO,                                              \n";
    $stSQL .="     (                                                     \n";
    $stSQL .="        SELECT                                             \n";
    $stSQL .="            V.*                                            \n";
    $stSQL .="        FROM                                               \n";
    $stSQL .="            economico.vigencia_servico as V                \n";
    $stSQL .="     ) AS VI                                               \n";
    $stSQL .=" WHERE                                                     \n";
    $stSQL .="     LN.cod_nivel       = NI.cod_nivel       AND           \n";
    $stSQL .="     NI.cod_nivel       = NO.cod_nivel       AND           \n";
    $stSQL .="     LN.cod_vigencia    = NI.cod_vigencia    AND           \n";
    $stSQL .="     NI.cod_vigencia    = NO.cod_vigencia    AND           \n";
    $stSQL .="     NO.cod_vigencia    = VI.cod_vigencia    AND           \n";
    $stSQL .="     LN.cod_servico     = LO.cod_servico                   \n";

    return $stSQL;
}

function recuperaServicoRelatorio(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaServicoRelatorio().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaServicoRelatorio()
{
    $stSQL .=" SELECT                                                    \n";
    $stSQL .="      SAL.cod_servico,                                     \n";
    $stSQL .="      SAL.nom_servico,                                     \n";
    $stSQL .="      SAL.valor AS aliquota,                               \n";
    $stSQL .="      SN.cod_nivel,                                        \n";
    $stSQL .="      SN.valor_composto AS masc_servico,                   \n";
    $stSQL .="      TO_CHAR( VS.dt_inicio , 'DD-MM-YYYY' ) AS vigencia,  \n";
    $stSQL .="      SA.cod_servico,                                      \n";
    $stSQL .="      A.cod_estrutural||' '|| A.nom_atividade AS atividade \n";
    $stSQL .="  FROM                                                     \n";
    $stSQL .="      (                                                    \n";
    $stSQL .="       SELECT                                              \n";
    $stSQL .="           SN.*,                                           \n";
    $stSQL .="           SN2.valor                                       \n";
    $stSQL .="       FROM                                                \n";
    $stSQL .="           (                                               \n";
    $stSQL .="             SELECT                                        \n";
    $stSQL .="                 MAX(SN.cod_nivel) AS cod_nivel,           \n";
    $stSQL .="                 SN.cod_vigencia ,                         \n";
    $stSQL .="                 SN.cod_servico,                           \n";
    $stSQL .="                 economico.fn_consulta_servico(SN.cod_vigencia,SN.cod_servico) AS valor_composto,                              \n";
    $stSQL .="                 publico.fn_mascarareduzida( economico.fn_consulta_servico(SN.cod_vigencia,SN.cod_servico) ) AS valor_reduzido \n";
    $stSQL .="             FROM                                     \n";
    $stSQL .="                 economico.nivel_servico_valor AS SN  \n";
    $stSQL .="             WHERE                                    \n";
    $stSQL .="                 SN.valor <> 0                        \n";
    $stSQL .="             GROUP BY                                 \n";
    $stSQL .="                 SN.cod_vigencia,                     \n";
    $stSQL .="                 SN.cod_servico                       \n";
    $stSQL .="             ) AS SN,                                 \n";
    $stSQL .="           economico.nivel_servico_valor AS SN2       \n";
    $stSQL .="       WHERE                                          \n";
    $stSQL .="           SN.cod_nivel       = SN2.cod_nivel AND     \n";
    $stSQL .="           SN.cod_servico     = SN2.cod_servico AND   \n";
    $stSQL .="           SN.cod_vigencia    = SN2.cod_vigencia      \n";
    $stSQL .="      ) AS SN,                                        \n";
    $stSQL .="      economico.nivel_servico AS NI,                  \n";
    $stSQL .="      economico.vigencia_servico AS VS,               \n";
    $stSQL .="      (                                               \n";
    $stSQL .="       SELECT                                         \n";
    $stSQL .="           S.*,                                       \n";
    $stSQL .="           AL.valor                                   \n";
    $stSQL .="       FROM                                           \n";
    $stSQL .="           economico.servico AS S                     \n";
    $stSQL .="       LEFT JOIN economico.aliquota_servico AS AL ON  \n";
    $stSQL .="           S.cod_servico = AL.cod_servico ) AS SAL    \n";
    $stSQL .="       LEFT JOIN economico.servico_atividade AS SA ON \n";
    $stSQL .="           SA.cod_servico = SAL.cod_servico           \n";
    $stSQL .="       LEFT JOIN economico.atividade AS A ON          \n";
    $stSQL .="           A.cod_atividade = SA.cod_atividade         \n";
    $stSQL .=" WHERE                                                \n";
    $stSQL .="    SN.cod_nivel    = NI.cod_nivel       AND          \n";
    $stSQL .="    SN.cod_vigencia = NI.cod_vigencia    AND          \n";
    $stSQL .="    SN.cod_servico  = SAL.cod_servico    AND          \n";
    $stSQL .="    VS.cod_vigencia = NI.cod_vigencia                 \n";
    $stSQL .=" ORDER BY                                             \n";
    $stSQL .="    SN.valor_composto                                 \n";

    return $stSQL;
}

function AtualizaServico($boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaAtualizaServico();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaDML( $stSql, $boTransacao );

    return $obErro;
}

function montaAtualizaServico()
{
    $stSql  = "  UPDATE  economico.servico \n";
    $stSql .= "  SET     cod_estrutural = cod_estrutural \n";
    $stSql .= "  WHERE   cod_estrutural like '".$this->getDado( "valor" )."%'\n";

    return $stSql;
}

function verificaServico(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaVerificaServico().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaVerificaServico()
{
    $stSql  = " SELECT  \n";
    $stSql .= "     es.* \n";
    $stSql .= " FROM  \n";
    $stSql .= "     economico.servico AS es \n";
    $stSql .= " INNER JOIN  \n";
    $stSql .= "     economico.servico_atividade AS esa \n";
    $stSql .= " ON  \n";
    $stSql .= "     esa.cod_servico = es.cod_servico \n";

    return $stSql;
}

function listaServicosRelatorio(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaListaServicosRelatorio().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaListaServicosRelatorio()
{
    $stSQL = "
        SELECT servico.cod_estrutural
             , servico.nom_servico
             , aliquota_servico.valor AS aliquota
             , to_char(vigencia_servico.dt_inicio, 'dd/mm/yyyy' ) AS vigencia
          FROM economico.servico
     LEFT JOIN ( SELECT tmp1.*
                   FROM economico.aliquota_servico AS tmp1
             INNER JOIN ( SELECT max(timestamp) AS timestamp
                               , cod_servico
                            FROM economico.aliquota_servico
                        GROUP BY cod_servico
                        )AS tmp2
                     ON tmp2.cod_servico = tmp1.cod_servico
                    AND tmp2.timestamp = tmp1.timestamp
               )AS aliquota_servico
            ON aliquota_servico.cod_servico = servico.cod_servico
    INNER JOIN ( SELECT cod_servico
                      , nivel_servico_valor.cod_vigencia
                   FROM economico.nivel_servico_valor
             INNER JOIN economico.nivel_servico
                     ON nivel_servico.cod_nivel = nivel_servico_valor.cod_nivel
                    AND nivel_servico.cod_vigencia = nivel_servico_valor.cod_vigencia
               GROUP BY cod_servico
                      , nivel_servico_valor.cod_vigencia
               )AS nivel_servico
            ON nivel_servico.cod_servico = servico.cod_servico
    INNER JOIN economico.vigencia_servico
            ON vigencia_servico.cod_vigencia = nivel_servico.cod_vigencia
    ";

    return $stSQL;
}

}
