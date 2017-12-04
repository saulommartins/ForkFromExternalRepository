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
    * Classe de mapeamento da função tcmba.fn_demonstrativo_consolidado_despesa
    * Data de Criação: 24/09/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TTCMBADemonstrativoConsolidadoDespesa.class.php 63409 2015-08-25 17:58:08Z franver $
    * $Rev: 63409 $
    * $Author: franver $
    * $Date: 2015-08-25 14:58:08 -0300 (Ter, 25 Ago 2015) $
    * 
    * Casos de uso: uc-02.01.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBADemonstrativoConsolidadoDespesa extends Persistente 
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        
        parent::Persistente();
        $this->setTabela('tcmba.fn_demonstrativo_consolidado_despesa');
    
        $this->AddCampo('tipo_registro'             , 'varchar', false, ''    , false, false);
        $this->AddCampo('unidade_gestora'           , 'varchar', false, ''    , false, false);
        $this->AddCampo('competencia'               , 'varchar', false, ''    , false, false);
        $this->AddCampo('num_orgao'                 , 'varchar', false, ''    , false, false);
        $this->AddCampo('num_unidade'               , 'varchar', false, ''    , false, false);
        $this->AddCampo('cod_funcao'                , 'varchar', false, ''    , false, false);
        $this->AddCampo('cod_subfuncao'             , 'varchar', false, ''    , false, false);
        $this->AddCampo('num_programa'              , 'varchar', false, ''    , false, false);
        $this->AddCampo('num_pao'                   , 'varchar', false, ''    , false, false);
        $this->AddCampo('cod_despesa'               , 'integer', false, ''    , false, false);
        $this->AddCampo('elemento_despesa'          , 'varchar', false, ''    , false, false);
        $this->AddCampo('cod_recurso'               , 'varchar', false, '    ', false, false);
        $this->AddCampo('dotacao_fixada'            , 'numeric', false, '14.2', false, false);
        $this->AddCampo('credito_suplementar'       , 'numeric', false, '14.2', false, false);
        $this->AddCampo('credito_suplementar_mes'   , 'numeric', false, '14.2', false, false);
        $this->AddCampo('credito_especial'          , 'numeric', false, '14.2', false, false);
        $this->AddCampo('credito_especial_mes'      , 'numeric', false, '14.2', false, false);
        $this->AddCampo('credito_extraordinario'    , 'numeric', false, '14.2', false, false);
        $this->AddCampo('credito_extraordinario_mes', 'numeric', false, '14.2', false, false);
        $this->AddCampo('reducoes'                  , 'numeric', false, '14.2', false, false);
        $this->AddCampo('reducoes_mes'              , 'numeric', false, '14.2', false, false);
        $this->AddCampo('transferencia'             , 'numeric', false, '14.2', false, false);
        $this->AddCampo('transferencia_mes'         , 'numeric', false, '14.2', false, false);
        $this->AddCampo('transferencia_anulacao'    , 'numeric', false, '14.2', false, false);
        $this->AddCampo('transferencia_anulacao_mes', 'numeric', false, '14.2', false, false);
        $this->AddCampo('qdd_acrescimo'             , 'numeric', false, '14.2', false, false);
        $this->AddCampo('qdd_acrescimo_mes'         , 'numeric', false, '14.2', false, false);
        $this->AddCampo('qdd_decrescimo'            , 'numeric', false, '14.2', false, false);
        $this->AddCampo('qdd_decrescimo_mes'        , 'numeric', false, '14.2', false, false);
        $this->AddCampo('dotacao_atualizada'        , 'numeric', false, '14.2', false, false);
        $this->AddCampo('empenhado_ano'             , 'numeric', false, '14.2', false, false);
        $this->AddCampo('empenhado_mes'             , 'numeric', false, '14.2', false, false);
        $this->AddCampo('liquidado_ano'             , 'numeric', false, '14.2', false, false);
        $this->AddCampo('liquidado_mes'             , 'numeric', false, '14.2', false, false);
        $this->AddCampo('pago_ano'                  , 'numeric', false, '14.2', false, false);
        $this->AddCampo('pago_mes'                  , 'numeric', false, '14.2', false, false);
        $this->AddCampo('saldo_pagar'               , 'numeric', false, '14.2', false, false);
        $this->AddCampo('saldo_disponivel'          , 'numeric', false, '14.2', false, false);
        $this->AddCampo('reservado_tcm'             , 'numeric', false, '14.2', false, false);
    }
    
    public function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaDadosTribunal()
    {
    
        $stSql  = "
          SELECT 1  AS tipo_registro
               , '".$this->getDado('unidade_gestora')."' AS unidade_gestora
               , '".$this->getDado('exercicio').$this->getDado('mes')."' AS competencia
               , num_orgao
               , num_unidade
               , cod_funcao
               , cod_subfuncao
               , num_programa
               , num_pao
               , num_acao
               , SUBSTR(REPLACE(classificacao, '.', ''), 1, 8) AS elemento_despesa
               , cod_recurso
               , vl_original AS dotacao_fixada
               , credito_suplementar
               , credito_suplementar_mes
               , credito_especial
               , credito_especial_mes
               , credito_extraordinario
               , credito_extraordinario_mes
               , transferencia
               , transferencia_mes
               , transferencia_anulacao
               , transferencia_anulacao_mes
               , '' AS qdd_acrescimo
               , '' AS qdd_acrescimo_mes
               , '' AS qdd_decrescimo
               , '' AS qdd_decrescimo_mes
               , reducoes AS credito_anulacao
               , reducoes_mes AS credito_anulacao_mes
               , empenhado_ano 
               , empenhado_per AS empenhado_mes
               , liquidado_ano
               , liquidado_per AS liquidado_mes
               , pago_ano
               , pago_per AS pago_mes
               , (empenhado_ano - pago_ano) AS saldo_pagar
               --, ((total_creditos - empenhado_ano) + anulado_ano) AS saldo_disponivel
               , ((saldo_inicial - empenhado_per) + anulado_per) AS saldo_disponivel
               , total_creditos AS dotacao_atualizada
               , '' AS reservado_tcm
            FROM ".$this->getTabela()." ( '".$this->getDado("exercicio")."'
                                        ,' AND od.cod_entidade IN (".$this->getDado("entidades").") '
                                        ,'".$this->getDado("data_inicio")."'
                                        ,'".$this->getDado("data_fim")."'
                                        ,'".$this->getDado("stCodEstruturalInicial")."'
                                        ,'".$this->getDado("stCodEstruturalFinal")."'
                                        ,'".$this->getDado("stCodReduzidoInicial")."'
                                        ,'".$this->getDado("stCodReduzidoFinal")."'
                                        ,'".$this->getDado("stControleDetalhado")."'
                                        ,'".$this->getDado("inNumOrgao")."'
                                        ,'".$this->getDado("inNumUnidade")."'
                                        , '".$this->getDado('stVerificaCreateDropTables')."' )
              AS retorno ( exercicio                  char(4)
                         , cod_despesa                INTEGER
                         , cod_entidade               INTEGER
                         , cod_programa               INTEGER
                         , cod_conta                  INTEGER
                         , num_pao                    INTEGER
                         , num_orgao                  INTEGER
                         , num_unidade                INTEGER
                         , cod_recurso                INTEGER
                         , cod_funcao                 INTEGER
                         , cod_subfuncao              INTEGER
                         , tipo_conta                 VARCHAR
                         , vl_original                NUMERIC
                         , dt_criacao                 DATE
                         , classificacao              VARCHAR
                         , descricao                  VARCHAR
                         , num_recurso                VARCHAR
                         , nom_recurso                VARCHAR
                         , nom_orgao                  VARCHAR
                         , nom_unidade                VARCHAR
                         , nom_funcao                 VARCHAR
                         , nom_subfuncao              VARCHAR
                         , nom_programa               VARCHAR
                         , nom_pao                    VARCHAR
                         , empenhado_ano              NUMERIC
                         , empenhado_per              NUMERIC
                         , anulado_ano                NUMERIC
                         , anulado_per                NUMERIC
                         , pago_ano                   NUMERIC
                         , pago_per                   NUMERIC
                         , liquidado_ano              NUMERIC
                         , liquidado_per              NUMERIC
                         , saldo_inicial              NUMERIC
                         , suplementacoes             NUMERIC
                         , reducoes                   NUMERIC
                         , reducoes_mes               NUMERIC
                         , transferencia_anulacao     NUMERIC
                         , transferencia_anulacao_mes NUMERIC
                         , total_creditos  	          NUMERIC
                         , credito_suplementar        NUMERIC
                         , credito_especial  	      NUMERIC
                         , credito_extraordinario     NUMERIC
                         , transferencia	          NUMERIC
                         , num_programa 	          VARCHAR
                         , num_acao 		          VARCHAR
                         , credito_suplementar_mes    NUMERIC
                         , credito_especial_mes       NUMERIC
                         , credito_extraordinario_mes NUMERIC
                         , transferencia_mes	      NUMERIC
                         )
        ";
        return $stSql;
    }

    public function __destruct(){}
}
?>