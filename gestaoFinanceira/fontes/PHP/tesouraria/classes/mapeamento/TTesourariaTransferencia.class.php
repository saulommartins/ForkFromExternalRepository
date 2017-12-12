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
    * Classe de mapeamento da tabela TESOURARIA_TRANSFERENCIA
    * Data de Criação: 21/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.04.09, uc-02.04.26, uc-02.04.27, uc-02.04.28,
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_TRANSFERENCIA
  * Data de Criação: 31/10/2005

  * @author Analista: Lucas Leusin Oaigen
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaTransferencia extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaTransferencia()
{
    parent::Persistente();
    $this->setTabela("tesouraria.transferencia");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_lote,exercicio,cod_entidade,tipo');

    $this->AddCampo('cod_lote'               , 'integer'  , true , ''  , true  , true  );
    $this->AddCampo('exercicio'              , 'varchar'  , true , '04', true  , true  );
    $this->AddCampo('cod_entidade'           , 'integer'  , true , ''  , true  , true  );
    $this->AddCampo('tipo'                   , 'char'     , true , '01', true  , true  );
    $this->AddCampo('cod_autenticacao'       , 'integer'  , true , ''  , false , true  );
    $this->AddCampo('dt_autenticacao'        , 'date'     , true , ''  , false , true  );
    $this->AddCampo('cod_plano_credito'      , 'integer'  , true , ''  , false , true  );
    $this->AddCampo('cod_plano_debito'       , 'integer'  , true , ''  , false , true  );
    $this->AddCampo('cod_boletim'            , 'integer'  , true , ''  , false , true  );
    $this->AddCampo('cod_historico'          , 'integer'  , true , ''  , false , true  );
    $this->AddCampo('cod_terminal'           , 'integer'  , true , ''  , false , true  );
    $this->AddCampo('timestamp_terminal'     , 'timestamp', true , ''  , false , true  );
    $this->AddCampo('cgm_usuario'            , 'integer'  , true , ''  , false , true  );
    $this->AddCampo('timestamp_usuario'      , 'timestamp', true , ''  , false , true  );
    $this->AddCampo('timestamp_transferencia', 'timestamp', false, ''  , false , false );
    $this->AddCampo('observacao'             , 'text'     , false, ''  , false , false );
    $this->AddCampo('valor'                  , 'numeric'  , true,  '14,2'  , false , false );
    $this->AddCampo('cod_tipo'               , 'integer'  , true,  ''  , false , true  );
}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT                                                                  \n";
    $stSql .= "     T.cod_lote,                                                                  \n";
    $stSql .= "     T.exercicio,                                                                  \n";
    $stSql .= "     T.cod_entidade,                                                                  \n";
    $stSql .= "     ent.nom_cgm as nom_entidade,                                                                  \n";
    $stSql .= "     T.tipo,                                                                  \n";
    $stSql .= "     T.cod_boletim,                                                                  \n";
    $stSql .= "     to_char(B.dt_boletim,'dd/mm/yyyy') as dt_boletim,                                                                  \n";
    $stSql .= "     T.cod_historico,                                                                  \n";
    $stSql .= "     to_char(T.timestamp_transferencia,'dd/mm/yyyy') as dt_transferencia,                                                                  \n";
    $stSql .= "     T.timestamp_transferencia,                                                                  \n";
    $stSql .= "     T.observacao,                                                                  \n";
    $stSql .= "     t.cod_plano_credito,                                                                  \n";
    $stSql .= "     credito.nom_conta as nom_conta_credito,                                                                  \n";
    $stSql .= "     t.cod_plano_debito,                                                                  \n";
    $stSql .= "     debito.nom_conta as nom_conta_debito,                                                                  \n";
    $stSql .= "     coalesce(t.valor,0.00) as valor,                                                                  \n";
    $stSql .= "     coalesce(te.valor,0.00) as valor_estornado,                    \n";
    $stSql .= "     ret.cod_recibo_extra as cod_recibo,                                                                  \n";
    $stSql .= "     tr.cod_recurso,                                                                  \n";
    $stSql .= "     tr.masc_recurso_red,                                                                  \n";
    $stSql .= "     tr.nom_recurso,                                                                  \n";
    $stSql .= "     tr.masc_recurso_red || ' - ' || tr.nom_recurso as recurso,                                                                  \n";
    $stSql .= "     tc.cod_credor,                                                                  \n";
    $stSql .= "     tc.nom_credor,                                                                  \n";
    $stSql .= "     t.cod_tipo                                              \n";
    $stSql .= " FROM                                                                  \n";
    $stSql .= "     tesouraria.transferencia as T                                                                  \n";
    $stSql .= "     LEFT JOIN                                                                  \n";
    $stSql .= "         tesouraria.recibo_extra_transferencia as ret                                                                  \n";
    $stSql .= "         on (    ret.exercicio    = t.exercicio                                                                  \n";
    $stSql .= "             AND ret.cod_entidade = t.cod_entidade                                                                  \n";
    $stSql .= "             AND ret.tipo         = t.tipo                                                                  \n";
    $stSql .= "             AND ret.cod_lote     = t.cod_lote )                                                                  \n";
    $stSql .= "     LEFT JOIN                                                                  \n";
    $stSql .= "        ( SELECT                                                                  \n";
    $stSql .= "             tr.cod_recurso,                                                                  \n";
    $stSql .= "             tr.exercicio,                                                                  \n";
    $stSql .= "             tr.tipo,                                                                  \n";
    $stSql .= "             tr.cod_entidade,                                                                  \n";
    $stSql .= "             tr.cod_lote,                                                                  \n";
    $stSql .= "             rec.nom_recurso,                                                             \n";
    $stSql .= "             rec.masc_recurso_red,                                                                  \n";
    $stSql .= "             rec.cod_detalhamento                                                             \n";
    $stSql .= "          FROM                                                                  \n";
    $stSql .= "             tesouraria.transferencia_recurso as TR,                                                                  \n";
    $stSql .= "             orcamento.recurso('".$this->getDado( 'stExercicio')."')  as REC                                                                  \n";
    $stSql .= "          WHERE                                                                  \n";
    $stSql .= "                 tr.cod_recurso  = rec.cod_recurso                                                                  \n";
    $stSql .= "             AND tr.exercicio     = rec.exercicio                                                                  \n";
    $stSql .= "        ) as TR on (     tr.tipo         = t.tipo                                                                  \n";
    $stSql .= "                     AND tr.exercicio    = t.exercicio                                                                  \n";
    $stSql .= "                     AND tr.cod_entidade = t.cod_entidade                                                                  \n";
    $stSql .= "                     AND tr.cod_lote     = t.cod_lote                                                                  \n";
    $stSql .= "                   )                                                                  \n";
    $stSql .= "     LEFT JOIN                                                                  \n";
    $stSql .= "        ( SELECT                                                                  \n";
    $stSql .= "             tc.numcgm as cod_credor,                                                                  \n";
    $stSql .= "             tc.exercicio,                                                                  \n";
    $stSql .= "             tc.tipo,                                                                  \n";
    $stSql .= "             tc.cod_entidade,                                                                  \n";
    $stSql .= "             tc.cod_lote,                                                                  \n";
    $stSql .= "             cgm.nom_cgm as nom_credor                                                                  \n";
    $stSql .= "          FROM                                                                  \n";
    $stSql .= "             tesouraria.transferencia_credor  as TC,                                                                  \n";
    $stSql .= "             sw_cgm  as CGM                                                                  \n";
    $stSql .= "          WHERE                                                                  \n";
    $stSql .= "                 tc.numcgm    = cgm.numcgm                                                                  \n";
    $stSql .= "        ) as TC on (     tc.tipo         = t.tipo                                                                  \n";
    $stSql .= "                     AND tc.exercicio    = t.exercicio                                                                  \n";
    $stSql .= "                     AND tc.cod_entidade = t.cod_entidade                                                                  \n";
    $stSql .= "                     AND tc.cod_lote     = t.cod_lote                                                                  \n";
    $stSql .= "                   )                                                                  \n";
    $stSql .= "     LEFT JOIN                                                                          \n";
    $stSql .= "         ( SELECT                                                                            \n";
    $stSql .= "              cgm.nom_cgm,                                                                           \n";
    $stSql .= "              e.cod_entidade,                                                                           \n";
    $stSql .= "              e.exercicio                                                                           \n";
    $stSql .= "           FROM                                                                           \n";
    $stSql .= "              sw_cgm as CGM,                                                                           \n";
    $stSql .= "              orcamento.entidade as E                                                                           \n";
    $stSql .= "           WHERE                                                                           \n";
    $stSql .= "              cgm.numcgm = e.numcgm                                                                           \n";
    $stSql .= "         ) as ENT on (                                                                             \n";
    $stSql .= "              ent.exercicio    = t.exercicio    AND                                                                           \n";
    $stSql .= "              ent.cod_entidade = t.cod_entidade                                                                           \n";
    $stSql .= "         )                                                                  \n";
    $stSql .= "     LEFT JOIN tesouraria.boletim as B on (                                                                  \n";
    $stSql .= "             B.cod_boletim  = T.cod_boletim  AND                                                                  \n";
    $stSql .= "             B.exercicio    = T.exercicio    AND                                                                  \n";
    $stSql .= "             B.cod_entidade = T.cod_entidade                                                                  \n";
    $stSql .= "     )                                                                  \n";
    $stSql .= "     LEFT JOIN                                                                  \n";
    $stSql .= "         ( SELECT                                                                  \n";
    $stSql .= "             pc.nom_conta,                                                                  \n";
    $stSql .= "             pa.cod_plano,                                                                  \n";
    $stSql .= "             pa.exercicio                                                                  \n";
    $stSql .= "           FROM                                                                  \n";
    $stSql .= "             contabilidade.plano_conta     as pc,                                                                  \n";
    $stSql .= "             contabilidade.plano_analitica as pa                                                                  \n";
    $stSql .= "           WHERE                                                                  \n";
    $stSql .= "             pa.exercicio = pc.exercicio AND                                                                  \n";
    $stSql .= "             pa.cod_conta = pc.cod_conta                                                                  \n";
    $stSql .= "         ) as debito on (                                                                  \n";
    $stSql .= "                 debito.cod_plano = t.cod_plano_debito AND                                                                  \n";
    $stSql .= "                 debito.exercicio = t.exercicio                                                                  \n";
    $stSql .= "         )                                                                  \n";
    $stSql .= "     LEFT JOIN                                                                  \n";
    $stSql .= "         ( SELECT                                                                  \n";
    $stSql .= "             pc.nom_conta,                                                                  \n";
    $stSql .= "             pa.cod_plano,                                                                  \n";
    $stSql .= "             pa.exercicio                                                                  \n";
    $stSql .= "           FROM                                                                  \n";
    $stSql .= "             contabilidade.plano_conta     as pc,                                                                  \n";
    $stSql .= "             contabilidade.plano_analitica as pa                                                                  \n";
    $stSql .= "           WHERE                                                                  \n";
    $stSql .= "             pa.exercicio = pc.exercicio AND                                                                  \n";
    $stSql .= "             pa.cod_conta = pc.cod_conta                                                                  \n";
    $stSql .= "         ) as credito on (                                                                  \n";
    $stSql .= "                 credito.cod_plano = t.cod_plano_credito AND                                                                  \n";
    $stSql .= "                 credito.exercicio = t.exercicio                                                                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "     LEFT JOIN                                                                   \n";
    $stSql .= "         ( SELECT                                                                   \n";
    $stSql .= "                 coalesce(sum(te.valor),0.00) as valor,                                                                   \n";
    $stSql .= "                 te.cod_lote,                                                                   \n";
    $stSql .= "                 te.cod_entidade,                                                                   \n";
    $stSql .= "                 te.exercicio,                                                                   \n";
    $stSql .= "                 te.tipo                                                                   \n";
    $stSql .= "           FROM tesouraria.transferencia_estornada as te                                                                   \n";
    $stSql .= "           GROUP BY                                                                   \n";
    $stSql .= "                 te.cod_lote,                                                                   \n";
    $stSql .= "                 te.cod_entidade,                                                                   \n";
    $stSql .= "                 te.exercicio,                                                                   \n";
    $stSql .= "                 te.tipo                                                                   \n";
    $stSql .= "         ) as te on (                                                                   \n";
    $stSql .= "                t.cod_lote        = te.cod_lote          AND                                                                   \n";
    $stSql .= "                t.cod_entidade    = te.cod_entidade      AND                                                                   \n";
    $stSql .= "                t.exercicio       = te.exercicio         AND                                                                   \n";
    $stSql .= "                t.tipo            = te.tipo                                                                   \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "     LEFT JOIN tesouraria.tipo_transferencia as TT on (                                          \n";
    $stSql .= "                t.cod_tipo = tt.cod_tipo )                                           \n";
    $stSql .= "     WHERE                                                                               \n";
if ($this->getDado('stExercicio'))
    $stSql .= "             t.exercicio = '".$this->getDado( 'stExercicio'   )."' AND                         \n";
    $stSql .= "         t.cod_entidade is not null                                                          \n";
if ($this->getDado('inCodRecibo'))
    $stSql .= "         AND ret.cod_recibo_extra = ".$this->getDado('inCodRecibo')." \n";
if ($this->getDado('inCodEntidade'))
    $stSql .= "         AND t.cod_entidade = ".$this->getDado('inCodEntidade')." \n";
if ($this->getDado('stDtBoletim'))
    $stSql .= "         AND b.dt_boletim = TO_DATE('".$this->getDado('stDtBoletim')."','dd/mm/yyyy') \n";
if ($this->getDado('inCodBoletim'))
    $stSql .= "         AND t.cod_boletim = ".$this->getDado('inCodBoletim')."   \n";
if ($this->getDado('inCodCredor'))
    $stSql .= "         AND tc.cod_credor = ".$this->getDado('inCodCredor')."   \n";
if ($this->getDado('inCodRecurso'))
    $stSql .= "         AND tr.cod_recurso = ".$this->getDado('inCodRecurso')."   \n";
if ($this->getDado('inCodPlanoDebito'))
    $stSql .= "         AND t.cod_plano_debito = ".$this->getDado('inCodPlanoDebito')."   \n";
if ($this->getDado('inCodPlanoCredito'))
    $stSql .= "         AND t.cod_plano_credito = ".$this->getDado('inCodPlanoCredito')."   \n";
if ($this->getDado('inCodTipo'))
    $stSql .= "         AND t.cod_tipo = ".$this->getDado('inCodTipo')." \n";
if ($this->getDado('stDestinacaoRecurso'))
    $stSql .= "         AND tr.masc_recurso_red = '".$this->getDado('stDestinacaoRecurso')."' \n";
if ($this->getDado('inCodDetalhamento'))
    $stSql .= "         AND tr.cod_detalhamento = ".$this->getDado('inCodDetalhamento')." \n";

    return $stSql;
}

