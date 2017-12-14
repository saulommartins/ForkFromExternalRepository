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
    * Classe de mapeamento da tabela tcmgo.contrato
    * Data de Criação   : 03/09/2008

    * @author Analista      Tonismar Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGOContrato extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCMGOContrato()
{
    parent::Persistente();
    $this->setTabela("tcmgo.contrato");

    $this->setCampoCod('cod_contrato');
    $this->setComplementoChave('exercicio,cod_entidade');

    $this->AddCampo( 'cod_contrato'          , 'integer'    , true  , ''    , true  , true  );
    $this->AddCampo( 'exercicio'             , 'char'       , true  , '4'   , true  , true  );
    $this->AddCampo( 'cod_entidade'          , 'integer'    , true  , ''    , true  , true  );
    $this->AddCampo( 'nro_contrato'          , 'integer'    , true  , ''    , false , false );
    $this->AddCampo( 'cod_assunto'           , 'integer'    , true  , ''    , false , true  );
    $this->AddCampo( 'cod_tipo'              , 'integer'    , true  , ''    , false , true  );
    $this->AddCampo( 'numero_termo'          , 'char   '    , true , '4'   , false , false  );
    $this->AddCampo( 'cod_modalidade'        , 'integer'    , true  , ''    , false , true  );
    $this->AddCampo( 'vl_contrato'           , 'numeric'    , true  , '14,2', false , false );
    $this->AddCampo( 'objeto_contrato'       , 'char'       , true  , '200' , false , false );
    $this->AddCampo( 'data_inicio'           , 'date'       , true  , ''    , false , false );
    $this->AddCampo( 'data_final'            , 'date'       , true  , ''    , false , false );
    $this->AddCampo( 'data_publicacao'       , 'date'       , true  , ''    , false , false );
    $this->AddCampo( 'nro_processo'          , 'numeric'    , false , '5,0' , false , false );
    $this->AddCampo( 'ano_processo'          , 'char'       , false , '4'   , false , false );
    $this->AddCampo( 'cod_sub_assunto'       , 'integer'    , false , ''    , false , false );
    $this->AddCampo( 'detalhamentosubassunto', 'char'       , false , '200' , false , false );
    $this->AddCampo( 'dt_firmatura'          , 'date'       , false , ''    , false , false );
    $this->AddCampo( 'dt_lancamento'         , 'date'       , false , ''    , false , false );
    $this->AddCampo( 'vl_acrescimo'          , 'numeric'    , false , '12,2', false , false );
    $this->AddCampo( 'vl_decrescimo'         , 'numeric'    , false , '12,2', false , false );
    $this->AddCampo( 'vl_contratual'         , 'numeric'    , false , '12,2', false , false );
    $this->AddCampo( 'dt_rescisao'           , 'date'       , false , ''    , false , false );
    $this->AddCampo( 'vl_final_contrato'     , 'numeric'    , false , '12,2', false , false );
    $this->addCampo( 'prazo'                 , 'integer'    , false , ''   , false , false );
}

function recuperaProximoContrato(&$rsRecordSet)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaProximoContrato();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

    return $obErro;
}

function montaRecuperaProximoContrato()
{
    $stSql  = " SELECT max(cod_contrato) + 1 as cod_contrato    \n";
    $stSql .= "   FROM tcmgo.contrato                           \n";

    return $stSql;

}

function recuperaTodosDespesa(&$rsRecordSet, $stFiltro)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaTodosDespesa().$stFiltro;
    $stSql .= "GROUP BY ce.cod_contrato                   \n";
    $stSql .= "       , ce.exercicio                      \n";
    $stSql .= "       , c.exercicio                       \n";
    $stSql .= "       , od.num_orgao                      \n";
    $stSql .= "       , ce.cod_empenho                    \n";
    $stSql .= "       , ee.dt_empenho                     \n";
    $stSql .= "       , nro_contrato                      \n";
    $stSql .= "       , cod_assunto                       \n";
    $stSql .= "       , c.cod_tipo                        \n";
    $stSql .= "       , c.nro_processo                    \n";
    $stSql .= "       , c.ano_processo                    \n";
    $stSql .= "       , c.cod_modalidade                  \n";
    $stSql .= "       , vl_contrato                       \n";
    $stSql .= "       , objeto_contrato                   \n";
    $stSql .= "       , data_inicio                       \n";
    $stSql .= "       , data_final                        \n";
    $stSql .= "       , data_publicacao                   \n";
    $stSql .= "       , dt_firmatura                      \n";
    $stSql .= "       , od.cod_programa                   \n";
    $stSql .= "       , od.num_orgao                      \n";
    $stSql .= "       , od.num_unidade                    \n";
    $stSql .= "       , od.cod_funcao                     \n";
    $stSql .= "       , od.cod_subfuncao                  \n";
    $stSql .= "       , od.num_pao                        \n";
    $stSql .= "       , tdp.elemento                      \n";
    $stSql .= "       , tdp.subelemento                   \n";
    $stSql .= "       , ocd.elemento                      \n";
//    $stSql .= "       , ocd.subelemento                   \n";
    $stSql .= "       , ped.cod_conta                     \n";
    $stSql .= "       , ped.cod_pre_empenho               \n";
    $stSql .= "       , sc.nom_cgm                        \n";
    $stSql .= "       , sc.tipo_pessoa                    \n";
    $stSql .= "       , sc.cpf_cnpj                       \n";
    if (Sessao::getExercicio() > 2012) {
        $stSql .= "       , empenho_modalidade.cod_fundamentacao \n";
        $stSql .= "       , empenho_modalidade.justificativa     \n";
        $stSql .= "       , empenho_modalidade.razao_escolha     \n";
        $stSql .= "       , processos.numero_processo            \n";
        $stSql .= "       , processos.exercicio_processo         \n";
        $stSql .= "       , processos.processo_administrativo    \n";
        $stSql .= "       , c.cod_assunto                        \n";
    }

    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

    return $obErro;
}

