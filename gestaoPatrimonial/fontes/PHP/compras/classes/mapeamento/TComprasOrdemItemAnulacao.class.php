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
    * Classe de mapeamento da tabela compras.ordem_item_anulacao
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TComprasOrdemItemAnulacao.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.ordem_item_anulacao
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasOrdemItemAnulacao extends Persistente
{
    /**
        * Método Construtor
        * @access Public
    */
    public function TComprasOrdemItemAnulacao()
    {
        parent::Persistente();
        $this->setTabela("compras.ordem_item_anulacao");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_entidade, cod_ordem, cod_pre_empenho, num_item, timestamp, tipo, exercicio_pre_empenho');

        $this->AddCampo('exercicio'             , 'char'     , true , '4'   , true , true );
        $this->AddCampo('cod_entidade'          , 'integer'  , true , ''    , true , true );
        $this->AddCampo('cod_ordem'             , 'integer'  , true , ''    , true , true );
        $this->AddCampo('cod_pre_empenho'       , 'integer'  , true , ''    , true , true );
        $this->AddCampo('num_item'              , 'integer'  , true , ''    , true , true );
        $this->AddCampo('timestamp'             , 'timestamp', true , ''    , true , true );
        $this->AddCampo('quantidade'            , 'numeric'  , true , '14,4', false, false);
        $this->AddCampo('vl_total'              , 'numeric'  , false, '14,2', false, false);
        $this->AddCampo('tipo'                  , 'char'     , true , '1'   , true , true );
        $this->AddCampo('exercicio_pre_empenho' , 'char'     , true , '4'   , true , true );
    }
}
?>
