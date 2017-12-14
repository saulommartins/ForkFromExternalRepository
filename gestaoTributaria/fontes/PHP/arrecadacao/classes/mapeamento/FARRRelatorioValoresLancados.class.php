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
    * Classe de mapeamento para relatorio de Valores Lançados
    * Data de Criação: 06/04/2006

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: FARRRelatorioValoresLancados.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.13
*/

/*
$Log$
Revision 1.38  2007/09/14 14:29:50  vitor
Retirado Debug

Revision 1.37  2007/09/13 13:42:49  vitor
retirado Debug

Revision 1.36  2007/09/13 13:38:26  vitor
uc-05.03.23

Revision 1.35  2007/07/30 21:07:22  dibueno
Bug#9782#

Revision 1.34  2007/05/23 19:36:19  dibueno
Bug #9279#

Revision 1.33  2007/05/15 15:04:24  dibueno
Identação

Revision 1.32  2007/05/09 13:16:57  dibueno
Codigo comentado excluido

Revision 1.31  2007/04/21 22:27:58  dibueno
Bug #9168#

Revision 1.30  2007/04/20 13:25:58  dibueno
Bug #9168#

Revision 1.29  2007/03/23 20:53:51  dibueno
*** empty log message ***

Revision 1.28  2007/03/21 14:20:41  dibueno
Bug #8416#

Revision 1.27  2007/03/19 20:25:42  dibueno
Bug #8416#

Revision 1.26  2007/03/08 21:55:22  dibueno
Melhorias no relatório SINTETICO

Revision 1.25  2007/03/08 13:55:47  dibueno
Valor correção no relatorio sintético

Revision 1.24  2007/02/26 20:35:39  dibueno
Bug #8416#

Revision 1.23  2007/02/23 20:32:23  dibueno
Bug #8416#

Revision 1.22  2007/02/23 19:33:05  dibueno
Bug #8416#

Revision 1.21  2007/02/23 18:33:28  dibueno
Bug #8416#

Revision 1.20  2007/02/23 14:38:48  dibueno
Bug #8416#

Revision 1.19  2007/02/22 15:15:19  dibueno
Bug #8416#

Revision 1.18  2007/02/21 19:50:06  dibueno
Bug #8416#

Revision 1.17  2006/12/05 09:48:34  cercato
Bug #7737#

Revision 1.16  2006/09/25 14:24:04  domluc
1) Adicionado Filtro Valor
2) Alterad o ordem dos componetes para atender mel	hor usuario

Revision 1.15  2006/09/15 10:40:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

set_time_limit(0);

class FARRRelatorioValoresLancados extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FARRRelatorioValoresLancados()
{
    parent::Persistente();
    $this->setTabela('imobiliario.fn_rl_cadastro_imobiliario');

    $this->setCampoCod('');
    $this->setComplementoChave('');

    $this->AddCampo( 'numcgm'           ,'integer'  , true, '',false, false );
    $this->AddCampo( 'nom_cgm'          ,'varchar'  , true, '',false, false );
    $this->AddCampo( 'inscricao'        ,'integer'  , true, '',false, false );
    $this->AddCampo( 'exercicio'        ,'integer'  , true, '',false, false );
    $this->AddCampo( 'cod_grupo'        ,'integer'  , true, '',false, false );
    $this->AddCampo( 'descricao'        ,'varchar'  , true, '',false, false );
    $this->AddCampo( 'numeracao'        ,'varchar'  , true, '',false, false );
    $this->AddCampo( 'info_parcela'     ,'varchar'  , true, '',false, false );
    $this->AddCampo( 'valor'            ,'numeric'  , true, '',false, false );
    $this->AddCampo( 'data_pagamento'   ,'date'     , true, '',false, false );
    $this->AddCampo( 'data_vencimento'  ,'date'     , true, '',false, false );
    $this->AddCampo( 'juros'            ,'numeric'  , true, '',false, false );
    $this->AddCampo( 'multa'            ,'numeric'  , true, '',false, false );

}

function recuperaRelatorioAnalitico(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaRelatorioAnalitico( $stFiltro );
    $this->setDebug($stSql);
    //$this->debug();//exit;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioAnalitico($stFiltro)
{
    $stSql = "  select *                                                                        \n";
    $stSql .="  from                                                                            \n";
    $stSql .="      arrecadacao.fn_rl_arrecadacao_analitico (                                   \n";
    $stSql .="          ". $stFiltro ."                                                         \n";
    $stSql .="      ) as (                                                                      \n";
    $stSql .="          inscricao_lancamento        integer                                     \n";
    $stSql .="          , tipo_inscricao            varchar                                     \n";
    $stSql .="          , cod_lancamento            integer                                     \n";
    $stSql .="          , exercicio                 varchar                                     \n";
    $stSql .="          , cgm_contribuinte          integer                                     \n";
    $stSql .="          , nom_contribuinte          varchar                                     \n";
    $stSql .="          , cod_condominio            varchar                                     \n";
    $stSql .="          , nom_condominio            varchar                                     \n";
    $stSql .="          , origem                    varchar                                     \n";
    $stSql .="          , cod_grupo                 varchar                                     \n";
    $stSql .="          , cod_credito               varchar                                     \n";
    $stSql .="          , cod_especie               varchar                                     \n";
    $stSql .="          , cod_genero                varchar                                     \n";
    $stSql .="          , cod_natureza              varchar                                     \n";
    $stSql .="          , situacao_lancamento       varchar                                     \n";
    $stSql .="          , situacao_parcela          varchar                                     \n";
    $stSql .="          , parcela_valida            boolean                                     \n";
    $stSql .="          , numeracao                 varchar                                     \n";
    $stSql .="          , cod_parcela               integer                                     \n";
    $stSql .="          , nr_parcela                integer                                     \n";
    $stSql .="          , info_parcela              varchar                                     \n";
    $stSql .="          , parcela_vencimento        date                                        \n";
    $stSql .="          , parcela_vencimento_br     varchar                                     \n";
    $stSql .="          , parcela_valor_normal      numeric                                     \n";
    $stSql .="          , parcela_valor_desconto    numeric                                     \n";
    $stSql .="          , correcao_aberto           numeric                                     \n";
    $stSql .="          , juros_aberto              numeric                                     \n";
    $stSql .="          , multa_aberto              numeric                                     \n";
    $stSql .="          , soma_aberto               numeric                                     \n";
    $stSql .="          , pagamento_data            varchar                                     \n";
    $stSql .="          , pagamento_valor           numeric                                     \n";
    $stSql .="          , correcao_pago             numeric                                     \n";
    $stSql .="          , juros_pago                numeric                                     \n";
    $stSql .="          , multa_pago                numeric                                     \n";
    $stSql .="          , diferenca_pago            numeric                                     \n";
    $stSql .="          , soma_pago                 numeric                                     \n";
    $stSql .="          , atividade                 varchar                                     \n";
    $stSql .="          , diferenca_real            numeric                                     \n";
    $stSql .="      )                                                                           \n";

    return $stSql;

}

function recuperaRelatorioSintetico(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaRelatorioSintetico( $stFiltro  );

    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioSintetico($stFiltro)
{
    $stSql = "  SELECT                                                                      \n";
    $stSql .="      origem                                                                  \n";
    $stSql .="      , exercicio                                                             \n";
    $stSql .="      , sum( soma_aberto ) as soma_aberto                                     \n";
    $stSql .="      , sum( pagamento_valor ) as pagamento_valor                             \n";
    $stSql .="      , sum( correcao_pago ) as correcao_pago                                 \n";
    $stSql .="      , sum( juros_pago ) as juros_pago                                       \n";
    $stSql .="      , sum( multa_pago ) as multa_pago                                       \n";
    $stSql .="      , sum( diferenca_real ) as diferenca_real                               \n";
    $stSql .="      , sum( soma_pago ) as soma_pago                                         \n";

    $stSql .="  FROM                                                                        \n";
    $stSql .="      (                                                                       \n";
    $stSql .="          SELECT                                                              \n";
    $stSql .="              *                                                               \n";
    $stSql .="          FROM                                                                \n";
    $stSql .="              arrecadacao.fn_rl_arrecadacao_analitico (                       \n";
    $stSql .="                  ". $stFiltro ."                                             \n";
    $stSql .="          ) as (                                                              \n";
    $stSql .="              inscricao_lancamento        integer                             \n";
    $stSql .="              , tipo_inscricao            varchar                             \n";
    $stSql .="              , cod_lancamento            integer                             \n";
    $stSql .="              , exercicio                 varchar                             \n";
    $stSql .="              , cgm_contribuinte          integer                             \n";
    $stSql .="              , nom_contribuinte          varchar                             \n";
    $stSql .="              , cod_condominio            varchar                             \n";
    $stSql .="              , nom_condominio            varchar                             \n";
    $stSql .="              , origem                    varchar                             \n";
    $stSql .="              , cod_grupo                 varchar                             \n";
    $stSql .="              , cod_credito               varchar                             \n";
    $stSql .="              , cod_especie               varchar                             \n";
    $stSql .="              , cod_genero                varchar                             \n";
    $stSql .="              , cod_natureza              varchar                             \n";
    $stSql .="              , situacao_lancamento       varchar                             \n";
    $stSql .="              , situacao_parcela          varchar                             \n";
    $stSql .="              , parcela_valida            boolean                             \n";
    $stSql .="              , numeracao                 varchar                             \n";
    $stSql .="              , cod_parcela               integer                             \n";
    $stSql .="              , nr_parcela                integer                             \n";
    $stSql .="              , info_parcela              varchar                             \n";
    $stSql .="              , parcela_vencimento        date                                \n";
    $stSql .="              , parcela_vencimento_br     varchar                             \n";
    $stSql .="              , parcela_valor_normal      numeric                             \n";
    $stSql .="              , parcela_valor_desconto    numeric                             \n";
    $stSql .="              , correcao_aberto           numeric                             \n";
    $stSql .="              , juros_aberto              numeric                             \n";
    $stSql .="              , multa_aberto              numeric                             \n";
    $stSql .="              , soma_aberto               numeric                             \n";
    $stSql .="              , pagamento_data            varchar                             \n";
    $stSql .="              , pagamento_valor           numeric                             \n";
    $stSql .="              , correcao_pago             numeric                             \n";
    $stSql .="              , juros_pago                numeric                             \n";
    $stSql .="              , multa_pago                numeric                             \n";
    $stSql .="              , diferenca_pago            numeric                             \n";
    $stSql .="              , soma_pago                 numeric                             \n";
    $stSql .="              , atividade                 varchar                             \n";
    $stSql .="              , diferenca_real            numeric                             \n";
    $stSql .="          )                                                                   \n";
    $stSql .="      ) as busca                                                              \n";
    $stSql .="  GROUP BY                                                                    \n";
    $stSql .="      origem, exercicio                                                       \n";
    $stSql .="  ORDER BY                                                                    \n";
    $stSql .="      origem, exercicio                                                       \n";

    return $stSql;

}

function recuperaRelatorioPeriodico(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaRelatorioPeriodico( $stFiltro  );

    $this->setDebug($stSql);
    //$this->debug();exit;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioPeriodico($stFiltro)
{
    $stSql = "select                                       \n";
    $stSql .="    *                                        \n";
    $stSql .="from                                         \n";
    $stSql .="    ARRECADACAO.FN_RL_PERIODICO_ARRECADACAO (\n";
    $stSql .="        ". $stFiltro ."                      \n";
    $stSql .="    ) as (                                   \n";
    $stSql .="        cod_grupo           integer          \n";
    $stSql .="        ,descricao          varchar          \n";
    $stSql .="        , lancado           numeric          \n";
    $stSql .="        , pago              numeric          \n";
    $stSql .="        , aberto_vencido    numeric          \n";
    $stSql .="        , aberto_a_vencer   numeric          \n";
    $stSql .="    )                                        \n";

    return $stSql;
}

function recuperaRelatorioPeriodicoPorCGM(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaRelatorioPeriodicoPorCGM( $stFiltro  );

    $this->setDebug($stSql);
    //$this->debug();
    //exit;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioPeriodicoPorCGM($stFiltro)
{
    $stSql = "select                                      \n";
    $stSql .="    *                                       \n";
    $stSql .="from                                        \n";
    $stSql .="    ARRECADACAO.FN_RL_PERIODICO_ARRECADACAO(\n";
    $stSql .="        ". $stFiltro ."                     \n";
    $stSql .="    ) as (                                  \n";
//    $stSql .="        nome_cgm         VARCHAR(200),      \n";
//    $stSql .="        numcgm          INTEGER,            \n";
//    $stSql .="        cod_lancamento  INTEGER,            \n";
//    $stSql .="        descricao       VARCHAR(80),        \n";
//    $stSql .="        tipo_credito    VARCHAR,            \n";
//    $stSql .="        lancado   NUMERIC(14,2),            \n";
//    $stSql .="        pago      NUMERIC(14,2),            \n";
//    $stSql .="        aberto_vencido   NUMERIC(14,2),     \n";
//    $stSql .="        aberto_a_receber   NUMERIC(14,2)    \n";
    $stSql .="        cod       integer                  \n";
    $stSql .="        ,descricao character varying       \n";
    $stSql .="        ,cgm       text                    \n";
    $stSql .="        ,lancado     numeric               \n";
    $stSql .="        ,pago  numeric                     \n";
    $stSql .="        ,aberto_vencido   numeric          \n";
    $stSql .="        ,aberto_a_vencer   numeric         \n";
    $stSql .="    )                                       \n";

    return $stSql;
}

}
