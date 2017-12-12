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

    * Classe de mapeamento da tabela CONTABILIDADE.PLANO_CONTA
    * Data de Criação: 01/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-02.02.02, uc-02.08.03, uc-02.08.07, uc-02.02.31, uc-02.04.03
    $Id: TContabilidadePlanoConta.class.php 64820 2016-04-06 14:23:36Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TContabilidadePlanoConta extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TContabilidadePlanoConta()
{
    parent::Persistente();
    $this->setTabela('contabilidade.plano_conta');

    $this->setCampoCod('cod_conta');
    $this->setComplementoChave('exercicio,cod_estrutural,indicador_superavit');

    $this->AddCampo('cod_conta'          ,'integer', true,   '', true,false);
    $this->AddCampo('exercicio'          ,   'char', true, '04', true, true);
    $this->AddCampo('nom_conta'          ,'varchar', true,'200',false,false);
    $this->AddCampo('cod_classificacao'  ,'integer', true,   '',false, true);
    $this->AddCampo('cod_sistema'        ,'integer', true,   '',false, true);
    $this->AddCampo('cod_estrutural'     ,'varchar', true,'160',false,false);
    $this->AddCampo('escrituracao'       ,'   char',false,  '9',false,false);
    $this->AddCampo('natureza_saldo'     ,'   char',false,  '7',false,false);
    $this->AddCampo('indicador_superavit','   char',false, '12',false,false);
    $this->AddCampo('funcao'             ,'   text',false, '12',false,false);
    $this->AddCampo('atributo_tcepe'     ,'integer',false,   '',false, true);
    $this->AddCampo('atributo_tcemg'     ,'integer',false,   '',false, true);
    $this->AddCampo('escrituracao_pcasp' ,'   char', true,  '1',false,false);
    $this->AddCampo('obrigatorio_tcmgo'  ,'boolean', true,   '',false,false);
}

