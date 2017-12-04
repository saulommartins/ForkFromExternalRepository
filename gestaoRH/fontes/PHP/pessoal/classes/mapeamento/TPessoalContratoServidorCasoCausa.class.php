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
* Classe de regra de negócio para PESSOAL.CAUSA_RESCISAO
* Data de Criação: 05/05/2005

* @author Desenvolvedor: Eduardo Antunez

* Casos de uso: uc-04.04.44

* $Id: TPessoalContratoServidorCasoCausa.class.php 65923 2016-06-30 13:18:20Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CAUSA_RESCISAO
  * Data de Criação: 05/05/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Eduardo Antunez

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalContratoServidorCasoCausa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalContratoServidorCasoCausa()
{
    parent::Persistente();
    $this->setTabela('pessoal.contrato_servidor_caso_causa');

    $this->setCampoCod('cod_contrato');
    $this->setComplementoChave('');

    $this->AddCampo( 'cod_contrato'   , 'integer'   , true  , '' , true  , false );
    $this->AddCampo( 'cod_caso_causa' , 'integer'   , true  , '' , true  , false );
    $this->AddCampo( 'dt_rescisao'    , 'date'      , false , '' , false , true  );
    $this->AddCampo( 'timestamp'      , 'timestamp' , false , '' , true  , true  );
    $this->AddCampo( 'inc_folha_salario', 'boolean' , false , '' , false , false );
    $this->AddCampo( 'inc_folha_decimo' , 'boolean' , false , '' , false , false );
}

