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
    * Classe de mapeamento da tabela FN_ORCAMENTO_BALANCETE_DESPESA
    * Data de Criação: 24/09/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.22
*/
require_once CLA_PERSISTENTE;

class FOrcamentoBalanceteDespesa extends Persistente
{
    /**
        * Método Construtor
        * @access public
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('orcamento.fn_balancete_despesa');
    
        $this->AddCampo('exercicio'             ,'varchar',false,    '',false,false);
        $this->AddCampo('cod_despesa'           ,'integer',false,    '',false,false);
        $this->AddCampo('cod_entidade'          ,'integer',false,    '',false,false);
        $this->AddCampo('cod_programa'          ,'integer',false,    '',false,false);
        $this->AddCampo('cod_conta'             ,'integer',false,    '',false,false);
        $this->AddCampo('num_pao'               ,'integer',false,    '',false,false);
        $this->AddCampo('num_orgao'             ,'integer',false,    '',false,false);
        $this->AddCampo('num_unidade'           ,'integer',false,    '',false,false);
        $this->AddCampo('cod_recurso'           ,'integer',false,    '',false,false);
        $this->AddCampo('cod_funcao'            ,'integer',false,    '',false,false);
        $this->AddCampo('cod_subfuncao'         ,'integer',false,    '',false,false);
        $this->AddCampo('tipo_conta'            ,'varchar',false,    '',false,false);
        $this->AddCampo('vl_original'           ,'numeric',false,'14.2',false,false);
        $this->AddCampo('dt_criacao'            ,   'date',false,    '',false,false);
        $this->AddCampo('classificacao'         ,'varchar',false,    '',false,false);
        $this->AddCampo('descricao'             ,'varchar',false,    '',false,false);
        $this->AddCampo('num_recurso'           ,'varchar',false,    '',false,false);
        $this->AddCampo('nom_recurso'           ,'varchar',false,    '',false,false);
        $this->AddCampo('nom_orgao'             ,'varchar',false,    '',false,false);
        $this->AddCampo('nom_unidade'           ,'varchar',false,    '',false,false);
        $this->AddCampo('nom_funcao'            ,'varchar',false,    '',false,false);
        $this->AddCampo('nom_subfuncao'         ,'varchar',false,    '',false,false);
        $this->AddCampo('nom_programa'          ,'varchar',false,    '',false,false);
        $this->AddCampo('nom_pao'               ,'varchar',false,    '',false,false);
        $this->AddCampo('empenhado_ano'         ,'numeric',false,'14.2',false,false);
        $this->AddCampo('empenhado_per'         ,'numeric',false,'14.2',false,false);
        $this->AddCampo('anulado_ano'           ,'numeric',false,'14.2',false,false);
        $this->AddCampo('anulado_per'           ,'numeric',false,'14.2',false,false);
        $this->AddCampo('pago_ano'              ,'numeric',false,'14.2',false,false);
        $this->AddCampo('pago_per'              ,'numeric',false,'14.2',false,false);
        $this->AddCampo('liquidado_ano'         ,'numeric',false,'14.2',false,false);
        $this->AddCampo('liquidado_per'         ,'numeric',false,'14.2',false,false);
        $this->AddCampo('saldo_inicial'         ,'numeric',false,'14.2',false,false);
        $this->AddCampo('suplementacoes'        ,'numeric',false,'14.2',false,false);
        $this->AddCampo('reducoes'              ,'numeric',false,'14.2',false,false);
        $this->AddCampo('total_creditos'        ,'numeric',false,'14.2',false,false);
        $this->AddCampo('credito_suplementar'   ,'numeric',false,'14.2',false,false);
        $this->AddCampo('credito_especial'      ,'numeric',false,'14.2',false,false);
        $this->AddCampo('credito_extraordinario','numeric',false,'14.2',false,false);
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
              SELECT *
                FROM ".$this->getTabela()."( '".$this->getDado("exercicio")."'
                                           , '".$this->getDado("stFiltro")."'
                                           , '".$this->getDado("stDataInicial")."'
                                           , '".$this->getDado("stDataFinal")."'
                                           , '".$this->getDado("stCodEstruturalInicial")."'
                                           , '".$this->getDado("stCodEstruturalFinal")."'
                                           , '".$this->getDado("stCodReduzidoInicial")."'
                                           , '".$this->getDado("stCodReduzidoFinal")."'
                                           , '".$this->getDado("stControleDetalhado")."'
                                           , '".$this->getDado("inNumOrgao")."'
                                           , '".$this->getDado("inNumUnidade")."'
                                           , '".$this->getDado('stVerificaCreateDropTables')."'
                                           )
                                  AS retorno
                                           ( exercicio              char(4)
                                           , cod_despesa            integer
                                           , cod_entidade           integer
                                           , cod_programa           integer
                                           , cod_conta              integer
                                           , num_pao                integer
                                           , num_orgao              integer
                                           , num_unidade            integer
                                           , cod_recurso            integer
                                           , cod_funcao             integer
                                           , cod_subfuncao          integer
                                           , tipo_conta             varchar
                                           , vl_original            numeric
                                           , dt_criacao             date
                                           , classificacao          varchar
                                           , descricao              varchar
                                           , num_recurso            varchar
                                           , nom_recurso            varchar
                                           , nom_orgao              varchar
                                           , nom_unidade            varchar
                                           , nom_funcao             varchar
                                           , nom_subfuncao          varchar
                                           , nom_programa           varchar
                                           , nom_pao                varchar
                                           , empenhado_ano          numeric
                                           , empenhado_per          numeric
                                           , anulado_ano            numeric
                                           , anulado_per            numeric
                                           , pago_ano               numeric
                                           , pago_per               numeric
                                           , liquidado_ano          numeric
                                           , liquidado_per          numeric
                                           , saldo_inicial          numeric
                                           , suplementacoes         numeric
                                           , reducoes               numeric
                                           , total_creditos         numeric
                                           , credito_suplementar    numeric
                                           , credito_especial       numeric
                                           , credito_extraordinario numeric
                                           , num_programa           varchar
                                           , num_acao               varchar
                                           )
        ";
        return $stSql;
    }

function recuperaTransparencia(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaTransparencia().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTransparencia()
{
    $stSql  = " SELECT
                        cod_entidade,
                        cod_despesa,
                        num_orgao,
                        num_unidade,
                        cod_funcao,
                        cod_subfuncao,
                        cod_programa,
                        0 as cod_subprograma,
                        num_pao,
                        replace(tabela.classificacao,'.','') as cod_subelemento,
                        cod_recurso,
                        saldo_inicial,
                        0 as atualizacao,
                        coalesce(tabela.credito_suplementar,0.00) as credito_suplementar,
                        coalesce(tabela.credito_especial,0.00) as credito_especial,
                        coalesce(tabela.credito_extraordinario,0.00) as credito_extraordinario,
                        coalesce(tabela.reducoes,0.00) as reducoes,
                        tabela.suplementacoes,
                        0.00 as reducao,
                        SUM(tabela.empenhado_per) as empenhado_per,
                        SUM(tabela.anulado_per) as anulado_per,
                        SUM(tabela.liquidado_per) as liquidado_per,
                        SUM(tabela.pago_per) as pago_per,
                        tabela.total_creditos as total_creditos,
                        SUM(tabela.empenhado_per) - SUM(tabela.anulado_per) as vl_empenho

                FROM ".$this->getTabela()."('".$this->getDado("stExercicio").
                                          "', 'AND OD.cod_entidade IN (".$this->getDado("stEntidades").")".
                                          "','".$this->getDado("stDataInicial").
                                          "','".$this->getDado("stDataFinal").
                                          "', '', '', '', '', '', '', '', ''
                                          ) as tabela(
                                                        exercicio               char(4),
                                                        cod_despesa             integer,
                                                        cod_entidade            integer,
                                                        cod_programa            integer,
                                                        cod_conta               integer,
                                                        num_pao                 integer,
                                                        num_orgao               integer,
                                                        num_unidade             integer,
                                                        cod_recurso             integer,
                                                        cod_funcao              integer,
                                                        cod_subfuncao           integer,
                                                        tipo_conta              varchar,
                                                        vl_original             numeric,
                                                        dt_criacao              date,
                                                        classificacao           varchar,
                                                        descricao               varchar,
                                                        num_recurso             varchar,
                                                        nom_recurso             varchar,
                                                        nom_orgao               varchar,
                                                        nom_unidade             varchar,
                                                        nom_funcao              varchar,
                                                        nom_subfuncao           varchar,
                                                        nom_programa            varchar,
                                                        nom_pao                 varchar,
                                                        empenhado_ano           numeric,
                                                        empenhado_per           numeric,
                                                        anulado_ano             numeric,
                                                        anulado_per             numeric,
                                                        pago_ano                numeric,
                                                        pago_per                numeric,
                                                        liquidado_ano           numeric,
                                                        liquidado_per           numeric,
                                                        saldo_inicial           numeric,
                                                        suplementacoes          numeric,
                                                        reducoes                numeric,
                                                        total_creditos          numeric,
                                                        credito_suplementar     numeric,
                                                        credito_especial        numeric,
                                                        credito_extraordinario  numeric,
                                                        num_programa            varchar,
                                                        num_acao                varchar
                                                    )

                                            GROUP BY
                                                        tabela.cod_entidade,
                                                        tabela.num_orgao,
                                                        tabela.num_unidade,
                                                        tabela.cod_funcao,
                                                        tabela.cod_subfuncao,
                                                        tabela.cod_programa,
                                                        tabela.num_pao,
                                                        tabela.cod_recurso,
                                                        replace(tabela.classificacao,'.',''),
                                                        tabela.cod_despesa,
                                                        tabela.saldo_inicial,
                                                        tabela.total_creditos,
                                                        tabela.reducoes,
                                                        tabela.credito_suplementar,
                                                        tabela.credito_especial,
                                                        tabela.credito_extraordinario,
                                                        tabela.suplementacoes
                                               ";

    return $stSql;
}

function consultaValorConta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaConsultaValorConta().$stFiltro.$stGroup.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaConsultaValorConta()
{
    $stQuebra = "\n";
    $stSql .= " SELECT SUM(func.vl_original) FROM                           ".$stQuebra;
    $stSql .= " ( ".$this->montaRecuperaTodos()." ) as func                 ".$stQuebra;
    $stSql .= " WHERE                                                       ".$stQuebra;
    $stSql .= "     cod_despesa IS NOT NULL                                 ".$stQuebra;

    return $stSql;
}

/**
    * Executa funcao fn_exportacao_liquidacao no banco de dados a partir do comando SQL montado no método montaRecuperaDadosLiquidacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosMANAD(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosMANAD().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosMANAD()
{
//    $stSql  .= "    replace(suplementacao,'.',',') as vl_sup_rec_vinc,                                          \n";

     $stSql= "   SELECT  'L250' as reg,
                      '2013' as exerc,
                        cod_entidade,
                       -- cod_despesa as cod_cta_desp,
                       -- cod_despesa as cod_subelemento,
                        num_orgao as cod_org,
                        num_unidade as cod_un_orc,
                        cod_funcao as cod_fun,
                        cod_subfuncao as cod_subfun,
                        cod_programa as cod_progr,
                        0 as cod_subprog,
                        num_pao as cod_proj_ativ_oe,
                       -- replace(tabela.classificacao,'.','') as cod_subelemento,
                        cod_recurso as cod_rec_vinc,
                        replace(saldo_inicial,'.',',') as vl_dotacao_inicial,
                        0,00 as vl_at_monetaria,
                        replace(coalesce(tabela.credito_suplementar,0.00),'.',',') as vl_cred_sup,
                        replace(coalesce(tabela.credito_especial,0.00),'.',',') as vl_cred_esp,
                        replace(coalesce(tabela.credito_extraordinario,0.00),'.',',') as vl_cred_ext,
                        replace(coalesce(tabela.reducoes,0.00),'.',',') as vl_red_dotacao,
                        tabela.suplementacoes,
                        0,00  as vl_red_rec_vinc       ,
                        replace(SUM(tabela.empenhado_per),'.',',') as empenhado_per,
                        replace(SUM(tabela.anulado_per),'.',',') as anulado_per,
                        replace(SUM(tabela.liquidado_per),'.',',') as liquidado_per ,
                        replace(SUM(tabela.liquidado_ano),'.',',') as vl_liquidado  ,
                        replace(SUM(tabela.pago_per),'.',',')  as vl_pago,
                        replace(tabela.total_creditos,'.',',') as total_creditos,
                        replace((SUM(tabela.empenhado_per) - SUM(tabela.anulado_per)),'.',',') as vl_empenhado,
                        0,00 as vl_lmtdo_lrf,
                        tabela.cod_conta,
                        tabela.exercicio,
                        replace(conta_despesa.cod_estrutural,'.','') as cod_subelemento,
                        replace(conta_despesa.cod_estrutural,'.','') as cod_cta_desp

                FROM ".$this->getTabela()."('".$this->getDado("stExercicio")."',
                                                             'AND OD.cod_entidade IN (".$this->getDado("stCodEntidades").")"."',
                                                             '".$this->getDado("dtInicial")."',
                                                             '".$this->getDado("dtFinal")."',
                                                             '', '', '', '', '', '', '', '' ) as tabela (
                                                        exercicio               char(4),
                                                        cod_despesa             integer,
                                                        cod_entidade            integer,
                                                        cod_programa            integer,
                                                        cod_conta               integer,
                                                        num_pao                 integer,
                                                        num_orgao               integer,
                                                        num_unidade             integer,
                                                        cod_recurso             integer,
                                                        cod_funcao              integer,
                                                        cod_subfuncao           integer,
                                                        tipo_conta              varchar,
                                                        vl_original             numeric,
                                                        dt_criacao              date,
                                                        classificacao           varchar,
                                                        descricao               varchar,
                                                        num_recurso             varchar,
                                                        nom_recurso             varchar,
                                                        nom_orgao               varchar,
                                                        nom_unidade             varchar,
                                                        nom_funcao              varchar,
                                                        nom_subfuncao           varchar,
                                                        nom_programa            varchar,
                                                        nom_pao                 varchar,
                                                        empenhado_ano           numeric,
                                                        empenhado_per           numeric,
                                                        anulado_ano             numeric,
                                                        anulado_per             numeric,
                                                        pago_ano                numeric,
                                                        pago_per                numeric,
                                                        liquidado_ano           numeric,
                                                        liquidado_per           numeric,
                                                        saldo_inicial           numeric,
                                                        suplementacoes          numeric,
                                                        reducoes                numeric,
                                                        total_creditos          numeric,
                                                        credito_suplementar     numeric,
                                                        credito_especial        numeric,
                                                        credito_extraordinario  numeric
                                                    )

                                           INNER JOIN orcamento.conta_despesa
                                                       ON conta_despesa.cod_conta = tabela.cod_conta
                                                     AND conta_despesa.exercicio = tabela.exercicio
                                            GROUP BY    tabela.cod_conta,
                                                        tabela.exercicio,
                                                        tabela.cod_entidade,
                                                        tabela.num_orgao,
                                                        tabela.num_unidade,
                                                        tabela.cod_funcao,
                                                        tabela.cod_subfuncao,
                                                        tabela.cod_programa,
                                                        tabela.num_pao,
                                                        tabela.cod_recurso,
                                                        replace(tabela.classificacao,'.',''),
                                                        tabela.cod_despesa,
                                                        tabela.saldo_inicial,
                                                        tabela.total_creditos,
                                                        tabela.reducoes,
                                                        tabela.credito_suplementar,
                                                        tabela.credito_especial,
                                                        tabela.credito_extraordinario,
                                                        tabela.suplementacoes,
                                                        conta_despesa.cod_estrutural
                                               ";

    return $stSql;

}
}