function montaRecuperaTodosDespesa()
{
    $stSql  = "         SELECT ce.cod_contrato                                   \n";
    $stSql .= "              , ce.exercicio                                      \n";
    $stSql .= "              , od.cod_programa                                   \n";
    $stSql .= "              , od.num_orgao                                      \n";
    $stSql .= "              , od.num_unidade                                    \n";
    $stSql .= "              , od.cod_funcao                                     \n";
    $stSql .= "              , od.cod_subfuncao                                  \n";
    $stSql .= "              , substr(TO_CHAR(od.num_pao, '9999'), 3, 3) as nro_proj_ativ\n";
    $stSql .= "              , substr(TO_CHAR(od.num_pao, '9999'), 2, 1) as natureza_acao\n";
    $stSql .= "              , CASE                                              \n";
    $stSql .= "                    WHEN tdp.elemento IS NULL                     \n";
    $stSql .= "                    THEN ocd.elemento                             \n";
    $stSql .= "                    ELSE tdp.elemento                             \n";
    $stSql .= "                END as elemento                                   \n";
    $stSql .= "              , CASE                                              \n";
    $stSql .= "                  WHEN tdp.subelemento IS NULL                    \n";
    $stSql .= "                  THEN '0'                                        \n";
    $stSql .= "                  ELSE tdp.subelemento                            \n";
    $stSql .= "                END as subelemento                                \n";
    $stSql .= "              , ce.cod_empenho                                    \n";
    $stSql .= "              , TO_CHAR(ee.dt_empenho,'ddmmyyyy') AS dt_empenho   \n";
    if ((Sessao::getExercicio() > '2008') and (Sessao::getExercicio() < '2011' )) {
        $stSql .= "              , LPAD(nro_contrato::varchar,8,'0') AS nro_contrato           \n";
    } else {
    $stSql .= "              , LPAD(nro_contrato::varchar,20,'0') AS nro_contrato           \n";
    }
    $stSql .= "              , c.exercicio as ano_contrato                       \n";
    $stSql .= "              , '1' as tipo_ajuste                                \n";
    $stSql .= "              , cod_assunto                                       \n";
    $stSql .= "              , c.cod_tipo                                        \n";
    $stSql .= "              , c.cod_modalidade                                  \n";
    if (Sessao::getExercicio() > 2012) {
        $stSql .= "              , CASE WHEN c.cod_modalidade = 10 OR c.cod_modalidade = 11 THEN \n";
        $stSql .= "                    cod_fundamentacao::integer                    \n";
        $stSql .= "                ELSE                                              \n";
        $stSql .= "                    0                                             \n";
        $stSql .= "                END AS fundamentacao_legal                        \n";
        $stSql .= "              , CASE WHEN c.cod_modalidade = 10 OR c.cod_modalidade = 11 THEN \n";
        $stSql .= "                    TRIM(regexp_replace(empenho_modalidade.justificativa ,E'\\r\\n',''))                                 \n";
        $stSql .= "                ELSE                                              \n";
        $stSql .= "                    ''                                            \n";
        $stSql .= "                END AS justificativa_dispensa                     \n";
        $stSql .= "              , CASE WHEN c.cod_modalidade = 10 OR c.cod_modalidade = 11 THEN \n";
        $stSql .= "                    TRIM(regexp_replace(empenho_modalidade.razao_escolha ,E'\\r\\n',''))                                 \n";
        $stSql .= "                ELSE                                              \n";
        $stSql .= "                    ''                                            \n";
        $stSql .= "                END AS razao_escolha                              \n";
        $stSql .= "              , CASE WHEN c.cod_modalidade = 10 OR c.cod_modalidade = 11 THEN \n";
        $stSql .= "                    processos.numero_processo                     \n";
        $stSql .= "                ELSE                                              \n";
        $stSql .= "                    '0'                                           \n";
        $stSql .= "                END AS nro_processo                               \n";
        $stSql .= "              , CASE WHEN c.cod_modalidade = 10 OR c.cod_modalidade = 11 THEN \n";
        $stSql .= "                    processos.exercicio_processo                  \n";
        $stSql .= "                ELSE                                              \n";
        $stSql .= "                    '0'                                           \n";
        $stSql .= "                END AS ano_processo                               \n";
        $stSql .= "              , 0 AS instrumento_contrato                         \n";
        $stSql .= "              , processos.processo_administrativo                 \n";
        $stSql .= "              , c.cod_assunto                                     \n";
    }
    $stSql .= "              , vl_contrato                                       \n";
    $stSql .= "              , objeto_contrato                                   \n";
    $stSql .= "              , COALESCE(TO_CHAR(data_inicio,'dd/mm/yyyy'),'00/00/0000') AS data_inicio  \n";
    $stSql .= "              , COALESCE(TO_CHAR(data_final,'dd/mm/yyyy'),'00/00/0000') AS data_final    \n";
    $stSql .= "              , COALESCE(TO_CHAR(data_publicacao,'dd/mm/yyyy'),'00/00/0000') AS data_publicacao  \n";
    $stSql .= "              , COALESCE(TO_CHAR(dt_firmatura,'dd/mm/yyyy'),'00/00/0000') AS dt_firmatura  \n";
    $stSql .= "              , sc.nom_cgm as nome_credor                         \n";
    $stSql .= "              , sc.tipo_pessoa                                    \n";
    $stSql .= "              , LPAD(sc.cpf_cnpj::varchar,14,'0') AS cpf_cnpj              \n";
    $stSql .= "              , 10 as tipo_registro                               \n";
    $stSql .= "              , 0 as nro_sequencial                               \n";
    $stSql .= "              , c.nro_processo                                    \n";
    $stSql .= "              , c.ano_processo                                    \n";
    $stSql .= "           FROM tcmgo.contrato c                                  \n";
    $stSql .= "     INNER JOIN tcmgo.contrato_empenho ce                         \n";
    $stSql .= "             ON c.cod_contrato        = ce.cod_contrato           \n";
    $stSql .= "            AND ce.exercicio_empenho  = c.exercicio               \n";
    $stSql .= "            AND c.cod_entidade        = ce.cod_entidade           \n";
    $stSql .= "     INNER JOIN empenho.empenho ee                                \n";
    $stSql .= "             ON ee.cod_empenho        = ce.cod_empenho            \n";
    $stSql .= "            AND ee.exercicio          = c.exercicio               \n";
    $stSql .= "            AND ee.cod_entidade       = ce.cod_entidade           \n";
    $stSql .= "     INNER JOIN empenho.pre_empenho_despesa ped                   \n";
    $stSql .= "             ON ped.cod_pre_empenho   = ee.cod_pre_empenho        \n";
    $stSql .= "            AND ee.exercicio=c.exercicio                          \n";
    $stSql .= "     INNER JOIN orcamento.despesa od                              \n";
    $stSql .= "             ON od.cod_despesa=ped.cod_despesa                    \n";
    $stSql .= "            AND od.exercicio=c.exercicio                          \n";
    $stSql .= "     INNER JOIN orcamento.pao op                                  \n";
    $stSql .= "             ON op.num_pao=od.num_pao                             \n";
    $stSql .= "            AND op.exercicio=c.exercicio                          \n";
    if (Sessao::getExercicio() > 2012) {
        $stSql .= "      LEFT JOIN tcmgo.empenho_modalidade                          \n";
        $stSql .= "             ON ee.cod_empenho        = empenho_modalidade.cod_empenho \n";
        $stSql .= "            AND ee.exercicio          = empenho_modalidade.exercicio \n";
        $stSql .= "            AND ee.cod_entidade       = empenho_modalidade.cod_entidade \n";
        $stSql .= "      LEFT JOIN tcmgo.processos                                   \n";
        $stSql .= "             ON ee.cod_empenho        = processos.cod_empenho \n";
        $stSql .= "            AND ee.exercicio          = processos.exercicio \n";
        $stSql .= "            AND ee.cod_entidade       = processos.cod_entidade \n";
    }
    $stSql .= "LEFT OUTER JOIN ( SELECT *                                        \n";
    $stSql .= "                       , substr(translate(estrutural, '.', ''), 1, 6) as elemento \n";
    $stSql .= "		                  , substr(translate(estrutural, '.', ''), 7, 2) as subelemento \n";
    $stSql .= "	                 FROM tcmgo.elemento_de_para                     \n";
    $stSql .= "                ) tdp                                             \n";
    $stSql .= "             ON tdp.cod_conta         = ped.cod_conta             \n";
    $stSql .= "            AND tdp.exercicio         = c.exercicio               \n";
    $stSql .= "           JOIN ( SELECT *                                        \n";
    $stSql .= "                       , substr(translate(cod_estrutural, '.', ''), 1, 6) as elemento \n";
    $stSql .= "           FROM orcamento.conta_despesa) ocd                      \n";
    $stSql .= "             ON ocd.cod_conta         = ped.cod_conta             \n";
    $stSql .= "            AND ocd.exercicio         = c.exercicio               \n";
    $stSql .= "     INNER JOIN empenho.pre_empenho epe                           \n";
    $stSql .= "             ON epe.cod_pre_empenho   = ped.cod_pre_empenho       \n";
    $stSql .= "            AND epe.exercicio         = ped.exercicio             \n";
    $stSql .= "     INNER JOIN (          SELECT sc.numcgm                       \n";
    $stSql .= "                                , nom_cgm                         \n";
    $stSql .= "                                , cpf as cpf_cnpj                 \n";
    if (Sessao::getExercicio() > 2012) {
        $stSql .= "                                , CASE WHEN sc.cod_pais = 1 THEN  \n";
        $stSql .= "                                    1                             \n";
        $stSql .= "                                  ELSE                            \n";
        $stSql .= "                                    3                             \n";
        $stSql .= "                                  END as tipo_pessoa              \n";
    } else {
        $stSql .= "                                , 1 as tipo_pessoa                \n";
    }
    $stSql .= "                                      FROM sw_cgm sc              \n";
    $stSql .= "                  LEFT OUTER JOIN sw_cgm_pessoa_fisica scpf       \n";
    $stSql .= "                               ON sc.numcgm = scpf.numcgm         \n";
    $stSql .= "                            WHERE cpf IS NOT NULL                 \n";
    $stSql .= "	                           UNION                                 \n";
    $stSql .= "                           SELECT sc.numcgm                       \n";
    $stSql .= "                                , nom_cgm                         \n";
    $stSql .= "                                , cnpj as cpf_cnpj                \n";
    if (Sessao::getExercicio() > 2012) {
        $stSql .= "                                , CASE WHEN sc.cod_pais = 1 THEN  \n";
        $stSql .= "                                    2                             \n";
        $stSql .= "                                  ELSE                            \n";
        $stSql .= "                                    3                             \n";
        $stSql .= "                                  END as tipo_pessoa              \n";
    } else {
        $stSql .= "                                , 2 as tipo_pessoa                \n";
    }
    $stSql .= "                             FROM sw_cgm sc                       \n";
    $stSql .= "                  LEFT OUTER JOIN sw_cgm_pessoa_juridica scpj     \n";
    $stSql .= "                               ON sc.numcgm = scpj.numcgm         \n";
    $stSql .= "                            WHERE cnpj IS NOT NULL                \n";
    $stSql .= "                ) sc                                              \n";
    $stSql .= "             ON sc.numcgm = epe.cgm_beneficiario                  \n";

    return $stSql;

}

