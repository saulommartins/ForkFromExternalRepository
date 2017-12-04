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
    * Arquivo de mapeamento para a função que busca os dados da despesa capital
    * Data de Criação   : 29/01/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id: FTCEMGDespesaCapital.class.php 62780 2015-06-17 14:35:08Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGDespesaCapital extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function FTCEMGDespesaCapital()
    {
        parent::Persistente();

        $this->setTabela('tcemg.fn_despesa_capital');
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
            SELECT mes AS periodo
                 , ROUND(desp_invest       ,2) AS desp_invest
                 , ROUND(desp_inv_finan    ,2) AS desp_inv_finan
                 , ROUND(desp_amort_div_int,2) AS desp_amort_div_int
                 , ROUND(desp_amort_div_ext,2) AS desp_amort_div_ext
                 , ROUND(desp_amort_div_mob,2) AS desp_amort_div_mob
                 , ROUND(desp_out_desp_cap ,2) AS desp_out_desp_cap
                 , ROUND(conc_emprestimos  ,2) AS conc_emprestimos
                 , ROUND(aquisicao_titulos ,2) AS aquisicao_titulos
                 , ROUND(incent_contrib    ,2) AS incent_contrib
                 , ROUND(incent_inst_finan ,2) AS incent_inst_finan
                 , LPAD(cod_tipo::varchar,2,'0') AS cod_tipo 
              FROM ".$this->getTabela()."('".$this->getDado('exercicio')."'
                                         ,'".$this->getDado('cod_entidade')."'
                                         , '".$this->getDado("dt_inicial")."'
                                         , '".$this->getDado("dt_final")."'
                                         ) AS retorno
                                          (  mes                 INTEGER,
                                             desp_invest         NUMERIC,
                                             desp_inv_finan      NUMERIC,
                                             desp_amort_div_int  NUMERIC,
                                             desp_amort_div_ext  NUMERIC,
                                             desp_amort_div_mob  NUMERIC,
                                             desp_out_desp_cap   NUMERIC,
                                             conc_emprestimos    NUMERIC,
                                             aquisicao_titulos   NUMERIC,
                                             incent_contrib      NUMERIC,
                                             incent_inst_finan   NUMERIC,
                                             cod_tipo            INTEGER
                                          )
          ORDER BY cod_tipo";

        return $stSql;
    }

}
