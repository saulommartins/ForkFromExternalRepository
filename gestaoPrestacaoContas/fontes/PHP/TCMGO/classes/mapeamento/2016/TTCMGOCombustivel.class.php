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

/*
    * Classe de mapeamento da tabela tcmgo.combustivel
    * Data de Criação   : 23/12/2008

    * @author Analista      Tonismar Bernardo
    * @author Desenvolvedor André Machado

    * @package URBEM
    * @subpackage

    $Id: TTCMGOCombustivel.class.php 65190 2016-04-29 19:36:51Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMGOCombustivel extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setTabela("tcmgo.combustivel");

    $this->setCampoCod('cod_combustivel');
    $this->setComplementoChave('cod_tipo');

    $this->AddCampo( 'cod_combustivel' , 'integer'    , true  , ''   , true  , true  );
    $this->AddCampo( 'cod_tipo'        , 'integer'    , true  , ''   , true  , true  );
    $this->AddCampo( 'descricao'       , 'varchar'    , true  , ''   , true  , true  );
}

function recuperaEmpenhoCombustivel(&$rsRecordSet, $stFiltro)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = $this->montaRecuperaEmpenhoCombustivel().$stFiltro;

    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

    return $obErro;
}

function montaRecuperaEmpenhoCombustivel()
{
    $stSql  = "SELECT 20 as tipo_registro                    \n";
    $stSql .= "     , despesa.cod_programa                   \n";
    $stSql .= "     , despesa.num_orgao                      \n";
    $stSql .= "     , despesa.num_unidade                    \n";
    $stSql .= "     , despesa.cod_funcao                     \n";
    $stSql .= "     , despesa.cod_subfuncao                  \n";
    $stSql .= "     , substr(TO_CHAR(despesa.num_pao, '9999'), 2, 1) as natureza_acao   \n";
    $stSql .= "     , substr(TO_CHAR(despesa.num_pao, '9999'), 3, 3) as nro_proj_ativ   \n";
    $stSql .= "     , orcamento.recuperaEstruturalDespesa(despesa.cod_conta, despesa.exercicio, 6, FALSE, FALSE) AS elemento_despesa   \n";
    $stSql .= "     , orcamento.recuperaEstruturalDespesa(despesa.cod_conta, despesa.exercicio, 2, TRUE, FALSE) AS subelemento_despesa \n";
    $stSql .= "     , empenho.cod_empenho                                                                       \n";
    $stSql .= "     , TO_CHAR(empenho.dt_empenho,'ddmmyyyy') AS dt_empenho                                      \n";
    $stSql .= "     , '' AS espaco_branco                                                                       \n";
    $stSql .= "     , 0 AS nro_sequencial                                                                       \n";
    $stSql .= " FROM empenho.item_pre_empenho_julgamento                                                        \n";
    $stSql .= "     JOIN frota.item                                                                             \n";
    $stSql .= "        ON item_pre_empenho_julgamento.cod_item = item.cod_item                                  \n";
    $stSql .= "     JOIN empenho.item_pre_empenho                                                               \n";
    $stSql .= "         ON item_pre_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho       \n";
    $stSql .= "         AND item_pre_empenho.exercicio = item_pre_empenho_julgamento.exercicio                  \n";
    $stSql .= "         AND item_pre_empenho.num_item = item_pre_empenho_julgamento.num_item                    \n";
    $stSql .= "     JOIN empenho.pre_empenho                                                                    \n";
    $stSql .= "         ON pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho                       \n";
    $stSql .= "         AND pre_empenho.exercicio = item_pre_empenho.exercicio                                  \n";
    $stSql .= "     JOIN empenho.empenho                                                                        \n";
    $stSql .= "         ON empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho                                \n";
    $stSql .= "         AND empenho.exercicio = pre_empenho.exercicio                                           \n";
    $stSql .= "     JOIN empenho.pre_empenho_despesa                                                            \n";
    $stSql .= "         ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho                    \n";
    $stSql .= "         AND pre_empenho_despesa.exercicio = pre_empenho.exercicio                               \n";
    $stSql .= "     JOIN orcamento.despesa                                                                      \n";
    $stSql .= "         ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa                                \n";
    $stSql .= "         AND despesa.exercicio = pre_empenho_despesa.exercicio                                   \n";
return $stSql;
}
function recuperaEstoqueCombustivel(&$rsRecordSet, $stFiltro)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = $this->montaRecuperaEstoqueCombustivel().$stFiltro;
    $stSql .= " GROUP BY alm.tipo_natureza,
                         an.cod_natureza,
                         an.descricao,
                         tc.descricao,
                         tc.cod_tipo,
                         tc.cod_combustivel,
                         qtdestper,
                         qtdCompra,
                         qtdDoacaoEnt,
                         qtdDoacaoSai,
                          qtdSaidaCons              \n";

    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

    return $obErro;
}

function montaRecuperaEstoqueCombustivel()
{
    $arFiltroRelatorio = Sessao::Read('filtroRelatorio');
    $stDataInicial     = $arFiltroRelatorio['stDataInicial'];
    $stDataFinal       = $arFiltroRelatorio['stDataFinal'];

    $stSql  = " SELECT 10 as tipo_registro                                                          \n";
    $stSql .= "      , alm.tipo_natureza                                                            \n";
    $stSql .= "      , 0 as cod_orgao                                                               \n";
    $stSql .= "      , sum(alm.quantidade) as qtdEstAtual                                           \n";
    $stSql .= "      , an.cod_natureza                                                              \n";
    $stSql .= "      , an.descricao                                                                 \n";
    $stSql .= "      , TC.descricao as combustivel                                                  \n";
    $stSql .= "      , TC.cod_tipo                                                                  \n";
    $stSql .= "      , TC.cod_combustivel                                                           \n";
    $stSql .= "      , temp.qtdEstPer                                                               \n";
    $stSql .= "      , temp1.qtdCompra                                                              \n";
    $stSql .= "      , temp2.qtdDoacaoEnt                                                           \n";
    $stSql .= "      , temp3.qtdDoacaoSai                                                           \n";
    $stSql .= "      , temp4.qtdSaidaCons                                                           \n";
    $stSql .= "    FROM almoxarifado.lancamento_material alm                                        \n";
    $stSql .= "    INNER JOIN almoxarifado.catalogo_item aci                                        \n";
    $stSql .= "       ON aci.cod_item = alm.cod_item                                                \n";
    $stSql .= "    INNER JOIN frota.item fi                                                         \n";
    $stSql .= "       ON aci.cod_item  = fi.cod_item                                                \n";
    $stSql .= "      AND fi.cod_tipo IN (1,3)                                                       \n";
    $stSql .= "    INNER JOIN tcmgo.combustivel_vinculo TCV                                         \n";
    $stSql .= "       ON TCV.cod_item = fi.cod_item                                                 \n";
    $stSql .= "     LEFT JOIN tcmgo.combustivel TC                                                  \n";
    $stSql .= "       ON TC.cod_combustivel = TCV.cod_combustivel                                   \n";
    $stSql .= "      AND TC.cod_tipo        = TCV.cod_tipo                                          \n";
    $stSql .= "    INNER JOIN almoxarifado.natureza AN                                              \n";
    $stSql .= "       ON AN.cod_natureza  = alm.cod_natureza                                        \n";
    $stSql .= "      AND AN.tipo_natureza = alm.tipo_natureza                                       \n";
    $stSql .= "    INNER JOIN almoxarifado.natureza_lancamento as ANL                               \n";
    $stSql .= "       ON ALM.exercicio_lancamento = ANL.exercicio_lancamento                        \n";
    $stSql .= "      AND ALM.num_lancamento       = ANL.num_lancamento                              \n";
    $stSql .= "      AND ALM.cod_natureza     = ANL.cod_natureza                                    \n";
    $stSql .= "      AND ALM.tipo_natureza    = ANL.tipo_natureza                                   \n";
    $stSql .= "     LEFT JOIN (                                                                     \n";
    $stSql .= "             SELECT                                                                  \n";
    $stSql .= "                 alm.tipo_natureza                                                   \n";
    $stSql .= "                 , sum(alm.quantidade) as qtdEstPer                                  \n";
    $stSql .= "                 , an.cod_natureza                                                   \n";
    $stSql .= "                 , an.descricao                                                      \n";
    $stSql .= "                 , TC.descricao as combustivel                                       \n";
    $stSql .= "               FROM almoxarifado.lancamento_material alm                             \n";
    $stSql .= "               INNER JOIN almoxarifado.catalogo_item aci                             \n";
    $stSql .= "                  ON aci.cod_item = alm.cod_item                                     \n";
    $stSql .= "               INNER JOIN frota.item fi                                              \n";
    $stSql .= "                  ON aci.cod_item  = fi.cod_item                                     \n";
    $stSql .= "                 AND fi.cod_tipo IN (1,3)                                            \n";
    $stSql .= "               INNER JOIN tcmgo.combustivel_vinculo TCV                              \n";
    $stSql .= "                  ON TCV.cod_item = fi.cod_item                                      \n";
    $stSql .= "                LEFT JOIN tcmgo.combustivel TC                                       \n";
    $stSql .= "                  ON TC.cod_combustivel = TCV.cod_combustivel                        \n";
    $stSql .= "                 AND TC.cod_tipo        = TCV.cod_tipo                               \n";
    $stSql .= "               INNER JOIN almoxarifado.natureza AN                                   \n";
    $stSql .= "                  ON AN.cod_natureza  = alm.cod_natureza                             \n";
    $stSql .= "                 AND AN.tipo_natureza = alm.tipo_natureza                            \n";
    $stSql .= "               INNER JOIN almoxarifado.natureza_lancamento as ANL                    \n";
    $stSql .= "                  ON ALM.exercicio_lancamento = ANL.exercicio_lancamento             \n";
    $stSql .= "                 AND ALM.num_lancamento       = ANL.num_lancamento                   \n";
    $stSql .= "                 AND ALM.cod_natureza     = ANL.cod_natureza                         \n";
    $stSql .= "                 AND ALM.tipo_natureza    = ANL.tipo_natureza                        \n";
    $stSql .= "            WHERE anl.timestamp < '".$stDataInicial."'                                 \n";
    $stSql .= "            GROUP BY alm.tipo_natureza,-- alm.cod_item, aci.descricao,               \n";
    $stSql .= "             an.cod_natureza, an.descricao, tc.descricao                             \n";
    $stSql .= "            ) as temp                                                                \n";
    $stSql .= "   ON temp.tipo_natureza  = alm.tipo_natureza                                        \n";
    $stSql .= "  AND temp.cod_natureza   = an.cod_natureza                                          \n";
    $stSql .= "  AND temp.descricao      = an.descricao                                             \n";
    $stSql .= "  AND temp.combustivel    = TC.descricao                                             \n";
    $stSql .= "     LEFT JOIN (                                                                     \n";
    $stSql .= "              SELECT                                                                 \n";
    $stSql .= "                  alm.tipo_natureza                                                  \n";
    $stSql .= "                  , sum(alm.quantidade) as qtdCompra                                 \n";
    $stSql .= "                  , an.cod_natureza                                                  \n";
    $stSql .= "                  , an.descricao                                                     \n";
    $stSql .= "                  , TC.descricao as combustivel                                      \n";
    $stSql .= "                FROM almoxarifado.lancamento_material alm                            \n";
    $stSql .= "                INNER JOIN almoxarifado.catalogo_item aci                            \n";
    $stSql .= "                   ON aci.cod_item = alm.cod_item                                    \n";
    $stSql .= "                INNER JOIN frota.item fi                                             \n";
    $stSql .= "                   ON aci.cod_item  = fi.cod_item                                    \n";
    $stSql .= "                  AND fi.cod_tipo IN (1,3)                                           \n";
    $stSql .= "                INNER JOIN tcmgo.combustivel_vinculo TCV                             \n";
    $stSql .= "                   ON TCV.cod_item = fi.cod_item                                     \n";
    $stSql .= "                 LEFT JOIN tcmgo.combustivel TC                                      \n";
    $stSql .= "                   ON TC.cod_combustivel = TCV.cod_combustivel                       \n";
    $stSql .= "                  AND TC.cod_tipo        = TCV.cod_tipo                              \n";
    $stSql .= "                INNER JOIN almoxarifado.natureza AN                                  \n";
    $stSql .= "                   ON AN.cod_natureza  = alm.cod_natureza                            \n";
    $stSql .= "                  AND AN.tipo_natureza = alm.tipo_natureza                           \n";
    $stSql .= "                INNER JOIN almoxarifado.natureza_lancamento as ANL                   \n";
    $stSql .= "                   ON ALM.exercicio_lancamento = ANL.exercicio_lancamento            \n";
    $stSql .= "                  AND ALM.num_lancamento       = ANL.num_lancamento                  \n";
    $stSql .= "                  AND ALM.cod_natureza     = ANL.cod_natureza                        \n";
    $stSql .= "                  AND ALM.tipo_natureza    = ANL.tipo_natureza                       \n";
    $stSql .= "             WHERE an.cod_natureza = 1                                               \n";
    $stSql .= "             GROUP BY alm.tipo_natureza,-- alm.cod_item, aci.descricao,              \n";
    $stSql .= "              an.cod_natureza, an.descricao, tc.descricao                            \n";
    $stSql .= "     ) as temp1                                                                      \n";
    $stSql .= "   ON temp1.tipo_natureza  = alm.tipo_natureza                                       \n";
    $stSql .= "  AND temp1.cod_natureza   = an.cod_natureza                                         \n";
    $stSql .= "  AND temp1.descricao      = an.descricao                                            \n";
    $stSql .= "  AND temp1.combustivel    = TC.descricao                                            \n";
    $stSql .= "     LEFT JOIN (                                                                     \n";
    $stSql .= "              SELECT                                                                 \n";
    $stSql .= "                  alm.tipo_natureza                                                  \n";
    $stSql .= "                  , sum(alm.quantidade) as qtdDoacaoEnt                              \n";
    $stSql .= "                  , an.cod_natureza                                                  \n";
    $stSql .= "                  , an.descricao                                                     \n";
    $stSql .= "                  , TC.descricao as combustivel                                      \n";
    $stSql .= "                FROM almoxarifado.lancamento_material alm                            \n";
    $stSql .= "                INNER JOIN almoxarifado.catalogo_item aci                            \n";
    $stSql .= "                   ON aci.cod_item = alm.cod_item                                    \n";
    $stSql .= "                INNER JOIN frota.item fi                                             \n";
    $stSql .= "                   ON aci.cod_item  = fi.cod_item                                    \n";
    $stSql .= "                  AND fi.cod_tipo IN (1,3)                                           \n";
    $stSql .= "                INNER JOIN tcmgo.combustivel_vinculo TCV                             \n";
    $stSql .= "                   ON TCV.cod_item = fi.cod_item                                     \n";
    $stSql .= "                 LEFT JOIN tcmgo.combustivel TC                                      \n";
    $stSql .= "                   ON TC.cod_combustivel = TCV.cod_combustivel                       \n";
    $stSql .= "                  AND TC.cod_tipo        = TCV.cod_tipo                              \n";
    $stSql .= "                INNER JOIN almoxarifado.natureza AN                                  \n";
    $stSql .= "                   ON AN.cod_natureza  = alm.cod_natureza                            \n";
    $stSql .= "                  AND AN.tipo_natureza = alm.tipo_natureza                           \n";
    $stSql .= "                INNER JOIN almoxarifado.natureza_lancamento as ANL                   \n";
    $stSql .= "                   ON ALM.exercicio_lancamento = ANL.exercicio_lancamento            \n";
    $stSql .= "                  AND ALM.num_lancamento       = ANL.num_lancamento                  \n";
    $stSql .= "                  AND ALM.cod_natureza     = ANL.cod_natureza                        \n";
    $stSql .= "                  AND ALM.tipo_natureza    = ANL.tipo_natureza                       \n";
    $stSql .= "             WHERE an.cod_natureza = 3 and an.tipo_natureza = 'E'                    \n";
    $stSql .= "             GROUP BY alm.tipo_natureza,                                             \n";
    $stSql .= "              an.cod_natureza, an.descricao, tc.descricao                            \n";
    $stSql .= "     ) as temp2                                                                      \n";
    $stSql .= "   ON temp2.tipo_natureza  = alm.tipo_natureza                                       \n";
    $stSql .= "  AND temp2.cod_natureza   = an.cod_natureza                                         \n";
    $stSql .= "  AND temp2.descricao      = an.descricao                                            \n";
    $stSql .= "  AND temp2.combustivel    = TC.descricao                                            \n";
    $stSql .= "      LEFT JOIN (                                                                    \n";
    $stSql .= "               SELECT                                                                \n";
    $stSql .= "                   alm.tipo_natureza                                                 \n";
    $stSql .= "                   , sum(alm.quantidade) as qtdDoacaoSai                             \n";
    $stSql .= "                   , an.cod_natureza                                                 \n";
    $stSql .= "                   , an.descricao                                                    \n";
    $stSql .= "                   , TC.descricao as combustivel                                     \n";
    $stSql .= "                 FROM almoxarifado.lancamento_material alm                           \n";
    $stSql .= "                 INNER JOIN almoxarifado.catalogo_item aci                           \n";
    $stSql .= "                    ON aci.cod_item = alm.cod_item                                   \n";
    $stSql .= "                 INNER JOIN frota.item fi                                            \n";
    $stSql .= "                    ON aci.cod_item  = fi.cod_item                                   \n";
    $stSql .= "                   AND fi.cod_tipo IN (1,3)                                          \n";
    $stSql .= "                 INNER JOIN tcmgo.combustivel_vinculo TCV                            \n";
    $stSql .= "                    ON TCV.cod_item = fi.cod_item                                    \n";
    $stSql .= "                  LEFT JOIN tcmgo.combustivel TC                                     \n";
    $stSql .= "                    ON TC.cod_combustivel = TCV.cod_combustivel                      \n";
    $stSql .= "                   AND TC.cod_tipo        = TCV.cod_tipo                             \n";
    $stSql .= "                 INNER JOIN almoxarifado.natureza AN                                 \n";
    $stSql .= "                    ON AN.cod_natureza  = alm.cod_natureza                           \n";
    $stSql .= "                   AND AN.tipo_natureza = alm.tipo_natureza                          \n";
    $stSql .= "                 INNER JOIN almoxarifado.natureza_lancamento as ANL                  \n";
    $stSql .= "                    ON ALM.exercicio_lancamento = ANL.exercicio_lancamento           \n";
    $stSql .= "                   AND ALM.num_lancamento       = ANL.num_lancamento                 \n";
    $stSql .= "                   AND ALM.cod_natureza     = ANL.cod_natureza                       \n";
    $stSql .= "                   AND ALM.tipo_natureza    = ANL.tipo_natureza                      \n";
    $stSql .= "              WHERE an.cod_natureza = 3 and an.tipo_natureza = 'S'                   \n";
    $stSql .= "              GROUP BY alm.tipo_natureza,                                            \n";
    $stSql .= "               an.cod_natureza, an.descricao, tc.descricao                           \n";
    $stSql .= "      ) as temp3                                                                     \n";
    $stSql .= "         ON temp3.tipo_natureza  = alm.tipo_natureza                                 \n";
    $stSql .= "        AND temp3.cod_natureza   = an.cod_natureza                                   \n";
    $stSql .= "        AND temp3.descricao      = an.descricao                                      \n";
    $stSql .= "        AND temp3.combustivel    = TC.descricao                                      \n";
    $stSql .= "     LEFT JOIN (                                                                     \n";
    $stSql .= "              SELECT                                                                 \n";
    $stSql .= "                  alm.tipo_natureza                                                  \n";
    $stSql .= "                  , sum(alm.quantidade) as qtdSaidaCons                              \n";
    $stSql .= "                  , an.cod_natureza                                                  \n";
    $stSql .= "                  , an.descricao                                                     \n";
    $stSql .= "                  , TC.descricao as combustivel                                      \n";
    $stSql .= "                FROM almoxarifado.lancamento_material alm                            \n";
    $stSql .= "                INNER JOIN almoxarifado.catalogo_item aci                            \n";
    $stSql .= "                   ON aci.cod_item = alm.cod_item                                    \n";
    $stSql .= "                INNER JOIN frota.item fi                                             \n";
    $stSql .= "                   ON aci.cod_item  = fi.cod_item                                    \n";
    $stSql .= "                  AND fi.cod_tipo IN (1,3)                                           \n";
    $stSql .= "                INNER JOIN tcmgo.combustivel_vinculo TCV                             \n";
    $stSql .= "                   ON TCV.cod_item = fi.cod_item                                     \n";
    $stSql .= "                 LEFT JOIN tcmgo.combustivel TC                                      \n";
    $stSql .= "                   ON TC.cod_combustivel = TCV.cod_combustivel                       \n";
    $stSql .= "                  AND TC.cod_tipo        = TCV.cod_tipo                              \n";
    $stSql .= "                INNER JOIN almoxarifado.natureza AN                                  \n";
    $stSql .= "                   ON AN.cod_natureza  = alm.cod_natureza                            \n";
    $stSql .= "                  AND AN.tipo_natureza = alm.tipo_natureza                           \n";
    $stSql .= "                INNER JOIN almoxarifado.natureza_lancamento as ANL                   \n";
    $stSql .= "                   ON ALM.exercicio_lancamento = ANL.exercicio_lancamento            \n";
    $stSql .= "                  AND ALM.num_lancamento       = ANL.num_lancamento                  \n";
    $stSql .= "                  AND ALM.cod_natureza     = ANL.cod_natureza                        \n";
    $stSql .= "                  AND ALM.tipo_natureza    = ANL.tipo_natureza                       \n";
    $stSql .= "             WHERE an.cod_natureza = 11 and an.tipo_natureza = 'S'                   \n";
    $stSql .= "             GROUP BY alm.tipo_natureza,                                             \n";
    $stSql .= "              an.cod_natureza, an.descricao, tc.descricao                            \n";
    $stSql .= "     ) as temp4                                                                      \n";
    $stSql .= "        ON temp4.tipo_natureza  = alm.tipo_natureza                                  \n";
    $stSql .= "       AND temp4.cod_natureza   = an.cod_natureza                                    \n";
    $stSql .= "       AND temp4.descricao      = an.descricao                                       \n";
    $stSql .= "       AND temp4.combustivel    = TC.descricao                                       \n";

    return $stSql;
}
}
?>
