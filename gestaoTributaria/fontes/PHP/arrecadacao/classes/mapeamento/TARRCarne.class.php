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

    * Classe de mapeamento da tabela ARRECADACAO.CARNE
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    $Id: TARRCarne.class.php 66548 2016-09-21 13:05:07Z evandro $

    * Casos de uso: uc-05.03.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TARRCarne extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.carne');

    $this->setCampoCod('numeracao');
    $this->setComplementoChave('');

    $this->AddCampo('numeracao','varchar',true,'17',true,false);
    $this->AddCampo('exercicio','varchar',true,''  ,false,false);
    $this->AddCampo('cod_parcela','integer',true,'',false,true);
    $this->AddCampo('cod_convenio','integer',true,'',true,true);
    $this->AddCampo('cod_carteira','integer',false,'',false,true);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);
    $this->AddCampo('impresso' ,'boolean',false,'',false,false);
}

function retornaDadosCompensacao(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRetornaDadosCompensacao().$stFiltro;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRetornaDadosCompensacao()
{
    $stSQL = "
        SELECT DISTINCT convenio_ficha_compensacao.*
             , substr( agencia.num_agencia::varchar,1, char_length(agencia.num_agencia::varchar) -1) || '-' || substr( agencia.num_agencia::varchar, char_length(agencia.num_agencia::varchar), 1 ) AS agencia
             , convenio.cedente AS codigo_cedente
             , carteira.num_carteira AS carteira
          FROM monetario.convenio_ficha_compensacao
    INNER JOIN monetario.convenio
            ON convenio.cod_convenio = convenio_ficha_compensacao.cod_convenio
    INNER JOIN monetario.carteira
            ON carteira.cod_convenio = convenio.cod_convenio
    INNER JOIN monetario.credito
            ON credito.cod_convenio = convenio.cod_convenio
     LEFT JOIN arrecadacao.credito_grupo
            ON credito_grupo.cod_credito = credito.cod_credito
           AND credito_grupo.cod_especie = credito.cod_especie
           AND credito_grupo.cod_natureza = credito.cod_natureza
           AND credito_grupo.cod_genero = credito.cod_genero
    INNER JOIN monetario.conta_corrente_convenio
            ON conta_corrente_convenio.cod_convenio = convenio.cod_convenio
    INNER JOIN monetario.conta_corrente
            ON conta_corrente.cod_conta_corrente = conta_corrente_convenio.cod_conta_corrente
           AND conta_corrente.cod_agencia = conta_corrente_convenio.cod_agencia
           AND conta_corrente.cod_banco = conta_corrente_convenio.cod_banco
    INNER JOIN monetario.agencia
            ON agencia.cod_banco = conta_corrente.cod_banco
           AND agencia.cod_agencia = conta_corrente.cod_agencia

    ";

    return $stSQL;
}

function selecionaCarne(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaSelecionaCarne().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaSelecionaCarne()
{
        $stSql =  "select                                                                         			\n";
        $stSql .=  "    al.cod_lancamento,                                                                	\n";
        $stSql .=  "    ap.cod_parcela,                                                                     \n";
        $stSql .=  "    ac.exercicio                                                                        \n";
        $stSql .=  "from                                                                                    \n";
        $stSql .=  "    arrecadacao.lancamento as al,                                               		\n";
        $stSql .=  "    arrecadacao.parcela as ap,                                                     		\n";
        $stSql .=  "    arrecadacao.lancamento_calculo as alc,                                  			\n";
        $stSql .=  "    sw_cgm                         as cgm,                                              \n";
        $stSql .=  "    arrecadacao.calculo as ac                                                        	\n";
        $stSql .=  "inner join arrecadacao.calculo_cgm acgm                                    				\n";
        $stSql .=  "    on acgm.cod_calculo = ac.cod_calculo                                     			\n";
        $stSql .=  "left join                                                                               \n";
        $stSql .=  "    monetario.credito as mc                                                           	\n";
        $stSql .=  "on                                                                                      \n";
        $stSql .=  "    mc.cod_credito     = ac.cod_credito                                           		\n";
        $stSql .=  "left join                                                                               \n";
        $stSql .=  "    (                                                                                   \n";
        $stSql .=  "        select                                                                          \n";
        $stSql .=  "            agv.cod_grupo,                                                              \n";
        $stSql .=  "            acgc.cod_calculo,                                                           \n";
        $stSql .=  "            agv.cod_vencimento                                                          \n";
        $stSql .=  "        from                                                                            \n";
        $stSql .=  "            arrecadacao.calculo_grupo_credito as acgc,                                  \n";
        $stSql .=  "            arrecadacao.calculo  as ac,                                                 \n";
        $stSql .=  "            arrecadacao.grupo_vencimento as agv                                         \n";
        $stSql .=  "        where                                                                           \n";
        $stSql .=  "            ac.cod_calculo      =  acgc.cod_calculo   and                               \n";
        $stSql .=  "            acgc.ano_exercicio =  ac.exercicio and                                      \n";
        $stSql .=  "            acgc.cod_grupo      =  agv.cod_grupo                                        \n";
        $stSql .=  "    ) macg                                                                              \n";
        $stSql .=  "on macg.cod_calculo    = ac.cod_calculo                                                 \n";
        $stSql .=  "left join                                                                               \n";
        $stSql .=  "    (select                                                                             \n";
        $stSql .=  "        ii.inscricao_municipal,                                                         \n";
        $stSql .=  "        aic.cod_calculo,                                                                \n";
        $stSql .=  "        il.codigo_composto,                                                             \n";
        $stSql .=  "        il.cod_localizacao                                                              \n";
        $stSql .=  "     from                                                                                               \n";
        $stSql .=  "        arrecadacao.imovel_calculo     as aic,                                         \n";
        $stSql .=  "        imobiliario.imovel             as ii,                                                     \n";
        $stSql .=  "        imobiliario.imovel_lote        as iil,                                                  \n";
        $stSql .=  "        imobiliario.lote_localizacao   as ill,                                                \n";
        $stSql .=  "        imobiliario.localizacao        as il                                                     \n";
        $stSql .=  "     where                                                                                               \n";
        $stSql .=  "        aic.inscricao_municipal = ii.inscricao_municipal  and                    \n";
        $stSql .=  "        ii.inscricao_municipal  = iil.inscricao_municipal and                       \n";
        $stSql .=  "        iil.cod_lote            = ill.cod_lote            and                                   \n";
        $stSql .=  "        ill.cod_localizacao     = il.cod_localizacao                                       \n";
        $stSql .=  "    )as aii                                                                                                \n";
        $stSql .=  "on aii.cod_calculo = ac.cod_calculo                                                      \n";
        $stSql .=  "left join                                                                                                  \n";
        $stSql .=  "    (select                                                                                               \n";
        $stSql .=  "        ece.inscricao_economica,                                                             \n";
        $stSql .=  "        acec.cod_calculo,                                                                          \n";
        $stSql .=  "        ea.cod_atividade,                                                                          \n";
        $stSql .=  "        ea.cod_estrutural                                                                          \n";
        $stSql .=  "     from                                                                                                  \n";
        $stSql .=  "        arrecadacao.cadastro_economico_calculo as acec,                      \n";
        $stSql .=  "        economico.cadastro_economico   as ece,                                     \n";
        $stSql .=  "        economico.atividade_cadastro_economico as eace,                     \n";
        $stSql .=  "        economico.atividade                    as ea                                         \n";
        $stSql .=  "     where                                                                                               \n";
        $stSql .=  "        acec.inscricao_economica = ece.inscricao_economica and          \n";
        $stSql .=  "        ece.inscricao_economica  = eace.inscricao_economica and         \n";
        $stSql .=  "        eace.cod_atividade       = ea.cod_atividade                                   \n";
        $stSql .=  "    )as aece                                                                                            \n";
        $stSql .=  "on aece.cod_calculo = ac.cod_calculo                                                   \n";
        $stSql .=  "where                                                                                                     \n";
        $stSql .=  "    ac.cod_calculo     = alc.cod_calculo   and                                          \n";
        $stSql .=  "    alc.cod_lancamento = al.cod_lancamento and                                  \n";
        $stSql .=  "    ap.cod_lancamento  = al.cod_lancamento and                                 \n";
        $stSql .=  "    acgm.numcgm        = cgm.numcgm                                                   \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function selecionaReemitirCarne(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaSelecionaReemitirCarne().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaCalculoParcela(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaCalculoParcela().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaCalculoParcela()
{
    $stSql ="  SELECT                                                                              \n";
    $stSql .="      ac.cod_calculo                                                                  \n";
    $stSql .="  FROM                                                                                \n";
    $stSql .="      arrecadacao.calculo as ac                                                       \n";
    $stSql .="      INNER JOIN arrecadacao.lancamento_calculo as alc                                \n";
    $stSql .="      ON alc.cod_calculo = ac.cod_calculo                                             \n";
    $stSql .="      INNER JOIN arrecadacao.lancamento as al                                         \n";
    $stSql .="      ON al.cod_lancamento = alc.cod_lancamento                                       \n";
    $stSql .="      INNER JOIN arrecadacao.parcela as ap                                            \n";
    $stSql .="      ON ap.cod_lancamento = al.cod_lancamento                                        \n";

    return $stSql;

}

function montaSelecionaReemitirCarne()
{
    $stSql =  "select                                                                                                   \n";
    $stSql .=  "    ac.cod_calculo,                                                                                \n";
    $stSql .=  "    al.cod_lancamento,                                                                         \n";
    $stSql .=  "    ap.cod_parcela,                                                                               \n";
    $stSql .=  "    mc.cod_credito,                                                                               \n";
    $stSql .=  "    macg.cod_grupo,                                                                             \n";
    $stSql .=  "    aii.inscricao_municipal,                                                                    \n";
    $stSql .=  "    aii.codigo_composto,                                                                       \n";
    $stSql .=  "    aii.cod_localizacao,                                                                          \n";
    $stSql .=  "    aece.inscricao_economica,                                                             \n";
    $stSql .=  "    aece.cod_atividade,                                                                        \n";
    $stSql .=  "    acn.cod_convenio,                                                                          \n";
    $stSql .=  "    acn.exercicio,                                                                                  \n";
    $stSql .=  "    acn.cod_carteira,                                                                            \n";
    $stSql .=  "    cgm.nom_cgm                                                                                 \n";
    $stSql .=  "from                                                                                                    \n";
    $stSql .=  "    arrecadacao.lancamento as al,                                                      \n";
    $stSql .=  "    arrecadacao.parcela as ap,                                                            \n";
    $stSql .=  "    arrecadacao.lancamento_calculo as alc,                                        \n";
    $stSql .=  "    sw_cgm                         as cgm,                                                      \n";
    $stSql .=  "    (select                                                                                             \n";
    $stSql .=  "        max(timestamp),                                                                         \n";
    $stSql .=  "        cod_parcela,                                                                               \n";
    $stSql .=  "        cod_convenio,                                                                             \n";
    $stSql .=  "        exercicio,                                                                                     \n";
    $stSql .=  "        cod_carteira                                                                                \n";
    $stSql .=  "     from                                                                                                \n";
    $stSql .=  "        arrecadacao.carne                                                                      \n";
    $stSql .=  "     group by                                                                                         \n";
    $stSql .=  "        cod_parcela,cod_convenio,cod_carteira,exercicio                        \n";
    $stSql .=  "    ) as acn,                                                                                           \n";
    $stSql .=  "    arrecadacao.calculo as ac                                                               \n";
    $stSql .=  "left join                                                                                                 \n";
    $stSql .=  "    monetario.credito as mc                                                                   \n";
    $stSql .=  "on                                                                                                         \n";
    $stSql .=  "    mc.cod_credito     = ac.cod_credito                                                  \n";
    $stSql .=  "left join                                                                                                  \n";
    $stSql .=  "    (                                                                                                        \n";
    $stSql .=  "        select                                                                                            \n";
    $stSql .=  "            agv.cod_grupo,                                                                          \n";
    $stSql .=  "            acgc.cod_calculo,                                                                      \n";
    $stSql .=  "            agv.cod_vencimento                                                                  \n";
    $stSql .=  "        from                                                                                               \n";
    $stSql .=  "            arrecadacao.calculo_grupo_credito as acgc,                            \n";
    $stSql .=  "            arrecadacao.calculo                  as ac,                                       \n";
    $stSql .=  "            arrecadacao.grupo_vencimento         as agv                             \n";
    $stSql .=  "        where                                                                                             \n";
    $stSql .=  "            ac.cod_calculo      =  acgc.cod_calculo   and                             \n";
    $stSql .=  "            acgc.cod_vencimento = agv.cod_vencimento and                     \n";
    $stSql .=  "            acgc.ano_exercicio =  ac.exercicio and                         \n";
    $stSql .=  "            acgc.cod_grupo      =  agv.cod_grupo                                        \n";
    $stSql .=  "    ) macg                                                                                                \n";
    $stSql .=  "on                                                                                                           \n";
    $stSql .=  "    macg.cod_calculo    = ac.cod_calculo                                                 \n";
    $stSql .=  "left join                                                                                                    \n";
    $stSql .=  "    (select                                                                                                 \n";
    $stSql .=  "        ii.inscricao_municipal,                                                                      \n";
    $stSql .=  "        aic.cod_calculo,                                                                               \n";
    $stSql .=  "        il.codigo_composto,                                                                         \n";
    $stSql .=  "        il.cod_localizacao                                                                             \n";
    $stSql .=  "     from                                                                                                    \n";
    $stSql .=  "        arrecadacao.imovel_calculo     as aic,                                             \n";
    $stSql .=  "        imobiliario.imovel             as ii,                                                         \n";
    $stSql .=  "        imobiliario.imovel_lote        as iil,                                                      \n";
    $stSql .=  "        imobiliario.lote_localizacao   as ill,                                                    \n";
    $stSql .=  "        imobiliario.localizacao        as il                                                        \n";
    $stSql .=  "     where                                                                                                 \n";
    $stSql .=  "        aic.inscricao_municipal = ii.inscricao_municipal  and                       \n";
    $stSql .=  "        ii.inscricao_municipal  = iil.inscricao_municipal and                         \n";
    $stSql .=  "        iil.cod_lote            = ill.cod_lote            and                                      \n";
    $stSql .=  "        ill.cod_localizacao     = il.cod_localizacao                                          \n";
    $stSql .=  "    )as aii                                                                                                   \n";
    $stSql .=  "on                                                                                                             \n";
    $stSql .=  "    aii.cod_calculo = ac.cod_calculo                                                           \n";
    $stSql .=  "left join                                                                                                      \n";
    $stSql .=  "    (select                                                                                                   \n";
    $stSql .=  "        ece.inscricao_economica,                                                                  \n";
    $stSql .=  "        acec.cod_calculo,                                                                               \n";
    $stSql .=  "        ea.cod_atividade,                                                                               \n";
    $stSql .=  "        ea.cod_estrutural                                                                               \n";
    $stSql .=  "     from                                                                                                       \n";
    $stSql .=  "        arrecadacao.cadastro_economico_calculo as acec,                           \n";
    $stSql .=  "        economico.cadastro_economico   as ece,                                          \n";
    $stSql .=  "        economico.atividade_cadastro_economico as eace,                           \n";
    $stSql .=  "        economico.atividade                    as ea                                               \n";
    $stSql .=  "     where                                                                                                      \n";
    $stSql .=  "        acec.inscricao_economica = ece.inscricao_economica and                 \n";
    $stSql .=  "        ece.inscricao_economica  = eace.inscricao_economica and                \n";
    $stSql .=  "        eace.cod_atividade       = ea.cod_atividade                                          \n";
    $stSql .=  "    )as aece                                                                                                    \n";
    $stSql .=  "on aece.cod_calculo = ac.cod_calculo                                                          \n";
    $stSql .=  "where                                                                                                            \n";
    $stSql .=  "    ac.cod_calculo     = alc.cod_calculo   and                                                 \n";
    $stSql .=  "    alc.cod_lancamento = al.cod_lancamento and                                         \n";
    $stSql .=  "    ap.cod_lancamento  = al.cod_lancamento and                                         \n";
    $stSql .=  "    ac.numcgm          = cgm.numcgm        and                                               \n";
    $stSql .=  "    ap.cod_parcela     = acn.cod_parcela                                                       \n";

    return $stSql;
}

function geraReemitirCarne(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaGeraReemitirCarne().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
/*****************************************
// para impressao no pdf / IPTU
*****************************************/
function montaGeraReemitirCarne()
{
;
$stSql = "    select                                     \n";
$stSql .= "           ii.inscricao_municipal,             \n";
$stSql .= " (
                SELECT
                    ic.nom_condominio

                FROM
                    imobiliario.imovel_condominio AS iic

                INNER JOIN
                    imobiliario.condominio AS ic
                ON
                    ic.cod_condominio = iic.cod_condominio

                WHERE
                    iic.inscricao_municipal = ii.inscricao_municipal
           )AS condominio,
           (
                SELECT
                    COALESCE(
                        (
                            SELECT valor from
                                imobiliario.atributo_lote_urbano_valor as ialu
                            WHERE ialu.cod_atributo = 7
                            AND  ialu.cod_lote = il.cod_lote
                            ORDER BY ialu.timestamp DESC limit 1
                        ),
                        (
                            SELECT valor from
                                imobiliario.atributo_lote_rural_valor as ialr
                            WHERE ialr.cod_atributo = 7
                            AND  ialr.cod_lote = il.cod_lote
                            ORDER BY ialr.timestamp DESC limit 1
                        )
                    )::varchar AS valor
                FROM
                    imobiliario.lote as il

                WHERE il.cod_lote = iic.cod_lote
           ) AS numero_lote,
           (
                SELECT
                    COALESCE(
                        (
                            SELECT valor from
                                imobiliario.atributo_lote_urbano_valor as ialu
                            WHERE ialu.cod_atributo = 5
                            AND  ialu.cod_lote = il.cod_lote
                            ORDER BY ialu.timestamp DESC limit 1
                        ),
                        (
                            SELECT valor from
                                imobiliario.atributo_lote_rural_valor as ialr
                            WHERE ialr.cod_atributo = 5
                            AND  ialr.cod_lote = il.cod_lote
                            ORDER BY ialr.timestamp DESC limit 1
                        )
                    )::varchar AS valor
                FROM
                    imobiliario.lote as il

                WHERE il.cod_lote = iic.cod_lote
           ) AS numero_quadra, \n";

$stSql .= "           (  \n";
$stSql .= "             SELECT \n";
$stSql .= "               tmp.aliquota_valor_avaliado \n";
$stSql .= "             FROM \n";
$stSql .= "               arrecadacao.imovel_v_venal AS tmp \n";
$stSql .= "             WHERE \n";
$stSql .= "               tmp.inscricao_municipal = ii.inscricao_municipal \n";
$stSql .= "                 AND tmp.timestamp = aic.timestamp \n";
$stSql .= "             LIMIT 1 \n";
$stSql .= "           )AS aliquota, \n";

$stSql .= "           ii.numero,                                                                                                                                             \n";
$stSql .= "           ii.cep,                                                                                                                                                   \n";
$stSql .= "           ii.complemento,                                                                                                                                    \n";
$stSql .= "           ac.cod_calculo,                                                                                                                                     \n";
$stSql .= "           ac.exercicio,                                                                                                                                         \n";
$stSql .= "           il.codigo_composto,                                                                                                                              \n";
$stSql .= "           il.cod_localizacao,                                                                                                                                 \n";
$stSql .= "           tl.nom_tipo||' '||nl.nom_logradouro as nom_logradouro,                                                                        \n";
$stSql .= "           uf.sigla_uf,                                                                                                                                             \n";
$stSql .= "           mu.nom_municipio,                                                                                                                                \n";
$stSql .= "           ict.cod_logradouro,                                                                                                                               \n";
$stSql .= "           ba.nom_bairro,                                                                                                                                      \n";
$stSql .= "           al.observacao,                                                                                                                                        \n";
$stSql .= "           imobiliario.fn_calcula_area_imovel_construcao(ii.inscricao_municipal) as area_edificada,                       \n";
$stSql .= "           tl.nom_tipo,                                                                                                                                             \n";
$stSql .= "           ial.area_real,                                                                                                                                            \n";
$stSql .= "           ARRECADACAO.FN_BUSCA_VALOR_AVALIADO_ITBI(ii.inscricao_municipal ) as valor_venal_total,             \n";
$stSql .= "           to_char(now(),'dd/mm/yyyy') as data_processamento,                                                                             \n";
$stSql .= "           case when acgc.descricao is not null then                                                                                              \n";
$stSql .= "               acgc.descricao                                                                                                                                    \n";
$stSql .= "           else mc.descricao_credito                                                                                                                       \n";
$stSql .= "           end as descricao,                                                                                                                                    \n";
$stSql .= "           case when acgc.exercicio is not null then                                                                                                         \n";
$stSql .= "               acgc.exercicio                                                                                                                                \n";
$stSql .= "           else ac.exercicio                                                                                                                                 \n";
$stSql .= "           end as exercicio_credito,                                                                                                                         \n";
$stSql .= "           case when acgc.cod_grupo is not null then                                                                                             \n";
$stSql .= "               acgc.cod_grupo::varchar                                                                                                                     \n";
$stSql .= "           else mc.cod_credito||'.'||mc.cod_especie||'.'||mc.cod_genero||'.'||mc.cod_natureza                                  \n";
$stSql .= "           end as cod_grupo,                                                                                                                                   \n";
$stSql .= "           mc.descricao_credito,                                                                                                                              \n";
$stSql .= "           mc.cod_credito,                                                                                                                                        \n";
$stSql .= "           ac.valor,                                                                                                                                                    \n";
$stSql .= "           al.valor as valor_lancado,                                                                                                                                   \n";
$stSql .= "           case when ( select count(1) from arrecadacao.calculo_cgm where cod_calculo = ac.cod_calculo) > 1 then                                                        \n";
$stSql .= "                    cgm.nom_cgm::varchar||' E OUTROS'                                                                                                                            \n";
$stSql .= "           else     cgm.nom_cgm::varchar                                                                                                                                \n";
$stSql .= "           end as nom_cgm,                                                                                                                                              \n";
$stSql .= "           cgm.numcgm,                                                                                                                                           \n";
$stSql .= "           case when sw_cgm_pessoa_fisica.cpf != '' then sw_cgm_pessoa_fisica.cpf else  sw_cgm_pessoa_juridica.cnpj end as cpf_cnpj,                                                                             \n";
$stSql .= "           array_to_string(arrecadacao.fn_busca_endereco_carne(aic.inscricao_municipal,ac.exercicio::int, al.cod_lancamento),'|*|') as enderecoEntrega,                              \n";
$stSql .= "           case when sw_cgm_pessoa_fisica is null then                                                                                                               \n";
$stSql .= "                publico.mascara_cpf_cnpj(sw_cgm_pessoa_juridica.cnpj, 'CNPJ')                                                                                                              \n";
$stSql .= "           else                                                                                                                                                      \n";
$stSql .= "                publico.mascara_cpf_cnpj(sw_cgm_pessoa_fisica.cpf, 'CPF')                                                                                                                             \n";
$stSql .= "           end as documento                                                                                                                                          \n";
$stSql .= "     from                                                                                                                                                                \n";
$stSql .= "         arrecadacao.calculo             as ac                                                                                                              \n";
$stSql .= "         INNER JOIN monetario.credito as mc                          ON mc.cod_credito = ac.cod_credito                         \n";
$stSql .= "         INNER JOIN arrecadacao.calculo_cgm as acgm          ON acgm.cod_calculo = ac.cod_calculo                     \n";
$stSql .= "         INNER JOIN sw_cgm as cgm                                        ON cgm.numcgm = acgm.numcgm                            \n";
$stSql .= "         INNER JOIN arrecadacao.lancamento_calculo as alc  ON alc.cod_calculo = ac.cod_calculo                         \n";
$stSql .= "         INNER JOIN arrecadacao.lancamento as al                ON al.cod_lancamento = alc.cod_lancamento           \n";
$stSql .= "         INNER JOIN arrecadacao.imovel_calculo as aic          ON aic.cod_calculo = ac.cod_calculo                          \n";
$stSql .= "         INNER JOIN imobiliario.imovel as ii                              ON ii.inscricao_municipal = aic.inscricao_municipal    \n";
$stSql .= "         INNER JOIN imobiliario.imovel_lote as iil                     ON ii.inscricao_municipal = iil.inscricao_municipal       \n";
$stSql .= "         INNER JOIN imobiliario.lote_localizacao as ill              ON ill.cod_lote = iil.cod_lote                                        \n";
$stSql .= "         INNER JOIN imobiliario.localizacao as il                      ON il.cod_localizacao = ill.cod_localizacao                   \n";
$stSql .= "         INNER JOIN imobiliario.imovel_confrontacao as iic     ON iic.inscricao_municipal = ii.inscricao_municipal      \n";
$stSql .= "         INNER JOIN imobiliario.area_lote as ial                      ON ial.cod_lote = iil.cod_lote                                       \n";

$stSql .= "         LEFT JOIN                                                                                                                                                     \n";
$stSql .= "          ( select agc.descricao, agc.cod_grupo, acgc.cod_calculo, agc.ano_exercicio as exercicio                                                                        \n";
$stSql .= "             from arrecadacao.grupo_credito as agc                                                                                                \n";
$stSql .= "             INNER JOIN  arrecadacao.calculo_grupo_credito as acgc                                                                        \n";
$stSql .= "             ON acgc.cod_grupo = agc.cod_grupo AND acgc.ano_exercicio = agc.ano_exercicio                              \n";
$stSql .= "          ) as acgc ON acgc.cod_calculo = alc.cod_calculo                                                                                     \n";
$stSql .= "         inner join imobiliario.confrontacao_trecho ict                                                                                            \n";
$stSql .= "                 on iic.cod_confrontacao    = ict.cod_confrontacao                                                                            \n";
$stSql .= "                and iic.cod_lote            = ict.cod_lote                                                                                                \n";
$stSql .= "         inner join sw_nome_logradouro nl                                                                                                             \n";
$stSql .= "                 on ict.cod_logradouro      = nl.cod_logradouro                                                                                 \n";
$stSql .= "         inner join sw_tipo_logradouro tl                                                                                                                \n";
$stSql .= "                 on nl.cod_tipo             = tl.cod_tipo                                                                                                  \n";
$stSql .= "         inner join sw_logradouro lo                                                                                                                       \n";
$stSql .= "                 on ict.cod_logradouro      = lo.cod_logradouro                                                                                 \n";
$stSql .= "         inner join imobiliario.lote_bairro ilb                                                                                                           \n";
$stSql .= "                 on iil.cod_lote = ilb.cod_lote                                                                                                             \n";
$stSql .= "         inner join sw_uf uf                                                                                                                                     \n";
$stSql .= "                 on ilb.cod_uf              = uf.cod_uf                                                                                                     \n";
$stSql .= "         inner join sw_municipio mu                                                                                                                       \n";
$stSql .= "                 on mu.cod_uf               = uf.cod_uf                                                                                                   \n";
$stSql .= "         inner join sw_bairro ba                                                                                                                             \n";
$stSql .= "                 on ilb.cod_municipio       = mu.cod_municipio                                                                                  \n";
$stSql .= "                and ilb.cod_bairro          = ba.cod_bairro                                                                                          \n";
$stSql .= "          left join sw_cgm_pessoa_fisica                                                                                                                     \n";
$stSql .= "                 ON cgm.numcgm = sw_cgm_pessoa_fisica.numcgm                                                                                                 \n";
$stSql .= "          left join sw_cgm_pessoa_juridica                                                                                                                   \n";
$stSql .= "                 ON cgm.numcgm = sw_cgm_pessoa_juridica.numcgm                                                                                               \n";
$stSql .= "    where                                                                                                                                                           \n";
$stSql .= "          cgm.numcgm  is not null                                                                                                                         \n";

   return $stSql;

}

function geraReemitirCarneManaquiri(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaGeraReemitirCarneManaquiri().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
/*****************************************
// para impressao no pdf / IPTU
*****************************************/
function montaGeraReemitirCarneManaquiri()
{
$stSql = "    select                                     \n";
$stSql .= "           ii.inscricao_municipal,             \n";
$stSql .= " (
                SELECT
                    ic.nom_condominio

                FROM
                    imobiliario.imovel_condominio AS iic

                INNER JOIN
                    imobiliario.condominio AS ic
                ON
                    ic.cod_condominio = iic.cod_condominio

                WHERE
                    iic.inscricao_municipal = ii.inscricao_municipal
           )AS condominio,
           (
                SELECT
                    COALESCE(
                        (
                            SELECT valor from
                                imobiliario.atributo_lote_urbano_valor as ialu
                            WHERE ialu.cod_atributo = 7
                            AND  ialu.cod_lote = il.cod_lote
                            ORDER BY ialu.timestamp DESC limit 1
                        ),
                        (
                            SELECT valor from
                                imobiliario.atributo_lote_rural_valor as ialr
                            WHERE ialr.cod_atributo = 7
                            AND  ialr.cod_lote = il.cod_lote
                            ORDER BY ialr.timestamp DESC limit 1
                        )
                    )::varchar AS valor
                FROM
                    imobiliario.lote as il

                WHERE il.cod_lote = iic.cod_lote
           ) AS numero_lote,
           (
                SELECT
                    COALESCE(
                        (
                            SELECT valor from
                                imobiliario.atributo_lote_urbano_valor as ialu
                            WHERE ialu.cod_atributo = 5
                            AND  ialu.cod_lote = il.cod_lote
                            ORDER BY ialu.timestamp DESC limit 1
                        ),
                        (
                            SELECT valor from
                                imobiliario.atributo_lote_rural_valor as ialr
                            WHERE ialr.cod_atributo = 5
                            AND  ialr.cod_lote = il.cod_lote
                            ORDER BY ialr.timestamp DESC limit 1
                        )
                    )::varchar AS valor
                FROM
                    imobiliario.lote as il

                WHERE il.cod_lote = iic.cod_lote
           ) AS numero_quadra,

(
             SELECT
               tmp.valor
             FROM
                arrecadacao.tabela_conversao_valores AS tmp
             WHERE
                CASE WHEN arrecadacao.verificaEdificacaoImovel(ii.inscricao_municipal) = TRUE THEN
                        tmp.parametro_1 = 'true'
                ELSE
                        tmp.parametro_2 = 'true'
                END
                AND tmp.cod_tabela = 1
                AND tmp.exercicio = ac.exercicio
             LIMIT 1
           )AS aliquota, \n";

$stSql .= "           ii.numero,                                                                                                                                             \n";
$stSql .= "           publico.fn_mascara_completa ('99999-999', ii.cep) AS cep,                                                                                                                                                   \n";
$stSql .= "           ii.complemento,                                                                                                                                    \n";
$stSql .= "           ac.cod_calculo,                                                                                                                                     \n";
$stSql .= "           ac.exercicio,                                                                                                                                         \n";
$stSql .= "           il.codigo_composto,                                                                                                                              \n";
$stSql .= "           il.cod_localizacao,                                                                                                                                 \n";
$stSql .= "           tl.nom_tipo||' '||nl.nom_logradouro as nom_logradouro,                                                                        \n";
$stSql .= "           uf.sigla_uf,                                                                                                                                             \n";
$stSql .= "           mu.nom_municipio,                                                                                                                                \n";
$stSql .= "           ict.cod_logradouro,                                                                                                                               \n";
$stSql .= "           ba.nom_bairro,                                                                                                                                      \n";
$stSql .= "           al.observacao,                                                                                                                                        \n";
$stSql .= "           imobiliario.fn_calcula_area_imovel_construcao(ii.inscricao_municipal) as area_edificada,                       \n";
$stSql .= "           tl.nom_tipo,                                                                                                                                             \n";
$stSql .= "           ial.area_real,                                                                                                                                            \n";
$stSql .= "           ARRECADACAO.FN_BUSCA_VALOR_CALCULADO_ITBI(ii.inscricao_municipal ) as valor_venal_total,             \n";
$stSql .= "           to_char(now(),'dd/mm/yyyy') as data_processamento,                                                                             \n";
$stSql .= "           case when acgc.descricao is not null then                                                                                              \n";
$stSql .= "               acgc.descricao                                                                                                                                    \n";
$stSql .= "           else mc.descricao_credito                                                                                                                       \n";
$stSql .= "           end as descricao,                                                                                                                                    \n";
$stSql .= "           case when acgc.cod_grupo is not null then                                                                                             \n";
$stSql .= "               acgc.cod_grupo::varchar                                                                                                                     \n";
$stSql .= "           else mc.cod_credito||'.'||mc.cod_especie||'.'||mc.cod_genero||'.'||mc.cod_natureza                                  \n";
$stSql .= "           end as cod_grupo,                                                                                                                                   \n";
$stSql .= "           mc.descricao_credito,                                                                                                                              \n";
$stSql .= "           mc.cod_credito,                                                                                                                                        \n";
$stSql .= "           ac.valor,                                                                                                                                                    \n";
$stSql .= "           al.valor as valor_lancado,                                                                                                                                   \n";
$stSql .= "           case when ( select count(1) from arrecadacao.calculo_cgm where cod_calculo = ac.cod_calculo) > 1 then                                                        \n";
$stSql .= "                    cgm.nom_cgm::varchar||' E OUTROS'                                                                                                                            \n";
$stSql .= "           else     cgm.nom_cgm::varchar                                                                                                                                \n";
$stSql .= "           end as nom_cgm,                                                                                                                                              \n";
$stSql .= "           case when pfcgm.cpf != '' then                                                                                                               \n";
$stSql .= "                publico.mascara_cpf_cnpj(pfcgm.cpf, 'CPF')                                                                                                              \n";
$stSql .= "           else                                                                                                                                                      \n";
$stSql .= "                publico.mascara_cpf_cnpj(pjcgm.cnpj, 'CNPJ')                                                                                                                             \n";
$stSql .= "           end as cpf_cnpj,                                                                                                                                \n";
$stSql .= "           cgm.numcgm,                                                                                                                                           \n";
$stSql .= "           array_to_string(arrecadacao.fn_busca_endereco_carne(aic.inscricao_municipal,ac.exercicio::int),'|*|') as enderecoEntrega                              \n";
$stSql .= "     from                                                                                                                                                                \n";
$stSql .= "         arrecadacao.calculo             as ac                                                                                                              \n";
$stSql .= "         INNER JOIN monetario.credito as mc                          ON mc.cod_credito = ac.cod_credito                         \n";
$stSql .= "         INNER JOIN arrecadacao.calculo_cgm as acgm          ON acgm.cod_calculo = ac.cod_calculo                     \n";
$stSql .= "         INNER JOIN sw_cgm as cgm                                        ON cgm.numcgm = acgm.numcgm                            \n";

$stSql .= "         LEFT JOIN sw_cgm_pessoa_fisica as pfcgm    ON pfcgm.numcgm = cgm.numcgm                                           \n";
$stSql .= "         LEFT JOIN sw_cgm_pessoa_juridica as pjcgm    ON pjcgm.numcgm = cgm.numcgm                  \n";

$stSql .= "         INNER JOIN arrecadacao.lancamento_calculo as alc  ON alc.cod_calculo = ac.cod_calculo                         \n";
$stSql .= "         INNER JOIN arrecadacao.lancamento as al                ON al.cod_lancamento = alc.cod_lancamento           \n";
$stSql .= "         INNER JOIN arrecadacao.imovel_calculo as aic          ON aic.cod_calculo = ac.cod_calculo                          \n";
$stSql .= "         INNER JOIN imobiliario.imovel as ii                              ON ii.inscricao_municipal = aic.inscricao_municipal    \n";
$stSql .= "         INNER JOIN imobiliario.imovel_lote as iil                     ON ii.inscricao_municipal = iil.inscricao_municipal       \n";
$stSql .= "         INNER JOIN imobiliario.lote_localizacao as ill              ON ill.cod_lote = iil.cod_lote                                        \n";
$stSql .= "         INNER JOIN imobiliario.localizacao as il                      ON il.cod_localizacao = ill.cod_localizacao                   \n";
$stSql .= "         INNER JOIN imobiliario.imovel_confrontacao as iic     ON iic.inscricao_municipal = ii.inscricao_municipal      \n";
$stSql .= "         INNER JOIN imobiliario.area_lote as ial                      ON ial.cod_lote = iil.cod_lote                      \n";
$stSql .= "                AND ial.timestamp = ( SELECT max(timestamp) FROM imobiliario.area_lote WHERE cod_lote = iil.cod_lote)     \n";

$stSql .= "         LEFT JOIN                                                                                                                                                     \n";
$stSql .= "          ( select agc.descricao, agc.cod_grupo, acgc.cod_calculo                                                                         \n";
$stSql .= "             from arrecadacao.grupo_credito as agc                                                                                                \n";
$stSql .= "             INNER JOIN  arrecadacao.calculo_grupo_credito as acgc                                                                        \n";
$stSql .= "             ON acgc.cod_grupo = agc.cod_grupo AND acgc.ano_exercicio = agc.ano_exercicio                              \n";
$stSql .= "          ) as acgc ON acgc.cod_calculo = alc.cod_calculo                                                                                     \n";
$stSql .= "         inner join imobiliario.confrontacao_trecho ict                                                                                            \n";
$stSql .= "                 on iic.cod_confrontacao    = ict.cod_confrontacao                                                                            \n";
$stSql .= "                and iic.cod_lote            = ict.cod_lote                                                                                                \n";
$stSql .= "         inner join sw_nome_logradouro nl                                                                                                             \n";
$stSql .= "                 on ict.cod_logradouro      = nl.cod_logradouro                                                                                 \n";
$stSql .= "         inner join sw_tipo_logradouro tl                                                                                                                \n";
$stSql .= "                 on nl.cod_tipo             = tl.cod_tipo                                                                                                  \n";
$stSql .= "         inner join sw_logradouro lo                                                                                                                       \n";
$stSql .= "                 on ict.cod_logradouro      = lo.cod_logradouro                                                                                 \n";
$stSql .= "         inner join imobiliario.lote_bairro ilb                                                                                                           \n";
$stSql .= "                 on iil.cod_lote = ilb.cod_lote                                                                                                             \n";
$stSql .= "         inner join sw_uf uf                                                                                                                                     \n";
$stSql .= "                 on ilb.cod_uf              = uf.cod_uf                                                                                                     \n";
$stSql .= "         inner join sw_municipio mu                                                                                                                       \n";
$stSql .= "                 on mu.cod_uf               = uf.cod_uf                                                                                                   \n";
$stSql .= "         inner join sw_bairro ba                                                                                                                             \n";
$stSql .= "                 on ilb.cod_municipio       = mu.cod_municipio                                                                                  \n";
$stSql .= "                and ilb.cod_bairro          = ba.cod_bairro                                                                                          \n";
$stSql .= "    where                                                                                                                                                           \n";
$stSql .= "          cgm.numcgm  is not null                                                                                                                         \n";

   return $stSql;

}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaObservacaoAlvaraConstrucaoManaquiri(&$rsRecordSet, $stFiltro, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaObservacaoAlvaraConstrucaoManaquiri( $stFiltro );
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaObservacaoAlvaraConstrucaoManaquiri($stFiltro)
{
    $stSql = "  SELECT licenca.cod_licenca
                     , licenca_imovel_area.area
                    , CASE WHEN atributo_construcao.valor_padrao != '' THEN
                        atributo_construcao.valor_padrao
                      ELSE
                        atributo_edificacao.valor_padrao
                      END AS tabela

                     FROM imobiliario.licenca

                     JOIN imobiliario.licenca_imovel
                       ON licenca_imovel.cod_licenca = licenca.cod_licenca
                      AND licenca_imovel.exercicio   = licenca.exercicio

                     JOIN imobiliario.licenca_imovel_area
                       ON licenca_imovel_area.cod_licenca         = licenca_imovel.cod_licenca
                      AND licenca_imovel_area.exercicio           = licenca_imovel.exercicio
                      AND licenca_imovel_area.inscricao_municipal = licenca_imovel.inscricao_municipal
--- Construção
                LEFT JOIN imobiliario.licenca_imovel_nova_construcao
                       ON licenca_imovel_nova_construcao.cod_licenca 	     = licenca_imovel.cod_licenca
                      AND licenca_imovel_nova_construcao.inscricao_municipal = licenca_imovel.inscricao_municipal
                      AND licenca_imovel_nova_construcao.exercicio           = licenca_imovel.exercicio

                LEFT JOIN imobiliario.atributo_construcao_outros_valor
                       ON atributo_construcao_outros_valor.cod_construcao = licenca_imovel_nova_construcao.cod_construcao

                LEFT JOIN administracao.atributo_valor_padrao AS atributo_construcao
                       ON atributo_construcao.cod_atributo = atributo_construcao_outros_valor.cod_atributo
                      AND atributo_construcao.cod_valor    = atributo_construcao_outros_valor.valor
--- Edificação
                LEFT JOIN imobiliario.licenca_imovel_nova_edificacao
                       ON licenca_imovel_nova_edificacao.cod_licenca 	     = licenca_imovel.cod_licenca
                      AND licenca_imovel_nova_edificacao.inscricao_municipal = licenca_imovel.inscricao_municipal
                      AND licenca_imovel_nova_edificacao.exercicio           = licenca_imovel.exercicio

                LEFT JOIN imobiliario.atributo_tipo_edificacao_valor
                       ON atributo_tipo_edificacao_valor.cod_construcao = licenca_imovel_nova_edificacao.cod_construcao
                      AND atributo_tipo_edificacao_valor.cod_tipo       = licenca_imovel_nova_edificacao.cod_tipo
                      AND atributo_tipo_edificacao_valor.cod_atributo   = 5052 -- atributo tipo de edificação, se reutilizar este carne para outro cliente, alterar este cód.

                LEFT JOIN administracao.atributo_valor_padrao AS atributo_edificacao
                       ON atributo_edificacao.cod_atributo = atributo_tipo_edificacao_valor.cod_atributo
                      AND atributo_edificacao.cod_valor    = atributo_tipo_edificacao_valor.valor

                    ".$stFiltro."

                 ORDER BY licenca.cod_licenca DESC LIMIT 1";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaValoresCarne(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaValoresCarne();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaValoresCarne()
{
    $stSql = "SELECT *                                                                                                 \n";
    $stSql .= "FROM tesouraria.fn_recupera_carne( '".$this->getDado('exercicio' )."'   			\n";
    $stSql .= "                                  ,'".$this->getDado('numeracao' )."' )          \n";
    $stSql .= "as retorno( numeracao    varchar                                                 \n";
    $stSql .= "           ,exercicio    varchar                                                 \n";
    $stSql .= "           ,dt_vencimento varchar                                                \n";
    $stSql .= "           ,vl_parcela   numeric                                                 \n";
    $stSql .= "           ,vl_desconto  numeric                                                 \n";
    $stSql .= "           ,vl_multa     numeric                                                 \n";
    $stSql .= "           ,vl_juros     numeric                                                 \n";
    $stSql .=");                                                                                \n";

    return $stSql;
}

function recuperaNomeDevolucao(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaNomeDevolucao().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaNomeDevolucao()
{
    $stSql  = " select \n";
    $stSql .= "     ac.cod_convenio, \n";
    $stSql .= "     cgm.nom_cgm, \n";
    $stSql .= "     cgm.numcgm \n";
    $stSql .= " from \n";
    $stSql .= "     arrecadacao.carne      as ac \n";
    $stSql .= " INNER JOIN \n";
    $stSql .= "     arrecadacao.parcela    as ap \n";
    $stSql .= " ON \n";
    $stSql .= "     ac.cod_parcela = ap.cod_parcela \n";
    $stSql .= " INNER JOIN \n";
    $stSql .= "     arrecadacao.lancamento as al \n";
    $stSql .= " ON \n";
    $stSql .= "     ap.cod_lancamento = al.cod_lancamento \n";
    $stSql .= " INNER JOIN \n";
    $stSql .= "     arrecadacao.lancamento_calculo AS alc \n";
    $stSql .= " ON \n";
    $stSql .= "     alc.cod_lancamento = al.cod_lancamento \n";
    $stSql .= " INNER JOIN \n";
    $stSql .= "     arrecadacao.calculo_cgm AS acc \n";
    $stSql .= " ON \n";
    $stSql .= "     acc.cod_calculo = alc.cod_calculo \n";
    $stSql .= " INNER JOIN \n";
    $stSql .= "     sw_cgm as cgm \n";
    $stSql .= " ON \n";
    $stSql .= "     acc.numcgm = cgm.numcgm     \n";

    return $stSql;
}

function recuperaLancamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaLancamento().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLancamento()
{
    $stSql = "
        SELECT c.numeracao
             , p.cod_parcela
             , p.cod_lancamento
             , l.total_parcelas
             , l.valor as valor_lancamento
             , arrecadacao.buscaValorParcela(p.cod_parcela)::numeric(14,2) as valor_parcela
             , lc.cod_calculo
             , lc.valor
             , p.nr_parcela
             , COALESCE ( pr.vencimento, p.vencimento ) AS vencimento
          FROM arrecadacao.carne c
    INNER JOIN arrecadacao.parcela p
            ON p.cod_parcela = c.cod_parcela
     LEFT JOIN ( SELECT tmp.*
                   FROM arrecadacao.parcela_reemissao AS tmp
                      JOIN arrecadacao.carne
                      ON carne.cod_parcela = tmp.cod_parcela
                     AND carne.numeracao = '".$this->getDado('numero_carne')."'
             INNER JOIN ( SELECT max(timestamp) AS timestamp
                               , cod_parcela
                            FROM arrecadacao.parcela_reemissao
                        GROUP BY cod_parcela
                        )AS tmp2
                     ON tmp.cod_parcela = tmp2.cod_parcela
                    AND tmp.timestamp = tmp2.timestamp
               )AS pr
            ON pr.cod_parcela = p.cod_parcela
    INNER JOIN arrecadacao.lancamento as l
            ON l.cod_lancamento = p.cod_lancamento
    INNER JOIN arrecadacao.lancamento_calculo as lc
            ON lc.cod_lancamento = l.cod_lancamento
    ";

    return $stSql;
}

function verificaPagamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaVerificaPagamento().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaVerificaPagamento()
{
    $stSql = "   SELECT                                      	\n";
    $stSql .= "       pagamento.numeracao,                      \n";
    $stSql .= "       pagamento.ocorrencia_pagamento,          	\n";
    $stSql .= "       tipo_pagamento.pagamento                  \n";
    $stSql .= "   FROM                                          \n";
    $stSql .= "       arrecadacao.pagamento

                  INNER JOIN
                      arrecadacao.tipo_pagamento
                  ON
                      tipo_pagamento.cod_tipo = pagamento.cod_tipo \n";
    $stSql .= "   WHERE                                         \n";
    $stSql .= "       numeracao IS NOT NULL                   	\n";

    return $stSql;
}

function recuperaConsulta(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "", $dtDataBase, $dtVencimentoPR, $stNumeracao)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaConsulta ($dtDataBase, $stFiltro.$stOrdem, $dtVencimentoPR, $stNumeracao);
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao  );

    return $obErro;
}

function montaRecuperaConsulta($dtDataBase, $stFiltro, $dtVencimentoPR, $stNumeracao)
{
$stSql = "  SELECT                                                                                      \n";
$stSql .="      consulta.*                                                                              \n";
$stSql .="      , ( CASE WHEN consulta.pagamento_data is not null THEN                                  \n";
$stSql .="              CASE WHEN                                                                       \n";
$stSql .="                  ( consulta.pagamento_valor !=                                               \n";
$stSql .="                  ( (consulta.parcela_valor - parcela_valor_desconto ) +                      \n";
$stSql .="                  consulta.parcela_juros_pagar + consulta.parcela_multa_pagar                 \n";
$stSql .="                  + parcela_correcao_pagar                                                    \n";
$stSql .="                  + consulta.tmp_pagamento_diferenca )                                        \n";
$stSql .="                  )                                                                           \n";

$stSql .="              THEN                                                                            \n";
$stSql .="                  coalesce (                                                                  \n";
$stSql .="                      consulta.pagamento_valor -                                              \n";
$stSql .="                      (( consulta.parcela_valor - consulta.parcela_valor_desconto ) +         \n";
$stSql .="                      ( consulta.parcela_juros_pagar )                                        \n";
$stSql .="                      + ( consulta.parcela_multa_pagar )                                      \n";
$stSql .="                      + ( consulta.parcela_correcao_pagar )                                   \n";
$stSql .="                      ), 0.00 )                                                               \n";
$stSql .="                  + coalesce( (                                                               \n";
$stSql .="                  ( consulta.parcela_juros_pago - consulta.parcela_juros_pagar )              \n";
$stSql .="                  + ( consulta.parcela_multa_pago - consulta.parcela_multa_pagar )            \n";
$stSql .="                  + ( consulta.parcela_correcao_pago - consulta.parcela_correcao_pagar )      \n";
$stSql .="                  ), 0.00 )                                                                   \n";
$stSql .="              ELSE                                                                            \n";
$stSql .="                  consulta.tmp_pagamento_diferenca                                            \n";
$stSql .="              END                                                                             \n";
$stSql .="          ELSE                                                                                \n";
$stSql .="              0.00                                                                            \n";
$stSql .="          END                                                                                 \n";
$stSql .="      ) as pagamento_diferenca                                                                \n";
$stSql .="      , ( CASE WHEN  consulta.situacao = 'Em Aberto' THEN                                     \n";
$stSql .="              consulta.parcela_juros_pagar                                                    \n";
$stSql .="          ELSE                                                                                \n";
$stSql .="              CASE WHEN consulta.pagamento_data is not null THEN                              \n";
$stSql .="                  consulta.parcela_juros_pago                                                 \n";
$stSql .="              ELSE                                                                            \n";
$stSql .="                  0.00                                                                        \n";
$stSql .="              END                                                                             \n";
$stSql .="          END                                                                                 \n";
$stSql .="      ) as parcela_juros                                                                      \n";
$stSql .="      , ( CASE WHEN  consulta.situacao = 'Em Aberto' THEN                                     \n";
$stSql .="              consulta.parcela_multa_pagar                                                    \n";
$stSql .="          ELSE                                                                                \n";
$stSql .="              CASE WHEN consulta.pagamento_data is not null THEN                              \n";
$stSql .="                  consulta.parcela_multa_pago                                                 \n";
$stSql .="              ELSE                                                                            \n";
$stSql .="                  0.00                                                                        \n";
$stSql .="              END                                                                             \n";
$stSql .="          END                                                                                 \n";
$stSql .="      ) as parcela_multa                                                                      \n";
$stSql .="      , ( CASE WHEN consulta.situacao = 'Em Aberto' THEN                                      \n";
$stSql .="              ( consulta.parcela_valor - parcela_valor_desconto                               \n";
$stSql .="              + consulta.parcela_juros_pagar + consulta.parcela_multa_pagar                   \n";
$stSql .="              + consulta.parcela_correcao_pagar )                                             \n";
$stSql .="          ELSE                                                                                \n";
$stSql .="              CASE WHEN consulta.pagamento_data is not null THEN                              \n";
$stSql .="                  consulta.pagamento_valor                                                    \n";
$stSql .="              ELSE                                                                            \n";
$stSql .="                  0.00                                                                        \n";
$stSql .="              END                                                                             \n";
$stSql .="          END                                                                                 \n";
$stSql .="      ) as valor_total                                                                        \n";
$stSql .="  FROM                                                                                        \n";
$stSql .="      (                                                                                       \n";
$stSql .="          select DISTINCT                                                                     \n";
$stSql .="              al.cod_lancamento                                                               \n";
$stSql .="              , carne.numeracao                                                               \n";
$stSql .="              , carne.cod_convenio                                                            \n";

$stSql .="          ---- PARCELA                                                                        \n";
$stSql .="              , ap.cod_parcela                                                                \n";
$stSql .="              , ap.nr_parcela                                                                 \n";
$stSql .="              , ( CASE WHEN apr.cod_parcela is not null THEN                                  \n";
$stSql .="                      to_char (arrecadacao.fn_atualiza_data_vencimento(apr.vencimento),       \n";
$stSql .="                      'dd/mm/YYYY')                                                           \n";
$stSql .="                  ELSE                                                                        \n";
$stSql .="                      to_char (arrecadacao.fn_atualiza_data_vencimento(ap.vencimento),        \n";
$stSql .="                      'dd/mm/YYYY')                                                           \n";
$stSql .="                  END                                                                         \n";
$stSql .="              )::varchar as parcela_vencimento_original                                       \n";
$stSql .="              , ( CASE WHEN apr.cod_parcela is null THEN                                      \n";
$stSql .="                      arrecadacao.fn_atualiza_data_vencimento(ap.vencimento)                  \n";
$stSql .="                  ELSE                                                                        \n";
$stSql .="                      arrecadacao.fn_atualiza_data_vencimento(apr.vencimento)                 \n";
$stSql .="                  END                                                                         \n";
$stSql .="              )::varchar as parcela_vencimento_US                                             \n";
$stSql .="              , to_char (arrecadacao.fn_atualiza_data_vencimento(ap.vencimento), 'dd/mm/YYYY')\n";
$stSql .="              as vencimento_original --VENCIMENTO PARA EXIBIÇÃO                               \n";
$stSql .="              , ap.valor as parcela_valor                                                     \n";
$stSql .="              , ( CASE WHEN apd.cod_parcela is not null        \n"; //AND apag.numeracao is not  NULL
$stSql .="                       AND (ap.vencimento >= '". $dtDataBase ."' ) THEN                       \n";
$stSql .="                      (ap.valor - apd.valor)                                                  \n";
$stSql .="                  ELSE                                                                        \n";
$stSql .="                      0.00                                                                    \n";
$stSql .="                  END                                                                         \n";
$stSql .="              )::numeric(14,2) as parcela_valor_desconto                                      \n";
$stSql .="              , ( select arrecadacao.buscaValorOriginalParcela( carne.numeracao ) as valor    \n";
$stSql .="              ) as parcela_valor_original                                                     \n";
$stSql .="              , ( CASE WHEN apd.cod_parcela is not null AND apag.numeracao is NULL            \n";
$stSql .="                       AND (ap.vencimento >= '". $dtDataBase ."' ) THEN                       \n";
$stSql .="                      arrecadacao.fn_percentual_desconto_parcela( ap.cod_parcela,             \n";
$stSql .="                      ap.vencimento, (carne.exercicio)::int )                                 \n";
$stSql .="                  ELSE                                                                        \n";
$stSql .="                      0.00                                                                    \n";
$stSql .="                  END                                                                         \n";
$stSql .="              ) as parcela_desconto_percentual                                                \n";
$stSql .="              , ( CASE WHEN ap.nr_parcela = 0 THEN                                            \n";
$stSql .="                      'Única'::VARCHAR                                                        \n";
$stSql .="                  ELSE                                                                        \n";
$stSql .="                      ap.nr_parcela::varchar||'/'||                                           \n";
$stSql .="                      arrecadacao.fn_total_parcelas(al.cod_lancamento)                        \n";
$stSql .="                  END                                                                         \n";
$stSql .="              ) as info_parcela                                                               \n";
$stSql .="              , ( CASE WHEN apag.numeracao is not null THEN                                   \n";
$stSql .="                      apag.pagamento_tipo                                                     \n";
$stSql .="                  ELSE                                                                        \n";
$stSql .="                      CASE WHEN acd.devolucao_data is not null THEN                           \n";
$stSql .="                          acd.devolucao_descricao                                             \n";
$stSql .="                      ELSE                                                                    \n";
$stSql .="                          CASE WHEN ap.nr_parcela = 0                                         \n";
$stSql .="                                      and (ap.vencimento < '". $dtDataBase ."')               \n";
$stSql .="                                      and baixa_manual_unica.valor = 'nao'                    \n";
$stSql .="                          THEN                                                                \n";
$stSql .="                              'Cancelada (Parcela única vencida)'                             \n";
$stSql .="                          ELSE                                                                \n";
$stSql .="                              'Em Aberto'                                                     \n";
$stSql .="                          END                                                                 \n";
$stSql .="                      END                                                                     \n";
$stSql .="                  END                                                                         \n";
$stSql .="              )::varchar as situacao                                                          \n";

$stSql .="          ---- PARCELA FIM                                                                    \n";
$stSql .="              , al.valor as lancamento_valor                                                  \n";

$stSql .="          ---- PAGAMENTO                                                                      \n";
$stSql .="              , to_char(apag.pagamento_data,'dd/mm/YYYY') as pagamento_data                   \n";
$stSql .="              , apag.pagamento_data_baixa                                                     \n";
$stSql .="              , apag.processo_pagamento                                                       \n";
$stSql .="              , apag.observacao                                                               \n";
$stSql .="              , apag.tp_pagamento                                                             \n";
$stSql .="              , apag.pagamento_tipo                                                           \n";
$stSql .="              , pag_lote.pagamento_cod_lote                                                   \n";
$stSql .="              , coalesce ( apag_dif.pagamento_diferenca, 0.00 ) as tmp_pagamento_diferenca    \n";
$stSql .="              , apag.pagamento_valor                                                          \n";
$stSql .="              , ( CASE WHEN pag_lote.numeracao is not null THEN                               \n";
$stSql .="                      pag_lote.cod_banco                                                      \n";
$stSql .="                  ELSE                                                                        \n";
$stSql .="                      pag_lote_manual.cod_banco                                               \n";
$stSql .="                  END                                                                         \n";
$stSql .="              ) as pagamento_cod_banco                                                        \n";
$stSql .="              , ( CASE WHEN pag_lote.numeracao is not null THEN                               \n";
$stSql .="                      pag_lote.num_banco                                                      \n";
$stSql .="                  ELSE                                                                        \n";
$stSql .="                      pag_lote_manual.num_banco                                               \n";
$stSql .="                  END                                                                         \n";
$stSql .="              ) as pagamento_num_banco                                                        \n";
$stSql .="              , ( CASE WHEN pag_lote.numeracao is not null THEN                               \n";
$stSql .="                      pag_lote.nom_banco                                                      \n";
$stSql .="                  ELSE                                                                        \n";
$stSql .="                      pag_lote_manual.nom_banco                                               \n";
$stSql .="                  END                                                                         \n";
$stSql .="              ) as pagamento_nom_banco                                                        \n";
$stSql .="              , ( CASE WHEN pag_lote.numeracao is not null THEN                               \n";
$stSql .="                      pag_lote.cod_agencia                                                    \n";
$stSql .="                  ELSE                                                                        \n";
$stSql .="                      pag_lote_manual.cod_agencia                                             \n";
$stSql .="                  END                                                                         \n";
$stSql .="              ) as pagamento_cod_agencia                                                      \n";
$stSql .="              , ( CASE WHEN pag_lote.numeracao is not null THEN                               \n";
$stSql .="                      pag_lote.num_agencia                                                    \n";
$stSql .="                  ELSE                                                                        \n";
$stSql .="                      pag_lote_manual.num_agencia                                             \n";
$stSql .="                  END                                                                         \n";
$stSql .="              ) as pagamento_num_agencia                                                      \n";
$stSql .="              , ( CASE WHEN pag_lote.numeracao is not null THEN                               \n";
$stSql .="                      pag_lote.nom_agencia                                                    \n";
$stSql .="                  ELSE                                                                        \n";
$stSql .="                      pag_lote_manual.nom_agencia                                             \n";
$stSql .="                  END                                                                         \n";
$stSql .="              ) as pagamento_nom_agencia                                                      \n";
$stSql .="              , ( CASE WHEN pag_lote.numeracao is not null THEN                               \n";
$stSql .="                      pag_lote.numcgm                                                         \n";
$stSql .="                  ELSE                                                                        \n";
$stSql .="                      apag.pagamento_cgm                                                      \n";
$stSql .="                  END                                                                         \n";
$stSql .="              ) as pagamento_numcgm                                                           \n";
$stSql .="              , ( CASE WHEN pag_lote.numeracao is not null THEN                               \n";
$stSql .="                      pag_lote.nom_cgm                                                        \n";
$stSql .="                  ELSE                                                                        \n";
$stSql .="                      apag.pagamento_nome                                                     \n";
$stSql .="                  END                                                                         \n";
$stSql .="              ) as pagamento_nomcgm                                                           \n";
$stSql .="              , apag.ocorrencia_pagamento                                                     \n";

$stSql .="          ---- CARNE DEVOLUCAO                                                                \n";
$stSql .="              , acd.devolucao_data                                                            \n";
$stSql .="              , acd.devolucao_descricao                                                       \n";

$stSql .="          ---- CARNE MIGRACAO                                                                 \n";
$stSql .="              , acm.numeracao_migracao as migracao_numeracao                                  \n";
$stSql .="              , acm.prefixo as migracao_prefixo                                               \n";

$stSql .="          ---- CONSOLIDACAO                                                                   \n";
$stSql .="              , accon.numeracao_consolidacao as consolidacao_numeracao                        \n";

$stSql .="          ---- PARCELA ACRESCIMOS                                                             \n";
$stSql .="              , ( CASE WHEN  \n";
$stSql .="                              ( ap.valor = 0.00 )                                          \n";
$stSql .="                              OR ( apag.pagamento_data is not null                            \n";
$stSql .="                                   AND ap.vencimento >= apag.pagamento_data )                 \n";
$stSql .="                  THEN                                                                        \n";
$stSql .="                      0.00                                                                    \n";
$stSql .="                  ELSE                                                                        \n";
$stSql .="                      aplica_correcao( carne.numeracao::varchar, carne.exercicio::integer, carne.cod_parcela, '".$dtDataBase."'::date )::numeric(14,2) \n";

$stSql .="                  END                                                                         \n";
$stSql .="              )::numeric(14,2) as parcela_correcao_pagar                                      \n";
$stSql .="              , ( CASE WHEN  \n";
$stSql .="                              ( ap.valor = 0.00 )                                          \n";
$stSql .="                              OR ( apag.pagamento_data is not null                            \n";
$stSql .="                                   AND ap.vencimento >= apag.pagamento_data )                 \n";
$stSql .="                  THEN                                                                        \n";
$stSql .="                      0.00                                                                    \n";
$stSql .="                  ELSE                                                                        \n";

$stSql .="                      aplica_juro( carne.numeracao::varchar, carne.exercicio::integer, carne.cod_parcela, '".$dtDataBase."'::date )::numeric(14,2) \n";
$stSql .="                  END                                                                         \n";
$stSql .="              )::numeric(14,2) as parcela_juros_pagar                                         \n";
$stSql .="              , ( CASE WHEN  \n";
$stSql .="                              ( ap.valor = 0.00 )                                          \n";
$stSql .="                              OR (apag.pagamento_data is not null                             \n";
$stSql .="                                  AND ap.vencimento >= apag.pagamento_data                    \n";
$stSql .="                              )                                                               \n";
$stSql .="                  THEN                                                                        \n";
$stSql .="                      0.00                                                                    \n";
$stSql .="                  ELSE                                                                        \n";
$stSql .="                      aplica_multa( carne.numeracao::varchar, carne.exercicio::integer, carne.cod_parcela, '".$dtDataBase."'::date )::numeric(14,2) \n";
$stSql .="                  END                                                                         \n";
$stSql .="              )::numeric(14,2) as parcela_multa_pagar                                         \n";
$stSql .="              , ( CASE WHEN ( apag.pagamento_data is not null                                 \n";
$stSql .="                              AND ap.vencimento < apag.pagamento_data )                       \n";
$stSql .="                  THEN                                                                        \n";
$stSql .="                      ( select                                                                \n";
$stSql .="                            sum(valor)                                                        \n";
$stSql .="                        from                                                                  \n";
$stSql .="                            arrecadacao.pagamento_acrescimo                                   \n";
$stSql .="                        where                                                                 \n";
$stSql .="                            numeracao = apag.numeracao                                        \n";
$stSql .="                            AND cod_convenio = apag.cod_convenio                              \n";
$stSql .="                            AND ocorrencia_pagamento = apag.ocorrencia_pagamento              \n";
$stSql .="                            AND cod_tipo = 1                                                  \n";
$stSql .="                      )                                                                       \n";
$stSql .="                  ELSE                                                                        \n";
$stSql .="                      0.00                                                                    \n";
$stSql .="                  END                                                                         \n";
$stSql .="              )::numeric(14,2) as parcela_correcao_pago                                       \n";
$stSql .="              , ( CASE WHEN ( apag.pagamento_data is not null                                 \n";
$stSql .="                              AND ap.vencimento < apag.pagamento_data )                       \n";
$stSql .="                  THEN                                                                        \n";
$stSql .="                      ( select                                                                \n";
$stSql .="                            sum(valor)                                                        \n";
$stSql .="                        from                                                                  \n";
$stSql .="                            arrecadacao.pagamento_acrescimo                                   \n";
$stSql .="                        where                                                                 \n";
$stSql .="                            numeracao = apag.numeracao                                        \n";
$stSql .="                            AND cod_convenio = apag.cod_convenio                              \n";
$stSql .="                            AND ocorrencia_pagamento = apag.ocorrencia_pagamento              \n";
$stSql .="                            AND cod_tipo = 3                                                  \n";
$stSql .="                      )                                                                       \n";
$stSql .="                  ELSE                                                                        \n";
$stSql .="                      0.00                                                                    \n";
$stSql .="                  END                                                                         \n";
$stSql .="              )::numeric(14,2) as parcela_multa_pago                                          \n";
$stSql .="              , ( CASE WHEN ( apag.pagamento_data is not null AND                             \n";
$stSql .="                              ap.vencimento < apag.pagamento_data )                           \n";
$stSql .="                  THEN                                                                        \n";
$stSql .="                      ( select                                                                \n";
$stSql .="                          sum(valor)                                                          \n";
$stSql .="                        from                                                                  \n";
$stSql .="                          arrecadacao.pagamento_acrescimo                                     \n";
$stSql .="                        where                                                                 \n";
$stSql .="                          numeracao = apag.numeracao                                          \n";
$stSql .="                          AND cod_convenio = apag.cod_convenio                                \n";
$stSql .="                          AND ocorrencia_pagamento = apag.ocorrencia_pagamento                \n";
$stSql .="                          AND cod_tipo = 2                                                    \n";
$stSql .="                      )                                                                       \n";
$stSql .="                  ELSE                                                                        \n";
$stSql .="                      0.00                                                                    \n";
$stSql .="                  END                                                                         \n";
$stSql .="              )::numeric(14,2) as parcela_juros_pago                                          \n";

$stSql .="  FROM                                                                                        \n";
$stSql .="      arrecadacao.carne as carne                                                              \n";

$stSql .="      LEFT JOIN (                                                                             \n";
$stSql .="          select                                                                              \n";
$stSql .="              exercicio                                                                       \n";
$stSql .="              , valor                                                                         \n";
$stSql .="          from                                                                                \n";
$stSql .="              administracao.configuracao                                                      \n";
$stSql .="          where parametro = 'baixa_manual' AND cod_modulo = 25                                \n";
$stSql .="      ) as baixa_manual_unica                                                                 \n";
$stSql .="      ON baixa_manual_unica.exercicio = carne.exercicio                                       \n";

$stSql .="  ---- PARCELA                                                                                \n";
$stSql .="      INNER JOIN (                                                                            \n";
$stSql .="          select                                                                              \n";
$stSql .="              cod_parcela                                                                     \n";
$stSql .="              , valor                                                                         \n";
$stSql .="              , arrecadacao.fn_atualiza_data_vencimento (vencimento) as vencimento            \n";
$stSql .="              , nr_parcela                                                                    \n";
$stSql .="              , cod_lancamento                                                                \n";
$stSql .="          from                                                                                \n";
$stSql .="              arrecadacao.parcela as ap                                                       \n";
$stSql .="      ) as ap                                                                                 \n";
$stSql .="      ON ap.cod_parcela = carne.cod_parcela                                                   \n";

$stSql .="      LEFT JOIN (                                                                             \n";
$stSql .="          select                                                                              \n";
$stSql .="              apr.cod_parcela                                                                 \n";
$stSql .="              , arrecadacao.fn_atualiza_data_vencimento( vencimento ) as vencimento           \n";
$stSql .="              , valor                                                                         \n";
$stSql .="          from                                                                                \n";
$stSql .="              arrecadacao.parcela_reemissao apr                                               \n";
$stSql .="              inner join (                                                                    \n";
$stSql .="                  select cod_parcela, min(timestamp) as timestamp                             \n";
$stSql .="                  from arrecadacao.parcela_reemissao                                          \n";
$stSql .="                  group by cod_parcela                                                        \n";
$stSql .="              ) as apr2                                                                       \n";
$stSql .="              ON apr2.cod_parcela = apr.cod_parcela                                           \n";
$stSql .="              AND apr2.timestamp = apr.timestamp                                              \n";
$stSql .="      ) as apr                                                                                \n";
$stSql .="      ON apr.cod_parcela = ap.cod_parcela                                                     \n";

$stSql .="      LEFT JOIN arrecadacao.parcela_desconto apd                                              \n";
$stSql .="      ON apd.cod_parcela = ap.cod_parcela                                                     \n";

$stSql .="    ---- #                                                                                    \n";
$stSql .="      INNER JOIN arrecadacao.lancamento as al                                                 \n";
$stSql .="      ON al.cod_lancamento = ap.cod_lancamento                                                \n";
$stSql .="      INNER JOIN arrecadacao.lancamento_calculo as alc                                        \n";
$stSql .="      ON alc.cod_lancamento = al.cod_lancamento                                               \n";
$stSql .="      INNER JOIN arrecadacao.calculo as ac                                                    \n";
$stSql .="      ON ac.cod_calculo = alc.cod_calculo                                                     \n";

$stSql .="  ---- PAGAMENTO                                                                              \n";
$stSql .="      LEFT JOIN (                                                                             \n";
$stSql .="          SELECT                                                                              \n";
$stSql .="              apag.numeracao                                                                  \n";
$stSql .="              , apag.cod_convenio                                                             \n";
$stSql .="              , apag.observacao                                                               \n";
$stSql .="              , atp.pagamento as tp_pagamento                                                 \n";
$stSql .="              , apag.data_pagamento as pagamento_data                                         \n";
$stSql .="              , to_char(apag.data_baixa,'dd/mm/YYYY') as pagamento_data_baixa                 \n";
$stSql .="              , app.cod_processo::varchar||'/'||app.ano_exercicio as processo_pagamento       \n";
$stSql .="              , cgm.numcgm as pagamento_cgm                                                   \n";
$stSql .="              , cgm.nom_cgm as pagamento_nome                                                 \n";
$stSql .="              , atp.nom_tipo as pagamento_tipo                                                \n";
$stSql .="              , apag.valor as pagamento_valor                                                 \n";
$stSql .="              , apag.ocorrencia_pagamento                                                     \n";
$stSql .="          FROM                                                                                \n";
$stSql .="              arrecadacao.pagamento as apag                                                   \n";
$stSql .="              INNER JOIN sw_cgm as cgm                                                        \n";
$stSql .="              ON cgm.numcgm = apag.numcgm                                                     \n";
$stSql .="              INNER JOIN arrecadacao.tipo_pagamento as atp                                    \n";
$stSql .="              ON atp.cod_tipo = apag.cod_tipo                                                 \n";
$stSql .="              LEFT JOIN arrecadacao.processo_pagamento as app                                 \n";
$stSql .="              ON app.numeracao = apag.numeracao AND app.cod_convenio = apag.cod_convenio      \n";
$stSql .="      ) as apag                                                                               \n";
$stSql .="      ON apag.numeracao = carne.numeracao                                                     \n";
$stSql .="      AND apag.cod_convenio = carne.cod_convenio                                              \n";

$stSql .="      LEFT JOIN (                                                                             \n";
$stSql .="          SELECT                                                                              \n";
$stSql .="              numeracao                                                                       \n";
$stSql .="              , cod_convenio                                                                  \n";
$stSql .="              , ocorrencia_pagamento                                                          \n";
$stSql .="              , sum( valor ) as pagamento_diferenca                                           \n";
$stSql .="          FROM arrecadacao.pagamento_diferenca                                                \n";
$stSql .="          GROUP BY numeracao, cod_convenio, ocorrencia_pagamento                              \n";
$stSql .="      ) as apag_dif                                                                           \n";
$stSql .="      ON apag_dif.numeracao = carne.numeracao                                                 \n";
$stSql .="      AND apag_dif.cod_convenio = carne.cod_convenio                                          \n";
$stSql .="      AND apag_dif.ocorrencia_pagamento = apag.ocorrencia_pagamento                           \n";

$stSql .="  ---- PAGAMENTO LOTE AUTOMATICO                                                              \n";
$stSql .="      LEFT JOIN (                                                                             \n";
$stSql .="          SELECT                                                                              \n";
$stSql .="              pag_lote.numeracao                                                              \n";
$stSql .="              , pag_lote.cod_convenio                                                         \n";
$stSql .="              , lote.cod_lote as pagamento_cod_lote                                           \n";
$stSql .="              , cgm.numcgm                                                                    \n";
$stSql .="              , cgm.nom_cgm                                                                   \n";
$stSql .="              , lote.data_lote                                                                \n";
$stSql .="              , mb.cod_banco                                                                  \n";
$stSql .="              , mb.num_banco                                                                  \n";
$stSql .="              , mb.nom_banco                                                                  \n";
$stSql .="              , mag.cod_agencia                                                               \n";
$stSql .="              , mag.num_agencia                                                               \n";
$stSql .="              , mag.nom_agencia                                                               \n";
$stSql .="              , pag_lote.ocorrencia_pagamento                                                 \n";
$stSql .="          FROM                                                                                \n";
$stSql .="              arrecadacao.pagamento_lote pag_lote                                             \n";
$stSql .="              INNER JOIN arrecadacao.lote lote                                                \n";
$stSql .="              ON lote.cod_lote = pag_lote.cod_lote                                            \n";
$stSql .="              AND pag_lote.exercicio = lote.exercicio                                         \n";
$stSql .="              INNER JOIN monetario.banco as mb ON mb.cod_banco = lote.cod_banco               \n";
$stSql .="              INNER JOIN sw_cgm cgm ON cgm.numcgm = lote.numcgm                               \n";
$stSql .="              LEFT JOIN monetario.conta_corrente_convenio mccc                                \n";
$stSql .="              ON mccc.cod_convenio = pag_lote.cod_convenio                                    \n";
$stSql .="              LEFT JOIN monetario.agencia mag                                                 \n";
$stSql .="              ON mag.cod_agencia = lote.cod_agencia                                           \n";
$stSql .="              AND mag.cod_banco = mb.cod_banco                                                \n";
$stSql .="      ) as pag_lote                                                                           \n";
$stSql .="      ON pag_lote.numeracao = carne.numeracao                                                 \n";
$stSql .="      AND pag_lote.cod_convenio = carne.cod_convenio                                          \n";

$stSql .="  ----- PAGAMENTO LOTE MANUAL                                                                 \n";
$stSql .="      LEFT JOIN (                                                                             \n";
$stSql .="          SELECT                                                                              \n";
$stSql .="              pag_lote.numeracao                                                              \n";
$stSql .="              , pag_lote.cod_convenio                                                         \n";
$stSql .="              , mb.cod_banco                                                                  \n";
$stSql .="              , mb.num_banco                                                                  \n";
$stSql .="              , mb.nom_banco                                                                  \n";
$stSql .="              , mag.cod_agencia                                                               \n";
$stSql .="              , mag.num_agencia                                                               \n";
$stSql .="              , mag.nom_agencia                                                               \n";
$stSql .="              , pag_lote.ocorrencia_pagamento                                                 \n";
$stSql .="          FROM                                                                                \n";
$stSql .="              arrecadacao.pagamento_lote_manual pag_lote                                      \n";
$stSql .="              INNER JOIN monetario.banco as mb ON mb.cod_banco = pag_lote.cod_banco           \n";
$stSql .="              LEFT JOIN monetario.conta_corrente_convenio mccc                                \n";
$stSql .="              ON mccc.cod_convenio = pag_lote.cod_convenio                                    \n";
$stSql .="              LEFT JOIN monetario.agencia mag                                                 \n";
$stSql .="              ON mag.cod_agencia = pag_lote.cod_agencia                                       \n";
$stSql .="              AND mag.cod_banco = mb.cod_banco                                                \n";
$stSql .="      ) as pag_lote_manual                                                                    \n";
$stSql .="      ON pag_lote_manual.numeracao = carne.numeracao                                          \n";
$stSql .="      AND pag_lote_manual.cod_convenio = carne.cod_convenio                                   \n";
$stSql .="      AND pag_lote_manual.ocorrencia_pagamento = apag.ocorrencia_pagamento                    \n";

$stSql .="  ---- CARNE DEVOLUCAO                                                                        \n";
$stSql .="      LEFT JOIN (                                                                             \n";
$stSql .="          SELECT                                                                              \n";
$stSql .="              acd.numeracao                                                                   \n";
$stSql .="              , acd.cod_convenio                                                              \n";
$stSql .="              , acd.dt_devolucao as devolucao_data                                            \n";
$stSql .="              , amd.descricao as devolucao_descricao                                          \n";
$stSql .="          FROM                                                                                \n";
$stSql .="              arrecadacao.carne_devolucao as acd                                              \n";
$stSql .="              INNER JOIN arrecadacao.motivo_devolucao as amd                                  \n";
$stSql .="              ON amd.cod_motivo = acd.cod_motivo                                              \n";
$stSql .="      ) as acd                                                                                \n";
$stSql .="      ON acd.numeracao = carne.numeracao                                                      \n";
$stSql .="      AND acd.cod_convenio = carne.cod_convenio                                               \n";

$stSql .="      LEFT JOIN arrecadacao.carne_migracao acm                                                \n";
$stSql .="      ON  acm.numeracao  = carne.numeracao                                                    \n";
$stSql .="      AND acm.cod_convenio = carne.cod_convenio                                               \n";
$stSql .="      LEFT JOIN arrecadacao.carne_consolidacao as accon                                       \n";
$stSql .="      ON accon.numeracao = carne.numeracao                                                    \n";
$stSql .="      AND accon.cod_convenio = carne.cod_convenio                                             \n";

$stSql .="  WHERE                                                                                       \n";
$stSql .="      ". $stFiltro ."                                                                         \n";
$stSql .="  ORDER BY                                                                                    \n";
$stSql .="      ap.nr_parcela                                                                           \n";
$stSql .="  ) as consulta                                                                               \n";

    return $stSql;

}

function recuperaDetalheCreditosConsulta(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "", $dtDataBase, $dtVencimentoPR, $stNumeracao)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaDetalheCreditosConsulta($dtDataBase, $dtVencimentoPR, $stNumeracao, $stFiltro) .$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaDetalheCreditosConsulta($dtDataBase, $dtVencimentoPR, $stNumeracao, $stFiltro)
{
    $arVencimentoPR = explode ('/', $dtVencimentoPR );
    $dtVencimentoPR_us = $arVencimentoPR[2].'-'.$arVencimentoPR[1].'-'.$arVencimentoPR[0];

$stSql = "  SELECT                                                                                          \n";
$stSql .="      *                                                                                           \n";
$stSql .="      , ( (   soma_total.valor_credito - soma_total.credito_descontos)                            \n";
$stSql .="            + soma_total.credito_juros_pagar + soma_total.credito_multa_pagar                     \n";
$stSql .="            + soma_total.credito_correcao_pagar + soma_total.diferenca                            \n";
$stSql .="      ) as valor_total                                                                            \n";
$stSql .="  FROM                                                                                            \n";
$stSql .="      (                                                                                           \n";
$stSql .="      SELECT                                                                                      \n";
$stSql .="          *                                                                                       \n";
$stSql .="          , ( CASE WHEN consulta.pagamento_data is not null AND ( consulta.tp_pagamento is true ) \n";
$stSql .="              THEN                                                                                \n";
$stSql .="                  ( consulta.pagamento_valor )                                                    \n";
$stSql .="                  - ( consulta.valor_credito - consulta.credito_descontos )                       \n";
$stSql .="                  + (consulta.credito_juros_pago - consulta.credito_juros_pagar)                  \n";
$stSql .="                  + (consulta.credito_multa_pago - consulta.credito_multa_pagar)                  \n";
$stSql .="                  + ( consulta.credito_correcao_pago - consulta.credito_correcao_pagar )          \n";
$stSql .="                  + consulta.pagamento_diferenca                                                  \n";
$stSql .="              ELSE                                                                                \n";
$stSql .="                  0.00                                                                            \n";
$stSql .="              END                                                                                 \n";
$stSql .="          ) as diferenca                                                                          \n";
$stSql .="          , to_char( consulta.pagamento_data,'dd/mm/YYYY' ) as pagamento_data                     \n";

$stSql .="      FROM                                                                                        \n";
$stSql .="          (                                                                                       \n";
$stSql .="          SELECT                                                                                  \n";
$stSql .="              split_part ( monetario.fn_busca_mascara_credito( ac.cod_credito, ac.cod_especie,    \n";
$stSql .="                  ac.cod_genero, ac.cod_natureza), '§', 1                                         \n";
$stSql .="              ) as credito_codigo_composto                                                        \n";
$stSql .="              , split_part ( monetario.fn_busca_mascara_credito( ac.cod_credito, ac.cod_especie,  \n";
$stSql .="                      ac.cod_genero, ac.cod_natureza), '§', 6                                     \n";
$stSql .="              ) as credito_nome                                                                   \n";
$stSql .="              , ac.cod_calculo                                                                    \n";
$stSql .="              , ac.valor as calculo_valor                                                         \n";
$stSql .="              , ap.vencimento as parcela_vencimento                                               \n";
$stSql .="              , apag.pagamento_data                                                               \n";
$stSql .="              , apag.tp_pagamento                                                                 \n";
$stSql .="              , apagc.valor as pagamento_valor                                                    \n";
$stSql .="              , coalesce (apag_dif.valor, 0.00) as pagamento_diferenca                            \n";
$stSql .="              , coalesce (apagcorr.valor, 0.00) as credito_correcao_pago                          \n";
$stSql .="              , coalesce (apagj.valor, 0.00) as credito_juros_pago                                \n";
$stSql .="              , coalesce (apagm.valor, 0.00) as credito_multa_pago                                \n";
$stSql .="              , (  CASE WHEN alc.valor = 0.00 THEN                                                \n";
$stSql .="                       0.00                                                                       \n";
$stSql .="                   ELSE                                                                           \n";
$stSql .="                      CASE WHEN ap.nr_parcela = 0 THEN                                            \n";
$stSql .="                          alc.valor                                                               \n";
$stSql .="                      ELSE                                                                        \n";
$stSql .="                      coalesce (
                                            (
                                                (( alc.valor * 100 ) / somaALC.valor )
                                                    *
                                                 ap.valor
                                            ) / 100,
                                            0.00
                                         )::numeric(14,6)                                         \n";
$stSql .="                      END                                                                         \n";
$stSql .="                  END                                                                             \n";
$stSql .="              )::numeric(14,2) as valor_credito                                                   \n";

$stSql .="          ---- ACRESCIMOS ABERTO                                                                  \n";

$stSql .="              , ( CASE WHEN ( ap.vencimento_antigo >= '". $dtDataBase ."' AND ap.nr_parcela >= 0 )        \n";
$stSql .="                          OR                                                                      \n";
$stSql .="                          ( ap.nr_parcela = 0 and baixa_manual_unica.valor = 'nao' )              \n";
$stSql .="                          OR                                                                      \n";
$stSql .="                          ( apag.pagamento_data is not null                                       \n";
$stSql .="                              AND                                                                 \n";
$stSql .="                            ap.vencimento >= apag.pagamento_data                                  \n";
$stSql .="                          )                                                                       \n";
$stSql .="                          OR ( ap.nr_parcela > 0 AND acd.numeracao is not null )                  \n";
$stSql .="                          OR  ( ap.valor = 0.00 )                                                 \n";
$stSql .="                  THEN                                                                            \n";
$stSql .="                      0.00                                                                        \n";
$stSql .="                  ELSE                                                                            \n";
$stSql .="                      coalesce (
                                            (
                                                (( alc.valor * 100 ) / somaALC.valor )
                                                    *
                                                 aplica_correcao ( carne.numeracao, ac.exercicio::int, ap.cod_parcela, '". $dtDataBase ."' )
                                            ) / 100,
                                            0.00
                                         )::numeric(14,6)                                         \n";
$stSql .="                  END                                                                             \n";
$stSql .="                )::numeric(14,2) as credito_correcao_pagar                                      \n";

$stSql .="              , ( CASE WHEN ( ap.vencimento_antigo >= '". $dtDataBase ."' AND ap.nr_parcela >= 0 )        \n";
$stSql .="                          OR                                                                      \n";
$stSql .="                          ( ap.nr_parcela = 0 and baixa_manual_unica.valor = 'nao' )              \n";
$stSql .="                          OR                                                                      \n";
$stSql .="                          ( apag.pagamento_data is not null                                       \n";
$stSql .="                              AND                                                                 \n";
$stSql .="                            ap.vencimento >= apag.pagamento_data                                  \n";
$stSql .="                          )                                                                       \n";
$stSql .="                          OR ( ap.nr_parcela > 0 AND acd.numeracao is not null )                  \n";
$stSql .="                          OR  ( ap.valor = 0.00 )                                                 \n";
$stSql .="                  THEN                                                                            \n";
$stSql .="                      0.00                                                                        \n";
$stSql .="                  ELSE                                                                            \n";
$stSql .="                      coalesce (
                                            (
                                                (( alc.valor * 100 ) / somaALC.valor )
                                                    *
                                                 aplica_juro ( carne.numeracao, ac.exercicio::int, ap.cod_parcela, '". $dtDataBase ."' )
                                            ) / 100,
                                            0.00
                                         )::numeric(14,6)                                         \n";
$stSql .="                  END                                                                             \n";
$stSql .="              )::numeric(14,2) as credito_juros_pagar                                             \n";

$stSql .="              , ( CASE WHEN ( ap.vencimento_antigo >= '". $dtDataBase ."' AND ap.nr_parcela >= 0 )        \n";
$stSql .="                          OR                                                                      \n";
$stSql .="                          ( ap.nr_parcela = 0 and baixa_manual_unica.valor = 'nao' )              \n";
$stSql .="                          OR                                                                      \n";
$stSql .="                          ( apag.pagamento_data is not null                                       \n";
$stSql .="                              AND                                                                 \n";
$stSql .="                            ap.vencimento >= apag.pagamento_data                                  \n";
$stSql .="                          )                                                                       \n";
$stSql .="                          OR ( ap.nr_parcela > 0 AND acd.numeracao is not null )                  \n";
$stSql .="                          OR  ( ap.valor = 0.00 )                                                 \n";
$stSql .="                  THEN                                                                            \n";
$stSql .="                      0.00                                                                        \n";
$stSql .="                  ELSE                                                                            \n";
$stSql .="                      coalesce (
                                            (
                                                (( alc.valor * 100 ) / somaALC.valor )
                                                    *
                                                 aplica_multa ( carne.numeracao, ac.exercicio::int, ap.cod_parcela, '". $dtDataBase ."' )
                                            ) / 100,
                                            0.00
                                         )::numeric(14,6)                                         \n";
$stSql .="                  END                                                                             \n";
$stSql .="              )::numeric(14,2) as credito_multa_pagar                                             \n";

$stSql .="              , ( CASE WHEN apag.pagamento_data is not null                                       \n";
$stSql .="                      AND apag.pagamento_data > ap.vencimento                                     \n";
$stSql .="                      OR ( ap.vencimento_antigo < '". $dtDataBase ."' )                           \n";
$stSql .="                      OR ( alc.valor = 0.00 )                                                     \n";
$stSql .="                  THEN                                                                            \n";
$stSql .="                      0.00                                                                        \n";
$stSql .="                  ELSE                                                                            \n";
$stSql .="
                                coalesce (
                                            (
                                                (( alc.valor * 100 ) / somaALC.valor )
                                                    *
                                                 apd.valor
                                            ) / 100,
                                            0.00
                                         )::numeric(14,6)                                                   \n";
$stSql .="                  END                                                                             \n";
$stSql .="              )::numeric(14,2) as credito_descontos                                               \n";
$stSql .="          , CASE WHEN
                            (
                                SELECT
                                    calculo_grupo_credito.cod_calculo
                                FROM
                                    arrecadacao.calculo_grupo_credito
                                WHERE
                                    calculo_grupo_credito.cod_calculo = ac.cod_calculo
                            ) IS NOT NULL AND ( apd.cod_parcela IS NOT NULL ) THEN
                            COALESCE(
                                (
                                    SELECT
                                        acg.desconto
                                    FROM
                                        arrecadacao.credito_grupo AS acg
                                    WHERE
                                        acg.cod_credito = ac.cod_credito
                                        AND acg.cod_genero = ac.cod_genero
                                        AND acg.cod_especie = ac.cod_especie
                                        AND acg.cod_natureza = ac.cod_natureza
                                        AND acg.cod_grupo = (
                                            SELECT
                                                cod_grupo
                                            FROM
                                                arrecadacao.calculo_grupo_credito
                                            WHERE
                                                calculo_grupo_credito.cod_calculo = ac.cod_calculo
                                        )
                                        AND acg.ano_exercicio = (
                                            SELECT
                                                ano_exercicio
                                            FROM
                                                arrecadacao.calculo_grupo_credito
                                            WHERE
                                                calculo_grupo_credito.cod_calculo = ac.cod_calculo
                                        )
                                ),
                                false
                            )
                    ELSE
                        CASE WHEN ( apd.cod_parcela IS NOT NULL ) THEN
                            true
                        ELSE
                            false
                        END
                    END AS usar_desconto \n";
$stSql .="          FROM                                                                                    \n";
$stSql .="              arrecadacao.carne as carne                                                          \n";

$stSql .="              LEFT JOIN (                                                                         \n";
$stSql .="                  select                                                                          \n";
$stSql .="                      exercicio                                                                   \n";
$stSql .="                      , valor                                                                     \n";
$stSql .="                  from                                                                            \n";
$stSql .="                      administracao.configuracao                                                  \n";
$stSql .="                  where parametro = 'baixa_manual' AND cod_modulo = 25                            \n";
$stSql .="              ) as baixa_manual_unica                                                             \n";
$stSql .="              ON baixa_manual_unica.exercicio = carne.exercicio                                   \n";

$stSql .="              INNER JOIN (                                                                        \n";
$stSql .="                  select                                                                          \n";
$stSql .="                      cod_parcela                                                                 \n";
$stSql .="                      , valor
                                , COALESCE(
                                    (
                                        SELECT
                                            arrecadacao.fn_atualiza_data_vencimento (parcela_reemissao.vencimento)
                                        FROM
                                            arrecadacao.parcela_reemissao
                                        WHERE
                                            parcela_reemissao.cod_parcela = ap.cod_parcela
                                        ORDER BY
                                            parcela_reemissao.timestamp ASC
                                        LIMIT 1
                                    ),
                                    arrecadacao.fn_atualiza_data_vencimento (ap.vencimento)
                                )AS vencimento_antigo    \n";
$stSql .="                      , arrecadacao.fn_atualiza_data_vencimento (vencimento) as vencimento        \n";
$stSql .="                      , nr_parcela                                                                \n";
$stSql .="                      , cod_lancamento                                                            \n";
$stSql .="                  from                                                                            \n";
$stSql .="                      arrecadacao.parcela as ap                                                   \n";
$stSql .="              ) as ap                                                                             \n";
$stSql .="              ON ap.cod_parcela = carne.cod_parcela                                               \n";

$stSql .="              LEFT JOIN arrecadacao.parcela_desconto as apd                                       \n";
$stSql .="              ON apd.cod_parcela = ap.cod_parcela                                                 \n";
$stSql .="              INNER JOIN arrecadacao.lancamento as al                                             \n";
$stSql .="              ON al.cod_lancamento = ap.cod_lancamento
                        INNER JOIN (
                            SELECT
                                sum(valor) AS valor
                                , cod_lancamento
                            FROM
                                arrecadacao.lancamento_calculo
                            GROUP BY
                                cod_lancamento
                        )AS somaALC
                        ON somaALC.cod_lancamento = al.cod_lancamento                                       \n";
$stSql .="              INNER JOIN arrecadacao.lancamento_calculo as alc                                    \n";
$stSql .="              ON alc.cod_lancamento = al.cod_lancamento                                           \n";
$stSql .="              INNER JOIN arrecadacao.calculo as ac                                                \n";
$stSql .="              ON ac.cod_calculo = alc.cod_calculo                                                 \n";

$stSql .="          ---- PAGAMENTO                                                                          \n";
$stSql .="              LEFT JOIN (                                                                         \n";
$stSql .="                  SELECT                                                                          \n";
$stSql .="                      apag.numeracao                                                              \n";
$stSql .="                      , apag.cod_convenio                                                         \n";
$stSql .="                      , apag.observacao                                                           \n";
$stSql .="                      , apag.data_pagamento as pagamento_data                                     \n";
$stSql .="                      , to_char(apag.data_baixa,'dd/mm/YYYY') as pagamento_data_baixa             \n";
$stSql .="                      , app.cod_processo::varchar||'/'||app.ano_exercicio as processo_pagamento   \n";
$stSql .="                      , cgm.numcgm as pagamento_cgm                                               \n";
$stSql .="                      , cgm.nom_cgm as pagamento_nome                                             \n";
$stSql .="                      , atp.nom_tipo as pagamento_tipo                                            \n";
$stSql .="                      , apag.valor as pagamento_valor                                             \n";
$stSql .="                      , apag.ocorrencia_pagamento                                                 \n";
$stSql .="                      , atp.pagamento as tp_pagamento                                             \n";
$stSql .="                      , apag.valor as pagamento_parcela_valor                                     \n";
$stSql .="                  FROM                                                                            \n";
$stSql .="                      arrecadacao.pagamento as apag                                               \n";
$stSql .="                      INNER JOIN sw_cgm as cgm                                                    \n";
$stSql .="                      ON cgm.numcgm = apag.numcgm                                                 \n";
$stSql .="                      INNER JOIN arrecadacao.tipo_pagamento as atp                                \n";
$stSql .="                      ON atp.cod_tipo = apag.cod_tipo                                             \n";
$stSql .="                      LEFT JOIN arrecadacao.processo_pagamento as app                             \n";
$stSql .="                      ON app.numeracao = apag.numeracao AND app.cod_convenio = apag.cod_convenio  \n";
$stSql .="              ) as apag                                                                           \n";
$stSql .="              ON apag.numeracao = carne.numeracao                                                 \n";
$stSql .="              AND apag.cod_convenio = carne.cod_convenio                                          \n";

$stSql .="              LEFT JOIN (                                                                         \n";
$stSql .="                  SELECT                                                                          \n";
$stSql .="                      numeracao                                                                   \n";
$stSql .="                      , cod_convenio                                                              \n";
$stSql .="                      , ocorrencia_pagamento                                                      \n";
$stSql .="                      , cod_calculo                                                               \n";
$stSql .="                      , valor                                                                     \n";
$stSql .="                  FROM arrecadacao.pagamento_diferenca                                            \n";
$stSql .="              ) as apag_dif                                                                       \n";
$stSql .="              ON apag_dif.numeracao = carne.numeracao                                             \n";
$stSql .="              AND apag_dif.cod_convenio = carne.cod_convenio                                      \n";
$stSql .="              AND apag_dif.ocorrencia_pagamento = apag.ocorrencia_pagamento                       \n";
$stSql .="              AND apag_dif.cod_calculo = ac.cod_calculo                                           \n";

$stSql .="              LEFT JOIN arrecadacao.pagamento_calculo as apagc                                    \n";
$stSql .="              ON apagc.cod_calculo = ac.cod_calculo                                               \n";
$stSql .="              AND apagc.numeracao = apag.numeracao                                                \n";
$stSql .="              AND apagc.cod_convenio = apag.cod_convenio                                          \n";
$stSql .="              AND apagc.ocorrencia_pagamento = apag.ocorrencia_pagamento                          \n";

$stSql .="              LEFT JOIN arrecadacao.pagamento_acrescimo as apagcorr                               \n";
$stSql .="              ON apagcorr.cod_calculo = ac.cod_calculo                                            \n";
$stSql .="              AND apagcorr.numeracao = apag.numeracao                                             \n";
$stSql .="              AND apagcorr.cod_convenio = apag.cod_convenio                                       \n";
$stSql .="              AND apagcorr.ocorrencia_pagamento = apag.ocorrencia_pagamento                       \n";
$stSql .="              AND apagcorr.cod_tipo = 1                                                           \n";

$stSql .="              LEFT JOIN arrecadacao.pagamento_acrescimo as apagj                                  \n";
$stSql .="              ON apagj.cod_calculo = ac.cod_calculo                                               \n";
$stSql .="              AND apagj.numeracao = apag.numeracao                                                \n";
$stSql .="              AND apagj.cod_convenio = apag.cod_convenio                                          \n";
$stSql .="              AND apagj.ocorrencia_pagamento = apag.ocorrencia_pagamento                          \n";
$stSql .="              AND apagj.cod_tipo = 2                                                              \n";

$stSql .="              LEFT JOIN arrecadacao.pagamento_acrescimo as apagm                                  \n";
$stSql .="              ON apagm.cod_calculo = ac.cod_calculo                                               \n";
$stSql .="              AND apagm.numeracao = apag.numeracao                                                \n";
$stSql .="              AND apagm.cod_convenio = apag.cod_convenio                                          \n";
$stSql .="              AND apagm.ocorrencia_pagamento = apag.ocorrencia_pagamento                          \n";
$stSql .="              AND apagm.cod_tipo = 3                                                              \n";

$stSql .="              LEFT JOIN arrecadacao.carne_devolucao as acd                                        \n";
$stSql .="              ON acd.numeracao = carne.numeracao                                                  \n";
$stSql .="              AND acd.cod_convenio = carne.cod_convenio                                           \n";

$stSql .="      WHERE                                                                                       \n";
$stSql .="          ". $stFiltro ."                                                                         \n";
$stSql .="  ) as consulta                                                                                   \n";
$stSql .=") as soma_total                                                                                   \n";

    return $stSql;
}

function montaRecuperaParcelaLancamentoPorNumeracao()
{
    $stSql = '
        select parcela.cod_lancamento
             , parcela.cod_parcela
          from arrecadacao.carne
    inner join arrecadacao.parcela
            on parcela.cod_parcela = carne.cod_parcela';

    return $stSql;

}

function recuperaDetalheCreditosBaixa(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "",$dtDataBase)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_parcela ";
    $stSql  = $this->montaRecuperaDetalheCreditosBaixa($dtDataBase).$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    //$this->debug();exit();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDetalheCreditosBaixa($dtDataBase)
{
    $stSql  = " SELECT tbl.*,                                                                           \n";
    $stSql .= "     tbl.valor_credito_jurosp::numeric(14,2) as valor_credito_juros,                     \n";
    $stSql .= "     tbl.valor_credito_multap::numeric(14,2) as valor_credito_multa,                     \n";
    $stSql .= "     tbl.valor_credito_correcaop::numeric(14,2) as valor_credito_correcao,               \n";
    $stSql .= "     descontop::numeric(14,2) as desconto                                                \n";
    $stSql .= " FROM    (                                                                               \n";
    $stSql .= "         SELECT alc.cod_lancamento                                                       \n";
    $stSql .= "             ,calc.cod_genero                                                            \n";
    $stSql .= "             ,calc.cod_especie                                                           \n";
    $stSql .= "             ,calc.cod_natureza \n";
    $stSql .= "             ,calc.cod_credito \n";

    $stSql .= "              , calc.cod_calculo                                                                                                   \n";
    $stSql .= "              , alc.valor                                                                                                   \n";
     //###############-------------------------- JUROS
    $stSql .= "      ,case when                                                                                        \n";
    $stSql .= "         ( apag.data_pagamento is not null ) then                                     \n";

    $stSql .= "         case when (apag.data_pagamento <= ap.vencimento) then       \n";
    $stSql .= "             (0.00)                                                                                        \n";
    $stSql .= "         else                                                                                               \n";
    $stSql .= "                                                                                                              \n";
    //---------------------------------- INICIO PARCELA PAGA ATRASADA
    $stSql .= "                         ( select coalesce (sum(valor), 0.00)                             \n";
    $stSql .="                              from arrecadacao.pagamento_acrescimo as apaj    \n";
    $stSql .= "                             where                                                                       \n";
    $stSql .= "                              apaj.numeracao = ac.numeracao                           \n";
    $stSql.= "                               and apaj.cod_calculo = calc.cod_calculo                 \n";
    $stSql .= "                              and apaj.cod_tipo = 2                                             \n";
    $stSql .= "                         )                                                                                   \n";
    //---------------------------------- FIM PARCELA PAGA ATRASADA
    $stSql .= "      end                                                                                                    \n";
    $stSql .= "      else                                                                                                   \n";

    $stSql .= "case when (aparr.vencimento is not null ) then \n";
    $stSql .= "             CASE when ( aparr.vencimento >  '".$dtDataBase."' OR ap.nr_parcela = 0 ) then              \n";
    $stSql .= "                 (0.00)                                                                                \n";
    $stSql .= "             ELSE                                                                                       \n";
    $stSql .= "                 aplica_juro_credito_parcela(ac.numeracao,ac.exercicio::int,ac.cod_parcela,'".$dtDataBase."', calc.cod_credito, calc.cod_especie, calc.cod_genero, calc.cod_natureza )  \n";
    $stSql .= "         END                                                                      \n";
    $stSql .= "else  \n";

    $stSql .= " case when ( ap.valor > 0 ) then \n";
    $stSql .="             CASE when ( ap.vencimento >  '".$dtDataBase."' OR ap.nr_parcela = 0 ) then              \n"; //adicionei o 'OR ap.nr_parcela = 0' pq nao deve calcular juros e multa para cota unica
    $stSql .="                 (0.00)                                                                                      \n";
    $stSql .="             ELSE                                                                                            \n";
    $stSql .= "                aplica_juro_credito_parcela(ac.numeracao,ac.exercicio::int,ac.cod_parcela,'".$dtDataBase."', calc.cod_credito, calc.cod_especie, calc.cod_genero, calc.cod_natureza )         \n";
    $stSql .= "         END                                                                                               \n";
    $stSql .= "     else                                                                                                  \n";
    $stSql .= "         0.00                                                                                              \n";
    $stSql .= "     end                                                                                                   \n";

    $stSql .= "     end                                                                                                   \n";

    $stSql .= " end as valor_credito_jurosp,                                                                              \n";

    //###############-------------------------- CORRECAO
    $stSql .= "      case when                                                                                        \n";
    $stSql .= "         ( apag.data_pagamento is not null ) then                                     \n";

    $stSql .= "         case when (apag.data_pagamento <= ap.vencimento) then       \n";
    $stSql .= "             (0.00)                                                                                        \n";
    $stSql .= "         else                                                                                               \n";
    $stSql .= "                                                                                                              \n";
    //---------------------------------- INICIO PARCELA PAGA ATRASADA
    $stSql .= "                         ( select coalesce (sum(valor), 0.00)                             \n";
    $stSql .="                              from arrecadacao.pagamento_acrescimo as apaj    \n";
    $stSql .= "                             where                                                                       \n";
    $stSql .= "                              apaj.numeracao = ac.numeracao                           \n";
    $stSql.= "                               and apaj.cod_calculo = calc.cod_calculo                 \n";
    $stSql .= "                              and apaj.cod_tipo = 1                                             \n";
    $stSql .= "                         )                                                                                   \n";
    //---------------------------------- FIM PARCELA PAGA ATRASADA
    $stSql .= "      end                                                                                                    \n";
    $stSql .= "      else                                                                                                   \n";

    $stSql .= "case when (aparr.vencimento is not null ) then \n";
    $stSql .= "             CASE when ( aparr.vencimento >  '".$dtDataBase."' OR ap.nr_parcela = 0 ) then              \n";
    $stSql .= "                 (0.00)                                                                                \n";
    $stSql .= "             ELSE                                                                                       \n";
    $stSql .= "                 aplica_correcao_credito_parcela(ac.numeracao,ac.exercicio::int,ac.cod_parcela,'".$dtDataBase."', calc.cod_credito, calc.cod_especie, calc.cod_genero, calc.cod_natureza )  \n";
    $stSql .= "         END                                                                      \n";
    $stSql .= "else  \n";

    $stSql .= " case when ( ap.valor > 0 ) then \n";
    $stSql .="             CASE when ( ap.vencimento >  '".$dtDataBase."' OR ap.nr_parcela = 0 ) then              \n"; //adicionei o 'OR ap.nr_parcela = 0' pq nao deve calcular juros e multa para cota unica
    $stSql .="                 (0.00)                                                                                      \n";
    $stSql .="             ELSE                                                                                            \n";
    $stSql .= "                aplica_correcao_credito_parcela(ac.numeracao,ac.exercicio::int,ac.cod_parcela,'".$dtDataBase."', calc.cod_credito, calc.cod_especie, calc.cod_genero, calc.cod_natureza )         \n";
    $stSql .= "         END                                                                                               \n";
    $stSql .= "     else                                                                                                  \n";
    $stSql .= "         0.00                                                                                              \n";
    $stSql .= "     end                                                                                                   \n";

    $stSql .= "     end                                                                                                   \n";

    $stSql .= " end as valor_credito_correcaop,                                                                              \n";

    //###############-------------------------- MULTAS
    $stSql .= "      case when                                                                                            \n";
    $stSql .= "         ( apag.data_pagamento is not null ) then                                                          \n";
    $stSql .= "         case when (apag.data_pagamento <= ap.vencimento) then                                             \n";
    $stSql .= "             (0.00)                                                                                       \n";
    $stSql .= "         else                                                                                              \n";
    //---------------------------------- INICIO PARCELA PAGA ATRASADA
    $stSql .= "                         ( select coalesce (sum(valor), 0.00)                             \n";
    $stSql .="                              from arrecadacao.pagamento_acrescimo as apaj    \n";
    $stSql .= "                             where                                                                       \n";
    $stSql .= "                              apaj.numeracao = ac.numeracao                           \n";
    $stSql.= "                               and apaj.cod_calculo = calc.cod_calculo                 \n";
    $stSql .= "                              and apaj.cod_tipo = 3                                             \n";
    $stSql .= "                         )                                                                                   \n";
    $stSql .= "         end                                                                                              \n";
    //---------------------------------- FIM PARCELA PAGA ATRASADA
    //---------------------------------- COMEÇO NAO-PAGA
    $stSql .= "      else                                                                                                  \n";

$stSql .= " case when (aparr.vencimento is not null ) then   \n";
$stSql .= "  case when ( ap.valor > 0 ) then \n";
$stSql .= "             CASE when ( aparr.vencimento >  '".$dtDataBase."' OR ap.nr_parcela = 0 ) then            \n";
$stSql .= "                 (0.00)                                                                         \n";
$stSql .= "             ELSE                                                                             \n";
$stSql .= "                 aplica_multa_credito_parcela(ac.numeracao,ac.exercicio::int,ac.cod_parcela,'".$dtDataBase."', calc.cod_credito, calc.cod_especie, calc.cod_genero, calc.cod_natureza ) \n";
$stSql .= "           END                                                                                               \n";
$stSql .= " end     \n";
$stSql .= " else        \n";

    $stSql .= "          case when ( ap.valor > 0 ) then \n";
    $stSql .="             CASE when ( ap.vencimento >=  '".$dtDataBase."' OR ap.nr_parcela = 0 ) then            \n"; //adicionei o 'OR ap.nr_parcela = 0' pq nao deve calcular juros e multa para cota unica
    $stSql .="                 (0.00)                                                                                     \n";
    $stSql .="             ELSE                                                                                           \n";
    $stSql .= "                aplica_multa_credito_parcela(ac.numeracao,ac.exercicio::int,ac.cod_parcela,'".$dtDataBase."', calc.cod_credito, calc.cod_especie, calc.cod_genero, calc.cod_natureza )     \n";
    $stSql .= "           END                                                                                               \n";
    $stSql .= "      else\n";
    $stSql .= "          0.00\n";
    $stSql .= "      end\n";

    $stSql .= "      end\n";

    $stSql .= " end as valor_credito_multap                                                                    \n";

    //############### DESCONTOS ##############################
     $stSql .= "    , ( CASE WHEN ( ( apd.valor is not null) AND (( apag.data_pagamento <= ap.vencimento )
                                  OR (apd.vencimento >= '".$dtDataBase."' )) )                          \n";
     $stSql .= "        THEN                                                                            \n";
     $stSql .= "            arrecadacao.fn_desconto_credito_lancamento( alc.cod_lancamento,             \n";
     $stSql .= "                                                        ap.cod_parcela,                 \n";
     $stSql .= "                                                        calc.cod_calculo,               \n";
     $stSql .= "                                                        calc.cod_credito,               \n";
     $stSql .= "                                                        calc.cod_especie,               \n";
     $stSql .= "                                                        calc.cod_genero,                \n";
     $stSql .= "                                                        calc.cod_natureza,              \n";
     $stSql .= "                                                        '".$dtDataBase."',              \n";
     $stSql .= "                                                        calc.valor,                     \n";
     $stSql .= "                                                        (calc.exercicio)::int           \n";
     $stSql .= "            )::numeric(14,6)                                                            \n";
     $stSql .= "        ELSE                                                                            \n";
     $stSql .= "            0.00                                                                        \n";
     $stSql .= "        END ) as descontop                                                              \n";
    //############### DESCONTOS ##############################

     $stSql .= "           , ac.numeracao                                                               \n";
     $stSql .= "           , ac.cod_convenio                                                            \n";
     $stSql .= "           , arrecadacao.fn_atualiza_data_vencimento ( ap.vencimento ) as vencimento    \n";
     $stSql .= "           , ap.cod_parcela                                                             \n";
     $stSql .= "           , ap.nr_parcela                                                              \n";
     $stSql .= "           , ap.valor as valor_parcela                                                  \n";
     $stSql .= "           , lancamento.divida AS divida_ativa                                          \n";

    $stSql .= "     FROM                                                                                \n";

    $stSql .= "         arrecadacao.calculo calc                                                        \n";
    if ($this->getDado('cod_lancamento') == '') {
        $stSql .= "         INNER JOIN arrecadacao.lancamento_calculo alc                                   \n";
    } else {
        $stSql .= "         INNER JOIN ( SELECT lancamento_calculo.*
                                           FROM arrecadacao.lancamento_calculo
                                          WHERE lancamento_calculo.cod_lancamento=".$this->getDado('cod_lancamento').") as alc \n";
    }
    $stSql .= "         ON alc.cod_calculo = calc.cod_calculo                                           \n";
    $stSql .= "     INNER JOIN arrecadacao.lancamento
                            ON lancamento.cod_lancamento = alc.cod_lancamento ";
    $stSql .= "         INNER JOIN arrecadacao.parcela as ap                                            \n";
    $stSql .= "         ON ap.cod_lancamento = alc.cod_lancamento                                       \n";

    $stSql .= "         LEFT JOIN (                                                                     \n";
    $stSql .= "             SELECT                                                                      \n";
    $stSql .= "                 arrecadacao.fn_atualiza_data_vencimento(apr.vencimento) as vencimento   \n";
    $stSql .= "                 , apr.cod_parcela                                                       \n";
    $stSql .= "             FROM                                                                        \n";
    $stSql .= "                 arrecadacao.parcela_reemissao apr                                       \n";
    $stSql .= "                 INNER JOIN (                                                            \n";
    $stSql .= "                     SELECT                                                              \n";
    $stSql .= "                         MIN(app.timestamp) AS timestamp,                                \n";
    $stSql .= "                         app.cod_parcela                                                 \n";
    $stSql .= "                     FROM                                                                \n";
    $stSql .= "                         arrecadacao.parcela_reemissao AS app                            \n";
    if ($this->getDado('cod_parcela') != '') {
        $stSql .= "                 where cod_parcela = " . $this->getDado('cod_parcela') . "           \n";
    }
    $stSql .= "                     GROUP BY cod_parcela                                                \n";
    $stSql .= "                 )AS ap                                                                  \n";
    $stSql .= "                 ON ap.timestamp = apr.timestamp                                         \n";
    $stSql .= "                 AND ap.cod_parcela = apr.cod_parcela                                    \n";
    $stSql .= "         )as aparr                                                                       \n";
    $stSql .= "         ON  aparr.cod_parcela = ap.cod_parcela                                          \n";

    $stSql .= "         LEFT JOIN arrecadacao.parcela_desconto apd                                      \n";
    $stSql .= "         ON apd.cod_parcela = ap.cod_parcela                                             \n";

    $stSql .= "         INNER JOIN arrecadacao.carne ac                                                 \n";
    $stSql .= "         ON ac.cod_parcela = ap.cod_parcela                                              \n";

    $stSql .= "         LEFT JOIN arrecadacao.pagamento apag                                            \n";
    $stSql .= "         ON apag.numeracao = ac.numeracao                                                \n";
    $stSql .= "         AND apag.cod_convenio = ac.cod_convenio                                         \n";
    $stSql .= "         AND apag.ocorrencia_pagamento = 1                                               \n";

    $stSql .= " ) as tbl                                                                                \n";

    return $stSql;
}

function recuperaListaReEmissaoEconomico(&$rsRecordSet, $stFiltro = "", $stFiltroPos = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stGroup = " group by cod_lancamento
        , nr_parcela
        , vencimento
        , numeracao
        , exercicio
        , cod_carteira
        , cod_convenio
        , numcgm
        , nom_cgm
        , cod_credito
        , cod_natureza
        , cod_genero
        , cod_especie
        , descricao_credito
        , convenio_atual
        , inscricao
        , inscricao_economica
        , carteira_atual
        , info_parcela
        , numeracao_migrada
        , origem
        , situacao
        , valida  ";
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_lancamento ";
    $stSql  = $this->montaRecuperaListaReEmissaoEconomico( $stFiltro ).$stFiltroPos.$stGroup.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaReEmissaoEconomico($stFiltro)
{
    $stSql  = "SELECT
       TABELA.cod_lancamento
     , TABELA.nr_parcela
     , TABELA.vencimento
     , TABELA.numeracao
     , TABELA.exercicio
     , TABELA.cod_carteira
     , TABELA.cod_convenio
     , TABELA.numcgm
     , TABELA.nom_cgm
     , TABELA.cod_credito
     , TABELA.cod_natureza
     , TABELA.cod_genero
     , TABELA.cod_especie
     , TABELA.descricao_credito
     , TABELA.convenio_atual
     , TABELA.inscricao
     , TABELA.inscricao_economica
     , TABELA.carteira_atual
     , TABELA.info_parcela
     , TABELA.numeracao_migrada
     , TABELA.origem
     , TABELA.situacao
     , TABELA.valida
     , SUM(TABELA.valor_normal) as valor
 FROM (
     select
           CASE WHEN MAX(carne_consolidacao.numeracao_consolidacao) IS NULL THEN
                al.cod_lancamento
           ELSE
                NULL
           END AS cod_lancamento
         , apar.cod_parcela
         , CASE WHEN max(carne_consolidacao.numeracao_consolidacao) IS NULL THEN
                apar.nr_parcela
           ELSE
                1
           END AS nr_parcela
         , COALESCE(aparr.valor, apar.valor) as valor_normal
         , CASE WHEN max(carne_consolidacao.numeracao_consolidacao) IS NULL THEN
                ( CASE WHEN aparr.vencimento IS NOT NULL THEN
                        to_char(aparr.vencimento,'dd/mm/YYYY')
                    ELSE
                        to_char(apar.vencimento,'dd/mm/YYYY')
                    END
                )
          ELSE
                to_char(apar.vencimento,'dd/mm/YYYY')
          END AS vencimento
         , CASE WHEN max(carne_consolidacao.numeracao_consolidacao) IS NOT NULL THEN
                max(carne_consolidacao.numeracao_consolidacao)
           ELSE
                max(carne.numeracao)
           END AS numeracao
         , ac.exercicio
         , carne.cod_carteira
         , carne.cod_convenio
         , sw_cgm.numcgm
         , sw_cgm.nom_cgm
         , credito.cod_credito
         , credito.cod_natureza
         , credito.cod_genero
         , credito.cod_especie
         , credito.descricao_credito
         , credito.cod_convenio as convenio_atual
         , cec.inscricao_economica as inscricao
         , cec.inscricao_economica
         , ( SELECT credito_carteira.cod_carteira
             FROM monetario.credito_carteira
             WHERE credito_carteira.cod_credito  = credito.cod_credito
             and credito_carteira.cod_convenio = credito.cod_convenio
             and credito_carteira.cod_natureza = credito.cod_natureza
             and credito_carteira.cod_genero   = credito.cod_genero
             and credito_carteira.cod_especie  = credito.cod_especie
         )  as carteira_atual
         , CASE WHEN max(carne_consolidacao.numeracao_consolidacao) IS NULL THEN
                CASE WHEN apar.nr_parcela = 0 THEN
                    'Única'
                ELSE
                    (apar.nr_parcela::varchar||'/'|| count(apar.nr_parcela))--arrecadacao.fn_total_parcelas(al.cod_lancamento))::varchar
                END
           ELSE
                '1/1'
           END AS info_parcela

        , arrecadacao.fn_numeracao_migrada(carne.numeracao) as numeracao_migrada

        , acgc.cod_grupo
        , ( CASE WHEN acgc.cod_grupo is not null THEN
                 acgc.cod_grupo ||' - '||agc.descricao
             ELSE
                 credito.cod_credito ||' - '|| credito.descricao_credito
             END
        ) as origem

        , arrecadacao.fn_situacao_carne(carne.numeracao,'f') as situacao
        , ( CASE WHEN apar.nr_parcela = 0
                AND arrecadacao.fn_situacao_carne(carne.numeracao,'f') = 'Vencida'
                AND baixa_manual_unica.valor = 'nao'
            THEN
                false
            ELSE
                true
            END
        ) as valida

     FROM

            arrecadacao.lancamento as al

            INNER JOIN (
                SELECT
                    max (alc.cod_calculo) as cod_calculo
                    , alc.cod_lancamento
                FROM
                    arrecadacao.lancamento_calculo as alc
                GROUP BY
                    alc.cod_lancamento
            ) as alc
            ON alc.cod_lancamento = al.cod_lancamento

            INNER JOIN arrecadacao.calculo as ac
            ON ac.cod_calculo = alc.cod_calculo

            INNER JOIN monetario.credito
            ON ac.cod_credito     = credito.cod_credito
            and ac.cod_natureza    = credito.cod_natureza
            and ac.cod_genero      = credito.cod_genero
            and ac.cod_especie     = credito.cod_especie

            INNER JOIN arrecadacao.cadastro_economico_calculo as cec
            ON cec.cod_calculo = ac.cod_calculo

            INNER JOIN arrecadacao.calculo_cgm
            ON calculo_cgm.cod_calculo = cec.cod_calculo

            INNER JOIN sw_cgm
            ON sw_cgm.numcgm = calculo_cgm.numcgm

            INNER JOIN arrecadacao.parcela as apar
            ON apar.cod_lancamento = al.cod_lancamento


            LEFT JOIN (
                select
                    exercicio
                    , valor
                from administracao.configuracao
                WHERE parametro = 'baixa_manual' AND cod_modulo = 25
            ) as baixa_manual_unica
            ON baixa_manual_unica.exercicio = ac.exercicio


            LEFT JOIN (
                SELECT
                    apr.vencimento
                    , apr.cod_parcela
                    , apr.valor
                FROM
                    arrecadacao.parcela_reemissao apr
                    INNER JOIN (
                        SELECT
                            MIN(app.timestamp) AS timestamp,
                            app.cod_parcela
                        FROM
                            arrecadacao.parcela_reemissao AS app
                        GROUP BY cod_parcela
                    )AS ap
                    ON ap.timestamp = apr.timestamp
                    and ap.cod_parcela = apr.cod_parcela
            )as aparr
            ON aparr.cod_parcela = apar.cod_parcela


            INNER JOIN arrecadacao.carne as carne
            ON carne.cod_parcela = apar.cod_parcela

            LEFT JOIN arrecadacao.carne_consolidacao
            ON carne.numeracao = carne_consolidacao.numeracao
            AND carne.cod_convenio = carne_consolidacao.cod_convenio

            LEFT JOIN arrecadacao.pagamento
            ON carne.numeracao = pagamento.numeracao
            AND pagamento.cod_convenio = carne.cod_convenio

            LEFT JOIN arrecadacao.calculo_grupo_credito as acgc
            ON acgc.cod_calculo = ac.cod_calculo
            AND acgc.ano_exercicio = ac.exercicio
            LEFT JOIN arrecadacao.grupo_credito agc
            ON agc.cod_grupo = acgc.cod_grupo
            AND agc.ano_exercicio = acgc.ano_exercicio
            --LEFT JOIN administracao.modulo admm
            --ON admm.cod_modulo = agc.cod_modulo



            ".$stFiltro."


        GROUP BY

            carne.numeracao
            , ac.exercicio
            , carne.cod_carteira
            , carne.cod_convenio
            , apar.cod_parcela
            , apar.nr_parcela
            , apar.valor
            , aparr.valor
            , apar.vencimento
            , al.cod_lancamento
            , aparr.vencimento
            , sw_cgm.numcgm
            , sw_cgm.nom_cgm
            , credito.cod_credito
            , credito.cod_natureza
            , credito.cod_genero
            , credito.cod_especie
            , credito.cod_convenio
            , credito.descricao_credito
            , cec.inscricao_economica
            , acgc.cod_grupo
            , agc.descricao
            , baixa_manual_unica.valor

    ) AS TABELA
    \n";

    return $stSql;

}

function recuperaListaReEmissaoImobiliario(&$rsRecordSet, $stFiltro = "", $stFiltroPos = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stGroup = " group by cod_lancamento
         , nr_parcela
         , vencimento
         , numeracao
         , exercicio
         , cod_carteira
         , cod_convenio
         , numcgm
         , nom_cgm
         , cod_credito
         , cod_natureza
         , cod_genero
         , cod_especie
         , descricao_credito
         , convenio_atual
         , inscricao
         , inscricao_municipal
         , carteira_atual
         , info_parcela
         , numeracao_migrada
         , cod_grupo
         , origem
         , situacao
         , valida ";
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_lancamento ";
    $stSql  = $this->montaRecuperaListaReEmissaoImobiliario( $stFiltro ).$stFiltroPos.$stGroup.$stOrdem;
    $this->setDebug($stSql);
    
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

// tunnig Gris
function montaRecuperaListaReEmissaoImobiliario($stFiltro)
{
    $stSql  = "
SELECT
        TABELA.cod_lancamento,
        --TABELA.cod_parcela,
        TABELA.nr_parcela,
        TABELA.vencimento,
        TABELA.numeracao,
        TABELA.exercicio,
        TABELA.cod_carteira,
        TABELA.cod_convenio,
        TABELA.numcgm,
        TABELA.nom_cgm,
        TABELA.cod_credito,
        TABELA.cod_natureza,
        TABELA.cod_genero,
        TABELA.cod_especie,
        TABELA.descricao_credito,
        TABELA.convenio_atual,
        TABELA.inscricao,
        TABELA.inscricao_municipal,
        TABELA.carteira_atual,
        TABELA.info_parcela,
        TABELA.numeracao_migrada,
        TABELA.cod_grupo,
        TABELA.origem,
        TABELA.situacao,
        TABELA.valida,
        sum(TABELA.valor_normal) as valor
FROM (
    select
           CASE WHEN MAX(carne_consolidacao.numeracao_consolidacao) IS NULL THEN
                al.cod_lancamento
           ELSE
                NULL
           END AS cod_lancamento
        , apar.cod_parcela
        , CASE WHEN max(carne_consolidacao.numeracao_consolidacao) IS NULL THEN
                apar.nr_parcela
          ELSE
                1
          END AS nr_parcela
        , COALESCE(aparr.valor, apar.valor) as valor_normal
        , CASE WHEN max(carne_consolidacao.numeracao_consolidacao) IS NULL THEN
                ( CASE WHEN aparr.vencimento IS NOT NULL THEN
                        to_char(aparr.vencimento,'dd/mm/YYYY')
                    ELSE
                        to_char(apar.vencimento,'dd/mm/YYYY')
                    END
                )
          ELSE
                to_char(apar.vencimento,'dd/mm/YYYY')
          END AS vencimento
        , CASE WHEN max(carne_consolidacao.numeracao_consolidacao) IS NOT NULL THEN
                max(carne_consolidacao.numeracao_consolidacao)
          ELSE
                max(carne.numeracao)
          END AS numeracao
--        , max(carne.numeracao) as numeracao
        , ac.exercicio
        , carne.cod_carteira
        , carne.cod_convenio
        , CAST((SELECT array_to_string( ARRAY( select numcgm from sw_cgm where numcgm IN ( SELECT numcgm FROM arrecadacao.calculo_cgm WHERE cod_calculo = ac.cod_calculo)), '/' ) ) AS VARCHAR) AS numcgm
        , CAST((SELECT array_to_string( ARRAY( select nom_cgm from sw_cgm where numcgm IN ( SELECT numcgm FROM arrecadacao.calculo_cgm WHERE cod_calculo = ac.cod_calculo)), '/' ) ) AS VARCHAR) AS nom_cgm
        , credito.cod_credito
        , credito.cod_natureza
        , credito.cod_genero
        , credito.cod_especie
        , credito.descricao_credito
        , credito.cod_convenio as convenio_atual
        , aic.inscricao_municipal as inscricao
        , aic.inscricao_municipal
        , ( SELECT credito_carteira.cod_carteira
            FROM monetario.credito_carteira
            WHERE credito_carteira.cod_credito  = credito.cod_credito
            and credito_carteira.cod_convenio = credito.cod_convenio
            and credito_carteira.cod_natureza = credito.cod_natureza
            and credito_carteira.cod_genero   = credito.cod_genero
            and credito_carteira.cod_especie  = credito.cod_especie
        )  as carteira_atual
--        , arrecadacao.fn_info_parcela( apar.cod_parcela ) as info_parcela

        , CASE WHEN max(carne_consolidacao.numeracao_consolidacao) IS NULL THEN
                arrecadacao.fn_info_parcela( apar.cod_parcela )
          ELSE
                '1/1'
          END AS info_parcela

        , arrecadacao.fn_numeracao_migrada(carne.numeracao) as numeracao_migrada

        , acgc.cod_grupo
        , ( CASE WHEN acgc.cod_grupo is not null THEN
                 acgc.cod_grupo ||' - '||agc.descricao
             ELSE
                 credito.cod_credito ||' - '|| credito.descricao_credito
             END
        ) as origem

        , arrecadacao.fn_situacao_carne(carne.numeracao,'f') as situacao
        , ( CASE WHEN apar.nr_parcela = 0
                AND arrecadacao.fn_situacao_carne(carne.numeracao,'f') = 'Vencida'
                AND baixa_manual_unica.valor = 'nao'
            THEN
                false
            ELSE
                true
            END
        ) as valida
--      , carne_consolidacao.numeracao_consolidacao
     FROM

            arrecadacao.lancamento as al

            INNER JOIN (
                SELECT
                    max (alc.cod_calculo) as cod_calculo
                    , alc.cod_lancamento
                FROM
                    arrecadacao.lancamento_calculo as alc
                GROUP BY
                    alc.cod_lancamento
            ) as alc
            ON alc.cod_lancamento = al.cod_lancamento

            INNER JOIN arrecadacao.calculo as ac
            ON ac.cod_calculo = alc.cod_calculo

            INNER JOIN monetario.credito
            ON ac.cod_credito     = credito.cod_credito
            and ac.cod_natureza    = credito.cod_natureza
            and ac.cod_genero      = credito.cod_genero
            and ac.cod_especie     = credito.cod_especie

            INNER JOIN arrecadacao.imovel_calculo as aic
            ON aic.cod_calculo = ac.cod_calculo

            INNER JOIN arrecadacao.parcela as apar
            ON apar.cod_lancamento = al.cod_lancamento


            LEFT JOIN (
                select
                    exercicio
                    , valor
                from administracao.configuracao
                WHERE parametro = 'baixa_manual_unica' AND cod_modulo = 25
            ) as baixa_manual_unica
            ON baixa_manual_unica.exercicio = ac.exercicio


            LEFT JOIN (
                SELECT
                    apr.vencimento
                    , apr.cod_parcela
                    , apr.valor
                FROM
                    arrecadacao.parcela_reemissao apr
                    INNER JOIN (
                        SELECT
                            MIN(app.timestamp) AS timestamp,
                            app.cod_parcela
                        FROM
                            arrecadacao.parcela_reemissao AS app
                        GROUP BY cod_parcela
                    )AS ap
                    ON ap.timestamp = apr.timestamp
                    and ap.cod_parcela = apr.cod_parcela
            )as aparr
            ON aparr.cod_parcela = apar.cod_parcela


            INNER JOIN arrecadacao.carne as carne
            ON carne.cod_parcela = apar.cod_parcela

            LEFT JOIN arrecadacao.carne_consolidacao
            ON carne.numeracao = carne_consolidacao.numeracao
            AND carne.cod_convenio = carne_consolidacao.cod_convenio

            LEFT JOIN arrecadacao.calculo_grupo_credito as acgc
            ON acgc.cod_calculo = ac.cod_calculo

            LEFT JOIN arrecadacao.grupo_credito agc
            ON agc.cod_grupo = acgc.cod_grupo
            AND agc.ano_exercicio = acgc.ano_exercicio

            LEFT JOIN administracao.modulo admm
            ON admm.cod_modulo = agc.cod_modulo



            ".$stFiltro."

        GROUP BY

            carne.numeracao
            , ac.exercicio
            , ac.cod_calculo
            , carne.cod_carteira
            , carne.cod_convenio
            , apar.cod_parcela
            , apar.nr_parcela
            , aparr.valor
            , apar.valor
            , apar.vencimento
            , al.cod_lancamento
            , aparr.vencimento
            , credito.cod_credito
            , credito.cod_natureza
            , credito.cod_genero
            , credito.cod_especie
            , credito.cod_convenio
            , credito.descricao_credito
            , aic.inscricao_municipal
            , acgc.cod_grupo
            , agc.descricao
            , baixa_manual_unica.valor

    ) AS TABELA

    \n";

    return $stSql;

}

function recuperaListaReEmissaoCgm(&$rsRecordSet, $stFiltro = "", $stFiltroExercicioPagamentos = "", $stFiltroPos ="", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_lancamento ";
    $stSql  = $this->montaRecuperaListaReEmissaoCgm($stFiltro).$stFiltroPos.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaReEmissaoCgm($stFiltro)
{
$stSql  = "
  SELECT todos.* FROM (
 SELECT
       TABELA.cod_lancamento
     , TABELA.nr_parcela
     , TABELA.vencimento
     , TABELA.numeracao
     , TABELA.exercicio
     , TABELA.cod_carteira
     , TABELA.cod_convenio
     , TABELA.numcgm
     , TABELA.nom_cgm
     , TABELA.cod_credito
     , TABELA.cod_natureza
     , TABELA.cod_genero
     , TABELA.cod_especie
     , TABELA.descricao_credito
     , TABELA.convenio_atual
     , TABELA.inscricao_economica
     , TABELA.inscricao_municipal
     , TABELA.inscricao
     , TABELA.carteira_atual
     , TABELA.info_parcela
     , TABELA.numeracao_migrada
     , TABELA.cod_grupo
     , TABELA.origem
     , TABELA.situacao
     , TABELA.valida
     , SUM(TABELA.valor_normal)  as valor
 FROM (
     select
           CASE WHEN MAX(carne_consolidacao.numeracao_consolidacao) IS NULL THEN
                al.cod_lancamento
           ELSE
                NULL
           END AS cod_lancamento
         , apar.cod_parcela
         , CASE WHEN MAX(carne_consolidacao.numeracao_consolidacao) IS NULL THEN
                apar.nr_parcela
           ELSE
                1
           END AS nr_parcela
         , COALESCE(aparr.valor, apar.valor) as valor_normal
         , CASE WHEN MAX(carne_consolidacao.numeracao_consolidacao) IS NULL THEN
                CASE WHEN aparr.vencimento IS NOT NULL THEN
                    to_char(aparr.vencimento,'dd/mm/YYYY')
                ELSE
                    to_char(apar.vencimento,'dd/mm/YYYY')
                END
           ELSE
                to_char(apar.vencimento,'dd/mm/YYYY')
           END AS vencimento
         , CASE WHEN MAX(carne_consolidacao.numeracao_consolidacao) IS NOT NULL THEN
                max(carne_consolidacao.numeracao_consolidacao)
           ELSE
                max(carne.numeracao)
           END as numeracao
         , carne.exercicio
         , carne.cod_carteira
         , carne.cod_convenio
         , sw_cgm.numcgm
         , sw_cgm.nom_cgm
         , credito.cod_credito
         , credito.cod_natureza
         , credito.cod_genero
         , credito.cod_especie
         , credito.descricao_credito
         , credito.cod_convenio as convenio_atual
         , cec.inscricao_economica
         , aic.inscricao_municipal
         , ( CASE WHEN cec.inscricao_economica IS NOT NULL THEN
                 cec.inscricao_economica
             WHEN aic.inscricao_municipal IS NOT NULL THEN
                 aic.inscricao_municipal
             END
         ) AS inscricao

         , ( SELECT credito_carteira.cod_carteira
             FROM monetario.credito_carteira
             WHERE credito_carteira.cod_credito  = credito.cod_credito
             and credito_carteira.cod_convenio = credito.cod_convenio
             and credito_carteira.cod_natureza = credito.cod_natureza
             and credito_carteira.cod_genero   = credito.cod_genero
             and credito_carteira.cod_especie  = credito.cod_especie
         )  as carteira_atual
         , CASE WHEN MAX(carne_consolidacao.numeracao_consolidacao) IS NULL THEN
                arrecadacao.fn_info_parcela( apar.cod_parcela )
           ELSE
                '1/1'
           END AS info_parcela

        , arrecadacao.fn_numeracao_migrada(carne.numeracao) as numeracao_migrada

        , acgc.cod_grupo
        , ( CASE WHEN acgc.cod_grupo is not null THEN
                 acgc.cod_grupo ||' - '||agc.descricao
             ELSE
                 credito.cod_credito ||' - '|| credito.descricao_credito
             END
        ) as origem

        , arrecadacao.fn_situacao_carne(carne.numeracao,'f') as situacao
        , ( CASE WHEN apar.nr_parcela = 0
                AND arrecadacao.fn_situacao_carne(carne.numeracao,'f') = 'Vencida'
                AND baixa_manual_unica.valor = 'nao'
            THEN
                false
            ELSE
                true
            END
        ) as valida

     FROM

            arrecadacao.lancamento as al

            INNER JOIN (
                SELECT
                    max (alc.cod_calculo) as cod_calculo
                    , alc.cod_lancamento
                FROM
                    arrecadacao.lancamento_calculo as alc
                GROUP BY
                    alc.cod_lancamento
            ) as alc
            ON alc.cod_lancamento = al.cod_lancamento

            INNER JOIN arrecadacao.calculo as ac
            ON ac.cod_calculo = alc.cod_calculo

            INNER JOIN monetario.credito
            ON ac.cod_credito     = credito.cod_credito
            and ac.cod_natureza    = credito.cod_natureza
            and ac.cod_genero      = credito.cod_genero
            and ac.cod_especie     = credito.cod_especie

            LEFT join arrecadacao.imovel_calculo aic
            ON aic.cod_calculo = ac.cod_calculo

            LEFT join arrecadacao.cadastro_economico_calculo cec
            ON cec.cod_calculo = ac.cod_calculo

            INNER JOIN arrecadacao.calculo_cgm
            ON calculo_cgm.cod_calculo = ac.cod_calculo

            INNER JOIN sw_cgm
            ON sw_cgm.numcgm = calculo_cgm.numcgm

            INNER JOIN arrecadacao.parcela as apar
            ON apar.cod_lancamento = al.cod_lancamento


            LEFT JOIN (
                select
                    exercicio
                    , valor
                from administracao.configuracao
                WHERE parametro = 'baixa_manual' AND cod_modulo = 25
            ) as baixa_manual_unica
            ON baixa_manual_unica.exercicio = ac.exercicio


            LEFT JOIN (
                SELECT
                    apr.vencimento
                    , apr.cod_parcela
                    , apr.valor
                FROM
                    arrecadacao.parcela_reemissao apr
                    INNER JOIN (
                        SELECT
                            MIN(app.timestamp) AS timestamp,
                            app.cod_parcela
                        FROM
                            arrecadacao.parcela_reemissao AS app
                        GROUP BY cod_parcela
                    )AS ap
                    ON ap.timestamp = apr.timestamp
                    and ap.cod_parcela = apr.cod_parcela
            )as aparr
            ON aparr.cod_parcela = apar.cod_parcela


            INNER JOIN arrecadacao.carne as carne
            ON carne.cod_parcela = apar.cod_parcela
            AND carne.exercicio = ac.exercicio

            LEFT JOIN arrecadacao.carne_consolidacao
            ON carne.numeracao = carne_consolidacao.numeracao
            AND carne.cod_convenio = carne_consolidacao.cod_convenio

            LEFT JOIN arrecadacao.calculo_grupo_credito as acgc
            ON acgc.cod_calculo = ac.cod_calculo
            AND acgc.ano_exercicio = ac.exercicio
            LEFT JOIN arrecadacao.grupo_credito agc
            ON agc.cod_grupo = acgc.cod_grupo
            AND agc.ano_exercicio = acgc.ano_exercicio
            LEFT JOIN administracao.modulo admm
            ON admm.cod_modulo = agc.cod_modulo



            ".$stFiltro."

        GROUP BY

            carne.numeracao
            , carne.exercicio
            , carne.cod_carteira
            , carne.cod_convenio
            , apar.cod_parcela
            , apar.nr_parcela
            , apar.valor
            , aparr.valor
            , apar.vencimento
            , al.cod_lancamento
            , aparr.vencimento
            , sw_cgm.numcgm
            , sw_cgm.nom_cgm
            , credito.cod_credito
            , credito.cod_natureza
            , credito.cod_genero
            , credito.cod_especie
            , credito.cod_convenio
            , credito.descricao_credito
            , aic.inscricao_municipal
            , cec.inscricao_economica
            , acgc.cod_grupo
            , agc.descricao
            , baixa_manual_unica.valor

    ) AS TABELA


    \n";

    return $stSql;

}

function recuperaListaReEmissaoDividaAtiva(&$rsRecordSet, $stFiltro = "", $stFiltroPos = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stGroup = " GROUP BY cod_lancamento
        , inscricao
        , nr_parcela
        , vencimento
        , numeracao
        , cod_convenio
        , cod_carteira
        , numcgm
        , nom_cgm
        , info_parcela
        , numeracao_migrada
        , origem
        , valida
        , numero_parcelamento
        , exercicio
        , situacao ";
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_lancamento ";
    $stSql  = $this->montaRecuperaListaReEmissaoDividaAtiva($stFiltro).$stFiltroPos.$stGroup.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaReEmissaoDividaAtiva($stFiltro)
{
    $stSql  = " SELECT tabela.cod_lancamento
                    , tabela.inscricao
--                    , tabela.cod_parcela
                    , tabela.nr_parcela
--                    , tabela.valor_normal
                    , tabela.vencimento
                    , tabela.numeracao
                    , tabela.cod_convenio
                    , tabela.cod_carteira
                    , tabela.numcgm
                    , tabela.nom_cgm
                    , tabela.info_parcela
                    , tabela.numeracao_migrada
                    , tabela.origem
                    , CASE WHEN tabela.nr_parcela         = 0
                            AND tabela.situacao           = 'Vencida'
                            AND tabela.baixa_manual_unica = 'nao' THEN
                            FALSE
                      ELSE
                            TRUE
                      END AS valida
                    , tabela.numero_parcelamento
                    , tabela.exercicio
                    , CASE WHEN tabela.situacao = 'Vencida' THEN
                          SUM(tabela.valor_normal)::numeric(14,2)
                      ELSE
                          SUM(arrecadacao.buscaValorParcela(tabela.cod_parcela))::numeric(14,2)
                      END AS valor
                FROM (
                           select distinct CASE WHEN MAX(carne_consolidacao.numeracao_consolidacao) IS NULL THEN
                                      lancamento.cod_lancamento
                                  ELSE
                                      NULL
                                  END AS cod_lancamento
                                , lista_inscricao_imob_eco_cgm_por_num_parcelamento( parcelamento.num_parcelamento )    AS inscricao
                                , parcela.cod_parcela
                                , CASE WHEN MAX(carne_consolidacao.numeracao_consolidacao) IS NULL THEN
                                        parcela.nr_parcela
                                  ELSE
                                        1
                                  END AS nr_parcela
                                , parcela.valor                                                                         AS valor_normal
                                , CASE WHEN MAX(carne_consolidacao.numeracao_consolidacao) IS NULL THEN
                                        ( CASE WHEN busca_primeira_reemissao(parcela.cod_parcela) IS NOT NULL THEN
                                                to_char(busca_primeira_reemissao(parcela.cod_parcela),'dd/mm/YYYY')
                                            ELSE
                                                to_char(parcela.vencimento,'dd/mm/YYYY')
                                            END
                                        )
                                  ELSE
                                        to_char(parcela.vencimento,'dd/mm/YYYY')
                                  END AS vencimento
                                , CASE WHEN MAX(carne_consolidacao.numeracao_consolidacao) IS NOT NULL THEN
                                        max(carne_consolidacao.numeracao_consolidacao)
                                  ELSE
                                        max(carne.numeracao)
                                  END AS numeracao
                                , carne.cod_convenio
                                , carne.cod_carteira
                                , calculo_cgm.numcgm
                                , (
                                    SELECT nom_cgm
                                      FROM sw_cgm
                                     WHERE sw_cgm.numcgm = calculo_cgm.numcgm
                                )                                                                                       AS nom_cgm
                                , CASE WHEN MAX(carne_consolidacao.numeracao_consolidacao) IS NULL THEN
                                      CASE WHEN parcela.nr_parcela = 0 THEN
                                          'Única'
                                      ELSE
                                          (parcela.nr_parcela::varchar||'/'|| arrecadacao.fn_total_parcelas(lancamento.cod_lancamento))::varchar
                                      END
                                  ELSE
                                        '1/1'
                                  END AS info_parcela
                                , (
                                    SELECT numeracao_migracao
                                      FROM arrecadacao.carne_migracao
                                     WHERE numeracao = carne.numeracao
                                  )                                                                                     AS numeracao_migrada
                                , parcelamento.numero_parcelamento                                                      AS origem
                                , arrecadacao.fn_situacao_carne(carne.numeracao,'f')                                    AS situacao
                                , parcelamento.numero_parcelamento
                                , parcelamento.exercicio
                                , baixa_manual_unica.valor                                                              AS baixa_manual_unica
                             FROM divida.parcelamento
                       INNER JOIN divida.parcela_origem
                               ON parcela_origem.num_parcelamento = parcelamento.num_parcelamento
                       INNER JOIN divida.divida_parcelamento
                               ON parcelamento.num_parcelamento = divida.divida_parcelamento.num_parcelamento
                        LEFT JOIN divida.divida_cancelada
                               ON divida_cancelada.cod_inscricao = divida_parcelamento.cod_inscricao
                              AND divida_cancelada.exercicio = divida_parcelamento.exercicio
                       INNER JOIN divida.parcela AS divida_parcela
                               ON divida_parcela.num_parcelamento = divida_parcelamento.num_parcelamento
                              AND divida_parcela.paga = false
                              AND divida_parcela.cancelada = false
                       INNER JOIN divida.parcela_calculo
                               ON parcela_calculo.num_parcelamento = divida_parcela.num_parcelamento
                              AND parcela_calculo.num_parcela      = divida_parcela.num_parcela
                       INNER JOIN arrecadacao.calculo
                               ON calculo.cod_calculo = parcela_calculo.cod_calculo
                       INNER JOIN arrecadacao.calculo_cgm
                               ON calculo_cgm.cod_calculo = calculo.cod_calculo
                       INNER JOIN arrecadacao.lancamento_calculo
                               ON lancamento_calculo.cod_calculo = calculo.cod_calculo
                       INNER JOIN arrecadacao.lancamento
                               ON lancamento.cod_lancamento = lancamento_calculo.cod_lancamento
                       INNER JOIN arrecadacao.parcela
                               ON parcela.cod_lancamento = lancamento.cod_lancamento
                        LEFT JOIN (
                                    select exercicio
                                         , valor
                                      from administracao.configuracao
                                     WHERE parametro = 'baixa_manual'
                                       AND cod_modulo = 25
                                  ) AS baixa_manual_unica
                               ON baixa_manual_unica.exercicio = calculo.exercicio
                       INNER JOIN arrecadacao.carne
                               ON carne.cod_parcela = parcela.cod_parcela
                        LEFT JOIN arrecadacao.carne_consolidacao
                               ON carne_consolidacao.numeracao = carne.numeracao
                              AND carne_consolidacao.cod_convenio = carne.cod_convenio
                       INNER JOIN monetario.credito
                               ON calculo.cod_credito     = credito.cod_credito
                              and calculo.cod_natureza    = credito.cod_natureza
                              and calculo.cod_genero      = credito.cod_genero
                              and calculo.cod_especie     = credito.cod_especie

                            ".$stFiltro."

                         GROUP BY carne.numeracao
                                , carne.exercicio
                                , carne.cod_carteira
                                , carne.cod_convenio
                                , parcela.cod_parcela
                                , parcela.nr_parcela
                                , parcela.valor
                                , parcela.vencimento
                                , lancamento.cod_lancamento
                                , calculo_cgm.numcgm
                                , divida_parcelamento.cod_inscricao
                                , credito.cod_credito
                                , credito.cod_natureza
                                , credito.cod_genero
                                , credito.cod_especie
                                , credito.descricao_credito
                                , divida_parcelamento.exercicio
                                , baixa_manual_unica.valor
                                , parcelamento.numero_parcelamento
                                , parcelamento.num_parcelamento
                                , parcelamento.exercicio
                     ) AS TABELA

    \n";

    return $stSql;
}

function recuperaBaixaDebitos(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_lancamento ";
    $arFiltro = explode("|tp|",$stFiltro);
    $stSql  = $this->montaRecuperaBaixaDebitos($arFiltro[1]).$arFiltro[0].$stOrdem;
    $this->setDebug($stSql);
    //$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    return $obErro;
}

function montaRecuperaBaixaDebitos($stTipo)
{
    $stSql = "           SELECT carne.numeracao                                                                    \n";
    $stSql .= "                , carne.exercicio                                                                    \n";
    $stSql .= "                , lanc.cod_lancamento                                                                \n";
    $stSql .= "                , cgm.numcgm                                                                         \n";
    $stSql .= "                , cgm.nom_cgm                                                                        \n";
    $stSql .= "                , grupo.cod_grupo                                                                    \n";
    $stSql .= "                , grupo.descricao                                                                    \n";
    $stSql .= "                , credito.cod_credito                                                                \n";
    $stSql .= "                , credito.cod_especie                                                                \n";
    $stSql .= "                , credito.cod_genero                                                                 \n";
    $stSql .= "                , credito.cod_natureza                                                               \n";
    $stSql .= "                , credito.descricao_credito                                                          \n";
    $stSql .= "                , (parcela.nr_parcela::varchar||'/'||                                                \n";
    $stSql .= "                    arrecadacao.fn_total_parcelas(lanc.cod_lancamento)::varchar                          \n";
    $stSql .= "                  ) as info_parcela                                                                      \n";
    if ( $stTipo == "ii")       $stSql .= " , icalculo.inscricao_municipal as inscricao                                 \n";
    elseif ( $stTipo == "ie")   $stSql .= " , ecalculo.inscricao_economica as inscricao                                 \n";
    else                        $stSql .= " , '' as inscricao                                                           \n";
    $stSql .= "             FROM arrecadacao.carne carne                                                                \n";
    $stSql .= "       INNER JOIN arrecadacao.parcela parcela                                                            \n";
    $stSql .= "               ON parcela.cod_parcela = carne.cod_parcela                                                \n";
    $stSql .= "       INNER JOIN arrecadacao.lancamento lanc                                                            \n";
    $stSql .= "               ON lanc.cod_lancamento = parcela.cod_lancamento                                           \n";
    $stSql .= "       INNER JOIN arrecadacao.calculo calculo                                                            \n";
    $stSql .= "               ON calculo.cod_calculo = ( SELECT cod_calculo                                             \n";
    $stSql .= "                                          FROM arrecadacao.fn_calculo_lancamento(lanc.cod_lancamento)    \n";
    $stSql .= "                                          AS (cod_calculo int))                                                                        \n";

    if ( $stTipo == "ii")
        $stSql .= " INNER JOIN arrecadacao.imovel_calculo icalculo ON icalculo.cod_calculo = calculo.cod_calculo               \n";
    elseif ( $stTipo == "ie")
        $stSql .= " INNER JOIN arrecadacao.cadastro_economico_calculo ecalculo ON ecalculo.cod_calculo = calculo.cod_calculo    \n";

    $stSql .= "       INNER JOIN arrecadacao.lancamento_calculo lancc                                                   \n";
    $stSql .= "               ON lancc.cod_calculo = calculo.cod_calculo                                                \n";
    $stSql .= "               AND lancc.cod_lancamento = lanc.cod_lancamento                                            \n";
    $stSql .= "       INNER JOIN arrecadacao.calculo_cgm ccgm                                                           \n";
    $stSql .= "               ON ccgm.cod_calculo = calculo.cod_calculo                                                 \n";
    $stSql .= "       INNER JOIN sw_cgm cgm                                                                             \n";
    $stSql .= "               ON cgm.numcgm = ccgm.numcgm                                                               \n";
    $stSql .= "       INNER JOIN monetario.credito credito                                                              \n";
    $stSql .= "               ON credito.cod_credito = calculo.cod_credito                                              \n";
    $stSql .= "              AND credito.cod_especie = calculo.cod_especie                                              \n";
    $stSql .= "              AND credito.cod_genero  = calculo.cod_genero                                               \n";
    $stSql .= "              AND credito.cod_natureza= calculo.cod_natureza                                             \n";
    $stSql .= "        LEFT JOIN (  SELECT gc.cod_grupo, gc.descricao, gc.ano_exercicio, cgc.cod_calculo, m.cod_modulo  \n";
    $stSql .= "                     FROM arrecadacao.calculo_grupo_credito cgc                                          \n";
    $stSql .= "                     INNER JOIN arrecadacao.grupo_credito gc ON gc.cod_grupo     = cgc.cod_grupo         \n";
    $stSql .= "                                                             AND gc.ano_exercicio = cgc.ano_exercicio    \n";
    $stSql .= "                     INNER JOIN administracao.modulo m       ON m.cod_modulo     = gc.cod_modulo         \n";
    $stSql .= "                  ) as grupo ON grupo.cod_calculo = calculo.cod_calculo                                  \n";
    $stSql .= "                             AND grupo.ano_exercicio = calculo.exercicio                                 \n";

    return $stSql;
}

function recuperaNumeracao(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaNumeracao().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaNumeracao()
{
    $stSql .= " select cm.numeracao,c.cod_convenio,p.cod_parcela                                        \n";
    $stSql .= " from arrecadacao.carne_migracao cm, arrecadacao.carne c, arrecadacao.parcela p          \n";
    $stSql .= " where c.numeracao=cm.numeracao and c.cod_parcela=p.cod_parcela                          \n";

    return $stSql;
}

function recuperaListaPagamentosConsulta(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_parcela ";
    $stSql  = $this->montaRecuperaListaPagamentosConsulta().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaListaPagamentosConsulta()
{
    $stSql  = "   select c.numeracao                                                                        \n";
    $stSql .= "        , arrecadacao.buscaNumeracaoMigracao(c.numeracao,c.cod_convenio) as num_migrada      \n";
    $stSql .= "        , c.cod_convenio                                                                     \n";
    $stSql .= "        , c.exercicio                                                                        \n";
    $stSql .= "        , pag.valor                                                                          \n";
    $stSql .= "        , pag.ocorrencia_pagamento                                                           \n";
    $stSql .= "        , to_char(pag.data_pagamento, 'dd/mm/YYYY') as data_pagamento                        \n";
    $stSql .= "        , pag.data_pagamento as data_pagamento_us                                            \n";
    $stSql .= "        , p.cod_lancamento                                                                   \n";
    $stSql .= "        , p.cod_parcela                                                                      \n";
    $stSql .= "        , to_char(p.vencimento, 'dd/mm/YYYY') as vencimento                                  \n";
    $stSql .= "        , pag.data_pagamento as data_pagamento_us                                            \n";
    $stSql .= "        , to_char(now()::date, 'dd/mm/YYYY') as dtDatabase_br                                \n";
    $stSql .= "        , nr_parcela||'/'||arrecadacao.fn_total_parcelas(p.cod_lancamento) as info_parcela   \n";
    $stSql .= "     from arrecadacao.parcela p                                                              \n";
    $stSql .= "        INNER JOIN arrecadacao.carne c ON c.cod_parcela = p.cod_parcela                      \n";
    $stSql .= "        INNER JOIN arrecadacao.pagamento pag ON pag.numeracao = c.numeracao                  \n";
    $stSql .= "                              AND pag.cod_convenio = c.cod_convenio                          \n";

    return $stSql;
}

function verificaCarneEconomico(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY p.cod_parcela ";
    $stSql  = $this->montaVerificaCarneEconomico().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaVerificaCarneEconomico()
{
    $stSql = "     SELECT                                                              \n";
    $stSql .= "         lc.cod_calculo,p.valor,cec.inscricao_economica                  \n";
    $stSql .= "     FROM                                                                \n";
    $stSql .= "         arrecadacao.carne c,                                            \n";
    $stSql .= "         arrecadacao.parcela p,                                          \n";
    $stSql .= "         arrecadacao.lancamento l,                                       \n";
    $stSql .= "         arrecadacao.lancamento_calculo lc,                              \n";
    $stSql .= "         arrecadacao.cadastro_economico_calculo cec                      \n";
    $stSql .= "     WHERE                                                               \n";
    $stSql .= "         p.cod_parcela = c.cod_parcela and                               \n";
    $stSql .= "         l.cod_lancamento = p.cod_lancamento and                         \n";
    $stSql .= "         lc.cod_lancamento = l.cod_lancamento and                        \n";
    $stSql .= "         cec.cod_calculo = lc.cod_calculo                                \n";

    return $stSql;
}

function listaParcelasLancamento(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY p.nr_parcela ";
    $stSql  = $this->montaListaParcelasLancamento().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaListaParcelasLancamento()
{
    $stSql = "     SELECT                                                                              \n";
    $stSql .= "         c.numeracao, c.cod_convenio, p.nr_parcela,                                      \n";
    $stSql .= "         arrecadacao.consultaCarneDevolucao(c.numeracao,c.cod_convenio) as devolucao,    \n";
    $stSql .= "         arrecadacao.consultaCarnePago(c.numeracao) as pago                              \n";
    $stSql .= "     FROM                                                                                \n";
    $stSql .= "         arrecadacao.carne c                                                             \n";
    $stSql .= "         INNER JOIN arrecadacao.parcela p ON p.cod_parcela = c.cod_parcela               \n";

    return $stSql;
}

function listaCarnesParaCancelar(&$rsRecordSet, $inCodParcela = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaListaCarnesParaCancelar($inCodParcela);
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaListaCarnesParaCancelar($inCodParcela)
{
    $stSql .= " SELECT
                    carne.numeracao,
                    carne.cod_convenio
                FROM
                    arrecadacao.carne

                LEFT JOIN
                    arrecadacao.carne_devolucao
                ON
                    carne_devolucao.numeracao = carne.numeracao

                WHERE
                    carne_devolucao.numeracao IS NULL
                    AND carne.cod_parcela = ".$inCodParcela;

    return $stSql;
}

function verificaCarneImobiliario(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaVerificaCarneImobiliario().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaVerificaCarneImobiliario()
{
    $stSql = "   SELECT                                                                \n";
    $stSql .= "       ic.inscricao_municipal                                            \n";
    $stSql .= "   FROM                                                                  \n";
    $stSql .= "       arrecadacao.imovel_calculo ic                                     \n";
    $stSql .= "   WHERE                                                                 \n";
    $stSql .= "       ic.inscricao_municipal IS NOT NULL and                            \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaValorParcelaJuroMulta(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaValorParcelaJuroMulta();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaValorParcelaJuroMulta()
{
    $stSql = "SELECT                                                                                               \n";
    $stSql .= " fn_juro_multa_proporcional( '".$this->getDado('data_vencimento' )."'                                \n";
    $stSql .= "                                  ,'".$this->getDado('data_pagamento' )."'                           \n";
    $stSql .= "                                  ,'".$this->getDado('valor' )."'                                    \n";
    $stSql .=") as parcela_juro_multa;                                                                              \n";

    return $stSql;
}

function recuperaListaConsultaCarne(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $stTipo="", $boNumeracao= "", $stFiltroExercicio ="", $stFiltroExercicio2 ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaListaConsultaCarne( $stFiltro, $stTipo, $boNumeracao, $stFiltroExercicio, $stFiltroExercicio2 ).$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaConsultaCarne($stFiltro,$stTipo,$boNumeracao,$stFiltroExercicio,$stFiltroExercicio2)
{
#=============================   COLETA DOS DADOS =======================
    $stSql  = " select DISTINCT                                                                     \n";
    $stSql .= "     lancamentos.*,                                                                  \n";
    $stSql .= "     ( split_part( lancamentos.origem_info, '§', 1) ) as cod_credito,                \n";
    $stSql .= "     ( split_part( lancamentos.origem_info, '§', 2) ) as cod_grupo,                  \n";
    $stSql .= "  CASE WHEN split_part( lancamentos.origem_info, '§', 4) = '' then
    split_part( lancamentos.origem_info, '§', 3)
      ELSE
          ( split_part( lancamentos.origem_info, '§', 3) ||' / '|| split_part(
lancamentos.origem_info, '§', 4))
    end as origem, \n";

    $stSql .= "     ( split_part( lancamentos.origem_info, '§', 4) ) as exercicio_origem,           \n";
    $stSql .= "     ( split_part( lancamentos.origem_info, '§', 6 )) as cod_modulo                  \n";
    $stSql .= "     , arrecadacao.fn_busca_lancamento_situacao( lancamentos.cod_lancamento )        \n";
    $stSql .= "     as situacao_lancamento                                                          \n";
    $stSql .= " FROM                                                                                \n";
    $stSql .= "(                                                                                    \n";
    $stSql .= "     select                                                                          \n";
    $stSql .= "     alc.cod_lancamento,                                                             \n";
    $stSql .= "     calc.exercicio,                                                                 \n";
    $stSql .= "     arrecadacao.fn_busca_origem_lancamento_sem_exercicio( alc.cod_lancamento, 2, 2  \n";
    $stSql .= "     ) as origem_info                                                                \n";

    if ($boNumeracao) {
        $stSql .= "     ,( case when naccon.numeracao is not null then                              \n";
        $stSql .= "       '(Cons. : '|| naccon.numeracao_consolidacao ||')'                         \n";
        $stSql .= "       else                                                                      \n";
        $stSql .= "         null                                                                    \n";
        $stSql .= "       end                                                                       \n";
        $stSql .= "     )as origem_consolidacao                                                     \n";
    }
    if ( $stTipo == "ii")
        $stSql .= "  , ic.inscricao_municipal as inscricao                                          \n";
    elseif ( $stTipo == "ie")
        $stSql .= " , cec.inscricao_economica as inscricao                                          \n";
    else {
        $stSql .= "     , (case                                                                     \n";
        $stSql .= "     when ic.cod_calculo  is not null then                                       \n";
        $stSql .= "         ic.inscricao_municipal                                                  \n";
        $stSql .= "     when cec.cod_calculo is not null then                                       \n";
        $stSql .= "         cec.inscricao_economica                                                 \n";
        $stSql .= "     end) as inscricao                                                           \n";
    }

    if ($stTipo == "ii") {
        $stSql .= "    ,descobreProprietarios(alc.cod_lancamento) as proprietarios                     \n";
    } else {
        $stSql .= "    ,( arrecadacao.buscaCgmLancamento (alc.cod_lancamento)||' - '||                  \n";
        $stSql .= "       arrecadacao.buscaContribuinteLancamento(alc.cod_lancamento)                   \n";
        $stSql .= "    )::varchar as proprietarios                                                      \n";
    }

    if ( $stTipo == "ii")
        $stSql .= " ,arrecadacao.fn_consulta_endereco_imovel(ic.inscricao_municipal) as dados_complementares  \n";
    elseif ( $stTipo == "ie")
        $stSql .= " ,arrecadacao.fn_consulta_endereco_empresa(cec.inscricao_economica) as dados_complementares\n";
    else {
        $stSql .= " , (case                                                                         \n";
        $stSql .= "     when ic.cod_calculo  is not null then                                       \n";
        $stSql .= "         arrecadacao.fn_consulta_endereco_imovel(ic.inscricao_municipal)         \n";
        $stSql .= "     when cec.cod_calculo is not null then                                       \n";
        $stSql .= "         arrecadacao.fn_consulta_endereco_empresa(cec.inscricao_economica)       \n";
        $stSql .= "     end) as dados_complementares                                                \n";
    }

    if ($stTipo != "ie") {
        $stSql .= "     , ( case when ic.cod_calculo  is not null then
                                arrecadacao.fn_ultimo_venal_por_im_lanc(ic.inscricao_municipal,  alc.cod_lancamento)
                            end
                        ) as venal                                                                  \n";

        $stSql .= "    , arrecadacao.fn_tipo_ultimo_venal_por_im_lanc(ic.inscricao_municipal,  alc.cod_lancamento) AS tipo_venal \n";
    }

    $stSql .= "     ,coalesce(al.total_parcelas,0)::int as num_parcelas                             \n";
    $stSql .= "     ,coalesce(arrecadacao.fn_num_unicas(alc.cod_lancamento),0)::int as num_unicas   \n";
    $stSql .= "     ,al.valor as valor_lancamento                                                   \n";
    $stSql .= "
                    , CASE WHEN ( calc.calculado = true ) THEN
                            'Calculado'
                      ELSE
                            'Lançamento Manual'
                      END AS tipo_calculo                                                           \n";
    if ($stTipo == "ie") {
        $stSql .= "     ,cef.competencia                                                                \n";
    }

    if ($boNumeracao) {

        $stSql .= "   , nac.numeracao                                                               \n";
        $stSql .= "   , nap.cod_parcela                                                             \n";
        $stSql .= "   , to_char(nap.vencimento,'dd/mm/yyyy') as vencimento                          \n";
        $stSql .= "   , to_char(now(),'dd/mm/yyyy') as database_br                                  \n";
        $stSql .= "   , apag.data_pagamento as pagamento                                            \n";
        $stSql .= "   , apag.ocorrencia_pagamento                                                   \n";
        $stSql .= "   , (case when naccon.numeracao is not null then                                \n";
        $stSql .= "        'Parcela ' || nap.nr_parcela ||'/'||                                     \n";
        $stSql .= "         arrecadacao.fn_total_parcelas(al.cod_lancamento)                        \n";
        $stSql .="         || ' ['||nac.numeracao || ']'                                            \n";
        $stSql .= "      else                                                                       \n";
        $stSql .= "           null                                                                  \n";
        $stSql .= "      end) as consolidacao                                                       \n";
    }

#=============================   COLETA DOS DADOS ======================= FIM

    $stSql .= " FROM                                                                                \n";

    if ($boNumeracao) {

        $stSql .= " arrecadacao.carne AS nac                                                        \n";

        $stSql .= " INNER JOIN arrecadacao.parcela AS nap                                           \n";
        $stSql .= " ON nap.cod_parcela = nac.cod_parcela                                            \n";

        $stSql .= " INNER JOIN arrecadacao.lancamento as al                                         \n";
        $stSql .= " ON al.cod_lancamento = nap.cod_lancamento                                       \n";

        $stSql .= " INNER JOIN arrecadacao.lancamento_calculo as alc                                \n";
        $stSql .= " ON alc.cod_lancamento = al.cod_lancamento                                       \n";

        $stSql .= " INNER JOIN arrecadacao.calculo as calc                                          \n";
        $stSql .= " ON calc.cod_calculo = alc.cod_calculo                                           \n";

        if ($stTipo == "ii") {
            $stSql .= "     INNER JOIN arrecadacao.imovel_calculo as ic                                     \n";
            $stSql .= "     ON ic.cod_calculo = calc.cod_calculo                                            \n";
        } elseif ($stTipo == "ie") {

            $stSql .= "     INNER JOIN arrecadacao.cadastro_economico_calculo as cec                        \n";
            $stSql .= "     ON cec.cod_calculo = calc.cod_calculo                                           \n";
            $stSql .= "     INNER JOIN arrecadacao.cadastro_economico_faturamento as cef                    \n";
            $stSql .= "     ON cef.inscricao_economica = cec.inscricao_economica AND                        \n";
            $stSql .= "        cef.timestamp = cec.timestamp                                                \n";
        }

        $stSql .= " INNER JOIN (                                                                    \n";
        $stSql .= "     select cgm.numcgm, cgm.nom_cgm, accgm.cod_calculo                           \n";
        $stSql .= "     from                                                                        \n";
        $stSql .= "         arrecadacao.calculo_cgm as accgm                                        \n";
        $stSql .= "         INNER JOIN sw_cgm as cgm ON cgm.numcgm = accgm.numcgm                   \n";
        $stSql .= " ) as cgm                                                                        \n";
        $stSql .= " ON cgm.cod_calculo = calc.cod_calculo                                           \n";

        $stSql .= " LEFT JOIN arrecadacao.pagamento as apag                                         \n";
        $stSql .= " ON apag.numeracao = nac.numeracao                                               \n";
        $stSql .= " AND apag.cod_convenio = nac.cod_convenio                                        \n";

        $stSql .= " LEFT JOIN arrecadacao.carne_consolidacao AS naccon                              \n";
        $stSql .= " ON naccon.numeracao = nac.numeracao                                             \n";
        $stSql .= " AND naccon.cod_convenio = nac.cod_convenio                                      \n";

    } else {

        $stSql .= " ( SELECT * FROM arrecadacao.calculo
                       WHERE cod_calculo
                         NOT IN( SELECT COD_CALCULO from divida.parcela_calculo
                                  INNER JOIN divida.parcela
                                  USING (num_parcelamento, num_parcela)
                                  WHERE parcela.cancelada = true )) as calc \n";

        $stSql .= " INNER JOIN (                                                                    \n";
        $stSql .= "     SELECT                                                                      \n";
        $stSql .= "         max(cod_calculo) as cod_calculo                                         \n";
        $stSql .= "         , cod_lancamento                                                        \n";
        $stSql .= "     FROM                                                                        \n";
        $stSql .= "         arrecadacao.lancamento_calculo                                          \n";
        $stSql .= "     GROUP BY cod_lancamento                                                     \n";
        $stSql .= " )  AS alc                                                                       \n";
        $stSql .= " ON alc.cod_calculo = calc.cod_calculo                                           \n";

        $stSql .= " INNER JOIN arrecadacao.lancamento AS al                                         \n";
        $stSql .= " ON al.cod_lancamento = alc.cod_lancamento                                       \n";

        if ($stTipo == "ii") {

            $stSql .= "     INNER JOIN arrecadacao.imovel_calculo as ic                                     \n";
            $stSql .= "     ON ic.cod_calculo = calc.cod_calculo                                            \n";

            $stSql .= "     INNER JOIN arrecadacao.calculo_cgm cgm ON cgm.cod_calculo = ic.cod_calculo      \n";

        } elseif ($stTipo == "ie") {

            $stSql .= "     INNER JOIN arrecadacao.cadastro_economico_calculo as cec                        \n";
            $stSql .= "     ON cec.cod_calculo = calc.cod_calculo                                           \n";
            $stSql .= "     INNER JOIN arrecadacao.cadastro_economico_faturamento as cef                    \n";
            $stSql .= "     ON cef.inscricao_economica = cec.inscricao_economica AND                        \n";
            $stSql .= "        cef.timestamp = cec.timestamp                                                \n";

            $stSql .= "     INNER JOIN arrecadacao.calculo_cgm cgm ON cgm.cod_calculo = cec.cod_calculo     \n";

        } else {

            $stSql .= " INNER JOIN arrecadacao.calculo_cgm  AS cgm                                          \n";
            $stSql .= " ON calc.cod_calculo = cgm.cod_calculo                                               \n";

        }

    }

    $stSql .= "     LEFT JOIN arrecadacao.calculo_grupo_credito as acgc                                 \n";
    $stSql .= "     ON acgc.cod_calculo = cgm.cod_calculo                                               \n";
    $stSql .= "     AND acgc.ano_exercicio = calc.exercicio                                             \n";

    if ($stTipo != "ie" && $stTipo != "ii") {

        $stSql .= "     LEFT JOIN arrecadacao.imovel_calculo ic                                         \n";
        $stSql .= "     ON ic.cod_calculo = calc.cod_calculo                                            \n";

        $stSql .= "     LEFT JOIN arrecadacao.cadastro_economico_calculo cec                            \n";
        $stSql .= "     ON cec.cod_calculo  = calc.cod_calculo                                          \n";

    }

    $stSql .= "    ". $stFiltro ."                                                                      \n";

    $stSql .= " ) as lancamentos                                                                        \n";
    $stSql .= " ORDER BY                                                                                \n";
    $stSql .= "     lancamentos.exercicio                                                               \n";
    $stSql .= "     , origem                                                                            \n";
    $stSql .= "     , lancamentos.inscricao                                                             \n";
    $stSql .= "     , lancamentos.cod_lancamento                                                        \n";

    return $stSql;
}

function CarneDiverso(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaCarneDiverso().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
/*****************************************
// para impressao no pdf / IPTU
*****************************************/
function montaCarneDiverso()
{
$stSql = "SELECT inscricao_municipal -- numero da inscricao [municipal, economica]
                , natureza
                , quadra
                , lote
                , (SELECT imobiliario.fn_area_real(inscricao_municipal)) as area_real
                , (SELECT imobiliario.fn_calcula_area_imovel(inscricao_municipal)) as area_edificada
                , zoneamento
                , endereco_entrega
                , bairro_entrega
                , cep_entrega
                , municipio_entrega
                , numero_entrega
                , nom_logradouro
                , bairro
                , cep
                , municipio
                , numero
                , cod_calculo
                , exercicio
                , data_processamento
                , descricao
                , ano_exercicio
                , cod_grupo
                , descricao_credito
                , cod_credito
                , valor ";
            if ($this->getDado('numcgm')) {
                $stSql .= ", array_to_string(array(select nom_cgm from sw_cgm where numcgm in (".$this->getDado('numcgm').")), '/') AS nom_cgm ";
            } else {
                $stSql .= ", nom_cgm";
            }

            $stSql .= "
                , observacao

            --
            -- Especifico para inscrição economica
            --
            , CASE WHEN natureza = 'Econômica' THEN
                (SELECT nom_atividade
                   FROM economico.atividade
                 INNER JOIN economico.atividade_cadastro_economico
                     ON atividade_cadastro_economico.cod_atividade = atividade.cod_atividade
                  WHERE atividade_cadastro_economico.inscricao_economica = inscricao_municipal
                    AND atividade_cadastro_economico.principal = true
               ORDER BY ocorrencia_atividade DESC LIMIT 1)
            END AS nom_atividade

            --
            -- Especifico para inscrição economica
            --
            , CASE WHEN natureza = 'Econômica' THEN
                (SELECT nom_fantasia
                   FROM sw_cgm_pessoa_juridica
                 INNER JOIN economico.cadastro_economico_empresa_direito
                    ON cadastro_economico_empresa_direito.numcgm = sw_cgm_pessoa_juridica.numcgm
                  WHERE cadastro_economico_empresa_direito.inscricao_economica = inscricao_municipal)
            END AS nom_fantasia

             FROM (
            select coalesce( cec.inscricao_economica, aic.inscricao_municipal) as inscricao_municipal,
                      CASE WHEN aic.inscricao_municipal IS NOT NULL THEN
                        'Imobiliária'
                      ELSE
                        'Econômica'
                      END AS natureza,

                      (
                        SELECT
                            COALESCE ( lote_urbano_valor.valor, lote_rural_valor.valor )
                        FROM
                            imobiliario.imovel_lote

                        LEFT JOIN
                            (
                                SELECT
                                    tmp.*
                                FROM
                                    imobiliario.atributo_lote_urbano_valor AS tmp
                                INNER JOIN
                                    (
                                        SELECT
                                            cod_atributo,
                                            cod_cadastro,
                                            cod_modulo,
                                            cod_lote,
                                            max(timestamp) AS timestamp
                                        FROM
                                            imobiliario.atributo_lote_urbano_valor
                                        GROUP BY
                                            cod_atributo,
                                            cod_cadastro,
                                            cod_modulo,
                                            cod_lote
                                    )AS tmp2
                                ON
                                    tmp.cod_atributo = tmp2.cod_atributo
                                    AND tmp.cod_cadastro = tmp2.cod_cadastro
                                    AND tmp.cod_modulo = tmp2.cod_modulo
                                    AND tmp.cod_lote = tmp2.cod_lote
                                    AND tmp.timestamp = tmp2.timestamp

                                WHERE
                                    tmp.cod_cadastro = 2
                                    AND tmp.cod_atributo = 5
                                    AND tmp.cod_modulo = 12
                            )AS lote_urbano_valor
                        ON
                            lote_urbano_valor.cod_lote = imovel_lote.cod_lote

                        LEFT JOIN
                            (
                                SELECT
                                    tmp.*
                                FROM
                                    imobiliario.atributo_lote_rural_valor AS tmp
                                INNER JOIN
                                    (
                                        SELECT
                                            cod_atributo,
                                            cod_cadastro,
                                            cod_modulo,
                                            cod_lote,
                                            max(timestamp) AS timestamp
                                        FROM
                                            imobiliario.atributo_lote_rural_valor
                                        GROUP BY
                                            cod_atributo,
                                            cod_cadastro,
                                            cod_modulo,
                                            cod_lote
                                    )AS tmp2
                                ON
                                    tmp.cod_atributo = tmp2.cod_atributo
                                    AND tmp.cod_cadastro = tmp2.cod_cadastro
                                    AND tmp.cod_modulo = tmp2.cod_modulo
                                    AND tmp.cod_lote = tmp2.cod_lote
                                    AND tmp.timestamp = tmp2.timestamp

                                WHERE
                                    tmp.cod_cadastro = 2
                                    AND tmp.cod_atributo = 5
                                    AND tmp.cod_modulo = 12
                            )AS lote_rural_valor
                        ON
                            lote_rural_valor.cod_lote = imovel_lote.cod_lote

                        WHERE
                            imovel_lote.inscricao_municipal = COALESCE( aic.inscricao_municipal, edf.inscricao_municipal )

                        LIMIT 1
                      )AS quadra,
                      (
                        SELECT
                            COALESCE ( lote_urbano_valor.valor, lote_rural_valor.valor )
                        FROM
                            imobiliario.imovel_lote

                        LEFT JOIN
                            (
                                SELECT
                                    tmp.*
                                FROM
                                    imobiliario.atributo_lote_urbano_valor AS tmp
                                INNER JOIN
                                    (
                                        SELECT
                                            cod_atributo,
                                            cod_cadastro,
                                            cod_modulo,
                                            cod_lote,
                                            max(timestamp) AS timestamp
                                        FROM
                                            imobiliario.atributo_lote_urbano_valor
                                        GROUP BY
                                            cod_atributo,
                                            cod_cadastro,
                                            cod_modulo,
                                            cod_lote
                                    )AS tmp2
                                ON
                                    tmp.cod_atributo = tmp2.cod_atributo
                                    AND tmp.cod_cadastro = tmp2.cod_cadastro
                                    AND tmp.cod_modulo = tmp2.cod_modulo
                                    AND tmp.cod_lote = tmp2.cod_lote
                                    AND tmp.timestamp = tmp2.timestamp

                                WHERE
                                    tmp.cod_cadastro = 2
                                    AND tmp.cod_atributo = 7
                                    AND tmp.cod_modulo = 12
                            )AS lote_urbano_valor
                        ON
                            lote_urbano_valor.cod_lote = imovel_lote.cod_lote

                        LEFT JOIN
                            (
                                SELECT
                                    tmp.*
                                FROM
                                    imobiliario.atributo_lote_rural_valor AS tmp
                                INNER JOIN
                                    (
                                        SELECT
                                            cod_atributo,
                                            cod_cadastro,
                                            cod_modulo,
                                            cod_lote,
                                            max(timestamp) AS timestamp
                                        FROM
                                            imobiliario.atributo_lote_rural_valor
                                        GROUP BY
                                            cod_atributo,
                                            cod_cadastro,
                                            cod_modulo,
                                            cod_lote
                                    )AS tmp2
                                ON
                                    tmp.cod_atributo = tmp2.cod_atributo
                                    AND tmp.cod_cadastro = tmp2.cod_cadastro
                                    AND tmp.cod_modulo = tmp2.cod_modulo
                                    AND tmp.cod_lote = tmp2.cod_lote
                                    AND tmp.timestamp = tmp2.timestamp

                                WHERE
                                    tmp.cod_cadastro = 2
                                    AND tmp.cod_atributo = 7
                                    AND tmp.cod_modulo = 12
                            )AS lote_rural_valor
                        ON
                            lote_rural_valor.cod_lote = imovel_lote.cod_lote

                        WHERE
                            imovel_lote.inscricao_municipal = COALESCE( aic.inscricao_municipal, edf.inscricao_municipal )

                        LIMIT 1
                      )AS lote,

                      CASE WHEN aic.inscricao_municipal IS NOT NULL THEN
                        (
                            SELECT
                                iloc.codigo_composto ||' - '|| iloc.nom_localizacao

                            FROM
                                imobiliario.imovel_lote AS iil

                            INNER JOIN
                                imobiliario.lote AS il
                            ON
                                il.cod_lote = iil.cod_lote

                            INNER JOIN
                                imobiliario.lote_localizacao AS illo
                            ON
                                illo.cod_lote = il.cod_lote

                            INNER JOIN
                                imobiliario.localizacao AS iloc
                            ON
                                iloc.cod_localizacao = illo.cod_localizacao

                            WHERE
                                iil.inscricao_municipal = aic.inscricao_municipal

                            ORDER BY
                                il.timestamp DESC

                            LIMIT 1
                        )
                    ELSE
                        CASE WHEN edf.inscricao_municipal IS NOT NULL THEN
                            (
                                SELECT
                                    iloc.codigo_composto ||' - '|| iloc.nom_localizacao

                                FROM
                                    imobiliario.imovel_lote AS iil

                                INNER JOIN
                                    imobiliario.lote AS il
                                ON
                                    il.cod_lote = iil.cod_lote

                                INNER JOIN
                                    imobiliario.lote_localizacao AS illo
                                ON
                                    illo.cod_lote = il.cod_lote

                                INNER JOIN
                                    imobiliario.localizacao AS iloc
                                ON
                                    iloc.cod_localizacao = illo.cod_localizacao

                                WHERE
                                    iil.inscricao_municipal = edf.inscricao_municipal

                                ORDER BY
                                    il.timestamp DESC

                                LIMIT 1
                            )
                        END
                      END AS zoneamento,

                    arrecadacao.fn_consulta_endereco_corresp_todos (
                        coalesce( aic.inscricao_municipal, cec.inscricao_economica, cgm.numcgm ),
                        CASE
                            WHEN aic.inscricao_municipal IS NOT NULL THEN
                                1
                            WHEN cec.inscricao_economica IS NOT NULL THEN
                                2
                        ELSE
                                3
                        END,
                        1
                    )AS endereco_entrega,

                    arrecadacao.fn_consulta_endereco_corresp_todos (
                        coalesce( aic.inscricao_municipal, cec.inscricao_economica, cgm.numcgm ),
                        CASE
                            WHEN aic.inscricao_municipal IS NOT NULL THEN
                                1
                            WHEN cec.inscricao_economica IS NOT NULL THEN
                                2
                        ELSE
                                3
                        END,
                        2
                    )AS bairro_entrega,

                    arrecadacao.fn_consulta_endereco_corresp_todos (
                        coalesce( aic.inscricao_municipal, cec.inscricao_economica, cgm.numcgm ),
                        CASE
                            WHEN aic.inscricao_municipal IS NOT NULL THEN
                                1
                            WHEN cec.inscricao_economica IS NOT NULL THEN
                                2
                        ELSE
                                3
                        END,
                        3
                    )AS cep_entrega,

                    arrecadacao.fn_consulta_endereco_corresp_todos (
                        coalesce( aic.inscricao_municipal, cec.inscricao_economica, cgm.numcgm ),
                        CASE
                            WHEN aic.inscricao_municipal IS NOT NULL THEN
                                1
                            WHEN cec.inscricao_economica IS NOT NULL THEN
                                2
                        ELSE
                                3
                        END,
                        4
                    )AS municipio_entrega,

                    arrecadacao.fn_consulta_endereco_corresp_todos (
                        coalesce( aic.inscricao_municipal, cec.inscricao_economica, cgm.numcgm ),
                        CASE
                            WHEN aic.inscricao_municipal IS NOT NULL THEN
                                1
                            WHEN cec.inscricao_economica IS NOT NULL THEN
                                2
                        ELSE
                                3
                        END,
                        6
                    )AS numero_entrega,

                    arrecadacao.fn_consulta_endereco_todos (
                        coalesce( aic.inscricao_municipal, cec.inscricao_economica, cgm.numcgm ),
                        CASE
                            WHEN aic.inscricao_municipal IS NOT NULL THEN
                                1
                            WHEN cec.inscricao_economica IS NOT NULL THEN
                                2
                        ELSE
                                3
                        END,
                        1
                    )AS nom_logradouro,

                    arrecadacao.fn_consulta_endereco_todos (
                        coalesce( aic.inscricao_municipal, cec.inscricao_economica, cgm.numcgm ),
                        CASE
                            WHEN aic.inscricao_municipal IS NOT NULL THEN
                                1
                            WHEN cec.inscricao_economica IS NOT NULL THEN
                                2
                        ELSE
                                3
                        END,
                        2
                    )AS bairro,

                    arrecadacao.fn_consulta_endereco_todos (
                        coalesce( aic.inscricao_municipal, cec.inscricao_economica, cgm.numcgm ),
                        CASE
                            WHEN aic.inscricao_municipal IS NOT NULL THEN
                                1
                            WHEN cec.inscricao_economica IS NOT NULL THEN
                                2
                        ELSE
                                3
                        END,
                        3
                    )AS cep,

                    arrecadacao.fn_consulta_endereco_todos (
                        coalesce( aic.inscricao_municipal, cec.inscricao_economica, cgm.numcgm ),
                        CASE
                            WHEN aic.inscricao_municipal IS NOT NULL THEN
                                1
                            WHEN cec.inscricao_economica IS NOT NULL THEN
                                2
                        ELSE
                                3
                        END,
                        4
                    )AS municipio,

                    arrecadacao.fn_consulta_endereco_todos (
                        coalesce( aic.inscricao_municipal, cec.inscricao_economica, cgm.numcgm ),
                        CASE
                            WHEN aic.inscricao_municipal IS NOT NULL THEN
                                1
                            WHEN cec.inscricao_economica IS NOT NULL THEN
                                2
                        ELSE
                                3
                        END,
                        6
                    )AS numero,
                    \n";

$stSql .= "           ac.cod_calculo,                                                                                                                                          \n";
$stSql .= "           ac.exercicio,                                                                                                                                               \n";
$stSql .= "           to_char(now(),'dd/mm/yyyy') as data_processamento,                                                                               \n";
$stSql .= "           case when acgc.descricao is not null then                                                                                                \n";
$stSql .= "               acgc.descricao                                                                                                                                      \n";
$stSql .= "           else mc.descricao_credito                                                                                                                         \n";
$stSql .= "           end as descricao,                                                                                                                                      \n";
$stSql .= "           acgc.ano_exercicio, \n";
$stSql .= "           case when acgc.cod_grupo is not null then                                                                                               \n";
$stSql .= "               acgc.cod_grupo::varchar                                                                                                                       \n";
$stSql .= "           else mc.cod_credito||'.'||mc.cod_especie||'.'||mc.cod_genero||'.'||mc.cod_natureza                                    \n";
$stSql .= "           end as cod_grupo,                                                                                                                                     \n";
$stSql .= "           mc.descricao_credito,                                                                                                                                \n";
$stSql .= "           mc.cod_credito,                                                                                                                                          \n";
$stSql .= "           ac.valor,                                                                                                                                                     \n";
$stSql .= "           cgm.nom_cgm,                                                                                                                                      \n";
$stSql .= "           al.observacao                                                                                                                                            \n";
$stSql .= "     from                                                                                                                                                                 \n";
$stSql .= "         arrecadacao.calculo             as ac                                           \n";
$stSql .= "         INNER JOIN monetario.credito as mc                                              \n";
$stSql .= "         ON mc.cod_credito = ac.cod_credito AND mc.cod_natureza = ac.cod_natureza        \n";
$stSql .= "         AND mc.cod_especie = ac.cod_especie  AND mc.cod_genero = ac.cod_genero          \n";
$stSql .= "         INNER JOIN arrecadacao.calculo_cgm as acgm          ON acgm.cod_calculo = ac.cod_calculo                      \n";
$stSql .= "         INNER JOIN sw_cgm as cgm                                        ON cgm.numcgm = acgm.numcgm                             \n";
$stSql .= "         INNER JOIN arrecadacao.lancamento_calculo as alc  ON alc.cod_calculo = ac.cod_calculo                          \n";
$stSql .= "         INNER JOIN arrecadacao.lancamento as al                ON al.cod_lancamento = alc.cod_lancamento            \n";
$stSql .= "         left join arrecadacao.imovel_calculo as aic          ON aic.cod_calculo = ac.cod_calculo                               \n";
$stSql .= "         left join arrecadacao.cadastro_economico_calculo as cec          ON cec.cod_calculo = ac.cod_calculo       \n";

$stSql .= "         LEFT JOIN ( \n";
$stSql .= "             SELECT \n";
$stSql .= "                 edf_tmp.inscricao_economica, \n";
$stSql .= "                 edf_tmp.inscricao_municipal, \n";
$stSql .= "                 edf_tmp.timestamp \n";
$stSql .= "             FROM \n";
$stSql .= "                 economico.domicilio_fiscal AS edf_tmp, \n";
$stSql .= "                 ( \n";
$stSql .= "                     SELECT \n";
$stSql .= "                         MAX (timestamp) AS timestamp, \n";
$stSql .= "                         inscricao_economica \n";
$stSql .= "                     FROM \n";
$stSql .= "                         economico.domicilio_fiscal \n";
$stSql .= "                     GROUP BY \n";
$stSql .= "                         inscricao_economica \n";
$stSql .= "                 )AS tmp \n";
$stSql .= "             WHERE \n";
$stSql .= "                 tmp.timestamp = edf_tmp.timestamp \n";
$stSql .= "                 AND tmp.inscricao_economica = edf_tmp.inscricao_economica \n";
$stSql .= "         )AS edf \n";
$stSql .= "         ON \n";
$stSql .= "             cec.inscricao_economica = edf.inscricao_economica \n";
$stSql .= "         LEFT JOIN ( \n";
$stSql .= "             SELECT \n";
$stSql .= "                 edi_tmp.timestamp, \n";
$stSql .= "                 edi_tmp.inscricao_economica \n";
$stSql .= "             FROM \n";
$stSql .= "                 economico.domicilio_informado AS edi_tmp, \n";
$stSql .= "                 ( \n";
$stSql .= "                     SELECT \n";
$stSql .= "                         MAX(timestamp) AS timestamp, \n";
$stSql .= "                         inscricao_economica \n";
$stSql .= "                     FROM \n";
$stSql .= "                         economico.domicilio_informado \n";
$stSql .= "                     GROUP BY \n";
$stSql .= "                         inscricao_economica \n";
$stSql .= "                 )AS tmp \n";
$stSql .= "             WHERE \n";
$stSql .= "                 tmp.timestamp = edi_tmp.timestamp \n";
$stSql .= "                 AND tmp.inscricao_economica = edi_tmp.inscricao_economica \n";
$stSql .= "         )AS edi \n";
$stSql .= "         ON \n";
$stSql .= "             cec.inscricao_economica = edi.inscricao_economica \n";

$stSql .= "         LEFT JOIN                                                                                                                                                      \n";
$stSql .= "          ( select agc.descricao, agc.cod_grupo, acgc.cod_calculo, agc.ano_exercicio                                            \n";
$stSql .= "             from arrecadacao.grupo_credito as agc                                                                                                  \n";
$stSql .= "             INNER JOIN  arrecadacao.calculo_grupo_credito as acgc                                                                         \n";
$stSql .= "             ON acgc.cod_grupo = agc.cod_grupo AND acgc.ano_exercicio = agc.ano_exercicio                            \n";
$stSql .= "          ) as acgc ON acgc.cod_calculo = alc.cod_calculo                     \n";
$stSql .= "    where                                                                                                                                                                \n";
$stSql .= "          cgm.numcgm  is not null                                                                                                                             \n";

   return $stSql;

}

function listaCarneDevolucao(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaListaCarneDevolucao( $stFiltro );
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaListaCarneDevolucao($stFiltro)
{
    $stSql  = "SELECT                                                                               \n";
    $stSql .= "    ac.numeracao,                                                                    \n";
    $stSql .= "    ac.cod_convenio                                                                  \n";
    $stSql .= "FROM                                                                                 \n";
    $stSql .= "    arrecadacao.carne AS ac                                                          \n";
    $stSql .= "INNER JOIN (                                                                         \n";
    $stSql .= "    SELECT                                                                           \n";
    $stSql .= "        ap.cod_parcela                                                               \n";
    $stSql .= "    FROM                                                                             \n";
    $stSql .= "        arrecadacao.parcela AS ap,                                                   \n";
    $stSql .= "        (                                                                            \n";
    $stSql .= "        SELECT DISTINCT                                                              \n";
    $stSql .= "            ap.cod_lancamento                                                        \n";
    $stSql .= "        FROM                                                                         \n";
    $stSql .= "            arrecadacao.carne AS ac                                                 \n";
    $stSql .= "            INNER JOIN arrecadacao.parcela AS ap  ON ap.cod_parcela = ac.cod_parcela      \n";
    $stSql .= "        WHERE                                                                        \n";
    $stSql .= "            ac.numeracao = '".$stFiltro."'                                          \n";
    $stSql .= "        ) AS cc                                                                      \n";
    $stSql .= "    WHERE                                                                            \n";
    $stSql .= "        cc.cod_lancamento = ap.cod_lancamento                                        \n";
    $stSql .= ") AS ll                                                                              \n";
    $stSql .= "ON  ll.cod_parcela = ac.cod_parcela                                                  \n";
    $stSql .= "INNER JOIN arrecadacao.carne_devolucao AS acd ON acd.numeracao = ac.numeracao           \n";
    $stSql .= "     WHERE acd.cod_motivo=101                                                        \n";

    return $stSql;
}

function listaPagamentosCancelados(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montalistaPagamentosCancelados( $stFiltro );
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montalistaPagamentosCancelados($stFiltro)
{
    $stSql  = "SELECT                                                                               \n";
    $stSql .= "    ac.numeracao,                                                                    \n";
    $stSql .= "    ac.cod_convenio,                                                                 \n";
    $stSql .= "    ap.ocorrencia_pagamento,                                                         \n";
    $stSql .= "    aproc.cod_processo                                                               \n";
    $stSql .= "FROM                                                                                 \n";
    $stSql .= "    arrecadacao.carne AS ac                                                          \n";
    $stSql .= "INNER JOIN (                                                                         \n";
    $stSql .= "    SELECT                                                                           \n";
    $stSql .= "        ap.cod_parcela                                                               \n";
    $stSql .= "    FROM                                                                             \n";
    $stSql .= "        arrecadacao.parcela AS ap,                                                   \n";
    $stSql .= "        (                                                                            \n";
    $stSql .= "        SELECT DISTINCT                                                              \n";
    $stSql .= "            ap.cod_lancamento                                                        \n";
    $stSql .= "        FROM                                                                         \n";
    $stSql .= "            arrecadacao.carne AS ac,                                                 \n";
    $stSql .= "            arrecadacao.parcela AS ap                                                \n";
    $stSql .= "        WHERE                                                                        \n";
    $stSql .= "            ap.nr_parcela = 0 AND                                                    \n";
    $stSql .= "            ac.numeracao = '".$stFiltro."' AND                                         \n";
    $stSql .= "            ap.cod_parcela = ac.cod_parcela                                          \n";
    $stSql .= "        ) AS cc                                                                      \n";
    $stSql .= "    WHERE                                                                            \n";
    $stSql .= "        cc.cod_lancamento = ap.cod_lancamento                                        \n";
    $stSql .= ") AS ll                                                                              \n";
    $stSql .= " ON                                                                                  \n";
    $stSql .= "    ll.cod_parcela = ac.cod_parcela                                                  \n";
    $stSql .= " INNER JOIN arrecadacao.pagamento AS ap ON ap.numeracao = ac.numeracao AND ap.cod_convenio = ac.cod_convenio     \n";
    $stSql .= " INNER JOIN arrecadacao.tipo_pagamento as atp ON atp.cod_tipo = ap.cod_tipo AND atp.pagamento =  false           \n";
    $stSql .="  LEFT JOIN arrecadacao.processo_pagamento as aproc ON aproc.numeracao = ap.numeracao                             \n";
    $stSql .="                                                    and aproc.cod_convenio = ap.cod_convenio                      \n";
    $stSql .="                                                    and aproc.ocorrencia_pagamento = ap.ocorrencia_pagamento      \n";

    return $stSql;
}

function recuperaDadosValorVenalIPTUGenerico(&$rsRecordSet, $inInscricao, $inExercicio, $inCodParcela, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosValorVenalIPTUGenerico( $inInscricao, $inExercicio, $inCodParcela );
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaDadosValorVenalIPTUGenerico($inInscricao, $inExercicio, $inCodParcela)
{
    $stSql  = " SELECT
                    recuperacadastroimobiliarioloteurbanolote ( IMOBILIARIO.FN_BUSCA_LOTE_IMOVEL( tmp.inscricao_municipal ) )  AS lote_urb,
                    recuperacadastroimobiliarioloteurbanoquadra ( IMOBILIARIO.FN_BUSCA_LOTE_IMOVEL( tmp.inscricao_municipal ) )  AS lote_quadra,

                    arrecadacao.fn_area_real( tmp.inscricao_municipal ) As area_lote,
                    imobiliario.fn_calcula_area_imovel( tmp.inscricao_municipal ) AS area_edificacao,
                    CASE WHEN arrecadacao.verificaEdificacaoImovel( tmp.inscricao_municipal ) = 't' THEN
                        COALESCE( IMOBILIARIO.BUSCAQUANTIDADEUNIDADESDEPENDENTES( tmp.inscricao_municipal ), 0 ) + 1
                    ELSE
                        0
                    END AS qtd_edificacao,
                    CASE WHEN arrecadacao.verificaEdificacaoImovel( tmp.inscricao_municipal ) = 't' THEN
                        '0,5%'
                    ELSE
                        '1%'
                    END AS aliquota,

                    CASE WHEN tmp.venal_territorial_calculado IS NOT NULL THEN
                        tmp.venal_territorial_calculado
                    ELSE
                        (
                        SELECT
                            COALESCE( venal_territorial_informado, venal_territorial_calculado)
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = tmp.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = tmp.exercicio
                            AND (venal_territorial_informado IS NOT NULL OR venal_territorial_calculado IS NOT NULL )
                        order by timestamp desc limit 1
                        )
                    END AS venal_territorial_calculado,

                    CASE WHEN tmp.venal_predial_calculado IS NOT NULL THEN
                        tmp.venal_predial_calculado
                    ELSE
                        (
                        SELECT
                            COALESCE( venal_predial_informado, venal_predial_calculado)
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = tmp.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = tmp.exercicio
                            AND (venal_predial_informado IS NOT NULL OR venal_predial_calculado IS NOT NULL )
                        order by timestamp desc limit 1
                        )
                    END AS venal_predial_calculado,

                    CASE WHEN tmp.venal_total_calculado IS NOT NULL THEN
                        tmp.venal_total_calculado
                    ELSE
                        (
                        SELECT
                            COALESCE( venal_total_informado, venal_total_calculado)
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = tmp.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = tmp.exercicio
                            AND (venal_total_informado IS NOT NULL OR venal_total_calculado IS NOT NULL )
                        order by timestamp desc limit 1
                        )
                    END AS venal_total_calculado,
                    (
                        SELECT
                            lote_localizacao.valor
                        FROM
                            imobiliario.lote_localizacao
                        WHERE
                            lote_localizacao.cod_lote = IMOBILIARIO.FN_BUSCA_LOTE_IMOVEL( tmp.inscricao_municipal )
                    )AS lote_valor

                FROM
                    (
                    SELECT
                        aivv.exercicio,
                        (
                            SELECT
                                venal_territorial_calculado
                            FROM
                                arrecadacao.imovel_v_venal
                            WHERE
                                arrecadacao.imovel_v_venal.inscricao_municipal = aivv.inscricao_municipal
                                AND arrecadacao.imovel_v_venal.exercicio = aivv.exercicio
                                AND arrecadacao.imovel_v_venal.timestamp = aic.timestamp
                            order by arrecadacao.imovel_v_venal.timestamp limit 1
                        )AS venal_territorial_calculado,
                        (
                            SELECT
                                venal_predial_calculado
                            FROM
                                arrecadacao.imovel_v_venal
                            WHERE
                                arrecadacao.imovel_v_venal.inscricao_municipal = aivv.inscricao_municipal
                                AND arrecadacao.imovel_v_venal.exercicio = aivv.exercicio
                                AND arrecadacao.imovel_v_venal.timestamp = aic.timestamp
                        )AS venal_predial_calculado,
                        (
                            SELECT
                                venal_total_calculado
                            FROM
                                arrecadacao.imovel_v_venal
                            WHERE
                                arrecadacao.imovel_v_venal.inscricao_municipal = aivv.inscricao_municipal
                                AND arrecadacao.imovel_v_venal.exercicio = aivv.exercicio
                                AND arrecadacao.imovel_v_venal.timestamp = aic.timestamp
                        )AS venal_total_calculado,
                        aivv.inscricao_municipal

                    FROM
                        arrecadacao.imovel_v_venal AS aivv

                    INNER JOIN
                        arrecadacao.imovel_calculo AS aic
                    ON
                        aic.timestamp = aivv.timestamp
                        AND aic.inscricao_municipal = aivv.inscricao_municipal
                        AND aic.cod_calculo = (SELECT cod_calculo FROM arrecadacao.lancamento_calculo WHERE arrecadacao.lancamento_calculo.cod_lancamento = (SELECT cod_lancamento from arrecadacao.parcela where cod_parcela = ".$inCodParcela.") limit 1)

                    WHERE aivv.inscricao_municipal = ".$inInscricao." AND aivv.exercicio = '".$inExercicio."'
                )AS tmp ";

    return $stSql;
}

function recuperaDadosValorVenalITBIGenerico(&$rsRecordSet, $inInscricao, $inExercicio, $inCodParcela, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosValorVenalITBIGenerico( $inInscricao, $inExercicio, $inCodParcela );
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaDadosValorVenalITBIGenerico($inInscricao, $inExercicio, $inCodParcela)
{
    $stSql  = " SELECT
                    recuperacadastroimobiliarioloteurbanolote ( IMOBILIARIO.FN_BUSCA_LOTE_IMOVEL( tmp.inscricao_municipal ) )  AS lote_urb,
                    recuperacadastroimobiliarioloteurbanoquadra ( IMOBILIARIO.FN_BUSCA_LOTE_IMOVEL( tmp.inscricao_municipal ) )  AS lote_quadra,
                    recuperacadastroimobiliarioloteruralarea ( IMOBILIARIO.FN_BUSCA_LOTE_IMOVEL( tmp.inscricao_municipal ) ) AS area_lote_rural,

                    arrecadacao.fn_area_real( tmp.inscricao_municipal ) As area_lote,
                    imobiliario.fn_calcula_area_imovel( tmp.inscricao_municipal ) AS area_edificacao,
                    CASE WHEN arrecadacao.verificaEdificacaoImovel( tmp.inscricao_municipal ) = 't' THEN
                        COALESCE( IMOBILIARIO.BUSCAQUANTIDADEUNIDADESDEPENDENTES( tmp.inscricao_municipal ), 0 ) + 1
                    ELSE
                        0
                    END AS qtd_edificacao,
                    tmp.aliquota_valor_avaliado AS aliquota,

                    CASE WHEN tmp.venal_territorial_avaliado IS NOT NULL THEN
                        tmp.venal_territorial_avaliado
                    ELSE
                        (
                        SELECT
                            venal_territorial_avaliado
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = tmp.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = tmp.exercicio
                            AND (venal_territorial_informado IS NOT NULL OR venal_territorial_calculado IS NOT NULL )
                        order by timestamp desc limit 1
                        )
                    END AS venal_territorial_calculado,

                    CASE WHEN tmp.venal_predial_avaliado IS NOT NULL THEN
                        tmp.venal_predial_avaliado
                    ELSE
                        (
                        SELECT
                            venal_predial_avaliado
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = tmp.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = tmp.exercicio
                            AND (venal_predial_informado IS NOT NULL OR venal_predial_calculado IS NOT NULL )
                        order by timestamp desc limit 1
                        )
                    END AS venal_predial_calculado,

                    CASE WHEN tmp.venal_total_avaliado IS NOT NULL THEN
                        tmp.venal_total_avaliado
                    ELSE
                        (
                        SELECT
                            venal_total_avaliado
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = tmp.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = tmp.exercicio
                            AND (venal_total_informado IS NOT NULL OR venal_total_calculado IS NOT NULL )
                        order by timestamp desc limit 1
                        )
                    END AS venal_total_calculado,
                    (
                        SELECT
                            lote_localizacao.valor
                        FROM
                            imobiliario.lote_localizacao
                        WHERE
                            lote_localizacao.cod_lote = IMOBILIARIO.FN_BUSCA_LOTE_IMOVEL( tmp.inscricao_municipal )
                    )AS lote_valor

                FROM
                    (
                    SELECT
                        aivv.exercicio,
                        (
                            SELECT
                                venal_territorial_avaliado
                            FROM
                                arrecadacao.imovel_v_venal
                            WHERE
                                arrecadacao.imovel_v_venal.inscricao_municipal = aivv.inscricao_municipal
                                AND arrecadacao.imovel_v_venal.exercicio = aivv.exercicio
                                AND arrecadacao.imovel_v_venal.timestamp = aic.timestamp
                            order by arrecadacao.imovel_v_venal.timestamp limit 1
                        )AS venal_territorial_avaliado,
                        (
                            SELECT
                                venal_predial_avaliado
                            FROM
                                arrecadacao.imovel_v_venal
                            WHERE
                                arrecadacao.imovel_v_venal.inscricao_municipal = aivv.inscricao_municipal
                                AND arrecadacao.imovel_v_venal.exercicio = aivv.exercicio
                                AND arrecadacao.imovel_v_venal.timestamp = aic.timestamp
                        )AS venal_predial_avaliado,
                        (
                            SELECT
                                venal_total_avaliado
                            FROM
                                arrecadacao.imovel_v_venal
                            WHERE
                                arrecadacao.imovel_v_venal.inscricao_municipal = aivv.inscricao_municipal
                                AND arrecadacao.imovel_v_venal.exercicio = aivv.exercicio
                                AND arrecadacao.imovel_v_venal.timestamp = aic.timestamp
                        )AS venal_total_avaliado,
                        aivv.inscricao_municipal,
                        aivv.aliquota_valor_avaliado

                    FROM
                        arrecadacao.imovel_v_venal AS aivv

                    INNER JOIN
                        arrecadacao.imovel_calculo AS aic
                    ON
                        aic.timestamp = aivv.timestamp
                        AND aic.inscricao_municipal = aivv.inscricao_municipal
                        AND aic.cod_calculo = (SELECT cod_calculo FROM arrecadacao.lancamento_calculo WHERE arrecadacao.lancamento_calculo.cod_lancamento = (SELECT cod_lancamento from arrecadacao.parcela where cod_parcela = ".$inCodParcela.") limit 1)

                    WHERE aivv.inscricao_municipal = ".$inInscricao." AND aivv.exercicio = '".$inExercicio."'
                )AS tmp ";

    return $stSql;
}

function recuperaDadosPrefeituraIPTUGenerico(&$rsRecordSet, $inExercicio, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosPrefeituraIPTUGenerico($inExercicio);
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    unset( $obConexao );

    return $obErro;
}

function montaRecuperaDadosPrefeituraIPTUGenerico($inExercicio)
{
    $stSql  = " SELECT
                    (
                        SELECT
                            valor
                        FROM
                            administracao.configuracao
                        WHERE
                            cod_modulo = 2
                            AND exercicio = '".$inExercicio."'
                            AND parametro = 'bairro'
                    ) AS bairro,
                    (
                        SELECT
                            valor
                        FROM
                            administracao.configuracao
                        WHERE
                            cod_modulo = 2
                            AND exercicio = '".$inExercicio."'
                            AND parametro = 'cep'
                    ) AS cep,
                    (
                        SELECT
                            valor
                        FROM
                            administracao.configuracao
                        WHERE
                            cod_modulo = 2
                            AND exercicio = '".$inExercicio."'
                            AND parametro = 'fone'
                    ) AS fone,
                    (
                        SELECT
                            valor
                        FROM
                            administracao.configuracao
                        WHERE
                            cod_modulo = 2
                            AND exercicio = '".$inExercicio."'
                            AND parametro = 'cnpj'
                    ) AS cnpj,
                    (
                        SELECT
                            valor
                        FROM
                            administracao.configuracao
                        WHERE
                            cod_modulo = 2
                            AND exercicio = '".$inExercicio."'
                            AND parametro = 'ddd'
                    ) AS ddd,
                    (
                        SELECT
                            valor
                        FROM
                            administracao.configuracao
                        WHERE
                            cod_modulo = 2
                            AND exercicio = '".$inExercicio."'
                            AND parametro = 'fax'
                    ) AS fax,
                    (
                        SELECT
                            valor
                        FROM
                            administracao.configuracao
                        WHERE
                            cod_modulo = 2
                            AND exercicio = '".$inExercicio."'
                            AND parametro = 'numero'
                    ) AS numero,
                    (
                        SELECT
                            valor
                        FROM
                            administracao.configuracao
                        WHERE
                            cod_modulo = 2
                            AND exercicio = '".$inExercicio."'
                            AND parametro = 'nom_prefeitura'
                    ) AS nom_prefeitura,
                    (
                        SELECT
                            valor
                        FROM
                            administracao.configuracao
                        WHERE
                            cod_modulo = 2
                            AND exercicio = '".$inExercicio."'
                            AND parametro = 'tipo_logradouro'
                    ) AS tipo_logradouro,
                    (
                        SELECT
                            valor
                        FROM
                            administracao.configuracao
                        WHERE
                            cod_modulo = 2
                            AND exercicio = '".$inExercicio."'
                            AND parametro = 'complemento'
                    ) AS complemento,
                    (
                        SELECT
                            valor
                        FROM
                            administracao.configuracao
                        WHERE
                            cod_modulo = 2
                            AND exercicio = '".$inExercicio."'
                            AND parametro = 'logradouro'
                    ) AS logradouro,
                    (
                        SELECT
                            nom_municipio
                        FROM
                            sw_municipio
                        WHERE
                            sw_municipio.cod_municipio = (
                                SELECT
                                    valor
                                FROM
                                    administracao.configuracao
                                WHERE
                                    cod_modulo = 2
                                    AND exercicio = '".$inExercicio."'
                                    AND parametro = 'cod_municipio'
                            )::integer
                            AND sw_municipio.cod_uf = (
                                SELECT
                                    valor
                                FROM
                                    administracao.configuracao
                                WHERE
                                    cod_modulo = 2
                                    AND exercicio = '".$inExercicio."'
                                    AND parametro = 'cod_uf'
                            )::integer
                    ) AS nom_municipio,
                    (
                        SELECT
                            sigla_uf
                        FROM
                            sw_uf
                        WHERE
                            sw_uf.cod_uf = (
                                SELECT
                                    valor
                                FROM
                                    administracao.configuracao
                                WHERE
                                    cod_modulo = 2
                                    AND exercicio = '".$inExercicio."'
                                    AND parametro = 'cod_uf'
                            )::integer
                    ) AS nom_uf,
                    (
                        SELECT
                            valor
                        FROM
                            administracao.configuracao
                        WHERE
                            cod_modulo = 25
                            AND exercicio = '".$inExercicio."'
                            AND parametro = 'carne_dam'
                    ) AS carne_dam,
                    (
                        SELECT
                            valor
                        FROM
                            administracao.configuracao
                        WHERE
                            cod_modulo = 25
                            AND exercicio = '".$inExercicio."'
                            AND parametro = 'carne_departamento'
                    ) AS carne_departamento,
                    (
                        SELECT
                            valor
                        FROM
                            administracao.configuracao
                        WHERE
                            cod_modulo = 25
                            AND exercicio = '".$inExercicio."'
                            AND parametro = 'carne_secretaria'
                    ) AS carne_secretaria ";

    return $stSql;
}

function recuperaNivelCarneIPTUGenerico(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaNivelCarneIPTUGenerico().$stFiltro;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaNivelCarneIPTUGenerico()
{
    $stSql  = " SELECT
                    nom_nivel
                FROM
                    imobiliario.nivel
                WHERE
                    nivel.cod_vigencia = (
                        SELECT
                            cod_vigencia
                        FROM
                            imobiliario.vigencia
                        WHERE
                            vigencia.dt_inicio < now()::date
                        ORDER BY
                            vigencia.dt_inicio
                        DESC
                        LIMIT 1
                    )
                ORDER BY
                    nivel.cod_nivel
                ASC \n";

    return $stSql;
}

function recuperaDadosIPTUMata(&$rsRecordSet, $stFiltro = "", $inCodParcela = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosIPTUMata( $stFiltro, $inCodParcela );
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function recuperaDadosIPTUMataDesonerado(&$rsRecordSet, $stFiltro = "", $inCodLancamento = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosIPTUMataDesonerado( $stFiltro, $inCodLancamento );
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaDadosIPTUMata($stFiltro = "", $inCodParcela)
{
    $stSql  = " SELECT \n";
    $stSql .= "     tmp.numero_quadra, \n";
    $stSql .= "     tmp.exercicio, \n";
    $stSql .= "     tmp.nom_localizacao AS distrito, \n";
    $stSql .= "     tmp.regiao, \n";
    $stSql .= "     tmp.zona, \n";
    $stSql .= "    (
                    split_part( tmp.valor_m2_limpeza, '§', 2 )
                   )AS valor_m2_limpeza_publica, \n";
    $stSql .= "     (
                        split_part( tmp.valor_m2_limpeza, '§', 1 )
                    )AS area_m2_limpeza_publica, \n";
    $stSql .= "     economico.fn_busca_aliquota_imposto( tmp.inscricao_municipal, tmp.exercicio::integer ) AS aliquota, \n";

    $stSql .= "     tmp.nro_parcela, \n";
    $stSql .= "     tmp.vencimento_parcela, \n";
    $stSql .= "     tmp.valor_parcela, \n";
    $stSql .= "     tmp.area_lote, \n";
    $stSql .= "     tmp.area_imovel, \n";
    $stSql .= "     tmp.area_imovel + arrecadacao.fn_vc2num(recuperaCadastroImobiliarioImovelAreaTotalDescoberta( tmp.inscricao_municipal )) AS area_total, \n";
    $stSql .= "     arrecadacao.fn_vc2num(recuperaCadastroImobiliarioImovelAreaTotalDescoberta( tmp.inscricao_municipal )) AS area_descoberta, \n";

    $stSql .= "     array_to_string(arrecadacao.fn_busca_endereco_carne(tmp.inscricao_municipal, tmp.exercicio::integer,(SELECT cod_lancamento from arrecadacao.parcela where cod_parcela = ".$inCodParcela.")  ),'|*|') as endereco_entrega, \n";

    $stSql .= "     ( arrecadacao.fn_vc2num(recuperaCadastroImobiliarioImovelAreaTotalDescoberta( tmp.inscricao_municipal )) * ( tmp.vupc / 2 ) )::numeric(14,2) AS valor_venal_construcao_descoberta, \n";
    $stSql .= "     ( tmp.area_imovel * tmp.vupc )::numeric(14,2) AS valor_venal_construcao_coberta, \n";

    $stSql .= "     tmp.cod_calculo, \n";
    $stSql .= "     tmp.vupt, \n";
    $stSql .= "     tmp.numero_lote, \n";

    $stSql .= "     CASE WHEN tmp.venal_territorial_calculado IS NOT NULL THEN
                        tmp.venal_territorial_calculado
                    ELSE
                        (
                        SELECT
                            COALESCE( venal_territorial_informado, venal_territorial_calculado)
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = tmp.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = tmp.exercicio
                            AND (venal_territorial_informado IS NOT NULL OR venal_territorial_calculado IS NOT NULL )
                        order by timestamp desc limit 1
                        )
                    END AS venal_territorial_calculado, \n";

    $stSql .= "     CASE WHEN tmp.venal_predial_calculado IS NOT NULL THEN
                        tmp.venal_predial_calculado
                    ELSE
                        (
                        SELECT
                            COALESCE( venal_predial_informado, venal_predial_calculado)
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = tmp.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = tmp.exercicio
                            AND (venal_predial_informado IS NOT NULL OR venal_predial_calculado IS NOT NULL )
                        order by timestamp desc limit 1
                        )
                    END AS venal_predial_calculado, \n";

    $stSql .= "     CASE WHEN tmp.venal_total_calculado IS NOT NULL THEN
                        tmp.venal_total_calculado
                    ELSE
                        (
                        SELECT
                            COALESCE( venal_total_informado, venal_total_calculado)
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = tmp.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = tmp.exercicio
                            AND (venal_total_informado IS NOT NULL OR venal_total_calculado IS NOT NULL )
                        order by timestamp desc limit 1
                        )
                    END AS venal_total_calculado, \n";

    $stSql .= "     tmp.inscricao_municipal, \n";

    $stSql .= "     (
                        SELECT
                            iic.cod_condominio || ic.nom_condominio

                        FROM
                            imobiliario.imovel_condominio AS iic

                        INNER JOIN
                            imobiliario.condominio AS ic
                        ON
                            ic.cod_condominio = iic.cod_condominio

                        WHERE
                            iic.inscricao_municipal = tmp.inscricao_municipal
                    )AS condominio, \n";
    $stSql .= "     tmp.cod_logradouro, \n";
    $stSql .= "     tmp.endereco_logradouro, \n";
    $stSql .= "     tmp.endereco_numero, \n";
    $stSql .= "     tmp.endereco_complemento, \n";
    $stSql .= "     tmp.cep, \n";
    $stSql .= "     tmp.nom_proprietario, \n";
    $stSql .= "     tmp.vupc, \n";
    $stSql .= "     ( tmp.vupc / 2 )::numeric(14,2) as vupcd , \n";

    $stSql .= "     tmp.numcgm_proprietario, \n";
    $stSql .= "     tmp.categoria_utilizacao_imovel, \n";
    $stSql .= "     COALESCE( tmp.taxa_limpeza_publica, 0.00) AS taxa_limpeza_publica, \n";
    $stSql .= "     COALESCE( tmp.imposto_territorial, 0.00) AS imposto_territorial, \n";
    $stSql .= "     COALESCE( tmp.imposto_predial, 0.00) AS imposto_predial, \n";
    $stSql .= "     COALESCE( tmp.imposto_territorial, 0.00) + COALESCE( tmp.imposto_predial, 0.00) AS  valor_imposto, \n";
    $stSql .= "     COALESCE( tmp.imposto_territorial, 0.00) + COALESCE( tmp.imposto_predial, 0.00) + COALESCE( tmp.taxa_limpeza_publica, 0.00) + COALESCE( tmp.taxa_luz, 0.00) AS valor_total_tributos, \n";
    $stSql .= "     COALESCE( tmp.taxa_luz, 0.00) AS taxa_luz \n";
    $stSql .= " FROM \n";
    $stSql .= "     ( \n";
    $stSql .= "     SELECT \n";
    $stSql .= "         economico.fn_busca_dados_taxa_limpeza( aivv.inscricao_municipal, aivv.exercicio::integer ) AS valor_m2_limpeza, \n";
    $stSql .= "         aivv.exercicio, \n";
    $stSql .= "         il.nom_localizacao, \n";
    $stSql .= "         CASE WHEN ap.nr_parcela = 0 THEN \n";
    $stSql .= "             'única'::text \n";
    $stSql .= "         ELSE \n";
    $stSql .= "             ap.nr_parcela::text \n";
    $stSql .= "         END AS nro_parcela, \n";
    $stSql .= "         to_char(ap.vencimento, 'dd/mm/yyyy' ) AS vencimento_parcela, \n";
    $stSql .= "         COALESCE( apd.valor, ap.valor ) AS valor_parcela, \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 COALESCE( ac.valor, 0.00 ) \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 arrecadacao.calculo AS ac \n";
    $stSql .= "             WHERE \n";
    $stSql .= "                 ac.cod_credito = 14 \n";
    $stSql .= "                 AND ac.cod_natureza = 1 \n";
    $stSql .= "                 AND ac.cod_genero = 2 \n";
    $stSql .= "                 AND ac.cod_especie = 1 \n";
    $stSql .= "                 AND ac.cod_calculo in (SELECT cod_calculo FROM arrecadacao.lancamento_calculo WHERE arrecadacao.lancamento_calculo.cod_lancamento = (SELECT cod_lancamento from arrecadacao.parcela where cod_parcela = ".$inCodParcela.") ) \n";
    $stSql .= "         )::numeric(14,6) AS taxa_limpeza_publica, \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 COALESCE( ac.valor, 0.00 ) \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 arrecadacao.calculo AS ac \n";
    $stSql .= "             WHERE \n";
    $stSql .= "                 ac.cod_credito = 2 \n";
    $stSql .= "                 AND ac.cod_natureza = 1 \n";
    $stSql .= "                 AND ac.cod_genero = 1 \n";
    $stSql .= "                 AND ac.cod_especie = 1 \n";
    $stSql .= "                 AND ac.cod_calculo in (
                                                        SELECT
                                                            cod_calculo
                                                        FROM
                                                            arrecadacao.lancamento_calculo
                                                        WHERE arrecadacao.lancamento_calculo.cod_lancamento = (SELECT cod_lancamento from arrecadacao.parcela where cod_parcela = ".$inCodParcela.")) \n";
    $stSql .= "         )::numeric(14,6) AS imposto_territorial, \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 COALESCE( ac.valor, 0.00 ) \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 arrecadacao.calculo AS ac \n";
    $stSql .= "             WHERE \n";
    $stSql .= "                 ac.cod_credito = 3 \n";
    $stSql .= "                 AND ac.cod_natureza = 1 \n";
    $stSql .= "                 AND ac.cod_genero = 1 \n";
    $stSql .= "                 AND ac.cod_especie = 1 \n";
    $stSql .= "                 AND ac.cod_calculo in (SELECT cod_calculo FROM arrecadacao.lancamento_calculo WHERE arrecadacao.lancamento_calculo.cod_lancamento = (SELECT cod_lancamento from arrecadacao.parcela where cod_parcela = ".$inCodParcela.") ) \n";
    $stSql .= "         )::numeric(14,6) AS imposto_predial, \n";
    $stSql .= "         arrecadacao.fn_area_real( aivv.inscricao_municipal  ) AS area_lote, \n";
    $stSql .= "         imobiliario.fn_calcula_area_imovel( aivv.inscricao_municipal ) AS area_imovel, \n";
    $stSql .= "         aic.cod_calculo, \n";
    $stSql .= "         aivv.timestamp, \n";
    $stSql .= "         itvm.valor_m2_territorial AS vupt, \n";

    $stSql .= "         (
                          SELECT
                              COALESCE(
                                   (
                                       SELECT valor from
                                            imobiliario.atributo_lote_urbano_valor as ialu
                                       WHERE ialu.cod_atributo = 7
                                       AND  ialu.cod_lote = il.cod_lote
                                       ORDER BY ialu.timestamp DESC limit 1
                                   ),
                                   (
                                       SELECT valor from
                                            imobiliario.atributo_lote_rural_valor as ialr
                                       WHERE ialr.cod_atributo = 7
                                       AND  ialr.cod_lote = il.cod_lote
                                       ORDER BY ialr.timestamp DESC limit 1
                                   )
                               )::varchar AS valor
                           FROM
                               imobiliario.lote as il

                           WHERE il.cod_lote = iic.cod_lote
                         ) AS numero_lote, \n";

    $stSql .= "         (
                           SELECT
                              COALESCE(
                                   (
                                       SELECT valor from
                                            imobiliario.atributo_lote_urbano_valor as ialu
                                       WHERE ialu.cod_atributo = 5
                                       AND  ialu.cod_lote = il.cod_lote
                                       ORDER BY ialu.timestamp DESC limit 1
                                   ),
                                   (
                                       SELECT valor from
                                            imobiliario.atributo_lote_rural_valor as ialr
                                       WHERE ialr.cod_atributo = 5
                                       AND  ialr.cod_lote = il.cod_lote
                                       ORDER BY ialr.timestamp DESC limit 1
                                   )
                               )::varchar AS valor
                           FROM
                               imobiliario.lote as il

                           WHERE il.cod_lote = iic.cod_lote
                         ) AS numero_quadra, \n";

    $stSql .= "        (
                        SELECT
                            venal_territorial_calculado
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = aivv.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = aivv.exercicio
                            AND arrecadacao.imovel_v_venal.timestamp = aic.timestamp
                        order by arrecadacao.imovel_v_venal.timestamp limit 1
                       )AS venal_territorial_calculado, \n";

    $stSql .= "        (
                        SELECT
                            venal_predial_calculado
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = aivv.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = aivv.exercicio
                            AND arrecadacao.imovel_v_venal.timestamp = aic.timestamp
                       )AS venal_predial_calculado, \n";

    $stSql .= "        (
                        SELECT
                            venal_total_calculado
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = aivv.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = aivv.exercicio
                            AND arrecadacao.imovel_v_venal.timestamp = aic.timestamp
                       )AS venal_total_calculado, \n";

    $stSql .= "         aivv.inscricao_municipal, \n";
    $stSql .= "         it.cod_logradouro, \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 coalesce(tl.nom_tipo, '' ) ||' '|| coalesce(nl.nom_logradouro, '' ) \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 sw_nome_logradouro nl, \n";
    $stSql .= "                 sw_tipo_logradouro tl \n";
    $stSql .= "             WHERE \n";
    $stSql .= "                 nl.cod_logradouro = it.cod_logradouro \n";
    $stSql .= "                 AND tl.cod_tipo = nl.cod_tipo \n";
    $stSql .= "             ORDER BY nl.timestamp desc limit 1 \n";
    $stSql .= "         )AS endereco_logradouro, \n";
    $stSql .= "         ii.numero AS endereco_numero, \n";
    $stSql .= "         ii.complemento AS endereco_complemento, \n";
    $stSql .= "         ii.cep, \n";
    $stSql .= "         array_to_string(array(
                             SELECT
                                 cgm.nom_cgm
                             FROM
                                 imobiliario.proprietario AS ip
                             INNER JOIN
                                 sw_cgm AS cgm
                             ON
                                 cgm.numcgm = ip.numcgm
                             WHERE
                                 ip.inscricao_municipal = aivv.inscricao_municipal
                             ORDER BY
                                 cgm.nom_cgm
                         ), '/')AS nom_proprietario,\n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 ip.numcgm \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 imobiliario.proprietario AS ip \n";
    $stSql .= "             WHERE \n";
    $stSql .= "                 ip.inscricao_municipal = aivv.inscricao_municipal \n";
    $stSql .= "             ORDER BY   \n";
    $stSql .= "                 ip.cota DESC \n";
    $stSql .= "             LIMIT 1 \n";
    $stSql .= "         )AS numcgm_proprietario, \n";

    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 COALESCE( ac.valor, 0.00 ) \n";
    $stSql .= "             FROM  \n";
    $stSql .= "                 arrecadacao.calculo AS ac \n";
    $stSql .= "             WHERE  \n";
    $stSql .= "                 ac.cod_credito = 16 \n";
    $stSql .= "                 AND ac.cod_natureza = 1  \n";
    $stSql .= "                 AND ac.cod_genero = 2  \n";
    $stSql .= "                 AND ac.cod_especie = 1  \n";
    $stSql .= "                 AND ac.cod_calculo in (SELECT cod_calculo FROM arrecadacao.lancamento_calculo WHERE arrecadacao.lancamento_calculo.cod_lancamento = (SELECT cod_lancamento from arrecadacao.parcela where cod_parcela = ".$inCodParcela.") ) \n";
    $stSql .= "         )AS taxa_luz, \n";

    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 aavp.valor_padrao \n";

    $stSql .= "             FROM \n";
    $stSql .= "                 imobiliario.atributo_imovel_valor AS iaiv \n";

    $stSql .= "             INNER JOIN \n";
    $stSql .= "                 administracao.atributo_valor_padrao AS aavp \n";
    $stSql .= "             ON \n";
    $stSql .= "                 aavp.cod_valor = iaiv.valor::integer \n";
    $stSql .= "                 AND aavp.cod_cadastro = 4 \n";
    $stSql .= "                 AND aavp.cod_modulo = 12 \n";
    $stSql .= "                 AND aavp.cod_atributo = 106 \n";

    $stSql .= "             WHERE \n";
    $stSql .= "                 iaiv.inscricao_municipal = aivv.inscricao_municipal \n";
    $stSql .= "                 AND iaiv.cod_atributo = 106 \n";
    $stSql .= "                 AND iaiv.cod_cadastro = 4 \n";
    $stSql .= "                 AND iaiv.cod_modulo = 12 \n";
    $stSql .= "                 AND iaiv.timestamp < aivv.timestamp \n";

    $stSql .= "             ORDER BY \n";
    $stSql .= "                 iaiv.timestamp DESC \n";

    $stSql .= "             LIMIT 1 \n";
    $stSql .= "         )AS zona, \n";

    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 aavp.valor_padrao \n";

    $stSql .= "             FROM \n";
    $stSql .= "                 imobiliario.atributo_imovel_valor AS iaiv \n";

    $stSql .= "             INNER JOIN \n";
    $stSql .= "                 administracao.atributo_valor_padrao AS aavp \n";
    $stSql .= "             ON \n";
    $stSql .= "                 aavp.cod_valor = iaiv.valor::integer \n";
    $stSql .= "                 AND aavp.cod_cadastro = 4 \n";
    $stSql .= "                 AND aavp.cod_modulo = 12 \n";
    $stSql .= "                 AND aavp.cod_atributo = 8 \n";

    $stSql .= "             WHERE \n";
    $stSql .= "                 iaiv.inscricao_municipal = aivv.inscricao_municipal \n";
    $stSql .= "                 AND iaiv.cod_atributo = 8 \n";
    $stSql .= "                 AND iaiv.cod_cadastro = 4 \n";
    $stSql .= "                 AND iaiv.cod_modulo = 12 \n";
    $stSql .= "                 AND iaiv.timestamp < aivv.timestamp \n";

    $stSql .= "             ORDER BY \n";
    $stSql .= "                 iaiv.timestamp DESC \n";

    $stSql .= "             LIMIT 1 \n";
    $stSql .= "         )AS categoria_utilizacao_imovel, \n";

    $stSql .= "             CASE WHEN iua.cod_tipo IS NOT NULL AND iua.cod_construcao IS NOT NULL THEN \n";
    $stSql .= "                  ( \n";

    $stSql .= "                      SELECT \n";
    $stSql .= "                         atcv.valor \n";
    $stSql .= "                      FROM \n";
    $stSql .= "                         imobiliario.atributo_tipo_edificacao_valor AS iatev \n";

    $stSql .= "                      INNER JOIN \n";
    $stSql .= "                         arrecadacao.tabela_conversao_valores AS atcv \n";
    $stSql .= "                      ON \n";
    $stSql .= "                         atcv.cod_tabela = 30 \n";
    $stSql .= "                         AND atcv.parametro_1 = iatev.valor \n";
    $stSql .= "                         AND atcv.exercicio = aivv.exercicio \n";
    $stSql .= "                      WHERE \n";
    $stSql .= "                         iatev.cod_atributo = 3 and \n";
    $stSql .= "                         iatev.cod_tipo = iua.cod_tipo \n";
    $stSql .= "                         AND iatev.cod_construcao = iua.cod_construcao \n";
    $stSql .= "                         AND iatev.timestamp < aivv.timestamp \n";
    $stSql .= "                      ORDER BY \n";
    $stSql .= "                         iatev.timestamp DESC \n";
    $stSql .= "                      LIMIT 1 \n";
    $stSql .= "                    )::numeric(14,6) \n";
    $stSql .= "               ELSE \n";
    $stSql .= "                 0.00 \n";
    $stSql .= "               END AS vupc,\n";

    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                tmp_il.nom_localizacao \n";
    $stSql .= "             FROM \n";
    $stSql .= "                imobiliario.localizacao AS tmp_il \n";

    $stSql .= "             INNER JOIN \n";
    $stSql .= "                imobiliario.localizacao_nivel AS tmp_iln \n";
    $stSql .= "             ON  \n";
    $stSql .= "                tmp_il.codigo_composto = tmp_iln.valor || '.00' \n";
    $stSql .= "                AND tmp_iln.cod_localizacao = il.cod_localizacao \n";
    $stSql .= "                AND tmp_iln.cod_nivel = 1 \n";
    $stSql .= "         ) AS regiao \n";

    $stSql .= "     FROM \n";
    $stSql .= "         arrecadacao.imovel_v_venal AS aivv \n";

    $stSql .= "     LEFT JOIN \n";
    $stSql .= "         imobiliario.unidade_autonoma AS iua \n";
    $stSql .= "     ON \n";
    $stSql .= "         iua.inscricao_municipal = aivv.inscricao_municipal \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         imobiliario.imovel AS ii \n";
    $stSql .= "     ON \n";
    $stSql .= "         ii.inscricao_municipal = aivv.inscricao_municipal \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         imobiliario.imovel_confrontacao AS iic \n";
    $stSql .= "     ON \n";
    $stSql .= "         iic.inscricao_municipal = ii.inscricao_municipal \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         imobiliario.lote_localizacao AS ill \n";
    $stSql .= "     ON \n";
    $stSql .= "         ill.cod_lote = iic.cod_lote \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         imobiliario.localizacao AS il \n";
    $stSql .= "     ON \n";
    $stSql .= "         il.cod_localizacao = ill.cod_localizacao \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         imobiliario.confrontacao_trecho AS ict \n";
    $stSql .= "     ON \n";
    $stSql .= "         ict.cod_confrontacao = iic.cod_confrontacao \n";
    $stSql .= "         AND ict.cod_lote = iic.cod_lote \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 ial.* \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 imobiliario.area_lote AS ial, \n";
    $stSql .= "                 ( \n";
    $stSql .= "                     SELECT \n";
    $stSql .= "                         MAX( timestamp ) AS timestamp, \n";
    $stSql .= "                         cod_lote \n";
    $stSql .= "                     FROM \n";
    $stSql .= "                         imobiliario.area_lote \n";
    $stSql .= "                     GROUP BY \n";
    $stSql .= "                         cod_lote \n";
    $stSql .= "                 )AS tmp \n";
    $stSql .= "             WHERE \n";
    $stSql .= "                 tmp.cod_lote = ial.cod_lote \n";
    $stSql .= "                 AND tmp.timestamp = ial.timestamp \n";
    $stSql .= "         )AS ial \n";
    $stSql .= "     ON \n";
    $stSql .= "         ial.cod_lote = iic.cod_lote \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         imobiliario.trecho it \n";
    $stSql .= "     ON \n";
    $stSql .= "         it.cod_trecho = ict.cod_trecho \n";
    $stSql .= "         AND it.cod_logradouro = ict.cod_logradouro \n";

    $stSql .= "     INNER JOIN
                        (
                            SELECT
                                tmp.*

                            FROM
                                imobiliario.trecho_valor_m2 AS tmp

                            INNER JOIN
                                (
                                    SELECT
                                        MAX( trecho_valor_m2.timestamp ) AS timestamp,
                                        trecho_valor_m2.cod_logradouro,
                                        trecho_valor_m2.cod_trecho

                                    FROM
                                        imobiliario.trecho_valor_m2

                                    GROUP BY
                                        trecho_valor_m2.cod_logradouro,
                                        trecho_valor_m2.cod_trecho
                                )AS tmp2
                            ON
                                tmp2.cod_logradouro = tmp.cod_logradouro
                                AND tmp2.cod_trecho = tmp.cod_trecho
                                AND tmp2.timestamp = tmp.timestamp
                        )AS itvm                                   ";
    $stSql .= "     ON \n";
    $stSql .= "         itvm.cod_trecho = ict.cod_trecho \n";
    $stSql .= "         AND itvm.cod_logradouro = ict.cod_logradouro \n";
    $stSql .= "     INNER JOIN  \n";
    $stSql .= "          arrecadacao.imovel_calculo AS aic \n";
    $stSql .= "     ON  \n";
    $stSql .= "         aic.timestamp = aivv.timestamp \n";
    $stSql .= "         AND aic.inscricao_municipal = aivv.inscricao_municipal \n";
    $stSql .= "         AND aic.cod_calculo = (SELECT cod_calculo FROM arrecadacao.lancamento_calculo WHERE arrecadacao.lancamento_calculo.cod_lancamento = (SELECT cod_lancamento from arrecadacao.parcela where cod_parcela = ".$inCodParcela.") limit 1) \n";

    $stSql .= "     INNER JOIN  \n";
    $stSql .= "         arrecadacao.parcela AS ap \n";
    $stSql .= "     ON  \n";
    $stSql .= "        ap.cod_lancamento = (SELECT cod_lancamento from arrecadacao.parcela where cod_parcela = ".$inCodParcela.")

                    LEFT JOIN
                        arrecadacao.parcela_desconto AS apd
                    ON
                        apd.cod_parcela = ap.cod_parcela \n";

    $stSql .= $stFiltro;

    $stSql .= " )AS tmp \n";

    return $stSql;
}

function montaRecuperaDadosIPTUMataDesonerado($stFiltro = "", $inCodLancamento)
{
    $stSql  = " SELECT \n";
    $stSql .= "     tmp.numero_quadra, \n";
    $stSql .= "     tmp.exercicio, \n";
    $stSql .= "     tmp.nom_localizacao AS distrito, \n";
    $stSql .= "     tmp.regiao, \n";
    $stSql .= "     tmp.zona, \n";
    $stSql .= "    (
                    split_part( tmp.valor_m2_limpeza, '§', 2 )
                   )AS valor_m2_limpeza_publica, \n";
    $stSql .= "     (
                        split_part( tmp.valor_m2_limpeza, '§', 1 )
                    )AS area_m2_limpeza_publica, \n";
    $stSql .= "     economico.fn_busca_aliquota_imposto( tmp.inscricao_municipal, tmp.exercicio::integer ) AS aliquota, \n";

    $stSql .= "     tmp.nro_parcela, \n";
    $stSql .= "     tmp.vencimento_parcela, \n";
    $stSql .= "     tmp.valor_parcela, \n";
    $stSql .= "     tmp.area_lote, \n";
    $stSql .= "     tmp.area_imovel, \n";
    $stSql .= "     tmp.area_imovel + arrecadacao.fn_vc2num(recuperaCadastroImobiliarioImovelAreaTotalDescoberta( tmp.inscricao_municipal )) AS area_total, \n";
    $stSql .= "     arrecadacao.fn_vc2num(recuperaCadastroImobiliarioImovelAreaTotalDescoberta( tmp.inscricao_municipal )) AS area_descoberta, \n";
    $stSql .= "     ( arrecadacao.fn_vc2num(recuperaCadastroImobiliarioImovelAreaTotalDescoberta( tmp.inscricao_municipal )) * ( tmp.vupc / 2 ) )::numeric(14,2) AS valor_venal_construcao_descoberta, \n";
    $stSql .= "     ( tmp.area_imovel * tmp.vupc )::numeric(14,2) AS valor_venal_construcao_coberta, \n";

    $stSql .= "     tmp.cod_calculo, \n";
    $stSql .= "     tmp.vupt, \n";
    $stSql .= "     tmp.numero_lote, \n";

    $stSql .= "     CASE WHEN tmp.venal_territorial_calculado IS NOT NULL THEN
                        tmp.venal_territorial_calculado
                    ELSE
                        (
                        SELECT
                            COALESCE( venal_territorial_informado, venal_territorial_calculado)
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = tmp.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = tmp.exercicio
                            AND (venal_territorial_informado IS NOT NULL OR venal_territorial_calculado IS NOT NULL )
                        order by timestamp desc limit 1
                        )
                    END AS venal_territorial_calculado, \n";

    $stSql .= "     CASE WHEN tmp.venal_predial_calculado IS NOT NULL THEN
                        tmp.venal_predial_calculado
                    ELSE
                        (
                        SELECT
                            COALESCE( venal_predial_informado, venal_predial_calculado)
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = tmp.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = tmp.exercicio
                            AND (venal_predial_informado IS NOT NULL OR venal_predial_calculado IS NOT NULL )
                        order by timestamp desc limit 1
                        )
                    END AS venal_predial_calculado, \n";

    $stSql .= "     CASE WHEN tmp.venal_total_calculado IS NOT NULL THEN
                        tmp.venal_total_calculado
                    ELSE
                        (
                        SELECT
                            COALESCE( venal_total_informado, venal_total_calculado)
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = tmp.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = tmp.exercicio
                            AND (venal_total_informado IS NOT NULL OR venal_total_calculado IS NOT NULL )
                        order by timestamp desc limit 1
                        )
                    END AS venal_total_calculado, \n";

    $stSql .= "     tmp.inscricao_municipal, \n";

    $stSql .= "     (
                        SELECT
                            iic.cod_condominio || ic.nom_condominio

                        FROM
                            imobiliario.imovel_condominio AS iic

                        INNER JOIN
                            imobiliario.condominio AS ic
                        ON
                            ic.cod_condominio = iic.cod_condominio

                        WHERE
                            iic.inscricao_municipal = tmp.inscricao_municipal
                    )AS condominio, \n";
    $stSql .= "     tmp.cod_logradouro, \n";
    $stSql .= "     tmp.endereco_logradouro, \n";
    $stSql .= "     tmp.endereco_numero, \n";
    $stSql .= "     tmp.endereco_complemento, \n";
    $stSql .= "     tmp.cep, \n";
    $stSql .= "     tmp.nom_proprietario, \n";
    $stSql .= "     tmp.vupc, \n";
    $stSql .= "     ( tmp.vupc / 2 )::numeric(14,2) as vupcd , \n";

    $stSql .= "     tmp.numcgm_proprietario, \n";
    $stSql .= "     tmp.categoria_utilizacao_imovel, \n";
    $stSql .= "     COALESCE( tmp.taxa_limpeza_publica, 0.00) AS taxa_limpeza_publica, \n";
    $stSql .= "     COALESCE( tmp.imposto_territorial, 0.00) AS imposto_territorial, \n";
    $stSql .= "     COALESCE( tmp.imposto_predial, 0.00) AS imposto_predial, \n";
    $stSql .= "     COALESCE( tmp.imposto_territorial, 0.00) + COALESCE( tmp.imposto_predial, 0.00) AS  valor_imposto, \n";
    $stSql .= "     COALESCE( tmp.imposto_territorial, 0.00) + COALESCE( tmp.imposto_predial, 0.00) + COALESCE( tmp.taxa_limpeza_publica, 0.00) + COALESCE( tmp.taxa_luz, 0.00) AS valor_total_tributos, \n";
    $stSql .= "     COALESCE( tmp.taxa_luz, 0.00) AS taxa_luz \n";
    $stSql .= " FROM \n";
    $stSql .= "     ( \n";
    $stSql .= "     SELECT \n";
    $stSql .= "         economico.fn_busca_dados_taxa_limpeza( aivv.inscricao_municipal, aivv.exercicio::integer ) AS valor_m2_limpeza, \n";
    $stSql .= "         aivv.exercicio, \n";
    $stSql .= "         il.nom_localizacao, \n";
    $stSql .= "         '0'::text AS nro_parcela, \n";
    $stSql .= "         '00/00/0000'::text AS vencimento_parcela, \n";
    $stSql .= "         '0'::float AS valor_parcela, \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 COALESCE( ac.valor, 0.00 ) \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 arrecadacao.calculo AS ac \n";
    $stSql .= "             WHERE \n";
    $stSql .= "                 ac.cod_credito = 14 \n";
    $stSql .= "                 AND ac.cod_natureza = 1 \n";
    $stSql .= "                 AND ac.cod_genero = 2 \n";
    $stSql .= "                 AND ac.cod_especie = 1 \n";
    $stSql .= "                 AND ac.cod_calculo in (SELECT cod_calculo FROM arrecadacao.lancamento_calculo WHERE arrecadacao.lancamento_calculo.cod_lancamento = $inCodLancamento ) \n";
    $stSql .= "         )::numeric(14,6) AS taxa_limpeza_publica, \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 COALESCE( ac.valor, 0.00 ) \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 arrecadacao.calculo AS ac \n";
    $stSql .= "             WHERE \n";
    $stSql .= "                 ac.cod_credito = 2 \n";
    $stSql .= "                 AND ac.cod_natureza = 1 \n";
    $stSql .= "                 AND ac.cod_genero = 1 \n";
    $stSql .= "                 AND ac.cod_especie = 1 \n";
    $stSql .= "                 AND ac.cod_calculo in (
                                                        SELECT
                                                            cod_calculo
                                                        FROM
                                                            arrecadacao.lancamento_calculo
                                                        WHERE arrecadacao.lancamento_calculo.cod_lancamento = $inCodLancamento ) \n";
    $stSql .= "         )::numeric(14,6) AS imposto_territorial, \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 COALESCE( ac.valor, 0.00 ) \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 arrecadacao.calculo AS ac \n";
    $stSql .= "             WHERE \n";
    $stSql .= "                 ac.cod_credito = 3 \n";
    $stSql .= "                 AND ac.cod_natureza = 1 \n";
    $stSql .= "                 AND ac.cod_genero = 1 \n";
    $stSql .= "                 AND ac.cod_especie = 1 \n";
    $stSql .= "                 AND ac.cod_calculo in (SELECT cod_calculo FROM arrecadacao.lancamento_calculo WHERE arrecadacao.lancamento_calculo.cod_lancamento = $inCodLancamento ) \n";
    $stSql .= "         )::numeric(14,6) AS imposto_predial, \n";
    $stSql .= "         imobiliario.fn_area_real( aivv.inscricao_municipal ) AS area_lote, \n";
    $stSql .= "         imobiliario.fn_calcula_area_imovel( aivv.inscricao_municipal ) AS area_imovel, \n";
    $stSql .= "         aic.cod_calculo, \n";
    $stSql .= "         aivv.timestamp, \n";
    $stSql .= "         itvm.valor_m2_territorial AS vupt, \n";

    $stSql .= "         (
                          SELECT
                              COALESCE(
                                   (
                                       SELECT valor from
                                            imobiliario.atributo_lote_urbano_valor as ialu
                                       WHERE ialu.cod_atributo = 7
                                       AND  ialu.cod_lote = il.cod_lote
                                       ORDER BY ialu.timestamp DESC limit 1
                                   ),
                                   (
                                       SELECT valor from
                                            imobiliario.atributo_lote_rural_valor as ialr
                                       WHERE ialr.cod_atributo = 7
                                       AND  ialr.cod_lote = il.cod_lote
                                       ORDER BY ialr.timestamp DESC limit 1
                                   )
                               )::varchar AS valor
                           FROM
                               imobiliario.lote as il

                           WHERE il.cod_lote = iic.cod_lote
                         ) AS numero_lote, \n";

    $stSql .= "         (
                           SELECT
                              COALESCE(
                                   (
                                       SELECT valor from
                                            imobiliario.atributo_lote_urbano_valor as ialu
                                       WHERE ialu.cod_atributo = 5
                                       AND  ialu.cod_lote = il.cod_lote
                                       ORDER BY ialu.timestamp DESC limit 1
                                   ),
                                   (
                                       SELECT valor from
                                            imobiliario.atributo_lote_rural_valor as ialr
                                       WHERE ialr.cod_atributo = 5
                                       AND  ialr.cod_lote = il.cod_lote
                                       ORDER BY ialr.timestamp DESC limit 1
                                   )
                               )::varchar AS valor
                           FROM
                               imobiliario.lote as il

                           WHERE il.cod_lote = iic.cod_lote
                         ) AS numero_quadra, \n";

    $stSql .= "        (
                        SELECT
                            venal_territorial_calculado
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = aivv.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = aivv.exercicio
                            AND arrecadacao.imovel_v_venal.timestamp = aic.timestamp
                        order by arrecadacao.imovel_v_venal.timestamp limit 1
                       )AS venal_territorial_calculado, \n";

    $stSql .= "        (
                        SELECT
                            venal_predial_calculado
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = aivv.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = aivv.exercicio
                            AND arrecadacao.imovel_v_venal.timestamp = aic.timestamp
                       )AS venal_predial_calculado, \n";

    $stSql .= "        (
                        SELECT
                            venal_total_calculado
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = aivv.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = aivv.exercicio
                            AND arrecadacao.imovel_v_venal.timestamp = aic.timestamp
                       )AS venal_total_calculado, \n";

    $stSql .= "         aivv.inscricao_municipal, \n";
    $stSql .= "         it.cod_logradouro, \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 coalesce(tl.nom_tipo, '' ) ||' '|| coalesce(nl.nom_logradouro, '' ) \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 sw_nome_logradouro nl, \n";
    $stSql .= "                 sw_tipo_logradouro tl \n";
    $stSql .= "             WHERE \n";
    $stSql .= "                 nl.cod_logradouro = it.cod_logradouro \n";
    $stSql .= "                 AND tl.cod_tipo = nl.cod_tipo \n";
    $stSql .= "             ORDER BY nl.timestamp desc limit 1 \n";
    $stSql .= "         )AS endereco_logradouro, \n";
    $stSql .= "         ii.numero AS endereco_numero, \n";
    $stSql .= "         ii.complemento AS endereco_complemento, \n";
    $stSql .= "         ii.cep, \n";
    $stSql .= "         array_to_string(array(
                             SELECT
                                 cgm.nom_cgm
                             FROM
                                 imobiliario.proprietario AS ip
                             INNER JOIN
                                 sw_cgm AS cgm
                             ON
                                 cgm.numcgm = ip.numcgm
                             WHERE
                                 ip.inscricao_municipal = aivv.inscricao_municipal
                             ORDER BY
                                 cgm.nom_cgm
                         ), '/')AS nom_proprietario,\n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 ip.numcgm \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 imobiliario.proprietario AS ip \n";
    $stSql .= "             WHERE \n";
    $stSql .= "                 ip.inscricao_municipal = aivv.inscricao_municipal \n";
    $stSql .= "             ORDER BY   \n";
    $stSql .= "                 ip.cota DESC \n";
    $stSql .= "             LIMIT 1 \n";
    $stSql .= "         )AS numcgm_proprietario, \n";

    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 COALESCE( ac.valor, 0.00 ) \n";
    $stSql .= "             FROM  \n";
    $stSql .= "                 arrecadacao.calculo AS ac \n";
    $stSql .= "             WHERE  \n";
    $stSql .= "                 ac.cod_credito = 16 \n";
    $stSql .= "                 AND ac.cod_natureza = 1  \n";
    $stSql .= "                 AND ac.cod_genero = 2  \n";
    $stSql .= "                 AND ac.cod_especie = 1  \n";
    $stSql .= "                 AND ac.cod_calculo in (SELECT cod_calculo FROM arrecadacao.lancamento_calculo WHERE arrecadacao.lancamento_calculo.cod_lancamento = $inCodLancamento ) \n";
    $stSql .= "         )AS taxa_luz, \n";

    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 aavp.valor_padrao \n";

    $stSql .= "             FROM \n";
    $stSql .= "                 imobiliario.atributo_imovel_valor AS iaiv \n";

    $stSql .= "             INNER JOIN \n";
    $stSql .= "                 administracao.atributo_valor_padrao AS aavp \n";
    $stSql .= "             ON \n";
    $stSql .= "                 aavp.cod_valor = iaiv.valor::integer \n";
    $stSql .= "                 AND aavp.cod_cadastro = 4 \n";
    $stSql .= "                 AND aavp.cod_modulo = 12 \n";
    $stSql .= "                 AND aavp.cod_atributo = 106 \n";

    $stSql .= "             WHERE \n";
    $stSql .= "                 iaiv.inscricao_municipal = aivv.inscricao_municipal \n";
    $stSql .= "                 AND iaiv.cod_atributo = 106 \n";
    $stSql .= "                 AND iaiv.cod_cadastro = 4 \n";
    $stSql .= "                 AND iaiv.cod_modulo = 12 \n";
    $stSql .= "                 AND iaiv.timestamp < aivv.timestamp \n";

    $stSql .= "             ORDER BY \n";
    $stSql .= "                 iaiv.timestamp DESC \n";

    $stSql .= "             LIMIT 1 \n";
    $stSql .= "         )AS zona, \n";

    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 aavp.valor_padrao \n";

    $stSql .= "             FROM \n";
    $stSql .= "                 imobiliario.atributo_imovel_valor AS iaiv \n";

    $stSql .= "             INNER JOIN \n";
    $stSql .= "                 administracao.atributo_valor_padrao AS aavp \n";
    $stSql .= "             ON \n";
    $stSql .= "                 aavp.cod_valor = iaiv.valor::integer \n";
    $stSql .= "                 AND aavp.cod_cadastro = 4 \n";
    $stSql .= "                 AND aavp.cod_modulo = 12 \n";
    $stSql .= "                 AND aavp.cod_atributo = 8 \n";

    $stSql .= "             WHERE \n";
    $stSql .= "                 iaiv.inscricao_municipal = aivv.inscricao_municipal \n";
    $stSql .= "                 AND iaiv.cod_atributo = 8 \n";
    $stSql .= "                 AND iaiv.cod_cadastro = 4 \n";
    $stSql .= "                 AND iaiv.cod_modulo = 12 \n";
    $stSql .= "                 AND iaiv.timestamp < aivv.timestamp \n";

    $stSql .= "             ORDER BY \n";
    $stSql .= "                 iaiv.timestamp DESC \n";

    $stSql .= "             LIMIT 1 \n";
    $stSql .= "         )AS categoria_utilizacao_imovel, \n";

    $stSql .= "             CASE WHEN iua.cod_tipo IS NOT NULL AND iua.cod_construcao IS NOT NULL THEN \n";
    $stSql .= "                  ( \n";

    $stSql .= "                      SELECT \n";
    $stSql .= "                         atcv.valor \n";
    $stSql .= "                      FROM \n";
    $stSql .= "                         imobiliario.atributo_tipo_edificacao_valor AS iatev \n";

    $stSql .= "                      INNER JOIN \n";
    $stSql .= "                         arrecadacao.tabela_conversao_valores AS atcv \n";
    $stSql .= "                      ON \n";
    $stSql .= "                         atcv.cod_tabela = 30 \n";
    $stSql .= "                         AND atcv.parametro_1 = iatev.valor \n";
    $stSql .= "                         AND atcv.exercicio = aivv.exercicio \n";
    $stSql .= "                      WHERE \n";
    $stSql .= "                         iatev.cod_atributo = 3 and \n";
    $stSql .= "                         iatev.cod_tipo = iua.cod_tipo \n";
    $stSql .= "                         AND iatev.cod_construcao = iua.cod_construcao \n";
    $stSql .= "                         AND iatev.timestamp < aivv.timestamp \n";
    $stSql .= "                      ORDER BY \n";
    $stSql .= "                         iatev.timestamp DESC \n";
    $stSql .= "                      LIMIT 1 \n";
    $stSql .= "                    )::numeric(14,6) \n";
    $stSql .= "               ELSE \n";
    $stSql .= "                 0.00 \n";
    $stSql .= "               END AS vupc,\n";

    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                tmp_il.nom_localizacao \n";
    $stSql .= "             FROM \n";
    $stSql .= "                imobiliario.localizacao AS tmp_il \n";

    $stSql .= "             INNER JOIN \n";
    $stSql .= "                imobiliario.localizacao_nivel AS tmp_iln \n";
    $stSql .= "             ON  \n";
    $stSql .= "                tmp_il.codigo_composto = tmp_iln.valor || '.00' \n";
    $stSql .= "                AND tmp_iln.cod_localizacao = il.cod_localizacao \n";
    $stSql .= "                AND tmp_iln.cod_nivel = 1 \n";
    $stSql .= "         ) AS regiao \n";

    $stSql .= "     FROM \n";
    $stSql .= "         arrecadacao.imovel_v_venal AS aivv \n";

    $stSql .= "     LEFT JOIN \n";
    $stSql .= "         imobiliario.unidade_autonoma AS iua \n";
    $stSql .= "     ON \n";
    $stSql .= "         iua.inscricao_municipal = aivv.inscricao_municipal \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         imobiliario.imovel AS ii \n";
    $stSql .= "     ON \n";
    $stSql .= "         ii.inscricao_municipal = aivv.inscricao_municipal \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         imobiliario.imovel_confrontacao AS iic \n";
    $stSql .= "     ON \n";
    $stSql .= "         iic.inscricao_municipal = ii.inscricao_municipal \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         imobiliario.lote_localizacao AS ill \n";
    $stSql .= "     ON \n";
    $stSql .= "         ill.cod_lote = iic.cod_lote \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         imobiliario.localizacao AS il \n";
    $stSql .= "     ON \n";
    $stSql .= "         il.cod_localizacao = ill.cod_localizacao \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         imobiliario.confrontacao_trecho AS ict \n";
    $stSql .= "     ON \n";
    $stSql .= "         ict.cod_confrontacao = iic.cod_confrontacao \n";
    $stSql .= "         AND ict.cod_lote = iic.cod_lote \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 ial.* \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 imobiliario.area_lote AS ial, \n";
    $stSql .= "                 ( \n";
    $stSql .= "                     SELECT \n";
    $stSql .= "                         MAX( timestamp ) AS timestamp, \n";
    $stSql .= "                         cod_lote \n";
    $stSql .= "                     FROM \n";
    $stSql .= "                         imobiliario.area_lote \n";
    $stSql .= "                     GROUP BY \n";
    $stSql .= "                         cod_lote \n";
    $stSql .= "                 )AS tmp \n";
    $stSql .= "             WHERE \n";
    $stSql .= "                 tmp.cod_lote = ial.cod_lote \n";
    $stSql .= "                 AND tmp.timestamp = ial.timestamp \n";
    $stSql .= "         )AS ial \n";
    $stSql .= "     ON \n";
    $stSql .= "         ial.cod_lote = iic.cod_lote \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         imobiliario.trecho it \n";
    $stSql .= "     ON \n";
    $stSql .= "         it.cod_trecho = ict.cod_trecho \n";
    $stSql .= "         AND it.cod_logradouro = ict.cod_logradouro \n";

    $stSql .= "     INNER JOIN
                        (
                            SELECT
                                tmp.*

                            FROM
                                imobiliario.trecho_valor_m2 AS tmp

                            INNER JOIN
                                (
                                    SELECT
                                        MAX( trecho_valor_m2.timestamp ) AS timestamp,
                                        trecho_valor_m2.cod_logradouro,
                                        trecho_valor_m2.cod_trecho

                                    FROM
                                        imobiliario.trecho_valor_m2

                                    GROUP BY
                                        trecho_valor_m2.cod_logradouro,
                                        trecho_valor_m2.cod_trecho
                                )AS tmp2
                            ON
                                tmp2.cod_logradouro = tmp.cod_logradouro
                                AND tmp2.cod_trecho = tmp.cod_trecho
                                AND tmp2.timestamp = tmp.timestamp
                        )AS itvm                                   ";
    $stSql .= "     ON \n";
    $stSql .= "         itvm.cod_trecho = ict.cod_trecho \n";
    $stSql .= "         AND itvm.cod_logradouro = ict.cod_logradouro \n";
    $stSql .= "     INNER JOIN  \n";
    $stSql .= "          arrecadacao.imovel_calculo AS aic \n";
    $stSql .= "     ON  \n";
    $stSql .= "         aic.timestamp = aivv.timestamp \n";
    $stSql .= "         AND aic.inscricao_municipal = aivv.inscricao_municipal \n";
    $stSql .= "         AND aic.cod_calculo = (SELECT cod_calculo FROM arrecadacao.lancamento_calculo WHERE arrecadacao.lancamento_calculo.cod_lancamento= $inCodLancamento LIMIT 1) \n";

    $stSql .= $stFiltro;

    $stSql .= " )AS tmp \n";

    return $stSql;
}

function recuperaDadosIPTUComplementarMata(&$rsRecordSet, $stFiltro = "", $inCodParcela = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosIPTUComplementarMata( $stFiltro, $inCodParcela );
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaDadosIPTUComplementarMata($stFiltro = "", $inCodParcela)
{
    $stSql  = " SELECT \n";
    $stSql .= "     tmp.numero_quadra, \n";
    $stSql .= "     tmp.exercicio, \n";
    $stSql .= "     tmp.nom_localizacao AS distrito, \n";
    $stSql .= "     tmp.regiao, \n";
    $stSql .= "     tmp.zona, \n";
    $stSql .= "    (
                    split_part( tmp.valor_m2_limpeza, '§', 2 )
                   )AS valor_m2_limpeza_publica, \n";
    $stSql .= "     (
                        split_part( tmp.valor_m2_limpeza, '§', 1 )
                    )AS area_m2_limpeza_publica, \n";
    $stSql .= "     economico.fn_busca_aliquota_imposto( tmp.inscricao_municipal, tmp.exercicio::integer ) AS aliquota, \n";

    $stSql .= "     tmp.nro_parcela, \n";
    $stSql .= "     tmp.vencimento_parcela, \n";
    $stSql .= "     tmp.valor_parcela, \n";
    $stSql .= "     tmp.area_lote, \n";
    $stSql .= "     tmp.area_imovel, \n";
    $stSql .= "     tmp.cod_calculo, \n";
    $stSql .= "     tmp.vupt, \n";
    $stSql .= "     tmp.numero_lote, \n";

    $stSql .= "     CASE WHEN tmp.venal_territorial_calculado IS NOT NULL THEN
                        tmp.venal_territorial_calculado
                    ELSE
                        (
                        SELECT
                            COALESCE( venal_territorial_informado, venal_territorial_calculado)
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = tmp.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = tmp.exercicio
                            AND (venal_territorial_informado IS NOT NULL OR venal_territorial_calculado IS NOT NULL )
                        order by timestamp desc limit 1
                        )
                    END AS venal_territorial_calculado, \n";

    $stSql .= "     CASE WHEN tmp.venal_predial_calculado IS NOT NULL THEN
                        tmp.venal_predial_calculado
                    ELSE
                        (
                        SELECT
                            COALESCE( venal_predial_informado, venal_predial_calculado)
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = tmp.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = tmp.exercicio
                            AND (venal_predial_informado IS NOT NULL OR venal_predial_calculado IS NOT NULL )
                        order by timestamp desc limit 1
                        )
                    END AS venal_predial_calculado, \n";

    $stSql .= "     CASE WHEN tmp.venal_total_calculado IS NOT NULL THEN
                        tmp.venal_total_calculado
                    ELSE
                        (
                        SELECT
                            COALESCE( venal_total_informado, venal_total_calculado)
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = tmp.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = tmp.exercicio
                            AND (venal_total_informado IS NOT NULL OR venal_total_calculado IS NOT NULL )
                        order by timestamp desc limit 1
                        )
                    END AS venal_total_calculado, \n";

    $stSql .= "     tmp.inscricao_municipal, \n";

    $stSql .= "     (
                        SELECT
                            iic.cod_condominio || ic.nom_condominio

                        FROM
                            imobiliario.imovel_condominio AS iic

                        INNER JOIN
                            imobiliario.condominio AS ic
                        ON
                            ic.cod_condominio = iic.cod_condominio

                        WHERE
                            iic.inscricao_municipal = tmp.inscricao_municipal
                    )AS condominio, \n";
    $stSql .= "     tmp.cod_logradouro, \n";
    $stSql .= "     tmp.endereco_logradouro, \n";
    $stSql .= "     tmp.endereco_numero, \n";
    $stSql .= "     tmp.endereco_complemento, \n";
    $stSql .= "     tmp.cep, \n";
    $stSql .= "     tmp.nom_proprietario, \n";
    $stSql .= "     tmp.vupc, \n";
    $stSql .= "     tmp.numcgm_proprietario, \n";
    $stSql .= "     tmp.categoria_utilizacao_imovel, \n";
    $stSql .= "     COALESCE( tmp.taxa_limpeza_publica, 0.00) AS taxa_limpeza_publica, \n";
    $stSql .= "     COALESCE( tmp.imposto_territorial, 0.00) AS imposto_territorial, \n";
    $stSql .= "     COALESCE( tmp.imposto_predial, 0.00) AS imposto_predial, \n";
    $stSql .= "     COALESCE( tmp.imposto_territorial, 0.00) + COALESCE( tmp.imposto_predial, 0.00) AS  valor_imposto, \n";
    $stSql .= "     COALESCE( tmp.imposto_territorial, 0.00) + COALESCE( tmp.imposto_predial, 0.00) + COALESCE( tmp.taxa_limpeza_publica, 0.00) + COALESCE( tmp.taxa_luz, 0.00) AS valor_total_tributos, \n";
    $stSql .= "     COALESCE( tmp.taxa_luz, 0.00) AS taxa_luz \n";
    $stSql .= " FROM \n";
    $stSql .= "     ( \n";
    $stSql .= "     SELECT \n";
    $stSql .= "         economico.fn_busca_dados_taxa_limpeza( aivv.inscricao_municipal, aivv.exercicio::integer ) AS valor_m2_limpeza, \n";
    $stSql .= "         aivv.exercicio, \n";
    $stSql .= "         il.nom_localizacao, \n";
    $stSql .= "         CASE WHEN ap.nr_parcela = 0 THEN \n";
    $stSql .= "             'única'::text \n";
    $stSql .= "         ELSE \n";
    $stSql .= "             ap.nr_parcela::text \n";
    $stSql .= "         END AS nro_parcela, \n";
    $stSql .= "         to_char(ap.vencimento, 'dd/mm/yyyy' ) AS vencimento_parcela, \n";
    $stSql .= "         COALESCE( apd.valor, ap.valor ) AS valor_parcela, \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 COALESCE( ac.valor, 0.00 ) \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 arrecadacao.calculo AS ac \n";
    $stSql .= "             WHERE \n";
    $stSql .= "                 ac.cod_credito = 14 \n";
    $stSql .= "                 AND ac.cod_natureza = 1 \n";
    $stSql .= "                 AND ac.cod_genero = 2 \n";
    $stSql .= "                 AND ac.cod_especie = 1 \n";
    $stSql .= "                 AND ac.cod_calculo in (SELECT cod_calculo FROM arrecadacao.lancamento_calculo WHERE arrecadacao.lancamento_calculo.cod_lancamento = (SELECT cod_lancamento from arrecadacao.parcela where cod_parcela = ".$inCodParcela.") ) \n";
    $stSql .= "         )::numeric(14,6) AS taxa_limpeza_publica, \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 COALESCE( ac.valor, 0.00 ) \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 arrecadacao.calculo AS ac \n";
    $stSql .= "             WHERE \n";
    $stSql .= "                 ac.cod_credito = 2 \n";
    $stSql .= "                 AND ac.cod_natureza = 1 \n";
    $stSql .= "                 AND ac.cod_genero = 1 \n";
    $stSql .= "                 AND ac.cod_especie = 1 \n";
    $stSql .= "                 AND ac.cod_calculo in (
                                                        SELECT
                                                            cod_calculo
                                                        FROM
                                                            arrecadacao.lancamento_calculo
                                                        WHERE arrecadacao.lancamento_calculo.cod_lancamento = (SELECT cod_lancamento from arrecadacao.parcela where cod_parcela = ".$inCodParcela.")) \n";
    $stSql .= "         )::numeric(14,6) AS imposto_territorial, \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 COALESCE( ac.valor, 0.00 ) \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 arrecadacao.calculo AS ac \n";
    $stSql .= "             WHERE \n";
    $stSql .= "                 ac.cod_credito = 3 \n";
    $stSql .= "                 AND ac.cod_natureza = 1 \n";
    $stSql .= "                 AND ac.cod_genero = 1 \n";
    $stSql .= "                 AND ac.cod_especie = 1 \n";
    $stSql .= "                 AND ac.cod_calculo in (SELECT cod_calculo FROM arrecadacao.lancamento_calculo WHERE arrecadacao.lancamento_calculo.cod_lancamento = (SELECT cod_lancamento from arrecadacao.parcela where cod_parcela = ".$inCodParcela.") ) \n";
    $stSql .= "         )::numeric(14,6) AS imposto_predial, \n";
    $stSql .= "         imobiliario.fn_area_real( aivv.inscricao_municipal ) AS area_lote, \n";
    $stSql .= "         imobiliario.fn_calcula_area_imovel( aivv.inscricao_municipal ) AS area_imovel, \n";
    $stSql .= "         aic.cod_calculo, \n";
    $stSql .= "         aivv.timestamp, \n";
    $stSql .= "         itvm.valor_m2_territorial AS vupt, \n";

    $stSql .= "         (
                          SELECT
                              COALESCE(
                                   (
                                       SELECT valor from
                                            imobiliario.atributo_lote_urbano_valor as ialu
                                       WHERE ialu.cod_atributo = 7
                                       AND  ialu.cod_lote = il.cod_lote
                                       ORDER BY ialu.timestamp DESC limit 1
                                   ),
                                   (
                                       SELECT valor from
                                            imobiliario.atributo_lote_rural_valor as ialr
                                       WHERE ialr.cod_atributo = 7
                                       AND  ialr.cod_lote = il.cod_lote
                                       ORDER BY ialr.timestamp DESC limit 1
                                   )
                               )::varchar AS valor
                           FROM
                               imobiliario.lote as il

                           WHERE il.cod_lote = iic.cod_lote
                         ) AS numero_lote, \n";

    $stSql .= "         (
                           SELECT
                              COALESCE(
                                   (
                                       SELECT valor from
                                            imobiliario.atributo_lote_urbano_valor as ialu
                                       WHERE ialu.cod_atributo = 5
                                       AND  ialu.cod_lote = il.cod_lote
                                       ORDER BY ialu.timestamp DESC limit 1
                                   ),
                                   (
                                       SELECT valor from
                                            imobiliario.atributo_lote_rural_valor as ialr
                                       WHERE ialr.cod_atributo = 5
                                       AND  ialr.cod_lote = il.cod_lote
                                       ORDER BY ialr.timestamp DESC limit 1
                                   )
                               )::varchar AS valor
                           FROM
                               imobiliario.lote as il

                           WHERE il.cod_lote = iic.cod_lote
                         ) AS numero_quadra, \n";

    $stSql .= "        (
                        SELECT
                            venal_territorial_calculado
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = aivv.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = aivv.exercicio
                            AND arrecadacao.imovel_v_venal.timestamp = aic.timestamp
                        order by arrecadacao.imovel_v_venal.timestamp limit 1
                       )AS venal_territorial_calculado, \n";

    $stSql .= "        (
                        SELECT
                            venal_predial_calculado
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = aivv.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = aivv.exercicio
                            AND arrecadacao.imovel_v_venal.timestamp = aic.timestamp
                       )AS venal_predial_calculado, \n";

    $stSql .= "        (
                        SELECT
                            venal_total_calculado
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = aivv.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = aivv.exercicio
                            AND arrecadacao.imovel_v_venal.timestamp = aic.timestamp
                       )AS venal_total_calculado, \n";

    $stSql .= "         aivv.inscricao_municipal, \n";
    $stSql .= "         it.cod_logradouro, \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 coalesce(tl.nom_tipo, '' ) ||' '|| coalesce(nl.nom_logradouro, '' ) \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 sw_nome_logradouro nl, \n";
    $stSql .= "                 sw_tipo_logradouro tl \n";
    $stSql .= "             WHERE \n";
    $stSql .= "                 nl.cod_logradouro = it.cod_logradouro \n";
    $stSql .= "                 AND tl.cod_tipo = nl.cod_tipo \n";
    $stSql .= "             ORDER BY nl.timestamp desc limit 1 \n";
    $stSql .= "         )AS endereco_logradouro, \n";
    $stSql .= "         ii.numero AS endereco_numero, \n";
    $stSql .= "         ii.complemento AS endereco_complemento, \n";
    $stSql .= "         ii.cep, \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 cgm.nom_cgm \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 imobiliario.proprietario AS ip \n";

    $stSql .= "             INNER JOIN \n";
    $stSql .= "                 sw_cgm AS cgm \n";
    $stSql .= "             ON \n";
    $stSql .= "                 cgm.numcgm = ip.numcgm \n";

    $stSql .= "             WHERE \n";
    $stSql .= "                 ip.inscricao_municipal = aivv.inscricao_municipal \n";

    $stSql .= "             ORDER BY \n";
    $stSql .= "                 ip.cota DESC \n";
    $stSql .= "             LIMIT 1 \n";
    $stSql .= "         )AS nom_proprietario, \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 ip.numcgm \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 imobiliario.proprietario AS ip \n";
    $stSql .= "             WHERE \n";
    $stSql .= "                 ip.inscricao_municipal = aivv.inscricao_municipal \n";
    $stSql .= "             ORDER BY   \n";
    $stSql .= "                 ip.cota DESC \n";
    $stSql .= "             LIMIT 1 \n";
    $stSql .= "         )AS numcgm_proprietario, \n";

    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 COALESCE( ac.valor, 0.00 ) \n";
    $stSql .= "             FROM  \n";
    $stSql .= "                 arrecadacao.calculo AS ac \n";
    $stSql .= "             WHERE  \n";
    $stSql .= "                 ac.cod_credito = 13 \n";
    $stSql .= "                 AND ac.cod_natureza = 1  \n";
    $stSql .= "                 AND ac.cod_genero = 2  \n";
    $stSql .= "                 AND ac.cod_especie = 1  \n";
    $stSql .= "                 AND ac.cod_calculo in (SELECT cod_calculo FROM arrecadacao.lancamento_calculo WHERE arrecadacao.lancamento_calculo.cod_lancamento = (SELECT cod_lancamento from arrecadacao.parcela where cod_parcela = ".$inCodParcela.") ) \n";
    $stSql .= "         )AS taxa_luz, \n";

    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 aavp.valor_padrao \n";

    $stSql .= "             FROM \n";
    $stSql .= "                 imobiliario.atributo_imovel_valor AS iaiv \n";

    $stSql .= "             INNER JOIN \n";
    $stSql .= "                 administracao.atributo_valor_padrao AS aavp \n";
    $stSql .= "             ON \n";
    $stSql .= "                 aavp.cod_valor = iaiv.valor::integer \n";
    $stSql .= "                 AND aavp.cod_cadastro = 4 \n";
    $stSql .= "                 AND aavp.cod_modulo = 12 \n";
    $stSql .= "                 AND aavp.cod_atributo = 106 \n";

    $stSql .= "             WHERE \n";
    $stSql .= "                 iaiv.inscricao_municipal = aivv.inscricao_municipal \n";
    $stSql .= "                 AND iaiv.cod_atributo = 106 \n";
    $stSql .= "                 AND iaiv.cod_cadastro = 4 \n";
    $stSql .= "                 AND iaiv.cod_modulo = 12 \n";
    $stSql .= "                 AND iaiv.timestamp < aivv.timestamp \n";

    $stSql .= "             ORDER BY \n";
    $stSql .= "                 iaiv.timestamp DESC \n";

    $stSql .= "             LIMIT 1 \n";
    $stSql .= "         )AS zona, \n";

    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 aavp.valor_padrao \n";

    $stSql .= "             FROM \n";
    $stSql .= "                 imobiliario.atributo_imovel_valor AS iaiv \n";

    $stSql .= "             INNER JOIN \n";
    $stSql .= "                 administracao.atributo_valor_padrao AS aavp \n";
    $stSql .= "             ON \n";
    $stSql .= "                 aavp.cod_valor = iaiv.valor::integer \n";
    $stSql .= "                 AND aavp.cod_cadastro = 4 \n";
    $stSql .= "                 AND aavp.cod_modulo = 12 \n";
    $stSql .= "                 AND aavp.cod_atributo = 8 \n";

    $stSql .= "             WHERE \n";
    $stSql .= "                 iaiv.inscricao_municipal = aivv.inscricao_municipal \n";
    $stSql .= "                 AND iaiv.cod_atributo = 8 \n";
    $stSql .= "                 AND iaiv.cod_cadastro = 4 \n";
    $stSql .= "                 AND iaiv.cod_modulo = 12 \n";
    $stSql .= "                 AND iaiv.timestamp < aivv.timestamp \n";

    $stSql .= "             ORDER BY \n";
    $stSql .= "                 iaiv.timestamp DESC \n";

    $stSql .= "             LIMIT 1 \n";
    $stSql .= "         )AS categoria_utilizacao_imovel, \n";

    $stSql .= "             CASE WHEN iua.cod_tipo IS NOT NULL AND iua.cod_construcao IS NOT NULL THEN \n";
    $stSql .= "                  ( \n";

    $stSql .= "                      SELECT \n";
    $stSql .= "                         atcv.valor \n";
    $stSql .= "                      FROM \n";
    $stSql .= "                         imobiliario.atributo_tipo_edificacao_valor AS iatev \n";

    $stSql .= "                      INNER JOIN \n";
    $stSql .= "                         arrecadacao.tabela_conversao_valores AS atcv \n";
    $stSql .= "                      ON \n";
    $stSql .= "                         atcv.cod_tabela = 30 \n";
    $stSql .= "                         AND atcv.parametro_1 = iatev.valor \n";
    $stSql .= "                         AND atcv.exercicio = aivv.exercicio \n";
    $stSql .= "                      WHERE \n";
    $stSql .= "                         iatev.cod_atributo = 3 and \n";
    $stSql .= "                         iatev.cod_tipo = iua.cod_tipo \n";
    $stSql .= "                         AND iatev.cod_construcao = iua.cod_construcao \n";
    $stSql .= "                         AND iatev.timestamp < aivv.timestamp \n";
    $stSql .= "                      ORDER BY \n";
    $stSql .= "                         iatev.timestamp DESC \n";
    $stSql .= "                      LIMIT 1 \n";
    $stSql .= "                    )::numeric(14,6) \n";
    $stSql .= "               ELSE \n";
    $stSql .= "                 0.00 \n";
    $stSql .= "               END AS vupc,\n";

    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                tmp_il.nom_localizacao \n";
    $stSql .= "             FROM \n";
    $stSql .= "                imobiliario.localizacao AS tmp_il \n";

    $stSql .= "             INNER JOIN \n";
    $stSql .= "                imobiliario.localizacao_nivel AS tmp_iln \n";
    $stSql .= "             ON  \n";
    $stSql .= "                tmp_il.codigo_composto = tmp_iln.valor || '.00' \n";
    $stSql .= "                AND tmp_iln.cod_localizacao = il.cod_localizacao \n";
    $stSql .= "                AND tmp_iln.cod_nivel = 1 \n";
    $stSql .= "         ) AS regiao \n";

    $stSql .= "     FROM \n";
    $stSql .= "         arrecadacao.imovel_v_venal AS aivv \n";

    $stSql .= "     LEFT JOIN \n";
    $stSql .= "         imobiliario.unidade_autonoma AS iua \n";
    $stSql .= "     ON \n";
    $stSql .= "         iua.inscricao_municipal = aivv.inscricao_municipal \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         imobiliario.imovel AS ii \n";
    $stSql .= "     ON \n";
    $stSql .= "         ii.inscricao_municipal = aivv.inscricao_municipal \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         imobiliario.imovel_confrontacao AS iic \n";
    $stSql .= "     ON \n";
    $stSql .= "         iic.inscricao_municipal = ii.inscricao_municipal \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         imobiliario.lote_localizacao AS ill \n";
    $stSql .= "     ON \n";
    $stSql .= "         ill.cod_lote = iic.cod_lote \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         imobiliario.localizacao AS il \n";
    $stSql .= "     ON \n";
    $stSql .= "         il.cod_localizacao = ill.cod_localizacao \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         imobiliario.confrontacao_trecho AS ict \n";
    $stSql .= "     ON \n";
    $stSql .= "         ict.cod_confrontacao = iic.cod_confrontacao \n";
    $stSql .= "         AND ict.cod_lote = iic.cod_lote \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 ial.* \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 imobiliario.area_lote AS ial, \n";
    $stSql .= "                 ( \n";
    $stSql .= "                     SELECT \n";
    $stSql .= "                         MAX( timestamp ) AS timestamp, \n";
    $stSql .= "                         cod_lote \n";
    $stSql .= "                     FROM \n";
    $stSql .= "                         imobiliario.area_lote \n";
    $stSql .= "                     GROUP BY \n";
    $stSql .= "                         cod_lote \n";
    $stSql .= "                 )AS tmp \n";
    $stSql .= "             WHERE \n";
    $stSql .= "                 tmp.cod_lote = ial.cod_lote \n";
    $stSql .= "                 AND tmp.timestamp = ial.timestamp \n";
    $stSql .= "         )AS ial \n";
    $stSql .= "     ON \n";
    $stSql .= "         ial.cod_lote = iic.cod_lote \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         imobiliario.trecho it \n";
    $stSql .= "     ON \n";
    $stSql .= "         it.cod_trecho = ict.cod_trecho \n";
    $stSql .= "         AND it.cod_logradouro = ict.cod_logradouro \n";

    $stSql .= "     INNER JOIN \n";
    $stSql .= "         imobiliario.trecho_valor_m2 AS itvm \n";
    $stSql .= "     ON \n";
    $stSql .= "         itvm.cod_trecho = ict.cod_trecho \n";
    $stSql .= "         AND itvm.cod_logradouro = ict.cod_logradouro \n";
    $stSql .= "     INNER JOIN  \n";
    $stSql .= "          arrecadacao.imovel_calculo AS aic \n";
    $stSql .= "     ON  \n";
    $stSql .= "         aic.timestamp = aivv.timestamp \n";
    $stSql .= "         AND aic.inscricao_municipal = aivv.inscricao_municipal \n";
    $stSql .= "         AND aic.cod_calculo = (SELECT cod_calculo FROM arrecadacao.lancamento_calculo WHERE arrecadacao.lancamento_calculo.cod_lancamento = (SELECT cod_lancamento from arrecadacao.parcela where cod_parcela = ".$inCodParcela.") limit 1) \n";

    $stSql .= "     INNER JOIN  \n";
    $stSql .= "         arrecadacao.parcela AS ap \n";
    $stSql .= "     ON  \n";
    $stSql .= "        ap.cod_lancamento = (SELECT cod_lancamento from arrecadacao.parcela where cod_parcela = ".$inCodParcela.")

                    LEFT JOIN
                        arrecadacao.parcela_desconto AS apd
                    ON
                        apd.cod_parcela = ap.cod_parcela \n";

    $stSql .= $stFiltro;

    $stSql .= " )AS tmp \n";

    return $stSql;
}

function buscaCabecalhoCarneGrafica(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montabuscaCabecalhoCarneGrafica().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montabuscaCabecalhoCarneGrafica()
{
    $stSql  = " SELECT                                                                                  \n";
    $stSql .= "     prefeitura.valor as prefeitura,                                                     \n";
    $stSql .= "     email.valor as email,                                                               \n";
    $stSql .= "     fone.valor as fone,                                                                 \n";
    $stSql .= "     fax.valor as fax,                                                                   \n";
    $stSql .= "     cnpj.valor as cnpj,                                                                 \n";
    $stSql .= "     febraban.valor as cod_febraban,                                                     \n";
    $stSql .= "     tipo_logradouro.valor as tipo_logradouro,                                           \n";
    $stSql .= "     logradouro.valor as logradouro,                                                     \n";
    $stSql .= "     numero.valor as numero,                                                             \n";
    $stSql .= "     complemento.valor as complemento,                                                   \n";
    $stSql .= "     bairro.valor as bairro,                                                             \n";
    $stSql .= "     mun.cod_municipio,                                                                  \n";
    $stSql .= "     mun.nom_municipio,                                                                  \n";
    $stSql .= "     uf.cod_uf,                                                                          \n";
    $stSql .= "     uf.sigla_uf,                                                                        \n";
    $stSql .= "     cep.valor as cep                                                                    \n";
    $stSql .= " FROM                                                                                    \n";
    $stSql .= "     (SELECT valor, exercicio FROM administracao.configuracao as conf                    \n";
    $stSql .= "         WHERE conf.cod_modulo = 2  and conf.parametro = 'cod_uf'                        \n";
    $stSql .= "     ) as conf                                                                           \n";
    $stSql .= "     INNER JOIN sw_uf as uf                                                              \n";
    $stSql .= "     ON uf.cod_uf = conf.valor::integer                                                  \n";
    $stSql .= "     INNER JOIN (                                                                        \n";
    $stSql .= "         SELECT  valor, parametro, exercicio,                                            \n";
    $stSql .= "                 mun.cod_municipio, mun.nom_municipio, mun.cod_uf                        \n";
    $stSql .= "         FROM administracao.configuracao as conf                                         \n";
    $stSql .= "         INNER JOIN sw_municipio as mun                                                  \n";
    $stSql .= "         ON conf.valor::integer = mun.cod_municipio                                      \n";
    $stSql .= "         WHERE conf.cod_modulo = 2  and conf.parametro = 'cod_municipio'                 \n";
    $stSql .= "     ) as mun                                                                            \n";
    $stSql .= "     ON mun.exercicio = conf.exercicio AND mun.cod_uf = uf.cod_uf                        \n";

    $stSql .= "     INNER JOIN (                                                                        \n";
    $stSql .= "         SELECT valor, parametro, exercicio                                              \n";
    $stSql .= "         FROM administracao.configuracao as conf                                         \n";
    $stSql .= "         WHERE conf.cod_modulo = 2 and conf.parametro = 'tipo_logradouro'                \n";
    $stSql .= "     ) as tipo_logradouro                                                                \n";
    $stSql .= "     ON tipo_logradouro.exercicio = conf.exercicio                                       \n";

    $stSql .= "     INNER JOIN (                                                                        \n";
    $stSql .= "         SELECT valor, parametro, exercicio                                              \n";
    $stSql .= "         FROM administracao.configuracao as conf                                         \n";
    $stSql .= "         WHERE conf.cod_modulo = 2 and conf.parametro = 'logradouro'                     \n";
    $stSql .= "     ) as logradouro                                                                     \n";
    $stSql .= "     ON logradouro.exercicio = conf.exercicio                                            \n";

    $stSql .= "     INNER JOIN (                                                                        \n";
    $stSql .= "         SELECT valor, parametro, exercicio                                              \n";
    $stSql .= "         FROM administracao.configuracao as conf                                         \n";
    $stSql .= "         WHERE conf.cod_modulo = 2 and conf.parametro = 'numero'                         \n";
    $stSql .= "     ) as numero                                                                         \n";
    $stSql .= "     ON numero.exercicio = conf.exercicio                                                \n";

    $stSql .= "     INNER JOIN (                                                                        \n";
    $stSql .= "         SELECT valor, parametro, exercicio                                              \n";
    $stSql .= "         FROM administracao.configuracao as conf                                         \n";
    $stSql .= "         WHERE conf.cod_modulo = 2 and conf.parametro = 'complemento'                    \n";
    $stSql .= "     ) as complemento                                                                    \n";
    $stSql .= "     ON complemento.exercicio = conf.exercicio                                           \n";

    $stSql .= "     INNER JOIN (                                                                        \n";
    $stSql .= "         SELECT valor, parametro, exercicio                                              \n";
    $stSql .= "         FROM administracao.configuracao as conf                                         \n";
    $stSql .= "         WHERE conf.cod_modulo = 2 and conf.parametro = 'bairro'                         \n";
    $stSql .= "     ) as bairro                                                                         \n";
    $stSql .= "     ON bairro.exercicio = conf.exercicio                                                \n";

    $stSql .= "     INNER JOIN (                                                                        \n";
    $stSql .= "         SELECT valor, parametro, exercicio                                              \n";
    $stSql .= "         FROM administracao.configuracao as conf                                         \n";
    $stSql .= "         WHERE conf.cod_modulo = 2 and conf.parametro = 'cep'                            \n";
    $stSql .= "     ) as cep                                                                            \n";
    $stSql .= "     ON cep.exercicio = conf.exercicio                                                   \n";

    $stSql .= "     INNER JOIN (                                                                        \n";
    $stSql .= "         SELECT valor, parametro, exercicio                                              \n";
    $stSql .= "         FROM administracao.configuracao as conf                                         \n";
    $stSql .= "         WHERE conf.cod_modulo = 2 and conf.parametro = 'cnpj'                           \n";
    $stSql .= "     ) as cnpj                                                                           \n";
    $stSql .= "     ON cnpj.exercicio = conf.exercicio                                                  \n";

    $stSql .= "     INNER JOIN (                                                                        \n";
    $stSql .= "         SELECT valor, parametro, exercicio                                              \n";
    $stSql .= "         FROM administracao.configuracao as conf                                         \n";
    $stSql .= "         WHERE conf.cod_modulo = 2 and conf.parametro = 'FEBRABAN'                       \n";
    $stSql .= "     ) as febraban                                                                       \n";
    $stSql .= "     ON febraban.exercicio = conf.exercicio                                              \n";

    $stSql .= "     INNER JOIN (                                                                        \n";
    $stSql .= "         SELECT valor, parametro, exercicio                                              \n";
    $stSql .= "         FROM administracao.configuracao as conf                                         \n";
    $stSql .= "         WHERE conf.cod_modulo = 2 and conf.parametro = 'e_mail'                         \n";
    $stSql .= "     ) as email                                                                          \n";
    $stSql .= "     ON email.exercicio = conf.exercicio                                                 \n";

    $stSql .= "     INNER JOIN (                                                                        \n";
    $stSql .= "         SELECT valor, parametro, exercicio                                              \n";
    $stSql .= "         FROM administracao.configuracao as conf                                         \n";
    $stSql .= "         WHERE conf.cod_modulo = 2 and conf.parametro = 'fax'                            \n";
    $stSql .= "     ) as fax                                                                            \n";
    $stSql .= "     ON fax.exercicio = conf.exercicio                                                   \n";

    $stSql .= "     INNER JOIN (                                                                        \n";
    $stSql .= "         SELECT valor, parametro, exercicio                                              \n";
    $stSql .= "         FROM administracao.configuracao as conf                                         \n";
    $stSql .= "         WHERE conf.cod_modulo = 2 and conf.parametro = 'fone'                           \n";
    $stSql .= "     ) as fone                                                                           \n";
    $stSql .= "     ON fone.exercicio = conf.exercicio                                                  \n";

    $stSql .= "     INNER JOIN (                                                                        \n";
    $stSql .= "         SELECT valor, parametro, exercicio                                              \n";
    $stSql .= "         FROM administracao.configuracao as conf                                         \n";
    $stSql .= "         WHERE conf.cod_modulo = 2 and conf.parametro = 'nom_prefeitura'                 \n";
    $stSql .= "     ) as prefeitura                                                                     \n";
    $stSql .= "     ON prefeitura.exercicio = conf.exercicio                                            \n";

    return $stSql;
}

function recuperaDadosTFFMata(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosTFFMata( $stFiltro );
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaDadosTFFMata($stFiltro = "")
{
    $stSql  = " SELECT \n";
    $stSql .= "     ac.exercicio, \n";
    $stSql .= "     ece.inscricao_economica, \n";
    $stSql .= "     edf.inscricao_municipal, \n";
    $stSql .= "     ( \n";
    $stSql .= "         SELECT \n";
    $stSql .= "             ict.cod_logradouro \n";
    $stSql .= "         FROM \n";
    $stSql .= "             imobiliario.imovel_confrontacao AS iic \n";

    $stSql .= "         INNER JOIN \n";
    $stSql .= "             imobiliario.confrontacao_trecho AS ict \n";
    $stSql .= "         ON \n";
    $stSql .= "             ict.cod_confrontacao = iic.cod_confrontacao \n";
    $stSql .= "             AND ict.cod_lote = iic.cod_lote \n";

    $stSql .= "         WHERE \n";
    $stSql .= "             iic.inscricao_municipal = edf.inscricao_municipal \n";
    $stSql .= "     )AS cod_logradouro, \n";
    $stSql .= "     ( \n";
    $stSql .= "         SELECT \n";
    $stSql .= "             nom_cgm \n";
    $stSql .= "         FROM \n";
    $stSql .= "             sw_cgm \n";
    $stSql .= "         WHERE \n";
    $stSql .= "             sw_cgm.numcgm = COALESCE( eceep.numcgm, eceed.numcgm, ecea.numcgm ) \n";
    $stSql .= "     )AS razao_social, \n";
    $stSql .= "     ( \n";
    $stSql .= "         SELECT \n";
    $stSql .= "             nom_fantasia \n";
    $stSql .= "         FROM \n";
    $stSql .= "             sw_cgm_pessoa_juridica \n";
    $stSql .= "         WHERE \n";
    $stSql .= "             sw_cgm_pessoa_juridica.numcgm = COALESCE( eceep.numcgm, eceed.numcgm, ecea.numcgm ) \n";
    $stSql .= "     )AS nome_fantasia, \n";
    $stSql .= "     ( \n";
    $stSql .= "         SELECT \n";
    $stSql .= "             COALESCE( sw_cgm_pessoa_fisica.cpf, sw_cgm_pessoa_juridica.cnpj ) AS cpf \n";
    $stSql .= "         FROM \n";
    $stSql .= "             sw_cgm \n";

    $stSql .= "         LEFT JOIN \n";
    $stSql .= "             sw_cgm_pessoa_fisica \n";
    $stSql .= "         ON \n";
    $stSql .= "             sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm \n";

    $stSql .= "         LEFT JOIN \n";
    $stSql .= "             sw_cgm_pessoa_juridica \n";
    $stSql .= "         ON \n";
    $stSql .= "             sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm \n";

    $stSql .= "         WHERE \n";
    $stSql .= "             sw_cgm.numcgm = COALESCE( eceep.numcgm, eceed.numcgm, ecea.numcgm ) \n";
    $stSql .= "     )AS cpf_cnpj, \n";
    $stSql .= "     ( \n";
    $stSql .= "         SELECT \n";
    $stSql .= "             ( \n";
    $stSql .= "                 SELECT \n";
    $stSql .= "                     nom_cgm \n";
    $stSql .= "                 FROM \n";
    $stSql .= "                     sw_cgm \n";
    $stSql .= "                 WHERE \n";
    $stSql .= "                     sw_cgm.numcgm = economico.cadastro_econ_resp_tecnico.numcgm \n";
    $stSql .= "             )AS nome \n";
    $stSql .= "         FROM \n";
    $stSql .= "             economico.cadastro_econ_resp_tecnico \n";
    $stSql .= "         WHERE \n";
    $stSql .= "             cadastro_econ_resp_tecnico.inscricao_economica = ece.inscricao_economica \n";
    $stSql .= "             AND cadastro_econ_resp_tecnico.ativo = true \n";
    $stSql .= "         ORDER BY  \n";
    $stSql .= "             cadastro_econ_resp_tecnico.timestamp \n";
    $stSql .= "         LIMIT 1 \n";
    $stSql .= "     )AS resposavel, \n";
    $stSql .= "     ( \n";
    $stSql .= "         SELECT \n";
    $stSql .= "             nom_atividade \n";
    $stSql .= "         FROM \n";
    $stSql .= "             economico.atividade \n";
    $stSql .= "         WHERE \n";
    $stSql .= "             cod_atividade = eace.cod_atividade \n";
    $stSql .= "         ORDER BY \n";
    $stSql .= "             economico.atividade.timestamp \n";
    $stSql .= "         LIMIT 1 \n";
    $stSql .= "     )AS atividade, \n";
    $stSql .= "     CASE WHEN (edf.inscricao_municipal IS NOT NULL) AND (edi.inscricao_economica IS NOT NULL) THEN  \n";
    $stSql .= "         CASE WHEN (edf.timestamp > edi.timestamp) THEN  \n";
    $stSql .= "             economico.fn_busca_domicilio_fiscal( edf.inscricao_municipal )  \n";
    $stSql .= "         ELSE  \n";
    $stSql .= "             economico.fn_busca_domicilio_informado( edi.inscricao_economica )  \n";
    $stSql .= "         END  \n";
    $stSql .= "     ELSE  \n";
    $stSql .= "         CASE WHEN (edf.inscricao_municipal IS NOT NULL) THEN \n";
    $stSql .= "             economico.fn_busca_domicilio_fiscal( edf.inscricao_municipal ) \n";
    $stSql .= "         ELSE  \n";
    $stSql .= "             CASE WHEN (edi.inscricao_economica IS NOT NULL) THEN \n";
    $stSql .= "                 economico.fn_busca_domicilio_informado( edi.inscricao_economica ) \n";
    $stSql .= "             END  \n";
    $stSql .= "         END  \n";
    $stSql .= "     END as endereco, \n";
    $stSql .= "     ill.valor AS nro_lote, \n";
    $stSql .= "     ( \n";
    $stSql .= "         SELECT \n";
    $stSql .= "            tmp_il.nom_localizacao \n";
    $stSql .= "         FROM \n";
    $stSql .= "            imobiliario.localizacao AS tmp_il \n";

    $stSql .= "         INNER JOIN \n";
    $stSql .= "            imobiliario.localizacao_nivel AS tmp_iln \n";
    $stSql .= "         ON  \n";
    $stSql .= "            tmp_il.codigo_composto = tmp_iln.valor || '.00' \n";
    $stSql .= "            AND tmp_iln.cod_localizacao = ilo.cod_localizacao \n";
    $stSql .= "            AND tmp_iln.cod_nivel = 1 \n";
    $stSql .= "     ) AS regiao, \n";
    $stSql .= "     ilo.nom_localizacao AS distrito \n";
    $stSql .= " ,acec.cod_calculo \n";
    $stSql .= " ,to_char(ap.vencimento, 'dd/mm/yyyy' ) AS vencimento \n";
    $stSql .= " ,ap.valor \n";

    $stSql .= " ,ap.nr_parcela
                ,( SELECT
                     lancamento.observacao
                   FROM
                     arrecadacao.lancamento
                   WHERE
                     lancamento.cod_lancamento = ap.cod_lancamento
                 ) AS observacao \n";

    $stSql .= " FROM \n";
    $stSql .= "     economico.cadastro_economico AS ece \n";

    $stSql .= " LEFT JOIN \n";
    $stSql .= "     economico.cadastro_economico_empresa_fato AS eceep \n";
    $stSql .= " ON \n";
    $stSql .= "     eceep.inscricao_economica = ece.inscricao_economica \n";

    $stSql .= " LEFT JOIN \n";
    $stSql .= "     economico.cadastro_economico_empresa_direito AS eceed \n";
    $stSql .= " ON \n";
    $stSql .= "     eceed.inscricao_economica = ece.inscricao_economica \n";

    $stSql .= " LEFT JOIN \n";
    $stSql .= "     economico.cadastro_economico_autonomo AS ecea \n";
    $stSql .= " ON \n";
    $stSql .= "     ecea.inscricao_economica = ece.inscricao_economica \n";

    $stSql .= " LEFT JOIN ( \n";
    $stSql .= "     SELECT  \n";
    $stSql .= "         edf_tmp.inscricao_economica, \n";
    $stSql .= "         edf_tmp.inscricao_municipal,  \n";
    $stSql .= "         edf_tmp.timestamp  \n";
    $stSql .= "     FROM  \n";
    $stSql .= "         economico.domicilio_fiscal AS edf_tmp, \n";
    $stSql .= "         (  \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 MAX (timestamp) AS timestamp, \n";
    $stSql .= "                 inscricao_economica  \n";
    $stSql .= "             FROM  \n";
    $stSql .= "                 economico.domicilio_fiscal \n";
    $stSql .= "             GROUP BY  \n";
    $stSql .= "                 inscricao_economica \n";
    $stSql .= "         )AS tmp  \n";
    $stSql .= "     WHERE  \n";
    $stSql .= "         tmp.timestamp = edf_tmp.timestamp \n";
    $stSql .= "         AND tmp.inscricao_economica = edf_tmp.inscricao_economica \n";
    $stSql .= " )AS edf  \n";
    $stSql .= " ON  \n";
    $stSql .= "     ece.inscricao_economica = edf.inscricao_economica \n";

    $stSql .= " LEFT JOIN ( \n";
    $stSql .= "     SELECT \n";
    $stSql .= "         iil_tmp.cod_lote, \n";
    $stSql .= "         iil_tmp.inscricao_municipal \n";
    $stSql .= "     FROM \n";
    $stSql .= "         imobiliario.imovel_lote AS iil_tmp, \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 MAX(timestamp) AS timestamp, \n";
    $stSql .= "                 inscricao_municipal \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 imobiliario.imovel_lote \n";
    $stSql .= "             GROUP BY \n";
    $stSql .= "                 inscricao_municipal \n";
    $stSql .= "         )AS tmp \n";
    $stSql .= "     WHERE \n";
    $stSql .= "         tmp.timestamp = iil_tmp.timestamp \n";
    $stSql .= "         AND tmp.inscricao_municipal = iil_tmp.inscricao_municipal \n";
    $stSql .= " )AS iil \n";
    $stSql .= " ON \n";
    $stSql .= "     iil.inscricao_municipal = edf.inscricao_municipal \n";

    $stSql .= " LEFT JOIN \n";
    $stSql .= "     (
                        SELECT
                            tmp.*
                        FROM
                            imobiliario.lote AS tmp
                        INNER JOIN
                            (
                                SELECT
                                    max(timestamp) AS timestamp,
                                    cod_lote
                                FROM
                                    imobiliario.lote
                                GROUP BY
                                    cod_lote
                            )AS tmp2
                        ON
                            tmp.cod_lote = tmp2.cod_lote
                            AND tmp.timestamp = tmp2.timestamp
                    )AS il \n";
    $stSql .= " ON \n";
    $stSql .= "     il.cod_lote = iil.cod_lote \n";

    $stSql .= " LEFT JOIN \n";
    $stSql .= "     imobiliario.lote_localizacao AS ill \n";
    $stSql .= " ON \n";
    $stSql .= "     ill.cod_lote = il.cod_lote \n";

    $stSql .= " LEFT JOIN \n";
    $stSql .= "     imobiliario.localizacao AS ilo \n";
    $stSql .= " ON \n";
    $stSql .= "     ilo.cod_localizacao = ill.cod_localizacao \n";

    $stSql .= " LEFT JOIN ( \n";
    $stSql .= "     SELECT \n";
    $stSql .= "         edi_tmp.timestamp, \n";
    $stSql .= "         edi_tmp.inscricao_economica \n";
    $stSql .= "     FROM \n";
    $stSql .= "         economico.domicilio_informado AS edi_tmp, \n";
    $stSql .= "         (  \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 MAX(timestamp) AS timestamp, \n";
    $stSql .= "                 inscricao_economica  \n";
    $stSql .= "             FROM  \n";
    $stSql .= "                 economico.domicilio_informado \n";
    $stSql .= "             GROUP BY \n";
    $stSql .= "                 inscricao_economica \n";
    $stSql .= "         )AS tmp \n";
    $stSql .= "     WHERE  \n";
    $stSql .= "         tmp.timestamp = edi_tmp.timestamp \n";
    $stSql .= "         AND tmp.inscricao_economica = edi_tmp.inscricao_economica \n";
    $stSql .= " )AS edi  \n";
    $stSql .= " ON \n";
    $stSql .= "     ece.inscricao_economica = edi.inscricao_economica \n";

    $stSql .= " INNER JOIN
                    (
                        SELECT
                            tmp.*
                        FROM
                            economico.atividade_cadastro_economico AS tmp
                        INNER JOIN
                            (
                                SELECT
                                    max(ocorrencia_atividade) as ocorrencia_atividade,
                                    inscricao_economica
                                FROM
                                    economico.atividade_cadastro_economico
                                WHERE
                                    principal = true
                                GROUP BY
                                    inscricao_economica
                            )AS tmp2
                        ON
                            tmp.inscricao_economica = tmp2.inscricao_economica
                            AND tmp.ocorrencia_atividade = tmp2.ocorrencia_atividade
                    )AS eace \n";
    $stSql .= " ON \n";
    $stSql .= "     eace.inscricao_economica = ece.inscricao_economica \n";
    $stSql .= "     AND eace.principal = true \n";

    $stSql .= " INNER JOIN \n";
    $stSql .= "     arrecadacao.cadastro_economico_calculo AS acec \n";
    $stSql .= " ON \n";
    $stSql .= "     acec.inscricao_economica = ece.inscricao_economica \n";

    $stSql .= " INNER JOIN \n";
    $stSql .= "     arrecadacao.calculo AS ac \n";
    $stSql .= " ON \n";
    $stSql .= "     ac.cod_calculo = acec.cod_calculo \n";

    $stSql .= " INNER JOIN \n";
    $stSql .= "     arrecadacao.lancamento_calculo AS lc \n";
    $stSql .= " ON \n";
    $stSql .= "     lc.cod_calculo = ac.cod_calculo \n";

    $stSql .= " INNER JOIN \n";
    $stSql .= "     arrecadacao.parcela AS ap \n";
    $stSql .= " ON \n";
    $stSql .= "     ap.cod_lancamento = lc.cod_lancamento \n";

    $stSql .= " WHERE \n";
    $stSql .= "     ac.cod_credito = 2 \n";
    $stSql .= "     AND ac.cod_natureza = 1 \n";
    $stSql .= "     AND ac.cod_genero = 2 \n";
    $stSql .= "     AND ac.cod_especie = 2 \n";
    $stSql .= $stFiltro;

    return $stSql;
}

function recuperaDadosISSEstimativaMata(&$rsRecordSet, $stFiltro = "", $inCodParcela = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosISSEstimativaMata( $stFiltro, $inCodParcela );
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaDadosISSEstimativaMata($stFiltro = "", $inCodParcela)
{
    $stSql  = " SELECT \n";
    $stSql .= "     (
                        SELECT
                            arrecadacao.carne.numeracao
                        FROM
                            arrecadacao.carne
                        WHERE
                            arrecadacao.carne.cod_parcela = ap.cod_parcela
                            AND arrecadacao.carne.exercicio = ac.exercicio
                            AND arrecadacao.carne.timestamp = ac.timestamp
                    )as numeracao_carne, \n";
    $stSql .= "     ac.exercicio, \n";
    $stSql .= "     ece.inscricao_economica, \n";
    $stSql .= "     edf.inscricao_municipal, \n";
    $stSql .= "     ( \n";
    $stSql .= "         SELECT \n";
    $stSql .= "             ict.cod_logradouro \n";
    $stSql .= "         FROM \n";
    $stSql .= "             imobiliario.imovel_confrontacao AS iic \n";

    $stSql .= "         INNER JOIN \n";
    $stSql .= "             imobiliario.confrontacao_trecho AS ict \n";
    $stSql .= "         ON \n";
    $stSql .= "             ict.cod_confrontacao = iic.cod_confrontacao \n";
    $stSql .= "             AND ict.cod_lote = iic.cod_lote \n";

    $stSql .= "         WHERE \n";
    $stSql .= "             iic.inscricao_municipal = edf.inscricao_municipal \n";
    $stSql .= "     )AS cod_logradouro, \n";
    $stSql .= "     ( \n";
    $stSql .= "         SELECT \n";
    $stSql .= "             nom_cgm \n";
    $stSql .= "         FROM \n";
    $stSql .= "             sw_cgm \n";
    $stSql .= "         WHERE \n";
    $stSql .= "             sw_cgm.numcgm = COALESCE( eceep.numcgm, eceed.numcgm, ecea.numcgm ) \n";
    $stSql .= "     )AS razao_social, \n";
    $stSql .= "     ( \n";
    $stSql .= "         SELECT \n";
    $stSql .= "             nom_fantasia \n";
    $stSql .= "         FROM \n";
    $stSql .= "             sw_cgm_pessoa_juridica \n";
    $stSql .= "         WHERE \n";
    $stSql .= "             sw_cgm_pessoa_juridica.numcgm = COALESCE( eceep.numcgm, eceed.numcgm, ecea.numcgm ) \n";
    $stSql .= "     )AS nome_fantasia, \n";
    $stSql .= "     ( \n";
    $stSql .= "         SELECT \n";
    $stSql .= "             COALESCE( sw_cgm_pessoa_fisica.cpf, sw_cgm_pessoa_juridica.cnpj ) AS cpf \n";
    $stSql .= "         FROM \n";
    $stSql .= "             sw_cgm \n";

    $stSql .= "         LEFT JOIN \n";
    $stSql .= "             sw_cgm_pessoa_fisica \n";
    $stSql .= "         ON \n";
    $stSql .= "             sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm \n";

    $stSql .= "         LEFT JOIN \n";
    $stSql .= "             sw_cgm_pessoa_juridica \n";
    $stSql .= "         ON \n";
    $stSql .= "             sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm \n";

    $stSql .= "         WHERE \n";
    $stSql .= "             sw_cgm.numcgm = COALESCE( eceep.numcgm, eceed.numcgm, ecea.numcgm ) \n";
    $stSql .= "     )AS cpf_cnpj, \n";
    $stSql .= "     ( \n";
    $stSql .= "         SELECT \n";
    $stSql .= "             ( \n";
    $stSql .= "                 SELECT \n";
    $stSql .= "                     nom_cgm \n";
    $stSql .= "                 FROM \n";
    $stSql .= "                     sw_cgm \n";
    $stSql .= "                 WHERE \n";
    $stSql .= "                     sw_cgm.numcgm = economico.cadastro_econ_resp_tecnico.numcgm \n";
    $stSql .= "             )AS nome \n";
    $stSql .= "         FROM \n";
    $stSql .= "             economico.cadastro_econ_resp_tecnico \n";
    $stSql .= "         WHERE \n";
    $stSql .= "             cadastro_econ_resp_tecnico.inscricao_economica = ece.inscricao_economica \n";
    $stSql .= "             AND cadastro_econ_resp_tecnico.ativo = true \n";
    $stSql .= "         ORDER BY  \n";
    $stSql .= "             cadastro_econ_resp_tecnico.timestamp \n";
    $stSql .= "         LIMIT 1 \n";
    $stSql .= "     )AS resposavel, \n";
    $stSql .= "     ( \n";
    $stSql .= "         SELECT \n";
    $stSql .= "             nom_atividade \n";
    $stSql .= "         FROM \n";
    $stSql .= "             economico.atividade \n";
    $stSql .= "         WHERE \n";
    $stSql .= "             cod_atividade = eace.cod_atividade \n";
    $stSql .= "         ORDER BY \n";
    $stSql .= "             economico.atividade.timestamp \n";
    $stSql .= "         LIMIT 1 \n";
    $stSql .= "     )AS atividade, \n";
    $stSql .= "     CASE WHEN (edf.inscricao_municipal IS NOT NULL) AND (edi.inscricao_economica IS NOT NULL) THEN  \n";
    $stSql .= "         CASE WHEN (edf.timestamp > edi.timestamp) THEN  \n";
    $stSql .= "             economico.fn_busca_domicilio_fiscal( ece.inscricao_economica )  \n";
    $stSql .= "         ELSE  \n";
    $stSql .= "             economico.fn_busca_domicilio_informado( edi.inscricao_economica )  \n";
    $stSql .= "         END  \n";
    $stSql .= "     ELSE  \n";
    $stSql .= "         CASE WHEN (edf.inscricao_municipal IS NOT NULL) THEN \n";
    $stSql .= "             economico.fn_busca_domicilio_fiscal( ece.inscricao_economica ) \n";
    $stSql .= "         ELSE  \n";
    $stSql .= "             CASE WHEN (edi.inscricao_economica IS NOT NULL) THEN \n";
    $stSql .= "                 economico.fn_busca_domicilio_informado( edi.inscricao_economica ) \n";
    $stSql .= "             END  \n";
    $stSql .= "         END  \n";
    $stSql .= "     END as endereco, \n";
    $stSql .= "     ill.valor AS nro_lote, \n";
    $stSql .= "     ( \n";
    $stSql .= "         SELECT \n";
    $stSql .= "            tmp_il.nom_localizacao \n";
    $stSql .= "         FROM \n";
    $stSql .= "            imobiliario.localizacao AS tmp_il \n";

    $stSql .= "         INNER JOIN \n";
    $stSql .= "            imobiliario.localizacao_nivel AS tmp_iln \n";
    $stSql .= "         ON  \n";
    $stSql .= "            tmp_il.codigo_composto = tmp_iln.valor || '.00' \n";
    $stSql .= "            AND tmp_iln.cod_localizacao = ilo.cod_localizacao \n";
    $stSql .= "            AND tmp_iln.cod_nivel = 1 \n";
    $stSql .= "     ) AS regiao, \n";
    $stSql .= "     ilo.nom_localizacao AS distrito \n";
    $stSql .= " ,acec.cod_calculo \n";
    $stSql .= " ,to_char(ap.vencimento, 'dd/mm/yyyy' ) AS vencimento \n";
    $stSql .= " ,ap.valor \n";

    $stSql .= " ,ap.nr_parcela \n";

    $stSql .= " FROM \n";
    $stSql .= "     economico.cadastro_economico AS ece \n";

    $stSql .= " LEFT JOIN \n";
    $stSql .= "     economico.cadastro_economico_empresa_fato AS eceep \n";
    $stSql .= " ON \n";
    $stSql .= "     eceep.inscricao_economica = ece.inscricao_economica \n";

    $stSql .= " LEFT JOIN \n";
    $stSql .= "     economico.cadastro_economico_empresa_direito AS eceed \n";
    $stSql .= " ON \n";
    $stSql .= "     eceed.inscricao_economica = ece.inscricao_economica \n";

    $stSql .= " LEFT JOIN \n";
    $stSql .= "     economico.cadastro_economico_autonomo AS ecea \n";
    $stSql .= " ON \n";
    $stSql .= "     ecea.inscricao_economica = ece.inscricao_economica \n";

    $stSql .= " LEFT JOIN ( \n";
    $stSql .= "     SELECT  \n";
    $stSql .= "         edf_tmp.inscricao_economica, \n";
    $stSql .= "         edf_tmp.inscricao_municipal,  \n";
    $stSql .= "         edf_tmp.timestamp  \n";
    $stSql .= "     FROM  \n";
    $stSql .= "         economico.domicilio_fiscal AS edf_tmp, \n";
    $stSql .= "         (  \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 MAX (timestamp) AS timestamp, \n";
    $stSql .= "                 inscricao_economica  \n";
    $stSql .= "             FROM  \n";
    $stSql .= "                 economico.domicilio_fiscal \n";
    $stSql .= "             GROUP BY  \n";
    $stSql .= "                 inscricao_economica \n";
    $stSql .= "         )AS tmp  \n";
    $stSql .= "     WHERE  \n";
    $stSql .= "         tmp.timestamp = edf_tmp.timestamp \n";
    $stSql .= "         AND tmp.inscricao_economica = edf_tmp.inscricao_economica \n";
    $stSql .= " )AS edf  \n";
    $stSql .= " ON  \n";
    $stSql .= "     ece.inscricao_economica = edf.inscricao_economica \n";

    $stSql .= " LEFT JOIN ( \n";
    $stSql .= "     SELECT \n";
    $stSql .= "         iil_tmp.cod_lote, \n";
    $stSql .= "         iil_tmp.inscricao_municipal \n";
    $stSql .= "     FROM \n";
    $stSql .= "         imobiliario.imovel_lote AS iil_tmp, \n";
    $stSql .= "         ( \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 MAX(timestamp) AS timestamp, \n";
    $stSql .= "                 inscricao_municipal \n";
    $stSql .= "             FROM \n";
    $stSql .= "                 imobiliario.imovel_lote \n";
    $stSql .= "             GROUP BY \n";
    $stSql .= "                 inscricao_municipal \n";
    $stSql .= "         )AS tmp \n";
    $stSql .= "     WHERE \n";
    $stSql .= "         tmp.timestamp = iil_tmp.timestamp \n";
    $stSql .= "         AND tmp.inscricao_municipal = iil_tmp.inscricao_municipal \n";
    $stSql .= " )AS iil \n";
    $stSql .= " ON \n";
    $stSql .= "     iil.inscricao_municipal = edf.inscricao_municipal \n";

    $stSql .= " LEFT JOIN \n";
    $stSql .= "     imobiliario.lote AS il \n";
    $stSql .= " ON \n";
    $stSql .= "     il.cod_lote = iil.cod_lote \n";

    $stSql .= " LEFT JOIN \n";
    $stSql .= "     imobiliario.lote_localizacao AS ill \n";
    $stSql .= " ON \n";
    $stSql .= "     ill.cod_lote = il.cod_lote \n";

    $stSql .= " LEFT JOIN \n";
    $stSql .= "     imobiliario.localizacao AS ilo \n";
    $stSql .= " ON \n";
    $stSql .= "     ilo.cod_localizacao = ill.cod_localizacao \n";

    $stSql .= " LEFT JOIN ( \n";
    $stSql .= "     SELECT \n";
    $stSql .= "         edi_tmp.timestamp, \n";
    $stSql .= "         edi_tmp.inscricao_economica \n";
    $stSql .= "     FROM \n";
    $stSql .= "         economico.domicilio_informado AS edi_tmp, \n";
    $stSql .= "         (  \n";
    $stSql .= "             SELECT \n";
    $stSql .= "                 MAX(timestamp) AS timestamp, \n";
    $stSql .= "                 inscricao_economica  \n";
    $stSql .= "             FROM  \n";
    $stSql .= "                 economico.domicilio_informado \n";
    $stSql .= "             GROUP BY \n";
    $stSql .= "                 inscricao_economica \n";
    $stSql .= "         )AS tmp \n";
    $stSql .= "     WHERE  \n";
    $stSql .= "         tmp.timestamp = edi_tmp.timestamp \n";
    $stSql .= "         AND tmp.inscricao_economica = edi_tmp.inscricao_economica \n";
    $stSql .= " )AS edi  \n";
    $stSql .= " ON \n";
    $stSql .= "     ece.inscricao_economica = edi.inscricao_economica \n";

    $stSql .= " INNER JOIN \n";
    $stSql .= "     economico.atividade_cadastro_economico AS eace \n";
    $stSql .= " ON \n";
    $stSql .= "     eace.inscricao_economica = ece.inscricao_economica \n";
    $stSql .= "     AND eace.principal = true \n";

    $stSql .= " INNER JOIN \n";
    $stSql .= "     arrecadacao.cadastro_economico_calculo AS acec \n";
    $stSql .= " ON \n";
    $stSql .= "     acec.inscricao_economica = ece.inscricao_economica \n";

    $stSql .= " INNER JOIN \n";
    $stSql .= "     arrecadacao.calculo AS ac \n";
    $stSql .= " ON \n";
    $stSql .= "     ac.cod_calculo = acec.cod_calculo \n";

    $stSql .= " INNER JOIN \n";
    $stSql .= "     arrecadacao.parcela AS ap \n";
    $stSql .= " ON \n";
    $stSql .= "     ap.cod_lancamento = (SELECT cod_lancamento from arrecadacao.parcela where cod_parcela = ".$inCodParcela.") \n";

    $stSql .= " WHERE \n";
    $stSql .= "     (( ac.cod_credito = 9
                    AND ac.cod_natureza = 1
                    AND ac.cod_genero = 1
                    AND ac.cod_especie = 2 )
                        OR
                    ( ac.cod_credito = 5
                    AND ac.cod_natureza = 1
                    AND ac.cod_genero = 1
                    AND ac.cod_especie = 2 )) \n";
    $stSql .= $stFiltro;

    return $stSql;
}

function recuperaDetalheCreditosBaixaDivida(&$rsRecordSet, $stFiltro = "", $boTransacao = "", $dtDataBase)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaDetalheCreditosBaixaDivida($dtDataBase).$stFiltro;    
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDetalheCreditosBaixaDivida($dtDataBase)
{
    $stSql  = " SELECT DISTINCT
                    parcelamento.cod_modalidade
                    , parcela.vencimento
                    , parcela.cod_lancamento
                    , lista_inscricao_imob_eco_cgm_por_num_parcelamento( parcelamento.num_parcelamento ) AS inscricao
                    , dp.vlr_parcela
                    , CASE WHEN parcelamento.judicial = TRUE IS NOT NULL THEN
                        split_part( aplica_acrescimo_modalidade_carne( carne.numeracao,  1, NULL, NULL, parcelamento.cod_modalidade, 2, parcelamento.num_parcelamento ,dp.vlr_parcela, parcela.vencimento, '".$dtDataBase."', 'true' ), ';', 1 )
                      ELSE
                        split_part( aplica_acrescimo_modalidade_carne( carne.numeracao,  0, NULL, NULL, parcelamento.cod_modalidade, 2, parcelamento.num_parcelamento ,dp.vlr_parcela, parcela.vencimento, '".$dtDataBase."', 'true' ), ';', 1 )
                      END AS juros

                    , CASE WHEN parcelamento.judicial = TRUE THEN
                        split_part( aplica_acrescimo_modalidade_carne( carne.numeracao,  1, NULL, NULL, parcelamento.cod_modalidade, 3, parcelamento.num_parcelamento ,dp.vlr_parcela, parcela.vencimento, '".$dtDataBase."', 'true' ), ';', 1 )
                      ELSE
                        split_part( aplica_acrescimo_modalidade_carne( carne.numeracao,  0, NULL, NULL, parcelamento.cod_modalidade, 3, parcelamento.num_parcelamento ,dp.vlr_parcela, parcela.vencimento, '".$dtDataBase."', 'true' ), ';', 1 )
                      END AS multa

                    , CASE WHEN parcelamento.judicial = TRUE THEN
                        split_part( aplica_acrescimo_modalidade_carne( carne.numeracao,  1, NULL, NULL, parcelamento.cod_modalidade, 1, parcelamento.num_parcelamento ,dp.vlr_parcela, parcela.vencimento, '".$dtDataBase."', 'true' ), ';', 1 )
                      ELSE
                        split_part( aplica_acrescimo_modalidade_carne( carne.numeracao,  0, NULL, NULL, parcelamento.cod_modalidade, 1, parcelamento.num_parcelamento ,dp.vlr_parcela, parcela.vencimento, '".$dtDataBase."', 'true' ), ';', 1 )
                      END AS correcao

                    , CASE WHEN parcelamento.judicial = TRUE THEN
                         aplica_acrescimo_modalidade_carne( carne.numeracao,  1, NULL, NULL, parcelamento.cod_modalidade, 2, parcelamento.num_parcelamento ,dp.vlr_parcela, parcela.vencimento, '".$dtDataBase."', 'true' )
                      ELSE
                         aplica_acrescimo_modalidade_carne( carne.numeracao,  0, NULL, NULL, parcelamento.cod_modalidade, 2, parcelamento.num_parcelamento ,dp.vlr_parcela, parcela.vencimento, '".$dtDataBase."', 'true' )
                      END AS juros_completo

                    , CASE WHEN parcelamento.judicial = TRUE THEN
                        aplica_acrescimo_modalidade_carne( carne.numeracao,  1, NULL, NULL, parcelamento.cod_modalidade, 3, parcelamento.num_parcelamento ,dp.vlr_parcela, parcela.vencimento, '".$dtDataBase."', 'true' )
                      ELSE
                        aplica_acrescimo_modalidade_carne( carne.numeracao,  0, NULL, NULL, parcelamento.cod_modalidade, 3, parcelamento.num_parcelamento ,dp.vlr_parcela, parcela.vencimento, '".$dtDataBase."', 'true' )
                      END AS multa_completo

                    , CASE WHEN parcelamento.judicial = TRUE THEN
                        aplica_acrescimo_modalidade_carne( carne.numeracao,  1, NULL, NULL, parcelamento.cod_modalidade, 1, parcelamento.num_parcelamento ,dp.vlr_parcela, parcela.vencimento, '".$dtDataBase."', 'true' )
                      ELSE
                        aplica_acrescimo_modalidade_carne( carne.numeracao,  0, NULL, NULL, parcelamento.cod_modalidade, 1, parcelamento.num_parcelamento ,dp.vlr_parcela, parcela.vencimento, '".$dtDataBase."', 'true' )
                      END AS correcao_completo

                    , dp.num_parcelamento
                    , dp.num_parcela
                FROM arrecadacao.carne

                INNER JOIN
                    (
                        SELECT  parcela.cod_parcela
                                  , CASE WHEN arrecadacao.fn_atualiza_data_vencimento ( COALESCE( reemissao.vencimento, parcela.vencimento ))  < '".$dtDataBase."' THEN
                                        COALESCE( reemissao.vencimento, parcela.vencimento ) 
                                    ELSE
                                        arrecadacao.fn_atualiza_data_vencimento ( COALESCE( reemissao.vencimento, parcela.vencimento ) ) 
                                    END AS vencimento                                
                                  , parcela.valor
                                  , parcela.nr_parcela
                                  , parcela.cod_lancamento

                           FROM arrecadacao.parcela

                             JOIN arrecadacao.carne
                               ON carne.cod_parcela = parcela.cod_parcela
                             AND carne.numeracao = '".$this->getDado('numero_carne')."'

                     LEFT JOIN
                            (          SELECT parcela_reemissao.timestamp
                                         , parcela_reemissao.cod_parcela
                                         , parcela_reemissao.vencimento
                                     FROM arrecadacao.parcela_reemissao
                               INNER JOIN (SELECT MIN(parcela_reemissao.timestamp) as timestamp
                                                , parcela_reemissao.cod_parcela
                                             FROM arrecadacao.parcela_reemissao
                                             JOIN arrecadacao.parcela
                                               ON parcela.cod_parcela = parcela_reemissao.cod_parcela
                                             JOIN arrecadacao.carne
                                               ON carne.cod_parcela = parcela.cod_parcela
                                            WHERE carne.numeracao = '".$this->getDado('numero_carne')."'
                                         GROUP BY parcela_reemissao.cod_parcela
                                         ) AS min_parcela_remissao
                              ON parcela_reemissao.cod_parcela = min_parcela_remissao.cod_parcela
                             AND parcela_reemissao.timestamp = min_parcela_remissao.timestamp  

                            )AS reemissao
                        ON reemissao.cod_parcela = parcela.cod_parcela
                    ) AS parcela
                           ON parcela.cod_parcela = carne.cod_parcela

                INNER JOIN arrecadacao.lancamento_calculo
                            ON lancamento_calculo.cod_lancamento = parcela.cod_lancamento

                INNER JOIN divida.parcela_calculo
                            ON parcela_calculo.num_parcela = parcela.nr_parcela
                          AND parcela_calculo.cod_calculo = lancamento_calculo.cod_calculo

                INNER JOIN divida.parcela AS dp
                            ON dp.num_parcelamento = parcela_calculo.num_parcelamento
                          AND dp.num_parcela = parcela_calculo.num_parcela

                INNER JOIN divida.parcelamento
                            ON parcelamento.num_parcelamento = parcela_calculo.num_parcelamento

                WHERE
    \n";

    return $stSql;
}

function recuperaModeloCarne(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaModeloCarne().$stFiltro;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaModeloCarne()
{
    $stSql  = " SELECT
                    amc.nom_modelo,
                    amc.nom_arquivo,
                    amc.cod_modelo

                FROM
                    arrecadacao.modelo_carne AS amc

                INNER JOIN
                    arrecadacao.acao_modelo_carne AS aamc
                ON
                    aamc.cod_modelo = amc.cod_modelo
    \n";

    return $stSql;
}

function CarneDivida(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaCarneDivida().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
/*****************************************
// para impressao no pdf / IPTU
*****************************************/
function montaCarneDivida()
{
    $stSql = " select distinct
                    coalesce( dde.inscricao_economica, ddi.inscricao_municipal) as inscricao_municipal,
                    CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                        arrecadacao.fn_consulta_endereco_imovel(ddi.inscricao_municipal)
                    ELSE
                        CASE WHEN (edf.inscricao_municipal IS NOT NULL) AND (edi.inscricao_economica IS NOT NULL) THEN
                            CASE WHEN (edf.timestamp > edi.timestamp) THEN
                                economico.fn_busca_domicilio_fiscal( edf.inscricao_municipal )
                            ELSE
                                economico.fn_busca_domicilio_informado( edi.inscricao_economica )
                            END
                        ELSE
                            CASE WHEN (edf.inscricao_municipal IS NOT NULL) THEN
                                economico.fn_busca_domicilio_fiscal( edf.inscricao_municipal )
                            ELSE
                                CASE WHEN (edi.inscricao_economica IS NOT NULL) THEN
                                    economico.fn_busca_domicilio_informado( edi.inscricao_economica )
                                ELSE
                                        cgm.tipo_logradouro||' '||cgm.logradouro||' '||cgm.numero||' '||cgm.complemento
                                END
                            END
                        END
                    END as nom_logradouro,
                    ac.cod_calculo,
                    ac.exercicio,
                    to_char(now(),'dd/mm/yyyy') as data_processamento,
                    case when acgc.descricao is not null then
                        acgc.descricao
                    else mc.descricao_credito
                    end as descricao,
                    case when acgc.cod_grupo is not null then
                        acgc.cod_grupo::varchar
                    else mc.cod_credito||'.'||mc.cod_especie||'.'||mc.cod_genero||'.'||mc.cod_natureza
                    end as cod_grupo,
                    arrecadacao.fn_busca_origem_lancamento(al.cod_lancamento,ac.exercicio,1,1) as descricao_cred,
                    mc.descricao_credito,
                    mc.cod_credito,
                    ac.valor,
                    cgm.nom_cgm,
                    cgm.numcgm,
                    CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                        arrecadacao.fn_proprietarios_imovel_nomes(ddi.inscricao_municipal)
                    ELSE
                        ''
                    END as proprietarios,
                    al.observacao,
                    al.cod_lancamento,
                    ddc.cod_inscricao,
                    ddp.num_parcelamento,
                    dpar.numero_parcelamento,
                    dpar.exercicio AS exercicio_cobranca
                from
                    divida.divida_cgm AS ddc

                LEFT JOIN
                    divida.divida_imovel AS ddi
                ON
                    ddi.cod_inscricao = ddc.cod_inscricao
                    AND ddi.exercicio = ddc.exercicio

                LEFT JOIN
                    divida.divida_empresa AS dde
                ON
                    dde.cod_inscricao = ddc.cod_inscricao
                    AND dde.exercicio = ddc.exercicio

                INNER JOIN
                    divida.divida_parcelamento AS ddp
                ON
                    ddp.exercicio = ddc.exercicio
                    AND ddp.cod_inscricao = ddc.cod_inscricao

                INNER JOIN
                    divida.parcelamento AS dpar
                ON
                    dpar.num_parcelamento = ddp.num_parcelamento

                INNER JOIN
                    divida.parcela AS dp
                ON
                    dp.num_parcelamento = ddp.num_parcelamento

                INNER JOIN
                    divida.parcela_calculo AS dpc
                ON
                    dpc.num_parcelamento = dp.num_parcelamento
                    AND dpc.num_parcela = dp.num_parcela

                INNER JOIN
                    arrecadacao.calculo as ac
                ON
                    ac.cod_calculo = dpc.cod_calculo

                INNER JOIN
                    monetario.credito as mc
                ON
                    mc.cod_credito = ac.cod_credito AND mc.cod_natureza = ac.cod_natureza
                    AND mc.cod_especie = ac.cod_especie  AND mc.cod_genero = ac.cod_genero

                INNER JOIN
                    sw_cgm as cgm
                ON
                    cgm.numcgm = ddc.numcgm

                INNER JOIN
                    arrecadacao.lancamento_calculo as alc
                ON
                    alc.cod_calculo = ac.cod_calculo

                INNER JOIN
                    arrecadacao.lancamento as al
                ON
                    al.cod_lancamento = alc.cod_lancamento

                LEFT JOIN (
                    SELECT
                        edf_tmp.inscricao_economica,
                        edf_tmp.inscricao_municipal,
                        edf_tmp.timestamp
                    FROM
                        economico.domicilio_fiscal AS edf_tmp,
                        (
                            SELECT
                                MAX (timestamp) AS timestamp,
                                inscricao_economica
                            FROM
                                economico.domicilio_fiscal
                            GROUP BY
                                inscricao_economica
                        )AS tmp
                    WHERE
                        tmp.timestamp = edf_tmp.timestamp
                        AND tmp.inscricao_economica = edf_tmp.inscricao_economica
                )AS edf
                ON
                    edf.inscricao_economica = dde.inscricao_economica

                LEFT JOIN (
                    SELECT
                        edi_tmp.timestamp,
                        edi_tmp.inscricao_economica
                    FROM
                        economico.domicilio_informado AS edi_tmp,
                        (
                            SELECT
                                MAX(timestamp) AS timestamp,
                                inscricao_economica
                            FROM
                                economico.domicilio_informado
                            GROUP BY
                                inscricao_economica
                        )AS tmp
                    WHERE
                        tmp.timestamp = edi_tmp.timestamp
                        AND tmp.inscricao_economica = edi_tmp.inscricao_economica
                )AS edi
                ON
                    dde.inscricao_economica = edi.inscricao_economica




                LEFT JOIN
                    ( select agc.descricao, agc.cod_grupo, acgc.cod_calculo, agc.ano_exercicio
                        from arrecadacao.grupo_credito as agc
                        INNER JOIN  arrecadacao.calculo_grupo_credito as acgc
                        ON acgc.cod_grupo = agc.cod_grupo AND acgc.ano_exercicio = agc.ano_exercicio
                    ) as acgc
                ON
                    acgc.cod_calculo = alc.cod_calculo AND acgc.ano_exercicio = ac.exercicio
                where
                    cgm.numcgm  is not null
                \n";

    return $stSql;
}

function ListaCarnesPagosLancamento(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaListaCarnesPagosLancamento( $stFiltro );
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaListaCarnesPagosLancamento($stFiltro)
{
    $stSql = " SELECT DISTINCT
                    apag.numeracao
                FROM
                    arrecadacao.lancamento_calculo AS alc

                INNER JOIN
                    arrecadacao.parcela AS ap
                ON
                    ap.cod_lancamento = alc.cod_lancamento

                INNER JOIN
                    arrecadacao.carne AS ac
                ON
                    ac.cod_parcela = ap.cod_parcela

                INNER JOIN
                    arrecadacao.pagamento AS apag
                ON
                    apag.numeracao = ac.numeracao

                WHERE
                    alc.cod_lancamento = ".$stFiltro;

    return $stSql;
}

function ListaDadosPorCreditoParaCarneIPTU(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaListaDadosPorCreditoParaCarneIPTU().$stFiltro;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaListaDadosPorCreditoParaCarneIPTU()
{
    $stSql = "
        SELECT DISTINCT
              mc.cod_credito||'.'||mc.cod_especie||'.'||mc.cod_genero||'.'||mc.cod_natureza||' - '||mc.descricao_credito AS credito,
              mc.cod_credito||'.'||mc.cod_especie||'.'||mc.cod_genero||'.'||mc.cod_natureza AS grupo,
              mc.descricao_credito AS descricao_credito,
              mc.cod_credito,
            CASE WHEN ap.nr_parcela = 0 THEN
                al.valor
            ELSE
                CASE WHEN ( arrecadacao.fn_total_parcelas(al.cod_lancamento) > 1 )
                            and ap.nr_parcela = 1
                THEN
                    al.valor * arrecadacao.calculaProporcaoParcela(ap.cod_parcela) - arrecadacao.fn_calcula_diff_credito_primeira_parcela( al.cod_lancamento, ac.cod_calculo )
                ELSE
                    ac.valor * arrecadacao.calculaProporcaoParcela(ap.cod_parcela)
                END
            END::numeric(14,2) AS valor_credito

            , (
                aplica_multa_credito_parcela( carne.numeracao,ac.exercicio::int, ap.cod_parcela, ap.vencimento, mc.cod_credito, mc.cod_especie, mc.cod_genero, mc.cod_natureza )
            )::numeric(14,2) as credito_multa_pagar,

            (
                aplica_correcao_credito_parcela( carne.numeracao,ac.exercicio::int, ap.cod_parcela, ap.vencimento, mc.cod_credito, mc.cod_especie, mc.cod_genero, mc.cod_natureza )
            )::numeric(14,2) as credito_correcao_pagar,

            (
            aplica_juro_credito_parcela( carne.numeracao,ac.exercicio::int, ap.cod_parcela, ap.vencimento, mc.cod_credito, mc.cod_especie, mc.cod_genero, mc.cod_natureza )
            )::numeric(14,2) as credito_juros_pagar,
            carne.numeracao,
            CASE WHEN ap.nr_parcela = 0 THEN
                'única'
            ELSE
                ap.nr_parcela||'/'||(
                    SELECT
                        max(parcela.nr_parcela)
                    FROM
                        arrecadacao.parcela
                    WHERE
                        parcela.cod_lancamento = al.cod_lancamento
                )::varchar
            END AS nr_parcela,
            to_char( ap.vencimento, 'dd/mm/yyyy' ) AS vencimento,
            acg.desconto,
            COALESCE (
                (
                    SELECT
                        parcela_desconto.valor
                    FROM
                        arrecadacao.parcela_desconto
                    WHERE
                        parcela_desconto.cod_parcela = ap.cod_parcela
                        AND parcela_desconto.vencimento >= now()::date
                ),
                0.00
            )AS desconto_parcela,
            COALESCE( acg.ordem, 1 )AS ordem,
            ap.nr_parcela AS numeracao_parcela

        FROM
            arrecadacao.lancamento_calculo as al

            INNER JOIN arrecadacao.parcela as ap
            ON ap.cod_lancamento = al.cod_lancamento

            INNER JOIN arrecadacao.carne
            ON carne.cod_parcela = ap.cod_parcela

            INNER JOIN arrecadacao.calculo as ac
            ON ac.cod_calculo = al.cod_calculo

            LEFT JOIN arrecadacao.calculo_grupo_credito as acgc
            ON acgc.cod_calculo = ac.cod_calculo
            AND acgc.ano_exercicio = ac.exercicio

            LEFT JOIN (
                select
                    exercicio
                    , valor
                from
                    administracao.configuracao
                where parametro = 'baixa_manual' AND cod_modulo = 25
            ) as baixa_manual_unica
            ON baixa_manual_unica.exercicio = acgc.ano_exercicio

            LEFT JOIN arrecadacao.grupo_credito as agc
            ON agc.cod_grupo = acgc.cod_grupo
            AND agc.ano_exercicio = acgc.ano_exercicio

            LEFT JOIN monetario.credito as mc
            ON mc.cod_credito = ac.cod_credito
            AND mc.cod_especie = ac.cod_especie
            AND mc.cod_genero = ac.cod_genero
            AND mc.cod_natureza = ac.cod_natureza

            LEFT JOIN arrecadacao.credito_grupo AS acg
            ON acg.cod_grupo = acgc.cod_grupo
            AND acg.ano_exercicio = acgc.ano_exercicio
            AND acg.cod_credito = ac.cod_credito
            AND acg.cod_especie = ac.cod_especie
            AND acg.cod_genero = ac.cod_genero
            AND acg.cod_natureza = ac.cod_natureza
    ";

    return $stSql;
}

function RetornaCPForCNPJ(&$rsRecordSet, $stNumCGM, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRetornaCPForCNPJ($stNumCGM);
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRetornaCPForCNPJ($stNumCGM)
{
    $stSql .= "         SELECT \n";
    $stSql .= "             COALESCE( sw_cgm_pessoa_fisica.cpf, sw_cgm_pessoa_juridica.cnpj ) AS cpf \n";
    $stSql .= "         FROM \n";
    $stSql .= "             sw_cgm \n";

    $stSql .= "         LEFT JOIN \n";
    $stSql .= "             sw_cgm_pessoa_fisica \n";
    $stSql .= "         ON \n";
    $stSql .= "             sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm \n";

    $stSql .= "         LEFT JOIN \n";
    $stSql .= "             sw_cgm_pessoa_juridica \n";
    $stSql .= "         ON \n";
    $stSql .= "             sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm \n";

    $stSql .= "         WHERE \n";
    $stSql .= "             sw_cgm.numcgm = ".$stNumCGM;

    return $stSql;
}

function ListaDadosPorGrupoParaCarneIPTUDataBase(&$rsRecordSet, $stFiltro = "",$stDataBase='', $boTransacao = "", $stOrdem=" ORDER BY numeracao_parcela ")
{
    if ($stDataBase=='') {
        $stDataBase = 'ap.vencimento';
    } else {
        $stDataBase = "'".$stDataBase."'::date";
    }
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaListaDadosPorGrupoParaCarneIPTU($stDataBase).$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function ListaDadosPorGrupoParaCarneIPTU(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    return $this->ListaDadosPorGrupoParaCarneIPTUDataBase( $rsRecordSet, $stFiltro,'', $boTransacao );
}

function montaListaDadosPorGrupoParaCarneIPTU($stDataBase)
{
    $stSql = "
       SELECT DISTINCT
            arrecadacao.fn_busca_origem_lancamento_sem_exercicio(al.cod_lancamento, 1, 1 ) AS credito
            , CASE WHEN apr.cod_parcela <> null THEN
                    ap.valor::numeric(14,2)
                 ELSE
                    CASE WHEN apr.valor <> null THEN
                        apr.valor::numeric(14,2)
                    ELSE
                        ap.valor::numeric(14,2)
                    END
                 END AS valor_credito
            , COALESCE (
                (
                    SELECT
                        valor::numeric(14,2)
                    FROM
                        arrecadacao.parcela_desconto
                    WHERE
                        parcela_desconto.cod_parcela = ap.cod_parcela";
                        if ($this->getDado("valida_desconto_itau") == false) {
                            $stSql .=  " AND parcela_desconto.vencimento >= now()::date";
                        }
        $stSql .="
                        
                ),
                0.00
            )AS valor_desconto

            , (
                aplica_multa( carne.numeracao, carne.exercicio::int, ap.cod_parcela, $stDataBase )
            )::numeric(14,2) as credito_multa_pagar,

            (
                aplica_correcao( carne.numeracao, carne.exercicio::int, ap.cod_parcela, $stDataBase )
            )::numeric(14,2) as credito_correcao_pagar,

            (
                aplica_juro( carne.numeracao, carne.exercicio::int, ap.cod_parcela, $stDataBase )
            )::numeric(14,2) as credito_juros_pagar,

            carne.numeracao,
            CASE WHEN ap.nr_parcela = 0 THEN
                'única'
            ELSE
                ap.nr_parcela||'/'||(
                    SELECT
                        max(parcela.nr_parcela)
                    FROM
                        arrecadacao.parcela
                    WHERE
                        parcela.cod_lancamento = al.cod_lancamento
                )::varchar
            END AS nr_parcela,
            to_char( $stDataBase, 'dd/mm/yyyy' ) AS vencimento,
            ap.nr_parcela AS numeracao_parcela

        FROM arrecadacao.lancamento_calculo as al

            INNER JOIN arrecadacao.parcela as ap
            ON ap.cod_lancamento = al.cod_lancamento

            LEFT JOIN ( SELECT tmp.*
                                FROM arrecadacao.parcela_reemissao AS tmp
                                        , (  SELECT timestamp
                                                       , cod_parcela
                                                       , valor
                                                FROM arrecadacao.parcela_reemissao
                                         ORDER BY timestamp DESC
                                        ) AS tmp2
                              WHERE tmp.cod_parcela = tmp2.cod_parcela
                                  AND tmp.timestamp = tmp2.timestamp
                         ) AS apr
                    ON apr.cod_parcela = ap.cod_parcela

            INNER JOIN arrecadacao.carne
            ON carne.cod_parcela = ap.cod_parcela
    ";

    return $stSql;
}

function recuperaDadosValorVenalIPTUGenericoUrbem(&$rsRecordSet, $inInscricao, $inExercicio, $inCodParcela, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosValorVenalIPTUGenericoUrbem( $inInscricao, $inExercicio, $inCodParcela );
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaDadosValorVenalIPTUGenericoUrbem($inInscricao, $inExercicio, $inCodParcela)
{
    $stSql  = " SELECT
                    (   SELECT coalesce(a.area_real,0) as area
                          FROM imobiliario.area_lote a
                             , imobiliario.lote b
                         WHERE a.cod_lote = b.cod_lote
                           and a.cod_lote = IMOBILIARIO.FN_BUSCA_LOTE_IMOVEL( tmp.inscricao_municipal )
                      ORDER BY a.timestamp DESC
                         LIMIT 1
                    ) As area_lote,
                    imobiliario.fn_calcula_area_imovel( tmp.inscricao_municipal ) AS area_edificacao,
                    CASE WHEN arrecadacao.verificaEdificacaoImovel( tmp.inscricao_municipal ) = 't' THEN
                        COALESCE( IMOBILIARIO.BUSCAQUANTIDADEUNIDADESDEPENDENTES( tmp.inscricao_municipal ), 0 ) + 1
                    ELSE
                        0
                    END AS qtd_edificacao,
                    CASE WHEN arrecadacao.verificaEdificacaoImovel( tmp.inscricao_municipal ) = 't' THEN
                        '0,5%'
                    ELSE
                        '1%'
                    END AS aliquota,

                    CASE WHEN tmp.venal_territorial_calculado IS NOT NULL THEN
                        tmp.venal_territorial_calculado
                    ELSE
                        (
                        SELECT
                            COALESCE( venal_territorial_informado, venal_territorial_calculado)
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = tmp.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = tmp.exercicio
                            AND (venal_territorial_informado IS NOT NULL OR venal_territorial_calculado IS NOT NULL )
                        order by timestamp desc limit 1
                        )
                    END AS venal_territorial_calculado,

                    CASE WHEN tmp.venal_predial_calculado IS NOT NULL THEN
                        tmp.venal_predial_calculado
                    ELSE
                        (
                        SELECT
                            COALESCE( venal_predial_informado, venal_predial_calculado)
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = tmp.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = tmp.exercicio
                            AND (venal_predial_informado IS NOT NULL OR venal_predial_calculado IS NOT NULL )
                        order by timestamp desc limit 1
                        )
                    END AS venal_predial_calculado,

                    CASE WHEN tmp.venal_total_calculado IS NOT NULL THEN
                        tmp.venal_total_calculado
                    ELSE
                        (
                        SELECT
                            COALESCE( venal_total_informado, venal_total_calculado)
                        FROM
                            arrecadacao.imovel_v_venal
                        WHERE
                            arrecadacao.imovel_v_venal.inscricao_municipal = tmp.inscricao_municipal
                            AND arrecadacao.imovel_v_venal.exercicio = tmp.exercicio
                            AND (venal_total_informado IS NOT NULL OR venal_total_calculado IS NOT NULL )
                        order by timestamp desc limit 1
                        )
                    END AS venal_total_calculado,
                    (
                        SELECT
                            lote_localizacao.valor
                        FROM
                            imobiliario.lote_localizacao
                        WHERE
                            lote_localizacao.cod_lote = IMOBILIARIO.FN_BUSCA_LOTE_IMOVEL( tmp.inscricao_municipal )
                    )AS lote_valor

                FROM
                    (
                    SELECT
                        aivv.exercicio,
                        (
                            SELECT
                                venal_territorial_calculado
                            FROM
                                arrecadacao.imovel_v_venal
                            WHERE
                                arrecadacao.imovel_v_venal.inscricao_municipal = aivv.inscricao_municipal
                                AND arrecadacao.imovel_v_venal.exercicio = aivv.exercicio
                                AND arrecadacao.imovel_v_venal.timestamp = aic.timestamp
                            order by arrecadacao.imovel_v_venal.timestamp limit 1
                        )AS venal_territorial_calculado,
                        (
                            SELECT
                                venal_predial_calculado
                            FROM
                                arrecadacao.imovel_v_venal
                            WHERE
                                arrecadacao.imovel_v_venal.inscricao_municipal = aivv.inscricao_municipal
                                AND arrecadacao.imovel_v_venal.exercicio = aivv.exercicio
                                AND arrecadacao.imovel_v_venal.timestamp = aic.timestamp
                        )AS venal_predial_calculado,
                        (
                            SELECT
                                venal_total_calculado
                            FROM
                                arrecadacao.imovel_v_venal
                            WHERE
                                arrecadacao.imovel_v_venal.inscricao_municipal = aivv.inscricao_municipal
                                AND arrecadacao.imovel_v_venal.exercicio = aivv.exercicio
                                AND arrecadacao.imovel_v_venal.timestamp = aic.timestamp
                        )AS venal_total_calculado,
                        aivv.inscricao_municipal

                    FROM
                        arrecadacao.imovel_v_venal AS aivv

                    INNER JOIN
                        arrecadacao.imovel_calculo AS aic
                    ON
                        aic.timestamp = aivv.timestamp
                        AND aic.inscricao_municipal = aivv.inscricao_municipal
                        AND aic.cod_calculo = (SELECT cod_calculo FROM arrecadacao.lancamento_calculo WHERE arrecadacao.lancamento_calculo.cod_lancamento = (SELECT cod_lancamento from arrecadacao.parcela where cod_parcela = ".$inCodParcela.") limit 1)

                    WHERE aivv.inscricao_municipal = ".$inInscricao." AND aivv.exercicio = '".$inExercicio."'
                )AS tmp ";

    return $stSql;
}

//recupera lancamentos com debitos em aberto por cgm, consulta utilizada no carne generico
function recuperaSituacaoArrecadacaoCGM(&$rsRecordSet, $inCGM, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaSituacaoArrecadacaoCGM( $inCGM );
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaSituacaoArrecadacaoCGM($inCGM)
{
    $stSql  = "
        SELECT DISTINCT
            parcela.cod_lancamento

        FROM
            arrecadacao.parcela

        INNER JOIN
            (
                SELECT DISTINCT
                    lancamento_calculo.cod_lancamento

                FROM
                    arrecadacao.lancamento_calculo

                INNER JOIN
                    arrecadacao.calculo_cgm
                ON
                    calculo_cgm.cod_calculo = lancamento_calculo.cod_calculo

                WHERE
                    calculo_cgm.numcgm = ".$inCGM."
            )AS lancamento_cgm
        ON
            lancamento_cgm.cod_lancamento = parcela.cod_lancamento

        INNER JOIN
            arrecadacao.carne
        ON
            carne.cod_parcela = parcela.cod_parcela

        LEFT JOIN
            arrecadacao.pagamento
        ON
            pagamento.numeracao = carne.numeracao

        LEFT JOIN
            arrecadacao.carne_devolucao
        ON
            carne_devolucao.numeracao = carne.numeracao

        WHERE
            carne_devolucao.numeracao IS NULL
            AND pagamento.numeracao IS NULL
            AND now()::date > parcela.vencimento
    ";

    return $stSql;
}

function recuperaNumeracaoParaCompensacao(&$rsRecordSet, $inCodCalculo, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaNumeracaoParaCompensacao( $inCodCalculo );
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaNumeracaoParaCompensacao($inCodCalculo)
{
    $stSql = "
        SELECT DISTINCT
            funcao.nom_funcao,
            carteira.cod_carteira,
            convenio.cod_convenio

        FROM
            arrecadacao.calculo

        INNER JOIN
            monetario.credito
        ON
            credito.cod_credito = calculo.cod_credito
            AND credito.cod_especie = calculo.cod_especie
            AND credito.cod_natureza = calculo.cod_natureza
            AND credito.cod_genero = calculo.cod_genero

        INNER JOIN
            monetario.convenio
        ON
            convenio.cod_convenio = credito.cod_convenio

        LEFT JOIN
            monetario.carteira
        ON
            carteira.cod_convenio = convenio.cod_convenio

        INNER JOIN
            monetario.tipo_convenio
        ON
            tipo_convenio.cod_tipo = convenio.cod_tipo

        INNER JOIN
            administracao.funcao
        ON
            funcao.cod_funcao = tipo_convenio.cod_funcao
            AND funcao.cod_biblioteca = tipo_convenio.cod_biblioteca
            AND funcao.cod_modulo = tipo_convenio.cod_modulo

        WHERE
            calculo.cod_calculo = ".$inCodCalculo;

    return $stSql;
}

function recuperaValorePorCreditoParaCarne(&$rsRecordSet, $inCodParcela, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaValorePorCreditoParaCarne( $inCodParcela );
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaValorePorCreditoParaCarne($inCodParcela)
{
    $stSql = "
        SELECT
            credito.descricao_credito AS credito,
            calculo.valor,
            lancamento.valor AS total

        FROM
            arrecadacao.parcela

        INNER JOIN
            arrecadacao.lancamento
        ON
            lancamento.cod_lancamento = parcela.cod_lancamento

        INNER JOIN
            arrecadacao.lancamento_calculo
        ON
            lancamento_calculo.cod_lancamento = parcela.cod_lancamento

        INNER JOIN
            arrecadacao.calculo
        ON
            calculo.cod_calculo = lancamento_calculo.cod_calculo

        INNER JOIN
            monetario.credito
        ON
            credito.cod_credito = calculo.cod_credito
            AND credito.cod_especie = calculo.cod_especie
            AND credito.cod_genero = calculo.cod_genero
            AND credito.cod_natureza = calculo.cod_natureza

        WHERE
            parcela.cod_parcela = ".$inCodParcela;

    return $stSql;
}

} // end of class

?>