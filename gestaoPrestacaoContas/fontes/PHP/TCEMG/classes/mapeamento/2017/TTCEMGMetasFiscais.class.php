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
    * Classe de mapeamento da tabela TCEMG.METAS_FISCAIS
    * Data de Criação: 20/02/2014

    * @author Analista: Eduardo Paculski Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @package URBEM
    * @subpackage Mapeamento
    *
    * $Id: TTCEMGMetasFiscais.class.php 62269 2015-04-15 18:28:39Z franver $
    *
    * $Name: $
    * $Date: 2015-04-15 15:28:39 -0300 (Qua, 15 Abr 2015) $
    * $Author: franver $
    * $Rev: 62269 $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEMGMetasFiscais extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGMetasFiscais()
    {
        parent::Persistente();
        $this->setTabela('tcemg.metas_fiscais');

        $this->setCampoCod('exercicio');
        $this->setComplementoChave('');

        $this->AddCampo('exercicio', 'varchar', true, '4', true, false);
        $this->AddCampo('valor_corrente_receita_total'              , 'numeric', false,'14,2',false,false);
        $this->AddCampo('valor_corrente_receita_primaria'           , 'numeric', false,'14,2',false,false);
        $this->AddCampo('valor_corrente_despesa_total'              , 'numeric', false,'14,2',false,false);
        $this->AddCampo('valor_corrente_despesa_primaria'           , 'numeric', false,'14,2',false,false);
        $this->AddCampo('valor_corrente_resultado_primario'         , 'numeric', false,'14,2',false,false);
        $this->AddCampo('valor_corrente_resultado_nominal'          , 'numeric', false,'14,2',false,false);
        $this->AddCampo('valor_corrente_divida_publica_consolidada' , 'numeric', false,'14,2',false,false);
        $this->AddCampo('valor_corrente_divida_consolidada_liquida' , 'numeric', false,'14,2',false,false);
        $this->AddCampo('valor_constante_receita_total'             , 'numeric', false,'14,2',false,false);
        $this->AddCampo('valor_constante_receita_primaria'          , 'numeric', false,'14,2',false,false);
        $this->AddCampo('valor_constante_despesa_total'             , 'numeric', false,'14,2',false,false);
        $this->AddCampo('valor_constante_despesa_primaria'          , 'numeric', false,'14,2',false,false);
        $this->AddCampo('valor_constante_resultado_primario'        , 'numeric', false,'14,2',false,false);
        $this->AddCampo('valor_constante_resultado_nominal'         , 'numeric', false,'14,2',false,false);
        $this->AddCampo('valor_constante_divida_publica_consolidada', 'numeric', false,'14,2',false,false);
        $this->AddCampo('valor_constante_divida_consolidada_liquida', 'numeric', false,'14,2',false,false);
        $this->AddCampo('percentual_pib_receita_total'              , 'numeric', false, '7,3',false,false);
        $this->AddCampo('percentual_pib_receita_primaria'           , 'numeric', false, '7,3',false,false);
        $this->AddCampo('percentual_pib_despesa_total'              , 'numeric', false, '7,3',false,false);
        $this->AddCampo('percentual_pib_despesa_primaria'           , 'numeric', false, '7,3',false,false);
        $this->AddCampo('percentual_pib_resultado_primario'         , 'numeric', false, '7,3',false,false);
        $this->AddCampo('percentual_pib_resultado_nominal'          , 'numeric', false, '7,3',false,false);
        $this->AddCampo('percentual_pib_divida_publica_consolidada' , 'numeric', false, '7,3',false,false);
        $this->AddCampo('percentual_pib_divida_consolidada_liquida' , 'numeric', false, '7,3',false,false);
        $this->AddCampo('valor_corrente_receita_primaria_adv'       , 'numeric', false, '14,2',false,false);
        $this->AddCampo('valor_corrente_despesa_primaria_gerada'    , 'numeric', false, '14,2',false,false);
        $this->AddCampo('valor_constante_receita_primaria_adv'      , 'numeric', false, '14,2',false,false);
        $this->AddCampo('valor_constante_despesa_primaria_gerada'   , 'numeric', false, '14,2',false,false);
        $this->AddCampo('percentual_pib_receita_primaria_adv'       , 'numeric', false, '7,3',false,false);
        $this->AddCampo('percentual_pib_despesa_primaria_adv'       , 'numeric', false, '7,3',false,false);
    }

    public function recuperaValoresMetasFiscais(&$rsRecordSet)
    {
        return $this->executaRecupera("montaRecuperaValoresMetasFiscais",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaValoresMetasFiscais()
    {
        $stSql = "
        SELECT exercicio
             , COALESCE(valor_corrente_receita_total, 0.00) AS valor_corrente_receita_total
             , COALESCE(valor_corrente_receita_primaria, 0.00) AS valor_corrente_receita_primaria
             , COALESCE(valor_corrente_despesa_total, 0.00) AS valor_corrente_despesa_total
             , COALESCE(valor_corrente_despesa_primaria, 0.00) AS valor_corrente_despesa_primaria
             , COALESCE(valor_corrente_resultado_primario, 0.00) AS valor_corrente_resultado_primario
             , COALESCE(valor_corrente_resultado_nominal, 0.00) AS valor_corrente_resultado_nominal
             , COALESCE(valor_corrente_divida_publica_consolidada, 0.00) AS valor_corrente_divida_publica_consolidada
             , COALESCE(valor_corrente_divida_consolidada_liquida, 0.00) AS valor_corrente_divida_consolidada_liquida
             , COALESCE(valor_constante_receita_total, 0.00) AS valor_constante_receita_total
             , COALESCE(valor_constante_receita_primaria, 0.00) AS valor_constante_receita_primaria
             , COALESCE(valor_constante_despesa_total, 0.00) AS valor_constante_despesa_total
             , COALESCE(valor_constante_despesa_primaria, 0.00) AS valor_constante_despesa_primaria
             , COALESCE(valor_constante_resultado_primario, 0.00) AS valor_constante_resultado_primario
             , COALESCE(valor_constante_resultado_nominal, 0.00) AS valor_constante_resultado_nominal
             , COALESCE(valor_constante_divida_publica_consolidada, 0.00) AS valor_constante_divida_publica_consolidada
             , COALESCE(valor_constante_divida_consolidada_liquida, 0.00) AS valor_constante_divida_consolidada_liquida
             , COALESCE(percentual_pib_receita_total, 0.000) AS percentual_pib_receita_total
             , COALESCE(percentual_pib_receita_primaria, 0.000) AS percentual_pib_receita_primaria
             , COALESCE(percentual_pib_despesa_total, 0.000) AS percentual_pib_despesa_total
             , COALESCE(percentual_pib_despesa_primaria, 0.000) AS percentual_pib_despesa_primaria
             , COALESCE(percentual_pib_resultado_primario, 0.000) AS percentual_pib_resultado_primario
             , COALESCE(percentual_pib_resultado_nominal, 0.000) AS percentual_pib_resultado_nominal
             , COALESCE(percentual_pib_divida_publica_consolidada, 0.000) AS percentual_pib_divida_publica_consolidada
             , COALESCE(percentual_pib_divida_consolidada_liquida, 0.000) AS percentual_pib_divida_consolidada_liquida
             , COALESCE(valor_corrente_receita_primaria_adv, 0.00) AS valor_corrente_receita_primaria_adv
             , COALESCE(valor_corrente_despesa_primaria_gerada, 0.00) AS valor_corrente_despesa_primaria_gerada
             , COALESCE(valor_constante_receita_primaria_adv, 0.00) AS valor_constante_receita_primaria_adv
             , COALESCE(valor_constante_despesa_primaria_gerada, 0.00) AS valor_constante_despesa_primaria_gerada
             , COALESCE(percentual_pib_receita_primaria_adv, 0.000) AS percentual_pib_receita_primaria_adv
             , COALESCE(percentual_pib_despesa_primaria_adv, 0.000) AS percentual_pib_despesa_primaria_adv
          FROM tcemg.metas_fiscais
         WHERE exercicio::INTEGER IN (".$this->getDado('exercicio').")
         ORDER BY exercicio
        ";

        return $stSql;
    }
    
    public function __destruct(){}

}
?>
