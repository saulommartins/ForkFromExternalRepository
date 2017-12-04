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
    * Arquivo de mapeamento para a função que busca os dados dos serviços de terceiros
    * Data de Criação   : 12/02/2015

    * @author Analista      Ane Caroline
    * @author Desenvolvedor Lisiane Morais

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGDemostrativoOpCredito extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCEMGDemostrativoOpCredito()
{
    parent::Persistente();

    $this->AddCampo('stExercicio'   ,'VARCHAR' ,false ,'' ,false ,false);
    $this->AddCampo('stCodEntidade' ,'VARCHAR' ,false ,'' ,false ,false);
    $this->AddCampo('inBimestre'    ,'INTEGER' ,false ,'' ,false ,false);
}

function montaRecuperaTodos()
{
    $stSql  = "
      SELECT 12 AS mes
           , 'N' AS nada_declarar
           , REPLACE( vl_imobiliaria_interna::VARCHAR, '.', '' ) AS vl_imobiliaria_interna
           , 0 AS vl_imobiliaria_externa 
           , REPLACE (vl_abertura_credito::VARCHAR, '.', '' ) AS vl_abertura_credito
           , 0 AS vl_deriv_ppp
           , 0 AS vl_aquis_fin
           , 0 AS vl_pela_venda_ter_bs
           , REPLACE( vl_dem_antec_receita::VARCHAR, '.', '' ) AS vl_dem_antec_receita
           , 0 AS vl_assuncao_rec_conf_div
           , 0 AS vl_outras_pper_credito
           , REPLACE( vl_contrat_externa::VARCHAR, '.', '' ) AS vl_contrat_externa
           , REPLACE( vl_parc_div_trib::VARCHAR, '.', '' ) AS vl_parc_div_trib
           , REPLACE( vl_parc_div_prev::VARCHAR, '.', '' ) AS vl_parc_div_prev
           , REPLACE( vl_parc_div_dem_cs::VARCHAR, '.', '' ) AS vl_parc_div_dem_cs           
           , REPLACE( vl_parc_div_fgts::VARCHAR, '.', '' ) AS vl_parc_div_fgts        
           , 0 AS vl_melhoria_adm_rec_gffp   
           , 0 AS vl_prog_ilum_pub   
           , 0 AS vl_amp_art9   
           , 0 AS vl_oper_vedadas
        FROM tcemg.demonstrativo_op_credito ( '".$this->getDado("stExercicio")."'
                                             , 'bimestre'
                                             , ".$this->getDado("inBimestre")."
                                             , '".$this->getDado("stCodEntidade")."'
                                           ) AS tbl (  mes                          INTEGER
                                                     , vl_imobiliaria_interna       NUMERIC
                                                     , vl_contrat_externa           NUMERIC
                                                     , vl_abertura_credito          NUMERIC
                                                     , vl_dem_antec_receita         NUMERIC
                                                     , vl_parc_div_trib             NUMERIC
                                                     , vl_parc_div_prev             NUMERIC
                                                     , vl_parc_div_dem_cs           NUMERIC
                                                     , vl_parc_div_fgts             NUMERIC
                                           ) ";
    return $stSql;
}

}
