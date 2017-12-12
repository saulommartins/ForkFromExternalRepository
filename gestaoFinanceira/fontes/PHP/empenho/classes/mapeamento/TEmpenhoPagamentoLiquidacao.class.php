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
    * Classe de mapeamento da tabela EMPENHO.PAGAMENTO_LIQUIDACAO
    * Data de Criação: 30/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TEmpenhoPagamentoLiquidacao.class.php 59612 2014-09-02 12:00:51Z gelson $
    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-06-29 10:53:13 -0300 (Sex, 29 Jun 2007) $

    * Casos de uso: uc-02.03.04,uc-02.04.05
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TEmpenhoPagamentoLiquidacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoPagamentoLiquidacao()
{
    parent::Persistente();
    $this->setTabela('empenho.pagamento_liquidacao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_ordem,exercicio,cod_entidade,exercicio_liquidacao,cod_nota');

    $this->AddCampo('cod_ordem','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'04',true,true);
    $this->AddCampo('cod_entidade','integer',true,'',true,false);
    $this->AddCampo('exercicio_liquidacao','char',true,'04',true,true);
    $this->AddCampo('cod_nota','integer',true,'',true,true);
    $this->AddCampo('vl_pagamento','numeric',true,'14,02',false,false);

}

function recuperaValorPago(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaValorPago().$stFiltro;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaValorOrdem(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaValorOrdem();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

//
// Utilizado inicialmente para baixas de pagamentos do siam(max).
//
function recuperaPagamentoLiquidacao(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaPagamentoLiquidacao().$stFiltro;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRecuperaRelacionamento()
{
    $stSql  = "                                                                 ";
    $stSql .= "SELECT                                                           \n";
    $stSql .= "    EOP.COD_ORDEM,                                               \n";
    $stSql .= "    EOP.EXERCICIO,                                               \n";
    $stSql .= "    EOP.COD_ENTIDADE,                                            \n";
    $stSql .= "    EPL.EXERCICIO_LIQUIDACAO,                                    \n";
    $stSql .= "    EPL.COD_NOTA,                                                \n";
    $stSql .= "    TO_CHAR ( ENL.DT_LIQUIDACAO ,'dd/mm/yyyy') AS DT_LIQUIDACAO, \n";
    $stSql .= "    publico.fn_numeric_br( EPL.VL_PAGAMENTO ) AS VL_PAGAMENTO,   \n";
    $stSql .= "    CGME.NOM_CGM AS ENTIDADE,                                    \n";
    $stSql .= "    ENL.COD_EMPENHO,                                             \n";
    $stSql .= "    ENL.EXERCICIO_EMPENHO,                                       \n";
    $stSql .= "    TO_CHAR ( EE.DT_EMPENHO ,'dd/mm/yyyy') AS DT_EMPENHO,        \n";
    $stSql .= "    EPE.CGM_BENEFICIARIO,                                        \n";
    $stSql .= "    CGM.NOM_CGM AS BENEFICIARIO                                  \n";
    $stSql .= "FROM                                                             \n";
    $stSql .= "  empenho.ordem_pagamento AS EOP                             \n";
    $stSql .= "LEFT JOIN                                                        \n";
    $stSql .= "  empenho.pagamento_liquidacao AS EPL                        \n";
    $stSql .= "ON                                                               \n";
    $stSql .= "    EPL.COD_ORDEM    = EOP.COD_ORDEM AND                         \n";
    $stSql .= "    EPL.EXERCICIO    = EOP.EXERCICIO AND                         \n";
    $stSql .= "    EPL.COD_ENTIDADE = EOP.COD_ENTIDADE                          \n";
    $stSql .= "LEFT JOIN                                                        \n";
    $stSql .= "  empenho.nota_liquidacao AS ENL                             \n";
    $stSql .= "ON                                                               \n";
    $stSql .= "    ENL.EXERCICIO    = EPL.EXERCICIO_LIQUIDACAO AND              \n";
    $stSql .= "    ENL.COD_ENTIDADE = EOP.COD_ENTIDADE AND                      \n";
    $stSql .= "    ENL.COD_NOTA     = EPL.COD_NOTA                              \n";
    $stSql .= "LEFT JOIN                                                        \n";
    $stSql .= "  empenho.empenho AS EE                                      \n";
    $stSql .= "ON                                                               \n";
    $stSql .= "    EE.COD_EMPENHO  = ENL.COD_EMPENHO AND                        \n";
    $stSql .= "    EE.EXERCICIO    = ENL.EXERCICIO_EMPENHO AND                  \n";
    $stSql .= "    EE.COD_ENTIDADE = ENL.COD_ENTIDADE                           \n";
    $stSql .= "LEFT JOIN                                                        \n";
    $stSql .= "  empenho.pre_empenho AS EPE                                 \n";
    $stSql .= "ON                                                               \n";
    $stSql .= "    EPE.EXERCICIO       = EE.EXERCICIO AND                       \n";
    $stSql .= "    EPE.COD_PRE_EMPENHO = EE.COD_PRE_EMPENHO                     \n";
    $stSql .= "LEFT JOIN                                                        \n";
    $stSql .= "  sw_cgm AS CGM                                              \n";
    $stSql .= "ON                                                               \n";
    $stSql .= "    CGM.NUMCGM = EPE.CGM_BENEFICIARIO                            \n";
    $stSql .= "LEFT JOIN                                                        \n";
    $stSql .= "  orcamento.entidade AS OE                                   \n";
    $stSql .= "ON                                                               \n";
    $stSql .= "  ( OE.COD_ENTIDADE = EOP.COD_ENTIDADE AND                       \n";
    $stSql .= "    OE.EXERCICIO    = EOP.EXERCICIO        )                     \n";
    $stSql .= "LEFT JOIN                                                        \n";
    $stSql .= "  sw_cgm AS CGME                                             \n";
    $stSql .= "ON                                                               \n";
    $stSql .= "    CGME.NUMCGM = OE.NUMCGM                                      \n";

    return $stSql;
}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRecuperaValorOrdem()
{
    $stSql .= "SELECT empenho.fn_consultar_valor_pagamento_ordem( '".$this->getDado("exercicio")."',".$this->getDado("cod_ordem").",".$this->getDado("cod_entidade").") as valor_ordem  \n";

    return $stSql;
}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRecuperaValorPago()
{
    $stSql  = "SELECT                                             \n";
    $stSql .= "    SUM(PL.VL_PAGAMENTO) AS VALOR_PAGO             \n";
    $stSql .= "FROM                                               \n";
    $stSql .= "    empenho.empenho AS E,                      \n";
    $stSql .= "    empenho.nota_liquidacao AS NL,             \n";
    $stSql .= "    (                                              \n";
    $stSql .= "    SELECT                                         \n";
    $stSql .= "        PL.*                                       \n";
    $stSql .= "    FROM                                           \n";
    $stSql .= "        empenho.pagamento_liquidacao AS PL     \n";

    /* VALOR ANULADO */
    $stSql .= "   left join ( \n";
    $stSql .= "               select  opla.cod_ordem \n";
    $stSql .= "                      ,opla.cod_entidade \n";
    $stSql .= "                      ,opla.exercicio \n";
    $stSql .= "                      ,coalesce(sum(vl_anulado), 0.00) as vl_anulado \n";
    $stSql .= "               from empenho.ordem_pagamento_liquidacao_anulada as opla \n";
    $stSql .= "               group by  opla.cod_ordem \n";
    $stSql .= "                        ,opla.cod_entidade \n";
    $stSql .= "                        ,opla.exercicio \n";
    $stSql .= "             ) as opa \n";
    $stSql .= "             on ( \n";
    $stSql .= "                      opa.cod_ordem    = pl.cod_ordem \n";
    $stSql .= "                  and opa.exercicio    = pl.exercicio \n";
    $stSql .= "                  and opa.cod_entidade = pl.cod_entidade \n";
    $stSql .= "                ) \n";

    $stSql .= "    WHERE                                          \n";
    $stSql .= "        opa.vl_anulado > pl.vl_pagamento          \n";
    $stSql .= "    ) AS PL                                        \n";
    $stSql .= "WHERE                                              \n";
    $stSql .= "    E.COD_EMPENHO = NL.COD_EMPENHO AND             \n";
    $stSql .= "    NL.COD_NOTA = PL.COD_NOTA                      \n";
    $stSql .= "                                                   \n";

    return $stSql;
}

function montaRecuperaPagamentoLiquidacao()
{
    $stSql  = "                                                                                ";
    $stSql .= "SELECT                                                                        \n";
    $stSql .= "    EPL.COD_ORDEM,                                                            \n";
    $stSql .= "    EPL.EXERCICIO,                                                            \n";
    $stSql .= "    EPL.COD_ENTIDADE,                                                         \n";
    $stSql .= "    EPL.EXERCICIO_LIQUIDACAO,                                                 \n";
    $stSql .= "    EPL.COD_NOTA,                                                             \n";
    $stSql .= "    EPL.VL_PAGAMENTO,                                                         \n";
    $stSql .= "    ENLP.VL_PAGO,                                                             \n";
    $stSql .= "    ENLPA.VL_ANULADO                                                          \n";
    $stSql .= "FROM                                                                          \n";
    $stSql .= "  empenho.pagamento_liquidacao AS EPL                                     \n";
    $stSql .= "LEFT JOIN                                                                     \n";
    $stSql .= "  empenho.pagamento_liquidacao_nota_liquidacao_paga AS EPLNLP             \n";
    $stSql .= "ON                                                                            \n";
    $stSql .= "    EPL.COD_ORDEM             = EPLNLP.COD_ORDEM             AND              \n";
    $stSql .= "    EPL.EXERCICIO             = EPLNLP.EXERCICIO             AND              \n";
    $stSql .= "    EPL.COD_ENTIDADE          = EPLNLP.COD_ENTIDADE          AND              \n";
    $stSql .= "    EPL.EXERCICIO_LIQUIDACAO  = EPLNLP.EXERCICIO_LIQUIDACAO  AND              \n";
    $stSql .= "    EPL.COD_NOTA              = EPLNLP.COD_NOTA                               \n";
    $stSql .= "LEFT JOIN                                                                     \n";
    $stSql .= "  empenho.nota_liquidacao_paga AS ENLP                                    \n";
    $stSql .= "ON                                                                            \n";
    $stSql .= "    EPLNLP.COD_NOTA           = ENLP.COD_NOTA                AND              \n";
    $stSql .= "    EPLNLP.COD_ENTIDADE       = ENLP.COD_ENTIDADE            AND              \n";
    $stSql .= "    EPLNLP.EXERCICIO          = ENLP.EXERCICIO               AND              \n";
    $stSql .= "    EPLNLP.TIMESTAMP          = ENLP.TIMESTAMP                                \n";
    $stSql .= "LEFT JOIN                                                                     \n";
    $stSql .= "  empenho.nota_liquidacao_paga_anulada AS ENLPA                           \n";
    $stSql .= "ON                                                                            \n";
    $stSql .= "    ENLP.COD_ENTIDADE          = ENLPA.COD_ENTIDADE          AND              \n";
    $stSql .= "    ENLP.COD_NOTA              = ENLPA.COD_NOTA              AND              \n";
    $stSql .= "    ENLP.EXERCICIO             = ENLPA.EXERCICIO             AND              \n";
    $stSql .= "    ENLP.TIMESTAMP             = ENLPA.TIMESTAMP                              \n";
    $stSql .= "                                                                              \n";

    return $stSql;
}

/**
    * Método para executar montaRecuperaLiquidacaoNaoPaga
    * @access Private
    * @param Object $rsRecordSet
    * @param Object $boTransacao
    * @return Object $obErro
*/
function recuperaLiquidacaoNaoPaga(&$rsRecordSet, $stFiltro = "", $stOrder = "",$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $this->setDado( 'stFiltro', $stFiltro );
    $stSql = $this->montaRecuperaLiquidacaoNaoPaga();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLiquidacaoNaoPaga()
{
    $stSql  = "SELECT *                                                                      \n";
    $stSql .= "FROM empenho.fn_lista_empenhos_pagar( '".$this->getDado('stFiltro')."'        \n";
    $stSql .= "                                     ,'".$this->getDado('stFiltroOrdem')."'   \n";
    $stSql .= ") AS retorno( empenho          varchar                                        \n";
    $stSql .= "             ,nota             varchar                                        \n";
    $stSql .= "             ,ordem            varchar                                        \n";
    $stSql .= "             ,cod_entidade     integer                                        \n";
    $stSql .= "             ,entidade         varchar                                        \n";
    $stSql .= "             ,cgm_beneficiario integer                                        \n";
    $stSql .= "             ,beneficiario     varchar                                        \n";
    $stSql .= "             ,vl_nota          numeric                                        \n";
    $stSql .= "             ,vl_ordem         numeric )                                      \n";

    return $stSql;
}

function recuperaLiquidacaoNaoPagaTesouraria(&$rsRecordSet, $stFiltro = "", $stOrder = "",$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $this->setDado( 'stFiltro', $stFiltro );
    $stSql = $this->montaRecuperaLiquidacaoNaoPagaTesouraria();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLiquidacaoNaoPagaTesouraria()
{
    $stSql  = "SELECT *                                                                      \n";
    $stSql .= "FROM empenho.fn_lista_empenhos_pagar_tesouraria( '".$this->getDado('stFiltro')."'        \n";
    $stSql .= "                                     ,'".$this->getDado('stFiltroOrdem')."'   \n";
    $stSql .= "                                     ,'".$this->getDado('stFiltroAuxiliar')."' \n";
    $stSql .= ") AS retorno( empenho          varchar                                        \n";
    $stSql .= "             ,nota             varchar                                        \n";
    $stSql .= "             ,adiantamento     varchar                                        \n";
    $stSql .= "             ,ordem            varchar                                        \n";
    $stSql .= "             ,cod_entidade     integer                                        \n";
    $stSql .= "             ,entidade         varchar                                        \n";
    $stSql .= "             ,cgm_beneficiario integer                                        \n";
    $stSql .= "             ,beneficiario     varchar                                        \n";
    $stSql .= "             ,vl_nota          numeric                                        \n";
    $stSql .= "             ,vl_ordem         numeric )                                      \n";

    return $stSql;

}

function montaRecuperaMaiorData()
{
    $stSql  =" SELECT                                                                                                                       \n";
    $stSql .="     CASE WHEN to_date('".$this->getDado("stDataOrdem")."','dd/mm/yyyy') < to_date('01/01/".$this->getDado("stExercicio")."','dd/mm/yyyy') THEN      \n";
    $stSql .="         CASE WHEN to_date(to_char(max(timestamp),'dd/mm/yyyy'),'dd/mm/yyyy') < to_date('01/01/".$this->getDado("stExercicio")."','dd/mm/yyyy') THEN \n";
    $stSql .="             '01/01/".$this->getDado("stExercicio")."'                                                                        \n";
    $stSql .="         ELSE                                                                                                                 \n";
    $stSql .="             to_char(max(timestamp),'dd/mm/yyyy')                                                                             \n";
    $stSql .="         END                                                                                                                  \n";
    $stSql .="     ELSE                                                                                                                     \n";
    $stSql .="         CASE WHEN to_date(to_char(max(timestamp),'dd/mm/yyyy'),'dd/mm/yyyy') < to_date('01/01/".$this->getDado("stExercicio")."','dd/mm/yyyy') THEN \n";
    $stSql .="             '".$this->getDado("stDataOrdem")."'                                                                              \n";
    $stSql .="         ELSE                                                                                                                 \n";
    $stSql .="             CASE WHEN to_date(to_char(max(timestamp),'dd/mm/yyyy'),'dd/mm/yyyy') < to_date('".$this->getDado("stDataOrdem")."','dd/mm/yyyy') THEN   \n";
    $stSql .="                 '".$this->getDado("stDataOrdem")."'                                                                          \n";
    $stSql .="             ELSE                                                                                                             \n";
    $stSql .="                 to_char(max(timestamp),'dd/mm/yyyy')                                                                         \n";
    $stSql .="             END                                                                                                              \n";
    $stSql .="         END                                                                                                                  \n";
    $stSql .="     END AS data_op                                                                                                           \n";
    $stSql .=" FROM                                                                                                                         \n";
    $stSql .="     empenho.nota_liquidacao_paga                                                                                             \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaMaiorData(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaMaiorData().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