function montaRecuperaGrupos()
{
    $stSQL  = "SELECT *                                            \n";
    $stSQL .= "       ,substr(cod_estrutural,1,1) as cod_grupo     \n";
    $stSQL .= "FROM   contabilidade.plano_conta                    \n";
    $stSQL .= "WHERE publico.fn_mascarareduzida(cod_estrutural)    \n";
    $stSQL .= " IN ( SELECT DISTINCT                               \n";
    $stSQL .= "      substr(cod_estrutural,1,1) as cod_grupo       \n";
    $stSQL .= "     FROM                                           \n";
    $stSQL .= "         contabilidade.plano_conta                  \n";
    $stSQL .= ")                                                   \n";

    return $stSQL;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaContaAnalitica.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiï¿½ï¿½o do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenaï¿½ï¿½o do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaGrupos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stOrdem = " ORDER BY cod_grupo";
    $stSql = $this->montaRecuperaGrupos().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContaAnalitica()
{
    $stSQL  = " SELECT                                          \n";
    $stSQL .= "     pa.cod_plano                                \n";
    $stSQL .= " FROM                                            \n";
    $stSQL .= "     contabilidade.plano_analitica as pa,        \n";
    $stSQL .= "     contabilidade.plano_conta as pc             \n";
    $stSQL .= " WHERE  pa.cod_conta = pc.cod_conta AND          \n";
    $stSQL .= " pa.exercicio = pc.exercicio                     \n";

    return $stSQL;

}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaVerificaImplantacaoSaldos
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiï¿½ï¿½o do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenaï¿½ï¿½o do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaVerificaImplantacaoSaldos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaVerificaImplantacaoSaldos().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaVerificaImplantacaoSaldos()
{
    $stSQL  = " SELECT                                                  \n";
    $stSQL .= "     CASE WHEN count(lo.exercicio) > 0 THEN 'true'       \n";
    $stSQL .= "     ELSE 'false' END as retorno                         \n";
    $stSQL .= " FROM                                                    \n";
    $stSQL .= "     contabilidade.lote as lo                            \n";
    $stSQL .= " WHERE lo.tipo='I'                                       \n";
    $stSQL .= " AND   lo.exercicio = '".$this->getDado("exercicio")."'  \n";

    return $stSQL;

}

function montaRecuperaContaAnaliticaAtivoPermanente()
{
    $stSQL  = " SELECT                                          \n";
    $stSQL .= "     pa.cod_plano,                               \n";
    $stSQL .= "     pc.cod_estrutural,                          \n";
    $stSQL .= "     pc.nom_conta                                \n";
    $stSQL .= " FROM                                            \n";
    $stSQL .= "     contabilidade.plano_conta as pc             \n";
    $stSQL .= " LEFT OUTER JOIN                                 \n";
    $stSQL .= "     contabilidade.plano_analitica as pa         \n";
    $stSQL .= " ON                                              \n";
    $stSQL .= "     pa.cod_conta = pc.cod_conta AND             \n";
    $stSQL .= "     pa.exercicio = pc.exercicio                 \n";
    $stSQL .= "WHERE                                            \n";
    $stSQL .= "     pa.cod_conta is not null                    \n";

    return $stSQL;

}

function montaRecuperaValorContaAtivoPermanente()
{
    $stSQL  = " SELECT                                          \n";
    $stSQL .= "     valor                                       \n";
    $stSQL .= " FROM                                            \n";
    $stSQL .= "    administracao.configuracao                   \n";
    $stSQL .= " WHERE                                           \n";
    $stSQL .= "  parametro = 'grupo_contas_permanente'          \n";

    return $stSQL;

}

function recuperaValorContaAtivoPermanente(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaValorContaAtivoPermanente().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaValorContaDepreciacao()
{
    $stSQL  = " SELECT                                          \n";
    $stSQL .= "     valor                                       \n";
    $stSQL .= " FROM                                            \n";
    $stSQL .= "    administracao.configuracao                   \n";
    $stSQL .= " WHERE                                           \n";
    $stSQL .= "  parametro = 'grupo_contas_depreciacao'         \n";

    return $stSQL;

}

function recuperaValorContaDepreciacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaValorContaDepreciacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaContaAnalitica.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiï¿½ï¿½o do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenaï¿½ï¿½o do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaContaAnalitica(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaContaAnalitica().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaContaPlanoAnalitica.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
public function recuperaContaPlanoAnalitica(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    
    $stSql = $this->montaRecuperaContaPlanoAnalitica().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaContaPlanoAnalitica()
{
    $stSQL  = "
        SELECT pa.cod_conta
             , pc.exercicio
             , pc.nom_conta
             , pc.cod_classificacao
             , pc.cod_sistema
             , pc.cod_estrutural
             , pa.cod_plano
          FROM contabilidade.plano_analitica AS pa
             , contabilidade.plano_conta as pc
         WHERE pa.cod_conta = pc.cod_conta
           AND pa.exercicio = pc.exercicio
    \n";

    return $stSQL;

}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaContaAnaliticaAtivoPermanente.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiï¿½ï¿½o do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenaï¿½ï¿½o do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaContaAnaliticaAtivoPermanente(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaContaAnaliticaAtivoPermanente().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaNivelConta(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaNivelConta().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaNivelConta()
{
    $stSql  = " SELECT                                                                      \n";
    $stSql .= "    publico.fn_nivel('".$this->getDado("cod_estrutural")."') as nivel_conta, \n";
    $stSql .= "    publico.fn_nivel(valor) as nivel_maximo                                  \n";
    $stSql .= "FROM                                                                         \n";
    $stSql .= "    administracao.configuracao                                               \n";
    $stSql .= "WHERE parametro='masc_plano_contas'                                          \n";
    $stSql .= " AND exercicio='".$this->getDado("exercicio")."'                             \n";

    return $stSql;
}

function montaRecuperaDadosExportacao()
{
    $stSQL  = " SELECT                                                              \n";
    $stSQL .= "     replace(pc.cod_estrutural,'.','') as cod_estrutural,            \n";
    $stSQL .= "     pr.cod_recurso,                                                 \n";
    $stSQL .= "     replace(pb.cod_banco,'-','') as cod_banco,                      \n";
    $stSQL .= "     replace(pb.cod_agencia,'-','') as cod_agencia,                  \n";
    $stSQL .= "     replace(replace(pb.conta_corrente,'.',''),'-','') as conta_corrente,            \n";
    $stSQL .= "     CASE SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,5)              \n";
    $stSQL .= "        WHEN '11111' THEN '1'                                        \n";
    $stSQL .= "        WHEN '11112' THEN '2'                                        \n";
    $stSQL .= "        WHEN '11211' THEN '2'                                        \n";
    $stSQL .= "        WHEN '11113' THEN '3'                                        \n";
    $stSQL .= "        WHEN '11212' THEN '3'                                        \n";
    $stSQL .= "        WHEN '11515' THEN '3'                                        \n";
    $stSQL .= "    END as tipo_conta,                                               \n";
    $stSQL .= "    CASE cc.cod_classificacao                                        \n";
    $stSQL .= "       WHEN 4 THEN 9                                                 \n";
    $stSQL .= "       ELSE cc.cod_classificacao                                     \n";
    $stSQL .= "    END as cod_classificacao                                         \n";
    $stSQL .= " FROM                                                                \n";
    $stSQL .= "     contabilidade.plano_conta as pc                             \n";
    $stSQL .= "     JOIN contabilidade.classificacao_contabil as cc ON          \n";
    $stSQL .="         cc.exercicio = pc.exercicio AND                              \n";
    $stSQL .="         cc.cod_classificacao = pc.cod_classificacao                  \n";
    $stSQL .= "     JOIN contabilidade.plano_analitica as pa ON                 \n";
    $stSQL .="         pa.cod_conta = pc.cod_conta AND                              \n";
    $stSQL .="         pa.exercicio = pc.exercicio                                  \n";
    $stSQL .= "     JOIN contabilidade.plano_banco as pb ON                         \n";
    $stSQL .="         pb.cod_plano = pa.cod_plano AND                              \n";
    $stSQL .="         pb.exercicio = pa.exercicio                                  \n";
    $stSQL .= "     JOIN contabilidade.plano_recurso as pr ON                   \n";
    $stSQL .="         pr.cod_plano = pa.cod_plano AND                              \n";
    $stSQL .="         pr.exercicio = pa.exercicio                                  \n";
    $stSQL .= " WHERE  cc.exercicio =  '".$this->getDado("stExercicio")."'          \n";
    $stSQL .= " and (                                                               \n";
    $stSQL .= "   pa.exercicio||pa.cod_plano in(                                    \n";
    $stSQL .= "                 select cd.exercicio||cd.cod_plano                   \n";
    $stSQL .= "                  from  contabilidade.conta_debito      as cd    \n";
    $stSQL .= "                   join contabilidade.valor_lancamento  as vl on \n";
    $stSQL .= "                   (                                                 \n";
    $stSQL .= "                     cd.cod_lote          = vl.cod_lote              \n";
    $stSQL .= "                     and cd.tipo          = vl.tipo                  \n";
    $stSQL .= "                     and cd.sequencia     = vl.sequencia             \n";
    $stSQL .= "                     and cd.exercicio     = vl.exercicio             \n";
    $stSQL .= "                     and cd.tipo_valor    = vl.tipo_valor            \n";
    $stSQL .= "                     and cd.cod_entidade  = vl.cod_entidade          \n";
    $stSQL .= "                   )                                                 \n";
    $stSQL .= "                   join contabilidade.lancamento  as la on       \n";
    $stSQL .= "                   (                                                 \n";
    $stSQL .= "                     la.sequencia         = vl.sequencia             \n";
    $stSQL .= "                     and la.cod_lote      = vl.cod_lote              \n";
    $stSQL .= "                     and la.tipo          = vl.tipo                  \n";
    $stSQL .= "                     and la.exercicio     = vl.exercicio             \n";
    $stSQL .= "                     and la.cod_entidade  = vl.cod_entidade          \n";
    $stSQL .= "                   )                                                 \n";
    $stSQL .= "                   join contabilidade.lote as lo on              \n";
    $stSQL .= "                   (                                                 \n";
    $stSQL .= "                     lo.cod_lote          = la.cod_lote              \n";
    $stSQL .= "                     and lo.exercicio     = la.exercicio             \n";
    $stSQL .= "                     and lo.tipo          = la.tipo                  \n";
    $stSQL .= "                     and lo.cod_entidade  = la.cod_entidade          \n";
    $stSQL .= "                   )                                                 \n";
    $stSQL .= "                  where                                              \n";
    $stSQL .= "                   vl.vl_lancamento <> 0.00                          \n";
    $stSQL .= "                   and lo.dt_lote between                            \n";
    $stSQL .= "                             to_date('".$this->getDado("dtInicial")."','dd/mm/yyyy') AND \n";
    $stSQL .= "                             to_date('".$this->getDado("dtFinal")."','dd/mm/yyyy')       \n";
    $stSQL .= "                   group by cd.exercicio, cd.cod_plano               \n";
    $stSQL .= "               )                                                     \n";
    $stSQL .= "   or                                                                \n";
    $stSQL .= "   pa.exercicio||pa.cod_plano in(                                    \n";
    $stSQL .= "                 select cc.exercicio||cc.cod_plano                   \n";
    $stSQL .= "                  from  contabilidade.conta_credito     as cc        \n";
    $stSQL .= "                   join contabilidade.valor_lancamento  as vl on \n";
    $stSQL .= "                   (                                                 \n";
    $stSQL .= "                     cc.cod_lote          = vl.cod_lote              \n";
    $stSQL .= "                     and cc.tipo          = vl.tipo                  \n";
    $stSQL .= "                     and cc.sequencia     = vl.sequencia             \n";
    $stSQL .= "                     and cc.exercicio     = vl.exercicio             \n";
    $stSQL .= "                     and cc.tipo_valor    = vl.tipo_valor            \n";
    $stSQL .= "                     and cc.cod_entidade  = vl.cod_entidade          \n";
    $stSQL .= "                   )                                                 \n";
    $stSQL .= "                  join contabilidade.lancamento  as la on        \n";
    $stSQL .= "                   (                                                 \n";
    $stSQL .= "                     la.sequencia         = vl.sequencia             \n";
    $stSQL .= "                     and la.cod_lote      = vl.cod_lote              \n";
    $stSQL .= "                     and la.tipo          = vl.tipo                  \n";
    $stSQL .= "                     and la.exercicio     = vl.exercicio             \n";
    $stSQL .= "                     and la.cod_entidade  = vl.cod_entidade          \n";
    $stSQL .= "                   )                                                 \n";
    $stSQL .= "                   join contabilidade.lote as lo on              \n";
    $stSQL .= "                   (                                                 \n";
    $stSQL .= "                     lo.cod_lote          = la.cod_lote              \n";
    $stSQL .= "                     and lo.exercicio     = la.exercicio             \n";
    $stSQL .= "                     and lo.tipo          = la.tipo                  \n";
    $stSQL .= "                     and lo.cod_entidade  = la.cod_entidade          \n";
    $stSQL .= "                   )                                                 \n";
    $stSQL .= "                  where                                              \n";
    $stSQL .= "                   vl.vl_lancamento <> 0.00                          \n";
    $stSQL .= "                   and lo.dt_lote between                            \n";
    $stSQL .= "                             to_date('".$this->getDado("dtInicial")."','dd/mm/yyyy') AND \n";
    $stSQL .= "                             to_date('".$this->getDado("dtFinal")."','dd/mm/yyyy')       \n";
    $stSQL .= "                   group by cc.exercicio, cc.cod_plano               \n";
    $stSQL .= "                 )                                                   \n";
    $stSQL .= "  )                                                                  \n";

    return $stSQL;
}

function montaRecuperaDadosExportacaoAjustes2005()
{
    $stSQL  = " SELECT                                                              \n";
//  $stSQL .= "     pc.cod_estrutural as codigo_estrutural,                         \n";
//  $stSQL .= "     pc.nom_conta as nom_conta,                                      \n";
    $stSQL .= "     replace(pc.cod_estrutural,'.','') as cod_estrutural,            \n";
//  $stSQL .= "     '".$this->getDado("inOrgaoUnidade")."' as orgao_unidade,        \n";
    $stSQL .= "     '9999' as orgao_unidade,                                        \n";
    $stSQL .= "     pb.cod_entidade as cod_entidade,                                \n";
    $stSQL .= "     pr.cod_recurso,                                                 \n";
    $stSQL .= "     replace(pb.cod_banco,'-','') as cod_banco,                      \n";
    $stSQL .= "     replace(replace(replace(pb.cod_agencia,' ',''),'.',''),'-','') as cod_agencia,                  \n";
    $stSQL .= "     replace(replace(replace(pb.conta_corrente,' ',''),'.',''),'-','') as conta_corrente,            \n";
    $stSQL .= "     CASE SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,5)              \n";
    $stSQL .= "        WHEN '11111' THEN '1'                                        \n";
    $stSQL .= "        WHEN '11112' THEN '2'                                        \n";
    $stSQL .= "        WHEN '11211' THEN '2'                                        \n";
    $stSQL .= "        WHEN '11113' THEN '3'                                        \n";
    $stSQL .= "        WHEN '11212' THEN '3'                                        \n";
    $stSQL .= "        WHEN '11515' THEN '3'                                        \n";
    $stSQL .= "    END as tipo_conta,                                               \n";
    $stSQL .= "    CASE cc.cod_classificacao                                        \n";
    $stSQL .= "       WHEN 4 THEN 9                                                 \n";
    $stSQL .= "       ELSE cc.cod_classificacao                                     \n";
    $stSQL .= "    END as cod_classificacao                                         \n";
    $stSQL .= " FROM                                                                \n";
    $stSQL .= "     contabilidade.plano_conta as pc                             \n";
    $stSQL .= "     JOIN contabilidade.classificacao_contabil as cc ON          \n";
    $stSQL .="         cc.exercicio = pc.exercicio AND                              \n";
    $stSQL .="         cc.cod_classificacao = pc.cod_classificacao                  \n";
    $stSQL .= "     JOIN contabilidade.plano_analitica as pa ON                 \n";
    $stSQL .="         pa.cod_conta = pc.cod_conta AND                              \n";
    $stSQL .="         pa.exercicio = pc.exercicio                                  \n";
    $stSQL .= "     INNER JOIN contabilidade.plano_banco as pb ON                   \n";
    $stSQL .="         pb.cod_plano = pa.cod_plano AND                              \n";
    $stSQL .="         pb.exercicio = pa.exercicio AND                              \n";
    $stSQL .="         pb.cod_entidade IN ( ".$this->getDado( "stCodEntidade").")   \n";
    $stSQL .= "     JOIN contabilidade.plano_recurso as pr ON                   \n";
    $stSQL .="         pr.cod_plano = pa.cod_plano AND                              \n";
    $stSQL .="         pr.exercicio = pa.exercicio                                  \n";
    $stSQL .= " WHERE  cc.exercicio =  '".$this->getDado("stExercicio")."'          \n";
    $stSQL .= " and (                                                               \n";
    $stSQL .= "   pa.exercicio||pa.cod_plano in(                                    \n";
    $stSQL .= "                 select cd.exercicio||cd.cod_plano                   \n";
    $stSQL .= "                  from  contabilidade.conta_debito      as cd    \n";
    $stSQL .= "                   join contabilidade.valor_lancamento  as vl on \n";
    $stSQL .= "                   (                                                 \n";
    $stSQL .= "                     cd.cod_lote          = vl.cod_lote              \n";
    $stSQL .= "                     and cd.tipo          = vl.tipo                  \n";
    $stSQL .= "                     and cd.sequencia     = vl.sequencia             \n";
    $stSQL .= "                     and cd.exercicio     = vl.exercicio             \n";
    $stSQL .= "                     and cd.tipo_valor    = vl.tipo_valor            \n";
    $stSQL .= "                     and cd.cod_entidade  = vl.cod_entidade          \n";
    $stSQL .= "                   )                                                 \n";
    $stSQL .= "                   join contabilidade.lancamento  as la on       \n";
    $stSQL .= "                   (                                                 \n";
    $stSQL .= "                     la.sequencia         = vl.sequencia             \n";
    $stSQL .= "                     and la.cod_lote      = vl.cod_lote              \n";
    $stSQL .= "                     and la.tipo          = vl.tipo                  \n";
    $stSQL .= "                     and la.exercicio     = vl.exercicio             \n";
    $stSQL .= "                     and la.cod_entidade  = vl.cod_entidade          \n";
    $stSQL .= "                   )                                                 \n";
    $stSQL .= "                   join contabilidade.lote as lo on              \n";
    $stSQL .= "                   (                                                 \n";
    $stSQL .= "                     lo.cod_lote          = la.cod_lote              \n";
    $stSQL .= "                     and lo.exercicio     = la.exercicio             \n";
    $stSQL .= "                     and lo.tipo          = la.tipo                  \n";
    $stSQL .= "                     and lo.cod_entidade  = la.cod_entidade          \n";
    $stSQL .= "                   )                                                 \n";
    $stSQL .= "                  where                                              \n";
    $stSQL .= "                   vl.vl_lancamento <> 0.00                          \n";
    $stSQL .= "                   and lo.dt_lote between                            \n";
    $stSQL .= "                             to_date('".$this->getDado("dtInicial")."','dd/mm/yyyy') AND \n";
    $stSQL .= "                             to_date('".$this->getDado("dtFinal")."','dd/mm/yyyy')       \n";
    $stSQL .= "                   group by cd.exercicio, cd.cod_plano               \n";
    $stSQL .= "               )                                                     \n";
    $stSQL .= "   or                                                                \n";
    $stSQL .= "   pa.exercicio||pa.cod_plano in(                                    \n";
    $stSQL .= "                 select cc.exercicio||cc.cod_plano                   \n";
    $stSQL .= "                  from  contabilidade.conta_credito     as cc        \n";
    $stSQL .= "                   join contabilidade.valor_lancamento  as vl on \n";
    $stSQL .= "                   (                                                 \n";
    $stSQL .= "                     cc.cod_lote          = vl.cod_lote              \n";
    $stSQL .= "                     and cc.tipo          = vl.tipo                  \n";
    $stSQL .= "                     and cc.sequencia     = vl.sequencia             \n";
    $stSQL .= "                     and cc.exercicio     = vl.exercicio             \n";
    $stSQL .= "                     and cc.tipo_valor    = vl.tipo_valor            \n";
    $stSQL .= "                     and cc.cod_entidade  = vl.cod_entidade          \n";
    $stSQL .= "                   )                                                 \n";
    $stSQL .= "                  join contabilidade.lancamento  as la on        \n";
    $stSQL .= "                   (                                                 \n";
    $stSQL .= "                     la.sequencia         = vl.sequencia             \n";
    $stSQL .= "                     and la.cod_lote      = vl.cod_lote              \n";
    $stSQL .= "                     and la.tipo          = vl.tipo                  \n";
    $stSQL .= "                     and la.exercicio     = vl.exercicio             \n";
    $stSQL .= "                     and la.cod_entidade  = vl.cod_entidade          \n";
    $stSQL .= "                   )                                                 \n";
    $stSQL .= "                   join contabilidade.lote as lo on              \n";
    $stSQL .= "                   (                                                 \n";
    $stSQL .= "                     lo.cod_lote          = la.cod_lote              \n";
    $stSQL .= "                     and lo.exercicio     = la.exercicio             \n";
    $stSQL .= "                     and lo.tipo          = la.tipo                  \n";
    $stSQL .= "                     and lo.cod_entidade  = la.cod_entidade          \n";
    $stSQL .= "                   )                                                 \n";
    $stSQL .= "                  where                                              \n";
    $stSQL .= "                   vl.vl_lancamento <> 0.00                          \n";
    $stSQL .= "                   and lo.dt_lote between                            \n";
    $stSQL .= "                             to_date('".$this->getDado("dtInicial")."','dd/mm/yyyy') AND \n";
    $stSQL .= "                             to_date('".$this->getDado("dtFinal")."','dd/mm/yyyy')       \n";
    $stSQL .= "                   group by cc.exercicio, cc.cod_plano               \n";
    $stSQL .= "                 )                                                   \n";
    $stSQL .= "  ) order by cod_estrutural                                          \n";

    return $stSQL;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaDadosExportacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiï¿½ï¿½o do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenaï¿½ï¿½o do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaDadosExportacaoAjustes
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiï¿½ï¿½o do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenaï¿½ï¿½o do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosExportacaoAjustes2005(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosExportacaoAjustes2005().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaDadosExportacaoAjustes
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiï¿½ï¿½o do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenaï¿½ï¿½o do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosExportacaoAjustes(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosExportacaoAjustes().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosExportacaoAjustes()
{
    $stSQL  = "     SELECT *,                                                               \n";
    $stSQL .= "         CASE WHEN cod_entidade = cod_entidade_prefeitura::INTEGER THEN 1    \n";
    $stSQL .= "              WHEN cod_entidade = cod_entidade_camara::INTEGER THEN 2        \n";
    $stSQL .= "              WHEN cod_entidade = cod_entidade_rpps::INTEGER THEN 3          \n";
    $stSQL .= "              ELSE 9                                                         \n";
    $stSQL .= "         END AS cod_classificacao                                            \n";
    $stSQL .= "        FROM (                                                               \n";
    $stSQL .= " SELECT                                                                      \n";
    $stSQL .= "     (SELECT valor FROM administracao.configuracao WHERE parametro = 'cod_entidade_prefeitura' and exercicio = '2012') AS cod_entidade_prefeitura ,\n";
    $stSQL .= "     (SELECT valor FROM administracao.configuracao WHERE parametro = 'cod_entidade_camara' and exercicio = '2012') AS cod_entidade_camara ,\n";
    $stSQL .= "     (SELECT valor FROM administracao.configuracao WHERE parametro = 'cod_entidade_rpps' and exercicio = '2012') AS cod_entidade_rpps ,\n";
    $stSQL .= "     replace(pc.cod_estrutural,'.','') as cod_estrutural,            \n";
    $stSQL .= "     '9999' as orgao_unidade,                                        \n";

    $stSQL .= "     case when pce.cod_entidade is not null then pce.cod_entidade else pb.cod_entidade end as cod_entidade, \n";

    $stSQL .= "     pr.cod_recurso,                                                 \n";
    $stSQL .= "     replace(pb.cod_banco::VARCHAR,'-','') as cod_banco,                      \n";
    $stSQL .= "     replace(replace(replace(pb.cod_agencia::VARCHAR,' ',''),'.',''),'-','') as cod_agencia,                  \n";
    $stSQL .= "     replace(replace(replace(pb.conta_corrente,' ',''),'.',''),'-','') as conta_corrente,            \n";
    if ($this->getDado("stExercicio") > 2012) {
        $stSQL .= "     CASE SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,7)              \n";
        $stSQL .= "        WHEN '1111101' THEN '1'                                      \n";
        $stSQL .= "        WHEN '1111119' THEN '2'                                      \n";
        $stSQL .= "        WHEN '1111106' THEN '2'                                      \n";
        $stSQL .= "        WHEN '1111150' THEN '3'                                      \n";
        $stSQL .= "        WHEN '114%'  THEN '3'                                        \n";
        $stSQL .= "    END as tipo_conta                                                \n";
    } else {
        $stSQL .= "     CASE SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,5)              \n";
        $stSQL .= "        WHEN '11111' THEN '1'                                        \n";
        $stSQL .= "        WHEN '11112' THEN '2'                                        \n";
        $stSQL .= "        WHEN '11113' THEN '3'                                        \n";
        $stSQL .= "        WHEN '115%'  THEN '3'                                        \n";
        $stSQL .= "    END as tipo_conta                                                \n";
    }
    $stSQL .= " FROM                                                                \n";
    $stSQL .= "     contabilidade.plano_conta as pc                             \n";
    $stSQL .= "     JOIN contabilidade.classificacao_contabil as cc ON          \n";
    $stSQL .="         cc.exercicio = pc.exercicio AND                              \n";
    $stSQL .="         cc.cod_classificacao = pc.cod_classificacao                  \n";

    $stSQL .="      LEFT JOIN  tcers.plano_conta_entidade as pce               \n";
    $stSQL .="          ON (   pc.cod_conta   = pce.cod_conta AND               \n";
    $stSQL .="                 pc.exercicio   = pce.exercicio )                 \n";

    $stSQL .= "     JOIN contabilidade.plano_analitica as pa ON                 \n";
    $stSQL .="         pa.cod_conta = pc.cod_conta AND                              \n";
    $stSQL .="         pa.exercicio = pc.exercicio                                  \n";
    $stSQL .= "     LEFT JOIN contabilidade.plano_banco as pb ON                   \n";
    $stSQL .="         pb.cod_plano = pa.cod_plano AND                              \n";
    $stSQL .="         pb.exercicio = pa.exercicio AND                              \n";
    $stSQL .="         pb.cod_entidade IN ( ".$this->getDado( "stCodEntidade").")   \n";
    $stSQL .= "     LEFT JOIN contabilidade.plano_recurso as pr ON                   \n";
    $stSQL .="         pr.cod_plano = pa.cod_plano AND                              \n";
    $stSQL .="         pr.exercicio = pa.exercicio                                  \n";
    $stSQL .= " WHERE  cc.exercicio =  '".$this->getDado("stExercicio")."'          \n";
    if ($this->getDado("stExercicio") > 2012) {
        $stSQL .= "   AND (SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,3) = '111')   \n";
        $stSQL .= "   AND (SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,3) <> '115')   \n";
    } else {
        $stSQL .= "   AND (SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,3) = '111' OR     \n";
        $stSQL .= "       SUBSTR(REPLACE(pc.cod_estrutural,'.',''),1,3) = '115')        \n";
    }
    $stSQL .= "   AND pc.cod_sistema = 1 \n";
    $stSQL .= " and (                                                               \n";
    $stSQL .= "   pa.exercicio||pa.cod_plano in(                                    \n";
    $stSQL .= "                 select cd.exercicio||cd.cod_plano                   \n";
    $stSQL .= "                  from  contabilidade.conta_debito      as cd    \n";
    $stSQL .= "                   join contabilidade.valor_lancamento  as vl on \n";
    $stSQL .= "                   (                                                 \n";
    $stSQL .= "                     cd.cod_lote          = vl.cod_lote              \n";
    $stSQL .= "                     and cd.tipo          = vl.tipo                  \n";
    $stSQL .= "                     and cd.sequencia     = vl.sequencia             \n";
    $stSQL .= "                     and cd.exercicio     = vl.exercicio             \n";
    $stSQL .= "                     and cd.tipo_valor    = vl.tipo_valor            \n";
    $stSQL .= "                     and cd.cod_entidade  = vl.cod_entidade          \n";
    $stSQL .= "                   )                                                 \n";
    $stSQL .= "                   join contabilidade.lancamento  as la on       \n";
    $stSQL .= "                   (                                                 \n";
    $stSQL .= "                     la.sequencia         = vl.sequencia             \n";
    $stSQL .= "                     and la.cod_lote      = vl.cod_lote              \n";
    $stSQL .= "                     and la.tipo          = vl.tipo                  \n";
    $stSQL .= "                     and la.exercicio     = vl.exercicio             \n";
    $stSQL .= "                     and la.cod_entidade  = vl.cod_entidade          \n";
    $stSQL .= "                   )                                                 \n";
    $stSQL .= "                   join contabilidade.lote as lo on              \n";
    $stSQL .= "                   (                                                 \n";
    $stSQL .= "                     lo.cod_lote          = la.cod_lote              \n";
    $stSQL .= "                     and lo.exercicio     = la.exercicio             \n";
    $stSQL .= "                     and lo.tipo          = la.tipo                  \n";
    $stSQL .= "                     and lo.cod_entidade  = la.cod_entidade          \n";
    $stSQL .= "                   )                                                 \n";
    $stSQL .= "                  where                                              \n";
    $stSQL .= "                   vl.vl_lancamento <> 0.00                          \n";
    $stSQL .= "                   and lo.dt_lote between                            \n";
    $stSQL .= "                             to_date('".$this->getDado("dtInicial")."','dd/mm/yyyy') AND \n";
    $stSQL .= "                             to_date('".$this->getDado("dtFinal")."','dd/mm/yyyy')       \n";
    $stSQL .="                    and cd.cod_entidade IN ( ".$this->getDado( "stCodEntidade").")   \n";
    $stSQL .= "                   group by cd.exercicio, cd.cod_plano               \n";
    $stSQL .= "               )                                                     \n";
    $stSQL .= "   or                                                                \n";
    $stSQL .= "   pa.exercicio||pa.cod_plano in(                                    \n";
    $stSQL .= "                 select cc.exercicio||cc.cod_plano                   \n";
    $stSQL .= "                  from  contabilidade.conta_credito     as cc        \n";
    $stSQL .= "                   join contabilidade.valor_lancamento  as vl on \n";
    $stSQL .= "                   (                                                 \n";
    $stSQL .= "                     cc.cod_lote          = vl.cod_lote              \n";
    $stSQL .= "                     and cc.tipo          = vl.tipo                  \n";
    $stSQL .= "                     and cc.sequencia     = vl.sequencia             \n";
    $stSQL .= "                     and cc.exercicio     = vl.exercicio             \n";
    $stSQL .= "                     and cc.tipo_valor    = vl.tipo_valor            \n";
    $stSQL .= "                     and cc.cod_entidade  = vl.cod_entidade          \n";
    $stSQL .= "                   )                                                 \n";
    $stSQL .= "                  join contabilidade.lancamento  as la on        \n";
    $stSQL .= "                   (                                                 \n";
    $stSQL .= "                     la.sequencia         = vl.sequencia             \n";
    $stSQL .= "                     and la.cod_lote      = vl.cod_lote              \n";
    $stSQL .= "                     and la.tipo          = vl.tipo                  \n";
    $stSQL .= "                     and la.exercicio     = vl.exercicio             \n";
    $stSQL .= "                     and la.cod_entidade  = vl.cod_entidade          \n";
    $stSQL .= "                   )                                                 \n";
    $stSQL .= "                   join contabilidade.lote as lo on              \n";
    $stSQL .= "                   (                                                 \n";
    $stSQL .= "                     lo.cod_lote          = la.cod_lote              \n";
    $stSQL .= "                     and lo.exercicio     = la.exercicio             \n";
    $stSQL .= "                     and lo.tipo          = la.tipo                  \n";
    $stSQL .= "                     and lo.cod_entidade  = la.cod_entidade          \n";
    $stSQL .= "                   )                                                 \n";
    $stSQL .= "                  where                                              \n";
    $stSQL .= "                   vl.vl_lancamento <> 0.00                          \n";
    $stSQL .= "                   and lo.dt_lote between                            \n";
    $stSQL .= "                             to_date('".$this->getDado("dtInicial")."','dd/mm/yyyy') AND \n";
    $stSQL .= "                             to_date('".$this->getDado("dtFinal")."','dd/mm/yyyy')       \n";
    $stSQL .="                    and cc.cod_entidade IN ( ".$this->getDado( "stCodEntidade").")   \n";
    $stSQL .= "                   group by cc.exercicio, cc.cod_plano               \n";
    $stSQL .= "                 )                                                   \n";
    $stSQL .= "  ) order by cod_estrutural                                          \n";
    $stSQL .= "  ) AS tabela                                                        \n";

    return $stSQL;
}

function montaRecuperaDadosExportacaoBalVerificacao()
{  
        $stSQL = " SELECT REPLACE(tabela.cod_estrutural,'.','') AS cod_estrutural                                                                                                                   
                 , cod_entidade
                 , SUBSTR(nom_sistema,1,1) AS natureza_informacao                                                                                                                             
                 , nivel                                                                                                                                                              
                 , tabela.nom_conta                                                                                                                                                          
                 , CASE WHEN vl_saldo_anterior >=0 THEN                                                                                                                                
                                    replace(vl_saldo_anterior::varchar,'-','')                                                                                                                                    
                                ELSE                                                                                                                                                    
                                    '0'                                                                                                                                                  
                                END AS  saldo_anterior_devedora                                                                                                                        
                 , CASE WHEN vl_saldo_anterior <0 THEN                                                                                                                                 
                                replace(vl_saldo_anterior::varchar,'-','')                                                                                                                                    
                            ELSE                                                                                                                                                    
                                '0'                                                                                                                                                  
                            END as  saldo_anterior_credora                                                                                                                         
                 , vl_saldo_debitos                                                                                                                                                   
                 , vl_saldo_creditos * -1 as vl_saldo_creditos                                                                                                                                        
                 , CASE WHEN vl_saldo_atual >=0 THEN                                                                                                                                   
                                 replace(vl_saldo_atual::varchar,'-','')                                                                                                                                       
                             ELSE                                                                                                                                                    
                                 '0'                                                                                                                                                  
                             END as  saldo_atual_devedora                                                                                                                           
                 , CASE WHEN vl_saldo_atual <0 THEN                                                                                                                                    
                                 replace(vl_saldo_atual::varchar,'-','')                                                                                                                                       
                             ELSE                                                                                                                                                    
                                 '0'                                                                                                                                                  
                             END as  saldo_atual_credora                                                                                                                            
                 , CASE WHEN trim(both ' ' from nom_sistema) = 'Não Informado' THEN                                                                                                                     
                             ''                                                                                                                                                    
                             ELSE nom_sistema                                                                                                                                
                             END as nom_sistema                                                                                                                                                   
                 , CASE WHEN tabela.escrituracao = 'analitica' THEN                                                                                                                        
                             'S'                                                                                                                                                   
                     WHEN tabela.escrituracao = 'sintetica' THEN                                                                                                                        
                             'N'                                                                                                                                                   
                             END as escrituracao                                                                                                                                                   
                 , CASE WHEN tabela.indicador_superavit = 'permanente' THEN                                                                                                                        
                             'P'                                                                                                                                                   
                         WHEN tabela.indicador_superavit = 'financeiro' THEN                                                                                                                        
                             'F'                                                                                                                                                   
                             END as indicador_superavit                                                                                                                                                   
                 , tipo_conta                                                                                                                                                         
                 , tabela.cod_recurso                                                                                                                                                 
            FROM (
                    SELECT *
                      FROM ( 
                        SELECT
                            tabela.cod_estrutural
                            , CASE WHEN pce.cod_entidade IS NOT NULL
                                        THEN pce.cod_entidade
                                ELSE pba.cod_entidade
                              END AS cod_entidade
                            , nivel
                            , tabela.nom_conta
                            , vl_saldo_anterior
                            , vl_saldo_debitos
                            , vl_saldo_creditos
                            , vl_saldo_atual 
                            , contabilidade.fn_tipo_conta_plano(pc.exercicio, tabela.cod_estrutural) AS tipo_conta
                            , sc.nom_sistema
                            , escrituracao
                            , tabela.indicador_superavit
                            , CASE WHEN tabela.cod_estrutural LIKE '8.2.1.1.1%'
                                   THEN COALESCE(plano_recurso.cod_recurso, 0)
                                   ELSE 0
                              END AS cod_recurso
                        FROM contabilidade.fn_rl_balancete_verificacao(  '".$this->getDado("stExercicio")."'
                                                                        ,'cod_entidade IN (".$this->getDado("stCodEntidades").")'
                                                                        ,'".$this->getDado("dtInicial")."'
                                                                        ,'".$this->getDado("dtFinal")."'
                                                                        , 'A'::char)
                            AS tabela (cod_estrutural VARCHAR
                                        , nivel INTEGER
                                        , nom_conta VARCHAR
                                        , cod_sistema INTEGER
                                        , indicador_superavit CHAR(12)
                                        , vl_saldo_anterior NUMERIC
                                        , vl_saldo_debitos NUMERIC
                                        , vl_saldo_creditos NUMERIC
                                        , vl_saldo_atual NUMERIC
                                        ) 
                            , contabilidade.plano_conta as pc 
                    LEFT JOIN contabilidade.plano_analitica 
                           ON plano_analitica.cod_conta = pc.cod_conta
                          AND plano_analitica.exercicio = pc.exercicio
                
                    LEFT JOIN contabilidade.plano_recurso
                           ON plano_recurso.cod_plano = plano_analitica.cod_plano
                          AND plano_recurso.exercicio = plano_analitica.exercicio
                    
                    LEFT JOIN ( 
                               SELECT
                                      pb.cod_entidade               
                                    , pa.cod_conta               
                                    , pa.exercicio               
                                 FROM               
                                    contabilidade.plano_banco as pb,               
                                    contabilidade.plano_analitica as pa               
                               WHERE               
                                    pb.cod_plano    = pa.cod_plano AND               
                                    pb.exercicio    = pa.exercicio               
                            ) as pba
                          ON (  pc.cod_conta   = pba.cod_conta AND               
                                pc.exercicio   = pba.exercicio )               
                    LEFT JOIN tcers.plano_conta_entidade as pce               
                           ON ( pc.cod_conta   = pce.cod_conta AND               
                                 pc.exercicio   = pce.exercicio ),               
                        contabilidade.sistema_contabil as sc 
            
                    WHERE '".$this->getDado("stExercicio")."' = pc.exercicio
                      AND tabela.cod_estrutural = pc.cod_estrutural
                      AND pc.exercicio = sc.exercicio
                      AND pc.cod_sistema = sc.cod_sistema 
                    ) AS tabela
                WHERE cod_entidade IS NULL
                   OR cod_entidade IN (".$this->getDado("stCodEntidades").")
                ) AS tabela
            WHERE (vl_saldo_debitos <> 0.00 or vl_saldo_creditos <> 0.00 or vl_saldo_anterior <> 0.00 )
         ORDER BY cod_estrutural; ";
    return $stSQL;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaDadosExportacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiï¿½ï¿½o do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenaï¿½ï¿½o do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosExportacaoBalVerificacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosExportacaoBalVerificacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaDadosExportacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condiï¿½ï¿½o do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenaï¿½ï¿½o do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function verificaMovimentacaoConta(&$rsRecordSet,$boTransacao = "" , $stCondicao = "" , $stOrdem = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaVerificaMovimentacaoConta().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaVerificaMovimentacaoConta()
{
$stSQL  ="        SELECT CASE WHEN (                                                                                     \n";
$stSQL .="         SELECT                                                                                               \n";
$stSQL .="              count(pc.cod_estrutural)                                                                        \n";
$stSQL .="         FROM                                                                                                 \n";
$stSQL .="              contabilidade.plano_conta as pc                                                                 \n";
$stSQL .="              JOIN (                                                                                          \n";
$stSQL .="                 select pad.cod_conta, pad.exercicio, cd.cod_plano from contabilidade.plano_analitica as pad  \n";
$stSQL .="                 INNER JOIN contabilidade.conta_debito as cd on (pad.cod_plano = cd.cod_plano and pad.exercicio = cd.exercicio) \n";
$stSQL .="              ) as pad on ( pc.cod_conta = pad.cod_conta  AND pc.exercicio = pad.exercicio)                   \n";
$stSQL .="              JOIN (                                                                                          \n";
$stSQL .="                select pac.cod_conta, pac.exercicio, cc.cod_plano from contabilidade.plano_analitica as pac   \n";
$stSQL .="                 INNER JOIN contabilidade.conta_credito as cc on (pac.cod_plano = cc.cod_plano and pac.exercicio = cc.exercicio) \n";
$stSQL .="              ) as pac on ( pc.cod_conta = pac.cod_conta  AND pc.exercicio = pac.exercicio)                   \n";
$stSQL .="         WHERE                                                                                                \n";
$stSQL .="                 pc.cod_estrutural like publico.fn_mascarareduzida('".$this->getDado('stCodEstrutural')."')||'%'                              \n";
$stSQL .="             AND pc.exercicio = '".$this->getDado('exercicio')."'                                             \n";
$stSQL .="         ) > 1 THEN true ELSE false END as RETORNO;                                                           \n";

    return $stSQL;
}

/**
    * Seta os dados pra fazer o recuperaEmpenhoEsfinge
    * @access Private
    * @return $stSql
*/
function montaRecuperaContaContabilEsfinge()
{
    $stSql  = "
select plano_conta.exercicio
--      ,plano_conta.cod_estrutural
--      ,case
--         --receita
--         when substring(cod_estrutural from 1 for 1) = '4' then 3
--         --despesa
--         when substring(cod_estrutural from 1 for 1) = '3' then 4
--         --conta nao vinculada
--         when substring(cod_estrutural from 1 for 1) = '1.1.1.1.1'
--          and substring(cod_estrutural from 1 for 1) = '1.1.1.1.2' then 2
--         --conta vinculada
--         when substring(cod_estrutural from 1 for 1) = '1.1.1.1.3' then 1
--         else 9
--      end as tipo_conta
--     ,plano_conta.nom_conta
--     ,case
--         when plano_analitica.cod_conta is not null then 1
--         else 0
--     end as analitica
--     ,publico.fn_nivel(plano_conta.cod_estrutural) as nivel_conta
--     ,case
--         --debito
--         when conta_debito.cod_plano is not null and conta_credito.cod_plano is null then 1
--         --credito
--         when conta_debito.cod_plano is null and conta_credito.cod_plano is not null then 2
--         --misto
--         when conta_debito.cod_plano is not null and conta_credito.cod_plano is not null then 3
--     end as tipo_natureza_saldo
--     ,plano_conta.cod_conta
--     ,publico.fn_mascara_completa('0.0.0.0.0.00.00.00.00.00', publico.fn_codigo_superior(plano_conta.cod_estrutural)) as cod_estrutural_superior
--     , plano_banco.cod_banco
--     , plano_banco.cod_agencia
--     , plano_banco.conta_corrente
from contabilidade.plano_conta
left join contabilidade.plano_analitica
  on plano_conta.exercicio = plano_analitica.exercicio
 and plano_conta.cod_conta = plano_analitica.cod_conta
left join contabilidade.conta_credito
  on plano_analitica.exercicio = conta_credito.exercicio
 and plano_analitica.cod_plano = conta_credito.cod_plano
left join contabilidade.conta_debito
  on plano_analitica.exercicio = conta_debito.exercicio
 and plano_analitica.cod_plano = conta_debito.cod_plano
left join contabilidade.plano_banco
  on plano_analitica.exercicio = plano_banco.exercicio
 and plano_analitica.cod_plano = plano_banco.cod_plano
 where  plano_conta.exercicio = '".$this->getDado('exercicio')."'";

    return $stSql;
}

/**
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaContaContabilEsfinge(&$rsRecordSet, $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaContaContabilEsfinge();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaContaSintetica(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
        return $this->executaRecupera("montaRecuperaContaSintetica",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}
function montaRecuperaContaSintetica()
{
    $stSql = "
        SELECT  cod_conta
             ,  cod_estrutural
             ,  nom_conta
          FROM  contabilidade.plano_conta
         WHERE  exercicio = '".$this->getDado('exercicio')."'
           AND  NOT EXISTS  (   SELECT 1
                                  FROM contabilidade.plano_analitica
                                 WHERE plano_analitica.cod_conta = plano_conta.cod_conta
                                   AND plano_analitica.exercicio = plano_conta.exercicio
                            )
    ";
    if ( $this->getDado('cod_estrutural') ) {
        $stSql.= " AND cod_estrutural = '".$this->getDado('cod_estrutural')."' ";
    }
    if ( $this->getDado('descricao') ) {
        $stSql.= " AND nom_conta ILIKE '%".$this->getDado('descricao')."%' ";
    }

    return $stSql;
}

function verificaContaDesdobrada(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
        return $this->executaRecupera("montaVerificaContaDesdobrada",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}
function montaVerificaContaDesdobrada()
{
    $stSql .= "
            SELECT CASE WHEN ( SELECT contas FROM (
                SELECT  count(1) as contas
                  FROM  contabilidade.plano_conta
                 WHERE  exercicio = '".$this->getDado('exercicio')."'                                                        \n";

                if ( $this->getDado('cod_estrutural') ) {
                    $stSql.= " AND cod_estrutural LIKE publico.fn_mascarareduzida('".$this->getDado('cod_estrutural')."')||'%' ";
                }

    $stSql .=" ) as stbl ) > 1 THEN true ELSE false END as RETORNO;                                                                      ";

    return $stSql;

}

 /*
    RECUPERA RECEITAS AS SEREM EXTRA-CORCAMENTARIAS/
    */
function recuperaClassReceitasExtraOrcamentariasCredito(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
        return $this->executaRecupera("montaRecuperaClassReceitasExtraOrcamentariasCredito",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}
function montaRecuperaClassReceitasExtraOrcamentariasCredito()
{
/*$stSql = "SELECT plano_analitica.cod_plano AS codigo
            , plano_analitica.exercicio
            , mccc.cod_entidade
            , plano_conta.cod_estrutural
            , plano_conta.nom_conta AS DESC
            , CASE
                WHEN (  ( SELECT plano_analitica_credito_acrescimo.cod_credito
                            FROM contabilidade.plano_analitica_credito_acrescimo
                           WHERE plano_analitica.cod_plano = plano_analitica_credito_acrescimo.cod_plano
                             AND plano_analitica.exercicio = plano_analitica_credito_acrescimo.exercicio
                           LIMIT 1
                        ) IS NOT NULL
                     )
                     OR
                     (  ( SELECT plano_analitica_credito.cod_credito
                            FROM contabilidade.plano_analitica_credito
                           WHERE plano_analitica.cod_plano = plano_analitica_credito.cod_plano
                             AND plano_analitica.exercicio = plano_analitica_credito.exercicio
                           LIMIT 1
                        ) IS NOT NULL
                     )
                  THEN
                    true
                  ELSE
                    false
                 END AS possui_creditos
            , CASE WHEN (arrec.cod_plano_credito IS NOT NULL) THEN
                       FALSE
                  ELSE
                       TRUE
                  END AS npossui_arrecadacao

         FROM ( SELECT *
                 FROM contabilidade.plano_conta
                WHERE plano_conta.cod_estrutural LIKE '1.1.2.%'
                  AND plano_conta.exercicio = ". $this->getDado('exercicio') ."
              ) AS plano_conta
            , contabilidade.plano_analitica
            LEFT JOIN (  SELECT
                              tesouraria.boletim_lote_transferencia.exercicio,
                              tesouraria.transferencia.cod_plano_credito

                         FROM tesouraria.boletim_lote_transferencia
                             ,tesouraria.transferencia
                         WHERE
                              boletim_lote_transferencia.exercicio    = transferencia.exercicio
                          AND boletim_lote_transferencia.cod_entidade = transferencia.cod_entidade
                          AND boletim_lote_transferencia.tipo         = transferencia.tipo
                          AND boletim_lote_transferencia.cod_lote     = transferencia.cod_lote
                          AND boletim_lote_transferencia.exercicio = '". $this->getDado('exercicio') ."'
                         GROUP BY
                              1,2
                      )    AS arrec  ON  (
                              contabilidade.plano_analitica.exercicio    = arrec.exercicio
                         AND  contabilidade.plano_analitica.cod_plano    = arrec.cod_plano_credito
                      )
       LEFT JOIN  ( SELECT  cod_credito, cod_especie, cod_genero, cod_natureza, exercicio, cod_entidade, cod_plano
                    FROM
                    contabilidade.plano_banco
                    JOIN  monetario.credito_conta_corrente ON
                          plano_banco.cod_banco = credito_conta_corrente.cod_banco AND
                          plano_banco.cod_agencia =  credito_conta_corrente.cod_agencia AND
                          plano_banco.cod_conta_corrente = credito_conta_corrente.cod_conta_corrente
                          ) mccc  ON
                          (
                          mccc.exercicio = plano_analitica.exercicio AND
                          mccc.cod_plano = plano_analitica.cod_plano
                          )
                    WHERE plano_conta.cod_conta = plano_analitica.cod_conta
                      AND plano_conta.exercicio = plano_analitica.exercicio	";
*/
    $stSql = "
    SELECT    plano_analitica.cod_plano AS codigo
            , plano_analitica.exercicio
            , CASE WHEN (creditos.cod_entidade IS NOT NULL) OR
                        (acrescimos.cod_entidade IS NOT NULL ) THEN
                CASE WHEN (creditos.cod_entidade IS NOT NULL) THEN
                    creditos.cod_entidade
                ELSE
                    acrescimos.cod_entidade
                END
              END AS cod_entidade
            , plano_conta.cod_estrutural
            , plano_conta.nom_conta AS DESC
            , CASE WHEN (creditos.cod_credito IS NOT NULL) OR (acrescimos.cod_credito IS NOT NULL) THEN
                            TRUE
                        ELSE
                            FALSE
                        END AS possui_creditos
            , CASE WHEN (arrec.cod_plano_credito IS NOT NULL) THEN
                       FALSE
                  ELSE
                       TRUE
                  END AS npossui_arrecadacao
    FROM ( SELECT *
           FROM contabilidade.plano_conta
            WHERE plano_conta.cod_estrutural LIKE '1.1.2.%' OR plano_conta.cod_estrutural LIKE '2.1.1.%' OR plano_conta.cod_estrutural LIKE '5%' OR plano_conta.cod_estrutural LIKE '6%'

         AND plano_conta.exercicio = '" . $this->getDado('exercicio') . "'
         ) AS plano_conta
         , contabilidade.plano_analitica

           --Verifica Arrecadações
           LEFT JOIN (  SELECT
                             tesouraria.boletim_lote_transferencia.exercicio,
                             tesouraria.transferencia.cod_plano_credito
                        FROM tesouraria.boletim_lote_transferencia
                            ,tesouraria.transferencia
                        WHERE
                             boletim_lote_transferencia.exercicio    = transferencia.exercicio
                         AND boletim_lote_transferencia.cod_entidade = transferencia.cod_entidade
                         AND boletim_lote_transferencia.tipo         = transferencia.tipo
                         AND boletim_lote_transferencia.cod_lote     = transferencia.cod_lote
                         AND boletim_lote_transferencia.exercicio = '" . $this->getDado('exercicio') . "'
                        GROUP BY 1,2
                     )    AS arrec  ON  (
                          contabilidade.plano_analitica.exercicio = arrec.exercicio
                      AND contabilidade.plano_analitica.cod_plano = arrec.cod_plano_credito
                     )
           --Verifica créditos e entidade do crédito
           LEFT JOIN ( SELECT
                         plano_analitica_credito.cod_credito
                       , plano_analitica_credito.cod_plano
                       , plano_analitica_credito.exercicio
                       , mccc.cod_entidade
                       FROM
                       monetario.credito
                       JOIN contabilidade.plano_analitica_credito ON
                                 plano_analitica_credito.cod_credito  = credito.cod_credito
                            AND  plano_analitica_credito.cod_natureza = credito.cod_natureza
                            AND  plano_analitica_credito.cod_genero   = credito.cod_genero
                            AND  plano_analitica_credito.cod_especie  = credito.cod_especie
                            AND  plano_analitica_credito.exercicio = '" . $this->getDado('exercicio') . "'
                       LEFT JOIN  ( SELECT
                                     cod_credito
                                    ,cod_especie
                                    ,cod_genero
                                    ,cod_natureza
                                    ,exercicio
                                    ,cod_entidade
                                    ,cod_plano
                                  FROM
                                       contabilidade.plano_banco
                                  JOIN monetario.credito_conta_corrente ON
                                       plano_banco.cod_banco = credito_conta_corrente.cod_banco
                                       AND  plano_banco.cod_agencia =  credito_conta_corrente.cod_agencia
                                       AND  plano_banco.cod_conta_corrente = credito_conta_corrente.cod_conta_corrente
                                       AND  plano_banco.exercicio = '" . $this->getDado('exercicio') . "'
                                  WHERE exercicio = '" . $this->getDado('exercicio') . "'
                                  GROUP BY 1,2,3,4,5,6,7
                                  ) mccc  ON  (
                                         mccc.cod_credito  = credito.cod_credito
                                    AND  mccc.cod_natureza = credito.cod_natureza
                                    AND  mccc.cod_genero   = credito.cod_genero
                                    AND  mccc.cod_especie  = credito.cod_especie
                                  )
                                  GROUP BY 1,2,3,4
                     ) creditos on (
                            creditos.exercicio = plano_analitica.exercicio
                        AND creditos.cod_plano = plano_analitica.cod_plano
                     )
           --Verifica acréscimos e entidade do acrescimo
           LEFT JOIN ( SELECT
                         plano_analitica_credito_acrescimo.cod_credito
                       , plano_analitica_credito_acrescimo.cod_plano
                       , plano_analitica_credito_acrescimo.exercicio
                       , mccc.cod_entidade
                       FROM
                       monetario.credito
                       JOIN contabilidade.plano_analitica_credito_acrescimo ON
                                 plano_analitica_credito_acrescimo.cod_credito  = credito.cod_credito
                            AND  plano_analitica_credito_acrescimo.cod_natureza = credito.cod_natureza
                            AND  plano_analitica_credito_acrescimo.cod_genero   = credito.cod_genero
                            AND  plano_analitica_credito_acrescimo.cod_especie  = credito.cod_especie
                            AND  plano_analitica_credito_acrescimo.exercicio = '" . $this->getDado('exercicio') . "'
                       LEFT JOIN  ( SELECT
                                       cod_credito
                                      ,cod_especie
                                      ,cod_genero
                                      ,cod_natureza
                                      ,exercicio
                                      ,cod_entidade
                                      ,cod_plano
                                    FROM
                                         contabilidade.plano_banco
                                    JOIN monetario.credito_conta_corrente ON
                                         plano_banco.cod_banco = credito_conta_corrente.cod_banco AND
                                         plano_banco.cod_agencia =  credito_conta_corrente.cod_agencia AND
                                         plano_banco.cod_conta_corrente = credito_conta_corrente.cod_conta_corrente AND
                                         plano_banco.exercicio = '" . $this->getDado('exercicio') . "'
                                    WHERE exercicio = '" . $this->getDado('exercicio') . "'
                                    GROUP BY 1,2,3,4,5,6,7
                                  ) mccc  ON  (
                                         mccc.cod_credito  = credito.cod_credito
                                     AND  mccc.cod_natureza = credito.cod_natureza
                                     AND  mccc.cod_genero   = credito.cod_genero
                                     AND  mccc.cod_especie  = credito.cod_especie
                                  )
                       GROUP BY 1,2,3,4
                     ) acrescimos on (
                              acrescimos.cod_plano   =  plano_analitica.cod_plano
                         AND  acrescimos.exercicio   =  plano_analitica.exercicio
                     )
        WHERE plano_conta.cod_conta = plano_analitica.cod_conta
        AND plano_conta.exercicio = plano_analitica.exercicio
        ";
        // filtro
        $stSql .= " AND plano_conta.exercicio = '" . $this->getDado('exercicio') . "' ";
        if ( $this->getDado('plano_inicial') )
             $stSql .= " AND plano_analitica.cod_plano BETWEEN '" . $this->getDado('plano_inicial') . "' \n";
        if ( $this->getDado('plano_final') )
             $stSql .= "	 AND '" . $this->getDado('plano_final') . "'";

             return $stSql;
}

/*
 * AS DUAS ABAIXO BUSCAM OS CREDITOS E ACRESCIMOS LISTADOS
 * NA CLASSIFICACAO DE RECEITAS QUANDO O CARA SELECIONA EXTRA-ORC E ENTRE NO FM
 */
function recuperaClassReceitasCreditosExtraOrcamentarios(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
        return $this->executaRecupera("montaRecuperaClassReceitasCreditosExtraOrcamentarios",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}
function montaRecuperaClassReceitasCreditosExtraOrcamentarios()
{
    $stSql = "
           select credito.cod_credito
            , credito.cod_especie
            , credito.cod_genero
            , credito.cod_natureza
                      , credito.cod_credito || '.' || credito.cod_especie || '.' || credito.cod_genero || '.' || credito.cod_natureza as codigo
                        , credito.descricao_credito as desc
                        , null as cod_acrescimo
                        , null as cod_tipo
                        , '' as descricao_acrescimo

         from ( select *
                 from contabilidade.plano_conta
                where plano_conta.cod_estrutural like '1.1.2.%'
                   or plano_conta.cod_estrutural like '2.%'
                   or plano_conta.cod_estrutural like '5.%'
                   or plano_conta.cod_estrutural like '6.%'
                  and plano_conta.exercicio = '". $this->getDado('exercicio') ."'
              ) as plano_conta
            , contabilidade.plano_analitica
              join contabilidade.plano_analitica_credito
                     on plano_analitica.cod_plano = plano_analitica_credito.cod_plano
                    and plano_analitica.exercicio = plano_analitica_credito.exercicio
              join monetario.credito
                     on credito.cod_credito  = plano_analitica_credito.cod_credito
                    and credito.cod_especie  = plano_analitica_credito.cod_especie
                    and credito.cod_genero   = plano_analitica_credito.cod_genero
                    and credito.cod_natureza = plano_analitica_credito.cod_natureza
          where plano_conta.cod_conta = plano_analitica.cod_conta
          and plano_conta.exercicio = plano_analitica.exercicio	";
    // filtro
    $stSql .= " and plano_analitica.exercicio = '" . $this->getDado('exercicio') . "' ";
    if ( $this->getDado('codigo') ) {
        $stSql .= " and plano_analitica.cod_plano = " . $this->getDado('codigo') . " \n";
    }

    $stSql .= "
             union
       select credito.cod_credito
            , credito.cod_especie
            , credito.cod_genero
            , credito.cod_natureza
                      , credito.cod_credito || '.' || credito.cod_especie || '.' || credito.cod_genero || '.' || credito.cod_natureza as codigo
                        , credito.descricao_credito as desc
                        , acrescimo.cod_acrescimo
                        , acrescimo.cod_tipo
                        , acrescimo.descricao_acrescimo

         from ( select *
                 from contabilidade.plano_conta
                where plano_conta.cod_estrutural like '1.1.2.%'
                   or plano_conta.cod_estrutural like '2.%'
                   or plano_conta.cod_estrutural like '5.%'
                   or plano_conta.cod_estrutural like '6.%'
                  and plano_conta.exercicio = '". $this->getDado('exercicio') ."'
              ) as plano_conta
            , contabilidade.plano_analitica
              join contabilidade.plano_analitica_credito_acrescimo
                     on plano_analitica.cod_plano = plano_analitica_credito_acrescimo.cod_plano
                    and plano_analitica.exercicio = plano_analitica_credito_acrescimo.exercicio
              join monetario.credito
                     on credito.cod_credito  = plano_analitica_credito_acrescimo.cod_credito
                    and credito.cod_especie  = plano_analitica_credito_acrescimo.cod_especie
                    and credito.cod_genero   = plano_analitica_credito_acrescimo.cod_genero
                    and credito.cod_natureza = plano_analitica_credito_acrescimo.cod_natureza
              join monetario.credito_acrescimo
                     on credito_acrescimo.cod_credito  = plano_analitica_credito_acrescimo.cod_credito
                    and credito_acrescimo.cod_especie  = plano_analitica_credito_acrescimo.cod_especie
                    and credito_acrescimo.cod_genero   = plano_analitica_credito_acrescimo.cod_genero
                    and credito_acrescimo.cod_natureza = plano_analitica_credito_acrescimo.cod_natureza
                    and credito_acrescimo.cod_acrescimo = plano_analitica_credito_acrescimo.cod_acrescimo
                    and credito_acrescimo.cod_tipo = plano_analitica_credito_acrescimo.cod_tipo
                            join monetario.acrescimo
                                         on acrescimo.cod_acrescimo = credito_acrescimo.cod_acrescimo
                    and acrescimo.cod_tipo = credito_acrescimo.cod_tipo

        where plano_conta.cod_conta = plano_analitica.cod_conta
          and plano_conta.exercicio = plano_analitica.exercicio	";
    // filtro
    $stSql .= " and plano_analitica.exercicio = '" . $this->getDado('exercicio') . "' ";
    if ( $this->getDado('codigo') ) {
        $stSql .= " and plano_analitica.cod_plano = " . $this->getDado('codigo') . " \n";
    }

    return $stSql;
}

/*
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stFiltro
    * @param  String  $stOrdem
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaCodEstrutural(&$rsRecordSet, $stFiltro, $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY
$stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaCodEstrutural().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCodEstrutural()
{
    $stSql = "SELECT cod_estrutural
                FROM contabilidade.plano_conta pc
          INNER JOIN contabilidade.plano_analitica pa
                  ON pc.exercicio = pa.exercicio AND pc.cod_conta = pa.cod_conta";

    return $stSql;
}

function recuperaDadosExportacaoBalVerificacaoEnceramento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    } else {
        $stOrdem = " ORDER BY cod_estrutural ";
    }

    if (Sessao::getExercicio() < '2015') {
        $stSql = $this->montaRecuperaDadosExportacaoBalVerificacaoEnceramento().$stCondicao.$stOrdem;
    } else {
        $stSql = $this->montaRecuperaDadosExportacaoBalVerificacaoEnceramento2016().$stCondicao.$stOrdem;
    }

    
    $this->setDebug( $stSql );    
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosExportacaoBalVerificacaoEnceramento()
{
    $stSQL  = " SELECT
                        replace(cod_estrutural,'.','') as cod_estrutural
                        ,CASE WHEN vl_saldo_anterior >=0 THEN
                                replace(vl_saldo_anterior::varchar,'-','')
                            ELSE
                                '0'
                        END as  saldo_anterior_devedora
                        ,CASE WHEN vl_saldo_anterior <0 THEN
                                replace(vl_saldo_anterior::varchar,'-','')
                            ELSE
                                '0'
                        END as  saldo_anterior_credora
                        ,vl_saldo_debitos
                        ,vl_saldo_creditos * -1 as vl_saldo_creditos
                        ,CASE WHEN vl_saldo_atual >=0 THEN
                                replace(vl_saldo_atual::varchar,'-','')
                            ELSE
                                '0'
                        END as  saldo_atual_devedora
                        ,CASE WHEN vl_saldo_atual <0 THEN
                                replace(vl_saldo_atual::varchar,'-','')
                            ELSE
                                '0'
                        END as  saldo_atual_credora
                        ,nom_conta
                        ,cod_entidade
                        ,tipo_conta
                        ,nivel
                        ,CASE WHEN trim(both ' ' from nom_sistema) = 'Não Informado' THEN
                                ''
                            ELSE substr(nom_sistema,1,1)
                         END as nom_sistema
                        ,CASE WHEN trim(both ' ' from nom_sistema) = 'Não Informado' THEN
                                ''
                            ELSE nom_sistema
                        END as natureza
                        ,CASE WHEN escrituracao = 'analitica' THEN
                                'S'
                            WHEN escrituracao = 'sintetica' THEN
                                'N'
                        END as escrituracao
                        ,CASE WHEN indicador_superavit = 'permanente' THEN
                                'P'
                            WHEN indicador_superavit = 'financeiro' THEN
                                'F'
                        END as indicador_superavit
     FROM
         contabilidade.fn_exportacao_balancete_verificacao('".$this->getDado("stExercicio")."'
                                                            ,' cod_entidade IN (".$this->getDado("stCodEntidades").")'
                                                            ,'".$this->getDado("dtInicial")."'
                                                            ,'".$this->getDado("dtFinal")."') 
     AS
         tabela( cod_estrutural      VARCHAR,
                 cod_entidade        INTEGER,
                 nivel               INTEGER,
                 nom_conta           VARCHAR,
                 vl_saldo_anterior   NUMERIC,
                 vl_saldo_debitos    NUMERIC,
                 vl_saldo_creditos   NUMERIC,
                 vl_saldo_atual      NUMERIC,
                 tipo_conta          VARCHAR,
                 nom_sistema         VARCHAR,
                 escrituracao        CHAR(9),
                 indicador_superavit CHAR(12))
     WHERE (vl_saldo_debitos <> 0.00 or vl_saldo_creditos <> 0.00 or vl_saldo_anterior <> 0.00 ) 
    ";
    return $stSQL;
}

function montaRecuperaDadosExportacaoBalVerificacaoEnceramento2016()
{
    $stSQL  = " SELECT
                        REPLACE(tabela.cod_estrutural,'.','') AS cod_estrutural
                      , CASE WHEN vl_saldo_anterior >= 0 THEN REPLACE(tabela.vl_saldo_anterior::varchar,'-','')
                             ELSE '0'
                        END AS  saldo_anterior_devedora
                      , CASE WHEN tabela.vl_saldo_anterior < 0 THEN REPLACE(tabela.vl_saldo_anterior::varchar,'-','')
                            ELSE '0'
                        END AS saldo_anterior_credora
                      , tabela.vl_saldo_debitos
                      , tabela.vl_saldo_creditos * -1 AS vl_saldo_creditos
                      , CASE WHEN tabela.vl_saldo_atual >= 0 THEN REPLACE(tabela.vl_saldo_atual::varchar,'-','')
                            ELSE '0'
                        END AS saldo_atual_devedora
                      , CASE WHEN tabela.vl_saldo_atual < 0 THEN REPLACE(tabela.vl_saldo_atual::varchar,'-','')
                             ELSE '0'
                        END AS saldo_atual_credora
                      , tabela.nom_conta
                      , tabela.cod_entidade
                      , tabela.tipo_conta
                      , tabela.nivel
                      , CASE WHEN trim(both ' ' FROM tabela.nom_sistema) = 'Não Informado' THEN ''
                             ELSE substr(tabela.nom_sistema,1,1)
                        END AS nom_sistema
                      , CASE WHEN trim(both ' ' FROM tabela.nom_sistema) = 'Não Informado' THEN ''
                             ELSE tabela.nom_sistema
                        END AS natureza
                      , CASE WHEN tabela.escrituracao = 'analitica' THEN 'S'
                             WHEN tabela.escrituracao = 'sintetica' THEN 'N'
                        END AS escrituracao
                      , CASE WHEN tabela.indicador_superavit = 'permanente' THEN 'P'
                             WHEN tabela.indicador_superavit = 'financeiro' THEN 'F'
                        END AS indicador_superavit
                      , COALESCE(recurso.cod_recurso, 0) AS cod_recurso

                  FROM contabilidade.fn_exportacao_balancete_verificacao ('".$this->getDado("stExercicio")."'
                                                                          ,' cod_entidade IN (".$this->getDado("stCodEntidades").")'
                                                                          ,'".$this->getDado("dtInicial")."'
                                                                          ,'".$this->getDado("dtFinal")."'
                                                                         ) 
                    AS tabela ( cod_estrutural      VARCHAR,
                                cod_entidade        INTEGER,
                                nivel               INTEGER,
                                nom_conta           VARCHAR,
                                vl_saldo_anterior   NUMERIC,
                                vl_saldo_debitos    NUMERIC,
                                vl_saldo_creditos   NUMERIC,
                                vl_saldo_atual      NUMERIC,
                                tipo_conta          VARCHAR,
                                nom_sistema         VARCHAR,
                                escrituracao        CHAR(9),
                                indicador_superavit CHAR(12)
                              )

            INNER JOIN contabilidade.plano_conta
                    ON plano_conta.cod_estrutural = tabela.cod_estrutural
                   AND plano_conta.exercicio = '".$this->getDado("stExercicio")."'

             LEFT JOIN contabilidade.plano_analitica
                    ON plano_analitica.cod_conta = plano_conta.cod_conta
                   AND plano_analitica.exercicio = plano_conta.exercicio

             LEFT JOIN contabilidade.plano_recurso
                    ON plano_recurso.cod_plano = plano_analitica.cod_plano
                   AND plano_recurso.exercicio = plano_analitica.exercicio

             LEFT JOIN orcamento.recurso
                    ON recurso.exercicio = plano_recurso.exercicio
                   AND recurso.cod_recurso = plano_recurso.cod_recurso

                  WHERE (vl_saldo_debitos <> 0.00 OR vl_saldo_creditos <> 0.00 OR vl_saldo_anterior <> 0.00 ) 
    ";
    return $stSQL;
}

}
?>
