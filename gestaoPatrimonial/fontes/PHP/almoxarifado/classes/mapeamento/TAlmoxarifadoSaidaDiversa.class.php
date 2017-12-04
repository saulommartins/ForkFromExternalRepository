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
    * Classe de mapeamento da tabela ALMOXARIFADO.SAIDA_DIVERSA
    * Data de Criação: 13/01/2009

    * @author Analista: Gelson
    * @author Desenvolvedor: Diogo Zarpelon

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TAlmoxarifadoSaidaDiversa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoSaidaDiversa()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.saida_diversa');

    $this->setCampoCod('cod_lancamento');
    $this->setComplementoChave('cod_item, cod_marca, cod_almoxarifado, cod_centro, cgm_solicitante');

    $this->AddCampo('cod_lancamento'   , 'sequence' , true , ''    , true  , false);
    $this->AddCampo('cod_item'         , 'integer'  , true , ''    , true  , 'TAlmoxarifadoLancamentoMaterial');
    $this->AddCampo('cod_marca'        , 'integer'  , true , ''    , true  , 'TAlmoxarifadoLancamentoMaterial');
    $this->AddCampo('cod_almoxarifado' , 'integer'  , true , ''    , true  , 'TAlmoxarifadoLancamentoMaterial');
    $this->AddCampo('cod_centro'       , 'integer'  , true , ''    , true  , 'TAlmoxarifadoLancamentoMaterial');
    $this->AddCampo('cgm_solicitante'  , 'integer'  , true , ''    , true  , '');
    $this->AddCampo('observacao'       , 'varchar'  , true , '160' , false , '');
}

}
