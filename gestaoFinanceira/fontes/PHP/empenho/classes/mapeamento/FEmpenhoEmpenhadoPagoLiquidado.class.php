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
    * Classe de mapeamento da tabela FN_ORCAMENTO_EMPENHO_EMPENHADO_PAGO_LIQUIDADO
    * Data de Criação: 18/02/2005

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Mapeamento

    $Id: FEmpenhoEmpenhadoPagoLiquidado.class.php 65276 2016-05-09 19:15:58Z franver $

    $Revision: 32880 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso : uc-02.03.06
*/
require_once CLA_PERSISTENTE;

class FEmpenhoEmpenhadoPagoLiquidado extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    
        $this->setTabela('empenho.fn_empenho_empenhado_pago_liquidado');
    
        $this->AddCampo('entidade'     , 'integer', false,    '', false, false);
        $this->AddCampo('empenho'      , 'integer', false,    '', false, false);
        $this->AddCampo('exercicio'    , 'varchar', false,    '', false, false);
        $this->AddCampo('cgm'          , 'integer', false,    '', false, false);
        $this->AddCampo('razao_social' , 'varchar', false,    '', false, false);
        $this->AddCampo('cod_nota'     , 'integer', false,    '', false, false);
        $this->AddCampo('data'         ,    'text', false,    '', false, false);
        $this->AddCampo('ordem'        , 'integer', false,    '', false, false);
        $this->AddCampo('conta'        , 'integer', false,    '', false, false);
        $this->AddCampo('nome_conta'   , 'varchar', false,  '50', false, false);
        $this->AddCampo('valor'        , 'numeric', false,'14.2', false, false);
        $this->AddCampo('valor_anulado', 'numeric', false,'14.2', false, false);
        $this->AddCampo('recurso'      , 'varchar', false, '200', false, false);
        $this->AddCampo('despesa'      , 'varchar', false, '200', false, false);
    
    }

    public function montaRecuperaTodos()
    {
        if ($this->getDado("boMostrarAnuladoMesmoPeriodo")) {
            $boMostrarAnuladoMesmoPeriodo = 'true';
        } else {
            $boMostrarAnuladoMesmoPeriodo = 'false';
        }
    
        $stSql = "
                  SELECT retorno.*
                    FROM ".$this->getTabela()." ( '".$this->getDado("exercicio")."'
                                                , '".$this->getDado("stFiltro")."'
                                                , '".$this->getDado("stDataInicial")."'
                                                , '".$this->getDado("stDataFinal")."'
                                                , '".$this->getDado("stEntidade")."'
                                                , '".$this->getDado("inOrgao")."'
                                                , '".$this->getDado("inUnidade")."'
                                                , '".$this->getDado("inCodPao")."'
                                                , '".$this->getDado("inRecurso")."'
                                                , '".str_replace(".","",$this->getDado("stElementoDespesa"))."'
                                                , '".$this->getDado("stDestinacaoRecurso")."'
                                                , '".$this->getDado("inCodDetalhamento")."'
                                                , '".$this->getDado("stElementoDespesa")."'
                                                , '".$this->getDado("inSituacao")."'
                                                , '".$this->getDado("inCodHistorico")."'
                                                , '".$this->getDado("stOrdenacao")."'
                                                , '".$this->getDado("inCodFuncao")."'
                                                , '".$this->getDado("inCodSubFuncao")."'
                                                , '".$this->getDado("inCodPrograma")."'
                                                , '".$this->getDado("inCodPlano")."'
                                                , '".$this->getDado("inCodDotacao")."'
                                                , ".$boMostrarAnuladoMesmoPeriodo."
                                                )
                                       AS retorno
                                                ( entidade            INTEGER
                                                , descricao_categoria VARCHAR
                                                , nom_tipo            VARCHAR
                                                , empenho             INTEGER
                                                , exercicio           CHAR(4)
                                                , cgm                 INTEGER
                                                , razao_social        VARCHAR
                                                , cod_nota            INTEGER
                                                , data                TEXT
                                                , ordem               INTEGER
                                                , conta               INTEGER
                                                , nome_conta          VARCHAR
                                                , valor               NUMERIC
                                                , valor_anulado       NUMERIC
                                                , descricao           VARCHAR
                                                , recurso             VARCHAR
                                                , despesa             VARCHAR
                                                )
        ";

        if (Sessao::getExercicio() > '2015') {
            if ($this->getDado("inCentroCusto") != '') {
                $stSql .= "
              INNER JOIN (
                          SELECT empenho.cod_empenho
                               , empenho.cod_entidade
                               , empenho.exercicio
                            FROM empenho.empenho
                      INNER JOIN empenho.pre_empenho
                              ON pre_empenho.exercicio = empenho.exercicio
                             AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                      INNER JOIN empenho.item_pre_empenho
                              ON item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             AND item_pre_empenho.exercicio = pre_empenho.exercicio
                           WHERE item_pre_empenho.cod_centro = ".$this->getDado("inCentroCusto")."
                        GROUP BY empenho.cod_empenho
                               , empenho.cod_entidade
                               , empenho.exercicio
                         ) AS empenho
                      ON empenho.cod_empenho  = retorno.empenho
                     AND empenho.cod_entidade = retorno.entidade
                     AND empenho.exercicio    = retorno.exercicio
                ";
            }
        }
        switch($this->getDado("stOrdenacao")){
            case 'data' :
                $stSql .= "
                ORDER BY to_date(retorno.data,'dd/mm/yyyy')
                       , entidade
                       , empenho
                       , exercicio
                       , cgm
                       , razao_social
                       , cod_nota
                       , ordem
                       , conta
                       , nome_conta
                ";
                break;
            
            case 'credor' :
                $stSql .= "
                ORDER BY to_date(retorno.data,'dd/mm/yyyy')
                       , razao_social
                       , entidade
                       , empenho
                       , exercicio
                       , cgm
                       , cod_nota
                       , ordem
                       , conta
                       , nome_conta
                ";
                break;
    
            case 'credor_data' :
                $stSql .= "
                ORDER BY razao_social
                       , to_date(retorno.data,'dd/mm/yyyy')
                       , entidade
                       , empenho
                       , exercicio
                       , cgm
                       , cod_nota
                       , ordem
                       , conta
                       , nome_conta
                ";
                break;
            default:
                $stSql .= "
                ORDER BY to_date(retorno.data,'dd/mm/yyyy')
                       , exercicio
                       , entidade
                       , empenho
                       , cod_nota
                       , ordem
                       , conta
                       , nome_conta
                ";
                break;
        }
        return $stSql;
    }

    public function recuperaPagosEstornados(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stGroup = "";
        $stSql = $this->montaRecuperaPagosEstornados().$stFiltro.$stGroup.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    public function montaRecuperaPagosEstornados()
    {
        $stSql = "
              SELECT retorno.entidade
                   , retorno.descricao_categoria
                   , retorno.nom_tipo
                   , retorno.empenho
                   , retorno.exercicio
                   , retorno.cgm
                   , SUBSTR(retorno.razao_social,1,45) as razao_social
                   , retorno.cod_nota
                   , retorno.data
                   , retorno.ordem
                   , retorno.conta
                   , SUBSTR(retorno.nome_conta,1,45) as nome_conta
                   , retorno.valor
                   , retorno.valor_estornado
                   , retorno.valor_liquido
                   , retorno.descricao
                   , retorno.recurso
                   , retorno.despesa
                FROM empenho.fn_empenho_empenhado_pago_estornado( '".$this->getDado("exercicio")."'
                                                                , '".$this->getDado("stFiltro")."'
                                                                , '".$this->getDado("stDataInicial")."'
                                                                , '".$this->getDado("stDataFinal")."'
                                                                , '".$this->getDado("stEntidade")."'
                                                                , '".$this->getDado("inOrgao")."'
                                                                , '".$this->getDado("inUnidade")."'
                                                                , '".$this->getDado("inCodPao")."'
                                                                , '".$this->getDado("inRecurso")."'
                                                                , '".str_replace(".","",$this->getDado("stElementoDespesa"))."'
                                                                , '".$this->getDado("stDestinacaoRecurso")."'
                                                                , '".$this->getDado("inCodDetalhamento")."'
                                                                , '".$this->getDado("stElementoDespesa")."'
                                                                , '".$this->getDado("inSituacao")."'
                                                                , '".$this->getDado("inCodHistorico")."'
                                                                , '".$this->getDado("stOrdenacao")."'
                                                                , '".$this->getDado("inCodFuncao")."'
                                                                , '".$this->getDado("inCodSubFuncao")."'
                                                                , '".$this->getDado("inCodPrograma")."'
                                                                , '".$this->getDado("inCodPlano")."'
                                                                , '".$this->getDado("inCodDotacao")."'
                                                                )
                                                       AS retorno
                                                                ( entidade            INTEGER
                                                                , descricao_categoria VARCHAR
                                                                , nom_tipo            VARCHAR
                                                                , empenho             INTEGER
                                                                , exercicio           CHAR(4)
                                                                , cgm                 INTEGER
                                                                , razao_social        VARCHAR
                                                                , cod_nota            INTEGER
                                                                , data                TEXT
                                                                , ordem               INTEGER
                                                                , conta               INTEGER
                                                                , nome_conta          VARCHAR
                                                                , valor               NUMERIC
                                                                , valor_estornado     NUMERIC
                                                                , valor_liquido       NUMERIC
                                                                , descricao           VARCHAR
                                                                , recurso             VARCHAR
                                                                , despesa             VARCHAR(150)
                                                                )
        ";

        if (Sessao::getExercicio() > '2015') {
            if ($this->getDado("inCentroCusto") != '') {
                $stSql .= "
              INNER JOIN (
                          SELECT empenho.cod_empenho
                               , empenho.cod_entidade
                               , empenho.exercicio
                            FROM empenho.empenho
                      INNER JOIN empenho.pre_empenho
                              ON pre_empenho.exercicio = empenho.exercicio
                             AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                      INNER JOIN empenho.item_pre_empenho
                              ON item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             AND item_pre_empenho.exercicio = pre_empenho.exercicio
                           WHERE item_pre_empenho.cod_centro = ".$this->getDado("inCentroCusto")."
                        GROUP BY empenho.cod_empenho
                               , empenho.cod_entidade
                               , empenho.exercicio
                         ) AS empenho
                      ON empenho.cod_empenho  = retorno.empenho
                     AND empenho.cod_entidade = retorno.entidade
                     AND empenho.exercicio    = retorno.exercicio
                ";
            }
        }
    
        switch($this->getDado("stOrdenacao")){
            case 'data' :
                $stSql .= "
                ORDER BY to_date(retorno.data,'dd/mm/yyyy')
                       , entidade
                       , empenho
                       , exercicio
                       , cgm
                       , razao_social
                       , cod_nota
                       , ordem
                       , conta
                       , nome_conta
                ";
                break;
            
            case 'credor' :
                $stSql .= "
                ORDER BY to_date(retorno.data,'dd/mm/yyyy')
                       , razao_social
                       , entidade
                       , empenho
                       , exercicio
                       , cgm
                       , cod_nota
                       , ordem
                       , conta
                       , nome_conta
                ";
                break;
    
            case 'credor_data' :
                $stSql .= "
                ORDER BY razao_social
                       , to_date(retorno.data,'dd/mm/yyyy')
                       , entidade
                       , empenho
                       , exercicio
                       , cgm
                       , cod_nota
                       , ordem
                       , conta
                       , nome_conta
                ";
                break;
        }
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
    $stSql .= " SELECT SUM(func.vl_original) FROM                        ".$stQuebra;
    $stSql .= " ( ".$this->montaRecuperaTodos()." ) as func              ".$stQuebra;
    $stSql .= " WHERE                                                    ".$stQuebra;
    $stSql .= "     empenho NOT NULL                                 ".$stQuebra;

    return $stSql;
}

function recuperaLiquidadoTotal(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stGroup = "";
    
    $stSql = $this->montaRecuperaLiquidadoTotal().$stFiltro.$stGroup.$stOrdem;
    
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
    return $obErro;
}

function montaRecuperaLiquidadoTotal()
{
    $stSql  = "SELECT * FROM empenho.fn_empenho_liquidado_total('".$this->getDado("exercicio")."', ".$this->getDado("stEntidade").", '".$this->getDado("stDataInicial")."', '".$this->getDado("stDataFinal")."') AS vl_total;";
    return $stSql;
}

function recuperaLiquidadoAnuladoTotal(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stGroup = "";
    
    $stSql = $this->montaRecuperaLiquidadoAnuladoTotal().$stFiltro.$stGroup.$stOrdem;
    
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
    return $obErro;
}

function montaRecuperaLiquidadoAnuladoTotal()
{
    $stSql  = "SELECT * FROM empenho.fn_empenho_liquidado_anulado_total('".$this->getDado("exercicio")."', ".$this->getDado("stEntidade").", '".$this->getDado("stDataInicial")."', '".$this->getDado("stDataFinal")."') AS vl_total_anulado;";
    return $stSql;
}

}