/**
    * Função responsável por verificar se uma das contas informadas é conta banco
    * @access Public
    * @param  Boolean  $boVerificado Boolean
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function verificaExisteContaBanco(&$boVerificado, $stFiltro, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaVerificaExisteContaBanco().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $boVerificado = $rsRecordSet->getCampo("boverificado");

    return $obErro;
}

/**
    * Monta sql para recuperaRelacionamento
    * @access Private
    * @return String
*/
function montaVerificaExisteContaBanco()
{
    $stSQL  = "    SELECT                               \n";
    $stSQL .= "        count(cod_plano) as boverificado \n";
    $stSQL .= "    FROM                                 \n";
    $stSQL .= "        contabilidade.plano_banco        \n";

    return $stSQL;
}

function montaVerificaSaldoContaAnalitica()
{
    $stSql  = "  select     \n";
    $stSql .= "     valor_saldo +valor_recibos as saldo     \n";
    $stSql .= "  from (     \n";
    $stSql .= "     select     \n";
    $stSql .= "         coalesce(sum(coalesce(re.valor,0.00)),0.00) as valor_recibos,     \n";
    $stSql .= "         coalesce((select * from tesouraria.fn_saldo_conta_tesouraria( '".$this->getDado( 'stExercicio'   )."'   \n";
    $stSql .= "                                                                      , ".$this->getDado( 'inCodPlano'    )."    \n";
    $stSql .= "                                                                      ,'01/01/".$this->getDado('stExercicio')."' \n";
    $stSql .= "                                                                      ,'".$this->getDado("stDtBoletim")."'                       \n";
    $stSql .= "                                                                      , true )),0.00 ) as valor_saldo             \n";
    $stSql .= "     from     \n";
    $stSql .= "         tesouraria.recibo_extra re     \n";
    $stSql .= "         left join tesouraria.recibo_extra_banco reb on(     \n";
    $stSql .= "             re.cod_recibo_extra = reb.cod_recibo_extra and     \n";
    $stSql .= "             re.exercicio = reb.exercicio and     \n";
    $stSql .= "             re.cod_entidade = reb.cod_entidade     \n";
    $stSql .= "         )     \n";
    $stSql .= "         left join tesouraria.recibo_extra_transferencia ret on(     \n";
    $stSql .= "             re.cod_recibo_extra     = ret.cod_recibo_extra     and     \n";
    $stSql .= "             re.exercicio             = ret.exercicio         and     \n";
    $stSql .= "             re.cod_entidade         = ret.cod_entidade     \n";
    $stSql .= "         )     \n";
    $stSql .= "         left join tesouraria.recibo_extra_anulacao rea on(     \n";
    $stSql .= "             re.cod_recibo_extra     = rea.cod_recibo_extra     and     \n";
    $stSql .= "             re.exercicio             = rea.exercicio         and     \n";
    $stSql .= "             re.cod_entidade         = rea.cod_entidade     \n";
    $stSql .= "         )     \n";
    $stSql .= "         where     \n";
    $stSql .= "         rea.cod_recibo_extra     is     null     and     \n";
    $stSql .= "         ret.cod_recibo_extra     is     null     and     \n";
    $stSql .= "         re.tipo_recibo           =    'D'        and     \n";
    $stSql .= "         reb.cod_plano            =    ".$this->getDado("inCodPlano")." and     \n";
    $stSql .= "         re.exercicio             =   '".$this->getDado("stExercicio")."'     \n";
    $stSql .= "            \n";
    $stSql .= "     )     \n";
    $stSql .= "  as tabela;     \n";

    return $stSql;
}

