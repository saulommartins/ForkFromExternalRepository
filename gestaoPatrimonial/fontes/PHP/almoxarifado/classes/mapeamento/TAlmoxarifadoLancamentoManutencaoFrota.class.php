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
 * Classe de mapeamento da tabela ALMOXARIFADO.LANCAMENTO_MANUTENCAO_FROTA
 * Data de Criação: 22/05/2009

 * @author Analista:      Gelson Wolowski Gonçalves <gelson.goncalves@cnm.org.br>
 * @author Desenvolvedor: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>

 * @package URBEM
 * @subpackage Mapeamento

 $Id:$

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

/**
 * Efetua conexão com a tabela  ALMOXARIFADO.LANCAMENTO_MANUTENCAO_FROTA
 *
 */
class TAlmoxarifadoLancamentoManutencaoFrota extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function TAlmoxarifadoLancamentoManutencaoFrota()
    {
        parent::Persistente();
        $this->setTabela('almoxarifado.lancamento_manutencao_frota');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_lancamento, cod_item, cod_marca, cod_almoxarifado, cod_centro');

        $this->AddCampo('cod_lancamento'   , 'integer' , true , ''  , true  , true);
        $this->AddCampo('cod_item'         , 'integer' , true , ''  , true  , true);
        $this->AddCampo('cod_marca'        , 'integer' , true , ''  , true  , true);
        $this->AddCampo('cod_almoxarifado' , 'integer' , true , ''  , true  , true);
        $this->AddCampo('cod_centro'       , 'integer' , true , ''  , true  , true);
        $this->AddCampo('cod_manutencao'   , 'integer' , true , ''  , false , true);
        $this->AddCampo('exercicio'        , 'char'    , true , '4' , false , true);
    }
}

?>
