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
    * Classe de mapeamento da tabela folhapagamento.reajuste_registro_evento_rescisao
    * Data de Criação: 04/12/2008

    * @author Analista     : Dagiane Vieira
    * @author Desenvolvedor: Rafael Garbin

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFolhaPagamentoReajusteRegistroEventoRescisao extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */
    public function TFolhaPagamentoReajusteRegistroEventoRescisao()
    {
        parent::Persistente();
        $this->setTabela("folhapagamento.reajuste_registro_evento_rescisao");

        $this->setCampoCod('');
        $this->setComplementoChave('cod_reajuste,cod_registro,timestamp,cod_evento,desdobramento');

        $this->AddCampo('cod_reajuste' ,'integer'  ,true  ,''   ,true,'TFolhaPagamentoReajuste');
        $this->AddCampo('cod_registro' ,'integer'  ,true  ,''   ,true,'TFolhaPagamentoRegistroEventoRescisao');
        $this->AddCampo('timestamp'    ,'timestamp',true  ,''   ,true,'TFolhaPagamentoRegistroEventoRescisao');
        $this->AddCampo('cod_evento'   ,'integer'  ,true  ,''   ,true,'TFolhaPagamentoRegistroEventoRescisao');
        $this->AddCampo('desdobramento','char'     ,true  ,'1'  ,true,'TFolhaPagamentoRegistroEventoRescisao');
    }

    public function recuperaReajusteRescisao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaReajusteRescisao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function recuperaReajustePensionistaRescisao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaReajustePensionistaRescisao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaReajustePensionistaRescisao()
    {
        $stSql  = "     SELECT registro_evento_rescisao.cod_contrato                                                                                                                                                                \n";
        $stSql .= "          , registro_evento_rescisao.cod_evento                                                                                                                                                                  \n";
        $stSql .= "          , registro_evento_rescisao.timestamp                                                                                                                                                                   \n";
        $stSql .= "          , to_real(registro_evento_rescisao.valor) as valor                                                                                                                                                     \n";
        $stSql .= "          , to_real(registro_evento_rescisao.quantidade) as quantidade                                                                                                                                           \n";
        $stSql .= "          , evento.descricao as evento                                                                                                                                                                           \n";
        $stSql .= "          , contrato.registro                                                                                                                                                                                    \n";
        $stSql .= "          , pensionista.nom_cgm                                                                                                                                                                                  \n";
        $stSql .= "          , (SELECT descricao FROM pessoal.regime WHERE cod_regime = contrato_pensionista_regime_funcao.cod_regime) as regime                                                           \n";
        $stSql .= "          , (SELECT descricao FROM pessoal.sub_divisao WHERE cod_sub_divisao = contrato_pensionista_sub_divisao_funcao.cod_sub_divisao) as sub_divisao                                  \n";
        $stSql .= "          , (SELECT descricao FROM pessoal.cargo WHERE cod_cargo = contrato_pensionista_funcao.cod_cargo) as funcao                                                                     \n";
        $stSql .= "          , (SELECT descricao FROM pessoal.especialidade WHERE cod_especialidade = contrato_pensionista_especialidade_funcao.cod_especialidade) as especialidade                        \n";
        $stSql .= "          , recuperaDescricaoOrgao(contrato_pensionista_orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as orgao                                                                                            \n";
        $stSql .= "       FROM folhapagamento.reajuste                                                                                                                                                     \n";
        $stSql .= " INNER JOIN folhapagamento.reajuste_registro_evento_rescisao                                                                                                                            \n";
        $stSql .= "         ON reajuste.cod_reajuste = reajuste_registro_evento_rescisao.cod_reajuste                                                                                                                               \n";
        $stSql .= " INNER JOIN folhapagamento.registro_evento_rescisao                                                                                                                                     \n";
        $stSql .= "         ON reajuste_registro_evento_rescisao.cod_registro     = registro_evento_rescisao.cod_registro                                                                                                           \n";
        $stSql .= "        AND reajuste_registro_evento_rescisao.timestamp        = registro_evento_rescisao.timestamp                                                                                                              \n";
        $stSql .= "        AND reajuste_registro_evento_rescisao.cod_evento       = registro_evento_rescisao.cod_evento                                                                                                             \n";
        $stSql .= " INNER JOIN folhapagamento.evento                                                                                                                                                       \n";
        $stSql .= "         ON registro_evento_rescisao.cod_evento = evento.cod_evento                                                                                                                                              \n";
        $stSql .= " INNER JOIN pessoal.contrato                                                                                                                                                            \n";
        $stSql .= "         ON registro_evento_rescisao.cod_contrato = contrato.cod_contrato                                                                                                                                        \n";
        $stSql .= " INNER JOIN pessoal.contrato_pensionista                                                                                                                                                \n";
        $stSql .= "         ON contrato.cod_contrato = contrato_pensionista.cod_contrato                                                                                                                                            \n";
        $stSql .= " INNER JOIN (SELECT contrato_pensionista_orgao.*                                                                                                                                                                 \n";
        $stSql .= "               FROM pessoal.contrato_pensionista_orgao                                                                                                                                  \n";
        $stSql .= "                  , (  SELECT cod_contrato                                                                                                                                                                       \n";
        $stSql .= "                            , max(timestamp) as timestamp                                                                                                                                                        \n";
        $stSql .= "                        FROM pessoal.contrato_pensionista_orgao                                                                                                                         \n";
        $stSql .= "                    GROUP BY cod_contrato) as max_contrato_pensionista_orgao                                                                                                                                     \n";
        $stSql .= "             WHERE contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato                                                                                                         \n";
        $stSql .= "               AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp ) AS contrato_pensionista_orgao                                                                               \n";
        $stSql .= "         ON contrato.cod_contrato = contrato_pensionista_orgao.cod_contrato                                                                                                                                      \n";
        $stSql .= " INNER JOIN (SELECT pensionista.*                                                                                                                                                                                \n";
        $stSql .= "                  , (SELECT nom_cgm FROM sw_cgm WHERE sw_cgm.numcgm = pensionista.numcgm) as nom_cgm                                                                                                             \n";
        $stSql .= "               FROM pessoal.pensionista) as pensionista                                                                                                                                 \n";
        $stSql .= "         ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                                                                                                                                   \n";
        $stSql .= "        AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente                                                                                                                         \n";
        $stSql .= "  LEFT JOIN (SELECT contrato_servidor_especialidade_funcao.*                                                                                                                                                     \n";
        $stSql .= "               FROM pessoal.contrato_servidor_especialidade_funcao                                                                                                                      \n";
        $stSql .= "                  , (  SELECT cod_contrato                                                                                                                                                                       \n";
        $stSql .= "                            , max(timestamp) as timestamp                                                                                                                                                        \n";
        $stSql .= "                         FROM pessoal.contrato_servidor_especialidade_funcao                                                                                                            \n";
        $stSql .= "                     GROUP BY cod_contrato) as max_contrato_servidor_especialidade_funcao                                                                                                                        \n";
        $stSql .= "            WHERE contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato                                                                                  \n";
        $stSql .= "              AND contrato_servidor_especialidade_funcao.timestamp = max_contrato_servidor_especialidade_funcao.timestamp ) AS contrato_pensionista_especialidade_funcao                                         \n";
        $stSql .= "         ON pensionista.cod_contrato_cedente = contrato_pensionista_especialidade_funcao.cod_contrato                                                                                                            \n";
        $stSql .= " INNER JOIN (SELECT contrato_servidor_regime_funcao.*                                                                                                                                                            \n";
        $stSql .= "               FROM pessoal.contrato_servidor_regime_funcao                                                                                                                             \n";
        $stSql .= "                  , (  SELECT cod_contrato                                                                                                                                                                       \n";
        $stSql .= "                            , max(timestamp) as timestamp                                                                                                                                                        \n";
        $stSql .= "                         FROM pessoal.contrato_servidor_regime_funcao                                                                                                                   \n";
        $stSql .= "                     GROUP BY cod_contrato) as max_contrato_servidor_regime_funcao                                                                                                                               \n";
        $stSql .= "               WHERE contrato_servidor_regime_funcao.cod_contrato = max_contrato_servidor_regime_funcao.cod_contrato                                                                                             \n";
        $stSql .= "                 AND contrato_servidor_regime_funcao.timestamp = max_contrato_servidor_regime_funcao.timestamp ) AS contrato_pensionista_regime_funcao                                                           \n";
        $stSql .= "         ON pensionista.cod_contrato_cedente = contrato_pensionista_regime_funcao.cod_contrato                                                                                                                   \n";
        $stSql .= " INNER JOIN (SELECT contrato_servidor_funcao.*                                                                                                                                                                   \n";
        $stSql .= "               FROM pessoal.contrato_servidor_funcao                                                                                                                                    \n";
        $stSql .= "                  , (  SELECT cod_contrato                                                                                                                                                                       \n";
        $stSql .= "                            , max(timestamp) as timestamp                                                                                                                                                        \n";
        $stSql .= "                         FROM pessoal.contrato_servidor_funcao                                                                                                                          \n";
        $stSql .= "                     GROUP BY cod_contrato) as max_contrato_servidor_funcao                                                                                                                                      \n";
        $stSql .= "               WHERE contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                                                                                                           \n";
        $stSql .= "                 AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp ) AS contrato_pensionista_funcao                                                                                \n";
        $stSql .= "         ON pensionista.cod_contrato_cedente = contrato_pensionista_funcao.cod_contrato                                                                                                                          \n";
        $stSql .= " INNER JOIN (SELECT contrato_servidor_sub_divisao_funcao.*                                                                                                                                                       \n";
        $stSql .= "               FROM pessoal.contrato_servidor_sub_divisao_funcao                                                                                                                        \n";
        $stSql .= "                  , (  SELECT cod_contrato                                                                                                                                                                       \n";
        $stSql .= "                            , max(timestamp) as timestamp                                                                                                                                                        \n";
        $stSql .= "                         FROM pessoal.contrato_servidor_sub_divisao_funcao                                                                                                              \n";
        $stSql .= "                     GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao                                                                                                                          \n";
        $stSql .= "               WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato                                                                                   \n";
        $stSql .= "                 AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp ) AS contrato_pensionista_sub_divisao_funcao                                            \n";
        $stSql .= "         ON pensionista.cod_contrato_cedente = contrato_pensionista_sub_divisao_funcao.cod_contrato                                                                                                              \n";
        $stSql .= "  LEFT JOIN (SELECT contrato_servidor_local.*                                                                                                                                                                    \n";
        $stSql .= "               FROM pessoal.contrato_servidor_local                                                                                                                                     \n";
        $stSql .= "                  , (  SELECT cod_contrato                                                                                                                                                                       \n";
        $stSql .= "                            , max(timestamp) as timestamp                                                                                                                                                        \n";
        $stSql .= "                         FROM pessoal.contrato_servidor_local                                                                                                                           \n";
        $stSql .= "                     GROUP BY cod_contrato) as max_contrato_servidor_local                                                                                                                                       \n";
        $stSql .= "               WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                                                                                                             \n";
        $stSql .= "                 AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp ) AS contrato_pensionista_local                                                                                   \n";
        $stSql .= "         ON pensionista.cod_contrato_cedente = contrato_pensionista_local.cod_contrato                                                                                                                           \n";
        $stSql .= " INNER JOIN ( SELECT contrato_servidor_padrao.*                                                                                                                                                                  \n";
        $stSql .= "                    , padrao.descricao as padrao                                                                                                                                                                 \n";
        $stSql .= "                    , valor                                                                                                                                                                                      \n";
        $stSql .= "                 FROM pessoal.contrato_servidor_padrao                                                                                                                                  \n";
        $stSql .= "                    , (  SELECT cod_contrato                                                                                                                                                                     \n";
        $stSql .= "                              , max(timestamp) as timestamp                                                                                                                                                      \n";
        $stSql .= "                           FROM pessoal.contrato_servidor_padrao                                                                                                                        \n";
        $stSql .= "                       GROUP BY cod_contrato) as max_contrato_servidor_padrao                                                                                                                                    \n";
        $stSql .= "                    , folhapagamento.padrao_padrao                                                                                                                                      \n";
        $stSql .= "                    , folhapagamento.padrao                                                                                                                                             \n";
        $stSql .= "                    , (  SELECT cod_padrao                                                                                                                                                                       \n";
        $stSql .= "                              , max(timestamp) as timestamp                                                                                                                                                      \n";
        $stSql .= "                           FROM folhapagamento.padrao_padrao                                                                                                                            \n";
        $stSql .= "                       GROUP BY cod_padrao) as max_padrao_padrao                                                                                                                                                 \n";
        $stSql .= "                WHERE contrato_servidor_padrao.cod_contrato = max_contrato_servidor_padrao.cod_contrato                                                                                                          \n";
        $stSql .= "                  AND contrato_servidor_padrao.timestamp = max_contrato_servidor_padrao.timestamp                                                                                                                \n";
        $stSql .= "                  AND contrato_servidor_padrao.cod_padrao = padrao_padrao.cod_padrao                                                                                                                             \n";
        $stSql .= "                  AND padrao_padrao.cod_padrao = padrao.cod_padrao                                                                                                                                               \n";
        $stSql .= "                  AND padrao_padrao.cod_padrao = max_padrao_padrao.cod_padrao                                                                                                                                    \n";
        $stSql .= "                  AND padrao_padrao.timestamp = max_padrao_padrao.timestamp) as contrato_pensionista_padrao                                                                                                      \n";
        $stSql .= "         ON pensionista.cod_contrato_cedente = contrato_pensionista_padrao.cod_contrato                                                                                                                          \n";

        if ($this->getDado("boAtributo") == true) {
            $stSql .= " INNER JOIN ( SELECT atributo_contrato_pensionista.*                                                                                                                                                          \n";
            $stSql .= "                FROM pessoal.atributo_contrato_pensionista                                                                                                                           \n";
            $stSql .= "                   , (  SELECT cod_contrato                                                                                                                                                                   \n";
            $stSql .= "                             , max(timestamp) as timestamp                                                                                                                                                    \n";
            $stSql .= "                          FROM pessoal.atributo_contrato_pensionista                                                                                                                 \n";
            $stSql .= "                      GROUP BY cod_contrato) as max_atributo_contrato_pensionista                                                                                                                             \n";
            $stSql .= "               WHERE atributo_contrato_pensionista.cod_contrato = max_atributo_contrato_pensionista.cod_contrato                                                                                              \n";
            $stSql .= "                 AND atributo_contrato_pensionista.timestamp = max_atributo_contrato_pensionista.timestamp ) AS atributo_contrato_pensionista                                                                 \n";
            $stSql .= "         ON contrato.cod_contrato = atributo_contrato_pensionista.cod_contrato                                                                                                                                \n";
        }
        $stSql .= "         WHERE NOT EXISTS ( SELECT cod_contrato                                                                                                                                                      \n";
        $stSql .= "                              FROM pessoal.contrato_servidor_caso_causa                                                                                                     \n";
        $stSql .= "                             WHERE to_char(dt_rescisao,'yyyy-mm') <= '".$this->getDado("competencia")."'                                                                                             \n";
        $stSql .= "                               AND contrato.cod_contrato = cod_contrato)                                    \n";
        $stSql .= "   AND registro_evento_rescisao.cod_evento = ".$this->getDado("inCodEvento")."                              \n";
        $stSql .= "   AND registro_evento_rescisao.cod_periodo_movimentacao = ".$this->getDado("inCodPeriodoMovimentacao")."   \n";
        $stSql .= "   AND reajuste.cod_reajuste = ".$this->getDado("inCodReajuste")."                                          \n";

        return $stSql;
    }

    public function montaRecuperaReajusteRescisao()
    {
        $stSql  = "        SELECT registro_evento_rescisao.cod_contrato                                                                                                                                                \n";
        $stSql .= "             , registro_evento_rescisao.cod_evento                                                                                                                                                  \n";
        $stSql .= "             , registro_evento_rescisao.timestamp                                                                                                                                                   \n";
        $stSql .= "             , to_real(registro_evento_rescisao.valor) as valor                                                                                                                                     \n";
        $stSql .= "             , to_real(registro_evento_rescisao.quantidade) as quantidade                                                                                                                           \n";
        $stSql .= "             , evento.descricao as evento                                                                                                                                                           \n";
        $stSql .= "             , contrato.registro                                                                                                                                                                    \n";
        $stSql .= "             , sw_cgm.nom_cgm                                                                                                                                                                       \n";
        $stSql .= "             , (SELECT descricao FROM pessoal.regime WHERE cod_regime = contrato_servidor_regime_funcao.cod_regime) AS regime                                              \n";
        $stSql .= "             , (SELECT descricao FROM pessoal.sub_divisao WHERE cod_sub_divisao = contrato_servidor_sub_divisao_funcao.cod_sub_divisao) AS sub_divisao                     \n";
        $stSql .= "             , (SELECT descricao FROM pessoal.cargo WHERE cod_cargo = contrato_servidor_funcao.cod_cargo) AS funcao                                                        \n";
        $stSql .= "             , recuperaDescricaoOrgao(contrato_servidor_orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as orgao                                                                               \n";
        $stSql .= "             , (SELECT descricao FROM pessoal.especialidade WHERE cod_especialidade = contrato_servidor_especialidade_funcao.cod_especialidade) AS especialidade           \n";
        $stSql .= "          FROM folhapagamento.reajuste                                                                                                                                     \n";
        $stSql .= "    INNER JOIN folhapagamento.reajuste_registro_evento_rescisao                                                                                                            \n";
        $stSql .= "            ON reajuste.cod_reajuste = reajuste_registro_evento_rescisao.cod_reajuste                                                                                                               \n";
        $stSql .= "    INNER JOIN folhapagamento.registro_evento_rescisao                                                                                                                     \n";
        $stSql .= "            ON reajuste_registro_evento_rescisao.cod_registro     = registro_evento_rescisao.cod_registro                                                                                           \n";
        $stSql .= "           AND reajuste_registro_evento_rescisao.timestamp        = registro_evento_rescisao.timestamp                                                                                              \n";
        $stSql .= "           AND reajuste_registro_evento_rescisao.cod_evento       = registro_evento_rescisao.cod_evento                                                                                             \n";
        $stSql .= "    INNER JOIN folhapagamento.evento                                                                                                                                       \n";
        $stSql .= "            ON registro_evento_rescisao.cod_evento = evento.cod_evento                                                                                                                              \n";
        $stSql .= "    INNER JOIN pessoal.contrato                                                                                                                                            \n";
        $stSql .= "            ON registro_evento_rescisao.cod_contrato = contrato.cod_contrato                                                                                                                        \n";
        $stSql .= "    INNER JOIN pessoal.servidor_contrato_servidor                                                                                                                          \n";
        $stSql .= "            ON registro_evento_rescisao.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                                                      \n";
        $stSql .= "    INNER JOIN pessoal.servidor                                                                                                                                            \n";
        $stSql .= "            ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                                                      \n";
        $stSql .= "    INNER JOIN sw_cgm                                                                                                                                                                               \n";
        $stSql .= "            ON sw_cgm.numcgm = servidor.numcgm                                                                                                                                                      \n";
        $stSql .= "    INNER JOIN (SELECT contrato_servidor_regime_funcao.*                                                                                                                                            \n";
        $stSql .= "                  FROM pessoal.contrato_servidor_regime_funcao                                                                                                             \n";
        $stSql .= "                     , (  SELECT cod_contrato                                                                                                                                                       \n";
        $stSql .= "                               , max(timestamp) as timestamp                                                                                                                                        \n";
        $stSql .= "                            FROM pessoal.contrato_servidor_regime_funcao                                                                                                   \n";
        $stSql .= "                        GROUP BY cod_contrato) as max_contrato_servidor_regime_funcao                                                                                                               \n";
        $stSql .= "                 WHERE contrato_servidor_regime_funcao.cod_contrato = max_contrato_servidor_regime_funcao.cod_contrato                                                                              \n";
        $stSql .= "                   AND contrato_servidor_regime_funcao.timestamp = max_contrato_servidor_regime_funcao.timestamp ) AS contrato_servidor_regime_funcao                                               \n";
        $stSql .= "            ON contrato.cod_contrato = contrato_servidor_regime_funcao.cod_contrato                                                                                                                 \n";
        $stSql .= "    INNER JOIN ( SELECT contrato_servidor_sub_divisao_funcao.*                                                                                                                                      \n";
        $stSql .= "                   FROM pessoal.contrato_servidor_sub_divisao_funcao                                                                                                       \n";
        $stSql .= "                      , (  SELECT cod_contrato                                                                                                                                                      \n";
        $stSql .= "                               , max(timestamp) as timestamp                                                                                                                                        \n";
        $stSql .= "                            FROM pessoal.contrato_servidor_sub_divisao_funcao                                                                                              \n";
        $stSql .= "                        GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao                                                                                                          \n";
        $stSql .= "                  WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato                                                                   \n";
        $stSql .= "                    AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp ) AS contrato_servidor_sub_divisao_funcao                               \n";
        $stSql .= "            ON contrato.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato                                                                                                            \n";
        $stSql .= "    INNER JOIN (SELECT contrato_servidor_funcao.*                                                                                                                                                   \n";
        $stSql .= "                  FROM pessoal.contrato_servidor_funcao                                                                                                                    \n";
        $stSql .= "                     , (  SELECT cod_contrato                                                                                                                                                       \n";
        $stSql .= "                               , max(timestamp) as timestamp                                                                                                                                        \n";
        $stSql .= "                            FROM pessoal.contrato_servidor_funcao                                                                                                          \n";
        $stSql .= "                        GROUP BY cod_contrato) as max_contrato_servidor_funcao                                                                                                                      \n";
        $stSql .= "                  WHERE contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                                                                                           \n";
        $stSql .= "                    AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp ) AS contrato_servidor_funcao                                                                   \n";
        $stSql .= "            ON contrato.cod_contrato = contrato_servidor_funcao.cod_contrato                                                                                                                        \n";
        $stSql .= "    INNER JOIN (SELECT contrato_servidor_orgao.*                                                                                                                                                    \n";
        $stSql .= "                  FROM pessoal.contrato_servidor_orgao                                                                                                                     \n";
        $stSql .= "                     , (  SELECT cod_contrato                                                                                                                                                       \n";
        $stSql .= "                               , max(timestamp) as timestamp                                                                                                                                        \n";
        $stSql .= "                            FROM pessoal.contrato_servidor_orgao                                                                                                           \n";
        $stSql .= "                        GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                                                                                       \n";
        $stSql .= "                 WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                                                                                              \n";
        $stSql .= "                   AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp ) AS contrato_servidor_orgao                                                                       \n";
        $stSql .= "            ON contrato.cod_contrato = contrato_servidor_orgao.cod_contrato                                                                                                                         \n";
        $stSql .= "     LEFT JOIN (SELECT contrato_servidor_especialidade_funcao.*                                                                                                                                     \n";
        $stSql .= "                  FROM pessoal.contrato_servidor_especialidade_funcao                                                                                                      \n";
        $stSql .= "                     , (  SELECT cod_contrato                                                                                                                                                       \n";
        $stSql .= "                               , max(timestamp) as timestamp                                                                                                                                        \n";
        $stSql .= "                            FROM pessoal.contrato_servidor_especialidade_funcao                                                                                            \n";
        $stSql .= "                        GROUP BY cod_contrato) as max_contrato_servidor_especialidade_funcao                                                                                                        \n";
        $stSql .= "                  WHERE contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato                                                               \n";
        $stSql .= "                    AND contrato_servidor_especialidade_funcao.timestamp = max_contrato_servidor_especialidade_funcao.timestamp ) AS contrato_servidor_especialidade_funcao                         \n";
        $stSql .= "            ON contrato.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato                                                                                                          \n";
        $stSql .= "         WHERE NOT EXISTS ( SELECT cod_contrato                                                                                                                                                     \n";
        $stSql .= "                              FROM pessoal.contrato_servidor_caso_causa                                                                                                    \n";
        $stSql .= "                             WHERE to_char(dt_rescisao,'yyyy-mm') <= '".$this->getDado("competencia")."'                                                                                            \n";
        $stSql .= "                               AND contrato.cod_contrato = cod_contrato)                                                                                                                            \n";

        if ($this->getDado("boAtributo") == true) {
            $stSql .= " INNER JOIN (SELECT atributo_contrato_servidor_valor.*                                                                                                                        \n";
            $stSql .= "               FROM pessoal.atributo_contrato_servidor_valor                                                                                         \n";
            $stSql .= "                  , (  SELECT cod_contrato                                                                                                                                    \n";
            $stSql .= "                            , max(timestamp) as timestamp                                                                                                                     \n";
            $stSql .= "                         FROM pessoal.atributo_contrato_servidor_valor                                                                               \n";
            $stSql .= "                     GROUP BY cod_contrato) as max_atributo_contrato_servidor_valor                                                                                           \n";
            $stSql .= "               WHERE atributo_contrato_servidor_valor.cod_contrato = max_atributo_contrato_servidor_valor.cod_contrato                                                        \n";
            $stSql .= "                 AND atributo_contrato_servidor_valor.timestamp = max_atributo_contrato_servidor_valor.timestamp ) AS atributo_contrato_servidor_valor                        \n";
            $stSql .= "         ON contrato.cod_contrato = atributo_contrato_servidor_valor.cod_contrato                                                                                             \n";
        }
        $stSql .= "         WHERE NOT EXISTS ( SELECT cod_contrato                                                                                                                                                      \n";
        $stSql .= "                              FROM pessoal.contrato_servidor_caso_causa                                                                                                     \n";
        $stSql .= "                             WHERE to_char(dt_rescisao,'yyyy-mm') <= '".$this->getDado("competencia")."'                                                                                             \n";
        $stSql .= "                               AND contrato.cod_contrato = cod_contrato)                                                                                                                             \n";

        if ($this->getDado("boAposentado") == true ) {
            $stSql .= "AND EXISTS (                                                                                          \n";
        }
        if ($this->getDado("boServidor") == true ) {
            $stSql .= "AND NOT EXISTS (                                                                                      \n";
        }
        $stSql .= "SELECT 1                                                                                                  \n";
        $stSql .= "  FROM pessoal.aposentadoria                                                     \n";
        $stSql .= "     , (SELECT cod_contrato                                                                               \n";
        $stSql .= "             , max(timestamp) as timestamp                                                                \n";
        $stSql .= "          FROM pessoal.aposentadoria                                             \n";
        $stSql .= "        GROUP BY cod_contrato) as max_aposentadoria                                                       \n";
        $stSql .= " WHERE aposentadoria.cod_contrato = max_aposentadoria.cod_contrato                                        \n";
        $stSql .= "   AND aposentadoria.timestamp = max_aposentadoria.timestamp                                              \n";
        $stSql .= "   AND NOT EXISTS (SELECT 1                                                                               \n";
        $stSql .= "                     FROM pessoal.aposentadoria_excluida                         \n";
        $stSql .= "                    WHERE aposentadoria.cod_contrato = aposentadoria_excluida.cod_contrato                \n";
        $stSql .= "                      AND aposentadoria.timestamp = aposentadoria.timestamp)                              \n";
        if ($this->getDado("boAposentado") == true ) {
            $stSql .= "   AND NOT EXISTS (SELECT 1                                                                           \n";
            $stSql .= "                     FROM pessoal.aposentadoria_encerramento                 \n";
            $stSql .= "                    WHERE aposentadoria.cod_contrato = aposentadoria_encerramento.cod_contrato        \n";
            $stSql .= "                      AND aposentadoria.timestamp = aposentadoria_encerramento.timestamp              \n";
            $stSql .= "                      AND to_char(dt_encerramento,'yyyy-mm') != '".$this->getDado("competencia")."')  \n";
        }
        $stSql .= "   AND contrato.cod_contrato = aposentadoria.cod_contrato)                                                \n";
        $stSql .= "   AND registro_evento_rescisao.cod_evento = ".$this->getDado("inCodEvento")."                            \n";
        $stSql .= "   AND registro_evento_rescisao.cod_periodo_movimentacao = ".$this->getDado("inCodPeriodoMovimentacao")." \n";
        $stSql .= "   AND reajuste.cod_reajuste = ".$this->getDado("inCodReajuste")."                                        \n";

        return $stSql;
    }
}
?>
