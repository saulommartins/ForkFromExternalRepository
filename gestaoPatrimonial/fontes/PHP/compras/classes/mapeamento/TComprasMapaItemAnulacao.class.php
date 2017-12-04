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
  * Classe de mapeamento da tabela compras.mapa_item_anulacao
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento

  * Casos de uso: uc-03.04.05

  $Id: TComprasMapaItemAnulacao.class.php 59612 2014-09-02 12:00:51Z gelson $

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  compras.mapa_item_anulacao
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
  */
class TComprasMapaItemAnulacao extends Persistente
{
    /**
      * Método Construtor
      * @access Private
      */
    public function TComprasMapaItemAnulacao()
    {
        parent::Persistente();
        $this->setTabela("compras.mapa_item_anulacao");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_mapa,exercicio_solicitacao,cod_entidade,cod_solicitacao,cod_centro,cod_item,timestamp');

        $this->AddCampo('exercicio'             , 'char'      , true  , '4'    , true  , 'TComprasMapaSolicitacaoAnulacao' );
        $this->AddCampo('cod_mapa'              , 'integer'   , true  , ''     , true  , 'TComprasMapaSolicitacaoAnulacao' );
        $this->AddCampo('exercicio_solicitacao' , 'char'      , true  , '4'    , true  , 'TComprasMapaSolicitacaoAnulacao' );
        $this->AddCampo('cod_entidade'          , 'integer'   , true  , ''     , true  , 'TComprasMapaSolicitacaoAnulacao' );
        $this->AddCampo('cod_solicitacao'       , 'integer'   , true  , ''     , true  , 'TComprasMapaSolicitacaoAnulacao' );
        $this->AddCampo('timestamp'             , 'timestamp' , false , ''     , true  , 'TComprasMapaSolicitacaoAnulacao' );
        $this->AddCampo('cod_centro'            , 'integer'   , true  , ''     , true  , true                                    );
        $this->AddCampo('cod_item'              , 'integer'   , true  , ''     , true  , true                                    );
        $this->AddCampo('lote'                  , 'integer'   , true  , ''     , true  , true                                    );
        $this->AddCampo('quantidade'            , 'numeric'   , true  , '14.4' , false , false                                   );
        $this->AddCampo('vl_total'              , 'numeric'   , true  , '14.2' , false , false                                   );
        $this->AddCampo('cod_conta'             , 'integer'   , true  , ''     , true  , true , 'TComprasMapaItemDotacao'        );
        $this->AddCampo('cod_despesa'           , 'integer'   , true  , ''     , true  , true , 'TComprasMapaItemDotacao'        );
    }

}

?>
