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

    * Classe de mapeamento da tabela ARRECADACAO.PARCELA
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRParcela.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.PARCELA
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRParcela extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRParcela()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.parcela');

    $this->setCampoCod('cod_parcela');
    $this->setComplementoChave('');

    $this->AddCampo('cod_parcela','integer',true,'',true,false);
    $this->AddCampo('cod_lancamento','integer',false,'',false,true);
    $this->AddCampo('nr_parcela','integer',false,'',false,false);
    $this->AddCampo('vencimento','date',true,'',false,false);
    $this->AddCampo('valor','numeric',false,'14,2',false,false);

}

//########## FUNCOES TEMPORARIAS
function recuperaListaConsulta(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_parcela ";
    $stSql  = $this->montaRecuperaListaConsulta ($stFiltro);
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaListaConsulta($inCodLancamento)
{
    $stSql = "SELECT                                                                       \n";
    $stSql .= "    *                                                                        \n";
    $stSql .= "FROM                                                                         \n";
    $stSql .= "    arrecadacao.fn_consulta_parcela (".$inCodLancamento.",now()::date) as    \n";
    $stSql .= "    (   cod_lancamento integer,                                              \n";
    $stSql .= "        cod_parcela integer,                                                 \n";
    $stSql .= "        nr_parcela integer,                                                  \n";
    $stSql .= "        valor numeric,                                                       \n";
    $stSql .= "        vencimento varchar,                                                  \n";
    $stSql .= "        vencimento_original varchar,                                         \n";
    $stSql .= "        vencimento_us date,                                                  \n";
    $stSql .= "        info_parcela varchar,                                                \n";
    $stSql .= "        database date,                                                       \n";
    $stSql .= "        database_br varchar,                                                 \n";
    $stSql .= "        numeracao varchar,                                                   \n";
    $stSql .= "        exercicio varchar,                                                   \n";
    $stSql .= "        situacao varchar,                                                    \n";
    $stSql .= "        situacao_resumida varchar,                                           \n";
    $stSql .= "        numeracao_migracao varchar,                                          \n";
    $stSql .= "        prefixo varchar,                                                     \n";
    $stSql .= "        pagamento date,                                                      \n";
    $stSql .= "        ocorrencia_pagamento integer                                         \n";
    $stSql .= "    );                                                                       \n";

    return $stSql;
}

function recuperaListaConsultaRelatorio(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_parcela ";
    $stSql  = $this->montaRecuperaListaConsultaRelatorio ($stFiltro);
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaConsultaRelatorio($inCodLancamento)
{
    $stSql = "SELECT                                                                       \n";
    $stSql .= "    *                                                                        \n";
    $stSql .= "FROM                                                                         \n";
    $stSql .= "    arrecadacao.fn_consulta_parcela_relatorio (".$inCodLancamento.",now()::date) as    \n";
    $stSql .= "    (   cod_lancamento integer,                                              \n";
    $stSql .= "        cod_parcela integer,                                                 \n";
    $stSql .= "        nr_parcela integer,                                                  \n";
    $stSql .= "        valor numeric,                                                       \n";
    $stSql .= "        vencimento varchar,                                                  \n";
    $stSql .= "        vencimento_original varchar,                                         \n";
    $stSql .= "        vencimento_us date,                                                  \n";
    $stSql .= "        info_parcela varchar,                                                \n";
    $stSql .= "        database date,                                                       \n";
    $stSql .= "        database_br varchar,                                                 \n";
    $stSql .= "        numeracao varchar,                                                   \n";
    $stSql .= "        exercicio varchar,                                                   \n";
    $stSql .= "        situacao varchar,                                                    \n";
    $stSql .= "        situacao_resumida varchar,                                           \n";
    $stSql .= "        numeracao_migracao varchar,                                          \n";
    $stSql .= "        prefixo varchar,                                                     \n";
    $stSql .= "        pagamento date,                                                      \n";
    $stSql .= "        ocorrencia_pagamento integer                                         \n";
    $stSql .= "    );                                                                       \n";

    return $stSql;
}

function recuperaParcelaCarne(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY timestamp desc limit 1 ";
    $stSql  = $this->montaRecuperaParcelaCarne().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaParcelaCarne()
{
    $stSql =" select                                   				\n";
    $stSql .="      ap.cod_parcela,                     				\n";
    $stSql .="      ap.cod_lancamento,                  				\n";
    $stSql .="      ap.nr_parcela,                      				\n";
    $stSql .="      arrecadacao.fn_info_parcela(ap.cod_parcela) as info,\n";
    $stSql .="      ap.vencimento-'1997-10-07'::date as fator_vencimento,\n";
    $stSql .=" to_char(arrecadacao.fn_atualiza_data_vencimento (ap.vencimento),'dd/mm/yyyy') as vencimento, \n";
    $stSql .="      arrecadacao.fn_atualiza_data_vencimento (ap.vencimento) as vencimento_US,   \n";
    $stSql .="      ap.valor,                           				\n";
    $stSql .="		fn_busca_desconto_parcela( ap.cod_parcela, 			\n";
    $stSql .="      ap.vencimento) as desconto, \n";
    $stSql .="      mar.numeracao,                      				\n";
    $stSql .="      mar.timestamp as timestamp          				\n";
    $stSql .=" from                                     				\n";
    $stSql .="     	arrecadacao.parcela as ap           				\n";
    $stSql .="		INNER JOIN            								\n";
    $stSql .="     (select                              				\n";
    $stSql .="         cod_parcela,                     				\n";
    $stSql .="         numeracao,                       				\n";
    $stSql .="         exercicio,                       				\n";
    $stSql .="         max(timestamp) as timestamp      				\n";
    $stSql .="     from                                 				\n";
    $stSql .="         arrecadacao.carne                				\n";
    $stSql .="     group by                             				\n";
    $stSql .="         cod_parcela,                     				\n";
    $stSql .="         numeracao,                       				\n";
    $stSql .="         exercicio                        				\n";
    $stSql .="     ) as mar                             				\n";
    $stSql .="     ON mar.cod_parcela = ap.cod_parcela  				\n";

    return $stSql;
}

function recuperaInfoParcelaCarne(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY ap.cod_parcela";
    $stSql  = $this->montaRecuperaInfoParcelaCarne().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaInfoParcelaCarne()
{
    $stSql =" select                                   				\n";
    $stSql .="      ap.cod_parcela,                     				\n";
    $stSql .="      ap.cod_lancamento,                  				\n";
    $stSql .="      ap.nr_parcela,                      				\n";
    $stSql .="      arrecadacao.fn_info_parcela(ap.cod_parcela) as info,\n";
    $stSql .="      ap.vencimento-'1997-10-07'::date as fator_vencimento,\n";
    $stSql .="      to_char(arrecadacao.fn_atualiza_data_vencimento (ap.vencimento),'dd/mm/yyyy') as vencimento, \n";
    $stSql .="      arrecadacao.fn_atualiza_data_vencimento (ap.vencimento) as vencimento_US,   \n";
    $stSql .="      ap.valor,                           				\n";
    $stSql .="	    fn_busca_desconto_parcela( ap.cod_parcela, 			\n";
    $stSql .="      ap.vencimento) as desconto \n";
    $stSql .=" from                                     				\n";
    $stSql .="     	arrecadacao.parcela as ap           				\n";

    return $stSql;
}

function recuperaListaReemissaoConsulta(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_parcela ";
    $stSql  = $this->montaRecuperaListaReemissaoConsulta($stFiltro);
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaListaReemissaoConsulta($inCodParcela)
{
    $stSql = "SELECT                                                                       \n";
    $stSql .= "    *                                                                        \n";
    $stSql .= "FROM                                                                         \n";
    $stSql .= "     arrecadacao.fn_lista_reemissoes(".$inCodParcela.") as                   \n";
    $stSql .= "    (   cod_parcela integer,                                                 \n";
    $stSql .= "        numeracao   varchar,                                                 \n";
    $stSql .= "        vencimento  varchar,                                                 \n";
    $stSql .= "        data_pagamento date,                                                 \n";
    $stSql .= "        ocorrencia_pagamento integer                                         \n";
    $stSql .= "    );                                                                       \n";

    return $stSql;

}

function recuperaParcelaValor(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaParcelaValor().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaParcelaValor()
{
    $stSql =" select   valor
                     from  arrecadacao.parcela as ap            ";

    return $stSql;
}

function recuperaParcelasParaEmissao(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY timestamp desc limit 1 ";
    $stSql  = $this->montaRecuperaParcelasParaEmissao().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaParcelasParaEmissao()
{
    $stSql = "       select carne.numeracao                       						             	\n";
    $stSql .= "            , carne.impresso                                                          	\n";
    $stSql .= "            , calculo.exercicio                                                      	\n";
    $stSql .= "            , carne.cod_convenio                                                      	\n";
    $stSql .= "            , carne.cod_carteira                                                      	\n";
    $stSql .= "            , credito.cod_convenio as convenio_atual                                  	\n";
    $stSql .= "            , carteira.cod_carteira as carteira_atual                                 	\n";
    $stSql .= "            , parcela.cod_parcela                                                     	\n";
    $stSql .= "            , parcela.nr_parcela                                                      	\n";
    $stSql .= "            , arrecadacao.fn_info_parcela(parcela.cod_parcela) as info_parcela        	\n";
    $stSql .= "            , parcela.vencimento as vencimento_parcela                                	\n";
    $stSql .= "            , to_char(parcela.vencimento,'dd/mm/YYYY')::varchar as vencimento_parcela_br \n";
    $stSql .= "            , parcela.valor as valor_parcela                                             \n";
    $stSql .= "            , lancamento.cod_lancamento                                                  \n";
    $stSql .= "            , lancamento.vencimento as vencimento_lancamento                             \n";
    $stSql .= "            , lancamento.valor as valor_lancamento                                       \n";
    $stSql .= "            , calculo_cgm.numcgm                                                         \n";
    $stSql .= "            , ( select nom_cgm from sw_cgm where numcgm = calculo_cgm.numcgm )           \n";
    $stSql .= "            , arrecadacao.buscaVinculoLancamento ( lancamento.cod_lancamento, (calculo.exercicio)::int )::varchar as vinculo                                                      \n";
    $stSql .= "            , arrecadacao.buscaIdVinculo(lancamento.cod_lancamento, (calculo.exercicio)::int )::varchar as id_vinculo                                                                      		\n";
    $stSql .= "            , md5(arrecadacao.buscaVinculoLancamento ( lancamento.cod_lancamento, (calculo.exercicio)::int ))::varchar as chave_vinculo                                               \n";
    $stSql .= "            , calculo_grupo_credito.cod_grupo                                            \n";
    $stSql .= "            , COALESCE( imovel_calculo.inscricao_municipal, cadastro_economico_calculo.inscricao_economica ) AS inscricao                \n";
    $stSql .= "             from arrecadacao.calculo                                                                                                            \n";
    $stSql .= "           left join arrecadacao.cadastro_economico_calculo                                                                                                                   \n";
    $stSql .= "                   on cadastro_economico_calculo.cod_calculo = calculo.cod_calculo                                                                                             \n";

    $stSql .= "           left join arrecadacao.imovel_calculo                                                                                                                   \n";
    $stSql .= "                   on imovel_calculo.cod_calculo = calculo.cod_calculo                                                                                             \n";

    $stSql .= "           inner join arrecadacao.calculo_cgm                                                                                                                   \n";
    $stSql .= "                   on calculo_cgm.cod_calculo = calculo.cod_calculo                                                                                             \n";
    $stSql .= "           inner join arrecadacao.lancamento_calculo                                                                                                            \n";
    $stSql .= "                   on lancamento_calculo.cod_calculo = calculo.cod_calculo                                                                                      \n";
    $stSql .= "           inner join arrecadacao.lancamento                                                                                                                    \n";
    $stSql .= "                   on lancamento.cod_lancamento = lancamento_calculo.cod_lancamento                                                                             \n";
    $stSql .= "            left join arrecadacao.calculo_grupo_credito                                                                                                         \n";
    $stSql .= "                   on calculo_grupo_credito.cod_calculo = calculo.cod_calculo AND calculo_grupo_credito.ano_exercicio = calculo.exercicio                       \n";
    $stSql .= "                                                                                                                                                                \n";
    $stSql .= "   inner join arrecadacao.parcela                                                                                                                               \n";
    $stSql .= "           on parcela.cod_lancamento = lancamento.cod_lancamento                                                                                                \n";
    $stSql .= "   inner join arrecadacao.carne                                                                                                                                 \n";
    $stSql .= "           on carne.cod_parcela = parcela.cod_parcela                                                                                                           \n";
    $stSql .= "    left join arrecadacao.carne_devolucao                                                                                                                       \n";
    $stSql .= "           on carne_devolucao.numeracao = carne.numeracao                                                                                                       \n";
    $stSql .= "          and carne_devolucao.cod_convenio = carne.cod_convenio                                                                                                 \n";
    $stSql .= "    left join arrecadacao.pagamento                                                                                                                             \n";
    $stSql .= "           on pagamento.numeracao = carne.numeracao                                                                                                             \n";
    $stSql .= "          and pagamento.cod_convenio = carne.cod_convenio                                                                                                       \n";
    $stSql .= "   inner join monetario.credito                                                                                                                                 \n";
    $stSql .= "           on credito.cod_credito = calculo.cod_credito                                                                                                         \n";
    $stSql .= "          and credito.cod_especie = calculo.cod_especie                                                                                                         \n";
    $stSql .= "          and credito.cod_genero  = calculo.cod_genero                                                                                                          \n";
    $stSql .= "          and credito.cod_natureza= calculo.cod_natureza                                                                                                        \n";
    $stSql .= "    left join monetario.carteira                                                                                                                                \n";
    $stSql .= "           on carteira.cod_convenio = credito.cod_convenio                                                                                                      \n";
    $stSql .= "        where  /* carne.impresso = TRUE                                                                                                                         \n";
    $stSql .= "        and*/  carne_devolucao.numeracao is null                                                                                                                \n";
    $stSql .= "        and pagamento.numeracao is null                                                                                                                         \n";
    $stSql .= "        and calculo.cod_calculo = ( select alc.cod_calculo                                                                                                      \n";
    $stSql .= "                                      from arrecadacao.lancamento_calculo alc                                                                                   \n";
    $stSql .= "                                     where alc.cod_lancamento = lancamento.cod_lancamento                                                                       \n";
    $stSql .= "                                  order by cod_calculo desc limit 1 ) and  carne.numeracao = ( select ultima_numeracao.numeracao                                \n";
    $stSql .= "                                    from arrecadacao.carne as ultima_numeracao                                                                                  \n";
    $stSql .= "                                   where ultima_numeracao.cod_parcela = parcela.cod_parcela                                                                     \n";
    $stSql .= "                                order by timestamp desc limit 1 )                                                                                               \n";
    $stSql .= "                                                                                                                                                                \n";

    return $stSql;
}

function recuperaListaProrrogacao(&$rsRecordSet, $stFiltro, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = $this->montaRecuperaListaProrrogacao($stFiltro);
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaProrrogacao($inCodParcela)
{
    $stSql = "
                SELECT DISTINCT
                    ap.cod_parcela,
                    ap.cod_lancamento,
                    to_char(ap.vencimento, 'dd/mm/yyyy') AS vencimento
                FROM
                    arrecadacao.parcela AS ap

                INNER JOIN
                    arrecadacao.lancamento_calculo AS alc
                ON
                    alc.cod_lancamento = ap.cod_lancamento

                INNER JOIN
                    arrecadacao.calculo AS ac
                ON
                    ac.cod_calculo = alc.cod_calculo

                LEFT JOIN
                    arrecadacao.calculo_grupo_credito AS acgc
                ON
                    acgc.cod_calculo = ac.cod_calculo

                WHERE
                    ap.nr_parcela = 0
           \n";

    return $stSql;
}

} // end of class
?>