function recuperaDetalhamentoContrato(&$rsRecordSet, $stFiltro)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDetalhamentoContrato().$stFiltro;
    $stSql.= " GROUP BY ce.cod_contrato           \n";
    $stSql .= "       , ce.exercicio              \n";
    $stSql .= "       , c.exercicio               \n";
    $stSql .= "       , od.num_orgao              \n";
    $stSql .= "       , ce.cod_empenho            \n";
    $stSql .= "       , ee.dt_empenho             \n";
    $stSql .= "       , nro_contrato              \n";
    $stSql .= "       , cod_assunto               \n";
    $stSql .= "       , c.cod_tipo                \n";
    $stSql .= "       , c.nro_processo            \n";
    $stSql .= "       , c.ano_processo            \n";
    $stSql .= "       , cod_modalidade            \n";
    $stSql .= "       , vl_contrato               \n";
    $stSql .= "       , objeto_contrato           \n";
    $stSql .= "       , data_inicio               \n";
    $stSql .= "       , data_final                \n";
    $stSql .= "       , data_publicacao           \n";
    $stSql .= "       , dt_firmatura              \n";
    $stSql .= "       , od.cod_programa           \n";
    $stSql .= "       , od.num_orgao              \n";
    $stSql .= "       , od.num_unidade            \n";
    $stSql .= "       , od.cod_funcao             \n";
    $stSql .= "       , od.cod_subfuncao          \n";
    $stSql .= "       , od.num_pao                \n";
    $stSql .= "       , tdp.elemento              \n";
    $stSql .= "       , tdp.subelemento           \n";
    $stSql .= "       , ocd.elemento              \n";
    $stSql .= "       , ped.cod_conta             \n";
    $stSql .= "       , ped.cod_pre_empenho       \n";
    $stSql .= "       , sc.nom_cgm                \n";
    $stSql .= "       , sc.tipo_pessoa            \n";
    $stSql .= "       , sc.cpf_cnpj               \n";
    $stSql .= "    , endereco                     \n";
    $stSql .= "    , latitude                     \n";
    $stSql .= "    , longitude                    \n";
    $stSql .= "    , bairro                       \n";
    $stSql .= "    , cod_sub_assunto              \n";
    $stSql .= "    , detalhamentosubassunto       \n";

    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

    return $obErro;
}

