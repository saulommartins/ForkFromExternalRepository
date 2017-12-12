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
    * Arquivo de mapeamento para a função que busca os dados da inscrição de restos a pagar
    * Data de Criação   : 21/01/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGInscRestosAPagar extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function FTCEMGInscRestosAPagar()
    {
        parent::Persistente();

        $this->setTabela('tcemg.fn_insc_restos_a_pagar');

        $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
        $this->AddCampo('cod_entidade'  ,'varchar',false,''    ,false,false);
        $this->AddCampo('mes'           ,'integer',false,''    ,false,false);
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
            SELECT mes
                 , REPLACE( ROUND(vl_processado,2)::VARCHAR, '.', '' ) AS vl_processado
                 , REPLACE( ROUND(vl_nao_processado,2)::VARCHAR, '.', '' ) AS vl_nao_processado
                 , REPLACE( ROUND(vl_despesa_nao_inscrita,2)::VARCHAR, '.', '' ) AS vl_despesa_nao_inscrita
                 , REPLACE( ROUND(vl_vinculado,2)::VARCHAR, '.', '' ) AS vl_vinculado
                 , REPLACE( ROUND(vl_nao_vinculado,2)::VARCHAR, '.', '' ) AS vl_nao_vinculado
                 , REPLACE( ROUND(vl_rpps_processado,2)::VARCHAR, '.', '' ) AS vl_rpps_processado
                 , REPLACE( ROUND(vl_rpps_nao_processado,2)::VARCHAR, '.', '' ) AS vl_rpps_nao_processado
                 , REPLACE( ROUND(vl_rpps_despesa_nao_inscrita,2)::VARCHAR, '.', '' ) AS vl_rpps_despesa_nao_inscrita
                 , REPLACE( ROUND(vl_rpps_vinculado,2)::VARCHAR, '.', '' ) AS vl_rpps_vinculado
                 , REPLACE( ROUND(vl_rpps_nao_vinculado,2)::VARCHAR, '.', '' ) AS vl_rpps_nao_vinculado
                 , REPLACE( ROUND(vl_processado_legislativo,2)::VARCHAR, '.', '' ) AS vl_processado_legislativo
                 , 'S' AS nada_declarar
                 , 0 AS vl_rppsas_processado
                 , 0 AS vl_rppsas_nao_processado
                 , 0 AS vl_rppsas_despesa_nao_inscrita
                 , 0 AS vl_rppsas_vinculado
                 , 0 AS vl_rppsas_nao_vinculado
              FROM ".$this->getTabela()."('" . $this->getDado('exercicio') . "','" . $this->getDado('cod_entidade') . "'," . $this->getDado('mes') . ") AS retorno
                                          ( mes                          INTEGER,
                                            vl_processado                NUMERIC,
                                            vl_nao_processado            NUMERIC,
                                            vl_despesa_nao_inscrita      NUMERIC,
                                            vl_vinculado                 NUMERIC,
                                            vl_nao_vinculado             NUMERIC,
                                            vl_rpps_processado           NUMERIC,
                                            vl_rpps_nao_processado       NUMERIC,
                                            vl_rpps_despesa_nao_inscrita NUMERIC,
                                            vl_rpps_vinculado            NUMERIC,
                                            vl_rpps_nao_vinculado        NUMERIC,
                                            vl_processado_legislativo    NUMERIC
                                          )";

        return $stSql;
    }

}

?>