function verificaSaldoContaAnalitica(&$nuSaldo, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaVerificaSaldoContaAnalitica();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $nuSaldo = $rsRecordSet->getCampo("saldo");

    return $obErro;
}

function montaRecuperaDadosReciboPagamentoExtra()
{
    $stSql  = "     SELECT                                                 \n";
    $stSql .= "          ret.cod_recibo_extra as cod_recibo                \n";
    $stSql .= "         ,t.cod_entidade                                    \n";
    $stSql .= "         ,t.exercicio                                       \n";
    $stSql .= "         ,t.cod_boletim                                     \n";
    $stSql .= "         ,t.cod_plano_debito as cod_plano_despesa           \n";
    $stSql .= "         ,t.cod_plano_credito as cod_plano_banco            \n";
    $stSql .= "         ,tr.cod_recurso                                    \n";
    $stSql .= "         ,tc.numcgm as cod_credor                           \n";
    $stSql .= "         ,t.cod_lote                                        \n";
    $stSql .= "         ,rec.masc_recurso                                  \n";
    $stSql .= "         ,t.valor                                           \n";
    $stSql .= "         ,te.valor as valor_estornado                       \n";
    $stSql .= "     FROM                                                   \n";
    $stSql .= "         tesouraria.transferencia    as t                   \n";
    $stSql .= "         LEFT JOIN                                          \n";
    $stSql .= "             tesouraria.recibo_extra_transferencia as ret   \n";
    $stSql .= "             on (    ret.exercicio    = t.exercicio         \n";
    $stSql .= "                 AND ret.cod_entidade = t.cod_entidade      \n";
    $stSql .= "                 AND ret.tipo         = t.tipo              \n";
    $stSql .= "                 AND ret.cod_lote     = t.cod_lote )        \n";
    $stSql .= "         LEFT JOIN                                          \n";
    $stSql .= "             tesouraria.transferencia_recurso as tr         \n";
    $stSql .= "             on (    tr.tipo         = t.tipo               \n";
    $stSql .= "                 AND tr.exercicio    = t.exercicio          \n";
    $stSql .= "                 AND tr.cod_entidade = t.cod_entidade       \n";
    $stSql .= "                 AND tr.cod_lote     = t.cod_lote )         \n";
    $stSql .= "         LEFT JOIN                                          \n";
    $stSql .= "             orcamento.recurso('".$this->getDado('exercicio')."') as rec \n";
    $stSql .= "             on (    rec.cod_recurso  = tr.cod_recurso      \n";
    $stSql .= "                 AND rec.exercicio    = tr.exercicio )      \n";
    $stSql .= "         LEFT JOIN                                          \n";
    $stSql .= "             tesouraria.transferencia_credor as tc          \n";
    $stSql .= "             on (    tc.tipo         = t.tipo               \n";
    $stSql .= "                 AND tc.exercicio    = t.exercicio          \n";
    $stSql .= "                 AND tc.cod_entidade = t.cod_entidade       \n";
    $stSql .= "                 AND tc.cod_lote     = t.cod_lote )         \n";
    $stSql .= "         LEFT JOIN (                                           \n";
    $stSql .= "            SELECT                                            \n";
    $stSql .= "                 coalesce(sum(te.valor),0.00) as valor,      \n";
    $stSql .= "                 cod_lote, cod_entidade, exercicio, tipo     \n";
    $stSql .= "            FROM tesouraria.transferencia_estornada as te      \n";
    $stSql .= "            GROUP BY cod_lote, cod_entidade, exercicio, tipo   \n";
    $stSql .= "            ) as te on (                                       \n";
    $stSql .= "                 t.cod_lote        = te.cod_lote          AND \n";
    $stSql .= "                 t.cod_entidade    = te.cod_entidade      AND \n";
    $stSql .= "                 t.exercicio       = te.exercicio         AND \n";
    $stSql .= "                 t.tipo            = te.tipo                   \n";
    $stSql .= "            )                                                    \n";
    $stSql .= "     WHERE                                                  \n";
    $stSql .= "             ret.tipo_recibo = 'D'                          \n";
    $stSql .= "         AND t.cod_lote is not null                         \n";
    $stSql .= "         AND (t.valor - COALESCE(te.valor,0)) > 0            \n";
    if ($this->getDado('cod_recibo')) {
        $stSql .= " AND ret.cod_recibo_extra = ".$this->getDado('cod_recibo')." \n ";
    }
    if ($this->getDado('cod_entidade')) {
        $stSql .= " AND ret.cod_entidade = ".$this->getDado('cod_entidade')." \n ";
    }
    if ($this->getDado('exercicio')) {
        $stSql .= " AND ret.exercicio = '".$this->getDado('exercicio')."' \n ";
    }

    return $stSql;
}