function montaRecuperaDetalhamentoContrato()
{
    $stSql  = " SELECT ce.cod_contrato                                  \n";
    $stSql .= "     , ce.exercicio                                      \n";
    $stSql .= "     , od.cod_programa                                   \n";
    $stSql .= "     , od.num_orgao                                      \n";
    $stSql .= "     , od.num_unidade                                    \n";
    $stSql .= "     , od.cod_funcao                                     \n";
    $stSql .= "     , od.cod_subfuncao                                  \n";
    $stSql .= "     , 11 as tipo_registro                               \n";
    $stSql .= "     , substr(TO_CHAR(od.num_pao, '9999'), 3, 3) as nro_proj_ativ   \n";
    $stSql .= "     , substr(TO_CHAR(od.num_pao, '9999'), 2, 1) as natureza_acao   \n";
    $stSql .= "     , CASE                                             \n";
    $stSql .= "          WHEN tdp.elemento IS NULL                     \n";
    $stSql .= "               THEN ocd.elemento                        \n";
    $stSql .= "          ELSE tdp.elemento                             \n";
    $stSql .= "        END as elemento                                 \n";
    $stSql .= "      , CASE                                            \n";
    $stSql .= "           WHEN tdp.subelemento IS NULL                 \n";
    $stSql .= "                THEN '0'                                \n";
    $stSql .= "           ELSE tdp.subelemento                         \n";
    $stSql .= "         END as subelemento                             \n";
    $stSql .= "      , ce.cod_empenho                                  \n";
    $stSql .= "      , ee.dt_empenho                                   \n";
    $stSql .= "      , LPAD(nro_contrato||c.exercicio,8,'0') AS nro_contrato  \n";
    $stSql .= "      , cod_assunto                                      \n";
    $stSql .= "      , c.cod_tipo                                       \n";
    $stSql .= "      , cod_modalidade                                   \n";
    $stSql .= "      , vl_contrato                                      \n";
    $stSql .= "      , objeto_contrato                                  \n";
    $stSql .= "      , COALESCE(TO_CHAR(data_inicio,'dd/mm/yyyy'),'00/00/0000') AS data_inicio \n";
    $stSql .= "      , COALESCE(TO_CHAR(data_final,'dd/mm/yyyy'),'00/00/0000') AS data_final   \n";
    $stSql .= "      , COALESCE(TO_CHAR(data_publicacao,'dd/mm/yyyy'),'00/00/0000') AS data_publicacao \n";
    $stSql .= "      , COALESCE(TO_CHAR(dt_firmatura,'dd/mm/yyyy'),'00/00/0000') AS dt_firmatura  \n";
    $stSql .= "      , sc.nom_cgm as nome_credor                        \n";
    $stSql .= "      , sc.tipo_pessoa                                   \n";
    $stSql .= "      , LPAD(sc.cpf_cnpj,14,'0') AS cpf_cnpj             \n";
    $stSql .= "      , 0 as nro_sequencial                              \n";
    $stSql .= "      , c.nro_processo                                   \n";
    $stSql .= "      , c.ano_processo                                   \n";
    $stSql .= "      , c.cod_sub_assunto                                \n";
    $stSql .= "      , c.detalhamentoSubAssunto                         \n";
    $stSql .= "      , tobra.endereco                                   \n";
    $stSql .= "      , replace((to_char(grau_latitude, '09')||to_char(minuto_latitude, '09')||to_char(segundo_latitude, '0999')),' ','')  as
latitude \n";
    $stSql .= "      , replace((to_char(grau_longitude, '09')||to_char(minuto_longitude, '09')||to_char(segundo_longitude, '0999')),' ','')  as
longitude \n";
    $stSql .= "      , tobra.bairro                                         \n";
    $stSql .= "           FROM tcmgo.contrato c                                \n";
    $stSql .= "     INNER JOIN tcmgo.contrato_empenho ce                       \n";
    $stSql .= "             ON c.cod_contrato        = ce.cod_contrato         \n";
    $stSql .= "            AND ce.exercicio_empenho  = c.exercicio             \n";
    $stSql .= "            AND c.cod_entidade        = ce.cod_entidade         \n";
    $stSql .= "     INNER JOIN empenho.empenho ee                              \n";
    $stSql .= "             ON ee.cod_empenho        = ce.cod_empenho          \n";
    $stSql .= "            AND ee.exercicio          = c.exercicio             \n";
    $stSql .= "            AND ee.cod_entidade       = ce.cod_entidade         \n";
    $stSql .= "      LEFT JOIN tcmgo.obra_empenho toe                          \n";
    $stSql .= "             ON toe.cod_empenho        = ee.cod_empenho         \n";
    $stSql .= "            AND toe.exercicio          = ee.exercicio           \n";
    $stSql .= "            AND toe.cod_entidade       = ee.cod_entidade        \n";
    $stSql .= "  LEFT JOIN tcmgo.obra tobra                                    \n";
    $stSql .= "     ON tobra.cod_obra  = toe.cod_obra                          \n";
    $stSql .= "    AND tobra.ano_obra  = toe.ano_obra                          \n";
    $stSql .= "     INNER JOIN empenho.pre_empenho_despesa ped                 \n";
    $stSql .= "             ON ped.cod_pre_empenho   = ee.cod_pre_empenho      \n";
    $stSql .= "            AND ee.exercicio=c.exercicio                        \n";
    $stSql .= "     INNER JOIN orcamento.despesa od                            \n";
    $stSql .= "             ON od.cod_despesa=ped.cod_despesa                  \n";
    $stSql .= "            AND od.exercicio=c.exercicio                        \n";
    $stSql .= "     INNER JOIN orcamento.pao op                                \n";
    $stSql .= "             ON op.num_pao=od.num_pao                           \n";
    $stSql .= "            AND op.exercicio=c.exercicio                        \n";
    $stSql .= "LEFT OUTER JOIN ( SELECT *                                      \n";
    $stSql .= "                       , substr(translate(estrutural, '.', ''), 1, 6) as elemento   \n";
    $stSql .= "                       , substr(translate(estrutural, '.', ''), 7, 2) as subelemento  \n";
    $stSql .= "                  FROM tcmgo.elemento_de_para                   \n";
    $stSql .= "                ) tdp                                           \n";
    $stSql .= "             ON tdp.cod_conta         = ped.cod_conta           \n";
    $stSql .= "            AND tdp.exercicio         = c.exercicio             \n";
    $stSql .= "           JOIN ( SELECT *                                      \n";
    $stSql .= "                       , substr(translate(cod_estrutural, '.', ''), 1, 6) as elemento  \n";
    $stSql .= "           FROM orcamento.conta_despesa) ocd                    \n";
    $stSql .= "             ON ocd.cod_conta         = ped.cod_conta           \n";
    $stSql .= "            AND ocd.exercicio         = c.exercicio             \n";
    $stSql .= "     INNER JOIN empenho.pre_empenho epe                         \n";
    $stSql .= "             ON epe.cod_pre_empenho   = ped.cod_pre_empenho     \n";
    $stSql .= "            AND epe.exercicio         = ped.exercicio           \n";
    $stSql .= "     INNER JOIN (          SELECT sc.numcgm                     \n";
    $stSql .= "                                , nom_cgm                       \n";
    $stSql .= "                                , cpf as cpf_cnpj               \n";
    $stSql .= "                              , 1 as tipo_pessoa                \n";
    $stSql .= "                                      FROM sw_cgm sc            \n";
    $stSql .= "                  LEFT OUTER JOIN sw_cgm_pessoa_fisica scpf     \n";
    $stSql .= "                               ON sc.numcgm = scpf.numcgm       \n";
    $stSql .= "                            WHERE cpf IS NOT NULL               \n";
    $stSql .= "                            UNION                               \n";
    $stSql .= "                           SELECT sc.numcgm                     \n";
    $stSql .= "                                , nom_cgm                       \n";
    $stSql .= "                                , cnpj as cpf_cnpj              \n";
    $stSql .= "                                , 2 as tipo_pessoa              \n";
    $stSql .= "                             FROM sw_cgm sc                     \n";
    $stSql .= "                  LEFT OUTER JOIN sw_cgm_pessoa_juridica scpj   \n";
    $stSql .= "                               ON sc.numcgm = scpj.numcgm       \n";
    $stSql .= "                            WHERE cnpj IS NOT NULL              \n";
    $stSql .= "                ) sc                                            \n";
    $stSql .= "             ON sc.numcgm = epe.cgm_beneficiario                \n";

    return $stSql;
}

