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

    * Classe de mapeamento da tabela 
    * Data de Criação: 10/02/2014

    * @category    Urbem
    * @package     TCE/MG
    * @author      Carolina Schwaab Marcal
    * $Id: TTCEMGRelatorioAnexoIIIA.class.php 62269 2015-04-15 18:28:39Z franver $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGRelatorioAnexoIIIA extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

    public function recuperaDadosAnexoIIIA(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDadosAnexoIIIA",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDadosAnexoIIIA()
    {
        $stSql = "
            SELECT
                    *
                    FROM tcemg.relatorio_anexo3a_despesas ( '".$this->getDado('exercicio')."'
                                                            ,'".$this->getDado('dtInicial')."'
                                                            ,'".$this->getDado('dtFinal')."'
                                                            ,'1,2,3'
                                                            ,'".$this->getDado('cod_conta')."' )
                    AS (
                         nivel           INTEGER                        
                        ,cod_funcao      INTEGER
                        ,cod_subfuncao   INTEGER
                        ,cod_programa    INTEGER
                        ,descricao       VARCHAR
                        ,valor_pagamento NUMERIC
                    )
        ";

        return $stSql;
    }

    public function recuperaContasRecursoDespesa(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaContasRecursoDespesa",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaContasRecursoDespesa()
    {
        $stSql = "  
          SELECT plano_analitica.cod_plano AS cod_plano
               , plano_conta.nom_conta AS nom_conta
               , plano_conta.cod_conta AS cod_conta
            FROM contabilidade.plano_banco
            JOIN contabilidade.plano_analitica
              ON plano_banco.exercicio = plano_analitica.exercicio
             AND plano_banco.cod_plano = plano_analitica.cod_plano
            JOIN contabilidade.plano_conta
              ON plano_analitica.exercicio = plano_conta.exercicio
             AND plano_analitica.cod_conta = plano_conta.cod_conta
           WHERE plano_banco.exercicio = '".Sessao::getExercicio()."'
           ORDER BY plano_analitica.cod_plano
            ";
        return $stSql;
    }
    
    public function __destruct(){}

}