function recuperaDadosReciboPagamentoExtra(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosReciboPagamentoExtra();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosReciboArrecadacaoExtra()
{
    $stSql  = "     SELECT                                                 \n";
    $stSql .= "          ret.cod_recibo_extra as cod_recibo                \n";
    $stSql .= "         ,t.cod_entidade                                    \n";
    $stSql .= "         ,t.exercicio                                       \n";
    $stSql .= "         ,t.cod_boletim                                     \n";
    $stSql .= "         ,t.cod_plano_debito as cod_plano_banco             \n";
    $stSql .= "         ,t.cod_plano_credito as cod_plano_receita          \n";
    $stSql .= "         ,tr.cod_recurso                                    \n";
    $stSql .= "         ,tc.numcgm as cod_credor                           \n";
    $stSql .= "         ,t.cod_lote                                        \n";
    $stSql .= "         ,t.valor                                           \n";
    $stSql .= "         ,t.valor                                           \n";
    $stSql .= "         ,rec.masc_recurso_red                              \n";
    $stSql .= "         ,rec.masc_recurso                                  \n";
    $stSql .= "         ,te.valor as valor_estornado                       \n";
    $stSql .= "     FROM                                                   \n";
    $stSql .= "         tesouraria.transferencia    as t                   \n";
    $stSql .= "         LEFT JOIN                                          \n";
    $stSql .= "             tesouraria.recibo_extra_transferencia as ret   \n";
    $stSql .= "             on (    ret.exercicio    = t.exercicio         \n";
    $stSql .= "                 AND ret.cod_entidade = t.cod_entidade      \n";
    $stSql .= "                 AND ret.tipo         = t.tipo              \n";
    $stSql .= "                 AND ret.cod_lote     = t.cod_lote )        \n";
    $stSql .= "         LEFT JOIN                                          \n";
    $stSql .= "             tesouraria.transferencia_recurso as tr         \n";
    $stSql .= "             on (    tr.tipo         = t.tipo               \n";
    $stSql .= "                 AND tr.exercicio    = t.exercicio          \n";
    $stSql .= "                 AND tr.cod_entidade = t.cod_entidade       \n";
    $stSql .= "                 AND tr.cod_lote     = t.cod_lote )         \n";
    $stSql .= "         LEFT JOIN                                          \n";
    $stSql .= "             orcamento.recurso('".$this->getDado('exercicio')."') as rec       \n";
    $stSql .= "             on (    rec.cod_recurso = tr.cod_recurso       \n";
    $stSql .= "                 AND rec.exercicio   = tr.exercicio )       \n";
    $stSql .= "         LEFT JOIN                                          \n";
    $stSql .= "             tesouraria.transferencia_credor as tc          \n";
    $stSql .= "             on (    tc.tipo         = t.tipo               \n";
    $stSql .= "                 AND tc.exercicio    = t.exercicio          \n";
    $stSql .= "                 AND tc.cod_entidade = t.cod_entidade       \n";
    $stSql .= "                 AND tc.cod_lote     = t.cod_lote )         \n";
    $stSql .= "         LEFT JOIN (                                           \n";
    $stSql .= "            SELECT                                            \n";
    $stSql .= "                 coalesce(sum(te.valor),0.00) as valor,      \n";
    $stSql .= "                 cod_lote, cod_entidade, exercicio, tipo     \n";
    $stSql .= "            FROM tesouraria.transferencia_estornada as te      \n";
    $stSql .= "            GROUP BY cod_lote, cod_entidade, exercicio, tipo   \n";
    $stSql .= "            ) as te on (                                       \n";
    $stSql .= "                 t.cod_lote        = te.cod_lote          AND \n";
    $stSql .= "                 t.cod_entidade    = te.cod_entidade      AND \n";
    $stSql .= "                 t.exercicio       = te.exercicio         AND \n";
    $stSql .= "                 t.tipo            = te.tipo                   \n";
    $stSql .= "            )                                                    \n";
    $stSql .= "     WHERE                                                  \n";
    $stSql .= "             ret.tipo_recibo = 'R'                          \n";
    $stSql .= "         AND t.cod_lote is not null                         \n";
    $stSql .= "         AND (t.valor - COALESCE(te.valor,0)) > 0           \n";
    if($this->getDado('cod_recibo'))
            $stSql .= " AND ret.cod_recibo_extra = ".$this->getDado('cod_recibo')." \n ";
    if($this->getDado('cod_entidade'))
            $stSql .= " AND ret.cod_entidade = ".$this->getDado('cod_entidade')." \n ";
    if($this->getDado('exercicio'))
            $stSql .= " AND ret.exercicio = '".$this->getDado('exercicio')."' \n ";

    return $stSql;
}

function recuperaDadosReciboArrecadacaoExtra(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosReciboArrecadacaoExtra();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