function recuperaProrrogacaoPrazo(&$rsRecordSet, $stFiltro)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaProrrogacaoPrazo().$stFiltro;
    $stSql .= "GROUP BY ce.cod_contrato                   \n";
    $stSql .= "       , ce.exercicio                      \n";
    $stSql .= "       , od.cod_programa                   \n";
    $stSql .= "       , od.num_orgao                      \n";
    $stSql .= "       , od.num_unidade                    \n";
    $stSql .= "       , od.cod_funcao                     \n";
    $stSql .= "       , od.cod_subfuncao                  \n";
    $stSql .= "       , c.nro_contrato                    \n";
    $stSql .= "       , c.exercicio                       \n";
    $stSql .= "       , nro_proj_ativ                     \n";
    $stSql .= "       , natureza_acao                     \n";
    $stSql .= "       , tdp.elemento                      \n";
    $stSql .= "       , tdp.subelemento                   \n";
    $stSql .= "       , c.dt_lancamento                   \n";
    $stSql .= "       , c.prazo                           \n";
    $stSql .= "       , ocd.elemento                      \n";
    $stSql .= "       , ce.cod_empenho                    \n";
    $stSql .= "       , cod_tipo                          \n";
    $stSql .= "       , dt_firmatura                      \n";
    $stSql .= "       , numero_termo                      \n";
    $stSql .= "       , nro_sequencial                    \n";

    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

    return $obErro;
}

