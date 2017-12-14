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
    * Classe de mapeamento da tabela EMPENHO.DESPESAS_FIXAS
    * Data de Criação: 30/11/2004

    * @author Analista: Cleisson Barboza e Diego Victoria
    * @author Desenvolvedor: Tonismar R. Bernardo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: tonismar $
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.03.29, uc-02.03.30
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TEmpenhoDespesasFixas extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoDespesasFixas()
{
    parent::Persistente();
    $this->setTabela('empenho.despesas_fixas');

    $this->setCampoCod('cod_despesa_fixa');
    $this->setComplementoChave('exercicio, cod_entidade, cod_despesa');

    $this->AddCampo('cod_despesa_fixa','sequence',true,'',true,false, 'TEmpenhoItemEmpenhoDespesasFiixas');
    $this->AddCampo('cod_despesa', 'integer',true,'',true,false, 'TOrcamentoDespesa' );
    $this->AddCampo('exercicio','char',true,'4',true,true, 'TEmpenhoItemDespesasFixas');
    $this->AddCampo('cod_entidade','integer',true,'',false,true, 'TOrcamentoEntidade');
    $this->AddCampo('numcgm','integer',true,'',true,true);
    $this->AddCampo('cod_local','integer',true,'',false,true);
    $this->AddCampo('cod_tipo','integer',true,'',false,true);
    $this->AddCampo('num_identificacao','integer',true,'',false,false);
    $this->AddCampo('num_contrato','integer',true,'',false,false);
    $this->AddCampo('dia_vencimento','integer',true,'',false,false);
    $this->AddCampo('historico','varchar',true,'',false,false);
    $this->AddCampo('status','char',true,'1',false,false);
    $this->AddCampo('dt_inicial','date',true,'',false,false);
    $this->AddCampo('dt_final','date',true,'',false,false);
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDespesasFixas(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDespesasFixas().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaRelatorioDespesasMensaisFixas(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRelatorioDespesasMensaisFixas().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaRelatorioDespesasMensaisFixasDetalhe(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRelatorioDespesasMensaisFixasDetalhe().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function verificaDespesaEmpenhada(&$boExisteEmpenho, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaVerificaDespesaEmpenhada();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    if($rsRecordSet->getCampo('nr_empenhos') > 0)
        $boExisteEmpenho = true;
    else
        $boExisteEmpenho = false;

    return $obErro;
}

function recuperaComplementoTipo(&$stComplemento, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaComplementoTipo();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    $stComplemento = $rsRecordSet->getCampo('complemento');

    return $obErro;
}

function montaRecuperaDespesasFixas()
{
    $stSql .= " select                                          \n";
    $stSql .= " 	edf.cod_despesa_fixa                        \n";
    $stSql .= " 	,edf.cod_entidade                           \n";
    $stSql .= " 	,etdf.cod_tipo                              \n";
    $stSql .= " 	,etdf.descricao                             \n";
    $stSql .= " 	,edf.num_identificacao                      \n";
    $stSql .= " 	,edf.num_contrato                           \n";
    $stSql .= " 	,edf.cod_despesa                            \n";
    $stSql .= " 	,cgm.nom_cgm                                \n";
    $stSql .= " 	,cgm.numcgm                                 \n";
    $stSql .= " 	,to_char(edf.timestamp, 'dd/mm/yyyy')  as dt_inclusao \n";
    $stSql .= " 	,cgm2.nom_cgm as nom_entidade               \n";
    $stSql .= "     ,edf.dia_vencimento                         \n";
    $stSql .= "     ,to_char(edf.dt_inicial, 'dd/mm/yyyy') as dt_inicial  \n";
    $stSql .= "     ,to_char(edf.dt_final, 'dd/mm/yyyy') as dt_final      \n";
    $stSql .= "     ,edf.historico                              \n";
    $stSql .= "     ,edf.status                                 \n";
    $stSql .= "     ,edf.cod_local	                            \n";
    $stSql .= "     ,edf.exercicio                              \n";
    $stSql .= " from                                            \n";
    $stSql .= " 	empenho.tipo_despesa_fixa as etdf           \n";
    $stSql .= " 	,empenho.despesas_fixas as edf              \n";
    $stSql .= " 	,sw_cgm as cgm                              \n";
    $stSql .= " 	,sw_cgm as cgm2                             \n";
    $stSql .= " 	,orcamento.entidade as oe                   \n";
    $stSql .= " where                                           \n";
    $stSql .= "     edf.cod_entidade = ".$this->getDado('cod_entidade')." \n";
    $stSql .= "     and edf.exercicio = '".$this->getDado('exercicio')."' \n";
    $stSql .= " 	and edf.cod_tipo = etdf.cod_tipo                      \n";
    $stSql .= " 	and edf.numcgm = cgm.numcgm                           \n";
    $stSql .= " 	and edf.cod_entidade = oe.cod_entidade                \n";
    $stSql .= " 	and oe.numcgm = cgm2.numcgm                           \n";
    $stSql .= " 	and oe.exercicio = '".$this->getDado('exercicio')."'  \n";

    if (( $this->getDado('cod_tipo') != 0 ) && ( !is_null($this->getDado('cod_tipo')) )) {
        $stSql .= "     and edf.cod_tipo = ".$this->getDado('cod_tipo')."         \n";
    }

    if ( $this->getDado('nr_contrato') ) {
        $stSql .= " and edf.num_contrato = ".$this->getDado('nr_contrato')."\n";
    }

    if ( $this->getDado('cod_local') ) {
        $stSql .= " and edf.cod_local = ".$this->getDado('cod_local')."   \n";
    }

    if ( $this->getDado('numcgm') ) {
        $stSql .= " and edf.numcgm = ".$this->getDado('numcgm')."         \n";
    }

    if ( $this->getDado('dt_inicial') ) {
        $stSql .= " and edf.dt_inicial >= to_date('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' ) \n";
    }

    if ( $this->getDado('dt_final') ) {
        $stSql .= " and edf.dt_final <= to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy' ) \n";
    }

    if ( $this->getDado('status') ) {
        $stSql .= " and status = '".$this->getDado('status')."' \n";
    }

    return $stSql;
}

function montaRecuperaRelatorioDespesasMensaisFixas()
{
    $stSql .= "  select                                                   	  \n";
    $stSql .= "  	edf.cod_despesa_fixa                                      \n";
    $stSql .= "  	,edf.cod_entidade                                         \n";
    $stSql .= "  	,etdf.cod_tipo                                            \n";
    $stSql .= "  	,etdf.descricao                                           \n";
    $stSql .= "  	,edf.num_identificacao                                    \n";
    $stSql .= "  	,edf.num_contrato                                         \n";
    $stSql .= "  	,edf.cod_despesa                                          \n";
    $stSql .= "  	,ocd.descricao as nom_despesa                             \n";
    $stSql .= "  	,cgm.nom_cgm                                              \n";
    $stSql .= "  	,cgm.numcgm                                               \n";
    $stSql .= "  	,to_char(edf.timestamp, 'dd/mm/yyyy')  as dt_inclusao     \n";
    $stSql .= "  	,cgm2.nom_cgm as nom_entidade                             \n";
    $stSql .= "      ,edf.dia_vencimento                                      \n";
    $stSql .= "      ,to_char(edf.dt_inicial, 'dd/mm/yyyy') as dt_inicial     \n";
    $stSql .= "      ,to_char(edf.dt_final, 'dd/mm/yyyy') as dt_final         \n";
    $stSql .= "      ,edf.historico                                           \n";
    $stSql .= "      ,edf.status                                              \n";
    $stSql .= "      ,edf.cod_local	                                          \n";
    $stSql .= "  	,al.descricao as nom_local                                             \n";
    $stSql .= "      ,edf.exercicio                                           \n";
    $stSql .= "  from                                                         \n";
    $stSql .= "  	empenho.tipo_despesa_fixa as etdf                         \n";
    $stSql .= "  	,empenho.despesas_fixas as edf                            \n";
    $stSql .= "  	,sw_cgm as cgm                                            \n";
    $stSql .= "  	,sw_cgm as cgm2                                           \n";
    $stSql .= "  	,orcamento.entidade as oe                                 \n";
    $stSql .= "  	,orcamento.despesa as od                                  \n";
    $stSql .= "  	,orcamento.conta_despesa as ocd                           \n";
    $stSql .= "  	,organograma.local as al                                \n";
    $stSql .= "  where                                                        \n";
    $stSql .= "     edf.cod_entidade = ".$this->getDado('cod_entidade')."     \n";
    $stSql .= "  	and oe.exercicio = '".$this->getDado('exercicio')."'      \n";
    $stSql .= "     and edf.exercicio = '".$this->getDado('exercicio')."'     \n";
    $stSql .= "  	and edf.cod_tipo = etdf.cod_tipo                          \n";
    $stSql .= "  	and edf.numcgm = cgm.numcgm                               \n";
    $stSql .= "  	and oe.numcgm = cgm2.numcgm                               \n";
    $stSql .= "  	and edf.cod_entidade = oe.cod_entidade                    \n";
    $stSql .= "  	and od.cod_despesa = edf.cod_despesa                      \n";
    $stSql .= "  	and od.exercicio = edf.exercicio                          \n";
    $stSql .= "  	and od.cod_entidade = edf.cod_entidade                    \n";
    $stSql .= "  	and od.exercicio = ocd.exercicio                          \n";
    $stSql .= "  	and od.cod_conta = ocd.cod_conta                          \n";
    $stSql .= "  	and edf.cod_local = al.cod_local                          \n";

    if (( $this->getDado('cod_tipo') != 0 ) && ( !is_null($this->getDado('cod_tipo')) )) {
        $stSql .= " and edf.cod_tipo = ".$this->getDado('cod_tipo')."         \n";
    }
    if ( $this->getDado('nr_contrato') ) {
        $stSql .= " and edf.num_contrato = ".$this->getDado('nr_contrato')."\n";
    }
    if ( $this->getDado('cod_despesa') ) {
        $stSql .= " and edf.cod_despesa = ".$this->getDado('cod_despesa')."\n";
    }

    if ( $this->getDado('cod_local') ) {
        $stSql .= " and edf.cod_local = ".$this->getDado('cod_local')."   \n";
    }

    if ( $this->getDado('numcgm') ) {
        $stSql .= " and edf.numcgm = ".$this->getDado('numcgm')."         \n";
    }

    if ( $this->getDado('dt_inicial') ) {
        $stSql .= " and edf.dt_inicial >= to_date('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' ) \n";
    }

    if ( $this->getDado('dt_final') ) {
        $stSql .= " and edf.dt_final <= to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy' ) \n";
    }

    if ( $this->getDado('status') ) {
        $stSql .= " and status = '".$this->getDado('status')."' \n";
    }

    return $stSql;
}

function montaRecuperaRelatorioDespesasMensaisFixasDetalhe()
{
      $stSql .= " select                                          \n";
      $stSql .= " 	ee.cod_empenho                                \n";
      $stSql .= " 	,to_char(ee.dt_empenho, 'dd/mm/yyyy' ) as dt_empenho \n";
      $stSql .= " 	,COALESCE(empenho.fn_consultar_valor_empenhado(ee.exercicio,ee.cod_empenho,ee.cod_entidade),0.00) as empenhado \n";
      $stSql .= " 	,COALESCE(empenho.fn_consultar_valor_liquidado(ee.exercicio,ee.cod_empenho,ee.cod_entidade),0.00) as liquidado \n";
      $stSql .= " 	,COALESCE(empenho.fn_consultar_valor_empenhado_pago(ee.exercicio,ee.cod_empenho,ee.cod_entidade),0.00) as pago \n";
      $stSql .= " 	,COALESCE((empenho.fn_consultar_valor_liquidado(ee.exercicio,ee.cod_empenho,ee.cod_entidade) - (empenho.fn_consultar_valor_liquidado_anulado(ee.exercicio,ee.cod_empenho,ee.cod_entidade) - empenho.fn_consultar_valor_liquidado_pago(ee.exercicio,ee.cod_empenho,ee.cod_entidade))),0.00) as pagar_liquidado \n";
      $stSql .= " from 												\n";
      $stSql .= " 	empenho.item_empenho_despesas_fixas as eiedf    \n";
      $stSql .= " 	,empenho.empenho as ee                          \n";
      $stSql .= " where                                             \n";
      $stSql .= " 	eiedf.exercicio = ee.exercicio                  \n";
      $stSql .= " 	and eiedf.cod_entidade = ee.cod_entidade        \n";
      $stSql .= " 	and eiedf.cod_pre_empenho = ee.cod_pre_empenho	\n";
      $stSql .= "   and eiedf.exercicio = '".$this->getDado('exercicio')."' \n";
      $stSql .= "   and eiedf.cod_entidade = ".$this->getDado('cod_entidade')." \n";
      $stSql .= "   and eiedf.cod_despesa = ".$this->getDado('cod_despesa')." \n";
      $stSql .= "   and eiedf.cod_despesa_fixa = ".$this->getDado('cod_despesa_fixa')." \n";

      return $stSql;
}

function recuperaIdentificador(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaIdentificador().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaIdentificador()
{
    $stSql .= " select											  \n";
    $stSql .= " 	edf.cod_despesa_fixa                          \n";
    $stSql .= " 	,edf.num_identificacao                        \n";
    $stSql .= " from                                              \n";
    $stSql .= " 	empenho.despesas_fixas as edf                 \n";
    $stSql .= " where                                             \n";
    $stSql .= "     edf.status  =  't' and                        \n";
    $stSql .= " 	edf.cod_tipo = ".$this->getDado('cod_tipo')." \n";
    if($this->getDado('exercicio'))
        $stSql .= " AND edf.exercicio = '".$this->getDado('exercicio')."' \n";

    return $stSql;
}

function recuperaIdentificadorUnico(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaIdentificadorUnico().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaIdentificadorUnico()
{
    $stSql .= " select											                        \n";
    $stSql .= " 	 edf.num_identificacao                                              \n";
    $stSql .= " 	,edf.exercicio                                                      \n";
    $stSql .= " 	,edf.cod_tipo                                                       \n";
    $stSql .= " from                                                                    \n";
    $stSql .= " 	empenho.despesas_fixas as edf                                       \n";
    $stSql .= " where                                                                   \n";
    $stSql .= " 	    edf.num_identificacao = ".$this->getDado('num_identificacao')." \n";
    $stSql .= " 	AND edf.exercicio         = '".$this->getDado('exercicio')."'       \n";
    $stSql .= " 	AND edf.cod_tipo          = ".$this->getDado('cod_tipo')."          \n";

    return $stSql;
}

function recuperaDespesasFixasIdentificacao(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDespesasFixasIdentificacao().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDespesasFixasIdentificacao()
{
    $stSql .=   "select                                      \n";
    $stSql .=   "	edf.cod_despesa_fixa                    \n";
    $stSql .=   "	,edf.num_identificacao                  \n";
    $stSql .=   "	,etdf.descricao                         \n";
    $stSql .=   "	,edf.cod_despesa                        \n";
    $stSql .=   "	,edf.exercicio                          \n";
    $stSql .=   "	,edf.cod_entidade                       \n";
    $stSql .=   "	,oe.numcgm as numcgm_entidade           \n";
    $stSql .=   "	,cgm.nom_cgm as nom_entidade            \n";
    $stSql .=   "	,edf.numcgm as numcgm_credor            \n";
    $stSql .=   "	,cgm2.nom_cgm as nom_credor             \n";
    $stSql .=   "	,ocd.descricao                          \n";
    $stSql .=   "	,od.num_orgao                           \n";
    $stSql .=   "	,od.num_unidade                         \n";
    $stSql .=   "	,edf.num_contrato                       \n";
    $stSql .=   "	,edf.cod_local                          \n";
    $stSql .=   "	,al.descricao as nom_local                           \n";
    $stSql .=   "	,edf.historico                          \n";
    $stSql .=   "   ,edf.dia_vencimento                     \n";
    $stSql .=   "from                                        \n";
    $stSql .=   "	empenho.despesas_fixas as edf           \n";
    $stSql .=   "	,empenho.tipo_despesa_fixa as etdf      \n";
    $stSql .=   "	,orcamento.entidade as oe               \n";
    $stSql .=   "	,orcamento.despesa as od                \n";
    $stSql .=   "	,orcamento.conta_despesa as ocd         \n";
    $stSql .=   "	,sw_cgm as cgm                          \n";
    $stSql .=   "	,sw_cgm as cgm2                         \n";
    $stSql .=   "	,organograma.local as al              \n";
    $stSql .=   "where                                       \n";
    $stSql .=   "	edf.num_identificacao = ".$this->getDado('num_identificacao')."\n";
    $stSql .=   "   and edf.cod_tipo = ".$this->getDado('cod_tipo')."\n";
    if($this->getDado('exercicio'))
        $stSql .= " and edf.exercicio = '".$this->getDado('exercicio')."'\n";
    $stSql .=   "	                                        \n";
    $stSql .=   "	and edf.cod_tipo = etdf.cod_tipo        \n";
    $stSql .=   "                                           \n";
    $stSql .=   "	and edf.exercicio = oe.exercicio        \n";
    $stSql .=   "	and edf.cod_entidade = oe.cod_entidade  \n";
    $stSql .=   "	                                        \n";
    $stSql .=   "	and oe.numcgm = cgm.numcgm              \n";
    $stSql .=   "	and edf.numcgm = cgm2.numcgm            \n";
    $stSql .=   "	                                        \n";
    $stSql .=   "	and edf.cod_despesa = od.cod_despesa    \n";
    $stSql .=   "	and edf.exercicio = od.exercicio        \n";
    $stSql .=   "	                                        \n";
    $stSql .=   "	and od.cod_conta = ocd.cod_conta        \n";
    $stSql .=   "	and od.exercicio = ocd.exercicio        \n";
    $stSql .=   "	                                        \n";
    $stSql .=   "	and edf.cod_local = al.cod_local        \n";

    return $stSql;
}

function recuperaMaxDataEmpenhoDespesasFixas(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaMaxDataEmpenhoDespesasFixas().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMaxDataEmpenhoDespesasFixas()
{
    $stSql .=   "select																\n";
    $stSql .=   "	to_char(max(ee.dt_vencimento), 'dd/mm/yyyy') as dt_vencimento   \n";
    $stSql .=   "from                                                               \n";
    $stSql .=   "	empenho.empenho as ee                                           \n";
    $stSql .=   "	,empenho.item_empenho_despesas_fixas as eiedf                   \n";
    $stSql .=   "	,empenho.despesas_fixas as edf                                  \n";
    $stSql .=   "	,empenho.tipo_despesa_fixa as etdf                              \n";
    $stSql .=   "where                                                              \n";
    $stSql .=   "	etdf.cod_tipo = edf.cod_tipo                                    \n";
    $stSql .=   "	and etdf.cod_tipo = ".$this->getDado('cod_tipo')."              \n";
    $stSql .=   "	                                                                \n";
    $stSql .=   "	and edf.exercicio = eiedf.exercicio                             \n";
    $stSql .=   "	and edf.cod_despesa = eiedf.cod_despesa                         \n";
    $stSql .=   "	and edf.cod_despesa_fixa = eiedf.cod_despesa_fixa               \n";
    $stSql .=   "	and edf.cod_entidade = eiedf.cod_entidade                       \n";
    $stSql .=   "	                                                                \n";
    $stSql .=   "	and eiedf.exercicio = ee.exercicio                              \n";
    $stSql .=   "	and eiedf.cod_entidade = ee.cod_entidade                        \n";
    $stSql .=   "	and eiedf.cod_pre_empenho = ee.cod_pre_empenho                  \n";
    if($this->getDado('exercicio'))
        $stSql .= " and ee.exercicio = '".$this->getDado('exercicio')."'            \n";
    if($this->getDado('cod_entidade'))
        $stSql .= " and ee.cod_entidade = '".$this->getDado('cod_entidade')."'      \n";

    return $stSql;
}

function verificaExistenciaDespesaFixaMes(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaVerificaExistenciaDespesaFixaMes().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaVerificaExistenciaDespesaFixaMes()
{
    $stSql  = " SELECT                                                                  \n";
    $stSql .= "     CASE when count(emp.cod_empenho) > 0  THEN 'true'                   \n";
    $stSql .= "     ELSE 'false'                                                        \n";
    $stSql .= "     END AS empenho_mes_atual                                            \n";
    $stSql .= " FROM                                                                    \n";
    $stSql .= "     empenho.despesas_fixas AS des                                       \n";
    $stSql .= "     LEFT JOIN(                                                          \n";
    $stSql .= "         SELECT                                                          \n";
    $stSql .= "             fix.cod_despesa_fixa,                                       \n";
    $stSql .= "             fix.cod_entidade,                                           \n";
    $stSql .= "             fix.cod_despesa,                                            \n";
    $stSql .= "             fix.exercicio,                                              \n";
    $stSql .= "             emp.cod_empenho                                             \n";
    $stSql .= "         FROM                                                            \n";
    $stSql .= "             empenho.empenho AS emp                                      \n";
    $stSql .= "             INNER JOIN empenho.item_empenho_despesas_fixas AS fix ON(   \n";
    $stSql .= "                 emp.cod_pre_empenho = fix.cod_pre_empenho               \n";
    $stSql .= "             AND emp.exercicio       = fix.exercicio                     \n";
    $stSql .= "             AND emp.cod_entidade    = fix.cod_entidade                  \n";
    $stSql .= "             )                                                           \n";
    $stSql .= "         WHERE                                                           \n";
    $stSql .= "             TO_CHAR(emp.dt_empenho,'mm') = '".$this->getDado('stMes')."'\n";
    $stSql .= "     ) AS emp ON(                                                        \n";
    $stSql .= "         emp.cod_despesa_fixa    = des.cod_despesa_fixa                  \n";
    $stSql .= "     AND emp.cod_entidade        = des.cod_entidade                      \n";
    $stSql .= "     AND emp.cod_despesa         = des.cod_despesa                       \n";
    $stSql .= "     AND emp.exercicio           = emp.exercicio                         \n";
    $stSql .= "     AND NOT EXISTS ( SELECT cod_empenho                                 \n";
    $stSql .= "                      FROM empenho.empenho_anulado as ea                 \n";
    $stSql .= "                      WHERE    ea.exercicio    = emp.exercicio           \n";
    $stSql .= "                           AND ea.cod_entidade = emp.cod_entidade        \n";
    $stSql .= "                           AND ea.cod_empenho  = emp.cod_empenho )       \n";
    $stSql .= "     )                                                                   \n";
    $stSql .= " WHERE                                                                   \n";
    $stSql .= "     des.cod_despesa_fixa= ".$this->getDado('inCodDespesaFixa')."        \n";
    $stSql .= " AND des.cod_despesa     = ".$this->getDado('inCodDespesa')."            \n";
    $stSql .= " AND des.exercicio       = '".$this->getDado('stExercicio')."'           \n";
    $stSql .= " AND des.cod_entidade    = ".$this->getDado('inCodEntidade')."           \n";

    return $stSql;
}

function montaVerificaDespesaEmpenhada()
{
    $stSql  = " SELECT                                                                \n";
    $stSql .= "     count(*) as nr_empenhos                                           \n";
    $stSql .= " FROM                                                                  \n";
    $stSql .= "     empenho.despesas_fixas              as EDF,                       \n";
    $stSql .= "     empenho.item_empenho_despesas_fixas as IED                        \n";
    $stSql .= " WHERE                                                                 \n";
    $stSql .= "     EDF.cod_despesa      = IED.cod_despesa      AND                   \n";
    $stSql .= "     EDF.cod_despesa_fixa = IED.cod_despesa_fixa AND                   \n";
    $stSql .= "     EDF.exercicio        = IED.exercicio        AND                   \n";
    $stSql .= "     EDF.cod_entidade     = IED.cod_entidade     AND                   \n";

    $stSql .= "     EDF.cod_despesa_fixa= ".$this->getDado('cod_despesa_fixa')."      \n";
    $stSql .= " AND EDF.cod_despesa     = ".$this->getDado('cod_despesa')."          \n";
    $stSql .= " AND EDF.exercicio       = '".$this->getDado('exercicio')."'         \n";
    $stSql .= " AND EDF.cod_entidade    = ".$this->getDado('cod_entidade')."         \n";

    return $stSql;

}

function montaRecuperaComplementoTipo()
{
    $stSql .= " SELECT											  \n";
    $stSql .= " 	complemento                                   \n";
    $stSql .= " FROM                                              \n";
    $stSql .= " 	empenho.tipo_despesa_fixa                     \n";
    $stSql .= " WHERE                                             \n";
    $stSql .= " 	cod_tipo = ".$this->getDado('cod_tipo')."     \n";

    return $stSql;
}

}
