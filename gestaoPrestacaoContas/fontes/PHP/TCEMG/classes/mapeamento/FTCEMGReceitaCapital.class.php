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
 * Arquivo de mapeamento para a função que busca os dados da receita capital
 * Data de Criação   : 06/03/2015
 * 
 * @author Analista: Dagiane Vieira
 * @author Desenvolvedor: Michel Teixeira
 *
 * $Id: FTCEMGReceitaCapital.class.php 61970 2015-03-19 17:18:14Z evandro $
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGReceitaCapital extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function FTCEMGReceitaCapital()
    {
        parent::Persistente();

        $this->setTabela('tcemg.fn_receita_capital');

        $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
        $this->AddCampo('cod_entidade'  ,'varchar',false,''    ,false,false);
        $this->AddCampo('mes'           ,'integer',false,''    ,false,false);
        $this->AddCampo('dt_inicial'    ,'varchar',false,''    ,false,false);
        $this->AddCampo('dt_final'      ,'varchar',false,''    ,false,false);
    }

    public function montaRecuperaTodos()
    {
        $stSql  = " SELECT 
                            ".$this->getDado('mes')." AS mes
                            , cod_tipo
                            , REPLACE(rec_alienacao      , '.', '') AS rec_alienacao
                            , REPLACE(rec_amort          , '.', '') AS rec_amort
                            , REPLACE(rec_transf_capital , '.', '') AS rec_transf_capital
                            , REPLACE(rec_convenios      , '.', '') AS rec_convenios
                            , REPLACE(out_rec_cap        , '.', '') AS out_rec_cap
                            , REPLACE(rec_ret_op_cred    , '.', '') AS rec_ret_op_cred
                            , REPLACE(rec_privat         , '.', '') AS rec_privat
                            , REPLACE(rec_ref_divida     , '.', '') AS rec_ref_divida
                            , REPLACE(rec_out_op_cred    , '.', '') AS rec_out_op_cred
                            , REPLACE(deducoes           , '.', '') AS deducoes
                    FROM ".$this->getTabela()." ( '".$this->getDado('exercicio')."'
                                                , '".$this->getDado('cod_entidade')."'
                                                , '".$this->getDado('dt_inicial')."'
                                                , '".$this->getDado('dt_final')."') 
                    AS retorno(
                                cod_tipo               VARCHAR,
                                rec_alienacao          VARCHAR,
                                rec_amort              VARCHAR,
                                rec_transf_capital     VARCHAR,
                                rec_convenios          VARCHAR,
                                out_rec_cap            VARCHAR,
                                rec_ret_op_cred        VARCHAR,
                                rec_privat             VARCHAR,
                                rec_ref_divida         VARCHAR,
                                rec_out_op_cred        VARCHAR,
                                deducoes               VARCHAR
                    )
                ";

        return $stSql;
    }

}