function montaRecuperaProrrogacaoPrazo()
{
    $stSql  = "         SELECT ce.cod_contrato                                   \n";
    $stSql .= "              , ce.exercicio                                      \n";
    $stSql .= "              , od.cod_programa                                   \n";
    $stSql .= "              , od.num_orgao                                      \n";
    $stSql .= "              , od.num_unidade                                    \n";
    $stSql .= "              , 21 as tipo_registro                               \n";
    $stSql .= "              , od.cod_funcao                                     \n";
    $stSql .= "              , od.cod_subfuncao                                  \n";
    $stSql .= "              , LPAD(nro_contrato::varchar,20,'0') AS nro_contrato           \n";
    $stSql .= "              , c.exercicio as ano_contrato                        \n";
    $stSql .= "              , '1' as tipo_ajuste                                \n";
    $stSql .= "              , substr(TO_CHAR(od.num_pao, '9999'), 3, 3) as nro_proj_ativ\n";
    $stSql .= "              , substr(TO_CHAR(od.num_pao, '9999'), 2, 1) as natureza_acao\n";
    $stSql .= "              , CASE                                              \n";
    $stSql .= "                    WHEN tdp.elemento IS NULL                     \n";
    $stSql .= "                    THEN ocd.elemento                             \n";
    $stSql .= "                    ELSE tdp.elemento                             \n";
    $stSql .= "                END as elemento                                   \n";
    $stSql .= "              , CASE                                              \n";
    $stSql .= "                  WHEN tdp.subelemento IS NULL                    \n";
    $stSql .= "                  THEN '0'                                        \n";
    $stSql .= "                  ELSE tdp.subelemento                            \n";
    $stSql .= "                END as subelemento                                \n";
    $stSql .= "              , ce.cod_empenho                                    \n";
    $stSql .= "              , c.cod_tipo                                        \n";
    $stSql .= "              , COALESCE(TO_CHAR(dt_firmatura,'dd/mm/yyyy'),'00/00/0000') AS dt_firmatura  \n";
    $stSql .= "              , c.numero_termo                                    \n";
    $stSql .= "              , c.prazo                                           \n";
    $stSql .= "              , 0 as nro_sequencial                               \n";
    $stSql .= "           FROM tcmgo.contrato c                                  \n";
    $stSql .= "     INNER JOIN tcmgo.contrato_empenho ce                         \n";
    $stSql .= "             ON c.cod_contrato        = ce.cod_contrato           \n";
    $stSql .= "            AND ce.exercicio_empenho  = c.exercicio               \n";
    $stSql .= "            AND c.cod_entidade        = ce.cod_entidade           \n";
    $stSql .= "     INNER JOIN empenho.empenho ee                                \n";
    $stSql .= "             ON ee.cod_empenho        = ce.cod_empenho            \n";
    $stSql .= "            AND ee.exercicio          = c.exercicio               \n";
    $stSql .= "            AND ee.cod_entidade       = ce.cod_entidade           \n";
    $stSql .= "     INNER JOIN empenho.pre_empenho_despesa ped                   \n";
    $stSql .= "             ON ped.cod_pre_empenho   = ee.cod_pre_empenho        \n";
    $stSql .= "            AND ee.exercicio=c.exercicio                          \n";
    $stSql .= "     INNER JOIN orcamento.despesa od                              \n";
    $stSql .= "             ON od.cod_despesa=ped.cod_despesa                    \n";
    $stSql .= "            AND od.exercicio=c.exercicio                          \n";
    $stSql .= "LEFT OUTER JOIN ( SELECT *                                        \n";
    $stSql .= "                       , substr(translate(estrutural, '.', ''), 1, 6) as elemento \n";
    $stSql .= "		                  , substr(translate(estrutural, '.', ''), 7, 2) as subelemento \n";
    $stSql .= "	                 FROM tcmgo.elemento_de_para                     \n";
    $stSql .= "                ) tdp                                             \n";
    $stSql .= "             ON tdp.cod_conta         = ped.cod_conta             \n";
    $stSql .= "            AND tdp.exercicio         = c.exercicio               \n";
    $stSql .= "           JOIN ( SELECT *                                        \n";
    $stSql .= "                       , substr(translate(cod_estrutural, '.', ''), 1, 6) as elemento \n";
    $stSql .= "           FROM orcamento.conta_despesa) ocd                      \n";
    $stSql .= "             ON ocd.cod_conta         = ped.cod_conta             \n";
    $stSql .= "            AND ocd.exercicio         = c.exercicio               \n";

    return $stSql;
}

function recuperaAcrescimoDecrescimo(&$rsRecordSet, $stFiltro)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaAcrescimoDecrescimo().$stFiltro;
    $stSql .= "GROUP BY ce.cod_contrato                   \n";
    $stSql .= "       , ce.exercicio                      \n";
    $stSql .= "       , od.cod_programa                   \n";
    $stSql .= "       , od.num_orgao                      \n";
    $stSql .= "       , od.num_unidade                    \n";
    $stSql .= "       , od.cod_funcao                     \n";
    $stSql .= "       , od.cod_subfuncao                  \n";
    $stSql .= "       , nro_proj_ativ                     \n";
    $stSql .= "       , natureza_acao                     \n";
    $stSql .= "       , tdp.elemento              \n";
    $stSql .= "       , tdp.subelemento           \n";
    $stSql .= "       , ocd.elemento              \n";
    $stSql .= "       , ce.cod_empenho                       \n";
    $stSql .= "       , cod_tipo                          \n";
    $stSql .= "       , c.dt_lancamento                   \n";
    $stSql .= "       , c.vl_acrescimo                    \n";
    $stSql .= "       , c.vl_decrescimo                   \n";
    $stSql .= "       , c.vl_contratual                   \n";
    $stSql .= "       , c.nro_contrato                    \n";
    $stSql .= "       , c.exercicio                       \n";
    $stSql .= "       , c.numero_termo                    \n";
    $stSql .= "       , nro_sequencial                    \n";

    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

    return $obErro;
}

