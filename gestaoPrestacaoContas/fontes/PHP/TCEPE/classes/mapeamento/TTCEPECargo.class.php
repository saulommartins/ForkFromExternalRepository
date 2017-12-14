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
    * Data de Criação   : 28/10/2014

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor:  Carlos Adriano
    $Id: TTCEPECargo.class.php 60559 2014-10-29 16:32:38Z carlos.silva $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPECargo extends Persistente {
/**
    * Método Construtor
    * @access Private
*/
    function TTCEPECargo() {
        parent::Persistente();
    }

    function montaRecuperaTodos() {
        
        $stSql =" 
                SELECT cargo.cod_cargo
                 , trim(cargo.descricao) AS descricao
                 , CASE WHEN cargo.cod_escolaridade = 0 THEN 0
                    WHEN cargo.cod_escolaridade = 1 OR cargo.cod_escolaridade = 2 THEN 1
                    WHEN cargo.cod_escolaridade = 4 OR cargo.cod_escolaridade = 5 THEN 2
                    WHEN cargo.cod_escolaridade = 6 OR cargo.cod_escolaridade = 7 THEN 3
                    WHEN cargo.cod_escolaridade = 8 THEN 4
                    WHEN cargo.cod_escolaridade = 9 THEN 5
                    WHEN cargo.cod_escolaridade = 14 OR cargo.cod_escolaridade = 15 THEN 6
                    WHEN cargo.cod_escolaridade = 10 OR cargo.cod_escolaridade = 12 THEN 7
                    WHEN cargo.cod_escolaridade = 11 OR cargo.cod_escolaridade = 13 THEN 8
                   END AS cod_escolaridade
                 , padrao.horas_semanais
            
                  FROM pessoal".$this->getDado('entidade').".cargo
            
            INNER JOIN (SELECT cargo_padrao.cod_padrao
                             , cargo_padrao.cod_cargo
                          FROM pessoal".$this->getDado('entidade').".cargo_padrao
                             , (  SELECT cod_cargo
                                       , max(timestamp) AS timestamp
                                    FROM pessoal".$this->getDado('entidade').".cargo_padrao
                                GROUP BY cod_cargo) AS max_cargo_padrao
                        WHERE max_cargo_padrao.cod_cargo = cargo_padrao.cod_cargo
                          AND max_cargo_padrao.timestamp = cargo_padrao.timestamp
                        ) AS cargo_padrao
                    ON cargo_padrao.cod_cargo = cargo.cod_cargo
            
            INNER JOIN folhapagamento".$this->getDado('entidade').".padrao
                    ON cargo_padrao.cod_padrao = padrao.cod_padrao
            
            INNER JOIN (SELECT padrao_padrao.cod_padrao
                             , padrao_padrao.valor
                             , padrao_padrao.vigencia
                          FROM folhapagamento".$this->getDado('entidade').".padrao_padrao
                             , (  SELECT cod_padrao
                                       , max(timestamp) AS timestamp
                                   FROM folhapagamento".$this->getDado('entidade').".padrao_padrao
                                  WHERE to_char(vigencia, 'yyyy-mm-dd') <= '".$this->getDado('dt_final')."'
                               GROUP BY cod_padrao) AS max_padrao_padrao
                        WHERE max_padrao_padrao.cod_padrao = padrao_padrao.cod_padrao
                          AND max_padrao_padrao.timestamp = padrao_padrao.timestamp
                        ) AS padrao_padrao
                ON padrao_padrao.cod_padrao = padrao.cod_padrao
            ORDER BY cod_cargo";
            
        return $stSql;
    }
}

?>