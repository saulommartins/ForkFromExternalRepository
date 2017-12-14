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
    * Classe de mapeamento da tabela folhapagamento.reajuste_percentual
    * Data de Criação: 06/04/2009

    * @author Analista     : Dagiane Vieira
    * @author Desenvolvedor: Alex Cardoso

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFolhaPagamentoReajustePercentual extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */
    public function TFolhaPagamentoReajustePercentual()
    {
        parent::Persistente();
        $this->setTabela("folhapagamento.reajuste_percentual");

        $this->setCampoCod('');
        $this->setComplementoChave('cod_reajuste');

        $this->AddCampo('cod_reajuste','integer'  ,true  ,''      ,true  ,'TFolhaPagamentoReajuste');
        $this->AddCampo('valor'       ,'numeric'  ,true  ,'14,4'  ,false ,false);

    }
}
?>