function montaRecuperaAcrescimoDecrescimo()
{
    $stSql  = "         SELECT ce.cod_contrato                                   \n";
    $stSql .= "              , ce.exercicio                                      \n";
    $stSql .= "              , od.cod_programa                                   \n";
    $stSql .= "              , od.num_orgao                                      \n";
    $stSql .= "              , od.num_unidade                                    \n";
    $stSql .= "              , 22 as tipo_registro                               \n";
    $stSql .= "              , od.cod_funcao                                     \n";
    $stSql .= "              , od.cod_subfuncao                                  \n";
    $stSql .= "              , substr(TO_CHAR(od.num_pao, '9999'), 3, 3) as nro_proj_ativ\n";
    $stSql .= "              , substr(TO_CHAR(od.num_pao, '9999'), 2, 1) as natureza_acao\n";
    $stSql .= "              , CASE                                              \n";
    $stSql .= "                    WHEN tdp.elemento IS NULL                     \n";
    $stSql .= "                    THEN ocd.elemento                             \n";
    $stSql .= "                    ELSE tdp.elemento                             \n";
    $stSql .= "                END as elemento                                   \n";
    $stSql .= "              , CASE                                              \n";
    $stSql .= "                  WHEN tdp.subelemento IS NULL                    \n";
    $stSql .= "                  THEN '0'                                        \n";
    $stSql .= "                  ELSE tdp.subelemento                            \n";
    $stSql .= "                END as subelemento                                \n";
    $stSql .= "              , ce.cod_empenho                                    \n";
    $stSql .= "              , c.cod_tipo                                        \n";
    $stSql .= "              , TO_CHAR(dt_lancamento,'dd/mm/yyyy')  as dt_lancamento  \n";
    $stSql .= "              , c.vl_acrescimo                                    \n";
    $stSql .= "              , c.vl_decrescimo                                   \n";
    $stSql .= "              , c.vl_contratual                                   \n";
    $stSql .= "              , c.cod_tipo                                        \n";
    $stSql .= "              , LPAD(nro_contrato::varchar,20,'0') AS nro_contrato           \n";
    $stSql .= "              , c.exercicio as ano_contrato                        \n";
    $stSql .= "              , '1' as tipo_ajuste                                \n";
    $stSql .= "              , c.numero_termo                                    \n";
    $stSql .= "              , 0 as nro_sequencial                               \n";
    $stSql .= "           FROM tcmgo.contrato c                                  \n";
    $stSql .= "     INNER JOIN tcmgo.contrato_empenho ce                         \n";
    $stSql .= "             ON c.cod_contrato        = ce.cod_contrato           \n";
    $stSql .= "            AND ce.exercicio_empenho  = c.exercicio               \n";
    $stSql .= "            AND c.cod_entidade        = ce.cod_entidade           \n";
    $stSql .= "     INNER JOIN empenho.empenho ee                                \n";
    $stSql .= "             ON ee.cod_empenho        = ce.cod_empenho            \n";
    $stSql .= "            AND ee.exercicio          = c.exercicio               \n";
    $stSql .= "            AND ee.cod_entidade       = ce.cod_entidade           \n";
    $stSql .= "     INNER JOIN empenho.pre_empenho_despesa ped                   \n";
    $stSql .= "             ON ped.cod_pre_empenho   = ee.cod_pre_empenho        \n";
    $stSql .= "            AND ee.exercicio=c.exercicio                          \n";
    $stSql .= "     INNER JOIN orcamento.despesa od                              \n";
    $stSql .= "             ON od.cod_despesa=ped.cod_despesa                    \n";
    $stSql .= "            AND od.exercicio=c.exercicio                          \n";
    $stSql .= "LEFT OUTER JOIN ( SELECT *                                        \n";
    $stSql .= "                       , substr(translate(estrutural, '.', ''), 1, 6) as elemento \n";
    $stSql .= "		                  , substr(translate(estrutural, '.', ''), 7, 2) as subelemento \n";
    $stSql .= "	                 FROM tcmgo.elemento_de_para                     \n";
    $stSql .= "                ) tdp                                             \n";
    $stSql .= "             ON tdp.cod_conta         = ped.cod_conta             \n";
    $stSql .= "            AND tdp.exercicio         = c.exercicio               \n";
    $stSql .= "           JOIN ( SELECT *                                        \n";
    $stSql .= "                       , substr(translate(cod_estrutural, '.', ''), 1, 6) as elemento \n";
    $stSql .= "           FROM orcamento.conta_despesa) ocd                      \n";
    $stSql .= "             ON ocd.cod_conta         = ped.cod_conta             \n";
    $stSql .= "            AND ocd.exercicio         = c.exercicio               \n";

    return $stSql;
}

function recuperaRescisaoContratual(&$rsRecordSet, $stFiltro)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRescisaoContratual().$stFiltro;
    $stSql .= "GROUP BY ce.cod_contrato                   \n";
    $stSql .= "       , ce.exercicio                      \n";
    $stSql .= "       , od.cod_programa                   \n";
    $stSql .= "       , od.num_orgao                      \n";
    $stSql .= "       , od.num_unidade                    \n";
    $stSql .= "       , od.cod_funcao                     \n";
    $stSql .= "       , od.cod_subfuncao                  \n";
    $stSql .= "       , nro_proj_ativ                     \n";
    $stSql .= "       , natureza_acao                     \n";
    $stSql .= "       , tdp.elemento                      \n";
    $stSql .= "       , tdp.subelemento                   \n";
    $stSql .= "       , ocd.elemento                      \n";
    $stSql .= "       , ce.cod_empenho                    \n";
    $stSql .= "       , cod_tipo                          \n";
    $stSql .= "       , c.dt_lancamento                   \n";
    $stSql .= "       , c.vl_acrescimo                    \n";
    $stSql .= "       , c.vl_decrescimo                   \n";
    $stSql .= "       , c.dt_rescisao                     \n";
//    $stSql .= "       , c.dt_rescisao                     \n";
//    $stSql .= "       , c.vl_cancelamento                 \n";
    $stSql .= "       , c.vl_final_contrato               \n";
    $stSql .= "       , c.vl_contratual                   \n";
    $stSql .= "       , nro_sequencial                    \n";
    $stSql .= "       , eea.dt_cancelamento              \n";
    $stSql .= "       , eea.vl_total_anulado              \n";
    $stSql .= "       , c.nro_contrato                    \n";
    $stSql .= "       , c.exercicio                       \n";
    $stSql .= "       , c.numero_termo                    \n";
//    $stSql .= "       , eea.dt_cancelamento               \n";

    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

    return $obErro;
}

