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
    * Classe de mapeamento da tabela ALMOXARIFADO.TRANSFERENCIA_ALMOXARIFADO_ITEM_DESTINO
    * Data de Criação: 18/12/2008

    * @author Analista: Gelson W
    * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

    * @package URBEM
    * @subpackage Mapeamento

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.TRANSFERENCIA_ALMOXARIFADO_ITEM_DESTINO
  * Data de Criação: 18/12/2008

  * @author Analista: Gelson W
  * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TAlmoxarifadoTransferenciaAlmoxarifadoItemDestino()
    {
        parent::Persistente();
        $this->setTabela('almoxarifado.transferencia_almoxarifado_item_destino');

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_transferencia, cod_item, cod_marca, cod_centro, cod_centro_destino, cod_lancamento, cod_almoxarifado');

        $this->AddCampo('exercicio','char',true,'4',true,true);
        $this->AddCampo('cod_transferencia','integer',true,'',true,true);
        $this->AddCampo('cod_item','integer',true,'',true,true, 'TAlmoxarifadoLancamentoMaterial' );
        $this->AddCampo('cod_marca','integer',true,'',true,true, 'TAlmoxarifadoLancamentoMaterial' );
        $this->AddCampo('cod_almoxarifado','integer',true,'',true,true, 'TAlmoxarifadoLancamentoMaterial' );
        $this->AddCampo('cod_centro','integer',true,'',true,true, 'TAlmoxarifadoLancamentoMaterial' );
        $this->AddCampo('cod_lancamento','integer',true,'',false,true, 'TAlmoxarifadoLancamentoMaterial' );
        $this->AddCampo('cod_centro_destino','integer',true,'',true,true, 'TAlmoxarifadoPedidoTransferenciaItem' );
    }
}