function recuperaRescisaoContrato(&$rsRecordSet , $stFiltro="" , $stOrdem="" , $boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (empty($stOrdem)) {
        $stOrdem .= " ORDER BY                         \n";
        $stOrdem .= "      pc.registro                 \n";
    }
    $stSql  = $this->montaRecuperaRescisaoContrato().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRescisaoContrato()
{
    $stSql .= "SELECT                                                                       \n";
    $stSql .= "     pc.registro                                                             \n";
    $stSql .= "    ,TO_CHAR(contrato_servidor_nomeacao_posse.dt_nomeacao,'dd/mm/yyyy') as dt_nomeacao              \n";
    $stSql .= "    ,TO_CHAR(contrato_servidor_nomeacao_posse.dt_posse   ,'dd/mm/yyyy') as dt_posse                 \n";
    $stSql .= "    ,TO_CHAR(contrato_servidor_nomeacao_posse.dt_admissao   ,'dd/mm/yyyy') as dt_admissao                 \n";
    $stSql .= "    ,oon.orgao                                                               \n";
    $stSql .= "    ,recuperaDescricaoOrgao(oon.cod_orgao,('".Sessao::getExercicio()."-01-01')::date) as descricao    \n";
    $stSql .= "    ,ps.numcgm                                                               \n";
    $stSql .= "    ,cgm.nom_cgm                                                             \n";
    $stSql .= "    ,pcs.cod_sub_divisao                                                     \n";
    $stSql .= "    ,pscs.cod_contrato                                                       \n";
    if ($this->getDado('rescindidos')) {
        $stSql .= ",pcscc.cod_caso_causa                                                    \n";
        $stSql .= ",TO_CHAR(pcscc.dt_rescisao,'dd/mm/yyyy') as dt_rescisao                  \n";
        $stSql .= ",( SELECT usuario.username FROM administracao.usuario where usuario.numcgm = cgm.numcgm) AS username \n";
        $stSql .= ",( SELECT usuario.status FROM administracao.usuario where usuario.numcgm = cgm.numcgm) AS status     \n";
    }
    $stSql .= "FROM                                                                         \n";
    $stSql .= "     pessoal.servidor as ps                                                  \n";
    $stSql .= "    ,pessoal.servidor_contrato_servidor as pscs                              \n";
    $stSql .= "    ,pessoal.contrato_servidor as pcs                                        \n";
    $stSql .= "    ,pessoal.contrato as pc                                                  \n";
    $stSql .= "    ,sw_cgm as cgm                                                           \n";
    $stSql .= "    ,pessoal.contrato_servidor_nomeacao_posse                                \n";
    $stSql .= "    ,(                                                                       \n";
    $stSql .= "        SELECT                                                               \n";
    $stSql .= "            pcsnp.cod_contrato,                                              \n";
    $stSql .= "            MAX ( pcsnp.timestamp ) AS timestamp                             \n";
    $stSql .= "        FROM                                                                 \n";
    $stSql .= "            pessoal.contrato_servidor_nomeacao_posse pcsnp                   \n";
    $stSql .= "        GROUP BY                                                             \n";
    $stSql .= "            pcsnp.cod_contrato) AS pcsnp_max                                 \n";
    $stSql .= "    ,(                                                                       \n";
    $stSql .= "        SELECT                                                               \n";
    $stSql .= "             pcso.cod_contrato                                               \n";
    $stSql .= "            ,pcso.cod_orgao                                                  \n";
    $stSql .= "        FROM                                                                 \n";
    $stSql .= "             pessoal.contrato_servidor_orgao pcso                            \n";
    $stSql .= "            ,(                                                               \n";
    $stSql .= "                SELECT                                                       \n";
    $stSql .= "                     cod_contrato                                            \n";
    $stSql .= "                    ,MAX(timestamp) as timestamp                             \n";
    $stSql .= "                FROM                                                         \n";
    $stSql .= "                     pessoal.contrato_servidor_orgao                         \n";
    $stSql .= "                GROUP BY                                                     \n";
    $stSql .= "                    cod_contrato ) as pcso_max                               \n";
    $stSql .= "        WHERE                                                                \n";
    $stSql .= "                pcso.cod_contrato = pcso_max.cod_contrato                    \n";
    $stSql .= "            AND pcso.timestamp    = pcso_max.timestamp) as pcso              \n";
    $stSql .= "    ,organograma.vw_orgao_nivel as oon                                       \n";
    if ($this->getDado('rescindidos'))
        $stSql .= ",pessoal.contrato_servidor_caso_causa as pcscc                           \n";
    $stSql .= "WHERE                                                                        \n";
    $stSql .= "        pscs.cod_servidor = ps.cod_servidor                                  \n";
    $stSql .= "    AND pcs.cod_contrato = pscs.cod_contrato                                 \n";
    $stSql .= "    AND pc.cod_contrato = pcs.cod_contrato                                   \n";
    $stSql .= "    AND pcsnp_max.cod_contrato = pcs.cod_contrato                            \n";
    $stSql .= "    AND pcso.cod_contrato = pcs.cod_contrato                                 \n";
    $stSql .= "    AND oon.cod_orgao = pcso.cod_orgao                                       \n";
    $stSql .= "    AND cgm.numcgm = ps.numcgm                                               \n";

    $stSql .= "    AND contrato_servidor_nomeacao_posse.cod_contrato = pcsnp_max.cod_contrato \n";
    $stSql .= "    AND contrato_servidor_nomeacao_posse.timestamp = pcsnp_max.timestamp       \n";

    if ($this->getDado('rescindidos'))
        $stSql .= "AND pcs.cod_contrato IN (                                                \n";
    else
        $stSql .= "AND pcs.cod_contrato NOT IN (                                            \n";
    $stSql .= "        SELECT                                                               \n";
    $stSql .= "            cod_contrato                                                     \n";
    $stSql .= "        FROM                                                                 \n";
    $stSql .= "            pessoal.contrato_servidor_caso_causa )                           \n";
    if ($this->getDado('rescindidos'))
        $stSql .= "AND pcs.cod_contrato = pcscc.cod_contrato                                \n";

    return $stSql;
}

function recuperaCasoCausa(&$rsRecordSet, $stFiltro="", $stOrdem="", $boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaCasoCausa().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCasoCausa()
{
    $stSql .= "SELECT                                                                    \n";
    $stSql .= "     pcc.cod_caso_causa                                                   \n";
    $stSql .= "    ,pcc.descricao                                                        \n";
    $stSql .= "    ,pcc.paga_aviso_previo                                                        \n";
    $stSql .= "FROM                                                                      \n";
    $stSql .= "    pessoal.caso_causa as pcc                                             \n";
    $stSql .= "   ,pessoal.causa_rescisao as pcr                                         \n";
    $stSql .= "   ,pessoal.caso_causa_sub_divisao as pccsd                               \n";
    $stSql .= "WHERE                                                                     \n";
    $stSql .= "        pcc.cod_causa_rescisao = pcr.cod_causa_rescisao                   \n";
    if ($inCodCausaRescisao = $this->getDado('cod_causa_rescisao'))
        $stSql .= "AND pcr.cod_causa_rescisao = ".$inCodCausaRescisao."                  \n";
    $stSql .= "    AND pcc.cod_caso_causa IN (                                           \n";
    $stSql .= "        SELECT                                                            \n";
    $stSql .= "            pcca.cod_caso_causa                                           \n";
    $stSql .= "        FROM                                                              \n";
    $stSql .= "             pessoal.periodo_caso ppc                                     \n";
    $stSql .= "            ,pessoal.caso_causa pcca                                      \n";
    $stSql .= "            ,pessoal.grupo_periodo pgp                                    \n";
    $stSql .= "        WHERE                                                             \n";
    $stSql .= "                ppc.cod_periodo = pcca.cod_periodo                        \n";
    $stSql .= "            AND pgp.cod_grupo_periodo = ppc.cod_grupo_periodo             \n";
    if ($inCodGrupoPeriodo = $this->getDado('cod_grupo_periodo'))
        $stSql .= "        AND ppc.cod_grupo_periodo = ".$inCodGrupoPeriodo."            \n";
    if ($inMeses = $this->getDado('meses'))
        $stSql .= "        AND '".$inMeses."' between periodo_inicial AND periodo_final  \n";
    $stSql .= "    )                                                                     \n";
    $stSql .= "    AND pccsd.cod_caso_causa = pcc.cod_caso_causa                         \n";
    if ($inCodSubDivisao = $this->getDado('cod_sub_divisao'))
        $stSql .= "AND pccsd.cod_sub_divisao = ".$inCodSubDivisao."                      \n";

    return $stSql;
}

function recuperaCalculaData(&$rsRecordSet, $dtInicial="'01/01/1990'", $dtFinal="'01/01/1990'", $boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaCalculaData($dtInicial, $dtFinal);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaCalculaData($dtInicial, $dtFinal)
{
    $stSql = "SELECT AGE(".$dtFinal.",".$dtInicial.") as tempo";

    return $stSql;
}

function recuperaConstratosRescindidosComRecolhimentoFGTS(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaConstratosRescindidosComRecolhimentoFGTS().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConstratosRescindidosComRecolhimentoFGTS()
{
    $stSql .= "SELECT contrato_servidor_caso_causa.cod_contrato                                                                            \n";
    $stSql .= "     , (SELECT registro FROM pessoal.contrato WHERE cod_contrato = contrato_servidor_caso_causa.cod_contrato) as registro   \n";
    $stSql .= "     , servidor.numcgm                                                                                                      \n";
    $stSql .= "     , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = servidor.numcgm) as nom_cgm                                               \n";
    $stSql .= "     , to_char(contrato_servidor_caso_causa.dt_rescisao,'dd/mm/yyyy') as dt_rescisao                                        \n";
    $stSql .= "  FROM pessoal.contrato_servidor_caso_causa                                                                                 \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                                                   \n";
    $stSql .= "     , pessoal.servidor                                                                                                     \n";
    $stSql .= "     , folhapagamento.registro_evento_periodo                                                                               \n";
    $stSql .= "     , folhapagamento.periodo_movimentacao                                                                                  \n";
    $stSql .= "     , folhapagamento.registro_evento                                                                                       \n";
    $stSql .= " WHERE contrato_servidor_caso_causa.cod_contrato = registro_evento_periodo.cod_contrato                                     \n";
    $stSql .= "   AND contrato_servidor_caso_causa.cod_contrato = servidor_contrato_servidor.cod_contrato                                  \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                                      \n";
    $stSql .= "   AND registro_evento_periodo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao                     \n";
    $stSql .= "   AND registro_evento_periodo.cod_registro = registro_evento.cod_registro                                                  \n";
    $stSql .= "   AND registro_evento.cod_evento = (SELECT fgts_evento.cod_evento                                                          \n";
    $stSql .= "                                       FROM folhapagamento.fgts_evento                                                      \n";
    $stSql .= "                                          , (SELECT cod_evento                                                              \n";
    $stSql .= "                                                  , cod_fgts                                                                \n";
    $stSql .= "                                                  , cod_tipo                                                                \n";
    $stSql .= "                                                  , max(timestamp) as timestamp                                             \n";
    $stSql .= "                                               FROM folhapagamento.fgts_evento                                              \n";
    $stSql .= "                                             GROUP BY cod_evento                                                            \n";
    $stSql .= "                                                    , cod_fgts                                                              \n";
    $stSql .= "                                                    , cod_tipo) as max_fgts_evento                                          \n";
    $stSql .= "                                      WHERE fgts_evento.cod_evento = max_fgts_evento.cod_evento                             \n";
    $stSql .= "                                        AND fgts_evento.cod_fgts = max_fgts_evento.cod_fgts                                 \n";
    $stSql .= "                                        AND fgts_evento.cod_tipo = max_fgts_evento.cod_tipo                                 \n";
    $stSql .= "                                        AND fgts_evento.timestamp = max_fgts_evento.timestamp                               \n";
    $stSql .= "                                        AND fgts_evento.cod_tipo = 1)                                                       \n";

    return $stSql;
}

function recuperaTermoRescisao(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY nom_cgm ";
    $stSql  = $this->montaRecuperaTermoRescisao().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTermoRescisao()
{
    $stSql .= "\n       SELECT contrato.*";
    $stSql .= "\n           , servidor_contrato_servidor.cod_servidor";
    $stSql .= "\n           , initcap(sw_cgm.nom_cgm) as nom_cgm";
    $stSql .= "\n           , initcap(sw_cgm.logradouro ||', '|| sw_cgm.numero || ', ' || sw_cgm.complemento) as endereco";
    $stSql .= "\n           , initcap(sw_cgm.bairro) as bairro";
    $stSql .= "\n           , substr(sw_cgm.cep,1,5)||'-'||substr(sw_cgm.cep,6,3) as cep";
    $stSql .= "\n           , (SELECT nom_municipio FROM sw_municipio WHERE sw_cgm.cod_municipio = sw_municipio.cod_municipio AND sw_cgm.cod_uf = sw_municipio.cod_uf) as nom_municipio";
    $stSql .= "\n           , (SELECT sigla_uf FROM sw_uf WHERE sw_cgm.cod_uf = sw_uf.cod_uf) as sigla_uf";
    $stSql .= "\n           , to_char(sw_cgm_pessoa_fisica.dt_nascimento,'dd/mm/yyyy') as dt_nascimento";
    $stSql .= "\n           , substr(sw_cgm_pessoa_fisica.cpf,1,3)||'.'||substr(sw_cgm_pessoa_fisica.cpf,4,3)||'.'||substr(sw_cgm_pessoa_fisica.cpf,7,3)||'-'||substr(sw_cgm_pessoa_fisica.cpf,10,2) as cpf";
    $stSql .= "\n           , initcap(servidor.nome_mae) as nome_mae";
    $stSql .= "\n           , sw_cgm_pessoa_fisica.servidor_pis_pasep";
    $stSql .= "\n           , to_char(contrato_servidor_nomeacao_posse.dt_admissao,'dd/mm/yyyy' ) as dt_admissao";
    $stSql .= "\n           , contrato_servidor_salario.salario";
    $stSql .= "\n           , to_char(aviso_previo.dt_aviso,'dd/mm/yyyy') as dt_aviso";
    $stSql .= "\n           , to_char(contrato_servidor_caso_causa.dt_rescisao,'dd/mm/yyyy') as dt_rescisao";
    $stSql .= "\n           , causa_rescisao.descricao";
    $stSql .= "\n           , causa_rescisao.num_causa";
    $stSql .= "\n           , (SELECT num_sefip FROM pessoal.sefip WHERE cod_sefip = causa_rescisao.cod_sefip_saida) as num_sefip";
    $stSql .= "\n           , contrato_servidor.cod_categoria";
    $stSql .= "\n           , ctps.numero ||'/'||ctps.serie ||'-'||ctps.orgao_expedidor as ctps";
    $stSql .= "\n           , (SELECT organograma.fn_consulta_orgao(orgao_nivel.cod_organograma,contrato_servidor_orgao.cod_orgao)) as orgao";
    $stSql .= "\n           , recuperaDescricaoOrgao(contrato_servidor_orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as desc_orgao";
    $stSql .= "\n           , (SELECT descricao FROM organograma.local WHERE cod_local = contrato_servidor_local.cod_local) as desc_local";
    $stSql .= "\n       FROM pessoal.contrato";
    $stSql .= "\n INNER JOIN pessoal.contrato_servidor";
    $stSql .= "\n         ON contrato.cod_contrato = contrato_servidor.cod_contrato";
    $stSql .= "\n INNER JOIN pessoal.servidor_contrato_servidor";
    $stSql .= "\n         ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato";
    $stSql .= "\n INNER JOIN pessoal.servidor";
    $stSql .= "\n         ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor";
    $stSql .= "\n INNER JOIN pessoal.contrato_servidor_caso_causa";
    $stSql .= "\n         ON contrato_servidor.cod_contrato = contrato_servidor_caso_causa.cod_contrato";
    $stSql .= "\n INNER JOIN pessoal.caso_causa";
    $stSql .= "\n         ON contrato_servidor_caso_causa.cod_caso_causa = caso_causa.cod_caso_causa";
    $stSql .= "\n INNER JOIN pessoal.causa_rescisao";
    $stSql .= "\n         ON caso_causa.cod_causa_rescisao = causa_rescisao.cod_causa_rescisao";
    $stSql .= "\n INNER JOIN ultimo_contrato_servidor_nomeacao_posse('".Sessao::getEntidade()."', ".$this->getDado('cod_periodo_movimentacao').") as contrato_servidor_nomeacao_posse";
    $stSql .= "\n         ON contrato_servidor.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato";
    $stSql .= "\n INNER JOIN ultimo_contrato_servidor_salario('".Sessao::getEntidade()."', ".$this->getDado('cod_periodo_movimentacao').") as contrato_servidor_salario";
    $stSql .= "\n         ON contrato_servidor.cod_contrato = contrato_servidor_salario.cod_contrato";
    $stSql .= "\n INNER JOIN ultimo_contrato_servidor_orgao('".Sessao::getEntidade()."', ".$this->getDado('cod_periodo_movimentacao').") as contrato_servidor_orgao";
    $stSql .= "\n         ON contrato_servidor_orgao.cod_contrato = contrato.cod_contrato";
    $stSql .= "\n INNER JOIN organograma.orgao_nivel";
    $stSql .= "\n         ON contrato_servidor_orgao.cod_orgao = orgao_nivel.cod_orgao";
    $stSql .= "\n         AND orgao_nivel.cod_nivel = publico.fn_nivel(organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, contrato_servidor_orgao.cod_orgao))";
    $stSql .= "\n INNER JOIN sw_cgm";
    $stSql .= "\n         ON servidor.numcgm = sw_cgm.numcgm";
    $stSql .= "\n INNER JOIN sw_cgm_pessoa_fisica";
    $stSql .= "\n         ON servidor.numcgm = sw_cgm_pessoa_fisica.numcgm";
    $stSql .= "\n INNER JOIN (  SELECT servidor_pis_pasep.*";
    $stSql .= "\n                 FROM pessoal.servidor_pis_pasep";
    $stSql .= "\n                     , (  SELECT cod_servidor";
    $stSql .= "\n                               , max(timestamp) as timestamp";
    $stSql .= "\n                           FROM pessoal.servidor_pis_pasep";
    $stSql .= "\n                       GROUP BY cod_servidor";
    $stSql .= "\n                       ) as max_servidor_pis_pasep";
    $stSql .= "\n                 WHERE servidor_pis_pasep.cod_servidor = max_servidor_pis_pasep.cod_servidor";
    $stSql .= "\n                   AND servidor_pis_pasep.timestamp    = max_servidor_pis_pasep.timestamp";
    $stSql .= "\n             ) as servidor_pis_pasep";
    $stSql .= "\n         ON servidor.cod_servidor = servidor_pis_pasep.cod_servidor";
    $stSql .= "\n  LEFT JOIN ultimo_contrato_servidor_local('".Sessao::getEntidade()."', ".$this->getDado('cod_periodo_movimentacao').") as contrato_servidor_local";
    $stSql .= "\n         ON contrato_servidor_local.cod_contrato = contrato_servidor.cod_contrato";
    $stSql .= "\n  LEFT JOIN pessoal.aviso_previo";
    $stSql .= "\n         ON contrato_servidor_caso_causa.cod_contrato = aviso_previo.cod_contrato";
    $stSql .= "\n  LEFT JOIN (SELECT ctps.*";
    $stSql .= "\n                   , servidor_ctps.cod_servidor";
    $stSql .= "\n               FROM pessoal.servidor_ctps";
    $stSql .= "\n                   , (  SELECT cod_servidor";
    $stSql .= "\n                             , max(dt_emissao) as dt_emissao";
    $stSql .= "\n                         FROM pessoal.servidor_ctps";
    $stSql .= "\n                             , pessoal.ctps";
    $stSql .= "\n                         WHERE servidor_ctps.cod_ctps = ctps.cod_ctps";
    $stSql .= "\n                     GROUP BY cod_servidor) as max_servidor_ctps";
    $stSql .= "\n                   , pessoal.ctps";
    $stSql .= "\n               WHERE servidor_ctps.cod_servidor = max_servidor_ctps.cod_servidor";
    $stSql .= "\n                            AND ctps.dt_emissao            = max_servidor_ctps.dt_emissao";
    $stSql .= "\n                            AND ctps.cod_ctps              = servidor_ctps.cod_ctps) as ctps";
    $stSql .= "\n         ON servidor.cod_servidor = ctps.cod_servidor";

    return $stSql;
}

function recuperaRescisaoComContratoCalculado(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY cod_contrato ";
    $stSql  = $this->montaRecuperaRescisaoComContratoCalculado().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRescisaoComContratoCalculado()
{
    $stSql .= "SELECT contrato_servidor_caso_causa.*                                                       \n";
    $stSql .= "  FROM pessoal.contrato_servidor_caso_causa                       \n";
    $stSql .= "     , folhapagamento.registro_evento_rescisao                    \n";
    $stSql .= "     , folhapagamento.evento_rescisao_calculado                   \n";
    $stSql .= " WHERE registro_evento_rescisao.cod_contrato = contrato_servidor_caso_causa.cod_contrato    \n";
    $stSql .= "   AND registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro       \n";

    return $stSql;
}

function recuperaSefipContrato(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaSefipContrato",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaSefipContrato()
{
    $stSql .= "SELECT num_sefip                                                                     \n";
    $stSql .= "  FROM pessoal.contrato_servidor_caso_causa                \n";
    $stSql .= "     , pessoal.caso_causa                                  \n";
    $stSql .= "     , pessoal.causa_rescisao                              \n";
    $stSql .= "     , pessoal.sefip                                       \n";
    $stSql .= " WHERE contrato_servidor_caso_causa.cod_caso_causa = caso_causa.cod_caso_causa       \n";
    $stSql .= "   AND caso_causa.cod_causa_rescisao = causa_rescisao.cod_causa_rescisao             \n";
    $stSql .= "   AND causa_rescisao.cod_sefip_saida = sefip.cod_sefip                              \n";

    return $stSql;
}

function recuperaCasoCausaContrato(&$rsRecordSet, $stFiltro="", $stOrdem="", $boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaCasoCausaContrato().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCasoCausaContrato()
{
     $stSQL  = " SELECT                                                                         \n";
     $stSQL .= "       causa_rescisao.cod_causa_rescisao                                        \n";
     $stSQL .= "       ,causa_rescisao.cod_sefip_saida                                          \n";
     $stSQL .= "       ,causa_rescisao.num_causa                                                \n";
     $stSQL .= "       ,causa_rescisao.descricao                                                \n";
     $stSQL .= "   FROM                                                                         \n";
     $stSQL .= "       pessoal.contrato_servidor_caso_causa                                     \n";
     $stSQL .= "       , pessoal.caso_causa                                                     \n";
     $stSQL .= "       , pessoal.causa_rescisao                                                 \n";
     $stSQL .= "  WHERE                                                                         \n";
     $stSQL .= "       contrato_servidor_caso_causa.cod_caso_causa = caso_causa.cod_caso_causa  \n";
     $stSQL .= "       AND caso_causa.cod_causa_rescisao = causa_rescisao.cod_causa_rescisao    \n";
     $stSQL .= "       AND cod_contrato = ".$this->getDado('cod_contrato')."                    \n";

     return $stSQL;
}

function recuperaCasoCausaRegistro(&$rsRecordSet, $stFiltro="", $stOrdem="", $boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaCasoCausaRegistro().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCasoCausaRegistro()
{
     $stSQL  = " SELECT                                                                         \n";
     $stSQL .= "        contrato_servidor_caso_causa.cod_caso_causa                             \n";
     $stSQL .= "       ,contrato_servidor_caso_causa.dt_rescisao                                \n";
     $stSQL .= "       ,contrato.registro                                                       \n";
     $stSQL .= "   FROM                                                                         \n";
     $stSQL .= "       pessoal.contrato_servidor_caso_causa                                     \n";
     $stSQL .= "       , pessoal.contrato                                                       \n";
     $stSQL .= "  WHERE                                                                         \n";
     $stSQL .= "       contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato        \n";

     return $stSQL;
}

}
