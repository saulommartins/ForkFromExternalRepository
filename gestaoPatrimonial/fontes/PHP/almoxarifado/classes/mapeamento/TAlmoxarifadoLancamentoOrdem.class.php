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
    * Classe de mapeamento da tabela ALMOXARIFADO.LANCAMENTO_ORDEM
    * Data de Criação: 02/06/2016

    * @author Analista: Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TAlmoxarifadoLancamentoOrdem.class.php 65631 2016-06-03 21:06:49Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TAlmoxarifadoLancamentoOrdem extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function TAlmoxarifadoLancamentoOrdem()
    {
        parent::Persistente();
        $this->setTabela('almoxarifado.lancamento_ordem');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_lancamento, cod_item, cod_marca, cod_almoxarifado, cod_centro, exercicio, cod_entidade, cod_ordem, tipo, cod_pre_empenho, exercicio_pre_empenho, num_item');

        $this->AddCampo('cod_lancamento'        , 'integer', true, '', true, true);
        $this->AddCampo('cod_item'              , 'integer', true, '', true, true);
        $this->AddCampo('cod_marca'             , 'integer', true, '', true, true);
        $this->AddCampo('cod_almoxarifado'      , 'integer', true, '', true, true);
        $this->AddCampo('cod_centro'            , 'integer', true, '', true, true);
        $this->AddCampo('exercicio'             , 'char'   , true,  4, true, true);
        $this->AddCampo('cod_entidade'          , 'integer', true, '', true, true);
        $this->AddCampo('cod_ordem'             , 'integer', true, '', true, true);
        $this->AddCampo('tipo'                  , 'char'   , true,  1, true, true);
        $this->AddCampo('cod_pre_empenho'       , 'integer', true, '', true, true);
        $this->AddCampo('exercicio_pre_empenho' , 'char'   , true,  4, true, true);
        $this->AddCampo('num_item'              , 'integer', true, '', true, true);
    }
}