function montaRecuperaRescisaoContratual()
{
    $stSql  = "         SELECT ce.cod_contrato                                   \n";
    $stSql .= "              , ce.exercicio                                      \n";
    $stSql .= "              , od.cod_programa                                   \n";
    $stSql .= "              , od.num_orgao                                      \n";
    $stSql .= "              , od.num_unidade                                    \n";
    $stSql .= "              , od.cod_funcao                                     \n";
    $stSql .= "              , od.cod_subfuncao                                  \n";
    $stSql .= "              , 23 as tipo_registro                               \n";
    $stSql .= "              , substr(TO_CHAR(od.num_pao, '9999'), 3, 3) as nro_proj_ativ\n";
    $stSql .= "              , substr(TO_CHAR(od.num_pao, '9999'), 2, 1) as natureza_acao\n";
    $stSql .= "              , CASE                                              \n";
    $stSql .= "                    WHEN tdp.elemento IS NULL                     \n";
    $stSql .= "                    THEN ocd.elemento                             \n";
    $stSql .= "                    ELSE tdp.elemento                             \n";
    $stSql .= "                END as elemento                                   \n";
    $stSql .= "              , CASE                                              \n";
    $stSql .= "                  WHEN tdp.subelemento IS NULL                    \n";
    $stSql .= "                  THEN '0'                                        \n";
    $stSql .= "                  ELSE tdp.subelemento                            \n";
    $stSql .= "                END as subelemento                                \n";
    $stSql .= "              , ce.cod_empenho                                    \n";
    $stSql .= "              , c.cod_tipo                                        \n";
    $stSql .= "              , TO_CHAR(dt_rescisao,'dd/mm/yyyy') AS dt_rescisao      \n";
//    $stSql .= "              , c.dt_rescisao AS dt_cancelamento                                 \n";
//  $stSql .= "              , c.vl_cancelamento                                 \n";
    $stSql .= "              , c.vl_final_contrato                               \n";
    $stSql .= "              , c.cod_tipo                                        \n";
    $stSql .= "              , 0 as nro_sequencial                               \n";
    $stSql .= "              , eea.vl_total_anulado AS vl_cancelamento           \n";
    $stSql .= "              , LPAD(nro_contrato::varchar,20,'0') AS nro_contrato           \n";
    $stSql .= "              , c.exercicio as ano_contrato                       \n";
    $stSql .= "              , '1' as tipo_ajuste                                \n";
    $stSql .= "              , c.numero_termo                                    \n";
    $stSql .= "              ,TO_CHAR(dt_cancelamento,'dd/mm/yyyy') AS dt_cancelamento                 \n";
    $stSql .= "           FROM tcmgo.contrato c                                  \n";
    $stSql .= "     INNER JOIN tcmgo.contrato_empenho ce                         \n";
    $stSql .= "             ON c.cod_contrato        = ce.cod_contrato           \n";
    $stSql .= "            AND ce.exercicio_empenho  = c.exercicio               \n";
    $stSql .= "            AND c.cod_entidade        = ce.cod_entidade           \n";
    $stSql .= "     INNER JOIN empenho.empenho ee                                \n";
    $stSql .= "             ON ee.cod_empenho        = ce.cod_empenho            \n";
    $stSql .= "            AND ee.exercicio          = c.exercicio               \n";
    $stSql .= "            AND ee.cod_entidade       = ce.cod_entidade           \n";
    $stSql .= "     INNER JOIN empenho.pre_empenho_despesa ped                   \n";
    $stSql .= "             ON ped.cod_pre_empenho   = ee.cod_pre_empenho        \n";
    $stSql .= "            AND ee.exercicio=c.exercicio                          \n";
    $stSql .= "     INNER JOIN orcamento.despesa od                              \n";
    $stSql .= "             ON od.cod_despesa=ped.cod_despesa                    \n";
    $stSql .= "            AND od.exercicio=c.exercicio                          \n";
    $stSql .= "  LEFT JOIN ( SELECT sum( coalesce( EEAI.vl_anulado, 0.00 ) ) AS vl_total_anulado   \n";
    $stSql .= "                                 ,EEA.exercicio                                     \n";
    $stSql .= "                                 ,EEA.cod_entidade                                  \n";
    $stSql .= "                                 ,EEA.cod_empenho                                   \n";
    $stSql .= "                 ,EEA.timestamp as dt_cancelamento                                  \n";
    $stSql .= "                           FROM empenho.empenho_anulado AS EEA                      \n";
    $stSql .= "                               ,empenho.empenho_anulado_item AS EEAI                \n";
    $stSql .= "                           WHERE EEA.exercicio    = EEAI.exercicio                  \n";
    $stSql .= "                           AND   EEA.cod_entidade = EEAI.cod_entidade               \n";
    $stSql .= "                           AND   EEA.cod_empenho  = EEAI.cod_empenho                \n";
    $stSql .= "                           AND   EEA.timestamp    = EEAI.timestamp                  \n";
    $stSql .= "                           GROUP BY EEA.exercicio                                   \n";
    $stSql .= "                                   ,EEA.cod_entidade                                \n";
    $stSql .= "                                   ,EEA.cod_empenho                                 \n";
    $stSql .= "                   , eea.timestamp                                                  \n";
    $stSql .= "                           ORDER BY EEA.exercicio                                   \n";
    $stSql .= "                                   ,EEA.cod_entidade                                \n";
    $stSql .= "                                   ,EEA.cod_empenho                                 \n";
    $stSql .= "               ) AS EEA ON( EE.exercicio    = EEA.exercicio                         \n";
    $stSql .= "                        AND EE.cod_entidade = EEA.cod_entidade                      \n";
    $stSql .= "                        AND EE.cod_empenho  = EEA.cod_empenho   )                   \n";

    $stSql .= "LEFT OUTER JOIN ( SELECT *                                        \n";
    $stSql .= "                       , substr(translate(estrutural, '.', ''), 1, 6) as elemento \n";
    $stSql .= "		                  , substr(translate(estrutural, '.', ''), 7, 2) as subelemento \n";
    $stSql .= "	                 FROM tcmgo.elemento_de_para                     \n";
    $stSql .= "                ) tdp                                             \n";
    $stSql .= "             ON tdp.cod_conta         = ped.cod_conta             \n";
    $stSql .= "            AND tdp.exercicio         = c.exercicio               \n";
    $stSql .= "           JOIN ( SELECT *                                        \n";
    $stSql .= "                       , substr(translate(cod_estrutural, '.', ''), 1, 6) as elemento \n";
    $stSql .= "           FROM orcamento.conta_despesa) ocd                      \n";
    $stSql .= "             ON ocd.cod_conta         = ped.cod_conta             \n";
    $stSql .= "            AND ocd.exercicio         = c.exercicio               \n";

    return $stSql;
}
}
?>
