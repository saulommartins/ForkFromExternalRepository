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

    $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_entidade'  ,'varchar',false,''    ,false,false);
    $this->AddCampo('mes'           ,'integer',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "
      SELECT 
             LPAD(mes::VARCHAR,2,'0') AS mes
           , 'N' AS nada_declarar
           , vl_imobiliaria_interna
           , '0.00'::NUMERIC(14,2) AS vl_imobiliaria_externa
           , vl_abertura_credito
           , '0.00'::NUMERIC(14,2) AS vl_deriv_ppp
           , '0.00'::NUMERIC(14,2) AS vl_aquis_fin
           , '0.00'::NUMERIC(14,2) AS vl_pela_venda_ter_bs
           , vl_dem_antec_receita
           , '0.00'::NUMERIC(14,2) AS vl_assuncao_rec_conf_div
           , '0.00'::NUMERIC(14,2) AS vl_outras_pper_credito
           , vl_contrat_externa     
           , vl_parc_div_trib             
           , vl_parc_div_prev            
           , vl_parc_div_dem_cs           
           , vl_parc_div_fgts        
           , '0.00'::NUMERIC(14,2) AS vl_melhoria_adm_rec_gffp   
           , '0.00'::NUMERIC(14,2) AS vl_prog_ilum_pub   
           , '0.00'::NUMERIC(14,2) AS vl_amp_art9   
           , '0.00'::NUMERIC(14,2) AS vl_oper_vedadas
        FROM tcemg.demonstrativo_op_credito(  '".$this->getDado("exercicio")."'
                                            , 'Mes'
                                            , ".$this->getDado("mes")."
                                            , '".$this->getDado("cod_entidade")."'
                                           ) AS tbl (  mes                          integer
                                                      ,vl_imobiliaria_interna       numeric
                                                      ,vl_contrat_externa           numeric
                                                      ,vl_abertura_credito          numeric
                                                      ,vl_dem_antec_receita         numeric
                                                      ,vl_parc_div_trib             numeric
                                                      ,vl_parc_div_prev             numeric
                                                      ,vl_parc_div_dem_cs           numeric
                                                      ,vl_parc_div_fgts             numeric
                                           )";
    return $stSql;
}

}
