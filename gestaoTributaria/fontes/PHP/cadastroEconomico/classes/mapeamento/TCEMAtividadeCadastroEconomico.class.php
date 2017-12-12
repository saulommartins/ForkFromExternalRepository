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
  * Classe de mapeamento da tabela ECONOMICO.ATIVIDADE_CADASTRO_ECONOMICO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMAtividadeCadastroEconomico.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.07
*/

/*
$Log$
Revision 1.15  2007/03/02 14:51:28  dibueno
Bug #7676#

Revision 1.14  2007/02/22 12:22:46  cassiano
Criado o método recupera modalidade lancamento.

Revision 1.13  2006/11/20 13:13:52  dibueno
Bug #7519#

Revision 1.12  2006/11/20 10:10:32  cercato
bug #7438#

Revision 1.11  2006/11/10 17:16:09  cercato
alteração do uc_05.02.13

Revision 1.10  2006/10/23 16:20:23  dibueno
Alterações no SQL para exibição de atividade principal

Revision 1.9  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.ATIVIDADE_CADASTRO_ECONOMICO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMAtividadeCadastroEconomico extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMAtividadeCadastroEconomico()
{
    parent::Persistente();
    $this->setTabela('economico.atividade_cadastro_economico');

    $this->setCampoCod('ocorrencia_atividade');
    $this->setComplementoChave('inscricao_economica,cod_atividade,ocorrencia_atividade');

    $this->AddCampo('inscricao_economica','integer',true,'',true,true);
    $this->AddCampo('cod_atividade','integer',true,'',true,true);
    $this->AddCampo('ocorrencia_atividade','integer',true,'',true,false);
    $this->AddCampo('principal','boolean',true,'',false,false);
    $this->AddCampo('dt_inicio','date',false,'',false,false);
    $this->AddCampo('dt_termino','date',false,'',false,false);
}

function recuperaAtividadeInscricao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "", $boMaxOcorrencia = true)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAtividadeInscricao($boMaxOcorrencia).$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAtividadeInscricao($boMaxOcorrencia)
{
    $stSql  = "   SELECT                                                                \n";
    $stSql .= "       ATV.COD_ATIVIDADE,                                                \n";
    $stSql .= "       ATV.NOM_ATIVIDADE,                                                \n";
    $stSql .= "       ATV.COD_ESTRUTURAL,                                               \n";
    $stSql .= "       ATE.PRINCIPAL,                                                    \n";
    $stSql .= "       TO_CHAR ( ATE.DT_INICIO,'dd/mm/yyyy' )  AS DT_INICIO,             \n";
    $stSql .= "       TO_CHAR ( ATE.DT_TERMINO,'dd/mm/yyyy' ) AS DT_TERMINO,            \n";
    $stSql .= "       ATE.OCORRENCIA_ATIVIDADE                                          \n";
    $stSql .= "   FROM                                                                  \n";
    $stSql .= "        (                                                                \n";
    $stSql .= "        SELECT                                                           \n";
    $stSql .= "            ate.inscricao_economica, ate.cod_atividade,                  \n";
    $stSql .= "            ate.principal, ate.dt_inicio,                                \n";
    $stSql .= "            ate.dt_termino, ate.ocorrencia_atividade                     \n";
    $stSql .= "        FROM                                                             \n";
    $stSql .= "            economico.atividade_cadastro_economico AS ATE                \n";
    if ($boMaxOcorrencia) {
        $stSql .= "         INNER JOIN (                                                \n";
        $stSql .= "             SELECT                                                  \n";
        $stSql .= "                 ate.inscricao_economica,                            \n";
        $stSql .= "                 MAX(ocorrencia_atividade) as ocorrencia_atividade   \n";
        $stSql .= "             FROM                                                    \n";
        $stSql .= "                 ECONOMICO.ATIVIDADE_CADASTRO_ECONOMICO AS ATE       \n";
        $stSql .= "             GROUP BY inscricao_economica                            \n";
        $stSql .= "         ) as ATE2                                                   \n";
        $stSql .= "         ON ATE2.inscricao_economica = ATE.inscricao_economica       \n";
        $stSql .= "         and ATE2.ocorrencia_atividade = ATE.ocorrencia_atividade    \n";
    }
    $stSql .="          ) as ATE                                                        \n";
    $stSql .= "         INNER JOIN economico.atividade AS ATV                           \n";
    $stSql .= "         ON ATV.COD_ATIVIDADE = ATE.COD_ATIVIDADE                        \n";
    $stSql .= "   WHERE                                                                 \n";
    $stSql .= "       ATV.COD_ATIVIDADE = ATE.COD_ATIVIDADE                             \n";

    return $stSql;
}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT                                                                  \n";
    $stSql .= "    COALESCE( MLIE.valor, CEML.valor ) AS nuValor,                      \n";
    $stSql .= "    MLIE.stTipoValor,                                                   \n";
    $stSql .= "    MLIE.inCodTipo,                                                     \n";
    $stSql .= "    MLIE.stDescricaoTipo,                                               \n";
    $stSql .= "    ACE.COD_ATIVIDADE,                                                  \n";
    $stSql .= "    ACE.INSCRICAO_ECONOMICA,                                            \n";
    $stSql .= "    AT.NOM_ATIVIDADE,                                                   \n";
    $stSql .= "    LN.VALOR_COMPOSTO,                                                  \n";
    $stSql .= "    ACE.OCORRENCIA_ATIVIDADE,                                           \n";
    $stSql .= "    ACE.PRINCIPAL,                                                      \n";

    $stSql .= "    (																	\n";
    $stSql .= "			CASE WHEN ACE.PRINCIPAL = true THEN								\n";
    $stSql .= "				'Sim'														\n";
    $stSql .= "			ELSE															\n";
    $stSql .= "				'Não'														\n";
    $stSql .= "			END																\n";
    $stSql .= "	   ) as atividade_principal,                                            \n";

    $stSql .= "    TO_CHAR ( ACE.DT_INICIO,'dd/mm/yyyy' )  AS DT_INICIO,               \n";
    $stSql .= "    TO_CHAR ( ACE.DT_TERMINO,'dd/mm/yyyy' ) AS DT_TERMINO,              \n";
    $stSql .= "    CASE                                                                \n";
    $stSql .= "        WHEN                                                            \n";
    $stSql .= "            CEEF.INSCRICAO_ECONOMICA IS NOT NULL                        \n";
    $stSql .= "        THEN                                                            \n";
    $stSql .= "            CEEF.NUMCGM                                                 \n";
    $stSql .= "        WHEN                                                            \n";
    $stSql .= "            CEED.INSCRICAO_ECONOMICA IS NOT NULL                        \n";
    $stSql .= "        THEN                                                            \n";
    $stSql .= "            CEED.NUMCGM                                                 \n";
    $stSql .= "        WHEN                                                            \n";
    $stSql .= "            CEA.INSCRICAO_ECONOMICA IS NOT NULL                         \n";
    $stSql .= "        THEN                                                            \n";
    $stSql .= "            CEA.NUMCGM                                                  \n";
    $stSql .= "    END AS NUMCGM,                                                      \n";
    $stSql .= "    CGM.NOM_CGM,                                                        \n";
    $stSql .= "    CASE                                                                \n";
    $stSql .= "        WHEN                                                            \n";
    $stSql .= "            MLIE.COD_MODALIDADE IS NOT NULL                             \n";
    $stSql .= "        THEN                                                            \n";
    $stSql .= "            MLIE.COD_MODALIDADE                                          \n";
    $stSql .= "        ELSE                                                            \n";
    $stSql .= "            EML.COD_MODALIDADE                                          \n";
    $stSql .= "    END,                                                                 \n";
//    $stSql .= "    CEML.COD_MODALIDADE,                                                \n";
    $stSql .= "    CASE                                                                \n";
    $stSql .= "        WHEN                                                            \n";
    $stSql .= "            MLIE.NOM_MODALIDADE IS NOT NULL                             \n";
    $stSql .= "        THEN                                                            \n";
    $stSql .= "            MLIE.NOM_MODALIDADE                                         \n";
    $stSql .= "        ELSE                                                            \n";
    $stSql .= "            EML.NOM_MODALIDADE                                          \n";
    $stSql .= "    END                                                                 \n";
    $stSql .= "FROM                                                                    \n";
    $stSql .= "  economico.atividade_cadastro_economico AS ACE                           \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "  economico.atividade AS AT                                               \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "    AT.COD_ATIVIDADE = ACE.COD_ATIVIDADE                                \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "    (                                                                   \n";
    $stSql .= "     SELECT                                                             \n";
    $stSql .= "         LN.*,                                                          \n";
    $stSql .= "         LN2.valor                                                      \n";
    $stSql .= "     FROM (                                                             \n";
    $stSql .= "           SELECT                                                       \n";
    $stSql .= "               MAX(LN.cod_nivel) AS cod_nivel,                          \n";
    $stSql .= "               LN.cod_vigencia ,LN.cod_atividade,                       \n";
    $stSql .= "               economico.fn_consulta_atividade                      \n";
    $stSql .= "                 (LN.cod_vigencia,LN.cod_atividade) AS valor_composto,  \n";
    $stSql .= "               publico.fn_mascarareduzida                          \n";
    $stSql .= "                 (economico.fn_consulta_atividade                   \n";
    $stSql .= "                 (LN.cod_vigencia,LN.cod_atividade) ) AS valor_reduzido \n";
    $stSql .= "           FROM                                                         \n";
    $stSql .= "               economico.nivel_atividade_valor AS LN                      \n";
    $stSql .= "           WHERE                                                        \n";
    $stSql .= "               LN.valor <> 0::varchar                                            \n";
    $stSql .= "           GROUP BY                                                     \n";
    $stSql .= "               LN.cod_vigencia,                                         \n";
    $stSql .= "               LN.cod_atividade) AS LN,                                 \n";
    $stSql .= "      economico.nivel_atividade_valor AS LN2                              \n";
    $stSql .= "     WHERE                                                              \n";
    $stSql .= "         LN.cod_nivel       = LN2.cod_nivel AND                         \n";
    $stSql .= "         LN.cod_atividade   = LN2.cod_atividade AND                     \n";
    $stSql .= "         LN.cod_vigencia    = LN2.cod_vigencia                          \n";
    $stSql .= "    ) AS LN                                                             \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "    LN.COD_ATIVIDADE = ACE.COD_ATIVIDADE                                \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "  economico.cadastro_economico_empresa_fato AS CEEF                       \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "    CEEF.INSCRICAO_ECONOMICA = ACE.INSCRICAO_ECONOMICA                  \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "  economico.cadastro_economico_empresa_direito AS CEED                    \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "    CEED.INSCRICAO_ECONOMICA = ACE.INSCRICAO_ECONOMICA                  \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "  economico.cadastro_economico_autonomo AS CEA                            \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "    CEA.INSCRICAO_ECONOMICA = ACE.INSCRICAO_ECONOMICA                   \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "  sw_cgm AS CGM                                                     \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "    CEA.NUMCGM = CGM.NUMCGM OR                                          \n";
    $stSql .= "    CEEF.NUMCGM = CGM.NUMCGM OR                                         \n";
    $stSql .= "    CEED.NUMCGM = CGM.NUMCGM                                            \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "  economico.atividade_modalidade_lancamento AS CEML              \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "    CEML.COD_ATIVIDADE       = ACE.COD_ATIVIDADE       AND              \n";
    $stSql .= "    CEML.DT_BAIXA IS NULL                              AND              \n";
    $stSql .= "    CEML.MOTIVO_BAIXA IS NULL                                           \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "  economico.modalidade_lancamento AS EML                                  \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "    EML.COD_MODALIDADE = CEML.COD_MODALIDADE                            \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "  ( SELECT                                                              \n";
    $stSql .= "        OCEML.VALOR,                                                    \n";
    $stSql .= "        OCEML.COD_ATIVIDADE,                                            \n";
    $stSql .= "        OCEML.INSCRICAO_ECONOMICA,                                      \n";
    $stSql .= "        OCEML.COD_MODALIDADE,                                           \n";
    $stSql .= "        OML.NOM_MODALIDADE,                                              \n";
    $stSql .= "        COALESCE ( ecemm.COD_MOEDA, ecemi.COD_INDICADOR ) AS inCodTipo, \n";
    $stSql .= "        CASE WHEN ecemm.COD_MOEDA IS NOT NULL THEN                       \n";
    $stSql .= "            'moeda'                                                      \n";
    $stSql .= "        ELSE                                                             \n";
    $stSql .= "            CASE WHEN ecemi.COD_INDICADOR IS NOT NULL THEN               \n";
    $stSql .= "                'indicador'                                              \n";
    $stSql .= "            ELSE                                                         \n";
    $stSql .= "                'percentual'                                             \n";
    $stSql .= "            END                                                          \n";
    $stSql .= "        END AS stTipoValor,                                              \n";

    $stSql .= "        CASE WHEN ecemm.COD_MOEDA IS NOT NULL THEN                       \n";
    $stSql .= "             (                                                           \n";
    $stSql .= "                SELECT                                                   \n";
    $stSql .= "                    mm.descricao_singular                                \n";
    $stSql .= "                FROM                                                     \n";
    $stSql .= "                    monetario.moeda AS mm                                \n";
    $stSql .= "                WHERE                                                    \n";
    $stSql .= "                    mm.cod_moeda = ecemm.COD_MOEDA                       \n";
    $stSql .= "             )                                                           \n";
    $stSql .= "        ELSE                                                             \n";
    $stSql .= "             CASE WHEN ecemi.cod_indicador IS NOT NULL THEN              \n";
    $stSql .= "                 (                                                           \n";
    $stSql .= "                      SELECT                                                   \n";
    $stSql .= "                         mie.descricao                                        \n";
    $stSql .= "                      FROM                                                     \n";
    $stSql .= "                         monetario.indicador_economico AS mie                 \n";
    $stSql .= "                      WHERE                                                    \n";
    $stSql .= "                         mie.cod_indicador = ecemi.cod_indicador              \n";
    $stSql .= "                 )                                                           \n";
    $stSql .= "             ELSE                                                             \n";

    $stSql .= "                 'Percentual'                                         \n";

    $stSql .= "             END                                                             \n";

    $stSql .= "        END AS stDescricaoTipo                                           \n";
    $stSql .= "    FROM                                                                \n";
    $stSql .= "        ECONOMICO.CADASTRO_ECONOMICO_MODALIDADE_LANCAMENTO AS OCEML     \n";

    $stSql .= "    INNER JOIN                                                          \n";
    $stSql .= "        ECONOMICO.MODALIDADE_LANCAMENTO AS OML                           \n";
    $stSql .= "    ON                                                                   \n";
    $stSql .= "        OCEML.COD_MODALIDADE = OML.COD_MODALIDADE                        \n";

    $stSql .= "    LEFT JOIN                                                            \n";
    $stSql .= "        economico.cad_econ_modalidade_moeda AS ecemm                     \n";
    $stSql .= "    ON                                                                   \n";
    $stSql .= "        ecemm.cod_atividade = OCEML.cod_atividade                        \n";
    $stSql .= "        AND ecemm.inscricao_economica = OCEML.inscricao_economica        \n";
    $stSql .= "        AND ecemm.cod_modalidade = OCEML.cod_modalidade                  \n";
    $stSql .= "        AND ecemm.dt_inicio = OCEML.dt_inicio                            \n";

    $stSql .= "    LEFT JOIN                                                            \n";
    $stSql .= "        economico.cad_econ_modalidade_indicador AS ecemi                 \n";
    $stSql .= "    ON                                                                   \n";
    $stSql .= "        ecemi.cod_atividade = OCEML.cod_atividade                        \n";
    $stSql .= "        AND ecemi.inscricao_economica = OCEML.inscricao_economica        \n";
    $stSql .= "        AND ecemi.cod_modalidade = OCEML.cod_modalidade                  \n";
    $stSql .= "        AND ecemi.dt_inicio = OCEML.dt_inicio                            \n";
    $stSql .= "  ) AS MLIE                                                             \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "  MLIE.INSCRICAO_ECONOMICA = ACE.INSCRICAO_ECONOMICA AND                \n";
    $stSql .= "  MLIE.COD_ATIVIDADE       = ACE.COD_ATIVIDADE                          \n";

    return $stSql;
}

function recuperaAtividadesInscricao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAtividadesInscricao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}
function montaRecuperaAtividadesInscricao()
{
    $stSql  = "     SELECT                                                                  \n";
    $stSql .= "         c.cod_atividade,                                                    \n";
    $stSql .= "         c.cod_estrutural,                                                   \n";
    $stSql .= "         c.nom_atividade,                                                    \n";
    $stSql .= "         b.inscricao_economica,                                              \n";
    $stSql .= "         b.principal                                                         \n";
    $stSql .= "     FROM                                                                    \n";
    $stSql .= "         economico.cadastro_economico a                                      \n";
    $stSql .= "         INNER JOIN economico.atividade_cadastro_economico b                 \n";
    $stSql .= "         ON a.inscricao_economica = b.inscricao_economica                    \n";
    $stSql .= "         INNER JOIN (                                                        \n";
    $stSql .= "             select                                                          \n";
    $stSql .= "                 ace.inscricao_economica                                     \n";
    $stSql .= "                 , max( ace.ocorrencia_atividade) as ocorrencia_atividade    \n";
    $stSql .= "             FROM                                                            \n";
    $stSql .= "                 economico.atividade_cadastro_economico as ace               \n";
    $stSql .= "             GROUP BY                                                        \n";
    $stSql .= "                 ace.inscricao_economica                                     \n";
    $stSql .= "         ) AS max                                                            \n";
    $stSql .= "         ON max.ocorrencia_atividade = b.ocorrencia_atividade                \n";
    $stSql .= "         AND max.inscricao_economica = b.inscricao_economica                 \n";
    $stSql .= "         INNER JOIN economico.atividade c                                    \n";
    $stSql .= "         ON c.cod_atividade = b.cod_atividade                                \n";

    return $stSql;
}

function recuperaModalidadeLancamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaModalidadeLancamento().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaModalidadeLancamento()
{
    $stSQL .= " SELECT                                                                                                                  \n";
    $stSQL .= "     cadastro_economico.inscricao_economica                                                                              \n";
    $stSQL .= "     ,atividade.nom_atividade                                                                                            \n";
    $stSQL .= "     ,atividade_cadastro_economico.cod_atividade                                                                         \n";
    $stSQL .= "     ,atividade_cadastro_economico.ocorrencia_atividade                                                                  \n";
    $stSQL .= "     ,atividade_cadastro_economico.principal                                                                             \n";
    $stSQL .= "     ,atividade_cadastro_economico.dt_inicio                                                                             \n";
    $stSQL .= "     ,atividade_cadastro_economico.dt_termino                                                                            \n";
    $stSQL .= "     ,COALESCE(  cadastro_economico_modalidade_lancamento.cod_modalidade,                                                \n";
    $stSQL .= "                 atividade_modalidade_lancamento.cod_modalidade ) AS cod_modalidade                                      \n";
    $stSQL .= "     ,(  SELECT                                                                                                          \n";
    $stSQL .= "             nom_modalidade                                                                                              \n";
    $stSQL .= "         FROM                                                                                                            \n";
    $stSQL .= "             economico.modalidade_lancamento                                                                             \n";
    $stSQL .= "         WHERE                                                                                                           \n";
    $stSQL .= "             cod_modalidade = COALESCE(  cadastro_economico_modalidade_lancamento.cod_modalidade,                        \n";
    $stSQL .= "                                          atividade_modalidade_lancamento.cod_modalidade )) AS nom_modalidade            \n";
    $stSQL .= " FROM                                                                                                                    \n";
    $stSQL .= "     economico.cadastro_economico                                                                                        \n";
    $stSQL .= " INNER JOIN                                                                                                              \n";
    $stSQL .= "     economico.atividade_cadastro_economico                                                                              \n";
    $stSQL .= " ON                                                                                                                      \n";
    $stSQL .= "     cadastro_economico.inscricao_economica = atividade_cadastro_economico.inscricao_economica                           \n";

    $stSQL .= " INNER JOIN                                                                                                              \n";
    $stSQL .= "     economico.atividade                                                                                                 \n";
    $stSQL .= " ON                                                                                                                      \n";
    $stSQL .= "     atividade.cod_atividade = atividade_cadastro_economico.cod_atividade                           \n";

    $stSQL .= " LEFT JOIN                                                                                                               \n";
    $stSQL .= "     economico.atividade_modalidade_lancamento                                                                           \n";
    $stSQL .= " ON                                                                                                                      \n";
    $stSQL .= "     atividade_cadastro_economico.cod_atividade = atividade_modalidade_lancamento.cod_atividade                          \n";
    $stSQL .= " LEFT JOIN                                                                                                               \n";
    $stSQL .= "     economico.cadastro_economico_modalidade_lancamento                                                                  \n";
    $stSQL .= " ON                                                                                                                      \n";
    $stSQL .= "     atividade_cadastro_economico.inscricao_economica = cadastro_economico_modalidade_lancamento.inscricao_economica AND \n";
    $stSQL .= "     atividade_cadastro_economico.cod_atividade = cadastro_economico_modalidade_lancamento.cod_atividade                 \n";

    return $stSQL;
}

}
