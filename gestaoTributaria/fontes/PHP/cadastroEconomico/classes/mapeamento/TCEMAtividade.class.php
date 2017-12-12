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
  * Classe de mapeamento da tabela ECONOMICO.ATIVIDADE
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMAtividade.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.07
*               uc-05.02.15
                uc-03.04.03
*/

/*
$Log$
Revision 1.10  2007/02/26 18:01:14  bruce
Bug #8450#

Revision 1.9  2007/02/14 17:40:20  tonismar
bug #8152

Revision 1.8  2006/12/18 18:02:10  dibueno
Alteração do Caso de Uso

Revision 1.7  2006/12/12 18:20:56  dibueno
Alterações para o relatório de atividades

Revision 1.6  2006/11/17 10:20:06  cercato
bug #7442#

Revision 1.5  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.ATIVIDADE
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMAtividade extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMAtividade()
{
    parent::Persistente();
    $this->setTabela('economico.atividade');

    $this->setCampoCod('cod_atividade');
    $this->setComplementoChave('cod_vigencia, cod_nivel');

    $this->AddCampo('cod_atividade'  ,'integer'   ,true  ,''    ,true  ,false );
    $this->AddCampo('nom_atividade'  ,'varchar'   ,true  ,'180' ,false ,false );
    $this->AddCampo('timestamp'      ,'timestamp' ,false ,''    ,false ,false );
    $this->AddCampo('cod_vigencia'   ,'integer'   ,true  ,''    ,false ,true  );
    $this->AddCampo('cod_nivel'      ,'integer'   ,true  ,''    ,false ,true  );
    $this->AddCampo('cod_estrutural' ,'varchar'   ,true  ,''    ,false ,false );

}

function recuperaAtividadeAtiva(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAtividadeAtiva().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAtividadeAtiva()
{
    $stSQL .=" SELECT                                                    \n";
    $stSQL .="     LN.*,                                                 \n";
    $stSQL .="     LO.nom_atividade,                                     \n";
    $stSQL .="     NI.mascara,                                           \n";
    $stSQL .="     NI.nom_nivel,                                         \n";
    $stSQL .="     LO.aliquota,                                          \n";
    $stSQL .="     LO.cod_estrutural_cnae,                               \n";
    $stSQL .="     LO.cod_cnae                                           \n";
    $stSQL .=" FROM                                                      \n";
    $stSQL .="     (                                                     \n";
    $stSQL .="      SELECT                                               \n";
    $stSQL .="          LN.*,                                            \n";
    $stSQL .="          LN2.valor                                        \n";
    $stSQL .="      FROM (                                               \n";
    $stSQL .="            SELECT                                         \n";
    $stSQL .="                MAX(LN.cod_nivel) AS cod_nivel,            \n";
    $stSQL .="                LN.cod_vigencia ,LN.cod_atividade,         \n";
    $stSQL .="                economico.fn_consulta_atividade            \n";
    $stSQL .="(LN.cod_vigencia,LN.cod_atividade) AS valor_composto,      \n";
    $stSQL .="                 publico.fn_mascarareduzida                \n";
    $stSQL .="( economico.fn_consulta_atividade                          \n";
    $stSQL .="(LN.cod_vigencia,LN.cod_atividade) ) AS valor_reduzido     \n";
    $stSQL .="            FROM                                           \n";
    $stSQL .="                economico.nivel_atividade_valor AS LN      \n";
    $stSQL .="            WHERE                                          \n";
    $stSQL .="                LN.valor::numeric <> 0::numeric            \n";
    $stSQL .="            GROUP BY                                       \n";
    $stSQL .="                LN.cod_vigencia,                           \n";
    $stSQL .="                LN.cod_atividade) AS LN,                   \n";
    $stSQL .="       economico.nivel_atividade_valor AS LN2              \n";
    $stSQL .="      WHERE                                                \n";
    $stSQL .="          LN.cod_nivel       = LN2.cod_nivel AND           \n";
    $stSQL .="          LN.cod_atividade   = LN2.cod_atividade AND       \n";
    $stSQL .="          LN.cod_vigencia    = LN2.cod_vigencia            \n";
    $stSQL .="     ) AS LN,                                              \n";
    $stSQL .="     economico.nivel_atividade AS NI,                      \n";
    $stSQL .="     (                                                     \n";
    $stSQL .="    SELECT                                                 \n";
    $stSQL .="        LOC.*,                                             \n";
    $stSQL .="        A.valor as aliquota,                               \n";
    $stSQL .="        ACF.cod_cnae,                                      \n";
    $stSQL .="        CF.cod_estrutural as cod_estrutural_cnae,          \n";
    $stSQL .="        CF.cod_cnae as codigo_cnae                         \n";
    $stSQL .="    FROM                                                   \n";
    $stSQL .="        economico.atividade AS LOC                         \n";
    $stSQL .="    LEFT JOIN                                              \n";
    $stSQL .="        economico.aliquota_atividade AS A                  \n";
    $stSQL .="    ON                                                     \n";
    $stSQL .="        LOC.cod_atividade = A.cod_atividade                \n";
    $stSQL .="    LEFT JOIN                                              \n";
    $stSQL .="        economico.atividade_cnae_fiscal AS ACF             \n";
    $stSQL .="    ON                                                     \n";
    $stSQL .="        LOC.cod_atividade = ACF.cod_atividade              \n";
    $stSQL .="    LEFT JOIN                                              \n";
    $stSQL .="        economico.cnae_fiscal as CF                        \n";
    $stSQL .="    ON                                                     \n";
    $stSQL .="        CF.cod_cnae = ACF.cod_cnae                         \n";
    $stSQL .="     ) AS LO                                               \n";
    $stSQL .=" WHERE                                                     \n";
    $stSQL .="     LN.cod_nivel       = NI.cod_nivel       AND           \n";
    $stSQL .="     LN.cod_vigencia    = NI.cod_vigencia    AND           \n";
    $stSQL .="     LN.cod_atividade   = LO.cod_atividade                 \n";

    return $stSQL;
}

function recuperaAtividadeRelatorio(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAtividadeRelatorio().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAtividadeRelatorio()
{
    $stSQL .= " SELECT                                                                              \n";
    $stSQL .= "     A.cod_atividade,                                                                \n";
    $stSQL .= "     A.cod_estrutural,                                                               \n";
    $stSQL .= "     A.nom_atividade,                                                                \n";
    $stSQL .= "     A.cod_nivel,                                                                    \n";
    $stSQL .= "     (   case when char_length (substring ( SA.cod_servico||' '||S.nom_servico from 1 for 80) ) > 79  then                                                                                        \n";
    $stSQL .= "             (substring ( SA.cod_servico||' '||S.nom_servico from 1 for 80 )||'..')  \n";
    $stSQL .= "         else                                                                        \n";
    $stSQL .= "             SA.cod_servico||' '||S.nom_servico                                      \n";
    $stSQL .= "         end                                                                         \n";
    $stSQL .= "     ) AS servico ,                                                                  \n";
    $stSQL .= "     SA.cod_servico||' '||S.nom_servico AS servico_completo,                         \n";
    $stSQL .= "     TO_CHAR( eva.dt_inicio, 'DD/MM/YYYY') as dt_inicio,                               \n";
    $stSQL .= "     AL.valor AS aliquota                                                            \n";
    $stSQL .= " FROM                                                                                \n";
    $stSQL .= "     economico.atividade A                                                           \n";
    $stSQL .= "    JOIN economico.nivel_atividade_valor AS enav ON                                  \n";
    $stSQL .= "         enav.cod_atividade = A.cod_atividade AND                                    \n";
    $stSQL .= "         enav.cod_vigencia  = A.cod_vigencia  AND                                    \n";
    $stSQL .= "         enav.cod_nivel     = A.cod_nivel                                            \n";
    $stSQL .= "    JOIN economico.nivel_atividade AS ena ON                                         \n";
    $stSQL .= "         ena.cod_nivel      = enav.cod_nivel AND                                     \n";
    $stSQL .= "         ena.cod_vigencia   = enav.cod_vigencia                                      \n";
    $stSQL .= "    JOIN economico.vigencia_atividade AS eva ON                                      \n";
    $stSQL .= "         eva.cod_vigencia   = ena.cod_vigencia                                       \n";
    $stSQL .= "     LEFT JOIN economico.servico_atividade SA ON                                     \n";
    $stSQL .= "         A.cod_atividade = SA.cod_atividade                                          \n";
    $stSQL .= "     LEFT JOIN economico.servico S ON                                                \n";
    $stSQL .= "         SA.cod_servico  = S.cod_servico                                             \n";
    $stSQL .= "     LEFT JOIN economico.aliquota_atividade AL ON                                    \n";
    $stSQL .= "         AL.cod_atividade = A.cod_atividade                                          \n";
    $stSQL .= "     LEFT JOIN (                                                                     \n";
    $stSQL .= "         SELECT   cod_atividade, max(timestamp) as timestamp                         \n";
    $stSQL .= "         FROM     economico.aliquota_atividade                                       \n";
    $stSQL .= "         GROUP BY cod_atividade                                                      \n";
    $stSQL .= " ) AS MAL ON                                                                         \n";
    $stSQL .= "     MAL.cod_atividade = AL.cod_atividade AND                                        \n";
    $stSQL .= "     MAL.timestamp     = AL.timestamp                                                \n";
    $stSQL .= " WHERE  A.cod_vigencia = eva.cod_vigencia                                            \n";

    return $stSQL;

}

function recuperaAtividadeCombo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAtividadeCombo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAtividadeCombo()
{
    $stSql .= "    select distinct * from (                                             \n";
    $stSql .= "    select                                                               \n";
    $stSql .= "        a.cod_atividade,                                                 \n";
    $stSql .= "        a.nom_atividade,                                                 \n";
    $stSql .= "        a.cod_nivel,                                                     \n";
    $stSql .= "        a.cod_estrutural,                                                \n";
    $stSql .= "        publico.fn_mascarareduzida(a.cod_estrutural) as valor_reduzido,  \n";
    $stSql .= "        na.cod_vigencia,                                                 \n";
    $stSql .= "        nav.valor                                                        \n";
    $stSql .= "    from                                                                 \n";
    $stSql .= "        economico.atividade   as a,                                      \n";
    $stSql .= "        economico.nivel_atividade_valor   as nav,                        \n";
    $stSql .= "        economico.nivel_atividade   as na                                \n";
    $stSql .= "    where                                                                \n";
    $stSql .= "        a.cod_vigencia      = nav.cod_vigencia  AND                      \n";
    $stSql .= "        a.cod_nivel         = nav.cod_nivel     AND                      \n";
    $stSql .= "        nav.cod_vigencia    = na.cod_vigencia   AND                      \n";
    $stSql .= "        nav.cod_nivel       = na.cod_nivel      AND                      \n";
    $stSql .= "        a.cod_atividade     = nav.cod_atividade                          \n";
    $stSql .= "    ) as ListarAtividades                                                \n";

    return $stSql;
}

function AtualizaAtividade($boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaAtualizaAtividade();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaDML( $stSql, $boTransacao );

    return $obErro;
}

function montaAtualizaAtividade()
{
    $stSql  = "  UPDATE  economico.atividade \n";
    $stSql .= "  SET     cod_estrutural = cod_estrutural \n";
    $stSql .= "  WHERE   cod_estrutural like '".$this->getDado( "valor" )."%'\n";

    return $stSql;
}

function recuperaAtividadePorEstrutural(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAtividadePorEstrutural().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAtividadePorEstrutural()
{
    $stSql  = " SELECT                                                                 \n";
    $stSql .= "      cod_atividade                                                     \n";
    $stSql .= "     ,nom_atividade                                                     \n";
    $stSql .= " FROM                                                                   \n";
    $stSql .= "     economico.atividade                                                \n";
    $stSql .= " WHERE                                                                  \n";
    $stSql .= "     cod_atividade =                                                    \n";
    $stSql .= " (                                                                      \n";
    $stSql .= "     SELECT                                                             \n";
    $stSql .= "         min(cod_atividade)                                             \n";
    $stSql .= "     FROM                                                               \n";
    $stSql .= "         economico.atividade                                            \n";
    $stSql .= "     WHERE                                                              \n";
    $stSql .= "         cod_estrutural like '".$this->getDado('cod_estrutural')."'     \n";
    $stSql .= " )                                                                      \n";

    return $stSql;
}

function recuperaMaxCodEstrutural(&$rsRecordSet, $inVigencia = -1, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaMaxCodEstrutural($inVigencia);
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMaxCodEstrutural($inVigencia)
{
    if ($inVigencia == -1) {
        $stSql  = "
            SELECT
                max(cod_estrutural) AS cod_estrutural
            FROM
                economico.atividade
            WHERE
                cod_vigencia = (
                    SELECT
                        cod_vigencia
                    FROM
                        economico.vigencia_atividade
                    WHERE
                        dt_inicio <= now()::date
                    ORDER BY
                        timestamp DESC
                    LIMIT 1
                )
        \n";
    } else {
        $stSql = "
            SELECT
                max(cod_estrutural) AS cod_estrutural
            FROM
                economico.atividade
            WHERE
                cod_vigencia = ".$inVigencia;
    }

    return $stSql;
}

}
