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
    * 
    * Data de Criação   : 16/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Michel Teixeira
    $Id: TTCEPELiquidacaoRestosEstorno.class.php 60545 2014-10-28 11:48:19Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEClasseNivelFaixa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TTCEPEClasseNivelFaixa()
    {
        parent::Persistente();
    }

    function montaRecuperaTodos()
    {
        $stSql = " SELECT
                            0 AS reservado_tce,
                            subselect.cod_cargo,
                            subselect.timestamp,
                            cargo_padrao.cod_padrao AS cod_classe,
                            padrao.descricao AS nomenclatura,
                            padrao_padrao.valor AS vencimento_base
                            
                    FROM pessoal".$this->getDado('cod_entidade').".cargo_padrao
                    
                    JOIN (
                            SELECT 
                                    cargo_padrao.cod_cargo,
                                    MAX(cargo_padrao.timestamp) AS timestamp
                                    
                            FROM pessoal".$this->getDado('cod_entidade').".cargo_padrao
                            
                            WHERE  cargo_padrao.timestamp <= (
                                                                SELECT ultimoTimestampPeriodoMovimentacao(cod_periodo_movimentacao,'".$this->getDado('cod_entidade')."')
                                                                  FROM folhapagamento".$this->getDado('cod_entidade').".periodo_movimentacao
                                                                 WHERE TO_CHAR(periodo_movimentacao.dt_inicial,'dd/mm/yyyy') = '".$this->getDado('dt_inicial')."'
                                                                   AND TO_CHAR(periodo_movimentacao.dt_final,'dd/mm/yyyy') = '".$this->getDado('dt_final')."'
                                                            )::timestamp
                                                            
                            GROUP BY cod_cargo
                            ORDER BY cod_cargo
                        ) AS subselect
                      ON subselect.cod_cargo = cargo_padrao.cod_cargo
                     AND subselect.timestamp = cargo_padrao.timestamp
                     
                    JOIN folhapagamento".$this->getDado('cod_entidade').".padrao
                      ON padrao.cod_padrao = cargo_padrao.cod_padrao
                      
                    JOIN folhapagamento".$this->getDado('cod_entidade').".padrao_padrao
                      ON padrao_padrao.cod_padrao = padrao.cod_padrao
                    AND padrao_padrao.timestamp = (
                                                    SELECT MAX(FPP.timestamp)
                                                      FROM folhapagamento.padrao_padrao AS FPP
                                                     WHERE FPP.cod_padrao = padrao_padrao.cod_padrao
                                                )
                                                
                    ORDER BY cod_cargo, cod_classe, timestamp";
                    
        return $stSql;
    }
}

?>