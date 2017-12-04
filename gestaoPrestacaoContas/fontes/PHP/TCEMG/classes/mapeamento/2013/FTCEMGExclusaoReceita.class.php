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
    * Arquivo de mapeamento para a função que busca os dados da exclusao da receita
    * Data de Criação   : 27/01/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id: FTCEMGExclusaoReceita.class.php 62635 2015-05-26 14:15:10Z arthur $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGExclusaoReceita extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function FTCEMGExclusaoReceita()
    {
        parent::Persistente();

        $this->setTabela('tcemg.fn_exclusao_receita');

        $this->AddCampo('exercicio'     ,'varchar' ,false  ,'' ,false ,false);
        $this->AddCampo('cod_entidade'  ,'varchar' ,false  ,'' ,false ,false);
        $this->AddCampo('bimestre'      ,'integer' ,false  ,'' ,false ,false);
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
            SELECT bimestre
                 , REPLACE(ROUND(contr_serv,2)::VARCHAR                , '.', '') AS contr_serv
                 , REPLACE(ROUND(compens_reg_prev,2)::VARCHAR          , '.', '') AS compens_reg_prev
                 , REPLACE(ROUND(out_duplic,2)::VARCHAR                , '.', '') AS out_duplic
                 , REPLACE(ROUND(contr_patronal,2)::VARCHAR            , '.', '') AS contr_patronal
                 , REPLACE(ROUND(contr_patronal,2)::VARCHAR            , '.', '') AS desc_outras_duplic
                 , REPLACE(ROUND(fundacoes_transf_corrente,2)::VARCHAR , '.', '') AS fundacoes_transf_corrente
                 , REPLACE(ROUND(autarquias_transf_corrente,2)::VARCHAR, '.', '') AS autarquias_transf_corrente
                 , REPLACE(ROUND(empestdep_transf_corrente,2)::VARCHAR , '.', '') AS empestdep_transf_corrente
                 , REPLACE(ROUND(demaisent_transf_corrente,2)::VARCHAR , '.', '') AS demaisent_transf_corrente
                 , REPLACE(ROUND(fundacoes_transf_capital,2)::VARCHAR  , '.', '') AS fundacoes_transf_capital
                 , REPLACE(ROUND(autarquias_transf_capital,2)::VARCHAR , '.', '') AS autarquias_transf_capital
                 , REPLACE(ROUND(empestdep_transf_capital,2)::VARCHAR  , '.', '') AS empestdep_transf_capital
                 , REPLACE(ROUND(demaisent_transf_capital,2)::VARCHAR  , '.', '') AS demaisent_transf_capital
              FROM " . $this->getTabela() . "('" . $this->getDado('exercicio') . "','" . $this->getDado('cod_entidade') . "'," . $this->getDado('bimestre') . ") AS tabela
                                               ( bimestre                    INTEGER,
                                                 contr_serv                  NUMERIC,
                                                 compens_reg_prev            NUMERIC,
                                                 out_duplic                  NUMERIC,
                                                 contr_patronal              NUMERIC,
                                                 desc_outras_duplic          NUMERIC,
                                                 fundacoes_transf_corrente   NUMERIC,
                                                 autarquias_transf_corrente  NUMERIC,
                                                 empestdep_transf_corrente   NUMERIC,
                                                 demaisent_transf_corrente   NUMERIC,
                                                 fundacoes_transf_capital    NUMERIC,
                                                 autarquias_transf_capital   NUMERIC,
                                                 empestdep_transf_capital    NUMERIC,
                                                 demaisent_transf_capital    NUMERIC
                                                )";

        return $stSql;
    }
}

?>