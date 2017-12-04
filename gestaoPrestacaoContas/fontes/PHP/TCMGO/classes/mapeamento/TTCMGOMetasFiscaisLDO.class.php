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
    * Classe de mapeamento da tabela TCEMG.METAS_FISCAIS_LDO
    * Data de Criação: 22/01/2015

    * @author Analista: Ane Pereira
    * @author Desenvolvedor: Arthur Cruz

    * @package URBEM
    * @subpackage Mapeamento
    *
    * $Id: TTCMGOMetasFiscaisLDO.class.php 61484 2015-01-22 17:36:55Z arthur $
    *
    * $Name: $
    * $Date: $
    * $Author: $
    * $Rev: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCMGOMetasFiscaisLDO extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCMGOMetasFiscaisLDO()
    {
        parent::Persistente();
        $this->setTabela('tcmgo.metas_fiscais_ldo');

        $this->setCampoCod('exercicio');
        $this->setComplementoChave('');

        $this->AddCampo('exercicio'                                 , 'varchar', true, '4'    , true ,false);
        $this->AddCampo('valor_corrente_receita'                    , 'numeric', false,'14,2' ,false ,false);
        $this->AddCampo('valor_corrente_despesa'                    , 'numeric', false,'14,2' ,false ,false);
        $this->AddCampo('valor_corrente_resultado_primario'         , 'numeric', false,'14,2' ,false ,false);
        $this->AddCampo('valor_corrente_resultado_nominal'          , 'numeric', false,'14,2' ,false ,false);
        $this->AddCampo('valor_corrente_divida_consolidada_liquida' , 'numeric', false,'14,2' ,false ,false);
    }

    public function recuperaValoresMetasFiscaisLDO(&$rsRecordSet)
    {
        return $this->executaRecupera("montaRecuperaValoresMetasFiscaisLDO", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaValoresMetasFiscaisLDO()
    {
        $stSql = "  SELECT exercicio
                         , REPLACE(valor_corrente_receita::VARCHAR, '.', ',')            AS valor_corrente_receita
                         , REPLACE(valor_corrente_despesa::VARCHAR, '.', ',')            AS valor_corrente_despesa
                         , REPLACE(valor_corrente_resultado_primario::VARCHAR, '.', ',') AS valor_corrente_resultado_primario
                         , REPLACE(valor_corrente_resultado_nominal::VARCHAR, '.', ',')  AS valor_corrente_resultado_nominal
                         , REPLACE(valor_corrente_divida_consolidada_liquida::VARCHAR, '.', ',') AS valor_corrente_divida_consolidada_liquida
                      FROM tcmgo.metas_fiscais_ldo
                     WHERE exercicio::INTEGER IN (".$this->getDado('exercicio').")
                     ORDER BY exercicio ";

        return $stSql;
    }
    
    public function __destruct(){}

}

?>