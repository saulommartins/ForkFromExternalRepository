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
    * Classe de mapeamento da tabela FN_EMPENHO_SITUACAO_AUTORIZACAO_EMPENHO
    * Data de Criação: 13/10/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $

    * Casos de uso: uc-02.03.34
*/
require_once CLA_PERSISTENTE;

class FEmpenhoSituacaoAutorizacaoEmpenho extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    
        $this->setTabela('empenho.fn_situacao_autorizacao_empenho');
    
        $this->AddCampo('empenho'       , 'INTEGER', false,    '', false, false);
        $this->AddCampo('entidade'      , 'INTEGER', false,    '', false, false);
        $this->AddCampo('exercicio'     , 'varchar', false,    '', false, false);
        $this->AddCampo('emissao'       ,    'text', false,    '', false, false);
        $this->AddCampo('credor'        , 'varchar', false,    '', false, false);
        $this->AddCampo('empenhado'     , 'numeric', false,'14.2', false, false);
        $this->AddCampo('anulado'       , 'numeric', false,'14.2', false, false);
        $this->AddCampo('liquidado'     , 'numeric', false,'14.2', false, false);
        $this->AddCampo('pago'          , 'numeric', false,'14.2', false, false);
        $this->AddCampo('data_pagamento',    'text', false,    '', false, false);
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
                  SELECT retorno.* 
                    FROM ".$this->getTabela()." ( '".$this->getDado("stEntidade")."'
                                                , '".$this->getDado("exercicio")."'
                                                , '".$this->getDado("stDataInicialEmissao")."'
                                                , '".$this->getDado("stDataFinalEmissao")."'
                                                , '".$this->getDado("stDataInicialAnulacao")."'
                                                , '".$this->getDado("stDataFinalAnulacao")."'
                                                , '".$this->getDado("stDataInicialLiquidacao")."'
                                                , '".$this->getDado("stDataFinalLiquidacao")."'
                                                , '".$this->getDado("stDataInicialEstornoLiquidacao")."'
                                                , '".$this->getDado("stDataFinalEstornoLiquidacao")."'
                                                , '".$this->getDado("stDataInicialPagamento")."'
                                                , '".$this->getDado("stDataFinalPagamento")."'
                                                , '".$this->getDado("stDataInicialEstornoPagamento")."'
                                                , '".$this->getDado("stDataFinalEstornoPagamento")."'
                                                , '".$this->getDado("inCodEmpenhoInicial")."'
                                                , '".$this->getDado("inCodEmpenhoFinal")."'
                                                , '".$this->getDado("inCodDotacao")."'
                                                , '".$this->getDado("inCodDespesa")."'
                                                , '".$this->getDado("inCodRecurso")."'
                                                , '".$this->getDado("stDestinacaoRecurso")."'
                                                , '".$this->getDado("inCodDetalhamento")."'
                                                , '".$this->getDado("inNumOrgao")."'
                                                , '".$this->getDado("inNumUnidade")."'
                                                , '".$this->getDado("inOrdenacao")."'
                                                , '".$this->getDado("inCodFornecedor")."'
                                                , '".$this->getDado("inSituacao")."'
                                                , '".$this->getDado("inCodAutorizacao")."'
                                                )
                                       AS retorno
                                                ( autorizacao         integer
                                                , exercicio           char(4)
                                                , emissao             text
                                                , entidade            integer
                                                , credor              varchar
                                                , empenho             integer
                                                , autorizado          numeric
                                                , autorizado_anulado  numeric
                                                , saldoautorizado     numeric
                                                , liquidado           numeric
                                                , pago                numeric
                                                , empenhadoapagar     numeric
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
        $stSql .= "
                GROUP BY retorno.autorizacao
                       , retorno.exercicio
                       , to_date(retorno.emissao,'dd/mm/yyyy')
                       , retorno.entidade
                       , retorno.credor
                       , retorno.empenho
                       , retorno.autorizado
                       , retorno.autorizado_anulado
                       , retorno.saldoautorizado
                       , retorno.liquidado
                       , retorno.pago
                       , retorno.empenhadoapagar
                       , retorno.emissao
                ORDER BY retorno.autorizacao
        ";
         return $stSql;
    }
}
