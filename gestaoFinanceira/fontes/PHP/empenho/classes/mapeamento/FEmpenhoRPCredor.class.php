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
    * Classe de mapeamento da tabela FN_EMPENHO_RESTOS_PAGAR_PAGAMENTO_ESTORNO
    * Data de Criação: 02/03/2005

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso : uc-02.03.10
*/

/*
$Log$
Revision 1.8  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FEmpenhoRPCredor extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FEmpenhoRPCredor()
{
    parent::Persistente();

    $this->setTabela('empenho.fn_empenho_restos_pagar_credor');

    $this->AddCampo('exercicio',         'varchar',false,'',false,false);
    $this->AddCampo('stFiltro',          'varchar',false,'',false,false);
    $this->AddCampo('stEntidade',        'varchar',false,'',false,false);
    $this->AddCampo('stDataInicial',     'text',   false,'',false,false);
    $this->AddCampo('stDataFinal',       'text',   false,'',false,false);
    $this->AddCampo('inCodEmpenhoInicial','integer',false,'',false,false);
    $this->AddCampo('inCodEmpenhoFinal', 'integer',false,'',false,false);
    $this->AddCampo('inOrgao',           'integer',false,'',false,false);
    $this->AddCampo('inUnidade',         'integer',false,'',false,false);
    $this->AddCampo('stElementoDespesa', 'varchar',false,'',false,false);
    $this->AddCampo('inRecurso',         'integer',false,'',false,false);
    $this->AddCampo('credor',            'varchar',false,'',false,false);
    $this->AddCampo('inFuncao',          'integer',false,'',false,false);
    $this->AddCampo('inSubFuncao',       'integer',false,'',false,false);
    $this->AddCampo('inOrdem',           'integer',false,'',false,false);
    $this->AddCampo('stMascara',         'text',   false,'',false,false);
    $this->AddCampo('inCodModulo',       'integer',false,'',false,false);

}

function montaRecuperaTodos()
{
    $stSql  = "select * FROM ".$this->getTabela()."(                  \n";
    $stSql .= "      '".$this->getDado("exercicio")."',               \n";
    $stSql .= "      '".$this->getDado("stFiltro")."',                \n";
    $stSql .= "      '".$this->getDado("stDataInicial")."',           \n";
    $stSql .= "      '".$this->getDado("stDataFinal")."',             \n";
    $stSql .= "      '".$this->getDado("stEntidade")."',              \n";
    $stSql .= "      '".$this->getDado("inOrgao")."',                 \n";
    $stSql .= "      '".$this->getDado("inUnidade")."',               \n";
    $stSql .= "      '".$this->getDado("inRecurso")."',               \n";
    $stSql .= "      '".$this->getDado("stDestinacaoRecurso")."',     \n";
    $stSql .= "      '".$this->getDado("inCodDetalhamento")."',       \n";
    $stSql .= "      '".$this->getDado("stElementoDespesa")."',       \n";
    $stSql .= "      '',                                              \n";
    $stSql .= "      '".$this->getDado("inFuncao")."',                \n";
    $stSql .= "      '".$this->getDado("inSubFuncao")."',             \n";
    $stSql .= "      '".$this->getDado("inOrdem")."',                 \n";
    $stSql .= "      '".$this->getDado("stMascara")."',               \n";
    $stSql .= "      '".$this->getDado("inCGM")."',                   \n";
    $stSql .= "      '".$this->getDado("inCodEmpenhoInicial")."',     \n";
    $stSql .= "      '".$this->getDado("inCodEmpenhoFinal")."'        \n";
    $stSql .= ") as retorno(                                          \n";
    $stSql .= "      dotacao varchar,                                 \n";
    $stSql .= "      cod_entidade integer,                            \n";
    $stSql .= "      empenho text,                                    \n";
    $stSql .= "      exercicio char(4),                               \n";
    $stSql .= "      cgm integer,                                     \n";
    $stSql .= "      razao_social varchar,                            \n";
    $stSql .= "      vl_empenhado numeric,                            \n";
    $stSql .= "      vl_empenhado_pago numeric,                       \n";
    $stSql .= "      vl_liquidado numeric,                            \n";
    $stSql .= "      vl_anulado numeric,                              \n";
    $stSql .= "      vl_apagar numeric,                               \n";
    $stSql .= "      data_empenho text,                               \n";
    $stSql .= "      data_vencimento text                             \n";
    $stSql .= ")                                                      \n";

    return $stSql;
}

function montaRecuperaMascara()
{
    $stSql .= "   SELECT   max(valor) AS masc_despesa                       \n";
    $stSql .= "     FROM   administracao.configuracao                                  \n";
    $stSql .= "     WHERE  parametro  = 'masc_despesa'                      \n";
//    $stSql .= "       AND  exercicio  = '".$this->getDado('exercicio')."'   \n";
    $stSql .= "       AND  cod_modulo = ".$this->getDado('inCodModulo')."   \n";

    return $stSql;
}

function recuperaMascara(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    //$stOrdem = ' order by ' . $stOrdem;

    $stSql = $this->montaRecuperaMascara().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaSaldosContas(&$rsRecordSet)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaSaldosContas();
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, '');

    return $obErro;
}

function montaRecuperaSaldosContas()
{
    $stExercicio = $this->getDado('exercicio') ;

$stSql  = "                    SELECT\n";
$stSql .= "                        sum(vl.vl_lancamento) as vl_lancamento\n";
$stSql .= "                        ,vl.cod_entidade\n";
$stSql .= "         ,pr.cod_recurso::integer\n";
$stSql .= "         , 'D' as tipo_lanc\n";
$stSql .= "         , lo.tipo                               \n";

$stSql .= "                    FROM\n";
$stSql .= "                         contabilidade.plano_conta            as pc\n";
$stSql .= "                        ,contabilidade.plano_analitica        as pa\n";

$stSql .= "         LEFT JOIN contabilidade.plano_recurso as pr\n";
$stSql .= "           ON pr.cod_plano    = pa.cod_plano\n";
$stSql .= "              AND pr.exercicio    = pa.exercicio\n";

$stSql .= "                        ,contabilidade.conta_debito           as cd\n";
$stSql .= "                        ,contabilidade.valor_lancamento       as vl\n";
$stSql .= "                        ,contabilidade.lancamento             as la\n";
$stSql .= "                        ,contabilidade.lote                   as lo\n";
$stSql .= "                        ,contabilidade.sistema_contabil       as sc\n";
$stSql .= "                    WHERE   pc.cod_conta    = pa.cod_conta\n";
$stSql .= "                    AND     pc.exercicio    = pa.exercicio\n";
$stSql .= "                    AND     pa.cod_plano    = cd.cod_plano\n";
$stSql .= "                    AND     pa.exercicio    = cd.exercicio\n";
$stSql .= "                    AND     cd.cod_lote     = vl.cod_lote\n";
$stSql .= "                    AND     cd.tipo         = vl.tipo\n";
$stSql .= "                    AND     cd.sequencia    = vl.sequencia\n";
$stSql .= "                    AND     cd.exercicio    = vl.exercicio\n";
$stSql .= "                    AND     cd.tipo_valor   = vl.tipo_valor\n";
$stSql .= "                    AND     cd.cod_entidade = vl.cod_entidade\n";
$stSql .= "                    AND     vl.cod_lote     = la.cod_lote\n";
$stSql .= "                    AND     vl.tipo         = la.tipo\n";
$stSql .= "                    AND     vl.sequencia    = la.sequencia\n";
$stSql .= "                    AND     vl.exercicio    = la.exercicio\n";
$stSql .= "                    AND     vl.cod_entidade = la.cod_entidade\n";
$stSql .= "                    AND     vl.tipo_valor   = 'D'                        \n";
$stSql .= "                    AND     la.cod_lote     = lo.cod_lote\n";
$stSql .= "                    AND     la.exercicio    = lo.exercicio\n";
$stSql .= "                    AND     la.tipo         = lo.tipo\n";
$stSql .= "                    AND     la.cod_entidade = lo.cod_entidade\n";
$stSql .= "                    AND     pa.exercicio = '".$stExercicio."'                        \n";
$stSql .= "                    AND     sc.cod_sistema  = pc.cod_sistema\n";
$stSql .= "                    AND     sc.exercicio    = pc.exercicio\n";
$stSql .= "and COD_ESTRUTURAL ilike '1.1.1.%'                                \n";
$stSql .= "and  dt_lote BETWEEN to_date( '01/01/".$stExercicio."' , 'dd/mm/yyyy' ) AND   to_date( '31/12/".$stExercicio."', 'dd/mm/yyyy' )  \n";
$stSql .= "--        AND   lo.tipo <> 'I'                               \n";

$stSql .= "GROUP BY vl.cod_entidade, pr.cod_recurso, tipo_lanc, lo.tipo\n";

$stSql .= "UNION\n";

$stSql .= "                    SELECT\n";
$stSql .= "                        SUM(vl.vl_lancamento) as vl_lancamento\n";
$stSql .= "                        ,vl.cod_entidade\n";
$stSql .= "         ,pr.cod_recurso::integer\n";
$stSql .= "         , 'C' as tipo_lanc\n";
$stSql .= "         , lo.tipo\n";

$stSql .= "                    FROM\n";
$stSql .= "                         contabilidade.plano_conta       as pc\n";
$stSql .= "                        ,contabilidade.plano_analitica   as pa\n";

$stSql .= "         LEFT JOIN contabilidade.plano_recurso as pr\n";
$stSql .= "           ON pr.cod_plano    = pa.cod_plano\n";
$stSql .= "              AND pr.exercicio    = pa.exercicio\n";

$stSql .= "                        ,contabilidade.conta_credito     as CC\n";
$stSql .= "                        ,contabilidade.valor_lancamento  as vl\n";
$stSql .= "                        ,contabilidade.lancamento        as la\n";
$stSql .= "                        ,contabilidade.lote              as Lo\n";
$stSql .= "                        ,contabilidade.sistema_contabil  as sc\n";
$stSql .= "                    WHERE   pc.cod_conta    = pa.cod_conta\n";
$stSql .= "                    AND     pc.exercicio    = pa.exercicio\n";
$stSql .= "                    AND     pa.cod_plano    = cc.cod_plano\n";
$stSql .= "                    AND     pa.exercicio    = cc.exercicio\n";

$stSql .= "                    AND     cc.cod_lote     = vl.cod_lote\n";
$stSql .= "                    AND     cc.tipo         = vl.tipo\n";
$stSql .= "                    AND     cc.sequencia    = vl.sequencia\n";
$stSql .= "                    AND     cc.exercicio    = vl.exercicio\n";
$stSql .= "                    AND     cc.tipo_valor   = vl.tipo_valor\n";
$stSql .= "                    AND     cc.cod_entidade = vl.cod_entidade\n";
$stSql .= "                    AND     vl.cod_lote     = la.cod_lote\n";
$stSql .= "                    AND     vl.tipo         = la.tipo\n";
$stSql .= "                    AND     vl.sequencia    = la.sequencia\n";
$stSql .= "                    AND     vl.exercicio    = la.exercicio\n";
$stSql .= "                    AND     vl.cod_entidade = la.cod_entidade\n";
$stSql .= "                    AND     vl.tipo_valor   = 'C'                        \n";
$stSql .= "                    AND     la.cod_lote     = lo.cod_lote\n";
$stSql .= "                    AND     la.exercicio    = lo.exercicio\n";
$stSql .= "                    AND     la.tipo         = lo.tipo\n";
$stSql .= "                    AND     la.cod_entidade = lo.cod_entidade\n";
$stSql .= "                    AND     pa.exercicio = '".$stExercicio."'                        \n";
$stSql .= "                    AND     sc.cod_sistema  = pc.cod_sistema\n";
$stSql .= "                    AND     sc.exercicio    = pc.exercicio\n";
$stSql .= "and COD_ESTRUTURAL ilike '1.1.1.%'                                \n";
$stSql .= "and  dt_lote BETWEEN to_date( '01/01/".$stExercicio."' , 'dd/mm/yyyy' ) AND   to_date( '31/12/".$stExercicio."', 'dd/mm/yyyy' )\n";
$stSql .= "--        AND   lo.tipo <> 'I'                               \n";

$stSql .= "GROUP BY vl.COD_ENTIDADE, pr.COD_RECURSO,tipo_lanc, lo.tipo                  \n";

 RETURN $stSql;

}

function recuperaRestosAPagar(&$rsRecordSet)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRestosAPagar();
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, '');

    return $obErro;
}

function montaRecuperaRestosAPagar()
{
    $stExercicio = $this->getDado('exercicio') ;
    $stEntidade  = $this->getDado('stEntidade');
    $stSql .= "

   SELECT lpad(cod_recurso,4,'0') as cod_recurso
        , tipo
    , cod_entidade
    , exercicio
    , cod_plano_debito
    , cod_plano_credito
         , sum(total_processados_exercicios_anteriores) AS col1
         , sum(total_processados_exercicio_anterior) AS col2
         , sum(total_nao_processados_exercicios_anteriores) AS col5
         , sum(total_nao_processados_exercicio_anterior) AS col6
         , sum(liquidados_nao_pagos) as liquidados_nao_pagos
         , sum(empenhados_nao_liquidados) as empenhados_nao_liquidados
     , estrutural_credito
     , estrutural_debito

      FROM stn.fn_rgf_anexo6_recurso_entidade('".$stExercicio."','".$stEntidade."','31/12/".$stExercicio."') AS tb
           (  cod_recurso integer
            , tipo varchar
        , cod_entidade integer
        , exercicio varchar
        , cod_plano_debito varchar
        , cod_plano_credito varchar
            , total_processados_exercicios_anteriores numeric
            , total_processados_exercicio_anterior numeric
            , total_nao_processados_exercicios_anteriores numeric
            , total_nao_processados_exercicio_anterior numeric
            , liquidados_nao_pagos numeric
            , empenhados_nao_liquidados numeric
        , estrutural_credito varchar
        , estrutural_debito varchar
         )
    GROUP BY cod_recurso, tipo, cod_entidade, exercicio, cod_plano_debito, cod_plano_credito, estrutural_credito, estrutural_debito
    ORDER BY cod_recurso, tipo

    ";

    return $stSql ;
}
}
