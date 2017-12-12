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

    * Classe de mapeamento da tabela ARRECADACAO.LANCAMENTO
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRLancamento.class.php 62094 2015-03-29 00:14:58Z lisiane $

* Casos de uso: uc-05.03.05
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.LANCAMENTO
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRLancamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRLancamento()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.lancamento');

    $this->setCampoCod('cod_lancamento');
    $this->setComplementoChave('');

    $this->AddCampo('cod_lancamento'    ,'integer'  ,true,'',true   ,false);
    //$this->AddCampo('numcgm','integer',true,'',false,true);
    $this->AddCampo('vencimento'        ,'date'     ,true,'',false  ,false);
    $this->AddCampo('total_parcelas'    ,'integer'  ,true,'',false  ,false);
    $this->AddCampo('ativo'             ,'boolean'  ,true,'',false  ,false);
    $this->AddCampo('observacao'        ,'text'     ,true,'',false  ,false);
    $this->AddCampo('observacao_sistema','text'     ,true,'',false  ,false);
    $this->AddCampo('valor'             ,'numeric'  ,true,'',false  ,false);
    $this->AddCampo('divida'            ,'boolean'  ,true,'',false  ,false);

}

function recuperaListaConsulta(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_lancamento ";
    $stSql  = $this->montaRecuperaListaConsulta( $stFiltro ).$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaConsulta($stFiltro)
{
    $stSql .= " select                                                                              \n";
    $stSql .= "     lancamentos.*                                                                   \n";

    $stSql .= " FROM                                                                                \n";
    $stSql .= "(                                                                                    \n";
    $stSql .= "     select DISTINCT grupo.cod_grupo,                                                \n";
    $stSql .= "     al.cod_lancamento,                                                              \n";
    $stSql .= "     grupo.cod_modulo,                                                               \n";
    $stSql .= "     case                                                                            \n";
    $stSql .= "          when grupo.cod_modulo is not  null then grupo.descricao ||'/'||grupo.ano_exercicio         \n";
    $stSql .= "          else mc.descricao_credito                                                                                                 \n";
    $stSql .= "     end as origem,                                                                                                                        \n";
    $stSql .= "     (                                                                                                                                              \n";
    $stSql .= "      case                                                                                                                                       \n";
    $stSql .= "          when ic.cod_calculo  is not null then ic.inscricao_municipal                                               \n";
    $stSql .= "          when cec.cod_calculo is not null then cec.inscricao_economica                                        \n";
    $stSql .= "       end                                                                                                                                       \n";
    $stSql .= "     ) as inscricao,                                                                                                                         \n";
    $stSql .= "     ( arrecadacao.buscaCgmLancamento (alc.cod_lancamento)||' - '||                                         \n";
    $stSql .= "       arrecadacao.buscaContribuinteLancamento(alc.cod_lancamento)                                      \n";
    $stSql .= "    )::varchar as proprietarios,                                                                                                      \n";
    $stSql .= "     case                                                                                                                                        \n";
    $stSql .= "     when ic.cod_calculo  is not null then                                                                                       \n";
    $stSql .= "         arrecadacao.fn_consulta_endereco_imovel(ic.inscricao_municipal)                                    \n";
    $stSql .= "     when cec.cod_calculo is not null then                                                                                     \n";
    $stSql .= "         arrecadacao.fn_consulta_endereco_empresa(cec.inscricao_economica)                           \n";
    $stSql .= "     else 'Nao Encontrado'                                                                                                             \n";
    $stSql .= "     end as dados_complementares,                                                                                             \n";
    $stSql .= "     arrecadacao.fn_ultimo_venal_por_im_lanc(ic.inscricao_municipal, alc.cod_lancamento) as venal,    \n";
    $stSql .= "     coalesce(al.total_parcelas,0)::int as num_parcelas,                                                                         \n";
    $stSql .= "     coalesce(arrecadacao.fn_num_unicas(alc.cod_lancamento),0)::int as num_unicas,                         \n";
    $stSql .="      arrecadacao.buscaValorLancadoLancamento ( al.cod_lancamento, ac.exercicio)::numeric(14,2) as valor_lancamento, \n";
    $stSql .="      arrecadacao.buscaValorCalculadoLancamento ( al.cod_lancamento, ac.exercicio)::numeric(14,2) as valor_calculado, \n";
    $stSql .= "     to_char(ac.timestamp, 'dd/mm/yyyy HH12:MI' ) AS timestamp_calculo,                                                \n";

    $stSql .= "     case  when ac.calculado = true then 'Calculado'::text                                                                        \n";
    $stSql .= "     else 'Manual'::text                                                                                                                               \n";
    $stSql .= "     end as tipo_calculo                                                                                                                             \n";

    $stSql .= " FROM                                                                                                                                                             \n";
    $stSql .= "     arrecadacao.calculo_cgm cgm                                                                                                                  \n";
    $stSql .= "     INNER JOIN arrecadacao.calculo ac ON cgm.cod_calculo = ac.cod_calculo                                                \n";
    $stSql .= "     INNER JOIN arrecadacao.lancamento_calculo as alc ON ac.cod_calculo = alc.cod_calculo                         \n";
    $stSql .= "     INNER JOIN arrecadacao.lancamento as al ON al.cod_lancamento = alc.cod_lancamento                         \n";
    $stSql .= "     LEFT JOIN   (    SELECT gc.cod_grupo, gc.descricao, gc.ano_exercicio, cgc.cod_calculo, m.cod_modulo     \n";
    $stSql .= "                           FROM arrecadacao.calculo_grupo_credito cgc                                                                     \n";
    $stSql .= "                           INNER JOIN arrecadacao.grupo_credito gc ON gc.cod_grupo     = cgc.cod_grupo           \n";
    $stSql .= "                                                                                                   AND gc.ano_exercicio = cgc.ano_exercicio              \n";
    $stSql .= "                           INNER JOIN administracao.modulo m       ON m.cod_modulo     = gc.cod_modulo                  \n";
    $stSql .= "                       ) as grupo ON grupo.cod_calculo = ac.cod_calculo AND grupo.ano_exercicio = ac.exercicio     \n";
    $stSql .= "    LEFT JOIN arrecadacao.imovel_calculo ic                             ON ic.cod_calculo     = ac.cod_calculo               \n";
    $stSql .= "    LEFT JOIN arrecadacao.cadastro_economico_calculo cec  ON cec.cod_calculo  = ac.cod_calculo               \n";
    $stSql .= "    INNER JOIN monetario.credito mc ON mc.cod_credito = ac.cod_credito                                                       \n";
    $stSql .= "                     AND mc.cod_especie = ac.cod_especie AND mc.cod_genero = ac.cod_genero                         \n";
    $stSql .= "                     AND mc.cod_natureza = ac.cod_natureza                                                                                  \n";
    $stSql .= "    ". $stFiltro ."                                                                                                                                                \n";

    $stSql .= " ) as lancamentos                                                                                                                                           \n";

    return $stSql;
}

function recuperaCalculosCredito(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_lancamento ";
    $stSql  = $this->montaRecuperaCalculosCredito().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCalculosCredito()
{
    $stSql  = " SELECT                                                                                  \n";
    $stSql .= "     al.cod_lancamento,                                                                  \n";
    $stSql .= "     agc.cod_grupo,                                                                      \n";
    $stSql .= "     mc.descricao_credito,                                                               \n";
    $stSql .= "     ac.cod_credito,                                                                     \n";
    $stSql .= "     ac.cod_especie,                                                                     \n";
    $stSql .= "     ac.cod_genero,                                                                      \n";
    $stSql .= "     ac.cod_natureza,                                                                    \n";
    $stSql .= "     alc.cod_calculo,                                                                    \n";
    $stSql .= "    -- ac.exercicio,                                                                       \n";
    $stSql .= "     CASE WHEN acgc.cod_grupo IS NOT NULL THEN      \n";
    $stSql .= "               agc.ano_exercicio                                                    \n";         
    $stSql .= "      ELSE                                                                                          \n";
    $stSql .= "              ac.exercicio                                                                      \n";
    $stSql .= "     END as exercicio,                                                                       \n";
    $stSql .= "     ac.valor as valor_calculado,                                                        \n";
    $stSql .= "     alc.valor ,                                                                         \n";
    $stSql .= "     split_part ( monetario.fn_busca_mascara_credito( mc.cod_credito, mc.cod_especie,    \n";
    $stSql .= "     mc.cod_genero, mc.cod_natureza ),'§', 1) as codigo_composto                         \n";
    $stSql .= " FROM                                                                                    \n";
    $stSql .= "     arrecadacao.lancamento as al                                                        \n";
    $stSql .= "     INNER JOIN arrecadacao.lancamento_calculo as alc                                    \n";
    $stSql .= "     ON alc.cod_lancamento = al.cod_lancamento                                           \n";
    $stSql .= "     INNER JOIN arrecadacao.calculo as ac                                                \n";
    $stSql .= "     ON ac.cod_calculo = alc.cod_calculo                                                 \n";
    $stSql .= "     LEFT JOIN arrecadacao.calculo_grupo_credito as acgc                                 \n";
    $stSql .= "     ON acgc.cod_calculo = alc.cod_calculo                                               \n";
    $stSql .= "     --AND acgc.ano_exercicio = ac.exercicio                                               \n";
    $stSql .= "     LEFT JOIN arrecadacao.grupo_credito as agc                                          \n";
    $stSql .= "     ON agc.cod_grupo = acgc.cod_grupo                                                   \n";
    $stSql .= "     AND agc.ano_exercicio = acgc.ano_exercicio                                          \n";
    $stSql .= "     INNER JOIN monetario.credito as mc                                                  \n";
    $stSql .= "     ON ac.cod_credito   = mc.cod_credito                                                \n";
    $stSql .= "     AND ac.cod_especie  = mc.cod_especie                                                \n";
    $stSql .= "     AND ac.cod_genero   = mc.cod_genero                                                 \n";
    $stSql .= "     AND ac.cod_natureza = mc.cod_natureza                                               \n";

    return $stSql;

}

//funcao deixa de existir, passando a ter apenas a recuperaCalculosCredito
function recuperaCalculosCreditoIndividual(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_lancamento ";
    $stSql  = $this->montaRecuperaCalculosCreditoIndividual().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCalculosCreditoIndividual()
{
    $stSql  = " SELECT                                                                      \n";
    $stSql .= "     l.cod_lancamento,                                                       \n";
    $stSql .= "     ac.cod_credito,                                                         \n";
    $stSql .= "     ac.cod_especie,                                                         \n";
    $stSql .= "     ac.cod_genero,                                                          \n";
    $stSql .= "     ac.cod_natureza,                                                        \n";
    $stSql .= "     mc.descricao_credito,                                                   \n";
    $stSql .= "     ac.exercicio,                                                           \n";
    $stSql .= "     ac.valor as valor_calculado,                                            \n";
    $stSql  .= "    lc.valor                                                                \n";
    $stSql .= " FROM                                                                        \n";
    $stSql .= "     arrecadacao.lancamento l,                                               \n";
    $stSql .= "     arrecadacao.lancamento_calculo lc,                                      \n";
    $stSql .= "     arrecadacao.calculo ac,                                                 \n";
    $stSql .= "     monetario.credito mc                                                    \n";
    $stSql .= " WHERE                                                                       \n";
    $stSql .= "     lc.cod_lancamento   = l.cod_lancamento   AND                            \n";
    $stSql .= "     ac.cod_calculo       = lc.cod_calculo    AND                            \n";
    $stSql .= "     mc.cod_credito      = ac.cod_credito     AND                            \n";
    $stSql .= "     mc.cod_especie      = ac.cod_especie     AND                            \n";
    $stSql .= "     mc.cod_genero       = ac.cod_genero      AND                            \n";
    $stSql .= "     mc.cod_natureza     = ac.cod_natureza                                   \n";

    return $stSql;

}

function recuperaSomaParcelasPagasImovel(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : "";
    $stSql  = $this->montaRecuperaSomaParcelasPagasImovel($stFiltro).$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaSomaParcelasPagasImovel($stFiltro)
{
    $stSql = "SELECT arrecadacao.somaParcelasPagasImovel(".$stFiltro.") as valor";

    return $stSql;
}

function recuperaSomaParcelasPagasLancamento(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : "";
    $stSql  = $this->montaRecuperaSomaParcelasPagasLancamento($stFiltro).$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaSomaParcelasPagasLancamento($stFiltro)
{
    $stSql = "SELECT arrecadacao.somaParcelasPagasLancamento(".$stFiltro.") as valor";

    return $stSql;

}

function recuperaSomaParcelasUnicasPagasImovel(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : "";
    $stSql  = $this->montaRecuperaSomaParcelasUnicasPagasImovel($stFiltro).$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaSomaParcelasUnicasPagasImovel($stFiltro)
{
    $stSql = "SELECT arrecadacao.somaParcelasUnicasPagasImovel(".$stFiltro.") as valor";

    return $stSql;

}

function recuperaSomaParcelasUnicasPagasLancamento(&$rsRecordSet, $stFiltro = "",$stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : "";
    $stSql  = $this->montaRecuperaSomaParcelasUnicasPagasLancamento($stFiltro).$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaSomaParcelasUnicasPagasLancamento($stFiltro)
{
    $stSql = "SELECT arrecadacao.somaParcelasUnicasPagasLancamento(".$stFiltro.") as valor";

    return $stSql;

}

function recuperaValorLancamentoAnterior(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : "";
    $stSql  = $this->montaRecuperaValorLancamentoAnterior($stFiltro).$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaValorLancamentoAnterior($stFiltro)
{
    $stSql = "SELECT arrecadacao.buscaValorLancamentoAnterior(".$stFiltro.") as valor";

    return $stSql;

}

function recuperaValorLancamentoAnteriorGrupo(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : "";
    $stSql  = $this->montaRecuperaValorLancamentoAnteriorGrupo($stFiltro).$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaValorLancamentoAnteriorGrupo($stFiltro)
{
    $stSql = "SELECT arrecadacao.buscaValorLancamentoAnteriorGrupo(".$stFiltro.") as valor";

    return $stSql;
}

function recuperaLancamentoAnterior(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : "";
    $stSql  = $this->montaRecuperaLancamentoAnterior($stFiltro).$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLancamentoAnterior($stFiltro)
{
    $stSql = "SELECT arrecadacao.buscaLancamentoAnterior(".$stFiltro.") as valor";

    return $stSql;
}

function recuperaParcelasPorLancamento(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_lancamento ";
    $stSql  = $this->montaRecuperaParcelasPorLancamento().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaParcelasPorLancamento()
{
    $stSql  = " SELECT numeracao, cod_convenio                          \n";
    $stSql .= " FROM   arrecadacao.parcela a                            \n";
    $stSql .= "      , arrecadacao.carne b                              \n";
    $stSql .= " WHERE                                                   \n";
    $stSql .= "     a.cod_parcela = b.cod_parcela                       \n";

    return $stSql;
}

function recuperaParcelasPorLancamentoNPagos(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : "";
    $stSql  = $this->montaRecuperaParcelasPorLancamentoNPagos().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaParcelasPorLancamentoNPagos()
{
    $stSql .= "     select b.numeracao, b.cod_convenio 				\n";
    $stSql .= "       from arrecadacao.lancamento l					\n";
    $stSql .= "inner join arrecadacao.parcela a						\n";
    $stSql .= "         on a.cod_lancamento = l.cod_lancamento		\n";
    $stSql .= " inner join arrecadacao.carne b						\n";
    $stSql .= "         on b.cod_parcela = a.cod_parcela			\n";
    $stSql .= "  left join arrecadacao.pagamento c					\n";
    $stSql .= "         on c.numeracao = b.numeracao				\n";
    $stSql .= "        and c.cod_convenio = b.cod_convenio			\n";
    $stSql .= "  left join arrecadacao.carne_devolucao d			\n";
    $stSql .= "         on d.numeracao = b.numeracao				\n";
    $stSql .= "        and d.cod_convenio = b.cod_convenio			\n";
    $stSql .= "      where c.numeracao is null						\n";
    $stSql .= "        and c.cod_convenio is null					\n";
    $stSql .= "        and d.numeracao is null						\n";
    $stSql .= "        and d.cod_convenio is null					\n";

    return $stSql;
}

function atualizaLancamento(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaAtualizaLancamento();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaAtualizaLancamento()
{
    $stSql .= "SELECT                                                                          \n";
    $stSql .= " arrecadacao.fn_atualiza_lancamento( '".$this->getDado('cod_lancamento' )."'    \n";
    $stSql .= "                                  ,'".$this->getDado('numeracao' )."'           \n";
    $stSql .= "                                  ,'".$this->getDado('valor' )."'               \n";
    $stSql .=");                                                                               \n";

    return $stSql;
}

function recuperaObservacaoLancamento(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_lancamento ";
    $stSql  = $this->montaRecuperaObservacaoLancamento().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaObservacaoLancamento()
{
    $stSql  = "select observacao, observacao_sistema from arrecadacao.lancamento ";

    return $stSql;
}

function recuperaProcessoLancamento(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_lancamento ";
    $stSql  = $this->montaRecuperaProcessoLancamento().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaProcessoLancamento()
{
    $stSql  = "     select alp.cod_lancamento                                           \n";
    $stSql .= "          , swp.cod_processo                                             \n";
    $stSql .= "          , swp.ano_exercicio                                            \n";
    $stSql .= "          , swp.resumo_assunto                                           \n";
    $stSql .= "          , swp.observacoes                                              \n";
    $stSql .= "       from arrecadacao.lancamento_processo alp                          \n";
    $stSql .= " inner join sw_processo swp                                              \n";
    $stSql .= "         on swp.cod_processo = alp.cod_processo                          \n";
    $stSql .= "        and swp.ano_exercicio = alp.ano_exercicio                        \n";

    return $stSql;

}

function recuperaRelatorioLancamento(&$rsRecordSet, $stFiltro = "", $boTransacao = "", $stOrder = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaRelatorioLancamento().$stFiltro.$stOrder;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioLancamento()
{
    $stSql = "
        SELECT *
          FROM ( SELECT DISTINCT lancamento.cod_lancamento
                      , to_char( lancamento.vencimento, 'dd/mm/yyyy' ) AS vencimento
                      , lancamento.total_parcelas
                      , lancamento.valor
                      , CAST((SELECT array_to_string( ARRAY( select numcgm from sw_cgm where numcgm IN ( SELECT numcgm FROM arrecadacao.calculo_cgm WHERE cod_calculo = lancamento_calculo.cod_calculo)), '/' ) ) AS VARCHAR) AS numcgm
                      , CAST((SELECT array_to_string( ARRAY( select nom_cgm from sw_cgm where numcgm IN ( SELECT numcgm FROM arrecadacao.calculo_cgm WHERE cod_calculo = lancamento_calculo.cod_calculo)), '/' ) ) AS VARCHAR) AS nom_cgm
                      , COALESCE ( cadastro_economico_calculo.inscricao_economica, imovel_calculo.inscricao_municipal, 0 ) AS inscricao
                      , CASE WHEN parcela_desconto.valor IS NOT NULL THEN
                                parcela_desconto.valor
                        ELSE
                                parcela.valor
                        END AS valor_parcela
                      , COALESCE ( carne.numeracao::varchar, '    -    ' ) AS numeracao
                      , COALESCE ( ( CASE WHEN parcela.nr_parcela = 0 THEN
                                             'única'::text
                                     ELSE
                                             parcela.nr_parcela||'/'||lancamento.total_parcelas::text
                                     END
                                   ), ' - ' ) AS nr_parcela
                      , COALESCE( to_char( parcela.vencimento, 'dd/mm/yyyy' ), ' - ' ) AS data_vencimento
                      , COALESCE ( ( CASE WHEN parcela.nr_parcela = 0 THEN
                                             0
                                     ELSE
                                             parcela.nr_parcela
                                     END
                                   ), 0) AS ordenacao

                   FROM arrecadacao.lancamento
             INNER JOIN arrecadacao.lancamento_calculo
                     ON lancamento_calculo.cod_lancamento = lancamento.cod_lancamento

              LEFT JOIN arrecadacao.imovel_calculo
                     ON imovel_calculo.cod_calculo = lancamento_calculo.cod_calculo

              LEFT JOIN arrecadacao.cadastro_economico_calculo
                     ON cadastro_economico_calculo.cod_calculo = lancamento_calculo.cod_calculo

              LEFT JOIN arrecadacao.parcela
                     ON parcela.cod_lancamento = lancamento.cod_lancamento

              LEFT JOIN arrecadacao.parcela_desconto
                     ON parcela_desconto.cod_parcela = parcela.cod_parcela


              LEFT JOIN arrecadacao.carne
                     ON carne.cod_parcela = parcela.cod_parcela
            ) AS lancamento

    ";

    return $stSql;
}

function recuperaRelatorioLancamentoGeral(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaRelatorioLancamentoGeral().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioLancamentoGeral()
{
    $stSql    = "SELECT                                                                 		\n";
    $stSql   .= "	alan.cod_lancamento,														\n";
    $stSql   .= "   to_char( alan.vencimento, 'dd/mm/yyyy' ) AS vencimento,						\n";
    $stSql   .= "	arrecadacao.fn_total_parcelas( alan.cod_lancamento ) as total_parcelas,		\n";
    $stSql   .= "   alan.valor,																	\n";
    $stSql   .= "	arrecadacao.buscaCgmLancamento( alan.cod_lancamento ) as numcgm,			\n";
    $stSql   .= "	arrecadacao.buscaContribuinteLancamento( alan.cod_lancamento ) as nom_cgm,	\n";
    $stSql   .= "	arrecadacao.buscaInscricaoLancamento( alan.cod_lancamento) as inscricao		\n";
    $stSql   .= "FROM																			\n";
    $stSql   .= "	arrecadacao.lancamento as alan												\n";
    $stSql   .= "WHERE                   														\n";

    return $stSql;

}

//recupera lancamento anterior por credito por inscricao economico
function recuperaLancamentoAnteriorCreditoCadEco(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = $this->montaRecuperaLancamentoAnteriorCreditoEco($stFiltro);
    $this->setDebug($stSql);

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLancamentoAnteriorCreditoEco($stFiltro)
{
    #$stSql = "SELECT arrecadacao.buscaLancamentoAnterior(".$stFiltro.") as valor";

    $stSql  = "select                                                                           \n";
    $stSql .= "    d.valor||'-'||d.cod_lancamento||'-'||d.total_parcelas||'-'||to_char( d.vencimento, 'dd/mm/yyyy' ) AS valor                                                                                  \n";
    $stSql .= "from                                                                             \n";
    $stSql .= "    arrecadacao.cadastro_economico_calculo a                                     \n";

    $stSql .= "LEFT JOIN arrecadacao.calculo_grupo_credito as acgc                              \n";
    $stSql .= "ON acgc.cod_calculo = a.cod_calculo                                              \n";

    $stSql .= "INNER JOIN arrecadacao.calculo as b                                              \n";
    $stSql .= "ON b.cod_calculo = a.cod_calculo                                                 \n";

    $stSql .= "INNER JOIN arrecadacao.lancamento_calculo c                                      \n";
    $stSql .= "ON c.cod_calculo = a.cod_calculo                                                 \n";

    $stSql .= "INNER JOIN arrecadacao.lancamento d                                              \n";
    $stSql .= "ON d.cod_lancamento = c.cod_lancamento                                           \n";

    $stSql .= $stFiltro;
    $stSql .= " AND acgc.cod_calculo IS NULL                                                    \n";

    $stSql .= "ORDER BY                                                                         \n";
    $stSql .= "    c.cod_lancamento desc                                                        \n";

    $stSql .= "LIMIT 1                                                                          \n";

    return $stSql;
}

//recupera lancamento anterior por credito por inscricao economico
function recuperaLancamentoAnteriorGrupoEco(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = $this->montaRecuperaLancamentoAnteriorGrupoEco($stFiltro);
    $this->setDebug($stSql);

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaLancamentoAnteriorGrupoEco($stFiltro)
{
    $stSql  = "select                                                                                   \n";
    $stSql .= "    al.valor||'-'||al.cod_lancamento||'-'||al.total_parcelas||'-'||to_char( al.vencimento, 'dd/mm/yyyy' ) AS valor                                                                             \n";
    $stSql .= "from                                                                                     \n";
    $stSql .= "    arrecadacao.cadastro_economico_calculo cec                                           \n";

    $stSql .= "INNER JOIN arrecadacao.calculo as calc                                                   \n";
    $stSql .= "ON calc.cod_calculo = cec.cod_calculo                                                    \n";

    $stSql .= "INNER JOIN arrecadacao.lancamento_calculo alc                                            \n";
    $stSql .= "ON alc.cod_calculo = calc.cod_calculo                                                    \n";

    $stSql .= "INNER JOIN arrecadacao.lancamento al                                                     \n";
    $stSql .= "ON al.cod_lancamento = alc.cod_lancamento                                                \n";

    $stSql .= "INNER JOIN arrecadacao.calculo_grupo_credito as acgc                                     \n";
    $stSql .= "ON acgc.cod_calculo = calc.cod_calculo                                                   \n";

    $stSql .= $stFiltro;

    $stSql .= "ORDER BY                                                                                 \n";
    $stSql .= "    al.cod_lancamento desc                                                               \n";

    $stSql .= "LIMIT 1                                                                                  \n";

    return $stSql;

}

//recupera lancamento anterior por credito por inscricao municipal
function recuperaLancamentoAnteriorCreditoImovel(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = $this->montaRecuperaLancamentoAnteriorCreditoImovel($stFiltro);
    $this->setDebug($stSql);

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLancamentoAnteriorCreditoImovel($stFiltro)
{
    $stSql  = "select                                                                           \n";
    $stSql .= "    d.valor||'-'||d.cod_lancamento||'-'||d.total_parcelas||'-'||to_char( d.vencimento, 'dd/mm/yyyy' ) AS valor                                                                                  \n";
    $stSql .= "from                                                                             \n";
    $stSql .= "    arrecadacao.imovel_calculo a                                                 \n";

    $stSql .= " LEFT JOIN arrecadacao.calculo_grupo_credito as acgc                             \n";
    $stSql .= " ON acgc.cod_calculo = a.cod_calculo                                             \n";

    $stSql .= "INNER JOIN arrecadacao.calculo as b                                              \n";
    $stSql .= "ON b.cod_calculo = a.cod_calculo                                                 \n";

    $stSql .= "INNER JOIN arrecadacao.lancamento_calculo c                                      \n";
    $stSql .= "ON c.cod_calculo = a.cod_calculo                                                 \n";

    $stSql .= "INNER JOIN arrecadacao.lancamento d                                              \n";
    $stSql .= "ON d.cod_lancamento = c.cod_lancamento                                           \n";

    $stSql .= $stFiltro;
    $stSql .= " AND acgc.cod_calculo IS NULL                                                    \n";

    $stSql .= "ORDER BY                                                                         \n";
    $stSql .= "    c.cod_lancamento desc                                                        \n";

    $stSql .= "LIMIT 1                                                                          \n";

    return $stSql;

}

//recupera lancamento anterior por credito por inscricao municipal
function recuperaLancamentoAnteriorGrupoImovel(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = $this->montaRecuperaLancamentoAnteriorGrupoImovel($stFiltro);
    $this->setDebug($stSql);

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaLancamentoAnteriorGrupoImovel($stFiltro)
{
    $stSql  = "select                                                                                   \n";
    $stSql .= "    al.valor||'-'||al.cod_lancamento||'-'||al.total_parcelas||'-'||to_char( al.vencimento, 'dd/mm/yyyy' ) AS valor                                                                             \n";
    $stSql .= "from                                                                                     \n";
    $stSql .= "    arrecadacao.imovel_calculo aic                                                       \n";

    $stSql .= "INNER JOIN arrecadacao.calculo as calc                                                   \n";
    $stSql .= "ON calc.cod_calculo = aic.cod_calculo                                                    \n";

    $stSql .= "INNER JOIN arrecadacao.lancamento_calculo alc                                            \n";
    $stSql .= "ON alc.cod_calculo = calc.cod_calculo                                                    \n";

    $stSql .= "INNER JOIN arrecadacao.lancamento al                                                     \n";
    $stSql .= "ON al.cod_lancamento = alc.cod_lancamento                                                \n";

    $stSql .= "INNER JOIN arrecadacao.calculo_grupo_credito as acgc                                     \n";
    $stSql .= "ON acgc.cod_calculo = calc.cod_calculo                                                   \n";

    $stSql .= $stFiltro;

    $stSql .= "ORDER BY                                                                                 \n";
    $stSql .= "    al.cod_lancamento desc                                                               \n";

    $stSql .= "LIMIT 1                                                                                  \n";

    return $stSql;

}

//recupera lancamento anterior por credito por CGM
function recuperaLancamentoAnteriorCreditoCGM(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = $this->montaRecuperaLancamentoAnteriorCreditoCGM($stFiltro);
    $this->setDebug($stSql);

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLancamentoAnteriorCreditoCGM($stFiltro)
{
    $stSql  = "select                                                                               \n";
    $stSql .= "    al.valor||'-'||al.cod_lancamento||'-'||al.total_parcelas||'-'||to_char( al.vencimento, 'dd/mm/yyyy' ) AS valor                                                                         \n";
    $stSql .= "from                                                                                 \n";
    $stSql .= "    arrecadacao.calculo_cgm as ccgm                                                  \n";

    $stSql .= "     LEFT JOIN arrecadacao.calculo_grupo_credito as acgc                             \n";
    $stSql .= "     ON acgc.cod_calculo = ccgm.cod_calculo                                          \n";
    $stSql .= "     ON aic.cod_calculo = ccgm.cod_calculo                                           \n";
    $stSql .= "     LEFT JOIN arrecadacao.imovel_calculo as aic                                     \n";
    $stSql .= "     ON aic.cod_calculo = ccgm.cod_calculo                                           \n";
    $stSql .= "     LEFT JOIN arrecadacao.cadastro_economico_calculo as cec                         \n";
    $stSql .= "     ON cec.cod_calculo = ccgm.cod_calculo                                           \n";

    $stSql .= "INNER JOIN arrecadacao.calculo as calc                                               \n";
    $stSql .= "ON calc.cod_calculo = ccgm.cod_calculo                                               \n";

    $stSql .= "INNER JOIN arrecadacao.lancamento_calculo as alc                                     \n";
    $stSql .= "ON c.cod_calculo = a.cod_calculo                                                     \n";

    $stSql .= "INNER JOIN arrecadacao.lancamento al                                                 \n";
    $stSql .= "ON d.cod_lancamento = c.cod_lancamento                                               \n";

    $stSql .= $stFiltro;
    $stSql .= " AND acgc.cod_calculo IS NULL AND aic.cod_calculo IS NULL                            \n";
    $stSql .= " AND cec.cod_calculo IS NULL                                                         \n";

    $stSql .= "ORDER BY                                                                             \n";
    $stSql .= "    c.cod_lancamento desc                                                            \n";

    $stSql .= "limit 1 \n";

    return $stSql;
}

//recupera lancamento anterior por credito por CGM
function recuperaLancamentoAnteriorGrupoCGM(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = $this->montaRecuperaLancamentoAnteriorGrupoCGM($stFiltro);
    $this->setDebug($stSql);

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLancamentoAnteriorGrupoCGM($stFiltro)
{
    $stSql  = "select                                                                                   \n";
    $stSql .= "    al.valor||'-'||al.cod_lancamento||'-'||al.total_parcelas||'-'||to_char( al.vencimento, 'dd/mm/yyyy' ) AS valor                                                                             \n";
    $stSql .= "from                                                                                     \n";
    $stSql .= "    arrecadacao.calculo_cgm as ccgm                                                      \n";

    $stSql .= "INNER JOIN arrecadacao.calculo as calc                                                   \n";
    $stSql .= "ON calc.cod_calculo = ccgm.cod_calculo                                                    \n";

    $stSql .= "INNER JOIN arrecadacao.lancamento_calculo alc                                            \n";
    $stSql .= "ON alc.cod_calculo = calc.cod_calculo                                                    \n";

    $stSql .= "INNER JOIN arrecadacao.lancamento al                                                     \n";
    $stSql .= "ON al.cod_lancamento = alc.cod_lancamento                                                \n";

    $stSql .= "INNER JOIN arrecadacao.calculo_grupo_credito as acgc                                     \n";
    $stSql .= "ON acgc.cod_calculo = calc.cod_calculo                                                   \n";

    $stSql .= $stFiltro;

    $stSql .= "ORDER BY                                                                                 \n";
    $stSql .= "    al.cod_lancamento desc                                                               \n";

    $stSql .= "LIMIT 1                                                                                  \n";

    return $stSql;

}

function recuperaCalculosPorCredito(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_lancamento ";
    $stSql  = $this->montaRecuperaCalculosPorCredito().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCalculosPorCredito()
{
    $stSql  = " SELECT                                                                  \n";
    $stSql .= "     alan.cod_lancamento,                                                \n";
    $stSql .= "     mc.descricao_credito,                                               \n";
    $stSql .= "     ac.cod_credito,                                                     \n";
    $stSql .= "     ac.cod_especie,                                                     \n";
    $stSql .= "     ac.cod_genero,                                                      \n";
    $stSql .= "     ac.cod_natureza,                                                    \n";
    $stSql .= "     ac.cod_calculo,                                                     \n";
    $stSql .= "     ac.exercicio,                                                       \n";
    $stSql .= "     ac.valor as valor_calculado,                                        \n";
    $stSql .= "     alanc.valor                                                         \n";
    $stSql .= " FROM                                                                    \n";
    $stSql .= "     arrecadacao.calculo as ac                                           \n";

    $stSql .= " INNER JOIN                                                              \n";
    $stSql .= "     monetario.credito as mc                                             \n";
    $stSql .= " ON                                                                      \n";
    $stSql .= "     ac.cod_credito = mc.cod_credito AND                                 \n";
    $stSql .= "     ac.cod_especie = mc.cod_especie AND                                 \n";
    $stSql .= "     ac.cod_genero  = mc.cod_genero  AND                                 \n";
    $stSql .= "     ac.cod_natureza = mc.cod_natureza                                   \n";

    $stSql .= " INNER JOIN                                                              \n";
    $stSql .= "     arrecadacao.lancamento_calculo as alanc                             \n";
    $stSql .= " ON                                                                      \n";
    $stSql .= "     alanc.cod_calculo = ac.cod_calculo                                  \n";

    $stSql .= " INNER JOIN                                                              \n";
    $stSql .= "     arrecadacao.lancamento as alan                                      \n";
    $stSql .= " ON                                                                      \n";
    $stSql .= "     alan.cod_lancamento = alanc.cod_lancamento                          \n";

    return $stSql;

}

function lancamentoAutomatico($inCodGrupo, $inExercicio, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ( Sessao::read( 'lancados' ) == -1 ) {
        $stSql = "
            SELECT
                grupo_credito.cod_modulo

            FROM
                arrecadacao.grupo_credito

            INNER JOIN
                arrecadacao.calendario_fiscal
            ON
                calendario_fiscal.cod_grupo = grupo_credito.cod_grupo
                AND calendario_fiscal.ano_exercicio = grupo_credito.ano_exercicio

            WHERE
                grupo_credito.cod_grupo = ".$inCodGrupo."
                AND grupo_credito.ano_exercicio = '".$inExercicio."'";

        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsListaCodModulo, $stSql, $boTransacao );
        if ( $obErro->Ocorreu() )
            return $obErro;

        Sessao::write( 'lancados', 0 );
        Sessao::write( 'lancados_cod_modulo', $rsListaCodModulo->getCampo( "cod_modulo" ) );
        Sessao::write( 'lancados_resultado', "" );
        if ( Sessao::read( 'lancados_cod_modulo' ) == 12 ) { //imovel
            $stSql = "
                     SELECT DISTINCT imovel.inscricao_municipal                                             AS inscricao
                          FROM imobiliario.imovel
                     LEFT JOIN (
                                     SELECT tmp.*
                                       FROM imobiliario.baixa_imovel                                        AS tmp
                                 INNER JOIN (
                                              SELECT MAX(baixa_imovel.timestamp)                            AS timestamp
                                                   , baixa_imovel.inscricao_municipal
                                                FROM imobiliario.baixa_imovel
                                            GROUP BY inscricao_municipal
                                            )                                                               AS tmp2
                                         ON tmp.inscricao_municipal = tmp2.inscricao_municipal
                                        AND tmp.timestamp           = tmp2.timestamp
                               )                                                                            AS bi --baixa_imovel
                            ON bi.inscricao_municipal               = imovel.inscricao_municipal
                    INNER JOIN arrecadacao.imovel_calculo                                                   AS AIC
                            ON AIC.inscricao_municipal              = imovel.inscricao_municipal
                    INNER JOIN arrecadacao.calculo
                            ON calculo.cod_calculo = AIC.cod_calculo
                           AND calculo.simulado = false
                           AND calculo.ativo = true
                    INNER JOIN arrecadacao.calculo_grupo_credito                                            AS ACGC
                            ON ACGC.cod_calculo                     = AIC.cod_calculo
                           AND ACGC.cod_grupo                       = ".$inCodGrupo."
                           AND ACGC.ano_exercicio                   = '".$inExercicio."'
                     LEFT JOIN arrecadacao.lancamento_calculo                                               AS ALC
                            ON ALC.cod_calculo                      = AIC.cod_calculo
                         WHERE ( ( bi.inscricao_municipal IS NULL ) OR ( (bi.inscricao_municipal IS NOT NULL) AND (bi.dt_termino IS NOT NULL) ) )
                           AND ALC.cod_calculo                      IS NULL
            ";

            $this->setDebug($stSql);
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
            if ( $obErro->Ocorreu() )
                return $obErro;
        } else {
            $stSql = "
                     SELECT DISTINCT cadastro_economico.inscricao_economica                              AS inscricao
                          FROM economico.cadastro_economico
                     LEFT JOIN (
                                     SELECT tmp.*
                                       FROM economico.baixa_cadastro_economico                              AS tmp
                                 INNER JOIN (
                                              SELECT MAX(baixa_cadastro_economico.timestamp)                AS timestamp
                                                   , baixa_cadastro_economico.inscricao_economica
                                                FROM economico.baixa_cadastro_economico
                                            GROUP BY inscricao_economica
                                            )                                                               AS tmp2
                                         ON tmp.inscricao_economica = tmp2.inscricao_economica
                                        AND tmp.timestamp           = tmp2.timestamp
                               )                                                                            AS bc --baixa_empresa
                            ON bc.inscricao_economica               = cadastro_economico.inscricao_economica
                    INNER JOIN arrecadacao.cadastro_economico_calculo                                       AS ACEC
                            ON ACEC.inscricao_economica             = cadastro_economico.inscricao_economica
                    INNER JOIN arrecadacao.calculo
                            ON calculo.cod_calculo = ACEC.cod_calculo
                           AND calculo.simulado = false
                           AND calculo.ativo = true
                    INNER JOIN arrecadacao.calculo_grupo_credito                                            AS ACGC
                            ON ACGC.cod_calculo                     = ACEC.cod_calculo
                           AND ACGC.cod_grupo                       = ".$inCodGrupo."
                           AND ACGC.ano_exercicio                   = '".$inExercicio."'
                     LEFT JOIN arrecadacao.lancamento_calculo                                               AS ALC
                            ON ALC.cod_calculo                      = ACEC.cod_calculo
                         WHERE ( ( bc.inscricao_economica IS NULL ) OR ( (bc.inscricao_economica IS NOT NULL) AND (bc.dt_termino IS NOT NULL) ) )
                           AND ALC.cod_calculo                      IS NULL
            ";

            $this->setDebug($stSql);
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
            if ( $obErro->Ocorreu() )
                return $obErro;
        }

        $arTMP = $rsRecordSet->getElementos();
        Sessao::write( 'lancados_total', count( $arTMP ) );
        Sessao::write( 'lista_lancados', $arTMP );
    } else {
        $stInscricoes = "";
        $arListaLanc = Sessao::read( 'lista_lancados' );
        for ($inTMP=0; $inTMP<100; $inTMP++) {
            if ( Sessao::read( 'lancados' ) >= Sessao::read( 'lancados_total' ) ) {
                break;
            }

            $stInscricoes .= $arListaLanc[ Sessao::read( 'lancados' ) ]["inscricao"];
            if ( ( $inTMP+1 < 100 ) && ( Sessao::read( 'lancados' ) + 1 < Sessao::read( 'lancados_total' ) ))
                $stInscricoes .= ", ";

            Sessao::write( 'lancados', Sessao::read( 'lancados' ) + 1 );
        }
        //Campo comentado para otimização , precisa ser reavaliado após lançamento v. 2020
//        $arInscricoes = explode (',', $stInscricoes);
//        foreach ($arInscricoes as $inInscricao) {
//            $stFilt = " AND COALESCE( imovel_calculo.inscricao_municipal, cadastro_economico_calculo.inscricao_economica, calculo_cgm.numcgm ) = ".$inInscricao;
//            $stFilt .= " AND calculo_grupo_credito.cod_grupo = ".$inCodGrupo;
//            $stFilt .= " AND calculo_grupo_credito.ano_exercicio = '".$inExercicio."'";
//            $this->VerificaLancamentos( $rsLancAnt, $stFilt, "", $boTransacao );
//
//            if ($rsLancAnt->getElementos() != "") {
//                $arTMPInscricoes[] =  $inInscricao;
//            }
//        }

    //   if ($arTMPInscricoes != "") {
    //       $stInscricoes = implode(",", $arTMPInscricoes);
           $stSql = " SELECT arrecadacao.fn_lancamento_automatico_intervalo('".$inCodGrupo."', '".$inExercicio."', '".$stInscricoes."') AS resultado ";
           $this->setDebug($stSql);
           $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
       // }
        if ( $obErro->ocorreu() )
            return $obErro;

        if ( !$rsRecordSet->eof() )
            Sessao::write( 'lancados_resultado',  Sessao::read( 'lancados_resultado' ).$rsRecordSet->getCampo("resultado") );
    }

    return $obErro;
}

function montaLancamentoAutomatico($inCodGrupo, $inExercicio)
{
    $stSql  = " SELECT arrecadacao.fn_lancamento_automatico( ".$inCodGrupo.", ".$inExercicio." ) AS resultado \n";

    return $stSql;
}

function VerificaLancamentos(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : "";
    $stSql  = $this->montaVerificaLancamentos().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaVerificaLancamentos()
{
    $stSql =  " SELECT
                    lancamento_calculo.cod_lancamento

                FROM
                    arrecadacao.calculo

                LEFT JOIN
                    arrecadacao.calculo_grupo_credito
                ON
                    calculo_grupo_credito.cod_calculo = calculo.cod_calculo

                LEFT JOIN
                    arrecadacao.calculo_cgm
                ON
                    calculo_cgm.cod_calculo = calculo.cod_calculo

                LEFT JOIN
                    arrecadacao.cadastro_economico_calculo
                ON
                    cadastro_economico_calculo.cod_calculo = calculo.cod_calculo

                LEFT JOIN
                    arrecadacao.imovel_calculo
                ON
                    imovel_calculo.cod_calculo = calculo.cod_calculo

                INNER JOIN
                    arrecadacao.lancamento_calculo
                ON
                    calculo.cod_calculo = lancamento_calculo.cod_calculo

                INNER JOIN
                    arrecadacao.lancamento
                ON
                    lancamento.cod_lancamento = lancamento_calculo.cod_lancamento
                    AND lancamento.ativo = true

                INNER JOIN
                    arrecadacao.parcela
                ON
                    parcela.cod_lancamento = lancamento_calculo.cod_lancamento

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
                    pagamento IS NULL
                    AND carne_devolucao IS NULL ";

    return $stSql;
}

function recuperaRelatorioLancamentoAutomatico(&$rsRecordSet, $stFiltro = "", $boTransacao = "", $stOrder = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaRelatorioLancamentoAutomatico().$stFiltro.$stOrder;
    $this->setDebug($stSql); 
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioLancamentoAutomatico()
{
    $stSql = "
        SELECT *
          FROM ( SELECT DISTINCT lancamento.cod_lancamento
                      , to_char( lancamento.vencimento, 'dd/mm/yyyy' ) AS vencimento
                      , lancamento.total_parcelas
                      , lancamento.valor
                      , CAST((SELECT array_to_string( ARRAY( select numcgm from sw_cgm where numcgm IN ( SELECT numcgm FROM arrecadacao.calculo_cgm WHERE cod_calculo = lancamento_calculo.cod_calculo)), '/' ) ) AS VARCHAR) AS numcgm
                      , CAST((SELECT array_to_string( ARRAY( select nom_cgm from sw_cgm where numcgm IN ( SELECT numcgm FROM arrecadacao.calculo_cgm WHERE cod_calculo = lancamento_calculo.cod_calculo)), '/' ) ) AS VARCHAR) AS nom_cgm
                      , COALESCE ( cadastro_economico_calculo.inscricao_economica, imovel_calculo.inscricao_municipal, 0 ) AS inscricao
                      , CASE WHEN parcela_desconto.valor IS NOT NULL THEN
                                parcela_desconto.valor
                        ELSE
                                parcela.valor
                        END AS valor_parcela
                      , COALESCE ( carne.numeracao::varchar, '    -    ' ) AS numeracao
                      , COALESCE ( ( CASE WHEN parcela.nr_parcela = 0 THEN
                                             'única'::text
                                     ELSE
                                             parcela.nr_parcela||'/'||lancamento.total_parcelas::text
                                     END
                                   ), ' - ' ) AS nr_parcela
                      , COALESCE( to_char( parcela.vencimento, 'dd/mm/yyyy' ), ' - ' ) AS data_vencimento
                      , COALESCE ( ( CASE WHEN parcela.nr_parcela = 0 THEN
                                             0
                                     ELSE
                                             parcela.nr_parcela
                                     END
                                   ), 0) AS ordenacao
                       , grupo_credito.ano_exercicio
                       , grupo_credito.cod_grupo
                       , COALESCE ( imovel_calculo.inscricao_municipal, 0 ) as inscricao_municipal
                       ,  COALESCE ( cadastro_economico_calculo.inscricao_economica, 0 ) AS inscricao_economica
                   FROM arrecadacao.lancamento
             INNER JOIN arrecadacao.lancamento_calculo
                     ON lancamento_calculo.cod_lancamento = lancamento.cod_lancamento

              LEFT JOIN arrecadacao.imovel_calculo
                     ON imovel_calculo.cod_calculo = lancamento_calculo.cod_calculo

              LEFT JOIN arrecadacao.cadastro_economico_calculo
                     ON cadastro_economico_calculo.cod_calculo = lancamento_calculo.cod_calculo

              LEFT JOIN arrecadacao.parcela
                     ON parcela.cod_lancamento = lancamento.cod_lancamento

              LEFT JOIN arrecadacao.parcela_desconto
                     ON parcela_desconto.cod_parcela = parcela.cod_parcela


              LEFT JOIN arrecadacao.carne
                     ON carne.cod_parcela = parcela.cod_parcela
                                  
               LEFT JOIN arrecadacao.calculo_grupo_credito 
                        ON calculo_grupo_credito.cod_calculo = lancamento_calculo.cod_calculo
                   
                       
               LEFT JOIN arrecadacao.grupo_credito
                        ON grupo_credito.ano_exercicio = calculo_grupo_credito.ano_exercicio
                      AND grupo_credito.cod_grupo = calculo_grupo_credito.cod_grupo
            ) AS lancamento

    ";

    return $stSql;
}
}//fim da class
?>
