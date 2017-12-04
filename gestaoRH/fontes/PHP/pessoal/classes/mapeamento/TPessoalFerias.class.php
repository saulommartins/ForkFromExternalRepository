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
    * Classe de mapeamento da tabela pessoal.ferias
    * Data de Criação: 08/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TPessoalFerias.class.php 64319 2016-01-15 13:51:29Z michel $

    * Casos de uso: uc-04.04.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TPessoalFerias extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setTabela("pessoal.ferias");

    $this->setCampoCod('cod_ferias');
    $this->setComplementoChave('');

    $this->AddCampo('cod_ferias'            ,'sequence' ,true   ,''     ,true   ,false                                  );
    $this->AddCampo('cod_contrato'          ,'integer'  ,true   ,''     ,false  ,'TPessoalContrato'                     );
    $this->AddCampo('cod_forma'             ,'integer'  ,true   ,''     ,false  ,'TPessoalFormaPagamentoFerias'         );
    $this->AddCampo('faltas'                ,'integer'  ,true   ,''     ,false  ,false                                  );
    $this->AddCampo('dias_ferias'           ,'integer'  ,true   ,''     ,false  ,false                                  );
    $this->AddCampo('dias_abono'            ,'integer'  ,true   ,''     ,false  ,false                                  );
    $this->AddCampo('dt_inicial_aquisitivo' ,'date'     ,true   ,''    ,false  ,false);
    $this->AddCampo('dt_final_aquisitivo'   ,'date'     ,true   ,''    ,false  ,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT ferias.cod_ferias                                                                            \n";
    $stSql .= "     , ferias.cod_contrato                                                                          \n";
    $stSql .= "     , ferias.cod_forma                                                                             \n";
    $stSql .= "     , ferias.faltas                                                                                \n";
    $stSql .= "     , ferias.dias_ferias                                                                           \n";
    $stSql .= "     , ferias.dias_abono                                                                            \n";
    $stSql .= "     , to_char(ferias.dt_inicial_aquisitivo,'dd/mm/yyyy') as dt_inicial_aquisitivo                  \n";
    $stSql .= "     , to_char(ferias.dt_final_aquisitivo  ,'dd/mm/yyyy') as dt_final_aquisitivo                    \n";
    $stSql .= "     , to_char(lancamento_ferias.dt_inicio            ,'dd/mm/yyyy') as dt_inicio                   \n";
    $stSql .= "     , to_char(lancamento_ferias.dt_fim               ,'dd/mm/yyyy') as dt_fim                      \n";
    $stSql .= "     , to_char(lancamento_ferias.dt_retorno           ,'dd/mm/yyyy') as dt_retorno                  \n";
    $stSql .= "     , lancamento_ferias.mes_competencia                                                            \n";
    $stSql .= "     , lancamento_ferias.ano_competencia                                                            \n";
    $stSql .= "     , lancamento_ferias.pagar_13                                                                   \n";
    $stSql .= "     , lancamento_ferias.cod_tipo                                                                   \n";
    $stSql .= "     , tipo_folha.descricao                                                                         \n";
    $stSql .= "     , forma_pagamento_ferias.dias                                                                  \n";
    $stSql .= "     , forma_pagamento_ferias.abono                                                                 \n";
    $stSql .= "  FROM pessoal.ferias                                                                               \n";
    $stSql .= "     , pessoal.lancamento_ferias                                                                    \n";
    $stSql .= "     , folhapagamento.tipo_folha                                                                    \n";
    $stSql .= "     , pessoal.forma_pagamento_ferias                                                               \n";
    $stSql .= " WHERE ferias.cod_ferias = lancamento_ferias.cod_ferias                                             \n";
    $stSql .= "   AND lancamento_ferias.cod_tipo = tipo_folha.cod_tipo                                             \n";
    $stSql .= "   AND ferias.cod_forma = forma_pagamento_ferias.cod_forma                                          \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaRelacionamento
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro    String de Filtro do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosDoContrato(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ( $stOrdem != "" ) ? " ORDER BY $stOrdem" : "";
    $stSql = $this->montaRecuperaDadosDoContrato().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosDoContrato()
{
    $stSql .="   SELECT servidor.numcgm                                                                                                    \n";
    $stSql .="        , sw_cgm.nom_cgm                                                                                                     \n";
    $stSql .="        , contrato.*                                                                                                         \n";
    $stSql .="        , contrato_servidor_local.cod_local                                                                                  \n";
    $stSql .="        , contrato_servidor_local.descricao_local                                                                            \n";
    $stSql .="        , vw_orgao_nivel.orgao as cod_estrutural                                                                             \n";
    $stSql .="        , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao_orgao                     \n";
    $stSql .="        , to_char(contrato_servidor_nomeacao_posse.dt_posse,'dd/mm/yyyy') as dt_posse                                        \n";
    $stSql .="        , to_char(contrato_servidor_nomeacao_posse.dt_nomeacao,'dd/mm/yyyy') as dt_nomeacao                                  \n";
    $stSql .="        , to_char(contrato_servidor_nomeacao_posse.dt_admissao,'dd/mm/yyyy') as dt_admissao                                  \n";
    $stSql .="        , contrato_servidor_funcao.cod_funcao                                                                                \n";
    $stSql .="        , contrato_servidor_funcao.descricao_funcao                                                                          \n";
    $stSql .="        , contrato_servidor_regime_funcao.cod_regime                                                                         \n";
    $stSql .="        , regime.descricao as descricao_regime                                                                               \n";
    $stSql .="     FROM pessoal.contrato                                                                          \n";
    $stSql .="LEFT JOIN (SELECT contrato_servidor_funcao.cod_cargo as cod_funcao                                                           \n";
    $stSql .="                , contrato_servidor_funcao.cod_contrato                                                                      \n";
    $stSql .="                , cargo.descricao as descricao_funcao                                                                        \n";
    $stSql .="             FROM pessoal.contrato_servidor_funcao                                                  \n";
    $stSql .="                , (SELECT cod_contrato                                                                                       \n";
    $stSql .="                        , max(timestamp) as timestamp                                                                        \n";
    $stSql .="                     FROM pessoal.contrato_servidor_funcao                                          \n";
    $stSql .="                   GROUP BY cod_contrato) as max_contrato_servidor_funcao                                                    \n";
    $stSql .="                , pessoal.cargo                                                                     \n";
    $stSql .="            WHERE contrato_servidor_funcao.cod_cargo = cargo.cod_cargo                                                       \n";
    $stSql .="              AND contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                          \n";
    $stSql .="              AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp) as contrato_servidor_funcao   \n";
    $stSql .="       ON contrato.cod_contrato = contrato_servidor_funcao.cod_contrato                                                      \n";
    $stSql .="LEFT JOIN (SELECT contrato_servidor_local.cod_local                                                                          \n";
    $stSql .="                , contrato_servidor_local.cod_contrato                                                                       \n";
    $stSql .="                , local.descricao as descricao_local                                                                         \n";
    $stSql .="             FROM pessoal.contrato_servidor_local                                                   \n";
    $stSql .="                , (SELECT cod_contrato                                                                                       \n";
    $stSql .="                        , max(timestamp) as timestamp                                                                        \n";
    $stSql .="                     FROM pessoal.contrato_servidor_local                                           \n";
    $stSql .="                   GROUP BY cod_contrato) as max_contrato_servidor_local                                                     \n";
    $stSql .="                , organograma.local                                                                                          \n";
    $stSql .="            WHERE contrato_servidor_local.cod_local = local.cod_local                                                        \n";
    $stSql .="              AND contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                            \n";
    $stSql .="              AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as contrato_servidor_local      \n";
    $stSql .="       ON contrato.cod_contrato = contrato_servidor_local.cod_contrato                                                       \n";
    $stSql .="        , pessoal.contrato_servidor_nomeacao_posse                                                  \n";
    $stSql .="        , (  SELECT cod_contrato                                                                                             \n";
    $stSql .="                  , max(timestamp) as timestamp                                                                              \n";
    $stSql .="               FROM pessoal.contrato_servidor_nomeacao_posse                                        \n";
    $stSql .="           GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse                                                    \n";
    $stSql .="        , pessoal.contrato_servidor_orgao                                                           \n";
    $stSql .="        , (  SELECT cod_contrato                                                                                             \n";
    $stSql .="                  , max(timestamp) as timestamp                                                                              \n";
    $stSql .="               FROM pessoal.contrato_servidor_orgao                                                 \n";
    $stSql .="           GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                             \n";
    $stSql .="        , pessoal.contrato_servidor_regime_funcao                                                   \n";
    $stSql .="        , (  SELECT cod_contrato                                                                                             \n";
    $stSql .="                  , max(timestamp) as timestamp                                                                              \n";
    $stSql .="               FROM pessoal.contrato_servidor_regime_funcao                                         \n";
    $stSql .="           GROUP BY cod_contrato) as max_contrato_servidor_regime_funcao                                                     \n";
    $stSql .="        , pessoal.contrato_servidor_sub_divisao_funcao                                              \n";
    $stSql .="        , (  SELECT cod_contrato                                                                                             \n";
    $stSql .="                  , max(timestamp) as timestamp                                                                              \n";
    $stSql .="               FROM pessoal.contrato_servidor_sub_divisao_funcao                                    \n";
    $stSql .="           GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao                                                \n";
    $stSql .="        , pessoal.regime                                                                            \n";
    $stSql .="        , organograma.orgao                                                                                                  \n";
    $stSql .="        , organograma.orgao_nivel                                                                                            \n";
    $stSql .="        , organograma.vw_orgao_nivel                                                                                         \n";
    $stSql .="        , pessoal.servidor_contrato_servidor                                                        \n";
    $stSql .="        , pessoal.servidor                                                                          \n";
    $stSql .="        , sw_cgm                                                                                                             \n";
    $stSql .="    WHERE contrato.cod_contrato = servidor_contrato_servidor.cod_contrato                                                    \n";
    $stSql .="      AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                    \n";
    $stSql .="      AND servidor.numcgm = sw_cgm.numcgm                                                                                    \n";
    $stSql .="      AND contrato.cod_contrato = contrato_servidor_orgao.cod_contrato                                                       \n";
    $stSql .="      AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                                    \n";
    $stSql .="      AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp                                          \n";
    $stSql .="      AND contrato_servidor_orgao.cod_orgao = orgao.cod_orgao                                                                \n";
    $stSql .="      AND orgao.cod_orgao = vw_orgao_nivel.cod_orgao                                                                         \n";
    $stSql .="      AND orgao.cod_orgao = orgao_nivel.cod_orgao                                                                            \n";
    $stSql .="      AND orgao_nivel.cod_nivel = vw_orgao_nivel.nivel                                                                       \n";
    $stSql .="      AND contrato.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato                                              \n";
    $stSql .="      AND contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato                  \n";
    $stSql .="      AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp                        \n";
    $stSql .="      AND contrato.cod_contrato = contrato_servidor_regime_funcao.cod_contrato                                               \n";
    $stSql .="      AND contrato_servidor_regime_funcao.cod_contrato = max_contrato_servidor_regime_funcao.cod_contrato                    \n";
    $stSql .="      AND contrato_servidor_regime_funcao.timestamp    = max_contrato_servidor_regime_funcao.timestamp                       \n";
    $stSql .="      AND contrato_servidor_regime_funcao.cod_regime = regime.cod_regime                                                     \n";
    $stSql .="      AND contrato.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato                                          \n";
    $stSql .="      AND contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato          \n";
    $stSql .="      AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp                \n";
    $stSql .= "     AND NOT EXISTS (SELECT 1                                                                \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_caso_causa WHERE contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato)\n";

    return $stSql;
}

function recuperaEmitirAvisoFerias(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ( $stOrdem != "" ) ? " ORDER BY $stOrdem" : "";
    $stSql = $this->montaRecuperaEmitirAvisoFerias ().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEmitirAvisoFerias()
{
    $stSql .= "Select                                                                                                                        \n ";
    $stSql .= "       pessoal.contrato.registro  as registro                                                        \n ";
    $stSql .= "     , pessoal.contrato.cod_contrato                                                                 \n ";
    $stSql .= "     , sw_cgm.nom_cgm             as servidor                                                                                 \n ";
    $stSql .= "     , organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao.cod_orgao) || ' - ' || recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as orgao       \n ";
    $stSql .= "     , cargo.descricao as cargo                                                                                               \n ";
    $stSql .= "     , ferias.*                                                                                                               \n ";
    $stSql .= "     , to_char(ferias.dt_inicial_aquisitivo,'dd/mm/yyyy') as periodo_aquisitivo_inicial                                       \n ";
    $stSql .= "     , to_char(ferias.dt_final_aquisitivo  ,'dd/mm/yyyy') as periodo_aquisitivo_final                                         \n ";
    $stSql .= "     , forma_pagamento_ferias.*                                                                                               \n ";
    $stSql .= "                                                                                                                              \n ";
    $stSql .= "     , (select especialidade.descricao                                                                                        \n ";
    $stSql .= "       from pessoal.especialidade                                                                    \n ";
    $stSql .= "       inner join pessoal.contrato_servidor_especialidade_cargo                                      \n ";
    $stSql .= "         on ( contrato_servidor_especialidade_cargo.cod_especialidade = especialidade.cod_especialidade )                     \n ";
    $stSql .= "       where contrato_servidor_especialidade_cargo.cod_contrato = contrato.cod_contrato                                       \n ";
    $stSql .= "        and  contrato_servidor_especialidade_cargo.cod_contrato = contrato_servidor.cod_cargo                                 \n ";
    $stSql .= "        ) as especialidade                                                                                                    \n ";
    $stSql .= "                                                                                                                              \n ";
    $stSql .= "     , (select local.descricao                                                                                                \n ";
    $stSql .= "        from  pessoal.contrato_servidor_local                                                        \n ";
    $stSql .= "        inner join organograma.local                                                                                          \n ";
    $stSql .= "            on ( local.cod_local = contrato_servidor_local.cod_local )                                                        \n ";
    $stSql .= "        inner join (select contrato_servidor_local.cod_contrato                                                               \n ";
    $stSql .= "                         , max ( contrato_servidor_local.timestamp) as timestamp                                              \n ";
    $stSql .= "                    from pessoal.contrato_servidor_local                                             \n ";
    $stSql .= "                    group by contrato_servidor_local.cod_contrato) as ultimo_contrato_servidor_local                          \n ";
    $stSql .= "              on( contrato_servidor_local.cod_contrato = ultimo_contrato_servidor_local.cod_contrato                          \n ";
    $stSql .= "              and contrato_servidor_local.timestamp    = ultimo_contrato_servidor_local.timestamp )                           \n ";
    $stSql .= "        where  contrato_servidor_local.cod_contrato = contrato_servidor.cod_contrato ) as local                               \n ";
    $stSql .= "                                                                                                                              \n ";
    $stSql .= "From pessoal.ferias                                                                                  \n ";
    $stSql .= "inner join pessoal.forma_pagamento_ferias                                                            \n ";
    $stSql .= "    on ( forma_pagamento_ferias.cod_forma = ferias.cod_forma )                                                                \n ";
    $stSql .= "inner join pessoal.contrato                                                                          \n ";
    $stSql .= "    on ( ferias.cod_contrato = contrato.cod_contrato )                                                                        \n ";
    $stSql .= "inner join pessoal.contrato_servidor                                                                 \n ";
    $stSql .= "    on contrato.cod_contrato = contrato_servidor.cod_contrato                                                                 \n ";
    $stSql .= "inner join pessoal.servidor_contrato_servidor                                                        \n ";
    $stSql .= "    on contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato                                               \n ";
    $stSql .= "inner join pessoal.servidor                                                                          \n ";
    $stSql .= "    on servidor.cod_servidor = servidor_contrato_servidor.cod_servidor                                                        \n ";
    $stSql .= "inner join sw_cgm_pessoa_fisica                                                                                               \n ";
    $stSql .= "    on servidor.numcgm = sw_cgm_pessoa_fisica.numcgm                                                                          \n ";
    $stSql .= "inner join sw_cgm                                                                                                             \n ";
    $stSql .= "    on sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm                                                                            \n ";
    $stSql .= "inner join pessoal.contrato_servidor_orgao                                                           \n ";
    $stSql .= "    on contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato                                                  \n ";
    $stSql .= "inner join organograma.orgao                                                                                                  \n ";
    $stSql .= "       on (orgao.cod_orgao       = contrato_servidor_orgao.cod_orgao                                                          \n ";
    $stSql .= "         and contrato_servidor_orgao.timestamp = (select max(timestamp) from                                                  \n ";
    $stSql .= "                                                        pessoal.contrato_servidor_orgao              \n ";
    $stSql .= "                                                    where contrato_servidor_orgao.cod_contrato = contrato.cod_contrato))      \n ";
    $stSql .= "inner join organograma.orgao_nivel                                                                                            \n ";
    $stSql .= "    on orgao.cod_orgao        = orgao_nivel.cod_orgao                                                                         \n ";
    $stSql .= "    and orgao_nivel.cod_nivel =                                                                                               \n ";
    $stSql .= "        publico.fn_nivel(organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao.cod_orgao))                         \n ";
    $stSql .= "inner join pessoal.cargo                                                                             \n ";
    $stSql .= "    on ( cargo.cod_cargo = contrato_servidor.cod_cargo )                                                                      \n ";
    $stSql .= "inner join pessoal.contrato_servidor_regime_funcao                                                   \n ";
    $stSql .= "    on (contrato_servidor_regime_funcao.cod_contrato = contrato_servidor.cod_contrato)                                        \n ";
    $stSql .= "inner join (select cod_contrato,                                                                                              \n ";
    $stSql .= "                   max ( timestamp ) as timestamp                                                                             \n ";
    $stSql .= "            from pessoal.contrato_servidor_regime_funcao                                             \n ";
    $stSql .= "            group by cod_contrato) as ultimo_contrato_servidor_regime                                                         \n ";
    $stSql .= "    on (ultimo_contrato_servidor_regime.cod_contrato = contrato_servidor_regime_funcao.cod_contrato                           \n ";
    $stSql .= "    and ultimo_contrato_servidor_regime.timestamp    = contrato_servidor_regime_funcao.timestamp)                             \n ";
    $stSql .= "                                                                                                                              \n ";
    $stSql .= "inner join pessoal.contrato_servidor_sub_divisao_funcao                                              \n ";
    $stSql .= "    on ( contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_servidor.cod_contrato)                                  \n ";
    $stSql .= "inner join (select cod_contrato,                                                                                              \n ";
    $stSql .= "                   max ( timestamp ) as timestamp                                                                             \n ";
    $stSql .= "           from pessoal.contrato_servidor_sub_divisao_funcao                                         \n ";
    $stSql .= "           group by cod_contrato                                                                                              \n ";
    $stSql .= "        ) as  ultimo_contrato_sub_divisao                                                                                     \n ";
    $stSql .= "    on (ultimo_contrato_sub_divisao.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato                          \n ";
    $stSql .= "    and ultimo_contrato_sub_divisao.timestamp    = contrato_servidor_sub_divisao_funcao.timestamp )                           \n ";

    return $stSql;

}

function recuperaReciboFerias(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ( $stOrdem != "" ) ? " ORDER BY $stOrdem" : " ORDER BY nom_cgm";
    $stSql = $this->montaRecuperaReciboFerias().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaReciboFerias()
{
    $stSql .= "   SELECT contrato.*                                                                                                                                                                     \n";
    $stSql .= "        , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = servidor.numcgm) as nom_cgm                                                                                                         \n";
    $stSql .= "        , (SELECT descricao FROM pessoal.cargo WHERE cod_cargo = contrato_servidor_funcao.cod_cargo) as desc_funcao                                                                      \n";
    $stSql .= "        , (SELECT descricao FROM pessoal.especialidade WHERE cod_especialidade = contrato_servidor_especialidade_funcao.cod_especialidade) as desc_especialidade                         \n";
    $stSql .= "        , recuperaDescricaoOrgao(contrato_servidor_orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as desc_orgao                                                                    \n";
    $stSql .= "        , (SELECT organograma.fn_consulta_orgao(orgao_nivel.cod_organograma,contrato_servidor_orgao.cod_orgao)) as orgao                                                                 \n";
    $stSql .= "        , (SELECT descricao FROM organograma.local WHERE cod_local = contrato_servidor_local.cod_local) as desc_local                                                                    \n";
    $stSql .= "        , contrato_servidor_salario.salario                                                                                                                                              \n";
    $stSql .= "        , ferias.faltas                                                                                                                                                                  \n";
    $stSql .= "        , ferias.dias_ferias                                                                                                                                                             \n";
    $stSql .= "        , ferias.dias_abono                                                                                                                                                              \n";
    $stSql .= "        , to_char(ferias.dt_inicial_aquisitivo,'dd/mm/yyyy') as dt_inicial_aquisitivo                                                                                                    \n";
    $stSql .= "        , to_char(ferias.dt_final_aquisitivo  ,'dd/mm/yyyy') as dt_final_aquisitivo                                                                                                      \n";
    $stSql .= "        , to_char(lancamento_ferias.dt_inicio            ,'dd/mm/yyyy') as dt_inicio                                                                                                     \n";
    $stSql .= "        , to_char(lancamento_ferias.dt_fim               ,'dd/mm/yyyy') as dt_fim                                                                                                        \n";
    $stSql .= "        , lancamento_ferias.mes_competencia                                                                                                                                              \n";
    $stSql .= "        , lancamento_ferias.ano_competencia                                                                                                                                              \n";
    $stSql .= "        , lancamento_ferias.cod_tipo                                                                                                                                                     \n";
    $stSql .= "     FROM pessoal.ferias                                                                                                                                                                 \n";
    $stSql .= "        , pessoal.lancamento_ferias                                                                                                                                                      \n";
    $stSql .= "        , pessoal.contrato                                                                                                                                                               \n";
    $stSql .= "        , pessoal.servidor_contrato_servidor                                                                                                                                             \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_especialidade_funcao.*                                                                                                                               \n";
    $stSql .= "             FROM pessoal.contrato_servidor_especialidade_funcao                                                                                                                         \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                                                 \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                                                  \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_especialidade_funcao                                                                                                               \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_especialidade_funcao                                                                                                  \n";
    $stSql .= "            WHERE contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato                                                          \n";
    $stSql .= "              AND contrato_servidor_especialidade_funcao.timestamp = max_contrato_servidor_especialidade_funcao.timestamp) as contrato_servidor_especialidade_funcao                     \n";
    $stSql .= "       ON servidor_contrato_servidor.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato                                                                                  \n";
    $stSql .= "LEFT JOIN (SELECT contrato_servidor_local.*                                                                                                                                              \n";
    $stSql .= "             FROM pessoal.contrato_servidor_local                                                                                                                                        \n";
    $stSql .= "                , (  SELECT cod_contrato                                                                                                                                                 \n";
    $stSql .= "                          , max(timestamp) as timestamp                                                                                                                                  \n";
    $stSql .= "                       FROM pessoal.contrato_servidor_local                                                                                                                              \n";
    $stSql .= "                   GROUP BY cod_contrato) as max_contrato_servidor_local                                                                                                                 \n";
    $stSql .= "            WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato                                                                                        \n";
    $stSql .= "              AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as contrato_servidor_local                                                                  \n";
    $stSql .= "       ON servidor_contrato_servidor.cod_contrato = contrato_servidor_local.cod_contrato                                                                                                 \n";
    $stSql .= "        , pessoal.servidor                                                                                                                                                               \n";
    $stSql .= "        , pessoal.contrato_servidor                                                                                                                                                      \n";
    $stSql .= "        , pessoal.contrato_servidor_funcao                                                                                                                                               \n";
    $stSql .= "        , (  SELECT cod_contrato                                                                                                                                                         \n";
    $stSql .= "                  , max(timestamp) as timestamp                                                                                                                                          \n";
    $stSql .= "               FROM pessoal.contrato_servidor_funcao                                                                                                                                     \n";
    $stSql .= "           GROUP BY cod_contrato) as max_contrato_servidor_funcao                                                                                                                        \n";
    $stSql .= "        , pessoal.contrato_servidor_orgao                                                                                                                                                \n";
    $stSql .= "        , (  SELECT cod_contrato                                                                                                                                                         \n";
    $stSql .= "                  , max(timestamp) as timestamp                                                                                                                                          \n";
    $stSql .= "               FROM pessoal.contrato_servidor_orgao                                                                                                                                      \n";
    $stSql .= "           GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                                                                                         \n";
    $stSql .= "        , organograma.orgao_nivel                                                                                                                                                        \n";
    $stSql .= "        , pessoal.contrato_servidor_salario                                                                                                                                              \n";
    $stSql .= "        , (  SELECT cod_contrato                                                                                                                                                         \n";
    $stSql .= "                  , max(timestamp) as timestamp                                                                                                                                          \n";
    $stSql .= "               FROM pessoal.contrato_servidor_salario                                                                                                                                    \n";
    $stSql .= "           GROUP BY cod_contrato) as max_contrato_servidor_salario                                                                                                                       \n";
    $stSql .= "    WHERE ferias.cod_ferias = lancamento_ferias.cod_ferias                                                                                                                               \n";
    $stSql .= "      AND ferias.cod_contrato = contrato.cod_contrato                                                                                                                                    \n";
    $stSql .= "      AND contrato.cod_contrato = servidor_contrato_servidor.cod_contrato                                                                                                                \n";
    $stSql .= "      AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                                                                                \n";
    $stSql .= "      AND servidor_contrato_servidor.cod_contrato = contrato_servidor_funcao.cod_contrato                                                                                                \n";
    $stSql .= "      AND contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                                                                                              \n";
    $stSql .= "      AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp                                                                                                    \n";
    $stSql .= "      AND servidor_contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato                                                                                                 \n";
    $stSql .= "      AND servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato                                                                                                       \n";
    $stSql .= "      AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato                                                                                                \n";
    $stSql .= "      AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp                                                                                                      \n";
    $stSql .= "      AND contrato_servidor_orgao.cod_orgao = orgao_nivel.cod_orgao                                                                                                                      \n";
    $stSql .= "      AND orgao_nivel.cod_nivel = publico.fn_nivel(organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, contrato_servidor_orgao.cod_orgao))                                        \n";
    $stSql .= "      AND servidor_contrato_servidor.cod_contrato = contrato_servidor_salario.cod_contrato                                                                                               \n";
    $stSql .= "      AND contrato_servidor_salario.cod_contrato = max_contrato_servidor_salario.cod_contrato                                                                                            \n";
    $stSql .= "      AND contrato_servidor_salario.timestamp = max_contrato_servidor_salario.timestamp                                                                                                  \n";
    $stSql .= "      AND contrato.cod_contrato NOT IN (SELECT cod_contrato                       \n";
    $stSql .= "                                         FROM pessoal.contrato_servidor_caso_causa )   \n";

    return $stSql;
}

function recuperaFerias(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaFerias",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaFerias()
{
    $stSql .= "   SELECT ferias.*                                               \n";
    $stSql .= "        , lote_ferias_lote.cod_lote                              \n";
    $stSql .= "     FROM pessoal.ferias               \n";
    $stSql .= "LEFT JOIN pessoal.lote_ferias_lote     \n";
    $stSql .= "       ON lote_ferias_lote.cod_ferias = ferias.cod_ferias        \n";

    return $stSql;
}


function concederFerias(&$rsRecordSet, $stFiltro = "", $stOrder = "",$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    
    $stGroupBy = " GROUP BY concederFerias.cod_ferias
                          , concederFerias.numcgm
                          , concederFerias.nom_cgm
                          , concederFerias.registro
                          , concederFerias.cod_contrato
                          , concederFerias.desc_local
                          , concederFerias.desc_orgao
                          , concederFerias.orgao
                          , concederFerias.dt_posse
                          , concederFerias.dt_admissao
                          , concederFerias.dt_nomeacao
                          , concederFerias.desc_funcao
                          , concederFerias.desc_regime_funcao
                          , concederFerias.cod_regime_funcao
                          , concederFerias.cod_funcao
                          , concederFerias.cod_local
                          , concederFerias.cod_orgao
                          , concederFerias.bo_cadastradas
                          , concederFerias.situacao
                          , concederFerias.dt_inicial_aquisitivo
                          , concederFerias.dt_final_aquisitivo
                          , concederFerias.dt_inicio
                          , concederFerias.dt_fim
                          , concederFerias.mes_competencia
                          , concederFerias.ano_competencia

                     HAVING CASE WHEN '".$this->getDado("stAcao")."' = 'incluir' THEN
                                    CASE WHEN SUM(coalesce(ferias.dias_ferias, 0)) + SUM(coalesce(ferias.dias_abono, 0)) < 30 THEN TRUE
                                    ELSE FALSE
                                    END
                           ELSE TRUE
                           END
            ";
    
    $stSql = $this->montaConcederFerias().$stFiltro.$stGroupBy.$stOrder;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaConcederFerias()
{
    if(!$this->getDado("boLote")){
        $stFiltroLote = '';
        if($this->getDado("stAcao") == 'incluir'){
            $stFiltroFerias = " AND ferias.cod_forma NOT IN (1,2)
                                AND ferias.cod_forma IN (3,4)
                              ";
        }else
            $stFiltroFerias = '';
    }else{
        $stFiltroLote = "

         INNER JOIN ( SELECT cod_contrato
                           , min(dt_inicial_aquisitivo) AS dt_inicial_aquisitivo
                           , min(dt_final_aquisitivo) AS dt_final_aquisitivo
                        FROM concederFerias('".$this->getDado("stTipoFiltro")."',
                                            '".$this->getDado("stValoresFiltro")."',
                                            ".$this->getDado("inCodPeriodoMovimentacao").",
                                            ".$this->getDado("boFeriasVencidas").",
                                            '".Sessao::getEntidade()."',
                                            '".Sessao::getExercicio()."',
                                            '".$this->getDado("stAcao")."',
                                            ".$this->getDado("inCodLote").",
                                            ".(($this->getDado("inCodRegime") != "")?$this->getDado("inCodRegime"):0)."
                                           ) AS concederFerias
                    GROUP BY cod_contrato
                     ) AS min_periodo_ferias
                  ON min_periodo_ferias.cod_contrato = concederFerias.cod_contrato
                 AND min_periodo_ferias.dt_inicial_aquisitivo = concederFerias.dt_inicial_aquisitivo
                 AND min_periodo_ferias.dt_final_aquisitivo = concederFerias.dt_final_aquisitivo

        ";
        
        $stFiltroFerias = " AND ferias.cod_forma NOT IN (1,2)
                            AND ferias.cod_forma IN (".$this->getDado("inCodFormaPagamento").")
                          ";
    }

    $stSql = "SELECT concederFerias.*
                   , CASE WHEN TRIM(concederFerias.mes_competencia) <> '' THEN
                               CASE WHEN TRIM(concederFerias.mes_competencia)::INTEGER > 0 THEN
                                         concederFerias.mes_competencia||'/'||concederFerias.ano_competencia
                               END
                     END AS competencia
                   , to_char(concederFerias.dt_inicial_aquisitivo,'dd/mm/yyyy') as dt_inicial_aquisitivo_formatado
                   , to_char(concederFerias.dt_final_aquisitivo,'dd/mm/yyyy') as dt_final_aquisitivo_formatado
                   , to_char(concederFerias.dt_admissao,'dd/mm/yyyy') as dt_admissao_formatado
                   , SUM(coalesce(ferias.dias_ferias, 0)) + SUM(coalesce(ferias.dias_abono, 0)) AS ferias_tiradas
                FROM concederFerias('".$this->getDado("stTipoFiltro")."',
                                           '".$this->getDado("stValoresFiltro")."',
                                            ".$this->getDado("inCodPeriodoMovimentacao").",
                                            ".$this->getDado("boFeriasVencidas").",
                                           '".Sessao::getEntidade()."',
                                           '".Sessao::getExercicio()."',
                                           '".$this->getDado("stAcao")."',
                                            ".$this->getDado("inCodLote").",
                                            ".(($this->getDado("inCodRegime") != "")?$this->getDado("inCodRegime"):0)."
                                    ) AS concederFerias
          ".$stFiltroLote."
           LEFT JOIN pessoal.ferias
                  ON ferias.dt_inicial_aquisitivo = concederFerias.dt_inicial_aquisitivo
                 AND ferias.dt_final_aquisitivo = concederFerias.dt_final_aquisitivo
                 AND ferias.cod_contrato = concederFerias.cod_contrato

               WHERE (   (    ferias.cod_forma IS NOT NULL
                          ".$stFiltroFerias."
                         )
                      OR ferias.cod_forma IS NULL
                     )
        ";

    return $stSql;
}
function possuiEvento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaPossuiEvento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);

}

function montaPossuiEvento()
{
     $stSql .=" SELECT * FROM folhapagamento.registro_evento_periodo
                         INNER JOIN folhapagamento.ultimo_registro_evento
                                 ON ultimo_registro_evento.cod_registro = registro_evento_periodo.cod_registro


";

     return $stSql;

}

function verificaDatasPeriodoAquisitivo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    
    $stSql = $this->montaVerificaDatasPeriodoAquisitivo().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaVerificaDatasPeriodoAquisitivo()
{
    $stSql .= "
      SELECT *
        FROM pessoal.ferias
        JOIN pessoal.contrato_servidor
          ON contrato_servidor.cod_contrato = ferias.cod_contrato
        JOIN pessoal.contrato
          ON contrato.cod_contrato = contrato_servidor.cod_contrato
       WHERE contrato.cod_contrato = ".$this->getDado('cod_contrato_aquisitivo')."
         AND (  TO_DATE('".$this->getDado('dt_inicial_aquisitivo')."','dd/mm/yyyy') BETWEEN ferias.dt_inicial_aquisitivo -- data inicial
                                                                                        AND ferias.dt_final_aquisitivo   -- data final
             OR TO_DATE('".$this->getDado('dt_final_aquisitivo')."','dd/mm/yyyy') BETWEEN ferias.dt_inicial_aquisitivo   -- data inicial
                                                                                      AND ferias.dt_final_aquisitivo     -- data final
             )
         AND cod_forma NOT IN (3,4)
    ";
    
    return $stSql;
}

function recuperaHistoricoFeriasRelatorio(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ( $stOrdem != "" ) ? " ORDER BY $stOrdem" : "";
    $stSql = $this->montaRecuperaHistoricoFeriasRelatorio().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaHistoricoFeriasRelatorio()
{
    $stSql = "  SELECT   TO_CHAR(dt_inicial_aquisitivo,'dd/mm/yyyy') as dt_inicial_aquisitivo
                        ,TO_CHAR(dt_final_aquisitivo,'dd/mm/yyyy') as dt_final_aquisitivo
                        ,TO_CHAR(dt_inicial_gozo,'dd/mm/yyyy') as dt_inicial_gozo
                        ,TO_CHAR(dt_final_gozo,'dd/mm/yyyy') as dt_final_gozo
                        ,faltas
                        ,dias_ferias as ferias
                        ,dias_abono as abono
                        ,mes_pagamento
                        ,folha
                        ,pagar_13
                FROM relatorioHistoricoFerias('".$this->getDado('cod_entidade')."'
                                            ,'".$this->getDado('exercicio')."'
                                            ,'".$this->getDado('data_limite')."'
                                            ,'".$this->getDado('tipo_filtro')."'
                                            ,'".$this->getDado('valor_filtro')."'
                                            );

    ";
    
    return $stSql;
}



}
